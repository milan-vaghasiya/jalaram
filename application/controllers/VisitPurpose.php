
<?php
class VisitPurpose extends MY_Controller
{
    private $indexPage = "visit_purpose/index";
    private $form = "visit_purpose/form";
    private $typeArray = ["visit Purpose"];
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "VisitPurpose";
		$this->data['headData']->controller = "visitPurpose";
        $this->data['headData']->pageUrl = "visitPurpose";
	}
	
	public function index(){
        $this->data['tableHeader'] = getConfigDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }
	
    public function getDTRows(){
        $result = $this->visitPurpose->getDTRows($this->input->post());
        $sendData = array();$i=1;$count=0;
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;         
            $sendData[] = getVisitPurposeData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addVisitPurpose(){
        $this->data['typeArray'] = $this->typeArray;
        $this->load->view($this->form, $this->data);
    }

    public function save(){
        $data = $this->input->post();
		$errorMessage = array();		
        if(empty($data['title']))
			$errorMessage['title'] = "visit purpose is required.";
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->visitPurpose->save($data));
        endif;
    }

    public function edit(){     
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->visitPurpose->getVisitPurpose($id);
        $this->data['typeArray'] = $this->typeArray;
        $this->load->view($this->form, $this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->visitPurpose->delete($id));
        endif;
    }
}
?>