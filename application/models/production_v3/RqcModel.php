<?php
class RqcModel extends MasterModel
{
    private $ic_inspection = "ic_inspection";
    private $production_log = "production_log";
   
    public function getDTRows($data){
        $data['tableName'] = $this->ic_inspection;
        $data['select'] = "ic_inspection.*,item_master.item_name,item_master.full_name,item_master.item_code,employee_master.emp_name,job_card.job_no,job_card.job_prefix,job_card.order_status,job_card.process,process_master.process_name,party_master.party_name,GROUP_CONCAT(DISTINCT(job_used_material.batch_no)) as heat_no,production_log.in_challan_no,production_log.log_date,production_log.production_qty";
        $data['leftJoin']['production_log'] = "production_log.id = ic_inspection.log_id";
        $data['leftJoin']['employee_master'] = "ic_inspection.created_by = employee_master.id";
        $data['leftJoin']['job_card'] = "job_card.id = ic_inspection.grn_id";
        $data['leftJoin']['job_used_material'] = "job_card.id = job_used_material.job_card_id";
        $data['leftJoin']['item_master'] = "item_master.id = ic_inspection.item_id";
        $data['leftJoin']['party_master'] = "party_master.id = ic_inspection.party_id";
        $data['leftJoin']['process_master'] = "process_master.id = ic_inspection.grn_trans_id";
        $data['where']['ic_inspection.trans_type'] = 4;

        $data['where']['DATE_FORMAT(ic_inspection.created_at,"%Y-%m-%d") >='] = $this->startYearDate;
        $data['where']['DATE_FORMAT(ic_inspection.created_at,"%Y-%m-%d") <='] = $this->endYearDate;

        $data['group_by'][] = 'ic_inspection.id';
        $data['order_by']['ic_inspection.created_at'] = "DESC";
        $data['order_by']['ic_inspection.id'] = "DESC";

		$data['searchCol'][] = "";
		$data['searchCol'][] = "";
        $data['searchCol'][] = "DATE_FORMAT(ic_inspection.created_at,'%d-%m-%Y')";
        $data['searchCol'][] = "ic_inspection.trans_no";
        $data['searchCol'][] = "job_card.job_no";
        $data['searchCol'][] = "item_master.item_code";
        $data['searchCol'][] = "process_master.process_name";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "employee_master.emp_name";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "production_log.in_challan_no";
        $data['searchCol'][] = "DATE_FORMAT(production_log.log_date,'%d-%m-%Y')";
        $data['searchCol'][] = "production_log.production_qty";
        $data['searchCol'][] = "ic_inspection.remark";

        $columns = array('', '', 'ic_inspection.created_at', 'ic_inspection.trans_no', 'job_card.job_no', 'item_master.item_code', 'process_master.process_name', 'party_master.party_name', 'employee_master.emp_name', '', 'production_log.in_challan_no', 'production_log.log_date','production_log.production_qty', 'ic_inspection.remark');
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }
        $result = $this->pagingRows($data);
        return $result;
    }

    public function getPendingRqcDTRows($data){
        $data['tableName'] = $this->production_log;
        $data['select']="production_log.*,item_master.item_code,item_master.item_name,item_master.full_name,job_card.job_no,job_card.job_prefix,process_master.process_name,party_master.party_name,vendor_challan.vendor_id,GROUP_CONCAT(DISTINCT(job_used_material.batch_no)) as heat_no";
        $data['leftJoin']['job_card'] = "job_card.id = production_log.job_card_id";
        $data['leftJoin']['job_used_material'] = "job_card.id = job_used_material.job_card_id";
        $data['leftJoin']['process_master'] = "process_master.id = production_log.process_id";
        $data['leftJoin']['item_master'] = "item_master.id = production_log.product_id";
        $data['leftJoin']['vendor_challan'] = "vendor_challan.id = production_log.challan_id";
        $data['leftJoin']['party_master'] = "party_master.id = vendor_challan.vendor_id";
        $data['leftJoin']['ic_inspection'] = "production_log.id = ic_inspection.log_id AND ic_inspection.trans_type = 4 AND ic_inspection.is_delete = 0";
     
        $data['where']['production_log.prod_type'] = 3;
        $data['where_in']['production_log.stage_type'] = [2,7];
        $data['where_in']['job_card.order_status'] = [2,4];
        $data['customWhere'][] = 'production_log.id != IFNULL(ic_inspection.log_id,0)';
        
        //FOR OLD RQC DATA Without log id entry
        $data['where']['production_log.log_date >='] = '2024-07-31';
        
        //$data['group_by'][]="production_log.job_card_id,production_log.process_id,production_log.machine_id";
        $data['group_by'][]="production_log.id";
        $data['order_by']['production_log.log_date'] ='DESC';

		$data['searchCol'][] = "";
		$data['searchCol'][] = "";
        $data['searchCol'][] = "production_log.log_date";
		$data['searchCol'][] = "job_card.job_no";
        $data['searchCol'][] = "item_master.item_code";
        $data['searchCol'][] = "process_master.process_name";
        $data['searchCol'][] = "party_master.party_name";
		$data['searchCol'][] = "production_log.in_challan_no";
        $data['searchCol'][] = "production_log.production_qty";

        $columns = array('', '', 'job_card.job_no', 'item_master.item_code', 'process_master.process_name','party_master.party_name','production_log.in_challan_no','production_log.production_qty');
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }
        $result = $this->pagingRows($data); //$this->printQuery();
        return $result;
    }

    public function getPendingRqcData($postData){
        $data['tableName'] = $this->production_log;
        $data['select']="production_log.id,production_log.in_challan_no,production_log.production_qty, production_log.job_card_id,item_master.item_code,job_card.job_no,job_card.job_prefix,process_master.process_name,party_master.party_name,vendor_challan.vendor_id";
        $data['leftJoin']['job_card'] = "job_card.id = production_log.job_card_id";
        $data['leftJoin']['process_master'] = "process_master.id = production_log.process_id";
        $data['leftJoin']['item_master'] = "item_master.id = production_log.product_id";
        $data['leftJoin']['vendor_challan'] = "vendor_challan.id = production_log.challan_id";
        $data['leftJoin']['party_master'] = "party_master.id = vendor_challan.vendor_id";
        $data['leftJoin']['ic_inspection'] = "production_log.id = ic_inspection.log_id AND ic_inspection.trans_type = 4 AND ic_inspection.is_delete = 0";
        $data['customWhere'][] = 'production_log.id != IFNULL(ic_inspection.log_id,0)';
        
        //FOR OLD RQC DATA Without log id entry
        $data['where']['production_log.log_date >='] = '2024-07-31';
     
		$data['where']['production_log.job_card_id'] = $postData['job_card_id'];
        $data['where']['production_log.process_id'] = $postData['process_id'];
        $data['where']['vendor_challan.vendor_id'] = $postData['vendor_id'];
        $data['where']['production_log.prod_type'] = 3;
        $data['where_in']['production_log.stage_type'] = [2,7];
        $data['where_in']['job_card.order_status'] = [2,4];
        $data['group_by'][]="production_log.id";
        $data['order_by']['production_log.id'] ='DESC';

        return $this->rows($data);
    }
    
    public function save($data){
        try{
			$this->db->trans_begin();
			$pre_inspection = Array();$param_ids = Array();$data['observation_sample'] = '';$reportTime =[]; $postData =[];
			
			foreach($data['report_time'] as $row){
				if(!empty($row)){
					$reportTime[] = $row;
				}
			}
			
			if(!empty($data['log_id'])){
				foreach($data['log_id'] as $val){
					$data['trans_no'] = "";
					$rqc_job_id = explode("-",$val);
					$log_id = $rqc_job_id[0];
					$job_card_id = $rqc_job_id[1];
					if(empty($data['id'])){ $data['trans_no'] = $this->getNextTransNo(); }
					$jobData = $this->jobcard_v3->getJobcard($job_card_id); 
					$pfcProcess = $this->item->getPrdProcessDataProductProcessWise(['item_id' => $data['item_id'], 'process_id' => $data['grn_trans_id'],'pfc_rev_no'=>$jobData->pfc_rev_no]);
					$insParamData =  $this->controlPlanV2->getCPDimenstion(['item_id'=>$data['item_id'],'process_no'=>$pfcProcess->pfc_process,'control_method'=>'RQC','rev_no'=>$jobData->cp_rev_no]);
					
					if(!empty($insParamData)):
						$sample_size = $data['sampling_qty'];
						foreach($insParamData as $row):
							$param = Array();
							for($j = 1; $j <=$sample_size; $j++):
								$param[] = $data['sample'.$j.'_'.$row->id];
								// unset($data['sample'.$j.'_'.$row->id]);
							endfor;
							$pre_inspection[$row->id] = $param;
							$param_ids[] = $row->id;
						endforeach;
					endif;
					$postData = [
						'id'=>$data['id'],
						'log_id'=>$log_id,
						'third_party'=>(!empty($data['third_party'])) ? $data['third_party'] : '',
						'grn_id'=>$job_card_id,
						'trans_type'=>$data['trans_type'],
						'item_id'=>$data['item_id'],
						'party_id'=>$data['party_id'],
						'trans_no'=>$data['trans_no'],
						'trans_date'=>$data['trans_date'],
						'grn_trans_id'=>$data['grn_trans_id'],
						'observation_sample'=>json_encode($pre_inspection),
						'param_count'=>count($insParamData),
						'sampling_qty'=>count($reportTime),
						'result'=>!empty($reportTime)?implode(',',$reportTime):'',
						'parameter_ids'=>implode(',',$param_ids),
						'remark'=>$data['remark'],
						'created_by'=>$data['created_by']
					];
					$param_ids = [];
					if(!empty($data['id'])){unset($postData['trans_no']);}
					$result = $this->store($this->ic_inspection,$postData);
				}
			}
			$result['url']='production_v3/rqc';
			if ($this->db->trans_status() !== FALSE):
				$this->db->trans_commit();
				return $result;
			endif;
		}catch(\Exception $e){
			$this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
		}	
    }
    
    /*public function save($data){
        try{
			$this->db->trans_begin();
            if(empty($data['id'])){ $data['trans_no'] = $this->getNextTransNo(); }
			$result = $this->store($this->ic_inspection,$data);
			$result['url']='production_v3/rqc';
			if ($this->db->trans_status() !== FALSE):
				$this->db->trans_commit();
				return $result;
			endif;
		}catch(\Exception $e){
			$this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
		}	
    }*/
   
    public function getNextTransNo(){
        $data['tableName'] = $this->ic_inspection;
        $data['select'] = "MAX(trans_no) as trans_no";
        $data['where']['trans_type'] = 4;
        $maxNo = $this->specificRow($data)->trans_no;
		$nextIIRNo = (!empty($maxNo))?($maxNo + 1):1;
		return $nextIIRNo;
    }
    
    public function getRqcData($id){
		$data['tableName'] = $this->ic_inspection;
        $data['select'] = "ic_inspection.*,item_master.item_name,item_master.full_name,item_master.item_code,employee_master.emp_name,job_card.job_prefix,job_card.job_no,job_card.process,process_master.process_name,party_master.party_name,job_card.pfc_rev_no,job_card.cp_rev_no,approve.emp_name as approve_name,production_log.in_challan_no,production_log.log_date,production_log.production_qty";
        $data['leftJoin']['employee_master'] = "ic_inspection.created_by = employee_master.id";
        $data['leftJoin']['production_log'] = "production_log.id = ic_inspection.log_id";
		$data['leftJoin']['employee_master as approve'] = "ic_inspection.verify_by = approve.id";
        $data['leftJoin']['job_card'] = "job_card.id = ic_inspection.grn_id";
        $data['leftJoin']['item_master'] = "item_master.id = ic_inspection.item_id";
        $data['leftJoin']['party_master'] = "party_master.id = ic_inspection.party_id";
        $data['leftJoin']['process_master'] = "process_master.id = ic_inspection.grn_trans_id";
		$data['where']['ic_inspection.id'] = $id;    
		return $this->row($data);
	}

    public function delete($id){
        try{
			$this->db->trans_begin();
			$result = $this->trash($this->ic_inspection,['id'=>$id]);
			if ($this->db->trans_status() !== FALSE):
				$this->db->trans_commit();
				return $result;
			endif;
		}catch(\Exception $e){
			$this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
		}	
    }

    public function getRQCReports($postData){
        $data['tableName'] = $this->ic_inspection;
        $data['select'] = "ic_inspection.*,item_master.item_name,item_master.full_name,item_master.item_code,employee_master.emp_name,job_card.job_no,job_card.job_prefix,job_card.process,process_master.process_name,mc.item_name as machine_name,mc.item_code as machine_code";
        $data['leftJoin']['employee_master'] = "ic_inspection.created_by = employee_master.id";
        $data['leftJoin']['job_card'] = "job_card.id = ic_inspection.grn_id	";
        $data['leftJoin']['item_master'] = "item_master.id = ic_inspection.item_id";
        $data['leftJoin']['item_master as mc'] = "mc.id = ic_inspection.party_id";
        $data['leftJoin']['process_master'] = "process_master.id = ic_inspection.grn_trans_id";
		$data['where']['ic_inspection.grn_id	'] = $postData['job_card_id'];    
		$data['where']['ic_inspection.grn_trans_id'] = $postData['process_id'];    
		$data['where']['ic_inspection.party_id'] = $postData['machine_id'];
		$data['where']['ic_inspection.item_id'] = $postData['item_id'];    
        if(!empty($postData['trans_date'])){$data['where']['ic_inspection.trans_date'] = $postData['trans_date'];}
        if(isset($postData['singleRow']) && $postData['singleRow']==1){

            return $this->row($data);
        }else{
            
		return $this->rows($data);
        }
    }

	public function approveRqc($data){
		$date = ($data['val'] == 1)?date('Y-m-d'):"";
		$isApprove =  ($data['val'] == 1)?$this->loginId:0;
        $this->edit($this->ic_inspection, ['id'=> $data['id']],[ 'verify_by' => $isApprove, 'verify_at'=>$date]);
        return ['status' => 1, 'message' => 'RQC ' . $data['msg'] . ' successfully.'];
	}
}
