<?php
class QualityReport extends MY_Controller
{
    private $qc_report_page = "report/qc_report/index";
    private $batch_tracability = "report/qc_report/batch_tracability";
    private $batch_history = "report/qc_report/batch_history";
	private $supplier_rating = "report/qc_report/supplier_rating";
	private $vendor_rating = "report/qc_report/vendor_rating";
	private $measuring_thread = "report/qc_report/measuring_thread";
	private $measuring_instrument = "report/qc_report/measuring_instrument";
    private $rejection_rework_monitoring = "report/production/rejection_rework_monitoring";
    private $rejection_monitoring = "report/qc_report/rejection_monitoring";
    private $nc_report = "report/qc_report/nc_report";
    private $vendor_gauge = "report/qc_report/vendor_gauge";
	private $rm_testing_register = "report/qc_report/rm_testing_register";
	private $inprocess_inspection = "report/qc_report/inprocess_inspection";
	private $rejection_analisys = "report/qc_report/rejection_analisys";

    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Quality Report";
		$this->data['headData']->controller = "reports/qualityReport";
		$this->data['floatingMenu'] = '';//$this->load->view('report/qc_report/floating_menu',[],true);
	    $this->data['refTypes'] = array('','GRN','Purchase Invoice','Material Issue','Delivery Challan','Sales Invoice','Manual Manage Stock','Production Finish','Visual Inspection','Store Transfer','Return Stock From Production','In Challan','Out Challan','Tools Issue','Stock Journal','Packing Material','Packing Product');
	}
	
	public function index(){
		$this->data['headData']->pageTitle = $this->data['pageHeader'] = 'QUALITY REPORT';
        $this->load->view($this->qc_report_page,$this->data);
    }

	/* Batch History */
	public function batchHistory(){
        $this->data['headData']->pageTitle = $this->data['pageHeader'] = 'BATCH WISE HISTORY REPORT';
		$this->data['batchData'] = $this->qualityReports->getBatchNoListForHistory();
        $this->load->view($this->batch_history,$this->data);
    }

	public function getBatchHistory(){
        $data = $this->input->post();
        $batchTracData = $this->qualityReports->getBatchHistory($data);
		$tbodyData=""; $tfootData="";$i=1;$stockQty=0;
		foreach($batchTracData as $row):
			$refType = ($row->ref_type > 0)?$this->data['stockTypes'][$row->ref_type] : "General Issue";
			$tbodyData .= '<tr>
				<td class="text-center">'.$i++.'</td>
				<td>'.formatDate($row->ref_date).'</td>
                <td>'.$row->ref_no.'</td>
				<td>'.$refType.'</td>
				<td>'.$row->item_name.'</td>
				<td>'.(($row->trans_type == 1)?floatVal($row->qty):"").'</td>
				<td>'.(($row->trans_type == 2)?abs(floatVal($row->qty)):"").'</td>';
			$tbodyData .='</tr>';
			$stockQty += floatVal($row->qty);
		endforeach;
		$tfootData .= '<tr class="thead-info">
					<th colspan="5" style="text-align:right !important;">Current Stock</th>
					<th colspan="2">'.round($stockQty,2).'</th>
					</tr>';
		$this->printJson(['status'=>1,"tbodyData"=>$tbodyData,"tfootData"=>$tfootData]);
	}

	/* Batch Tracability Report */
	public function batchTracability(){
        $this->data['headData']->pageTitle = $this->data['pageHeader'] = 'Batch Tracability Report';
		$this->data['itemData'] = $this->item->getItemList(3);
        $this->load->view($this->batch_tracability,$this->data);
    }

	public function getBatchList(){
		$data = $this->input->post();
		$batchData = $this->qualityReports->getBatchListByItem($data['item_id']);
		$itemList="<option value=''>Select Batch</option>";
		foreach($batchData as $row):
			$itemList.='<option value="'.$row->batch_no.'">'.$row->batch_no.'</option>';
		endforeach;
		$this->printJson(['status'=>1,"itemList"=>$itemList]);
	}

	public function getBatchTracability(){
        $data = $this->input->post();
        $batchTracData = $this->qualityReports->getBatchTracability($data);
		$tbodyData=""; $tfootData="";$i=1;$stockQty=0;
		foreach($batchTracData as $row):
			$refType = ($row->ref_type > 0)?$this->data['stockTypes'][$row->ref_type] : "General Issue";
			$reference="Purchase Material Arrived";
			if($row->ref_type==3)
			{
				$refData = $this->qualityReports->getMIfgName($row->ref_id);
				if(!empty($refData)){ $reference = $refData->item_name.' <a href="'.base_url('jobcard').'/printDetailedRouteCard/'.$refData->job_id.'" target="_blank">('.$refData->job_prefix.$refData->job_no.')</a>'; } 
				else { $reference = "General Issue"; }
			}
			if($row->ref_type==10)
			{
				$returnData = $this->qualityReports->getReturnfgName($row->ref_id);
				$reference = $returnData->item_name.' ('.$returnData->job_prefix.$returnData->job_no.')';
			}
			$tbodyData .= '<tr>
				<td class="text-center">'.$i++.'</td>
				<td>'.formatDate($row->ref_date).'</td>
                <td>'.$row->ref_no.'</td>
				<td>'.$refType.'</td>
				<td>'.$reference.'</td>
				<td>'.(($row->trans_type == 1)?floatVal($row->qty):"").'</td>
				<td>'.(($row->trans_type == 2)?abs(floatVal($row->qty)):"").'</td>';
			$tbodyData .='</tr>';
			$stockQty += floatVal($row->qty);
		endforeach;
		$tfootData .= '<tr class="thead-info">
					<th colspan="5" style="text-align:right !important;">Current Stock</th>
					<th colspan="2">'.round($stockQty,2).'</th>
					</tr>';
		$this->printJson(['status'=>1,"tbodyData"=>$tbodyData,"tfootData"=>$tfootData]);
	}

	/* Supplier Rating Report */
	public function supplierRating(){
        $this->data['headData']->pageTitle = $this->data['pageHeader'] = 'SUPPLIER RATING REPORT';
		$this->data['supplierData'] = $this->party->getSupplierList();
        $this->load->view($this->supplier_rating,$this->data);
    }

	public function getSupplierRating(){
		$data = $this->input->post();
		$errorMessage = array();

		if($data['to_date'] < $data['from_date'])
			$errorMessage['to_date'] = "Invalid date.";

		$supplierItems = $this->qualityReports->getSupplierRatingItems($data);
		$supplierDetails = $this->party->getParty($data['party_id']);

		$tbodyData=""; $tfootData="";$i=1; 
		$tsq=0;$tn=0;$tq1=0;$tq2=0;$tq3=0;$tt1=0;$tt2=0;$tt3=0;

		foreach($supplierItems as $items):
			$data['item_id']=$items->id;
			$qtyData = $this->qualityReports->getInspectedMaterialGBJ($data);
				
			$supplierData = $this->qualityReports->getSupplierRating($data);
			$qty=0; $t1=0; $t2=0; $t3=0; $remark="";$wdate ="";

			foreach($supplierData as $row):
				$qty+= $row->qty;
				$wdate = date('Y-m-d',strtotime("+7 day", strtotime($row->delivery_date)));
				
				if($row->grn_date <= $row->delivery_date){$t1 += $row->qty;}
				elseif($row->grn_date <= $wdate){$t2 += $row->qty;}
				else{$t3 += $row->qty;}
				
				$remark=$row->remark;
			endforeach;

			$tbodyData .= '<tr>
        					<td class="text-center">'.$i++.'</td>
        					<td>'.$items->item_name.'</td>
        					<td>'.$qty.'</td>
        					<td>'.$qtyData->insQty.'</td>
        					<td>'.$qtyData->aQty.'</td>
        					<td>'.$qtyData->udQty.'</td>
        					<td>'.$qtyData->rQty.'</td>
        					<td>'.$t1.'</td>
        					<td>'.$t2.'</td>
        					<td>'.$t3.'</td>
        					<td></td>
        					<td>'.$remark.'</td>
    					</tr>';
			$tsq+=$qty;$tn+=$qtyData->insQty;
			$tq1+=$qtyData->aQty;$tq2+=$qtyData->udQty;$tq3+=$qtyData->rQty;
			$tt1+=$t1;$tt2+=$t2;$tt3+=$t3;
		endforeach;
		
		$deliveryRate = 0;
		if(!empty($tt1) && !empty($tsq)){
		    $deliveryRate = round(((($tt1 + (0.75 * $tt2) + (0 * $tt3) ) / $tsq ) * 100),2);
		}
		$qcRate = 0;
		if(!empty($tq1) && !empty($tsq)){
		    $qcRate = round(((($tq1 + (0.5 * $tq2) + (0 * $tq3) ) / $tsq) * 100),2);
		}
		$theadData = '<tr class="text-center">
							<th colspan="10">SUPPLIER RATING REPORT</th>
							<th colspan="2">F PU 06 (00/01.06.20)</th>
						</tr>
						<tr>
							<th colspan="4">Supplier\'s Name : '.$supplierDetails->party_name.'</th>
							<th colspan="4">Period : '.date('d-m-Y',strtotime($data['from_date'])).' TO '.date('d-m-Y',strtotime($data['to_date'])).'</th>
							<th colspan="4">Date : '.date('d-m-Y').'</th>
						</tr>
						<tr class="text-center">
							<th rowspan="3" style="min-width:50px;">Sr No.</th>
							<th rowspan="3" style="min-width:100px;">Item Description</th>
							<th rowspan="3" style="min-width:50px;">Quantity Supplied</th>
							<th rowspan="3" style="min-width:50px;">Inspected Qty.<br />(N)</th>
							<th colspan="3">Quality Rating : '.$qcRate.'%</th>
							<th colspan="3">Delivery Rating : '.$deliveryRate.'%</th>
							<th rowspan="3" style="min-width:50px;">Premium Freight</th>
							<th rowspan="3" style="min-width:100px;">Remark</th>
						</tr>
						<tr class="text-center">
							<th colspan="3">Quantity</th>
							<th colspan="3">Quantity Received</th>
						</tr>
						<tr class="text-center">
							<th>Accepted<br>(Q1)</th>
							<th>Accept.U/D<br>(Q2)</th>
							<th>Rejected<br>(Q3)</th>
							<th>Intime<br>(T1)</th>
							<th>Late upto 1 week<br>(T2)</th>
							<th>Late beyond week<br>(T3)</th>
						</tr>';
		
		$tfootData = '<tr>
    					<th colspan="2" class="text-right">Total</th>
    					<th>'.$tsq.'</th>
    					<th>'.$tn.'</th>
    					<th>'.$tq1.'</th>
    					<th>'.$tq2.'</th>
    					<th>'.$tq3.'</th>
    					<th>'.$tt1.'</th>
    					<th>'.$tt2.'</th>
    					<th>'.$tt3.'</th>
    					<th></th>
    					<th></th>
					</tr>';

		if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
			$this->printJson(['status'=>1,"theadData"=>$theadData,"tbodyData"=>$tbodyData,"tfootData"=>$tfootData]);
		endif;
	}

	/* Vendor Rating report  */
	public function vendorRating(){
		$this->data['headData']->pageTitle = $this->data['pageHeader'] = 'VENDOR RATING REPORT';
		$this->data['vendorData'] = $this->party->getVendorList();
        $this->load->view($this->vendor_rating,$this->data);
	}
	
	/* Vendor Rating report  */
	public function getVendorRatingData(){
		$data =$this->input->post();
		$challanData = $this->qualityReports->getVendorChallanData($data);
		$vendorData = $this->party->getParty($data['party_id']);
		$data = $this->input->post();
		$errorMessage = array();

		if($data['to_date'] < $data['from_date'])
			$errorMessage['to_date'] = "Invalid date.";

		$tbodyData=""; $tfootData="";$i=1; 
		$tIn=0; $tOk=0; $tRej=0; $inTime=0; $lt=0; $ltb=0;
		foreach($challanData as $row):
				$rejData = $this->qualityReports->getVendorRejData(['job_card_id'=>$row->job_card_id,'vendor_id'=>$data['party_id']]);
				$rejQty = !empty($rejData->rej_qty)?$rejData->rej_qty:0;
				$tbodyData .= '<tr>
					<td class="text-center">'.$i++.'</td>
					<td>['.$row->item_code.'] '.$row->item_name.'</td>
					<td>'.$row->in_qty.'</td>
					<td>'.$row->in_qty.'</td>
					<td>'.($row->in_qty - $rejQty).'</td>
					<td></td>
					<td>'.$rejQty.'</td>
					<td>'.floatval($row->in_time_qty).'</td>
					<td>'.floatval($row->lt_qty).'</td>
					<td>'.floatval($row->lt_beyond_qty).'</td>
					<td></td>
				</tr>';
			$tIn += $row->in_qty; 
			$tOk += ($row->in_qty - $rejQty); 
			$tRej += $rejQty; 
			$inTime += $row->in_time_qty; 
			$lt += $row->lt_qty;
			$ltb += $row->lt_beyond_qty;
		endforeach;
		
		$deliveryRate = 0;
		if(!empty($inTime) && !empty($tIn)){
		    $deliveryRate = round(((($inTime + (0.75 * $lt) + (0 * $ltb) ) / $tIn ) * 100),2);
		}
		$qcRate = 0;
		if(!empty($tRej) && !empty($tIn)){
		    $qcRate = round((($tRej / $tIn) * 100),2);
		}
		
		$theadData = '<tr class="text-center">
    			<th colspan="10">VENDOR RATING REPORT</th>
    			<th colspan="2">F PL 03 (00/01.06.20)</th>
    		</tr>
    		<tr>
    			<th colspan="4">Vendor Name : '.$vendorData->party_name.'</th>
    			<th colspan="4">Period : '.date('d-m-Y',strtotime($data['from_date'])).' TO '.date('d-m-Y',strtotime($data['to_date'])).'</th>
    			<th colspan="4">Date : '.date('d-m-Y').'</th>
    		</tr>
    		<tr class="text-center">
    			<th rowspan="3" style="min-width:50px;">Sr No.</th>
    			<th rowspan="3" style="min-width:100px;">Item Description</th>
    			<th rowspan="3" style="min-width:50px;">Quantity Supplied</th>
    			<th rowspan="3" style="min-width:50px;">Inspected Qty.<br />(N)</th>
    			<th colspan="3">Quality Rating : '.$qcRate.'%</th>
    			<th colspan="3">Delivery Rating : '.$deliveryRate.'%</th>
    			<th rowspan="3" style="min-width:100px;">Remark</th>
    		</tr>
    		<tr class="text-center">
    			<th colspan="3">Quantity</th>
    			<th colspan="3">Quantity Received</th>
    		</tr>
    		<tr class="text-center">
    			<th>Accepted<br>(Q1)</th>
    			<th>Accept.U/D<br>(Q2)</th>
    			<th>Rejected<br>(Q3)</th>
    			<th>Intime<br>(T1)</th>
    			<th>Late upto 1 week<br>(T2)</th>
    			<th>Late beyond week<br>(T3)</th>
    		</tr>';
    		
		$tfootData = '<tr>
				<th colspan="2" class="text-right">Total</th>
				<th>'.$tIn.'</th>
				<th>'.$tIn.'</th>
				<th>'.$tOk.'</th>
				<th></th>
				<th>'.$tRej.'</th>
				<th>'.$inTime.'</th>
				<th>'.$lt.'</th>
				<th>'.$ltb.'</th>
				<th></th>
			</tr>';
	
		if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
			$this->printJson(['status'=>1,"tbodyData"=>$tbodyData,"theadData"=>$theadData,"tfootData"=>$tfootData]);
		endif;
	}

	/* Measuring Thread Data */
	public function measuringThread(){
		$this->data['headData']->pageTitle = $this->data['pageHeader'] = 'LIST OF MEASURING DEVICES(GAUGES)';
		$this->data['threadData'] = $this->qualityReports->getMeasuringDevice(7);
        $this->load->view($this->measuring_thread,$this->data);
	}

	/* Measuring Instrument Data */
	public function measuringInstrument(){
		$this->data['headData']->pageTitle = $this->data['pageHeader'] = 'List Of Measuring Device(Instruments)';
		$this->data['instrumentsData'] = $this->qualityReports->getMeasuringDevice(6);
        $this->load->view($this->measuring_instrument,$this->data);
	}
	
    /* Rejection Rework Monitoring  avruti*/
    public function rejectionReworkMonitoring(){
        $this->data['headData']->pageTitle = $this->data['pageHeader'] = 'REJECTION & REWORK MONITORING REPORT';
        $this->data['itemDataList'] = $this->item->getItemList(1);
        $this->load->view($this->rejection_rework_monitoring,$this->data);
    }

    public function getRejectionReworkMonitoring(){
        $data = $this->input->post();
        $errorMessage = array();
		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
			$data['rtype'] = 1;
            $rejectionData = $this->productionReportsNew->getRejectionReworkMonitoring($data);
            $this->printJson($rejectionData);
        endif;
    }

	/* Rejection Monitoring  avruti*/
	public function rejectionMonitoring(){
		$this->data['headData']->pageTitle = $this->data['pageHeader'] = 'REJECTION MONITORING REPORT';
		$this->data['itemDataList'] = $this->item->getItemList(1);
		$this->data['jobcardData'] = $this->jobcard_v3->getJobcardList();
		$this->load->view($this->rejection_monitoring,$this->data);
	}

    public function getRejectionMonitoring(){
		$data = $this->input->post();
		$errorMessage = array();
		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
			$rejectionData = $this->qualityReports->getRejRwProdLogV2($data);
 			
		    $tbody = ''; $tfoot = '';
		    if (!empty($rejectionData)) :
			 	$i = 1;$footRwspan=13;
			 	$totalRejectCost = 0;$totalRejectQty = 0; $totalRwQty = 0;
			 	foreach ($rejectionData as $row)
			 	{
					$item_price = $row->price;
					if ($row->currency != 'INR') :
						$inr = $this->salesReportModel->getCurrencyConversion($row->currency);
						if (!empty($inr)){$item_price = $inr[0]->inrrate * $row->price;}
					endif;
					
					$rejColumns = '<td></td><td></td><td></td><td></td><td></td><td></td>'; 
					$rwColumns = '<td></td><td></td><td></td><td></td><td></td>';
					
					if($row->manag_type == 1)
					{
						$rejectCost = round($row->rejrw_qty * $item_price,2);
						$rejection_reason = (!empty($row->reason_name)) ? $row->reason_name : "";
						$belongs_to = (!empty($row->belongs_to_name)) ? $row->belongs_to_name : "";
						
						$rejColumns = '<td>' . $row->rejrw_qty . '</td>
								<td>' . $rejection_reason . '</td>
								<td>' . $row->remark . '</td>
								<td>' . $belongs_to . '</td>
								<td>' . (!empty($row->vendor_name) ? $row->vendor_name : 'IN HOUSE') . '</td>
								<td>' . $rejectCost . '</td>';
								
						$totalRejectQty += $row->rejrw_qty;
					}
					if($row->manag_type == 2)
					{
						$rw_reason = (!empty($row->reason_name)) ? $row->reason_name : "";
						$belongs_to = (!empty($row->belongs_to_name)) ? $row->belongs_to_name : "";
						$rwColumns = '<td>' . $row->rejrw_qty . '</td>
										<td>' . $rw_reason . '</td>
										<td>' . $row->remark . '</td>
										<td>' . $belongs_to . '</td>
										<td>' . (!empty($row->vendor_name) ? $row->vendor_name : 'IN HOUSE') . '</td>';
						$totalRwQty += $row->rejrw_qty;
					}

					$tbody .= '<tr>
						<td>' . $i++ . '</td>
						<td>' . formatDate($row->log_date) . '</td>
						<td>' . $row->item_code . '</td>
						<td>' . $row->process_name . '</td>
						<td>' . $row->shift_name . '</td>
						<td>' . $row->machine_code . '</td>
						<td>' . $row->emp_name . '</td>
						<td>'.getPrefixNumber($row->job_prefix,$row->job_no).'</td>';
					
					if(!empty($data['rtype']) AND $data['rtype'] == 2){
					    $tbody .= $rwColumns;
					}else{
					    $footRwspan = 8;
					}
					$tbody .= $rejColumns;
					
					$tbody .= '</tr>';
					$totalRejectCost += $rejectCost;
			 	}
	 
			 	$tfoot .= '<tr class="thead-info">
				    <th colspan="'.$footRwspan.'" class="text-right">Total Reject Qty.</th>
				    <th>' . $totalRejectQty . '</th>
			 		<th colspan="4" class="text-right">Total Reject Cost</th>
			 		<th>' . round($totalRejectCost,2) . '</th>
			 	</tr>';
			endif;
			 
			$this->printJson(['status' => 1, 'tbody' => $tbody, 'tfoot' => $tfoot]);
		endif;
	}
	
	public function ncReport(){
		$this->data['headData']->pageTitle = $this->data['pageHeader'] = ' Final Inspection NC Report';
		$this->data['jobcardData'] = $this->jobcard_v3->getJobcardList();
        $this->load->view($this->nc_report,$this->data);
	}

	public function getNCReportData($jsonData=""){
	    if(!empty($jsonData)):
            $data = (Array) decodeURL($jsonData);
        else:
            $data = $this->input->post();
        endif;
        
		$reportdata = $this->qualityReports->getNCReportData($data);
		
		$tbodyData="";
		foreach($reportdata as $row):
			$rejection_qty=[];$rejection_reason=[];$rejection_from=[];
			$rework_qty=[];$rework_reason=[];$rework_from=[];
			$rej_reason=(!empty($row->rej_reason))?json_decode($row->rej_reason):'';
			$rejrwnData = $this->qualityReports->getRejRwData(['log_id'=>$row->id]);
			
			if(!empty($rejrwnData)){
				$i=0;
				foreach($rejrwnData as $rr){
					if($rr->manag_type == 1){
					    $rejection_qty[] = (!empty($rr->qty)) ? $rr->qty : "";
					    $rejection_reason[] = (!empty($rr->reason_name)) ? $rr->reason_name : "";
					    $rejection_from[]=(!empty($rr->dept_name)) ? $rr->dept_name : "Raw Material";
					    //$rejection_from[]=(!empty($rr->belongs_to_name)) ? $rr->belongs_to_name : "";
					}
					if($rr->manag_type == 2){
					    $rework_qty[] = (!empty($rr->qty)) ? $rr->qty : "";
					    $rework_reason[] = (!empty($rr->reason_name)) ? $rr->reason_name : "";
					    $rework_from[]=(!empty($rr->dept_name)) ? $rr->dept_name : "Raw Material";
					    //$rework_from[]=(!empty($rr->belongs_to_name)) ? $rr->belongs_to_name : "";
					}
				}
			}
			
			$tbodyData .= '<tr>
					<td>'.formatDate($row->log_date).'</td>
					<td>'.$row->item_code.'</td>
					<td>'.$row->inspection_type_name.'</td>
					<td>'.getPrefixNumber($row->job_prefix,$row->job_no).'</td>
					<td>'.$row->production_qty.'</td>
					<td>'.$row->ok_qty.'</td>
					<!--<td>'.$row->rw_qty.'</td>-->
					<td>'.implode('<hr style="margin: 2px auto;">',$rework_qty).'</td>
					<td>'.implode('<hr style="margin: 2px auto;">',$rework_reason).'</td>
					<td>'.implode('<hr style="margin: 2px auto;">',$rework_from).'</td>
					<td>'.$row->rej_qty.'</td>
					<td>'.implode('<hr style="margin: 2px auto;">',$rejection_qty).'</td>
					<td>'.implode('<hr style="margin: 2px auto;">',$rejection_reason).'</td>
					<td>'.implode('<hr style="margin: 2px auto;">',$rejection_from).'</td>
					<td>'.$row->emp_name.'</td>
					<td>'.$row->supervisor.'</td>
					<td>'.number_format($row->rej_qty*$row->price,2).'</td>
					<td>'.number_format(($row->production_time/60),2).'</td>';
			$tbodyData .='</tr>';
		endforeach;
		
				if($data['type'] == 2){

			$theadData = '<thead>
                            <tr class="text-center">
                                <th colspan="13">Final Inspection NC Report</th>
								<th colspan="3">FQA 06<br>00(01/06/2020)</th>
                            </tr>
							<tr>
								<th>Date</th>
								<th>Part</th>
								<th>Inspection Type</th>
								<th>Jobcard</th>
								<th>Inspected Qty.</th>
								<th>OK Qty.</th>
								<th>Rework Qty.</th>
                                <th>Rework Reason</th>
                                <th>Defect belong to Rework</th>
                                <th>Rejection Qty</th>
                                <th>Rejection Reason</th>
                                <th>Defect belong to Rejection</th>
                                <th>Inspector</th>
                                <th>Supervisor</th>
                                <th>Rejection Cost</th>
                                <th>Inspection Time(In Hours.)</th>
							</tr>
						</thead>';

			$response = '<table border="1">
							<thead>'.$theadData.'</thead>
							<tbody>'.$tbodyData.'</tbody>
						</table>';
						
			$xls_filename = 'Final_Inspection_NC_Report_'.date('d_m_Y').'.xls';
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment; filename='.$xls_filename);
			header('Pragma: no-cache');
			header('Expires: 0');
			echo $response;
		} else {
			$this->printJson(['status'=>1,"tbodyData"=>$tbodyData]);
		}
	}

    /* Vendor Gauge In Out Challan Report Data * Created By Meghavi @10/09/2022*/
	public function vendorGaugeInOut(){
		$this->data['headData']->pageTitle = $this->data['pageHeader'] = 'VENDOR GAUGE IN OUT CHALLAN';
		$this->data['vendorData'] = $this->party->getVendorList();
        $this->load->view($this->vendor_gauge,$this->data);
	}

	public function getVendorGaugeData(){
        $data = $this->input->post();
        $errorMessage = array();
		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $vendorGaugeData = $this->qualityReports->getVendorGaugeData($data);
            $tbody=''; $i=1; $blankInTd='<td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>';
           
            if(!empty($vendorGaugeData)):
                foreach($vendorGaugeData as $row): 
					$partyName = (!empty($row->party_name)) ? $row->party_name : 'In House';
					
					$return['item_id'] = $row->item_id; $return['ref_id'] = $row->id; $return['trans_type'] = 1;
                    $returnData = $this->outChallan->getReceiveItemTrans($return)['result'];
                    $returnCount = count($returnData);

                    $tbody .= '<tr>
                        <td>'.$i++.'</td>
                        <td>'.getPrefixNumber($row->challan_prefix, $row->challan_no).'</td>
                        <td>'.formatDate($row->challan_date).'</td>
                        <td>'.$partyName.'</td>
                        <td>'.$row->item_name.'</td>
                        <td>'.floatVal($row->qty).'</td>';

						if($returnCount > 0):
                            $j=1; $totalPend = 0;
                            foreach($returnData as $recRow):
								$pending_qty = abs($row->qty) - (abs($recRow->qty) + $totalPend);
                                $tbody.='<td>'.formatDate($recRow->ref_date).'</td>
                                            <td>'.abs($recRow->qty).'</td>
                                            <td>'.floatval($pending_qty).'</td>';
                                if($j != $returnCount){$tbody.='</tr><tr>'.$blankInTd; }
                                $j++; $totalPend += $recRow->qty;
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
    
    /* Vendor Gauge In Out Challan Report Data *
	 Created By Meghavi @10/09/2022*/
	public function rmTestingRegister(){
		$this->data['headData']->pageTitle = $this->data['pageHeader'] = 'RAW MATERIAL TESTING REGISTER';
        $this->load->view($this->rm_testing_register,$this->data);
	}

	public function getRmTestingRegister(){
        $data = $this->input->post();
        $errorMessage = array();
		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $rmTestingRegisterData = $this->qualityReports->getRmTestingRegister($data);
            $tbody=''; $i=1;
			
            if(!empty($rmTestingRegisterData)): 
                foreach($rmTestingRegisterData as $row):  
					$row->product_code = ''; $c=0;
					if(!empty($row->fgitem_id)):
						$la = explode(",",$row->fgitem_id);
						if(!empty($la)){
							foreach($la as $fgid){
								$fg = $this->grnModel->getFinishGoods($fgid);
								if(!empty($fg)):
									if($c==0){
										$row->product_code .= $fg->item_code;
									}else{
										$row->product_code .= '<br>'.$fg->item_code;
									}$c++;
								else:
									$row->product_code = "";
								endif;
							}
						}
					endif; 
                    $tbody .= '<tr>
                        <td>'.$i++.'</td>
                        <td>'.getPrefixNumber($row->grn_prefix,$row->grn_no).'</td>
                        <td>'.formatDate($row->grn_date).'</td>
                        <td>'.$row->party_name.'</td>
                        <td>'.$row->material_grade.'</td>
                        <td>'.$row->item_name.'</td>
                        <td>'.$row->batch_no.'</td>
                        <td>'.$row->qty.'</td>
                        <td>'.$row->unit_name.'</td>
                        <td>'.$row->product_code.'</td>
                        <td>'.$row->name_of_agency.'</td>
                        <td>'.$row->test_description.'</td>
                        <td>'.$row->sample_qty.'</td>
                        <td>'.$row->test_report_no.'</td>
                        <td>'.$row->test_remark.'</td>
                        <td>'.$row->test_result.'</td>
                        <td>'.$row->inspector_name.'</td>
                        <td>'.$row->mill_tc.'</td>
                    </tr>';
                endforeach;
            endif;
			
            $this->printJson(['status'=>1, 'tbody'=>$tbody]);
        endif;
    }
    
    public function inprocessInspection(){
        $this->data['headData']->pageTitle = $this->data['pageHeader'] = 'IN PROCESS INSPECTION REPORT ';
		$this->data['jobData'] = $this->jobcard_v3->getJobcardList();
        $this->load->view($this->inprocess_inspection,$this->data);
    }
    
	public function getProcessList(){ 
        $job_card_id = $this->input->post('job_card_id');  
		$processData = $this->qualityReports->getJobCardProcessList($job_card_id); 
		$options = '<option value="">Select Process</option>';
		foreach($processData as $row):
			$options .= '<option value="'.$row->id.'">'.$row->process_name.'</option>';
		endforeach;
        $this->printJson(['status'=>1, 'options'=>$options]);
	}

	public function getInProcessInspectionData(){
        $data = $this->input->post();

		$masterData = $this->qualityReports->getInspectionForPrint(['job_card_id'=>$data['job_card_id'], 'process_id'=>$data['process_id']]);
        $tbodyDataA="";
		if(!empty($masterData)): 
			$tbodyDataA.= '<tr>
						<td>'.$masterData->item_code.'</td>
						<td>'.$masterData->process_name.'</td>
						<td>'.$masterData->emp_name.'</td>
						<td>'.$masterData->shift_name.'</td>
						<td>'.(!empty($masterData->machine_code)?'['.$masterData->machine_code.'] '.$masterData->machine_name:$masterData->machine_name).'</td>
						<td>'.getPrefixNumber($masterData->job_prefix,$masterData->job_no).'</td>
						<td>'.formatDate($masterData->trans_date).'</td>';
			$tbodyDataA.='</tr>';
		else:
			$tbodyDataA.= '<tr><td colspan="8" style="text-align:center;">No Data Found</td></tr>';
		endif;		


		$pfcProcess = $this->item->getPrdProcessDataProductProcessWise(['item_id'=>$data['item_id'], 'process_id'=>$data['process_id'], 'pfc_rev_no'=>$data['pfc_rev_no']]);
		$paramData = $this->controlPlanV2->getCPDimenstion(['item_id'=>$data['item_id'], 'process_no'=>$pfcProcess->pfc_process, 'control_method'=>'PIR', 'responsibility'=>'INSP', 'rev_no'=>$data['cp_rev_no']]);
		$objData = [];
		if(!empty($masterData->grn_id)){
			$objData = $this->pir->getPirDataForPrint(['grn_id'=>$masterData->grn_id,'machine_id'=>$masterData->party_id, 'grn_trans_id'=>$masterData->grn_trans_id, 'trans_date'=>$masterData->trans_date]);
		}
		

		$rcount = count($objData);
		$paramIds = explode(',', $masterData->parameter_ids);

		$theadDataB=''; $tbodyDataB=""; $i=1; $observation=''; $tblsample='';

		if(!empty($paramData)):
            foreach($paramData as $row):

                if (in_array($row->id, $paramIds)) :
                    $os = json_decode($masterData->observation_sample);
                    $diamention ='';
                    if($row->requirement==1){ $diamention = $row->min_req.'/'.$row->max_req ; }
                    if($row->requirement==2){ $diamention = $row->min_req.' '.$row->other_req ; }
                    if($row->requirement==3){ $diamention = $row->max_req.' '.$row->other_req ; }
                    if($row->requirement==4){ $diamention = $row->other_req ; }
                    $char_class=''; if(!empty($row->char_class)){ $char_class='<img src="' . base_url('assets/images/symbols/'.$row->char_class.'.png') . '" style="width:20px;display:inline-block;vertical-align:middle;" />'; }

                    $tbodyDataB .= '<tr>
                        <td style="text-align:center;" height="30">'.$i.'</td>
                        <td>'.$row->product_param.'</td>
                        <td>'.$diamention.'</td>
                        <td style="text-align:center;">'.$row->min_req.'</td>
                        <td style="text-align:center;">'.$row->max_req.'</td>
                        <td>'.$row->ipr_measur_tech.'</td>';
                    
                        foreach($objData as $read):
                            if($i==1){
                                $trans_date = (!empty($read->result)?date("h:i A",strtotime($read->result)):'');
                                $observation .= '<th style="text-align:center;">'.$trans_date.'</th>';
                            }
                            $obj = New StdClass; 
                            $obj = json_decode($read->observation_sample);
                            if(!empty($obj->{$row->id})):
                                $tbodyDataB .= '<td style="text-align:center;">'.$obj->{$row->id}[0].'</td>';
                            endif;
                        endforeach;
                        $tbodyDataB .= '</tr>';

                    $i++;
                endif;
            endforeach;
        else:
            $tbodyDataB = '<tr><td colspan="7" style="text-align:center;">No Data Found</td></tr>';
        endif;

		$theadDataB.='<tr style="text-align:center;">
            <th rowspan="2" style="width:5%;">#</th>
            <th rowspan="2">Product Characteristics</th>
            <th rowspan="2">Specification</th>
            <th colspan="2">Tolerance</th>
            <th rowspan="2">Evaluation <br>Measurement Techniques</th>
			<th colspan="'.$rcount.'">Observation</th>			
		</tr>
		<tr style="text-align:center;">
		    <th>LCL</th>
            <th>UCL</th>
		 	'.$observation.'
		</tr>';

		$this->printJson(['status'=>1, "tbodyDataA"=>$tbodyDataA, "tbodyDataB"=>$tbodyDataB, "theadDataB"=>$theadDataB]);
    }

	public function rejectionAnalisys(){
		$this->data['headData']->pageTitle = $this->data['pageHeader'] = 'REJECTION ANALISYS REPORT ';
		$this->data['itemData'] = $this->item->getItemList(1);
		$this->data['processList'] = $this->process->getProcessList();
		$this->load->view($this->rejection_analisys,$this->data);
	}

	public function getRejectionAnalisysData(){
		$data = $this->input->post();
		$rejectionSummary = $this->qualityReports->getRejectionSummary($data);

		$tbody = '';$i=1; $total_prod_qty = 0; $total_rej_qty = 0; $trans_rej_total = 0;
		foreach($rejectionSummary as $rejSum):
			$data['item_id'] = $rejSum->product_id;
			$transaction = $this->qualityReports->getRejectionTransaction($data);

			$qualitySummaryRate = (!empty($rejSum->production_qty) && !empty($rejSum->rej_qty))?(round(((($rejSum->production_qty - $rejSum->rej_qty) * 100)/ $rejSum->production_qty),2)):0;
			$rejSummaryRate = (!empty($rejSum->production_qty) && !empty($rejSum->rej_qty))?(round((($rejSum->rej_qty * 100)/ $rejSum->production_qty),2)):0;
			
			if(!empty($transaction)):
				$j = 0;$transRowFirst = ''; $transRows = '';
				foreach($transaction as $row):
					if($j == 0):						
						$transRowFirst .= '<td class="text-center">'.$row->qty.'</td>';
						$transRowFirst .= '<td class="text-left">'.$row->reason_name.'</td>';
						$transRowFirst .= '<td class="text-left">'.$row->belongs_to_name.'</td>';
						$transRowFirst .= '<td class="text-left">'.$row->vendor_name.'</td>';
						$transRowFirst .= '<td class="text-left">'.$row->remark.'</td>';					
					else:
						$transRows .= '<tr>';
							$transRows .= '<td>-</td>';
							$transRows .= '<td>-</td>';
							$transRows .= '<td>-</td>';
							$transRows .= '<td>-</td>';
							$transRows .= '<td>-</td>';
							$transRows .= '<td>-</td>';
							$transRows .= '<td class="text-center">'.$row->qty.'</td>';
							$transRows .= '<td class="text-left">'.$row->reason_name.'</td>';
							$transRows .= '<td class="text-left">'.$row->belongs_to_name.'</td>';
							$transRows .= '<td class="text-left">'.$row->vendor_name.'</td>';
							$transRows .= '<td class="text-left">'.$row->remark.'</td>';
						$transRows .= '</tr>';
					endif;
					$trans_rej_total += $row->qty;
					$j++;
				endforeach;
			else:
				$transRowFirst = '<td></td>';
				$transRowFirst .= '<td></td>';
				$transRowFirst .= '<td></td>';
				$transRowFirst .= '<td></td>';
				$transRowFirst .= '<td></td>';
			endif;

			$tbody .= '<tr>';
				$tbody .= '<td class="text-center">'.$i++.'</td>';
				$tbody .= '<td class="text-left">'.$rejSum->item_code.'</td>';
				$tbody .= '<td class="text-center">'.$rejSum->production_qty.'</td>';
				$tbody .= '<td class="text-center">'.$rejSum->rej_qty.'</td>';
				$tbody .= '<td class="text-center">'.$qualitySummaryRate.'</td>';
				$tbody .= '<td class="text-center">'.$rejSummaryRate.'</td>';
				$tbody .= $transRowFirst;
			$tbody .= '</tr>';
			$tbody .= $transRows;

			$total_prod_qty += $rejSum->production_qty;
			$total_rej_qty += $rejSum->rej_qty;
		endforeach;

		$tfooter = '';

		$qualityRate = (!empty($total_prod_qty) && !empty($total_rej_qty))?round(((($total_prod_qty - $total_rej_qty) * 100)/ $total_prod_qty),2):0;
		$rejectionRate = (!empty($total_prod_qty) && !empty($total_rej_qty))?round((($total_rej_qty * 100)/ $total_prod_qty),2):0;
		$tfooter .= '<tr>';
			$tfooter .= '<th class="text-right" colspan="2">Total</th>';
			$tfooter .= '<th>'.$total_prod_qty.'</th>';
			$tfooter .= '<th>'.$total_rej_qty.'</th>';
			$tfooter .= '<th>'.$qualityRate.'</th>';
			$tfooter .= '<th>'.$rejectionRate.'</th>';
			$tfooter .= '<th>'.$trans_rej_total.'</th>';
			$tfooter .= '<th colspan="4"></th>';
		$tfooter .= '</tr>';

		$this->printJson(['status'=>1,'tbody'=>$tbody,'tfoot'=>$tfooter]);
	}
}
?>