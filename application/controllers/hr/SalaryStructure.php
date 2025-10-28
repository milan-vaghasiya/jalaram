<?php
class SalaryStructure extends MY_Controller{
    private $indexPage = "hr/salary_structure/index";
    private $formPage = "hr/salary_structure/form";
    private $salaryHead = "hr/salary_structure/salary_heads";
    
    private $typeArray = ["1"=>'Earnings',"-1"=>"Deduction"];
    private $caltypeArray = ["1"=>'Basic',"2"=>"HRA","3"=>'PF',"4"=>"Speacial"];
    private $parentheadArray = ["1"=>'Gross Earning',"2"=>"General Earning","3"=>'Gross Deduction',"4"=>"General Deduction"];
    private $calMethodArray = ["1"=>'Percentage (%)',"2"=>"Amount","3"=>"Auto"];
    private $calOnArray = ["1"=>"CTC","2"=>'Basic+DA',"3"=>"Gross Salary"];
    
	public function __construct(){
		parent::__construct(); 
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Salary Structure";
		$this->data['headData']->controller = "hr/salaryStructure";
        $this->data['headData']->pageUrl = "hr/salaryStructure";
	}
	
	public function index(){
        $this->data['headData']->pageUrl = "hr/salaryStructure";
        $this->data['tableHeader'] = getHrDtHeader('salaryStructure');
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $result = $this->salaryStructure->getDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getSalaryStructureData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addCtcFormat(){
        $this->load->view($this->formPage,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['format_name']))
            $errorMessage['format_name'] = "Format Name is required.";
        if(empty($data['effect_from']))
            $errorMessage['effect_from'] = "Effect From is required.";
        if(empty($data['basic_da']) OR $data['basic_da'] <= 0)
            $errorMessage['basic_da'] = "Basic + DA is required.";
        //if(empty($data['hra']) OR $data['hra'] <= 0)
            //$errorMessage['hra'] = "HRA is required.";
            
        if($this->salaryStructure->checkDuplicateCtcFormat($data['format_name'],$data['id']) > 0)
            $errorMessage['head_name'] = "Format Name is Duplicate.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            if(empty($data['id'])){ 
                $data['created_at'] = date('Y-m-d H:i:s'); 
                $data['created_by'] = $this->session->userdata('loginId');
            }else{
                $data['updated_at'] = date('Y-m-d H:i:s'); 
                $data['updated_by'] = $this->session->userdata('loginId');
            }
            $result = $this->salaryStructure->save($data);
            $this->printJson($result);
        endif;
    }
    
    public function edit(){
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->salaryStructure->getCtcFormat($id);
        $this->load->view($this->formPage,$this->data);
    }
    
    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $result = $this->salaryStructure->delete($id);
            $this->printJson($result);
        endif;
    }
    
    public function getSalaryheads(){
        $ctc_id = $this->input->post('id');
        $this->data['salaryHead'] = $this->salaryStructure->getsalaryStructure($ctc_id);
        $this->data['typeArray'] = $this->typeArray;
        $this->data['parentheadArray'] = $this->parentheadArray;
        $this->data['calMethodArray'] = $this->calMethodArray;
        $this->data['calOnArray'] = $this->calOnArray;
        $this->data['salaryData'] = $this->getSalaryStructureTbl($ctc_id);
        $this->data['ctc_id'] = $ctc_id;
        $this->load->view($this->salaryHead,$this->data);
    }
    
     public function saveSalaryStructure(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['head_name']))
            $errorMessage['head_name'] = "Head Name is required.";
        if(empty($data['type']))
            $errorMessage['type'] = "Type is required.";
        if(empty($data['parent_head']))
            $errorMessage['parent_head'] = "Parent Head is required.";
        if(empty($data['cal_method']))
            $errorMessage['cal_method'] = "Cal. Method is required.";
        if(empty($data['cal_value']))
            $errorMessage['cal_value'] = "Cal. Value is required.";
            
        if($this->salaryStructure->checkDuplicateSalaryStructure($data['head_name'],$data['id'],$data['ctc_id']) > 0)
            $errorMessage['head_name'] = "Head Name is Duplicate.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            if(empty($data['id'])){ 
                $data['created_at'] = date('Y-m-d H:i:s'); 
                $data['created_by'] = $this->session->userdata('loginId');
            }else{
                $data['updated_at'] = date('Y-m-d H:i:s'); 
                $data['updated_by'] = $this->session->userdata('loginId');
            } unset($data['calonSelect']);
            $result = $this->salaryStructure->saveSalaryStructure($data);
            $salaryData = $this->getSalaryStructureTbl($data['ctc_id']);
            $this->printJson($salaryData);
        endif;
    }
    
    public function deleteSalaryStructure(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $salryData = $this->salaryStructure->getsalaryStructureById($id);
            $result = $this->salaryStructure->deleteSalaryStructure($id);
            $salaryData = $this->getSalaryStructureTbl($salryData->ctc_id);
            $this->printJson($salaryData);
        endif;
    }
    
    public function getSalaryStructureTbl($id){
        $result = $this->salaryStructure->getsalaryStructure($id);
        
        $tbody=''; $i=1;
        if(!empty($result)):
            foreach($result as $row):
                $tbody .= '<tr>
                    <td>'.$i++.'</td>
                    <td>'.$row->head_name.'</td>
                    <td>'.$this->typeArray[$row->type].'</td>
                    <td>'.$this->parentheadArray[$row->parent_head].'</td>
                    <td>'.$this->calMethodArray[$row->cal_method].'</td>
                    <td>'.$row->cal_value.'</td>
                    <td>'.$row->min_val.'</td>
                    <td>
                        <button type="button" onclick="deleteSalaryStructure('.$row->id.');" class="btn btn-sm btn-outline-danger btn-delete permission-remove"><i class="ti-trash"></i></button>
                    </td>
                </tr>';
            endforeach;
        else:
            $tbody .= "<tr><td class='text-center' colspan='8'>No data Found</td></tr>";
        endif;
        return ['status'=>1,'salaryBody'=>$tbody];
    }
}
?>