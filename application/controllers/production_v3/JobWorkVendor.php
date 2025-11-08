<?php
class JobWorkVendor extends MY_Controller{
    private $indexPage = "production_v3/job_work_vendor/index";
    private $returnForm = "production_v3/job_work_vendor/job_work_return";
	private $challanForm = "production_v3/job_work_vendor/challan_form";
	private $pending_challan = "production_v3/job_work_vendor/pending_challan";
	private $vendor_log = "production_v3/job_work_vendor/vendor_log";
	private $return_material = "production_v3/job_work_vendor/return_material";
	private $hold_ok_mevement = "production_v3/job_work_vendor/hold_ok_mevement";
	
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Outsource";
		$this->data['headData']->controller = "production_v3/jobWorkVendor";
		// $this->data['headData']->pageUrl = "production_v3/jobWorkVendor";
	}
	
	public function index($status=0){
		$this->data['status'] = $status;
        $this->data['tableHeader'] = getProductionHeader("jobWorkVendor");
		$this->data['vendorData'] = $this->party->getVendorList(); 
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status=0,$dates='',$vendor_id=''){
		$data = $this->input->post(); 
		$data['status'] = $status; $data['vendor_id'] = $vendor_id;
		if(!empty($dates)){$data['from_date'] = explode('~',$dates)[0];$data['to_date'] = explode('~',$dates)[1];}
        $result = $this->jobWorkVendor_v3->getDTRows($data);
        
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            
            $row->status_per = round($row->status_per,2);
            
            $pendingDays='';
            if($row->status_per < 100){
                $challan_date = date_create($row->trans_date);
    			$curr_date = date_create();
    			$calculateDays = date_diff($challan_date, $curr_date);
    			$pendingDays = (100 - $calculateDays->d).' Days';
            }
            
            if($row->status_per == 0):
				$row->status = '<span class="badge badge-pill badge-danger m-1 fs-12">Pending - '.$row->status_per.'% - '.$pendingDays.'</span>';
			elseif($row->status_per < 100):
				$row->status = '<span class="badge badge-pill badge-warning m-1 fs-12">In Process - '.$row->status_per.'% - '.$pendingDays.'</span>';
			else:
				$row->status = '<span class="badge badge-pill badge-success m-1 fs-12">Completed - '.$row->status_per.'%</span>';
			endif;	
			
            $row->controller = $this->data['headData']->controller;			
            $sendData[] = getJobWorkVendorData($row);
        endforeach;
        $result['data'] =$sendData;
        $this->printJson($result);
    }

    public function jobWorkReturn(){
        $data = $this->input->post();
        $this->data['dataRow'] = $data;
        $this->data['transHtml'] = $this->jobWorkVendor_v3->getReturnTransaction($data['id'])['html'];
        $this->load->view($this->returnForm,$this->data);
    }

    public function jobWorkReturnSave(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['qty'])):
            $errorMessage['qty'] = "Qty is required.";
        else:
            $pendingQty = $this->jobWorkVendor_v3->getJobWorkVendorRow($data['id'])->pending_qty;
            if($data['qty'] > $pendingQty):
                $errorMessage['qty'] = "Qty not available for returned.";
            endif;
        endif;

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->jobWorkVendor_v3->saveJobWorkReturn($data));
        endif;
    }

    public function deleteReturnTrans(){
        $data = $this->input->post();
        if(empty($data['id']) || $data['key'] == ""):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->jobWorkVendor_v3->deleteReturnTrans($data));
        endif;
    }

    /* Vendor Challan */
	public function pendingChallan(){
		$this->data['tableHeader'] = getProductionHeader('pendingVendorChallan');
		$this->data['vendorData'] = $this->party->getVendorList();
		$this->load->view($this->pending_challan,$this->data);
	}

    public function getPendingChallanDTRows(){
        $result = $this->jobWorkVendor_v3->getPendingChallanDTRows($this->input->post());

		$sendData = array();$i=1;
        foreach($result['data'] as $row):
			$row->sr_no = $i++;
			$row->controller = $this->data['headData']->controller;
            $sendData[] = getPendingVendorChallanData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function createVendorChallan(){
        $party_id=$this->input->post('party_id');
		$this->data['party_id'] = $party_id;
		$this->data['processList'] = $this->process->getProcessList();
		$this->data['trans_prefix'] = 'VDC/'.$this->shortYear.'/';
		$this->data['trans_no'] = $this->jobWorkVendor_v3->nextTransNo();
		$this->data['materialData'] = $this->item->getItemList(9);
		$this->load->view($this->challanForm,$this->data);
    }

	public function saveVendorChallan(){
		$data = $this->input->post(); 
		
		$errorMessage = array();

		$data['challan_prefix'] = 'JO/'.$this->shortYear.'/';
		$data['challan_no'] = $this->jobWorkVendor_v3->nextChallanNo();

		$data['material_data']=""; $materialArray = array();
		if(isset($data['item_id']) && !empty($data['item_id'])):
			foreach($data['item_id'] as $key=>$value):
				$materialArray[] = [
					'item_id' => $value,
					'out_qty' => $data['out_qty'][$key],
					'in_qty' => 0
				];
			endforeach;
			$data['material_data'] = json_encode($materialArray);
		endif;
		
		if (formatDate($data['trans_date'], 'Y-m-d') < $this->startYearDate OR formatDate($data['trans_date'], 'Y-m-d') > $this->endYearDate)
			$errorMessage['trans_date'] = "Invalid Date (Out of Financial Year).";
		
		if(empty($data['material_data']))
		    $errorMessage['packingError'] = "Material Details Required.";
		
		if(!isset($data['job_approval_id'])):
			$errorMessage['orderError'] = "Please Check atleast one order.";
		else:
    		foreach($data['ch_qty'] as $key=>$val):
    		    if(empty($val) or $val <= 0):
    			    $errorMessage['chQty'.$data['job_approval_id'][$key]] = "Challan qty is required.";
    			endif;
    			
    			if(empty($data['jobwork_order_id'][$data['job_approval_id'][$key]])):
    			   $errorMessage['jwoId'.$data['job_approval_id'][$key]] = "J.W. Order is required.";
    			endif;
    			
    			if(empty($data['weight'][$key])):
    			   $errorMessage['weight'.$data['job_approval_id'][$key]] = "Weight is required.";
    			endif;

				if(empty($data['process_ids'][$key])):
    			   $errorMessage['process_id'.$data['job_approval_id'][$key]] = "Process is required.";
				else:
					$processId = explode(",",$data['process_ids'][$key]);
					$data['challan_type'][$key] = ((count($processId) == 1)?1:2);
					$approveData = $this->processMovement->getApprovalData(['id'=>$data['job_approval_id'][$key]]);
					$process = explode(",",$approveData->process);
					$outProcessList = $processId;
					$a = 0;$jwoProcessIds = array();
					foreach ($process as $k => $value) :
						if (isset($outProcessList[$a])) :
							$processKey = array_search($outProcessList[$a], $process);
							$jwoProcessIds[$processKey] = $outProcessList[$a];
							$a++;
						endif;
					endforeach;
					ksort($jwoProcessIds);
					
					$processList = array();
					foreach ($jwoProcessIds as $k => $value) :
						$processList[] = $value;
					endforeach;
					
					$nextProcessKey = array_search($approveData->in_process_id,$process);
					$i = 0;$error = false;
					foreach($process as $ky => $pid):
						if ($ky >= $nextProcessKey) :
							if (isset($processList[$i])) :
								if ($processList[$i] != $pid) :
									$error = true;
									break;
								endif;
								$i++;
							endif;
						endif;
					endforeach;

					if ($error == true) :
						$errorMessage['process_id' . $data['job_approval_id'][$key]] = "Invalid Process Sequence.";
					endif;
    			endif;
    		endforeach;
    	endif;

		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
			$data['created_by'] = $this->loginId;
			$data['version'] = 2;

			$masterData = [
				'id'=>$data['id'],
				'trans_date' => $data['trans_date'],
				'trans_prefix' => $data['trans_prefix'],
				'trans_no' => $data['trans_no'],
				'trans_number' => $data['trans_number'],
				'vendor_id' => $data['vendor_id'],
				'material_data' => $data['material_data'],
				'remark' => !empty($data['remark']) ? $data['remark'] : NULL,
				'created_by' => $this->loginId
			];

			$transData = [
				'id' => '',
				'type' => 1,
				'process_id' => $data['process_id'],
				'process_ids' => $data['process_ids'],
				'challan_type' => $data['challan_type'],
				'job_approval_id' => $data['job_approval_id'],
				'jobwork_order_id' => $data['jobwork_order_id'],
				'qty' => $data['ch_qty'],
				'w_pcs' => $data['w_pcs'],
				'weight' => $data['weight'],
				'created_by' => $this->loginId
			];

			$this->printJson($this->jobWorkVendor_v3->saveVendorChallan($masterData,$transData));
		endif;
	}
	
	public function getJobworkOrderList(){
		$data = $this->input->post();
		

		$tbodyData = ""; $jwoTd = "";
		$movementData = $this->jobWorkVendor_v3->getPendingChallanData(['process_id'=>$data['process_id']]);
		$processList = $this->process->getProcessList();
		// $masterProcess
		$masterProcess = array_reduce($processList, function($masterProcess, $process) { 
					$masterProcess[$process->id] = $process; 
					return $masterProcess; 
				}, []);
		$i=1;
		if(!empty($movementData)){
			foreach($movementData as $row){
				$process = explode(",",$row->process);
				$processKey = array_search($data['process_id'],$process);

				$processOptions = '';
				foreach($process as $key => $pid):
					if($key >= $processKey):
						$selected = (($processKey == $key)?'selected readonly':'');
						$processOptions .= '<option value="'.$pid.'" '.$selected.'>'.$masterProcess[$pid]->process_name.'</option>';
					endif;
				endforeach;
				$pending_qty = $row->qty - $row->challan_qty;

				$orderList = $this->jobWorkOrder->getJobworkOrderList(['vendor_id'=>$data['vendor_id'],'item_id'=>$row->product_id,'process_id'=>$data['process_id']]);
				
				$jwoTd = '<select data-rowid="'.$row->job_approval_id.'" name="jobwork_order_id['.($row->job_approval_id).']" class="form-control single-select">
					<option value="">Select JWO</option>';
					foreach($orderList as $jwo):
						$jwoTd .= '<option value="'.$jwo->id.'">'.getPrefixNumber($jwo->jwo_prefix,$jwo->jwo_no).'</option>'; 
					endforeach;
				$jwoTd .= '</select><div class="error jwoId' . $row->job_approval_id . '"></div>';
				
				$processTd = '<select data-rowid="'.$row->job_approval_id.'" id="processSelect_'.$i.'"  data-input_id="process_ids_'.$i.'" class="form-control jp_multiselect"  multiple="multiple">
								'.$processOptions.'
							 </select>
							 <input type="hidden" id="process_ids_'.$i.'" name="process_ids[]" value="'.$data['process_id'].'" disabled>
							 ';
				$tbodyData .='<tr>
					<td>
						<input type="checkbox" id="md_checkbox_' . $i . '" name="job_approval_id[]" class="filled-in chk-col-success challanCheck" data-rowid="' . $i . '" value="' . $row->job_approval_id . '"  ><label for="md_checkbox_' . $i . '" class="mr-3"></label>
					</td>
					<td>' . getPrefixNumber($row->job_prefix,$row->job_no) . '</td>
					<td>' . $row->item_code . '</td>
					<td>
						' . $processTd . '
						<div class="error process_id' . $row->job_approval_id . '"></div>
					</td>
					<td>' . floatVal($row->qty) . '</td>
					<td>' . floatval($pending_qty) . '</td>
					<td>' . $jwoTd . '</td>
					<td>     
						<input type="text" id="ch_qty' . $i . '" data-pending_qty ="'.$pending_qty.'" name="ch_qty[]" data-rowid="' . $i . '" class="form-control challanQty floatOnly" value="0" disabled>
						<input type="hidden" id="w_pcs'.$i.'" name="w_pcs[]" value="'.floatval($row->w_pcs).'" class="form-control challanQty" disabled>
						<div class="error chQty' . $row->job_approval_id . '"></div>
					</td>
					<td>
						<input type="text" id="weight' . $i . '" name="weight[]" class="form-control floatOnly req" value="0" disabled/>
						<div class="error weight' . $row->job_approval_id . '"></div>
					</td>
				</tr>';
				$i++;
			}
		}

        $this->printJson(['status'=>1,'tbodyData'=>$tbodyData]);
	}
	
    /* delete vendor challan */
	public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->jobWorkVendor_v3->deleteChallan($id));
        endif;
    }

    function jobworkOutChallan($id){
		$jobData = $this->jobWorkVendor_v3->getVendorChallanData($id);
		$companyData = $this->db->where('id',1)->get('company_info')->row();
		$response="";$logo=base_url('assets/images/logo.png');
		
		$topSectionO ='<table class="table" style="border-bottom:1px solid #036aae;">
						<tr>
							<td style="width:30%;"><img src="'.$logo.'" style="height:40px;"></td>
							<td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">JOB WORK CHALLAN</td>
							<td style="width:30%;" class="text-right"><span style="letter-spacing:1px;">GST No.:<span style="letter-spacing:1px;">'.$companyData->company_gst_no.'</span><br><b>Original Copy</b></span></td>
						</tr>
					</table>
					<table class="table" style="border-bottom:1px solid #036aae;margin-bottom:2px;">
						<tr><td class="org-address text-center" style="font-size:13px;">'.$companyData->company_address.'</td></tr>
					</table>';
		$topSectionV ='<table class="table" style="border-bottom:1px solid #036aae;">
						<tr>
							<td style="width:30%;"><img src="'.$logo.'" style="height:40px;"></td>
							<td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">JOB WORK CHALLAN</td>
							<td style="width:30%;" class="text-right"><span style="letter-spacing:1px;">GST No.:<span style="letter-spacing:1px;">'.$companyData->company_gst_no.'</span><br><b>Vendor Copy</b></span></td>
						</tr>
					</table>
					<table class="table" style="border-bottom:1px solid #036aae;margin-bottom:2px;">
						<tr><td class="org-address text-center" style="font-size:13px;">'.$companyData->company_address.'</td></tr>
					</table>';
		$baseSection = '<table class="vendor_challan_table">
							<tr style="">
								<td rowspan="2" style="width:70%;vertical-align:top;">
									<b>TO : '.$jobData->party_name.'</b><br>
									<span style="font-size:12px;">'.$jobData->party_address.'<br>
									<b>GSTIN No. :</b> <span style="letter-spacing:2px;">'.$jobData->gstin.'</span>
								</td>
								<td class="text-left" height="25"><b>Challan No. :</b> '.$jobData->trans_number.' </td>
							</tr>
							<tr>
								<td class="text-left" height="25"><b>Challan Date :</b> '.date("d-m-Y",strtotime($jobData->trans_date)).' </td>
							</tr>
						</table>';
		$itemList='<table class="table table-bordered jobChallanTable">
					<tr class="text-center bg-light-grey">
						<th>#</th>
						<th>Part Code</th>
						<th>Job No.</th>
						<th>Delivery Date</th>
						<th>Process</th>
						<th>Remarks</th>
						<th style="width:15%;">Nos.</th>
					</tr>';
			// $jobTrans = explode(',', $jobData->job_inward_id);
			$packingData = json_decode($jobData->material_data); 
			$i=1;$itemCode="";$jobNo="";$deliveryDate="";$processName="";$remark="";$inQty="";$weight=""; $totalOut=0; $totalWeight=0; $blnkRow=4;
			$jobTransData = $this->jobWorkVendor_v3->getJobworkVendorTrans(['challan_id'=>$id]);
			foreach($jobTransData as $row):
				// if(!empty($jobTransData)){
                    $jobData1 = new stdClass();
                    $jobData1->id = $row->job_card_id;
                    $jobData1->product_id = $row->item_id;
                    $reqMaterials = $this->jobcard_v3->getProcessWiseRequiredMaterials($jobData1)['resultData'][0]; 
                    
    				$pdays = (!empty($row->production_days)) ? "+".$row->production_days." day" : "+0 day";
    				$delivery_date = date('d-m-Y',strtotime($pdays, strtotime($row->trans_date)));
    
    
    				$itemList.='<tr>
    					<td style="vertical-align:top;padding:5px;">'.$i++.'</td>
    					<td style="vertical-align:top;padding:5px;">'.$row->item_code.'</td>
    					<td style="vertical-align:top;padding:5px;">'. getPrefixNumber($row->job_prefix,$row->job_no).' <br> <small>(Batch No: '.((!empty($reqMaterials['heat_no']))?$reqMaterials['heat_no']:"").')</small> </td>
    					<td style="vertical-align:top;padding:5px;">'.$delivery_date.'</td>
    					<td style="vertical-align:top;padding:5px;">'.$row->process_name.'</td>
    					<td style="vertical-align:top;padding:5px;">'.$row->jwoRemark.'</td>
    					<td class="text-center" style="vertical-align:top;padding:5px;">'.((!empty($row->qty))?sprintf('%0.0f',$row->qty):'').'</td>
    				</tr>';
    				$totalOut += sprintf('%0.0f',$row->qty);
				// }
			endforeach;
		$materialDetails="";
		if(!empty($packingData)): $i=1; 
			foreach($packingData as $row):
			    if(!empty($row->item_id)){
				    $item_name = $this->item->getItem($row->item_id)->item_name;
				    if($i==1){$materialDetails .= $item_name.' ( Out Qty:. '.$row->out_qty.' )';}
				    else{$materialDetails .= '<br> '.$item_name.' ( Out Qty:. '.$row->out_qty.' )';}
				    $i++;
			    }
			endforeach;
		endif;
		
		for($j=$i;$j<$blnkRow;$j++):
			$itemList.='<tr>
    			<td style="vertical-align:top;padding:5px;" height="50px"></td>
    			<td style="vertical-align:top;padding:5px;"></td>
    			<td style="vertical-align:top;padding:5px;"></td>
    			<td style="vertical-align:top;padding:5px;"></td>
    			<td style="vertical-align:top;padding:5px;"></td>
    			<td style="vertical-align:top;padding:5px;"></td>
    			<td style="vertical-align:top;padding:5px;"></td>
			</tr>';
		endfor;
		
		$itemList.='<tr class="bg-light-grey">';
			$itemList.='<th class="text-right" style="font-size:14px;" colspan="6">Total</th>';
			$itemList.='<th class="text-center" style="font-size:14px;">'.sprintf('%0.0f', $totalOut).'</th>';
		$itemList.='</tr>';	
		
		$remark = (!empty($jobData->remark) ? '<br><b>Remark:</b> '.$jobData->remark : '');
		
		$itemList.='<tr>
			<th class="text-left" style="vertical-align:top;height:50px;padding:5px;font-weight: normal;" colspan="7">
				<b>Material Details : </b>'.$materialDetails.'
				'.$remark.'
			</th>
		</tr>';
		
		$itemList.='</table>';
		
		$bottomTable='<table class="table table-bordered" style="width:100%;">';
			$bottomTable.='<tr>';
				$bottomTable.='<td class="text-center" style="width:50%;border:0px;"></td>';
				$bottomTable.='<td class="text-center" style="width:50%;font-size:1rem;border:0px;"><b>For, '.$companyData->company_name.'</b></td>';
			$bottomTable.='</tr>';
			$bottomTable.='<tr><td colspan="2" height="60" style="border:0px;"></td></tr>';
			$bottomTable.='<tr>';
				$bottomTable.='<td class="text-center" style="vertical-align:bottom !important;font-size:1rem;border:0px;">Received By</td>';
				$bottomTable.='<td class="text-center" style="font-size:1rem;border:0px;">Authorised Signatory</td>';
			$bottomTable.='</tr>';
		$bottomTable.='</table>';
		
		$originalCopy = '<div style="width:210mm;height:140mm;">'.$topSectionO.$baseSection.$itemList.$bottomTable.'</div>';
		$vendorCopy = '<div style="width:210mm;height:140mm;">'.$topSectionV.$baseSection.$itemList.$bottomTable.'</div>';
		
		$pdfData = $originalCopy."<br>".$vendorCopy;
		
		// print_r($pdfData);exit;
		$mpdf = $this->m_pdf->load();
		$pdfFileName='DC-REG-'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetProtection(array('print'));
		
		$mpdf->AddPage('P','','','','',5,5,5,5,5,5,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
	}

    public function returnVendorMaterial(){
		$id = $this->input->post('id');
		$dataRow = $this->jobWorkVendor_v3->getVendorChallanData($id);
		$packingData = !empty($dataRow->material_data)?json_decode($dataRow->material_data):[];
		$tbody="";
		if(!empty($packingData)): $i=1;
            foreach($packingData as $row):
				$item_name = $this->item->getItem($row->item_id)->item_name;
				$pendingQty = $row->out_qty - $row->in_qty;
				if($pendingQty > 0):
					$tbody.='<tr>
						<td style="width:5%;">'.$i++.'</td>
						<td>
							'.$item_name.'
							<input type="hidden" name="item_id[]" value="'.$row->item_id.'">  
						</td>
						<td>
							'.$row->out_qty.'
							<input type="hidden" name="out_qty[]" value="'.$row->out_qty.'">
							<input type="hidden" name="pre_in_qty[]" value="'.$row->in_qty.'">  
						</td>
						<td>
							<input type="text" class="form-control floatOnly" name="in_qty[]" value="">                                
						</td>
						<td>
							'.$pendingQty.'                                
						</td>
					</tr>';
				endif;
			endforeach;
		else:
		    $tbody.='<tr class="text-center"><td colspan="5">No Data Found</td></tr>';
		endif;

		$dataRow->tbody = $tbody;
		$this->data['dataRow'] = $dataRow;
		$this->load->view($this->return_material,$this->data);
	}

	public function saveReturnMaterial(){
		$data = $this->input->post();
		$data['material_data']=""; $materialArray = array();
		if(isset($data['item_id']) && !empty($data['item_id'])):
			foreach($data['item_id'] as $key=>$value):
				$materialArray[] = [
					'item_id' => $value,
					'out_qty' => $data['out_qty'][$key],
					'in_qty' => ($data['pre_in_qty'][$key] + $data['in_qty'][$key]),
				];
			endforeach;
			$data['material_data'] = json_encode($materialArray);
		endif;

		$this->printJson($this->jobWorkVendor_v3->saveReturnMaterial($data));
	}

	/************* Reteturn Log */
	public function addVendorLog(){
		$data = $this->input->post();
        $dataRow = $this->processMovement->getApprovalData(['id'=>$data['id']]); 
        $dataRow->job_approval_id = $dataRow->id;
        unset($dataRow->id);
        $dataRow->process_id = $dataRow->in_process_id;
        $dataRow->ref_id = $data['ch_trans_id'];
        $ctData=$this->process->getProductProcess(['process_id'=>$dataRow->process_id,'item_id'=>$dataRow->product_id]);
		$dataRow->m_ct = (!empty($ctData)) ? $ctData->master_ct : 0 ;
		$this->data['dataRow'] = $dataRow;
		$chData = $this->jobWorkVendor_v3->getJobworkVendorDetail(['id'=>$data['ch_trans_id']]);
		$this->data['transData'] = $this->receiveLogHtml(['challan_id'=>$chData->challan_id,'job_approval_id'=>$dataRow->job_approval_id]);
		$this->load->view($this->vendor_log, $this->data);
       
	}
	
	public function receiveLogHtml($data){
		$tbody = '';
		$transData = $this->jobWorkVendor_v3->getVendorReceiveTrans($data);
		$this->data['return_material'] = $this->jobWorkVendor_v3->getVendorChallanData($data['challan_id']);
		
		if(!empty($transData)){
			$i=1;
			foreach($transData as $row){
				$deleteButton = "";
				if($row->is_approve == 0){
					$deleteParam = $row->id.",'Log'";
					$deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trashLog('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
				}
				$tbody .='<tr>
					<td>'.$i++.'</td>
					<td>'.formatDate($row->log_date).'</td>
					<td>'.$row->in_challan_no.'</td>
					<td>'.$row->production_qty.'</td>
					<td>'.$deleteButton.'</td>
				</tr>';
			}
		}
		return $tbody;
	}

	public function saveVendorLog(){
		$data = $this->input->post();
        $errorMessage = array();
        
        if(empty($data['job_card_id'])){
            $errorMessage['job_card_id'] = "Job Card is required.";
        }
        
        if(empty($data['process_id'])){
            $errorMessage['process_id'] = "Process is required.";
        }
        
        if(empty($data['production_qty']) ){
            $errorMessage['production_qty'] = "Qty is required.";
        }
        
        if(empty($data['log_date']) && empty($data['is_approve'])){
            $errorMessage['log_date'] = "Date is required.";
        }elseif($data['log_date'] < date('Y-m-d') && empty($data['is_approve'])){ 
            $errorMessage['log_date'] = "Invalid Date.";
        }
        
        if(empty($data['in_challan_no']) && empty($data['is_approve'])){
            $errorMessage['in_challan_no'] = "In Challan No is required.";
        }
        
        /*Store Return Material*/
		$data['material_data']=""; $materialArray = array();
		if(isset($data['item_id']) && !empty($data['item_id'])):
			foreach($data['item_id'] as $key=>$value):
				$in_qty = (!empty($data['in_qty'][$key]) ? $data['in_qty'][$key] : 0);
				$out_qty = (!empty($data['out_qty'][$key]) ? $data['out_qty'][$key] : 0);
				$pre_in_qty = (!empty($data['pre_in_qty'][$key]) ? $data['pre_in_qty'][$key] : 0);
				$materialArray[] = [
					'item_id' => $value,
					'out_qty' => $out_qty,
					'in_qty' => ($pre_in_qty + $in_qty),
				];
			endforeach;
			$data['material_data'] = json_encode($materialArray);
		endif;
		
		$return_material = array_sum(array_column($materialArray, 'in_qty'));
        if(count($materialArray) > 0){
			if ($return_material <= 0) {
				$errorMessage['genral_error'] = "Return Material is required.";
			}			
		}
		
		
		
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            
            $data['ok_qty'] = $data['production_qty'];
			$challanData = $this->jobWorkVendor_v3->getJobWorkVendorRow($data['ref_id']);
            if ($data['production_qty'] > ($challanData->pending_qty)) {
                $errorMessage['general_error'] = "Qty is greater than pending qty";
                $this->printJson(['status' => 0, 'message' => $errorMessage]);
            }
            $data['challan_id'] = $challanData->challan_id;
            unset($data['log_type']);
            if(!empty($data['is_approve'])){
                $data['is_approve']= $this->loginId;
                $data['approved_at']= date("Y-m-d");
            }else{
                $data['created_by'] = $this->session->userdata('loginId'); 
            }
            
            /*Store Return Material*/
            $this->jobWorkVendor_v3->saveReturnMaterial($data);
			unset($data['item_id'],$data['out_qty'],$data['pre_in_qty'],$data['in_qty'],$data['material_data']);
            
            $this->printJson($this->jobWorkVendor_v3->saveReceiveChallan($data));
        endif;
	}

	public function deleteLog(){
		$data = $this->input->post();
        $errorMessage = array();
        
        if(empty($data['id'])){
            $errorMessage['id'] = "Something is wrong.";
        }
       
        if(!empty($errorMessage)):
            $this->printJson(['status'=>2,'message'=>$errorMessage]);
        else:
            $result = $this->jobWorkVendor_v3->deleteLog($data);
			if($result['status'] == 1){
				$result['tbody'] = $this->receiveLogHtml(['challan_id'=>$result['challan_id'],'job_approval_id'=>$result['job_approval_id']]);
			}   
            $this->printJson($result);
        endif;
	}

	public function vendorReceiveIndex($status=0){
		$this->data['status'] = $status;
        $this->data['tableHeader'] = getProductionHeader("jobWorkVendorReceive");
		$this->data['vendorData'] = $this->party->getVendorList(); 
        $this->load->view('production_v3/job_work_vendor/vendor_receive_index',$this->data);
    }

    public function getVendorReceiveDTRows($status=0,$dates='',$vendor_id=''){
		$data = $this->input->post(); 
		$data['status'] = $status; $data['vendor_id'] = $vendor_id;
		if(!empty($dates)){$data['from_date'] = explode('~',$dates)[0];$data['to_date'] = explode('~',$dates)[1];}
        $result = $this->jobWorkVendor_v3->getVendorReceiveDTRows($data);
        
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $row->controller = $this->data['headData']->controller;			
            $sendData[] = getJobWorkVendorReceiveData($row);
        endforeach;
        $result['data'] =$sendData;
        $this->printJson($result);
    }

	public function acceptChallan(){
        $data = $this->input->post();
        if(empty($data['ch_trans_id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->jobWorkVendor_v3->acceptChallan($data));
        endif;
    }
}
?>