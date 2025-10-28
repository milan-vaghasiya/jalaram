<?php
class Employee extends MY_Controller{
	
	public function __construct()	{
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Profile";
		$this->data['headData']->controller = "app/employee";
	}
	
	public function index(){
		$this->data['headData']->appMenu = "app/employee";
		$this->data['empData'] = $this->employee->getEmp($this->loginId);
        $this->load->view('app/emp_profile',$this->data);
    }
}
?>