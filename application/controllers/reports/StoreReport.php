<?php
class StoreReport extends MY_Controller
{
    private $indexPage = "report/store_report/index";
    private $issue_register = "report/store_report/issue_register";
    private $stock_register = "report/store_report/stock_register";
    private $inventory_monitor = "report/store_report/inventory_monitor";
    private $consumable_report = "report/store_report/consumable_report";
    private $fgstock_report = "report/store_report/fgstock_report";
    private $tool_issue_register = "report/store_report/tool_issue_register";
    private $scrap_book = "report/store_report/scrap_book";
    private $item_history = "report/sales_report/item_history";
    private $stock_wise_report = "report/store_report/stock_wise_report";
    private $misplaced_item="report/store_report/misplaced_item";
    private $in_process_stock ="report/store_report/in_process_stock";

    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Store Report";
		$this->data['headData']->controller = "reports/storeReport";
		$this->data['floatingMenu'] = '';//$this->load->view('report/store_report/floating_menu',[],true);
        $this->data['refTypes'] = array('','GRN','Purchase Invoice','Material Issue','Delivery Challan','Sales Invoice','Manual Manage Stock','Production Finish','Visual Inspection','Store Transfer','Return Stock From Production','In Challan','Out Challan','Tools Issue','Stock Journal','Packing Material','Packing Product','Rejection Scrap','Production Scrap','Credit Note','Debit Note','General Issue', 'Stock Verification', 'Process Movement', 'Production Rejection', 'Production Rejection Scrap', 'Move to Allocation RM Store', 'Move To Received RM Store', 'Move To Packing Area', 'RM Process', 'Short Closed');
    }
	
	public function index(){
		$this->data['pageHeader'] = 'STORE REPORT';
        $this->load->view($this->indexPage,$this->data);
    }
 
    /* ISSUE REGISTER (CONSUMABLE) REPORT */
    public function issueRegister(){
		$this->data['headData']->pageTitle = "Issue Register";
        $this->data['pageHeader'] = 'ISSUE REGISTER (Consumable/Raw Material) REPORT';
        $this->data['deptData'] = $this->department->getDepartmentList();
        $this->load->view($this->issue_register,$this->data);
    }

    public function getIssueRegister(){
        $data = $this->input->post();
        $errorMessage = array();
		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $issueData = $this->storeReportModel->getIssueRegister($data);
            $tbody="";$i=1; $thead=""; $tfoot=""; $totalQty=0; $totalItemPrice=0; $total=0;
            if($data['item_type'] == 3):
                $thead.="<th colspan='7'>Issue Register (Raw Material)</th>
                <th colspan='2'>F ST 04 (00/01.06.20)</th>";
            elseif($data['item_type'] == 2):
                $thead.="<th colspan='7'>Issue Register (Consumable)</th>
                <th colspan='2'>F ST 04 (00/01.06.20)</th>";
            else:
                
                $thead.="<th colspan='7'>Issue Register (Consumable/Raw Material)</th>
                <th colspan='2'>F ST 04 (00/01.06.20)</th>";
            endif;
            foreach($issueData as $row):
                
                $empdata = $this->employee->getEmp($row->collected_by);
                $emp_name = (!empty( $empdata))?$empdata->emp_name:"";
                $data['item_id']=$row->item_id;
                $prs=$this->purchaseReport->getLastPrice($data);
                
                $price=$row->itemPrice;
                if(!empty($prs))
                {
                    $price=$prs->price;
                }
                $totalPrice = (abs($row->qty) * $price);
                $tbody .= '<tr>
                    <td>'.$i++.'</td>
                    <td>'.formatDate($row->ref_date).'</td>
                    <td>'.$row->item_name.'</td>
                    <td>'.$row->dept_name.'</td>
                    <td>'.abs($row->qty).'</td>
                    <td>'.$emp_name.'</td>
                    <td>'.$row->remark.'</td>
                    <td>'.$price.(!empty($prs)?' (P) ':' (D) ').'</td>
                    <td>'.round($totalPrice, 2).'</td>
                </tr>';
                $totalQty+=abs($row->qty);$totalItemPrice += $price; $total += $totalPrice;
            endforeach;
            $tfoot = '<tr>
                    <th colspan="4">Total</th>
                    <th>'.round($totalQty).'</th>
                    <th colspan="2"></th>
                    <th>'.round($totalItemPrice, 2).'</th>
                    <th>'.round($total, 2).'</th>
                </tr>';
            $this->printJson(['status'=>1, 'thead'=>$thead, 'tbody'=>$tbody, 'tfoot'=>$tfoot]);
        endif;
    }

    /* STOCK REGISTER (CONSUMABLE) REPORT */
    public function stockRegister(){
		$this->data['headData']->pageTitle = "Stock Register";
        $this->data['pageHeader'] = 'STOCK REGISTER (CONSUMABLE) REPORT';
        $this->data['item_type'] = 2;
        $this->load->view($this->stock_register,$this->data);
    }

    /* STOCK REGISTER (RAW MATERIAL) REPORT */
    public function stockRegisterRawMaterial(){
		$this->data['headData']->pageTitle = "Stock Register";
        $this->data['pageHeader'] = 'STOCK REGISTER (RAW MATERIAL) REPORT';
        $this->data['item_type'] = 3;
        $this->load->view($this->stock_register,$this->data);
    }

    public function getStockRegister(){
        $data = $this->input->post();
        $errorMessage = array();
		if(!empty($data['to_date']))
			$errorMessage['toDate'] = "Required date.";

        if(empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            //$itemData = $this->storeReportModel->getStockRegister($data['item_type']);
            $itemData = $this->accountingReport->getStockRegister($data);
            $thead="";$tbody="";$i=1;$receiptQty=0;$issuedQty=0;
            
            if(!empty($itemData)):
                $type = ($data['item_type'] == 2)? "Consumable":"Raw Material";
                $formate = ($data['item_type'] == 2)? "F ST 05":"F ST 02";
                $thead = '<tr class="text-center"><th colspan="4">Stock Register ('.$type.')</th><th>'.$formate.'(00/01.06.20)</th></tr><tr><th>#</th><th>Item Description</th><th>Receipt Qty.</th><th>Issued Qty.</th><th>Balance Qty.</th></tr>';
                foreach($itemData as $row):
                    $data['item_id'] = $row->id;
                    $receiptQty = $row->rqty; $issuedQty = $row->iqty;
                    $balanceQty = 0;
                    if(!empty($issuedQty) AND !empty($receiptQty)){ $balanceQty = $receiptQty - abs($issuedQty); }
                    $tbody .= '<tr>
                        <td>'.$i++.'</td>
                        <td>'.$row->item_name.'</td>
                        <td class="text-right">'.floatVal($receiptQty).'</td>
                        <td class="text-right">'.abs(floatVal($issuedQty)).'</td>
                        <td class="text-right">'.sprintf('%0.2f',$balanceQty).'</td>
                    </tr>';
                endforeach;
            else:
                $tbody .= '<tr style="text-align:center;"><td colspan="5">Data not found</td></tr>';
            endif;
            $this->printJson(['status'=>1, 'thead'=>$thead, 'tbody'=>$tbody]);
        endif;
    }

    /* INVENTORY MONITORING REPORT */
    public function inventoryMonitor(){
		$this->data['headData']->pageTitle = "Inventory Monitor";
        $this->data['pageHeader'] = 'INVENTORY MONITORING REPORT';
        $this->data['itemGroup'] = $this->storeReportModel->getItemGroup();
        $this->load->view($this->inventory_monitor,$this->data);
    }
    
    //CREATED BY Meghavi 16-05-2023 
    public function getItemForInventoryMonitor(){
	    $data = $this->input->post(); 
	    $result = array();
	   
	        $options="";
	        $result = $this->item->getItemListForSelect($data['item_type']); //print_r($result);exit;
    		if(!empty($result)): 
    			foreach($result as $row):
        			    $item_name = (!empty($row->item_code))? "[".$row->item_code."] ".$row->item_name : $row->item_name;
        				$options .= "<option value='".$row->id."'>".$item_name."</option>";
    			endforeach;
    		endif;

		$this->printJson(['status'=>1, 'options'=>$options]);
	}

    //UPDATED BY Meghavi 16-05-2023 
    public function getInventoryMonitor(){
        $data = $this->input->post(); 
        $errorMessage = array();
		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $itemData = $this->storeReportModel->getInventoryMonitor($data);
            $tbody="";$i=1;$opningStock=0;$closingStock=0;$fyOpeningStock=0;$totalOpeningStock=0;$monthlyInward=0;$monthlyCons=0;$inventory=0;$amount=0;$total=0;$totalInventory=0;$totalValue=0;$totalUP=0;
            foreach($itemData as $row)://if($row->id==124):
                $data['item_id'] = $row->id;$untPrice=0;
                $fyOSData = Array();
                $opningStock = (!empty($row->opening_qty)) ? $row->opening_qty : 0;
                $monthlyInward = $row->rqty;
                $monthlyCons = abs($row->iqty);
                $totalOpeningStock = floatval($opningStock);
                $closingStock = ($totalOpeningStock + $monthlyInward - $monthlyCons);
                $untPrice = $row->price;
                $total = round(($closingStock * ($untPrice *  $row->inrrate)), 2);
                
                
                    $tbody .= '<tr>
                        <td>'.$i++.'</td>
                        <td>'.((!empty($row->item_code))?'['.$row->item_code.'] '.$row->item_name : $row->item_name).'</td>
                        <td>'.floatVal($row->min_qty).'</td>
                        <td>'.floatVal($row->max_qty).'</td>
                        <td>'.floatVal($totalOpeningStock).'</td>
                        <td>'.floatVal(round($monthlyInward,2)).'</td>
                        <td>'.floatVal(round($monthlyCons,2)).'</td>
                        <td>'.floatVal(round($closingStock,2)).'</td>
                        <td>'.number_format($untPrice, 2).'</td>
                        <td>'.number_format($total, 2).'</td>
                    </tr>';
                    $totalInventory += round($closingStock,2);
                    $totalValue += $total;
                
            endforeach;
            
            $totalUP = (!empty($totalInventory)) ? round(($totalValue / $totalInventory),2) : 0;
            $this->printJson(['status'=>1, 'tbody'=>$tbody, 'totalInventory'=>number_format($totalInventory,2), 'totalUP'=>number_format($totalUP,2), 'totalValue'=>number_format($totalValue,2)]);
        endif;
    }

    /* Consumable Report */
    public function consumableReport(){
		$this->data['headData']->pageTitle = "Consumable Report";
        $this->data['pageHeader'] = 'CONSUMABLES REPORT';
        $consumableData = $this->storeReportModel->getConsumable();

        $i=1;  $this->data['tbody']='';
        if(!empty($consumableData)){
            foreach($consumableData as $row):                
                $locData = $this->storeReportModel->getItemLocation($row->id);
                if(!empty($locData)){
                    $location_id = explode(',',$locData->location_id);
                    $x=1;$location='';
                    foreach($location_id as $lid)
                    {
                        if(!empty($lid)){
                            $store_name = $this->store->getStoreLocation($lid)->store_name;
                            if($x == 1){ $location .= $store_name; }
                            else { $location .= ', '.$store_name; } $x++;
                        }
                    }
                }
                $size = (!empty($row->size))? ' / '.$row->size : "";
                $this->data['tbody'] .= '<tr>
                    <td>'.$i++.'</td>
                    <td>'.$row->item_name.'</td>
                    <td>'.$row->material_grade.$size.'</td>
                    <td>'.$row->min_qty.'</td>
                    <td>'.$location.'</td>
                    <td>'.$row->description.'</td>
                </tr>';
            endforeach;
        }
        $this->load->view($this->consumable_report,$this->data);
    }

    /* Stock Statement finish producct */
    public function fgStockReport(){
		$this->data['headData']->pageTitle = "Stock Statement";
        $this->data['pageHeader'] = 'STOCK STATEMENT REPORT';
        $this->data['customerList'] = $this->party->getCustomerList();
        $this->load->view($this->fgstock_report,$this->data);
    }
    
    public function getFgStockReport(){
        $data = $this->input->post();
        $errorMessage = array();
		if(empty($data['to_date']))
			$errorMessage['toDate'] = "Date is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['item_type'] = 1;$stockType = '';
            if($data['stock_type'] == '0'){$stockType = 'stockQty = 0';}
            if($data['stock_type'] == 1){$stockType = 'stockQty > 0';}
            $itemData = $this->accountingReport->getStockRegister($data,$stockType);
            $itemData = $this->accountingReport->getStockRegister($data);
            $thead="";$tbody="";$tfoot='';$i=1;$receiptQty=0;$issuedQty=0;$totalAmt=0; $treceiptQty = 0; $tissuedQty = 0; $tbalanceQty = 0;
            
            if(!empty($itemData)):
                foreach($itemData as $row):  
                    $data['item_id'] = $row->id;
                    $bQty = 0;
                    $receiptQty = $row->rqty; 
                    $issuedQty = $row->iqty;
                    $itmStock = $row->stockQty;
                    if(!empty($row->stockQty)){$bQty = $row->stockQty;}
                    
                    $balanceQty=0;
                    if($row->item_type == 1){ $balanceQty = round($bQty,3); } 
                    else { $balanceQty = round($receiptQty - abs($issuedQty),3); } 
                    
                    //$balanceQty = round($bQty,3);
                    
                    $price = (!empty($row->inrrate))? ($row->price * $row->inrrate) : $row->price;
                    $tamt = ($balanceQty > 0)? round($balanceQty * $price, 2) : 0;
                    $tbody .= '<tr>
                                <td>'.$i++.'</td>
                                <td>'.$row->item_code.'</td>
                                <td>'.$row->item_name.'</td>
                                <td>'.$row->party_name.'</td>
                                <td>'.$row->drawing_no.'</td>
                                <td>'.$row->rev_no.'</td>
                                <td>'.floatVal($balanceQty).'</td>
                            </tr>';
                    $totalAmt += $tamt;
                    $treceiptQty += $receiptQty; 
                    $tissuedQty += $issuedQty;
                    $tbalanceQty += $balanceQty;
                endforeach;
                $tfoot = '<tr>
                        <th colspan="6">Total</th>
                        <th class="text-right">' .number_format($tbalanceQty,2). '</th>
                    </tr>';            
            else:
                $tfoot = '<tr>
                        <th colspan="2">Total</th>
                        <th class="text-right">0</th>
                        <th class="text-right">0</th>
                        <th class="text-right">0</th>
                        <th class="text-right">0</th>
                    </tr>';     
            endif;
            
            $this->printJson(['status'=>1, 'tbody'=>$tbody, 'tfoot'=>$tfoot]);
        endif;
    }
    
    /*TOOL ISSUE REGISTER (CONSUMABLE) REPORT */  
    public function toolissueRegister(){
		$this->data['headData']->pageTitle = "Tool Issue Register";
        $this->data['pageHeader'] = 'TOOL ISSUE REGISTER REPORT';
        $this->data['deptData'] = $this->department->getMachiningDepartment(8);
        $this->data['jobCardData'] = $this->storeReportModel->getJobcardList();
        $this->load->view($this->tool_issue_register,$this->data);
        
    } 
    
	public function getToolIssueRegister(){
        $data = $this->input->post();
        $errorMessage = array();
		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $issueData = $this->storeReportModel->getToolIssueRegister($data); 
			
            $tbody=""; $i=1; $total_amount = 0;$total_qty = 0;
            foreach($issueData as $row):
                $data['item_id']=$row->item_id;
                $prs=$this->purchaseReport->getItemLastGrnPrice($data);
                $price=$row->price;
                if(!empty($prs->price))
                {
                    $price=round($prs->price,2);
                }
                $amount = round((floatVal($row->qty) * floatval($price)),2);
				$partCode=''; $jobNo = '';
				if(!empty($row->job_no)){
                    $jobNo = getPrefixNumber($row->job_prefix,$row->job_no);
                    $pcode = $this->item->getItem($row->product_id)->item_code;
                    $partCode = '['.$pcode.'] ';
                }
                
                $tbody .= '<tr>
                    <td class="text-center">'.$i++.'</td>
                    <td>'.formatDate($row->issue_date).'</td>
                    <td>'.$row->item_name.'</td>
                    <td>'.$row->name.'</td>
                    <td>'.$partCode.(!empty($jobNo) ? $jobNo : 'General Issue').'</td>
                    <td>'.floatVal($row->qty).'</td>
                    <td>'.floatval($price).(!empty($prs)?' (G) ':' (D) ').'</td>
                    <td>'.$amount.'</td>
                </tr>';
				$total_amount += $amount;$total_qty += $row->qty;
            endforeach;
			$avgPrice = ($total_qty > 0) ? round((floatVal($total_amount / $total_qty)),2) : 0;
			$thead = '<tr class="text-center" id="theadData">
                    <th colspan="5">Tool Issue Register (F PR 13 (00/01.06.2020))</th>
                    <th>'.floatVal($total_qty).'</th>
                    <th>'.$avgPrice.'</th>
                    <th>'.moneyFormatIndia($total_amount).'</th>
                </tr>
				<tr>  
					<th>#</th>
					<th>Date</th>
					<th>Product</th>
					<th>Department</th>
					<th>Job Card</th>
					<th>Qty.</th>
					<th>Price</th>
					<th>Amount</th>
				</tr>';
			$tfoot = '<tr>
                    <th colspan="5">Total</th>
                    <th>'.floatVal($total_qty).'</th>
                    <th>'.$avgPrice.'</th>
                    <th>'.moneyFormatIndia($total_amount).'</th>
                </tr>';
            $this->printJson(['status'=>1, 'tbody'=>$tbody, 'tfoot'=>$tfoot, 'thead'=>$thead]);
        endif;
    }

    /**
     * Created By Mansee @ 09-12-2021
     */
    public function scrapBook(){
		$this->data['headData']->pageTitle = "Scrap Book";
        $this->data['pageHeader'] = 'SCRAP BOOK REPORT';
        $this->data['scrapGroup']=$this->materialGrade->getScrapList();
        $this->data['materialGrade']=$this->materialGrade->getMaterialGrades();        
        $this->load->view($this->scrap_book,$this->data);
    }

    public function getScrapReport(){
        $data = $this->input->post();
        $errorMessage = array();
		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $itemData = $this->storeReportModel->getRowMaterialScrapQty($data);
            $tbody="";$i=1;
            $totalQty = $total_inqty = $total_outqty = 0;
            $totalPrice = 0;
            $netValuation = 0;
            $avgPrice = 0;
            if(!empty($itemData)):
                foreach($itemData as $row):
                    $value = ($row->stock_qty > 0 AND $row->price > 0) ? round(($row->price*$row->stock_qty),2) : 0;
                    $tbody .= '<tr>
                        <td>'.$i++.'</td>
                        <td>'.$row->item_name.'</td>
                        <td>'.$row->in_qty.'</td>
                        <td>'.$row->out_qty.'</td>
                        <td>'.$row->stock_qty.'</td>
                        <td>'.round($row->price,2).'</td>
                        <td>'.$value.'</td>                    
                    </tr>';
                    $netValuation += $value;
                    $totalQty += $row->stock_qty;
                    $total_inqty += $row->in_qty;
                    $total_outqty += $row->out_qty;
                    $totalPrice += $row->price;
                endforeach;
                $avgPrice = (!empty($netValuation))?round(($netValuation / $totalQty),2):0;
            endif;           
            $thead = '<tr>
							<th>#</th>
							<th>Item Name</th>
							<th>In Qty</th>
							<th>Out Qty</th>
							<th>Scrap Qty <br><small> As On '.date('d-m-Y',strtotime($data['to_date'])).'</small></th>
							<th>Price</th>
							<th>Valuation</th>
						</tr>';
            $this->printJson(['status'=>1, 'thead'=>$thead,'tbody'=>$tbody,'avg_price'=>$avgPrice,'net_valuation'=>$netValuation,'total_inqty'=>round($total_inqty,3),'total_outqty'=>round($total_outqty,3),'total_qty'=>round($totalQty,3)]);
        endif;
    }
    
    /* ITEM HISTORY Report */
    public function itemHistory(){
		$this->data['headData']->pageTitle = "Item History";
        $this->data['pageHeader'] = 'ITEM HISTORY REPORT';
        $this->data['itemTypeData'] = $this->item->getItemGroup();
        $this->data['locationData'] = $this->store->getProcessStoreLocationList();
        $this->load->view($this->item_history, $this->data);
    }

    public function getItemList(){
        $item_type = $this->input->post('item_type');
        $itemData = $this->item->getItemListForSelect($item_type);

        $item="";
        $item.='<option value="">Select Item</option>';
        foreach($itemData as $row):
            $item.= '<option value="'.$row->id.'">'.(!empty($row->item_code)?'['.$row->item_code.']':'').$row->item_name.'</option>';
        endforeach;
        $this->printJson(['status' => 1, 'itemData' => $item]);
    }

    public function getItemHistory(){
        $data = $this->input->post();
        $itemData = $this->store->getItemHistory($data['item_id'], $data['location_id']);

        $i=1; $tbody =""; $tfoot=""; $credit=0;$debit=0; $tcredit=0;$tdebit=0; $tbalance=0;
        foreach($itemData as $row):
            if($row->location_id != $this->MIS_PLC_STORE->id):
                $credit=0;$debit=0;
                $transType = ($row->ref_type >= 0)? $this->data['stockTypes'][$row->ref_type] : "Opening Stock";
                if($row->trans_type == 1){ $credit = abs($row->qty);$tbalance +=abs($row->qty); } else { $debit = abs($row->qty);$tbalance -=abs($row->qty); }
                if($transType == 'Material Issue'){$row->ref_no = $row->batch_no;}
                
                $tbody .= '<tr>
                    <td>' . $i++ . '</td>
                    <td>'.$transType.' [ '.$row->location.' ]</td>
                    <td>'.$row->ref_no.'</td>
                    <td>'.formatDate($row->ref_date).'</td>
                    <td>'.floatVal(round($credit,3)).'</td>
                    <td>'.floatVal(round($debit,3)).'</td>
                    <td>'.floatVal(round($tbalance,3)).'</td>
                </tr>';
                $tcredit += $credit; $tdebit += $debit;
            endif;
        endforeach;
        $tfoot .= '<tr class="thead-info">
                <th colspan="4">Total</th>
                <th>' .floatVal(round($tcredit,3)). '</th>
                <th>' .floatVal(round($tdebit,3)). '</th>
                <th>' .floatVal(round($tbalance,3)). '</th>
            </tr>';

        $this->printJson(['status' => 1, 'tbody' => $tbody, 'tfoot' => $tfoot]);
    }
    
    /* Store Wise Stock Statement 
        Avruti @21-04-2022
    */
    public function storeWiseStockReport(){
		$this->data['headData']->pageTitle = "Store Wise Stock Statement";
        $this->data['pageHeader'] = 'STORE WISE STOCK STATEMENT REPORT';
        $this->data['locationData'] = $this->store->getStoreLocationListWithoutProcess();
        $this->data['partyList'] = $this->party->getPartyListOnCategory(1);
        $this->load->view($this->stock_wise_report,$this->data);
    }

    public function getStoreWiseStockReport(){
        $data = $this->input->post();
        $errorMessage = array();
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $fgData = $this->storeReportModel->getStoreWiseStockReport($data); 
            $tbody="";$i=1;
            foreach($fgData as $row):
                if($row->qty > 0):
                    $tbody .= '<tr>
                        <td>'.$i++.'</td>
                        <td>'.$row->item_code.'</td>
                        <td>'.$row->item_name.'</td>
                        <td>'.$row->batch_no.'</td>
                        <td>'.$row->qty.'</td>
                    </tr>';
                endif;
            endforeach;
            $this->printJson(['status'=>1, 'tbody'=>$tbody]);
        endif;
    }
    
     /* MISPLACED ITEM HISTORY Report */
    public function misplacedItem()
    {
        $this->data['pageHeader'] = 'MISPLACED ITEM HISTORY REPORT';
		$this->data['headData']->pageTitle = "MISPLACED ITEM HISTORY REPORT";
        $this->data['itemData'] = $this->storeReportModel->getMisplacedItemList();
        $this->load->view($this->misplaced_item, $this->data);
    }

    public function getMisplacedItemHistory(){
        $data = $this->input->post();
        $itemData = $this->storeReportModel->getMisplacedItemHistory($data);
        $i=1; $tbody =""; 
        foreach($itemData as $row):

            $tbody .= '<tr>
                <td>' . $i++ . '</td>
                <td>'.$row->item_code.'</td>
                <td>'.$row->item_name.'</td>
                <td>'.date("d-m-Y H:i:s",strtotime($row->created_at)).'</td>
                <td>'.$row->emp_name.'</td>
                <td>'.$row->batch_no.'</td>
                <td>'.$row->ref_batch.'</td>
                <td>'.floatVal($row->qty).'</td>
            </tr>';
        endforeach;
        $this->printJson(['status' => 1, 'tbody' => $tbody]);
    }
    
    public function inProcessStock(){
        $this->data['headData']->pageTitle = $this->data['pageHeader'] = 'In Process Stock';
        $this->data['processData'] = $this->process->getProcessList();
        $this->data['vendorData'] = $this->party->getVendorList();
        $this->data['partyList'] = $this->party->getPartyListOnCategory(1);
        $this->load->view($this->in_process_stock,$this->data);
    }

    public function getInProcessStockData(){
        $data = $this->input->post();
        $errorMessage = array();
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $tbody="";$totalQty=0;$totalQtyKg=0;
            if($data['stock_type'] == 'PROCESS'){
                $fgData = $this->storeReportModel->getInProcessStockData($data['process_id'],$data['party_id']); 
                $i=1;
                foreach($fgData as $row):
                    $qty = $row->in_qty-$row->out_qty-(!empty($row->rej_qty)?$row->rej_qty:0);
                    if($qty > 0):
                        $totalQty+=$qty;
                        $tbody .= '<tr>
                            <td>'.$i++.'</td>
                            <td>'.$row->item_code.'</td>
                            <td>'.$row->item_name.'</td>
                            <td>'.(getPrefixNumber($row->job_prefix,$row->job_no)).'</td>
                            <td>'.$qty.'</td>
                            <td>-</td>
                        </tr>';
                    endif;
                endforeach;
            }else{
                $fgData = $this->storeReportModel->getInProcessStockVendorWise($data['process_id'],$data['party_id']); 
                $i=1;
                foreach($fgData as $row):
                    if($row->qty > 0):
                        $totalQty+=$row->qty;
                        $totalQtyKg+=$row->qty_kg;
                        $tbody .= '<tr>
                            <td>'.$i++.'</td>
                            <td>'.$row->item_code.'</td>
                            <td>'.$row->item_name.'</td>
                            <td>'.(getPrefixNumber($row->job_prefix,$row->job_no)).'</td>
                            <td>'.$row->qty.'</td>
                            <td>'.$row->qty_kg.'</td>
                        </tr>';
                    endif;
                endforeach;
            }
            $tfoot='<tr class="thead-info">
                <th class="text-right" colspan="4">Total</th>
                <th>'.$totalQty.'</th>
                <th>'.$totalQtyKg.'</th>
            </tr>';
            $this->printJson(['status'=>1, 'tbody'=>$tbody, 'tfoot'=>$tfoot]);
        endif;
    }
}
?>