<?php

class RmProcess extends MY_Controller
{
    private $indexPage = "rm_process/index";
    private $formPage = "rm_process/form";
    private $return_rm = "rm_process/return_rm";
	
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "RM Process";
		$this->data['headData']->controller = "rmProcess";
		$this->data['headData']->pageUrl = "rmProcess";
	}
	
	public function index(){
        $this->data['tableHeader'] = getProductionHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $data = $this->input->post(); 
        $result = $this->rmProcessModel->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            
            $recivedItem = $this->rmProcessModel->getRmProcessList($row->id);
            $row->return_itm = ''; $j=1;
            foreach($recivedItem as $itm):
                if(!empty($itm->item_id)){
                    if($j==1){
                        $row->return_itm .= $itm->item_name.' (Qty: '.$itm->qty.' '.$itm->unit_name.')'; 
                    } else { 
                        $row->return_itm .= '<br>'.$itm->item_name.' (Qty: '.$itm->qty.' '.$itm->unit_name.')'; 
                    } $j++;
                } 
            endforeach;
            
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getRmProcessData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addRmProcess(){
        $this->data['itemList']=$this->item->getItemList(3);
        $this->data['vendorList']=$this->party->getVendorList();
        $this->load->view($this->formPage,$this->data);
    }

	/**
	 * Updated By Mansee
	 * Date : 12-02-2022
	*/
    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        
        // if(empty($data['log_date']))
        // $errorMessage['log_date'] = "Date is required.";
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');  
            $this->printJson($this->rmProcessModel->save($data));
        endif;
    }
	
    public function edit(){     
        $this->data['itemList']=$this->item->getItemList(3);
        $this->data['vendorList']=$this->party->getVendorList();
        $dataRow = $this->rmProcessModel->getRmProcess($this->input->post('id'));
        $this->data['dataRow'] = $dataRow;
        $this->load->view($this->formPage,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->rmProcessModel->delete($id));
        endif;
    }

    public function batchWiseItemStock(){
		$data = $this->input->post();
        $result = $this->rmProcessModel->batchWiseItemStock($data);
        $this->printJson($result);
	}

    /*Created By : Avruti @21-3-2022 */
    public function returnRmProcess(){
        $id = $this->input->post('id'); 
        $this->data['itemList']=$this->item->getItemList(3);
        $this->data['locationData'] = $this->store->getStoreLocationListWithoutProcess();
        $this->data['dataRow'] = $dataRow = $this->rmProcessModel->getRmProcess($id);  
        $this->data['calData'] = $this->rmProcessModel->getRmProcessList($id);
        $this->data['batchData'] = $this->rmProcessModel->getRMbatch($dataRow->trans_ref_id); 
        $this->load->view($this->return_rm,$this->data);
    }

    public function saveReturnRm(){
        $data = $this->input->post(); //print_r($data);exit;
        $errorMessage = array();
        if(empty($data['return_item_id']))
            $errorMessage['return_item_id'] = "Item is required.";
        if(empty($data['qty']))
            $errorMessage['qty'] = "Qty is required.";
        if(empty($data['location_id']))
            $errorMessage['location_id'] = "Location is required."; 

        if(!empty($data['return_item_id'])):
            $pendingQty = $this->rmProcessModel->getReturnPending($data['trans_ref_id'],$data['ref_batch']);
            if($data['qty'] > $pendingQty):
                $errorMessage['qty'] = "Qty not available for return.";
            endif;
        endif;
                      
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $response = $this->rmProcessModel->saveReturnRm($data);
            $result = $this->rmProcessModel->getRmProcessList($data['ref_id']);
            $i=1;$tbodyData="";
            if(!empty($result)) :
                foreach ($result as $row) :
                    $deleteParam = $row->id.",'RmProcess'";
                    $tbodyData.= '<tr>
                            <td>'.$i.'</td>
                            <td>'.$row->item_name.'</td>
                            <td>'.$row->qty.'</td>
                            <td>'.$row->location.'</td>
                            <td>'.$row->batch_no.'</td>    
                            <td class="text-center">
                            <a class="btn btn-outline-danger btn-delete" href="javascript:void(0)" onclick="trashReturnRm('.$row->id.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>
                            </td>
                            </tr>'; 
                            $i++;
                endforeach;
            else:
                $tbodyData.= '<tr><td colspan="6" style="text-align:center;">No Data Found</td></tr>';
            endif;
            $this->printJson(['status'=>1, "tbodyData"=>$tbodyData]);
        endif;
    }

    public function deleteReturnRm(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $ref_id=$this->rmProcessModel->deleteReturnRm($id);
            $result = $this->rmProcessModel->getRmProcessList($ref_id);
            $i=1;$tbodyData=""; 
            if(!empty($result)) :
                foreach ($result as $row) :
                    $deleteParam = $row->id.",'RmProcess'";
                    $tbodyData.= '<tr>
                            <td>'.$i.'</td>
                            <td>'.$row->item_name.'</td>
                            <td>'.$row->qty.'</td>
                            <td>'.$row->location.'</td>
                            <td>'.$row->batch_no.'</td>    
                            <td class="text-center">
                            <a class="btn btn-outline-danger btn-delete" href="javascript:void(0)" onclick="trashReturnRm('.$row->id.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>
                            </td>
                            </tr>'; $i++;
                endforeach;
            else:
                $tbodyData.= '<tr><td colspan="6" style="text-align:center;">No Data Found</td></tr>';
            endif;
            $this->printJson(['status'=>1, "tbodyData"=>$tbodyData]);
        endif;
    }
    
    public function getJobOrderList(){
		$vendor_id = $this->input->post('vendor_id');
		$product_id = $this->input->post('item_id');
		$job_order_id = $this->input->post('job_order_id'); 
        $result = $this->rmProcessModel->getJobOrderList($vendor_id,$product_id); 
		$options="";
		if(!empty($result)): 
			$options .= '<option value="">Select Job Work Order</option>';
			foreach($result as $row):
				$selected = (!empty($job_order_id) && $job_order_id == $row->id)?'selected':'';
			
					$options .= "<option data-row='".json_encode($row)."' value='".$row->id."' ".$selected." >".getPrefixNumber($row->jwo_prefix,$row->jwo_no)."</option>";
		
				endforeach;
		else:
			$options .= '<option value="">Select Job Work Order</option>';
		endif;
		
		$this->printJson(['status'=>1, 'options'=>$options]);
	}
	
	function rmProcessOutChallan($id){
		$jobData = $this->rmProcessModel->getRmProcessData($id);
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
								<td class="text-left" height="25"><b>Challan No. :</b> '.$jobData->remark.' </td>
							</tr>
							<tr>
								<td class="text-left" height="25"><b>Challan Date :</b> '.date("d-m-Y",strtotime($jobData->created_at)).' </td>
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
			
        $i=1;$itemCode="";$jobNo="";$deliveryDate="";$processName="";$remark="";$inQty="";$weight=""; $totalOut=0; $totalWeight=0; $blnkRow=4;
        
        if(!empty($jobData)){

            $pdays = (!empty($jobData->production_days)) ? "+".$jobData->production_days." day" : "+0 day";
    		$delivery_date = date('d-m-Y',strtotime($pdays, strtotime($jobData->created_at)));

            $itemList.='<tr>
                <td style="vertical-align:top;padding:5px;">'.$i++.'</td>
                <td style="vertical-align:top;padding:5px;">'.$jobData->item_name.'</td>
                <td style="vertical-align:top;padding:5px;">'. ((!empty($jobData->challan_prefix))? getPrefixNumber($jobData->challan_prefix,$jobData->challan_no) : '').' <br> <small>(Batch No: '.((!empty($jobData->batch_no))? $jobData->batch_no : "").')</small> </td>
                <td style="vertical-align:top;padding:5px;">'.((!empty($delivery_date))?$delivery_date:'').'</td>
                <td style="vertical-align:top;padding:5px;">'.((!empty($jobData->process_name))?$jobData->process_name:'').'</td>
                <td style="vertical-align:top;padding:5px;"></td>
                <td class="text-center" style="vertical-align:top;padding:5px;">'.((!empty($jobData->qty))?sprintf('%0.0f',$jobData->qty):'').'</td>
            </tr>';
            $totalOut += sprintf('%0.0f',$jobData->qty);
        }
		$materialDetails="";
		
		
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
		
		
		$itemList.='<tr>
			<th class="text-left" style="vertical-align:top;height:50px;padding:5px;font-weight: normal;" colspan="7">
				<b>Material Details : </b>'.$materialDetails.'
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
		
		$mpdf = new \Mpdf\Mpdf();
		$pdfFileName='DC-REG-'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetProtection(array('print'));
		
		$mpdf->AddPage('P','','','','',5,5,5,5,5,5,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
	}
}
?>