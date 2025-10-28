<?php
class Departments extends MY_Controller
{
    private $indexPage = "hr/department/index";
    private $departmentForm = "hr/department/form";
	private $deptCategory = ["1"=>"Admin","2"=>"HR","3"=>"Purchase","4"=>"Sales","5"=>"Store","6"=>"QC","7"=>"General","8"=>"Machining"];
    
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Departments";
		$this->data['headData']->controller = "hr/departments";
		$this->data['headData']->pageUrl = "hr/departments";
	}
	
	public function index(){
        $this->data['tableHeader'] = getHrDtHeader('departments');
        $this->data['empData'] = $this->department->getEmployees();
        $this->load->view($this->indexPage,$this->data);
    }
	
	public function getDTRows(){
        $result = $this->department->getDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;$row->leave_auths = '';$c=0;
			if(!empty($row->leave_authorities)):
				$la = explode(",",$row->leave_authorities);
				if(!empty($la))
				{
					foreach($la as $empid)
					{
						$row->leave_auths = "";
						$emp = $this->department->getLeaveAuthority($empid);
						if(!empty($emp)):
							if($c==0){$row->leave_auths .= $emp->emp_name;}else{$row->leave_auths .= '<br>'.$emp->emp_name;}$c++;
						else:
							$row->leave_auths = "";
						endif;
					}
				}
			endif;
			$row->category = $this->deptCategory[$row->category];
            $sendData[] = getDepartmentData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }
	
    public function addDepartment(){
        $this->data['empData'] = $this->department->getEmployees();
        $this->data['categoryData'] = $this->deptCategory;
        $this->load->view($this->departmentForm,$this->data);
    }
    
    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['name']))
            $errorMessage['name'] = "Department name is required.";
        if(empty($data['category']))
            $errorMessage['category'] = "Category is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
			//unset($data['empSelect']);
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->department->save($data));
        endif;
    }

    public function edit(){
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->department->getDepartment($id);
        $this->data['empData'] = $this->department->getEmployees();
        $this->data['categoryData'] = $this->deptCategory;
        $this->load->view($this->departmentForm,$this->data);
    }

    public function delete()
    {
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->department->delete($id));
        endif;
    }
    
}
?>