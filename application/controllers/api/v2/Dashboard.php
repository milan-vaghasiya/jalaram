<?php
class Dashboard extends MY_Apicontroller{

	public function __construct(){
		parent::__construct();
        $this->data['headData']->pageTitle = "Dashboard";
        $this->data['headData']->pageUrl = "api/v2/dashboard";
        $this->data['headData']->base_url = base_url();
	}

    public function index(){
        $this->printJson(['status'=>1,'message'=>'Data Found.','data'=>$this->data]);
    }
}
?>