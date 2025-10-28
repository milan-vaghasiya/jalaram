<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class Items extends MY_Controller
{
    private $indexPage = "item/index";
    private $itemForm = "item/form";
    private $itemStockUpdateForm = "item/stock_update";
    private $itemOpeningStockForm = "item/opening_update";
    private $importData = "item/import";
    private $indexPacking = "packing_material/index";
    private $formPacking = "packing_material/form";
    private $gstPercentage = Array(["rate"=>0,"val"=>'NIL'],["rate"=>0.25,"val"=>'0.25%'],["rate"=>3,"val"=>'3%'],["rate"=>5,"val"=>'5%'],["rate"=>12,"val"=>'12%'],["rate"=>18,"val"=>'18%'],["rate"=>28,"val"=>'28%']);
	
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Items";
		$this->data['headData']->controller = "items";
	}
	
	public function index(){
        $this->data['headData']->pageUrl = "items/pitems";
        $this->data['item_type'] = 3;
        $this->data['categoryList'] = $this->item->getCategoryList(3);
        $this->data['tableHeader'] = getDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }
	
	public function pitems(){
	    $this->data['headData']->pageTitle = "Raw Material";
        $this->data['headData']->pageUrl = "items/pitems";
        $this->data['item_type'] = 3;
        $this->data['categoryList'] = $this->item->getCategoryList(3);
        $this->data['tableHeader'] = getPurchaseDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function consumable(){
        $this->data['headData']->pageUrl = "items/consumable";
        $this->data['item_type'] = 2;
        $this->data['categoryList'] = $this->item->getCategoryList(2);
        $this->data['tableHeader'] = getStoreDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($item_type){  

        $result = $this->item->getDTRows($this->input->post(),$item_type); 
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $itmStock = $this->store->getItemStockRTD($row->id,$row->item_type);
            $row->qty = 0;
            if(!empty($itmStock->qty)){ $row->qty = $itmStock->qty;}
            $sendData[] = getItemData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    /* Updated at : 09-12-2021 [Milan chauhan] */
    public function addItem($item_type){
        $this->data['item_type'] = $item_type;
        $this->data['unitData'] = $this->item->itemUnits();
        $this->data['gstPercentage'] = $this->gstPercentage;
        $this->data['categoryList'] = $this->item->getCategoryList($item_type);
        $this->data['subGroup'] = $this->item->getSubGroupList();
        $this->data['materialGrade'] = $this->materialGrade->getMaterialGrades();
        $this->load->view($this->itemForm,$this->data);
    }
    
    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
		
		if($data['item_type'] == 3){
			if(empty($data['itmsize']) AND empty($data['itmshape']) AND empty($data['itmbartype']) AND empty($data['itmmaterialtype']))
				$errorMessage['item_name'] = "Item Name is required.";
		}else{
			if(empty($data['item_name']))
				$errorMessage['item_name'] = "Item Name is required.";
		}
        if(empty($data['unit_id']))
            $errorMessage['unit_id'] = "Unit is required.";
        if(empty($data['category_id']))
            $errorMessage['category_id'] = "Category is required.";
        if(empty($data['gst_per']))
            $errorMessage['gst_per'] = "GST is required.";

        if($data['batch_stock'] == 2 && empty($data['item_code'])){
            $errorMessage['item_code'] = "Item Code is required.";
        }
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
			if($data['item_type'] == 3):
			    
				$data['item_name'] = (!empty($data['itmsize']))?$data['itmsize'].' ':'';
				$data['item_name'] .= (!empty($data['itmshape']))?$data['itmshape'].' ':'';
				$data['item_name'] .= (!empty($data['itmbartype']))?$data['itmbartype'].' ':'';
				$data['item_name'] .= (!empty($data['itmmaterialtype']))?$data['itmmaterialtype']:'';
    			$data['item_image'] =  $data['itmsize'] . '~@' . $data['itmshape'] . '~@' . $data['itmbartype'] . '~@' . $data['itmmaterialtype'];
				$data['size'] = $data['itmsize'];
				
				unset($data['itmsize'],$data['itmshape'],$data['itmbartype'],$data['itmmaterialtype']);
            else:
                $size = Array();
                if(!empty($data['diameter'])){$size[] = $data['diameter'];}
                if(!empty($data['flute_length'])){$size[] = $data['flute_length'];}
                if(!empty($data['length'])){$size[] = $data['length'];}
                $data['size'] = (!empty($size)) ? implode('X',$size) : NULL;
                
                unset($data['diameter'],$data['length'],$data['flute_length']);
			endif;
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->item->save($data));
        endif;
    }

    /* Updated at : 09-12-2021 [Milan chauhan] */
    public function edit(){
        $id = $this->input->post('id');
        $this->data['unitData'] = $this->item->itemUnits();
        $this->data['gstPercentage'] = $this->gstPercentage;
        $this->data['dataRow'] = $this->item->getItem($id);
        $this->data['categoryList'] = $this->item->getCategoryList($this->data['dataRow']->item_type);
        $this->data['subGroup'] = $this->item->getSubGroupList();
        $this->data['materialGrade'] = $this->materialGrade->getMaterialGrades();
        $this->load->view($this->itemForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->item->delete($id));
        endif;
    }

    public function addStockTrans(){
        $id = $this->input->post('id');
        $this->data['stockTransData'] = $this->item->getStockTrans($id);
        $this->load->view($this->itemStockUpdateForm,$this->data);
    }

    public function saveStockTrans(){
        $data = $this->input->post();
        if(empty($data['trans_date']))
            $errorMessage['trans_date'] = "Date is required.";
        if(empty($data['qty']))
            $errorMessage['qty'] = "Quantity is required.";
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
		    $this->printJson(["status"=>1,"stockData"=>$this->item->saveStockTrans($data)]);
        endif;
	}

    public function deleteStockTrans(){
		$id = $this->input->post('id');
		$this->printJson($this->item->deleteStockTrans($id));
	}
	
	public function addOpeningStock(){
        $id = $this->input->post('id');
        $this->data['openingStockData'] = $this->item->getItemOpeningTrans($id);
        $this->data['locationData'] = $this->store->getStoreLocationList();
        $this->load->view($this->itemOpeningStockForm,$this->data);
    }

    public function saveOpeningStock(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['location_id']))
            $errorMessage['location_id'] = "Store Location is required.";
        if(empty($data['qty']))
            $errorMessage['qty'] = "Qty. is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['ref_date'] = $this->startYearDate;
            $data['created_by'] = $this->session->userdata('loginId');
            //print_r($data);exit;
            $this->printJson($this->item->saveOpeningStock($data));
        endif;
    }

    public function deleteOpeningStockTrans(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->item->deleteOpeningStockTrans($id));
        endif;
    }
    
    public function getItemData(){
		$id = $this->input->post('itemId');
        $result = $this->item->getItem($id);
        $unitData = $this->item->itemUnit($result->unit_id);
        $result->unit_name = $unitData->unit_name;
        $result->description = $unitData->description;
		$this->printJson($result);
	}
	
    public function importRM(){
        $this->load->view($this->importData,$this->data);
    }

    public function importRMExcel(){
        
		$postData = $this->input->post(); 
		$insp_excel = '';
		if(isset($_FILES['insp_excel']['name']) || !empty($_FILES['insp_excel']['name']) ):
			$this->load->library('upload');
			$_FILES['userfile']['name']     = $_FILES['insp_excel']['name'];
			$_FILES['userfile']['type']     = $_FILES['insp_excel']['type'];
			$_FILES['userfile']['tmp_name'] = $_FILES['insp_excel']['tmp_name'];
			$_FILES['userfile']['error']    = $_FILES['insp_excel']['error'];
			$_FILES['userfile']['size']     = $_FILES['insp_excel']['size'];
			
			$imagePath = realpath(APPPATH . '../assets/uploads/items');
			$config = ['file_name' => "items_".$_FILES['userfile']['name'],'allowed_types' => '*','max_size' => 10240,'overwrite' => FALSE, 'upload_path' =>$imagePath];

			$this->upload->initialize($config);
			if (!$this->upload->do_upload()):
				$errorMessage['insp_excel'] = $this->upload->display_errors();
				$this->printJson(["status"=>0,"message"=>$errorMessage]);
			else:
				$uploadData = $this->upload->data();
				$insp_excel = $uploadData['file_name'];
			endif;
			if(!empty($insp_excel))
			{
				$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($imagePath.'/'.$insp_excel); 
				$fileData = array($spreadsheet->getSheetByName('items')->toArray(null,true,true,true));
				$fieldArray = Array();
				
				if(!empty($fileData))
				{
					$fieldArray = $fileData[0][1];$row = 0;
					for($i=2;$i<=count($fileData[0]);$i++)
					{
						$rowData = Array();$c='A';
						foreach($fileData[0][$i] as $key=>$colData):
							$rowData[strtolower($fieldArray[$c++])] = $colData;
						endforeach;
						$this->item->saveImportRM($rowData);
                        $row++;
					}
					
				}
				$this->printJson(['status'=>1,'message'=>$row.' Record updated successfully.']);
			}
			else{$this->printJson(['status'=>0,'message'=>'Data not found...!']);}
		else:
			$this->printJson(['status'=>0,'message'=>'Please Select File!']);
		endif;
    }

    // Created By Meghavi @29/006/2023
    public function packingMaterial(){
        $this->data['headData']->pageUrl = "items/packingMaterial";
        $this->data['item_type'] = 9;
        $this->data['tableHeader'] = getDispatchDtHeader('packingMaterial');
        $this->load->view($this->indexPacking,$this->data);
    }
    
    public function getPackingMaterialDTRows($item_type){ 
        $result = $this->item->getDTRows($this->input->post(),$item_type);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
			$itmStock = $this->store->getItemStockRTD($row->id,$row->item_type);
            $row->stock_qty = 0;
            if(!empty($itmStock->qty)){ $row->stock_qty = $itmStock->qty;}
            $sendData[] = getPackingItemData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }
    
    public function addPackingMaterial($item_type){
        $this->data['item_type'] = $item_type;
        $this->data['unitData'] = $this->item->itemUnits();
        $this->data['categoryList'] = $this->item->getCategoryList($item_type);
        $this->data['hsnData'] = $this->hsnModel->getHsnList();
        $this->load->view($this->formPacking,$this->data);
    }

    public function savePackingMaterial(){
        $data = $this->input->post(); 
        $errorMessage = array();
        if(empty($data['unit_id']))
            $errorMessage['unit_id'] = "Unit is required.";
        if($data['make_brand'] == 'Polythin'){
            if(empty($data['max_tvalue_per']) OR empty($data['min_tqty_per']))
                $errorMessage['item_name'] = "Item Name is required.";
            if(empty($data['material_spec']))
                $errorMessage['material_spec'] = "Micron is required.";
        }elseif($data['make_brand'] == 'Box'){
            if(empty($data['max_tvalue_per']) OR empty($data['min_tqty_per']) OR empty($data['max_tqty_per']) OR empty($data['typeof_machine']))
                $errorMessage['item_name'] = "Item Name is required.";
        }else{
            if(empty($data['item_name']))
                $errorMessage['item_name'] = "Item Name is required.";
        }

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            if($data['make_brand'] == 'Polythin'):
				$data['full_name'] = trim($data['item_code']).' '.trim($data['category_name']).' ('.trim($data['max_tvalue_per']).'X'.trim($data['min_tqty_per']).') '.trim($data['material_spec']).' Mcr';
				$data['item_name'] = $data['full_name'];

            elseif($data['make_brand'] == 'Box'):
                $data['full_name'] = trim($data['item_code']).' '.trim($data['category_name']).' ('.trim($data['max_tvalue_per']).'X'.trim($data['min_tqty_per']).'X'.trim($data['max_tqty_per']).') '.trim($data['typeof_machine']).' PLY';
				$data['item_name'] = $data['full_name'];
            else:

                $fname = Array();
                if(!empty($data['item_code'])){$fname[] = trim($data['item_code']);}
                if(!empty($data['category_name'])){$fname[] = trim($data['category_name']);}
                if(!empty($data['item_name'])){$fname[] = trim($data['item_name']);}
                $data['full_name'] = (!empty($fname)) ? implode(' ',$fname) : '';
                $data['full_name'] = trim($data['item_code']).' '.trim($data['category_name']).' '.trim($data['item_name']);
				$data['item_name'] = $data['full_name'];

            endif;
            unset($data['category_name']);
            
			if(!empty($data['hsn_code'])):
			    $hsnData = $this->hsnModel->getHSNDetailByCode($data['hsn_code']);
				$data['gst_per'] = $hsnData->igst;
			endif;
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->item->save($data));
        endif;
    }

    public function editPackingMaterial(){
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->item->getItem($id);
        $this->data['unitData'] = $this->item->itemUnits();
        $this->data['categoryList'] = $this->item->getCategoryList(9);
        $this->data['hsnData'] = $this->hsnModel->getHsnList();
        $this->load->view($this->formPacking,$this->data);
    }
}
?>