<?php
class PayrollModel extends MasterModel{
    private $payrollTrans = "payroll_transaction";
    private $empMaster = "employee_master";
    private $empSalary = "emp_salary_detail";
    
	public function getDTRows($data){		
		$data['tableName'] = $this->payrollTrans;
        $data['group_by'][] = "month";
		return $this->pagingRows($data);
    }

    public function getEmployeeListForSalary($data){
        $queryData = array();
        $queryData['tableName'] = $this->empMaster;
        $queryData['where']['emp_dept_id'] = $data['dept_id'];
        $queryData['where']['emp_type'] = $data['emp_type'];
        return $this->rows($queryData);
    }
    
    public function getEmpSalary(){
        $data['select'] = "emp_salary_detail.*,employee_master.emp_name,employee_master.emp_designation,emp_designation.title";
		$data['join']['employee_master'] = "employee_master.id = emp_salary_detail.emp_id";
		$data['join']['emp_designation'] = "emp_designation.id = employee_master.emp_designation";
        $data['where']['emp_salary_detail.basic_salary >']=0;
		$data['tableName'] = $this->empSalary;
        return $this->rows($data);
    }

	public function getPayrollData($month){
		$data['select'] = "payroll_transaction.*,employee_master.emp_name,employee_master.emp_designation,emp_designation.title";
		$data['join']['employee_master'] = "employee_master.id = payroll_transaction.emp_id";
		$data['join']['emp_designation'] = "emp_designation.id = employee_master.emp_designation";
        $data['where']['payroll_transaction.month']=$month;
        $data['tableName'] = $this->payrollTrans;
		return $this->rows($data);
    }

	public function getSalarySumByMonth($month){
		$data['select'] = "SUM(net_salary) as salary_sum";
        $data['where']['month']=$month;
        $data['tableName'] = $this->payrollTrans;
		return $this->row($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();
            $result = array();
            /* foreach($data['emp_id'] as $key=>$value):
                $salaryData = [
                    'id' => $data['id'][$key],
                    'month' => $data['month'],
                    'ledger_id' => $data['ledger_id'],
                    'emp_id' => $data['emp_id'][$key],
                    'basic_salary' => $data['basic_salary'][$key],
                    'hra' => $data['hra'][$key],
                    'ta' => $data['ta'][$key],
                    'da' => $data['da'][$key],
                    'oa' => $data['oa'][$key],
                    'bonus_amount' => $data['bonus_amount'][$key],
                    'pf_amount' => $data['pf_amount'][$key],
                    'prof_tax' => $data['prof_tax'][$key],
                    'other_deduction' => $data['other_deduction'][$key],
                    'present_days' => $data['present_days'][$key],
                    'absent_days' => $data['absent_days'][$key],
                    'leave_loss' => $data['leave_loss'][$key],
                    'net_salary' => $data['net_salary'][$key],
                    'remark' => $data['remark'][$key],
                    'created_by' => $data['created_by']
                ];

                $result = $this->store($this->payrollTrans,$salaryData,'Payroll');
            endforeach; */

            foreach($data['salary_data'] as $row):
                unset($row['emp_name']);
                $row['id'] = "";
                $row['ledger_id'] = $data['ledger_id'];
                $row['month'] = $data['month'];
                $row['dept_id'] = $data['dept_id'];
                $row['emp_type'] = $data['emp_type'];
                $row['created_by'] = $data['created_by'];

                $result = $this->store($this->payrollTrans,$row,'Payroll');
            endforeach;

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
        return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }


    public function delete($id){
        try{
            $this->db->trans_begin();
        $result = $this->trash($this->payrollTrans,['id'=>$id],'Payroll');
        if ($this->db->trans_status() !== FALSE):
            $this->db->trans_commit();
            return $result;
        endif;
    }catch(\Exception $e){
        $this->db->trans_rollback();
       return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
    }	
    }
	
	/*  Create By : Avruti @26-11-2021 5:00 PM
    update by : 
    note : 
*/

     //---------------- API Code Start ------//

     public function getCount(){
        $data['tableName'] = $this->payrollTrans;
        return $this->numRows($data);
    }

    public function getPayrollList_api($limit, $start){	
		$data['tableName'] = $this->payrollTrans;
        $data['length'] = $limit;
        $data['start'] = $start;
        return $this->rows($data);
    }

    //------ API Code End -------//
}
?>