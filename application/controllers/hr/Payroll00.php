<?php
class Payroll extends MY_Controller
{
    private $indexPage = "hr/payroll/index";
    private $payrollForm = "hr/payroll/form";
    private $payrollDataPage = "hr/payroll/payroll_data";
	
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Payroll";
		$this->data['headData']->controller = "hr/payroll";
		$this->data['headData']->pageUrl = "hr/payroll";
	}
	
	public function index(){
        $this->data['tableHeader'] = getHrDtHeader('payroll');
        $this->load->view($this->indexPage,$this->data);
    }
	
	public function getDTRows(){
        $result = $this->payroll->getDTRows($this->input->post());
		$sendData = array();$i=1;
        foreach($result['data'] as $row):      
			$row->sr_no = $i++;
			$row->salary_sum = $this->payroll->getSalarySumByMonth($row->month)->salary_sum;
            $sendData[] = getPayrollData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }
	
    public function loadSalaryForm(){
        $this->data['empData'] = $this->payroll->getEmpSalary();
        $this->load->view($this->payrollForm,$this->data);
    }
    
    public function getPayrollData($month){
        $this->data['empData'] = $this->payroll->getPayrollData($month);
        $this->load->view($this->payrollDataPage,$this->data);
    }
    public function makeSalary(){
        $this->data['empData'] = $this->payroll->getEmpSalary();
        $this->load->view($this->payrollForm,$this->data);
    }
    
    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['month']))
            $errorMessage['month'] = "Month is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->payroll->save($data));
        endif;
    }

    public function edit(){
        $month = $this->input->post('month');
        $this->data['empData'] = $this->payroll->getEmpSalary();
        $this->data['empData'] = $this->payroll->getPayrollData($month);
        $this->load->view($this->payrollForm,$this->data);
    }

    public function delete()
    {
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->payroll->delete($id));
        endif;
    }
    
}
?>