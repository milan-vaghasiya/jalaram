<?php
class PirModel extends MasterModel
{
    private $ic_inspection = "ic_inspection";
    private $production_log = "production_log";
  
    public function getDTRows($data){
        $data['tableName'] = $this->ic_inspection;
		$data['select'] = "ic_inspection.*,item_master.item_name,item_master.full_name,item_master.item_code,employee_master.emp_name,job_card.job_no,job_card.job_prefix,job_card.order_status,job_card.process,process_master.process_name,mc.item_name as machine_name,mc.item_code as machine_code,emp.emp_name as operator_name";

        $data['leftJoin']['employee_master'] = "ic_inspection.created_by = employee_master.id";
        $data['leftJoin']['employee_master as emp'] = "ic_inspection.operator_id = emp.id";
        $data['leftJoin']['job_card'] = "job_card.id = ic_inspection.grn_id";
        $data['leftJoin']['item_master'] = "item_master.id = ic_inspection.item_id";
        $data['leftJoin']['item_master as mc'] = "mc.id = ic_inspection.party_id";
        $data['leftJoin']['process_master'] = "process_master.id = ic_inspection.grn_trans_id";
        $data['where']['ic_inspection.trans_type'] = 3;

        $data['where']['ic_inspection.trans_date >='] = $this->startYearDate;
        $data['where']['ic_inspection.trans_date <='] = $this->endYearDate;

        $data['order_by']['ic_inspection.trans_date'] = "DESC";
        $data['order_by']['ic_inspection.id'] = "DESC";

		$data['searchCol'][] = "";
		$data['searchCol'][] = "";
        $data['searchCol'][] = "DATE_FORMAT(ic_inspection.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "ic_inspection.trans_no";
        $data['searchCol'][] = "job_card.job_no";
        $data['searchCol'][] = "item_master.item_code";
        $data['searchCol'][] = "process_master.process_name";
        $data['searchCol'][] = "mc.item_code";
        $data['searchCol'][] = "emp.emp_name";
        $data['searchCol'][] = "employee_master.emp_name";
        $data['searchCol'][] = "ic_inspection.remark";

        $columns = array('','','ic_inspection.trans_date','ic_inspection.trans_no','job_card.job_no','item_master.item_code','process_master.process_name','mc.item_code','emp.emp_name','employee_master.emp_name','ic_inspection.remark');
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }
        $result = $this->pagingRows($data);
        return $result;
    }

    public function getPendingPirDTRows($data){
        $data['tableName'] = $this->production_log;
        $data['select']="production_log.*,item_master.item_code,item_master.item_name,item_master.full_name,job_card.job_no,job_card.job_prefix,process_master.process_name";//,employee_master.emp_name as operator_name,machine.item_name as machine_name,machine.item_code as machine_code";
        $data['leftJoin']['job_card'] = "job_card.id = production_log.job_card_id";
        $data['leftJoin']['process_master'] = "process_master.id = production_log.out_process_id";
        $data['leftJoin']['item_master'] = "item_master.id = production_log.product_id";
		//$data['leftJoin']['employee_master'] = "employee_master.id = production_log.operator_id";
        //$data['leftJoin']['item_master as machine'] = "machine.id = production_log.machine_id";
     
        $data['where']['process_master.is_pir'] = 1;
        $data['where']['production_log.prod_type'] = 4;
        $data['where']['production_log.stage_type'] = 2;
        $data['where']['job_card.order_status'] = 2;
        $data['group_by'][]="production_log.job_card_id,production_log.process_id,production_log.machine_id";
        $data['order_by']['production_log.job_card_id'] ='DESC';

		$data['searchCol'][] = "";
		$data['searchCol'][] = "";
        $data['searchCol'][] = "job_card.job_no";
        $data['searchCol'][] = "item_master.item_code";
        $data['searchCol'][] = "process_master.process_name";
        //$data['searchCol'][] = "employee_master.emp_name";
        //$data['searchCol'][] = "machine.item_name";
		$data['searchCol'][] = "";
		
        $columns = array('', '', 'job_card.job_no', 'item_master.full_name', 'process_master.process_name','');
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }
        $result = $this->pagingRows($data);
        return $result;
    }

    public function save($data){
        try{
			$this->db->trans_begin();
            if(empty($data['id'])){ $data['trans_no'] = $this->getNextTransNo(); }
			$result = $this->store($this->ic_inspection,$data);
			$result['url']='production_v3/pir';
			if ($this->db->trans_status() !== FALSE):
				$this->db->trans_commit();
				return $result;
			endif;
		}catch(\Exception $e){
			$this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
		}	
    }
   
    public function getNextTransNo(){
        $data['tableName'] = $this->ic_inspection;
        $data['select'] = "MAX(trans_no) as trans_no";
        $data['where']['trans_type'] = 3;
        $maxNo = $this->specificRow($data)->trans_no;
		$nextIIRNo = (!empty($maxNo))?($maxNo + 1):1;
		return $nextIIRNo;
    }
    
	public function getPirData($id){
		$data['tableName'] = $this->ic_inspection;
        $data['select'] = "ic_inspection.*,item_master.item_name,item_master.full_name,item_master.item_code,employee_master.emp_name,job_card.job_prefix,job_card.job_no,job_card.process,process_master.process_name,mc.item_name as machine_name,mc.item_code as machine_code,job_card.pfc_rev_no,job_card.cp_rev_no,approve.emp_name as approve_name,emp.emp_name as operator_name,emp.emp_code as operator_code";
        $data['leftJoin']['employee_master'] = "ic_inspection.created_by = employee_master.id";
		$data['leftJoin']['employee_master as approve'] = "ic_inspection.verify_by = approve.id";
		$data['leftJoin']['employee_master as emp'] = "ic_inspection.operator_id = emp.id";
        $data['leftJoin']['job_card'] = "job_card.id = ic_inspection.grn_id";
        $data['leftJoin']['item_master'] = "item_master.id = ic_inspection.item_id";
        $data['leftJoin']['item_master as mc'] = "mc.id = ic_inspection.party_id";
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

    public function getPIRReports($postData){
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

    /* Created By :- Sweta @28-08-2023 */
    public function getPirDataForPrint($postData){
		$data['tableName'] = $this->ic_inspection;
        $data['select'] = "ic_inspection.*,item_master.item_name,item_master.full_name,item_master.item_code,employee_master.emp_name,job_card.job_prefix,job_card.job_no,job_card.process,process_master.process_name,mc.item_name as machine_name,mc.item_code as machine_code,job_card.pfc_rev_no";
        $data['leftJoin']['employee_master'] = "ic_inspection.created_by = employee_master.id";
        $data['leftJoin']['job_card'] = "job_card.id = ic_inspection.grn_id";
        $data['leftJoin']['item_master'] = "item_master.id = ic_inspection.item_id";
        $data['leftJoin']['item_master as mc'] = "mc.id = ic_inspection.party_id";
        $data['leftJoin']['process_master'] = "process_master.id = ic_inspection.grn_trans_id";
        
        if(!empty($postData['id'])){ $data['where']['ic_inspection.id'] = $postData['id']; }
        if(!empty($postData['grn_id'])){ $data['where']['ic_inspection.grn_id'] = $postData['grn_id']; }
        if(!empty($postData['machine_id'])){ $data['where']['ic_inspection.party_id'] = $postData['machine_id']; }
		if(!empty($postData['grn_trans_id'])){ $data['where']['ic_inspection.grn_trans_id'] = $postData['grn_trans_id']; }
		if(!empty($postData['trans_date'])){ $data['where']['ic_inspection.trans_date'] = $postData['trans_date']; }

		return $this->rows($data); 
	}

	public function approvePir($data){
		$date = ($data['val'] == 1)?date('Y-m-d'):"";
		$isApprove =  ($data['val'] == 1)?$this->loginId:0;
        $this->edit($this->ic_inspection, ['id'=> $data['id']],[ 'verify_by' => $isApprove, 'verify_at'=>$date]);
        return ['status' => 1, 'message' => 'PIR ' . $data['msg'] . ' successfully.'];
	}
}
