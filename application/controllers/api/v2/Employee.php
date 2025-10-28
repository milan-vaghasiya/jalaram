<?php
class Employee extends MY_Apicontroller{
    public function __construct(){
        parent::__construct();        
        $this->data['headData']->pageTitle = "Employee";
        $this->data['headData']->pageUrl = "api/v2/employee";
        $this->data['headData']->base_url = base_url();
    }

    public function getEmployeeDetail(){
        $empDetail = $this->employee->getEmployee($this->loginId);
        unset($empDetail->emp_password,$empDetail->emp_psc,$empDetail->auth_token,$empDetail->web_token,$empDetail->device_token);
        $empDetail->emp_profile = base_url("assets/uploads/emp_profile/".((!empty($empDetail->emp_profile))?$empDetail->emp_profile:"user_default.png"));
        $this->data['empData'] = $empDetail;
        $this->printJson(['status'=>1,'message'=>'Data Found.','data'=>$this->data]);
    }

    public function changePassword(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['old_password']))
            $errorMessage['old_password'] = "Old Password is required.";
        if(empty($data['new_password']))
            $errorMessage['new_password'] = "New Password is required.";
        if(empty($data['cpassword']))
            $errorMessage['cpassword'] = "Confirm Password is required.";
        if(!empty($data['new_password']) && !empty($data['cpassword'])):
            if($data['new_password'] != $data['cpassword'])
                $errorMessage['cpassword'] = "Confirm Password and New Password is Not match!.";
        endif;

        if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
            $data['id'] = $this->loginId;
			$result =  $this->employee->changePassword($this->loginId,$data);
			$this->printJson($result);
		endif;
    }
}
?>