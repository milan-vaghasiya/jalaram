<?php
class ProcessCode extends MY_Controller
{
    private $indexPage = "npd/process_code/index";
    private $form = "npd/process_code/form";
    
    
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "CP Process Code";
		$this->data['headData']->controller = "npd/processCode";
        $this->data['headData']->pageUrl = "npd/processCode";		
	}
	
	public function index(){
        $this->data['tableHeader'] = getProductionHeader('processCode');
        $this->load->view($this->indexPage,$this->data);
    }
	
    public function getDTRows(){
        $result = $this->processCode->getDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getprocessCode($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }
    
    public function addProcessCode(){
        $this->load->view($this->form,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['process_code']))
            $errorMessage['process_code'] = "process code  is required.";
        if(empty($data['description']))
            $errorMessage['description'] = "description is required.";
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->processCode->save($data));
        endif;
    }

    public function edit(){
        $data = $this->input->post();
        $this->data['dataRow'] = $this->processCode->getprocesscode($data['id']);
        $this->load->view($this->form,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->processCode->delete($id));
        endif;
    }
    
  
}
?>