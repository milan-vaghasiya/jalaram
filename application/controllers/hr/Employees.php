<?php
class Employees extends MY_Controller
{
    private $indexPage = "hr/employee/index";
    private $employeeForm = "hr/employee/form";
    private $empSalary = "hr/employee/emp_Salary";
    private $empDocs = "hr/employee/emp_Docs";
    private $empNom = "hr/employee/emp_Nom";
    private $empEdu = "hr/employee/emp_Edu";
    private $profile = "hr/employee/emp_profile";
    private $leaveAuthority = "hr/employee/leave_authority";
    private $employeeDevice="hr/employee/employee_device";
    private $empRelieveRejoin="hr/employee/emp_relive_rejoin";
    private $relieved_emp_list="hr/employee/relieved_emp_list";
    private $copy_permission = "hr/employee/copy_permission";
    private $dashPermission = "hr/employee/dash_permission";
    
    private $empRole = ["1"=>"Admin","2"=>"Production Manager","3"=>"Accountant","4"=>"Sales Manager","5"=>"Purchase Manager","6"=>"Employee"];
    private $gender = ["M"=>"Male","F"=>"Female","O"=>"Other"];
    private $systemDesignation = [1=>"Machine Operator",2=>"Line Inspector",3=>"Setup Inspector",4=>"Process Setter",5=>"Final QC Inspector"];
    
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Employees";
		$this->data['headData']->controller = "hr/employees";   
        $this->data['headData']->pageUrl = "hr/employees";
	}
	
	public function index(){        
        $this->data['tableHeader'] = getHrDtHeader('employees');
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status=0){
        $data = $this->input->post(); $data['status']=$status;
        $result = $this->employee->getDTRows($data);
        $sendData = array();$i=1;$count=0;
		foreach($result['data'] as $row):
			$row->sr_no = $i++; 
			$row->emp_role = (isset($this->empRole[$row->emp_role]))?$this->empRole[$row->emp_role]:"";  
			$row->loginId = $this->loginId;
			$sendData[] = getEmployeeData($row);
		endforeach;
		
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addEmployee(){
        $this->data['deptRows'] = $this->department->getDepartmentList();
        $this->data['roleData'] = $this->empRole;
        $this->data['genderData'] = $this->gender;
        $this->data['descRows'] = $this->employee->getDesignation();
        $this->data['systemDesignation'] = $this->systemDesignation;
        $this->data['categoryData'] = $this->category->getCategoryList(); 
        $this->data['shiftData'] = $this->shiftModel->getShiftList();
        $this->load->view($this->employeeForm,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['emp_name']))
            $errorMessage['emp_name'] = "Employee name is required.";
        if(empty($data['emp_role']))
            $errorMessage['emp_role'] = "Role is required.";
        if(empty($data['emp_contact']))
             $errorMessage['emp_contact'] = "Contact No. is required.";
        // if(empty($data['emp_alt_contact']))
        //     $errorMessage['emp_alt_contact'] = "Emergency No. is required.";
        if(empty($data['emp_dept_id']))
            $errorMessage['emp_dept_id'] = "Department is required.";
        if(empty($data['emp_designation']))
        {
            if(empty($data['designationTitle']))
                $errorMessage['emp_designation'] = "Designation is required.";
            else
                $data['emp_designation'] = $this->employee->saveDesignation($data['designationTitle'],$data['emp_dept_id']);
        }
        unset($data['designationTitle']);
        if(empty($data['id'])):
            /* if(empty($data['emp_password']))
                $errorMessage['emp_password'] = "Password is required.";
            if(!empty($data['emp_password']) && $data['emp_password'] != $data['emp_password_c'])
                $errorMessage['emp_password_c'] = "Confirm Password not match."; */
            $data['emp_password'] = "123456";
        endif;
		
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['emp_name'] = ucwords($data['emp_name']);
            $data['created_by'] = $this->session->userdata('loginId');            
            $this->printJson($this->employee->save($data));
        endif;
    }

    public function edit(){
        $id = $this->input->post('id');
        $this->data['deptRows'] = $this->department->getDepartmentList();
        $this->data['roleData'] = $this->empRole;
        $this->data['genderData'] = $this->gender;
        $this->data['descRows'] = $this->employee->getDesignation();
        $this->data['systemDesignation'] = $this->systemDesignation;
        $this->data['categoryData'] = $this->category->getCategoryList(); 
        $this->data['shiftData'] = $this->shiftModel->getShiftList();
        $result = $this->employee->getEmp($id);
        //$result->designation = $this->employee->getDesignation($result->emp_dept_id,$result->emp_designation)['result'];
        $this->data['dataRow'] = $result;
        $this->load->view($this->employeeForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->employee->delete($id));
        endif;
    }

    public function activeInactive(){
        $postData = $this->input->post();
        if(empty($postData['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->employee->activeInactive($postData));
        endif;
    }
    
    public function changePassword(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['old_password']))
            $errorMessage['old_password'] = "Old Password is required.";
        if(empty($data['new_password']))
            $errorMessage['new_password'] = "New Password is required.";
        if(empty($data['cpassword']))
            $errorMessage['cpassword'] = "Confirm Password is required.";
        if(!empty($data['new_password']) && !empty($data['cpassword'])):
            if($data['new_password'] != $data['cpassword'])
                $errorMessage['cpassword'] = "Confirm Password and New Password is Not match!.";
        endif;

        if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
            $id = $this->session->userdata('loginId');
			$result =  $this->employee->changePassword($id,$data);
			$this->printJson($result);
		endif;
    }

    public function getDesignation(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
        else:
            $this->printJson($this->employee->getDesignation($id));
        endif;
    }

    public function getEmpSalary(){
        $emp_id = $this->input->post('id');
        $this->data['dataRow'] = $this->employee->getEmpSalary($emp_id);
        $this->data['emp_id'] = $emp_id;
        $this->load->view($this->empSalary,$this->data);
    }

    public function updateEmpSalary(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['salary_basis']))
            $errorMessage['salary_basis'] = "Salary Basis is required.";
        if(empty($data['basic_salary']))
            $errorMessage['basic_salary'] = "Basic Salary is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->employee->saveEmpSalary($data));
        endif;
    }

    public function getEmpDocs(){
        $emp_id = $this->input->post('id');
        $this->data['dataRow'] = $this->employee->getEmpDocs($emp_id);
        $this->data['emp_id'] = $emp_id;
        $this->load->view($this->empDocs,$this->data);
    }

    public function updateEmpDocs(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['emp_id']))
            $errorMessage['emp_id'] = "Employee id is required.";
        if(empty($data['old_uan_no']))
            $errorMessage['old_uan_no'] = "Old Uan No is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->employee->saveEmpDocs($data));
        endif;
    }

    public function getEmpNom(){
        $emp_id = $this->input->post('id');
        $this->data['nomData'] = $this->employee->getNominationData($emp_id);
        $this->data['genderData'] = $this->gender;
        $this->data['emp_id'] = $emp_id;
        $this->load->view($this->empNom,$this->data);
    }

    public function updateEmpNom(){
        $data = $this->input->post();
		$errorMessage = array();
		if(empty($data['nom_name'])){
			$errorMessage['nom_name'] = "Name is required.";
		}
        if(empty($data['nom_relation'])){
			$errorMessage['nom_relation'] = "Relation is required.";
		}
        if(empty($data['nom_dob'])){
			$errorMessage['nom_dob'] = "Date of birth is required.";
		}
        
		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->employee->saveEmpNom($data);

            $empNom = $this->employee->getNominationData($data['emp_id']);
            $tbodyData="";$i=1; 
            if(!empty($empNom)):
                $i=1;
                foreach($empNom as $row):
                    $tbodyData.= '<tr>
                                <td>' . $i++ . '</td>
                                <td>' . $row->nom_name . '</td>
                                <td>' . $row->nom_gender . '</td>
                                <td>' . $row->nom_relation . '</td>
                                <td>' . $row->nom_dob . '</td>
                                <td>' . $row->nom_proportion . ' </td>
                                <td class="text-center">
                                    <button type="button" onclick="trashEmpNom('.$row->id.','.$row->emp_id.');" class="btn btn-outline-danger waves-effect waves-light btn-delete permission-remove"><i class="ti-trash"></i></button>
                                </td>
                            </tr>';
                endforeach;
            else:
                $tbodyData.= '<tr><td colspan="7" style="text-align:center;">No Data Found</td></tr>';
            endif;
			$this->printJson(['status'=>1,"tbodyData"=>$tbodyData]);
		endif;
    }

    public function deleteEmpNom(){
        $data=$this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->employee->deleteEmpNom($data['id']);
            $empNom = $this->employee->getNominationData($data['emp_id']);
            $tbodyData="";$i=1; 
            if(!empty($empNom)):
                $i=1;
                foreach($empNom as $row):
                    $tbodyData.= '<tr>
                                <td>' . $i++ . '</td>
                                <td>' . $row->nom_name . '</td>
                                <td>' . $row->nom_gender . '</td>
                                <td>' . $row->nom_relation . '</td>
                                <td>' . $row->nom_dob . '</td>
                                <td>' . $row->nom_proportion . ' </td>
                                <td class="text-center">
                                    <button type="button" onclick="trashEmpNom('.$row->id.','.$row->emp_id.');" class="btn btn-outline-danger waves-effect waves-light btn-delete permission-remove"><i class="ti-trash"></i></button>
                                </td>
                            </tr>';
                endforeach;
            else:
                $tbodyData.= '<tr><td colspan="7" style="text-align:center;">No Data Found</td></tr>';
            endif;
			$this->printJson(['status'=>1,"tbodyData"=>$tbodyData]);
        endif;
    }

    public function getEmpEdu(){
        $emp_id = $this->input->post('id');
        $this->data['eduData'] = $this->employee->getEducationData($emp_id);
        $this->data['emp_id'] = $emp_id;
        $this->load->view($this->empEdu,$this->data);
    }

    public function updateEmpEdu(){
        $data = $this->input->post();
		$errorMessage = array();
		if(empty($data['course'])){
			$errorMessage['course'] = "Course is required.";
		}
        if(empty($data['passing_year'])){
			$errorMessage['passing_year'] = "Passing Year is required.";
		}
        if(empty($data['grade'])){
			$errorMessage['grade'] = "Grade is required.";
		}
		
		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->employee->saveEmpEdu($data);

            $empEdu = $this->employee->getEducationData($data['emp_id']);
            $tbodyData="";$i=1; 
            if(!empty($empEdu)):
                $i=1;
                foreach($empEdu as $row):
                    $tbodyData.= '<tr>
                                <td>' . $i++ . '</td>
                                <td>' . $row->course . '</td>
                                <td>' . $row->university . '</td>
                                <td>' . $row->passing_year . ' </td>
                                <td>' . $row->grade . '</td>
                                <td class="text-center">
                                    <button type="button" onclick="trashEmpEdu('.$row->id.','.$row->emp_id.');" class="btn btn-outline-danger waves-effect waves-light btn-delete permission-remove"><i class="ti-trash"></i></button>
                                </td>
                            </tr>';
                endforeach;
            else:
                $tbodyData.= '<tr><td colspan="6" style="text-align:center;">No Data Found</td></tr>';
            endif;
			$this->printJson(['status'=>1,"tbodyData"=>$tbodyData]);
		endif;
    }

    public function deleteEmpEdu(){
        $data=$this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->employee->deleteEmpEdu($data['id']);
            $empEdu = $this->employee->getEducationData($data['emp_id']);
            $tbodyData="";$i=1; 
            if(!empty($empEdu)):
                $i=1;
                foreach($empEdu as $row):
                    $tbodyData.= '<tr>
                                <td>' . $i++ . '</td>
                                <td>' . $row->course . '</td>
                                <td>' . $row->university . '</td>
                                <td>' . $row->passing_year . ' </td>
                                <td>' . $row->grade . '</td>
                                <td class="text-center">
                                    <button type="button" onclick="trashEmpEdu('.$row->id.','.$row->emp_id.');" class="btn btn-outline-danger waves-effect waves-light btn-delete permission-remove"><i class="ti-trash"></i></button>
                                </td>
                            </tr>';
                endforeach;
            else:
                $tbodyData.= '<tr><td colspan="6" style="text-align:center;">No Data Found</td></tr>';
            endif;
			$this->printJson(['status'=>1,"tbodyData"=>$tbodyData]);
        endif;
    }

    /* employee profile */
    public function empProfile($emp_id){
        $this->data['empData'] = $this->employee->getEmployee($emp_id);
        $this->data['empSalary'] = $this->employee->getEmpSalary($emp_id);
        $this->data['empDocs'] = $this->employee->getEmpDocs($emp_id);
        $this->data['empNom'] = $this->employee->getNominationData($emp_id);
        $this->data['empEdu'] = $this->employee->getEducationData($emp_id);
        $this->data['roleData'] = $this->empRole;
        $this->data['genderData'] = $this->gender;
        $this->data['emp_id'] = $emp_id;
        $this->data['companyInfo'] = $this->employee->getCompanyInfo();
        $this->data['docData'] = $this->employee->getEmpDocuments($emp_id);
        $this->data['deptRows'] = $this->department->getDepartmentList();
        $this->data['descRows'] = $this->employee->getDesignation();
        $this->data['systemDesignation'] = $this->systemDesignation;
        $this->data['categoryData'] = $this->category->getCategoryList();
        $this->data['gradeData'] = explode(',', $this->employee->getMasterOptions()->emp_grade);
        $this->data['dataRow'] = $this->employee->getEmp($emp_id);
        $this->data['staffData'] = $this->employee->getStaffSkill($emp_id);
        $this->data['skillData'] = $this->skillMaster->getDeptWiseSkill($this->data['dataRow']->emp_dept_id);
        $this->data['ctcFormat'] = $this->salaryStructure->getCtcFormats(); 
        $this->data['empFacility'] = $this->employee->getFacilityData($emp_id);
        $this->load->view($this->profile,$this->data);
    }	

    public function changeEmpPsw(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->employee->changeEmpPsw($id));
        endif;
    }

    /*************** Leave Module **********************/
    /* LeaveAuthority */
    public function getEmpLeaveAuthority(){
        $emp_id = $this->input->post('id');
        $this->data['deptRows'] = $this->department->getDepartmentList();
        $this->data['descRows'] = $this->employee->getDesignation();
        $this->data['leaveData'] = $this->employee->getLeaveHierarchy($emp_id);
        $this->data['emp_id'] = $emp_id;
        $this->load->view($this->leaveAuthority,$this->data);
    }

    public function saveLeaveAuthority(){
        $data = $this->input->post();
        $errorMessage = Array();
        if(empty($data['dept_id']))
            $errorMessage['emp_dept_id'] = " Pelase select department.";
        if(empty($data['desi_id']))
            $errorMessage['emp_designation'] = " Pelase select designation.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $response = $this->employee->saveLeaveAuthority($data);
			if($response['status'] == 0):
				$this->printJson($response);
			else:
				$this->printJson($this->setLeaveAuthorityView($data['emp_id']));
			endif;
        endif;
    }

    public function setLeaveAuthorityView($emp_id){
        $leaveHierarchyData = $this->employee->getLeaveHierarchy($emp_id);
        $leaveHtml = '';$i=1;
        if (!empty($leaveHierarchyData)) :
            foreach ($leaveHierarchyData as $row) :
				$deleteParam = $row->id.','.$row->emp_id;
				$deleteButton = '<a class="btn btn-outline-danger btn-sm btn-delete" href="javascript:void(0)" onclick="deleteLeaveAuthority('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
                $leaveHtml .= '<tr id="'.$row->id.'">
                            <td class="text-center">'.$i++.'</td>
                            <td>'.$row->name.'</td>
                            <td>'.$row->title.'</td>
                            <td class="text-center">'.$row->priority.'</td>
                            <td>'.$deleteButton.'</td>
                        </tr>';
            endforeach;
        else :
            $leaveHtml .= '<tr><td colspan="5" class="text-center">No Data Found.</td></tr>';
        endif;
        return ['status' => 1, "leaveHtml" => $leaveHtml];
    }

    public function setLeaveApprovalPriority(){
        $data = $this->input->post();
		$errorMessage = array();		
		if(empty($data['id']))
			$errorMessage['id'] = "Item ID is required.";
		
		if(empty($errorMessage)):
			$this->printJson($this->employee->setLeaveApprovalPriority($data));			
		endif;
    }

    public function deleteLeaveAuthority(){
        $data = $this->input->post();
        $errorMessage = Array();
        if(empty($data['id']))
            $errorMessage['general']= " ID is Required";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>2,'message'=>$errorMessage]);
        else:
            $response = $this->employee->deleteLeaveAuthority($data);
            $this->printJson($this->setLeaveAuthorityView($data['emp_id']));
        endif;
    }

    public function empRelive(){
        $data=$this->input->post();
        $this->data['dataRow']=new stdClass();
        $this->data['dataRow']->id=$data['id'];
        $this->data['dataRow']->is_delete=2;
        $this->data['empFacility'] = $this->employee->getFacilityData($data['id']);
        $this->load->view($this->empRelieveRejoin,$this->data);
    }

    public function saveEmpRelieve(){ 
        $data = $this->input->post();
		$errorMessage = array();
        if($data['is_delete']==2){
            if(empty($data['emp_relieve_date'])){
                $errorMessage['emp_relieve_date'] = "Relieve Date is required.";
            }
            if(empty($data['reason'])){
                $errorMessage['reason'] = "Reason is required.";
            }
        }else{
            if(empty($data['emp_joining_date'])){
                $errorMessage['emp_joining_date'] = "ReJoining Date is required.";
            }
        }
		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->employee->saveEmpReliveJoinData($data));
		endif;
    }

   
    public function getRelievedEmpDTRows(){
        $result = $this->employee->getRelievedEmpDTRows($this->input->post());
        $sendData = array();$i=1;$count=0;
		foreach($result['data'] as $row):              
			$value = ($row->is_active == 1)?0:1;
			$checked = ($row->is_active == 1)?"checked":"";
			if($row->emp_role!=1):
				$count = 1;
				$row->active_html = '<input type="checkbox" id="activeInactive'.$i.'" class="bt-switch activeInactive permission-modify" data-on-color="success"  data-off-color="danger" data-on-text="Active" data-off-text="Inactive" data-id="'.$row->id.'" data-val="'.$value.'" data-row_id="'.$i.'" '.$checked.'>';
			else:
				$row->active_html = '<input type="checkbox" id="activeInactive'.$i.'" class="bt-switch activeInactive permission-modify" data-on-color="success"  data-off-color="danger" data-on-text="Active" data-off-text="Inactive" data-id="'.$row->id.'" data-val="'.$value.'" data-row_id="'.$i.'" '.$checked.'>';
			endif;
			$row->sr_no = $i++; 
			
			//Meghavi
			$optionStatus =new stdClass(); //$this->employee->checkEmployeeStatus($row->id);
			$row->salary = (!empty($optionStatus->salary)) ? '<i class="fa fa-check text-primary"></i>' : '';
			$row->document = (!empty($optionStatus->document)) ? '<i class="fa fa-check text-primary"></i>' : '';
			$row->nomination = (!empty($optionStatus->nomination)) ? '<i class="fa fa-check text-primary"></i>' : '';
			$row->education = (!empty($optionStatus->education)) ? '<i class="fa fa-check text-primary"></i>' : '';
			$row->leave = (!empty($optionStatus->leave)) ? '<i class="fa fa-check text-primary"></i>' : '';
			
			//$row->emp_role = $this->empRole[$row->emp_role];         
			$sendData[] = getEmpRelievedData($row);
		endforeach;
		
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function empRelievedList(){
        $this->data['headData']->pageTitle = "Relieved Employee";
        $this->data['tableHeader'] = getHrDtHeader('relievedEmployee');
        $this->load->view($this->relieved_emp_list,$this->data);
    }
    
    public function empRejoin(){
        $data=$this->input->post();
        $this->data['dataRow']=new stdClass();
        $this->data['dataRow']->id=$data['id'];
        $this->data['dataRow']->is_delete=0;
        $this->load->view($this->empRelieveRejoin,$this->data);
    }

    /*** Add Employee In Device ***/
    public function addEmployeeInDevice(){
        $id = $this->input->post('id');
       
        $this->data['deviceList']=$this->employee->getDeviceForEmployee();
        $this->data['dataRow'] = $this->employee->getEmp($id);
        $this->data['emp_id'] = $id;

        $this->load->view($this->employeeDevice,$this->data);
        // if(empty($id)):
        //     $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        // else:
        //     $this->printJson($this->employee->addEmployeeInDevice($id));
        // endif;
    }

    /*** Save Employee In Device ***/
    public function saveEmployeeInDevice()
    {
        $data=$this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->employee->addEmployeeInDevice($data['id'],$data['emp_id']));
        endif;
    }
    
    /*** Remove Employee From Device ***/
    public function removeEmployeeInDevice()
    {
        $data=$this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->employee->removeEmployeeInDevice($data['id'],$data['emp_id']));
        endif;
    }
    
    /*** Transfer Empcode In Device ***/
    public function transferEmpCode()
    {
        ini_set('max_execution_time', 0);
        $data=$this->input->post();
        if(empty($data['emp_id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again1.']);
        else:
            $this->printJson($this->employee->transferEmpCode($data));
        endif;
    }
    
    /*** Update Emp Profile Picture ***/ 
    public function updateProfilePic(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['emp_id']))
            $errorMessage['emp_id'] = "Employee id is required.";
        
        if($_FILES['emp_profile']['name'] != null || !empty($_FILES['emp_profile']['name'])):
            //
            $this->load->library('upload');
            $_FILES['userfile']['name']     = $_FILES['emp_profile']['name'];
            $_FILES['userfile']['type']     = $_FILES['emp_profile']['type'];
            $_FILES['userfile']['tmp_name'] = $_FILES['emp_profile']['tmp_name'];
            $_FILES['userfile']['error']    = $_FILES['emp_profile']['error'];
            $_FILES['userfile']['size']     = $_FILES['emp_profile']['size'];
            
            $imagePath = realpath(APPPATH . '../assets/uploads/emp_profile/');
            $ext = pathinfo($_FILES['emp_profile']['name'], PATHINFO_EXTENSION);
            $config = ['file_name' => $data['emp_id'].'.'.$ext,'allowed_types' => '*','max_size' => 10240,'overwrite' => FALSE, 'upload_path' => $imagePath];
            if(file_exists($config['upload_path'].'/'.$config['file_name'])) unlink($config['upload_path'].'/'.$config['file_name']);
            $this->upload->initialize($config);
            if (!$this->upload->do_upload()):
                $errorMessage['emp_profile'] = $this->upload->display_errors();
                $this->printJson(["status"=>0,"message"=>$errorMessage]);
            else:
                $uploadData = $this->upload->data();
                $data['emp_profile'] = $uploadData['file_name'];
            endif;
        else:
            $data['emp_profile'] = '';
            $errorMessage['emp_id'] = "Image Not Found.";
        endif;

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->employee->updateProfilePic($data));
        endif;
    }

    public function saveEmpDocumentsParam(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['doc_name']))
            $errorMessage['doc_name'] = "Document Name is required.";
        if(empty($data['doc_no']))
            $errorMessage['doc_no'] = "Document No is required.";
        if(empty($data['doc_type']))
            $errorMessage['doc_type'] = "Document Type is required.";

        if($_FILES['doc_file']['name'] != null || !empty($_FILES['doc_file']['name'])):
            $this->load->library('upload');
            $_FILES['userfile']['name']     = $_FILES['doc_file']['name'];
            $_FILES['userfile']['type']     = $_FILES['doc_file']['type'];
            $_FILES['userfile']['tmp_name'] = $_FILES['doc_file']['tmp_name'];
            $_FILES['userfile']['error']    = $_FILES['doc_file']['error'];
            $_FILES['userfile']['size']     = $_FILES['doc_file']['size'];
            
            $imagePath = realpath(APPPATH . '../assets/uploads/emp_documents/');
            $config = ['file_name' => time()."_doc_file_".$data['emp_id']."_".$_FILES['userfile']['name'],'allowed_types' => '*','max_size' => 10240,'overwrite' => FALSE, 'upload_path'	=>$imagePath];

            $this->upload->initialize($config);
            if (!$this->upload->do_upload()):
                $errorMessage['doc_file'] = $this->upload->display_errors();
                $this->printJson(["status"=>0,"message"=>$errorMessage]);
            else:
                $uploadData = $this->upload->data();
                $data['doc_file'] = $uploadData['file_name'];
            endif;
        else:
            unset($data['doc_file']);
        endif; 

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->employee->saveEmpDocumentsParam($data);
            $docData = $this->employee->getEmpDocuments($data['emp_id']);
            //print_r($docData);
            $tbodyData="";$i=1; 
            if(!empty($docData)):
                $i=1;
                foreach($docData as $row):
                    $tbodyData.= '<tr>
                                <td class="text-center">'.$i++.'</td>
                                <td class="text-center">'.$row->doc_name.'</td>
                                <td class="text-center">'.$row->doc_no.'</td>
                                <td class="text-center">'.$row->doc_type_name.'</td>
                                <td class="text-center">'.((!empty($row->doc_file))?'<a href="'.base_url('assets/uploads/emp_documents/'.$row->doc_file).'" target="_blank"><i class="fa fa-download"></i></a>':"") .'</td>
                                <td class="text-center">
                                    <button type="button" onclick="trashEmpDocuments('.$row->id.','.$row->emp_id.');" class="btn btn-outline-danger waves-effect waves-light btn-delete permission-remove"><i class="ti-trash"></i></button>
                                </td>
                            </tr>';
                endforeach;
            else:
                $tbodyData.= '<tr><td colspan="7" style="text-align:center;">No Data Found</td></tr>';
            endif;
            $this->printJson(['status'=>1,"tbodyData"=>$tbodyData]);
        endif;
    }

    public function deleteEmpDocuments(){
        $data=$this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->employee->deleteEmpDocuments($data['id']);
            $docData = $this->employee->getEmpDocuments($data['emp_id']);
            $tbodyData="";$i=1; 
            if(!empty($docData)):
                $i=1;
                foreach($docData as $row):
                    $tbodyData.= '<tr>
                                <td>'.$i++.'</td>
                                <td>'.$row->doc_name.'</td>
                                <td>'.$row->doc_no.'</td>
                                <td>'.$row->doc_type.'</td>
                                <td>'.((!empty($row->doc_file))?'<a href="'.base_url('assets/uploads/emp_documents/'.$row->doc_file).'" target="_blank"><i class="fa fa-download"></i></a>':"") .'</td>
                                <td class="text-center">
                                    <button type="button" onclick="trashEmpDocuments('.$row->id.','.$row->emp_id.');" class="btn btn-outline-danger waves-effect waves-light btn-delete permission-remove"><i class="ti-trash"></i></button>
                                </td>
                            </tr>';
                endforeach;
            else:
                $tbodyData.= '<tr><td colspan="7" style="text-align:center;">No Data Found</td></tr>';
            endif;
			$this->printJson(['status'=>1,"tbodyData"=>$tbodyData]);
        endif;
    }

    public function editProfile(){
        $data = $this->input->post();
        $errorMessage = array();
        if($data['form_type'] == 'personalDetail'):
            if(empty($data['emp_name']))
                $errorMessage['emp_name'] = "Employee name is required.";
            if(empty($data['emp_code']))
                $errorMessage['emp_code'] = "Employee Code is required.";
            $data['emp_name'] = ucwords($data['emp_name']);
            if(empty($data['emp_contact']))
                $errorMessage['emp_contact'] = "Contact No. is required.";
            //if(empty($data['emp_alt_contact']))
            //    $errorMessage['emp_alt_contact'] = "Emergency No. is required.";
        endif; 
        if($data['form_type'] == 'workprofile'):
            if(empty($data['emp_dept_id']))
                $errorMessage['emp_dept_id'] = "Department is required.";
            if(empty($data['emp_designation']))
                $errorMessage['emp_designation'] = "Designation is required.";
            if(empty($data['emp_type']))
                $errorMessage['emp_type'] = "Employee Type is required.";
                
            if($data['sal_pay_mode'] != 2)
            {
                if(empty($data['bank_name']))
                    $errorMessage['bank_name'] = "Bank is required.";
                if(empty($data['account_no']))
                    $errorMessage['account_no'] = "Acc. No. is required.";
                if(empty($data['ifsc_code']))
                    $errorMessage['ifsc_code'] = "IFSC Code is required.";
            }
        endif;  
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['emp_role'] = 6;
            $this->printJson($this->employee->editProfile($data));
        endif;
    }

    public function updateStaffSkill(){
        $data = $this->input->post(); 
        $errorMessage = array();

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->employee->updateStaffSkill($data));
        endif;
    }
    
    public function calculateCTC(){
        $postData = $this->input->post(); 
        $errorMessage = array();
		if(empty($postData['ctc_emp_id']))
            $errorMessage['ctc_emp_id'] = "Emp ID Required";
		if(empty($postData['ctc_format']))
            $errorMessage['ctc_format'] = "CTC Format Required";
		if(empty($postData['ctc_amount']))
            $errorMessage['ctc_amount'] = "Amount Required";
		
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $ctcData = $this->salaryStructure->getCtcFromat($postData['ctc_format']);
			$salaryHeadData = $this->salaryStructure->getSalaryHeadsOnCtcFormat($postData['ctc_format']);
			
			$ctcAmt = $postData['ctc_amount'];$gross_salary=0;
			$basic_salary = round((($ctcAmt * $ctcData->basic_da)/100));
			if($basic_salary < $ctcData->min_wages){$basic_salary = $ctcData->min_wages;}
			$hra = ($ctcData->hra > 0) ? round((($basic_salary * $ctcData->hra)/100)) : 0;
					
            $postData['salary_duration'] = $ctcData->salary_duration;
            $ctcStructure = '<table class="table table-bordered align-items-center">';
			$ctcStructure .= '<thead class="thead-info"><tr>';
				$ctcStructure .= '<th>Fixed Components</th>';
            if($postData['salary_duration'] == "M"):
				$ctcStructure .= '<th>Annual</th>';
            endif;
			$ctcStructure .= '<th  style="width:20%;">Monthly </th>';
			$ctcStructure .= '</tr></thead><tbody>';
			
			$ctcAmt = $postData['ctc_amount'];
			$grossEarn = '';$genEarn = '';$grossDed = '';
			$grossEarnTotal = $basic_salary + $hra;$genEarnTotal = 0;$grossDedTotal = 0;
            $headAmount = array();$calculationAmt = array();$dataRow = array();
			
            $dataRow['basic_da'] = [
                'head_name' => "Basic + DA",
                'parent_head' => 1,
                'head_amount' => round($basic_salary)
            ];
            
			if($hra > 0 )
			{
                $dataRow['hra'] = [
                    'head_name' => "HRA",
                    'parent_head' => 1,
                    'head_amount' => round($hra)
                ];
			}
			
			$grossCTC = $grossEarnTotal; $autoHeads = array();
			if(!empty($salaryHeadData)){  
				foreach($salaryHeadData as $row){
					$monthlyAmt = 0;
					if(!empty($row->cal_on)):
						$calON = 0;
						if($row->cal_on == 1){$calON = $ctcAmt;} // ON CTC
						if($row->cal_on == 2){$calON = $basic_salary;} // ON BASIC SALARY
						if($row->cal_on == 3){$calON = $grossCTC;} // ON GROSS SALARY
					
						if($row->cal_method == 1):
							$monthlyAmt = round((($row->cal_value * $calON)/100),2);
						elseif($row->cal_method == 2):
							$monthlyAmt = round($row->cal_value,2);
						endif;
						$headAmount[$row->id] = ($monthlyAmt * $row->type);
					else:
					    $autoHeads[$row->id] = $row->type;
					    $headAmount[$row->id] = 0;
					endif;
					
					$grossCTC += ($row->parent_head == 1 || $row->parent_head == 2)?round($headAmount[$row->id]):0;

                    switch($row->parent_head){
                        case 1:
                            $grossEarnTotal += round($headAmount[$row->id]);break;
                        case 2:
                            $genEarnTotal += round($headAmount[$row->id]);break;
                        case 3:
                            $grossDedTotal += round($headAmount[$row->id]);break;
                    }
					
					$dataRow[$row->id] = [
					    'head_name' => $row->head_name,
					    'parent_head' => $row->parent_head,
					    'head_amount' => round($headAmount[$row->id])
					];
				}
			}
            
            // Calculate Gratuity
            if($ctcData->gratuity_days > 0):
				$gratuityAmount = round((($basic_salary * $ctcData->gratuity_per)/$ctcData->gratuity_days)/12,-1);

				if(!empty($gratuityAmount)):
					$gratuityAmount = (ceil($gratuityAmount/50) * 50);
					
					$dataRow['gratuity'] = [
					    'head_name' => "Gratuity",
					    'parent_head' => 2,
					    'head_amount' => $gratuityAmount
					];
					$grossCTC += $gratuityAmount;
					$genEarnTotal += round($gratuityAmount);
				endif;
            endif;
			
			// Calculate Employer Provident Fund
			$pfAmount = 0;$npf = 0;$pfAmt = 0;
            if($ctcData->pf_status > 0):
				$pfON = ($grossEarnTotal + ($ctcAmt - $grossCTC)) - $hra;
				$pfAmt = $this->calcPF($pfON,$ctcData->pf_per);
				while($npf != $pfAmt){					
					$pfON = ($grossEarnTotal + ($ctcAmt - $grossCTC)) - $hra;
					$pfAmt = $this->calcPF($pfON,$ctcData->pf_per);
					
					$grossCTC += $pfAmt;
					$genEarnTotal += round($pfAmt);
					$grossDedTotal += round(($pfAmt * -1));
					$diffAmt = $ctcAmt - $grossCTC;
					$grossEarnTotal += $diffAmt;
					//$pfON = ($grossEarnTotal + ($ctcAmt - $grossCTC)) - $hra;
					$npf = round(($pfON * $ctcData->pf_per)/100);
				}
				$pfAmount = $pfAmt;
            endif;
            
            $diffAmount = $ctcAmt - $grossCTC;
			
			foreach($autoHeads as $headId => $type):
			    $dataRow[$headId]['head_amount'] = round(($diffAmount / count($autoHeads)) * $type); 
			endforeach;
            //$grossEarnTotal += $diffAmount;
			
			// Final PF
			if($pfAmount > 0):				
				$dataRow['pfe'] = [
					'head_name' => "Employer Provident Fund @ 12%",
					'parent_head' => 2,
					'head_amount' => $pfAmount
				];
				
				$dataRow['pfd'] = [
					'head_name' => "Employer Provident Fund @ 12%",
					'parent_head' => 3,
					'head_amount' => round($pfAmount)
				];
				
            endif;
            
            // Calculate Profession Tax
            $professionTax = $this->salaryStructure->getProfessionTaxBaseOnGrossSalary($grossEarnTotal);
            if(!empty($professionTax)){
                $dataRow['pt'] = [
				    'head_name' => "Profession Tax",
				    'parent_head' => 3,
				    'head_amount' => $professionTax->amount
				];
                $grossDedTotal += round(($professionTax->amount * -1));
            }	
            
			foreach($dataRow as $key => $row):
			    switch($row['parent_head']){
                    case 1:
                        $grossEarn .= '<tr><td>'.$row['head_name'].'</td>';
                        if($postData['salary_duration'] == "M"):
                            $grossEarn .= '<td id="'.$key.'_amount_y">'.round($row['head_amount'] * 12).'</td>';
                        endif;
                        $grossEarn .= '<td>
                                <input type="text" name="salary_head_json['.$key.'][amount]" class="form-control floatOnly gross_total calculateEmpSalary" data-y_td="'.$key.'_amount_y" value="'.round($row['head_amount']).'">
                            </td></tr>';
                        break;
                    case 2:
                        $genEarn .= '<tr><td>'.$row['head_name'].'</td>';
                        if($postData['salary_duration'] == "M"):
                            $genEarn .= '<td id="'.$key.'_amount_y">'.round($row['head_amount'] * 12).'</td>';
                        endif;
                        $genEarn .= '<td>
                                <input type="text" name="salary_head_json['.$key.'][amount]" class="form-control floatOnly grand_total calculateEmpSalary" data-y_td="'.$key.'_amount_y" value="'.round($row['head_amount']).'">
                            </td></tr>';
                        break;
                    case 3:
                        $grossDed .= '<tr><td>'.$row['head_name'].'</td>';
                        if($postData['salary_duration'] == "M"):
                            $grossDed .= '<td id="'.$key.'_amount_y">'.round(abs($row['head_amount'] * 12)).'</td>';
                        endif;
                        $grossDed .= '<td>
                                <input type="text" name="salary_head_json['.$key.'][amount]" class="form-control floatOnly gross_ded calculateEmpSalary" data-y_td="'.$key.'_amount_y" value="'.round(abs($row['head_amount'])).'">
                            </td></tr>';
                        break;
                }
			endforeach;

            $netPayTotal = round($genEarnTotal+$grossEarnTotal+$grossDedTotal);
            //print_r($headAmount);exit;

            if($grossEarnTotal > 0):
                $grossEarn .= '<tr class="bg-light">';
                    $grossEarn .= '<th>Gross Salary Total</th>';
                    if($postData['salary_duration'] == "M"):
                        $grossEarn .= '<th id="gross_total_y">'.($grossEarnTotal * 12).'</th>';
                    endif;
                    $grossEarn .= '<th><input type="text"  id="gross_total" class="form-control" value="'.$grossEarnTotal.'" readonly></th>';
                $grossEarn .= '</tr>';
            endif;
            if($genEarnTotal > 0):
                $genEarn .= '<tr class="bg-light">';
                    $genEarn .= '<th>Grand Total - CTC</th>';
                    if($postData['salary_duration'] == "M"):
                        $genEarn .= '<th id="grand_total_y">'.(($genEarnTotal+$grossEarnTotal) * 12).'</th>';
                    endif;
                    $genEarn .= '<th><input type="text"  id="grand_total" class="form-control" value="'.($genEarnTotal+$grossEarnTotal).'" readonly></th>';
                $genEarn .= '</tr>';
            endif;
            if($grossDedTotal > 0):
                /* $grossDed .= '<tr class="bg-light">';
                    $grossDed .= '<th>Gross Deduction</th>';
                    if($postData['salary_duration'] == "M"):
                        $grossDed .= '<th id="gross_ded_total_y">'.(abs($grossDedTotal) * 12).'</th>';
                    endif;
                    $grossDed .= '<th><input type="text"  id="gross_ded_total" class="form-control" value="'.abs($grossDedTotal).'" readonly></th>';
                $grossDed .= '</tr>'; */
            endif;
			$netPay = '<tr class="bg-light-info">';
				$netPay .= '<th>On Hand Salary</th>';
                if($postData['salary_duration'] == "M"):
                    $netPay .= '<th id="net_pay_y">'.($netPayTotal * 12).'</th>';
                endif;
                $netPay .= '<th>                                
                                <input type="text" name="net_pay" id="net_pay" class="form-control" value="'.$netPayTotal.'" readonly>                                
                                <input type="hidden" name="salary_duration" value="'.$ctcData->salary_duration.'">
                            </th>';
			$netPay .= '</tr>';
			$ctcStructure .= $grossEarn.$genEarn.$grossDed.$netPay.'</tbody></table>';
		
			// Save CTC to Employee Master
			
			/* $empData = [
				'id'=>$postData['ctc_emp_id'],
				'ctc_format' => $postData['ctc_format'],
				'ctc_amount' => $postData['ctc_amount'],
				'salary_duration' => $postData['salary_duration'],
				//'month_hours' => $postData['month_hours'],
				'updated_by' => $this->loginId
			];
			$this->employee->saveEmpSalary($empData); */
			
            $this->printJson(['status'=>1,'ctcStructure'=>$ctcStructure]);
        endif;
    }
	
	public function calcPF($pfON,$pf_per){
		return round(($pfON * $pf_per)/100);
	}
	
	public function saveEmpSalaryStructure(){
        $data = $this->input->post();
        $errorMessage = array();

        if($data['ctc_format'] > 0):
            if(empty($data['ctc_amount']))
                $errorMessage['ctc_amount'] = "Amount is required.";
            if(empty($data['effect_start']))
                $errorMessage['effect_start'] = "Effective Date is required.";
        endif;

        if(empty($data['salary_head_json']))
            $errorMessage['salary_head'] = "Salary Heads is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            //print_r($data);exit;
            $this->printJson($this->employee->saveEmpSalaryStructure($data));
        endif;
    }

    public function getEmpActiveSalaryStructure(){
        $emp_id = $this->input->post('emp_id');
        $empData = $this->employee->getEmp($emp_id);
        
        $activeStructure = $this->employee->getActiveSalaryStructure($emp_id);
        $html = '';$ctc_amount = 0;$effect_start = "";$format_id="";
        if(!empty($empData->emp_type) && $empData->emp_type == "1"):
            if(!empty($activeStructure)):
                $salaryHeadData = $this->salaryStructure->getSalaryHeadsOnCtcFormat($activeStructure->format_id);

                $empSalarayHeads = json_decode($activeStructure->salary_head_json);

                $dataRow = array();
                $dataRow['basic_da'] = [
                    'head_name' => "Basic + DA",
                    'parent_head' => 1,
                    'head_amount' => ((!empty($empSalarayHeads->basic_da->amount))?round($empSalarayHeads->basic_da->amount):0)
                ];

                if(!empty($empSalarayHeads->hra->amount)):
                    $dataRow['hra'] = [
                        'head_name' => "HRA",
                        'parent_head' => 1,
                        'head_amount' => round($empSalarayHeads->hra->amount)
                    ];
                endif;

                if(!empty($salaryHeadData)):  
                    foreach($salaryHeadData as $row):
                        $dataRow[$row->id] = [
                            'head_name' => $row->head_name,
                            'parent_head' => $row->parent_head,
                            'head_amount' => ((!empty($empSalarayHeads->{$row->id}->amount))?round($empSalarayHeads->{$row->id}->amount):0)
                        ];
                    endforeach;
                endif;

                if(!empty($empSalarayHeads->gratuity->amount)):
                    $dataRow['gratuity'] = [
                        'head_name' => "Gratuity",
                        'parent_head' => 2,
                        'head_amount' => round($empSalarayHeads->gratuity->amount)
                    ];
                endif;

                if(!empty($empSalarayHeads->pfe->amount)):
                    $dataRow['pfe'] = [
                        'head_name' => "Employer Provident Fund @ 12%",
                        'parent_head' => 2,
                        'head_amount' => round($empSalarayHeads->pfe->amount)
                    ];
                endif;
                
                if(!empty($empSalarayHeads->pfd->amount)):
                    $dataRow['pfd'] = [
                        'head_name' => "Employer Provident Fund @ 12%",
                        'parent_head' => 3,
                        'head_amount' => round($empSalarayHeads->pfd->amount)
                    ];
                endif;

                if(!empty($empSalarayHeads->pt->amount)):
                    $dataRow['pt'] = [
                        'head_name' => "Profession Tax",
                        'parent_head' => 3,
                        'head_amount' => round($empSalarayHeads->pt->amount)
                    ];
                endif;

                $grossEarnTotal = 0;$genEarnTotal = 0;$grossDedTotal = 0;
                $grossEarn = '';$genEarn = '';$grossDed = '';
                foreach($dataRow as $key => $row):
                    switch($row['parent_head']):
                        case 1:
                            $grossEarn .= '<tr><td>'.$row['head_name'].'</td>';
                            if($activeStructure->salary_duration == "M"):
                                $grossEarn .= '<td id="'.$key.'_amount_y">'.round($row['head_amount'] * 12).'</td>';
                            endif;
                            $grossEarn .= '<td>
                                    <input type="text" name="salary_head_json['.$key.'][amount]" class="form-control floatOnly gross_total calculateEmpSalary" data-y_td="'.$key.'_amount_y" value="'.round($row['head_amount']).'">
                                </td></tr>';

                            $grossEarnTotal += round($row['head_amount']);
                            break;
                        case 2:
                            $genEarn .= '<tr><td>'.$row['head_name'].'</td>';
                            if($activeStructure->salary_duration == "M"):
                                $genEarn .= '<td id="'.$key.'_amount_y">'.round($row['head_amount'] * 12).'</td>';
                            endif;
                            $genEarn .= '<td>
                                    <input type="text" name="salary_head_json['.$key.'][amount]" class="form-control floatOnly grand_total calculateEmpSalary" data-y_td="'.$key.'_amount_y" value="'.round($row['head_amount']).'">
                                </td></tr>';

                            $genEarnTotal += round($row['head_amount']);
                            break;
                        case 3:
                            $grossDed .= '<tr><td>'.$row['head_name'].'</td>';
                            if($activeStructure->salary_duration == "M"):
                                $grossDed .= '<td id="'.$key.'_amount_y">'.round(abs($row['head_amount'] * 12)).'</td>';
                            endif;
                            $grossDed .= '<td>
                                    <input type="text" name="salary_head_json['.$key.'][amount]" class="form-control floatOnly gross_ded calculateEmpSalary" data-y_td="'.$key.'_amount_y" value="'.round(abs($row['head_amount'])).'">
                                </td></tr>';

                            $grossDedTotal += round($row['head_amount']);
                            break;
                    endswitch;
                endforeach;

                $netPayTotal = 0;
                $netPayTotal = round(($grossEarnTotal) - $grossDedTotal);

                if($grossEarnTotal > 0):
                    $grossEarn .= '<tr class="bg-light">';
                        $grossEarn .= '<th>Gross Salary Total</th>';
                        if($activeStructure->salary_duration == "M"):
                            $grossEarn .= '<th id="gross_total_y">'.($grossEarnTotal * 12).'</th>';
                        endif;
                        $grossEarn .= '<th><input type="text"  id="gross_total" class="form-control" value="'.$grossEarnTotal.'" readonly></th>';
                    $grossEarn .= '</tr>';
                endif;
                if($genEarnTotal > 0):
                    $genEarn .= '<tr class="bg-light">';
                        $genEarn .= '<th>Grand Total - CTC</th>';
                        if($activeStructure->salary_duration == "M"):
                            $genEarn .= '<th id="grand_total_y">'.(($genEarnTotal+$grossEarnTotal) * 12).'</th>';
                        endif;
                        $genEarn .= '<th><input type="text"  id="grand_total" class="form-control" value="'.($genEarnTotal+$grossEarnTotal).'" readonly></th>';
                    $genEarn .= '</tr>';
                endif;
                if($grossDedTotal > 0):
                    /* $grossDed .= '<tr class="bg-light">';
                        $grossDed .= '<th>Gross Deduction</th>';
                        if($activeStructure->salary_duration == "M"):
                            $grossDed .= '<th id="gross_ded_total_y">'.(abs($grossDedTotal) * 12).'</th>';
                        endif;
                        $grossDed .= '<th><input type="text"  id="gross_ded_total" class="form-control" value="'.abs($grossDedTotal).'" readonly></th>';
                    $grossDed .= '</tr>'; */
                endif;
                $netPay = '<tr class="bg-light-info">';
                    $netPay .= '<th>On Hand Salary</th>';
                    if($activeStructure->salary_duration == "M"):
                        $netPay .= '<th id="net_pay_y">'.($netPayTotal * 12).'</th>';
                    endif;
                    $netPay .= '<th>                                
                                    <input type="text" name="net_pay" id="net_pay" class="form-control" value="'.$netPayTotal.'" readonly>                                
                                    <input type="hidden" name="salary_duration" value="'.$activeStructure->salary_duration.'">
                                </th>';
                $netPay .= '</tr>';

                $html = '<table class="table table-bordered align-items-center">';
                $html .= '<thead class="thead-info"><tr>';
                    $html .= '<th>Fixed Components</th>';
                if($activeStructure->salary_duration == "M"):
                    $html .= '<th>Annual</th>';
                endif;
                $html .= '<th  style="width:20%;">Monthly</th>';
                $html .= '</tr></thead><tbody>';
                $html .= $grossEarn.$genEarn.$grossDed.$netPay.'</tbody></table>';

                $ctc_amount = $activeStructure->ctc_amount;
                $effect_start = $activeStructure->effect_start;
                $format_id = $activeStructure->format_id;
            endif;
        else:
            $empSalarayHeads = (!empty($activeStructure->salary_head_json))?json_decode($activeStructure->salary_head_json):array();

            $html .= '<input type="hidden" name="ctc_format" value="-1"><input type="hidden" name="salary_duration" value="D">';
            
            $html .= '<table class="table table-bordered align-items-center">
                <thead class="thead-info">
                    <tr>
                        <th>Earning</th>
                        <th style="width: 150px;">Earning Type</th>
                        <th style="width: 150px;">Earning Value</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Actual Wage</td>
                        <td>
                            <select name="salary_head_json[actual_wage][cal_method]" id="actual_wage_cal_method" class="form-control">
                                <option value="1" '.((!empty($empSalarayHeads->actual_wage->cal_method) && $empSalarayHeads->actual_wage->cal_method == 1)?"selected":"").'>Amount</option>
                                <option value="2" '.((!empty($empSalarayHeads->actual_wage->cal_method) && $empSalarayHeads->actual_wage->cal_method == 2)?"selected":"").'>Percentage</option>
                                <!--<option value="3" '.((!empty($empSalarayHeads->actual_wage->cal_method) && $empSalarayHeads->actual_wage->cal_method == 3)?"selected":"").'>Auto</option>-->
                            </select>
                        </td>
                        <td>
                            <input type="hidden" name="salary_head_json[actual_wage][type]" id="actual_wage_type" value="1">
                            <input type="text" name="salary_head_json[actual_wage][cal_value]" id="actual_wage_cal_value" class="form-control floatOnly" value="'.((!empty($empSalarayHeads->actual_wage->cal_value))?$empSalarayHeads->actual_wage->cal_value:"").'">
                        </td>
                    </tr>
                    <tr>
                        <td>Payroll Wages</td>
                        <td>
                            <select name="salary_head_json[payroll_wages][cal_method]" id="payroll_wages_cal_method" class="form-control">
                                <option value="1" '.((!empty($empSalarayHeads->payroll_wages->cal_method) && $empSalarayHeads->payroll_wages->cal_method == 1)?"selected":"").'>Amount</option>
                                <option value="2" '.((!empty($empSalarayHeads->payroll_wages->cal_method) && $empSalarayHeads->payroll_wages->cal_method == 2)?"selected":"").'>Percentage</option>
                                <!--<option value="3" '.((!empty($empSalarayHeads->payroll_wages->cal_method) && $empSalarayHeads->payroll_wages->cal_method == 3)?"selected":"").'>Auto</option>-->
                            </select>
                        </td>
                        <td>
                            <input type="hidden" name="salary_head_json[payroll_wages][type]" id="actual_wage_type" value="1">
                            <input type="text" name="salary_head_json[payroll_wages][cal_value]" id="actual_wage_cal_value" class="form-control floatOnly" value="'.((!empty($empSalarayHeads->payroll_wages->cal_value))?$empSalarayHeads->payroll_wages->cal_value:$empData->payroll_wages).'">
                        </td>
                    </tr>
                    <tr>
                        <td>Basic</td>
                        <td>
                            <select name="salary_head_json[basic_da][cal_method]" id="basic_da_cal_method" class="form-control">
                                <option value="1" '.((!empty($empSalarayHeads->basic_da->cal_method) && $empSalarayHeads->basic_da->cal_method == 1)?"selected":"").'>Amount</option>
                                <option value="2" '.((!empty($empSalarayHeads->basic_da->cal_method) && $empSalarayHeads->basic_da->cal_method == 2)?"selected":"").'>Percentage</option>
                                <!--<option value="3" '.((!empty($empSalarayHeads->basic_da->cal_method) && $empSalarayHeads->basic_da->cal_method == 3)?"selected":"").'>Auto</option>-->
                            </select>
                        </td>
                        <td>
                            <input type="hidden" name="salary_head_json[basic_da][type]" id="basic_da_type" value="1">
                            <input type="text" name="salary_head_json[basic_da][cal_value]" id="basic_da_cal_value" class="form-control floatOnly" value="'.((!empty($empSalarayHeads->basic_da->cal_value))?$empSalarayHeads->basic_da->cal_value:"").'">
                        </td>
                    </tr>
                    <tr>
                        <td>HRA</td>
                        <td>
                            <select name="salary_head_json[hra][cal_method]" id="hra_cal_method" class="form-control">
                                <option value="1" '.((!empty($empSalarayHeads->hra->cal_method) && $empSalarayHeads->hra->cal_method == 1)?"selected":"").'>Amount</option>
                                <option value="2" '.((!empty($empSalarayHeads->hra->cal_method) && $empSalarayHeads->hra->cal_method == 2)?"selected":"").'>Percentage</option>
                                <!--<option value="3" '.((!empty($empSalarayHeads->hra->cal_method) && $empSalarayHeads->hra->cal_method == 3)?"selected":"").'>Auto</option>-->
                            </select>
                        </td>
                        <td>
                            <input type="hidden" name="salary_head_json[hra][type]" id="hra_type" value="1">
                            <input type="text" name="salary_head_json[hra][cal_value]" id="hra_cal_value" class="form-control floatOnly" value="'.((!empty($empSalarayHeads->hra->cal_value))?$empSalarayHeads->hra->cal_value:"").'">
                        </td>
                    </tr>
                    <tr style="display: none;">
                        <td>Other Allowance</td>
                        <td>
                            <input type="hidden" name="salary_head_json[other_allowance][cal_method]" id="other_allowance_cal_method" value="3">
                        </td>
                        <td>
                            <input type="hidden" name="salary_head_json[other_allowance][type]" id="other_allowance_type" value="1">
                            <input type="hidden" name="salary_head_json[other_allowance][cal_value]" id="other_allowance_cal_value" class="form-control floatOnly" value="'.((!empty($empSalarayHeads->other_allowance->cal_value))?$empSalarayHeads->other_allowance->cal_value:"").'">
                        </td>
                    </tr>
                    <tr class="thead-info">
                        <th>Deduction</th>
                        <th>Deduction Type</th>
                        <th>Deduction Value</th>
                    </tr>';
                    if((!empty($empData->pf_applicable) && $empData->pf_applicable == 1)):
                        $html .= '<tr>
                            <td>PF</td>
                            <td>
                                <select name="salary_head_json[pf][cal_method]" id="pf_cal_method" class="form-control">
                                    <option value="1" '.((!empty($empSalarayHeads->pf->cal_method) && $empSalarayHeads->pf->cal_method == 1)?"selected":"").'>Amount</option>
                                    <option value="2" '.((!empty($empSalarayHeads->pf->cal_method) && $empSalarayHeads->pf->cal_method == 2)?"selected":"").'>Percentage</option>
                                    <!--<option value="3" '.((!empty($empSalarayHeads->pf->cal_method) && $empSalarayHeads->pf->cal_method == 3)?"selected":"").'>Auto</option>-->
                                </select>
                            </td>
                            <td>
                                <input type="hidden" name="salary_head_json[pf][type]" id="pf_type" value="-1">
                                <input type="text" name="salary_head_json[pf][cal_value]" id="pf_cal_value" class="form-control floatOnly" value="'.((!empty($empSalarayHeads->pf->cal_value))?$empSalarayHeads->pf->cal_value:"").'">
                            </td>
                        </tr>';
                    endif;
                    $html .= '<tr>
                        <td>PT</td>
                        <td>
                            <select name="salary_head_json[pt][cal_method]" id="pt_cal_method" class="form-control">
                                <option value="1" '.((!empty($empSalarayHeads->pt->cal_method) && $empSalarayHeads->pt->cal_method == 1)?"selected":"").'>Amount</option>
                                <option value="2" '.((!empty($empSalarayHeads->pt->cal_method) && $empSalarayHeads->pt->cal_method == 2)?"selected":"").'>Percentage</option>
                                <!--<option value="3" '.((!empty($empSalarayHeads->pt->cal_method) && $empSalarayHeads->pt->cal_method == 3)?"selected":"").'>Auto</option>-->
                            </select>
                        </td>
                        <td>
                            <input type="hidden" name="salary_head_json[pt][type]" id="pt_type" value="-1">
                            <input type="text" name="salary_head_json[pt][cal_value]" id="pt_cal_value" class="form-control floatOnly" value="'.((!empty($empSalarayHeads->pt->cal_value))?$empSalarayHeads->pt->cal_value:"").'">
                        </td>
                    </tr>
                    <tr>
                        <td>LWF</td>
                        <td>
                            <select name="salary_head_json[lwf][cal_method]" id="lwf_cal_method" class="form-control">
                                <option value="1" '.((!empty($empSalarayHeads->lwf->cal_method) && $empSalarayHeads->lwf->cal_method == 1)?"selected":"").'>Amount</option>
                                <option value="2" '.((!empty($empSalarayHeads->lwf->cal_method) && $empSalarayHeads->lwf->cal_method == 2)?"selected":"").'>Percentage</option>
                                <!--<option value="3" '.((!empty($empSalarayHeads->lwf->cal_method) && $empSalarayHeads->lwf->cal_method == 3)?"selected":"").'>Auto</option>-->
                            </select>
                        </td>
                        <td>
                            <input type="hidden" name="salary_head_json[lwf][type]" id="lwf_type" value="-1">
                            <input type="text" name="salary_head_json[lwf][cal_value]" id="lwf_cal_value" class="form-control floatOnly" value="'.((!empty($empSalarayHeads->lwf->cal_value))?$empSalarayHeads->lwf->cal_value:"").'">
                        </td>
                    </tr>
                </tbody>
            </table>';
        endif;
        $this->printJson(['html'=>$html,'format_id'=>$format_id,'ctc_amount'=>$ctc_amount,'effect_start'=>$effect_start]);
    }
	
    // Emp Facility *Created By Meghavi @22/12/22*
    public function updateEmpFacility(){
        $data = $this->input->post();
		$errorMessage = array();
		if(empty($data['issue_date'])){
			$errorMessage['issue_date'] = "Issue Date is required.";
		}
        if(empty($data['type'])){
			$errorMessage['type'] = "Type is required.";
		}
     
		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->employee->saveEmpFacility($data);

            $empFacibility = $this->employee->getFacilityData($data['emp_id']);
            $tbodyData="";$i=1; 
            if(!empty($empFacibility)):
                $i=1;
                foreach($empFacibility as $row):
                    $type="";
                    if($row->type == 1){
                        $type="Uniform";
                    }elseif($row->type == 2){
                        
                        $type="Quater";
                    }elseif($row->type == 3){
                        
                        $type="Mobile";
                    }
                    $tbodyData.= '<tr>
                                <td>' . $i++ . '</td>
                                <td>' .(formatDate($row->issue_date)). '</td>
                                <td>' . $type . '</td>
                                <td>' . $row->description . ' </td>
                                <td>' . $row->specs . '</td>
                                <td class="text-center">
                                    <button type="button" onclick="trashEmpFacility('.$row->id.','.$row->emp_id.');" class="btn btn-outline-danger waves-effect waves-light btn-delete permission-remove"><i class="ti-trash"></i></button>
                                </td>
                            </tr>';
                endforeach;
            else:
                $tbodyData.= '<tr><td colspan="6" style="text-align:center;">No Data Found</td></tr>';
            endif;
			$this->printJson(['status'=>1,"tbodyData"=>$tbodyData]);
		endif;
    }                 

    public function deleteEmpFacility(){
        $data=$this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->employee->deleteEmpFacility($data['id']);
            $empFacibility = $this->employee->getFacilityData($data['emp_id']);
            $tbodyData="";$i=1; 
            if(!empty($empFacibility)):
                $i=1;
                foreach($empFacibility as $row):
                    $tbodyData.= '<tr>
                                <td>' . $i++ . '</td>
                                <td>' . (formatDate($row->issue_date)) . '</td>
                                <td>' . $row->type . '</td>
                                <td>' . $row->description . ' </td>
                                <td>' . $row->specs . '</td>
                                <td class="text-center">
                                    <button type="button" onclick="trashEmpFacility('.$row->id.','.$row->emp_id.');" class="btn btn-outline-danger waves-effect waves-light btn-delete permission-remove"><i class="ti-trash"></i></button>
                                </td>
                            </tr>';
                endforeach;
            else:
                $tbodyData.= '<tr><td colspan="6" style="text-align:center;">No Data Found</td></tr>';
            endif;
			$this->printJson(['status'=>1,"tbodyData"=>$tbodyData]);
        endif;
    }

    public function empPermission(){
	    $this->data['headData']->pageTitle = "Emp. Permission";
        $this->data['empList'] = $this->employee->getActiveEmpList();
        $this->data['permission'] = $this->permission->getPermission(0,1);
        $this->load->view('hr/employee/emp_permission',$this->data);
    }

    public function empPermissionReport(){
        $this->data['empList'] = $this->employee->getActiveEmpList();
        $this->data['permission'] = $this->permission->getPermission(1,1);
        $this->load->view('hr/employee/emp_permission_report',$this->data);
    }

    public function appPermission(){        
        $this->data['empList'] = $this->employee->getActiveEmpList();
        $this->data['permission'] = $this->permission->getPermission(0,2);
        $this->load->view('hr/employee/emp_permission_app',$this->data);
    }
    
    public function savePermission(){
        $data = $this->input->post();
        $errorMessage = array();
        
        if(empty($data['emp_id']))
            $errorMessage['emp_id'] = "Employee name is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->permission->save($data));
        endif;
    }

    public function editPermission(){
        $emp_id = $this->input->post('emp_id');
        $menu_type = $this->input->post('menu_type');
        $this->printJson($this->permission->editPermission($emp_id,$menu_type));
    }

    //Avruti @08-04-2024
    public function dashPermission(){
        $this->data['headData']->pageUrl = "hr/employees/empPermission";
        $this->data['empList'] = $this->employee->getEmpList();
        $this->data['dashPermisson'] = $this->permission->getDashboardWidget();
        $this->load->view($this->dashPermission,$this->data);
    }

    public function saveDashPermission(){
        $data = $this->input->post();
        $errorMessage = array();
        
        if(empty($data['emp_id']))
            $errorMessage['emp_id'] = "Employee name is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->permission->saveDashPermission($data));
        endif;
    }

    public function editDashPermission(){
        $emp_id = $this->input->post('emp_id');
        $this->printJson($this->permission->editDashPermission($emp_id));
    }

    //Created By Karmi @12/08/2022
    public function copyPermission(){
        $this->data['fromList'] = $this->employee->getAllEmpList();
        $this->data['toList'] = $this->employee->getAllEmpList();
        $this->load->view($this->copy_permission,$this->data);
    }

    public function saveCopyPermission(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['from_id'])){
            $errorMessage['from_id'] = "From User is required.";
        }
        if(empty($data['to_id'])){
            $errorMessage['to_id'] = "To User is required.";
        }
        if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
            $fromData = $this->permission->getEmployeePermission($data['from_id']);            
            $this->printJson($this->permission->saveCopyPermission($data,$fromData['mainPermission'],$fromData['subMenuPermission']));
        endif;
    }
}
?>