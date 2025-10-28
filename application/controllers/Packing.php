<?php
class Packing extends MY_Controller{

    private $indexPage = "packing/index";
    private $formPage = "packing/form";
    private $dispatchIndex = "packing/dispatch_index";
    private $dispatchForm = "packing/dispatch_form";
    private $standardIndex = "packing_standard/index";
    private $standardForm = "packing_standard/form";
    private $exportForm = "packing/export_form";
    private $indexExportPage = "packing/export_packing_index";
    private $stockIndex = "packing/stock_index";
    private $stockForm = "packing/stock_form";
    private $indexDispAdvPage = "packing/dispatch_advice_index";
    private $indexDispAdvForm = "packing/dispatch_advice_form";
   
    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Packing";
		$this->data['headData']->controller = "packing";
		// $this->data['headData']->pageUrl = "packing"; 
	}

    public function index(){
        $this->data['tableHeader'] = getDispatchDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data); 
    }

    public function getDTRows($status=0){
        $data = $this->input->post();$data['status'] = $status;
		$result = $this->packings->getDTRows($data); 
        $sendData = array();$i=$data['start']+1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $batchData = json_decode($row->batch_detail);
            $batchNoArr = array_column($batchData,'batch_no');
            $row->batch_no = implode(', ',$batchNoArr);
            $sendData[] = getPackingData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addPacking(){
        $this->data['trans_no'] = sprintf('%04d',$this->packings->getNetxNo());
        $this->data['trans_prefix'] = "PACK";
        $this->data['productData'] = $this->item->getItemList(1);
        //$this->data['packingMaterial'] =  $this->item->getItemList(2);
        $this->load->view($this->formPage,$this->data);
    }

    public function getStandardByfgid(){
        $fg_id = $this->input->post('fg_id'); $options='<option value="">Select Packing Material</option>';
        $packingData = $this->packingStandard->getStandardByfgid($fg_id);
        if(!empty($packingData)):
            foreach($packingData as $row):
                $options .= '<option value="'.$row->box_item_id.'" data-box_wt="'.$row->empty_box_wt.'">'.$row->item_name.'</option>';
            endforeach;
        endif;
        $this->printJson(['status'=>1,'options'=>$options]);
    }

    public function getSalesOrderList(){
        $item_id = $this->input->post('fg_id'); $options='<option value="">Select Sales Order</option>';
        $soData = $this->salesOrder->pendingSoByItemId($item_id);
        if(!empty($soData)):
            foreach($soData as $row):
                $options .= '<option value="'.$row->id.'">'.$row.' ['.$row->item_code.'] '.$row->item_name.'</option>';
            endforeach;
        endif;
        $this->printJson(['status'=>1,'options'=>$options]);
    }

    public function getBatchNo(){ 
        $item_id = $this->input->post('product_id');
        $batchData = $this->packings->batchWiseItemStock($item_id);
        $options = '<option value="" data-batch_no="" data-stock="">Select Batch No.</option>';
        foreach($batchData as $row):
			if($row->qty > 0):
				$options .= '<option value="'.$row->location_id.'" data-batch_no="'.$row->batch_no.'" data-stock="'.$row->qty.'">[ '.$row->store_name.' ]'.$row->batch_no.'</option>';
			endif;
        endforeach; 
        $this->printJson(['status'=>1,'options'=>$options]);
    }

    public function getProductBatchDetails(){
        $data = $this->input->post();
        $postData = ['item_id'=>$data['item_id'],'location_id'=>$this->PROD_STORE->id,'stock_required'=>1];
        $batchData = $this->store->getItemStockBatchWise($postData);

        $i=1;$tbody = '';
        if(!empty($batchData)):
            foreach($batchData as $row):
                $batchId = trim(preg_replace('/[^A-Za-z0-9]/', '', $row->batch_no)).$row->location_id;
                $location_name = '['.$row->store_name.'] '.$row->location;
                $tbody .= '<tr id="'.$batchId.'">
                    <td>'.$i.'</td>
                    <td>['.$row->store_name.'] '.$row->location.'</td>
                    <td>'.$row->batch_no.'</td>
                    <td id="closing_stock_'.$i.'">'.floatval($row->qty).'</td>
                    <td>
                        <input type="text" name="batch_qty[]" id="batch_qty_'.$i.'" class="form-control floatOnly calculateBatchQty" data-srno="'.$i.'" value="">
                        <input type="hidden" name="location_id[]" id="location_id_'.$i.'" value="'.$row->location_id.'">
                        <input type="hidden" name="batch_no[]" id="batch_no_'.$i.'" value="'.$row->batch_no.'">
                        <input type="hidden" name="batch_id[]" id="batch_id_'.$i.'" value="'.$batchId.'">
                        <input type="hidden" name="location_name[]" id="location_name_'.$i.'" value="'.$location_name.'">
                        <input type="hidden" name="batch_stock[]" id="batch_stock_'.$i.'" value="'.floatVal($row->qty).'">
                        <div class="error batch_qty_'.$i.'"></div>
                    </td>
                </tr>';
                $i++;
            endforeach;
        else:
            $tbody .= '<tr id="batchNoData">
                <td colspan="5" class="text-center">
                    No data available in table
                </td>
            </tr>';
        endif;
        $boxData = $this->packings->getProductPackStandard(['item_id'=>$data['item_id']]);
        $boxOptions = '<option value="">Select Packing Material</option>';
        if(!empty($boxData)){
            foreach($boxData as $row){
                $boxOptions .= '<option value="'.$row->box_id.'" data-qty_box="'.$row->qty_per_box.'">'.$row->item_name.'</option>';
            }
        }
        $this->printJson(['status'=>1,'batchTbody'=>$tbody,'boxOptions'=>$boxOptions]);
    }

    public function save(){
        $data = $this->input->post();
        unset($data['batch_qty'],$data['location_id'],$data['batch_no'],$data['batch_id'],$data['location_name'],$data['batch_stock'],$data['so_no']);
        $errorMessage = array();

        if(empty($data['item_id']))
            $errorMessage['item_id'] = "Product Name is required.";
        if(empty($data['trans_date']))
            $errorMessage['trans_date'] = "Packing Date is required.";
        if(empty($data['material_data'])):
            $errorMessage['material_error'] = "Packing Transaction is requried.";
        else:
            foreach($data['material_data'] as $key => $row):                
                $postData = ['item_id'=>$row['box_item_id'],'location_id'=>$this->PKG_STORE->id,'batch_no'=>"General Batch"];
                $currentStock = $this->packings->getItemCurrentStock($postData);
                $stockQty = (!empty($currentStock->qty))?$currentStock->qty:0;
                if(!empty($row['id'])):
                    $packingTrans = $this->packings->getPackingTransRow($row['id']);
                    $stockQty = $stockQty + $packingTrans->total_box;
                endif;
                if($row['total_box'] > $stockQty):
                    $errorMessage['total_box_'.$key] = "Stock not avalible.";
                endif;
            endforeach;
        endif;
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            if(empty($data['id'])):
                $data['trans_no'] = sprintf("%04d",$this->packings->getNetxNo());
                $data['trans_prefix'] = "PACK";
                $data['trans_number'] = $data['trans_prefix'].$data['trans_no'];
                $data['created_by'] = $this->loginId;
            else:
                $data['trans_number'] = $data['trans_prefix'].sprintf("%04d",$data['trans_no']);
                $data['updated_by'] = $this->loginId;
            endif;
            $data['total_box'] = array_sum(array_column($data['material_data'],'total_box'));
            $data['total_qty'] = array_sum(array_column($data['material_data'],'total_box_qty'));
            $this->printJson($this->packings->save($data));
        endif;
    }

    public function edit($id){
        $packingOrderData = $this->packings->getPacking($id);    
        $this->data['dataRow'] = $packingOrderData;
        $this->data['itemData'] = $this->item->getItemList(1);
        $this->data['packingMaterial'] =  $this->item->getItemList(2);
        $this->load->view($this->formPage,$this->data);
    }

    public function delete(){
		$id = $this->input->post('id');
		if(empty($id)):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->packings->delete($id));
		endif;
	}
	
	// Packing Sticker Print | Created BY JP@14.07.24
    public function packedBoxSticker($packing_id){
        
        $packData = $this->packings->getPacking($packing_id);
        $packageData = (!empty($packData->items)) ? $packData->items : [];
	
        $logo = base_url('assets/images/logo.png');
        $boxData='';
        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => [100,50]]); // Landscap
        $pdfFileName ='pack' . '.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->SetDisplayMode('fullpage');
        if(!empty($packageData))
        {   
            foreach($packageData as $row){
                
                $batchData = json_decode($row->batch_detail);
                $batchNoArr = array_column($batchData,'batch_no');
                $row->batch_no = implode(', ',$batchNoArr);
                
                
                $po_number = []; $row->gross_wt = 0;
                $batchTrans = $this->packings->getPackingTransDetail($batchNoArr);
                if(!empty($batchTrans))
                {
                    foreach($batchTrans as $bt)
                    {
                        if(!empty($bt->cust_po_no)){$po_number[] = $bt->cust_po_no;}
                    }
                }
                for($p=1;$p<=$row->total_box;$p++)
                {
                    $jnFont = (count($batchNoArr) > 1) ? '12px' : '19px';
                    $row->po_number = implode(',',$po_number); 
                    $row->gross_wt = ($row->qty_box * $packData->wt_pcs) + $row->box_wt;
                    $boxData = '<div style="text-align:center;padding:1mm 1mm;"> <!--bottom:0px;position:absolute;rotate:-90;-->
                					<table class="table item-list-pck" >
                						<tr>
                							<td class="text-center" colspan="2">CUSTOMER REF. No.<br><b  style="font-size:19px;"> <b>'.$packData->part_no.'</b></td>
                							<td  class="text-center">PACKING DATE <br> <b style="font-size:'.$jnFont.'">'.formatDate($packData->trans_date).'</b></td>
                						</tr>
                						<tr>
                							<td class="text-center" style="font-size:15px;" height="38" colspan="3"> <b>'.$packData->item_code.'</b></td>
                						</tr>
                						<tr>
                							<td  class="text-center"  colspan="2">
                								JOB CARD No. <br> <b style="font-size:'.$jnFont.'">'.$row->batch_no.'</b>
                							</td>
                							<td  class="text-center" >NOS/BOX <br> <b style="font-size:19px;">'.floatVal($row->qty_box).'</b></td>
                							
                						</tr>
                						<tr>
                							<td class="text-center">GROSS WEIGHT<br><b  style="font-size:19px;">'.sprintf('%.3f',$row->gross_wt).' Kgs</b></td>
                							<td class="text-center">TOTAL NOS<br><b  style="font-size:19px;">'.floatval($row->total_box_qty).'</b></td>
                							<td  class="text-center">
                								TOTAL BOX <br> <b style="font-size:19px;">'.str_pad($p,2,"0",STR_PAD_LEFT).'/'.floatVal($row->total_box).'</b>
                							</td>
                						</tr>
                					</table>
                				</div>';
                	
        			$mpdf->AddPage('P', '', '', '', '', 0, 0, 1, 1, 1, 1);
        			$mpdf->WriteHTML($boxData);
                }
            }
        }
        $mpdf->Output($pdfFileName, 'I');
    }

    /* Created By NYN @16/11/2022 */
    public function packingStandard(){
        $this->data['tableHeader'] = getDispatchDtHeader('packingStandard');
        $this->load->view($this->standardIndex,$this->data);
    }

    /* Created By NYN @16/11/2022 */
    public function getStandardDTRows(){
        $data = $this->input->post();
		$result = $this->item->getProdOptDTRows($data,1);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;

            $row->packing_standard = '';
            $standard = $this->packings->getProductPackStandard(['item_id'=>$row->id]);
            if(!empty($standard)){ $row->packing_standard = '<i class="fa fa-check text-primary"></i>'; }

            $sendData[] = getStandardData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    /* Created By NYN @16/11/2022 */
    public function updatePackingStandard($item_id=0){
        if(empty($item_id)){
            $item_id = $this->input->post('item_id');
        }
        $this->data['boxData'] = $this->item->getItemList(9);
        $this->data['standardData'] = $this->packingStandardTbl(['item_id'=>$item_id])['tbody'];
        $this->data['item_id'] = $item_id;
        $this->load->view($this->standardForm,$this->data);   
    }

    /* Created By NYN @16/11/2022 */
    public function savePackingStandard(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['wt_pcs']))
            $errorMessage['wt_pcs'] = "Weight Per Pcs. is required.";
        if(empty($data['box_id']))
            $errorMessage['box_id'] = "Packing Material is required.";
        if($data['box_type'] == 0)
        {
            if(empty($data['qty_per_box']))
                $errorMessage['qty_per_box'] = "Qty Per Box is required.";
            if(empty($data['wt_per_box']))
                $errorMessage['wt_per_box'] = "Box Weight is required.";
        }
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $result = $this->packings->savePackingStandard($data);
            $standardData = $this->packingStandardTbl(['item_id'=>$data['item_id']]);
            $result['tbody'] = $standardData['tbody'];
            $result['option'] = $standardData['option'];
            $this->printJson($result);
        endif;
    }

    /* Created By NYN @16/11/2022 */
    public function deletePackingStandard(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $result = $this->packings->deletePackingStandard($data['id']);
            $standardData = $this->packingStandardTbl(['item_id'=>$data['item_id']]);
            $result['tbody'] = $standardData['tbody'];
            $this->printJson($result);
        endif;
    }

    /* Created By NYN @16/11/2022 */
    public function packingStandardTbl($data){
        $standardData = $this->packings->getProductPackStandard($data);
        $tbody='';$option='<option value="">Select Packing Material</option>';$i=1;
        if(!empty($standardData)):
            foreach($standardData as $row):
                $deleteParam = $row->id.",".$row->item_id.",'Packing Standard'";
                $tbody.='<tr>
                    <td>'.$i++.'</td>
                    <td>'.(($row->box_type == 0)?'Box' : 'Pallet').'</td>
                    <td>'.$row->item_name.'</td>
                    <td>'.$row->qty_per_box.'</td>
                    <td>'.$row->wt_per_box.'</td>
                    <td class="text-center">
                        <a href="javascript:void(0)" class="btn btn-sm btn-outline-danger btn-delete permission-remove" onclick="trashPackingStandard('.$deleteParam.');" datatip="Remove" flow="left"><i class="ti-trash"></i></a>
                    </td>
                </tr>';
                
                
                $option .= '<option value="'.$row->box_id.'" data-box_wt="'.$row->wt_per_box.'">'.$row->item_name.'</option>';
            endforeach;
        else:
            $tbody.='<tr>
                    <td colspan="6" class="text-center">No Data Found</td>
                </tr>';
        endif;

        return ['status'=>1,'tbody'=>$tbody,'option'=>$option];
    }

	/* Dispatch Domestic */
    public function dispatchDomestic(){
        $this->data['tableHeader'] = getSalesDtHeader('dispatchDomestic');
		$this->data['dt_rows'] = 'getDomesticRows';
		$this->data['title'] = 'Dispatch Domestic';
        $this->load->view($this->dispatchIndex,$this->data);
    }

    public function getDomesticRows($status=0){
		$data = $this->input->post(); $data['status'] = $status;
		$result = $this->challan->getChallanDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row): 
            $row->sr_no = $i++;
			$row->request_for = 'Challan';
            $sendData[] = getDispatchMaterialData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
	}

    public function addDispatchMaterial(){
        $data = $this->input->post();
        $this->data['dataRow'] = $dataRow = $this->challan->challanTransRow($data['id']); 
        $this->data['batchData'] = $this->store->batchWiseItemStock(['item_id' => $dataRow->item_id]);
        $this->load->view($this->dispatchForm,$this->data);
    }
	
	public function batchWiseItemStock(){
		$data = $this->input->post(); 
        $result = $this->store->batchWiseItemStock($data)['result'];
        $i=1;$tbody="";$ttbox=0;
        if(!empty($result)):
            $batch_no = array();$batch_qty = array();$location_id = array();
            $batch_no = (!empty($data['batch_no']))?((!is_array($data['batch_no']))?explode(",",$data['batch_no']):$data['batch_no']):array();
            $batch_qty = (!empty($data['batch_qty']))?((!is_array($data['batch_qty']))?explode(",",$data['batch_qty']):$data['batch_qty']):array();
            $location_id = (!empty($data['location_id']))?((!is_array($data['location_id']))?explode(",",$data['location_id']):$data['location_id']):array();
            $size = (!empty($data['size']))?((!is_array($data['size']))?explode(",",$data['size']):$data['size']):array();
            foreach($result as $row):                
                if($row->qty > 0 || !empty($batch_no) && in_array($row->batch_no,$batch_no)  && (!empty($size) && in_array($row->size,$size))):
                    if((!empty($batch_no) && in_array($row->batch_no,$batch_no) && in_array($row->location_id,$location_id) && (!empty($size) && in_array($row->size,$size))) || ( (!empty($size) && in_array($row->size,$size) ) && empty($batch_no))):
                        $qty = 0;
                        $qty = $batch_qty[array_search($row->batch_no,$batch_no)];
                        $cl_stock = (!empty($data['trans_id']))?floatVal($row->qty + $qty):floatVal($row->qty);
                    else:
                        $qty = "0";
                        $cl_stock = floatVal($row->qty);
                    endif;                                
                    $totalBox = ($row->size>0) ? ($row->qty/$row->size) : 0;
                    $ttbox += $totalBox;
                    
                    $tbody .= '<tr>';
                        $tbody .= '<td class="text-center">'.$i.'</td>';
                        $tbody .= '<td class="disBatch">['.$row->store_name.'] '.$row->location.'</td>';
                        $tbody .= '<td class="disBatch">'.$row->batch_no.'</td>';
                        $tbody .= '<td class="disBatch">'.floatVal($row->qty).'</td>';
                        $tbody .= '<td class="text-center">'.floatVal($row->size).' x '.$totalBox.'</td>';
                        $tbody .= '<td class="text-center">
                                    <input type="text" class="form-control boxCnt numericOnly" id="box_cnt_'.$i.'" data-rowid="'.$i.'" data-box_size="'.floatVal($row->size).'" data-box_limit="'.$totalBox.'" min="0" value="'.(!empty($qty) ? ($qty/floatVal($row->size)) : 0).'" />
                                </td>';
                        $tbody .= '<td>
                            <input type="text" name="batch_quantity[]" class="form-control batchQty numericOnly" id="batch_qty_'.$i.'" data-rowid="'.$i.'" data-cl_stock="'.$cl_stock.'" min="0" value="'.$qty.'" readonly />
                            <input type="hidden" name="batch_number[]" id="batch_number'.$i.'" value="'.$row->batch_no.'" />
                            <input type="hidden" name="location[]" id="location'.$i.'" value="'.$row->location_id.'" />
                            <input type="hidden" name="qty_per_box[]" id="qty_per_box'.$i.'" value="'.$row->size.'">
                            <div class="error qty_per_box'.$i.'"></div>
                        </td>';
                    $tbody .= '</tr>';
                    $i++;
                endif;
            endforeach;
        else:
            $tbody = '<tr><td class="text-center" colspan="6">No Data Found.</td></tr>';
        endif;
        
        $tentativePackData="";
        if($data['packing_type'] == 2){
            $tentativeData = $this->packings->getExportData(['item_id'=>$data['item_id'],'req_id'=>$data['req_id'],'packing_type'=>1]);
            if(!empty($tentativeData)){
                $tentativePackData.='<table class="table table-bordered">
                    <thead>
                    <tr>
                    <th colspan="5" class="text-center">Tentative Packing Detail</th>
                    </tr>
                    <tr>
                        <th>#</th>
                        <th>Package No</th>
                        <th>Qty/Box</th>
                        <th>Total Box</th>
                        <th>Total Qty</th>
                    </tr>
                    </thead><tbody>';
                    
                foreach($tentativeData as $row)
                {
                    $tentativePackData.='<tr>
                        <td>'.$i++.'</td>
                        <td>'.$row->package_no.'</td>
                        <td>'.$row->qty_box.'</td>
                        <td>'.$row->total_box.'</td>
                        <td>'.$row->total_qty.'</td>
                    </tr>';
                }
                $tentativePackData.='</tbody></table>';
            }
        }
		$this->printJson(['status','batchData'=>$tbody,'tentativePackData'=>$tentativePackData,'ttbox'=>$ttbox]);
	}

    public function saveDispatchMaterial(){
        $data = $this->input->post();
        $errorMessage = array();

        if(floatVal($data['totalQty']) != floatVal($data['challan_qty']))
            $errorMessage['totalQty'] = "Invalid Qty.";
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $result = $this->packings->saveDispatchMaterial($data);
            $this->printJson($result);
        endif;
    }

	public function dispatchExport(){
        $this->data['headData']->pageUrl = "packing/dispatchExport"; 
        $this->data['tableHeader'] = getDispatchDtHeader('dispatchExport');
		$this->data['dt_rows'] = 'getExportRows';
		$this->data['title'] = 'Dispatch Export';
        $this->load->view($this->dispatchIndex,$this->data);
	}
	
	public function getExportRows($status=0){
		$data = $this->input->post(); $data['status'] = $status;
		$result = $this->dispatchRequest->getDispatchReqRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row): 
            $row->sr_no = $i++;
			$row->request_for = 'Challan';
			
            $sendData[] = getExportMaterialData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
	}

	public function packingExport($req_no=0,$party_id=0,$packing_type=1){
		$this->data['req_id'] = $req_no;
		$this->data['party_id'] = $party_id;
		$this->data['requestData'] = $this->dispatchRequest->getRequestForChallan(['party_id' => $party_id,'ref_no'=>$req_no]);
        $this->data['packing_type'] = $packing_type;
        $this->load->view($this->exportForm,$this->data);
	}	
	
    public function saveExportPacking(){
        $data = $this->input->post(); 
        unset($data['batch_qty'],$data['location_id'],$data['batch_no'],$data['batch_id'],$data['location_name'],$data['batch_stock'],$data['so_no']);
        $errorMessage = array();

        if(empty($data['item_data']))
            $errorMessage['item_id'] = "Product Name is required.";
        if(empty($data['packing_date']))
            $errorMessage['packing_date'] = "Packing Date is required.";
        if(empty($data['item_data'])):
            $errorMessage['item_id'] = "Product Name is required.";
        endif;
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            if(empty($data['id'])):
                $data['trans_no'] = $this->packings->getNextExportNo($data['packing_type']);
                $data['trans_prefix'] = ($data['packing_type'] == 1)?'TEP':'FEP';
                $data['trans_number'] = $data['trans_prefix'].sprintf('%04d',$data['trans_no']);
                $data['created_by'] = $this->loginId;
            else:
                $data['trans_number'] = $data['trans_prefix'].sprintf('%04d',$data['trans_no']);
                $data['updated_by'] = $this->loginId;
            endif;
            $this->printJson($this->packings->saveExportPacking($data));
        endif;
    }

    public function exportPackingIndex($packing_type){
        $this->data['headData']->pageUrl = "packing/dispatchExport"; 
        $this->data['packing_type'] = $packing_type;
        $this->data['tableHeader'] = getDispatchDtHeader('exportPacking');
        $this->load->view($this->indexExportPage,$this->data); 
    }

    public function getExportDTRows($packing_type,$status=0){
        if($packing_type == 3){ $packing_type=2; $status=1; }
        
        $data = $this->input->post();
        $data['status'] = $status; 
        $data['packing_type'] = $packing_type;
		$result = $this->packings->getExportDTRows($data); 
		
        $sendData = array();$i=$data['start']+1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getExportPackingData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function editExportPacking($transNo,$packing_type){
        $this->data['packing_type'] = $packing_type;
        $this->data['exportData'] = $exportData = $this->packings->getExportData(['trans_no'=>$transNo,'packing_type'=>$packing_type]); 
        $req_id = implode(',',array_unique(array_column($exportData,'req_id')));
        $this->data['requestData'] = $this->dispatchRequest->getRequestForChallan(['party_id' => $exportData[0]->party_id,'req_id'=>$req_id]);
        $this->load->view($this->exportForm,$this->data);
    }
   
    public function deleteExportPacking(){
        $data  =$this->input->post();
        $this->printJson($this->packings->deleteExportPacking($data));
    }

    public function printPackingTag(){
        $data = $this->input->post();
        $printFormat = $this->printFormat->getPrintFormat($data['format_id']);
        $packingData = $this->packings->getExportDetail($data['item_id']);  
        
        $packingData->job_card_no = "";
        $packingData->job_card_no = $packingData->batch_no;
        $packingData->tag_remark = 'Total Box- '.$packingData->total_box.' ('.$packingData->total_qty.'Pcs)';
        $packingData->company_name = "JAY JALARAM PRECISION COMPONENT LLP";
        $packingData->dispatch_date = formatDate($data['dispatch_date']);
        $packingData->lr_no = $data['lr_no'];
        $packingData->trans_way = $data['trans_way'];
        $packingData->heat_no = $data['heat_no'];
        $packingData->inv_no = $data['inv_no'];
        $packingData->lot_qty = $data['lot_qty'];
        
        $batchData = json_decode($packingData->batch_detail);
        $batchNoArr = (!empty($batchData)) ? array_column($batchData,'batch_no') : '';
        $packingData->job_card_no = (!empty($batchNoArr)) ? implode(', ',$batchNoArr) : '';

        $fieldList = json_decode($printFormat->formate_field);
        $html = "";
        foreach($fieldList as $key=>$label):
            if($key == 'company_name'):
                $html .= '<tr><th colspan="2"><h4><u>'.$packingData->{$key}.'</u></h4></th></tr>';
            else:
                if($key=='qty_per_box'){$key = 'qty_box';}
                if($key == 'gross_wt') {
                    $total_wt = $packingData->total_qty * $packingData->wpp;
                    $gross_wt = (!empty($total_wt) AND !empty($packingData->total_box)) ? ($total_wt / $packingData->total_box) : 0;
                    
                    $html .= '<tr>
                        <th style="font-size:10px;text-align:left;">Gross Wt(kg)</th>
                        <td style="font-size:10px;">'.floatval($gross_wt).'</td>
                    </tr>';
                }else{
                    $html .= '<tr>
                        <th style="font-size:10px;text-align:left;">'.$label.'</th>
                        <td style="font-size:10px;">'.$packingData->{$key}.' </td>
                    </tr>';
                }
            endif;
        endforeach;

        $pdata = '';
        for($i=1;$i<=$data['print_qty'];$i++):
            $pdata .= '<div style="width:100mm;height:50mm;text-align:left;float:left;padding:1mm 1mm;">
                        <table style="width:100%;" class="table item-list-bb">
                            '.$html.'
                        </table>
                    </div>';
        endfor;
        
        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => [$printFormat->width, $printFormat->height]]);

		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->setTitle($printFormat->format_name);
        $mpdf->AddPage('P','','','','',0,0,2,2,2,2);
        $mpdf->WriteHTML($pdata);
        $mpdf->Output('tag_print.pdf','I');
    }

    public function packingPdf($trans_no,$packing_type,$type=0,$p_or_m='P'){
        $packingData = $this->packings->getExportData(['trans_no'=>$trans_no,'packing_type'=>$packing_type]);
     
		$this->data['companyData'] = $this->purchaseOrder->getCompanyInfo();
		
		$logo=base_url('assets/images/logo.png?v='.time());
		$this->data['letter_head']=base_url('assets/images/letterhead_top.png?v='.time());
        $packageData = $this->packings->packingTransGroupByPackage(['trans_no'=>$trans_no,'packing_type'=>$packing_type]);
        $dataArray=array();
        foreach($packageData as $row){
            $row->itemData=$this->packings->getExportDataForPrint(['trans_no'=>$trans_no,'packing_type'=>$packing_type,'package_no'=>$row->package_no]);
            $dataArray[]=$row;
        }
        
        $this->data['packingMasterData'] = $packingMasterData =$packingData[0];
        $this->data['packingData']=$dataArray;
        $this->data['pdf_type'] = $type;
		$pdfData = $this->load->view('packing/packing_print',$this->data,true);        
        
        $packing_no = $packingMasterData->trans_prefix.sprintf("%04d",$packingMasterData->trans_no);
        
        
		$mpdf = new \Mpdf\Mpdf();
		$fileName= preg_replace('/[^A-Za-z0-9]/',"_",$packing_no).'.pdf';
		$filePath = realpath(APPPATH . '../assets/uploads/packing/');
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v='.time()));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
        $mpdf->setTitle($packing_no);
        $mpdf->shrink_tables_to_fit=1;
		
        if($packing_type == 1):
            $mpdf->SetWatermarkText('TENTATIVE',0.05);
            $mpdf->showWatermarkText = true;
        else:
		    $mpdf->SetWatermarkImage($logo,0.03,array(120,60));
		    $mpdf->showWatermarkImage = true;
		endif;
		
		$mpdf->SetHTMLHeader("");
		$mpdf->SetHTMLFooter("");
		$mpdf->AddPage('P','','','','',5,5,5,5,5,5,'','','','','','','','','','A4-P');
		$pdfData = '<table><tr><td>'.$pdfData.'</td></tr></table>';
		$mpdf->WriteHTML($pdfData);
		
		if($p_or_m == 'P'):
			$mpdf->Output($fileName,'I');
		else:
		    $packType = '';
        	$mpdf->Output($filePath.'/'.$fileName, 'F');
			return ['pdf_file'=>$filePath.'/'.$fileName,'packing_no'=>$packing_no,'packType'=>(!empty($packing_type==1)?'Tentative Export':'Final Export')];
		endif;
    }

    public function printFormatList(){
        $printFormat = $this->printFormat->getAllPrintFormats();

        $options = '<option value="">Select Format</option>';
        foreach($printFormat as $row):
            $options .= '<option value="'.$row->id.'">'.$row->format_name.'</option>';
        endforeach;

        $this->printJson(['status'=>1,'options'=>$options]);
    }

    public function getPackingItems(){
        $data=$this->input->post();
        $itemData=$this->packings->getExportData(['trans_no'=>$data['trans_no'],'packing_type'=>$data['packing_type']]);
        $options ='<option value="">Select Item</option>';
        if(!empty($itemData)){
            foreach($itemData as $row):
                $itemName=(!empty($row->item_code)?'['.$row->item_code.'] ':'').$row->item_name;
                $options .= '<option value="'.$row->id.'" >'.$itemName.'</option>';
            endforeach;
        }
        $this->printJson(['status'=>1,'options'=>$options]);
    }
    
    /* Created By Jp@13.06.2023*/
    public function sendMail(){
		$postData = $this->input->post(); 
		
        $printData = $this->packingPdf($postData['id'],$postData['ref_no'],$postData['attach_type'],'M'); // Ref No (Pack Type) 1 = Tentative, 2 = Final Export
        
    	$empData = $this->employee->getEmp($this->loginId);
		if(!empty($printData))
		{
    		$attachment = $printData['pdf_file'];
            $ref_no = $printData['packing_no'];
            $packType = $printData['packType'];
            
            $signData['sender_name'] = $empData->emp_name;
            $signData['sender_contact'] = (!empty($empData->emp_contact) OR $empData->emp_contact == 'admin') ? '9904709771' : $empData->emp_contact;
            $signData['sender_designation'] = $empData->designation;
            $signData['sign_email'] = 'dispatch.jalaram@gmail.com';
            
    		$emailSignature = $this->mails->getSignature($signData);
    
    		$mailData = array();
    		$mailData['sender_email'] = 'dispatch.jalaram@gmail.com';
    		$mailData['receiver_email'] = 'logistics@jayjalaramind.com';
    		$mailData['cc_email'] = 'salescoordinator@jayajalaramind.com,export@jayjalaramind.com,sales1@jayjalaramind.com';
    		$mailData['bcc_email'] = '';
    		$mailData['mail_type'] = 7;
    		$mailData['ref_id'] = 0;
    		$mailData['ref_no'] = 0;
    		$mailData['created_by'] = $this->loginId;
    		$mailData['subject'] = $packType.' Packing Annexure : '.$ref_no;
    		
    		$mail_body = '<div style="font-size:12pt;font-family: Bookman Old Style;">';
    		    $mail_body .= '<b>Dear Team,</b><br><br>';
    		    $mail_body .= 'Wishing you a good day!<br>';
    		    $mail_body .= 'Here, we are enclosing our '.$packType.' Packing Annexure with Packing No.: <b>'.$ref_no.'</b><br><br>Please find the attachment.<br><br><br>';
            $mail_body .= '</div>';
    		$mail_body .= $emailSignature;
    		$mailData['mail_body'] = $mail_body;
    		
    		$result = $this->mails->sendMail($mailData, [$attachment]);
    		unlink($attachment);
    		$this->printJson($result);
        }
		else
		{
		    $this->printJson(['status'=>0,'message'=>'Contact Email Not Found.']);
		}
	}

	public function oldPackingStock(){
        $this->data['tableHeader'] = getDispatchDtHeader('oldPackingStock');
        $this->load->view($this->stockIndex,$this->data);
	}
	
	public function getOldStockDTRows(){
        $data = $this->input->post();
        $result = $this->packings->getOldStockDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getOldStockData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addStock(){
        $this->data['itemList'] = $this->item->getItemList(1);
        $this->load->view($this->stockForm, $this->data);
    }

    public function saveStock(){
        $data = $this->input->post();
		$errorMessage = array();		

		if(empty($data['ref_date']))
			$errorMessage['ref_date'] = "Date is required.";
        if(empty($data['item_id']))
			$errorMessage['item_id'] = "Item Name is required.";
        if(empty(floatVal($data['qty'])))
			$errorMessage['qty'] = "Qty is required.";
        if(empty($data['batch_no']))
            $errorMessage['batch_no'] = "Batch No is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['location_id'] = $this->PROD_STORE->id;
            $this->printJson($this->packings->saveStock($data));
        endif;
    }
    
    public function dispatchAdvice() {
        $this->data['headData']->pageUrl = "packing/dispatchAdvice"; 
        $this->data['packing_type'] = 1;
        $this->data['tableHeader'] = getDispatchDtHeader('exportPacking');
        $this->load->view($this->indexDispAdvPage,$this->data); 
    }

    public function getDispAdvDTRows($packing_type,$status=0){        
        $data = $this->input->post();
        $data['status'] = $status; 
        $data['packing_type'] = $packing_type;
		$result = $this->packings->getExportDTRows($data); 
        $sendData = array();$i=$data['start']+1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getDispatchAdviceData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function editDispatchAdvice() {
        $data = $this->input->post();
        $packing_type = 1;
        $this->data['dataRow'] = $dataRow = $this->packings->getExportDetail($data['id']);
        $packingData = $this->packings->getExportData(['trans_no'=>$dataRow->trans_no,'packing_type'=>$packing_type]);
        $packageData = $this->packings->packingTransGroupByPackage(['trans_no'=>$dataRow->trans_no,'packing_type'=>$packing_type]);
        $dataArray=array();
        foreach($packageData as $row){
            $row->itemData=$this->packings->getExportDataForPrint(['trans_no'=>$dataRow->trans_no,'packing_type'=>$packing_type,'package_no'=>$row->package_no]);
            $dataArray[]=$row;
        }
        $this->data['packingData']=$dataArray;
        $this->data['pdf_type'] = 0;
        $this->load->view($this->indexDispAdvForm,$this->data);
    }

    public function saveDispatchAdvice() {
        $data = $this->input->post();
		$errorMessage = array();		

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->packings->saveDispatchAdvice($data));
        endif;
    }
    
    public function printWoodenBoxLabel($id) {
        $packingData = $this->packings->getExportDetail($id);
        $exportData = $this->packings->getExportData(['trans_no'=>$packingData->trans_no,'packing_type'=>$packingData->packing_type]);
        
        $tot_pall_ship = max(array_column($exportData, 'package_no'));

        $pdfData = '';$pack_no=array();
        foreach($exportData as $row):
            if(in_array($row->package_no, $pack_no)){
                $pdfData .= '<tr>
                                <td>'.$row->item_name.'</td>
                                <td>'.$row->total_qty.'</td>
                                <td>'.$row->item_alias.'</td>
                            </tr>';
            } else {
                if(!empty($pack_no)){
                    $pdfData .= '<tr>
                                <th>S/Mark</th>
                                <td colspan="2">'.$packingData->smark.'</td>
                            </tr>
                        </tbody>
                    </table> <br><br>';
                }
                $pack_no[] = $row->package_no;
                $pdfData .= '<table class="table item-list-bb">
                            <thead></thead>
                            <tbody>
                                <tr>
                                    <th style="width:40%;text-align:left;">PO Number:</th>
                                    <td colspan="2">'.$row->doc_no.'</td>
                                </tr>
                                <tr>
                                    <th style="width:40%;text-align:left;">Pallet Number</th>
                                    <td colspan="2">'.$row->package_no.'</td>
                                </tr>
                                <tr>
                                    <th style="width:40%;text-align:left;">Total Pallet in Shipment</th>
                                    <td colspan="2">'.$tot_pall_ship.'</td>
                                </tr>
                                
                                <tr>
                                    <th>Item Description</th>
                                    <th>Qty (Nos.)</th>
                                    <th>Customer Ref No.</th>
                                </tr>
                                <tr>
                                    <td>'.$row->item_name.'</td>
                                    <td>'.$row->total_qty.'</td>
                                    <td>'.$row->item_alias.'</td>
                                </tr>';
            }
        endforeach;

        $pdfData .= '<tr>
                    <th>S/Mark</th>
                    <td colspan="2">'.$packingData->smark.'</td>
                </tr>
            </tbody>
        </table> <br><br>';

        $packing_no = 'Wooden Box Tag'.$packingData->trans_no;
		$mpdf = new \Mpdf\Mpdf();
		$fileName= preg_replace('/[^A-Za-z0-9]/',"_",$packing_no).'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v='.time()));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
        $mpdf->setTitle($packing_no);
		$mpdf->AddPage('P','','','','',5,5,5,5,5,5,'','','','','','','','','','A5-P');
		$pdfData = '<div>'.$pdfData.'</div>';
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($fileName,'I');
    }
    
    public function commercialPackingPdf($id,$packing_type,$type=0,$p_or_m='P'){
        $packingData = $this->packings->getExportDetail($id);
        $sign_img = base_url('assets/uploads/emp_sign/sign_'.(281).'.png');
		$logo=base_url('assets/images/logo.png?v='.time());
		
        /**/
        $packageData = $this->packings->packingTransGroupByPackage(['trans_no'=>$packingData->trans_no,'packing_type'=>$packing_type]);
        $dataArray=array();
        foreach($packageData as $row){
            $row->itemData=$this->packings->getExportDataForPrint(['trans_no'=>$packingData->trans_no,'packing_type'=>$packing_type,'package_no'=>$row->package_no]);
            $dataArray[]=$row;
        }
        $this->data['packingData']=$dataArray;
        $this->data['pdf_type'] = $packing_type;

		$this->data['companyData'] = $this->purchaseOrder->getCompanyInfo();
		$this->data['letter_head']=base_url('assets/images/letterhead_top.png?v='.time());
        $this->data['dataRow'] = $packingData;
        
        $this->data['partyData'] = $this->party->getParty($packingData->party_id);
        
        $this->data['pdf_type'] = $type;
        $this->data['authorise_sign'] = '<img src="'.$sign_img.'" style="width:100px;">';
        //$this->data['packageNum'] = $this->commercialPacking->getCommercialPackingItemsGroupByBox($packingData->so_id);
      
		$pdfData = $this->load->view('packing/commercial_packing_pdf',$this->data,true);  
        
        $packing_no = $packingData->trans_prefix.sprintf("%04d",$packingData->trans_no);
        $mpdf = new \Mpdf\Mpdf();
		$fileName= preg_replace('/[^A-Za-z0-9]/',"_",$packing_no).'.pdf';
		$filePath = realpath(APPPATH . '../assets/uploads/packing/');
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v='.time()));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
        $mpdf->setTitle($packing_no);
        $mpdf->shrink_tables_to_fit=1;
		
        if($packing_type == 1):
            $mpdf->SetWatermarkText('TENTATIVE',0.05);
            $mpdf->showWatermarkText = true;
        else:
		    $mpdf->SetWatermarkImage($logo,0.03,array(120,60));
		    $mpdf->showWatermarkImage = true;
		endif;
		
		$mpdf->SetHTMLHeader("");
		$mpdf->SetHTMLFooter("");
		$mpdf->AddPage('P','','','','',5,5,5,5,5,5,'','','','','','','','','','A4-P');
		$pdfData = '<table><tr><td>'.$pdfData.'</td></tr></table>';
        
		$mpdf->WriteHTML($pdfData);
		
		if($p_or_m == 'P'):
			$mpdf->Output($fileName,'I');
		else:
		    $packType = '';
        	$mpdf->Output($filePath.'/'.$fileName, 'F');
			return ['pdf_file'=>$filePath.'/'.$fileName,'packing_no'=>$packing_no,'packType'=>(!empty($packing_type==1)?'Tentative Export':'Final Export')];
		endif;
    }

    public function commercialInvoicePdf($id,$packing_type,$type=0,$p_or_m='P'){
        $packingData = $this->packings->getExportDetail($id);
        $sign_img = base_url('assets/uploads/emp_sign/sign_'.(281).'.png');
		$logo=base_url('assets/images/logo.png?v='.time());
     
		$this->data['companyData'] = $this->purchaseOrder->getCompanyInfo();
		$this->data['letter_head']=base_url('assets/images/letterhead_top.png?v='.time());
        $this->data['dataRow'] = $packingData;
        $this->data['partyData'] = $this->party->getParty($packingData->party_id);
        $this->data['itemData'] = $this->packings->getTentativePackingListItems(['trans_no' => $packingData->trans_no, 'packing_type' => $packing_type]);
        $this->data['pdf_type'] = $type;
        $this->data['authorise_sign'] = '<img src="'.$sign_img.'" style="width:100px;">';
        $this->data['packageNum'] = $this->packings->getTentativePackingListItems(['trans_no' => $packingData->trans_no, 'packing_type' => $packing_type,"package_no"=>1]);

		$pdfData = $this->load->view('packing/commercial_invoice_pdf',$this->data,true);        
        
        $packing_no = $packingData->trans_prefix.sprintf("%04d",$packingData->trans_no);
        $mpdf = new \Mpdf\Mpdf();
		$fileName= preg_replace('/[^A-Za-z0-9]/',"_",$packing_no).'.pdf';
		$filePath = realpath(APPPATH . '../assets/uploads/packing/');
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v='.time()));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
        $mpdf->setTitle($packing_no);
        $mpdf->shrink_tables_to_fit=1;
		
        if($packing_type == 1):
            $mpdf->SetWatermarkText('TENTATIVE',0.05);
            $mpdf->showWatermarkText = true;
        else:
		    $mpdf->SetWatermarkImage($logo,0.03,array(120,60));
		    $mpdf->showWatermarkImage = true;
		endif;
		
		$mpdf->SetHTMLHeader("");
		$mpdf->SetHTMLFooter("");
		$mpdf->AddPage('P','','','','',5,5,5,5,5,5,'','','','','','','','','','A4-P');
		$pdfData = '<table><tr><td>'.$pdfData.'</td></tr></table>';
		$mpdf->WriteHTML($pdfData);
		
		if($p_or_m == 'P'):
			$mpdf->Output($fileName,'I');
		else:
		    $packType = '';
        	$mpdf->Output($filePath.'/'.$fileName, 'F');
			return ['pdf_file'=>$filePath.'/'.$fileName,'packing_no'=>$packing_no,'packType'=>(!empty($packing_type==1)?'Tentative Export':'Final Export')];
		endif;
    }
}
?>