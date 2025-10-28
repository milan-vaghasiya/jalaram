<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/* get Pagewise Table Header */
function getProductionHeader($page){
    /* Vendor Header */
    $data['vendor'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['vendor'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"]; 
	$data['vendor'][] = ["name"=>"Company Name"];
	$data['vendor'][] = ["name"=>"Contact Person"];
    $data['vendor'][] = ["name"=>"Contact No."];
    $data['vendor'][] = ["name"=>"Address"];
    $data['vendor'][] = ["name"=>"Create Date"];
    
    /* Process Header */
    $data['process'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['process'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['process'][] = ["name"=>"Process Name"];
    $data['process'][] = ["name"=>"Department"];
    $data['process'][] = ["name"=>"Is Machining?"];
    $data['process'][] = ["name"=>"Remark"];
    
    /* Costing Header */
    $data['costing'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center","srnoPosition"=>0];
    $data['costing'][] = ["name"=>"Part Code"];
    $data['costing'][] = ["name"=>"Costing","style"=>"width:10%;","textAlign"=>"center"];
    $data['costing'][] = ["name"=>"Action","style"=>"width:15%;","textAlign"=>"center"];

    /* Job Card Header */
    $data['jobcard'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];
    $data['jobcard'][] = ["name"=>"#","style"=>"width:4%;","textAlign"=>"center"];
    $data['jobcard'][] = ["name"=>"Job No.","style"=>"width:9%;","textAlign"=>"center"];
    $data['jobcard'][] = ["name"=>"Job Date","textAlign"=>"center"];
    $data['jobcard'][] = ["name"=>"Delivery Date","textAlign"=>"center"];
    $data['jobcard'][] = ["name"=>"Customer","textAlign"=>"center"];
    $data['jobcard'][] = ["name"=>"Product","textAlign"=>"center"];
    $data['jobcard'][] = ["name"=>"Order Qty.","textAlign"=>"center"];
    $data['jobcard'][] = ["name"=>"Status","textAlign"=>"center"];
    $data['jobcard'][] = ["name"=>"Remark"];
    $data['jobcard'][] = ["name"=>"Last Activity"];

    /* Material Request */
    $data['materialRequest'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['materialRequest'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['materialRequest'][] = ["name"=>"Request Date"];
    $data['materialRequest'][] = ["name"=>"Delivery Date"];
    $data['materialRequest'][] = ["name"=>"Finish Good"];
    $data['materialRequest'][] = ["name"=>"Request Item Name"];
    $data['materialRequest'][] = ["name"=>"Request Item Qty"];
    $data['materialRequest'][] = ["name"=>"Status"];

    /* Jobwork Order Header */
    $data['jobWorkOrder'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['jobWorkOrder'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
    $data['jobWorkOrder'][] = ["name"=>"Order Date"];
    $data['jobWorkOrder'][] = ["name"=>"Order No."];
    $data['jobWorkOrder'][] = ["name"=>"Vendor Name"];
    $data['jobWorkOrder'][] = ["name"=>"Product"];
    $data['jobWorkOrder'][] = ["name"=>"Qty"];
    $data['jobWorkOrder'][] = ["name"=>"Rate"];
    $data['jobWorkOrder'][] = ["name"=>"Status","textAlign"=>"center"];
    $data['jobWorkOrder'][] = ["name"=>"Process"];
    $data['jobWorkOrder'][] = ["name"=>"Remark"];

    /* Job Work Header */
    $data['jobWork'][] = ["name" => "Action", "style" => "width:5%;", "textAlign" => "center"];
    $data['jobWork'][] = ["name" => "#", "style" => "width:4%;", "textAlign" => "center"];
    $data['jobWork'][] = ["name" => "Job No.", "style" => "width:9%;", "textAlign" => "center"];
    $data['jobWork'][] = ["name" => "Vendor"];
    $data['jobWork'][] = ["name" => "Product", "textAlign" => "center"];
    $data['jobWork'][] = ["name" => "Process"];
    $data['jobWork'][] = ["name" => "Status", "textAlign" => "center"];
    $data['jobWork'][] = ["name" => "Out Qty", "textAlign" => "center"];
    $data['jobWork'][] = ["name" => "In Qty", "textAlign" => "center"];
    $data['jobWork'][] = ["name" => "Reject Qty", "textAlign" => "center"];
    $data['jobWork'][] = ["name" => "Rework Qty", "textAlign" => "center"];
    $data['jobWork'][] = ["name" => "Pending Qty", "textAlign" => "center"]; 

    /* Job Work Vendor Header */
    $data['jobWorkVendor'][] = ["name" => "Action", "style" => "width:5%;", "textAlign" => "center"];
    $data['jobWorkVendor'][] = ["name" => "#", "style" => "width:4%;", "textAlign" => "center"];
    $data['jobWorkVendor'][] = ["name" => "Challan Date", "textAlign" => "center"];
    $data['jobWorkVendor'][] = ["name" => "Challan No", "textAlign" => "center"];
    $data['jobWorkVendor'][] = ["name" => "Job Date", "style" => "width:9%;", "textAlign" => "center"];
    $data['jobWorkVendor'][] = ["name" => "Job No.", "style" => "width:9%;", "textAlign" => "center"];
    $data['jobWorkVendor'][] = ["name" => "Vendor"];
    $data['jobWorkVendor'][] = ["name" => "Product"];
    $data['jobWorkVendor'][] = ["name" => "Process"];
    $data['jobWorkVendor'][] = ["name" => "Status", "textAlign" => "center"];
    $data['jobWorkVendor'][] = ["name" => "Weight", "textAlign" => "center"];
    $data['jobWorkVendor'][] = ["name" => "Out Qty", "textAlign" => "center"];
    $data['jobWorkVendor'][] = ["name" => "In Qty", "textAlign" => "center"];
    $data['jobWorkVendor'][] = ["name" => "Pending Qty", "textAlign" => "center"];
    $data['jobWorkVendor'][] = ["name" => "Return Qty", "textAlign" => "center"];
    $data['jobWorkVendor'][] = ["name" => "Remark", "textAlign" => "center"];

    /* Rejection Header */
    $data['rejectionComments'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['rejectionComments'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"]; 
    $data['rejectionComments'][] = ["name"=>"Rejection/Rework Comment"];
    $data['rejectionComments'][] = ["name"=>"Type"];

    /* Product Option Header */
    $data['productOption'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center","srnoPosition"=>0];
    $data['productOption'][] = ["name"=>"Part Code"];
    $data['productOption'][] = ["name"=>"BOM","style"=>"width:10%;","textAlign"=>"center"];
    $data['productOption'][] = ["name"=>"Screp","style"=>"width:10%;","textAlign"=>"center"];
    $data['productOption'][] = ["name"=>"Cycle Time","style"=>"width:10%;","textAlign"=>"center"];
    //$data['productOption'][] = ["name"=>"Tool","style"=>"width:10%;","textAlign"=>"center"];
    $data['productOption'][] = ["name"=>"Action","style"=>"width:15%;","textAlign"=>"center"];

    /* Idle Reason Header */
    $data['idleReason'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['idleReason'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"]; 
    $data['idleReason'][] = ["name"=>"Idle Code","style"=>"width:10%;","textAlign"=>"center"];
    $data['idleReason'][] = ["name"=>"Idle Reason"]; 

    /* Scrap Header */
    $data['scrap'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];
	$data['scrap'][] = ["name"=>"#","style"=>"width:4%;","textAlign"=>"center"];
	$data['scrap'][] = ["name"=>"Date.","style"=>"width:9%;","textAlign"=>"center"];
    $data['scrap'][] = ["name"=>"Jobcard","textAlign"=>"center"];
    $data['scrap'][] = ["name"=>"Item","textAlign"=>"center"];
    $data['scrap'][] = ["name"=>"Scrap Qty","textAlign"=>"center"];
    $data['scrap'][] = ["name"=>"Ok Qty","textAlign"=>"center"];
    
    /* Log Sheet  Header */
    $data['logSheet'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];
	$data['logSheet'][] = ["name"=>"#","style"=>"width:4%;","textAlign"=>"center"];
    $data['logSheet'][] = ["name"=>"Job No.","textAlign"=>"center"];
    $data['logSheet'][] = ["name"=>"Part Code","textAlign"=>"center"];
    $data['logSheet'][] = ["name"=>"Date","textAlign"=>"center"];
	$data['logSheet'][] = ["name"=>"Process","style"=>"width:9%;","textAlign"=>"center"];
    $data['logSheet'][] = ["name"=>"Machine","textAlign"=>"center"];
    $data['logSheet'][] = ["name"=>"Operator","textAlign"=>"center"];
    $data['logSheet'][] = ["name"=>"Qty.","textAlign"=>"center"];
    $data['logSheet'][] = ["name"=>"Rejection Qty","textAlign"=>"center"];
    $data['logSheet'][] = ["name"=>"Rework Qty","textAlign"=>"center"];
    
    /* RM Process  Header */
    $data['rmProcess'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];
    $data['rmProcess'][] = ["name"=>"#","style"=>"width:4%;","textAlign"=>"center"];
    $data['rmProcess'][] = ["name"=>"Date.","textAlign"=>"center"];
    $data['rmProcess'][] = ["name"=>"Vendor","style"=>"width:9%;","textAlign"=>"center"];
    $data['rmProcess'][] = ["name"=>"Jobwork Order","textAlign"=>"center"];
    $data['rmProcess'][] = ["name"=>"Item Name","textAlign"=>"center"];
    $data['rmProcess'][] = ["name"=>"Qty","textAlign"=>"center"];
    $data['rmProcess'][] = ["name"=>"Recived Item","textAlign"=>"center"];
    
    /* Hold Area  Header */
    $data['holdAreaMovement'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];
    $data['holdAreaMovement'][] = ["name"=>"#","style"=>"width:4%;","textAlign"=>"center"];
    $data['holdAreaMovement'][] = ["name"=>"Date.","textAlign"=>"center"];
    $data['holdAreaMovement'][] = ["name"=>"Job No.","textAlign"=>"center"];
    $data['holdAreaMovement'][] = ["name"=>"Product","textAlign"=>"center"];
    $data['holdAreaMovement'][] = ["name"=>"Process","style"=>"width:9%;","textAlign"=>"center"];
    $data['holdAreaMovement'][] = ["name"=>"Vendor","style"=>"width:9%;","textAlign"=>"center"];
    $data['holdAreaMovement'][] = ["name"=>"Qty","style"=>"width:9%;","textAlign"=>"center"];
    $data['holdAreaMovement'][] = ["name"=>"Ok Qty","style"=>"width:9%;","textAlign"=>"center"];
    $data['holdAreaMovement'][] = ["name"=>"Rejection Qty","style"=>"width:9%;","textAlign"=>"center"];
    $data['holdAreaMovement'][] = ["name"=>"Pending Qty","style"=>"width:9%;","textAlign"=>"center"];
    $data['holdAreaMovement'][] = ["name"=>"Pending Days","style"=>"width:9%;","textAlign"=>"center"];
    
    /* Hold Area  Header */
    $data['holdToOk'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];
    $data['holdToOk'][] = ["name"=>"#","style"=>"width:4%;","textAlign"=>"center"];
    $data['holdToOk'][] = ["name"=>"Date.","textAlign"=>"center"];
    $data['holdToOk'][] = ["name"=>"Job No.","textAlign"=>"center"];
    $data['holdToOk'][] = ["name"=>"Product","textAlign"=>"center"];
    $data['holdToOk'][] = ["name"=>"Process","style"=>"width:9%;","textAlign"=>"center"];
    $data['holdToOk'][] = ["name"=>"Vendor","style"=>"width:9%;","textAlign"=>"center"];
    $data['holdToOk'][] = ["name"=>"Qty","style"=>"width:9%;","textAlign"=>"center"];

    /* Gerenate Scrap */
    $data['generateScrap'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];
    $data['generateScrap'][] = ["name"=>"#","style"=>"width:4%;","textAlign"=>"center"];
    $data['generateScrap'][] = ["name"=>"Date","textAlign"=>"center"];
    $data['generateScrap'][] = ["name"=>"Item Name","textAlign"=>"center"];
    $data['generateScrap'][] = ["name"=>"Qty.","textAlign"=>"center"];
    $data['generateScrap'][] = ["name"=>"Remark","textAlign"=>"center"];
    

    /*** Pending Production Log Entry */
    $data['pendingProductionLog'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];
    $data['pendingProductionLog'][] = ["name"=>"#","style"=>"width:4%;","textAlign"=>"center"];
    $data['pendingProductionLog'][] = ["name"=>"Job No","textAlign"=>"center"];
    $data['pendingProductionLog'][] = ["name"=>"Product","textAlign"=>"center"];
    $data['pendingProductionLog'][] = ["name"=>"Process","textAlign"=>"center"];
    $data['pendingProductionLog'][] = ["name"=>"In Qty","textAlign"=>"center"];
    $data['pendingProductionLog'][] = ["name"=>"Production Qty","textAlign"=>"center"];
    $data['pendingProductionLog'][] = ["name"=>"Rej Qty","textAlign"=>"center"];
    $data['pendingProductionLog'][] = ["name"=>"RW Qty","textAlign"=>"center"];
    $data['pendingProductionLog'][] = ["name"=>"Pending Prod. Qty","textAlign"=>"center"];

    /* Production Log Pending For Approve  Header */
    $data['pendingApprovedLog'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];
    $data['pendingApprovedLog'][] = ["name"=>"#","style"=>"width:4%;","textAlign"=>"center"];
    $data['pendingApprovedLog'][] = ["name"=>"Job No.","textAlign"=>"center"];
    $data['pendingApprovedLog'][] = ["name"=>"Part Code","textAlign"=>"center"];
    $data['pendingApprovedLog'][] = ["name"=>"Date","textAlign"=>"center"];
    $data['pendingApprovedLog'][] = ["name"=>"Process","style"=>"width:9%;","textAlign"=>"center"];
    $data['pendingApprovedLog'][] = ["name"=>"Machine","textAlign"=>"center"];
    $data['pendingApprovedLog'][] = ["name"=>"Operator","textAlign"=>"center"];
    $data['pendingApprovedLog'][] = ["name"=>"Production Qty.","textAlign"=>"center"];

    /* Log Sheet  Header */
    $data['approvedLog'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];
	$data['approvedLog'][] = ["name"=>"#","style"=>"width:4%;","textAlign"=>"center"];
    $data['approvedLog'][] = ["name"=>"Job No.","textAlign"=>"center"];
    $data['approvedLog'][] = ["name"=>"Part Code","textAlign"=>"center"];
    $data['approvedLog'][] = ["name"=>"Date","textAlign"=>"center"];
	$data['approvedLog'][] = ["name"=>"Process","style"=>"width:9%;","textAlign"=>"center"];
    $data['approvedLog'][] = ["name"=>"Machine","textAlign"=>"center"];
    $data['approvedLog'][] = ["name"=>"Operator","textAlign"=>"center"];
    $data['approvedLog'][] = ["name"=>"Ok Qty.","textAlign"=>"center"];
    $data['approvedLog'][] = ["name"=>"Rejection Qty","textAlign"=>"center"];
    $data['approvedLog'][] = ["name"=>"Rework Qty","textAlign"=>"center"];
    $data['approvedLog'][] = ["name"=>"Hold Qty","textAlign"=>"center"];
    $data['approvedLog'][] = ["name"=>"Approved By","textAlign"=>"center"];

    /* vendor Challan Header */
    $data['pendingVendorChallan'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center","srnoPosition"=>0];
    $data['pendingVendorChallan'][] = ["name"=>"Job No"];
    $data['pendingVendorChallan'][] = ["name"=>"Product"];
    $data['pendingVendorChallan'][] = ["name"=>"Next Process"];
    $data['pendingVendorChallan'][] = ["name"=>"Qty"];
    $data['pendingVendorChallan'][] = ["name"=>"Challan Qty"];
    $data['pendingVendorChallan'][] = ["name"=>"Pending Qty"];

    /** PIR Report */
    $data['pir'][] = ["name" => "Action"];
    $data['pir'][] = ["name"=>"#"];
    $data['pir'][] = ["name"=>"PIR Date"];
    $data['pir'][] = ["name"=>"PIR No"];
    $data['pir'][] = ["name"=>"Jobcard"];
    $data['pir'][] = ["name"=>"Part Code"];
    $data['pir'][] = ["name"=>"Process"];
    $data['pir'][] = ["name"=>"Machine"];    
	$data['pir'][] = ["name"=>"Operator"];
    $data['pir'][] = ["name"=>"PIR By"];
    $data['pir'][] = ["name"=>"Remark"];

    /** Pending PIR Report */
    $data['pendingPir'][] = ["name" => "Action"];
    $data['pendingPir'][] = ["name"=>"#"];
    $data['pendingPir'][] = ["name"=>"Jobcard"];
    $data['pendingPir'][] = ["name"=>"Part Code"];
    $data['pendingPir'][] = ["name"=>"Process"];

    /** RQC Report */
    $data['rqc'][] = ["name" => "Action"];
    $data['rqc'][] = ["name"=>"#"];
    $data['rqc'][] = ["name"=>"RQC Date"];
    $data['rqc'][] = ["name"=>"RQC No"];
    $data['rqc'][] = ["name"=>"Jobcard"];
    $data['rqc'][] = ["name"=>"Part Code"];
    $data['rqc'][] = ["name"=>"Process"];
    $data['rqc'][] = ["name"=>"Vendor"];
    $data['rqc'][] = ["name"=>"RQC By"];
    $data['rqc'][] = ["name"=>"Heat No"];
    $data['rqc'][] = ["name"=>"In Ch. No"];
    $data['rqc'][] = ["name"=>"In Ch. Date"];
    $data['rqc'][] = ["name"=>"In Ch. Qty."];
    $data['rqc'][] = ["name"=>"Remark"];

    /** Pending RQC Report */
    $data['pendingRqc'][] = ["name" => "Action"];
    $data['pendingRqc'][] = ["name"=>"#"];
    $data['pendingRqc'][] = ["name"=>"Date"];
    $data['pendingRqc'][] = ["name"=>"Jobcard"];
    $data['pendingRqc'][] = ["name"=>"Part Code"];
    $data['pendingRqc'][] = ["name"=>"Process"];
    $data['pendingRqc'][] = ["name"=>"Vendor"];
    $data['pendingRqc'][] = ["name"=>"In Challan No"];
    $data['pendingRqc'][] = ["name"=>"Heat No"];
    $data['pendingRqc'][] = ["name"=>"Lot Qty"];

    /* NPD Job Card Header */
    $data['npdJobcard'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];
    $data['npdJobcard'][] = ["name"=>"#","style"=>"width:4%;","textAlign"=>"center"];
    $data['npdJobcard'][] = ["name"=>"Job No.","style"=>"width:9%;","textAlign"=>"center"];
    $data['npdJobcard'][] = ["name"=>"Job Date","textAlign"=>"center"];
    $data['npdJobcard'][] = ["name"=>"Product","textAlign"=>"center"];
    $data['npdJobcard'][] = ["name"=>"Qty.","textAlign"=>"center"];
    $data['npdJobcard'][] = ["name"=>"Remark"];
    
    /* PFC process Code Header*/
    $data['processCode'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['processCode'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['processCode'][] = ["name"=>"Process Code"];
    $data['processCode'][] = ["name"=>"Description"];

     /* Production Stock Approval Header*/ //26-09-2024
     $data['stockApproval'][] = ["name"=>"Action","style"=>"width:5%;"];
     $data['stockApproval'][] = ["name"=>"#","style"=>"width:5%;"];
     $data['stockApproval'][] = ["name"=>"Date"];
     $data['stockApproval'][] = ["name"=>"Job No"];
     $data['stockApproval'][] = ["name"=>"Product"];
     $data['stockApproval'][] = ["name"=>"Location"];
     $data['stockApproval'][] = ["name"=>"Qty"];
    
    /* Job Work Vendor Header */
    $data['vendorEntry'][] = ["name" => "Action", "style" => "width:5%;", "textAlign" => "center"];
    $data['vendorEntry'][] = ["name" => "#", "style" => "width:4%;", "textAlign" => "center"];
    $data['vendorEntry'][] = ["name" => "Challan Date", "textAlign" => "center"];
    $data['vendorEntry'][] = ["name" => "Challan No", "textAlign" => "center"];
    $data['vendorEntry'][] = ["name" => "Job Date", "style" => "width:9%;", "textAlign" => "center"];
    $data['vendorEntry'][] = ["name" => "Job No.", "style" => "width:9%;", "textAlign" => "center"];
    $data['vendorEntry'][] = ["name" => "Product"];
    $data['vendorEntry'][] = ["name" => "Process"];
    $data['vendorEntry'][] = ["name" => "Status", "textAlign" => "center"];
    $data['vendorEntry'][] = ["name" => "Weight", "textAlign" => "center"];
    $data['vendorEntry'][] = ["name" => "In Qty", "textAlign" => "center"];
    $data['vendorEntry'][] = ["name" => "Out Qty", "textAlign" => "center"];
    $data['vendorEntry'][] = ["name" => "Pending Qty", "textAlign" => "center"];
    $data['vendorEntry'][] = ["name" => "Return Qty", "textAlign" => "center"];
    $data['vendorEntry'][] = ["name" => "Remark", "textAlign" => "center"];


    /* Job Work Vendor Receive Header */
    $data['jobWorkVendorReceive'][] = ["name" => "Action", "style" => "width:5%;", "textAlign" => "center"];
    $data['jobWorkVendorReceive'][] = ["name" => "#", "style" => "width:4%;", "textAlign" => "center"];
    $data['jobWorkVendorReceive'][] = ["name" => "Challan Date", "textAlign" => "center"];
    $data['jobWorkVendorReceive'][] = ["name" => "Challan No", "textAlign" => "center"];
    $data['jobWorkVendorReceive'][] = ["name" => "Job Date", "style" => "width:9%;", "textAlign" => "center"];
    $data['jobWorkVendorReceive'][] = ["name" => "Job No.", "style" => "width:9%;", "textAlign" => "center"];
    $data['jobWorkVendorReceive'][] = ["name" => "Vendor"];
    $data['jobWorkVendorReceive'][] = ["name" => "Product"];
    $data['jobWorkVendorReceive'][] = ["name" => "In Challan No", "textAlign" => "center"];
    $data['jobWorkVendorReceive'][] = ["name" => "In Challan Date", "textAlign" => "center"];
    $data['jobWorkVendorReceive'][] = ["name" => "Process"];
    $data['jobWorkVendorReceive'][] = ["name" => "Production Qty", "textAlign" => "center"];
    $data['jobWorkVendorReceive'][] = ["name" => "Without Process Qty", "textAlign" => "center"];

    /* Costing Header */
    $data['productCosting'][] = ["name"=>"Action","style"=>"width:15%;","textAlign"=>"center"];
    $data['productCosting'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
    $data['productCosting'][] = ["name"=>"Enq No"];
    $data['productCosting'][] = ["name"=>"Party Name"];
    $data['productCosting'][] = ["name"=>"Part Code"];
    $data['productCosting'][] = ["name"=>"Part Name"];
    $data['productCosting'][] = ["name"=>"Revision No.","style"=>"width:10%;","textAlign"=>"center"];

    /* Purchase Costing Header */
    $data['purchaseCostReq'][] = ["name"=>"Action","style"=>"width:15%;","textAlign"=>"center"];
    $data['purchaseCostReq'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
    $data['purchaseCostReq'][] = ["name"=>"Enq No"];
    $data['purchaseCostReq'][] = ["name"=>"Party Name"];
    $data['purchaseCostReq'][] = ["name"=>"Part Code"];
    $data['purchaseCostReq'][] = ["name"=>"Part Name"];
    $data['purchaseCostReq'][] = ["name"=>"Material Grade"];
    // $data['purchaseCostReq'][] = ["name"=>"Dimension"];
    $data['purchaseCostReq'][] = ["name"=>"MOQ"];
    $data['purchaseCostReq'][] = ["name"=>"Gross Wt"];
    $data['purchaseCostReq'][] = ["name"=>"Finish Wt"];
    $data['purchaseCostReq'][] = ["name"=>"Required Material"];

    /* Mfg Costing Header */
    $data['mfgCostReq'][] = ["name"=>"Action","style"=>"width:15%;","textAlign"=>"center"];
    $data['mfgCostReq'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
    $data['mfgCostReq'][] = ["name"=>"Enq No"];
    $data['mfgCostReq'][] = ["name"=>"Party Name"];
    $data['mfgCostReq'][] = ["name"=>"Part Code"];
    $data['mfgCostReq'][] = ["name"=>"Part Name"];
    $data['mfgCostReq'][] = ["name"=>"Material Grade"];
    $data['mfgCostReq'][] = ["name"=>"Dimension"];
    $data['mfgCostReq'][] = ["name"=>"MOQ"];
    $data['mfgCostReq'][] = ["name"=>"Gross Wt"];
    $data['mfgCostReq'][] = ["name"=>"Finish Wt"];
    $data['mfgCostReq'][] = ["name"=>"Required Material"];

    return tableHeader($data[$page]);
}

/* Process Table Data */
function getProcessData($data){
    $deleteParam = $data->id.",'Process'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editProcess', 'title' : 'Update Process'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	
    $mhrParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'addMhr', 'title' : 'MHR Detail : ".$data->process_name."', 'fnEdit':'addMhr','fnsave':'saveMhr'}";

    $mhrButton = '<a class="btn btn-primary btn-edit permission-approve" href="javascript:void(0)" datatip="MHR" flow="down" onclick="edit('.$mhrParam.');"><i class="fas fa-rupee-sign" ></i></a>';

	$action = getActionButton($mhrButton.$editButton.$deleteButton);
    return [$action,$data->sr_no,$data->process_name,$data->department,$data->is_machining,$data->remark];
}

/* Costing Data * Created By Meghavi @10/08/2022 */
function getCostingData($data){
    $btn = '<button type="button" class="btn btn-info addCosting permission-modify" data-id="'.$data->id.'" data-product_name="'.$data->item_code.'" data-button="both" data-modal_id="modal-md" data-function="viewProductProcess" data-fnsave="saveCosting" data-form_title="Product Costing" datatip="Product Costing" flow="down"><i class="fa fa-list"></i></button>';
    return [$data->sr_no,$data->item_code,$data->process,$btn];
}

/* Job Card Table Data */
function getJobcardData($data){
    $deleteParam = $data->id.",'Jobcard'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editJobcard', 'title' : 'Update Jobcard'}";
    $reqParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'requiredTest', 'title' : 'Requirement'}";

    $editButton="";$deleteButton = "";$startOrder = "";$holdOrder = "";$restartOrder = '';$dispatchBtn = ''; $shortClose = ''; $updateJob = '';

    $shortClose = '<a class="btn btn-instagram btn-shortClose changeOrderStatus permission-modify" href="javascript:void(0)" datatip="Short Close" flow="down" data-val="6" data-id="'.$data->id.'"><i class="ti-close"></i></a>';
    
    $jobNo = '<a href="'.base_url($data->controller."/view/".$data->id).'">'.(getPrefixNumber($data->job_prefix,$data->job_no)).'</a>';
    
    if($data->order_status == 0):
        if(empty($data->md_status)):
            $dispatchBtn = '<a class="btn btn-success btn-request permission-write" href="javascript:void(0)" datatip="Material Request" flow="down" data-id="'.$data->id.'" data-function="materialRequest"><i class="fas fa-paper-plane" ></i></a>';
        else:
            if($data->mr_status == 0):
                $startOrder = '<a class="btn btn-success btn-start materialReceived permission-modify" href="javascript:void(0)" datatip="Material Received" flow="down" data-val="1" data-id="'.$data->id.'"><i class="fa fa-check" ></i></a>';
            else:
                $startOrder = '<a class="btn btn-success btn-start changeOrderStatus permission-modify" href="javascript:void(0)" datatip="Start" flow="down" data-val="1" data-id="'.$data->id.'"><i class="ti-control-play" ></i></a>';
            endif;
        endif;
    elseif($data->order_status == 2):
        $holdOrder = '<a class="btn btn-danger btn-hold changeOrderStatus permission-modify" href="javascript:void(0)" datatip="Hold" flow="down" data-val="3" data-id="'.$data->id.'"><i class="ti-control-pause" ></i></a>';
        
        if(isset($data->pendingQty)):
            $updateQtyParam = "{'id' : " . $data->id . ", 'modal_id' : 'modal-lg', 'form_id' : 'updateJobQty', 'title' : 'Update Jobcard Qty [".$data->job_prefix.$data->job_no."] Pending Qty.: ".$data->pendingQty."', 'fnEdit':'updateJobQty','button':'close'}";
            $updateJob = '<a class="btn btn-info permission-modify" href="javascript:void(0)" datatip="Update Job Qty." flow="down" onclick="edit(' . $updateQtyParam . ');"><i class="ti-exchange-vertical"></i></a>';
        endif;
    elseif($data->order_status == 3):
        $jobNo = getPrefixNumber($data->job_prefix,$data->job_no);
        $restartOrder = '<a class="btn btn-success btn-restart changeOrderStatus permission-modify" href="javascript:void(0)" datatip="Restart" flow="down" data-val="2" data-id="'.$data->id.'"><i class="ti-control-play"></i></a>';
    elseif($data->order_status == 4):
        $shortClose = '';
    elseif($data->order_status == 5):
        $shortClose = '';
        $jobNo = getPrefixNumber($data->job_prefix,$data->job_no);
        
        $restartOrder = '<a class="btn btn-success btn-restart changeOrderStatus permission-modify" href="javascript:void(0)" datatip="Restart" flow="down" data-val="2" data-id="'.$data->id.'"><i class="ti-control-play" ></i></a>';
    elseif($data->order_status == 6):
        $editButton="";$deleteButton = "";$startOrder = "";$holdOrder = "";$restartOrder = ''; $dispatchBtn = '';$shortClose='';
        $jobNo = getPrefixNumber($data->job_prefix,$data->job_no);
       
        $restartOrder = '<a class="btn btn-success btn-restart changeOrderStatus permission-modify" href="javascript:void(0)" datatip="Restart" flow="down" data-val="2" data-id="'.$data->id.'"><i class="ti-control-play" ></i></a>';
    endif;

    //Regular Order
    if(empty($data->md_status) && empty($data->ref_id) && empty($data->order_status)):
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';        
    endif;

    //Rework Order
    if(!empty($data->ref_id) && empty($data->order_status)):
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    endif;
	
	// last activity
    $firstdate = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d'))));
    $seconddate = date('Y-m-d', strtotime('-2 day', strtotime(date('Y-m-d'))));
    $thirdate = date('Y-m-d', strtotime('-3 day', strtotime(date('Y-m-d'))));
    $lastAdate = date('Y-m-d', strtotime($data->last_activity)); 

    $color='';
    if($lastAdate >= $firstdate) { $color="text-primary"; } 
	elseif($lastAdate == $seconddate) { $color="text-dark"; } 
	else { $color="text-danger"; }

    $last_activity = '<a href="javascript:void(0);" class="'.$color.' viewLastActivity" data-trans_id="'.$data->id.'" data-job_no="'.(getPrefixNumber($data->job_prefix,$data->job_no)).'" datatip="View Last Activity" flow="down"><b>'.$data->last_activity.'</b></a>';

    $type = ($data->job_category == 0) ? 'Manufacturing' : 'Jobwork';
    $print = '<a href="'.base_url($data->controller).'/printDetailedRouteCard/'.$data->id.'" class="btn btn-instagram" target="_blank"><i class="fa fa-print"></i></a>';
    
    $action = getActionButton($updateJob.$dispatchBtn.$startOrder.$holdOrder.$restartOrder.$shortClose.$print.$editButton.$deleteButton);
    return [$action,$data->sr_no,$jobNo,date("d-m-Y",strtotime($data->job_date)),date("d-m-Y",strtotime($data->delivery_date)),$data->party_code,$data->item_code,floatVal($data->qty),$data->order_status_label,$data->remark,$last_activity];
}

/* Material Request Data */
function getMaterialRequest($data){
    $editButton=''; $deleteButton='';
    if($data->order_status == 2 || $data->order_status == 0):
        $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'materialRequest', 'title' : 'Material Request'}";
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
        
        $deleteParam = $data->id.",'Request'"; 
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    endif;    

    $action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,(!empty($data->req_date))?date("d-m-Y",strtotime($data->req_date)):"",(!empty($data->dispatch_date))?date("d-m-Y",strtotime($data->dispatch_date)):"",$data->fg_name,$data->req_item_name,$data->req_qty,$data->order_status_label];
}

/* Jobwork Order Data */
function getJobWorkOrderData($data){
    $deleteParam = $data->id.",'Job Work Order'"; $approve = "";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editJobOrder', 'title' : 'Update Job Work Order'}";
    $editButton='';$deleteButton='';
    if(empty($data->is_approve)){
        $approve = '<a href="javascript:void(0)"  class="btn btn-facebook approveJobWorkOrderView permission-approve" data-id="'.$data->id.'" data-val="1" data-msg="Approve" datatip="Approve Job Work Order" flow="down" ><i class="fa fa-check" ></i></a>';

        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    } else {
        $approve = '<a href="javascript:void(0)"  class="btn btn-facebook approveJobWorkOrder permission-approve" data-id="'.$data->id.'" data-val="2" data-msg="Reject" datatip="Reject Job Work Order" flow="down" ><i class="fa fa-ban" ></i></a>';
    }
    

    //$printBtn = '<a class="btn btn-warning btn-edit" href="'.base_url($data->controller.'/jobworkOrderChallan/'.$data->id).'" target="_blank" datatip="Regular Print" flow="down"><i class="fas fa-print" ></i></a>';
    $printBtnFull = '<a class="btn btn-warning btn-edit" href="'.base_url($data->controller.'/jobworkOrderChallanFull/'.$data->id).'" target="_blank" datatip="Full Page Print" flow="down"><i class="fas fa-print" ></i></a>';
	
	if(empty($data->is_close)){
        $shortClose = '<a class="btn btn-dark btn-shortClose changeOrderStatus permission-modify" href="javascript:void(0)" datatip="Short Close" flow="down" data-val="1" data-id="'.$data->id.'"><i class="ti-close"></i></a>';
        $action = getActionButton($approve.$shortClose.$printBtnFull.$editButton.$deleteButton);
    }else{
        $shortClose = '<a class="btn btn-dark btn-shortClose changeOrderStatus permission-modify" href="javascript:void(0)" datatip="Re-open" flow="down" data-val="0" data-id="'.$data->id.'"><i class="ti-loop"></i></a>';
        $action = getActionButton($shortClose);
    }
	
	$qty = ($data->rate_per == 1) ? $data->qty : $data->qty_kg ;
	$productName ="";
    if($data->item_type == 1){
        $productName = $data->item_code;
    }else{
        $productName = $data->item_name;
    }
    return [$action,$data->sr_no,formatDate($data->jwo_date),getPrefixNumber($data->jwo_prefix,$data->jwo_no),$data->party_name,$productName,floatVal($qty),sprintf('%0.2f',$data->rate),$data->approve_status,$data->process,$data->remark];
    
}
/* Job Work Table Data */
function getJobWorkData($data){
    $returnBtn=""; $printBtn="";
    //$printBtn = '<a class="btn btn-success btn-edit" href="'.base_url($data->controller.'/jobworkOutChallan/'.$data->id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';

    if(empty($data->accepted_by)):         
        $button = '<a class="btn btn-success permission-write" onclick="acceptJob('.$data->id.')" href="javascript:void(0)"  datatip="Accept" flow="down"><i class="fa fa-check"></i></a>';        
    else:
        $dataRow = ['product_name'=>$data->item_code,'ref_id'=>$data->id,'product_id'=>$data->product_id,'in_process_id'=>$data->process_id,'job_card_id'=>$data->job_card_id,'process_name'=>$data->process_name,'pending_qty'=>$data->pending_qty,'issue_batch_no'=>$data->issue_batch_no,'issue_material_qty'=>$data->issue_material_qty,'material_used_id'=>$data->material_used_id,'minDate'=>$data->minDate];

        $button = "<a class='btn btn-warning getForward permission-modify' href='javascript:void(0)' datatip='Receive Material' flow='down' data-row='".json_encode($dataRow)."' data-toggle='modal' data-target='#outwardModal'><i class='fas fa-paper-plane' ></i></a>";

        if(!empty($data->pending_qty)):
            $returnParams = ['product_name'=>$data->item_code,'job_trans_id'=>$data->id,'job_approval_id'=>$data->job_approval_id,'product_id'=>$data->product_id,'in_process_id'=>$data->process_id,'job_card_id'=>$data->job_card_id,'process_name'=>$data->process_name,'pending_qty'=>$data->pending_qty,'minDate'=>$data->minDate,'job_process_ids'=>$data->job_process_ids,'fnEdit'=>"jobWorkReturn",'fnsave'=>"jobWorkReturnSave",'modal_id'=>"modal-lg",'title'=>"Return",'form_id'=>"jobWorkReturnSave"];
    
            $returnBtn = "<a class='btn btn-info btn-edit ' href='javascript:void(0)' datatip='Return' flow='down' onclick='jobWorkReturn(".json_encode($returnParams).");'><i class='fas fa-reply'></i></a>";
        endif;
    endif;
    $action = getActionButton($returnBtn.$button.$printBtn);
    return [$action,$data->sr_no,getPrefixNumber($data->job_prefix,$data->job_no),$data->party_name,$data->item_code,$data->process_name,$data->status,floatVal($data->in_qty),floatVal($data->out_qty),floatVal($data->rejection_qty),floatVal($data->rework_qty),floatVal($data->pending_qty)];
}

/* Job Work Table Data */
function getJobWorkVendorData($data){
    $returnBtn="";$moveBtn="";  $deleteButton = "";

    if(!empty(floatVal($data->pending_qty))):
        $returnParams = ['product_name'=>$data->item_code,'id'=>$data->id,'product_id'=>$data->item_id,'process_id'=>$data->process_id,'process_name'=>$data->process_name,'pending_qty'=>$data->pending_qty,'job_card_id'=>$data->job_card_id,'production_approval_id'=>$data->job_approval_id,'production_trans_id'=>$data->ref_id,'fnEdit'=>"jobWorkReturn",'fnsave'=>"jobWorkReturnSave",'modal_id'=>"modal-lg",'title'=>"Return",'form_id'=>"jobWorkReturnSave"];

        /* $returnBtn = "<a class='btn btn-info btn-edit ' href='javascript:void(0)' datatip='Return Without Process' flow='down' onclick='jobWorkReturn(".json_encode($returnParams).");'><i class='fas fa-reply'></i></a>"; */
        
        $outParam = "{'id' : " . $data->job_approval_id . ", 'ch_trans_id' : ".$data->id." , 'challan_id' : ".$data->challan_id." ,'modal_id' : 'modal-lg', 'form_id' : 'outWard', 'title' : '".$data->trans_number." [".$data->item_code."]  Pend. Qty: ".floatVal($data->pending_qty)."'}";
    
        /* $moveBtn = '<a class="btn btn-success btn-edit" href="javascript:void(0)" datatip="Receive Material" flow="down" onclick="outward(' . $outParam . ');"><i class="fas fa-paper-plane"></i></a>'; */
    endif;
    if(empty(floatVal($data->return_qty)) && $data->trans_status == 0):
        $deleteParam = $data->challan_id.""; 
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    endif;
    
    $ewbPDF = '';$ewbDetailPDF = '';$generateEWB = '';   
    if(!empty($data->eway_bill_no) && $data->eway_bill_no > 0):
        $ewbPDF = '<a href="'.base_url('ewaybill/ewb_pdf/'.$data->eway_bill_no).'" target="_blank" datatip="EWB PDF" flow="down" class="btn btn-dark"><i class="fa fa-print"></i></a>';

        $ewbDetailPDF = '<a href="'.base_url('ewaybill/ewb_detail_pdf/'.$data->eway_bill_no).'" target="_blank" datatip="EWB DETAIL PDF" flow="down" class="btn btn-warning"><i class="fa fa-print"></i></a>';
    else:
        if(empty($data->eway_bill_no)):
            $ewbParam = "{'id' : ".$data->challan_id.", 'modal_id' : 'modal-xl', 'form_id' : 'generateEwb', 'title' : 'E-way Bill For Challan No. : ".($data->trans_number)."', 'fnEdit' : 'loadJCEWBForm', 'fnsave' : 'generateNewEwb', 'fnonclick' : 'generateNewEway'}";

            $generateEWB = '<a href="javascript:void(0)" class="btn btn-dark" datatip="E-way Bill" flow="down" onclick="ewaybill('.$ewbParam.');"><i class="fa fa-truck"></i></a>';
        endif;
    endif;
    $returnMaterialParam = "{'id' : ".$data->challan_id.", 'modal_id' : 'modal-lg', 'form_id' : 'editVendorChallan', 'title' : 'Return  Material', 'fnEdit':'returnVendorMaterial','fnsave':'saveReturnMaterial'}";
    $returnMaterialBtn = '<a class="btn btn-info btn-edit permission-modify" href="javascript:void(0)" datatip="Return Material" flow="down" onclick="edit('.$returnMaterialParam.');"><i class="fab fa-dropbox"></i></a>';
	// $returnMaterialBtn = '';
	
	$printBtn = '<a class="btn btn-success btn-edit" href="'.base_url('production_v3/jobWorkVendor/jobworkOutChallan/'.$data->challan_id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';
    $action = getActionButton($generateEWB.$ewbPDF.$ewbDetailPDF.$moveBtn.$printBtn.$returnBtn.$returnMaterialBtn.$deleteButton);

    return [$action,$data->sr_no,formatDate($data->trans_date),$data->trans_number,date('d-m-Y',strtotime($data->job_date)),getPrefixNumber($data->job_prefix,$data->job_no),$data->party_name,$data->item_code,$data->process_name,$data->status,floatVal($data->weight),floatVal($data->qty),floatVal($data->return_qty),floatVal($data->pending_qty),floatVal($data->without_process_qty),$data->remark];
}

/* Production Opration Data */
function getProductionOperationData($data){
    $deleteParam = $data->id.",'Production Operation'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editProductionOperation', 'title' : 'Update Production Operation'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->operation_name];
}

/* Product Option Data */
function getProductOptionData($data){

	$btn = '<div class="btn-group" role="group" aria-label="Basic example">
				<button type="button" class="btn btn-twitter productKit permission-modify printbtn" data-id="'.$data->id.'" data-product_name="'.$data->item_code.'" data-button="both" data-modal_id="modal-lg" data-function="addProductKitItems" data-form_title="Create Material BOM" datatip="BOM" flow="down"><i class="fas fa-dolly-flatbed"></i></button>
				
				<button type="button" class="btn btn-info viewItemProcess permission-modify" data-id="'.$data->id.'" data-product_name="'.$data->item_code.'" data-button="close" data-modal_id="modal-xl" data-function="addProductProcess" data-form_title="Set Product Process" datatip="View Process" flow="down"><i class="fa fa-list"></i></button>
				
				<button type="button" class="btn btn-twitter addProductOption permission-modify" data-id="'.$data->id.'" data-product_name="'.$data->item_code.'" data-button="both" data-modal_id="modal-md" data-function="addCycleTime" data-fnsave="saveCT" data-form_title="Set Cycle Time" datatip="Cycle Time" flow="down"><i class="fa fa-clock"></i></button>
				
				<button type="button" class="btn btn-info addProductOption permission-modify" data-id="'.$data->id.'" data-product_name="'.$data->item_code.'" data-button="close" data-modal_id="modal-xl" data-function="productPrcLog" data-form_title="Cycle Time Log" datatip="Cycle Time Log" flow="down"><i class="fa fa-info"></i></button>
				
				<!--<button type="button" class="btn btn-info addProductOption permission-modify" data-id="'.$data->id.'" data-product_name="'.$data->item_name.'" data-button="close" data-modal_id="modal-xl" data-function="addToolConsumption" data-fnsave="saveToolConsumption" data-form_title="Set Tool Consumption" datatip="Tool Consumption" flow="left"><i class="fas fa-wrench"></i></button>-->
            </div>';

    //return [$data->sr_no,$data->item_code,$data->bom,$data->process,$data->cycleTime,$data->tool,$btn];
    return [$data->sr_no,$data->item_code,$data->bom,$data->process,$data->cycleTime,$btn];
}

/* Process Setup Data */
function getProcessSetupData($data){
    $acceptBtn = "";$editButton = "";
    if(empty($data->setup_start_time)):
        $acceptBtn = '<a class="btn btn-success permission-write" onclick="acceptJob('.$data->id.')" href="javascript:void(0)"  datatip="Accept" flow="down"><i class="fa fa-check"></i></a>'; 
    else:
        $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editProcessSetup', 'title' : 'Process Setup', 'fnEdit' : 'processSetup'}";

        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Finish Setup" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    endif;    

    $action = getActionButton($acceptBtn.$editButton);
    return [$action,$data->sr_no,date("d-m-Y",strtotime($data->request_date)),$data->status,$data->setup_type_name,$data->setter_name,$data->setup_note,getPrefixNumber($data->job_prefix,$data->job_no),$data->item_code,$data->process_name,(!empty($data->machine_code) || !empty($data->machine_name))?'[ '.$data->machine_code.' ] '.$data->machine_name:"",$data->inspector_name,(!empty($data->setup_start_time))?date("d-m-Y h:i:s A",strtotime($data->setup_start_time)):"",(!empty($data->setup_end_time))?date("d-m-Y h:i:s A",strtotime($data->setup_end_time)):"",$data->duration,$data->setter_note];
}

/* Line Inspection Data */
function getLineInspectionData($data){
    $btnParam = ['ref_id'=>$data->id,'product_id'=>$data->product_id,'process_id'=>$data->process_id,'job_card_id'=>$data->job_card_id,'product_name'=>$data->product_code,'process_name'=>$data->process_name,'pending_qty'=>$data->pending_qty,'mindate'=>$data->minDate,'modal_id'=>'modal-xxl','form_id'=>'lineInspectionFrom','title'=>'Line Inspection'];

    $button = "<a class='btn btn-warning getForward permission-modify' href='javascript:void(0)' datatip='Forward' flow='down' onclick='lineInspection(".json_encode($btnParam).");'><i class='fas fa-paper-plane' ></i></a>";

    $action = getActionButton($button);
    $selectBox = '<input type="checkbox" name="ref_id[]" id="ref_id_'.$data->sr_no.'" class="filled-in chk-col-success BulkLineInspection" value="'.$data->id.'"><label for="ref_id_'.$data->sr_no.'"></label>';
    if($data->inspected_qty >= $data->in_qty):
        $selectBox = "";
    endif;
    return [$action,$data->sr_no,$selectBox,getPrefixNumber($data->job_prefix,$data->job_no),$data->process_name,$data->product_code,(!empty($data->party_name))?$data->party_name:"In House",$data->in_qty,$data->out_qty,$data->rejection_qty,$data->rework_qty,$data->status];
}

/* Vendor Challan Data */
function getVendorChallanData($data){
    $deleteParam = $data->id.",'Vendor Challan'";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    $printBtn = '<a class="btn btn-success btn-edit" href="'.base_url($data->controller.'/jobworkOutChallan/'.$data->id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';
    
    
    $returnParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editVendorChallan', 'title' : 'Return Vendor Material', 'fnEdit':'returnVendorMaterial','fnsave':'saveReturnMaterial'}";
    $returnBtn = '<a class="btn btn-info btn-edit permission-modify" href="javascript:void(0)" datatip="Return Vendor Material" flow="down" onclick="edit('.$returnParam.');"><i class="fas fa-reply"></i></a>';
    
    $ewbPDF = '';$ewbDetailPDF = '';$generateEWB = '';   
    if(!empty($data->eway_bill_no) && $data->eway_bill_no > 0):
        $ewbPDF = '<a href="'.base_url('ewaybill/ewb_pdf/'.$data->eway_bill_no).'" target="_blank" datatip="EWB PDF" flow="down" class="btn btn-dark"><i class="fa fa-print"></i></a>';

        $ewbDetailPDF = '<a href="'.base_url('ewaybill/ewb_detail_pdf/'.$data->eway_bill_no).'" target="_blank" datatip="EWB DETAIL PDF" flow="down" class="btn btn-warning"><i class="fa fa-print"></i></a>';
    else:
        if(empty($data->eway_bill_no)):
            $ewbParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'generateEwb', 'title' : 'E-way Bill For Challan No. : ".(getPrefixNumber($data->challan_prefix,$data->challan_no))."', 'fnEdit' : 'loadJCEWBForm', 'fnsave' : 'generateNewEwb', 'fnonclick' : 'generateNewEway'}";

            $generateEWB = '<a href="javascript:void(0)" class="btn btn-dark" datatip="E-way Bill" flow="down" onclick="ewaybill('.$ewbParam.');"><i class="fa fa-truck"></i></a>';
        endif;
    endif;

	$action = getActionButton($returnBtn.$printBtn.$generateEWB.$ewbPDF.$ewbDetailPDF.$deleteButton);
    return [$action,$data->sr_no,formatDate($data->challan_date),getPrefixNumber($data->challan_prefix,$data->challan_no),$data->party_name,$data->item_code,$data->qty];
}

/* Scrap Table Data */
function getScrapData($data){
    $deleteParam = $data->id.",'Rejection Scrap'";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $transList = '<a href="javascript:void(0)" class="btn btn-primary createTransList permission-read" data-id="'.$data->id.'"  datatip="Transaction List" flow="down"><i class="fa fa-list" ></i></a>';
    $action = getActionButton($transList.$deleteButton);
    return [$action,$data->sr_no,formatDate($data->trans_date),getPrefixNumber($data->job_prefix,$data->job_no),$data->item_code,$data->scrap_qty,$data->ok_qty];
}

function getLogSheetData($data){
    $action = ""; $editButton= "";  $deleteButton= "";
    if($data->prod_type != 3 && $data->is_approve == 0){
        $editParam = "{'id' : ".$data->id.",'log_type' : ".$data->log_type.", 'modal_id' : 'modal-xl', 'form_id' : 'productionLog', 'title' : 'Update Production Log'}";
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="editLog('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
        
        $deleteParam = $data->id.",'Production Log'";
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    }
    $rwQty = $data->rw_qty;
    
    $viewParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'productionLog', 'title' : 'Production Log','button':'close','fnEdit':'viewLogData'}";
    $viewButton = '<a class="btn btn-success btn-edit permission-read" href="javascript:void(0)" datatip="View" flow="down" onclick="edit('.$viewParam.');"><i class="fa fa-eye" ></i></a>';
    
    $action = getActionButton($viewButton.$editButton.$deleteButton);

    return [$action,$data->sr_no,getPrefixNumber($data->job_prefix, $data->job_no),$data->product_name,formatDate($data->log_date),$data->process_name,$data->item_code,$data->emp_name,$data->production_qty,$data->rej_qty,$rwQty];
}

// Created By Avruti @23/04/2022
function getRmProcessData($data){
    $action = "";  $deleteParam = $data->ref_batch.",'RM Process'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'rmProcess', 'title' : 'Update RM Process'}";
    $returnParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'rmProcess', 'title' : 'Return Rm Process [".$data->item_name." ]','button':'close','fnEdit':'returnRmProcess','fnsave':'saveReturnRM'}";

    $returnRMButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Return" flow="down" onclick="edit('.$returnParam.');"><i class="fas fa-reply" ></i></a>';
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $jwoNo = (!empty($data->jwo_prefix) && !empty($data->jwo_no))?getPrefixNumber($data->jwo_prefix,$data->jwo_no):'';

    $printBtn = '<a class="btn btn-info btn-edit" href="'.base_url($data->controller.'/rmProcessOutChallan/'.$data->id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';
    
    $action = getActionButton($printBtn.$returnRMButton.$deleteButton);
    return [$action,$data->sr_no,formatDate($data->ref_date),$data->party_name,$jwoNo,$data->item_name,$data->qty,$data->return_itm];
}

/* Created By Mansee @ 14-06-2022 */
function getHoldAreaMovementData($data){
    $pending_qty=$data->in_qty-$data->ok_qty-$data->rej_qty;
    $outParam = "{'id' : " . $data->production_approval_id . ", 'entry_type' : ".$data->entry_type.", 'trans_ref_id' : ".$data->id.", 'pending_qty' : ".$pending_qty." ,'modal_id' : 'modal-xl', 'form_id' : 'outWard', 'title' : 'Process Moved','button' : 'close'}";  
    
    $moveBtn = '<a class="btn btn-success btn-edit" href="javascript:void(0)" datatip="Move" flow="down" onclick="outward(' . $outParam . ');"><i class="fas fa-paper-plane"></i></a>';
    $action = getActionButton($moveBtn);
    
    $dateDiff = time() - strtotime($data->entry_date);
    $pendingDays = round((time() - strtotime($data->entry_date)) / (60 * 60 * 24));
    
    return [$action,$data->sr_no,formatDate($data->entry_date),getPrefixNumber($data->job_prefix, $data->job_no),'['.$data->item_code.'] '.$data->item_name,$data->process_name,$data->vendor_name,$data->in_qty,$data->ok_qty,$data->rej_qty,$pending_qty,$pendingDays.' Days'];
}

function getHoldToOkMovementData($data){
    $pending_qty=$data->in_qty;
    $outParam = "{'id' : " . $data->id . " , 'pending_qty' : ".$pending_qty." ,'modal_id' : 'modal-xl', 'form_id' : 'outWard', 'title' : 'Process Moved','button' : 'close'}";  
    
    $moveBtn = '<a class="btn btn-success btn-edit" href="javascript:void(0)" datatip="Move" flow="down" onclick="outward(' . $outParam . ');"><i class="fas fa-paper-plane"></i></a>';
    $action = getActionButton($moveBtn);

    return [$action,$data->sr_no,formatDate($data->entry_date),getPrefixNumber($data->job_prefix, $data->job_no),$data->item_code,$data->process_name,$data->vendor_name,$data->in_qty];
}

function getGenerateScrapData($data){
    $deleteParam = $data->id.",'Scrape'";    
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editScrap', 'title' : 'Update Scrap'}";

    $editButton = '<a class="btn btn-success btn-edit" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->ref_date,$data->item_name,$data->qty,$data->remark];
}

function getPendingProductionLog($data){    
	$pending_qty = $data->in_qty - $data->production_qty-$data->ch_qty;
    $title = "Add Log  [ Pending Qty : ".$pending_qty."]";
    if($data->prod_type == 1){
        $title =  (($data->log_type ==0)?"Add Production Ok Log":"Add Rejection/Rework Log ")."[ Pending Qty : ".$pending_qty."]";
    }
    
    $addParam = "{'id' : ".$data->id.",'log_type' : ".$data->log_type.", 'modal_id' : 'modal-xl', 'form_id' : 'rejectionLog', 'title' : '".$title ."', 'fnEdit' : 'addProductionLog', 'fnsave' : 'save'}";
    $addButton = '<a class="btn btn-success btn-edit " href="javascript:void(0)" datatip="Add Log" flow="down" onclick="openLogForm('.$addParam.');"><i class=" fas fa-plus-circle" ></i></a>';
    $action = getActionButton($addButton);
    
    return [$action,$data->sr_no,getPrefixNumber($data->job_prefix, $data->job_no),$data->item_code,$data->process_name,$data->in_qty,$data->totalOkQty,$data->total_rej_qty,$data->total_rw_qty,$pending_qty];

}

function getProductionLogData($data){
    $action = ""; $editButton= "";  $deleteButton= "";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'productionLog', 'title' : 'Approve Production Log', 'fnEdit':'addProductionLogApproval'}";
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="editLog('.$editParam.');"><i class=" fas fa-check-square" ></i></a>';
    $action = getActionButton($editButton);
    return [$action,$data->sr_no,getPrefixNumber($data->job_prefix, $data->job_no),$data->product_name,formatDate($data->log_date),$data->process_name,$data->item_code,$data->emp_name,$data->production_qty];
}

function getApprovedProductionLogData($data){
    $action = ""; $editButton= "";  $deleteButton= "";
    $pendingMovement = $data->total_ok_qty - $data->out_qty;
    if($data->ok_qty <= $pendingMovement  && empty($data->edit_disabled)){
        $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'productionLog', 'title' : 'Approve Production Log', 'fnEdit':'addProductionLogApproval'}";
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Approve Log" flow="down" onclick="editLog('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    
        $deleteParam = $data->id.",'Production Log'";
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    }

	$action = getActionButton($editButton.$deleteButton);


    $rwQty = $data->rework_qty;
    if($data->rework_qty > 0){
        $rwQty = '<a href="'.base_url('production_v3/productionLog/reworkmanagement/'.$data->id).'" target="_blank">'.$data->rework_qty.'</a>';
    }

    /*$rwQty = $data->rw_qty;
    if($data->rw_qty > 0){
        $rwQty = '<a href="'.base_url('production_v3/productionLog/reworkmanagement/'.$data->id).'" target="_blank">'.$data->rw_qty.'</a>';
    }*/
    
    $holdQty = $data->hold_qty;
    if($data->hold_qty > 0){
        $holdParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'productionLog', 'title' : 'Approve Production Log', 'fnEdit':'holdQtyLog','fnsave':'saveHoldQtyLog'}";
       
        $holdQty = '<a class="permission-modify" href="javascript:void(0)" onclick="editLog('.$holdParam.');">'.$data->hold_qty.'</a>';

    }
    return [$action,$data->sr_no,getPrefixNumber($data->job_prefix, $data->job_no),$data->product_name,formatDate($data->log_date),$data->process_name,$data->item_code,$data->emp_name,$data->ok_qty,$data->rej_qty,$rwQty, $holdQty, $data->approval_name];
}

function getPendingVendorChallanData($data){
    $action="";
    $pendingQty  = $data->qty-$data->challan_qty;
    return [$data->sr_no,getPrefixNumber($data->job_prefix, $data->job_no),$data->item_code,$data->process_name,floatval($data->qty),floatval($data->challan_qty),floatval($pendingQty)];

}

function getPIRData($data){
    $editBtn = '';$deleteBtn='';
    $printBtn = '<a class="btn btn-warning btn-edit" href="'.base_url($data->controller.'/pir_pdf/'.$data->id).'" target="_blank" datatip="PIR Print" flow="down"><i class="fas fa-print" ></i></a>';
    if($data->order_status == 2){  
        $editBtn = '<a href="'.base_url($data->controller.'/edit/'.$data->id).'" class="btn btn-success btn-edit " datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
        $deleteParam = $data->id;
        $deleteBtn = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    }
	$approvePir = '';
	if(empty($data->verify_by)){
		$approvePir = '<a class="btn btn-info btn-approve approvePir permission-approve" href="javascript:void(0)" datatip="Approve" flow="down" data-msg="Approve" data-val="1" data-id="'.$data->id.'"><i class="ti-check" ></i></a>';
	}
   
    $action = getActionButton($approvePir.$printBtn.$editBtn.$deleteBtn);
    return [ $action,$data->sr_no,formatDate($data->trans_date),$data->trans_no,getPrefixNumber($data->job_prefix,$data->job_no),$data->item_code,$data->process_name,(!empty($data->machine_code)?'['.$data->machine_code.'] ':'').$data->machine_name,$data->operator_name,$data->emp_name,$data->remark];
}

function getPendingPIRData($data){
	$pirBtn = '<a class="btn btn-warning btn-edit" href="'.base_url($data->controller.'/addPirReport/'.$data->job_card_id.'/'.$data->out_process_id).'"  datatip="Add Report" flow="down"><i class=" fas fa-clipboard-list"></i></a>';
    
	$action = getActionButton($pirBtn);
	return [ $action,$data->sr_no,getPrefixNumber($data->job_prefix,$data->job_no),$data->item_code,$data->process_name];
}

function getRQCData($data){
    $editBtn = '';$deleteBtn='';
    $printBtn = '<a class="btn btn-warning btn-edit" href="'.base_url($data->controller.'/rqc_pdf/'.$data->id).'" target="_blank" datatip="RQC Print" flow="down"><i class="fas fa-print" ></i></a>';
    if($data->order_status == 2){  
        $editBtn = '<a href="'.base_url($data->controller.'/edit/'.$data->id).'" class="btn btn-success btn-edit " datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
        $deleteParam = $data->id;
        $deleteBtn = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    }
	
	$approveRqc = '';
	if(empty($data->verify_by)){
		$approveRqc = '<a class="btn btn-info btn-approve approveRqc permission-approve" href="javascript:void(0)" datatip="Approve" flow="down" data-msg="Approve" data-val="1" data-id="'.$data->id.'"><i class="ti-check" ></i></a>';
	}
	
	$tpDownload = "";
    if(!empty($data->third_party)):
        $tpDownload = '<a href="'.base_url('assets/uploads/rqc_third_party/'.$data->third_party).'" class="btn btn-info waves-effect waves-light"><i class="fa fa-arrow-down"></i></a>';
    endif;
	
    $action = getActionButton($approveRqc.$tpDownload.$printBtn.$editBtn.$deleteBtn);
    return [ $action,$data->sr_no,formatDate($data->trans_date),$data->trans_no,getPrefixNumber($data->job_prefix,$data->job_no),$data->item_code,$data->process_name,(!empty($data->party_name)?$data->party_name:''),$data->emp_name,$data->heat_no,$data->in_challan_no,(!empty($data->log_date))?formatDate($data->log_date):'',$data->production_qty,$data->remark];
}

function getPendingRQCData($data){
    $pirBtn = '<a class="btn btn-warning btn-edit" href="'.base_url($data->controller.'/addRqcReport/'.$data->job_card_id.'/'.$data->process_id.'/'.$data->vendor_id.'/'.$data->id).'"  datatip="Add Report" flow="down"><i class=" fas fa-clipboard-list"></i></a>';
    $action = getActionButton($pirBtn);
    return [ $action,$data->sr_no,formatDate($data->log_date),getPrefixNumber($data->job_prefix,$data->job_no),$data->item_code,$data->process_name,$data->party_name,$data->in_challan_no,$data->heat_no,$data->production_qty];
}

/* NPD Job Card Table Data */
function getNpdJobcardData($data){
    $editButton="";$deleteButton="";$startJob="";$completeJob="";$jobNo="";
    
    $deleteParam = $data->id.",'NPD Jobcard'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editJobcard', 'title' : 'Update Jobcard'}";

    if($data->order_status == 0):
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';        
    elseif($data->order_status == 1):
        $startJob = '<a class="btn btn-success btn-start changeNpdJobStatus permission-modify" href="javascript:void(0)" datatip="Start" flow="down" data-msg="Start" data-val="2" data-id="'.$data->id.'"><i class="ti-control-play" ></i></a>';
    elseif($data->order_status == 2):    
        $completeJobParam = "{'id' : " . $data->id . ", 'modal_id' : 'modal-lg', 'form_id' : 'storeLocation', 'title' : 'Complete Job Card','button' : 'close', 'fnEdit' : 'storeLocation'}";
        $completeJob = '<a class="btn btn-info btn-edit" href="javascript:void(0)" datatip="Complete" flow="down" onclick="edit(' . $completeJobParam . ');"><i class="fas fa-check"></i></a>';
    endif;

    if($data->order_status >= 2):
	    $jobNo = '<a href="'.base_url($data->controller."/view/".$data->id).'">'.(getPrefixNumber($data->job_prefix,$data->job_no)).'</a>';
    else:
        $jobNo = (getPrefixNumber($data->job_prefix,$data->job_no));   
	endif;

    $action = getActionButton($startJob.$completeJob.$editButton.$deleteButton);
    return [$action,$data->sr_no,$jobNo,date("d-m-Y",strtotime($data->job_date)),$data->item_code,floatVal($data->qty),$data->remark];
}

function getprocessCode($data){
    $action = "";
    
        $deleteParam = $data->id.",'Process Code'";
        $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'edit', 'title' : 'Update ProcessCode'}";

        $editButton = '<a class="btn btn-success permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

        $deleteButton = '<a class="btn btn-danger permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
        $action = getActionButton($editButton.$deleteButton);
   

    return [$action,$data->sr_no,$data->process_code,$data->description];
}

/** Production Stock Approval Data */ //26-09-2024
function getStockApprovalData($data){
    $approveParam = "{'id' : ".$data->id.",'msg':'Are you sure you want to Approve This Qty ?'}";
    $approveBtn = '<a class="btn btn-facebook btn-edit permission-approve" href="javascript:void(0)" datatip="Approve" flow="down" onclick="approveStock('.$approveParam.');"><i class="fas fa-check-circle" ></i></a>';
    $action = getActionButton($approveBtn);
    return [$action,$data->sr_no,formatDate($data->ref_date),$data->batch_no,$data->item_code,$data->location,$data->qty];
}



function getVendorChallanEntryData($data){
    $moveBtn =  $moveLogBtn = $acceptBtn = $printBtn = '';
    if($data->trans_status == 0){
        $acceptParam = "{'id' : " . $data->challan_id . " ,'trans_status':'1','msg':'Are you sure want to accept this challan'}";

        $acceptBtn = '<a class="btn btn-primary btn-edit" href="javascript:void(0)" datatip="Accept" flow="down" onclick="changeChallanStatus(' . $acceptParam . ');"><i class="fas fa-check"></i></a>';
    }else{
        if(empty($data->receive_qty) || $data->receive_qty == 0){
            $acceptParam = "{'id' : " . $data->challan_id . " ,'trans_status':'0','msg':'Are you sure want to Un accept this challan'}";

            $acceptBtn = '<a class="btn btn-warning btn-edit" href="javascript:void(0)" datatip="Un Accept" flow="down" onclick="changeChallanStatus(' . $acceptParam . ');"><i class="mdi mdi-close"></i></a>';
        }
        $printBtn = '<a class="btn btn-success btn-edit" href="'.base_url('production_v3/jobWorkVendor/jobworkOutChallan/'.$data->challan_id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';
        
        $outParam = "{'id' : " . $data->id . " ,'modal_id' : 'modal-lg', 'form_id' : 'outwardLog', 'title' : '".$data->trans_number." [".$data->item_code."]  Pend. Qty: ".floatVal($data->pending_qty)."','fnEdit':'outwardLog'}";

        $moveBtn = '<a class="btn btn-primary btn-edit" href="javascript:void(0)" datatip="Material Outward" flow="down" onclick="customEdit(' . $outParam . ');"><i class="fas fa-paper-plane"></i></a>';

        $outLogParam = "{'ch_trans_id' : " . $data->id . " ,'modal_id' : 'modal-lg', 'form_id' : 'outwardLog', 'title' : '".$data->trans_number." [".$data->item_code."]  Pend. Qty: ".floatVal($data->pending_qty)."','fnEdit':'getVendorOutLog','button':'close'}";

        $moveLogBtn = '<a class="btn btn-info btn-edit" href="javascript:void(0)" datatip="Log" flow="down" onclick="customEdit(' . $outLogParam . ');"><i class="fas fa-clipboard-list"></i></a>';
    }
	

    $action = getActionButton($acceptBtn.$moveBtn.$moveLogBtn.$printBtn);

    return [$action,$data->sr_no,formatDate($data->trans_date),$data->trans_number,date('d-m-Y',strtotime($data->job_date)),getPrefixNumber($data->job_prefix,$data->job_no),$data->item_code,$data->process_name,$data->status,floatVal($data->weight),floatVal($data->qty + $data->without_process_qty),floatVal($data->receive_qty),floatVal($data->pending_qty),floatVal($data->without_process_qty),$data->remark];
}

function getJobWorkVendorReceiveData($data){
	$printBtn = '<a class="btn btn-success btn-edit" href="'.base_url('production_v3/jobWorkVendor/jobworkOutChallan/'.$data->challan_id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';
    
    $approveParam = "{'ch_trans_id' : ".$data->ch_trans_id.",'in_challan_no' :'".$data->in_challan_no."','trans_no' :'".$data->trans_no."'}";
    $approveBtn = '<a class="btn btn-facebook btn-edit permission-approve" href="javascript:void(0)" datatip="Accept" flow="down" onclick="acceptChallan('.$approveParam.');"><i class="fas fa-check-circle" ></i></a>';
    $action = getActionButton($approveBtn.$printBtn);

    return [$action,$data->sr_no,formatDate($data->trans_date),$data->trans_number,date('d-m-Y',strtotime($data->job_date)),getPrefixNumber($data->job_prefix,$data->job_no),$data->party_name,$data->item_code,$data->in_challan_no,formatDate($data->in_challan_date),$data->process_name,$data->production_qtys,$data->without_prs_qtys];
}

function getProductCostingData($data){
    $rmCost = '';$mfgCost = '';$costBtn='';$approveBtn = '';$wcBtn = '';
    if($data->rm_cost_request == 0 && $data->approve_by == 0){
        $reqParam = "{'id' : ".$data->id.",'item_id' : ".$data->item_id.", 'modal_id' : 'modal-md', 'form_id' : 'rmCostRequest', 'title' : 'RM Costing Request', 'fnEdit':'rmCostRequest','fnsave':'saveRmCostRequest'}";
        $rmCost = '<a href="javascript:void(0)" class="btn btn-primary btn-delete permission-modify" onclick="edit('.$reqParam.');" datatip="RM Costing Request" flow="down"><i class="fas fa-paper-plane"></i></a>'; 
    }
    if($data->mfg_cost_request	 == 0 && !empty($data->grade_id)  && $data->approve_by == 0){
        $reqParam = "{'id' : ".$data->id.",'fnsave' : 'saveMfgCostRequest','message' : 'Are you sure you want to send request ?'}";
        $mfgCost = '<a href="javascript:void(0)" class="btn btn-info btn-delete permission-modify" onclick="sentRequest('.$reqParam.');" datatip="Mfg. Costing Request" flow="down"><i class="fas fa-paper-plane"></i></a>'; 
    }

    if($data->approve_by == 0){
        $costParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'addCostDetail', 'title' : 'Cost Detail', 'fnEdit':'addCostDetail','fnsave':'saveCostDetail'}";
        $costBtn = '<a href="javascript:void(0)" class="btn btn-success btn-delete permission-modify" onclick="edit('.$costParam.');" datatip="Costing" flow="down"><i class="fas fa-clipboard-list"></i></a>'; 

        if(!empty($data->final_cost)){
            $approveParam = "{'id' : ".$data->id.",'fnsave' : 'approveCost','message' : 'Are you sure you want to approve this cost ?'}";
            $approveBtn = '<a href="javascript:void(0)" class="btn btn-info btn-delete permission-approve" onclick="sentRequest('.$approveParam.');" datatip="Approve Cost" flow="down"><i class="fas fa-check"></i></a>'; 
        }
    }else{
        $costParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'reviseCostDetail', 'title' : 'Revise Cost Detail', 'fnEdit':'reviseCostDetail','fnsave':'saveCostDetail'}";
        $costBtn = '<a href="javascript:void(0)" class="btn btn-facebook btn-delete permission-modify" onclick="edit('.$costParam.');" datatip="Costing" flow="down"><i class="fa fa-retweet"></i></a>'; 
    }
    
    $wcParam = "{'id' : ".$data->id.",'item_id' : ".$data->item_id.", 'modal_id' : 'modal-md', 'form_id' : 'wcCalc', 'title' : 'Weight Calculator', 'fnEdit':'wcCalc','fnsave':'saveWeight'}";
    $wcBtn = '<a href="javascript:void(0)" class="btn btn-dark btn-delete permission-modify" onclick="edit('.$wcParam.');" datatip="Weight Calculator" flow="down"><i class="fas fa-balance-scale"></i></a>'; 
    
    $print = '<a href="'.base_url('costing/printCostDetail/'.$data->id).'" class="btn btn-instagram" target="_blank"><i class="fa fa-print"></i></a>';
    $rev_no = $data->rev_no;
    if(!empty($rev_no)){
        $revParam = "{'id' : ".$data->enq_id.", 'modal_id' : 'modal-lg', 'form_id' : 'reviseCostDetail', 'title' : 'Revision Detail', 'fnEdit':'getCostingRevList','button':'close'}";
        $rev_no = '<a href="javascript:void(0)" onclick="edit('.$revParam.');" datatip="Revision" flow="down">'.$data->rev_no.'</a>';
    }
    $action = getActionButton($wcBtn.$approveBtn.$rmCost.$mfgCost.$costBtn.$print);
    return [$action,$data->sr_no,$data->enq_number,$data->party_name,$data->item_code,$data->item_name, $rev_no];
}

function getPurchaseCostReq($data){
    $reqParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'addRmCost', 'title' : 'RM Costing', 'fnEdit':'addRmCost','fnsave':'saveRmCost'}";
    $rmCost = '<a href="javascript:void(0)" class="btn btn-primary btn-delete permission-modify" onclick="edit('.$reqParam.');" datatip="RM Costing" flow="down"><i class="fas fa-paper-plane"></i></a>'; 
    $action = getActionButton($rmCost);
    return [$action,$data->sr_no,$data->enq_number,$data->party_name,$data->item_code,$data->item_name,$data->material_grade,$data->dimension,$data->moq,$data->gross_wt,$data->finish_wt,($data->moq * $data->gross_wt)];
}

function getMfgCostReq($data){
    $reqParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'addMfgCost', 'title' : 'Mfg. Costing', 'fnEdit':'addMfgCost','button':'close'}";
    $rmCost = '<a href="javascript:void(0)" class="btn btn-primary btn-delete permission-modify" onclick="edit('.$reqParam.');" datatip="Mfg. Costing" flow="down"><i class="fas fa-paper-plane"></i></a>'; 
    $action = getActionButton($rmCost);
    return [$action,$data->sr_no,$data->enq_number,$data->party_name,$data->item_code,$data->item_name,$data->material_grade,$data->moq,$data->gross_wt,$data->finish_wt,($data->moq * $data->gross_wt)];
}
?>