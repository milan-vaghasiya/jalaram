<?php
class Rqc extends MY_Controller
{
    private $indexPage = "production_v3/rqc/index";
    private $pendingIndexPage = "production_v3/rqc/pending_rqc_index";
    private $formPage = "production_v3/rqc/form";

    public function __construct()
    {
        parent::__construct();
        $this->isLoggedin();
        $this->data['headData']->pageTitle = "Receiving Quality Control";
        $this->data['headData']->controller = "production_v3/rqc";
        $this->data['headData']->pageUrl = "production_v3/rqc";
    }

    public function index()
    {
        $this->data['tableHeader'] = getProductionHeader("pendingRqc");
        $this->load->view($this->pendingIndexPage, $this->data);
    }
    
    public function getPendingRqcDTRows($status=0)
    {
        $data = $this->input->post(); $data['status'] = $status;
        $result = $this->rqc->getPendingRqcDTRows($data);
        $sendData = array(); $i = 1;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getPendingRQCData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function rqcIndex()
    {
        $this->data['tableHeader'] = getProductionHeader("rqc");
        $this->load->view($this->indexPage, $this->data);
    }

    public function getDTRows($status=0)
    {
        $data = $this->input->post();
        $data['status'] = $status;
        $result = $this->rqc->getDTRows($data);
        $sendData = array(); $i = 1;
		
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getRQCData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addRqcReport($job_card_id,$process_id,$vendor_id,$log_id=0)
    {
        $jobData = $this->jobcard_v3->getJobcard($job_card_id);
        $prsData = $this->process->getProcess($process_id);
        $partyData = $this->party->getParty($vendor_id);
        $this->data['party_name'] = $partyData->party_name;
        $this->data['job_card_id'] = $job_card_id;
        $this->data['process_id'] = $process_id;
        $this->data['vendor_id'] = $vendor_id;
        $this->data['process_name'] = $prsData->process_name;
        $this->data['jobData'] = $jobData;
        $this->data['log_id'] = $log_id;
        $this->data['rqcList'] = $this->rqc->getPendingRqcData(['job_card_id'=>$job_card_id,'process_id'=>$process_id,'vendor_id'=>$vendor_id]);
        
        $rqcData  = $this->rqc->getRQCReports(['job_card_id'=>$job_card_id,'process_id'=>$process_id,'machine_id'=>'','item_id'=>$jobData->product_id,'trans_date'=>date("Y-m-d"),'singleRow'=>1]);
        
        if(!empty($rqcData)){
            $this->data['dataRow']=$rqcData;	
        }
        $pfcProcess = $this->item->getPrdProcessDataProductProcessWise(['item_id' =>$jobData->product_id, 'process_id' => $process_id,'pfc_rev_no'=>$jobData->pfc_rev_no]);
        if(!empty($pfcProcess->pfc_process)){
            $this->data['paramData'] =  $this->controlPlanV2->getCPDimenstion(['item_id'=>$jobData->product_id,'control_method'=>'RQC','process_no'=>$pfcProcess->pfc_process,'rev_no'=>$jobData->cp_rev_no]); 
        }
		$reqMaterials = $this->jobcard_v3->getMaterialIssueData($jobData); 
        $this->data['reqMaterials'] = (!empty($reqMaterials['resultData']))?$reqMaterials['resultData'][0]:'';
        $this->data['productLogData'] = $this->productionLog->getLogs($log_id);
        $this->load->view($this->formPage, $this->data);
    }

    /*public function save(){
        $data = $this->input->post();
        $errorMessage = Array();
		if(empty($data['item_id']))
            $errorMessage['item_id'] = "Item is required.";
        if(empty($data['grn_id']))
            $errorMessage['grn_id'] = "Jobcard is required.";
        if(empty($data['grn_trans_id']))
            $errorMessage['grn_trans_id'] = "Process No is required.";
        if(empty($data['party_id']))
            $errorMessage['party_id'] = "Machine is required.";

        if(empty($data['report_time'][0]))
            $errorMessage['general'] = "Enter atleast one report time";
            
        $jobData = $this->jobcard_v3->getJobcard($data['grn_id']); 
        $pfcProcess = $this->item->getPrdProcessDataProductProcessWise(['item_id' => $data['item_id'], 'process_id' => $data['grn_trans_id'],'pfc_rev_no'=>$jobData->pfc_rev_no]);
        $insParamData =  $this->controlPlanV2->getCPDimenstion(['item_id'=>$data['item_id'],'process_no'=>$pfcProcess->pfc_process,'control_method'=>'RQC','rev_no'=>$jobData->cp_rev_no]);
        
        if(count($insParamData) <= 0)
            $errorMessage['general'] = "Item Parameter is required.";
		if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            if(count($insParamData) <= 0)
                $errorMessage['general'] = "Item Parameter is required.";

            $pre_inspection = Array();$param_ids = Array();$data['observation_sample'] = '';$reportTime =[];
            if(!empty($insParamData)):
                $sample_size = $data['sampling_qty'];
                foreach($insParamData as $row):
                    $param = Array();
                    for($j = 1; $j <=$sample_size; $j++):
                        $param[] = $data['sample'.$j.'_'.$row->id];
                        unset($data['sample'.$j.'_'.$row->id]);
                    endfor;
                    $pre_inspection[$row->id] = $param;
                    $param_ids[] = $row->id;
                endforeach;
                
                foreach($data['report_time'] as $row){
                    if(!empty($row)){
                        $reportTime[] = $row;
                    }
                }
            endif;
			
			if($_FILES['third_party']['name'] != null || !empty($_FILES['third_party']['name'])):
                $this->load->library('upload');
				$_FILES['userfile']['name']     = $_FILES['third_party']['name'];
				$_FILES['userfile']['type']     = $_FILES['third_party']['type'];
				$_FILES['userfile']['tmp_name'] = $_FILES['third_party']['tmp_name'];
				$_FILES['userfile']['error']    = $_FILES['third_party']['error'];
				$_FILES['userfile']['size']     = $_FILES['third_party']['size'];
				
				$imagePath = realpath(APPPATH . '../assets/uploads/rqc_third_party/');
				$config = ['file_name' => $_FILES['userfile']['name'].time(),'allowed_types' => '*','max_size' => 10240,'overwrite' => FALSE, 'upload_path'	=>$imagePath];

				$this->upload->initialize($config);
				if(!$this->upload->do_upload()):
					$errorMessage['third_party'] = $this->upload->display_errors();
					$this->printJson(["status"=>0,"message"=>$errorMessage]);
				else:
					$uploadData = $this->upload->data();
					$data['third_party'] = $uploadData['file_name'];
				endif;
			endif;
			
            unset($data['sample_size'],$data['report_time']);
            $data['parameter_ids'] = implode(',',$param_ids);
            $data['observation_sample'] = json_encode($pre_inspection);
            $data['param_count'] = count($insParamData);
            $data['sampling_qty'] = count($reportTime);
            $data['result'] = !empty($reportTime)?implode(',',$reportTime):'';
            $data['created_by'] = $this->session->userdata('loginId');
            
            $this->printJson($this->rqc->save($data));
        endif;
    }*/
    
    public function save(){
        $data = $this->input->post();
        $errorMessage = Array();
        
		if(empty($data['item_id']))
            $errorMessage['item_id'] = "Item is required.";
        if(empty($data['grn_trans_id']))
            $errorMessage['grn_trans_id'] = "Process No is required.";
        if(empty($data['party_id']))
            $errorMessage['party_id'] = "Machine is required.";
        if(empty($data['report_time'][0]))
            $errorMessage['general'] = "Enter atleast one report time";
        if(empty($data['log_id']))
            $errorMessage['log_id'] = "Challan is required";
            
        if(empty($data['trans_date'])){
            $errorMessage['trans_date'] = "Date is required";
        }elseif($data['trans_date'] < date('Y-m-d')){ 
            $errorMessage['trans_date'] = "Invalid Date.";
        }
            
		if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
			if($_FILES['third_party']['name'] != null || !empty($_FILES['third_party']['name'])):
                $this->load->library('upload');
				$_FILES['userfile']['name']     = $_FILES['third_party']['name'];
				$_FILES['userfile']['type']     = $_FILES['third_party']['type'];
				$_FILES['userfile']['tmp_name'] = $_FILES['third_party']['tmp_name'];
				$_FILES['userfile']['error']    = $_FILES['third_party']['error'];
				$_FILES['userfile']['size']     = $_FILES['third_party']['size'];
				
				$imagePath = realpath(APPPATH . '../assets/uploads/rqc_third_party/');
				$config = ['file_name' => $_FILES['userfile']['name'].time(),'allowed_types' => '*','max_size' => 10240,'overwrite' => FALSE, 'upload_path'	=>$imagePath];

				$this->upload->initialize($config);
				if(!$this->upload->do_upload()):
					$errorMessage['third_party'] = $this->upload->display_errors();
					$this->printJson(["status"=>0,"message"=>$errorMessage]);
				else:
					$uploadData = $this->upload->data();
					$data['third_party'] = $uploadData['file_name'];
				endif;
			endif;
			
            unset($data['sample_size']);
            $data['created_by'] = $this->session->userdata('loginId');
			
            $this->printJson($this->rqc->save($data));
        endif;
    }

    public function edit($id){
        $this->data['dataRow']= $rqcData = $this->rqc->getRqcData($id);	
        $pfcProcess = $this->item->getPrdProcessDataProductProcessWise(['item_id' =>$rqcData->item_id ,'process_id' =>$rqcData->grn_trans_id,'pfc_rev_no'=>$rqcData->pfc_rev_no]);
        $this->data['paramData'] =  $this->controlPlanV2->getCPDimenstion(['item_id'=>$rqcData->item_id,'process_no'=>$pfcProcess->pfc_process,'control_method'=>'RQC','responsibility'=>'INSP','rev_no'=>$rqcData->cp_rev_no]);
		$this->data['companyData'] = $this->purchaseOrder->getCompanyInfo();
        $this->data['machineList'] = $this->item->getItemList(5);
        $this->data['rqcList'] = $this->rqc->getPendingRqcData(['job_card_id'=>$rqcData->grn_id,'process_id'=>$rqcData->grn_trans_id,'vendor_id'=>$rqcData->party_id]);
		$jobData = $this->jobcard_v3->getJobcard($rqcData->grn_id);
		$reqMaterials = $this->jobcard_v3->getMaterialIssueData($jobData); 
        $this->data['reqMaterials'] = (!empty($reqMaterials['resultData']))?$reqMaterials['resultData'][0]:'';
        $this->load->view($this->formPage, $this->data);
    }
    
    /* Updated By :- Sweta @28-08-2023 */
    public function rqc_pdf($id){
        $this->data['rqcData'] = $rqcData = $this->rqc->getRqcData($id);		
        $pfcProcess = $this->item->getPrdProcessDataProductProcessWise(['item_id' =>$rqcData->item_id ,'process_id' =>$rqcData->grn_trans_id,'pfc_rev_no'=>$rqcData->pfc_rev_no]);

		$paramData =  $this->controlPlanV2->getCPDimenstion(['item_id'=>$rqcData->item_id,'process_no'=>$pfcProcess->pfc_process,'control_method'=>'RQC','responsibility'=>'INSP','rev_no'=>$rqcData->cp_rev_no]);

		$pramIds = explode(',', $rqcData->parameter_ids);
		$smplingQty = ($rqcData->sampling_qty > 0) ? $rqcData->sampling_qty : 0;
        
		$jobData = $this->jobcard_v3->getJobcard($rqcData->grn_id);
		$reqMaterials = $this->jobcard_v3->getMaterialIssueData($jobData); 
        $this->data['reqMaterials'] = (!empty($reqMaterials['resultData']))?$reqMaterials['resultData'][0]:'';	
									
        $tbodyData="";$theadData="";$i=1;
        if(!empty($paramData)):
            foreach($paramData as $row):

                if (in_array($row->id, $pramIds)) :
                    $diamention ='';
                    if($row->requirement==1){ $diamention = $row->min_req.'/'.$row->max_req ; }
                    if($row->requirement==2){ $diamention = $row->min_req.' '.$row->other_req ; }
                    if($row->requirement==3){ $diamention = $row->max_req.' '.$row->other_req ; }
                    if($row->requirement==4){ $diamention = $row->other_req ; }
                    $char_class=''; if(!empty($row->char_class)){ $char_class='<img src="' . base_url('assets/images/symbols/'.$row->char_class.'.png') . '" style="width:20px;display:inline-block;vertical-align:middle;" />'; }

                    $paramItems = '<tr>
                        <td style="text-align:center;" height="30">'.$i.'</td>
                        <td style="text-align:center;">'.$row->process_no.' '.$char_class.'</td>
                        <td>'.$row->product_param.'</td>
                        <td style="text-align:center;">'.$diamention.'</td>
                        <td>'.$row->iir_measur_tech.'</td>
                        <td style="text-align:center;">'.$row->iir_size.'</td>
                        <td style="text-align:center;">'.$row->iir_freq_text.'</td>';
                    
                        $objData = $this->pir->getPirDataForPrint(['id'=>$rqcData->id]);

                        foreach($objData as $read):
                            $obs_time = explode(",",$read->result);
                            $obs_time_count = count($obs_time);
                            foreach($obs_time as $key=>$value){
                                if($i==1){
                                    $trans_date = (!empty($value)?$value:'');
                                    $theadData .= '<td style="text-align:center;width:70px;">'.$trans_date.'</td>';
                                }
                            }
                            $obj = New StdClass; 
                            $obj = json_decode($read->observation_sample);
                            $obj_count = count($obj->{$row->id});
                            for($j=0; $j<$obj_count; $j++):
                                if(!empty($obj->{$row->id})):
                                    $paramItems .= '<td style="text-align:center;">'.$obj->{$row->id}[$j].'</td>';
                                endif;
                            endfor;
                        endforeach;
                        $paramItems .= '</tr>';
                    $tbodyData .= $paramItems;

                    $i++;
                endif;
            endforeach;
        else:
            $tbodyData.= '<tr><td colspan="8" style="text-align:center;">No Data Found</td></tr>';
        endif;
        $this->data['rcount'] = (!empty($paramData)?$paramData[0]->iir_size:1); //$rcount;
        $this->data['theadData'] = $theadData;
        $this->data['tbodyData'] = $tbodyData;

		$logo=base_url('assets/images/logo.png'); $unapproved = base_url('assets/images/unapproved.jpg');
		$this->data['letter_head']=base_url('assets/images/letterhead_top.png');
		
		$bodyData = $this->load->view('production_v3/rqc/rqc_print',$this->data,true);
		$htmlHeader = '<table class="table">
							<tr>
								<td style="width:25%;"><img src="'.$logo.'" style="height:50px;"></td>
								<td class="org_title text-center" style="font-size:1rem;width:50%">Receiving Quality Control</td>
								<td style="width:25%;" class="text-right"><span style="font-size:0.8rem;">F QA 02 01(01/08/2021)</td>
							</tr>
						</table><hr>';
		$htmlFooter = '<table class="table" style="border-bottom:1px solid #000000;">
						<tr>
							<td style="width:50%;"></td>
							<td style="width:25%;" class="text-center">'.$rqcData->emp_name.'</td>
							<td style="width:25%;" class="text-center">'.$rqcData->approve_name.'</td>
						</tr>
						<tr>
							<td style="width:50%;"></td>
							<td style="width:25%;" class="text-center"><b>Inspected By</b></td>
							<td style="width:25%;" class="text-center"><b>Verified By</b></td>
						</tr>
					</table>
					<table class="table top-table" style="margin-top:10px;">
						<tr>
							<!--<td style="width:25%;">PO No. & Date : </td>-->
							<td style="width:25%;"></td>
							<td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
						</tr>
					</table>';
		
        $pdfData = '<div>'.$bodyData.'</div>';

		$mpdf = new \Mpdf\Mpdf();
		$pdfFileName='rqc'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetProtection(array('print'));		
		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('L','','','','',5,5,25,30,5,5,'','','','','','','','','','A4-L');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');	
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->rqc->delete($id));
        endif;
    }

	public function approveRqc(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->rqc->approveRqc($data));
        endif;
    }

}
