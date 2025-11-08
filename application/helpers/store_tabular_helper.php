<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/* get Pagewise Table Header */
function getStoreDtHeader($page){
    /* store header */
    $data['store'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['store'][] = ["name"=>"#","style"=>"width:5%;"]; 
    $data['store'][] = ["name"=>"Store Name"];
    $data['store'][] = ["name"=>"Location"];
    $data['store'][] = ["name"=>"Remark"];

    /* Dispatch Material */
    $data['jobMaterialDispatch'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];
	$data['jobMaterialDispatch'][] = ["name"=>"#","style"=>"width:4%;","textAlign"=>"center"];
    $data['jobMaterialDispatch'][] = ["name"=>"Job No.","style"=>"width:9%;"];
    $data['jobMaterialDispatch'][] = ["name"=>"Request Date","textAlign"=>"center"];
    $data['jobMaterialDispatch'][] = ["name"=>"Item Name"];
    $data['jobMaterialDispatch'][] = ["name"=>"Stock Qty","textAlign"=>"center"];
    $data['jobMaterialDispatch'][] = ["name"=>"Requested Qty","textAlign"=>"center"];    
    $data['jobMaterialDispatch'][] = ["name"=>"Issue Qty","textAlign"=>"center"];
    $data['jobMaterialDispatch'][] = ["name"=>"Issue Date","textAlign"=>"center"];
    $data['jobMaterialDispatch'][] = ["name"=>"Pending Qty","textAlign"=>"center"]; 
    $data['jobMaterialDispatch'][] = ["name"=>"Status","textAlign"=>"center"]; 

    /* Item Header */
    $data['items'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['items'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['items'][] = ["name"=>"Item Code"];
    $data['items'][] = ["name"=>"Item Name"];
    $data['items'][] = ["name"=>"HSN Code"];
    //$data['items'][] = ["name"=>"Opening Qty"];
    $data['items'][] = ["name"=>"Stock Type"];
    $data['items'][] = ["name"=>"Stock Qty"];
    $data['items'][] = ["name"=>"Manage Stock"];

    /* Capital Goods Header */
    $data['capitalGoods'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['capitalGoods'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['capitalGoods'][] = ["name"=>"Item Name"];
    $data['capitalGoods'][] = ["name"=>"Category"];
    $data['capitalGoods'][] = ["name"=>"Opening Qty"];
    $data['capitalGoods'][] = ["name"=>"Stock Qty"];
    $data['capitalGoods'][] = ["name"=>"Manage Stock"];

    /* Item Header */
	$data['storeItem'][] = ["name"=>"#","style"=>"width:5%;","srnoPosition"=>0];
	$data['storeItem'][] = ["name"=>"Item Code"];
    $data['storeItem'][] = ["name"=>"Item Name"];
    $data['storeItem'][] = ["name"=>"HSN Code"];
    $data['storeItem'][] = ["name"=>"Opening Qty","textAlign"=>"right"];
    $data['storeItem'][] = ["name"=>"Stock Qty","textAlign"=>"right"];
    
    /* FG Item Header */
	$data['fgLedger'][] = ["name"=>"#","style"=>"width:5%;","srnoPosition"=>0];
	$data['fgLedger'][] = ["name"=>"Item Code"];
    $data['fgLedger'][] = ["name"=>"Item Name"];
    $data['fgLedger'][] = ["name"=>"HSN Code"];
    $data['fgLedger'][] = ["name"=>"Opening Qty","textAlign"=>"right"];
    $data['fgLedger'][] = ["name"=>"Packing Qty","textAlign"=>"right"];
    $data['fgLedger'][] = ["name"=>"RTD Qty","textAlign"=>"right"];
    $data['fgLedger'][] = ["name"=>"Total Stock","textAlign"=>"right"];

    /* LIST OF STOCK VERIFICATION  */
    $data['stockVerification'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center","srnoPosition"=>0];
    $data['stockVerification'][] = ["name"=>"Part Name"];
    $data['stockVerification'][] = ["name"=>"Part No."];
    $data['stockVerification'][] = ["name"=>"Stock Register Qty."];
    $data['stockVerification'][] = ["name"=>"Physical Qty."];
    $data['stockVerification'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];


	/* Stock Journal Header */
    $data['stockJournal'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['stockJournal'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
    $data['stockJournal'][] = ["name"=>"Date"];
    $data['stockJournal'][] = ["name"=>"RM Item Name"];
    $data['stockJournal'][] = ["name"=>"RM Qty."];
    $data['stockJournal'][] = ["name"=>"FG Item Name"];
    $data['stockJournal'][] = ["name"=>"FG Qty."];
    $data['stockJournal'][] = ["name"=>"Remark"];

    /* GRN Header */
    $data['grn'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['grn'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['grn'][] = ["name"=>"GRN No."];
	$data['grn'][] = ["name"=>"Challan No."];
    $data['grn'][] = ["name"=>"GRN Date"];
    $data['grn'][] = ["name"=>"Order No."];
    $data['grn'][] = ["name"=>"Supplier/Customer"];
    $data['grn'][] = ["name"=>"Item"];
    $data['grn'][] = ["name"=>"Qty"];
    $data['grn'][] = ["name"=>"UOM"];
    $data['grn'][] = ["name"=>"Qty.(Opt. UOM)"];
    $data['grn'][] = ["name"=>"F.G.(Used In)"];
    $data['grn'][] = ["name"=>"Heat/Batch No."];
    $data['grn'][] = ["name"=>"Colour Code"];	
    $data['grn'][] = ["name"=>"Location"];		
	
	 /* General Material Issue */
    $data['generalIssue'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];
	$data['generalIssue'][] = ["name"=>"#","style"=>"width:4%;","textAlign"=>"center"];
	$data['generalIssue'][] = ["name"=>"Issue Date","style"=>"width:4%;","textAlign"=>"center"];
	$data['generalIssue'][] = ["name"=>"Issue By","style"=>"width:4%;","textAlign"=>"center"];
	$data['generalIssue'][] = ["name"=>"Collected By","style"=>"width:4%;","textAlign"=>"center"];
	$data['generalIssue'][] = ["name"=>"No. Of. Item","style"=>"width:4%;","textAlign"=>"center"];
	
    /* Item Header */
	$data['rmStock'][] = ["name"=>"#","style"=>"width:5%;","srnoPosition"=>0];
	$data['rmStock'][] = ["name"=>"ID"];
    $data['rmStock'][] = ["name"=>"LID"];
    $data['rmStock'][] = ["name"=>"Item Name"];
    $data['rmStock'][] = ["name"=>"Location"];
    $data['rmStock'][] = ["name"=>"Batch No"];
    $data['rmStock'][] = ["name"=>"Current Stock","textAlign"=>"right"];
    
    /* General Material Pending Request */
    $data['pendingRequest'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];
	$data['pendingRequest'][] = ["name"=>"#","style"=>"width:4%;","textAlign"=>"center"];
    $data['pendingRequest'][] = ["name"=>"Request Date","textAlign"=>"center"];
    $data['pendingRequest'][] = ["name"=>"Item Name"];
    $data['pendingRequest'][] = ["name"=>"Stock Qty","textAlign"=>"center"];
    $data['pendingRequest'][] = ["name"=>"Requested Qty","textAlign"=>"center"];    
    $data['pendingRequest'][] = ["name"=>"Issue Qty","textAlign"=>"center"];
    $data['pendingRequest'][] = ["name"=>"Pending Qty","textAlign"=>"center"]; 
    
    /* General Material Request */
    $data['generalMaterialRequest'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];
	$data['generalMaterialRequest'][] = ["name"=>"#","style"=>"width:4%;","textAlign"=>"center"];
    $data['generalMaterialRequest'][] = ["name"=>"Request Date","textAlign"=>"center"];
    $data['generalMaterialRequest'][] = ["name"=>"Item Name"];
    $data['generalMaterialRequest'][] = ["name"=>"Stock Qty","textAlign"=>"center"];
    $data['generalMaterialRequest'][] = ["name"=>"Requested Qty","textAlign"=>"center"]; 
    
    /* Tools Issue Header */
    $data['toolsIssue'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];
    $data['toolsIssue'][] = ["name"=>"#","style"=>"width:4%;","textAlign"=>"center"];
    $data['toolsIssue'][] = ["name"=>"Issue Date","textAlign"=>"center"];
    $data['toolsIssue'][] = ["name"=>"Issue No.","textAlign"=>"center"];
    $data['toolsIssue'][] = ["name"=>"Item Name","textAlign"=>"center"];
    $data['toolsIssue'][] = ["name" => "Batch No", "textAlign" => "center"];
    $data['toolsIssue'][] = ["name" => "Issue Qty", "textAlign" => "center"];
    $data['toolsIssue'][] = ["name" => "Return Qty", "textAlign" => "center"];
    $data['toolsIssue'][] = ["name" => "Pending Qty", "textAlign" => "center"];

    /* Inspection Material */
    $data['inspectionMaterial'][] = ["name" => "#", "style" => "width:4%;", "textAlign" => "center" , "srnoPosition" => 0];
    $data['inspectionMaterial'][] = ["name" => "Issue No.", "style" => "width:4%;", "textAlign" => "center"];
    $data['inspectionMaterial'][] = ["name" => "Item Name", "textAlign" => "center"];
    $data['inspectionMaterial'][] = ["name" => "Serial No", "textAlign" => "center"];
    $data['inspectionMaterial'][] = ["name" => "Return Qty", "textAlign" => "center"];
    $data['inspectionMaterial'][] = ["name" => "Used", "textAlign" => "center"];
    // $data['inspectionMaterial'][] = ["name" => "Fresh", "textAlign" => "center"];
    $data['inspectionMaterial'][] = ["name" => "Scrap", "textAlign" => "center"];
    $data['inspectionMaterial'][] = ["name" => "Regrinding ", "textAlign" => "center"];
    $data['inspectionMaterial'][] = ["name" => "Convert to Other", "textAlign" => "center"];
    $data['inspectionMaterial'][] = ["name" => "Broken", "textAlign" => "center"];
    $data['inspectionMaterial'][] = ["name" => "Missed", "textAlign" => "center"];
    $data['inspectionMaterial'][] = ["name" => "Action", "style" => "width:5%;", "textAlign" => "center"];

    /* Regrinding Inspection Material */
    $masterCheckBox = '<input type="checkbox" id="masterSelect" class="filled-in chk-col-success BulkRequest" value=""><label for="masterSelect">ALL</label>';
    $data['pendingRegrinding'][] = ["name"=>$masterCheckBox,"style"=>"width:10%;","textAlign"=>"center","orderable"=>"false"];
    $data['pendingRegrinding'][] = ["name" => "#", "style" => "width:4%;", "textAlign" => "center" ];
    $data['pendingRegrinding'][] = ["name" => "Issue No.", "style" => "width:4%;", "textAlign" => "center"];
    $data['pendingRegrinding'][] = ["name" => "Item Name", "textAlign" => "center"];
    $data['pendingRegrinding'][] = ["name" => "Batch No", "textAlign" => "center"];
    $data['pendingRegrinding'][] = ["name" => "Regrinding ", "textAlign" => "center"];

    /* Regrinding Challan Data */
    $data['regrindingChallan'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['regrindingChallan'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['regrindingChallan'][] = ["name"=>"Challan Date"];
    $data['regrindingChallan'][] = ["name"=>"Challan No"];
    $data['regrindingChallan'][] = ["name"=>"Party"];

    /* Regrinding Challan Data */
    $data['regrindingInspection'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['regrindingInspection'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['regrindingInspection'][] = ["name"=>"Challan Date"];
    $data['regrindingInspection'][] = ["name"=>"Challan No"];
    $data['regrindingInspection'][] = ["name"=>"Party"];
    $data['regrindingInspection'][] = ["name"=>"Item"];
    $data['regrindingInspection'][] = ["name"=>"Serial No"];
    $data['regrindingInspection'][] = ["name"=>"Receive Date"];
    $data['regrindingInspection'][] = ["name"=>"In Challan No"];
    $data['regrindingInspection'][] = ["name"=>"Size"];
    $data['regrindingInspection'][] = ["name"=>"Receive Size"];
    $data['regrindingInspection'][] = ["name"=>"Regrinding Reason"];

    /*Regrinding Reason Data */
    $data['regrindingReason'][] = ["name" => "Action", "style" => "width:5%;"];
    $data['regrindingReason'][] = ["name" => "#", "style" => "width:5%;"];
    $data['regrindingReason'][] = ["name" => "Reamrk"];

    /* ReRegrinding Inspection Material */
    $masterCheckBox = '<input type="checkbox" id="masterSelect" class="filled-in chk-col-success BulkRequest" value=""><label for="masterSelect">ALL</label>';
    $data['reRegrinding'][] = ["name"=>$masterCheckBox,"style"=>"width:10%;","textAlign"=>"center","orderable"=>"false", "srnoPosition" => 0];
    $data['reRegrinding'][] = ["name" => "#", "style" => "width:4%;", "textAlign" => "center" ];
    $data['reRegrinding'][] = ["name" => "Date", "style" => "width:4%;", "textAlign" => "center"];
    $data['reRegrinding'][] = ["name" => "In Challan No", "style" => "width:4%;", "textAlign" => "center"];
    $data['reRegrinding'][] = ["name" => "Party", "style" => "width:4%;", "textAlign" => "center"];
    $data['reRegrinding'][] = ["name" => "Item Name", "textAlign" => "center"];
    $data['reRegrinding'][] = ["name" => "Batch No", "textAlign" => "center"];
    $data['reRegrinding'][] = ["name" => "Size ", "textAlign" => "center"];
    $data['reRegrinding'][] = ["name" => "Received Size ", "textAlign" => "center"];

    /* Npd Rm Issue */
    $data['npdMaterialIssue'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];
	$data['npdMaterialIssue'][] = ["name"=>"#","style"=>"width:4%;","textAlign"=>"center"];
    $data['npdMaterialIssue'][] = ["name"=>"Request Date","textAlign"=>"center"];
    $data['npdMaterialIssue'][] = ["name"=>"Npd Job No","textAlign"=>"center"];
    $data['npdMaterialIssue'][] = ["name"=>"Item Name"];
    //$data['npdMaterialIssue'][] = ["name"=>"Stock Qty","textAlign"=>"center"];
    $data['npdMaterialIssue'][] = ["name"=>"Requested Qty","textAlign"=>"center"];    
    $data['npdMaterialIssue'][] = ["name"=>"Issue Qty","textAlign"=>"center"];
    $data['npdMaterialIssue'][] = ["name"=>"Pending Qty","textAlign"=>"center"];

    return tableHeader($data[$page]);
}

/* Store Table Data */
function getStoreData($data){
    $deleteParam = $data->id.",'Store'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editStoreLocation', 'title' : 'Update Store Location'}";

    $editButton=''; $deleteButton='';
    if($data->store_type == 0){
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    }
	$action = getActionButton($editButton.$deleteButton);
	
    return [$action,$data->sr_no,$data->store_name,$data->location,$data->remark];
}


/* Item Table Data */
function getItemsData($data){
    $deleteParam = $data->id.",'Item'";
    $editParam = "{'id' : ".$data->id.",'item_type' :".$data->item_type.", 'modal_id' : 'modal-lg', 'form_id' : 'editItem', 'title' : 'Update Item', 'fnsave' : 'save'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $action = getActionButton($editButton.$deleteButton);    

    $updateStockBtn = "";
    /* $updateStockBtn = ($data->rm_type == 0)?'<button type="button" class="btn waves-effect waves-light btn-outline-warning itemStockUpdate permission-modify" data-id="'.$data->id.'" data-item_name="'.$data->item_name.'" data-button="close" data-modal_id="modal-lg" data-function="addStockTrans" data-form_title="Update Stock">Update Stock</button>':''; */

	$mq = '';
    if($data->qty < $data->min_qty){ $mq = 'text-danger'; }
	$qty = '<a href="'.base_url('reports/productReport/itemWiseStock/'.$data->id).'" class="'.$mq.'">'.$data->qty.' ('.$data->unit_name.')</a>';

    $openingStock = '<button type="button" class="btn waves-effect waves-light btn-outline-primary itemOpeningStock permission-modify" data-id="'.$data->id.'" data-item_name="'.$data->item_name.'" data-button="close" data-modal_id="modal-lg" data-function="addOpeningStock" data-form_title="Opening Stock">Opening Stock</button>';
	
    return [$action,$data->sr_no,$data->item_code,$data->item_name,$data->hsn_code,$qty,$openingStock.' '.$updateStockBtn];
}

/* Capital Goods Table Data */
function getCapitalGoods($data){
    $deleteParam = $data->id.",'Item'";
    $editParam = "{'id' : ".$data->id.",'item_type' :".$data->item_type.", 'modal_id' : 'modal-lg', 'form_id' : 'editItem', 'title' : 'Update Item', 'fnsave' : 'save'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $action = getActionButton($editButton.$deleteButton);    

    $updateStockBtn = "";
    /* $updateStockBtn = ($data->rm_type == 0)?'<button type="button" class="btn waves-effect waves-light btn-outline-warning itemStockUpdate permission-modify" data-id="'.$data->id.'" data-item_name="'.$data->item_name.'" data-button="close" data-modal_id="modal-lg" data-function="addStockTrans" data-form_title="Update Stock">Update Stock</button>':''; */

	$mq = '';
    if($data->qty < $data->min_qty){ $mq = 'text-danger'; }
	$qty = '<a href="'.base_url('reports/productReport/itemWiseStock/'.$data->id).'" class="'.$mq.'">'.$data->qty.' ('.$data->unit_name.')</a>';

    $openingStock = '<button type="button" class="btn waves-effect waves-light btn-outline-primary itemOpeningStock permission-modify" data-id="'.$data->id.'" data-item_name="'.$data->item_name.'" data-button="close" data-modal_id="modal-lg" data-function="addOpeningStock" data-form_title="Opening Stock">Opening Stock</button>';
	
    return [$action,$data->sr_no,$data->item_name,$data->category_name,$data->opening_qty.' ('.$data->unit_name.')',$qty,$openingStock.' '.$updateStockBtn];
}

/* Store Item Table Data */
function getStoreItemData($data){
    $mq = '';
    if($data->qty < $data->min_qty){ $mq = 'text-danger'; }
	$qty = '<a href="'.base_url('store/itemStockTransfer/'.$data->id).'" class="'.$mq.'">'.number_format($data->qty ,3).' ('.$data->unit_name.')</a>';
	
    return [$data->sr_no,$data->item_code,$data->item_name,$data->hsn_code,$data->opening_qty.' ('.$data->unit_name.')',$qty];
}

/* Fg Ledger Table Data */
function getfgLedgerData($data){
    $mq = '';
    if($data->rtd_qty < $data->min_qty){ $mq = 'text-danger'; }
	$rtd_qty = '<a href="'.base_url('store/itemStockTransfer/'.$data->id.'/'.$data->item_type.'/'.$data->rtd_location).'" class="'.$mq.'" target="_blank">'.number_format($data->rtd_qty ,3).' ('.$data->unit_name.')</a>';
	$par_qty = '<a href="'.base_url('store/itemStockTransfer/'.$data->id.'/'.$data->item_type.'/'.$data->prod_location).'" class="'.$mq.'" target="_blank">'.number_format($data->par_qty ,3).' ('.$data->unit_name.')</a>';
	
    return [$data->sr_no,$data->item_code,$data->item_name,$data->hsn_code,$data->opening_qty.' ('.$data->unit_name.')',$par_qty,$rtd_qty,$data->stock_qty];
}

/* Process Table Data */
function getStoresData($page,$data){
	
	switch($page)
	{
		case 'purchaseReport':
						return [$data->sr_no,$data->item_code,$data->item_name,$data->hsn_code,printDecimal($data->gst),printDecimal($data->qty)];
						break;
		case 'products':
						break;
	}
	return [];
}

/* Stock Journal Data */
function getStockjournalData($data){
    $deleteParam = $data->id.",'Stock journal'";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    $action = getActionButton($deleteButton);   
    return [$action,$data->sr_no,formatDate($data->date),$data->rm_name,$data->rm_qty,$data->fg_name,$data->fg_qty,$data->remark];
}

/* GRN Table Data */
function getGRNData($data){
    $deleteParam = $data->grn_id.",'GRN'";$itemList = "";$mailBtn='';
    $ref_no = str_replace('/','_',getPrefixNumber($data->grn_prefix,$data->grn_no));
    $emailParam = $data->grn_id.",'".$ref_no."'";

    $itemList = '<a href="javascript:void(0)" class="btn btn-primary createItemList permission-read" data-id="'.$data->grn_id.'" data-party_name="'.$data->party_name.'" datatip="Item List" flow="down"><i class="fa fa-list" ></i></a>';

    $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->grn_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';

    $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	
	$printBtn = '<a class="btn btn-info btn-edit permission-read" href="'.base_url($data->controller.'/printGrn/'.$data->grn_id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';
    $identTag = '<a class="btn btn-danger btn-edit permission-read" href="'.base_url($data->controller.'/materialIdentTag/'.$data->id).'" target="_blank" datatip="Print Identification Tag" flow="down"><i class="fas fa-print" ></i></a>';
    $mailBtn = '<a class="btn btn-warning permission-read" href="javascript:void(0)" onclick="sendEmail('.$emailParam.')" datatip="Send Mail" flow="down"><i class="fas fa-envelope" ></i></a>';
	$action = '';$order_no = "";
	if($data->type == 1 && $data->inspected_qty < $data->qty):
		$action = getActionButton($identTag.$printBtn.$mailBtn.$itemList.$edit.$delete);
    else:
        $action = getActionButton($identTag.$printBtn);
    endif;

    if($data->type == 2):
        $action = getActionButton($identTag.$printBtn.$itemList.$edit.$delete);
	endif;

	if(!empty($data->po_no) and !empty($data->po_prefix)):
		$order_no = getPrefixNumber($data->po_prefix,$data->po_no);
	endif;
    return [$action,$data->sr_no,getPrefixNumber($data->grn_prefix,$data->grn_no),$data->challan_no,formatDate($data->grn_date),$order_no,$data->party_name,$data->item_name,$data->qty,$data->unit_name,$data->qty_kg,$data->product_code,$data->batch_no,$data->color_code,$data->store_name]; 
}

/* General Issue Table Data */
function getGeneralIssueData($data){
    $deleteParam = $data->id.",'Material'";
    $dispatchParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'dispatchMaterial', 'title' : 'Tools Issue'}";

    $dispatchBtn = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="dispatch('.$dispatchParam.');"><i class="ti-pencil-alt"></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)"  onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    
    $itemListButton = '<a href="javascript:void(0)" class="btn btn-primary  permission-read viewMaterialIssueTrans" data-id="'.$data->id.'"  datatip="Item List" flow="down"  ><i class="fa fa-list" ></i></a>';

    $action = getActionButton($itemListButton.$dispatchBtn.$deleteButton);

    return [$action,$data->sr_no,(!empty($data->dispatch_date))?date("d-m-Y",strtotime($data->dispatch_date)):"",$data->issue_by,$data->collect_by,$data->total_item];
}


function getStoreRMItemData($data){
    return [$data->item_id,$data->item_id,$data->location_id,$data->item_name,$data->location.'( '.$data->store_name.' )',$data->batch_no,number_format($data->current_stock ,3)];
}

function getGeneralPendingRequestData($data){
    $deleteParam = $data->id.",'Material'";  $pendingQty=0; 
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)"  onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    
    $issueParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'editStoreLocation', 'title' : 'Issue General Material'}";    
    
    $issueButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Issue Material" flow="down" onclick="edit('.$issueParam.');"><i class="fas fa-paper-plane"></i></a>';
    $pendingQty = $data->req_qty - $data->dispatch_qty;
    $action = getActionButton($issueButton.$deleteButton);
    return [$action,$data->sr_no,(!empty($data->req_date))?date("d-m-Y",strtotime($data->req_date)):"",$data->item_name,floatVal($data->req_item_stock),floatVal($data->req_qty),floatVal($data->dispatch_qty),$pendingQty];
}

function getGeneralRequestData($data)
{
    $deleteParam = $data->id.",'Request'";   
    $deleteButton = '<a class="btn btn-danger btn-delete" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    
    $action = getActionButton($deleteButton);
    return [$action,$data->sr_no,(!empty($data->req_date))?date("d-m-Y",strtotime($data->req_date)):"",$data->item_name,floatVal($data->req_item_stock),floatVal($data->req_qty)];
    
}

// Created By Meghavi
/* Job Material Dispatch Table Data */
function getJobMaterialIssueData($data){
    $deleteParam = $data->id.",'Dispatch'";
    $shortClose = '';
    $consumptionBtn ="";
    $dispatchBtn="";
    $requestParamBtn="";
    $deleteButton="";
    
    $pendingQty = $data->req_qty - $data->dispatch_qty;
    $pendingQty = ($pendingQty < 0)?0:floatVal(round($pendingQty,3));
    
    if($pendingQty > 0):
        $shortClose = '<a class="btn btn-instagram btn-shortClose changeOrderStatus permission-modify" href="javascript:void(0)" datatip="Close" flow="down" data-val="1" data-id="'.$data->id.'"><i class="ti-close"></i></a>';

        $dispatchParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'dispatchMaterial', 'title' : 'Material Issue'}";

        $consumptionBtn = "{'id' : ".$data->product_id.",'job_card_id' : ".$data->job_card_id.", 'modal_id' : 'modal-md', 'form_id' : 'toolConsumption', 'title' : 'Tool Consumption'}";

        $consumptionBtn = '<a class="btn btn-warning btn-consumption permission-modify" href="javascript:void(0)" datatip="Tool Consumption" flow="down" onclick="consumption('.$consumptionBtn.');"><i class="fas fa-wrench"></i></a>';

        $dispatchBtn = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Material Issue" flow="down" onclick="dispatch('.$dispatchParam.');"><i class="fas fa-paper-plane"></i></a>';
        
        $requestParamBtn = '<a class="btn btn-info btn-request permission-modify" href="javascript:void(0)" datatip="Purchase Request" flow="down" onclick="request('.$data->id.');"><i class="icon-Check"></i></a>';

        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    endif;
    $action = getActionButton($shortClose.$dispatchBtn.$deleteButton);
    //if($data->req_type == 0 && $data->req_status == 0) {$action = getActionButton($requestParamBtn.$dispatchBtn.$deleteButton);}

    $itemName = (!empty($data->dispatch_item_name))?$data->dispatch_item_name:$data->req_item_name;

    $stockQty = (!empty($data->dispatch_item_name))?$data->dispatch_item_stock:$data->req_item_stock;
    
    $unitName = (!empty($data->dis_unit_name))?$data->dis_unit_name:$data->req_unit_name;

    
    
    return [$action,$data->sr_no,(!empty($data->job_no))?getPrefixNumber($data->job_prefix,$data->job_no):"General Issue",(!empty($data->req_date))?date("d-m-Y",strtotime($data->req_date)):"",$itemName,floatVal($stockQty)." (".$unitName.") ",$data->req_qty,$data->dispatch_qty,(!empty($data->dispatch_date))?date("d-m-Y",strtotime($data->dispatch_date)):"",$pendingQty,$data->order_status_label];
}

function getToolsIssueData($data){

    $deleteParam = $data->id.",'Dispatch'";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $pendingQty = $data->issue_qty - (!empty($data->return_qty) ? $data->return_qty : 0);
    $issueBtn = "";
    if($data->is_returnable == 1):
        $title = '['.$data->item_code.'] '.$data->item_name.' [<small> Pending Qty : '.$pendingQty.' </small>]';
        $issueParam = "{'id' : " . $data->id . ",'batch_no':'".$data->batch_no."','pending_qty':".$pendingQty.",'size':'".$data->size."', 'modal_id' : 'modal-xl', 'form_id' : 'materialReturn', 'title' : '".$title."','fnedit' : 'returnForm', 'fnsave' : 'saveReturnMaterial'}";
        $issueBtn = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Return" flow="down" onclick="returnMaterial(' . $issueParam . ');"><i class="fas fa-paper-plane"></i></a>';
    endif;

    $issue_date = (!empty($data->issue_date))?date("d-m-Y",strtotime($data->issue_date)):"";
    $deleteButton="";
    $action = getActionButton($issueBtn.$deleteButton);
    return [$action, $data->sr_no, $issue_date, (!empty($data->issue_no)?$data->issue_number:''), $data->item_name, $data->batch_no, $data->issue_qty, $data->return_qty, $pendingQty];
}


function getInspectionData($data)
{
    $storeParam = "{'id' : " . $data->id . ", 'modal_id' : 'modal-lg', 'form_id' : 'storeLocation', 'title' : '".$data->item_name."','fnEdit' : 'inspectionView', 'fnsave' : 'saveInspLocation'}";
    $storeBtn = '<a class="btn btn-facebook btn-sm" href="javascript:void(0)" datatip="Store Location" flow="down" onclick="edit(' . $storeParam . ');"><i class="fas fa-database"></i></i></a>';
    $used_qty = '<input id="used_qty_'.$data->id.'" value="'.$data->used_qty.'" type="text" class="form-control">';
    $used_qty .= '<input id="fresh_qty_'.$data->id.'" value="'.$data->fresh_qty.'" type="hidden" class="form-control">';
    $scrap_qty = '<input id="scrap_qty_'.$data->id.'" value="'.$data->scrap_qty.'" type="text" class="form-control">';
    $regranding_qty ='<div class="input-group">
                        <input id="regranding_qty_'.$data->id.'"  value="'.$data->regranding_qty.'" type="text" class="form-control">
                        <input id="regrinding_reason_'.$data->id.'" value="0" type="hidden">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-success regrindingReason" data-btn_id="'.$data->id.'">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div><div class="error regrinding_reason'.$data->id.'"></div>';
    $convert_item = '<div class="input-group">
                        <input id="convert_qty_'.$data->id.'" value="0" type="text" class="form-control">
                        <input id="convert_item_id_'.$data->id.'" value="0" type="hidden">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-success convertItem" data-btn_id="'.$data->id.'">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div><div class="error convert_qty'.$data->id.'"></div>';
    $broken_qty = '<input id="broken_qty_'.$data->id.'" value="'.$data->broken_qty.'" type="text" class="form-control">';
    $miss_qty = '<input id="miss_qty_'.$data->id.'" value="'.$data->missed_qty.'" type="text" class="form-control">';
    $save_btn = '<input id="return_qty_'.$data->id.'" value="'.$data->return_qty.'" type="hidden">
                <button type="button" class="btn btn-success saveInspection" data-btn_id="'.$data->id.'">Save</button>
                <div class="error genral_error'.$data->id.'"></div>';
    //$action = getActionButton($vieButton);
    if(empty($data->status)){
        return [$data->sr_no, $data->issue_number, $data->item_name,$data->batch_no, $data->return_qty, $used_qty, $scrap_qty, $regranding_qty, $convert_item, $broken_qty, $miss_qty, $save_btn];
    }elseif($data->status == 1){
        return [$data->sr_no, $data->issue_number, $data->item_name,$data->batch_no, $data->return_qty, $data->used_qty, $data->scrap_qty, $data->regranding_qty, $data->convert_qty, $data->broken_qty, $data->missed_qty, $storeBtn];
    }elseif($data->status == 2){
        return [$data->sr_no, $data->issue_number, $data->item_name,$data->batch_no, $data->return_qty, $data->used_qty, $data->scrap_qty, $data->regranding_qty, $data->convert_qty, $data->broken_qty, $data->missed_qty, $data->statusText];
    }
}

/*** Reginding Inspection Data */
function getRegrindingData($data)
{
    $selectBox = '<input type="checkbox" name="ref_id[]" id="ref_id_'.$data->sr_no.'" class="filled-in chk-col-success BulkRequest" value="'.$data->id.'"><label for="ref_id_'.$data->sr_no.'"></label>';
    return [$selectBox,$data->sr_no, sprintf("ISU%05d", $data->issue_no), $data->item_name,$data->batch_no,$data->regranding_qty];
}

/*** Regrinding Challan */
function getRegrindingChallanData($data){
    $receiveBtn ='';
    if(empty($data->trans_status)){
        $receiveParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'receive_challan', 'title' : 'Receive Challan',fnsave: 'saveReceiveItem',fnEdit: 'receiveChallan'}";
        $receiveBtn = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Receive Challan" flow="down" onclick="edit('.$receiveParam.');"><i class="fas fa-paper-plane" ></i></a>';
    }
    
    $challanView = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'regChData', 'title' : 'Regrinding Challan','fnEdit': 'challanView','button':'close'}";
    $challanViewBtn = '<a class="btn btn-facebook btn-edit permission-modify" href="javascript:void(0)" datatip="Challan Detail" flow="down" onclick="edit('.$challanView.');"><i class="fa fa-eye" ></i></a>';

    $printChallanBtn = '<a class="btn btn-primary" href="'.base_url($data->controller.'/regrindingChallanPrint/'.$data->id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';

    $action = getActionButton($printChallanBtn.$receiveBtn.$challanViewBtn);
    return [$action,$data->sr_no,formatDate($data->trans_date),getPrefixNumber($data->trans_prefix,$data->trans_no),$data->party_name];
}

/*** Regrinding Challan */

function getRegrindingInspectionData($data){
    $inspBtn ='';
    $inspParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'receive_challan', 'title' : 'Inspection',fnsave: 'saveInspectedChallanItem',fnEdit: 'inspectReceivedChallan'}";
    $inspBtn = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Inspection" flow="down" onclick="edit('.$inspParam.');"><i class="fa fa-check" ></i></a>';

    $action = getActionButton($inspBtn);
    return [$action,$data->sr_no,formatDate($data->trans_date),getPrefixNumber($data->trans_prefix,$data->trans_no),$data->party_name,$data->item_name,$data->batch_no,formatDate($data->cod_date),$data->drg_rev_no,$data->rev_no,$data->grn_data,$data->regrinding_reason];
}

function getReRegrindingData($data){
    $selectBox = '<input type="checkbox" name="ref_id[]" id="ref_id_'.$data->sr_no.'" class="filled-in chk-col-success BulkRequest" value="'.$data->id.'"><label for="ref_id_'.$data->sr_no.'"></label>';
    return [$selectBox,$data->sr_no, formatDate($data->trans_date),$data->drg_rev_no,$data->party_name, $data->item_name,$data->batch_no,$data->rev_no,$data->grn_data];
}

/*Regrinding Reason Table Data */
function getRegrindingReasonData($data)
{
    $deleteParam = $data->id.",'RegrindingReason'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editRegrindingReason', 'title' : 'Update Regrinding Reason'}";
    
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->remark];
}

function getNpdIssueData($data){
    $deleteParam = $data->id.",'Material'";  $pendingQty=0; 
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)"  onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    
    $issueParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'editStoreLocation', 'title' : 'Issue General Material'}";    
    
    $issueButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Issue Material" flow="down" onclick="edit('.$issueParam.');"><i class="fas fa-paper-plane"></i></a>';
    $pendingQty = $data->req_qty - $data->dispatch_qty;
    $action = getActionButton($issueButton);
    return [$action,$data->sr_no,(!empty($data->req_date))?date("d-m-Y",strtotime($data->req_date)):"",getPrefixNumber($data->job_prefix,$data->job_no),$data->item_name,floatVal($data->req_qty),floatVal($data->dispatch_qty),$pendingQty];
}
?>