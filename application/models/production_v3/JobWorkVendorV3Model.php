<?php
class JobWorkVendorV3Model extends MasterModel{
    private $jobCard = "job_card";
    private $productionApproval = "production_approval";
    private $vendor_challan = "vendor_challan";
    private $vendor_challan_trans = "vendor_challan_trans";
    private $productionTrans = "production_transaction";
    private $vendorProductionTrans = "vendor_production_trans";
    private $jobWorkChallan = "jobwork_challan";
    private $production_log = "production_log";

    /***Pending Challan */
    public function getPendingChallanDTRows($data){
        $data['tableName'] = $this->productionApproval;
        $data['select'] = "production_approval.ch_qty as qty,job_card.job_date,job_card.job_no,job_card.job_prefix,item_master.item_name,item_master.item_code,process_master.process_name,IFNULL(challan.challan_qty,0) as challan_qty";

        $data['leftJoin']['job_card'] = "production_approval.job_card_id = job_card.id";
        $data['leftJoin']['item_master'] = "production_approval.product_id = item_master.id";
        $data['leftJoin']['process_master'] = "production_approval.out_process_id = process_master.id";
        $data['leftJoin']['(select SUM(qty) as challan_qty,job_card_id,process_id from vendor_challan_trans where is_delete = 0 AND type = 1 group by job_card_id,process_id) as challan'] = "challan.job_card_id  = production_approval.job_card_id AND challan.process_id  = production_approval.in_process_id";

        $data['having'][] = '(production_approval.ch_qty - challan_qty) > 0';

        $data['group_by'][]="production_approval.job_card_id,production_approval.out_process_id";

        $data['searchCol'][] = "CONCAT(job_card.job_prefix,job_card.job_no)";
        $data['searchCol'][] = "item_master.item_code";
        $data['searchCol'][] = "process_master.process_name";
        $data['searchCol'][] = "process_master.process_name";
        $data['searchCol'][] = "process_master.process_name";
        $data['searchCol'][] = "process_master.process_name";
        $data['searchCol'][] = "process_master.process_name";

        $columns =array('','','job_card.job_no','item_master.item_code','process_master.process_name','','');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		$result = $this->pagingRows($data);
        return $result;
    }

    public function getPendingChallanData($postData){
        $data['tableName'] = $this->production_log;
        $data['select'] = "production_approval.id as job_approval_id,production_log.product_id,production_approval.ch_qty as qty,(CASE WHEN production_log.w_pcs > 0 THEN (SUM(production_log.production_qty * production_log.w_pcs) / SUM(production_log.production_qty)) ELSE 0 END) as w_pcs,job_card.job_date,job_card.job_no,job_card.job_prefix,item_master.item_name,item_master.item_code,process_master.process_name,IFNULL(challan.challan_qty,0) as challan_qty,job_card.process";

        $data['leftJoin']['job_card'] = "production_log.job_card_id = job_card.id";
        $data['leftJoin']['production_approval'] = "production_log.job_card_id = production_approval.job_card_id AND production_log.out_process_id = production_approval.in_process_id";
        $data['leftJoin']['item_master'] = "production_log.product_id = item_master.id";
        $data['leftJoin']['process_master'] = "production_log.out_process_id = process_master.id";
        $data['leftJoin']['(select SUM(qty) as challan_qty,job_card_id,process_id from vendor_challan_trans where is_delete = 0  AND type=1 group by job_card_id,process_id) as challan'] = "challan.job_card_id  = production_log.job_card_id AND challan.process_id  = production_log.out_process_id";

       
        $data['where']['production_log.send_to'] = 1;
        $data['where']['production_log.prod_type'] = 4;
        $data['where']['production_log.out_process_id'] = $postData['process_id'];
        $data['having'][] = '(production_approval.ch_qty - challan_qty) > 0';

        $data['group_by'][]="production_log.job_card_id,production_log.out_process_id";
        return $this->rows($data);
    }

    public function getDTRows($data){ 
        $data['tableName'] = $this->vendor_challan_trans;
        $data['select'] = "vendor_challan_trans.*,vendor_challan.trans_status,vendor_challan.trans_date,vendor_challan.trans_number,vendor_challan.remark,job_card.job_date,job_card.job_no,job_card.job_prefix,party_master.party_name,item_master.item_name,item_master.item_code,process_master.process_name,((vendor_challan_trans.return_qty * 100) / vendor_challan_trans.qty) as status_per,(vendor_challan_trans.qty - vendor_challan_trans.return_qty) as pending_qty,vendor_challan.eway_bill_no";

        $data['leftJoin']['vendor_challan'] = "vendor_challan.id = vendor_challan_trans.challan_id";
        $data['leftJoin']['job_card'] = "vendor_challan_trans.job_card_id = job_card.id";
        $data['leftJoin']['party_master'] = "vendor_challan.vendor_id = party_master.id";
        $data['leftJoin']['item_master'] = "vendor_challan_trans.item_id = item_master.id";
        $data['leftJoin']['process_master'] = "vendor_challan_trans.process_id = process_master.id";

        if($data['status'] == 0):
            $data['where']['(vendor_challan_trans.qty - vendor_challan_trans.return_qty) > '] = 0;
        endif;
        if($data['status'] == 1):
            $data['where']['(vendor_challan_trans.qty - vendor_challan_trans.return_qty) <= '] = 0;
        endif;
        
        if(!empty($data['vendor_id'])){$data['where']['vendor_challan.vendor_id'] = $data['vendor_id'];}
        if(!empty($data['from_date'])){ $data['where']['vendor_challan.trans_date >= '] = $data['from_date']; }
        if(!empty($data['to_date'])){ $data['where']['vendor_challan.trans_date <= '] = $data['to_date']; }
        
        $data['where']['vendor_challan_trans.type'] = 1;
        $data['order_by']['vendor_challan_trans.id'] = "DESC";

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "DATE_FORMAT(vendor_challan.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "vendor_challan.trans_number";
        $data['searchCol'][] = "DATE_FORMAT(job_card.job_date,'%d-%m-%Y')";
        $data['searchCol'][] = "CONCAT(job_card.job_prefix,job_card.job_no)";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "item_master.item_code";
        $data['searchCol'][] = "process_master.process_name";
        $data['searchCol'][] = "vendor_challan_trans.weight";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		$result = $this->pagingRows($data); //$this->printQuery();
        return $result;
    }
    
