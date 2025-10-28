<?php
defined( 'BASEPATH' )OR exit( 'No direct script access allowed' );
class PurchaseOrder extends MY_Controller{
    private $indexPage = 'purchase_order/index';
    private $orderForm = "purchase_order/form";
    public function __construct(){
        parent::__construct();
        $this->isLoggedin();
		$this->data['headData']->pageTitle = "Purchase Orders";
		$this->data['headData']->controller = "purchaseOrder";
		$this->data['headData']->pageUrl = "purchaseOrder";
    }

    public function index(){
        $this->data['tableHeader'] = getPurchaseDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }
    
    public function getDTRows($status=0){
		$data = $this->input->post(); $data['status'] = $status;
        $result = $this->purchaseOrder->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;        
            $pq = $row->qty - $row->rec_qty;    
            $row->pending_qty = ($pq >=0 ) ? $pq : 0;
            
            if(empty($row->is_approve)):
				$row->order_status_label = '<span class="badge badge-pill badge-danger m-1">Unapproved</span>';
			else:
				$row->order_status_label = '<span class="badge badge-pill badge-success m-1">Approved</span><br>'.formatDate($row->approve_date);
			endif;
            if($row->order_status == 2):
				$row->order_status_label = '<span class="badge badge-pill badge-dark m-1">Short Close</span>';
			endif;
            
            $row->controller = "purchaseOrder";
			$sendData[] = getPurchaseOrderData($row);
        endforeach;        
        $result['data'] = $sendData;
        $this->printJson($result);
    }

	public function createOrder($id){
		$this->data['enquiryData'] = $this->purchaseEnquiry->getEnquiry($id);
		$this->data['partyData'] = $this->party->getPartyList();
        $this->data['itemData'] = $this->purchaseOrder->getItemLists("2,3,4,5,6,7");
        $this->data['itemTypeData'] = $this->item->getItemGroup();
        $this->data['unitData'] = $this->item->itemUnits();
		$this->data['po_prefix'] = 'PO/'.$this->shortYear.'/';
        $this->data['nextPoNo'] = $this->purchaseOrder->nextPoNo();
		$this->data['fgItemList'] = $this->purchaseOrder->getItemList(1);
		$this->data['terms'] = $this->terms->getTermsList();
        $this->load->view($this->orderForm,$this->data);
	}

    public function addPurchaseOrder(){
        $this->data['partyData'] = $this->party->getPartyList();
		$this->data['nextPoNo'] = $this->purchaseOrder->nextPoNo();
		$this->data['po_prefix'] = 'PO/'.$this->shortYear.'/';
        $this->data['itemData'] = '';
        $this->data['itemTypeData'] = $this->item->getItemGroup();
        $this->data['unitData'] = $this->item->itemUnits();
		$this->data['fgItemList'] = $this->purchaseOrder->getItemList(1);
		$this->data['terms'] = $this->terms->getTermsList();
        $this->load->view($this->orderForm,$this->data);
	}

    public function getItemData(){
		$id = $this->input->post('itemId');
        $result = $this->item->getItem($id);
        $result->unit_name = $this->item->itemUnit($result->unit_id)->unit_name;
		$this->printJson($result);
	}

