<?php
class Process extends MY_Controller
{
    private $indexPage = "process/index";
    private $processForm = "process/form";
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Process";
		$this->data['headData']->controller = "process";
		$this->data['headData']->pageUrl = "process";
	}
	
	public function index(){
        $this->data['tableHeader'] = getProductionHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $result = $this->process->getDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $departments=explode(",",$row->dept_id);
            $departmet_name="";
            if(!empty($departments)){ $i=1;
                foreach($departments as $dept){
                    $deptData=$this->department->getDepartment($dept);
                    if($i!=1){ $departmet_name.=' ,'; }
                    $departmet_name.=$deptData->name; $i++;
                }
            }
            $row->department=$departmet_name;
            $sendData[] = getProcessData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addProcess(){
		$this->data['deptRows'] = $this->department->getDepartmentList();
        $this->load->view($this->processForm,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['process_name']))
            $errorMessage['process_name'] = "Process name is required.";
        if(empty($data['dept_id']))
            $errorMessage['dept_id'] = "Department is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->process->save($data));
        endif;
    }

    public function edit(){
        $this->data['dataRow'] = $this->process->getProcess($this->input->post('id'));
		$this->data['deptRows'] = $this->department->getDepartmentList();
        $this->load->view($this->processForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->process->delete($id));
        endif;
    }

    public function processMigration()
    {
        $this->printJson($this->process->processMigration());
    }

    public function addMhr(){
        $data = $this->input->post();
        $this->data['id'] = $data['id'];
        $this->data['gradeList'] = $this->materialGrade->getMaterialGrades();
        $this->data['mhrData'] = $this->process->getProcessMhrDetail(['process_id'=>$data['id']]);
        $this->load->view('process/mhr_form',$this->data);
    }

    public function saveMhr(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['process_id'])){ $errorMessage['process_id'] = "Process is required.";}
        if(empty(array_sum($data['mhr']))){
            $errorMessage['general_error'] = "MHR is required.";
        }
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->process->saveMhr($data));
        endif;
    }
    
}
?>