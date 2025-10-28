<?php 

defined( 'BASEPATH' )OR exit( 'No direct script access allowed' );

//header('Content-Type:application/json');

if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin:*");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
}
// Access-Control headers are received during OPTIONS requests

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE,OPTIONS");
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers:{$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    exit(0);
}

class VisitorLogs extends CI_Controller{
    public function __construct(){
        parent::__construct();   
		$this->load->library('fcm');
        $this->load->model('masterModel');
		$this->load->model('NotificationModel','notification');
        $this->load->model('VisitorLogModel','visitorLog');
        $this->load->model('ItemModel','item');
        $this->load->model('hr/EmployeeModel','employee');
		$this->load->model('hr/DepartmentModel','department');
		$this->load->model('MasterDetailModel','masterDetail');
		$this->data = Array();
		
		$this->masterModel->loginId = 0;
		$this->notification->loginId = 0;
		$this->data['dates'] = getFinDates(date('Y-m-d'));
		$this->visitorLog->startYearDate = $this->data['dates'][0];
		$this->visitorLog->endYearDate = $this->data['dates'][1];
    }
    
    public function printJson($data){
		print json_encode($data);exit;
	}
	
	public function appointment(){
	    $this->load->view('hr/visitor/index',$this->data);
	}
	public function appointmentForm($jsonData=""){
        $data = (Array) decodeURL($jsonData);
		$this->data['contact_no'] = $data['contact_no'];
		$this->data['dataRow'] = $this->visitorLog->getLastVisitData($data);
        $this->data['empList'] = $this->employee->getEmpListForVisit(); //print_r($this->data['empList']);
		$this->data['purposeList'] = $this->masterDetail->getTypeforItem(9);
	    $this->load->view('hr/visitor/form',$this->data);
	}
	
	public function waitingPage($id=0){
	    if(empty($id)){redirect(base_url('api/v1/visitorLogs/appointment'));}
        $this->data['request_id'] = $id;
	    $this->load->view('hr/visitor/waiting_page',$this->data);
	}
	
	public function save(){
	    $data = $this->input->post();$data['id']='';
        $errorMessage = array();
		
        if(empty($data['vname']))
            $errorMessage['vname'] = "Name is required.";
        if(empty($data['contact_no']))
            $errorMessage['contact_no'] = "Contact is required.";
        if(empty($data['company_name']))
            $errorMessage['company_name'] = "Company is required.";
        if(empty($data['address']))
            $errorMessage['address'] = "Address is required.";
        if(empty($data['wtm']))
            $errorMessage['wtm'] = "Person is required.";
        if(empty($data['purpose']))
            $errorMessage['purpose'] = "Purpose is required.";

		if(empty($data['no_of_visitor']))
            $errorMessage['no_of_visitor'] = "required.";
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
			$result = $this->visitorLog->save($data);
			//print_r($result);exit;
			$this->printJson($result);
		endif;
	}
	
	public function checkForApproval(){
	    $data = $this->input->post();
		$result = $this->visitorLog->checkForApproval($data);
		if(!empty($result->approved_at)):
			// n2a(date('m'))
			$this->printJson(['status'=>1,'approved_at'=>$result->approved_at,'visit_number'=>$result->visit_number]);
		else:
			$this->printJson(['status'=>0,'approved_at'=>'']);
		endif;
	}
	
	public function getEmployeeList(){
	    $data = $this->input->post();
		$result = $this->employee->getEmpListForVisit($data);
		$empList = '<option value="">Select Person</option>';
		if(!empty($result)){foreach($result as $row){$empList .= '<option value="'.$row->id.'">'.$row->emp_name.'</option>';}}
		
		$this->printJson(['status'=>1,'empList'=>$empList]);
		
	}
	
	public function exit(){
	    $this->load->view('hr/visitor/exit_index',$this->data);
	}

	public function exitCompany(){
        $data = $this->input->post();
		$data['visit_date'] = date("Y-m-d");
		$visitData = $this->visitorLog->getLastVisitData($data);
		if(empty($visitData)):
			$errorMessage = array();
			$errorMessage['contact_no'] = "Contact No is invalid.";
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
			$data['id'] = $visitData->id;
			$this->printJson($this->visitorLog->exitCompany($data));
		endif;
    }

	
	public function exitPage($id=0){
        $this->data['request_id'] = $id;
	    $this->load->view('hr/visitor/exit_page',$this->data);
	}
}

?>