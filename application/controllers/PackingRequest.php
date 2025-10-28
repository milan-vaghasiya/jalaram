<?php

class PackingRequest extends MY_Controller
{
    private $indexPage = "packing_request/index";
    private $packing_request_form = "packing_request/packing_request_form";
    
    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "PackingRequest";
		$this->data['headData']->controller = "packingRequest";
		$this->data['headData']->pageUrl = "packingRequest";
	}
	
	public function index(){
        $this->data['tableHeader'] = getSalesDtHeader($this->data['headData']->controller);
        $this->data['itemData'] = $this->item->getItemList(1);       
        $this->data['partyData'] = $this->party->getCustomerList();
        $this->load->view($this->indexPage,$this->data);
    }
    
    public function getDTRows($status=0,$party_id=0,$item_id=0){
		$data = $this->input->post(); $data['status'] = $status; 
        $data['party_id'] = $party_id;
        $data['item_id'] = $item_id;
		$result = $this->packingRequest->getRequestedRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getPackingRequestData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function getPackingRequset(){
        $this->data['trans_prefix'] = "PCKR/".$this->shortYear.'/';
        $this->data['nextTransNo'] = $this->packingRequest->nextTransNo();
        $this->data['partyData'] = $this->party->getCustomerList();
        $this->load->view($this->packing_request_form,$this->data);
    }

    public function save(){ 
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['trans_child_id']))
            $errorMessage['trans_child_id'] = "Sales Order is required.";
        if(empty($data['request_qty']))
            $errorMessage['request_qty'] = "Qty. is required.";
        if(empty($data['req_date']))
            $errorMessage['req_date'] = "Requested Date is required.";
        if(empty($data['delivery_date']))
            $errorMessage['delivery_date'] = "Delivery Date is required.";
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->packingRequest->savePackingRequest($data));
        endif;
    }

    public function editPackingRequset(){
        $id = $this->input->post('id');
        $this->data['trans_prefix'] = $this->transModel->getTransPrefix(23);
        $this->data['nextTransNo'] = $this->packingRequest->nextTransNo();
        $this->data['partyData'] = $this->party->getCustomerList();
        $this->data['dataRow'] = $this->packingRequest->getPackingReqData($id);
        $this->data['editData'] = $this->packingRequest->getRequestEditData($this->data['dataRow']->trans_no);
        $this->data['soData'] = '';
        $this->load->view($this->packing_request_form,$this->data);
    }

    public function getSOList(){
        $party_id = $this->input->post('party_id');
		$options="";
        
        if(!empty($party_id)):
            $soData = $this->packingRequest->getSalesOrderList($party_id);
            $options = '<option value="">Select Sales Order</option>';
            foreach($soData as $row):  
                $itmStock = $this->store->getItemCurrentStock($row->item_id,$this->PROD_STORE->id);
                $stock_qty = (!empty($itmStock->qty))?$itmStock->qty:0;      

                $options .= '<option value="'.$row->id.'" data-stock_qty = "'.$stock_qty.'" data-item_id="'.$row->item_id.'"   data-delivery_date="'.$row->delivery_date.'" data-item_code="'.$row->item_code.'" data-item_name="'.$row->item_name.'" data-item_alias="'.$row->item_alias.'" data-trans_main_id="'.$row->trans_main_id.'" data-pending_qty="'.($row->qty - $row->request_qty).'">'.getPrefixNumber($row->trans_prefix,$row->trans_no).' ['.$row->item_code.' | Pend. Qty:'.($row->qty - $row->request_qty).' | Del.Dt.:'.formatDate($row->delivery_date).']</option>';
            endforeach;
        endif;
        $this->printJson(['options'=>$options]);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->packingRequest->delete($id));
        endif;
    }

    public function getItemList(){
        $party_id = $this->input->post('party_id');
		$options="";
        if(!empty($party_id)):
            $soData = $this->packingRequest->getItemList($party_id);
            $options = '<option value="">Select Item Name</option>';
            foreach($soData as $row):        
                $selected = (!empty($party_id) && $party_id == $row->id)?"selected":"";
                $options .= '<option value="'.$row->id.'" '.$selected.'>['.$row->item_code.'] '.$row->item_name.'</option>';
            endforeach;
        endif;
        $this->printJson(['options'=>$options]);
    }
    
    // Created By Meghavi @26/12/22
    public function getItemData(){
        $this->printJson($this->packingRequest->getItemData($this->input->post('id'))); 
    }
}
?>