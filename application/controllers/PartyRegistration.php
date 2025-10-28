<?php
class PartyRegistration extends CI_Controller{

	public function __construct(){
		parent::__construct();
		$this->load->model('LoginModel','login_model');
	}
	
	public function index(){
		$this->load->view('party_registration');
	}
	
	public function getRegistration($id=0){
	    $this->data['id'] = $id;
	    $this->data['dataRow']= $this->login_model->getPartyData($id);
		$this->load->view('party_registration',$this->data);
	}
	
	public function saveRegistration(){
        $data = $this->input->post(); 
        
        $errorMessage = array();
        if(empty($data['party_name']))
            $errorMessage['party_name'] = "Company name is required.";
            
        if(empty($data['party_category']))
            $errorMessage['party_category'] = "Party Category is required.";
            
        if(empty($data['contact_person']))
            $errorMessage['contact_person'] = "Contact Person is required.";
            
        if(empty($data['party_mobile']))
            $errorMessage['party_mobile'] = "Contact No. is required.";
            
        if(empty($data['gstin']))
            $errorMessage['gstin'] = 'Gstin is required.';
            
        if(empty($data['party_address']))
            $errorMessage['party_address'] = "Address is required.";
            
        if(empty($data['party_pincode']))
            $errorMessage['party_pincode'] = "Address Pincode is required.";
		
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            print json_encode($this->login_model->saveRegistration($data)); exit;
        endif;
    }
}
?>