    public function getVendorDTRows($data){ 
        $data['tableName'] = $this->vendor_challan_trans;
        $data['select'] = "vendor_challan_trans.*,vendor_challan.trans_status,vendor_challan.trans_date,vendor_challan.trans_number,vendor_challan.remark,job_card.job_date,job_card.job_no,job_card.job_prefix,party_master.party_name,item_master.item_name,item_master.item_code,process_master.process_name,((IFNULL(receive.receive_qty,0) * 100) / (vendor_challan_trans.qty + vendor_challan_trans.without_process_qty)) as status_per,((vendor_challan_trans.qty + vendor_challan_trans.without_process_qty) - IFNULL(receive.receive_qty,0)) as pending_qty,vendor_challan.eway_bill_no, IFNULL(receive.receive_qty,0) AS receive_qty";

        $data['leftJoin']['vendor_challan'] = "vendor_challan.id = vendor_challan_trans.challan_id";
        $data['leftJoin']['job_card'] = "vendor_challan_trans.job_card_id = job_card.id";
        $data['leftJoin']['party_master'] = "vendor_challan.vendor_id = party_master.id";
        $data['leftJoin']['item_master'] = "vendor_challan_trans.item_id = item_master.id";
        $data['leftJoin']['process_master'] = "vendor_challan_trans.process_id = process_master.id";
        $data['leftJoin']['(SELECT SUM(production_qty + without_prs_qty) As receive_qty,ch_trans_id,process_id FROM vendor_receive WHERE is_delete = 0 GROUP BY ch_trans_id,process_id)receive'] = "vendor_challan_trans.process_id = receive.process_id AND receive.ch_trans_id = vendor_challan_trans.id";
        
        
        if($data['status'] == 0):   // PENDING FOR ACCEPTANCE
            $data['where']['vendor_challan.trans_status'] = 0;
        endif;
        
        if($data['status'] == 1):   // PENDING FOR DISPATCH
            $data['where']['vendor_challan.trans_status'] = 1;
            $data['having'][] = '((vendor_challan_trans.qty + vendor_challan_trans.without_process_qty) - receive_qty) > 0';
        endif;
        
        if($data['status'] == 2):   // DISPATCHED
             $data['having'][] = '((vendor_challan_trans.qty+ vendor_challan_trans.without_process_qty) - receive_qty) <= 0';
            //$data['where']['job_card.job_date >= '] = $this->startYearDate;
            //$data['where']['job_card.job_date <= '] = $this->endYearDate;
        endif;
        
        if(!empty($data['vendor_id'])){$data['where']['vendor_challan.vendor_id'] = $data['vendor_id'];}
        
        if(!empty($data['from_date'])){ $data['where']['vendor_challan.trans_date >= '] = $data['from_date']; }
        if(!empty($data['to_date'])){ $data['where']['vendor_challan.trans_date <= '] = $data['to_date']; }
        
        $data['where']['vendor_challan_trans.type'] = 1;
        $data['where']['vendor_challan.ref_id'] = 0;
        $data['order_by']['vendor_challan_trans.id'] = "DESC";

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "DATE_FORMAT(vendor_challan.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "vendor_challan.trans_number";
        $data['searchCol'][] = "DATE_FORMAT(job_card.job_date,'%d-%m-%Y')";
        $data['searchCol'][] = "CONCAT(job_card.job_prefix,job_card.job_no)";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "item_master.item_code";
        $data['searchCol'][] = "process_master.process_name";
        $data['searchCol'][] = "vendor_challan_trans.weight";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		$result = $this->pagingRows($data);
        // print_r($this->db->last_query());exit;
        return $result;
    }

    public function getVendorReceiveDTRows($data){
         $style = 'style="margin:0px;padding:0px"';
        $data['tableName'] = 'vendor_receive';
        $data['select'] = "vendor_receive.*,vendor_challan.trans_number,vendor_challan.trans_date,job_card.job_date,job_card.job_no,job_card.job_prefix,party_master.party_name,item_master.item_name,item_master.item_code,GROUP_CONCAT(process_master.process_name SEPARATOR '<hr ".$style.">') AS process_name,GROUP_CONCAT(vendor_receive.production_qty SEPARATOR '<hr ".$style.">') AS production_qtys,GROUP_CONCAT(vendor_receive.without_prs_qty SEPARATOR '<hr ".$style.">') AS without_prs_qtys";
        $data['leftJoin']['vendor_challan'] = 'vendor_challan.id = vendor_receive.challan_id';
        $data['leftJoin']['vendor_challan_trans'] = 'vendor_challan_trans.id = vendor_receive.ch_trans_id';
        $data['leftJoin']['job_card'] = "vendor_challan_trans.job_card_id = job_card.id";
        $data['leftJoin']['party_master'] = "vendor_challan.vendor_id = party_master.id";
        $data['leftJoin']['item_master'] = "vendor_challan_trans.item_id = item_master.id";
        $data['leftJoin']['process_master'] = "vendor_receive.process_id = process_master.id";

        $data['group_by'][] = 'vendor_receive.ch_trans_id,vendor_receive.in_challan_no';
        if($data['status'] == 0):
            $data['where']['vendor_receive.accepted_by'] =0;
        endif;
        if($data['status'] == 1):
            $data['where']['vendor_receive.accepted_by >'] =0;
        endif;
        if(!empty($data['vendor_id'])){$data['where']['vendor_challan.vendor_id'] = $data['vendor_id'];}
        
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "DATE_FORMAT(vendor_challan.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "vendor_challan.trans_number";
        $data['searchCol'][] = "DATE_FORMAT(job_card.job_date,'%d-%m-%Y')";
        $data['searchCol'][] = "CONCAT(job_card.job_prefix,job_card.job_no)";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "item_master.item_code";
        $data['searchCol'][] = "process_master.process_name";
        $data['searchCol'][] = "vendor_receive.in_challan_no";
        $data['searchCol'][] = "DATE_FORMAT(vendor_receive.in_challan_date,'%d-%m-%Y')";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		$result = $this->pagingRows($data);
        // print_r($this->db->last_query());exit;
        return $result;
       
    }
    
    public function getJobWorkVendorRow($id){
        $data['tableName'] = $this->vendor_challan_trans;
        $data['select'] = "vendor_challan_trans.*,(vendor_challan_trans.qty - vendor_challan_trans.return_qty) as pending_qty";
        $data['where']['id'] = $id;
        return $this->row($data);
    }

    public function nextTransNo(){
        $data['select'] = "MAX(trans_no) as transNo";
        $data['tableName'] = "vendor_challan";
        $data['where']['trans_date >= '] = $this->startYearDate;
        $data['where']['trans_date <= '] = $this->endYearDate;
		$transNo = $this->specificRow($data)->transNo;
		$nextTransNo = (!empty($transNo))?($transNo + 1):1;
		return $nextTransNo;
    }
    
