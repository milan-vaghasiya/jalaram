<?php
class Machines extends MY_Controller{
    private $indexPage = "machine/index";
    private $machineForm = "machine/form";
    private $activityForm = "machine/activity";
	private $gstPercentage = Array(["rate"=>0,"val"=>'NIL'],["rate"=>0.25,"val"=>'0.25%'],["rate"=>3,"val"=>'3%'],["rate"=>5,"val"=>'5%'],["rate"=>12,"val"=>'12%'],["rate"=>18,"val"=>'18%'],["rate"=>28,"val"=>'28%']);
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Machines";
		$this->data['headData']->controller = "machines";
		$this->data['headData']->pageUrl = "machines";
	}
	
	public function index(){
        $this->data['tableHeader'] = getMaintenanceDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $result = $this->machine->getDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $row->process_name = '';
            if(!empty($row->process_id)):
                $pdata = $this->machine->getProcess($row->process_id);
                $z=0;
                foreach($pdata as $row1):
                    if($z==0) {$row->process_name .= $row1->process_name;}else{$row->process_name .= ',<br>'.$row1->process_name;}$z++;
                endforeach;
            endif;
            $sendData[] = getMachineData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addMachine(){
        $this->data['gstPercentage'] = $this->gstPercentage;
        $this->data['processData'] = $this->process->getProcessList();
        $this->data['deptData'] = $this->department->getMachiningDepartment(8);
        $this->data['categoryList'] = $this->item->getCategoryList(5);
        $this->load->view($this->machineForm,$this->data);
    }

    public function getProcessData(){
        $id = $this->input->post('dept_id');
        $processData = $this->process->getDepartmentWiseProcess($id);
        $option = "";
        foreach ($processData as $row):
            $option .= '<option value="' . $row->id . '" >' . $row->process_name . '</option>';
        endforeach;
        
        $this->printJson(['status'=>1, 'option'=>$option]);
    }

    public function save(){
        $data = $this->input->post();
        if(isset($data['ftype']) and $data['ftype'] == 'activities'):
            unset($data['ftype']);
            $this->saveActivity($data);
        else:
            $errorMessage = array();
            if(empty($data['item_code']))
                $errorMessage['item_code'] = "Machine no. is required.";
            //if(empty($data['machine_brand']))
                //$errorMessage['machine_brand'] = "Brand Name is required.";
            //if(empty($data['machine_model']))
                //$errorMessage['machine_model'] = "Machine Model is required.";
            if(empty($data['location']))
                $errorMessage['location'] = "Department is required.";
            //if(empty($data['process_id']))
                //$errorMessage['process_id'] = "Process Name is required.";
            if(empty($data['category_id']))
                $errorMessage['category_id'] = "Category is required.";

            if(!empty($errorMessage)):
                $this->printJson(['status'=>0,'message'=>$errorMessage]);
            else:
                unset($data['processSelect']);
                $data['item_type']=5;
                $data['created_by'] = $this->session->userdata('loginId');
                $this->printJson($this->machine->save($data));
            endif;
        endif;
        
    }

    public function edit(){
        $this->data['gstPercentage'] = $this->gstPercentage;
        $this->data['deptData'] = $this->department->getMachiningDepartment(8);
        $this->data['dataRow'] = $this->machine->getMachine($this->input->post('id'));
        $this->data['processData'] =  $this->process->getDepartmentWiseProcess($this->data['dataRow']->location);
        $this->data['categoryList'] = $this->item->getCategoryList(5);
        $this->load->view($this->machineForm,$this->data);
    }

    public function setActivity(){
        $id = $this->input->post('id');
        $this->data['activityData'] = $this->machine->getActivity();
		$this->data['dataRow'] = $this->machine->getmaintanenceData($id);
        $this->load->view($this->activityForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->machine->delete($id));
        endif;
    }
  
    public function saveActivity() {
		$data = $this->input->post();
		$errorMessage = array();
		if(empty($data['activity_id']))
			$errorMessage['activity_id'] = "Machine Activities is required.";
        if(empty($data['activity_id'][0]))
			$errorMessage['activity_error'] = "Activities is required.";     
            
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
			$createdBy = Array();
			foreach($data['activity_id'] as $key=>$value){$createdBy[] = $this->session->userdata('loginId');}
            $activityData = [
                'id' => $data['id'],
				'activity_id' => $data['activity_id'],
				'checking_frequancy' => $data['checking_frequancy'],
                'created_by' =>  $createdBy
            ];
            $this->printJson($this->machine->saveActivity($data['machine_id'],$activityData));
        endif;
    } 
}
?>