<?php
defined( 'BASEPATH' )OR exit( 'No direct script access allowed' );
class Ewaybill extends MY_Controller{
	private $page = "eway-bill";

	//EwayBill/Einvoice AuthData
	private $authData = [
		'fromGst' => '24AAPFJ1952P1ZS',
		'euser' => 'JAYJALARAM_API_JJL',
		'epass' => 'Rj0206@t'
	]; 
	
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "E-Way Bill";
        $this->data['headData']->controller = "ewaybill";
        $this->load->helper('file');
	}
	
	public function index(){
        $this->data['rows'] = $this->eway->getEwayBills();
		$this->load->view($this->page,$this->data);
    }

    public function loadEWBForm(){
		$id = $this->input->post('id');
		$this->data['type'] = "EWB";
		$this->data['doc_type'] = "INV";
		$this->data['invoiceData'] = $this->eway->loadFormData($id);
		$this->data['transportData'] = $this->transport->getTransportList();
		$this->load->view('eway_bill/ewb_form',$this->data);
    }

	public function loadJCEWBForm(){
		$id = $this->input->post('id');
		$this->data['type'] = "JCEWB";
		$this->data['doc_type'] = "CHL";
		$this->data['invoiceData'] = $this->eway->loadJCEWBFormData($id);
		$this->data['transportData'] = $this->transport->getTransportList();
		$this->load->view('eway_bill/ewb_form',$this->data);
	}

	public function loadEinvForm(){
		$id = $this->input->post('id');
		$this->data['invoiceData'] = $this->eway->loadFormData($id);
		$this->data['transportData'] = $this->transport->getTransportList();
		$this->load->view('eway_bill/einv_form',$this->data);
	}

	public function vehicleSearch(){
		$this->printJson($this->eway->vehicleSearch());
	}      

    public function generateNewEwb(){
        $data = $this->input->post();  
        $errorMessage = array();
        if(empty($data['doc_type']))
            $errorMessage['doc_type'] = "Document Type is required.";
        if(empty($data['supply_type']))
            $errorMessage['supply_type'] = "Supply Type is required.";
        if(empty($data['sub_supply_type']))
            $errorMessage['sub_supply_type'] = "Sub Supply Type is required.";
        if(empty($data['trans_mode']) && !empty($data['vehicle_no']))
            $errorMessage['trans_mode'] = "Transport Mode is required.";
        if(empty($data['trans_distance']))
            $errorMessage['trans_distance'] = "Trans. Distance is required.";
        if(empty($data['transport_id']) && empty($data['vehicle_no']))
            $errorMessage['vehicle_no'] = "Vehicle no. is required.";
        if(!isset($data['ref_id']))
            $errorMessage['ref_id'] = "Sales ID not found";
        if(empty($data['ship_pincode']))
            $errorMessage['ship_pincode'] = "Shipping Pincode is required.";
        

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else: 
            $authToken = $this->eway->getEwbAuthToken($this->authData);
            if($authToken['status'] != 1):
                $this->printJson($authToken);
            else:
                $storeEwbData = $this->eway->save($data);

                $this->authData['token'] = $authToken['token'];
                $postData['ewbJson'] = $storeEwbData['data'];
                $postData['doc_type'] = $data['doc_type'];
                $postData['ref_id'] = $data['ref_id'];
                $postData['ewb_id'] = $storeEwbData['id'];
                $this->printJson($this->eway->generateEwayBill($postData,$this->authData));
            endif;
        endif;        
    }

	public function generateNewEinv(){
		$data = $this->input->post();
		$errorMessage = array();

		if(empty($data['ref_id']))
			$errorMessage['general_error'] = "Somthing is wrong.";
		if(!empty($data['ewb_status']) && empty($data['trans_distance']))
			$errorMessage['trans_distance'] = "Trans. Distance is required.";

		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else: 
			$authToken = $this->eway->getEwbAuthToken($this->authData);
            if($authToken['status'] != 1):
                $this->printJson($authToken);
            else:
                $storeEwbData = $this->eway->save($data);

				if($storeEwbData['status'] == 2):
					$this->printJson($storeEwbData);
				else:
					$this->authData['token'] = $authToken['token'];
					$postData['einvData'] = $storeEwbData['data'];
					$postData['doc_type'] = $data['doc_type'];
					$postData['ref_id'] = $data['ref_id'];
					$postData['einv_id'] = $storeEwbData['id'];
					$this->printJson($this->eway->generateEinvoice($postData,$this->authData));
				endif;
            endif;
		endif;
	}

	public function ewb_pdf($eway_bill_no){
        $ewbData = $this->db->where('eway_bill_no',$eway_bill_no)->where('is_delete',0)->get('eway_bill_master')->row();
		if(!empty($ewbData))
		{
			$inrSymbol=base_url('assets/images/inr.png');
			$ewbJson = json_decode($ewbData->json_data);
			$supplyType = ($ewbJson->supplyType=='O') ? 'Outward' : 'Inward';
			$subSupplyType = Array('','Supply','Import','Export','Job Work','For Own Use','Job Work Returns','Sales Returns','Others','SKD/CKD','Line Sales','Recipient Not Known','Exhibition or Fairs');
			$transMode = Array('','Road','Rail','Air','Ship');
			$transactionType = ['1'=>'Regular','2'=>'Bill To - Ship To','3'=>'Bill From - Dispatch From','4'=>'Combination of 2 and 3'];
			$reasonForTransportation = $supplyType .'-'.$subSupplyType[(int)$ewbJson->subSupplyType];
			$vehicle = (!empty($ewbJson->vehicleNo)) ? $ewbJson->vehicleNo : $ewbJson->transporterId ;
			$qrText = 'EWB No.: '.$ewbData->eway_bill_no.', From:'.$ewbJson->fromGstin.', Valid Untill: '.date("d/m/Y h:i:s A",strtotime($ewbData->valid_up_to));
			$ewbQrCode = $this->getQRCode($qrText,'assets/uploads/eway_bill/qr_code/',$ewbData->eway_bill_no);
			$ewbQrImg = (!empty($ewbQrCode))?'<img src="'.base_url($ewbQrCode).'" width="130">':'';

			$totalItams = ((count($ewbJson->itemList) - 1) > 0) ? ' (+'.(count($ewbJson->itemList) - 1). ' Items)' : '' ;
			$ewbHtml = '<div class="barcode text-center">'.$ewbQrImg.'</div>
						<table class="table ewbTable">
							<tr><th class="ewbTitle bg-light"colspan="3">PRINT E-WAY BILL</th></tr>
							<tr><th style="width:35%;">E-Way Bill No: </th><td style="width:80px;">&nbsp;</td><td>'.$ewbData->eway_bill_no.'</td></tr>
							<tr><th>E-Way Bill Date: </th><td>&nbsp;</td><td>'.date("d/m/Y h:i:s A",strtotime($ewbData->eway_bill_date)).'</td></tr>
							<tr><th>Generated By: </th><td>&nbsp;</td><td>'.$ewbJson->fromGstin.'-'.$ewbJson->fromTrdName.'</td></tr>
							<tr><th>Valid From: </th><td>&nbsp;</td><td>'.date("d/m/Y h:i:s A",strtotime($ewbData->eway_bill_date)).' ['.$ewbJson->transDistance.'Kms]</td></tr>
							<tr><th>Valid Until: </th><td>&nbsp;</td><td>'.date("d/m/Y h:i:s A",strtotime($ewbData->valid_up_to)).'</td></tr>
							
							<tr><th class="ewbTitle bg-light"colspan="3">PART – A</th></tr>
							<tr><th>GSTIN of Supplier </th><td>&nbsp;</td><td>'.$ewbJson->fromGstin.'-'.$ewbJson->fromTrdName.'</td></tr>
							<tr><th>Place of Dispatch </th><td>&nbsp;</td><td>'.$ewbJson->fromPlace.'-'.$ewbJson->fromPincode.'</td></tr>
							<tr><th>GSTIN of Recipient </th><td>&nbsp;</td><td>'.$ewbJson->toGstin.'-'.$ewbJson->toTrdName.'</td></tr>
							<tr><th>Place of Delivery </th><td>&nbsp;</td><td>'.$ewbJson->toPlace.'-'.$ewbJson->toPincode.'</td></tr>
							<tr><th>Document No. </th><td>&nbsp;</td><td>'.$ewbJson->docNo.'</td></tr>
							<tr><th>Document Date </th><td>&nbsp;</td><td>'.$ewbJson->docDate.'</td></tr>
							<tr><th>Value of Goods </th><td>&nbsp;</td><td><img src="'.$inrSymbol.'" width="10"> '.$ewbJson->totInvValue.'</td></tr>
							<tr><th>Transaction Type </th><td>&nbsp;</td><td>'.$transactionType[$ewbJson->transactionType].'</td></tr>
							<tr><th>HSN Code </th><td>&nbsp;</td><td>'.$ewbJson->mainHsnCode.$totalItams.'</td></tr>
							<tr><th>Reason for Transportation </th><td>&nbsp;</td><td>'.$reasonForTransportation.'</td></tr>
							<tr><th>Transporter </th><td>&nbsp;</td><td>'.$ewbJson->transporterId.'</td></tr>
							
							<tr><th class="ewbTitle bg-light"colspan="3">PART – B</th></tr>
						</table>
						<table class="table ewbBottomTable">
							<tr>
								<th>Mode</th><th>Vehicle / Trans<br>Doc No & Dt</th><th>From</th><th>Entered Date</th>
								<th>Entered By</th><th>CEWB No. (If any)</th><th>Multi Veh. Info (If any)</th>
							</tr>
							<tr>
								<td>'.$transMode[$ewbJson->transMode].'</td>
								<td>'.$vehicle.'</td>
								<td>'.$ewbJson->fromPlace.'</td>
								<td>'.date("d/m/Y<\b\\r />h:i:s A",strtotime($ewbData->eway_bill_date)).'</td>
								<td>'.$ewbJson->fromGstin.'</td>
								<td>-</td>
								<td>-</td>
							</tr>
						</table>
						<div class="barcode text-center" style="margin-bottom:20px;margin-top:10px;"><barcode code="'.$ewbData->eway_bill_no.'" type="C128A" height="1" text="1" /><br>'.$ewbData->eway_bill_no.'</div>';
			// echo $ewbHtml;
			$mpdf = new \Mpdf\Mpdf();
			$pdfFileName=base_url('assets/uploads/eway_bill/'.$eway_bill_no.'.pdf');
			$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
			$mpdf->WriteHTML($stylesheet,1);
			$mpdf->SetDisplayMode('fullpage');
			$mpdf->SetProtection(array('print'));
			$mpdf->AddPage('P','','','','',10,10,10,10,10,10);
			$mpdf->WriteHTML('<div class="ewbOuter" style="border:1px solid #000000;padding:2px;">'.$ewbHtml.'</div>');
			$mpdf->Output($pdfFileName,'I');
		}
		else
		{
			return redirect(base_url('salesInvoice'));
		}
	}

	public function ewb_detail_pdf($eway_bill_no){
		$ewbData = $this->db->where('eway_bill_no', $eway_bill_no)->where('is_delete',0)->get('eway_bill_master')->row();
		$salesData =  $this->salesInvoice->getInvoice($ewbData->ref_id);  

		$orgData = $this->masterModel->getCompanyInfo();
		$ewbJson = json_decode($ewbData->json_data);
		$vehicle = (!empty($ewbJson->vehicleNo)) ? $ewbJson->vehicleNo : $ewbJson->transporterId;
		$qrText = 'EWB No.: ' . $ewbData->eway_bill_no . ', From:' . $ewbJson->fromGstin . ', Valid Untill: ' . date("d/m/Y h:i:s A", strtotime($ewbData->valid_up_to));
		$ewbQrCode = $this->getQRCode($qrText,'assets/uploads/eway_bill/qr_code/',$ewbData->eway_bill_no);
		$ewbQrImg = (!empty($ewbQrCode))?'<img src="'.base_url($ewbQrCode).'" width="50">':'';
		$totalItams = ((count($ewbJson->itemList) - 1) > 0) ? ' (+' . (count($ewbJson->itemList) - 1) . ' Items)' : '';
		$ewbPartB = '<br>';

		$ewbPartHeader = '<table style="padding-top:0px;margin-top:0px;margin-bottom:0px;border-bottom:1px solid #888;">
		<tr>
		<td style="width:30%;">' . date("d/m/y H:i A") . '</td>
		<td style="text-align:center;font-size:28;width:40%;font-weight:bold;">e-Way Bill</td>
		<td style="width:20%;font-size:12px;text-align:right;">E-Way Bill System</td>
		<td style="text-align:right;width:10%;">' . $ewbQrImg . '</td>
		</tr>
		</table>';

		
		$subSupplyType = Array('','Supply','Import','Export','Job Work','For Own Use','Job Work Returns','Sales Returns','Others','SKD/CKD','Line Sales','Recipient Not Known','Exhibition or Fairs');
		$transMode = Array('','Road','Rail','Air','Ship');
		$transactionType = ['1'=>'Regular','2'=>'Bill To - Ship To','3'=>'Bill From - Dispatch From','4'=>'Combination of 2 and 3'];

		$transactionType = $transactionType[$ewbJson->transactionType];
		$outward_type = ($ewbJson->supplyType=='O') ? 'Outward' : 'Inward';
		$sub_outward_type = $subSupplyType[$ewbJson->subSupplyType];		
		$docType=($ewbJson->docType=='INV')?"Tax Invoice":"Delivery Challan";

		$ewbPartA = '<table class="ewbTable" style="margin-top:0px;pedding-top:10px;" cellpadding="5">
					<tbody>
						<tr>
							<th colspan="4" style="text-align:left;">1. E-WAY BILL Details</th>					
						</tr>

						<tr>
							<td  style="width:32%;">E-Way Bill No: <b> <br>' . $ewbData->eway_bill_no . '</b></td>
							<td  style="width:35%;">Generated Date:<b> <br>' . date("d/m/Y h:i:s A", strtotime($ewbData->eway_bill_date)) . '</b></td>
							<td  style="width:32%;">Generated By:<b> <br>' . $ewbJson->fromGstin . '</b></td>
						</tr>

						<tr>
							<td  style="width:32%;">Valid Upto:<b> <br>' . date("d/m/Y h:i:s A", strtotime($ewbData->valid_up_to)) . '</b></td>
							<td  style="width:35%;">Mode: <b> <br>' . $transMode[$ewbJson->transMode] . '</b></td>
							<td style="width:32%;">Approx Distance:<b> <br>' . $ewbJson->transDistance . 'Kms</b></td>
						</tr>

						<tr>
							<td style="width:32%;">Type: <b> <br>' . $outward_type.' - '.$sub_outward_type . '</b></td>
							<td style="width:35%;">Document Details:<b> <br>' . $docType . ' - ' . $ewbJson->docNo . ' - ' . $ewbJson->docDate . '</b></td>
							<td style="width:32%;">Transaction type: <b><br>' . $transactionType . '</b></td>						
						</tr>
					</tbody>
				</table>';
		$partyData = $this->party->getParty($ewbData->party_id);
		$stateData = $this->db->where('id', $partyData->state_id)->get("states")->row();
		$ewbPartB = '<hr>
		<table class="ewbTable" style="border-bottom:1px solid #888;">
		<tr>
		<th colspan="2" style="text-align:left;">2. Address Details</th>
		</tr>
		
		</table>
		<table class="table ewbTable table-bordered text-left " style="border-bottom:1px solid #888;" >
		<tr >
		<th style="width:50%;border-right:1px solid;">From</th>
		<th style="width:50%;border-right:1px solid">To</th>
		</tr>
		<tr >
		<td  style="width:50%;border-right:1px solid" >
		GSTIN :  ' . $orgData->company_gst_no . '
		</td>
		<td style="width:50%;border-right:1px solid">
		GSTIN : ' .  $partyData->gstin . '
		</td>
		</tr>
		
		<tr>
		<td style="width:50%;border-right:1px solid">'.$orgData->company_name.'</td>
		<td style="width:50%;">' . $partyData->party_name . '</td>
		</tr>
		<tr>
		<td style="width:50%;border-right:1px solid">'.$orgData->company_state.'</td>
		<td style="width:50%;">' . $stateData->name . '</td>
		</tr>
		<tr>
		<tr><td style="border-right:1px solid"></td><td style="border-right:1px solid"></td></tr>
		
		<tr>
		<td style="width:50%;border-right:1px solid;">:: Dispatch From ::</td>
		<td style="width:50%;">:: Ship To ::</td>
		</tr>
		<tr>
		<td style="width:50%;border-right:1px solid">' . $ewbJson->fromAddr1.$ewbJson->fromAddr2 . '</td>
		<td style="width:50%;">' . $ewbData->ship_address . '</td>
		</tr>
		<tr>
		<td style="width:50%;border-right:1px solid">' . $ewbJson->fromPlace . ' - ' . $ewbJson->fromPincode . '</td>
		<td style="width:50%;">' . $ewbJson->toPlace . ' - '  . $ewbJson->toPincode . '</td>
		</tr>
		<tr>
		<td style="width:50%;border-right:1px solid">' . (!empty($ewbJson->fromState)?$ewbJson->fromState:'') . '</td>
		<td style="width:50%;">' . (!empty($ewbJson->toState)?$ewbJson->toState:'')  . ' </td>
		</tr>
	
		</table>';

		$ewbPartC = '<table class="ewbTable"><tr><th colspan="5" style="text-align:left;">3. Goods Details</th></tr></table>
					<table style="margin-top:0px;pedding-top:10px;border: 1px solid #e9ecef;" class="table table-bordered ewbBottomTable">
						<thead>	
							<tr>
								<th class="text-center" style="">HSN Code</th>
								<th class="text-center" style="width:35%">Product Name & Desc. </th>
								<th class="text-center" style="">Quantity</th>
								<th class="text-center" style="">Taxable Amount Rs.</th>
								<th class="text-center" style="">Tax Rate (C+S+I+Cess+CessNon.Advol)</th>
							</tr>
					</thead>';
		$j=0;
		foreach ($ewbJson->itemList as $row) {
			$ewbPartC .= '<tbody><tr>
							<td class="text-center" style="">' . $row->hsnCode . '</td>
							<td class="text-center" style="">' . $row->productName . '<br>' . $row->productDesc . '</td>
							<td class="text-center" style="">' . $row->quantity . '</td>
							<td class="text-center" style="">' . $row->taxableAmount . '</td>
							<td class="text-center" style="">' . (floatval($row->sgstRate) + floatval($row->cgstRate) + floatval($row->igstRate) + floatval($row->cessRate) + floatval($row->cessNonAdvol)) . '</td>
							</tr></tbody>';$j++;
		}
		if($j<11)
		{
			for($i=$j;$i<11;$i++)
			{$ewbPartC .= '<tr style="border-top:1px solid"><td>&nbsp;</td><td></td><td></td><td></td><td></td></tr>';}
		}
		$ewbPartC .=	'</table>
		<table class="ewbTable" cellpadding="5">
						<tr>
						<td style="">Tot. Taxable Amt : <b>' . $ewbJson->totalValue . '</td>
						<td style="">CGST Amt : <b>' . $ewbJson->cgstValue . '</b></td>
						<td style="">SGST Amt : <b>' . $ewbJson->sgstValue . '</td>
						<td style="">IGST Amt : <b>' . $ewbJson->igstValue . '</td>
						<td style="">CESS Amt : <b>' . $ewbJson->cessValue . '</td>
						<td style="">CESS Non.Advol Amt : <b>' . $ewbJson->cessNonAdvolValue . '
						</td>
						</tr>
						<tr>
						
						<td style="text-align:left;font-size:11px">Other Amt : <b>' . $ewbJson->otherValue . '</b></td>
						<td style="text-align:left;font-size:11px" colspan="5">Total Inv.Amt : <b>' . $ewbJson->totInvValue . '</b></td>
						</tr>
						</table>';

		$ewbPartD = '<hr><table style="margin-top:0px;border-bottom:1px solid #888;" class="ewbTable" cellpadding="5">
		<tr>
		<th colspan="2" style="text-align:left;">4. Transportation Details</th>
		</tr>
		<tr>
		<td style="">Transporter ID & Name : <b>' . $ewbJson->transporterId . ' & ' . $ewbJson->transporterName . '</b> </td>
		<td style="">Transporter Doc. No & Date :<b>' . $ewbJson->transDocNo . ' & ' . $ewbJson->transDocDate . '</td>
		</tr>
		</table>';

		if (!empty($ewbJson->transMode)) :
			$ewbPartE = '<table style="margin-top:0px;"  cellpadding="5"><tr><th colspan="2" style="text-align:left;">5. Vehicle Details</th></tr></table>
			<table class="table ewbBottomTable" cellpadding="5">
				
							<tr>
								<th style="">Mode</th>
								<th style="">Vehicle / Trans<br>Doc No & Dt</th>
								<th style="">From</th><th style="">Entered Date</th>
								<th style="">Entered By</th>
								<th style="">CEWB No. (If any)</th>
								<th style="">Multi Veh. Info (If any)</th>
							</tr>
							<tr>
								<td style="">' . $transMode[$ewbJson->transMode] . '</td>
								<td style="">' . $vehicle . '</td>
								<td style="">' . $ewbJson->fromPlace . '</td>
								<td style="">' . date("d/m/Y<\b\\r />h:i:s A", strtotime($ewbData->eway_bill_date)) . '</td>
								<td style="">' . $ewbJson->fromGstin . '</td>
								<td style="">-</td>
								<td style="">-</td>
							</tr>
						</table>';
		endif;
		$ewbBarcode = '<div class="barcode text-center" style="margin-bottom:20px;margin-top:20px;"><barcode code="' . $ewbData->eway_bill_no . '" type="C128A" height="0.7" text="1" /><br>' . $ewbData->eway_bill_no . '</div>';

		$ewbHtml = $ewbPartHeader . $ewbPartA . $ewbPartB . $ewbPartC . $ewbPartD . $ewbPartE . $ewbBarcode;
		//echo $ewbHtml;exit;
		$mpdf = new \Mpdf\Mpdf();
		$pdfFileName = base_url('assets/uploads/eway_bill/' . $eway_bill_no . '.pdf');
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet, 1);
		$mpdf->SetDisplayMode('fullpage');
		//$mpdf->SetProtection(array('print'));
		$mpdf->AddPage('P', '', '', '', '', 5, 5, 0, 0, 0, 0);
		$mpdf->WriteHTML($ewbHtml);
		$mpdf->Output($pdfFileName, 'I');
	}

	public function einv_pdf($ack_no){
		$einvData =  $this->eway->getEInvData($ack_no);
		$partyData = $this->party->getParty($einvData->party_id);
		$stateData = $this->db->where('id', $partyData->state_id)->get("states")->row();
		
		$this->data['stateData'] = $stateData;
		$this->data['einvData'] = $einvData->response_json;
		$this->data['companyData'] = $this->salesOrder->getCompanyInfo();

		$pdfData = $this->load->view('eway_bill/einv_pdf',$this->data,true);
		//echo $pdfData;exit;
		$mpdf = new \Mpdf\Mpdf();
		$pdfFileName=$ack_no.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css'));
		$stylesheet = file_get_contents(base_url('assets/css/style.css?v='.time()));
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->showWatermarkImage = true;
		$mpdf->SetProtection(array('print'));
		
		$mpdf->AddPage('P','','','','',5,5,3,3,5,5,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
	}
}
?>