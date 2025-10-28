<?php
class SampleInvoice extends MY_Controller{
    private $index = "sample_invoice/index";
    private $form = "sample_invoice/form";

    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Sample Invoice";
		$this->data['headData']->controller = "sampleInvoice";
		$this->data['headData']->pageUrl = "sampleInvoice";
	}
	
	public function index(){
		$this->data['tableHeader'] = getSalesDtHeader($this->data['headData']->controller);    
        $this->load->view($this->index,$this->data);
    }

    public function getDTRows($status = 0){
		$data = $this->input->post(); 
		$data['entry_type'] = 30;
		$data['status'] = $status;
        $result = $this->sampleInvoice->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getSampleInvoiceData($row);
        endforeach;        
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addSampleInvoice(){
        $this->data['customerData'] = $this->party->getCustomerList();
        $this->load->view($this->form,$this->data);
    }

    public function getPackingNoList(){
        $party_id = $this->input->post('party_id');
        $result = $this->sampleInvoice->getPackingNoList($party_id); 
        $packingList = '<option value="">Select Packing No.</option>';
        foreach($result as $row):
            $packingList .= '<option value="'.$row->trans_no.'">'.$row->trans_prefix.(sprintf("%04d",$row->trans_no)).' [Packing Date : '.formatDate($row->packing_date).']</option>';
        endforeach;
        $this->printJson(['status'=>1,'packingList'=>$packingList]);
    }

    public function getPackingItemList(){
        $packing_id = $this->input->post('packing_id');
        $result = $this->sampleInvoice->getPackingItemList($packing_id);//print_r($this->db->last_query());
        $this->printJson(['status'=>1,'itemList'=>$result]);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['party_id']))
            $errorMessage['party_id'] = "Party name is required.";
        
        if(empty($data['ref_id']))
            $errorMessage['ref_id'] = "Packing No. is required.";

        if(empty($data['extra_fields']['pre_carriage_by']))
            $errorMessage['pre_carriage_by'] = "Pre-Carriage by is required.";

        if(empty($data['extra_fields']['place_of_rec_by_pre_carrier']))
            $errorMessage['place_of_rec_by_pre_carrier'] = "Place of receipt by Pre-Carrier is required."; 
            
        if(empty($data['extra_fields']['vessel_flight']))
            $errorMessage['vessel_flight'] = "Vessel / Flight is required."; 

        if(empty($data['extra_fields']['port_of_loading']))
            $errorMessage['port_of_loading'] = "Port Of Loading is required."; 

        if(empty($data['extra_fields']['port_of_discharge']))
            $errorMessage['port_of_discharge'] = "Port Of Discharge is required."; 

        if(empty($data['extra_fields']['place_of_delivery']))
            $errorMessage['place_of_delivery'] = "Place Of Delivery is required.";
            
        if(empty($data['extra_fields']['country_of_final_destonation']))
            $errorMessage['country_of_final_destonation'] = "Country of Final Destination is required.";

        if(empty($data['item_data']))
            $errorMessage['item_name_error'] = "Item is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $itemData = $data['item_data'];
            unset($data['item_data']);

            $data['ref_by'] = $data['ref_id'];
            $data['doc_date'] = (!empty($data['doc_no']))?$data['doc_date']:NULL;
            $data['entry_type'] = 30;
            $data['created_by'] = $this->loginId;
            $data['extra_fields']['packing_trans_id'] = implode(",",array_column($itemData,'ref_id'));            

            $data['extra_fields']['no_of_wooden_box'] = 0;
            foreach($itemData as $row):
                if($row['taxable_amount'] > 0):
                    $data['extra_fields']['no_of_wooden_box'] += 1;
                endif;
            endforeach;

            $customerPOData = $this->commercialPacking->getCustomerPOData($data['extra_fields']['packing_trans_id']);
            $cust_po_no=array();$cust_po_date=array();$so_id=array();
            foreach($customerPOData as $row):
                if(!in_array($row->id,$so_id)):
                    $cust_po_no[] = $row->doc_no;
                    $cust_po_date[] = date("d-m-Y",strtotime($row->doc_date));
                    $so_id[] = $row->id;
                endif;
            endforeach;

            $data['extra_fields']['so_id'] = implode(",",$so_id);
            $data['extra_fields']['cust_po_no'] = implode(",",$cust_po_no);
            $data['extra_fields']['cust_po_date'] = implode(",",$cust_po_date);
            $data['extra_fields']['total_net_weight'] = array_sum(array_column($itemData,'drg_rev_no'));
            $data['extra_fields']['total_gross_weight'] = array_sum(array_column($itemData,'rev_no'));

            $data['extra_fields'] = json_encode($data['extra_fields']);
            $masterData = $data;
            $this->printJson($this->sampleInvoice->save($masterData,$itemData));
        endif;
    }

    public function edit($id){
        $this->data['customerData'] = $this->party->getCustomerList();
        $result = $this->sampleInvoice->getSampleInvoiceData($id);
        $packingData = $this->sampleInvoice->getPackingItemList($result->ref_id);
        $itemData = $result->itemData;
        unset($result->itemData);

        $dataRows = array();
        foreach($packingData as $row):
            if(in_array($row->id,array_column($itemData,'ref_id'))):
                $itemKey = array_search($row->id,array_column($itemData,'ref_id'));
                $row->id = $itemData[$itemKey]->id;
                $row->ref_id = $itemData[$itemKey]->ref_id;
                $row->hsn_code = $itemData[$itemKey]->hsn_code;
                $row->hsn_desc = $itemData[$itemKey]->hsn_desc;
                $row->drg_rev_no = $itemData[$itemKey]->drg_rev_no;
                $row->rev_no = $itemData[$itemKey]->rev_no;
                $row->price = $itemData[$itemKey]->price;
                $row->amount = $itemData[$itemKey]->amount;
                $row->taxable_amount = $itemData[$itemKey]->taxable_amount;
                $row->net_amount = $itemData[$itemKey]->net_amount;
            else:
                $row->ref_id = $row->id;
                $row->id = "";

                $row->drg_rev_no = $row->amount;
                $row->rev_no = $row->net_amount;

                $row->price = $row->item_price;
                $row->amount = round(($row->qty * $row->price),3);
                $row->taxable_amount = $row->net_amount = $row->amount;
            endif;
            $dataRows[] = $row;
        endforeach;
        $result->itemData = $dataRows;

        $packingListData = $this->sampleInvoice->getAllPackingNoList($result->party_id);
        $packingList = '<option value="">Select Packing No.</option>';
        foreach($packingListData as $row):
            if($row->comm_pack_id == 0 || $row->trans_no == $result->ref_id){
                $selected = ($row->trans_no == $result->ref_id)?"selected":"";
                $packingList .= '<option value="'.$row->trans_no.'" '.$selected.'>'.$row->trans_prefix.(sprintf("%04d",$row->trans_no)).' [Packing Date : '.formatDate($row->packing_date).']</option>';
            }            
        endforeach;

        $result->packing_no_list = $packingList;
        $this->data['dataRow'] = $result;
        $this->load->view($this->form,$this->data);
    }

    public function delete(){
		$id = $this->input->post('id');
		if(empty($id)):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->sampleInvoice->delete($id));
		endif;
	}

    public function sampleInvoicePdf($id){
		$result = $this->sampleInvoice->getSampleInvoiceData($id,1);
        $this->data['dataRow'] = $result;
        $this->data['companyInfo'] = $this->masterModel->getCompanyInfo();
        $this->data['partyData'] = $this->party->getParty($result->party_id);
        $this->data['packageNum'] = $this->commercialPacking->getCommercialInvItemsGroupByBox($id);

        $packingData = $this->packings->getExportData(['trans_no'=>$result->ref_id,'packing_type'=>1]);
        $packageData = $this->packings->packingTransGroupByPackage(['trans_no'=>$result->ref_id,'packing_type'=>1]);
        $dataArray=array();
        foreach($packageData as $row){
            $row->itemData=$this->packings->getExportDataForPrint(['trans_no'=>$result->ref_id,'packing_type'=>1,'package_no'=>$row->package_no]);
            $dataArray[]=$row;
        }
        $this->data['packingMasterData'] = $packingMasterData = $packingData[0];
        $this->data['packingData'] = $dataArray;

        $sign_img = base_url('assets/uploads/emp_sign/sign_'.(281).'.png');
        $this->data['authorise_sign'] = '<img src="'.$sign_img.'" style="width:100px;">'; 
		$pdfData = $this->load->view('sample_invoice/sample_invoice_pdf',$this->data,true);

        //print_r($pdfData);exit;
		
        $mpdf =  new \Mpdf\Mpdf();
        $pdfFileName=preg_replace('/[^A-Za-z0-9]/',"_",$result->trans_number).'.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
        $mpdf->WriteHTML($stylesheet,1);
        $mpdf->setTitle($result->trans_number);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->SetProtection(array('print'));
        
        $mpdf->SetWatermarkText('FREE SAMPLE, NO COMMERCIAL VALUE',0.20);
        $mpdf->showWatermarkText = true;
        
        $mpdf->AddPage('P','','','','',5,5,5,5,5,5,'','','','','','','','','','A4-P');
        $mpdf->WriteHTML($pdfData);
        $mpdf->Output($pdfFileName,'I');		
	}
}
?>