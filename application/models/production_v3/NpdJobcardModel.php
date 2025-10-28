<?php
class NpdJobcardModel extends MasterModel{
    private $npd_job_trans = "npd_job_trans";
    private $jobMaterialDispatch = "job_material_dispatch";
    private $job_card = "job_card";
    private $stockTransaction = "stock_transaction";

	public function getDTRows($data){        
        $data['tableName'] = $this->job_card;
        $data['select'] = "job_card.*,item_master.item_name,item_master.item_code,job_card.id as job_card_id";
        $data['leftJoin']['item_master'] = "item_master.id = job_card.product_id";
        $data['where']['job_card.job_category'] = 2;
        
        // Pending
        if(isset($data['status']) && $data['status'] == 0){
            $data['where_in']['job_card.order_status'] = [0,1,2];
        }
        // Completed
        if(isset($data['status']) && $data['status'] == 1){
            $data['where']['job_card.order_status'] = 4;
        }
        
        $data['where']['job_card.job_date >= '] = $this->startYearDate;
        $data['where']['job_card.job_date <= '] = $this->endYearDate;
        
        
        $data['order_by']['job_card.job_date'] = "DESC";
        $data['order_by']['job_card.id'] = "DESC";

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "CONCAT(job_card.job_prefix,'/',job_card.job_no,'/')";
        $data['searchCol'][] = "DATE_FORMAT(job_card.job_date,'%d-%m-%Y')";
        $data['searchCol'][] = "item_master.item_code";
        $data['searchCol'][] = "job_card.qty";
        $data['searchCol'][] = "job_card.remark";

		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		$result = $this->pagingRows($data);
        return $result;
    }
	
