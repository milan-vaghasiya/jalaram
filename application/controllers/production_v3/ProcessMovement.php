<?php

class ProcessMovement extends MY_Controller
{
    private $approvalForm = "production_v3/process_movement/form";
    private $storeLocation = "production_v3/process_movement/store_location";
    private $stockApprovalIndex = "production_v3/process_movement/stock_approval"; //26-09-2024

    public function __construct(){
        parent::__construct();
        $this->isLoggedin();
        $this->data['headData']->pageTitle = "Process Movement";
        $this->data['headData']->controller = "production_v3/processMovement";
        $this->data['headData']->pageUrl = "production_v3/processMovement";
    }

    public function processMovement(){
        $data=$this->input->post();
        $id =  $data['id'];
        $outwardData = $this->processMovement->getApprovalData(['id'=>$id]);
        $outwardData->minDate = (!empty($outwardData->entry_date)) ? $outwardData->entry_date : $outwardData->job_date;
        
        if(!empty($outwardData->in_process_id)){
            $outwardData->pqty = $outwardData->total_ok_qty - $outwardData->out_qty;
        }else{
            $outwardData->pqty = $outwardData->in_qty - $outwardData->out_qty;

        }
        $this->data['dataRow'] = $outwardData;
        $this->data['outwardTrans'] = $this->productionMovementHtml($data['id']);
        $this->load->view($this->approvalForm, $this->data);
    }

    /* Save Outward Trans */
    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if (empty($data['job_card_id']))
            $errorMessage['job_card_id'] = "Job Card No. is required.";
        if (empty($data['product_id']))
            $errorMessage['product_id'] = "Product Name is required.";
       
        if (empty($data['out_qty']))
            $errorMessage['out_qty'] = "Out Qty. is required.";

        if (!empty($data['out_qty']) && $data['out_qty'] < 0)
            $errorMessage['out_qty'] = "Out Qty. is invalid.";

        if (empty($data['entry_date']) or $data['entry_date'] == null or $data['entry_date'] == "") :
            $errorMessage['entry_date'] = "Date is required.";
        endif;
        
        $outwardData = $this->processMovement->getApprovalData(['id'=>$data['ref_id']]);
        if (!empty($data['out_qty']) ):
           
            if(!empty($data['in_process_id'])){
                $pendingQty =$outwardData->total_ok_qty - $outwardData->out_qty; //$outwardData->in_qty - ($outwardData->out_qty + $rejectionQty);    
            }    else{
                $pendingQty =$outwardData->in_qty - ($outwardData->out_qty);    
            } 

            if($pendingQty < $data['out_qty']):
                $errorMessage['out_qty'] = "Qty not available.";
            endif;
        endif;

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $data['created_by'] = $this->session->userdata('loginId');
            $result = $this->processMovement->save($data);
            $result['outwardTrans'] = $this->productionMovementHtml($data['ref_id']);
            $this->printJson($result);
        endif;
    }

    public function productionMovementHtml($job_approval_id){
        $transaData = $this->processMovement->getOutwardTrans($job_approval_id);
        $html = '';$i=1;
        if(!empty($transaData)){
            foreach($transaData as $row){
                $printBtn = '<a href="'.base_url('production_v3/jobcard/printProcessIdentification/'.$row->id).'" target="_blank" class="btn btn-sm btn-outline-info waves-effect waves-light mr-2" title="Print"><i class="fas fa-print"></i></a>';
                $deleteBtn = '<button type="button" onclick="trashOutward(' . $row->id . ');" class="btn btn-sm btn-outline-danger waves-effect waves-light"><i class="ti-trash"></i></button>';
                $html .= '<tr>
                            <td>' . $i++ . '</td>
                            <td>' . formatDate($row->log_date) . '</td>
                            <td>' . (!empty($row->send_to)?'Vendor':'Inhouse') . '</td>
                            <td>' . $row->production_qty . '</td>
                            <td>' . $row->remark . '</td>
                            <td class="text-center" style="width:10%;">'.$printBtn.$deleteBtn.'</td>
                        </tr>';
            }
        }else{
            $html = '<tr> <td colspan="6"> No data available</td> </tr>';
        }
        return $html;
    }

    /* Delete Outward Trans */
    public function delete(){
        $id = $this->input->post('id');
        if (empty($id)) :
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else :
            $result = $this->processMovement->delete($id);
            $result['outwardTrans'] = $this->productionMovementHtml($result['job_approval_id']);
            $this->printJson($result);
        endif;
    }

	/* Store Location */
	public function storeLocation()
    {
        $id = $this->input->post('id');
        $transid = $this->input->post('transid');
        $jobcardData = $this->jobcard_v3->getJobCard($id);
        $outwardData = $this->processMovement->getOutward($transid);
        $outwardData->minDate = (!empty($outwardData->entry_date)) ? $outwardData->entry_date : $outwardData->job_date;
        $this->data['dataRow'] = $outwardData;

        $this->data['job_id'] = $id;
        $this->data['ref_id'] = $transid;
        $this->data['jobNo'] = getPrefixNumber($jobcardData->job_prefix, $jobcardData->job_no);
        $this->data['qty'] = $jobcardData->unstored_qty;
        $this->data['pending_qty'] = $jobcardData->unstored_qty;
        $this->data['product_name'] = $this->item->getItem($jobcardData->product_id)->item_code;
        $this->data['locationData'] = $this->store->getStoreLocationList();
        $this->data['transactionData'] = $this->processMovement->getStoreLocationTrans($id);
        $this->load->view($this->storeLocation, $this->data);
    }

    public function saveStoreLocation()
    {
        $data = $this->input->post();
        $errorMessage = array();
        $jobcardData = $this->jobcard_v3->getJobCard($data['job_id']);
        if (empty($data['location_id']))
            $errorMessage['location_id'] = "Store Location is required.";
        if (!empty($data['qty']) && $data['qty'] != "0.000") :
            if ($data['qty'] > $jobcardData->unstored_qty) :
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
            $this->printJson($this->processMovement->saveStoreLocation($data));
        endif;
    }

    public function deleteStoreLocationTrans()
    {
        $id = $this->input->post('id');
        if (empty($id)) :
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else :
            $this->printJson($this->processMovement->deleteStoreLocationTrans($id));
        endif;
    }

    //26-09-2024
    public function stockApproval(){
        $this->data['headData']->pageUrl = "production_v3/processMovement/stockApproval";
        $this->data['headData']->pageTitle = "Production Log";   
        $this->data['tableHeader'] = getProductionHeader('stockApproval');
        $this->load->view($this->stockApprovalIndex,$this->data);
    }

    //26-9-2024
    public function getStockApprovalDtRows(){
        $data = $this->input->post(); 
        $result = $this->processMovement->getStockApprovalDtRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getStockApprovalData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function approveStockEntry()
    {
        $data = $this->input->post();
        if (empty($data['id'])) :
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else :
            $this->printJson($this->processMovement->approveStockEntry($data));
        endif;
    }
}
