<?php
class PurchaseReport extends MY_Controller
{
    private $indexPage = "report/purchase_report/index";
    private $raw_material = "report/purchase_report/raw_material";
    private $purchase_monitoring = "report/purchase_report/purchase_monitoring";
    private $purchase_inward = "report/purchase_report/purchase_inward";
    private $supplier_wise_item = "report/purchase_report/supplier_wise_item";
    private $grn_tracking = "report/purchase_report/grn_tracking";

    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Purchase Report";
		$this->data['headData']->controller = "reports/purchaseReport";
		$this->data['floatingMenu'] = '';//$this->load->view('report/purchase_report/floating_menu',[],true);
	}
	
	public function index(){
		$this->data['pageHeader'] = 'PURCHASE REPORT';
        $this->load->view($this->indexPage,$this->data);
    }

    /* RawMaterial Report */
	public function rawMaterialReport(){
		$this->data['headData']->pageTitle = "Raw Material Report";
        $this->data['pageHeader'] = 'RAW MATERIAL REPORT';
        $this->data['rawMaterialData'] = $this->storeReportModel->getrawMaterialReport();
        $this->load->view($this->raw_material,$this->data);
    }

    /* Purchase Monitoring Report */
    public function purchaseMonitoring(){
        $this->data['pageHeader'] = 'PURCHASE MONITORING REGISTER REPORT';
        $this->data['itemTypeData'] = $this->item->getItemGroup();
        $this->load->view($this->purchase_monitoring,$this->data);
    }

    public function getPurchaseMonitoring(){
        $data = $this->input->post();
        $errorMessage = array();
		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $purchaseData = $this->purchaseReport->getPurchaseMonitoring($data);
            $tbody="";$i=1;
            $blankInTd='<td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>';
            foreach($purchaseData as $row):
                    $data['item_id'] = $row->item_id;$data['grn_trans_id'] = $row->id;
                    
                    $receiptData = [];
                    if($data['report_type'] == 0){						
						$receiptData = $this->purchaseReport->getPurchaseReceipt($data);
					}else{
						$receiptData = $this->purchaseReport->getQcInstrumentReceive($data);
					}
                    
                    $receiptCount = count($receiptData);
                    
                    $tbody .= '<tr>
                        <td class="text-center">'.$i++.'</td>
                        <td>'.formatDate($row->po_date).'</td>
                        <td>'.$row->item_name.'</td>
                        <td>'.$row->party_name.'</td>
                        <td>'.getPrefixNumber($row->po_prefix,$row->po_no).'</td>
                        <td>'.floatval($row->qty).'</td>
                        <td>'.formatDate($row->delivery_date).'</td>';
                        if($receiptCount > 0):
                            $j=1;
                            foreach($receiptData as $recRow):
                                $totalAmt = $recRow->qty * $recRow->price;
                                
                                if($data['report_type'] == 0){						
									$challan_no = getPrefixNumber($recRow->grn_prefix,$recRow->grn_no);
									$qty = $recRow->qty;
								}else{
									$challan_no = $recRow->in_challan_no;
									$qty = $recRow->rec_qty;
								}
                                
                                $tbody.='<td>'.$challan_no.'</td>
                                            <td>'.formatDate($recRow->grn_date).'</td>
                                            <td>'.floatval($qty).'</td>
                                            <td>'.floatval($recRow->price).'</td>
                                            <td>'.floatval($totalAmt).'</td>';
                                if($j != $receiptCount){$tbody.='</tr><tr>'.$blankInTd; }
                                $j++;
                            endforeach;
                        else:
                            $tbody.='<td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>';
                        endif;
                        $tbody.='</tr>';
            endforeach;
            $this->printJson(['status'=>1, 'tbody'=>$tbody]);
        endif;
    }
    
    /* Purchase Inward Report */
    public function purchaseInward(){
        $this->data['pageHeader'] = 'PURCHASE INWARD REPORT';
        $this->data['itemTypeData'] = $this->item->getItemGroup();
        $this->load->view($this->purchase_inward,$this->data);
    }
    
    public function getPurchaseInward(){
        $data = $this->input->post();
		if($data['report_type'] == 0){
			$inwardData = $this->purchaseReport->getPurchaseInward($data);
		}else{			
			$inwardData = $this->purchaseReport->getQcInstrumentData($data);
		}
		
        $i=1; $tbody=''; $totalAmt=0; $poNo=''; $tfoot = ''; $totalQty=0; $totalItemPrice=0;  $totalDiscAmt=0; $total=0;
        if(!empty($inwardData)){
            foreach($inwardData as $row):
                $discAmt=0;
				$qty = ($data['report_type'] == 0) ? $row->qty : $row->rec_qty;
                $totalAmt = ($qty * $row->price);
                if($row->disc_per > 0){
                    $discAmt = round(($totalAmt * $row->disc_per) / 100,2);
                    $totalAmt = $totalAmt - $discAmt;
                }
                
                if(!empty($row->po_prefix) && !empty($row->po_no)){
                    $poNo = getPrefixNumber($row->po_prefix,$row->po_no);
                }
				$grn_no = ($data['report_type'] == 0) ? getPrefixNumber($row->grn_prefix,$row->grn_no) : $row->in_challan_no;
				
                $tbody .= '<tr>
                    <td>'.$i++.'</td>
                    <td>'.formatDate($row->grn_date).'</td>
                    <td>'.$grn_no.'</td>
                    <td>'.$poNo.'</td>
                    <td>'.formatDate($row->po_date).'</td>
                    <td>'.$row->item_name.'</td>
                    <td>'.$row->party_name.'</td>
                    <td>'.$qty.'</td>
                    <td>'.$row->price.'</td>
                    <td>'.$discAmt.'</td>
                    <td>'.$totalAmt.'</td>
                </tr>';
                $totalQty += $qty; $totalItemPrice += $row->price; $totalDiscAmt += $discAmt; $total += $totalAmt;
            endforeach;
            
            $tfoot = '<tr>
                <th colspan="7">Total</th>
                <th>'.round($totalQty).'</th>
                <th>'.round($totalItemPrice, 2).'</th>
                <th>'.round($totalDiscAmt, 2).'</th>
                <th>'.round($total, 2).'</th>
            </tr>';
        }
        
        $thead = '<tr>
            <th class="text-left" colspan="7">Purchase Inward</th>
            <th class="text-center">'.round($totalQty).'</th>
            <th class="text-center">'.round($totalItemPrice, 2).'</th>
            <th class="text-center">'.round($totalDiscAmt, 2).'</th>
            <th class="text-center">'.round($total, 2).'</th>
        </tr>
    	<tr class="text-center">
    		<th>#</th>
    		<th style="min-width:80px;">Date</th>
    		<th style="min-width:100px;">GRN No.</th>
    		<th style="min-width:80px;">P.O. No.</th>
    		<th style="min-width:80px;">P.O. Date</th>
    		<th style="min-width:100px;">Item Description</th>
    		<th style="min-width:100px;">Suppliers Name</th>
    		<th style="min-width:50px;">Qty.</th>
    		<th style="min-width:50px;">Price</th>
    		<th style="min-width:50px;">Disc Amount</th>
    		<th style="min-width:50px;">Total Amount</th>
    	</tr>';
        
        $this->printJson(['status'=>1, 'tbody'=>$tbody, 'tfoot'=>$tfoot, 'thead'=>$thead]);
    }
    
    public function getCategoryList(){
        $catData = $this->item->getCategoryList($this->input->post('item_type'));
        $options = ''; $i=1;
        foreach($catData as $row):
            $options.= '<option value="'.$row->id.'">'.$row->category_name.'</option>';
        endforeach;
        $this->printJson(['status' => 1, 'options' => $options]);
    }
    
    /* Supplier Wise Item Report  Created By Avruti @08/08/2022 */
    public function supplierWiseItem(){
        $this->data['headData']->pageTitle = 'Supplier Wise Item & Item Wise Supplier Report';
        $this->data['pageHeader'] = 'SUPPLIER WISE ITEM & ITEM WISE SUPPLIER REPORT';      
        $this->data['partyData'] = $this->party->getSupplierList();
        $this->data['itemData'] = $this->item->getItemLists('2,3');
        $this->load->view($this->supplier_wise_item,$this->data);
    }

 	//Created By Avruti @08/08/2022
    public function getSupplierWiseItem(){
        $data = $this->input->post();

        $purchaseData = $this->purchaseReport->getSupplierWiseItem($data);
        $tbody="";$i=1;
        foreach($purchaseData as $row):
                $tbody .= '<tr>
                    <td>'.$i++.'</td>
                    <td>'.$row->party_name.'</td>
                    <td>' . (!empty($row->item_code) ? '[' . $row->item_code . '] ' . $row->item_name : $row->item_name) . '</td>';
                $tbody.='</tr>';
        endforeach;
        $this->printJson(['status'=>1, 'tbody'=>$tbody]);
    }
    
    /* GRN Tracking Report  Created By Avruti @09/08/2022*/
    public function grnTracking(){
        $this->data['headData']->pageTitle = 'Grn Tracking Report';
        $this->data['pageHeader'] = 'GRN TRACKING REPORT';       
        $this->data['partyData'] = $this->party->getSupplierList();
        $this->load->view($this->grn_tracking,$this->data);
    }

 	//Created By Avruti @09/08/2022
    public function getGrnTracking(){
        $data = $this->input->post();
        $errorMessage = array();
		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $grnData = $this->purchaseReport->getGrnTracking($data);
            $tbody="";$i=1;
            foreach($grnData as $row):
                $inqDate =""; $approveDate="";
                if(!empty($row->approve_date)){ 
                    $grn_date = new DateTime(date('Y-m-d',strtotime($row->grn_date)));
                    $appDate = new DateTime(date('Y-m-d',strtotime($row->approve_date)));
                    $dueDays = $grn_date->diff($appDate);
                    $day = $dueDays->format('%d');
                    $daysDiff = ($day > 0) ? $day.' Days' : 'Same Day';
                    $approveDate = $daysDiff.'<br><small>('.formatDate($row->approve_date).')</small>'; 
                }

                if(!empty($row->inspection_date)){
                    $appDate = new DateTime(date('Y-m-d',strtotime($row->approve_date)));
                    $qcDate = new DateTime(date('Y-m-d',strtotime($row->inspection_date)));
                    $dueDays = $appDate->diff($qcDate);
                    $day = $dueDays->format('%d');
                    $daysDiff = ($day > 0) ? $day.' Days' : 'Same Day';
                    $inqDate = $daysDiff.'<br><small>('.formatDate($row->inspection_date).')</small>'; 
                }
                
                
                
                
                
                /*
                if(!empty($row->inspection_date)){ 
                    $inspectionDate = new DateTime(date('Y-m-d',strtotime($row->inspection_date)));
                    $grnDate = new DateTime(date('Y-m-d',strtotime($row->grn_date)));
                    $dueDays = $inspectionDate->diff($grnDate);
                    $day = $dueDays->format('%d');
                    $daysDiff = ($day > 0) ? $day.' Days' : 'Same Day';
                    $inqDate = formatDate($row->inspection_date).'<br>('.$daysDiff.')'; 
                }

                if(!empty($row->approve_date)){
                    $inspectionDate = new DateTime(date('Y-m-d',strtotime($row->inspection_date)));
                    $appDate = new DateTime(date('Y-m-d',strtotime($row->approve_date)));
                    $dueDays = $inspectionDate->diff($appDate);
                    $day = $dueDays->format('%d');
                    $daysDiff = ($day > 0) ? $day.' Days' : 'Same Day';
                    $approveDate = formatDate($row->approve_date).'<br>('.$daysDiff.')'; 
                }*/
                    $tbody .= '<tr>
                        <td class="text-center">'.$i++.'</td>
                        <td>'.formatDate($row->grn_date).'</td>
                        <td>'.getPrefixNumber($row->grn_prefix,$row->grn_no).'</td>
                        <td>'.$row->party_name.'</td>
                        <td>'.$row->item_name.'</td>
                        <td>'.floatval($row->qty).'</td>
                        <td>'.$approveDate.'</td>
                        <td>'.$inqDate.'</td>';
                    $tbody.='</tr>';
            endforeach;
            $this->printJson(['status'=>1, 'tbody'=>$tbody]);
        endif;
    }
}
?>