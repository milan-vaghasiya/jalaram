<?php
class Ecn extends MY_Controller
{
    private $revise_check_point ="npd/ecn/revise_check_point";
    private $revise_ch_index ="npd/ecn/revise_ch_index";
    private $revise_ch_pending_index ="npd/ecn/revise_ch_pending_index";
    private $revise_ch_review_index ="npd/ecn/revise_ch_review_index";
    private $verify_form ="npd/ecn/verify_form";
    private $cp_rev_index ="npd/ecn/cp_rev_index";
    private $cp_rev_form ="npd/ecn/cp_rev_form";
    
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Ecn";
		$this->data['headData']->controller = "npd/ecn";
	}

    /*
        ---------------------- Revision Check Point ----------------------
        created By :- Sweta @12-08-2023
    */
    public function reviseCheckPoint($status = 0,$entry_type =1){
        $this->data['status'] = $status;
        $this->data['entry_type'] = $entry_type;
		$this->data['headData']->pageUrl = "npd/ecn/reviseCheckPoint";
        $this->data['tableHeader'] = getSalesDtHeader("reviseChPoint");
        $this->load->view($this->revise_ch_index,$this->data);
    }

    public function getRevChDTRows($status=0,$entry_type = 1){  
        $data = $this->input->post(); $data['status'] = $status;$data['entry_type'] = $entry_type;
        $result = $this->ecn->getRevChDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $row->status_label='';
            $row->controller = $this->data['headData']->controller;
            if($row->status == 0):
				$row->status_label = '<span class="badge badge-pill badge-danger m-1">Pending</span>';
			elseif($row->status == 1):
				$row->status_label = '<span class="badge badge-pill badge-warning m-1">In Process</span>';
            elseif($row->status == 2):
                $row->status_label = '<span class="badge badge-pill badge-dark m-1">Closed</span>';
            elseif($row->status == 3):
                $row->status_label = '<span class="badge badge-pill badge-success m-1">Approved</span>';
			endif;
            $sendData[] = getRevChData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function reviseCheckPointPending(){
		$this->data['headData']->pageUrl = "npd/ecn/reviseCheckPointPending";
        $this->data['tableHeader'] = getSalesDtHeader("reviseChPointPending");
        $this->load->view($this->revise_ch_pending_index,$this->data);
    }

    public function getRevChPendingDTRows($status=0,$entry_type = 1){  
        $data = $this->input->post(); $data['status'] = $status;$data['entry_type'] = $entry_type;
        $result = $this->ecn->getRevChPendingDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $row->loginId = $this->loginId;
            $sendData[] = getRevChPendingData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function reviseCheckPointReview(){
		$this->data['headData']->pageUrl = "npd/ecn/reviseCheckPointReview";
        $this->data['tableHeader'] = getSalesDtHeader("reviseChPointReview");
        $this->load->view($this->revise_ch_review_index,$this->data);
    }

    public function getRevChReviewDTRows($status=0,$entry_type = 1){  
        $data = $this->input->post(); $data['status'] = $status;$data['entry_type'] = $entry_type;
        $result = $this->ecn->getRevChReviewDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getRevChReviewData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addReviseCheckPoint($ref_id = ""){
        $entry_type=1;
        if(!empty($ref_id)){
            $dataRow = $this->ecn->getCheckPointReview($ref_id);
            $dataRow->ref_id = $ref_id;
            $dataRow->entry_type = 2;
            $rev_no= $this->ecn->getNextJJIRevNo($dataRow->item_id,2);
            $dataRow->rev_no = sprintf("CP%02d",$rev_no);
            unset($dataRow->id);
            $this->data['dataRow'] = $dataRow;
            $entry_type = 2;
        }
        $this->data['itemData'] = $this->item->getItemList(1);
        $this->data['ecn_note_no'] = $this->ecn->nextRevChNo($entry_type);
        $this->data['ecn_prefix'] = date("y").'/';
        $this->data['revList'] = $this->masterDetail->getRevChPointList();
        $this->data['mtGradeData'] = $this->materialGrade->getMaterialGrades();
        $this->data['deptData'] = $this->department->getDepartmentList();
        $this->data['empData'] = $this->employee->getEmpList();
        $this->load->view($this->revise_check_point,$this->data);
    }

    public function getOldRevGradeByItem(){
        $data = $this->input->post();      
        $itemData = $this->ecn->getPrevRevisionData(['item_id'=>$data['item_id'],'rev_date'=>$data['rev_date']]);
		
        $gradeOption="";  
        // if(!empty($itemData)):
            $mtGradeData = $this->materialGrade->getMaterialGrades();
            $gradeOption = '';

            foreach($mtGradeData as $row) :
                $selected = (!empty($itemData->material_grade) && (in_array($row->material_grade,explode(",",$itemData->material_grade)))) ? "selected" : "";
                $gradeOption .= '<option value="' . $row->material_grade . '" '.$selected.'>' . $row->material_grade . '</option>';
            endforeach;

        // endif;
        /*** Revision No */
        $nextRevNo = $this->ecn->getNextJJIRevNo($data['item_id']);

        $this->printJson(['gradeOption'=>$gradeOption,'material_grade'=>(!empty($itemData->material_grade)?$itemData->material_grade:''),'jji_rev_no'=> sprintf("R%02d",$nextRevNo)]);
    }


    public function saveRevChPoint(){
        $data = $this->input->post();
		$errorMessage = array();
        if(empty($data['item_id'])){
			$errorMessage['item_id'] = "Item Name is required.";
		}
		if(empty($data['ecn_note_no'])){
			$errorMessage['ecn_note_no'] = "ECN Note No. is required.";
		}
		if(empty($data['rev_no'])){
			$errorMessage['rev_no'] = "Revision No. is required.";
		}
		if(empty($data['rev_date'])){
			$errorMessage['rev_date'] = "Revision Date is required.";
		}
		// if(empty($data['ecn_drg_no'])){
		// 	$errorMessage['ecn_drg_no'] = "Drawing No. is required.";
		// }
		// if(empty($data['ecn_no'])){
		// 	$errorMessage['ecn_no'] = "ECN No. is required.";
		// }
		if(empty($data['ecn_received_date'])){
			$errorMessage['ecn_received_date'] = "ECN Received Date is required.";
		}
		if(empty($data['target_date'])){
			$errorMessage['target_date'] = "Target Date is required.";
		}
		if(empty($data['material_grade'])){
			$errorMessage['material_grade'] = "Material Grade is required.";
		}
		if(empty($data['dept_id'])){
			$errorMessage['dept_id'] = "Department is required.";
		}
        $i=1;$yCount=0;
        foreach($data['is_change'] as $key=>$is_change)
        {
            if(empty($is_change)){
                $errorMessage['is_change'.$i] = "Please Select Any One.";
            }
            if($is_change == "Y")
            {
            	if(empty($data['old_description'][$key])){
                    $errorMessage['old_description'.$i] = "Old Description is required.";
                }
            	if(empty($data['description'][$key])){
                    $errorMessage['description'.$i] = "New Description is required.";
                }
                if(empty($data['responsibility'][$key])){
                    $errorMessage['responsibility'.$i] = "Responsibility is required.";
                }
                if(empty($data['ch_target_date'][$key])){
                    $errorMessage['ch_target_date'.$i] = "Target Date is required.";
                }
                $yCount++;
            }
            $i++;
        }
		if($yCount == 0){
		    $errorMessage['item_name_error'] = "Please select atleast one check point as Y";
		}
		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
            unset($data['deptSelect'],$data['empSelect']);

            $masterData = [
                'id' => $data['rev_id'],
                'entry_type' => $data['entry_type'],
                'ref_id' => $data['ref_id'],
                'ecn_type' => $data['ecn_type'],
                'rev_type' => $data['rev_type'],
                'item_id' => $data['item_id'],
                'ecn_note_no' => $data['ecn_note_no'],
                'ecn_prefix' => $data['ecn_prefix'],
                'rev_no' => $data['rev_no'],
                'rev_date' => $data['rev_date'],
                'cust_rev_no' => $data['cust_rev_no'],
                'cust_rev_date' => $data['cust_rev_date'],
                'ecn_drg_no' => $data['ecn_drg_no'],
                'ecn_no' => $data['ecn_no'],
                'ecn_received_date' => $data['ecn_received_date'],
                'target_date' => $data['target_date'],
                'material_grade' => $data['material_grade'],
                'dept_id' => $data['dept_id'],
                'remark' => $data['remark'],
                'pfc_remark' => $data['pfc_remark'],
                'created_by' => $this->session->userdata('loginId')
            ];

            $transData = [
                'id' => $data['rev_ch_id'],
                'check_point_id' => $data['check_point_id'],
                'is_change' => $data['is_change'],
                'old_description' => $data['old_description'],
                'description' => $data['description'],
                'responsibility' => $data['responsibility'],
                'ch_target_date' => $data['ch_target_date'],
                'created_by' => $this->session->userdata('loginId')
            ];

			$this->printJson($this->ecn->saveRevChPoint($masterData,$transData));
		endif;
    }

    public function editReviseCheckPoint($id){
        $this->data['dataRow'] = $dataRow = $this->ecn->getCheckPointReview($id);
        $this->data['revList'] = $this->ecn->getCheckPointReviewTrans($id);
        $this->data['itemData'] = $this->item->getItemList(1);
        $this->data['mtGradeData'] = $this->materialGrade->getMaterialGrades();
        $this->data['deptData'] = $this->department->getDepartmentList();
        $this->data['empData'] = $this->employee->getEmpList();
        $this->load->view($this->revise_check_point,$this->data);
    }

    public function deleteEcn(){
        $id = $this->input->post('id');
		if(empty($id)):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->ecn->deleteEcn($id));
		endif;
    } 
    
    public function startEcn(){
        $data = $this->input->post();
        $this->printJson($this->ecn->startEcn($data));
    }

    public function activeRevision(){
        $data = $this->input->post();
        $this->printJson($this->ecn->activeRevision($data));
    }
    
    public function addVerification(){
        $id = $this->input->post('id');
        $this->data['id'] = $id;
        $this->load->view($this->verify_form,$this->data);
    }

    public function saveVerification(){
        $data = $this->input->post();
		$errorMessage = array();
		if(empty($data['status'])){
			$errorMessage['status'] = "Verification Status is required.";
		}
		if(empty($data['completion_date'])){
			$errorMessage['completion_date'] = "Completion Date is required.";
		}
		
		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
			$this->printJson($this->ecn->saveVerification($data));
		endif;
    }

    public function restartVerification(){
        $data = $this->input->post();
        $this->printJson($this->ecn->restartVerification($data));
    }
    
    public function checkPointView(){
        $data = $this->input->post();
        $id = $data['id'];
        $this->data['rev_id'] = $id;
        $this->data['view'] = $data['view'];
        $this->data['reviewData'] = $reviewData = $this->ecn->getCheckPointReview($id);
        $this->data['reviewData']->itemData = $this->ecn->getCheckPointReviewTrans($id);
        $this->data['revData'] = $this->ecn->getPrevRevisionData(['item_id'=>$reviewData->item_id,'rev_date'=>$reviewData->rev_date,'entry_type'=>$reviewData->entry_type]);
        $this->data['empData'] = $this->employee->getEmployee($this->loginId);
        $this->data['ext_qty'] = $this->store->getItemStockBatchWise(['item_id'=>$reviewData->item_id,'location_id'=>$this->RTD_STORE->id,'single_row'=>1]); 
        $this->data['wip_qty'] = $this->ecn->getWipQty(['item_id'=>$reviewData->item_id]);
        $this->data['rm_qty'] = $this->ecn->getRMQty(['item_id'=>$reviewData->item_id]); 
        if($data['view'] == 1){
            $this->data['deptReview'] = $this->ecn->getDeptReviewForPrint(['rev_id'=>$id]);
        }       
        $pdfData = $this->load->view('npd/ecn/approve_review',$this->data,true); 
        $this->printJson(['status'=>1,'pdfData'=>$pdfData]);    
    }

    public function approveCheckPoint(){
        $data = $this->input->post();
        if(empty($data['rev_id'])):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->ecn->approveCheckPoint($data));
		endif;
    }

    function printRevision($id){
        $this->data['reviewData'] = $reviewData = $this->ecn->getCheckPointReview($id);
        $this->data['reviewData']->itemData = $this->ecn->getCheckPointReviewTrans($id);
        $this->data['oldRevData'] = $this->ecn->getPrevRevisionData(['item_id'=>$reviewData->item_id,'rev_date'=>$reviewData->rev_date,'entry_type'=>$reviewData->entry_type]);
        $this->data['deptReview'] = $this->ecn->getDeptReviewForPrint(['rev_id'=>$id]);
     
		$logo=base_url('assets/images/logo.png');
		$this->data['letter_head']=base_url('assets/images/logo.png');
		
		$pdfData = $this->load->view('npd/ecn/print_revision',$this->data,true);
		$htmlHeader = '
        <table class="table item-list-bb">
            <tr>
                <td><img src="'.$this->data['letter_head'].'" class="img" style="width:13%;height:6%;"></td>
                <th style="font-size:18px;">Engineering Change Note Review Report (ECN)</th>
                <!--<th style="font-size:12px;">R-NPD-10 (00/01.10.17)</th>-->
            </tr>
        </table>';
        
		$mpdf = new \Mpdf\Mpdf();
		$pdfFileName='REVISION-'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetWatermarkImage($logo,0.03,array(120,45));
		$mpdf->showWatermarkImage = true;
		$mpdf->SetProtection(array('print'));
		
		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->SetHTMLFooter("");
		$mpdf->AddPage('P','','','','',5,5,22,5,5,5,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
	}

    public function saveFinalApprove(){
        $data = $this->input->post();
        if(empty($data['rev_id'])):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->ecn->saveFinalApprove($data));
		endif;
    }

    public function closeEcn(){
        $data = $this->input->post();
        $this->printJson($this->ecn->closeEcn($data));
    }
    /* ---------------------- End Revision Check Point ---------------------- */

    /*** Control Plan Revision */
    public function cpRevList($status = 1){
        $this->data['status'] = $status;
        $this->data['tableHeader'] = getSalesDtHeader("controlPlanRev");
        $this->load->view($this->cp_rev_index,$this->data);
    }

    public function getCpRevDTRows($status=0){  
        $data = $this->input->post(); $data['status'] = $status;
        $result = $this->ecn->getCpRevDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $row->status_label='';
            $row->controller = $this->data['headData']->controller;
           
            $sendData[] = getCpRevData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function changeCpRevStatus(){
        $data = $this->input->post();
        $this->printJson($this->ecn->changeCpRevStatus($data));
    }

    public function addCpRevision(){
        $data = $this->input->post();
        $this->data['ref_id'] = $data['id'];
        $this->data['item_id'] = $data['item_id'];
        $this->data['rev_no'] = $this->ecn->nextRevChNo(2,$data['item_id']);
        $this->load->view($this->cp_rev_form,$this->data);
    }

    public function saveCpRevision(){
        $data = $this->input->post();
		$errorMessage = array();
		if(empty($data['rev_no'])){ $errorMessage['rev_no'] = "Revisiopn No is required."; }
		if(empty($data['rev_date'])){ $errorMessage['rev_date'] = "Revision Date is required."; }
        if(empty($data['remark'])){ $errorMessage['remark'] = "Remark is required."; }
		
		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
            $data['status'] = 1;
            $data['created_by'] = $this->loginId;
			$this->printJson($this->ecn->saveCpRevision($data));
		endif;
    }
}
?>