<?php
class npdJobcard extends MY_Controller{

    private $indexPage = "production_v3/npd_job/index";
    private $formPage = "production_v3/npd_job/form";
    private $jobDetail = "production_v3/npd_job/jobcard_detail";
    private $storeLocation = "production_v3/npd_job/store_location";
    private $requestForm = "job_material_dispatch/purchase_request";

    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "NPD Jobcard";
		$this->data['headData']->controller = "production_v3/npdJobcard";
	}
	
	public function index(){
        $this->data['headData']->pageUrl = "production_v3/npdJobcard";
        $this->data['tableHeader'] = getProductionHeader("npdJobcard");
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status=0){
        $data = $this->input->post(); $data['status'] = $status;
        $result = $this->npdJobcard->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getNpdJobcardData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addJobcard(){
        $this->data['jobPrefix'] = "NJC/".$this->shortYear.'/';
        $this->data['jobNo'] = $this->jobcard_v3->getNextJobNo(2);
        $this->data['productData'] = $this->item->getItemList(1);
        $this->data['rmList'] = $this->item->getItemList(3);
        $this->load->view($this->formPage,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['product_id'])){ $errorMessage['product_id'] = "Product is required.";}
        if(empty($data['rm_item_id'])){ $errorMessage['rm_item_id'] = "Raw Material is required.";}
        if(empty($data['rm_req_qty'])){ $errorMessage['rm_req_qty'] = "Request Qty required.";}
		if(empty($data['qty']) || $data['qty'] == "0.000") {$errorMessage['qty'] = "Quantity is required.";}
		if(empty($data['job_date'])):
			$errorMessage['job_date'] = "Date is required.";
		else:
		    if(($data['job_date'] < $this->startYearDate) OR ($data['job_date'] > $this->endYearDate) )
			    $errorMessage['job_date'] = "Invalid Date";
		endif;

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->npdJobcard->save($data));
        endif;
    }

    public function edit(){
        $id = $this->input->post('id');
        
        $this->data['jobPrefix'] = "NJC/".$this->shortYear.'/';
        $this->data['jobNo'] = $this->jobcard_v3->getNextJobNo(2);
        $this->data['dataRow'] = $this->jobcard_v3->getJobCard($id);
        $this->data['productData'] = $this->item->getItemList(1);
        $this->data['rmList'] = $this->item->getItemList(3);
        $this->load->view($this->formPage,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->npdJobcard->delete($id));
        endif;
    }

    public function view($id){
        $this->data['headData']->pageUrl = "production_v3/npdJobcard";
        $jobCardData = $this->jobcard_v3->getJobCard($id);
        $this->data['dataRow'] = $jobCardData;
		$this->data['processList'] = $this->process->getProcessList();
        $this->data['machineList'] = $this->item->getItemList(5);
        $this->data['operatorList'] = $this->employee->getMachineOperatorList();
        $this->data['logData'] = $this->npdJobcard->getNpdJobTransDetail($id);
        $this->data['reqMaterials'] = $this->jobMaterial->getJobMaterialIssueNpd($jobCardData);
        $this->load->view($this->jobDetail,$this->data);
    }

    public function saveLogDetail(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['entry_date']))
            $errorMessage['entry_date'] = "Date is required.";
        if(empty($data['process_id']))
            $errorMessage['process_id'] = "Proces is required.";
        /*if(empty($data['machine_id']))
            $errorMessage['machine_id'] = "Machine is required.";
        if(empty($data['operator_id']))
            $errorMessage['operator_id'] = "Operator is required.";
        if(empty($data['cycle_time']))
            $errorMessage['cycle_time'] = "Cycle Time is required.";
        if(empty($data['production_time']))
            $errorMessage['production_time'] = "Production Time is required.";*/
        if(empty($data['ok_qty']) && empty($data['rejection_qty']))
            $errorMessage['ok_qty'] = "Ok Qty OR Rejection Qty is required.";     

        $jobCardData = $this->jobcard_v3->getJobCard($data['job_card_id']);
        $pendingQty = ($jobCardData->qty - $jobCardData->total_reject_qty);
        $okQty  = (!empty($data['ok_qty']) ? $data['ok_qty'] : 0 );
        $rejectQty = (!empty($data['rejection_qty']) ? $data['rejection_qty'] : 0 );
      
        if(($okQty + $rejectQty) > $pendingQty){
            $errorMessage['ok_qty'] = "Invalid Qty.";
        }

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->npdJobcard->saveLogDetail($data);

            $logData = $this->npdJobcard->getNpdJobTransDetail($data['job_card_id']);
            $tbodyData="";$i=1; 
            if(!empty($logData)):
                $i=1;
                foreach($logData as $row):
                    $tbodyData.= '<tr>
                                <td class="text-center">'.$i++.'</td>
                                <td class="text-center">'.formatdate($row->entry_date).'</td>
                                <td class="text-center">'.$row->process_name.'</td>
                                <td class="text-center">'.$row->item_name.'</td>
                                <td class="text-center">'.$row->emp_name.'</td>
                                <td class="text-center">'.$row->cycle_time.'</td>
                                <td class="text-center">'.$row->production_time.'</td>
                                <td class="text-center">'.$row->ok_qty.'</td>
                                <td class="text-center">'.$row->rejection_qty.'</td>
                                <td class="text-center">'.$row->remark.'</td>
                                <td class="text-center">
                                    <button type="button" onclick="trashLogdetail('.$row->id.','.$row->job_card_id.');" class="btn btn-outline-danger waves-effect waves-light btn-delete permission-remove"><i class="ti-trash"></i></button>
                                </td>
                            </tr>';
                endforeach;
            else:
                $tbodyData.= '<tr><td colspan="11" style="text-align:center;">No Data Found</td></tr>';
            endif;
            $this->printJson(['status'=>1,"tbodyData"=>$tbodyData]);
        endif;
    }

    public function deleteLogTransdetail(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:            
            $this->npdJobcard->deleteLogTransdetail($data);
            $logData = $this->npdJobcard->getNpdJobTransDetail($data['job_card_id']);

            $tbodyData="";$i=1; 
            if(!empty($logData)):
                $i=1;
                foreach($logData as $row):
                    $tbodyData.= '<tr>
                                <td class="text-center">'.$i++.'</td>
                                <td class="text-center">'.formatdate($row->entry_date).'</td>
                                <td class="text-center">'.$row->process_name.'</td>
                                <td class="text-center">'.$row->item_name.'</td>
                                <td class="text-center">'.$row->emp_name.'</td>
                                <td class="text-center">'.$row->cycle_time.'</td>
                                <td class="text-center">'.$row->production_time.'</td>
                                <td class="text-center">'.$row->ok_qty.'</td>
                                <td class="text-center">'.$row->rejection_qty.'</td>
                                <td class="text-center">'.$row->remark.'</td>
                                <td class="text-center">
                                    <button type="button" onclick="trashLogdetail('.$row->id.','.$row->job_card_id.');" class="btn btn-outline-danger waves-effect waves-light btn-delete permission-remove"><i class="ti-trash"></i></button>
                                </td>
                            </tr>';
                endforeach;
            else:
                $tbodyData.= '<tr><td colspan="11" style="text-align:center;">No Data Found</td></tr>';
            endif;
            $this->printJson(['status'=>1,"tbodyData"=>$tbodyData]);
        endif;
    }

    public function storeLocation(){
        $id = $this->input->post('id');
        $jobcardData = $this->jobcard_v3->getJobCard($id);
        $this->data['job_id'] = $id;
        $this->data['jobNo'] = getPrefixNumber($jobcardData->job_prefix, $jobcardData->job_no);
        $this->data['pending_qty'] = ($jobcardData->qty - ($jobcardData->total_reject_qty + $jobcardData->total_out_qty));
        $this->data['product_name'] = $this->item->getItem($jobcardData->product_id)->item_code;
        $this->data['transactionData'] = $this->npdJobcard->getStoreLocationTrans($id);
        $this->load->view($this->storeLocation, $this->data);
    }

    public function saveStoreLocation(){
        $data = $this->input->post();
        $errorMessage = array();
        $jobcardData = $this->jobcard_v3->getJobCard($data['job_id']);
        $pendingQty = ($jobcardData->qty - ($jobcardData->total_reject_qty + $jobcardData->total_out_qty));
        if (empty($data['location_id']))
            $errorMessage['location_id'] = "Store Location is required.";
        if (!empty($data['qty']) && $data['qty'] != "0.000") :
            if ($data['qty'] > $pendingQty) :
                $errorMessage['qty'] = "Invalid Qty.";
            endif;
        else :
            $errorMessage['qty'] = "Qty is required.";
        endif;
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $data['trans_date'] = formatDate($data['trans_date'], 'Y-m-d');
            $data['product_id'] = $jobcardData->product_id;
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->npdJobcard->saveStoreLocation($data));
        endif;
    }

    public function deleteStoreLocationTrans(){
        $id = $this->input->post('id');
        if (empty($id)) :
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else :
            $this->printJson($this->npdJobcard->deleteStoreLocationTrans($id));
        endif;
    }

    public function changeNpdJobStatus(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->npdJobcard->changeNpdJobStatus($data));
        endif;
    }
    
    public function addPurchaseRequest(){
        $this->data['itemData'] = $this->item->getItemLists('2,3');
        $this->data['nextReqNo'] = $this->jobMaretialRequest->nextReqNo();
        $this->data['fgData'] = $this->item->getItemLists(1);
        $this->load->view($this->requestForm,$this->data);
    }

    public function savePurchaseRequest(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['req_item_id'][0]))
            $errorMessage['req_item_id'] = "Item Name is required.";
        if(empty($data['req_date']))
            $errorMessage['req_date'] = "Request Date is required.";
        if(empty($data['req_qty'][0]))
            $errorMessage['req_qty'] = "Request Qty. is required.";
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $itemData = [
				'id' => $data['id'],
				'req_item_id' => $data['req_item_id'],
				'req_qty' => $data['req_qty'],
				'req_item_name' => $data['req_item_name']
			];
            unset($data['req_item_id'], $data['req_item_name'], $data['req_qty']);
            $this->printJson($this->jobMaterial->savePurchaseRequest($data,$itemData));
        endif;
    }
}
?>