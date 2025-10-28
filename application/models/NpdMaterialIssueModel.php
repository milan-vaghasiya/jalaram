<?php
class NpdMaterialIssueModel extends MasterModel
{
    private $jobMaterialDispatch = "job_material_dispatch";
    private $itemMaster = "item_master";

   
    /*** Npd Material Issue Request Data */
    public function getNpdRequestData($data)
    {
        $data['tableName'] = $this->jobMaterialDispatch;
        $data['select'] = "job_material_dispatch.*,item_master.item_name,unit_master.unit_name,job_card.job_prefix,job_card.job_no,job_card.rm_item_id";//,stockTrans.stock_qty";
        $data['leftJoin']['job_card'] = "job_material_dispatch.job_card_id = job_card.id";
        $data['leftJoin']['item_master'] = "job_material_dispatch.req_item_id = item_master.id";
        $data['leftJoin']['unit_master'] = "item_master.unit_id = unit_master.id";
        //$data['leftJoin']['(SELECT SUM(qty) as stock_qty,item_id FROM stock_transaction WHERE is_delete = 0 GROUP BY item_id) as stockTrans'] = "stockTrans.item_id = job_card.rm_item_id";
        $data['where']['job_material_dispatch.issue_type'] = 4;
        
        if($data['status'] == 0){
            $data['customWhere'][] = '(job_material_dispatch.req_qty - job_material_dispatch.dispatch_qty) > 0';
        }else{
            $data['customWhere'][] = '(job_material_dispatch.req_qty - job_material_dispatch.dispatch_qty) <=  0';
        }            
        
        $data['searchCol'][] = "DATE_FORMAT(job_material_dispatch.req_date,'%d-%m-%Y')";
        $data['searchCol'][] = "CONCAT(job_card.job_prefix,'/',job_card.job_no,'/')";
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "job_material_dispatch.req_qty";
        $data['searchCol'][] = "job_material_dispatch.dispatch_qty";
        $data['searchCol'][] = "DATE_FORMAT(job_material_dispatch.dispatch_date,'%d-%m-%Y')";
        $data['searchCol'][] = "";

		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getNpdMaterialIssue($id)
    {
        $data['tableName'] = $this->jobMaterialDispatch;
        $data['select'] = "job_material_dispatch.*,item_master.id as item_id,item_master.item_name,unit_master.unit_name,job_card.id as job_card_id,job_card.job_prefix,job_card.job_no";
        $data['leftJoin']['job_card'] = "job_material_dispatch.job_card_id = job_card.id";
        $data['leftJoin']['item_master'] = "job_material_dispatch.req_item_id = item_master.id";
        $data['leftJoin']['unit_master'] = "item_master.unit_id = unit_master.id";
        $data['where']['job_material_dispatch.issue_type'] = 4;
        $data['where']['job_material_dispatch.id'] = $id;
        $result = $this->row($data);
        return $result;
    }

    public function saveNpdMaterial($data){
      

        try{
            $this->db->trans_begin();
                $this->remove('stock_transaction',['ref_id'=>$data['job_card_id'],'trans_ref_id'=>$data['id'],'ref_type'=>43]);
                $issueData = [
                    'id' => $data['id'],
                    'dispatch_date' => $data['dispatch_date'],
                    'dispatch_item_id' => $data['item_id'],
                    'dispatch_qty' => array_sum($data['dispatch_qty']),
                    'dispatch_by' => $data['dispatch_by'],
                    'remark' => $data['remark'],
                ];
                $saveIssueData = $this->store($this->jobMaterialDispatch,$issueData);
                foreach($data['batch_no'] as $key=>$value):
                    $stockTrans = [
                        'id' => "",
                        'location_id' => $data['location_id'][$key],
                        'batch_no' => $value,
                        'trans_type' => 2,
                        'item_id' => $data['item_id'],
                        'qty' => "-".$data['dispatch_qty'][$key],
                        'ref_type' => 43,
                        'ref_id' =>$data['job_card_id'],
                        'trans_ref_id' => $data['id'],
                        'ref_no'=> (!empty($data['job_no']) ? getPrefixNumber($data['job_prefix'],$data['job_no']) : ""),
                        'ref_date' => $data['dispatch_date'],
                        'created_by' => $data['dispatch_by']
                    ];
                    $this->store('stock_transaction',$stockTrans);  
                endforeach;
                
                if(!empty($data['job_card_id'])):
                    $this->edit('job_card',['id'=>$data['job_card_id'],'order_status'=>0],['order_status'=>1]);   
                endif;
                
                if ($this->db->trans_status() !== FALSE):
                    $this->db->trans_commit();
                    return ['status'=>1,'message'=>'Material Issue suucessfully.'];
                endif;
            
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	        
    }

    public function getNpdIssueBatchTrans($id){
        $data['tableName'] = "stock_transaction";
        $data['select'] = "stock_transaction.*,location_master.store_name,location_master.location";
        $data['leftJoin']['location_master'] = 'location_master.id = stock_transaction.location_id';
        $data['where']['stock_transaction.trans_ref_id'] = $id;
        $data['where']['stock_transaction.ref_type'] = 43;
        $data['where']['stock_transaction.trans_type'] = 2;
        return $this->rows($data);
    }
}