    public function saveVendorChallan($masterData,$transData){
        try{
            $this->db->trans_begin();
            $result = $this->store($this->vendor_challan,$masterData,'Vendor Challan');
            foreach($transData['job_approval_id'] as $key=>$value)
            {
                $approveData = $this->processMovement->getApprovalData(['id'=>$value]);
                $trans = [
                    'id' => "",
                    'type' => 1,
                    'challan_id' => $result['insert_id'],
                    'job_card_id' => $approveData->job_card_id,
                    'item_id' => $approveData->product_id,
                    'process_id' => $transData['process_id'],
                    'process_ids' => $transData['process_ids'][$key],
                    'challan_type' => $transData['challan_type'][$key],
                    'job_approval_id' => $transData['job_approval_id'][$key],
                    'jobwork_order_id' =>$transData['jobwork_order_id'][$transData['job_approval_id'][$key]],
                    'qty' => $transData['qty'][$key],
                    'w_pcs' => $transData['w_pcs'][$key],
                    'weight' => $transData['weight'][$key],
                    'created_by' => $this->loginId,
                    'auto_log_id'=>(!empty($transData['auto_log_id'][$key])?$transData['auto_log_id'][$key]:'')
                ];
                
                $chresult = $this->store($this->vendor_challan_trans,$trans,'Vendor Challan Trans');
                $ch_trans_id = $chresult['insert_id'];
            }
            $result['ch_trans_id'] = $ch_trans_id;
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function saveReceiveChallan($data){
        try{
            $this->db->trans_begin();
            
            $challanData = [
                'id'=>'',
                'type'=>2,
                'in_challan_no'=>$data['in_challan_no'],
                'challan_id'=>$data['challan_id'],
                'in_challan_date'=>$data['log_date'],
                'ref_id'=>$data['ref_id'],
                'item_id'=>$data['product_id'],
                'job_card_id'=>$data['job_card_id'],
                'job_approval_id'=>$data['job_approval_id'],
                'process_id'=>$data['process_id'],
                'qty'=>$data['production_qty'],
                'auto_log_id'=>$data['auto_log_id'],
            ];
            
            $result = $this->store($this->vendor_challan_trans,$challanData);
            $setData = array();
            $setData['tableName'] = $this->vendor_challan_trans;
            $setData['where']['id'] = $data['ref_id'];
            $setData['set']['return_qty'] = "return_qty, + " . $data['production_qty'];
            $this->setValue($setData);
            $data['ref_id'] = $result['insert_id'];
            // unset($data['challan_id']);
            $logData = $this->productionLog->save($data);
            
            if ($this->db->trans_status() !== FALSE ):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function getNextReceiveNo(){
        $data['select'] = "MAX(trans_no) as transNo";
        $data['tableName'] = "vendor_receive";
		$transNo = $this->specificRow($data)->transNo;
		$nextTransNo = (!empty($transNo))?($transNo + 1):1;
		return $nextTransNo;
    }

    public function checkDuplicateInChNo($data){
        $queryData['tableName'] = 'vendor_receive';
        $queryData['leftJoin']['vendor_challan'] = 'vendor_challan.id = vendor_receive.challan_id';
		if(!empty($data['in_challan_no'])):
            $queryData['where']['vendor_receive.in_challan_no'] = $data['in_challan_no']; 
        endif;
        
        if(!empty($data['vendor_id'])):
            $queryData['where']['vendor_challan.vendor_id'] = $data['vendor_id']; 
        endif;
        $queryData['resultType'] = "numRows";
        return $this->specificRow($queryData);
    }

    public function saveVendorReceive($data){
        try{
            $this->db->trans_begin();
            $chData = $this->jobWorkVendor_v3->getJobworkVendorDetail(['id'=>$data['challan_trans_id']]);
            /*$masterData = $this->getVendorChallanData($chData->challan_id);
            $data['vendor_id'] = $masterData->vendor_id;
            if ($this->checkDuplicateInChNo($data) > 0) :
				$errorMessage['in_challan_no'] = "In Challan No is duplicate.";
				return ['status' => 0, 'message' => $errorMessage];
            endif;*/
            $trans_no = $this->getNextReceiveNo();
            foreach($data['process_id'] As $key=>$process_id){
                $trans = [
                    'id'=>'',
                    'trans_no'=>$trans_no,
                    'challan_id'=>$chData->challan_id,
                    'ch_trans_id'=>$chData->id,
                    'process_id'=>$process_id,
                    'production_qty'=>$data['production_qty'][$key],
                    'without_prs_qty'=>$data['without_prs_qty'][$key],
                    'in_challan_no'=>$data['in_challan_no'],
                    'in_challan_date'=>$data['in_challan_date'],
                    'entry_type'=>$data['entry_type'],
                    'created_by'=>$this->loginId,
                    'created_at'=>date("Y-m-d H:i:s")
                ];
                $result = $this->store('vendor_receive',$trans);
            }
            if ($this->db->trans_status() !== FALSE ):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	

    }

    public function deleteVendorLog($data){
        try{
            $this->db->trans_begin();
            $data['customWhere'] = 'vendor_receive.accepted_by > 0';
            $logData = $this->getVendorReceive($data);
            if(!empty($logData)){
                 return ['status'=>0,'message'=>"You can not delete this log"];
            }
           
            
            $result = $this->trash('vendor_receive',['ch_trans_id'=>$data['ch_trans_id'],'trans_no'=>$data['trans_no']]);
            
            if ($this->db->trans_status() !== FALSE ):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function getVendorReceive($param){
        $data['tableName'] = 'vendor_receive';
        $data['select'] = "vendor_receive.*,process_master.process_name";
        $data['leftJoin']['process_master'] = 'process_master.id = vendor_receive.process_id';
        if(!empty($param['ch_trans_id'])){$data['where']['ch_trans_id'] = $param['ch_trans_id'];}
        if(!empty($param['in_challan_no'])){$data['where']['in_challan_no'] = $param['in_challan_no'];}
        if(!empty($param['trans_no'])){$data['where']['trans_no'] = $param['trans_no'];}
        if(!empty($param['customWhere'])){
                $data['customWhere'][] = $param['customWhere'];
        }
        return $this->rows($data);
    }
    public function acceptChallan($data){
        try{
            $this->db->trans_begin();
            $receiveTrans = $this->getVendorReceive(['ch_trans_id'=>$data['ch_trans_id'],'in_challan_no'=>$data['in_challan_no'],'trans_no'=>$data['trans_no']]);
            
            $chData = $this->jobWorkVendor_v3->getJobworkVendorDetail(['id'=>$data['ch_trans_id']]);
            $chMaster = $this->jobWorkVendor_v3->getVendorChallanData($chData->challan_id);
            $key=0;
            foreach($receiveTrans As $row){

                $this->store("vendor_receive",['id'=>$row->id,'accepted_by'=>$this->loginId,'accepted_at'=>date("Y-m-d")]);
                $ch_trans_id = $production_approval_id='';
                if($key == 0){
                    $receiveData = [
                        'id' =>'',
                        'prod_type' => 3,
                        'm_ct' =>'' ,
                        'part_code' => '',
                        'product_id' => $chData->item_id,
                        'job_approval_id' => $chData->job_approval_id,
                        'job_card_id' => $chData->job_card_id,
                        'process_id' => $row->process_id,
                        'ref_id' => $chData->id,
                        'log_date' => $row->in_challan_date,
                        'in_challan_no' => $row->in_challan_no,
                        'production_qty' => $row->production_qty,
                        'ok_qty' => $row->production_qty,
                        'challan_id' => $chData->challan_id,
                        'created_by' => $this->loginId,
                        'auto_log_id'=>$row->trans_no
                    ];

                    if((count($receiveTrans) - 1) > $key){
                        $receiveData['is_approve'] = $this->loginId;
                        $receiveData['approved_at'] = date("Y-m-d");
                    }
                    $this->saveReceiveChallan($receiveData);
                    $ch_trans_id = $chData->id;
                    $production_approval_id = $chData->job_approval_id;
                }else{

                    //Prev Process Movement Auto
                    if($receiveTrans[$key-1]->production_qty > 0){
                        $prevApproval = $this->processMovement->getApprovalData(['job_card_id'=>$chData->job_card_id,'in_process_id'=>$receiveTrans[$key-1]->process_id]);
                        $movementData = [
                            'id' => '',
                            'w_pcs' =>'',
                            'total_weight' => '',
                            'finished_weight' =>'',
                            'material_request' =>'',
                            'ref_id' => $prevApproval->id,
                            'job_card_no' => $prevApproval->job_no,
                            'job_card_id' => $prevApproval->job_card_id,
                            'product_id' => $prevApproval->product_id,
                            'in_process_id' => $prevApproval->in_process_id,
                            'out_process_id' =>$prevApproval->out_process_id,
                            'in_qty' =>$receiveTrans[$key-1]->production_qty,
                            'trans_ref_id' =>'',
                            'from_entry_type' => '',
                            'entry_date' =>$row->in_challan_date,
                            'out_qty' =>  $receiveTrans[$key-1]->production_qty,
                            'in_qty_kg' => '',
                            'send_to' =>1,
                            'remark' => '',
                            'created_by' => $this->loginId,
                            'auto_log_id'=>$row->trans_no
                        ];
                        $result = $this->processMovement->save($movementData);
                        
                        //Generate New Challan
                        $masterData = [
                            'id'=>'',
                            'trans_date' => $chMaster->trans_date,
                            'trans_prefix' => $chMaster->trans_prefix,
                            'trans_no' =>$chMaster->trans_no,
                            'trans_number' => $chMaster->trans_number,
                            'vendor_id' => $chMaster->vendor_id,
                            'material_data' =>$chMaster->material_data,
                            'ref_id' =>$chMaster->id,
                            'created_by' => $chMaster->created_by,
                            'accepted_by' => $chMaster->accepted_by,
                            'accepted_at' => $chMaster->accepted_at,
                            'auto_log_id'=>$row->trans_no
                        ];

                        $approvaldata = $this->processMovement->getApprovalData(['job_card_id'=>$chData->job_card_id,'in_process_id'=>$row->process_id]);
                        $transData = [];
                        $transData['id'][] = '';
                        $transData['type'] = 1;
                        $transData['process_id'] = $row->process_id;
                        $transData['process_ids']= $chData->process_ids;
                        $transData['challan_type'][] = 2;
                        $transData['job_approval_id'][] =$approvaldata->id;
                        $transData['jobwork_order_id'][$approvaldata->id] = $chData->jobwork_order_id;
                        $transData['qty'][] =$receiveTrans[$key-1]->production_qty;
                        $transData['w_pcs'][] = 0;
                        $transData['weight'][]= 0;
                        $transData['auto_log_id'][]= $row->trans_no;
                        $transData['created_by'] = $this->loginId;
                        
                        $chResult = $this->jobWorkVendor_v3->saveVendorChallan($masterData,$transData);

                        //Auto Receive Challan
                        $receiveData = [
                            'id' =>'',
                            'prod_type' => 3,
                            'm_ct' =>'' ,
                            'part_code' => '',
                            'product_id' => $chData->item_id,
                            'job_approval_id' => $approvaldata->id,
                            'job_card_id' => $chData->job_card_id,
                            'process_id' => $row->process_id,
                            'ref_id' => $chResult['ch_trans_id'],
                            'log_date' =>  $row->in_challan_date,
                            'in_challan_no' => $row->in_challan_no,
                            'production_qty' => $row->production_qty,
                            'ok_qty' => $row->production_qty,
                            'challan_id' => $chResult['id'],
                            'created_by' => $this->loginId,
                            'auto_log_id'=>$row->trans_no
                        ];
                        if((count($receiveTrans) - 1) > $key){
                            $receiveData['is_approve'] = $this->loginId;
                            $receiveData['approved_at'] = date("Y-m-d");
                        }
                        $this->saveReceiveChallan($receiveData);
                        $ch_trans_id =$chResult['ch_trans_id'];
                        $production_approval_id = $approvaldata->id;
                    }

                }

                if($row->without_prs_qty > 0){
                    $returnData = [
                        'id' => $ch_trans_id,
                        'pending_qty' =>$row->without_prs_qty,
                        'job_card_id' => $chData->job_card_id,
                        'production_approval_id' =>$production_approval_id,
                        'production_trans_id' => 0,
                        'process_id' => $row->process_id,
                        'product_id' =>  $chData->item_id,
                        'entry_date' => $row->in_challan_date,
                        'qty' =>$row->without_prs_qty,
                        'total_weight' => '',
                        'remark' => '',
                        'auto_log_id'=>$row->trans_no
                    ];
                    $this->jobWorkVendor_v3->saveJobWorkReturn($returnData);
                }
                $key++;
            }
            
            
            if ($this->db->trans_status() !== FALSE ):
                $this->db->trans_commit();
                return ['status'=>1,'message'=>'Accepted Successfully'];
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }
    public function deleteLog($data){
        try{
            $this->db->trans_begin();
            $logData = $this->getVendorReceiveTrans(['id'=>$data['id'],'single_row'=>1]);
            $chRcvData = $this->getJobworkVendorDetail(['id'=>$logData->ref_id]);
            $chOutData = $this->getJobworkVendorDetail(['id'=>$chRcvData->ref_id]);
            if($chOutData->challan_type == 1){
                $setData = array();
                $setData['tableName'] = $this->vendor_challan_trans;
                $setData['where']['id'] = $chRcvData->ref_id;
                $setData['set']['return_qty'] = "return_qty, - " . $logData->production_qty;
                $this->setValue($setData);
                /** vendor Receive Table Effect */
                $this->edit("vendor_receive",['ch_trans_id'=>$chRcvData->ref_id,'in_challan_no'=>$chRcvData->in_challan_no],['accepted_by'=>0,'accepted_at'=>null]);

                $this->trash($this->vendor_challan_trans,['id'=>$logData->ref_id]);
                $result =  $this->trash($this->production_log,['id'=>$data['id']]);
                $result['challan_id'] = $logData->challan_id;
                $result['job_approval_id'] = $logData->job_approval_id;
            }else{
                $receiveTrans = $this->getVendorReceive(['in_challan_no'=>$chRcvData->in_challan_no,'trans_no'=>$chOutData->auto_log_id]);
                foreach($receiveTrans As $row){
                    $logData = $this->getVendorReceiveTrans(['process_id'=>$row->process_id,'auto_log_id'=>$row->trans_no,'single_row'=>1]);
                  
                    $chRcvData = $this->getJobworkVendorDetail(['process_id'=>$row->process_id,'auto_log_id'=>$row->trans_no,'type'=>2]);
                   
                    $setData = array();
                    $setData['tableName'] = $this->vendor_challan_trans;
                    $setData['where']['id'] = $chRcvData->ref_id;
                    $setData['set']['return_qty'] = "return_qty, - " . $logData->production_qty;
                    $this->setValue($setData);

                    
                    $result =  $this->trash($this->production_log,['id'=>$logData->id]);
                    if($row->without_prs_qty > 0){
                        $returnResult = $this->deleteReturnTrans(['id'=>$chRcvData->ref_id,'qty'=>$row->without_prs_qty]);
                        if($returnResult['status'] == 0){
                            return $returnResult;
                        }
                    }
                    $this->productionLog->jobApprovalQtyEffect($logData->job_approval_id);
                    // TRASH MOVEMENT
                    $movementQuery['tableName'] = 'production_log';
                    $movementQuery['select'] = 'production_log.*';
                    $movementQuery['where']['production_log.prod_type'] = 4;
                    $movementQuery['where']['production_log.process_id'] = $row->process_id;
                    $movementQuery['where']['production_log.auto_log_id'] = $row->trans_no;
                    $movementQuery['where']['production_log.job_card_id'] = $logData->job_card_id;
                    $movementData = $this->row($movementQuery);
                    if(!empty($movementData)){
                        $movementResult = $this->processMovement->delete($movementData->id);
                        if($movementResult['status'] == 0){
                            return $movementResult;
                        }
                    }
                   

                    
                    $this->edit("vendor_receive",['ch_trans_id'=>$chRcvData->ref_id,'in_challan_no'=>$chRcvData->in_challan_no],['accepted_by'=>0,'accepted_at'=>null]);

                    $result['challan_id'] = $logData->challan_id;
                    $result['job_approval_id'] = $logData->job_approval_id;
                }
                $this->trash($this->vendor_challan_trans,['auto_log_id'=>$chOutData->auto_log_id],'Vendor Challan');
                $result = $this->trash($this->vendor_challan,['auto_log_id'=>$chOutData->auto_log_id],'Vendor Challan');
            }
            
            if ($this->db->trans_status() !== FALSE ):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }
    
	public function getVendorChallanData($id){
        $queryData['tableName'] = $this->vendor_challan;
        $queryData['select'] = "vendor_challan.*,party_master.party_name,party_master.party_address,party_master.gstin";
        $queryData['leftJoin']['party_master'] = "party_master.id = vendor_challan.vendor_id";
        $queryData['where']['vendor_challan.id'] = $id;
        return $this->row($queryData);
    }

    public function getJobworkVendorData($id){
        $data['tableName'] = $this->vendor_challan_trans;
        $data['select'] = "vendor_challan_trans.*,vendor_challan.trans_date,vendor_challan.trans_number,job_card.job_date,job_card.job_no,job_card.job_prefix,party_master.party_name,item_master.item_name,item_master.item_code,process_master.process_name,((vendor_challan_trans.return_qty * 100) / vendor_challan_trans.qty) as status_per,(vendor_challan_trans.qty - vendor_challan_trans.return_qty) as pending_qty";
        $data['leftJoin']['vendor_challan'] = "vendor_challan.id = vendor_challan_trans.challan_id";
        $data['leftJoin']['job_card'] = "vendor_challan_trans.job_card_id = job_card.id";
        $data['leftJoin']['party_master'] = "vendor_challan.vendor_id = party_master.id";
        $data['leftJoin']['item_master'] = "vendor_challan_trans.item_id = item_master.id";
        $data['leftJoin']['process_master'] = "vendor_challan_trans.process_id = process_master.id";
        $data['where']['vendor_challan_trans.challan_id'] = $id;
        return $this->row($data);
    }

    public function getJobworkVendorTrans($param){
        $data['tableName'] = $this->vendor_challan_trans;
        $data['select'] = "vendor_challan_trans.*,vendor_challan.trans_date,vendor_challan.trans_number,job_card.job_date,job_card.job_no,job_card.job_prefix,party_master.party_name,item_master.item_name,item_master.item_code,process_master.process_name,((vendor_challan_trans.return_qty * 100) / vendor_challan_trans.qty) as status_per,(vendor_challan_trans.qty - vendor_challan_trans.return_qty) as pending_qty,vendor_challan.remark as jwoRemark";
        $data['leftJoin']['vendor_challan'] = "vendor_challan.id = vendor_challan_trans.challan_id";
        $data['leftJoin']['job_card'] = "vendor_challan_trans.job_card_id = job_card.id";
        $data['leftJoin']['party_master'] = "vendor_challan.vendor_id = party_master.id";
        $data['leftJoin']['item_master'] = "vendor_challan_trans.item_id = item_master.id";
        $data['leftJoin']['process_master'] = "vendor_challan_trans.process_id = process_master.id";
        $data['where']['vendor_challan_trans.type'] = 1;
        if(!empty($param['challan_id'])){$data['where']['vendor_challan_trans.challan_id'] = $param['challan_id'];}
        if(!empty($param['id'])){$data['where']['vendor_challan_trans.id'] = $param['id'];}
        if(!empty($param['signle_row'])){
            return $this->row($data);
        }else{
            return $this->rows($data);
        }
        
    }
	

    public function getJobworkVendorDetail($param = array()){
        $data['tableName'] = $this->vendor_challan_trans;
        $data['select'] = "vendor_challan_trans.*";
        if(!empty($param['id'])){ $data['where']['vendor_challan_trans.id'] = $param['id']; }
        if(!empty($param['process_id'])){ $data['where']['vendor_challan_trans.process_id'] = $param['process_id']; }
        if(!empty($param['auto_log_id'])){ $data['where']['vendor_challan_trans.auto_log_id'] = $param['auto_log_id']; }
        if(!empty($param['type'])){ $data['where']['vendor_challan_trans.type'] = $param['type']; }
        return $this->row($data);
    }


    public function deleteChallan($id){
        try{
            $this->db->trans_begin();

            $challanData = $this->getVendorChallan($id);
            $invardData = $this->getJobworkVendorTrans(['challan_id'=>$id]);
            foreach($invardData as $row):
                $inward_qty = $row->return_qty + $row->without_process_qty;
                if( $inward_qty > 0){
                    return ['status'=>0,'message'=>'you can not delete this challan.'];
                }
            endforeach;
            $this->trash($this->vendor_challan_trans,['challan_id'=>$id],'Vendor Challan');
            $result = $this->trash($this->vendor_challan,['id'=>$id],'Vendor Challan');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }
    
    public function changeChallanStatus($data){
        try{
            $this->db->trans_begin();
            if($data['trans_status'] == 1){
                $data['accepted_at'] = date("Y-m-d H:i:s");
                $data['accepted_by'] = $this->loginId;
            }else{
                $data['accepted_at'] = NULL;
                $data['accepted_by'] = 0;
            }
            $result = $this->store('vendor_challan',$data);
            
            if ($this->db->trans_status() !== FALSE ):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

	/****************************************************** */
    
    public function saveJobWorkReturn($data){
        try{
            $this->db->trans_begin();
            $setData = array();
            $setData['tableName'] = $this->vendor_challan_trans;
            $setData['where']['id'] = $data['id'];
            $setData['set']['qty'] = "qty, - " . $data['qty'];
            $setData['set']['without_process_qty'] = "without_process_qty, + " . $data['qty'];
            $this->setValue($setData);

            $returnData = $this->getJobworkVendorDetail(['id'=>$data['id']]);
            $jsonData = array();
            if (!empty($returnData->return_json)) :
                $jsonData = json_decode($returnData->return_json);
                $jsonData[] = ['entry_date' => $data['entry_date'], 'qty' => $data['qty'], 'total_weight' => $data['total_weight'], 'remark' => $data['remark']];
            else :
                $jsonData[] = ['entry_date' => $data['entry_date'], 'qty' => $data['qty'], 'total_weight' => $data['total_weight'], 'remark' => $data['remark']];
            endif;
            $this->store($this->vendor_challan_trans, ['id' => $data['id'], 'return_json' => json_encode($jsonData)]);
            /*******************************************************/
            /** Remove From Transactions */
            /** Previous Process Data */
            $queryData = array();
            $queryData['tableName'] = $this->productionApproval;
            $queryData['where']['job_card_id'] = $data['job_card_id'];
            $queryData['where']['out_process_id'] = $data['process_id'];
            $prevPrsData = $this->row($queryData);

            $setData = array();
            $setData['tableName'] = $this->productionApproval;
            $setData['where']['id'] = $prevPrsData->id;
            $setData['set']['out_qty'] = "out_qty, - " . $data['qty'];
            $this->setValue($setData);

            $setData = array();
            $setData['tableName'] = $this->productionApproval;
            $setData['where']['id'] = $data['production_approval_id'];
            $setData['set']['in_qty'] = "in_qty, - " . $data['qty'];
            $setData['set']['inward_qty'] = "inward_qty, - " . $data['qty'];
            $setData['set']['ch_qty'] = "ch_qty, - " . $data['qty'];
            $this->setValue($setData);

        
            $transData = $this->getReturnTransaction($data['id']);
            $result =  ['status' => 1, 'message' => "Material returned successfully.", 'transHtml' => $transData['html'], 'pending_qty' => $transData['pending_qty']];
            if ($this->db->trans_status() !== FALSE ):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function deleteReturnTrans($data){
        try{
            $this->db->trans_begin();
            $returnData = $this->getJobworkVendorDetail(['id'=>$data['id']]);
            $queryData['tableName'] = $this->productionApproval;
            $queryData['where']['production_approval.out_process_id'] = $returnData->process_id;
            $queryData['where']['production_approval.job_card_id'] = $returnData->job_card_id;
            $prevData = $this->row($queryData);
           
            $jsonData = json_decode($returnData->return_json);
            $dataRow = new stdClass();
            if(!empty($data['key'])){
                $dataRow = $jsonData[$data['key']];
                unset($jsonData[$data['key']]);
            }elseif(!empty($data['qty'])){
                $dataRow->qty = $data['qty'];
            }
            

            if($dataRow->qty > ($prevData->total_ok_qty - $prevData->out_qty)){
                $transData = $this->getReturnTransaction($data['id']);
                return  ['status' => 0, 'message' => "You can not delete this log.", 'transHtml' => $transData['html'], 'pending_qty' => $transData['pending_qty']];
            }
            $setData = array();
            $setData['tableName'] = $this->vendor_challan_trans;
            $setData['where']['id'] = $data['id'];
            $setData['set']['qty'] = "qty, + " . $dataRow->qty;
            $setData['set']['without_process_qty'] = "without_process_qty, - " . $dataRow->qty;
            $this->setValue($setData);
            $jsonData = (!empty($jsonData)) ? json_encode($jsonData) : "";
            $this->store($this->vendor_challan_trans, ['id' => $data['id'], 'return_json' => $jsonData]);


            /** Remove From Transactions */
            /** Previous Process Data */
            $queryData = array();
            $queryData['tableName'] = $this->productionApproval;
            $queryData['where']['job_card_id'] = $returnData->job_card_id;
            $queryData['where']['out_process_id'] =$returnData->process_id;
            $prevPrsData = $this->row($queryData);

            $setData = array();
            $setData['tableName'] = $this->productionApproval;
            $setData['where']['id'] = $prevPrsData->id;
            $setData['set']['out_qty'] = "out_qty, + " . $dataRow->qty;
            $this->setValue($setData);

            $setData = array();
            $setData['tableName'] = $this->productionApproval;
            $setData['where']['id'] =$returnData->job_approval_id;
            $setData['set']['in_qty'] = "in_qty, + " . $dataRow->qty;
            $setData['set']['inward_qty'] = "inward_qty, + " . $dataRow->qty;
            $setData['set']['ch_qty'] = "ch_qty, + " . $dataRow->qty;
            $this->setValue($setData);

            $transData = $this->getReturnTransaction($data['id']);
            $result =  ['status' => 1, 'message' => "Transaction Removed successfully.", 'transHtml' => $transData['html'], 'pending_qty' => $transData['pending_qty']];
            if ($this->db->trans_status() !== FALSE ):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }


    public function getReturnTransaction($id){
        $returnData = $this->getJobWorkVendorRow($id);
        $jsonData = array();
        if(!empty($returnData->return_json)):
            $jsonData = json_decode($returnData->return_json);
            $transHtml = "";
            $i=1;
            foreach($jsonData as $key=>$row):
                $transHtml .= '<tr>
                    <td class="text-center" style="width:10%;">'.$i.'</td>
                    <td class="text-center">'.$row->entry_date.'</td>
                    <td class="text-center">'.$row->qty.'</td>
                    <td>'.$row->remark.'</td>
                    <td class="text-center" style="width:20%;">
                        <button type="button" onclick="trashReturn('.$returnData->id.','.$key.');" class="btn btn-outline-danger waves-effect waves-light permission-remove"><i class="ti-trash"></i></button>
                    </td>
                </tr>';
                $i++;
            endforeach;
        else:
            $transHtml =  '<tr><td colspan="5" class="text-center">No data available in table</td></tr>';
        endif;
        return ['html'=>$transHtml,'result'=>$jsonData,'pending_qty'=>floatVal($returnData->pending_qty)];
    }

    
    /* Vendor Challan */
	public function createVendorChallan($party_id){
		$queryData['tableName'] = $this->vendorProductionTrans;
		$queryData['select'] = "vendor_production_trans.*,process_master.process_name,item_master.item_name,item_master.item_code,job_card.job_no,job_card.job_prefix,party_master.party_name";
		$queryData['leftJoin']['process_master'] =  "process_master.id = vendor_production_trans.process_id";
		$queryData['leftJoin']['item_master'] =  "item_master.id = vendor_production_trans.product_id";
		$queryData['leftJoin']['job_card'] =  "job_card.id = vendor_production_trans.job_card_id";
		$queryData['leftJoin']['party_master'] = "party_master.id = vendor_production_trans.vendor_id";
		$queryData['where']['vendor_production_trans.challan_status'] = 0;
		$queryData['where']['vendor_production_trans.vendor_id'] = $party_id;
        //$queryData['where']['vendor_production_trans.job_order_id > ']  = 0;
		$resultData = $this->rows($queryData);
	
		$html="";
		if(!empty($resultData)):
			$i=1;
			foreach($resultData as $row):
				$html .= '<option value="' . $row->id . '" >['.$row->item_code.'] '.getPrefixNumber($row->job_prefix,$row->job_no).' (Out Qty.: '.floatVal($row->out_qty).', In Qty.: '.floatVal($row->in_qty).')</option>';
				$i++;
			endforeach;
		else:
			$html = '<tr><td class="text-center" colspan="7">No Data Found</td></tr>';
		endif;

		$materialData =  $this->packings->getConsumable(1);
		$mOption='<option value="">Select Material </option>';
		foreach($materialData as $row):
			$mOption.= '<option value="'.$row->id.'">'.$row->item_name.'</option>';
		endforeach; 

		return ['status'=>1,'htmlData'=>$html,'result'=>$resultData,'materialData'=>$mOption];
	}

    public function nextChallanNo(){
        $data['select'] = "MAX(challan_no) as challanNo";
        $data['tableName'] = $this->jobWorkChallan;
        $data['where']['version'] = 2;
        $data['where']['challan_date >= '] = $this->startYearDate;
        $data['where']['challan_date <= '] = $this->endYearDate;
		$challanNo = $this->specificRow($data)->challanNo;
		$nextChallanNo = (!empty($challanNo))?($challanNo + 1):1;
		return $nextChallanNo;
    }

    public function getVendorChallan($id){
        $queryData['tableName'] = $this->vendor_challan;
        $queryData['select'] = "vendor_challan.*,party_master.party_name,party_master.party_address,party_master.gstin";
        $queryData['leftJoin']['party_master'] = "party_master.id = vendor_challan.vendor_id";
        $queryData['where']['vendor_challan.id'] = $id;
        return $this->row($queryData);
    }

    public function saveReturnMaterial($data){
        try{
            $this->db->trans_begin();

            $data['id'] = (!empty($data['challan_id']) ? $data['challan_id'] : $data['id']);

            $transData = [
                'id' => $data['id'],
                'material_data' =>!empty( $data['material_data'])? $data['material_data']:''
            ];
            $result = $this->store($this->vendor_challan,$transData,'Return Material');
            
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function getVendorReceiveTrans($param){
        $data['tableName'] = $this->production_log;
        $data['select'] = "production_log.*";
        $data['leftJoin']['production_approval'] = "production_log.job_approval_id = production_approval.id";
        if(!empty($param['job_approval_id'])){$data['where']['production_log.job_approval_id'] = $param['job_approval_id'];}
        if(!empty($param['challan_id'])){$data['where']['production_log.challan_id'] = $param['challan_id'];}
        if(!empty($param['process_id'])){$data['where']['production_log.process_id'] = $param['process_id'];}
        if(!empty($param['auto_log_id'])){$data['where']['production_log.auto_log_id'] = $param['auto_log_id'];}
        if(!empty($param['ref_id'])){$data['where']['production_log.ref_id'] = $param['ref_id'];}
        if(!empty($param['id'])){$data['where']['production_log.id'] = $param['id'];}
        $data['where_in']['production_log.prod_type'] = 3;
        if(!empty($param['single_row'])){
            $result = $this->row($data);
        }else{
            $result = $this->rows($data);
        }
       
        return $result;
    }
    
    
    /* API Function Start */

    public function getJobWorkVendorList($data){
        $queryData['tableName'] = $this->vendorProductionTrans;
        $queryData['select'] = "vendor_production_trans.*,job_card.job_no,job_card.job_prefix,party_master.party_name,item_master.item_name,item_master.item_code,process_master.process_name,((vendor_production_trans.in_qty * 100) / vendor_production_trans.out_qty) as status_per,(vendor_production_trans.out_qty - vendor_production_trans.in_qty) as pending_qty";

        $queryData['leftJoin']['job_card'] = "vendor_production_trans.job_card_id = job_card.id";
        $queryData['leftJoin']['party_master'] = "vendor_production_trans.vendor_id = party_master.id";
        $queryData['leftJoin']['item_master'] = "vendor_production_trans.product_id = item_master.id";
        $queryData['leftJoin']['process_master'] = "vendor_production_trans.process_id = process_master.id";

        if($data['status'] == 0):
            $queryData['where']['((vendor_production_trans.in_qty * 100) / vendor_production_trans.out_qty) < '] = 100;
        endif;
        if($data['status'] == 1):
            $queryData['where']['((vendor_production_trans.in_qty * 100) / vendor_production_trans.out_qty) >= '] = 100;
            $queryData['where']['job_card.job_date >= '] = $this->startYearDate;
            $queryData['where']['job_card.job_date <= '] = $this->endYearDate;
        endif;
        
        $queryData['order_by']['vendor_production_trans.id'] = "DESC";

        if(!empty($data['search'])):
            $queryData['like']['CONCAT(job_card.job_prefix,job_card.job_no)'] = $data['search'];
            $queryData['like']['party_master.party_name'] = $data['search'];
            $queryData['like']['item_master.item_code'] = $data['search'];
            $queryData['like']['process_master.process_name'] = $data['search'];
        endif;

        $queryData['length'] = $data['limit'];
		$queryData['start'] = $data['off_set'];
        
        return $this->rows($queryData);
    }

    /**** START API Function ****/
    
    
    
    /*** END API V2 Function ****/
    
    public function getPendingChallans($data){ 
        $queryData['tableName'] = $this->vendor_challan;
        $queryData['select'] = "vendor_challan.id as challan_id,vendor_challan.trans_date as challan_date,vendor_challan.trans_number as challan_no, IFNULL(challan_trans.total_items,0) as total_items, vendor_challan.remark,employee_master.emp_name AS created_by_name";

        $queryData['leftJoin']['(SELECT challan_id, count(*) as total_items FROM vendor_challan_trans WHERE is_delete=0 GROUP BY challan_id) challan_trans'] = "vendor_challan.id = challan_trans.challan_id";
        
        $queryData['leftJoin']['employee_master'] = 'employee_master.id = vendor_challan.created_by';
        
        $queryData['where']['vendor_challan.trans_status'] = 0;
        
        if(!empty($data['vendor_id'])){$queryData['where']['vendor_challan.vendor_id'] = $data['vendor_id'];}
        
        if(!empty($data['from_date'])){ $queryData['where']['vendor_challan.trans_date >= '] = $data['from_date']; }
        if(!empty($data['to_date'])){ $queryData['where']['vendor_challan.trans_date <= '] = $data['to_date']; }
        
        $queryData['where']['vendor_challan.ref_id'] = 0;
        
        $queryData['order_by']['vendor_challan.trans_date'] = 'DESC';
        $queryData['order_by']['vendor_challan.id'] = "DESC";
        
		$result = $this->rows($queryData);
        // print_r($this->db->last_query());exit;
        return $result;
    }
    
    public function getChallanTransList($data){ 
        $queryData['tableName'] = $this->vendor_challan_trans;
        $queryData['select'] = "vendor_challan_trans.id, vendor_challan_trans.challan_id, vendor_challan_trans.qty, vendor_challan_trans.type,vendor_challan_trans.process_ids, vendor_challan.trans_date,vendor_challan.trans_number,vendor_challan.remark, item_master.item_name,item_master.item_code,process_master.process_name,((IFNULL(receive.receive_qty,0) * 100) / vendor_challan_trans.qty) as status_per,(vendor_challan_trans.qty - IFNULL(receive.receive_qty,0)) as pending_qty,vendor_challan.eway_bill_no, IFNULL(receive.receive_qty,0) AS receive_qty,GROUP_CONCAT(processes.process_name) AS process_names";

        $queryData['leftJoin']['vendor_challan'] = "vendor_challan.id = vendor_challan_trans.challan_id";
        //$queryData['leftJoin']['job_card'] = "vendor_challan_trans.job_card_id = job_card.id";
        //$queryData['leftJoin']['party_master'] = "vendor_challan.vendor_id = party_master.id";
        $queryData['leftJoin']['item_master'] = "vendor_challan_trans.item_id = item_master.id";
        $queryData['leftJoin']['process_master'] = "vendor_challan_trans.process_id = process_master.id";
        $queryData['leftJoin']['process_master AS processes'] = "FIND_IN_SET(processes.id,vendor_challan_trans.process_ids) > 0";
        $queryData['leftJoin']['(SELECT SUM(production_qty + without_prs_qty) As receive_qty,ch_trans_id,process_id FROM vendor_receive WHERE is_delete = 0 GROUP BY ch_trans_id,process_id)receive'] = "vendor_challan_trans.process_id = receive.process_id AND receive.ch_trans_id = vendor_challan_trans.id";

        $queryData['group_by'][] = 'vendor_challan_trans.id';
        if($data['status'] == 0):   // PENDING FOR ACCEPTANCE
            $queryData['where']['vendor_challan.trans_status'] = 0;
        endif;
        
        if($data['status'] == 1):   // PENDING FOR DISPATCH
            $queryData['where']['vendor_challan.trans_status'] = 1;
            $queryData['having'][] = '(vendor_challan_trans.qty - receive_qty) > 0';
        endif;
        
        if($data['status'] == 2):   // DISPATCHED
             $queryData['having'][] = '(vendor_challan_trans.qty - receive_qty) <= 0';
            //$data['where']['job_card.job_date >= '] = $this->startYearDate;
            //$data['where']['job_card.job_date <= '] = $this->endYearDate;
        endif;
        
        if(!empty($data['limit'])){ $queryData['limit'] = $data['limit']; }
        
		if(isset($data['start'])){ $queryData['start'] = $data['start']; }
		
		if(!empty($data['length'])){ $queryData['length'] = $data['length']; }
        
        if(!empty($data['vendor_id'])){$queryData['where']['vendor_challan.vendor_id'] = $data['vendor_id'];}
        
        if(!empty($data['from_date'])){ $queryData['where']['vendor_challan.trans_date >= '] = $data['from_date']; }
        if(!empty($data['to_date'])){ $queryData['where']['vendor_challan.trans_date <= '] = $data['to_date']; }
        
        $queryData['where']['vendor_challan_trans.type'] = 1;
        $queryData['where']['vendor_challan.ref_id'] = 0;
        
        $queryData['order_by']['vendor_challan.trans_date'] = 'DESC';
        $queryData['order_by']['vendor_challan_trans.id'] = "DESC";
        
		$result = $this->rows($queryData);
        // print_r($this->db->last_query());exit;
        return $result;
    }
    
    /* API V2 Function Start */
}
?>