	public function getItemListForSelect(){
		$item_type = $this->input->post('item_type');
		$item_id = $this->input->post('item_id'); 
        $result = $this->purchaseOrder->getItemListForSelect($item_type);
		$options="";
		if(!empty($result)): 
			$options .= '<option value="">Select Item Name</option>';
			foreach($result as $row):
				$selected = (!empty($item_id) && $item_id == $row->id)?'selected':'';
				$options .= "<option data-row='".json_encode($row)."' value='".$row->id."' ".$selected.">[".$row->item_code."] ".$row->item_name."</option>";
			endforeach;
		else:
			$options .= '<option value="">Select Item Name</option>';
		endif;
		
		$this->printJson(['status'=>1, 'options'=>$options]);
	}

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['party_id']))
            $errorMessage['party_id'] = "Party Name is required.";
        if(empty($data['po_no']))
            $errorMessage['po_no'] = 'PO. No. is required.';
        if(empty($data['item_id'][0]))
            $errorMessage['item_id'] = 'Item Name is required.';
		if(empty($data['term_id'][0]))
			$errorMessage['term_id'] = "Terms Conditions is required.";
			
		if(!empty($errorMessage)): 
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:	
			$data['terms_conditions'] = "";$termsArray = array();
			if(isset($data['term_id']) && !empty($data['term_id'])):
				foreach($data['term_id'] as $key=>$value):
					$termsArray[] = [
						'term_id' => $value,
						'term_title' => $data['term_title'][$key],
						'condition' => $data['condition'][$key]
					];
				endforeach;
				$data['terms_conditions'] = json_encode($termsArray);
			endif;
			
			if(!empty($data['freight_amt'])):
				$data['freight_gst'] = ($data['freight_amt'] * 0.18);
			endif;
			if(!empty($data['packing_charge'])):
				$data['packing_gst'] = ($data['packing_charge'] * 0.18);
			endif;


			$masterData = [ 
				'id' => $data['order_id'],
				'enq_id' => $data['enq_id'],
				'po_prefix'=>$data['po_prefix'], 
				'po_no'=>$data['po_no'], 
				'po_date' => date('Y-m-d',strtotime($data['po_date'])), 
				'gst_type' => $data['gst_type'], 
				'party_id' => $data['party_id'],			
				'quotation_no' => $data['quotation_no'],
				'quotation_date' => date('Y-m-d',strtotime($data['quotation_date'])),
				'reference_by' => $data['reference_by'],
				'destination' => $data['destination'],
				'amount' => $data['amount_total'],
				'freight_amt' => $data['freight_amt'],
				'freight_gst' => $data['freight_gst'],
				'packing_charge' => $data['packing_charge'],
				'packing_gst' => $data['packing_gst'],
				'igst_amt' => $data['igst_amt_total'], 
				'cgst_amt' => $data['cgst_amt_total'], 
				'sgst_amt' => $data['sgst_amt_total'], 
				'disc_amt' => $data['disc_amt_total'],
				'round_off' => $data['round_off'], 
				'net_amount' => $data['net_amount_total'],
				'remark' => $data['remark'],
				'terms_conditions' => $data['terms_conditions'],
                'created_by' => $this->session->userdata('loginId'),
				'req_id' => $data['req_id']
			];
							
			$itemData = [
				'id' => $data['trans_id'],
				'item_id' => $data['item_id'],
				'item_type' => $data['item_type'],
				'unit_id' => $data['unit_id'],
				'fgitem_id' => $data['fgitem_id'],
                'fgitem_name' => $data['fgitem_name'],
				'hsn_code' => $data['hsn_code'],
				'delivery_date' => $data['delivery_date'],
				'qty' => $data['qty'],
				'price' => $data['price'],
				'igst' => $data['igst'],
				'sgst' => $data['sgst'],
				'cgst' => $data['cgst'],
				'igst_amt' => $data['igst_amt'],
				'sgst_amt' => $data['sgst_amt'],
				'cgst_amt' => $data['cgst_amt'],
				'amount' => $data['amount'],
				'disc_per' => $data['disc_per'],
				'disc_amt' => $data['disc_amt'],
				'net_amount' => $data['net_amount'],
				'mill_tc' => $data['mill_tc'],
                'created_by' => $this->session->userdata('loginId')
			];
			$this->printJson($this->purchaseOrder->save($masterData,$itemData));
		endif;
    }

    public function edit($id){
        $this->data['partyData'] = $this->party->getPartyList();
		$this->data['itemData'] = $this->purchaseOrder->getItemLists("2,3,4,5,6,7");
        $this->data['itemTypeData'] = $this->item->getItemGroup();
        $this->data['unitData'] = $this->item->itemUnits();
        $this->data['dataRow'] = $this->purchaseOrder->getPurchaseOrder($id);
		$this->data['terms'] = $this->terms->getTermsList();
		$this->data['fgItemList'] = $this->purchaseOrder->getItemList(1);
        $this->load->view($this->orderForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
		if(empty($id)):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->purchaseOrder->deleteOrder($id));
		endif;
    }   

	/* NYN */
	public function addPOFromRequest($id){
		$this->data['req_id'] = $id;
        $this->data['partyData'] = $this->party->getPartyList();
		$this->data['nextPoNo'] = $this->purchaseOrder->nextPoNo();
		$this->data['po_prefix'] = 'PO/'.$this->shortYear.'/';
		$this->data['itemData'] = $this->purchaseOrder->getItemLists("2,3,4,5,6,7");
        $this->data['itemTypeData'] = $this->item->getItemGroup();
        $this->data['unitData'] = $this->item->itemUnits();
		$this->data['fgItemList'] = $this->purchaseOrder->getItemList(1);
		$this->data['terms'] = $this->terms->getTermsList();
        $this->data['reqItemData'] = $this->purchaseRequest->getPurchaseRequestForOrder($id);
        $this->load->view($this->orderForm,$this->data);
	}

	public function getPartyOrders(){
		$this->printJson($this->purchaseOrder->getPartyOrders($this->input->post('party_id')));
	}

	public function approvePOrder(){
		$data = $this->input->post();
		
		if(empty($data['id'])):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->purchaseOrder->approvePOrder($data));
		endif;
	}

    public function closePOrder(){
		$data = $this->input->post();
		
		if(empty($data['id'])):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->purchaseOrder->closePOrder($data));
		endif;
	}
	
    //Updated By NYN @13/12/2021
	function printPO($id,$type=0){
		$this->data['poData'] = $this->purchaseOrder->getPurchaseOrder($id);
		$this->data['partyData'] = $this->party->getParty($this->data['poData']->party_id);
		$this->data['companyData'] = $this->purchaseOrder->getCompanyInfo();
		$response="";
		$logo=base_url('assets/images/logo.png'); //$unapproved = base_url('assets/images/unapproved.jpg');
		$this->data['letter_head']=base_url('assets/images/letterhead_top.png');
		
		$this->data['poData']->taxableAmt = $this->data['poData']->amount - $this->data['poData']->freight_amt - $this->data['poData']->packing_charge;
		
		$pdfData = $this->load->view('purchase_order/printpo',$this->data,true);
		
		$poData = $this->data['poData'];
		$prepare = $this->employee->getEmp($poData->created_by);
		$prepareBy = (!empty($prepare->emp_name) ? $prepare->emp_name:'').' <br>('.formatDate($poData->created_at).')'; 
		$approveBy = ''; $prepareSign=''; $approveSign = '';
		if(!empty($poData->is_approve)){
			$approve = $this->employee->getEmp($poData->is_approve);
			$approveBy .= (!empty($prepare->emp_name) ? $approve->emp_name:'').' <br>('.formatDate($poData->approve_date).')'; 
			$sign_img = base_url('assets/uploads/emp_sign/sign_'.$poData->is_approve.'.png');
			$approveSign = '<img src="'.$sign_img.'" style="width:100px;">'; 
		}
		
		$htmlHeader = '<img src="'.$this->data['letter_head'].'" class="img">';
		$htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;border-bottom:1px solid #000000;">
						<tr>
							<td style="width:50%;" rowspan="4"></td>
							<th colspan="2">For, '.$this->data['companyData']->company_name.'</th>
						</tr>
						<tr>
							<td style="width:25%;" class="text-center">'.$prepareSign.'</td>
							<td style="width:25%;" class="text-center">'.$approveSign.'</td>
						</tr>
						<tr>
							<td style="width:25%;" class="text-center">'.$prepareBy.'</td>
							<td style="width:25%;" class="text-center">'.$approveBy.'</td>
						</tr>
						<tr>
							<td style="width:25%;" class="text-center"><b>Prepared By</b></td>
							<td style="width:25%;" class="text-center"><b>Authorised By</b></td>
						</tr>
					</table>
					<table class="table top-table" style="margin-top:10px;">
						<tr>
							<td style="width:25%;">PO No. & Date : '.$poData->po_prefix.$poData->po_no.'-'.formatDate($poData->po_date).'</td>
							<td style="width:25%;"></td>
							<td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
						</tr>
					</table>';
		
		$mpdf = new \Mpdf\Mpdf();
		$fileName = str_replace("/","_",getPrefixNumber($poData->po_prefix,$poData->po_no).'.pdf');
		$filePath = realpath(APPPATH . '../assets/uploads/purchase/');
		/*$stylesheet = file_get_contents(base_url('assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css'));
		$stylesheet = file_get_contents(base_url('assets/css/style.css?v='.time()));*/
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		//$mpdf->SetWatermarkImage($logo,0.03,array(120,60));
		if(!empty($poData->is_approve)){ $mpdf->SetWatermarkImage($logo,0.05,array(120,60)); }
		else{ $mpdf->SetWatermarkText('Not Approved Copy',0.1);$mpdf->showWatermarkText = true; }
		$mpdf->showWatermarkImage = true;
		$mpdf->SetProtection(array('print'));
		$mpdf->setTitle(str_replace("/","_",getPrefixNumber($poData->po_prefix,$poData->po_no)));
		
		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('P','','','','',5,5,40,60,5,5,5,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		if(empty($type)):
			$mpdf->Output($fileName,'I');
		else:
        	$mpdf->Output($filePath.'/'.$fileName, 'F');
			return $filePath.'/'.$fileName;
		endif;
	}
	
	public function getLastPrice(){
		$data=$this->input->post();
		$result=$this->purchaseReport->getLastPrice($data);
		$this->printJson($result);
	}
	public function getItemPriceLists(){
		$data=$this->input->post();
		$this->printJson($this->purchaseOrder->getFamilyItem($data['item_id'],$data['family_id']));
	}

	public function getItemList(){
        $this->printJson($this->purchaseOrder->getPurchaseItemList($this->input->post('order_id')));
    }
	
	// Created By meghavi 29-11-21
	public function purchaseOrderView(){
		$id=$this->input->post('id');
		$this->data['poData'] = $this->purchaseOrder->getPurchaseOrder($id);
		$this->data['partyData'] = $this->party->getParty($this->data['poData']->party_id);
		$this->data['companyData'] = $companyData = $this->purchaseOrder->getCompanyInfo();
		$response="";
		$logo=base_url('assets/images/logo.png'); $unapproved = base_url('assets/images/unapproved.jpg');
		$this->data['letter_head']=base_url('assets/images/letterhead_top.png');
		
		$this->data['poData']->taxableAmt = $this->data['poData']->amount - $this->data['poData']->freight_amt - $this->data['poData']->packing_charge;
		
		$pdfData = $this->load->view('purchase_order/printpo',$this->data,true);// print_r($pdfData);exit;
	
		$this->printJson(['status'=>1,'pdfData'=>$pdfData]); 
	}
	
	/*  Creted By : Avruti @7-12-2021 04:00 PM
        update by : 
        note : 
    */  
	public function createPurchaseOrder(){
		$data = $this->input->post(); 
		$this->data['req_id'] = implode(',',$data['pr_id']);
        $this->data['partyData'] = $this->party->getPartyList();
		$this->data['nextPoNo'] = $this->purchaseOrder->nextPoNo();
		$this->data['po_prefix'] = 'PO/'.$this->shortYear.'/';
		$this->data['itemData'] = $this->purchaseOrder->getItemLists("2,3,4,5,6,7");
		$this->data['itemTypeData'] = $this->item->getItemGroup();
        $this->data['unitData'] = $this->item->itemUnits();
		$this->data['fgItemList'] = $this->purchaseOrder->getItemList(1);
		$this->data['terms'] = $this->terms->getTermsList();
        $this->data['reqItemData'] = $this->purchaseRequest->createPurchaseOrder($data); //print_r($this->data['reqItemData']);exit;
        $this->load->view($this->orderForm,$this->data);
    }
    
    /* Created By Jp@01092022*/
    public function sendMail(){
		$postData = $this->input->post(); 
		$poData = $this->purchaseOrder->getPurchaseOrderById($postData['id']);
		if(!empty($poData->party_email) OR !empty($poData->contact_email)){
		    $contactEmail = (!empty($poData->contact_email)) ? $poData->contact_email : '';
		    $contactEmail = (!empty($poData->party_email) AND empty($contactEmail)) ? $poData->party_email : '';
		    
		    $attachment = $this->printPO($postData['id'],1);
            $ref_no = str_replace('_','/',$postData['ref_no']);
            
            $signData['sender_name'] = 'Hardik Thummar';
            $signData['sender_contact'] = '+91 99047 09700';
            $signData['sender_designation'] = 'PURCHASE MANAGER';
            $signData['sign_email'] = 'purchase@jayjalaramind.com';
            $contact_person = (!empty($poData->contact_person)) ? $poData->contact_person : 'Sir/Ma’am';
            
    		$emailSignature = $this->mails->getSignature($signData);
    
    		$mailData = array();
    		$mailData['sender_email'] = 'purchase@jayjalaramind.com';
    		//$mailData['receiver_email'] = 'jagdishpatelsoft@gmail.com';
    		$mailData['receiver_email'] = $contactEmail;
    		$mailData['cc_email'] = 'info@jayjalaramind.com';//'jagdishpatelsoft@gmail.com,rakeshkantaria74@gmail.com';
    		$mailData['mail_type'] = 6;
    		$mailData['ref_id'] = 0;
    		$mailData['ref_no'] = 0;
    		$mailData['created_by'] = $this->loginId;
    		$mailData['subject'] = 'Purchase Order';
    		$mail_body = '<div style="font-size:12pt;font-family: Bookman Old Style;">';
    		    $mail_body .= '<b>Dear '.$contact_person.',</b><br><br>';
    		    $mail_body .= 'Please find enclosed herewith our Purchase Order No.: <b>'.$ref_no.'</b><br><br>';
                $mail_body .= 'Kindly acknowledge receipt of an order and send us order acceptance/confirmation within a day.<br><br>';
                $mail_body .= 'Your prompt reply will highly be appreciated.<br><br>';
                $mail_body .= '<b>Notes: </b>';
                $mail_body .= '<ol style="list-style-type: decimal;">';
                    $mail_body .= '<li>You must have to deliver the materials as per delivery schedule mentioned in the order.</li>';
        		    $mail_body .= '<li>You must have to write our Purchase Order No. in the Invoice</li>';
        		    $mail_body .= '<li>You have to send an Invoice along with the materials only</li>';
    		    $mail_body .= '</ol>';
    		$mail_body .= '</div>';
    		$mail_body .= $emailSignature;
    		$mailData['mail_body'] = $mail_body;
    		//print_r($poData);exit;
    		$result = $this->mails->sendMail($mailData, [$attachment]);
    		unlink($attachment);
    		$this->printJson($result);
		}
		else
		{
		    $this->printJson(['status'=>0,'message'=>'Contact Email Not Found.']);
		}
	}
}
?>