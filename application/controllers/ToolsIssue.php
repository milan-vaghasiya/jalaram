<?php 
class ToolsIssue extends MY_Controller{
    private $indexPage = "tools_issue/index";
    private $dispatchForm = "tools_issue/form";
    private $returnForm = "tools_issue/return_form";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();        
		$this->data['headData']->pageTitle = "Tools Issue";
		$this->data['headData']->controller = "toolsIssue";
		$this->data['headData']->pageUrl = "toolsIssue";
        $this->checkGrants("toolsIssue");
	}
	
	public function index(){
        $this->data['tableHeader'] = getStoreDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }
	
	public function getDTRows($status=0){
        $data = $this->input->post(); $data['status'] = $status; 
        $result = $this->toolsIssue->getDTRows($data);
        $sendData = array();$i=1; 
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;           
            $sendData[] = getToolsIssueData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addDispatch(){
        $this->data['jobCardData'] = $this->jobcard_v3->getJobcardList();
        $this->data['locationData'] = $this->store->getStoreLocationList();
        $this->data['itemData'] = $this->item->getItemList(2);
        $this->data['deptData'] = $this->department->getMachiningDepartment(8);
        $this->data['machineList'] = [];// $this->item->getItemList(5);
        $this->data['empData'] = $this->employee->getEmployeeList();
        $this->data['partyData'] = $this->party->getVendorList(); 
        $this->load->view($this->dispatchForm,$this->data);
    }

    public function getItemLocation(){
        $data = $this->input->post();
        $batchWiseStock = $this->toolsIssue->batchWiseItemStock(['item_id' => $data['item_id'], 'batch_no' => '', 'location_id' => '', 'batch_qty' => '', 'trans_id' => '']);
        $this->printJson(['status' => 1, 'batchWiseStock' => $batchWiseStock['batchData']]);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if($data['party_id'] == ""){
            $errorMessage['party_id'] = "Vendor is required.";
        }  
        if($data['is_returnable'] == ""){
            $errorMessage['is_returnable'] = "Returnable is required.";
        }   
        if(empty($data['collected_by'])){
            $errorMessage['collected_by'] = "Material Collected By is required.";
        }
        if(empty($data['item_id'])){
            $errorMessage['item_id'] = "Issue Item Name is required.";
        }
        $newQty = array_sum($data['batch_quantity']);
        if ($newQty<=0)
            $errorMessage['batch_quantity'] = "Qty is required.";  

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:  
            $data['issue_prefix'] = 'ISU/'.$this->shortYear.'/';
            
            $data['created_by'] = $data['dispatch_by'] = $this->session->userdata('loginId');  
            
            $data['location_id'] = implode(",", $data['location']);
            $data['batch_no'] = implode(",", $data['batch_number']);
            $data['batch_qty'] = implode(",", $data['batch_quantity']);
            $data['size'] = implode(",", $data['size']);
            unset($data['location'], $data['batch_number'], $data['batch_quantity'],$data['pending_issue']);

            $result = $this->toolsIssue->save($data);
            $this->printJson($result);

        endif;
    }

    // public function delete(){
    //     $id = $this->input->post('id');
    //     if(empty($id)):
    //         $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
    //     else:
    //         $this->printJson($this->toolsIssue->delete($id));
    //     endif;
    // }

    public function returnForm()
    {
        $id = $this->input->post('id');
        $this->data['batch_no']  = $this->input->post('batch_no');
        $this->data['pending_qty']  = $this->input->post('pending_qty');
        $this->data['size']  = $this->input->post('size');
        $this->data['ref_id'] = $id;
        $this->load->view($this->returnForm, $this->data);
    }

    public function saveReturnMaterial()
    {
        $data = $this->input->post(); 
        $errorMessage = array(); 
        
        if(empty($data['trans_date']))
            $errorMessage['trans_date'] = "Return Date is required.";
        if(empty($data['used_qty'][0]) AND empty($data['missed_qty'][0]) AND empty($data['broken_qty'][0]) AND empty($data['scrap_qty'][0]) AND empty($data['regranding_qty'][0])):
            $errorMessage['genral_error'] = "Return Qty. is required.";
        else:
            $totalUsed = array_sum($data['used_qty']);
            $totalMissed = array_sum($data['missed_qty']);
            $totalBroken = array_sum($data['broken_qty']);
            $totalScrap = array_sum($data['scrap_qty']);
            $totalRegrading = array_sum($data['regranding_qty']);

            $data['qty'] = $totalUsed + $totalMissed + $totalBroken +$totalScrap+$totalRegrading;
            if ($data['qty'] > $data['pending_qty']) {
                $errorMessage['genral_error'] = "Return Qty. is not valid";
                $this->printJson(['status' => 0, 'message' => $errorMessage]);
            }
        endif;
        
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $data['created_by']  = $this->session->userdata('loginId'); 
            $this->printJson($this->toolsIssue->saveReturnMaterial($data));
        endif;
    }
    
    public function getMachines(){
        $data = $this->input->post();
        $machineList = $this->item->getMachineList(['dept_id' => $data['dept_id']]);
        $machineOpt = '<option value="">Select Machine</option>';
        if(!empty($machineList))
        {
            foreach($machineList as $row)
            {
                $mCode = (!empty($row->item_code)) ? '['.$row->item_code.']' : '';
                $machineOpt .= '<option value="'.$row->id.'">'.$mCode.' '.$row->item_name.'</option>';
            }
        }
        $this->printJson(['status' => 1, 'machineOpt' => $machineOpt]);
    }
    
}
?>