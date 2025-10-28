<?php
class Products extends MY_Controller
{
    private $indexPage = "product/index";
    private $productForm = "product/form";
    private $productProcessForm = "product/product_process";
    private $viewProductProcess = "product/view_product_process";
    private $productKitItem = "product/product_kit";
    private $fgRevision = "product/fg_revisions";
    private $revise_check_point ="product/revise_check_point";
    private $revise_ch_index ="product/revise_ch_index";
    private $revise_ch_pending_index ="product/revise_ch_pending_index";
    private $revise_ch_review_index ="product/revise_ch_review_index";
    private $verify_form ="product/verify_form";
    
    private $automotiveArray = ["1"=>'Yes',"2"=>"No"];
    private $gstPercentage = Array(["rate"=>0,"val"=>'NIL'],["rate"=>0.1,"val"=>'0.1%'],["rate"=>0.25,"val"=>'0.25%'],["rate"=>3,"val"=>'3%'],["rate"=>5,"val"=>'5%'],["rate"=>12,"val"=>'12%'],["rate"=>18,"val"=>'18%'],["rate"=>28,"val"=>'28%']);
	
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Products";
		$this->data['headData']->controller = "products";
		$this->data['headData']->pageUrl = "products";
	}

    public function index(){
        $this->data['tableHeader'] = getSalesDtHeader($this->data['headData']->controller);
        $this->data['customerList'] = $this->party->getCustomerList();
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){        
        $result = $this->item->getDTRows($this->input->post(),1);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $itmStock = $this->store->getItemStock($row->id);
            $row->qty = 0;
            if(!empty($itmStock->qty)){$row->qty = $itmStock->qty;}
            $row->loginId = $this->loginId;
            $sendData[] = getProductData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addProduct(){
        $this->data['unitData'] = $this->item->itemUnits();
        $this->data['customerList'] = $this->party->getCustomerList();//print_r($this->data['customerList']);
        //$this->data['customerList'] = $this->party->getLastPartCode();
        $this->data['automotiveArray'] = $this->automotiveArray;
        $this->data['gstPercentage'] = $this->gstPercentage;
        $this->data['processData'] = $this->process->getProcessList();
		$this->data['materialGrades'] = explode(',', $this->item->getMasterOptions()->material_grade);
        $this->data['categoryList'] = $this->item->getCategoryList(1);
		$this->data['hsnData'] = $this->hsnModel->getHsnList();
        $this->load->view($this->productForm,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['item_name']))
            $errorMessage['item_name'] = "Part Name is required.";
        if(empty($data['unit_id']))
            $errorMessage['unit_id'] = "Unit is required.";
        if(empty($data['party_code']))
            $errorMessage['party_code'] = "Party Code is required.";
        if(empty($data['material_grade']))
        {
            if(!empty($data['gradeName']))
                $data['material_grade'] = $this->masterOption->saveGradeName($data['gradeName']);
        }
        unset($data['gradeName']);

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:

//             if($_FILES['drawing_file']['name'] != null || !empty($_FILES['drawing_file']['name'])):
//                 $this->load->library('upload');
// 				$_FILES['userfile']['name']     = $_FILES['drawing_file']['name'];
// 				$_FILES['userfile']['type']     = $_FILES['drawing_file']['type'];
// 				$_FILES['userfile']['tmp_name'] = $_FILES['drawing_file']['tmp_name'];
// 				$_FILES['userfile']['error']    = $_FILES['drawing_file']['error'];
// 				$_FILES['userfile']['size']     = $_FILES['drawing_file']['size'];
				
// 				$imagePath = realpath(APPPATH . '../assets/uploads/items/drawings');
// 				$config = ['file_name' => time()."_order_item_".$_FILES['userfile']['name'],'allowed_types' => '*','max_size' => 10240,'overwrite' => FALSE, 'upload_path'	=>$imagePath];

// 				$this->upload->initialize($config);
// 				if (!$this->upload->do_upload()):
// 					$errorMessage['drawing_file'] = $this->upload->display_errors();
// 					$this->printJson(["status"=>0,"message"=>$errorMessage]);
// 				else:
// 					$uploadData = $this->upload->data();
// 					$data['drawing_file'] = $uploadData['file_name'];
// 				endif;
// 			else:
// 				unset($data['drawing_file']);
// 			endif;
			
			if($_FILES['item_image']['name'] != null || !empty($_FILES['item_image']['name'])):
                $this->load->library('upload');
				$_FILES['userfile']['name']     = $_FILES['item_image']['name'];
				$_FILES['userfile']['type']     = $_FILES['item_image']['type'];
				$_FILES['userfile']['tmp_name'] = $_FILES['item_image']['tmp_name'];
				$_FILES['userfile']['error']    = $_FILES['item_image']['error'];
				$_FILES['userfile']['size']     = $_FILES['item_image']['size'];
				
				$imagePath = realpath(APPPATH . '../assets/uploads/items/');
				$config = ['file_name' => time()."_order_item_".$_FILES['userfile']['name'],'allowed_types' => '*','max_size' => 10240,'overwrite' => FALSE, 'upload_path'	=>$imagePath];

				$this->upload->initialize($config);
				if (!$this->upload->do_upload()):
					$errorMessage['item_image'] = $this->upload->display_errors();
					$this->printJson(["status"=>0,"message"=>$errorMessage]);
				else:
					$uploadData = $this->upload->data();
					$data['item_image'] = $uploadData['file_name'];
				endif;
			else:
				unset($data['item_image']);
			endif;

            $data['item_code'] = $data['party_code'].$data['item_code'];
            unset($data['processSelect'],$data['party_code']);
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->item->save($data));
        endif;
    }

    public function edit(){
        $id = $this->input->post('id');
        $this->data['unitData'] = $this->item->itemUnits();
        $this->data['customerList'] = $this->party->getCustomerList();
        $this->data['automotiveArray'] = $this->automotiveArray;
        $this->data['dataRow'] = $this->item->getItem($id);
        $this->data['gstPercentage'] = $this->gstPercentage;
        $this->data['processData'] = $this->process->getProcessList();
        $this->data['categoryList'] = $this->item->getCategoryList(1);
		$this->data['materialGrades'] = explode(',', $this->item->getMasterOptions()->material_grade);
        $this->data['productProcess'] = $this->item->getProductProcessForSelect($id);
		$this->data['hsnData'] = $this->hsnModel->getHsnList();
        $this->load->view($this->productForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->item->delete($id));
        endif;
    }

    public function addProductProcess(){
        $id = $this->input->post('id');        
        $this->data['processData'] = $this->process->getProcessList();
        $this->load->view($this->productProcessForm,$this->data);
    }

    public function saveProductProcess(){
        $data = $this->input->post();
        $errorMessage = "";

        if(empty($data['item_id']))
            $errorMessage .= "Somthing went wrong.";
        /* if(empty($data['process'][0]))
            $errorMessage .= " Pelase select product process."; */

        if(!empty($errorMessage)):
            $this->printJson(['status'=>2,'message'=>$errorMessage]);
        else:
            //$data['created_by'] = $this->session->userdata('loginId');
            $response = $this->item->saveProductProcess($data);
            $this->printJson($this->setProcessView($data['item_id']));
        endif;
    }

    public function setProcessView($id)
    {
        $processData = $this->item->getItemProcess($id);
        $operationData = $this->operation->getOperationList();
        $processHtml = '';
        if (!empty($processData)) :
            $i = 1; $html = ""; $options=Array(); $opt='';
            foreach ($processData as $row) :
                $opt='';
                $ops = $this->item->getProductOperationForSelect($row->id);
                foreach($operationData as $operation):
                    $selected = (!empty($ops) && (in_array($operation->id, explode(',',$ops)))) ? "selected" : "";
                     $opt .= '<option value="'.$operation->id.'" data-id="'.$row->id.'" '.$selected.'>'.$operation->operation_name.'</option>';
                endforeach;
                $options[$row->id] = $opt;
            endforeach;

            foreach ($processData as $row) :
                $processHtml .= '<tr id="'.$row->id.'">
                        <td class="text-center">'.$i++.'</td>
                        <td>'.$row->process_name.'</td>
                        <td class="text-center">'.$row->sequence.'</td>
                        <!--<td><select name="operationSelect" id="operationSelect'.$row->id.'" data-input_id="operation_id'.$row->id.'" class="form-control jp_multiselect operation_id" multiple="multiple">'.
                                $options[$row->id]
                            .'</select>
                            <input type="hidden" name="operation_id" id="operation_id'.$row->id.'" data-id="'.$row->id.'" value="'.$row->operation.'" /></td>-->
                        <td>
							<input type="text" class="form-control floatOnly finished_weight" id="finished_weight'.$row->id.'" data-id="'.$row->id.'" value="'.$row->finished_weight.'" onKeyUp="updateFinishedWeight('.$row->id.')">
						</td>   
                        <td>
                            <input type="text" class="form-control floatOnly setting_time" id="setting_time'.$row->id.'" data-id="'.$row->id.'" value="'.$row->setting_time.'" onKeyUp="updateSettingTime('.$row->id.')">
                        </td>
                        <td>
                            <input type="text" class="form-control tool_no" id="tool_no'.$row->id.'" data-id="'.$row->id.'" value="'.$row->tool_no.'" onKeyUp="updateToolNo('.$row->id.')">
                        </td> 
                    </tr>';
            endforeach;
        else :
            $processHtml .= '<tr><td colspan="4" class="text-center">No Data Found.</td></tr>';
        endif;
        return ['status' => 1, "processHtml" => $processHtml];
    }

    public function viewProductProcess(){
        $id = $this->input->post('id');
        $this->data['processDataList'] = $this->process->getProcessList();
        $this->data['operationDataList'] = $this->operation->getOperationList();
        $this->data['productProcess'] = $this->item->getProductProcessForSelect($id);
        $this->data['processData'] = $this->item->getItemProcess($id); 

		$this->data['productOperation']="";$options=Array();$opt='';
		foreach ($this->data['processData'] as $row) :
			$opt='';
			$ops = $this->item->getProductOperationForSelect($row->id);
			foreach($this->data['operationDataList'] as $operation):
				$selected = (!empty($ops) && (in_array($operation->id, explode(',',$ops)))) ? "selected" : "";
				 $opt .= '<option value="'.$operation->id.'" data-id="'.$row->id.'" '.$selected.'>'.$operation->operation_name.'</option>';
			endforeach;
			$options[$row->id] = $opt;
		endforeach;
		$this->data['productOperation'] = $options;
        $this->data['item_id'] = $id;   
        $this->load->view($this->viewProductProcess,$this->data);
    }

    public function updateProductProcessSequance(){
        $data = $this->input->post();
		$errorMessage = array();		
		if(empty($data['id']))
			$errorMessage['id'] = "Item ID is required.";
		
		if(empty($errorMessage)):
			$this->printJson($this->item->updateProductProcessSequance($data));			
		endif;
    }

    public function addProductKitItems(){
        $id = $this->input->post('id');
        $this->data['productKitData'] = $this->item->getProductKitData($id);
        $this->data['rawMaterial'] = $this->item->getItemLists("3");
        $this->data['process'] = $this->item->getProductWiseProcessList($id);
        $this->load->view($this->productKitItem,$this->data);
    }

    public function saveProductKit(){
        $data = $this->input->post();
		$errorMessage = array();
		if(empty($data['ref_item_id'][0])){
			$errorMessage['kit_item_id'] = "Item Name is required.";
		}
		if(empty($data['qty'][0])){
			$errorMessage['kit_item_qty'] = "Qty. is required.";
		}
		
		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
			$this->printJson($this->item->saveProductKit($data));
		endif;
    }
	
    public function saveProductOperation(){
        $data = $this->input->post();
        $this->printJson($this->item->saveProductOperation($data));
    }
    
    public function getItemData(){
		$id = $this->input->post('itemId');
        $result = $this->item->getItem($id);
        $unitData = $this->item->itemUnit($result->unit_id);
        $result->unit_name = $unitData->unit_name;
        $result->description = $unitData->description;
		$this->printJson($result);
	}
    
    public function getFgRevision(){
        $item_id = $this->input->post('id');
        $this->data['dataRow'] = $this->item->getFgRevision($item_id);
        $this->data['item_id'] = $item_id;
        $this->load->view($this->fgRevision,$this->data);
    }

    public function updateFgRevision(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['date']))
            $errorMessage['date'] = "Date is required.";
        if(empty($data['change_reason']))
            $errorMessage['change_reason'] = "Change Reason is required.";
        if(empty($data['description']))
            $errorMessage['description'] = "Description is required.";
        if(empty($data['new_rev_no']))
            $errorMessage['new_rev_no'] = "Revision No is required.";
        if(empty($data['new_specs']))
            $errorMessage['new_specs'] = "Specification is required.";
       
        if($data['feasibility_status'] =='Yes')
            $errorMessage['feasibilty_remark'] = "Feasibilty Remarkis required.";
        if(empty($data['fg_stock']))
            $errorMessage['fg_stock'] = "Fg Stock is required.";
      
        if($data['cost_effect'] =='Yes')
            $errorMessage['cost_remark'] = "Cost Remark is required.";
        if(empty($data['auth_required']))
            $errorMessage['auth_required'] = "Cft Auth is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $itemData=$this->item->getItem($data['item_id']);
            $data['old_rev_no']=$itemData->rev_no;
            $data['old_specs']=$itemData->rev_specification;
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->item->saveFgRevision($data));
        endif;
    }

}
?>