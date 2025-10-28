<?php
class ProcessMovementV3Model extends MasterModel
{
    private $jobCard = "job_card";
    private $jobBom = "job_bom";
    private $productionApproval = "production_approval";
    private $productionTrans = "production_transaction";
    private $production_log = "production_log";
    private $vendorProductionTrans = "vendor_production_trans";
    private $jobUsedMaterial = "job_used_material";
    private $jobMaterialDispatch = "job_material_dispatch";
    private $setupRequest = "prod_setup_request";
    private $setupRequestTrans = "prod_setup_trans";
    private $itemMaster = "item_master";
    private $stockTransaction = "stock_transaction";
    private $jobWorkChallan = "jobwork_challan";
    private $rej_rw_management = "rej_rw_management";
    private $vendor_challan_trans = "rej_rw_management";

    public function getApprovalData($postData){
        $data['tableName'] = $this->productionApproval;
        $data['select'] = "production_approval.*,job_card.pfc_rev_no,job_card.job_date,job_card.job_no,job_card.job_prefix,job_card.delivery_date,job_card.process,item_master.item_name as product_name,item_master.item_code as product_code,(CASE WHEN production_approval.in_process_id = 0 THEN 'Initial Stage' ELSE ipm.process_name END) as in_process_name, opm.process_name as out_process_name,product_process.finished_weight,job_card.cp_rev_no";
        $data['leftJoin']['job_card'] = "production_approval.job_card_id = job_card.id";
        $data['leftJoin']['item_master'] = "production_approval.product_id = item_master.id";
        $data['leftJoin']['process_master AS ipm'] = "production_approval.in_process_id = ipm.id";
        $data['leftJoin']['process_master AS opm'] = "production_approval.out_process_id = opm.id";
        $data['leftJoin']['product_process'] = "product_process.item_id = production_approval.product_id AND product_process.process_id = production_approval.in_process_id";
        if(!empty($postData['id'])){ $data['where']['production_approval.id'] = $postData['id']; }
        if(!empty($postData['job_card_id'])){ $data['where']['production_approval.job_card_id'] = $postData['job_card_id']; }
        if(isset($postData['in_process_id'])){ $data['where']['production_approval.in_process_id'] = $postData['in_process_id']; }
        if(!empty($postData['out_process_id'])){ $data['where']['production_approval.out_process_id'] = $postData['out_process_id']; }
        $result = $this->row($data);
        
        if (!empty($result)) :
            $vendorData = array();
            $vendorData['tableName'] ="vendor_challan";
            $vendorData['select'] = "vendor_challan.vendor_id,party_master.party_name";
            $vendorData['leftJoin']['vendor_challan_trans'] = "vendor_challan_trans.challan_id = vendor_challan.id";
            $vendorData['leftJoin']['party_master'] = "party_master.id = vendor_challan.vendor_id";
            $vendorData['where']['vendor_challan_trans.job_approval_id'] = $result->id;
           
            $vendorData['group_by'][] = ['vendor_challan.vendor_id'];
            $vndData = $this->rows($vendorData);

            $result->vendor = (!empty($vndData)) ? implode(", ", array_column($vndData, 'party_name')) : "In House";
        endif;
        
        return $result;
    }
    
