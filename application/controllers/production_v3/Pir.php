<?php
class Pir extends MY_Controller
{
    private $indexPage = "production_v3/pir/index";
    private $pendingIndexPage = "production_v3/pir/pending_pir_index";
    private $formPage = "production_v3/pir/form";

    public function __construct(){
        parent::__construct();
        $this->isLoggedin();
        $this->data['headData']->pageTitle = "PIR Report";
        $this->data['headData']->controller = "production_v3/pir";
        $this->data['headData']->pageUrl = "production_v3/pir";
    }

    public function index(){
        $this->data['tableHeader'] = getProductionHeader("pendingPir");
        $this->load->view($this->pendingIndexPage, $this->data);
    }
    
	public function getPendingPirDTRows($status=0){
        $data = $this->input->post();
        $data['status'] = $status;
        $result = $this->pir->getPendingPirDTRows($data);
        $sendData = array();
        $i = 1;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getPendingPIRData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function pirIndex(){
        $this->data['tableHeader'] = getProductionHeader("pir");
        $this->load->view($this->indexPage, $this->data);
    }

    public function getDTRows($status=0){
        $data = $this->input->post();
        $data['status'] = $status;
        $result = $this->pir->getDTRows($data);
        $sendData = array();
        $i = 1;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getPIRData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addPirReport($job_card_id,$process_id){
        $jobData = $this->jobcard_v3->getJobcard($job_card_id); 
        $prsData = $this->process->getProcess($process_id);
        $this->data['job_card_id'] = $job_card_id;
        $this->data['process_id'] = $process_id;
        $this->data['process_name'] = $prsData->process_name;
        $this->data['machineList'] = $this->item->getItemList(5);        
		$this->data['empData'] = $this->employee->getMachineOperatorList();
        $this->data['machine_name'] = !empty($mcData->item_name)?$mcData->item_name:'';
        $this->data['machine_code'] = !empty($mcData->item_code)?$mcData->item_code:'';
        $this->data['jobData'] = $jobData;
        $pirData  = $this->pir->getPIRReports(['job_card_id'=>$job_card_id,'process_id'=>$process_id,'machine_id'=>'','item_id'=>$jobData->product_id,'trans_date'=>date("Y-m-d"),'singleRow'=>1]);
        
        if(!empty($pirData)){
            $this->data['dataRow']=$pirData;	
        }
        $pfcProcess = $this->item->getPrdProcessDataProductProcessWise(['item_id' =>$jobData->product_id, 'process_id' => $process_id,'pfc_rev_no'=>$jobData->pfc_rev_no]);
        if(!empty($pfcProcess->pfc_process)){
            $this->data['paramData'] =  $this->controlPlanV2->getCPDimenstion(['item_id'=>$jobData->product_id,'control_method'=>'PIR','process_no'=>$pfcProcess->pfc_process,'rev_no'=>$jobData->cp_rev_no]); 
        }
		
        $this->load->view($this->formPage, $this->data);
    }

    public function save(){
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
        $insParamData =  $this->controlPlanV2->getCPDimenstion(['item_id'=>$data['item_id'],'process_no'=>$pfcProcess->pfc_process,'control_method'=>'PIR','rev_no'=>$jobData->cp_rev_no]);
        if(count($insParamData) <= 0)
            $errorMessage['general'] = "Item Parameter is required.";
		if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
           
            if(count($insParamData) <= 0)
                $errorMessage['general'] = "Item Parameter is required.";

            $pre_inspection = Array();$param_ids = Array();$data['observation_sample'] = '';$reportTime =[];
            if(!empty($insParamData)):
                $sample_size = 1;
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
            unset($data['sample_size'],$data['report_time']);
            $data['parameter_ids'] = implode(',',$param_ids);
            $data['observation_sample'] = json_encode($pre_inspection);
            $data['param_count'] = count($insParamData);
            $data['sampling_qty'] = count($reportTime);
            $data['result'] = !empty($reportTime)?implode(',',$reportTime):'';
            $data['created_by'] = $this->session->userdata('loginId');
            // print_r($data);exit;
            $this->printJson($this->pir->save($data));
        endif;
    }

    public function edit($id){
        $this->data['dataRow']=$pirData = $this->pir->getPirData($id);	
        $pfcProcess = $this->item->getPrdProcessDataProductProcessWise(['item_id' =>$pirData->item_id ,'process_id' =>$pirData->grn_trans_id,'pfc_rev_no'=>$pirData->pfc_rev_no]);
        $this->data['paramData'] =  $this->controlPlanV2->getCPDimenstion(['item_id'=>$pirData->item_id,'process_no'=>$pfcProcess->pfc_process,'control_method'=>'PIR','responsibility'=>'INSP','rev_no'=>$pirData->cp_rev_no]);
		$this->data['companyData'] = $this->purchaseOrder->getCompanyInfo();
        $this->data['machineList'] = $this->item->getItemList(5);        
		$this->data['empData'] = $this->employee->getMachineOperatorList();
        $this->load->view($this->formPage, $this->data);
    }
    
    /* Updated By :- Sweta @28-08-2023 */
    public function pir_pdf($id){
        $this->data['pirData'] = $pirData = $this->pir->getPirData($id);		
        $pfcProcess = $this->item->getPrdProcessDataProductProcessWise(['item_id' =>$pirData->item_id ,'process_id' =>$pirData->grn_trans_id,'pfc_rev_no'=>$pirData->pfc_rev_no]);

		$paramData = $this->controlPlanV2->getCPDimenstion(['item_id'=>$pirData->item_id,'process_no'=>$pfcProcess->pfc_process,'control_method'=>'PIR','responsibility'=>'INSP','rev_no'=>$pirData->cp_rev_no]);

		$pramIds = explode(',', $pirData->parameter_ids);
		$smplingQty = ($pirData->sampling_qty > 0) ? $pirData->sampling_qty : 0;

        $tbodyData="";$theadData="";$i=1;
        if(!empty($paramData)):
            foreach($paramData as $row):

                if (in_array($row->id, $pramIds)) :
                    $os = json_decode($pirData->observation_sample);
                    $diamention ='';
                    if($row->requirement==1){ $diamention = $row->min_req.'/'.$row->max_req ; }
                    if($row->requirement==2){ $diamention = $row->min_req.' '.$row->other_req ; }
                    if($row->requirement==3){ $diamention = $row->max_req.' '.$row->other_req ; }
                    if($row->requirement==4){ $diamention = $row->other_req ; }
                    $char_class=''; if(!empty($row->char_class)){ $char_class='<img src="' . base_url('assets/images/symbols/'.$row->char_class.'.png') . '" style="width:20px;display:inline-block;vertical-align:middle;" />'; }

                    $paramItems = '<tr>
                        <td style="text-align:center;" height="30">'.$i.'</td>
                        <td style="text-align:center;">'.$row->process_no.' '.$char_class.'</td>
                        <td style="text-align:center;">'.$row->product_param.'</td>
                        <td style="text-align:center;">'.$diamention.'</td>
                        <td style="text-align:center;">'.$row->ipr_measur_tech.'</td>
                        <td style="text-align:center;">'.$row->ipr_size.'</td>
                        <td style="text-align:center;">'.$row->ipr_freq_text.'</td>';
                    
                        $objData = $this->pir->getPirDataForPrint(['grn_id'=>$pirData->grn_id,'machine_id'=>$pirData->party_id, 'grn_trans_id'=>$pirData->grn_trans_id, 'trans_date'=>$pirData->trans_date]);

                        $rcount = count($objData);
                        foreach($objData as $read):
                            if($i==1){
                                $trans_date = (!empty($read->result)?date("h:i A",strtotime($read->result)):'');
                                $theadData .= '<td style="text-align:center;">'.$trans_date.'</td>';
                            }
                            $obj = New StdClass; 
                            $obj = json_decode($read->observation_sample);
                            if(!empty($obj->{$row->id})):
                                $paramItems .= '<td style="text-align:center;">'.$obj->{$row->id}[0].'</td>';
                            endif;
                        endforeach;
                        $paramItems .= '</tr>';
                    $tbodyData .= $paramItems;

                    $i++;
                endif;
            endforeach;
        else:
            $tbodyData.= '<tr><td colspan="8" style="text-align:center;">No Data Found</td></tr>';
        endif;
        $this->data['rcount'] = $rcount;
        $this->data['theadData'] = $theadData;
        $this->data['tbodyData'] = $tbodyData;

		$logo=base_url('assets/images/logo.png'); $unapproved = base_url('assets/images/unapproved.jpg');
		$this->data['letter_head']=base_url('assets/images/letterhead_top.png');
		
		$bodyData = $this->load->view('production_v3/pir/pir_print',$this->data,true);
		$htmlHeader = '<table class="table">
							<tr>
								<td style="width:25%;"><img src="'.$logo.'" style="height:50px;"></td>
								<td class="org_title text-center" style="font-size:1rem;width:50%">Inprocess (Patrol) Inspection Report</td>
								<td style="width:25%;" class="text-right"><span style="font-size:0.8rem;">F QA 04 00(01/06/2020)</td>
							</tr>
						</table><hr>';
		$htmlFooter = '<table class="table" style="border-bottom:1px solid #000000;">
						<tr>
							<td style="width:50%;"></td>
							<td style="width:25%;" class="text-center">'.$pirData->emp_name.'</td>
							<td style="width:25%;" class="text-center">'.$pirData->approve_name.'</td>
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
		$pdfFileName='pir'.$id.'.pdf';
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
            $this->printJson($this->pir->delete($id));
        endif;
    }

	public function approvePir(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->pir->approvePir($data));
        endif;
    }

}