    public function save($data){
        try{
            $this->db->trans_begin();  
            $jobCardData = array();  
            if(!empty($data['id'])):
                $jobCardData = $this->getNpdJobCard($data['id']);
                if(!empty($jobCardData->md_status) && empty($jobCardData->ref_id)):
                    return ['status'=>2,'message'=>"Production In-Process. You can't update this job card."];
                endif;

                if(!empty($jobCardData->ref_id) && !empty($jobCardData->order_status)):
                    return ['status'=>2,'message'=>"Production In-Process. You can't update this job card."];
                endif;

            else:
                $job_prefix = "NJC/".$this->shortYear.'/';
                $data['job_no'] = $this->jobcard_v3->getNextJobNo(2);
                $data['job_prefix'] = $job_prefix;
            endif;
            $rm_item_id = $data['rm_item_id'];
            $rm_req_qty = $data['rm_req_qty'];
           // unset($data['rm_item_id'],$data['rm_req_qty']);
            $saveJobCard = $this->store($this->job_card,$data,'Job Card');
            $job_card_id = !empty($data['id'])?$data['id']:$saveJobCard['insert_id'];
            /** Material Request */
            $rmReqData = [
                'material_type' => 1,
                'issue_type' => 4,
                'job_card_id' => $job_card_id,
                'req_date' => formatDate($data['job_date'],'Y-m-d'),
                'req_item_id' => $rm_item_id,
                'req_qty' => $rm_req_qty,
                
            ];
            if(empty($data['id'])){
                $rmReqData["id"] = "";
                $rmReqData["created_by"] = $this->loginId;
                $rmReqData["created_at"] = date("Y-m-d H:i:s");
                $this->store($this->jobMaterialDispatch,$rmReqData);
            }else{
                $rmReqData["updated_by"] = $this->loginId;
                $rmReqData["updated_at"] = date("Y-m-d H:i:s");
                $this->edit($this->jobMaterialDispatch,['job_card_id'=>$job_card_id,'issue_type'=>4],$rmReqData);
            }
            
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $saveJobCard;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	        
    }

    public function delete($id){
        try{
            $this->db->trans_begin();
            $this->trash($this->jobMaterialDispatch,['job_card_id'=>$id,'issue_type'=> 4,'is_delete'=>0]);
            $result = $this->trash($this->job_card,['id'=>$id],"Job Card");

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function changeNpdJobStatus($data) {
        $this->store($this->job_card, ['id'=> $data['id'], 'order_status' => $data['order_status']]);
        return ['status' => 1, 'message' => 'Npd Job Card' . $data['msg'] . ' successfully.'];
    }

    public function getNpdJobcard($id){
        $data['tableName'] = $this->job_card;
        $data['select'] = 'job_card.*,item_master.item_code,item_master.item_name';
        $data['leftJoin']['item_master'] = 'item_master.id = job_card.product_id';
        $data['where']['job_card.id'] = $id;
        return $this->row($data);
    }

    public function saveLogDetail($data){
        try{
            $this->db->trans_begin();
            $setData = array();
            $setData['tableName'] = $this->job_card;
            $setData['where']['id'] = $data['job_card_id'];
            $setData['set']['total_reject_qty'] = 'total_reject_qty, + ' . (!empty($data['rejection_qty'])?$data['rejection_qty']:0);
            $this->setValue($setData);
    
            $result = $this->store($this->npd_job_trans,$data,'Log Item');
           
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function getNpdJobTransDetail($id){
        $data['tableName'] = $this->npd_job_trans;
        $data['select'] = 'npd_job_trans.*,item_master.item_name,employee_master.emp_name,process_master.process_name';
        $data['leftJoin']['item_master'] = 'item_master.id = npd_job_trans.machine_id';
        $data['leftJoin']['employee_master'] = 'employee_master.id = npd_job_trans.operator_id';
        $data['leftJoin']['process_master'] = 'process_master.id = npd_job_trans.process_id';
        $data['where']['npd_job_trans.job_card_id'] = $id;		
        return $this->rows($data);
    }

    public function getNpdJobTrans($id){
        $data['tableName'] = $this->npd_job_trans;
        $data['select'] = 'npd_job_trans.*,';
        $data['where']['npd_job_trans.id'] = $id;		
        return $this->row($data);
    }

    public function deleteLogTransdetail($data){
        try{
            $this->db->trans_begin();

            $jobData = $this->getNpdJobTrans($data['id']);
            $setData = array();
            $setData['tableName'] = $this->job_card;
            $setData['where']['id'] = $jobData->job_card_id;
            $setData['set']['total_reject_qty'] = 'total_reject_qty, - ' . $jobData->rejection_qty;
            $this->setValue($setData);

            $result = $this->trash($this->npd_job_trans,['id'=>$data['id']],'Log Item');
            
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
        return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function getStoreLocationTrans($id){
        $queryData['tableName'] = $this->stockTransaction;
        $queryData['select'] = "stock_transaction.*,location_master.store_name,location_master.location";
        $queryData['leftJoin']['location_master'] = "stock_transaction.location_id = location_master.id";
        $queryData['where']['stock_transaction.ref_type'] = 44;
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

    public function saveStoreLocation($data){
        try {
            $this->db->trans_begin();

            $stockTrans = [
                'id' => "",
                'location_id' => $data['location_id'],
                'trans_type' => 1,
                'item_id' => $data['product_id'],
                'qty' => $data['qty'],
                'ref_type' => 44,
                'ref_id' => $data['job_id'],
                'ref_no' => $data['batch_no'],
                'ref_date' => $data['trans_date'],
                'created_by' => $data['created_by']
            ];
            if($data['location_id'] == $this->HLD_STORE->id){
                $stockTrans['stock_effect']=0;
            }
            if (!empty($data['batch_no'])){
                $stockTrans['batch_no'] = $data['batch_no'];
            }
            $stockSave = $this->store($this->stockTransaction, $stockTrans);

            $setData = array();
            $setData['tableName'] = $this->job_card;
            $setData['where']['id'] = $data['job_id'];
            $setData['set']['total_out_qty'] = 'total_out_qty, + ' . $data['qty'];
            $this->setValue($setData);


            $jobCardData = $this->jobcard_v3->getJobCard($data['job_id']); 
            $totalQty = $jobCardData->total_reject_qty + $jobCardData->total_out_qty;
            if ($totalQty >= $jobCardData->qty) :
                $this->store($this->job_card, ['id' => $jobCardData->id, 'order_status' => 4]);
            endif;

            $result = ['status' => 1, 'message' => "Stock Transfer successfully.", 'htmlData' => $this->getStoreLocationTrans($data['job_id'])['htmlData'], 'unstored_qty' => ($jobCardData->qty - ($jobCardData->total_reject_qty + $jobCardData->total_out_qty))];

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function deleteStoreLocationTrans($id){
        try {
            $this->db->trans_begin();

            $queryData['tableName'] = $this->stockTransaction;
            $queryData['where']['id'] = $id;
            $stockTrans = $this->row($queryData);

            if (!empty($stockTrans)) :
                $setData = array();
                $setData['tableName'] = $this->job_card;
                $setData['where']['id'] = $stockTrans->ref_id;
                $setData['set']['total_out_qty'] = 'total_out_qty, - ' . $stockTrans->qty;
                $this->setValue($setData);

                $jobCardData = $this->jobcard_v3->getJobCard($stockTrans->ref_id);
                    
                $this->store($this->job_card, ['id' => $jobCardData->id, 'order_status' => 2]);
                $this->remove($this->stockTransaction, ['id' => $id]);
                $this->remove($this->stockTransaction,['trans_type'=>2,'ref_type'=>44,'ref_id'=> $stockTrans->ref_id]);
                
                $result = ['status' => 1, 'message' => 'Stock Transaction deleted successfully.', 'htmlData' => $this->getStoreLocationTrans($stockTrans->ref_id)['htmlData'], 'unstored_qty' => ($jobCardData->qty - ($jobCardData->total_reject_qty + $jobCardData->total_out_qty)),'ref_id'=>$stockTrans->ref_id];
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
}
?>