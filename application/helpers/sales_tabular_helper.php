<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');
/* get Pagewise Table Header */

function getSalesDtHeader($page){
    
    /* Lead Header */
    $data['lead'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['lead'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"]; 
	$data['lead'][] = ["name"=>"Company Name"];
	$data['lead'][] = ["name"=>"Contact Person"];
    $data['lead'][] = ["name"=>"Contact No."];
    $data['lead'][] = ["name"=>"Email"];
    $data['lead'][] = ["name"=>"Company Type"];
    $data['lead'][] = ["name"=>"Sector"];
    $data['lead'][] = ["name"=>"Source"];
    $data['lead'][] = ["name"=>"Country"];
    $data['lead'][] = ["name"=>"Created At"];
    $data['lead'][] = ["name"=>"Updated At"];
    
    /* Party Header */
    $data['customer'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['customer'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"]; 
	$data['customer'][] = ["name"=>"Company Name"];
	$data['customer'][] = ["name"=>"Contact Person"];
    $data['customer'][] = ["name"=>"Contact No."];
    $data['customer'][] = ["name"=>"Party Code"];
    $data['customer'][] = ["name"=>"Currency"];
    $data['customer'][] = ["name"=>"Create Date"];
    
	/* Sales Enquiry Header */
	$data['salesEnquiry'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['salesEnquiry'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['salesEnquiry'][] = ["name"=>"Enq. No."];
    $data['salesEnquiry'][] = ["name"=>"Enq. Date"];
    $data['salesEnquiry'][] = ["name"=>"Customer Name"];
    $data['salesEnquiry'][] = ["name"=>"Item Name"];
    $data['salesEnquiry'][] = ["name"=>"Qty"];
    $data['salesEnquiry'][] = ["name"=>"Status"];
    $data['salesEnquiry'][] = ["name"=>"Quoted","style"=>"width:5%;","textAlign"=>"center"];
    $data['salesEnquiry'][] = ["name"=>"Feasible","style"=>"width:5%;","textAlign"=>"center"];
    $data['salesEnquiry'][] = ["name"=>"Not Feasible","style"=>"width:5%;","textAlign"=>"center"];
    $data['salesEnquiry'][] = ["name"=>"Remark"];

	/* Sales Quotation Header */
    $data['salesQuotation'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['salesQuotation'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['salesQuotation'][] = ["name"=>"Quote No."];
    $data['salesQuotation'][] = ["name"=>"Quote Date"];
    $data['salesQuotation'][] = ["name"=>"Customer Name"];
    //$data['salesQuotation'][] = ["name"=>"Product Name"];
    //$data['salesQuotation'][] = ["name"=>"Qty"];
    //$data['salesQuotation'][] = ["name"=>"Quote Price"];
    //$data['salesQuotation'][] = ["name"=>"Confirmed Price"];
    $data['salesQuotation'][] = ["name"=>"Confirmed Date"];
    $data['salesQuotation'][] = ["name"=>"Enq. No."];

    /* Sales Order Header */
    $data['salesOrder'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['salesOrder'][] = ["name"=>"#","textAlign"=>"center"];
	$data['salesOrder'][] = ["name"=>"SO. No.","textAlign"=>"center"];
    $data['salesOrder'][] = ["name"=>"SO. Entry Date","style"=>"width:10%;","textAlign"=>"center"];
    $data['salesOrder'][] = ["name"=>"Slaes Type","textAlign"=>"center"];
    $data['salesOrder'][] = ["name"=>"Customer Name"];
    $data['salesOrder'][] = ["name"=>"Cust. PO.NO."];
	$data['salesOrder'][] = ["name"=>"Quot. No."];
    //$data['salesOrder'][] = ["name"=>"Product"];
    //$data['salesOrder'][] = ["name"=>"Order Qty.","textAlign"=>"center"];
    //$data['salesOrder'][] = ["name"=>"Dispatch Qty.","textAlign"=>"center"];
    //$data['salesOrder'][] = ["name"=>"Pending Qty.","textAlign"=>"center","textAlign"=>"center"];
    $data['salesOrder'][] = ["name"=>"Delivery Date","textAlign"=>"center"]; 
    $data['salesOrder'][] = ["name"=>"Status","textAlign"=>"center"]; 

    /* Delivery Challan Header */
    $data['deliveryChallan'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['deliveryChallan'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
	$data['deliveryChallan'][] = ["name"=>"Challan. No.","textAlign"=>"center"];
    $data['deliveryChallan'][] = ["name"=>"DC. Date","textAlign"=>"center"];
    $data['deliveryChallan'][] = ["name"=>"Customer Name"]; 
    $data['deliveryChallan'][] = ["name"=>"Invoice No."]; 
    //$data['deliveryChallan'][] = ["name"=>"Product Name"]; 
    //$data['deliveryChallan'][] = ["name"=>"Dispatch Qty.","textAlign"=>"center"];

    /* Sales Invoice Header */
    $data['salesInvoice'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];
	$data['salesInvoice'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
	$data['salesInvoice'][] = ["name"=>"Invoice No.","textAlign"=>"center"];
    $data['salesInvoice'][] = ["name"=>"Invoice Date","textAlign"=>"center"];
    $data['salesInvoice'][] = ["name"=>"Invoice Type","textAlign"=>"center"];
    $data['salesInvoice'][] = ["name"=>"Customer Name"]; 
    $data['salesInvoice'][] = ["name"=>"Cust. PO.NO."];
    $data['salesInvoice'][] = ["name"=>"Bill Amount","textAlign"=>"right"];  

	/* packing instruction Header */
	$data['packingInstruction'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['packingInstruction'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
	$data['packingInstruction'][] = ["name"=>"Dispatch Date"];
	$data['packingInstruction'][] = ["name"=>"Item Code"];
	$data['packingInstruction'][] = ["name"=>"Item Name"];
	$data['packingInstruction'][] = ["name"=>"Qty."];
	$data['packingInstruction'][] = ["name"=>"Remark"];
	$data['packingInstruction'][] = ["name"=>"Status","textAlign"=>"center"];

	/* Product Header */
	$data['products'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['products'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['products'][] = ["name"=>"Part Code"];
	$data['products'][] = ["name"=>"Part Name"];
	$data['products'][] = ["name"=>"HSN Code"];
	$data['products'][] = ["name"=>"Part No"];
	$data['products'][] = ["name"=>"Customer Code"];
	$data['products'][] = ["name"=>"Drawing No."];
	$data['products'][] = ["name"=>"Rev. No."];
	$data['products'][] = ["name"=>"Price"];
	$data['products'][] = ["name"=>"Opening Qty"];
	$data['products'][] = ["name"=>"Create Date"];

	/*	Cycle Time Header */
    $data['cycleTime'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['cycleTime'][] = ["name"=>"Part Code"];
    $data['cycleTime'][] = ["name"=>"Manage Time","style"=>"width:20%;"];

    /* Tool Consumption Header */
    $data['toolConsumption'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['toolConsumption'][] = ["name"=>"Tool Description"];
    $data['toolConsumption'][] = ["name"=>"Action","style"=>"width:20%;"];

    /* Proforma Invoice Header */
    $data['proformaInvoice'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['proformaInvoice'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['proformaInvoice'][] = ["name"=>"Invoice No."];
    $data['proformaInvoice'][] = ["name"=>"Invoice Date"];
    $data['proformaInvoice'][] = ["name"=>"Customer Name"]; 
    // $data['proformaInvoice'][] = ["name"=>"Product Name"]; 
    // $data['proformaInvoice'][] = ["name"=>"Product Amount"]; 
    $data['proformaInvoice'][] = ["name"=>"Bill Amount"]; 
    
    /* feasibility Reason Header */
	$data['feasibilityReason'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['feasibilityReason'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
	$data['feasibilityReason'][] = ["name"=>"Type"];
	$data['feasibilityReason'][] = ["name"=>"Feasibility Reason"];

    /* Commercial Packing Header */
    $data['commercialPacking'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];
	$data['commercialPacking'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
    $data['commercialPacking'][] = ["name"=>"Com. Pac. No.","textAlign"=>"center"];
    $data['commercialPacking'][] = ["name"=>"Packing No.","textAlign"=>"center"];
	$data['commercialPacking'][] = ["name"=>"Invoice No.","textAlign"=>"center"];
    $data['commercialPacking'][] = ["name"=>"Invoice Date","textAlign"=>"center"];
    $data['commercialPacking'][] = ["name"=>"Customer Name"]; 
    $data['commercialPacking'][] = ["name"=>"Total Net Weight","textAlign"=>"center"]; 
    $data['commercialPacking'][] = ["name"=>"Total Gross Weight","textAlign"=>"center"]; 
	
	/* Commercial Invoice Header */
    $data['commercialInvoice'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];
	$data['commercialInvoice'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
    $data['commercialInvoice'][] = ["name"=>"Com. INV. No.","textAlign"=>"center"];
    $data['commercialInvoice'][] = ["name"=>"Packing No.","textAlign"=>"center"];
	$data['commercialInvoice'][] = ["name"=>"Invoice No.","textAlign"=>"center"];
    $data['commercialInvoice'][] = ["name"=>"Invoice Date","textAlign"=>"center"];
    $data['commercialInvoice'][] = ["name"=>"Customer Name"]; 
    $data['commercialInvoice'][] = ["name"=>"Net Amount","textAlign"=>"center"]; 

    /* Commercial Packing Header */
    $data['customPacking'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];
	$data['customPacking'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
    $data['customPacking'][] = ["name"=>"Cum. Pac. No.","textAlign"=>"center"];
    $data['customPacking'][] = ["name"=>"Packing No.","textAlign"=>"center"];
	$data['customPacking'][] = ["name"=>"Invoice No.","textAlign"=>"center"];
    $data['customPacking'][] = ["name"=>"Invoice Date","textAlign"=>"center"];
    $data['customPacking'][] = ["name"=>"Customer Name"]; 
    $data['customPacking'][] = ["name"=>"Total Net Weight","textAlign"=>"center"]; 
    $data['customPacking'][] = ["name"=>"Total Gross Weight","textAlign"=>"center"]; 

    /* Custom Invoice Header */
    $data['customInvoice'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];
	$data['customInvoice'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
    $data['customInvoice'][] = ["name"=>"Cum. INV. No.","textAlign"=>"center"];
    $data['customInvoice'][] = ["name"=>"Packing No.","textAlign"=>"center"];
	$data['customInvoice'][] = ["name"=>"Invoice No.","textAlign"=>"center"];
    $data['customInvoice'][] = ["name"=>"Invoice Date","textAlign"=>"center"];
    $data['customInvoice'][] = ["name"=>"Customer Name"]; 
    $data['customInvoice'][] = ["name"=>"Net Amount","textAlign"=>"center"];
    
    /* Request Header */
	$data['packingRequest'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['packingRequest'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
	$data['packingRequest'][] = ["name"=>"Req. Date"];
	$data['packingRequest'][] = ["name"=>"Req. No."];
    $data['packingRequest'][] = ["name"=>"So No."];
	$data['packingRequest'][] = ["name"=>"Customer"];
	$data['packingRequest'][] = ["name"=>"Item Name"];
	$data['packingRequest'][] = ["name"=>"Req. Qty","textAlign"=>"center"];
    $data['packingRequest'][] = ["name"=>"Packed Qty","style"=>"width:250px;","textAlign"=>"center"];
	$data['packingRequest'][] = ["name"=>"Pending Qty","textAlign"=>"center"];
    $data['packingRequest'][] = ["name"=>"Transport"];
    $data['packingRequest'][] = ["name"=>"Remark"];
	
	/* Pending Packing Request Header */
	$data['pendingPackingReq'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['pendingPackingReq'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
	$data['pendingPackingReq'][] = ["name"=>"Req. Date"];
	$data['pendingPackingReq'][] = ["name"=>"Del. Date"];
	$data['pendingPackingReq'][] = ["name"=>"So No."];
	$data['pendingPackingReq'][] = ["name"=>"Customer"];
	$data['pendingPackingReq'][] = ["name"=>"Item Code"];
	$data['pendingPackingReq'][] = ["name"=>"Req. Qty","style"=>"width:250px;","textAlign"=>"center"];
	$data['pendingPackingReq'][] = ["name"=>"Packed Qty","style"=>"width:250px;","textAlign"=>"center"];
	$data['pendingPackingReq'][] = ["name"=>"Pending Qty","style"=>"width:250px;","textAlign"=>"center"];
    $data['pendingPackingReq'][] = ["name"=>"Transport"];
    $data['pendingPackingReq'][] = ["name"=>"Remark","style"=>"width:15%;"];

     /*  Header */
     $data['rfq'][] = ["name"=>"Action","style"=>"width:5%;"];
     $data['rfq'][] = ["name"=>"#","style"=>"width:5%;"];
     $data['rfq'][] = ["name"=>"Enquiry No."];
     $data['rfq'][] = ["name"=>"Enquiry Date"];
     $data['rfq'][] = ["name"=>"Supplier Name"];
     $data['rfq'][] = ["name"=>"Item Description"];
     $data['rfq'][] = ["name"=>"Qty"];
     $data['rfq'][] = ["name"=>"Status"];
 
     /* Responsibility Header */
    $data['responsibility'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['responsibility'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['responsibility'][] = ["name"=>"Remark"];

    /* Request Header */
	$data['dispatchRequest'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['dispatchRequest'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
	$data['dispatchRequest'][] = ["name"=>"Req. Date"];
	$data['dispatchRequest'][] = ["name"=>"Req. No."];
    $data['dispatchRequest'][] = ["name"=>"S.O. No."];
    $data['dispatchRequest'][] = ["name"=>"Cust. Po. No."];
	$data['dispatchRequest'][] = ["name"=>"Customer"];
	$data['dispatchRequest'][] = ["name"=>"Item Name"];
	$data['dispatchRequest'][] = ["name"=>"Req. Qty","textAlign"=>"center"];
    $data['dispatchRequest'][] = ["name"=>"Ch/Inv Qty","style"=>"width:250px;","textAlign"=>"center"];
	$data['dispatchRequest'][] = ["name"=>"Pending Qty","textAlign"=>"center"];
    $data['dispatchRequest'][] = ["name"=>"Remark"];

    /* Dispatch Domestic */
    $data['dispatchDomestic'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['dispatchDomestic'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
	$data['dispatchDomestic'][] = ["name"=>"Challan Date"];
	$data['dispatchDomestic'][] = ["name"=>"Challan No."];
	$data['dispatchDomestic'][] = ["name"=>"Customer"];
	$data['dispatchDomestic'][] = ["name"=>"Item Name"];
	$data['dispatchDomestic'][] = ["name"=>"Challan Qty","textAlign"=>"center"];
	

    /* Revision Checkpoint Master Header */
    $data['reviseChPoint'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['reviseChPoint'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['reviseChPoint'][] = ["name"=>"Item Name"];
    $data['reviseChPoint'][] = ["name"=>"ECN Note No."];
    $data['reviseChPoint'][] = ["name"=>"JJI Rev. No."];
    $data['reviseChPoint'][] = ["name"=>"JJI Rev. Date"];
    $data['reviseChPoint'][] = ["name"=>"Cust. Rev No."];
    $data['reviseChPoint'][] = ["name"=>"Cust Rev. Date"];
    $data['reviseChPoint'][] = ["name"=>"ECN Received Date"];
    $data['reviseChPoint'][] = ["name"=>"Target Date"];
    $data['reviseChPoint'][] = ["name"=>"Material Grade"];
    $data['reviseChPoint'][] = ["name"=>"Status"];

    /* Revision Checkpoint Transaction Header */
    $data['reviseChPointPending'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['reviseChPointPending'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['reviseChPointPending'][] = ["name"=>"Item Name"];
    $data['reviseChPointPending'][] = ["name"=>"ECN No."];
    $data['reviseChPointPending'][] = ["name"=>"Revision No."];
    $data['reviseChPointPending'][] = ["name"=>"Revision Date"];
    $data['reviseChPointPending'][] = ["name"=>"Check Points"];
    $data['reviseChPointPending'][] = ["name"=>"Old Description"];
    $data['reviseChPointPending'][] = ["name"=>"New Description"];
    $data['reviseChPointPending'][] = ["name"=>"Responsibility"];
    $data['reviseChPointPending'][] = ["name"=>"Target Date"];

    /* Revision Checkpoint Review Header */
    $data['reviseChPointReview'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['reviseChPointReview'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['reviseChPointReview'][] = ["name"=>"Item Name"];
    $data['reviseChPointReview'][] = ["name"=>"ECN Note No."];
    $data['reviseChPointReview'][] = ["name"=>"Revision No."];
    $data['reviseChPointReview'][] = ["name"=>"Revision Date"];
    $data['reviseChPointReview'][] = ["name"=>"Drawing No."];
    $data['reviseChPointReview'][] = ["name"=>"ECN No."];
    $data['reviseChPointReview'][] = ["name"=>"ECN Received Date"];
    $data['reviseChPointReview'][] = ["name"=>"Target Date"];
    $data['reviseChPointReview'][] = ["name"=>"Material Grade"];


    /* Control Plan Revision Header */
    $data['controlPlanRev'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['controlPlanRev'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['controlPlanRev'][] = ["name"=>"Item Name"];
    $data['controlPlanRev'][] = ["name"=>"Rev No"];
    $data['controlPlanRev'][] = ["name"=>"Rev Date"];
    $data['controlPlanRev'][] = ["name"=>"Pfc Rev. No."];
    $data['controlPlanRev'][] = ["name"=>"Remark"];
    
    /* Pending Response Header */
    $data['pendingResponse'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['pendingResponse'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"]; 
    $data['pendingResponse'][] = ["name"=>"Company Name"];
    $data['pendingResponse'][] = ["name"=>"Contact Person"];
    $data['pendingResponse'][] = ["name"=>"Contact No."];
    $data['pendingResponse'][] = ["name"=>"Reminder Date"];
    $data['pendingResponse'][] = ["name"=>"Reminder Time"];
    $data['pendingResponse'][] = ["name"=>"Mode"];
    $data['pendingResponse'][] = ["name"=>"Note"];

    /* Sample Invoice Header */
    $data['sampleInvoice'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];
	$data['sampleInvoice'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
    $data['sampleInvoice'][] = ["name"=>"SM. INV. No.","textAlign"=>"center"];
    $data['sampleInvoice'][] = ["name"=>"Packing No.","textAlign"=>"center"];
	$data['sampleInvoice'][] = ["name"=>"Invoice No.","textAlign"=>"center"];
    $data['sampleInvoice'][] = ["name"=>"Invoice Date","textAlign"=>"center"];
    $data['sampleInvoice'][] = ["name"=>"Customer Name"]; 
    $data['sampleInvoice'][] = ["name"=>"Net Amount","textAlign"=>"center"]; 

	return tableHeader($data[$page]);
}

/* Sales Enquiry Table Data */
function getSalesEnquiryData($data){
    $deleteParam = $data->trans_main_id.",'Sales Enquiry'";
    $closeParam = $data->trans_main_id.",'Sales Enquiry'";
    $edit = "";$delete = "";$close = "";$reopen = "";$quotation="";   $changeParty="";$feasibleBtn="";
    if(empty($data->trans_status)):
        $feasibleBtn = '<a href="javascript:void(0)" class="btn btn-warning btn-delete permission-remove" onclick="feasibilityRequest('.$data->id.',1);" datatip="Fesibility Request" flow="down"><i class="fas fa-paper-plane"></i></a>'; 

        $quotation = '<a href="'.base_url('salesQuotation/createQuotation/'.$data->trans_main_id).'" class="btn btn-info permission-write" datatip="Create Quotation" flow="down"><i class="fa fa-file-alt"></i></a>';
        $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->trans_main_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
        $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    
    elseif($data->trans_status == 1 && $data->notFisibalTab != 2):
        $changeParty = '<a href="javascript:void(0);" class="btn btn-warning changeParty" data-trans_main_id="'.$data->trans_main_id.'" data-enq_no="'.(getPrefixNumber($data->trans_prefix,$data->trans_no)).'" data-party_id="'.$data->party_id.'"  datatip="Change Party" flow="down"><i class="fas fa-retweet"></i></a>';
    else:
        $edit = '';//'<a href="'.base_url($data->controller.'/edit/'.$data->trans_main_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
        /*if($data->trans_status == 1):
            $close = '<a href="javascript:void(0)" class="btn btn-dark" onclick="closeEnquiry('.$closeParam.');" datatip="Close Enquiry" flow="down"><i class="ti-close"></i></a>';
        else:
            $reopen = '<a href="javascript:void(0)" class="btn btn-warning" onclick="reopenEnquiry('.$closeParam.');" datatip="Reopen Enquiry" flow="down"><i class="fa fa-retweet"></i></a>';
        endif;*/
    endif;
    
    $quotedCount = ''; $fisibleCount =''; $notfisibalCount ='';
    if($data->notFisibalTab != 2){
        if(!empty($data->quotedCount) > 0){
            $quotedCount = '<a href="javascript:void(0);" class="getFeasibleData" data-status="3" data-trans_main_id="'.$data->trans_main_id.'" data-enq_no="'.(getPrefixNumber($data->trans_prefix,$data->trans_no)).'" datatip="QuotedCount" flow="down"><b>'.$data->quotedCount.'</b></a>';
        }
        if(!empty($data->fisibleCount) > 0){
            $fisibleCount = '<a href="javascript:void(0);" class=" getFeasibleData" data-status="1" data-trans_main_id="'.$data->trans_main_id.'" data-enq_no="'.(getPrefixNumber($data->trans_prefix,$data->trans_no)).'" datatip="FisibleCount" flow="down"><b>'.$data->fisibleCount.'</b></a>';
        }
    } else {
        $quotedCount = "-";
        $fisibleCount = "-";
    }
    if(!empty($data->notfisibalCount) > 0){
        $notfisibalCount = '<a href="javascript:void(0);" class=" getFeasibleData" data-status="2" data-trans_main_id="'.$data->trans_main_id.'" data-enq_no="'.(getPrefixNumber($data->trans_prefix,$data->trans_no)).'" datatip="NotfisibalCount" flow="down"><b>'.$data->notfisibalCount.'</b></a>';
    }
    $feasCostBtn = '';
    if($data->feasible_cost_req == 0){
        $reqParam = "{'id' : ".$data->id.",'item_id' : ".$data->item_id."}";
        $feasCostBtn = '<a href="javascript:void(0)" class="btn btn-warning btn-delete permission-remove" onclick="sentFesibilityCostingReq('.$reqParam.');" datatip="Fesibility & Costing Request" flow="down"><i class="fas fa-paper-plane"></i></a>'; 

    }
    $action = getActionButton($feasCostBtn.$quotation.$edit.$delete.$close.$reopen);
    return [$action,$data->sr_no,getPrefixNumber($data->trans_prefix,$data->trans_no),date("d-m-Y",strtotime($data->trans_date)),$data->party_name,$data->item_name,$data->qty,$data->status,$quotedCount,$fisibleCount,$notfisibalCount,$data->remark];
}

/* Sales Quotation Table Data */
function getSalesQuotationData($data){
    $deleteParam = $data->trans_main_id.",'Sales Quotation'";
    $closeParam = $data->trans_main_id.",'Sales Quotation'";
    
    $confirm = ""; $edit = ""; $delete = ""; $saleOrder =""; $printBtn = ''; $revision = ''; $followup=""; $mailBtn='';
    
    $ref_no = str_replace('/','_',getPrefixNumber($data->trans_prefix,$data->trans_no));
    $emailParam = $data->trans_main_id.",'".$ref_no."'";
    
    if(empty($data->confirm_by)):
        $confirm = '<a href="javascript:void(0)" class="btn btn-info confirmQuotation permission-write" data-id="'.$data->trans_main_id.'" data-quote_id="'.$data->trans_main_id.'"  data-party="'.$data->party_name.'" data-customer_id="'.$data->party_id.'" data-quote_no="'.getPrefixNumber($data->trans_prefix,$data->trans_no).'" data-quotation_date="'.date("d-m-Y",strtotime($data->trans_date)).'" data-button="both" data-modal_id="modal-lg" data-function="getQuotationItems" data-form_title="Confirm Quotation" datatip="Confirm Quotation" flow="down"><i class="fa fa-check"></i></a>';
        $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->trans_main_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
		
		$followup='<a href="javascript:void(0)" class="btn btn-warning addFolloUp permission-write" data-id="'.$data->trans_main_id.'" data-button="both" data-modal_id="modal-lg" data-function="getFollowUp" data-form_title="Follow Up" datatip="Follow Up" flow="down"><i class="fa fa-list-ul"></i></a>';
		
        $revision = '<a href="'.base_url($data->controller.'/reviseQuotation/'.$data->trans_main_id).'" class="btn btn-primary btn-edit permission-modify" datatip="Revision" flow="down"><i class="fa fa-retweet"></i></a>';
		$delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    else:
        $saleOrder = '<a href="javascript:void(0)" class="btn btn-info createSalesOrder permission-write" data-id="'.$data->trans_main_id.'" data-quote_id="'.$data->trans_main_id.'"  data-party="'.$data->party_name.'" data-customer_id="'.$data->party_id.'" data-quote_no="'.getPrefixNumber($data->trans_prefix,$data->trans_no).'" data-quotation_date="'.date("d-m-Y",strtotime($data->trans_date)).'" data-button="both" data-modal_id="modal-lg"  data-form_title="Create Sales Order" datatip="Create Order" flow="down"><i class="fa fa-file-alt"></i></a>';
    endif;
    
    $mailBtn = '<a class="btn btn-info permission-read" href="javascript:void(0)" onclick="sendEmail('.$emailParam.')" datatip="Send Mail" flow="down"><i class="fas fa-envelope" ></i></a>';
	
	$printBtn = '<a class="btn btn-success btn-edit permission-read" href="'.base_url($data->controller.'/printQuotation/'.$data->trans_main_id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';
    $printBtnv2 = '<a class="btn btn-success btn-edit permission-read" href="'.base_url($data->controller.'/printQuotationv2/'.$data->trans_main_id).'" target="_blank" datatip="Printv2" flow="down"><i class="fas fa-print" ></i></a>';
    $printRevisionBtn = '<a class="btn btn-facebook btn-edit permission-read createSalesQuotation"  datatip="View Revised Quatation" data-id="'.$data->trans_main_id.'" data-sq_no="'.getPrefixNumber($data->trans_prefix,$data->trans_no).'" flow="down"><i class="fas fa-eye" ></i></a>';
    
    $action = getActionButton($printBtn.$printBtnv2.$printRevisionBtn.$confirm.$followup.$mailBtn.$revision.$edit.$delete.$saleOrder);
    return [$action,$data->sr_no,getPrefixNumber($data->trans_prefix,$data->trans_no).' (Rev. No.'.$data->quote_rev_no.')',date("d-m-Y",strtotime($data->trans_date)),$data->party_name,(!empty($data->cod_date))?date("d-m-Y",strtotime($data->cod_date)):"",$data->ref_no];
}

/* Sales Order Table Data */
function getSalesOrderData($data){
    $deleteParam = $data->trans_main_id.",'Sales Order'";
    $view = ""; $edit = ""; $delete = ""; $complete = ""; $invoiceCreate = "";$dispatch = ""; $approve='';$invoice = "";$itemList='';$mailBtn='';
    $ref_no = str_replace('/','_',getPrefixNumber($data->trans_prefix,$data->trans_no));
    $emailParam = $data->trans_main_id.",'".$ref_no."'";
    $closeParam = "{'id' : ".$data->trans_main_id.", 'modal_id' : 'modal-lg', 'form_id' : 'closeSalesOrder', 'title' : 'Close Sales Order', 'fnEdit' : 'closeSalesOrder', 'fnsave' : 'saveCloseSO'}";
    $printBtn = '<a class="btn btn-dribbble btn-edit permission-read" href="'.base_url($data->controller.'/salesOrder_pdf/'.$data->trans_main_id).'" target="_blank" datatip="Print Sales Order" flow="down"><i class="fas fa-print" ></i></a>';
	
    if(empty($data->trans_status)):
        if(!empty($data->is_approve == 0)){
            // $approve = '<a href="javascript:void(0)" class="btn btn-facebook approveSOrder permission-approve" data-id="'.$data->trans_main_id.'" data-val="1" data-msg="Approve" datatip="Approve Order" flow="down" ><i class="fa fa-check" ></i></a>';
            $approve = '<a href="javascript:void(0)" onclick="openView('.$data->trans_main_id.')" class="btn btn-info btn-edit permission-approve" datatip="Approve Order" flow="down"><i class="fa fa-check"></i></a>';
            $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->trans_main_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
            $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
        }
        else{
            $approve = '<a href="javascript:void(0)" class="btn btn-facebook approveSOrder permission-approve" data-id="'.$data->trans_main_id.'" data-val="0" data-msg="Reject" datatip="Reject Order" flow="down" ><i class="ti-close" ></i></a>';
			$edit = '<a href="'.base_url($data->controller.'/edit/'.$data->trans_main_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
            $dispatch = '<a href="javascript:void(0)" class="btn btn-primary createDeliveryChallan permission-write" data-id="'.$data->party_id.'" data-party_name="'.$data->party_name.'" datatip="Create Challan" flow="down"><i class="fa fa-truck" ></i></a>';
            $invoice = '<a href="javascript:void(0)" class="btn btn-primary createSalesInvoice permission-write" data-id="'.$data->party_id.'" data-party_name="'.$data->party_name.'" datatip="Create Invoice" flow="down"><i class="fa fa-file-alt" ></i></a>';
            $itemList = '<a href="javascript:void(0)" class="btn btn-primary createItemList permission-read" data-id="'.$data->trans_main_id.'" data-party_name="'.$data->party_name.'" datatip="Item List" flow="down"><i class="fa fa-list" ></i></a>';     
            $mailBtn = '<a class="btn btn-warning permission-read" href="javascript:void(0)" onclick="sendEmail('.$emailParam.')" datatip="Send Mail" flow="down"><i class="fas fa-envelope" ></i></a>';
        }
        $complete = '<a class="btn btn-info btn-solution permission-modify" href="javascript:void(0)" datatip="Short Close" flow="down" onclick="edit('.$closeParam.');"><i class="fa fa-window-close"></i></a>';
        //$complete = '<a href="javascript:void(0)" class="btn btn-warning completeOrderItem permission-modify" data-id="'.$data->id.'" data-val="2" data-msg="Closed" datatip="Short Close" flow="down" ><i class="ti-close" ></i></a>';
        //$edit = '<a href="'.base_url($data->controller.'/edit/'.$data->trans_main_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
        //$delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';     
    endif;    
    // $action = getActionButton($approve.$printBtn.$complete.$dispatch.$invoice.$edit.$delete);
    $action = getActionButton($approve.$printBtn.$itemList.$complete.$mailBtn.$edit.$delete);
    $orderType = "";
    $salesType = "";
    if($data->sales_type == 1):
        $orderType = "Manufacturing";
        $salesType = "Manufacturing (Domestics)";
    elseif($data->sales_type == 2):
        $orderType = "Manufacturing";
        $salesType = "Manufacturing (Export)";
    elseif($data->sales_type == 3):
        $orderType = "Job Order";
        $salesType = "Jobwork (Domestics)";
    endif;
	
	
	$responseData[] = $action;
	$responseData[] = $data->sr_no;
	$responseData[] = getPrefixNumber($data->trans_prefix,$data->trans_no);
    $responseData[] = formatDate($data->trans_date);
    $responseData[] = $salesType;
    $responseData[] = $data->party_name;    
    $responseData[] = $data->doc_no;
	$responseData[] = $data->ref_no;
    //$responseData[] = $data->item_name;
    //$responseData[] = floatVal($data->qty);
    //$responseData[] = floatVal($data->dispatch_qty);
    //$responseData[] = floatVal($data->pending_qty);
    $responseData[] = formatDate($data->cod_date); 	
    $responseData[] = $data->order_status_label;
	return $responseData;
}

/* Delivery Challan */
function getDeliveryChallanData($data){
    $deleteParam = $data->trans_main_id.",'Delivery Challan'";
    $invoice = "";$edit = "";$delete = "";$itemList="";$printBtn="";$backPrint ="";$mailBtn='';
    $ref_no = str_replace('/','_',$data->trans_prefix).'_'.$data->trans_no;
    $emailParam = $data->trans_main_id.",'".$ref_no."'";
	
    //if(empty($data->trans_status)):
    //    $invoice = '<a href="javascript:void(0)" class="btn btn-info createInvoice permission-write" data-id="'.$data->party_id.'" data-party_name="'.$data->party_name.'" datatip="Invoice" flow="down"><i class="fa fa-file-alt" ></i></a>';    
    //    $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->trans_main_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
    //    $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    //endif;
	
	if(empty($data->status)):
	   $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->trans_main_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
       $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    elseif(!empty($data->status) && $data->status == 1):
		$edit = '<a href="'.base_url($data->controller.'/edit/'.$data->trans_main_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
        $mailBtn = '<a class="btn btn-warning permission-read" href="javascript:void(0)" onclick="sendMail('.$emailParam.')" datatip="Send Mail" flow="down"><i class="fas fa-envelope" ></i></a>';
		$invoice = '<a href="javascript:void(0)" class="btn btn-info createInvoice permission-write" data-id="'.$data->party_id.'" data-party_name="'.$data->party_name.'" datatip="Invoice" flow="down"><i class="fa fa-file-alt" ></i></a>';    
        $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	else:
		$mailBtn = '<a class="btn btn-warning permission-read" href="javascript:void(0)" onclick="sendMail('.$emailParam.')" datatip="Send Mail" flow="down"><i class="fas fa-envelope" ></i></a>';
	endif;
    if($data->party_id == 5):
        $backPrint = '<a class="btn btn-danger btn-edit" href="'.base_url('deliveryChallan/back_pdf_forBhavani/'.$data->trans_main_id).'" target="_blank" datatip="Back Print" flow="down"><i class="fas fa-print" ></i></a>';
        
        $printBtn = '<a class="btn btn-warning btn-edit" href="'.base_url('deliveryChallan/challan_pdf_Forbhvani/'.$data->trans_main_id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';
    else:
        $printBtn = '<a href="javascript:void(0)" class="btn btn-warning btn-edit printInvoice" datatip="Print Delivery Challan" flow="down" data-id="'.$data->trans_main_id.'" data-function="challan_pdf"><i class="fa fa-print"></i></a>';
    endif;
    $itemList = '<a href="javascript:void(0)" class="btn btn-primary createItemList permission-read" data-id="'.$data->trans_main_id.'" data-party_name="'.$data->party_name.'" datatip="Item List" flow="down"><i class="fa fa-list" ></i></a>';
    $action = getActionButton($printBtn.$backPrint.$invoice.$mailBtn.$itemList.$edit.$delete);
    return [$action,$data->sr_no,getPrefixNumber($data->trans_prefix,$data->trans_no),formatDate($data->trans_date),$data->party_code,$data->inv_no];
}

/* Packing Instruction Table Data*/
function getPackingInstructionData($data){
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editPacking', 'title' : 'Update Packing Quantity'}";
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    
    $action = getActionButton($editButton);
    return [$action,$data->sr_no,formatDate($data->dispatch_date),$data->item_code,$data->item_name,$data->qty,$data->remark,$data->packing_status_label];
}

/* Sales Invoice Table Data */
function getSalesInvoiceData($data){
    $deleteParam = $data->id.",'Sales Invoice'";$printNew="";
    
    if($data->tp == 'ITEMWISE'){$data->id = $data->trans_main_id;}
    $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
    $copyInv = '<a href="'.base_url($data->controller.'/copyInv/'.$data->id).'" class="btn btn-info btn-edit permission-modify" datatip="Copy" flow="down"><i class="ti-write"></i></a>';

    $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

	$printExport=""; $printCustom=""; $print="";
    if($data->sales_type == 4){
        $printExport = '<a href="javascript:void(0)" class="btn btn-info btn-edit printInvoice permission-approve" datatip="Print Export Invoice" flow="down" data-id="'.$data->id.'" data-function="export_invoice_pdf"><i class="fa fa-print"></i></a>';
    } else if($data->sales_type == 3){
        $printCustom = '<a href="javascript:void(0)" class="btn btn-info btn-edit printInvoice permission-approve" datatip="Print Custom Invoice" flow="down" data-id="'.$data->id.'" data-function="custom_invoice_pdf"><i class="fa fa-print"></i></a>';
    } else if($data->sales_type == 2){        
        $printNew = '<a href="javascript:void(0)" class="btn btn-dark btn-edit printInvoiceNew permission-approve" datatip="Print Invoice" flow="down" data-id="'.$data->id.'" data-function="invoice_pdf1"><i class="fa fa-print"></i></a>';
    } else {
        $print = '<a href="javascript:void(0)" class="btn btn-info btn-edit printInvoice permission-approve" datatip="Print Invoice" flow="down" data-id="'.$data->id.'" data-function="invoice_pdf"><i class="fa fa-print"></i></a>';    
    } $blButton='';
    if($data->entry_type == 8){
        $blParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'blData', 'title' : 'Bill of Lading', 'fnEdit' : 'getBlData', 'fnsave' : 'updateBlData'}";
        $blButton = '<a class="btn btn-primary permission-modify" href="javascript:void(0)" datatip="Bill of Lading" flow="down" onclick="edit('.$blParam.');"><i class="icon-Bitcoin"></i></a>';
    }
    
    if($data->listType == 'LISTING')
    {
        $action = getActionButton($printNew.$printCustom.$printExport.$print.$blButton.$edit.$delete);
    	if($data->tp == 'BILLWISE')
    	{
    		return [$action,$data->sr_no,$data->trans_number,date("d-m-Y",strtotime($data->trans_date)),$data->party_name,$data->taxable_amount,$data->gst_amount,$data->net_amount];
    	}
    	else
    	{
    		return [$action,$data->sr_no,$data->trans_number,date("d-m-Y",strtotime($data->trans_date)),$data->party_name,$data->item_name.' <small>'.$data->item_remark.'</small>',floatVal($data->qty),$data->price,$data->disc_amount,$data->amount];
    	}
    }
    
    if($data->listType == 'REPORT')
    {
        if($data->tp == 'ITEMWISE'){$data->id = $data->trans_main_id;}
        $trno = $data->trans_number;
        //if(in_array($data->userRole,[-1,1,3])){$trno= '<a href="'.base_url('salesInvoice/edit/'.$data->id).'" target="_blank" datatip="Edit Invoice" flow="right"> '.$data->trans_number.'</a>';}
          
    	if($data->tp == 'BILLWISE')
    	{
    		return [$data->sr_no,$trno,date("d-m-Y",strtotime($data->trans_date)),$data->party_name,$data->taxable_amount,$data->gst_amount,$data->net_amount];
    	}
    	else
    	{
    		return [$data->sr_no,$trno,date("d-m-Y",strtotime($data->trans_date)),$data->party_name,$data->item_name.' <small>'.$data->item_remark.'</small>',floatVal($data->qty),$data->price,$data->disc_amount,$data->amount];
    	}
    }
}

function getSalesInvoiceData00($data){
    $deleteParam = $data->id.",'Sales Invoice'";
    $itemlist="";
    $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
    $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    $itemList = '<a href="javascript:void(0)" class="btn btn-primary createItemList permission-read" data-id="'.$data->id.'" data-party_name="'.$data->party_name.'" datatip="Item List" flow="down"><i class="fa fa-list" ></i></a>';
	$printExport=""; $printCustom=""; $print="";
    if($data->sales_type == 4){
        $printExport = '<a href="javascript:void(0)" class="btn btn-info btn-edit printInvoice permission-approve" datatip="Print Export Invoice" flow="down" data-id="'.$data->id.'" data-function="export_invoice_pdf"><i class="fa fa-print"></i></a>';
    } else if($data->sales_type == 3){
        $printCustom = '<a href="javascript:void(0)" class="btn btn-info btn-edit printInvoice permission-approve" datatip="Print Custom Invoice" flow="down" data-id="'.$data->id.'" data-function="custom_invoice_pdf"><i class="fa fa-print"></i></a>';
    } else {
        $print = '<a href="javascript:void(0)" class="btn btn-info btn-edit printInvoice permission-approve" datatip="Print Invoice" flow="down" data-id="'.$data->id.'" data-function="invoice_pdf"><i class="fa fa-print"></i></a>';
    }
    
    if($data->sales_type == 1):
        $salesType = "Manufacturing (Domestics)";
    elseif($data->sales_type == 2):
        $salesType = "Manufacturing (Export)";
    elseif($data->sales_type == 3):
        $salesType = "Jobwork (Domestics)";
    endif;
	
    $action = getActionButton($printCustom.$printExport.$print.$itemList.$edit.$delete);
    return [$action,$data->sr_no,getPrefixNumber($data->trans_prefix,$data->trans_no),date("d-m-Y",strtotime($data->trans_date)),$salesType,$data->party_name,$data->po_no,$data->net_amount];
}

/* Proforma Invoice Table Data */
function getProformaInvoiceData($data){
    $deleteParam = $data->trans_main_id.",'Proforma Invoice'";
    $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->trans_main_id).'" class="btn btn-success btn-edit permision-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
    $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	$print = '<a href="javascript:void(0)" class="btn btn-primary btn-edit printInvoice" datatip="Print Invoice" flow="down" data-id="'.$data->trans_main_id.'"><i class="fa fa-print"></i></a>';
	
    $action = getActionButton($print.$edit.$delete);
    return [$action,$data->sr_no,getPrefixNumber($data->trans_prefix,$data->trans_no),date("d-m-Y",strtotime($data->trans_date)),$data->party_name,$data->inv_amount];
}

/* Product Table Data */
function getProductData($data){
    $deleteParam = $data->id.",'Product'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editProduct', 'title' : 'Update Product'}";
    $fgRevisionParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'fgRevision', 'title' : 'Fg Revision', 'fnEdit' : 'getFgRevision', 'fnsave' : 'updateFgRevision'}";
    $fgRevisionButton = '<a class="btn btn-info btn-salary permission-modify" href="javascript:void(0)" datatip="Fg Revision" flow="down" onclick="edit('.$fgRevisionParam.');"><i class="fa fa-list"></i></a>';
    $setProductProcess = '<a href="javascript:void(0)" class="btn btn-info setProductProcess permission-modify" datatip="Set Product Process" data-id="'.$data->id.'" data-product_name="'.$data->item_name.'" data-button="both" data-modal_id="modal-md" data-function="addProductProcess" data-form_title="Set Product Process" flow="down"><i class="fas fa-cogs"></i></a>';
    $viewProductProcess = '<a href="javascript:void(0)" class="btn btn-purple viewItemProcess permission-modify" datatip="View Process" data-id="'.$data->id.'" data-product_name="'.$data->item_name.'" data-button="close" data-modal_id="modal-lg" data-function="viewProductProcess" data-form_title="View Product Process" flow="down"><i class="fa fa-list"></i></a>';
    $productKit = '<a href="javascript:void(0)" class="btn btn-warning productKit permission-modify" datatip="Product BOM" data-id="'.$data->id.'" data-product_name="'.$data->item_name.'" data-button="both" data-modal_id="modal-lg" data-function="addProductKitItems" data-form_title="Product BOM" flow="down"><i class="fas fa-dolly-flatbed"></i></a>';
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	
	$mq = '';
    if($data->qty < $data->min_qty){ $mq = 'text-danger'; }
	$qty = '<a href="'.base_url('reports/productReport/itemWiseStock/'.$data->id).'" class="'.$mq.'">'.$data->qty.' ('.$data->unit_name.')</a>';
    
    $openingStock = '<a href="javascript:void(0)" class="btn btn-warning itemOpeningStock" data-id="'.$data->id.'" data-item_name="'.$data->item_name.'" data-button="close" data-modal_id="modal-lg"" datatip="Opening Stock" flow="down" data-function="addOpeningStock" data-form_title="Opening Stock"><i class="fas fa-database "></i></a>';
	
	//NPD Product
	if($data->id == 11324){
	    $openingStock = $fgRevisionButton = $editButton = $deleteButton = '';
	}
	
	$action = getActionButton($openingStock.$fgRevisionButton.$editButton.$deleteButton);
    return [$action,$data->sr_no,$data->item_code,$data->item_name,$data->hsn_code,$data->part_no,$data->party_code,$data->drawing_no,$data->rev_no,$data->price,$data->opening_qty.' ('.$data->unit_name.')',formatDate($data->created_at)];
}

/* Tool Cunsumption Table Data*/
function ToolConsumption($data){
    $toolConsumption = '<button type="button" class="btn waves-effect waves-light btn-outline-primary addToolConsumption permission-modify" data-id="'.$data->id.'" data-product_name="'.$data->item_name.'" data-button="both" data-modal_id="modal-md" data-function="addToolConsumption" data-form_title="Add Tool Consumption">Add Tool Consumption</button>';
    return [$data->sr_no,$data->item_code,$toolConsumption];
}

/* Feasibility Reason Data  */
function getFeasibilityReasonData($data){
    
    $deleteParam = $data->id.",'Rejected Reason'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editFeasibilityReason', 'title' : 'Update Rejected Reason'}";
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    $type = ($data->type == 3)?"Item Feasibility":"Customer Feedback";
    $action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$type,$data->remark];
}

/* Commercial Packing Data  */
function getCommercialPackingData($data){
    $deleteParam = $data->id.",'Commercial Packing'";

    $edit = '';$delete = '';
    if($data->trans_status == 0):
        $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';

        $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    endif;

    $migrateItemNamesParam = $data->id.",'tc'";//first param is main table id and second param is table name alias for checking condition in model
    $migrateItemNames = '<a href="javascript:void(0)" class="btn btn-info" onclick="migrateItemNames('.$migrateItemNamesParam.');" datatip="Update Item Name" flow="down"><i class="fas fa-sync"></i></a>';


    $printBtn = '<a class="btn btn-warning btn-edit" href="'.base_url($data->controller.'/commercialPackingPdf/'.$data->id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';
    $excelBtn = '<a class="btn btn-facebook btn-edit" href="'.base_url($data->controller.'/commercialPackingPdf/'.$data->id.'/EXCEL').'" target="_blank" datatip="Excel" flow="down"><i class="fa fa-file" ></i></a>';

    $data->doc_date = (!empty($data->doc_date))?date("d-m-Y",strtotime($data->doc_date)):"";
    $action = getActionButton($migrateItemNames.$excelBtn.$printBtn.$edit.$delete);
    return [$action,'',$data->trans_number,$data->packing_no,$data->doc_no,$data->doc_date,$data->party_name,$data->total_amount,$data->net_amount];
}

/* Commercial Invoice Data */
function getCommercialInvoiceData($data){
    $deleteParam = $data->id.",'Commercial Invoice'";
    
    $edit = '';$delete = '';
    if($data->trans_status == 0):
        $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';

        $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    endif;
    
    $migrateItemNamesParam = $data->id.",'tc'";//first param is main table id and second param is table name alias for checking condition in model
    $migrateItemNames = '<a href="javascript:void(0)" class="btn btn-info" onclick="migrateItemNames('.$migrateItemNamesParam.');" datatip="Update Item Name" flow="down"><i class="fas fa-sync"></i></a>';

    
    $printBtn = '<a class="btn btn-warning btn-edit" href="'.base_url($data->controller.'/commercialInvoicePdf/'.$data->id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';

    $excelBtn = '<a class="btn btn-facebook btn-edit" href="'.base_url($data->controller.'/commercialInvoicePdf/'.$data->id.'/EXCEL').'" target="_blank" datatip="Excel" flow="down"><i class="fa fa-file" ></i></a>';

    $action = getActionButton($migrateItemNames.$excelBtn.$printBtn.$edit.$delete);

    return [$action,'',$data->trans_number,$data->packing_no,$data->doc_no,((!empty($data->doc_date))?date("d-m-Y",strtotime($data->doc_date)):""),$data->party_name,$data->net_amount];
}


/* Custom Packing Data */
function getCustomPackingData($data){
    $deleteParam = $data->id.",'Custom Packing'";

    $edit = '';$delete = '';
    if($data->trans_status == 0):
        $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';

        $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    endif;
    
    $migrateItemNamesParam = $data->id.",'tc'";//first param is main table id and second param is table name alias for checking condition in model
    $migrateItemNames = '<a href="javascript:void(0)" class="btn btn-info" onclick="migrateItemNames('.$migrateItemNamesParam.');" datatip="Update Item Name" flow="down"><i class="fas fa-sync"></i></a>';

    
    $printBtn = '<a class="btn btn-warning btn-edit" href="'.base_url($data->controller.'/customPackingPdf/'.$data->id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';

    $data->doc_date = (!empty($data->doc_date))?date("d-m-Y",strtotime($data->doc_date)):"";
    $action = getActionButton($migrateItemNames.$printBtn.$edit.$delete);
    return [$action,'',$data->trans_number,$data->packing_no,$data->doc_no,$data->doc_date,$data->party_name,$data->total_amount,$data->net_amount];
}

/* Custom Invoice Data */
function getCustomInvocieData($data){
    $deleteParam = $data->id.",'Custom Invoice'";
    
    $edit = '';$delete = '';
    if($data->trans_status == 0):
        $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';

        $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    endif;
    
    $migrateItemNamesParam = $data->id.",'tc'";//first param is main table id and second param is table name alias for checking condition in model
    $migrateItemNames = '<a href="javascript:void(0)" class="btn btn-info" onclick="migrateItemNames('.$migrateItemNamesParam.');" datatip="Update Item Name" flow="down"><i class="fas fa-sync"></i></a>';

    
    $printBtn = '<a class="btn btn-warning btn-edit" href="'.base_url($data->controller.'/customInvoicePdf/'.$data->id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';

    $evdPrintBtn = '<a class="btn btn-dark btn-edit" href="'.base_url($data->controller.'/evdPdf/'.$data->id).'" target="_blank" datatip="EVD Print" flow="down"><i class="fas fa-print" ></i></a>';
    $scometPrintBtn = '<a href="javascript:void(0)" class="btn btn-info btn-edit printScomet" datatip="Scomet Print" flow="down" data-id="'.$data->id.'" data-function="scometPrint"><i class="fa fa-print"></i></a>';

    $dbkPrintBtn = '<a class="btn btn-info btn-edit" href="'.base_url($data->controller.'/dbkPdf/'.$data->id).'" target="_blank" datatip="DBK Print" flow="down"><i class="fas fa-print" ></i></a>';

    $action = getActionButton($migrateItemNames.$dbkPrintBtn.$evdPrintBtn.$scometPrintBtn.$printBtn.$edit.$delete);
    return [$action,'',$data->trans_number,$data->packing_no,$data->doc_no,((!empty($data->doc_date))?date("d-m-Y",strtotime($data->doc_date)):""),$data->party_name,$data->net_amount];
}

/* Request Table Data*/
function getPackingRequestData($data){ 
    $deleteParam = $data->id.",'Request'"; 
    $editButton=""; $deleteButton="";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'packingRequset', 'title' : 'Packing Requset', 'fnEdit' : 'editPackingRequset'}";
    //if(empty($data->status) && $data->pack_link_qty <= 0){
    if(floatVal($data->request_qty) >= floatVal($data->pack_link_qty)){
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Packing Requset" flow="down" onclick="edit('.$editParam.');"><i class="fa fa-edit"></i></a>';
    }
    if(empty($data->status) && $data->pack_link_qty <= 0){
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    }
    $itemList = '<a href="javascript:void(0)" class="btn btn-primary createItemList permission-read" data-id="'.$data->id.'"  datatip="Item List" flow="down"><i class="fa fa-list" ></i></a>';
    
    $action = getActionButton($itemList.$editButton.$deleteButton);
    return [$action,$data->sr_no,formatDate($data->req_date),getPrefixNumber($data->trans_prefix,$data->trans_no),getPrefixNumber($data->so_prefix,$data->so_no),'['.$data->party_code.'] '.$data->party_name,'['.$data->item_code.'] '.$data->item_name,floatVal($data->request_qty),floatVal($data->pack_link_qty),floatVal($data->pending_qty),$data->trans_way,$data->remark];
}

function getPendingPackingRequestData($data){ 
    $linkButton=""; $packingButton="";
    if($data->country_id == 101):
        $linkParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'packingRequset', 'title' : 'Link Packing Requset', 'fnEdit' : 'linkPackingRequest','fnsave' : 'savePackReqLink'}";
        $linkButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Link Packing" flow="down" onclick="edit('.$linkParam.');"><i class="mdi mdi-link"></i></a>';
    else:
        $packingButton = '<a class="btn btn-info btn-edit permission-modify" href="'.base_url('packing/packingFromRequest/'.$data->id).'" datatip="Tentative Packing" flow="down"><i class="fas fa-box-open" ></i></a>';
    endif;
    $action = getActionButton($linkButton.$packingButton);
    return [$action,$data->sr_no,formatDate($data->req_date),formatDate($data->delivery_date),getPrefixNumber($data->so_prefix,$data->so_no),$data->party_code,$data->item_code,floatVal($data->request_qty),floatVal($data->dispatch_qty),floatVal($data->pending_qty),$data->trans_way,$data->remark];
}

function getNPDSalesEnquiryData($data){
    $feasibleAcceptBtn ='';$addFeasibleBtn='';$emailBtn = ""; $npdBtn ="";$dcftBtn="";$intRTSBtn=""; $feasiblemailBtn = "";

    if($data->trans_status == 1):
        $feasibleAcceptBtn= '<a href="javascript:void(0)" class="btn btn-warning btn-delete permission-remove" onclick="feasibilityRequest('.$data->id.',2);" datatip="Fesibility Request" flow="down"><i class=" fas fa-check-circle
        "></i></a>'; 
    elseif($data->trans_status == 2):
        $addParam = "{'id' : " . $data->id . ", 'modal_id' : 'modal-md', 'form_id' : 'feasibilityForm', 'title' : 'Add Feasibility Days','fnEdit' : 'addFeasibilityDays', 'fnsave' : 'saveFeasibilityDays'}";
        $addFeasibleBtn = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Add Feasibility Days" flow="down" onclick="edit(' . $addParam . ');"><i class=" fas fa-plus"></i></a>';
        if(!empty($data->feasible)){
            $mailparam = "{'id' : " . $data->id . ", 'modal_id' : 'modal-md', 'form_id' : 'feasibilityForm', 'title' : 'Add Feasibility Days','fnedit' : 'reviewNSendMail', 'fnsave' : 'sendMail'}";
            $emailBtn = '<a class="btn btn-facebook btn-edit permission-modify" href="javascript:void(0)" datatip=" Feasibility Days Mail" flow="down" onclick="sendMail(' . $mailparam . ');"><i class=" icon-Mail-Forward"></i></a>';
        }
    endif;

    if($data->trans_status > 1 && !empty($data->feasible)){
        $dcftBtn = '<a  class="btn  btn-info btn-edit decideCFT" data-button="close" data-modal_id="modal-lg"  data-enq_id="'.$data->id.'" data-function="decideCFT" data-form_title="Decide CFT" data-fnsave="saveCFT" datatip = "Decide CFT"  flow="down" > <i class="fas fa-user-plus
        "></i> </a>	';
        if(!empty($data->pending_initiate) && $data->trans_status == 2){
            $intRTSBtn = '<a class="btn  btn-primary initiateRTS" data-enq_id="'.$data->id.'" datatip = "Initiate RTS"  flow="down"><i class="mdi mdi-arrow-right-drop-circle"></i></a>	';
        }
    }
    $enqNo = getPrefixNumber($data->trans_prefix,$data->trans_no);
    if($data->trans_status == 3){
        $enqNo = '<a href="'.base_url($data->controller.'/npdParts/'.$data->id).'" target="_blank">'.getPrefixNumber($data->trans_prefix,$data->trans_no).'</a>';

        if(empty($data->feasible_email_by) && !empty($data->rts_completed)){
            $feasibleMailparam = "{'id' : " . $data->id . ", 'modal_id' : 'modal-md', 'form_id' : 'feasibilityForm', 'title' : 'Feasibility Mail','fnedit' : 'feasibleMailSend', 'fnsave' : 'feasibleMailSendSave'}";
            $feasiblemailBtn = '<a class="btn btn-facebook btn-edit permission-modify" href="javascript:void(0)" datatip="Feasibility Mail" flow="down" onclick="feasibleMailSend(' . $feasibleMailparam . ');"><i class="icon-Mail-Forward"></i></a>';
        }
    }
    
    $quotedCount = ''; $fisibleCount =''; $notfisibalCount ='';
    if($data->notFisibalTab != 2){
        if(!empty($data->quotedCount) > 0){
            $quotedCount = $data->quotedCount;
        }
        if(!empty($data->fisibleCount) > 0){
            $fisibleCount = '<a href="javascript:void(0);" class=" getFeasibleData" data-status="1" data-trans_main_id="'.$data->trans_main_id.'" data-enq_no="'.(getPrefixNumber($data->trans_prefix,$data->trans_no)).'" datatip="FisibleCount" flow="down"><b>'.$data->fisibleCount.'</b></a>';
        }
    } else {
        $quotedCount = "-";
        $fisibleCount = "-";
    }
    if(!empty($data->notfisibalCount) > 0){
        $notfisibalCount = '<a href="javascript:void(0);" class=" getFeasibleData" data-status="2" data-trans_main_id="'.$data->trans_main_id.'" data-enq_no="'.(getPrefixNumber($data->trans_prefix,$data->trans_no)).'" datatip="NotfisibalCount" flow="down"><b>'.$data->notfisibalCount.'</b></a>';
    }
    $action = getActionButton($feasiblemailBtn.$intRTSBtn .$dcftBtn.$emailBtn.$feasibleAcceptBtn.$addFeasibleBtn);

    return [$action,$data->sr_no, $enqNo,date("d-m-Y",strtotime($data->trans_date)),$data->party_name,$data->item_name,$data->qty,$data->status,$quotedCount,$fisibleCount,$notfisibalCount,$data->remark];
}

/* Employee Responsibility Table Data */
function getResponsibilityData($data){
    $deleteParam = $data->id.",'Responsibility'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editEmployeeResponsibility', 'title' : 'Update Employee Responsibility'}";
    $edit = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    $action = getActionButton($edit.$delete);
    return [$action,$data->sr_no,$data->remark];
}

function getRFQData($data){
    $deleteParam = $data->ref_id.",'Purchase Enquiry'";
    $closeParam = $data->ref_id.",'Purchase Enquiry'";
    $closeParam = $data->ref_id.",'Purchase Enquiry'";
    $enqComplete = "";$edit = "";$delete = "";$close = "";$reopen = ""; $approve="";$reject="";

    $cnDate = (!empty($data->enq_ref_date))?date("d-m-Y",strtotime($data->enq_ref_date)):"";
    if(($data->confirm_status == 0)):
        $reject = '<a href="javascript:void(0)" class="btn btn-success approvePEnquiry permission-modify" data-id="'.$data->ref_id.'" data-val="3" data-msg="Reject" datatip="Reject Enquiry" flow="down"><i class="fa fa-window-close"></i></a>';     
        $enqComplete = '<a href="javascript:void(0)" class="btn btn-info btn-complete enquiryConfirmed permission-modify" data-id="'.$data->ref_id.'" data-party="'.$data->supplier_name.'" data-enqno="'.$data->enq_prefix.$data->enq_no.'" data-enqdate="'.date("d-m-Y",strtotime($data->enq_date)).'" data-button="both" data-modal_id="modal-xl" data-function="getRFQEnquiryData" data-form_title="Purchase Enquiry Quotation" datatip="Quotation" flow="down"><i class="fa fa-check"></i></a>';
        $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->ref_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
        $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" data-tooltip="tooltip" data-placement="bottom" data-original-title="Remove" flow="down"><i class="ti-trash"></i></a>';
    endif;
    $action = getActionButton($approve.$reject.$enqComplete.$edit.$delete);
    return [$action,$data->sr_no,$data->enq_prefix.$data->enq_no.(!empty($data->sub_enq_no)?'/'.$data->sub_enq_no:''),date("d-m-Y",strtotime($data->enq_date)),$data->supplier_name,$data->item_name,$data->qty,$data->status,$data->item_remark];
}

function getDispatchRequestData($data){
	$action='';$shortClose = '';
    if(empty($data->request_for)):
		$editButton=""; $deleteButton="";
		if(floatVal($data->req_qty) > floatVal($data->dispatch_qty) && $data->status == 0){
			$editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'dispatchRequset', 'title' : 'Dispatch Requset', 'fnEdit' : 'editDispatchRequset'}";
			$editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Dispatch Requset" flow="down" onclick="edit('.$editParam.');"><i class="fa fa-edit"></i></a>';
			
			$deleteParam = $data->id.",'Dispatch Request'"; 
			$deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
			$shortClose = '<a class="btn btn-instagram btn-shortClose changeReqStatus permission-modify" href="javascript:void(0)" datatip="Short Close" flow="down" data-val="3" data-id="'.$data->id.'"><i class="sl-icon-close"></i></a>';
		}
		$action = getActionButton($shortClose.$editButton.$deleteButton);
		
    elseif(!empty($data->request_for) && $data->request_for == 'Challan'):
        $challan = '<a href="javascript:void(0)" class="btn btn-info createChallan permission-write" data-id="'.$data->party_id.'" data-party_name="'.$data->party_name.'" datatip="Challan" flow="down"><i class="fa fa-file-alt" ></i></a>';    
        
        $action = getActionButton($challan);
    endif;
    $so_no = (!empty($data->so_prefix) && !empty($data->so_no))?getPrefixNumber($data->so_prefix,$data->so_no):'';
    $req_no = (!empty($data->trans_prefix) && !empty($data->trans_no))?getPrefixNumber($data->trans_prefix,$data->trans_no):'';
    return [$action,$data->sr_no,formatDate($data->req_date), $req_no, $so_no,$data->cust_po_no,'['.$data->party_code.'] '.$data->party_name,'['.$data->item_code.'] '.$data->item_name,floatVal($data->req_qty),floatVal($data->dispatch_qty),floatVal($data->pending_qty),$data->remark];
}

function getDispatchMaterialData($data){
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'dispatchMaterial', 'title' : 'Dispatch Material [".$data->item_code."]', 'fnEdit' : 'addDispatchMaterial', 'fnsave' : 'saveDispatchMaterial'}";
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Dispatch Material" flow="down" onclick="edit('.$editParam.');"><i class="ti-truck"></i></a>';
		
    $action = getActionButton($editButton);
    return [$action,$data->sr_no,formatDate($data->trans_date),getPrefixNumber($data->trans_prefix,$data->trans_no),'['.$data->party_code.'] '.$data->party_name,'['.$data->item_code.'] '.$data->item_name,floatVal($data->qty)];
}


/* Revision Check Point Master Table Data */
function getRevChData($data){
    $delete=""; $edit=""; $startBtn=""; $activeButton=""; $fApproveBtn=""; $closeBtn="";$cpBtn="";

    if(empty($data->status))
    {
        $deleteParam = $data->id.",'ECN'";
        $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trashEcn('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

        $edit = '<a href="'.base_url($data->controller.'/editReviseCheckPoint/'.$data->id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';

        $startBtn = '<a class="btn btn-info btn-start startEcn permission-modify" href="javascript:void(0)" datatip="Start" flow="down" data-id="'.$data->id.'"><i class="ti-control-play"></i></a>';
    }
    elseif($data->status == 1)
    {
        $closeBtn = '<a class="btn btn-dark btn-close closeEcn permission-modify" href="javascript:void(0)" datatip="Close" flow="down" data-id="'.$data->id.'"><i class="ti-close"></i></a>';
    }
    elseif($data->status == 2)
    {
        $fApproveBtn = '<a href="javascript:void(0)" onclick="approveEcn('.$data->id.')" class="btn btn-info btn-edit " datatip="Final Approve" flow="down"><i class="fa fa-check"></i></a>';

        
    }
    if($data->status == 3){
        if(empty($data->is_active)){
            $activeParam = "{'id' : ".$data->id.",'is_active':'1','msg':'Are you sure you want to active this revision ?'}";
            $activeButton = '<a class="btn btn-facebook btn-edit permission-modify" href="javascript:void(0)" datatip="Active" flow="down" onclick="activeRevision('.$activeParam.');"><i class="fas fa-check-circle" ></i></a>';
        }else{
            $activeParam = "{'id' : ".$data->id.",'is_active':'0','msg':'Are you sure you want to deactive this revision ?'}";
            $activeButton = '<a class="btn btn-danger btn-edit permission-modify" href="javascript:void(0)" datatip="Deactive" flow="down" onclick="activeRevision('.$activeParam.');"><i class="far fa-times-circle" ></i></a>';
        }
        if($data->entry_type == 1){
            // $cpParam = "{'postData' : {'id' : ".$data->id.",'item_id' : ".$data->item_id."},'fnedit':'addCpRevision','fnsave':'saveCpRevision','title':'Add Control Plan Revision For  ".$data->rev_no."', 'modal_id' : 'modal-md', 'form_id' : 'addCpRevision'}";
            $cpBtn='<a  href="'.base_url($data->controller.'/addReviseCheckPoint/'.$data->id).'" class="btn btn-info btn-edit permission-modify" datatip="Add Control Plan Revision" flow="down" ><i class="fas fa-plus"></i></a>';
        }
        
    }

    $printBtn = '<a class="btn btn-warning btn-edit" href="'.base_url($data->controller.'/printRevision/'.$data->id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';

    $action = getActionButton($cpBtn.$edit.$delete.$startBtn.$closeBtn.$fApproveBtn.$activeButton.$printBtn);

    return [$action,$data->sr_no,$data->item_code,$data->ecn_prefix.$data->ecn_note_no,$data->rev_no,formatDate($data->rev_date),$data->cust_rev_no,formatDate($data->cust_rev_date),formatDate($data->ecn_received_date),formatDate($data->target_date),$data->material_grade,$data->status_label];
}

/* Revision Check Point Transaction Table Data */
function getRevChPendingData($data){
    $verifyBtn=""; $restartBtn="";
    if(in_array($data->loginId, explode(",",$data->responsibility))){
        if(empty($data->status) || $data->status == 4)
        {
            $verifyParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'verification', 'title' : 'Verification', 'fnsave' : 'saveVerification' ,'fnEdit' : 'addVerification'}";
            $verifyBtn='<a class="btn btn-info btn-edit permission-modify" href="javascript:void(0)" datatip="Verification" flow="down" onclick="edit('.$verifyParam.');"><i class="fas fa-check"></i></a>';
        }
        if($data->status == 3)
        {
            $restartBtn = '<a class="btn btn-success btn-restart restartVerification permission-modify" href="javascript:void(0)" datatip="Restart" flow="down" data-id="'.$data->id.'"><i class="ti-control-play" ></i></a>';
        }
    }
    

    $action = getActionButton($verifyBtn.$restartBtn);
    $revNo = $data->rev_no;
    if($data->entry_type == 2){
        $revNo = '['.$data->pfc_rev_no.']  '.$data->rev_no;
    }
    return [$action,$data->sr_no,$data->item_code,$data->ecn_no,$revNo,$data->rev_date,$data->title,$data->old_description,$data->description,$data->emp_name,formatDate($data->ch_target_date)];
}

/* Revision Check Point Review Table Data */
function getRevChReviewData($data){
    $approve="";
    if(empty($data->approve_by))
    {
        $approve = '<a href="javascript:void(0)" onclick="openView('.$data->id.')" class="btn btn-info btn-edit " datatip="Approve" flow="down"><i class="fa fa-check"></i></a>';
    }

    $action = getActionButton($approve);
    return [$action,$data->sr_no,$data->item_code,$data->ecn_note_no,$data->rev_no,formatDate($data->rev_date),$data->ecn_drg_no,$data->ecn_no,formatDate($data->ecn_received_date),formatDate($data->target_date),$data->material_grade];
}

/* Control Plan Revision Table Data */
function getCpRevData($data){
    $delete=""; $edit=""; $startBtn=""; $activeButton=""; $fApproveBtn=""; $closeBtn="";

    if($data->status == 1)
    {
        $deleteParam = $data->id.",'Control Plan'";
        $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trashEcn('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';


        $aprvParam = "{'postData' : {'id' : ".$data->id.",'status':3},'message' : 'Are you sure you want to approve this Revision ? '}";
        $fApproveBtn='<a class="btn btn-info btn-edit permission-modify" href="javascript:void(0)" datatip="Approve" flow="down" onclick="changeCpRevStatus('.$aprvParam.');"><i class="fas fa-check"></i></a>';

        
    }
    if($data->status == 3){
        if(empty($data->is_active)){
            $activeParam = "{'postData' :{'id' : ".$data->id.",'is_active':'1'},'message':'Are you sure you want to active this revision ?'}";
            $activeButton = '<a class="btn btn-facebook btn-edit permission-modify" href="javascript:void(0)" datatip="Active" flow="down" onclick="changeCpRevStatus('.$activeParam.');"><i class="fas fa-check-circle" ></i></a>';
        }else{
            $activeParam = "{'postData' :{'id' : ".$data->id.",'is_active':'0'},'message':'Are you sure you want to deactive this revision ?'}";
            $activeButton = '<a class="btn btn-danger btn-edit permission-modify" href="javascript:void(0)" datatip="Deactive" flow="down" onclick="changeCpRevStatus('.$activeParam.');"><i class="far fa-times-circle" ></i></a>';
        }
    }

    $action = getActionButton($delete.$fApproveBtn.$activeButton);

    return [$action,$data->sr_no,$data->item_code,$data->rev_no,formatDate($data->rev_date),$data->pfc_rev_no,$data->remark];
}

/* Lead Data */
function getLeadData($data){

    $title = ($data->party_category == 1 ? "Customer": ($data->party_category == 2 ? "Vendor":"Supplier"));
    $deleteParam = $data->id.",'".$title."'";
    $editParam = "{'id' : ".$data->id.", 'party_category': ".$data->party_category.", 'modal_id' : 'modal-xl', 'form_id' : 'editParty', 'title' : 'Update ".$title."'}";
    $approvalParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'partyApproval', 'title' : 'Party Approval', 'fnEdit' : 'partyApproval', 'fnsave' : 'savePartyApproval'}";

    $approvalButton = '<a class="btn btn-info btn-approval permission-approve" href="javascript:void(0)" datatip="Party Approval" flow="down" onclick="edit('.$approvalParam.');"><i class="fa fa-check" ></i></a>';

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt"></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    
    $gstJsonBtn="";$contactBtn="";$reminderBtn="";$followUpBtn="";
    if($data->party_category == 1 && $data->party_type == 1):
        $gstParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg','button' : 'close', 'form_id' : 'gstDetail', 'title' : 'GST Detail', 'fnEdit' : 'getGstDetail', 'fnsave' : 'saveGst'}";
        $gstJsonBtn = '<a class="btn btn-warning btn-contact permission-modify" href="javascript:void(0)" datatip="GST Detail" flow="down" onclick="edit('.$gstParam.');"><i class="fab fa-google"></i></a>';

        $contactParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg','button' : 'close', 'form_id' : 'contactDetail', 'title' : 'Contact Detail', 'fnEdit' : 'getContactDetail', 'fnsave' : 'saveContact'}";
        $contactBtn = '<a class="btn btn-info btn-contact permission-modify" href="javascript:void(0)" datatip="Contact Detail" flow="down" onclick="edit('.$contactParam.');"><i class="fa fa-address-book"></i></a>';
    else:
        $contactParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg','button' : 'close', 'form_id' : 'contactDetail', 'title' : 'Contact Detail', 'fnEdit' : 'getContactDetail', 'fnsave' : 'saveContact'}";
        $contactBtn = '<a class="btn btn-info btn-contact permission-modify" href="javascript:void(0)" datatip="Contact Detail" flow="down" onclick="edit('.$contactParam.');"><i class="fa fa-address-book"></i></a>';
    
        $reminderParam = "{'id' : ".$data->id.",'modal_id' : 'modal-md','button' : 'close', 'form_id' : 'reminderForm', 'title' : 'Reminder', 'fnEdit' : 'getReminder', 'fnsave' : 'saveSalesLog'}";
        $reminderBtn = '<a class="btn btn-danger permission-modify" href="javascript:void(0)" datatip="Reminder" flow="down" onclick="edit('.$reminderParam.');"><i class="far fa-bell"></i></a>';

        $followUpParam = "{'id' : ".$data->id.",'modal_id' : 'modal-md','button' : 'close', 'form_id' : 'followUpForm', 'title' : 'Follow Up', 'fnEdit' : 'getFollowUp', 'fnsave' : 'saveSalesLog'}";
        $followUpBtn = '<a class="btn btn-primary  btn-edit permission-modify" href="javascript:void(0)" datatip="Follow Up" flow="down" onclick="edit('.$followUpParam.');"><i class="fa fa-list-ul"></i></a>';
    endif;
    
    $supplierRegistration=''; $partyDetailsBtn='';
    if($data->party_category == 3 || $data->party_category == 2):
        $partyDetailsParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg','button' : 'both', 'form_id' : 'partyDetails', 'title' : 'Party Details', 'fnEdit' : 'getPartyDetails', 'fnsave' : 'savePartyDetails'}";
        $partyDetailsBtn = '<a class="btn btn-primary permission-modify" href="javascript:void(0)" datatip="Add Party Details" flow="down" onclick="edit('.$partyDetailsParam.');"><i class="fas fa-plus-circle"></i></a>';
        
        $supplierRegistration = '<a class="btn btn-warning btn-edit" href="'.base_url('parties/supplierRegistration/'.$data->id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';
    endif;
    
    $action = getActionButton($contactBtn.$followUpBtn.$reminderBtn.$editButton.$deleteButton);
    
    $responseData = [$action,$data->sr_no,$data->party_name,$data->contact_person,$data->party_mobile,$data->party_email,$data->company_type,$data->sector,$data->source,$data->country_name,formatDate($data->created_at),formatDate($data->updated_at)];
    return $responseData;
}
/* Pending Response Table Data */
function getPendingResponseData($data){
    $responseBtn='';
    $responseParam = "{'id' : ".$data->id.",'modal_id' : 'modal-md','button' : 'both', 'form_id' : 'responseForm', 'title' : 'Response', 'fnEdit' : 'getResponse', 'fnsave' : 'saveResponse'}";
    $responseBtn = '<a class="btn btn-info  btn-edit permission-modify" href="javascript:void(0)" datatip="Response" flow="down" onclick="edit('.$responseParam.');"><i class="icon-Mail-Read"></i></a>';

    $action = getActionButton($responseBtn);
    return [$action,$data->sr_no,$data->party_name,$data->contact_person,$data->party_mobile,formatDate($data->ref_date),$data->reminder_time,$data->mode,$data->notes];
}

/* Sample Invoice Data */
function getSampleInvoiceData($data){
    $deleteParam = $data->id.",'Invoice'";
    
    $edit = '';$delete = '';
    if($data->trans_status == 0):
        $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';

        $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    endif;

    $printBtn = '<a class="btn btn-warning btn-edit" href="'.base_url($data->controller.'/sampleInvoicePdf/'.$data->id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';

    $action = getActionButton($printBtn.$edit.$delete);

    return [$action,'',$data->trans_number,$data->packing_no,$data->doc_no,((!empty($data->doc_date))?date("d-m-Y",strtotime($data->doc_date)):""),$data->party_name,$data->net_amount];
}
?>