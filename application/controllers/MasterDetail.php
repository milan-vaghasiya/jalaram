<?php
class MasterDetail extends MY_Controller
{
    private $indexPage = "master_detail/index";
    private $masterForm = "master_detail/form";
   

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Master Detail";
		$this->data['headData']->controller = "masterDetail";
        $this->data['headData']->pageUrl = "masterDetail";
	}
	
	public function index(){
        $this->data['tableHeader'] = getConfigDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }
	
    public function getDTRows($type=1){
        $data = $this->input->post(); $data['type'] = $type;
        $result = $this->masterDetail->getDTRows($data,$type);
        $sendData = array();$i=1;
        foreach($result['data'] as $row): 
            if($row->type == 1):
				$row->typeName = 'Industry Type';
			elseif($row->type == 2):
                $row->typeName = 'Firm Type';  
            elseif($row->type == 3):
                $row->typeName = 'Stage Type';
            elseif($row->type == 4):
                $row->typeName = 'class Type';
            elseif($row->type == 5):
                $row->typeName = 'Party Docs';
            elseif($row->type == 6):
                $row->typeName = 'Invoice Label';
            elseif($row->type == 7):
                $row->typeName = 'Amendment Reason';
            elseif($row->type == 8): /* Updated By :- Sweta @12-08-2023 */
                $row->typeName = 'Revision Checkpoint';
            else:
                $row->typeName = 'Other Type';
            endif;             
            $row->sr_no = $i++;         
            $sendData[] = getMasterDetailData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addMasterDetail($type=0){
        $this->data['type'] = $type;
        $this->data['customerData'] = $this->party->getCustomerList();
        $this->load->view($this->masterForm,$this->data);
    }

    /* Updated By :- Sweta @12-08-2023 */
    public function save(){
        $data = $this->input->post();
		$errorMessage = array();		
        if(empty($data['title'])){
			$errorMessage['title'] = "Title is required.";
        }
		if(empty($data['party_id']) && $data['type'] ==6){
			$errorMessage['party_id'] = "Party  is required.";
        }
        if($data['type'] == 8){
            if(empty($data['title'])){
			    $errorMessage['title'] = "Revision Checkpoint is required.";
            }
        }
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->masterDetail->save($data));
        endif;
    }

    public function edit(){     
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->masterDetail->getMasterDetail($id);    
        $this->data['customerData'] = $this->party->getCustomerList($id); 
        $this->load->view($this->masterForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->masterDetail->delete($id));
        endif;
    }
}