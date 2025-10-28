<?php
class SalesReport extends MY_Controller
{
    private $indexPage = "report/sales_report/index";
    private $order_monitor = "report/sales_report/order_monitor";
    private $dispatch_plan = "report/sales_report/dispatch_plan";
    private $packing_report = "report/sales_report/packing_report";
    private $dispatch_summary = "report/sales_report/dispatch_summary";
    private $item_history = "report/sales_report/item_history";
    private $sales_enquiry = "report/sales_report/sales_enquiry";
    private $monthlySales = "report/sales_report/monthly_sales";
    private $dispatch_plan_summary = "report/sales_report/dispatch_plan_summary";
    private $enquiry_monitoring = "report/sales_report/enquiry_monitoring";
    private $sales_target = "report/sales_report/sales_target";
    private $sales_order_summary = "report/sales_report/sales_order_summary";
    private $custom_enquiry_register = "report/sales_report/custom_enquiry_register";
    private $quotation_monitoring = "report/sales_report/quotation_monitoring";
    private $packing_history = "report/sales_report/packing_history"; 
    private $appointment_register = "report/sales_report/appointment_register";
    private $followup_register = "report/sales_report/followup_register";
    private $monthly_dispatch_plan = "report/sales_report/monthly_dispatch_plan";
    
    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Sales Report";
		$this->data['headData']->controller = "reports/salesReport";
		$this->data['floatingMenu'] = '';//$this->load->view('report/sales_report/floating_menu',[],true);    
        $this->data['monthData'] = $this->getMonthListFY();
	}
	
	public function index(){
		$this->data['pageHeader'] = 'SALES REPORT';
        $this->load->view($this->indexPage,$this->data);
    }
    
    /* Customer's Order Monitoring */
	public function orderMonitor(){
        $this->data['headData']->pageTitle = $this->data['pageHeader'] = 'CUSTOMER ORDER MONITORING REPORT';
        $this->data['customerList'] = $this->party->getCustomerList();
        $this->load->view($this->order_monitor,$this->data);
    }
    
    public function getOrderMonitor(){
        $data = $this->input->post();
        $errorMessage = array();
		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $orderData = $this->salesReportModel->getOrderMonitor($data);
            $tbody="";$i=1;$blankInTd='<td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>';
            foreach($orderData as $row):
                $data['id'] = $row->id;
                $data['sales_type'] = $row->sales_type;
                $data['trans_date'] = $row->trans_date;
                $invoiceData = $this->salesReportModel->getInvoiceData($data); 
                $invoiceCount = count($invoiceData);
                
                $totalDispatchQty = array_sum(array_column($invoiceData,'dqty'));
                
                if($data['trans_status'] == 'ALL' || ($data['trans_status'] == 0 && floatVal($totalDispatchQty) < floatVal($row->qty)) || ($data['trans_status'] == 1  &&  floatVal($totalDispatchQty) >= floatVal($row->qty))):
                    $rp = 0;
                    $tbody .= '<tr>
                        <td>'.$i++.'</td>
                        <td>'.formatDate($row->doc_date).'</td>
                        <td>'.$row->doc_no.'</td>
                        <td>'.$row->party_code.'</td>
                        <td>'.$row->item_code.'</td>
                        <td>'.floatVal($row->qty).'</td>
                        <td>'.formatDate($row->cod_date).'</td>
                        <td>'.$row->drg_rev_no.'</td>
                        <td>'.getPrefixNumber($row->trans_prefix,$row->trans_no).'</td>
                        <td>'.formatDate($row->trans_date).'</td>
                        <td>'.$row->emp_name.'</td>';
                        
                        if($invoiceCount > 0):
                            $j=1;$dqty=0; $totalQty=0; $dPr=0;
                            foreach($invoiceData as $invRow):
                                $dqty = (!empty($invRow->dqty))?$invRow->dqty:0;
                                $totalQty += $dqty;
                                $qtyD = ($row->qty) - $totalQty;
                                $dPr = (( $qtyD * 100 ) / $row->qty);
                                $schInvNo = (!empty($invRow->inv_no))?getPrefixNumber($invRow->inv_prefix,$invRow->inv_no):getPrefixNumber($invRow->trans_prefix,$invRow->trans_no);
                                $tbody.='<td>'.$schInvNo.'</td>
                                        <td>'.formatDate($invRow->trans_date).'</td>
                                        <td>'.floatval($dqty).'</td>
                                        <td>'.floatval($totalQty).'</td>
                                        <td>'.$qtyD.'</td>
                                        <td>'.number_format($dPr,2).'%</td>';
                                if($j != $invoiceCount){
                                    $tbody.='</tr><tr class="">
                                        <td>'.$i++.'</td>
                                        <td>'.formatDate($row->doc_date).'</td>
                                        <td>'.$row->doc_no.'</td>
                                        <td>'.$row->party_code.'</td>
                                        <td>'.$row->item_code.'</td>
                                        <td>'.floatVal($row->qty).'</td>
                                        <td>'.formatDate($row->cod_date).'</td>
                                        <td>'.$row->drg_rev_no.'</td>
                                        <td>'.getPrefixNumber($row->trans_prefix,$row->trans_no).'</td>
                                        <td>'.formatDate($row->trans_date).'</td>
                                        <td>'.$row->emp_name.'</td>'; 
                                }
                                $j++;
                            endforeach;
                        else:
                            $tbody.='<td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>';
                        endif;
                    $tbody .= '</tr>';
                endif;
            endforeach;
            $this->printJson(['status'=>1, 'tbody'=>$tbody]);
        endif;
    }
    
    /*   Dispatch Plan Report    */
    public function dispatchPlan()
    {
        $this->data['headData']->pageTitle = $this->data['pageHeader'] = 'DISPATCH PLAN REPORT';
        $this->load->view($this->dispatch_plan, $this->data);
    }
    
    public function getDispatchPlan()
    {
        $data = $this->input->post();
        $errorMessage = array();
        if ($data['to_date'] < $data['from_date'])
            $errorMessage['toDate'] = "Invalid date.";
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $orderData = $this->salesReportModel->getDispatchPlan($data);
            $tbody = "";$i = 1;$toq=0;$tov=0;$wipq=0;$tpq=0;$tpv=0;$tdq=0;$tdv=0;$tpckq=0;$tpackv=0;$pq=0;$pv=0;
            $used_qty=Array();
            foreach ($orderData as $row) :
                $data['trans_main_id'] = $row->trans_main_id;
                $data['item_id'] = $row->item_id;
                //$itmData = $this->item->getItem($row->item_id);
                $price=0;
                if($row->currency!='INR')
                {         
                    $inr=$this->salesReportModel->getCurrencyConversion($row->currency);
                    if(!empty($inr)){$price=$inr[0]->inrrate*$row->price;}
                }
                else{$price=$row->price;}
				
				if(!isset($used_qty[$row->item_id])){$used_qty[$row->item_id] = 0;}
                $pendingQty = $row->qty - $row->dispatch_qty;
				$pckQty=0;$packingQty = 0;
				
				$pckQty = $row->packingQty - $used_qty[$row->item_id];
				if($pckQty > 0){if($pckQty > $pendingQty){$pckQty=$pendingQty;}}else{$pckQty=0;}
				
				if($pckQty > 0):
					$packingQty = $pckQty;
					$used_qty[$row->item_id] += $pckQty;
				endif;
                $wipQty = $this->salesReportModel->getWIPQtyForDispatchPlan($data);
                
                $planQty = $row->qty - $row->dispatch_qty - $packingQty;
				if($planQty < 0 ){$planQty = 0;}
                
				$jobData = new StdClass;
				$jobData = $this->salesReportModel->getJobcardBySO($row->so_id,$row->item_id);
				$del_date = formatDate($row->trans_date);
				if(!empty($jobData)){$del_date = formatDate($jobData->delivery_date);}
                $tbody .= '<tr>
                    <td>' . $i++ . '</td>
                    <td>' . formatDate($row->trans_date) . '</td>
                    <td>' . $row->party_code . '</td>
                    <td>' . $row->item_code . '</td>
                    <td>' . formatDate($row->cod_date) . '</td>
                    <td>' . $del_date . '</td>
                    <td>' . floatVal($price) . '</td>
                    <td>' . floatVal($row->qty) . '</td>
                    <td>' . floatVal($row->qty * $price) . '</td>
                    <td>' . floatVal($wipQty[0]->qty) . '</td>
                    <td>' . floatVal($planQty) . '</td>
                    <td>' . floatVal($planQty * $price) . '</td>
                    <td>' . floatVal($row->dispatch_qty) . '</td>
                    <td>' . floatVal($row->dispatch_qty * $price) . '</td>
                    <td>' . floatVal($packingQty) . '</td>
                    <td>' . floatVal($packingQty * $price) . '</td>
                    <td>' . floatVal($pendingQty) . '</td>
                    <td>' . floatval($pendingQty * $price) . '</td>';
                $tbody .= '</tr>';
				$toq+=floatVal($row->qty);$tov+=floatVal($row->qty * $price);$wipq+=floatVal($wipQty[0]->qty);
				$tpq+=floatVal($planQty);$tpv+=floatVal($planQty * $price);
				$tdq+=floatVal($row->dispatch_qty);$tdv+=floatVal($row->dispatch_qty * $price);
				$tpckq+=floatVal($packingQty);$tpackv+=floatVal($packingQty * $price);
				$pq+=floatVal($pendingQty);$pv+=floatval($pendingQty * $price);
            endforeach;
			$tfoot = '<tr class="thead-info">
						<th colspan="7">TOTAL</th>
						<th>' . $toq . '</th>
						<th>' . $tov . '</th>
						<th>' . $wipq . '</th>
						<th>' . $tpq . '</th>
						<th>' . $tpv . '</th>
						<th>' . $tdq . '</th>
						<th>' . $tdv . '</th>
						<th>' . $tpckq . '</th>
						<th>' . $tpackv . '</th>
						<th>' . $pq . '</th>
						<th>' . $pv . '</th>
					</tr>';
            $this->printJson(['status' => 1, 'tbody' => $tbody, 'tfoot' => $tfoot]);
        endif;
    }
    
    /*   Dispatch Summary Report */
    public function dispatchSummary()
    {
        $this->data['headData']->pageTitle = $this->data['pageHeader'] = 'Customer wise Dispatch Report';
        $this->data['partyData'] = $this->party->getCustomerList();
        $this->data['itemData'] = $this->item->getItemListForSelect(1);
        $this->load->view($this->dispatch_summary, $this->data);
    }
    
    public function getPartyItems(){
        $party_id = $this->input->post('party_id');
        $itemData = $this->item->getPartyItemList($party_id);
        $partyItems='';
        if(!empty($itemData)):
			foreach ($itemData as $row):
				$partyItems .= "<option value='".$row->id."' data-row='".json_encode($row)."'>[".$row->item_code."] ".$row->item_name."</option>";
			endforeach;
        endif;
        $this->printJson(['status'=>1,'partyItems'=>$partyItems]);
    }
    
    public function getDispatchSummary(){
        $data = $this->input->post();
        $dispatchData = $this->salesReportModel->getDispatchSummary($data);
        $i=1; $tbody =""; $tfoot=""; $tqty=0;$tamt=0;
        foreach($dispatchData as $row):
            $schInvNo = (!empty($row->inv_no))?getPrefixNumber($row->inv_prefix,$row->inv_no):getPrefixNumber($row->trans_prefix,$row->trans_no);
            $row->price = (!empty($row->inv_price))?$row->inv_price:$row->price;
            $amt = floatVal(round($row->qty * $row->price,2));
            $tbody .= '<tr>
                <td>' . $i++ . '</td>
                <td>[' . $row->party_code.']' .$row->party_name. '</td>
                <td>' . $row->item_code . '</td>
                <td>' . $schInvNo . '</td>
                <td>' . formatDate($row->trans_date) . '</td>
                <td>'.floatVal($row->qty).'</td>
                <td>'.floatVal($row->price).'</td>
                <td>'.$amt.'</td>
            </tr>';
            $tqty += $row->qty; $tamt += $amt;
        endforeach;
        $tfoot .= '<tr class="thead-info">
                <th colspan="5">Total</th>
                <th>' .floatVal($tqty). '</th>
                <th></th>
                <th>' .moneyFormatIndia($tamt). '</th>
            </tr>';
            
        $this->printJson(['status' => 1, 'tbody' => $tbody, 'tfoot' => $tfoot]);
    }
    
    /* ITEM HISTORY Report */
    public function itemHistory()
    {
        $this->data['headData']->pageTitle = $this->data['pageHeader'] = 'ITEM HISTORY REPORT';
        $this->data['itemTypeData'] = $this->item->getItemGroup();
        $this->data['locationData'] = $this->store->getProcessStoreLocationList();
        $this->load->view($this->item_history, $this->data);
    }
    
    public function getItemList(){
        $itemData = $this->item->getItemListForSelect($this->input->post('item_type'));
        $item ='<option value="">Select Item</option>';
        foreach($itemData as $row):
            $item_name = (!empty($row->item_code))? '['.$row->item_code.'] '.$row->item_name : $row->item_name;
            $item.= '<option value="'.$row->id.'">'.$item_name.'</option>';
        endforeach;
        
        $this->printJson(['status' => 1, 'itemData' => $item]);
    }
    
    public function getItemHistory(){
        $data = $this->input->post();
        $itemData = $this->salesReportModel->getItemHistory($data['item_id'], $data['location_id']);
        $i=1; $tbody =""; $tfoot=""; $credit=0;$debit=0; $tcredit=0;$tdebit=0; $tbalance=0;
        foreach($itemData as $row):
            if($row->location_id != $this->MIS_PLC_STORE->id):
                $credit=0;$debit=0;
                $transType = ($row->ref_type >= 0)?$this->data['stockTypes'][$row->ref_type] : "Opening Stock";
                if($row->trans_type == 1){ $credit = abs($row->qty);$tbalance +=abs($row->qty); } else { $debit = abs($row->qty);$tbalance -=abs($row->qty); }
                if($transType == 'Material Issue'){$row->ref_no = $row->batch_no;}
                $tbody .= '<tr>
                    <td>' . $i++ . '</td>
                    <td>'.$transType.' [ '.$row->location.' ]</td>
                    <td>'.$row->ref_no.'</td>
                    <td>'.formatDate($row->ref_date).'</td>
                    <td>'.floatVal(round($credit)).'</td>
                    <td>'.floatVal(round($debit)).'</td>
                    <td>'.floatVal(round($tbalance)).'</td>
                </tr>';
                $tcredit += $credit; $tdebit += $debit;
            endif;
        endforeach;
        $tfoot .= '<tr class="thead-info">
                <th colspan="4">Total</th>
                <th>' .floatVal(round($tcredit,2)). '</th>
                <th>' .floatVal(round($tdebit,2)). '</th>
                <th>' .floatVal(round($tbalance,2)). '</th>
            </tr>';
        $this->printJson(['status' => 1, 'tbody' => $tbody, 'tfoot' => $tfoot]);
    }
    
    public function salesEnquiry()
    {
        $this->data['headData']->pageTitle = $this->data['pageHeader'] = 'Regreated Enquiry';
        $this->data['resonData'] = $this->feasibilityReason->getFeasibilityReasonList();
        $this->data['partyData'] = $this->party->getCustomerList();
        $this->load->view($this->sales_enquiry, $this->data);
    }
	
    public function getSalesEnquiry(){
        $data = $this->input->post();
        $errorMessage = array();
		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $enquiryData = $this->salesReportModel->getSalesEnquiry($data);
            $tbody=''; $i=1;
            if(!empty($enquiryData)):
                foreach($enquiryData as $row):
                    $tbody .= '<tr>
                        <td>' . $i++ . '</td>
                        <td>' . getPrefixNumber($row->trans_prefix, $row->trans_no) . '</td>
                        <td>' . formatDate($row->trans_date) . '</td>
                        <td>[' . $row->party_code.']' .$row->party_name. '</td>
                        <td>' . $row->item_name . '</td>
                        <td>' . $row->reason . '</td>
                        <td>'.floatVal($row->qty).'</td>
                    </tr>';
                endforeach;
            else:
            endif;
            $this->printJson(['status'=>1, 'tbody'=>$tbody]);
        endif;
    }
    
    /* Monthly Sales Reports */
    public function monthlySales()
    {
        $this->data['headData']->pageTitle = $this->data['pageHeader'] = 'MONTHLY SALES';
        $this->data['partyList'] = $this->party->getCustomerList();  
        $this->data['productList']=$this->item->getItemLists(1);
        $this->load->view($this->monthlySales, $this->data);
    }
    
    public function getMonthlySalesData()
    {
        $data = $this->input->post();
        $errorMessage = array();
        if ($data['to_date'] < $data['from_date'])
            $errorMessage['toDate'] = "Invalid date.";
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $salesData = $this->salesReportModel->getSalesData($data);
            $tbody=""; $i=1; $tfoot=""; $totalTaxAmt=0; $totalGstAmt=0; $totalDiscAmt=0; $TotalNetAmt=0;
            
            foreach ($salesData as $row) :
                $tbody .= '<tr>
                    <td>' . $i++ . '</td>
                    <td>' . formatDate($row->trans_date) . '</td>
                    <td>' . getPrefixNumber($row->trans_prefix, $row->trans_no) . '</td>
                    <td>' . $row->party_name . '</td>
                    <td class="text-right">' . floatVal($row->taxable_amount) . '</td>
                    <td class="text-right">' . floatVal($row->gst_amount) . '</td>
                    <td class="text-right">' . floatVal($row->disc_amount) . '</td>
                    <td class="text-right">' . floatVal($row->net_amount) . '</td>
                </tr>';
                $totalTaxAmt += floatVal($row->taxable_amount);
                $totalGstAmt += floatVal($row->gst_amount);
                $totalDiscAmt += floatVal($row->disc_amount);
                $TotalNetAmt += floatVal($row->net_amount);
            endforeach;
            $tfoot .= '<tr class="thead-info">
                <th colspan="4">Total</th>
                <th class="text-right">' .floatVal($totalTaxAmt). '</th>
                <th class="text-right">' .floatVal($totalGstAmt). '</th>
                <th class="text-right">' .floatVal($totalDiscAmt). '</th>
                <th class="text-right">' .floatVal($TotalNetAmt). '</th>
            </tr>';
            $this->printJson(['status' => 1, 'tbody' => $tbody, 'tfoot' => $tfoot]);
        endif;
    }
    
    public function dispatchPlanSummary(){
        $this->data['headData']->pageTitle = $this->data['pageHeader'] = 'Monthly Order Summary';
        $this->data['partyData'] = $this->party->getCustomerList();
        $this->data['itemData'] = $this->item->getItemListForSelect(1);
        $this->load->view($this->dispatch_plan_summary, $this->data);
    }
    
    public function getDispatchPlanSummary(){
        $data = $this->input->post();
        
        $dispatchData = $this->salesReportModel->getDispatchPlanSummary($data);
        $i=1; $tbody =""; $thead=""; $tfoot=""; $tqty=0;$tamt=0;
        foreach($dispatchData as $row):
            //if($row->qty >= $row->dispatch_qty):
                $qty = $row->qty;$item_price=0;
                if($row->currency != 'INR')
                {         
                    $inr=$this->salesReportModel->getCurrencyConversion($row->currency);
                    if(!empty($inr)){$item_price=$inr[0]->inrrate*$row->price;}else{$item_price=$row->price;}
                }
                else{$item_price=$row->price;}
                $amt = round(($qty * $item_price),2);
                $tbody .= '<tr>
                    <td>' . $i++ . '</td>
                    <td>[' . $row->party_code.']' .$row->party_name. '</td>
                    <td>' . $row->item_code . '</td>
                    <td>' . $row->doc_no . '</td>
                    <td>' . getPrefixNumber($row->trans_prefix, $row->trans_no) . '</td>
                    <td>' . formatDate($row->cod_date) . '</td>
                    <td class="text-right">'.sprintf("%1\$.2f",$qty).'</td>
                    <td class="text-right">'.sprintf("%1\$.2f",$item_price).'</td>
                    <td class="text-right">'.sprintf("%1\$.2f",$amt).'</td>
                </tr>';
                $tqty += $qty; $tamt += $amt;
            //endif;
        endforeach;
        $thead .= '<tr class="thead-info">
                <th colspan="6">Monthly Order Summary</th>
                <th class="text-right">' .sprintf("%1\$.2f",$tqty). '</th>
                <th></th>
                <th class="text-right">' .sprintf("%1\$.2f",$tamt). '</th>
            </tr>
            <tr>
                <th style="min-width:25px;">#</th>
                <th style="min-width:100px;">Customer</th>
                <th style="min-width:100px;">Part</th>
                <th style="min-width:100px;">Cust. PO. No.</th>
                <th style="min-width:100px;">SO. No.</th>
                <th style="min-width:100px;">Delivery date</th>
                <th class="text-right" style="min-width:50px;">Quantity</th>
                <th class="text-right" style="min-width:50px;">Price</th>
                <th class="text-right" style="min-width:50px;">Total Amount</th>
            </tr>';
        $tfoot .= '<tr class="thead-info">
                <th colspan="6">Total</th>
                <th class="text-right">' .sprintf("%1\$.2f",$tqty). '</th>
                <th></th>
                <th class="text-right">' .sprintf("%1\$.2f",$tamt). '</th>
            </tr>';
            
        $this->printJson(['status' => 1, 'tbody' => $tbody, 'thead' => $thead, 'tfoot' => $tfoot]);
    }
    
    /*   Packing Report    */
    public function packingReport()
    {
        $this->data['headData']->pageTitle = $this->data['pageHeader'] = 'PACKING REPORT';
        $this->load->view($this->packing_report, $this->data);
    }
    
    public function getPackingPlan_old()
    {
        $data = $this->input->post();
        $errorMessage = array();
        if ($data['to_date'] < $data['from_date'])
            $errorMessage['toDate'] = "Invalid date.";
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $packingData = $this->salesReportModel->getPackingPlan($data);
            $tbody = "";$i = 1;$tpckq=0;$tpackv=0;$tdq=0;$tdv=0;$sq=0;$sv=0;
            $used_qty=Array();$q=0;
            foreach ($packingData as $row) :
                
				if(!isset($used_qty[$row->item_id]))
				{
					$used_qty[$row->item_id] = 0;$data['item_id'] = $row->item_id;
					$dispatchData=$this->salesReportModel->getDispatchMaterial($data);
					if(!empty($dispatchData)){$used_qty[$row->item_id]=$q=$dispatchData->dispatch_qty;}
				}
                $item_price=0;$stockQty = 0; $dispatch_qty=$used_qty[$row->item_id];
                /*if($row->currency!='INR')
                {         
                    $inr=$this->salesReportModel->getCurrencyConversion($row->currency);
                    if(!empty($inr)){$item_price=$inr[0]->inrrate*$row->item_price;}
                }
                else{$item_price=$row->item_price;}*/
				$item_price=$row->item_price;
				if($dispatch_qty > 0):
					if($dispatch_qty > $row->packing_qty){$dispatch_qty = $row->packing_qty;}
					$used_qty[$row->item_id] -= $dispatch_qty;
				endif;
				
                $stockQty = $row->packing_qty - $dispatch_qty;
                $withoutPacking = $row->totalStock - $row->packing_qty;
				
                $tbody .= '<tr>
                    <td>' . $i++ . '</td>
                    <td>' . formatDate($row->packing_date) . '</td>
                    <td>' . $row->party_code . '</td>
                    <td>' . $row->item_code . '</td>
                    <td class="text-right">' . floatVal($item_price) . '</td>
                    <td class="text-right">' . formatDecimal(floatVal($dispatch_qty)). '</td>
                    <td class="text-right">' . formatDecimal(floatVal($stockQty)). '</td>
                    <td class="text-right">' . formatDecimal(floatVal($dispatch_qty + $stockQty)) . '</td>
                    <td class="text-right">' . formatDecimal(floatVal($dispatch_qty * $item_price)) . '</td>
                    <td class="text-right">' . formatDecimal(floatVal($stockQty * $item_price)) . '</td>
                    <td class="text-right">' . formatDecimal(floatVal(floatVal($dispatch_qty + $stockQty) * $item_price)) . '</td>';
                $tbody .= '</tr>';
				$tpckq+=floatVal($dispatch_qty + $stockQty);$tpackv+=floatVal(floatVal($dispatch_qty + $stockQty) * $item_price);
				$tdq+=floatVal($dispatch_qty);$tdv+=floatVal($dispatch_qty * $item_price);
				$sq+=floatVal($stockQty);$sv+=floatval($stockQty * $item_price);
            endforeach;
			$tfoot = '<tr class="thead-info">
						<th colspan="5">TOTAL</th>
						<th>' . formatDecimal($tdq) . '</th>
						<th>' . formatDecimal($sq) . '</th>
						<th>' . formatDecimal($tpckq) . '</th>
						<th>' . formatDecimal($tdv) . '</th>
						<th>' . formatDecimal($sv) . '</th>
						<th>' . formatDecimal($tpackv) . '</th>
					</tr>';
            $this->printJson(['status' => 1, 'tbody' => $tbody, 'tfoot' => $tfoot]);
        endif;
    }
    
    public function getPackingPlan()
    {
        $data = $this->input->post();
        $errorMessage = array();
        if ($data['to_date'] < $data['from_date'])
            $errorMessage['toDate'] = "Invalid date.";
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $packingData = $this->salesReportModel->getPackingPlan($data);
            $tbody = "";$i = 1;$tpckq=0;$tpackv=0;$tdq=0;$tdv=0;$sq=0;$sv=0;
            $used_qty=Array();$q=0;
            foreach ($packingData as $row) :
                
                $item_price=0;$stockQty = 0; $dispatch_qty=0;$dispatch_price=0;$data['item_id']=$row->item_id;$disc_amt=0;
				$dispatchData=$this->salesReportModel->getDispatchOnPacking($data);
				//print_r($dispatchData);
				if(!empty($dispatchData))
				{
					$dispatch_qty=$dispatchData->dispatch_qty;
					//$dispatch_price=(!empty($dispatchData->dispatch_price)) ? round(($dispatch_qty/$dispatchData->dispatch_price),2) : 0;
					$dispatch_price = round($dispatchData->dispatch_price,2);
					$disc_amt=$dispatchData->disc_amt;
				}
                /*if($row->currency!='INR')
                {         
                    $inr=$this->salesReportModel->getCurrencyConversion($row->currency);
                    if(!empty($inr)){$item_price=$inr[0]->inrrate*$row->item_price;}
                }
                else{$item_price=$row->item_price;}*/
				$item_price=$row->item_price;
				
                //$stockQty = $row->packing_qty - $dispatch_qty;
                $stockQty = 0;
                $stockData = $this->salesReportModel->getRFDStock($row->item_id);
                if(!empty($stockData)){$stockQty = $stockData->rfd_qty;}
				
                $tbody .= '<tr>
                    <td>' . $i++ . '</td>
                    <!--<td>' . formatDate($row->packing_date) . '</td>-->
                    <td>' . $row->item_code . '</td>
                    <td class="text-right">' . floatVal($item_price) . '</td>
                    <td class="text-right">' . floatVal($dispatch_price) . '</td>
                    <td class="text-right">' . formatDecimal($dispatch_qty). '</td>
                    <td class="text-right">' . formatDecimal($stockQty). '</td>
                    <td class="text-right">' . formatDecimal($row->packing_qty) . '</td>
                    <td class="text-right">' . formatDecimal(($dispatch_qty * $dispatch_price)-$disc_amt) . '</td>
                    <td class="text-right">' . formatDecimal($stockQty * $item_price) . '</td>
                    <td class="text-right">' . formatDecimal($row->packing_qty * $item_price) . '</td>';
                $tbody .= '</tr>';
				$tpckq+=floatVal($row->packing_qty);$tpackv+=floatVal(floatVal($row->packing_qty) * $item_price);
				$tdq+=floatVal($dispatch_qty);$tdv+=floatVal($dispatch_qty * $dispatch_price);
				$sq+=floatVal($stockQty);$sv+=floatval($stockQty * $item_price);
            endforeach;
			$tfoot = '<tr class="thead-info">
						<th colspan="4">TOTAL</th>
						<th>' . formatDecimal($tdq) . '</th>
						<th>' . formatDecimal($sq) . '</th>
						<th>' . formatDecimal($tpckq) . '</th>
						<th>' . formatDecimal($tdv) . '</th>
						<th>' . formatDecimal($sv) . '</th>
						<th>' . formatDecimal($tpackv) . '</th>
					</tr>';
            $this->printJson(['status' => 1, 'tbody' => $tbody, 'tfoot' => $tfoot]);
        endif;
    }
    
    /*
    * Create By : Karmi @06-12-2021
    * Updated By : Mansee @ 13-12-2021 [Party wise filter]
    * Note : 
    */
    public function enquiryMonitoring(){
        $this->data['headData']->pageTitle = $this->data['pageHeader'] = 'Enquiry v/s order';
        $this->data['partyData'] = $this->party->getCustomerList();
        $this->load->view($this->enquiry_monitoring, $this->data);
    }
    
    /*
    * Create By : Karmi @06-12-2021
    * Updated By : Mansee @ 13-12-2021 [Party wise filter]
    * Note : 
    */
    public function getEnquiryMonitoring()
    {
        $data = $this->input->post(); //print_r($data);exit;
        $i = 1;
        $tbody = "";
        $tfoot = "";
        
        if (empty($data['party_id'])) :
            $EnqMonitorData = $this->salesReportModel->getEnquiryMonitoring($data);
            foreach ($EnqMonitorData as $row) :
                $data['party_id'] = $row->party_id;
                $countData = $this->salesReportModel->getEnquiryCount($data);
                $tbody .= '<tr>
                    <td>' . $i++ . '</td>
                    <td>' . $row->party_name . '</td>
                    <td>' . $countData->totalEnquiry . '</td>
                    <td>' . $countData->quoted . '</td>
                    <td>' . $countData->pending . '</td>
                    <td>' . $countData->confirmSo . '</td>
                    <td>' . $countData->pendingSo . '</td>
                </tr>';
            endforeach;
            $tfoot .='<tr>
                <th colspan="7"></th>
            </tr>';
        else :
            $total = 0;
            foreach($data['party_id'] as $key=>$value):
                $qryData['from_date'] = $data['from_date'];
                $qryData['to_date'] = $data['to_date'];
                $qryData['party_id'] = $value;
                $EnqMonitorData = $this->salesReportModel->getSalesEnquiryByParty($qryData);
                if(!empty($EnqMonitorData)):
                    foreach ($EnqMonitorData as $enqData) :
                        $quoteData = $this->salesReportModel->getSalesQuotation($enqData->id);
                        $total_amount = array();
                        $quoteNo = array();
                        $quotedt = array();
                        $quoteDays=array();
                        $transCount = $this->salesEnquiry->getFisiblityCount($enqData->id);
                        $itm = $this->salesEnquiry->getTransChild($enqData->id);
                        
                        $orderNo = array();
                        $orderdt = array();
                        $orderDays=array();
                        foreach ($quoteData as $quote) :
                            $total_amount[] = ($quote->total_amount * $quote->inrrate);
                            $total += ($quote->total_amount * $quote->inrrate);
                            $quoteNo[] = getPrefixNumber($quote->trans_prefix, $quote->trans_no);
                            $quotedt[] = formatDate($quote->trans_date) ;
                            $date2 = strtotime($enqData->trans_date);
                            $date1 = strtotime($quote->trans_date);
                            $datediff = $date1 - $date2;
                            $quoteDays[] =  (floor($datediff / (60 * 60 * 24)));
                        
                        
                            $orderData = $this->salesReportModel->getSalesOrder($quote->id);
                            if (!empty($orderData)) :
                                foreach ($orderData as $order) :
                                    $orderNo[] = getPrefixNumber($order->trans_prefix, $order->trans_no);
                                    $orderdt[] = formatDate($order->trans_date) ;
                                    $orderDate1 = strtotime($order->trans_date);
                                    $orderDate2 = strtotime($quote->trans_date);
                                    $orderDateDiff = $orderDate1 - $orderDate2;
                                    $orderDays[] =  (floor($orderDateDiff / (60 * 60 * 24)));
                                endforeach;
                            endif;
                        endforeach;
                        $quoteprefix = (!empty($quoteNo) ? implode('<hr>', $quoteNo) : '');
                        $quoteDate = (!empty($quotedt) ? implode('<hr>', $quotedt) : '');
                        $quoteTotalDays = (!empty($quoteDays) ? implode('<hr>', $quoteDays) : '');
                        $quotetotal_amount = (!empty($total_amount) ? implode('<hr>', $total_amount) : '');
                        $orderprefix = (!empty($orderNo) ? implode('<hr>', $orderNo) : '');
                        $orderDate = (!empty($orderdt) ? implode('<hr>', $orderdt) : '');
                        $orderTotalDays = (!empty($orderDays) ? implode('<hr>', $orderDays) : '');
                        $tbody .= '<tr>
                                        <td style="min-width:25px;">' . $i++ . '</td>
                                        <td style="min-width:100px;">' . $enqData->party_name . '</td>
                                        <td style="min-width:100px;">' .formatDate($enqData->trans_date)  . '</td>
                                        <td style="min-width:100px;">' . getPrefixNumber($enqData->trans_prefix, $enqData->trans_no)  . '</td>
                                        <td style="min-width:100px;">' . $quoteDate . '</td>
                                        <td style="min-width:100px;">' . $quoteprefix . '</td>
                                        <td style="min-width:50px;">' . $transCount->quoted . '</td>
                                        <td style="min-width:50px;">' . (count($itm) - $transCount->quoted) . '</td>
                                        <td style="min-width:100px;">' .$quotetotal_amount . '</td>
                                        <td style="min-width:100px;">'.$quoteTotalDays.'</td>
                                        <td style="min-width:100px;">' . $orderDate . '</td>
                                        <td style="min-width:100px;">' . $orderprefix . '</td>
                                        <td style="min-width:100px;">'.$orderTotalDays.'</td>
                                </tr>';
                       
                    endforeach;
                endif;
            endforeach;
            $tfoot .='<tr>
                <th colspan="8" style="text-align: left">Total</th>
                <th class="text-right">'.number_format($total).'</th>
                <th colspan="4"></th>
            </tr>';
        endif;
        $this->printJson(['status' => 1, 'tbody' => $tbody, 'tfoot' => $tfoot]);
    }
    
    /* 
        Created By Avruti @ 30-12-2021
    */
    public function salesTarget(){
        $this->data['headData']->pageTitle = $this->data['pageHeader'] = 'TARGET/SALES ORDER';
        $this->load->view($this->sales_target, $this->data);
    }
    
    public function getTargetRows(){
		$postData = $this->input->post();
        $errorMessage = array();
		
        if(empty($postData['month']))
            $errorMessage['month'] = "Month is required.";
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
			$partyData = $this->employee->getTargetRows($postData);
			$hiddenInputs = '<input type="hidden" id="sexecutive" name="executive" value="'.$postData['sales_executive'].'" />';
			$hiddenInputs .= '<input type="hidden" id="smonth" name="smonth" value="'.$postData['month'].'" />';
			$targetData = ''; $tfoot='';$i=1; $performance=0; $totalInv = 0; $totalOrder = 0; $totalTarget = 0;
			if(!empty($partyData)):
				foreach($partyData as $row):
                    if($row->business_target > 0):
    				    $postData['party_id']=$row->id;$salesTargetORD = 0;$salesTargetINV = 0 ;
        			    $salesTargetDataORD = $this->salesReportModel->getSalesOrderTarget($postData);
                        if(!empty($salesTargetDataORD->totalOrderAmt)){$salesTargetORD = $salesTargetDataORD->totalOrderAmt;}
                        
        			    $salesTargetDataINV = $this->salesReportModel->getSalesInvoiceTarget($postData);
                        if(!empty($salesTargetDataINV->totalInvoiceAmt)){$salesTargetINV = $salesTargetDataINV->totalInvoiceAmt;}
                        
                        $performance = 0;
                        if($salesTargetORD > 0 && $row->business_target >0){$performance = ($salesTargetORD * 100) / ($row->business_target);}
                        
    					$targetData .= '<tr>';
    						$targetData .= '<td>'.$i++.'</td>';
    						$targetData .= '<td>'.$row->party_name.'</td>';
    						$targetData .= '<td>'.$row->contact_person.'</td>';
    						$targetData .= '<td class="text-right">'.$row->business_target.'</td>';
    						$targetData .= '<td class="text-right">'.moneyFormatIndia(round($salesTargetORD,2)).'</td>';
    						$targetData .= '<td class="text-right">'.moneyFormatIndia(round($salesTargetINV,2)).'</td>';
    						$targetData .= '<td class="text-right">'.moneyFormatIndia(round($performance,2)).'%</td>';
    					$targetData .= '</tr>';
                        $totalTarget += $row->business_target;$totalOrder += $salesTargetORD; $totalInv += $salesTargetINV;
                    endif;
				endforeach;
                $tfoot .='<tr class="thead-info">
                    <th colspan="3" style="text-align: left">Total</th>
                    <th class="text-right">'.moneyFormatIndia(round($totalTarget,2)).'</th>
                    <th class="text-right">'.moneyFormatIndia(round($totalOrder,2)).'</th>
                    <th class="text-right">'.moneyFormatIndia(round($totalInv,2)).'</th>
                    <th></th>
                </tr>';
				$this->printJson(['status'=>1,'targetData'=>$targetData,'hiddenInputs'=>$hiddenInputs,'tfoot'=>$tfoot]);
			endif;
		endif;
    }
    
    /* Sales Order Summary */
	public function orderSummary(){
        $this->data['headData']->pageTitle = $this->data['pageHeader'] = 'SALES ORDER SUMMARY REPORT';
        $this->data['customerList'] = $this->party->getCustomerList();
        $this->load->view($this->sales_order_summary, $this->data);
    }
    
    public function getOrderSummary(){
        $data = $this->input->post();
        $errorMessage = array();
		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $orderData = $this->salesReportModel->getOrderSummary($data);
            $tbody=""; $thead=""; $i=1; $totalEstiQty = 0; $totalEstiAmt = 0; $totalOrdrQty = 0; $totalOrdrAmt = 0; $totalDisQty = 0; $totalDisAmt = 0; $totalPendQty = 0; $totalPendAmt = 0;
            foreach($orderData as $row):
                $dispatch = $this->salesReportModel->getOrderWiseDispatch($row->id,$row->sales_type);
                
                $item_price=0;
                if($row->currency != 'INR')
                {         
                    $inr=$this->salesReportModel->getCurrencyConversion($row->currency);
                    if(!empty($inr)){$item_price=$inr[0]->inrrate*$row->price;}else{$item_price=$row->price;}
                }
                else{$item_price=$row->price;}
                
                
                $estimate_qty = 0; $estimate_amt = 0; $dispatch_qty = ''; $dispatch_amt = ''; $dispatch_rate = ''; $pending_qty = ''; $pending_amt = ''; $pending_rate = '';
                if($row->qty_kg > 0){
                    $estimate_qty = $row->qty_kg;
                    $estimate_amt = round(($row->qty_kg * $item_price), 2);
                }else{
                    $estimate_qty = $row->qty;
                    $estimate_amt = round(($row->qty * $item_price),2);//$row->amount;
                }
                
                if(!empty($dispatch->dispatch_qty)){
                    $dispatch_qty = floatval($dispatch->dispatch_qty);
                    $dispatch_amt = round(($dispatch->dispatch_qty * $item_price), 2);
                    $dispatch_rate = round($item_price, 2);
                }
                $dQty = (!empty($dispatch->dispatch_qty))?$dispatch->dispatch_qty:0;
                $pending_qty = $row->qty - $dQty;
                if($pending_qty > 0){
                    $pending_qty = floatval(($row->qty - $dQty));
                    $pending_amt = round(($pending_qty * $item_price), 2);
                    $pending_rate = round($item_price, 2);
                } else { $pending_qty=0; $pending_amt=0; $pending_rate=0; }
                if(($data['trans_status'] == 0 && floatval($pending_qty) > 0) || ($data['trans_status'] == 1 && floatval($pending_qty) == 0) || ($data['trans_status'] == 'ALL')): 
                    $tbody .= '<tr>
                        <td>'.$i++.'</td>
                        <td>'.getPrefixNumber($row->trans_prefix,$row->trans_no).'</td>
                        <td>'.formatDate($row->trans_date).'</td>
                        <td>'.$row->party_name.'</td>
                        <td>['.$row->item_code.'] <br>'.$row->item_name.'</td>
                        <td>'.floatval($estimate_qty).'</td>
                        <td>'.round($item_price, 2).'</td>
                        <td>'.round($estimate_amt, 2).'</td>
                        <td>'.floatval($row->qty).'</td>
                        <td>'.round($item_price, 2).'</td>
                        <td>'.round(($row->qty * $item_price), 2).'</td>
                        <td>'.$dispatch_qty.'</td>
                        <td>'.$dispatch_rate.'</td>
                        <td>'.$dispatch_amt.'</td>
                        <td>'.$pending_qty.'</td>
                        <td>'.$pending_rate.'</td>
                        <td>'.$pending_amt.'</td>
                    </tr>';
                    
                    $totalEstiQty += $estimate_qty;
                    $totalEstiAmt += $estimate_amt;
                    $totalOrdrQty += $row->qty;
                    $totalOrdrAmt += ($row->qty * $item_price);
                    $totalDisQty += floatval($dispatch_qty);
                    $totalDisAmt += round($dispatch_amt, 2);
                    $totalPendQty += floatval($pending_qty);
                    $totalPendAmt += round($pending_amt, 2);
                endif;
            endforeach;
            
            $thead .= '<tr>
				<th rowspan="2">#</th>
				<th rowspan="2">S.O. No.</th>
				<th rowspan="2">S.O. Date</th>
				<th rowspan="2">Customer Name</th>
				<th rowspan="2">Item Name</th>
				<th style="color:#aa0000;background:#f7f7c8;">'.$totalEstiQty.'</th>
				<th style="background:#f7f7c8;">Estimated</th>
				<th style="color:#aa0000;background:#f7f7c8;">'.$totalEstiAmt.'</th>
				<th style="color:#aa0000;background:#00ffcc;">'.$totalOrdrQty.'</th>
                <th style="background:#00ffcc;">Order</th>
                <th style="color:#aa0000;background:#00ffcc;">'.$totalOrdrAmt.'</th>
                <th style="color:#aa0000;background:#fff1e2;">'.$totalDisQty.'</th>
                <th style="background:#fff1e2;">Dispatch</th>
                <th style="color:#aa0000;background:#fff1e2;">'.$totalDisAmt.'</th>
                <th style="color:#aa0000;background:#dbffe8;">'.$totalPendQty.'</th>
                <th style="background:#dbffe8;">Pending</th>
                <th style="color:#aa0000;background:#dbffe8;">'.$totalPendAmt.'</th>
			</tr>
            <tr>
                <th style="background:#f7f7c8;">Qty.</th>
				<th style="background:#f7f7c8;">Rate</th>
				<th style="background:#f7f7c8;">Amount</th>
				<th style="background:#00ffcc;">Qty.</th>
				<th style="background:#00ffcc;">Rate</th>
				<th style="background:#00ffcc;">Amount</th>
                <th style="background:#fff1e2;">Qty.</th>
				<th style="background:#fff1e2;">Rate</th>
				<th style="background:#fff1e2;">Amount</th>
                <th style="background:#dbffe8;">Qty.</th>
				<th style="background:#dbffe8;">Rate</th>
				<th style="background:#dbffe8;">Amount</th>
            </tr>';
            
            $this->printJson(['status'=>1, 'tbody'=>$tbody, 'thead'=>$thead]);
        endif;
    }
    
    public function customerEnquiryRegister(){
        $this->data['headData']->pageTitle = $this->data['pageHeader'] = 'Customer Enquiry Register';
        $this->load->view($this->custom_enquiry_register, $this->data);
    }
	
    public function getCustomerEnquiryRegister(){
        $data = $this->input->post();
        $errorMessage = array();
		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $enquiryData = $this->salesReportModel->getCustomerEnquiryRegister($data);
            $tbody=''; $i=1;
            if(!empty($enquiryData)):
                foreach($enquiryData as $row):
                    
                    if($row->currency != 'INR')
                    {         
                        $inr=$this->salesReportModel->getCurrencyConversion($row->currency);
                        if(!empty($inr)){ $quote_amt = $inr[0]->inrrate * $row->quote_amount; }
                    }
                    else{ $quote_amt = $row->quote_amount; }
                    
                    $partyName=(!empty($row->party_code)?'['.$row->party_code.']':'').$row->party_name;
                    $tbody .= '<tr>
                        <td>'. $i++.'</td>
                        <td>'.getPrefixNumber($row->trans_prefix, $row->trans_no).'</td>
                        <td>'.formatDate($row->trans_date).'</td>
                        <td class="text-left">'.$partyName.'</td>
                        <td>'.$row->ref_by.'</td>
                        <td class="text-left">'. $row->item_name.'</td>
                        <td>'.floatVal($row->qty).'</td>
                        <td>'.$row->drg_rev_no.'</td>  
                        <td>'.$row->batch_no.'</td>
                        <td>'.$row->feasible.'</td>                        
                        <td>'.((!empty($row->quote_no))?getPrefixNumber($row->quote_prefix,$row->quote_no):'').'<br>'.formatDate($row->quote_date).'</td>
                        <td></td>
                        <td>'.((!empty($row->so_no))?getPrefixNumber($row->so_prefix,$row->so_no):'').'</td>                        
                        <td>'.formatDate($row->so_date).'</td>                        
                        <td >'.$row->reason .'</td>
                        <td class="text-left">'.$row->sales_executive.'</td>
                        <td>'.moneyFormatIndia(round($quote_amt,3)).'</td>
                    </tr>';
                endforeach;
            endif;
            $this->printJson(['status'=>1, 'tbody'=>$tbody]);
        endif;
    }
    
    /* Sales Quotation Monitoring ---Karmi @05/07/2022 */
	public function salesQuotationMonitoring(){
        $this->data['headData']->pageTitle = $this->data['pageHeader'] = 'SALES QUOTATION MONITORING REPORT';
        $this->data['partyData'] = $this->party->getCustomerList();
        $this->load->view($this->quotation_monitoring, $this->data);
    }
    
    public function getSalesQuotationMonitoring(){
        $data = $this->input->post();
        $errorMessage = array();
        
		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $quotationData = $this->salesReportModel->getSalesQuotationMonitoring($data);
            $tbody=''; $i=1; $blankInTd='<td>-</td><td>-</td><td>-</td><td>-</td>';
            if(!empty($quotationData)):
                foreach($quotationData as $row):
                    $followCount = 0; 
                    $followData = (!empty($row->extra_fields))?json_decode($row->extra_fields):array();
                    $followCount = count($followData);
                    $tbody .= '<tr>
                        <td class="text-center">'. $i++.'</td>
                        <td class="text-center"><a href="'.base_url('salesQuotation/printQuotation/'.$row->trans_main_id).'" target="_blank" datatip="Print" flow="down">'.getPrefixNumber($row->trans_prefix, $row->trans_no).'</a></td>
                        <td class="text-center">'.formatDate($row->trans_date).'</td>
                        <td class="text-center">'.$row->party_name.'</td>';
                        
                    if($followCount > 0):
                        $j=1;
                        foreach($followData as $fup):
							$tbody .= '<td class="text-center">'.formatDate($fup->trans_date).'</td>
                            <td class="text-center">'.$fup->sales_executiveName.'</td>
                            <td class="text-center">'.$fup->f_note.'</td>';
                            if($j != $followCount){$tbody.='</tr><tr>'.$blankInTd; }
                            $j++; 
                        endforeach;
                    else:
                        $tbody.='<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>';
                    endif;
                    $tbody.='</tr>';   
                endforeach;
            endif;
            $this->printJson(['status'=>1, 'tbody'=>$tbody]);
        endif;
    }
    
    //Created By Karmi @12/08/2022
    public function packingHistory(){
        $this->data['headData']->pageTitle = $this->data['pageHeader'] = 'PACKING HISTORY';
        $this->data['partyData'] = $this->party->getCustomerList();
        $this->load->view($this->packing_history,$this->data);
    }

    public function getPackingHistory(){
        $data = $this->input->post();
        $packingData = $this->salesReportModel->getPackingHistory($data); 
        
        $tbody=''; $tfoot=''; $i=1; $soNo = ""; $pending_qty = 0; $oldDays =0; $pqty=0; $tamt=0;
        
        if(!empty($packingData)):
            foreach($packingData as $row):
                if(!empty($data['to_date'])){ 
                    $toDate = new DateTime(date('Y-m-d',strtotime($data['to_date'])));
                    $packingDate = new DateTime(date('Y-m-d',strtotime($row->trans_date)));
                    $dueDays = date_diff($packingDate,$toDate);
                    $day = $dueDays->format('%a');
                    $oldDays = ($day + 1).' Days'; 
                }
                
                $pending_qty = $row->dispatch_qty < $row->total_box_qty ? ($row->total_box_qty - $row->dispatch_qty) : 0; 
                $cls= ($oldDays > 30 && $pending_qty > 0) ? 'text-danger font-bold' : '';
                
                if($row->currency != 'INR'){         
                    $inr=$this->salesReportModel->getCurrencyConversion($row->currency);
                    if(!empty($inr)){$item_price=$inr[0]->inrrate*$row->price;}else{$item_price=$row->price;}
                }
                else{$item_price=$row->price;}
                $amt = $pending_qty * $item_price;
                
                $tbody .= '<tr>
                    <td>' . $i++ . '</td>
                    <td>' . formatDate($row->trans_date) . '</td>
                    <td>' . $row->trans_number . '</td>
                    <td>' . $row->so_no . '</td>
                    <td>' . $row->party_code.'</td>
                    <td>' . $row->item_code . '</td>
                    <td>'.floatVal($row->total_box_qty).'</td>
                    <td>'.floatVal($row->dispatch_qty).'</td>
                    <td>'.floatVal($pending_qty).'</td>
                    <td>'.floatVal($amt).'</td>
                    <td class="'.$cls.'">'.(($pending_qty > 0)?$oldDays:"-").'</td>
                </tr>';
                 $pqty += $pending_qty; $tamt += $amt;
            endforeach;
        endif;
        
        $tfoot .= '<tr class="thead-info">
            <th colspan="8">Total</th>
            <th class="text-right">' .floatVal($pqty). '</th>
            <th class="text-right">' .floatVal($tamt). '</th>
            <th class="text-right"></th>
        </tr>';
        $this->printJson(['status'=>1, 'tbody'=>$tbody,'tfoot'=>$tfoot]);
    }
    
    /* Appointment Register Report */
    public function appointmentRegister(){
        $this->data['headData']->pageTitle = $this->data['pageHeader'] = "APPOINTMENT REGISTER REPORT";
        $this->data['mode'] = ["Email","Online","Visit","Phone","Other"];
        $this->load->view($this->appointment_register,$this->data);
    }

    public function getAppointmentRegister(){
        $data = $this->input->post();
        $result = $this->salesReportModel->getAppointmentRegister($data);
        $i=1; $tbody='';
        if(!empty($result)):
            foreach($result as $row):
                $daysDiff = '';
                if(!empty($row->ref_date) AND !empty($row->updated_at)){
                    $ref_date = new DateTime($row->ref_date);
                    $resDate = new DateTime($row->updated_at);
                    $due_days = $ref_date->diff($resDate)->format("%r%a");
                    $daysDiff = ($due_days > 0) ? $due_days : 'On Time';
                }
                $updatedDate = (!empty($row->remark)) ? formatDate($row->updated_at) :"";
                $days = (!empty($row->remark)) ? $daysDiff  :"";
                $tbody .= '<tr>
                    <td>'.formatDate($row->ref_date).'</td>
                    <td>'.$row->party_name.'</td>
                    <td>'.$row->mode.'</td>
                    <td>'.$row->notes.'</td>
                    <td>'.$row->remark.'</td>
                    <td>'.$updatedDate.'</td>
                    <td>'.$days.'</td>';
                $tbody .= '</tr>';
            endforeach; 
        endif;  
        $this->printJson(['status'=>1, 'tbody'=>$tbody]);
    } 

    /* Follow Up Register Report */
    public function followUpRegister(){
		$this->data['headData']->pageTitle = $this->data['pageHeader'] = "FOLLOWUP REGISTER REPORT";
        $this->load->view($this->followup_register,$this->data);
    }

    public function getFollowUpRegister(){
        $data = $this->input->post();
        $result = $this->salesReportModel->getFollowUpRegister($data);
        $i=1;$tbody='';
        if(!empty($result)):
            foreach($result as $row):
                $tbody .= '<tr>
                    <td>'.$i++.'</td>
                    <td>'.formatDate($row->created_at).'</td>
                    <td>'.$row->party_name.'</td>
                    <td>'.$row->notes.'</td>
                    </tr>';
            endforeach; 
        endif;  
        $this->printJson(['status'=>1, 'tbody'=>$tbody]);
    }
    
    public function monthlydispatchPlan(){
        $this->data['headData']->pageTitle = $this->data['pageHeader'] = 'Monthly Dispatch Plan';
        $this->data['partyData'] = $this->party->getCustomerList();
        $this->data['itemData'] = $this->item->getItemListForSelect(1);
        $this->load->view($this->monthly_dispatch_plan, $this->data);
    }
    
    public function getmonthlydispatchPlan(){
        $data = $this->input->post();
        
        $dispatchData = $this->salesReportModel->getDispatchPlanSummary($data);
        $i=1; $tbody =""; $thead=""; $tfoot=""; $tqty=0;$tamt=0; $dqty=0; $damt=0; $pqty=0; $pamt=0;
        foreach($dispatchData as $row):
            //if($row->qty >= $row->dispatch_qty):
                $dispatch = $this->salesReportModel->getOrderWiseDispatch($row->id,$row->sales_type);
                $qty = $row->qty;$item_price=0; $dispatch_qty = ''; $dispatch_amt = ''; $pending_qty = ''; $pending_amt = '';
                if($row->currency != 'INR')
                {         
                    $inr=$this->salesReportModel->getCurrencyConversion($row->currency);
                    if(!empty($inr)){$item_price=$inr[0]->inrrate*$row->price;}else{$item_price=$row->price;}
                }
                else{$item_price=$row->price;}
                
                if(!empty($dispatch->dispatch_qty)){
                    $dispatch_qty = floatval($dispatch->dispatch_qty);
                    $dispatch_amt = round(($dispatch->dispatch_qty * $item_price), 2);
                    $dispatch_rate = round($item_price, 2);
                }
                $dQty = (!empty($dispatch->dispatch_qty))?$dispatch->dispatch_qty:0;
                $pending_qty = $row->qty - $dQty;
                if($pending_qty > 0){
                    $pending_qty = floatval(($row->qty - $dQty));
                    $pending_amt = round(($pending_qty * $item_price), 2);
                    $pending_rate = round($item_price, 2);
                } else { $pending_qty=0; $pending_amt=0; $pending_rate=0; }
                
                $amt = round(($qty * $item_price),2);
                $tbody .= '<tr>
                    <td>' . $i++ . '</td>
                    <td>' . $row->party_code.'</td>
                    <td>' . $row->item_code . '</td>
                    <td>' . $row->doc_no . '</td>
                    <td>' . getPrefixNumber($row->trans_prefix, $row->trans_no) . '</td>
                    <td>' . formatDate($row->prod_target_date) . '</td>
                    <td class="text-right">'.sprintf("%1\$.2f",$qty).'</td>
                    <td class="text-right">'.sprintf("%1\$.2f",$amt).'</td>
                    <td  class="text-right">'.sprintf("%1\$.2f",$dispatch_qty).'</td>
                    <td  class="text-right">'.sprintf("%1\$.2f",$dispatch_amt).'</td>
                    <td  class="text-right">'.sprintf("%1\$.2f",$pending_qty).'</td>
                    <td  class="text-right">'.sprintf("%1\$.2f",$pending_amt).'</td>
                </tr>';
                $tqty += $qty; $tamt += $amt; 
                $dqty += floatval($dispatch_qty); $damt +=floatval($dispatch_amt);
                $pqty += $pending_qty; $pamt +=$pending_amt;
            //endif;
        endforeach;
        $thead .= '<tr class="thead-info">
                <th colspan="6">Monthly Dispatch Plan</th>
                <th colspan="2" class="text-center">Order</th>
                <th colspan="2" class="text-center">Dispatch</th>
                <th colspan="2" class="text-center">Pending</th>
            </tr>
            <tr>
                <th style="min-width:25px;">#</th>
                <th style="min-width:100px;">Customer</th>
                <th style="min-width:100px;">Part</th>
                <th style="min-width:100px;">Cust. PO. No.</th>
                <th style="min-width:100px;">SO. No.</th>
                <th style="min-width:100px;">Prod. Target Date</th>
                <th class="text-right" style="min-width:50px;">Quantity</th>
                <th class="text-right" style="min-width:50px;">Amount</th>
                <th class="text-right" style="min-width:50px;">Quantity</th>
                <th class="text-right" style="min-width:50px;">Amount</th>
                <th class="text-right" style="min-width:50px;">Quantity</th>
                <th class="text-right" style="min-width:50px;">Amount</th>
            </tr>';
        $tfoot .= '<tr class="thead-info">
                <th colspan="6">Total</th>
                <th class="text-right">' .sprintf("%1\$.2f",$tqty). '</th>
                <th class="text-right">' .sprintf("%1\$.2f",$tamt). '</th>
                <th class="text-right">' .sprintf("%1\$.2f",$dqty). '</th>
                <th class="text-right">' .sprintf("%1\$.2f",$damt). '</th>
                <th class="text-right">' .sprintf("%1\$.2f",$pqty). '</th>
                <th class="text-right">' .sprintf("%1\$.2f",$pamt). '</th>
            </tr>';
            
        $this->printJson(['status' => 1, 'tbody' => $tbody, 'thead' => $thead, 'tfoot' => $tfoot]);
    }
}
?>