	public function save($data){
        try {
            $this->db->trans_begin();
            $outwardData = $this->getApprovalData(['id'=>$data['ref_id']]);
			$nextAprvData = $this->getApprovalData(['in_process_id'=>$data['out_process_id'],'job_card_id'=>$data['job_card_id']]);
            $jobData = $this->jobcard_v3->getJobcard($data['job_card_id']);
            
            // Check Material Stock
            if( empty($outwardData->in_process_id) ){
                $reqMaterial = $this->jobcard_v3->getProcessWiseRequiredMaterial($jobData);
                if(!empty($reqMaterial['resultData'])){
                    foreach($reqMaterial['resultData'] as $row){
                       $reqQty = $data['out_qty'] * $row['bom_qty'];
                       
                       if(round($reqQty,3) > round($row['pending_stock_qty'],3)){
                            $errorMessage = array();
                            $errorMessage['out_qty']="Material not available for this out qty. ".$row['item_name']." ** ".round($reqQty,3)." > ".round($row['pending_stock_qty'],3);
                            return ['status'=>0,'message'=>$errorMessage];
                       }
                    }
                }
            }
            
            $inwardData = [
                'id' => $data['id'],
                'log_date' => $data['entry_date'],
                'prod_type' => 4,
                'job_card_id' => $data['job_card_id'],
                'job_approval_id' => $data['ref_id'],
                'process_id' => $data['in_process_id'],
                'product_id' => $data['product_id'],
                'ok_qty' => $data['out_qty'],
                'production_qty' => $data['out_qty'],
                'w_pcs' => (!empty($data['in_qty_kg']) AND !empty($data['out_qty'])) ? ($data['in_qty_kg'] / $data['out_qty']) : 0,
                'remark' => $data['remark'],
                'production_time' => "",
                'cycle_time' => "",
                'send_to' => $data['send_to'],
                'out_process_id' => $data['out_process_id'],
                'stage_type' => $nextAprvData->stage_type,
                'created_by' => $data['created_by'],
                'auto_log_id'=>(!empty($data['auto_log_id'])?$data['auto_log_id']:'')
            ];
            $saveInward = $this->store($this->production_log, $inwardData);
            //update out qty
            
            $setData = array();
            $setData['tableName'] = $this->productionApproval;
            $setData['where']['id'] = $data['ref_id'];
            if(empty($data['in_process_id'])){
                $setData['set']['total_ok_qty'] = 'total_ok_qty, + ' . $data['out_qty'];
            }
            $setData['set']['out_qty'] = 'out_qty, + ' . $data['out_qty'];
            $this->setValue($setData);
          
            if (!empty($data['out_process_id'])){
                $setData = array();
                $setData['tableName'] = $this->productionApproval;
                $setData['where']['in_process_id'] = $data['out_process_id'];
                $setData['where']['job_card_id'] = $data['job_card_id'];
                $setData['where']['trans_type'] = $outwardData->trans_type;
                $setData['where']['ref_id'] = $outwardData->ref_id;
                $setData['set']['inward_qty'] = 'inward_qty, + ' . $data['out_qty'];
                $setData['set']['in_qty'] = 'in_qty, + ' . $data['out_qty'];
                if(!empty($data['send_to'])){
                    $setData['set']['ch_qty'] = 'ch_qty, + ' . $data['out_qty'];
                }
                $this->setValue($setData);                
            }else{
                $setData = array();
                $setData['tableName'] = $this->jobCard;
                $setData['where']['id'] = $data['job_card_id'];
                $setData['set']['unstored_qty'] = 'unstored_qty, + ' . $data['out_qty'];
                $this->setValue($setData);
            }
           
            $approvalData = $this->getApprovalData(['id'=>$data['ref_id']]);
            $totalLogData=$this->productionLog->getPrdLogOnProcessNJob(['job_card_id'=>$data['job_card_id']]);
            $total_rej_qty = (!empty($totalLogData->rejection_qty)) ? $totalLogData->rejection_qty : 0;
            $total_prod_qty=($approvalData->out_qty+$total_rej_qty);
            
            $jobProcesses = explode(",", $jobData->process);
            if ($data['in_process_id'] == $jobProcesses[(count($jobProcesses)-1)] &&   $total_prod_qty>= $jobData->qty) :
                /* Update Used Stock in Job Material Used */
                $setData = array();
                $setData['tableName'] = $this->jobUsedMaterial;
                $setData['where']['job_card_id'] = $data['job_card_id'];
                $setData['set']['used_qty'] = 'used_qty, + (' . $total_prod_qty . ' * wp_qty)';
                $this->setValue($setData);

                /** Minus stock in received material */
                $queryData = array();
                $queryData['tableName'] = "stock_transaction";
                $queryData['where']['trans_type'] = 1;
                $queryData['where']['ref_type'] = 27;
                $queryData['where']['ref_id'] = $data['job_card_id'];
                $queryData['where']['location_id'] = $this->RCV_RM_STORE->id;
                $queryData['group_by'][] = "batch_no";
                $issueRMList = $this->rows($queryData);
                if(!empty($issueRMList)):
                    foreach($issueRMList as $row):
                        $bomQuery['tableName'] = $this->jobBom;
                        $bomQuery['where']['ref_item_id'] = $row->item_id;
                        $bomQuery['where']['job_card_id'] = $data['job_card_id'];
                        $bomData = $this->row($bomQuery);

                        $stockMinusTrans = [
                            'id' => "",
                            'location_id' =>$this->RCV_RM_STORE->id,
                            'batch_no' =>$row->batch_no,
                            'trans_type' => 2,
                            'item_id' => $row->item_id,
                            'qty' =>'-'.($bomData->qty*$total_prod_qty),
                            'ref_type' => 27,
                            'ref_id' =>$row->ref_id,
                            'trans_ref_id' =>$saveInward['insert_id'],
                            'ref_no'=>$row->ref_no,
                            'ref_date' =>$data['entry_date'],
                            'created_by' => $this->session->userdata('loginId'),
                            'stock_effect' => 0
                        ];
                        $this->store('stock_transaction',$stockMinusTrans);
                    endforeach;
                endif;
                $this->store($this->jobCard, ['id' =>$data['job_card_id'], 'order_status' => 4]);
            endif;

            $pendingQty = 0;
            $approvalData = $this->getApprovalData($data['ref_id']);
            if(!empty($approvalData->in_process_id)){
                $pendingQty =$approvalData->total_ok_qty- $approvalData->out_qty;
            }else{
                $pendingQty = $approvalData->in_qty - $approvalData->out_qty;
            }


            $result = ['status' => 1, 'message' => 'Outward saved successfully.','pending_qty'=>$pendingQty];
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getOutwardTrans($jon_approval_id){
        $data['tableName'] = $this->production_log;
        $data['select'] = "production_log.*";
        $data['leftJoin']['production_approval'] = "production_log.job_approval_id = production_approval.id";
        $data['leftJoin']['process_master'] = "production_log.process_id = process_master.id";
        $data['where']['production_log.job_approval_id'] = $jon_approval_id;
        $data['where_in']['production_log.prod_type'] = 4;
        $result = $this->rows($data);
        return $result;
    }

    public function delete($id){
        try {
            $this->db->trans_begin();
            $data['tableName'] = $this->production_log;
            $data['where']['id'] = $id;
            $inwardData = $this->row($data);
            
            $jobData = $this->jobcard_v3->getJobcard($inwardData->job_card_id);
            $processApproval = $this->getApprovalData(['id'=>$inwardData->job_approval_id]);
            $nextProcessApproval = $this->getApprovalData(['job_card_id'=>$inwardData->job_card_id, 'in_process_id'=>$processApproval->out_process_id]);
            
            $logSheetData = $this->productionLog->getPrdLogOnProcessNJob(['job_card_id'=>$nextProcessApproval->job_card_id,'process_id'=>$nextProcessApproval->in_process_id]);
            $nextProcessRejQty = (!empty($logSheetData->rejection_qty))?$logSheetData->rejection_qty:0;
            $pendingQty = $nextProcessApproval->in_qty - ($nextProcessApproval->total_ok_qty+$nextProcessApproval->total_rejection_qty+$nextProcessApproval->total_rework_qty);// $logSheetData->production_qty;
            if(empty($processApproval->out_process_id) && $inwardData->production_qty > $jobData->unstored_qty && empty($inwardData->trans_ref_id)):
                return ['status' => 0, 'message' => "You can't delete this outward because This outward is Stored",'job_approval_id'=>$inwardData->job_approval_id];
            else:
                if($inwardData->send_to == 0){
                    if (!empty($processApproval->out_process_id) && $inwardData->production_qty > $pendingQty ) :
                        return ['status' => 0, 'message' => "You can't delete this outward because This outward moved to next process.",'job_approval_id'=>$inwardData->job_approval_id];
                    endif;
                }elseif($inwardData->send_to == 1 && $inwardData->auto_log_id == 0){
                    $prdQry['tableName'] = $this->production_log;
                    $prdQry['select'] = "SUM(production_log.production_qty) as qty,IFNULL(challan.challan_qty,0) as challan_qty";
                    $prdQry['leftJoin']['(select SUM(qty) as challan_qty,job_card_id,process_id from vendor_challan_trans where is_delete = 0  AND type=1 group by job_card_id,process_id) as challan'] = "challan.job_card_id  = production_log.job_card_id AND challan.process_id  = production_log.out_process_id";
                    $prdQry['where']['production_log.send_to'] = 1;
                    $prdQry['where']['production_log.prod_type'] = 4;
                    $prdQry['where']['production_log.job_approval_id'] = $inwardData->job_approval_id;
                    $prdQry['group_by'][]="production_log.job_card_id,production_log.out_process_id";
                    $prdResult = $this->row($prdQry);
                    $pendingQty  = $prdResult->qty-$prdResult->challan_qty;
                    if($inwardData->production_qty > $pendingQty){
                        return ['status' => 0, 'message' => "You can't delete this outward.",'job_approval_id'=>$inwardData->job_approval_id];
                    }
                }
                
            endif;
            
            //update out qty
            $setData = array();
            $setData['tableName'] = $this->productionApproval;
            $setData['where']['id'] = $inwardData->job_approval_id;
            //if (!empty($processApproval->out_process_id))
            $setData['set']['out_qty'] = 'out_qty, - ' . $inwardData->production_qty;
            if (empty($processApproval->in_process_id)){
                $setData['set']['total_ok_qty'] = 'total_ok_qty, - ' . $inwardData->production_qty;
            }
            $this->setValue($setData);
           
            if (!empty($processApproval->out_process_id)) :
                /** Next Process */
                $setData = array();
                $setData['tableName'] = $this->productionApproval;
                $setData['where']['in_process_id'] = $processApproval->out_process_id;
                $setData['where']['job_card_id'] = $inwardData->job_card_id;
                $setData['where']['trans_type'] = 0;
                $setData['where']['ref_id'] = 0;
                $setData['set']['inward_qty'] = 'inward_qty, - ' . $inwardData->production_qty;
                $setData['set']['in_qty'] = 'in_qty, - ' . $inwardData->production_qty;
                if($inwardData->send_to == 1){
                    $setData['set']['ch_qty'] = 'ch_qty, - ' . $inwardData->production_qty;
                }
                $this->setValue($setData);
            endif;

            /*** If First Process then Maintain Batchwise Rowmaterial ***/
            $jobProcesses = explode(",", $jobData->process);
            if ($inwardData->process_id == $jobProcesses[count( $jobProcesses)-1]) :
                $this->trash($this->jobMaterialDispatch, ['job_card_id' => $inwardData->job_card_id, 'job_trans_id' => $inwardData->id]);
                $totalLogData=$this->productionLog->getPrdLogOnProcessNJob(['job_card_id'=>$inwardData->job_card_id]);
                $total_rej_qty = (!empty($totalLogData->rejection_qty)) ? $totalLogData->rejection_qty : 0;
                $total_prod_qty=($processApproval->out_qty+$total_rej_qty);
               
                /* Update Used Stock in Job Material Used */
                $setData = array();
                $setData['tableName'] = $this->jobUsedMaterial;
                $setData['where']['job_card_id'] = $inwardData->job_card_id;
                $setData['set']['used_qty'] = 'used_qty, - (' . $total_prod_qty . '* wp_qty)';
                $qryresult = $this->setValue($setData);
                $this->remove($this->stockTransaction,['trans_type' => 2,'trans_ref_id'=> $id,'ref_type'=>27,'ref_id' =>$inwardData->job_card_id]);
                $this->store($this->jobCard, ['id' => $inwardData->job_card_id, 'order_status' => 2]);
            endif;

            if ($jobProcesses[count($jobProcesses) - 1] == $inwardData->process_id ) :
                $setData = array();
                $setData['tableName'] = $this->jobCard;
                $setData['where']['id'] = $inwardData->job_card_id;
                $setData['set']['unstored_qty'] = 'unstored_qty, - ' . $inwardData->production_qty;
                $this->setValue($setData);

                $this->remove($this->stockTransaction,['trans_type'=>1,'trans_ref_id'=> $id,'ref_type'=>7,'ref_id' =>$inwardData->job_card_id]);
            endif;

            if ($jobProcesses[count($jobProcesses) - 1] == $inwardData->process_id ) :
                $this->remove($this->stockTransaction,['trans_type'=>1,'trans_ref_id'=> $id,'ref_type'=>7,'ref_id' =>$inwardData->job_card_id]);
            endif;
            $result = $this->trash($this->production_log, ['id' => $id], 'Outward');

            $this->remove($this->stockTransaction,['trans_ref_id' => $id,'ref_type' => 28,'ref_id' =>$inwardData->job_card_id]); 
            
            /* Get Pending Qty for Movement */
            $approvalData = $this->getApprovalData(['id'=>$inwardData->job_approval_id]);
            if(!empty($approvalData->in_process_id)){
                $pendingQty =$approvalData->total_ok_qty- $approvalData->out_qty;
            }else{
                $pendingQty = $approvalData->in_qty - $approvalData->out_qty;
            }

            $result['job_approval_id'] = $inwardData->job_approval_id;
			$result['process_id'] = $inwardData->process_id;
            $result['pending_qty'] = $pendingQty;
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getOutward($id)
    {
        $data['tableName'] = $this->productionApproval;
        $data['select'] = "production_approval.*,job_card.job_date,job_card.job_no,job_card.job_prefix,job_card.delivery_date,item_master.item_name as product_name,item_master.item_code as product_code";
        $data['leftJoin']['job_card'] = "production_approval.job_card_id = job_card.id";
        $data['leftJoin']['item_master'] = "production_approval.product_id = item_master.id";
        $data['where']['production_approval.id'] = $id;
        return $this->row($data);
    }
	
    public function getStoreLocationTrans($id)
    {
        $queryData['tableName'] = $this->stockTransaction;
        $queryData['select'] = "stock_transaction.*,location_master.store_name,location_master.location";
        $queryData['leftJoin']['location_master'] = "stock_transaction.location_id = location_master.id";
        $queryData['where']['stock_transaction.ref_type'] = 28;
        $queryData['where']['stock_transaction.ref_id'] = $id;
        $queryData['where']['stock_transaction.trans_type'] = 1;
        $queryData['where']['stock_transaction.ref_batch'] = NULL;
        $stockTrans = $this->rows($queryData);
        $html = '';
        if (!empty($stockTrans)) :
            $i = 1;
            foreach ($stockTrans as $row) :
                $deleteBtn = '<button type="button" onclick="trashStockTrans(' . $row->id . ');" class="btn btn-outline-danger waves-effect waves-light"><i class="ti-trash"></i></button>';
                $html .= '<tr>
                            <td class="text-center" style="width: 5%;">' . $i++ . '</td>
                            <td class="text-center">' . $row->batch_no . '</td>
                            <td class="text-center">[ ' . $row->store_name . ' ] ' . $row->location . '</td>
                            <td class="text-center">' . $row->qty . '</td>
                            <td class="text-center" style="width: 8%;">' . $deleteBtn . '</td>
                        </tr>';
            endforeach;
        else :
            $html .= '<tr>
                        <td class="text-center" colspan="5">No Data Found.</td>
                    </tr>';
        endif;
        return ['status' => 1, 'htmlData' => $html, 'result' => $stockTrans];
    }

    public function saveStoreLocation($data)
    {
        try {
            $this->db->trans_begin();
            $jobData = $this->jobcard_v3->getJobcard($data['job_id']);

            $queryData = array();
            $queryData['tableName'] = $this->productionApproval;
            $queryData['where']['id'] = $data['ref_id'];
            $processMovementData = $this->row($queryData);            

            $stockTrans = [
                'id' => "",
                'location_id' => $data['location_id'],
                'trans_type' => 1,
                'item_id' => $data['product_id'],
                'qty' => $data['qty'],
                'ref_type' => 28,
                'ref_id' => $data['job_id'],
                'ref_no' => getPrefixNumber($jobData->job_prefix, $jobData->job_no),
                'trans_ref_id' => $data['ref_id'],
                'ref_date' => $data['trans_date'],
                'stock_effect'=>0, //26-09-2024
                'created_by' => $data['created_by']
            ];
            if($data['location_id'] == $this->HLD_STORE->id){
                $stockTrans['stock_effect']=0;
            }
            if (!empty($data['batch_no']))
                $stockTrans['batch_no'] = getPrefixNumber($jobData->job_prefix, $jobData->job_no);
            $stockSave = $this->store($this->stockTransaction, $stockTrans);            

            /*************************************************/
            /* Mansee @ 14-06-2022 Save In Production Transaction*/
            if($data['location_id'] == $this->HLD_STORE->id){
                $prdTrans=[
                    'id'=>'',
                    'entry_type'=>'3',
                    'entry_date'=>$data['trans_date'],
                    'ref_id'=> $stockSave['insert_id'],
                    'job_card_id'=>$data['job_id'],
                    'production_approval_id'=>$data['ref_id'],
                    'process_id'=>$processMovementData->in_process_id,
                    'product_id'=>$processMovementData->product_id,
                    'in_qty'=>$data['qty'],
                    'created_by'=>$this->session->userdata('loginId'),
                    'created_at'=>date("Y-m-d H:i:s")
                ];
                $this->store($this->productionTrans,$prdTrans); 
            }
            /*************************************************/
            $setData = array();
            $setData['tableName'] = $this->jobCard;
            $setData['where']['id'] = $data['job_id'];
            $setData['set']['unstored_qty'] = 'unstored_qty, - ' . $data['qty'];
            $this->setValue($setData);

            $setData = array();
            $setData['tableName'] = $this->itemMaster;
            $setData['where']['id'] = $data['product_id'];
            $setData['set']['qty'] = 'qty, + ' . $data['qty'];
            $this->setValue($setData);

            $queryData = array();
            $queryData['tableName'] = $this->productionApproval;
            $queryData['where']['id'] = $data['ref_id'];
            $approvalData = $this->row($queryData);

            $jobCardData = $this->jobcard_v3->getJobCard($data['job_id']); 
            $logData=$this->logSheet->getPrdLogOnProcessNJob($data['job_id']);
            $totalQty = $logData->rejection_qty + $approvalData->out_qty;
            if ($totalQty >= $jobCardData->qty) :
                $this->store($this->jobCard, ['id' => $jobCardData->id, 'order_status' => 4]);
            endif;

            $result = ['status' => 1, 'message' => "Stock Transfer successfully.", 'htmlData' => $this->getStoreLocationTrans($data['job_id'])['htmlData'], 'unstored_qty' => $jobCardData->unstored_qty];
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function deleteStoreLocationTrans($id)
    {
        try {
            $this->db->trans_begin();
            $queryData['tableName'] = $this->stockTransaction;
            $queryData['where']['id'] = $id;
            $stockTrans = $this->row($queryData);
            if (!empty($stockTrans)) :
                $setData = array();
                $setData['tableName'] = $this->jobCard;
                $setData['where']['id'] = $stockTrans->ref_id;
                $setData['set']['unstored_qty'] = 'unstored_qty, + ' . $stockTrans->qty;
                $this->setValue($setData);

                $setData = array();
                $setData['tableName'] = $this->itemMaster;
                $setData['where']['id'] = $stockTrans->item_id;
                $setData['set']['qty'] = 'qty, - ' . $stockTrans->qty;
                $this->setValue($setData);

                $queryData = array();
                $queryData['tableName'] = $this->productionApproval;
                $queryData['where']['id'] = $stockTrans->trans_ref_id;
                $approvalData = $this->row($queryData);

                $jobCardData = $this->jobcard_v3->getJobCard($stockTrans->ref_id);
                    
                // endif;
                $this->store($this->jobCard, ['id' => $jobCardData->id, 'order_status' => 2]);
                $this->remove($this->stockTransaction, ['id' => $id]);
                //$this->trash($this->productionTrans, ['ref_id' => $id,'entry_type'=>3]);
                $this->remove($this->stockTransaction,['trans_type'=>2,'ref_type'=>7,'trans_ref_id'=>$stockTrans->trans_ref_id,'ref_id'=> $stockTrans->ref_id]);

                
                $result = ['status' => 1, 'message' => 'Stock Transaction deleted successfully.', 'htmlData' => $this->getStoreLocationTrans($stockTrans->ref_id)['htmlData'], 'unstored_qty' => $jobCardData->unstored_qty,'ref_id'=>$stockTrans->ref_id];
            else :
                $result = ['status' => 0, 'message' => 'Stock transaction already deleted.'];
            endif;
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    // 06-02-2024
    public function getMovementTransactions($id,$entry_type){
        $data['tableName'] = $this->production_log;
        $data['select'] = "production_log.*,(CASE WHEN production_log.send_to = 0 THEN 'In House' ELSE party_master.party_name END) as vendor_name,employee_master.emp_name,mc.item_name as machine_name,mc.item_code as machine_code,shift_master.shift_name,process_master.process_name";
        $data['leftJoin']['party_master'] = "production_log.send_to = party_master.id";
        $data['leftJoin']['employee_master'] = "production_log.operator_id = employee_master.id";
        $data['leftJoin']['shift_master'] = "shift_master.id = production_log.shift_id";
        $data['leftJoin']['item_master as mc'] = "mc.id = production_log.machine_id";
        $data['leftJoin']['process_master'] = "process_master.id = production_log.process_id";
        $data['where']['production_log.job_card_id'] = $id;
        $data['where_in']['production_log.prod_type'] = $entry_type;
        $result = $this->rows($data);
        return $result;
    }

    //26-09-2024
    public function getStockApprovalDtRows($data){
        $data['tableName'] = $this->stockTransaction;
        $data['select'] = "stock_transaction.*,item_master.item_code,location_master.location";
        $data['leftJoin']['location_master'] = "location_master.id = stock_transaction.location_id";
        $data['leftJoin']['item_master'] = "item_master.id = stock_transaction.item_id";
        
        $data['where']['stock_transaction.stock_effect'] = 0;
        $data['where']['stock_transaction.ref_type'] = 28;
        
        $data['searchCol'][] ="";
        $data['searchCol'][] ="";
        $data['searchCol'][] = "DATE_FORMAT(stock_transaction.ref_date,'%d-%m-%Y')";
        $data['searchCol'][] = "stock_transaction.batch_no";
        $data['searchCol'][] = "item_master.item_code";
        $data['searchCol'][] = "location_master.location";
        $data['searchCol'][] = "stock_transaction.qty";
      
        $columns = array('', '', 'stock_transaction.ref_date', 'stock_transaction.batch_no','item_master.item_code','location_master.location', 'stock_transaction.qty');
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }
        return $this->pagingRows($data);
    }

    public function approveStockEntry($data){ 
        try{
            $this->db->trans_begin();
          
            $result = $this->store($this->stockTransaction,['id'=>$data['id'],'stock_effect'=>1,'updated_by'=>$this->loginId,'updated_at'=>date("Y-m-d H:i:s")]);

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
        return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

}
