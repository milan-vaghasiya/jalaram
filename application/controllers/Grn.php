<?php
defined( 'BASEPATH' )OR exit( 'No direct script access allowed' );
class Grn extends MY_Controller
{
	private $indexPage = "grn/index";
	private $grnForm = "grn/form";
    private $material_inspection = "grn/material_inspection";
	private $inInspection = "grn/in_inspection";
	private $testReport = "grn/test_report";
	private $deviationReport = "grn/deviation_report";
	private $approve_inspection = "grn/approve_inspection";
	private $updatetestReport = "grn/update_test_report";
	
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Goods Receipt Note";
		$this->data['headData']->controller = "grn";
	}

	public function index(){
		$this->data['headData']->pageUrl = "grn";
        $this->data['tableHeader'] = getStoreDtHeader($this->data['headData']->controller);
		$this->load->view($this->indexPage,$this->data);
	}

    public function getDTRows(){
		$result = $this->grnModel->getDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;        
            
            $row->product_code = ''; $c=0;
			if(!empty($row->fgitem_id)):
				$la = explode(",",$row->fgitem_id);
				if(!empty($la)){
					foreach($la as $fgid){
						$fg = $this->grnModel->getFinishGoods($fgid);
						if(!empty($fg)):
							if($c==0){
                                $row->product_code .= $fg->item_code;
                            }else{
                                $row->product_code .= '<br>'.$fg->item_code;
                            }$c++;
						else:
							$row->product_code = "";
						endif;
					}
				}
			endif; 
            
            $row->controller = "grn";    
            $sendData[] = getGRNData($row);
        endforeach;        
        $result['data'] = $sendData;
        $this->printJson($result);
    }
	
	public function addGRN(){
		$this->data['nextGrnNo'] = $this->grnModel->nextGrnNo();
		$this->data['grn_prefix'] = 'GRN/'.$this->shortYear.'/';
		$this->data['itemData'] = '';
        $this->data['itemTypeData'] = $this->item->getItemGroup();
        $this->data['unitData'] = $this->item->itemUnits();
        $this->data['partyData'] = $this->party->getPartyList();
		$this->data['locationData'] = $this->store->getStoreLocationListWithoutProcess();
		$this->data['fgItemList'] = $this->purchaseOrder->getItemList(1);		
		$this->data['colorList'] = explode(',',$this->grnModel->getMasterOptions()->color_code);
		$this->data['soList'] = '';
		$this->load->view($this->grnForm,$this->data);
	}

	public function createGrn(){
		$data = $this->input->post();
		if($data['ref_id']):
			$orderItems = $this->purchaseOrder->getOrderItems($data['ref_id']);
			$orderData = new stdClass();
			$orderData->party_id = $data['party_id'];
			$orderData->id = implode(",",$data['ref_id']);
			$this->data['orderItems'] = $orderItems;
			$this->data['orderData'] = $orderData;
			$year = (date('m') > 3)?date('y').'-'.(date('y') +1):(date('y')-1).'-'.date('y');
			$this->data['nextGrnNo'] = $this->grnModel->nextGrnNo();
			$this->data['grn_prefix'] = 'GRN/'.$year.'/';
			$this->data['itemData'] = $this->purchaseOrder->getItemList();
			$this->data['itemTypeData'] = $this->item->getItemGroup();
			$this->data['unitData'] = $this->item->itemUnits();
			$this->data['partyData'] = $this->party->getPartyList();
			$this->data['locationData'] = $this->store->getStoreLocationListWithoutProcess();
			$this->data['fgItemList'] = $this->purchaseOrder->getItemList(1);
			$this->data['colorList'] = explode(',',$this->grnModel->getMasterOptions()->color_code);
			$this->load->view($this->grnForm,$this->data);
		else:
			return redirect(base_url('purchaseOrder'));
		endif;
	}
	
	public function getItemData(){
		$id = $this->input->post('itemId');
        $result = $this->item->getItem($id);
        $result->unit_name = $this->item->itemUnit($result->unit_id)->unit_name;
		$this->printJson($result);
    }

	public function getItemListForSelect(){
		$item_type = $this->input->post('item_type');
		$item_id = $this->input->post('item_id'); 
        $result = $this->purchaseOrder->getItemListForSelect($item_type);
		$options="";
		if(!empty($result)): 
			$options .= '<option value="">Select Item Name</option>';
			foreach($result as $row):
				$selected = (!empty($item_id) && $item_id == $row->id)?'selected':'';
				$item_name = (!empty($row->item_code))? "[".$row->item_code."] ".$row->item_name : $row->item_name;
				$options .= "<option data-row='".json_encode($row)."' value='".$row->id."' ".$selected.">".$item_name."</option>";
			endforeach;
		else:
			$options .= '<option value="">Select Item Name</option>';
		endif;
		
		$this->printJson(['status'=>1, 'options'=>$options]);
	}
    
	public function save(){
		$data = $this->input->post();
		$errorMessage = array();
		
		if(empty($data['grn_no']))
			$errorMessage['grn_no'] = "GRN No. is required.";
		if(empty($data['party_id']))
			$errorMessage['party_id'] = "Supplier Name is required.";
		if(empty($data['item_id'][0]))
			$errorMessage['general_error'] = "Item is required.";
		if(empty($data['type']))
			$errorMessage['type'] = "Grn Type is required.";

		if(!empty($data['item_id'])):
			foreach($data['location_id'] as $key=>$value):
				if(empty($value)):
					$errorMessage['general_error'] = "Location is required.";
					break;
				endif;
			endforeach;
		endif;
		
		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
			$masterData = [ 
				'id' => $data['grn_id'],
				'type' => $data['type'],
				'order_id' => $data['order_id'],
				'grn_prefix' => $data['grn_prefix'], 
				'grn_no' => $data['grn_no'], 
				'grn_date' => date('Y-m-d',strtotime($data['grn_date'])),
				'party_id' => $data['party_id'], 
				'challan_no' => $data['challan_no'], 
				'remark' => $data['remark'],
                'created_by' => $this->session->userdata('loginId')
			];
							
			$itemData = [
				'id' => $data['trans_id'],
				'item_id' => $data['item_id'],
				'item_type' => $data['item_type'],
				'unit_id' => $data['unit_id'],
				'fgitem_id' => $data['fgitem_id'],
                'fgitem_name' => $data['fgitem_name'],
				'batch_no' => $data['batch_no'],
				'po_trans_id' => $data['po_trans_id'],
				'so_id' => $data['so_id'],
				'location_id' => $data['location_id'],
				'qty' => $data['qty'],				
				'qty_kg' => $data['qty_kg'],
				'price' => $data['price'],
				'disc_per' => $data['disc_per'],
				'color_code' => $data['color_code'],
				'item_remark' => $data['item_remark'],
                'created_by' => $this->session->userdata('loginId')
			];
			
			$this->printJson($this->grnModel->save($masterData,$itemData));
		endif;
	}
	
	public function edit($id){
		if(empty($id)):
			return redirect(base_url('grn'));
		else:
			$this->data['grnData'] = $this->grnModel->editInv($id);
			$this->data['itemData'] = $this->purchaseOrder->getItemLists("2,3,4,5,6,7");
			$this->data['itemTypeData'] = $this->item->getItemGroup();
            $this->data['unitData'] = $this->item->itemUnits();
            $this->data['partyData'] = $this->party->getPartyList();
			$this->data['locationData'] = $this->store->getStoreLocationListWithoutProcess();
			$this->data['fgItemList'] = $this->purchaseOrder->getItemList(1);
			$this->data['colorList'] = explode(',',$this->grnModel->getMasterOptions()->color_code);
			$this->data['soList'] = $this->grnModel->getSoListForSelect($this->data['grnData']->party_id); 
			$this->load->view($this->grnForm,$this->data);
		endif;
	}
	
	public function delete(){
		$id = $this->input->post('id');
		if(empty($id)):
			$this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
		else:
			$this->printJson($this->grnModel->delete($id));
		endif;
	}

	public function rmIqc($item_type = 3){
		$this->data['item_type'] = $item_type;
		$this->data['headData']->pageUrl = "grn/rmIqc";
		$this->data['tableHeader'] = getQualityDtHeader('materialInspection');
		$this->data['fgItemList'] = $this->purchaseOrder->getItemList(1);
		$this->load->view($this->material_inspection,$this->data);
	}
	
	public function otherIqc($item_type = 3){
		$this->data['item_type'] = $item_type;
		$this->data['headData']->pageUrl = "grn/otherIqc/0";
		$this->data['tableHeader'] = getQualityDtHeader('materialInspection');
		$this->data['fgItemList'] = $this->purchaseOrder->getItemList(1);
		$this->load->view($this->material_inspection,$this->data);
	}

    // radhika 15-9-21
    public function purchaseMaterialInspectionList($item_type=3,$status=0){
		$data = $this->input->post(); $data['status'] = $status; $data['item_type'] = $item_type;
        $result = $this->grnModel->purchaseMaterialInspectionList($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;        
            
			$row->status_label=""; $row->approve_status_label="";
			
			if($row->inspected_qty == "0.000"){
				$row->status_label = '<span class="badge badge-pill badge-danger m-1">Pending</span>';
			}else{
			    $insp_by = (!empty($row->insp_name)? '<br><small>'.$row->insp_name.' ('.formatDate($row->inspection_date).')</small>' :'');
			    
				if($row->inspection_status == "Ok"){
					$row->status_label = '<span class="badge badge-pill badge-success m-1">Accepted</span>'.$insp_by;
				}else{
					$row->status_label ='<span class="badge badge-pill badge-danger m-1">Rejected</span>'.$insp_by;
				}
			}
			
			if($row->is_approve == 0){
				$row->approve_status_label ='<span class="badge badge-pill badge-danger m-1">UnApproved</span>';
			}else{
			    $approve = (!empty($row->approve_name)? '<br><small>'.$row->approve_name.' ('.formatDate($row->approve_date).')</small>' :'');
				$row->approve_status_label ='<span class="badge badge-pill badge-success m-1">Approved</span>'.$approve;
			}
			
            $row->controller = "purchaseInvoice";
			$sendData[] = getPurchaseMaterialInspectionData($row);
        endforeach;        
        $result['data'] = $sendData;
        $this->printJson($result);
    }

	public function getInspectedMaterial(){
		$id = $this->input->post('id');
		$this->printJson($this->grnModel->getInspectedMaterial($id));
	}
	
	public function inspectedMaterialSave(){
		$data = $this->input->post();
		$errorMessage = array();
		$i=1;$total_qty = 0;
		foreach($data['item_id'] as $key=>$value):
			$inspected_qty = ($data['inspection_status'][$key] == "Ok")?($data['recived_qty'][$key] - $data['short_qty'][$key]):0;
			$data['reject_qty'][$key] = ($data['inspection_status'][$key] != "Ok")?$data['recived_qty'][$key]:0;
			$data['short_qty'][$key] = ($data['inspection_status'][$key] == "Ok")?$data['short_qty'][$key]:0;

			$total_qty = $inspected_qty + $data['ud_qty'][$key] + $data['reject_qty'][$key] + $data['scrape_qty'][$key];			
			if($total_qty > $data['recived_qty'][$key]):
				$errorMessage['recived_qty'.$i] = "Received Qty. mismatched.";
			endif;
			$i++;
		endforeach;

		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
            $data['created_by'] = $this->session->userdata('loginId');
			$this->printJson($this->grnModel->inspectedMaterialSave($data));
		endif;
    }	

	public function getItemsForGRN(){
		$party_id = $this->input->post('party_id');
		$this->printJson(["status" => 1,"itemOptions" => $this->grnModel->getItemsForGRN($party_id)]);
	}
	
	public function itemColorCode(){
		$this->printJson($this->grnModel->itemColorCode());
	}
	
	public function setFGItems(){
		$fgitem_id = $this->input->post('fgitem_id');	
		$fgItemList = $this->purchaseOrder->getItemList(1);		
		$fgOpt = '';
		if(!empty($fgItemList) ):
			foreach($fgItemList as $row):
				$selected = '';
				if(!empty($fgitem_id)){if (in_array($row->id,explode(',',$fgitem_id))) {$selected = "selected";}}
				$fgOpt .= '<option value="'.$row->id.'" '.$selected.'>'.$row->item_code.'</option>';
			endforeach;
		endif;
		$this->printJson(['status'=>1,'fgOpt'=>$fgOpt]);
	}
	
	public function migrateGrnItems(){
		$grnItems = $this->db->select('id,item_id')->where('is_delete',0)->get('grn_transaction')->result();
		foreach($grnItems as $row):
			$itemData = $this->item->getItem($row->item_id);
			$this->db->where('id',$row->id)->update('grn_transaction',['item_type'=>$itemData->item_type]);
		endforeach;
		echo "Migrate Success.";exit;
	}

	public function inInspection($id){
        $this->data['dataRow'] = $dataRow = $this->grnModel->getInInspectionMaterial($id);
		$this->data['inInspectData'] = $inInspectData = $this->grnModel->getInInspection($id);
		if(!empty($inInspectData)){
			$this->data['paramData'] =  $this->controlPlanV2->getCPDimenstion(['item_id'=>$inInspectData->fg_item_id,'stage_type'=>1,'responsibility'=>'INSP','control_method'=>'IIR','rev_no'=> $inInspectData->pfc_rev_no]);
			$this->data['pfcRevList'] = $this->ecn->getCpRevData(['item_id'=>$inInspectData->fg_item_id,'status'=>3]);
		}
		$this->data['fgList'] = $this->productionReportsNew->getProductionBomData(['ref_item_id'=>$dataRow->item_id,'group_by'=>'item_id']);
		$this->data['sampleSize'] =  $this->reactionPlan->getSampleSize($dataRow->qty,'IIR');
		$this->load->view($this->inInspection,$this->data);
	}
	
	public function inInspectionData(){
		$data = $this->input->post(); 
		$paramData = $this->controlPlanV2->getCPDimenstion(['item_id'=>$data['item_id'],'stage_type'=>1,'responsibility'=>'INSP','control_method'=>'IIR','rev_no'=>$data['pfc_rev_no']]);
        
		$tbodyData="";$i=1; 
		
		$param = (array)$paramData; 
		$iir_lot_time = array_filter($param,function($item){ return $item->iir_freq_time == 'Lot'; }); 
		$iir_size_column = array_column($iir_lot_time,'iir_size');
		$iir_size = (!empty($iir_size_column))?max($iir_size_column):10; 
		$sample_size = (!empty($iir_size)?$iir_size:10);
        $inInspectData = $this->grnModel->getInInspection($data['grn_trans_id']);                                           
		if(!empty($paramData)):
			foreach($paramData as $row):
				$obj = New StdClass;
				$cls="";
				if(!empty($row->lower_limit) OR !empty($row->upper_limit)):
					$cls="floatOnly";
				endif;
				$diamention ='';
				if($row->requirement==1){ $diamention = $row->min_req.'/'.$row->max_req ; }
				if($row->requirement==2){ $diamention = $row->min_req.' '.$row->other_req ; }
				if($row->requirement==3){ $diamention = $row->max_req.' '.$row->other_req ; }
				if($row->requirement==4){ $diamention = $row->other_req ; }
				if(!empty($inInspectData)):
					$obj = json_decode($inInspectData->observation_sample); 
				endif;
				$inspOption = '';
				$inspOption  = '<option value="Ok" '.((!empty($obj->{$row->id}) && ($obj->{$row->id}[$sample_size]=='Ok'))?'selected':'').' >Ok</option>
								<option value="Not Ok" '.((!empty($obj->{$row->id}) && ($obj->{$row->id}[$sample_size]=='Not Ok'))?'selected':'').'>Not Ok</option>';
				$tbodyData.= '<tr>
							<td style="text-align:center;">'.$i++.'</td>
							<td>' . $row->product_param . '</td>
							<td>' . $diamention . '</td>
							<td>' . $row->iir_measur_tech . '</td>';
				for($c=0;$c<$sample_size;$c++):
					if(!empty($obj->{$row->id})):
						$tbodyData .= '<td><input type="text" name="sample'.($c+1).'_'.$row->id.'" id="sample'.($c+1).'_'.$i.'" class="form-control text-center parameter_limit'.$cls.'" value="'.$obj->{$row->id}[$c].'" data-min="'.$row->min_req.'" data-max="' . $row->max_req . '" data-requirement="' . $row->requirement . '" data-row_id ="'.$i.'" ></td>';
					else:
						$tbodyData .= '<td><input type="text" name="sample'.($c+1).'_'.$row->id.'" id="sample'.($c+1).'_'.$i.'" class="form-control text-center parameter_limit'.$cls.'" value=""  data-min="'.$row->min_req.'" data-max="' . $row->max_req . '" data-requirement="' . $row->requirement . '" data-row_id ="'.$i.'"></td>';
					endif;
				endfor;
				if(!empty($obj->{$row->id})):
					$tbodyData .= '<td style="width:100px;"><select name="status_'.$row->id.'" id="status_'.$i.'" class="form-control  text-center" value="'.$obj->{$row->id}[$sample_size].'">'.$inspOption.'</select></td></tr>';
				else:
					$tbodyData .= '<td style="width:100px;"><select name="status_'.$row->id.'" id="status_'.$i.'" class="form-control  text-center" value="">'.$inspOption.'</select></td></tr>';
				endif;
				
			endforeach;
		else:
			$tbodyData .='<tr><th colspan="'.(5+$sample_size).'">No data available.</th></tr>';
		endif;
		
		$theadData = '<tr style="text-align:center;">
			<th rowspan="2" style="width:5%;">#</th>
            <th rowspan="2">Product/Process Char.</th>
            <th rowspan="2">Specification</th>
            <th rowspan="2">Measurement Tech.</th>
			<th colspan="'.$sample_size.'">Observation on Samples</th>
			<th rowspan="2">Status</th>
        </tr>
        <tr style="text-align:center;">';
        
        for($c=0;$c<$sample_size;$c++):
           $theadData .= '<th>'.($c+1).'</th>';
        endfor;
        
        $theadData .= '</tr>';
		
		$this->printJson(['status'=>1,"theadData"=>$theadData,"tbodyData"=>$tbodyData,'sample_size'=>$sample_size]);
	}

	public function saveInInspection(){
		$data = $this->input->post();
        $errorMessage = Array();

		if(empty($data['item_id'])){
            $errorMessage['item_id'] = "Item is required.";
		}
		if(empty($data['fg_item_id'])){
            $errorMessage['fg_item_id'] = "Finish Good is required.";
		}
		if(empty($data['pfc_rev_no'])){
            $errorMessage['pfc_rev_no'] = "Revision No. is required.";
		}
		$insParamData =  $this->controlPlanV2->getCPDimenstion(['item_id'=>$data['fg_item_id'],'stage_type'=>1,'responsibility'=>'INSP','control_method'=>'IIR','rev_no'=>$data['pfc_rev_no']]);
        if(count($insParamData) <= 0)
            $errorMessage['general'] = "Item Parameter is required.";

        $pre_inspection = Array();$param_ids = Array();$data['observation_sample'] = '';
        if(!empty($insParamData)):
            foreach($insParamData as $row):
                $param = Array();
                for($j = 1; $j <= $data['sample_size']; $j++):
                    $param[] = $data['sample'.$j.'_'.$row->id];
                    unset($data['sample'.$j.'_'.$row->id]);
                endfor;
                $param[] = $data['status_'.$row->id];
                $pre_inspection[$row->id] = $param;
				$param_ids[] = $row->id;
                unset($data['status_'.$row->id]);
            endforeach;
        endif;
		unset($data['sample_size']);
		$data['parameter_ids'] = implode(',',$param_ids);
        $data['observation_sample'] = json_encode($pre_inspection);
        $data['param_count'] = count($insParamData);
		
		if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
			$data['trans_date'] = date("Y-m-d");
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->grnModel->saveInInspection($data));
        endif;
	}

	public function getGrnOrders(){ 
		$party = $this->input->post('party_id'); 
		$this->printJson($this->grnModel->getGrnOrders($party));
	}
	
	public function inInspection_pdf($id){
		$this->data['inInspectData'] = $inInspectData = $this->grnModel->getInInspection($id);
		$paramData = [] ;$controlMethodArray = [];$prepareBy="";$approveBy="";
		if(!empty($this->data['inInspectData'])){
			$this->data['paramData'] =  $this->controlPlanV2->getCPDimenstion(['item_id'=>$this->data['inInspectData']->fg_item_id,'stage_type'=>1,'responsibility'=>'INSP','rev_no'=>$this->data['inInspectData']->pfc_rev_no]);
			
			$inInspectData = $this->data['inInspectData'];
			$inInspectData->fgCode="";
			if(!empty($inInspectData->fgitem_id)):
				$fgId = explode(',', $inInspectData->fgitem_id); $i=1; 
				foreach($fgId as $key=>$value):
					$fgData = $this->grnModel->getFinishGoods($value);
					if($i==1){ $inInspectData->fgCode.=$fgData->item_code; }
					else{ $inInspectData->fgCode.= ', '.$fgData->item_code; } $i++;
				endforeach;
			endif;
		}
		
		$logo=base_url('assets/images/logo.png');
		
		$pdfData = $this->load->view('grn/printInInspection',$this->data,true);

		$htmlHeader = '<table class="table">
							<tr>
								<td style="width:25%;"><img src="'.$logo.'" style="height:50px;"></td>
								<td class="org_title text-center" style="font-size:1rem;width:50%">RAW MATERIAL RECIVING INSPECTION REPORT</td>
								<td style="width:25%;" class="text-right"><span style="font-size:0.8rem;">F QA 01 00(01/06/2020)</td>
							</tr>
						</table><hr>';
		$htmlFooter = '<table class="table top-table" style="margin-top:10px;">
						<tr>
							<td style="width:25%;"></td>
							<td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
						</tr>
					</table>';
		
		$mpdf = new \Mpdf\Mpdf();
		$pdfFileName='DC-REG-'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetWatermarkImage($logo,0.03,array(120,60));
		if(!empty($inInspectData->is_approve)){ $mpdf->SetWatermarkImage($logo,0.05,array(120,60));$mpdf->showWatermarkImage = true; }
		else{ $mpdf->SetWatermarkText('Not Approved Copy',0.1);$mpdf->showWatermarkText = true; }
		$mpdf->SetProtection(array('print'));
		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('L','','','','',5,5,23,7,5,5,'','','','','','','','','','A4-L');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');		
	}

	public function getGrnList(){
        $this->printJson($this->grnModel->getGrnList($this->input->post('grn_id')));
    }

	//Create By : Avruti @15-04-2022
	public function getSoListForSelect(){
		$party_id = $this->input->post('party_id'); 
		$so_id = $this->input->post('so_id'); 
		$result = $this->grnModel->getSoListForSelect($party_id); //print_r($result);exit;
		$options="";
		if(!empty($result)): 
			$options .= '<option value="">Select So No.</option>';
			foreach($result as $row):
				$selected = (!empty($so_id) && $so_id == $row->id)?'selected':'';
				$options .= "<option data-row='".json_encode($row)."' value='".$row->id."' ".$selected.">".getPrefixNumber($row->trans_prefix,$row->trans_no)."</option>";
			endforeach;
		else:
			$options .= '<option value="">Select So No.</option>';
		endif;
		
		$this->printJson(['status'=>1, 'options'=>$options]);
	}

	//Created By Karmi @15/04/2022
	public function getPartyOrders(){
		$this->printJson($this->grnModel->getPartyOrders($this->input->post('party_id')));
	}
	
	//Test Report * Created By Meghavi @10/08/2022
	public function getTestReport(){
        $grn_id = $this->input->post('id');
        $this->data['dataRow'] = $this->grnModel->getTestReport($grn_id);
        $this->data['grn_id'] = $grn_id;
		$this->data['supplierList'] = $this->party->getSupplierList();
		$this->data['tcReportData'] = $this->getTestReportTable($grn_id);
		$this->data['testDescList'] = explode(',',$this->grnModel->getMasterOptions()->test_description);
        $this->load->view($this->testReport,$this->data);
    }

    public function updateTestReport(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['agency_id']))
            $errorMessage['agency_id'] = "Agency Name is required.";
		if(empty($data['name_of_agency']))
            $errorMessage['name_of_agency'] = "Agency Name is required.";		
        if(empty($data['test_description']))
            $errorMessage['test_description'] = "Description is required.";
		if(empty($data['sample_qty']))
            $errorMessage['sample_qty'] = "Sample Qty is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            /*if($_FILES['tc_file']['name'] != null || !empty($_FILES['tc_file']['name'])):
                $this->load->library('upload');
				$_FILES['userfile']['name']     = $_FILES['tc_file']['name'];
				$_FILES['userfile']['type']     = $_FILES['tc_file']['type'];
				$_FILES['userfile']['tmp_name'] = $_FILES['tc_file']['tmp_name'];
				$_FILES['userfile']['error']    = $_FILES['tc_file']['error'];
				$_FILES['userfile']['size']     = $_FILES['tc_file']['size'];
				
				$imagePath = realpath(APPPATH . '../assets/uploads/test_report/');
				$config = ['file_name' => "test_report".time(),'allowed_types' => '*','max_size' => 10240,'overwrite' => FALSE, 'upload_path'	=>$imagePath];

				$this->upload->initialize($config);
				if (!$this->upload->do_upload()):
					$errorMessage['item_image'] = $this->upload->display_errors();
					$this->printJson(["status"=>0,"message"=>$errorMessage]);
				else:
					$uploadData = $this->upload->data();
					$data['tc_file'] = $uploadData['file_name'];
				endif;
			else:
				unset($data['tc_file']);
			endif;*/
			
            $data['created_by'] = $this->session->userdata('loginId');
			$this->grnModel->saveTestReport($data);
			$tcReportData = $this->getTestReportTable($data['id']);
            $this->printJson(['status'=>1,'tcReportData'=>$tcReportData]);
        endif;
    }

	public function deleteTestReport(){
		$data = $this->input->post();

		if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
			$this->grnModel->deleteTestReport($data['id']);
			$tcReportData = $this->getTestReportTable($data['grn_trans_id']);
            $this->printJson(['status'=>1,'tcReportData'=>$tcReportData]);
        endif;
	}
	
	public function getTestReportTable($grn_trans_id){
		$result = $this->grnModel->getTestReportTrans($grn_trans_id);

		$i=1; $tbodyData = "";
		if (!empty($result)) :
			foreach ($result as $row) :
			    $tdDownload = '';
			    if(!empty($row->tc_file)) {  $tdDownload = '<a href="'.base_url('assets/uploads/test_report/'.$row->tc_file).'" target="_blank"><i class="fa fa-download"></i></a>'; } 
			    $editTestReport = '';
			    $editTestReport = "{'id' : ".$row->id.", 'modal_id' : 'modal-lg', 'form_id' : 'updateTR', 'title' : 'Test Report', 'fnEdit' : 'updateTR', 'save_id' : 'updateTestReport','button':'both','fnsave':'updateTestReportV2'}";
				$tbodyData .=  '<tr>
					<td>' . $i++ . '</td>
					<td>' . $row->name_of_agency . '</td>
					<td>' . $row->test_description . '</td>
					<td>' . $row->mill_tc . '</td>
					<td>' . $row->sample_qty . '</td>
					<td>' . $row->test_report_no . '</td>
					<td>' . $row->test_remark . '</td>
					<td>' . $row->test_result . '</td>
					<td>' . $row->inspector_name . '</td>
					<td>' . $row->remark . '</td>
					<td>' . $tdDownload . '</td>
					<td class="text-center">
					    <a class="btn btn-outline-primary btn-sm btn-delete" href="'.base_url("grn/printTestReport/".$row->id).'" target="_blank" datatip="Print" flow="left"><i class="fa fa-print"></i></a>
						<a class="btn btn-outline-success btn-sm btn-delete" href="javascript:void(0)" onclick="editTestReport('.$editTestReport.');" datatip="Edit" flow="left"><i class="ti-pencil-alt"></i></a>
						<a class="btn btn-outline-danger btn-sm btn-delete" href="javascript:void(0)" onclick="trashTestReport('.$row->id.','.$row->grn_trans_id.');" datatip="Remove" flow="left"><i class="ti-trash"></i></a>
					</td>
				</tr>';
			endforeach;
		else :
			$tbodyData .= '<tr><td colspan="11" style="text-align:center;">No Data Found</td></tr>';
		endif;
		return $tbodyData;
	}

	//Created By Meghavi @28/06/2022
	function printGrn($id,$type=0){
		$this->data['grnData'] = $this->grnModel->editInv($id);
		$this->data['partyData'] = $this->party->getParty($this->data['grnData']->party_id);
		$this->data['companyData'] = $this->purchaseOrder->getCompanyInfo();
		$response="";
		$logo=base_url('assets/images/logo.png'); $unapproved = base_url('assets/images/unapproved.jpg');
		$this->data['letter_head']=base_url('assets/images/letterhead_top.png');
		$grnData = $this->data['grnData']; 
        $this->data['type'] = $type;
		$pdfData = $this->load->view('grn/printGrn',$this->data,true);
		$htmlHeader = '<img src="'.$this->data['letter_head'].'" class="img">';
		
		$htmlFooter ='';
		$mpdf = $this->m_pdf->load();
		$fileName = str_replace("/","_",getPrefixNumber($grnData->grn_prefix,$grnData->grn_no).'.pdf');
		$filePath = realpath(APPPATH . '../assets/uploads/sales_quotation/');
		$stylesheet = file_get_contents(base_url('assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css'));
		$stylesheet = file_get_contents(base_url('assets/css/style.css?v='.time()));
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		
		$mpdf->SetProtection(array('print'));
		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('P','','','','',5,5,40,5,5,5,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
	       
		
		if(empty($type)):
			$mpdf->Output($fileName,'I');
		elseif($type==1):
        	$mpdf->Output($filePath.'/'.$fileName, 'F');
			return $filePath.'/'.$fileName;
		else:
		    return $pdfData;
		endif;
	}
	
	//Created By Avruti @14/08/2022
	public function deviationReport($id)
	{
        $this->data['dataRow'] = $this->grnModel->getInInspectionMaterial($id);
		$product_code = "";
		$c=0;
		if(!empty($this->data['dataRow']->fgitem_id)):
			$la = explode(",",$this->data['dataRow']->fgitem_id);
			if(!empty($la)){
				foreach($la as $fgid){
					$fg = $this->grnModel->getFinishGoods($fgid);
					if(!empty($fg)):
						if($c==0){ $product_code .= $fg->item_code; }
						else{ $product_code .= '<br>'.$fg->item_code; } $c++;
					endif;
				}
			}
		endif;
		
		$this->data['inInspectData'] = $this->grnModel->getInInspection($id);
		$paramData = [] ;$controlMethodArray = [];
		if(!empty($this->data['inInspectData'])){
			$this->data['paramData'] =  $this->controlPlanV2->getCPDimenstion(['item_id'=>$this->data['inInspectData']->fg_item_id,'stage_type'=>1,'responsibility'=>'INSP','rev_no'=>$this->data['inInspectData']->pfc_rev_no]);
			$inInspectData = $this->data['inInspectData'];
		}
		
		$this->data['product_code'] = $product_code;
		$this->data['dData'] = $this->grnModel->getInInspectionDeviation($id); 
		$this->load->view($this->deviationReport,$this->data);
	}

	//Created By Avruti @14/08/2022
	public function saveDeviationReport(){
		$data = $this->input->post(); 
        $errorMessage = Array();

		$inInspectData = $this->grnModel->getInInspection($data['grn_trans_id']);
        $insParamData = $this->controlPlanV2->getCPDimenstion(['item_id'=>$inInspectData->fg_item_id,'stage_type'=>1,'responsibility'=>'INSP','rev_no'=>$inInspectData->pfc_rev_no]);

        if(count($insParamData) <= 0)
            $errorMessage['general'] = "Item Parameter is required.";

        $pre_inspection = Array(); $param_ids = Array(); $data['observation_sample'] = '';
        if(!empty($insParamData)):
            foreach($insParamData as $row):
				$obj = New StdClass;
                $param = Array();
				if(!empty($inInspectData)):
					$obj = json_decode($inInspectData->observation_sample);
				endif;
				if(!empty($obj->{$row->id})):
					if($obj->{$row->id}[10] == 'Not Ok'):
					
						$param[] = $data['observation_'.$row->id];
						unset($data['observation_'.$row->id]);

						$param[] = $data['qty_'.$row->id];
						unset($data['qty_'.$row->id]);

						$param[] = $data['deviation_'.$row->id];
						unset($data['deviation_'.$row->id]);

						$pre_inspection[$row->id] = $param;
						$param_ids[] = $row->id;
					endif;
				endif;		
            endforeach;
        endif;

		$data['parameter_ids'] = implode(',',$param_ids);
        $data['observation_sample'] = json_encode($pre_inspection);
        $data['param_count'] = count($insParamData);
		
		if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
           
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->grnModel->saveInInspection($data));
        endif;
	}

	//Created By Avruti @14/08/2022
	function printDeviation($id){
		$this->data['deviationData'] = $this->grnModel->getInInspectionDeviation($id); 
        $this->data['paramData'] = $this->item->getPreInspectionParam($this->data['deviationData']->item_id);

		$deviationData = $this->data['deviationData'];
		$devArray=array();
		if(!empty($deviationData->observation_sample)):
			$devId = json_decode($deviationData->observation_sample); 
			foreach($devId as $key=>$value):
				$devData=new stdClass();
				$fgData = $this->controlPlanV2->getPfcTrans($key); 
				
				$diamention = '';
				if ($fgData->requirement == 1) { $diamention = $fgData->min_req . '/' . $fgData->max_req; }
				if ($fgData->requirement == 2) { $diamention = $fgData->min_req . ' ' . $fgData->other_req; }
				if ($fgData->requirement == 3) { $diamention = $fgData->max_req . ' ' . $fgData->other_req; }
				if ($fgData->requirement == 4) { $diamention = $fgData->other_req; }
				
				$devData->parameter=$fgData->product_param; 
				$devData->specification=$diamention; 
				$devData->observation_=$value[0];
				$devData->qty_=$value[1];
				$devData->deviation_=$value[2];
				$devArray[]=$devData;
			endforeach;
		endif;
		$this->data['devArray']=$devArray;

		$deviationData->fgCode="";
		if(!empty($deviationData->fgitem_id)):
			$fgId = explode(',', $deviationData->fgitem_id); $i=1; 
			foreach($fgId as $key=>$value):
				$fgData = $this->grnModel->getFinishGoods($value);
				if($i==1){ $deviationData->fgCode.=$fgData->item_code; }
				else{ $deviationData->fgCode.= ', '.$fgData->item_code; } $i++;
			endforeach;
		endif;

		$prepare = $this->employee->getEmp($deviationData->created_by);
		$prepareBy = $prepare->emp_name.' <br>('.formatDate($deviationData->created_at).')'; 
		$approveBy = '';
		if(!empty($deviationData->is_approve)){
			$approve = $this->employee->getEmp($deviationData->is_approve);
			$approveBy .= $approve->emp_name.' <br>('.formatDate($deviationData->approve_date).')'; 
		}

		$logo = base_url('assets/images/logo.png'); 
		$this->data['letter_head']=base_url('assets/images/letterhead_top.png');
		
		$pdfData = $this->load->view('grn/printDeviation',$this->data,true);
		
		$htmlHeader = '<table class="table">
							<tr>
								<td style="width:25%;"><img src="'.$logo.'" style="height:50px;"></td>
								<td class="org_title text-center" style="font-size:1.4rem;width:50%">DEVIATION APPROVAL REPORT</td>
								<td style="width:25%;" class="text-right"><span style="font-size:0.8rem;">F QA 15 00(01/06/2020)</td>
							</tr>
						</table><hr>';
		$htmlFooter = '<table class="table" style="border-bottom:1px solid #000000;">
						<tr>
							<td style="width:50%;"></td>
							<td style="width:25%;" class="text-center">'.$prepareBy.'</td>
							<td style="width:25%;" class="text-center">'.$approveBy.'</td>
						</tr>
						<tr>
							<td style="width:50%;"></td>
							<td style="width:25%;" class="text-center"><b>Prepared By</b></td>
							<td style="width:25%;" class="text-center"><b>Approved By</b></td>
						</tr>
					</table>
					<table class="table top-table" style="margin-top:10px;">
						<tr>
							<td style="width:25%;"></td>
							<td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
						</tr>
					</table>';
					
		$mpdf = new \Mpdf\Mpdf();
		$pdfFileName='GRN_DEVIATION'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetWatermarkImage($logo,0.03,array(120,60));
		$mpdf->SetProtection(array('print'));
		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('P','','','','',5,5,30,5,5,5,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
	}
	
    /* Created By Jp@09092022*/
    public function sendMail(){
		$postData = $this->input->post();
		$attachment = $this->printGrn($postData['id'],1);
		$receiveDetail = $this->printGrn($postData['id'],2);
        $ref_no = str_replace('_','/',$postData['ref_no']);
        
        $signData['sender_name'] = 'Jayveersinh Gohil';
        $signData['sender_contact'] = '+91 9904709775';
        $signData['sender_designation'] = 'Store Incharge';
        $signData['sign_email'] = 'store@jayjalaramind.com';
        
		$emailSignature = $this->mails->getSignature($signData);

		$mailData = array();
		$mailData['sender_email'] = 'store@jayjalaramind.com';
		//$mailData['receiver_email'] = 'jagdishpatelsoft@gmail.com';
		$mailData['receiver_email'] = 'purchase@jayjalaramind.com';
		$mailData['cc_email'] = 'production@jayjalaramind.com,npd@jayjalaramind.com';
		$mailData['mail_type'] = 7;
		$mailData['ref_id'] = 0;
		$mailData['ref_no'] = 0;
		$mailData['created_by'] = $this->loginId;
		$mailData['subject'] = 'Material Received : '.$ref_no;
		
		$mail_body = '<div style="font-size:12pt;font-family: Bookman Old Style;">';
		    $mail_body .= '<b>Dear All,</b><br><br>';
		    $mail_body .= 'Warm Greetings of the Day<br>';
		    $mail_body .= 'We have received the below listed material.<br><br>Here, I am enclosing Goods Rreceipt Note with Ref. No.: <b>'.$ref_no.'</b><br>';
            $mail_body .= $receiveDetail.'<br><br>';
        $mail_body .= '</div>';
		$mail_body .= $emailSignature;
		$mailData['mail_body'] = $mail_body;
		
		$result = $this->mails->sendMail($mailData, [$attachment]);
		unlink($attachment);
		$this->printJson($result);
	}
	
	//Created By Meghavi @09/12/2022
	function materialIdentTag($id){
		$grnData = $this->grnModel->getgrnItemTableRow($id);
		$this->data['partyData'] = $this->party->getParty($grnData->party_id);
		$this->data['companyData'] = $this->purchaseOrder->getCompanyInfo();
		$logo = base_url('assets/images/logo.png'); 
		$this->data['letter_head']=base_url('assets/images/letterhead_top.png');
  
		$product_code = ''; $c=0;
		if(!empty($grnData->fgitem_id)): $la = explode(",",$grnData->fgitem_id);
			if(!empty($la)){
				foreach($la as $fgid){
					$fg = $this->grnModel->getFinishGoods($fgid);
					if(!empty($fg)):
						if($c==0){ $product_code .= $fg->item_code; }else{ $product_code .= '<br>'.$fg->item_code; }$c++;
					else:
						$product_code = "";
					endif;
				}
			}
		endif; 
		
		$htmlHeader = '<table class="table">
			<tr>
				<td style="width:25%;"><img src="'.$logo.'" style="height:100px;"></td>
				<td class="org_title text-center" style="font-size:2rem;width:50%">Raw Material Identification Tag</td>
				<td style="width:25%;" class="text-right"><span style="font-size:1.5rem;">F QA 23(01/01.09.2021)</td>
			</tr>
		</table><hr>';
		$pdfData = '<div class="row">
			<div class="col-12">
				<table class="table item-list-bb" style="margin-top:25px;">
					<tr>
						<th style="width:30%; font-size:1.3rem; padding:20px;" class="text-left">GRN Date : </th>
						<td style="width:70%; font-size:1.3rem; padding:20px;">'.formatDate($grnData->grn_date).'</td>
					</tr>
					<tr>
						<th style="width:30%; font-size:1.3rem; padding:20px;" class="text-left">Part No : </th>
						<td style="width:70%; font-size:1.3rem; padding:20px;">'.$product_code.'</td>
					</tr>
					<tr>
						<th style="width:30%; font-size:1.3rem; padding:20px;" class="text-left">Material Grade/RM Size :</th>
						<td style="width:70%; font-size:1.3rem; padding:20px;">'.$grnData->item_name.'</td>
					</tr>
					<tr>
						<th style="width:30%; font-size:1.3rem; padding:20px;" class="text-left">Colour Code : </th>
						<td style="width:70%; font-size:1.3rem; padding:20px;">'.$grnData->color_code.'</td>
					</tr>
					<tr>
						<th style="width:30%; font-size:1.3rem; padding:20px;" class="text-left">Batch Code/Heat : </th>
						<td style="width:70%; font-size:1.3rem; padding:20px;">'.$grnData->batch_no.'</td>
					</tr>
					<tr>
						<th style="width:30%; font-size:1.3rem; padding:20px;" class="text-left">Received Qty. : </th>
						<td style="width:70%; font-size:1.3rem; padding:20px;">'.$grnData->qty.'</td>
					</tr>
					<tr>
						<th style="width:30%; font-size:1.3rem; padding:20px;" class="text-left">Supplier Name : </th>
						<td style="width:70%; font-size:1.3rem; padding:20px;">'.$grnData->party_name.'</td>
					</tr>
					<tr>
						<th style="width:30%; font-size:1.3rem; padding:20px;" class="text-left">Name & Sign :</th>
						<td style="width:70%; font-size:1.3rem; padding:20px;">'.$grnData->emp_name.'</td>
					</tr>
				</table>
			</div>
		</div>';
		$htmlFooter = '';

		$mpdf = $this->m_pdf->load();
		$fileName = str_replace("/","_",getPrefixNumber($grnData->grn_prefix,$grnData->grn_no).'.pdf');
		$filePath = realpath(APPPATH . '../assets/uploads/sales_quotation/');
		$stylesheet = file_get_contents(base_url('assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css'));
		$stylesheet = file_get_contents(base_url('assets/css/style.css?v='.time()));
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		
		$mpdf->SetProtection(array('print'));
		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('P','','','','',10,10,40,10,5,5,'','','','','','','','','','A4-L');
		$mpdf->WriteHTML($pdfData);
	       
		
		if(empty($type)):
			$mpdf->Output($fileName,'I');
		elseif($type==1):
        	$mpdf->Output($filePath.'/'.$fileName, 'F');
			return $filePath.'/'.$fileName;
		else:
		    return $pdfData;
		endif;
	}
	
	public function getRevisionList(){
        $data = $this->input->post();
        $pfcList =$this->ecn->getCpRevData(['item_id'=>$data['item_id'],'status'=>3]);
        $options = '<option value="">Select Revision</option>';
        if(!empty($pfcList)){
            foreach($pfcList as $row){
                $options.='<option value="'.$row->rev_no.'">'.$row->rev_no.' | PFC REV NO : '.$row->pfc_rev_no.'</option>';
            }
        }
        $this->printJson(['status'=>1,'options'=>$options]);
    }
    
    public function rejectInspection(){
		$data = $this->input->post();	
		if(empty($data['id'])):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->grnModel->rejectInspection($data));
		endif;
	}

	public function approveInspection()
	{
        $id = $this->input->post('id');
		$this->data['inInspectData'] = $inInspectData = $this->grnModel->getInInspection($id);
		$this->data['tcReportData'] =  $this->grnModel->getTestReportTrans($id);

		$paramData = [] ;$controlMethodArray = [];$prepareBy="";$approveBy="";
		if(!empty($this->data['inInspectData'])){
			
			$this->data['paramData'] =  $this->controlPlanV2->getCPDimenstion(['item_id'=>$this->data['inInspectData']->fg_item_id,'stage_type'=>1,'responsibility'=>'INSP','rev_no'=>$this->data['inInspectData']->pfc_rev_no]);
			
			$inInspectData = $this->data['inInspectData'];
			$inInspectData->fgCode="";
			if(!empty($inInspectData->fgitem_id)):
				$fgId = explode(',', $inInspectData->fgitem_id); $i=1; 
				foreach($fgId as $key=>$value):
					$fgData = $this->grnModel->getFinishGoods($value);
					if($i==1){ $inInspectData->fgCode.=$fgData->item_code; }
					else{ $inInspectData->fgCode.= ', '.$fgData->item_code; } $i++;
				endforeach;
			endif;
		}
		$this->load->view($this->approve_inspection,$this->data);
	}
	
	public function saveApproveInspection(){
		$data = $this->input->post(); 
        $errorMessage = Array();
		$insParamData =  $this->controlPlanV2->getCPDimenstion(['item_id'=>$data['fg_item_id'],'stage_type'=>1,'responsibility'=>'INSP','control_method'=>'IIR','rev_no'=>$data['pfc_rev_no']]);
        if(count($insParamData) <= 0)
            $errorMessage['general'] = "Item Parameter is required.";

        $pre_inspection = Array();$param_ids = Array();$data['observation_sample'] = '';
        if(!empty($insParamData)):
            foreach($insParamData as $row):
                $param = Array();
                for($j = 1; $j <= $data['sample_size']; $j++):
                    $param[] = $data['sample'.$j.'_'.$row->id];
                    unset($data['sample'.$j.'_'.$row->id]);
                endfor;

                $param[] = $data['status_'.$row->id];
                $pre_inspection[$row->id] = $param;
				$param_ids[] = $row->id;
                unset($data['status_'.$row->id]);

				$param[] = $data['result_'.$row->id];
                $pre_inspection[$row->id] = $param;
                unset($data['result_'.$row->id]);
            endforeach;
        endif;
		unset($data['sample_size']);
		$data['parameter_ids'] = implode(',',$param_ids);
        $data['observation_sample'] = json_encode($pre_inspection);
        $data['param_count'] = count($insParamData);

		if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->grnModel->saveApproveInspection($data));
        endif;
	}
	
    public function updateTR() {
    	$id = $this->input->post('id');
    	$this->data['dataRow'] = $this->grnModel->getTestReportData($id);
    	$this->data['modal_id'] = 'modal-lg';
    	$this->data['form_id'] = '';
    	$this->data['title'] = 'Update report';
    	$this->data['fnEdit'] = '';
    	$this->load->view($this->updatetestReport,$this->data);
    }

    public function updateTestReportV2()
    {
    	$data = $this->input->post();
        $errorMessage = array();

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            if(isset($_FILES['tc_file']['name']) && $_FILES['tc_file']['name'] != null || !empty($_FILES['tc_file']['name'])):
                $this->load->library('upload');
				$_FILES['userfile']['name']     = $_FILES['tc_file']['name'];
				$_FILES['userfile']['type']     = $_FILES['tc_file']['type'];
				$_FILES['userfile']['tmp_name'] = $_FILES['tc_file']['tmp_name'];
				$_FILES['userfile']['error']    = $_FILES['tc_file']['error'];
				$_FILES['userfile']['size']     = $_FILES['tc_file']['size'];
				
				$imagePath = realpath(APPPATH . '../assets/uploads/test_report/');
				$config = [ 'file_name' => "test_report".time(), 'allowed_types' => '*', 'max_size' => 10240, 'overwrite' => FALSE, 'upload_path' => $imagePath ];

				$this->upload->initialize($config);
				if (!$this->upload->do_upload()):
					$errorMessage['item_image'] = $this->upload->display_errors();
					$this->printJson(["status"=>0,"message"=>$errorMessage]);
				else:
					$uploadData = $this->upload->data();
					$data['tc_file'] = $uploadData['file_name'];
				endif;
			else:
				// unset($data['tc_file']);

			endif;
			
            // $data['created_by'] = $this->session->userdata('loginId');
			$this->grnModel->updateTestReport($data);
			$tcReportData = $this->getTestReportTable($data['grn_trans_id']);
            $this->printJson(['status'=>1,'tcReportData'=>$tcReportData]);
        endif;
    }
    
    public function printTestReport($grn_trans_id){
    	$grnTestReport = $this->grnModel->getTestReportdata($grn_trans_id);
    	
    	$logo = base_url('assets/images/logo.png');
				
		$pdfData = '<table class="table">
			<tr>
				<td style="width:25%;"><img src="'.$logo.'" style="height:40px;"></td>
				<td class="org_title text-center" style="font-size:1rem;">MATERIAL TEST CHALLAN</td>
			</tr>
		</table>';

    	$pdfData .= '<div class="row">
			<div class="col-12">
				<table class="table item-list-bb text-left">
				   <tr> 
						<th>Lab Name</th> 
						<th>'.$grnTestReport->name_of_agency.'</th> 
						<th>Date</th> 
						<th>'.formatDate($grnTestReport->grn_date).'</th>
					</tr>
					<tr>
						<th>Part No.</th> 
						<th colspan="3">'.$grnTestReport->part_name.'</th>  
					</tr> 
					<tr>
						<th>Size</th> 
						<th colspan="3">'.$grnTestReport->item_name.'</th> 
					</tr> 
					<tr> 
					    <th>Heat/Batch No.</th> 
						<th>'.$grnTestReport->batch_no.'</th>
						<th>Material</th> 
						<th>'.$grnTestReport->material_grade.'</th>
					</tr>
					<tr>
						<th>Test Type</th> 
						<th colspan="3">'.$grnTestReport->test_description.'</th> 
					</tr>
					<tr> 
						<th>Remark</th> 
						<th colspan="3">'.$grnTestReport->remark.'</th> 
					</tr>
				</table>
			</div>
		</div>';

    	$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => ['100', '60']]);    
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
        $mpdf->WriteHTML($stylesheet,1);
        $mpdf->SetHTMLHeader($htmlHeader);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->setTitle("Print Tag");
        $mpdf->AddPage('P','','','','',2,2,2,2,2,2);
        $mpdf->WriteHTML($pdfData);
        $mpdf->Output('tag_print.pdf','I');
        
    }
}
?>