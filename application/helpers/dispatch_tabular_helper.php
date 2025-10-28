<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/* get Pagewise Table Header */
function getDispatchDtHeader($page)
{
    /* packing Header */
    $data['packing'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['packing'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
    $data['packing'][] = ["name"=>"Packing No."];
    $data['packing'][] = ["name"=>"Packing Date"];
    $data['packing'][] = ["name"=>"SO No."];
    $data['packing'][] = ["name"=>"Product Name"];
    $data['packing'][] = ["name"=>"Job No."];
    $data['packing'][] = ["name"=>"Box Capacity"];
    $data['packing'][] = ["name"=>"Total Box"];
    $data['packing'][] = ["name"=>"Total Qty."];
    $data['packing'][] = ["name"=>"Pending Dispatch"];
    $data['packing'][] = ["name"=>"Remark"];
    
    /* Packing Invoiced Header */
    //$data['packingInvoiced'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['packingInvoiced'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center","srnoPosition"=>0];    
    $data['packingInvoiced'][] = ["name"=>"Inv. No."];
    $data['packingInvoiced'][] = ["name"=>"Inv. Date"];
    $data['packingInvoiced'][] = ["name"=>"Customer"];
    $data['packingInvoiced'][] = ["name"=>"So. No."];
    $data['packingInvoiced'][] = ["name"=>"Item"];
    $data['packingInvoiced'][] = ["name"=>"Inv. Qty."];
    $data['packingInvoiced'][] = ["name"=>"Packing. No."];
    $data['packingInvoiced'][] = ["name"=>"Packing. Qty."];

    /* packing bom Header */
    $data['packingBom'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center","srnoPosition"=>0];
    $data['packingBom'][] = ["name"=>"Product"];
    $data['packingBom'][] = ["name"=>"Action","style"=>"width:15%;","textAlign"=>"center"];

    /* packing Standard Header */
    $data['packingStandard'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center","srnoPosition"=>0];
    $data['packingStandard'][] = ["name"=>"Product","style"=>"width:70%;"];
    $data['packingStandard'][] = ["name"=>"Packing Standard","style"=>"width:10%;","textAlign"=>"center"];
    $data['packingStandard'][] = ["name"=>"Action","style"=>"width:15%;","textAlign"=>"center"];

    /* Delivery Challan Header */
    $data['deliveryChallan'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['deliveryChallan'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
	$data['deliveryChallan'][] = ["name"=>"Challan. No.","textAlign"=>"center"];
    $data['deliveryChallan'][] = ["name"=>"DC. Date","textAlign"=>"center"];
    $data['deliveryChallan'][] = ["name"=>"Customer Name"]; 
    $data['deliveryChallan'][] = ["name"=>"Product Name"]; 
    $data['deliveryChallan'][] = ["name"=>"Dispatch Qty.","textAlign"=>"center"]; 

    /* Stock Adjustment Header */
    $data['stockAdjustment'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['stockAdjustment'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
	$data['stockAdjustment'][] = ["name"=>"Product"];
    $data['stockAdjustment'][] = ["name"=>"Location"];
    $data['stockAdjustment'][] = ["name"=>"Batch No."]; 
    $data['stockAdjustment'][] = ["name"=>"Stock Qty.","textAlign"=>"center"];
    
     /* Export packing Header */
     $data['exportPacking'][] = ["name"=>"Action","style"=>"width:5%;"];
     $data['exportPacking'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
     $data['exportPacking'][] = ["name"=>"Packing No."];
     $data['exportPacking'][] = ["name"=>"Packing Date"];
     $data['exportPacking'][] = ["name"=>"Req. No."];
     $data['exportPacking'][] = ["name"=>"SO No."];
     $data['exportPacking'][] = ["name"=>"Cust. PO No."];
     $data['exportPacking'][] = ["name"=>"Product Name"];
     $data['exportPacking'][] = ["name"=>"Box Capacity"];
     $data['exportPacking'][] = ["name"=>"Total Box"];
     $data['exportPacking'][] = ["name"=>"Total Qty."];
     $data['exportPacking'][] = ["name"=>"Remark"];
     
    /* Dispatch Export */
    $data['dispatchExport'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['dispatchExport'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
	$data['dispatchExport'][] = ["name"=>"Req. Date"];
	$data['dispatchExport'][] = ["name"=>"Req. No."];
	$data['dispatchExport'][] = ["name"=>"Customer"];
	$data['dispatchExport'][] = ["name"=>"Item Name"];
	$data['dispatchExport'][] = ["name"=>"Req. Qty","textAlign"=>"center"];
	$data['dispatchExport'][] = ["name"=>"Final Packing Qty","textAlign"=>"center"];
	$data['dispatchExport'][] = ["name"=>"Pending Qty","textAlign"=>"center"];
	
	/* Item Header */
    $data['packingMaterial'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['packingMaterial'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['packingMaterial'][] = ["name"=>"Item Code"];
    $data['packingMaterial'][] = ["name"=>"Item Name"];
    $data['packingMaterial'][] = ["name"=>"HSN Code"];
    $data['packingMaterial'][] = ["name"=>"Stock Qty"];
    $data['packingMaterial'][] = ["name"=>"Manage Stock"];
	
	/* Old Packing Stock */
	$data['oldPackingStock'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['oldPackingStock'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
	$data['oldPackingStock'][] = ["name"=>"Item Code"];
	$data['oldPackingStock'][] = ["name"=>"Item Name"];
	$data['oldPackingStock'][] = ["name"=>"Batch No"];
	$data['oldPackingStock'][] = ["name"=>"Qty"];
	$data['oldPackingStock'][] = ["name"=>"Date"];
	$data['oldPackingStock'][] = ["name"=>"Remark"];
	
    return tableHeader($data[$page]);

}

/* Packing Data */
function getPackingData($data){
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editPackingOrder', 'title' : 'Update Packing Order'}";
    $edit="";$delete="";
    if($data->pending_qty >0)
    {
        $edit = '<a class="btn btn-success btn-sm btn-edit permission-modify" href="'.base_url('packing/edit/'.$data->id).'" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
    
        $deleteParam = $data->id.",'Packing Order'";
        $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    }
    $item_name = (!empty($data->item_code) ? "[".$data->item_code."] ".$data->item_name : $data->item_name);
    
    $stickerPrint='<a class="btn btn-facebook btn-edit" href="'.base_url('packing/packedBoxSticker/'.$data->id).'" target="_blank" datatip="Print Sticker" flow="down"><i class="fas fa-print" ></i></a>';

    $action = getActionButton($stickerPrint.$edit.$delete);
    return [$action,$data->sr_no,$data->trans_number,formatDate($data->trans_date),$data->so_no,$item_name,$data->batch_no,$data->qty_box,$data->total_box,$data->total_box_qty,$data->pending_qty,$data->remark];
}

function getPackingBomData($data){
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editPackingBom', 'title' : 'Update Packing BOM'}";
    
    $btn = '<div class="btn-group" role="group" aria-label="Basic example">
                <a class="btn btn-success btn-sm btn-edit permission-modify" href="javascript:void(0)" datatip="BOM" flow="down" onclick="edit('.$editParam.');"><i class="fas fa-dolly-flatbed"></i></a>
            </div>';
    return [$data->sr_no,$data->item_code,$btn];
}

/* Delivery Challan */
function getDeliveryChallansData($data){

    $deleteParam = $data->trans_main_id.",'Delivery Challan'";$invoice = "";$edit = "";$delete = "";

    if(empty($data->trans_status)):

        $invoice = '<a href="javascript:void(0)" class="btn btn-primary createInvoice permission-write" data-id="'.$data->party_id.'" data-party_name="'.$data->party_name.'" datatip="Invoice" flow="down"><i class="fa fa-file-alt" ></i></a>';    

        $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->trans_main_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';

        $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    endif;	
    $action = getActionButton($invoice.$edit.$delete);
    return [$action,$data->sr_no,getPrefixNumber($data->trans_prefix,$data->trans_no),formatDate($data->trans_date),$data->party_code,$data->item_name,floatVal($data->qty)];

}

/* Stock Adjustment Data*/
function getStockAdjustmentData($data){ 
    $stfParam = "{'location_id':".$data->location_id.",'transfer_location':".$data->transfer_location.",'item_id':".$data->item_id.",'stock_qty':".floatVal($data->current_stock).",'batch_no':'".$data->batch_no."','modal_id' : 'modal-md', 'form_id' : 'stockTransfer', 'title' : 'Misplaced','fnSave' : 'saveStockTransfer'}";
    $stfBtn = '<a class="btn btn-success btn-edit" href="javascript:void(0)" datatip="Misplaced" flow="down" onclick="stockTransfer('.$stfParam.');"><i class="ti-control-shuffle" ></i></a>';

    $itemName = (!empty($data->item_code))?'['.$data->item_code.'] '.$data->item_name:$data->item_name;
    $action = getActionButton($stfBtn);

    return [$action,$data->sr_no,$itemName,$data->store_name.' ['.$data->location.']',$data->batch_no,$data->current_stock];
}

/* Packing Standard Data */
function getStandardData($data){
    $btn = '<div class="btn-group" role="group" aria-label="Basic example">
                <button type="button" class="btn btn-info packingStandard" data-id="'.$data->id.'" data-item_code="'.$data->item_code.'" data-wt_pcs="'.$data->wt_pcs.'" data-button="close" data-modal_id="modal-lg" data-function="updatePackingStandard" data-form_title="Update Packing Standard" datatip="Packing Standard" flow="left"><i class="fas fa-dolly-flatbed"></i></button>
            </div>';
    return [$data->sr_no,$data->item_code,$data->packing_standard,$btn]; 
}

function getPackingInvoicedData($data){
    $action = "";
    return [$data->sr_no,$data->trans_number,formatDate($data->trans_date),$data->party_code,$data->doc_no,$data->item_code,$data->qty,$data->packing_no,$data->packing_qty];
}

/* Packing Data */
function getExportPackingData($data){
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editPackingOrder', 'title' : 'Update Packing Order'}";
    $mailBtn=''; 
    $edit = '';$delete = '';
    if($data->comm_pack_id == 0)
    {
        if($data->port_loading == '' && $data->port_dispatch == '' && $data->destination_country == '' && $data->nomination_agent == ''){
            $edit = '<a class="btn btn-success btn-sm btn-edit permission-modify" href="'.base_url('packing/editExportPacking/'.$data->trans_no.'/'.$data->packing_type).'" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
        }
        
        if($data->packing_type == 2){
            $deleteParam = $data->id.",'Packing Order'";
            $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trashExportPacking('.$data->trans_no.','.$data->packing_type.','.$data->req_id.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
        }
    }
    $item_name = (!empty($data->item_code) ? "[".$data->item_code."] ".$data->item_name : $data->item_name);
    $internalPrintBtn = '<a class="btn btn-warning btn-edit " href="'.base_url('packing/packingPdf/'.$data->trans_no.'/'.$data->packing_type).'" target="_blank" datatip="Internal Print" flow="down"><i class="fas fa-print" ></i></a>';
    $customerPrintBtn = '<a class="btn btn-primary btn-edit " href="'.base_url('packing/packingPdf/'.$data->trans_no.'/'.$data->packing_type.'/1').'" target="_blank" datatip="Customer Print" flow="down"><i class="fas fa-print" ></i></a>';
    $customPrintBtn = '<a class="btn btn-dark btn-edit " href="'.base_url('packing/packingPdf/'.$data->trans_no.'/'.$data->packing_type.'/2').'" target="_blank" datatip="Custom Print" flow="down"><i class="fas fa-print" ></i></a>';

    $packingPrint = '<a href="javascript:void(0)" class="btn btn-danger packingTag " data-id="'.$data->id.'" data-trans_no="'.$data->trans_no.'" data-packing_type="'.$data->packing_type.'" data-soid="'.$data->so_id.'" data-packing_sticker="" datatip="Print Packing Tag" flow="down"><i class="fas fa-print" ></i></a>';
    
    $boxLabel = '<a class="btn btn-primary btn-edit " href="'.base_url('packing/printWoodenBoxLabel/'.$data->id).'" target="_blank" datatip="Wooden Box Label" flow="down"><i class="fas fa-print" ></i></a>';
    
    $internalParam = $data->trans_no.','.$data->packing_type.',0';
    $mailInterBtn = '<a class="btn btn-success permission-read" href="javascript:void(0)" onclick="sendMail('.$internalParam.')" datatip="Send Mail Internal Copy" flow="down"><i class="fas fa-envelope" ></i></a>';
    
    $custParam = $data->trans_no.','.$data->packing_type.',1';
    $mailCustBtn = '<a class="btn btn-success permission-read" href="javascript:void(0)" onclick="sendMail('.$custParam.')" datatip="Send Mail Customer Copy" flow="down"><i class="fas fa-envelope" ></i></a>';
    
    $action = getActionButton($boxLabel.$packingPrint.$mailInterBtn.$internalPrintBtn.$mailCustBtn.$customerPrintBtn.$customPrintBtn.$edit.$delete);
    
    $prNumber = (!empty($data->pr_prefix)) ? getPrefixNumber($data->pr_prefix,$data->pr_no) : $data->pr_prefix.$data->pr_no;
    $soNumber = (!empty($data->so_prefix)) ? getPrefixNumber($data->so_prefix,$data->so_no) : $data->so_prefix.$data->so_no;
    
    return [$action,$data->sr_no,$data->trans_prefix.sprintf("%04d",$data->trans_no),formatDate($data->packing_date),$prNumber,$soNumber,$data->doc_no,$item_name,$data->qty_box,$data->total_box,$data->total_qty,$data->remark];
}

/* Dispatch Advice Data */
function getDispatchAdviceData($data) {
    
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'editDispatchAdvice', 'fnEdit' : 'editDispatchAdvice', 'fnsave' : 'saveDispatchAdvice', 'title' : 'Dispatch Advice'}";
    $edit = '<a class="btn btn-success btn-edit" href="javascript:void(0)" datatip="Update Dispatch Advise" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    if($data->comm_pack_id == 0){
        $deleteParam = $data->id.",'Packing Order'";
        $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trashExportPacking('.$data->trans_no.','.$data->packing_type.','.$data->req_id.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    }
    $internalPrintBtn = '<a class="btn btn-warning btn-edit " href="'.base_url('packing/packingPdf/'.$data->trans_no.'/'.$data->packing_type).'" target="_blank" datatip="Internal Print" flow="down"><i class="fas fa-print" ></i></a>';

    $customerPrintBtn = '<a class="btn btn-primary btn-edit " href="'.base_url('packing/packingPdf/'.$data->trans_no.'/'.$data->packing_type.'/1').'" target="_blank" datatip="Customer Print" flow="down"><i class="fas fa-print" ></i></a>';

    $packingPrintBtn = '<a class="btn btn-info btn-edit " href="'.base_url('packing/commercialPackingPdf/'.$data->id.'/'.$data->packing_type).'" target="_blank" datatip="Commercial Packing" flow="down"><i class="fas fa-print" ></i></a>';
    
    $packingInvoiceBtn = '<a class="btn btn-dark btn-edit " href="'.base_url('packing/commercialInvoicePdf/'.$data->id.'/'.$data->packing_type).'" target="_blank" datatip="Commercial Invoice" flow="down"><i class="fas fa-print" ></i></a>';

    $action = getActionButton($internalPrintBtn.$customerPrintBtn.$packingPrintBtn.$packingInvoiceBtn.$edit.$delete);

    $item_name = (!empty($data->item_code) ? "[".$data->item_code."] ".$data->item_name : $data->item_name);
    $prNumber = (!empty($data->pr_prefix)) ? getPrefixNumber($data->pr_prefix,$data->pr_no) : $data->pr_prefix.$data->pr_no;
    $soNumber = (!empty($data->so_prefix)) ? getPrefixNumber($data->so_prefix,$data->so_no) : $data->so_prefix.$data->so_no;

    return [$action,$data->sr_no,$data->trans_prefix.sprintf("%04d",$data->trans_no),formatDate($data->packing_date),$prNumber,$soNumber,$data->doc_no,$item_name,$data->qty_box,$data->total_box,$data->total_qty,$data->remark];
}

function getExportMaterialData($data){
    $tentative ='';$final="";
    if($data->status ==0){
        $tentative = '<a href="'.base_url('packing/packingExport/'.$data->trans_no.'/'.$data->party_id.'/1').'"  class="btn btn-info permission-write" datatip="Tentative Packing" flow="down"><i class="fab fa-tumblr" ></i></a>';
    }
    if($data->status == 0 || $data->status == 1 || $data->status == 2){
        $final = '<a href="'.base_url('packing/packingExport/'.$data->trans_no.'/'.$data->party_id.'/2').'"  class="btn btn-warning permission-write" datatip="Final Packing" flow="down"><i class=" fab fa-facebook-f" ></i></a>';
    }
	$action=getActionButton($tentative.$final);
	 $pendingQty = $data->req_qty - $data->dispatch_qty;
    return [$action,$data->sr_no,formatDate($data->req_date),getPrefixNumber($data->trans_prefix,$data->trans_no),'['.$data->party_code.'] '.$data->party_name,'['.$data->item_code.'] '.$data->item_name,floatVal($data->req_qty),floatVal($data->dispatch_qty),floatval($pendingQty)];
}

/* Item Table Data */
function getPackingItemData($data){
    $deleteParam = $data->id.",'Item'";
    $editParam = "{'id' : ".$data->id.",'item_type' :".$data->item_type.", 'modal_id' : 'modal-lg', 'form_id' : 'editItem', 'title' : 'Update Item','fnEdit' : 'editPackingMaterial', 'fnsave' : 'savePackingMaterial'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $approvalButton = '';
    if(empty($data->approved_by)){
        $approvalParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'itemApproval', 'title' : 'Item Approval : <br><small>".$data->item_name."</small>', 'fnEdit' : 'itemApproval', 'fnsave' : 'saveItemApproval'}";
        $approvalButton = '<a class="btn btn-info btn-approval permission-modify" href="javascript:void(0)" datatip="Item Approval" flow="down" onclick="edit('.$approvalParam.');"><i class="fa fa-check" ></i></a>';
    }
    $hsnDetail = '';
	if(!empty($data->hsnDetail)){$hsnDetail = '<br><small>'.$data->hsnDetail.'</small>';}
	
    $mq = '';
    if($data->stock_qty < $data->min_qty){ $mq = 'text-danger'; }
	$qty = '<a href="'.base_url('reports/productReport/itemWiseStock/'.$data->id).'" class="'.$mq.'">'.$data->stock_qty.' ('.$data->unit_name.')</a>';

    $openingStock = '<button type="button" class="btn waves-effect waves-light btn-outline-primary itemOpeningStock permission-modify" data-id="'.$data->id.'" data-item_name="'.$data->item_name.'" data-button="close" data-modal_id="modal-lg" data-function="addOpeningStock" data-form_title="Opening Stock">Opening Stock</button>';
	$approvalButton = '';
    $action = getActionButton($approvalButton.$editButton.$deleteButton);    
    return [$action,$data->sr_no,$data->item_code,$data->full_name,$data->hsn_code,$qty,$openingStock];
}

function getOldStockData($data){
	$action = getActionButton('');
	return [$action,$data->sr_no,$data->item_code,$data->item_name,$data->batch_no,floatVal($data->qty),formatDate($data->ref_date),$data->remark];
}
?>