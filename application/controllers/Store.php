<?php
class Store extends MY_Controller
{
    private $indexPage = "store/index";
    private $storeForm = "store/form";
    
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Store";
		$this->data['headData']->controller = "store";
	}
	
	public function index(){
        $this->data['headData']->pageUrl = "store";
        $this->data['tableHeader'] = getStoreDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }
	
    public function getDTRows(){
        $result = $this->store->getDTRows($this->input->post());
        $sendData = array();$i=1;$count=0;
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;
            $sendData[] = getStoreData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addStoreLocation(){
        $this->data['storeNames'] = $this->store->getStoreNames();
        $this->load->view($this->storeForm, $this->data);
    }

    public function save(){
        $data = $this->input->post();
		$errorMessage = array();		
		if(empty($data['store_name']))
            if(empty($data['storename']))
			    $errorMessage['store_name'] = "Store Name is required.";
            else
            $data['store_name'] = $data['storename'];
        unset($data['storename']);
        if(empty($data['location']))
			$errorMessage['location'] = "Location is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->store->save($data));
        endif;
    }

    public function edit(){     
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->store->getStoreLocation($id);
        $this->data['storeNames'] = $this->store->getStoreNames();
        $this->load->view($this->storeForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->store->delete($id));
        endif;
    }

    public function items(){
        $this->data['headData']->pageUrl = "store/items";
        $this->data['tableHeader'] = getStoreDtHeader('storeItem');
        //$this->data['partyList'] = $this->party->getPartyListOnCategory(1);
        $this->load->view("store/item_list",$this->data);
    }
    
    public function getPartyList(){
        $partyList = $this->party->getPartyListOnCategory($this->input->post('party_category'));
        
        $options = '<option value="">Select All</option>';
        foreach($partyList as $row):
            $options .= '<option value="'.$row->id.'">'.$row->party_code.'</option>';
        endforeach;
        $this->printJson(['status'=>1,'options'=>$options]);
    }

    public function itemList($type=3,$party_id=0){
        $data = $this->input->post();$data['party_id'] = $party_id;
        $result = $this->item->getStockDTRows($data,$type);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++; $row->qty = 0;
            
            if(!empty($row->stock_qty)){ $row->qty = $row->stock_qty; }
            
            $sendData[] = getStoreItemData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function itemStockTransfer($item_id="",$item_type="",$location_id=""){
		$this->data['itemId'] = $item_id;
		$this->data['item_type'] = $item_type;
		$this->data['location_id'] = $location_id;
        $this->load->view('store/stock_transfer',$this->data);
    }

    public function getstockTransferData(){
        $data = $this->input->post();
        $data['ignore_scrap']=1; // Used when Scrap Stock Not Required
        $result = $this->store->getItemWiseStock($data);
        $this->printJson($result);
    }

    public function stockTransfer(){
        $this->data['dataRow'] = $this->input->post();
        $this->data['locationData'] = $this->store->getStoreLocationListWithoutProcess();
        $this->load->view('store/stock_transfer_form',$this->data);
    }

    public function saveStockTransfer(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['to_location_id']))
            $errorMessage['to_location_id'] = "Store Location is required.";
        if(empty($data['transfer_qty']))
            $errorMessage['transfer_qty'] = "Qty is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $checkStock = $this->store->checkBatchWiseStock($data);
            if($checkStock->qty < $data['transfer_qty']):
                $this->printJson(['status'=>2,'message'=>'Stock not avalible.','stock_qty'=>$checkStock->qty]);
            else:
                $data['created_by'] = $this->session->userdata('loginId');
                $this->printJson($this->store->saveStockTransfer($data));
            endif;
        endif;
    }
    
    public function rmStock(){
        $this->data['headData']->pageUrl = "store";
        $this->data['tableHeader'] = getStoreDtHeader('rmStock');
        $this->load->view("store/rm_stock",$this->data);
    }
    
    public function rmStockDTRows($type){
        $data = $this->input->post();
        $data['item_type']=$type;
        $result = $this->store->getStockDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getStoreRMItemData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    /* Use Only Delivery Challan, Sales Invoice and Credit Note*/
    public function batchWiseItemStock(){
		$data = $this->input->post();
        $result = $this->store->batchWiseItemStock($data);
        $this->printJson($result);
	}
	
	public function itemTransfer(){
        $this->data['dataRow'] = $this->input->post();
        $this->data['itemList'] = $this->item->getItemLists(1);
        $this->load->view('store/item_transfer_form',$this->data);
    }

    public function saveItemTransfer(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['new_item_id']))
            $errorMessage['new_item_id'] = "Item is required.";
        if(empty($data['transfer_qty']))
            $errorMessage['transfer_qty'] = "Qty is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $checkStock = $this->store->checkBatchWiseStock($data);
            if($checkStock->qty < $data['transfer_qty']):
                $this->printJson(['status'=>2,'message'=>'Stock not avalible.','stock_qty'=>$checkStock->qty]);
            else:
                $data['created_by'] = $this->session->userdata('loginId');
                $this->printJson($this->store->saveItemTransfer($data));
            endif;
        endif;
    }
    
    public function fgLedger(){
        $this->data['headData']->pageUrl = "store/fgLedger";
        $this->data['tableHeader'] = getStoreDtHeader('fgLedger');
        $this->data['partyList'] = $this->party->getCustomerList();
        $this->load->view("store/fg_ledger",$this->data);
    }
    
    public function getfgLedger($type, $party_id='', $stock_type=''){
        $data = $this->input->post(); 
        $data['party_id'] = $party_id; 
        $data['stock_type'] = $stock_type;
        $result = $this->item->getFGStockDTRows($data, $type);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++; $row->rtd_qty = 0; $row->par_qty = 0;
            
            $row->rtd_location = $this->RTD_STORE->id;
            $row->prod_location = $this->PROD_STORE->id;
            
            $itmStock = $this->store->getFGStockLedger($row->id,$row->item_type);
            if(!empty($itmStock->rtd_qty)){ $row->rtd_qty = $itmStock->rtd_qty; }
            if(!empty($itmStock->par_qty)){ $row->par_qty = $itmStock->par_qty; }
            
            $row->stock_qty = (!empty($row->stock_qty) AND $row->stock_qty > 0) ? $row->stock_qty : 0;
            
            $sendData[] = getfgLedgerData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }
}