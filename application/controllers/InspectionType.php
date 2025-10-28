<?php
class InspectionType extends MY_Controller
{
    private $indexPage = "inspection_type/index";
    private $formPage = "inspection_type/form";
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Inspection Type";
		$this->data['headData']->controller = "inspectionType";
		$this->data['headData']->pageUrl = "inspectionType";
	}
	
	public function index(){
        $this->data['tableHeader'] = getQualityDtHeader($this->data['headData']->controller);
        $this->data['type'] = 1;
        $this->load->view($this->indexPage,$this->data);
    }

    public function inspIndex(){
        $this->data['tableHeader'] = getQualityDtHeader($this->data['headData']->controller);
        $this->data['type'] = 2;
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($type){
        $data = $this->input->post(); $data['type'] = $type;
        $result = $this->inspectionType->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getInspectionTypeData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addInspectionType($type){
        $this->data['type'] = $type;
        $this->load->view($this->formPage,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['inspection_type'])):
            if($data['entry_type'] == 1):
                $errorMessage['inspection_type'] = "Inspection Type is required.";
            else:
                $errorMessage['inspection_type'] = "Inspection Parameter is required.";
            endif;
        endif;

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->inspectionType->save($data));
        endif;
    }

    public function edit(){
        $this->data['dataRow'] = $this->inspectionType->getInspectionType($this->input->post('id'));
        $this->load->view($this->formPage,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->inspectionType->delete($id));
        endif;
    }
    
}
?>