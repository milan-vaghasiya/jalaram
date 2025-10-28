<?php
class EmpLoanModel extends MasterModel
{
    private $empMaster = "employee_master";
    private $transMain = "trans_main";
    
	public function getDTRows($data){
        $data['tableName'] = $this->transMain;
        $data['select'] = "trans_main.*,employee_master.emp_name,employee_master.emp_code";
        $data['leftJoin']['employee_master'] = "trans_main.sales_executive = employee_master.id";
        $data['where']['trans_main.entry_type'] =22;

        $data['searchCol'][] = "employee_master.emp_name";
        $data['searchCol'][] = "trans_main.trans_date";
        $data['searchCol'][] = "trans_main.net_amount";
        $data['searchCol'][] = "trans_main.remark";
        
        
		$columns =array('','','employee_master.emp_name','trans_main.trans_date','trans_main.net_amount','trans_main.remark');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getEmpLoan($id)
    {
        $data['where']['id'] = $id;
        $data['tableName'] = $this->transMain;
        return $this->row($data);
    }
    
	public function getEmployeeList()
    {
        $data['tableName'] = $this->empMaster;
        return $this->rows($data);
    }

    public function save($data)
    {
        try{
			$this->db->trans_begin();
			$result = $this->store($this->transMain,$data,'Loan');
			$data['id'] = (empty($data['id']))?$result['insert_id']:$data['id'];	

			$this->transModel->ledgerEffects($data);

			if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
		}catch(\Exception $e){
            $this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }              
    }

   

    public function delete($id)
    {
        try{
			$this->db->trans_begin();
			
			$result= $this->trash($this->transMain,['id'=>$id],'Loan');
			$this->transModel->deleteLedgerTrans($id);

			if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
		}catch(\Exception $e){
            $this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }        
    }
    
}
?>