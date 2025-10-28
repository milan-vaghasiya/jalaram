<?php
class DropdownOptions extends MY_Controller
{
    private $indexPage = "dropdown/index";
    private $formPage = "dropdown/form";
   
    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Dropdown Options";
		$this->data['headData']->controller = "dropdownOptions";
		$this->data['headData']->pageUrl = "dropdownOptions";
		$this->data['dropdownType'] = $this->dropdownType = ['','SCOMET POINTS'];
	}
	
	public function index(){
        $this->data['tableHeader'] = getConfigDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $result = $this->dropdown->getDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $row->dropdownType = $this->dropdownType[$row->type];
            $sendData[] = getDropdowngData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }


    public function addDropdownOption(){
        $this->load->view($this->formPage,$this->data);
    }

    public function save()
    {
        $data = $this->input->post();
        $errorMessage = array();
        if (empty($data['description']))
            $errorMessage['description'] = "Description is required.";

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->dropdown->save($data));
        endif;
    }

    public function edit(){
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->dropdown->getDropdownDetail($id);
        $this->load->view($this->formPage,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->dropdown->delete($id));
        endif;
    }
    
}
?>