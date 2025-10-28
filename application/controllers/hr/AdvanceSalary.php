<?php
class AdvanceSalary extends MY_Controller
{
	private $indexpage = "hr/advance_salary/index";
    private $form = "hr/advance_salary/form";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Advance Salary";
		$this->data['headData']->controller = "hr/advanceSalary";
        $this->data['headData']->pageUrl = "hr/advanceSalary";
	}

    //view table
	public function index(){    
        $this->data['tableHeader'] = getHrDtHeader('advanceSalary');
        $this->load->view($this->indexpage,$this->data);
    }

    public function getDTRows(){
        $result = $this->advanceSalary->getDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;         
            $sendData[] = getAdvanceSalaryData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addAdvance()
    {
        $this->data['empData'] = $this->advanceSalary->getEmployeeList();
        $this->data['ledgerData'] = $this->party->getPartyListOnGroupCode(['"BA"','"CS"']);
        $this->load->view($this->form,$this->data);
    }

    public function save()
    {
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['emp_id']))
            $errorMessage['emp_id'] = "Employee is required.";
        if(empty($data['vou_acc_id']))
            $errorMessage['vou_acc_id'] = "Ledger is required.";
        if(empty($data['amount']))
            $errorMessage['amount'] = "Amount is required.";
        if(empty($data['reason']))
            $errorMessage['reason'] = "Reason is required.";
    
        // if(!empty($data['emp_id'])){
        //     $salarydata=$this->employee->getEmpSalary($data['emp_id']);
        //     if($data['amount'] > $salarydata->basic_salary)
        //         $errorMessage['amount'] = "Amount is bigger than basic salary.";
        // }
        if(!empty($errorMessage)):
                $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $partyData = $this->party->getParty($data['vou_acc_id']);
            $accountData = $this->party->getPartyListOnSystemCode("SAC");
            $trans_no = $this->transModel->nextTransNo(21);
            $trans_prefix = $this->transModel->getTransPrefix(21);
            unset($data['empSelect']);
            $masterData = [ 
				'id' => $data['id'],
				'entry_type' => 21,
				'trans_prefix'=>$trans_prefix, 
				'trans_no'=>$trans_no, 
				'doc_no'=>'', 
				'trans_mode'=>'', 
				'trans_number'=>$trans_prefix.$trans_no, 
				'trans_date' => date('Y-m-d',strtotime($data['date'])),  
				'doc_date' => date('Y-m-d',strtotime($data['date'])),  
				'party_id' => $data['vou_acc_id'],
                'opp_acc_id'=>$accountData->id,
                'vou_acc_id'=>$data['vou_acc_id'],
				'party_name' => $partyData->party_name,
				'sales_executive' => $data['emp_id'],
				'net_amount' => $data['amount'],
				'remark' => $data['reason'],
                'created_by' => $this->session->userdata('loginId')
			];
            //print_r($masterData);exit;
            $this->printJson($this->advanceSalary->save($masterData));
        endif;
    }
    
    public function edit()
    {
        $id = $this->input->post('id'); 
        $this->data['dataRow'] = $this->advanceSalary->getEmp($id);
        $this->data['empData'] = $this->advanceSalary->getEmployeeList();
        $this->data['ledgerData'] = $this->party->getPartyListOnGroupCode(['"BA"','"CS"']);
        $this->load->view($this->form,$this->data);
    }
    
    public function delete()
    {
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->advanceSalary->delete($id));
        endif;
    }
}
?>