<?php
class UserConfig extends MY_Controller{
    private $indexPage = "user_config";
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "User Config";
		$this->data['headData']->controller = "userConfig";
        $this->data['headData']->pageUrl = "userConfig";
	}
	
	public function index(){
        $this->data['empList'] = $this->employee->getEmployeeList();
        $this->data['companyInfo'] = $this->masterModel->getCompanyInfo();
        $this->load->view($this->indexPage,$this->data);
    }

    public function getLoginConfig(){
        $data = $this->input->post();
        $empList = $this->employee->getEmployeeList();

        $options = "";$empIds = [];
        foreach($empList as $row):
            $selected = ($data['access_type'] == $row->access_type)?"selected":"";
            if(!empty($selected)): $empIds[] = $row->id; endif;
            $options .= '<option value="' . $row->id . '" '.$selected.'>[ '.$row->emp_code.' ] '.$row->emp_name.'</option>';
        endforeach;

        $this->printJson(['status'=>1,'empOptions'=>$options,'empIds'=>implode(",",$empIds)]);
    }

    public function save(){
        $data = $this->input->post();

        if(!empty($data['access_type'])):
            $this->db->where_in('id',$data['emp_id'],false);
            $this->db->update('employee_master',['access_type'=>$data['access_type']]);
        endif;

        $this->printJson(['status'=>1,'message'=>'User Configration saved successfully.']);
    }

    public function saveLoginConfig(){
        $data = $this->input->post();
        $errorMessage = [];

        if(empty($data['static_ip'])):
            if(empty($data['login_start_time'])):
                $errorMessage['login_start_time'] = "Start Time is required.";
            endif;
            if(empty($data['login_end_time'])):
                $errorMessage['login_end_time'] = "End Time is required.";
            endif;
        endif;
        if(empty($data['login_start_time']) && empty($data['login_end_time'])):
            if(empty($data['static_ip'])):
                $errorMessage['static_ip'] = "IP Address is required.";
            endif;
        endif;

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['login_start_time'] = (!empty($data['login_start_time']))?$data['login_start_time']:null;
            $data['login_end_time'] = (!empty($data['login_end_time']))?$data['login_end_time']:null;
            
            $this->db->where_in('id',1,false);
            $this->db->update('company_info',$data);        

            $this->printJson(['status'=>1,'message'=>'User Configration saved successfully.']);
        endif;
    }
}
?>