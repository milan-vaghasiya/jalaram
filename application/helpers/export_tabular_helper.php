<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
/* get Pagewise Table Header */
function getExportDtHeader($page){
    /* Shipping Bill Header */
    $data['shippingBill'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['shippingBill'][] = ["name"=>"#","style"=>"width:5%;"]; 
    $data['shippingBill'][] = ["name"=>"Inv. No."];
    $data['shippingBill'][] = ["name"=>"Inv. Date"];
    $data['shippingBill'][] = ["name"=>"Buyer Name"];
    $data['shippingBill'][] = ["name"=>"Destination Country"];

    $data['completedShippingBill'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['completedShippingBill'][] = ["name"=>"#","style"=>"width:5%;"]; 
    $data['completedShippingBill'][] = ["name"=>"Inv. No."];
    $data['completedShippingBill'][] = ["name"=>"Inv. Date"];
    $data['completedShippingBill'][] = ["name"=>"Buyer Name"];
    $data['completedShippingBill'][] = ["name"=>"Invoice Currency"];
    $data['completedShippingBill'][] = ["name"=>"SB Amount (FC)"];
    $data['completedShippingBill'][] = ["name"=>"Port Code"];
    $data['completedShippingBill'][] = ["name"=>"SB No."];
    $data['completedShippingBill'][] = ["name"=>"SB Date"];

    /* Bill Of Loading Header */
    $data['ladingBill'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['ladingBill'][] = ["name"=>"#","style"=>"width:5%;"]; 
    $data['ladingBill'][] = ["name"=>"Inv. No."];
    $data['ladingBill'][] = ["name"=>"Inv. Date"];
    $data['ladingBill'][] = ["name"=>"Buyer Name"];
    $data['ladingBill'][] = ["name"=>"Destination Country"];
    $data['ladingBill'][] = ["name"=>"Port of Loading"];
    $data['ladingBill'][] = ["name"=>"Port of Discharge"];

    $data['completedLadingBill'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['completedLadingBill'][] = ["name"=>"#","style"=>"width:5%;"]; 
    $data['completedLadingBill'][] = ["name"=>"Inv. No."];
    $data['completedLadingBill'][] = ["name"=>"Inv. Date"];
    $data['completedLadingBill'][] = ["name"=>"Buyer Name"];
    $data['completedLadingBill'][] = ["name"=>"Destination Country"];
    $data['completedLadingBill'][] = ["name"=>"Port of Loading"];
    $data['completedLadingBill'][] = ["name"=>"Port of Discharge"];
    $data['completedLadingBill'][] = ["name"=>"BL/AWB No."];
    $data['completedLadingBill'][] = ["name"=>"BL/AWB Date"];

    /* Export Incentives Header */
    $data['exportIncentives'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['exportIncentives'][] = ["name"=>"#","style"=>"width:5%;"]; 
    $data['exportIncentives'][] = ["name"=>"Inv. No."];
    $data['exportIncentives'][] = ["name"=>"Inv. Date"];
    $data['exportIncentives'][] = ["name"=>"Drawback Amount"];
    $data['exportIncentives'][] = ["name"=>"Drawback Date"];
    $data['exportIncentives'][] = ["name"=>"IGST Amount"];
    $data['exportIncentives'][] = ["name"=>"IGST Refund Date"];
    $data['exportIncentives'][] = ["name"=>"IGST Refund Error"];
    $data['exportIncentives'][] = ["name"=>"RODTEP Amount"];
    $data['exportIncentives'][] = ["name"=>"RODTEP Date"];

    /* Swift Remittance Header */
    $data['swiftRemittance'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['swiftRemittance'][] = ["name"=>"#","style"=>"width:5%;"]; 
    $data['swiftRemittance'][] = ["name"=>"FIRC Number"];
    $data['swiftRemittance'][] = ["name"=>"Remittance Date"];
    $data['swiftRemittance'][] = ["name"=>"Remitter Name"];
    $data['swiftRemittance'][] = ["name"=>"Remitter Country"];
    $data['swiftRemittance'][] = ["name"=>"SWIFT Currency"];
    $data['swiftRemittance'][] = ["name"=>"SWIFT Amount"];
    $data['swiftRemittance'][] = ["name"=>"FIRC Amount"];
    $data['swiftRemittance'][] = ["name"=>"SWIFT Remark"];

    /* Remittance Transfer */
    $data['remittanceTransfer'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['remittanceTransfer'][] = ["name"=>"#","style"=>"width:5%;"]; 
    $data['remittanceTransfer'][] = ["name"=>"FIRC Number"];
    $data['remittanceTransfer'][] = ["name"=>"Remittance Date"];
    $data['remittanceTransfer'][] = ["name"=>"Remitter Name"];
    $data['remittanceTransfer'][] = ["name"=>"Remitter Country"];
    $data['remittanceTransfer'][] = ["name"=>"SWIFT Currency"];
    $data['remittanceTransfer'][] = ["name"=>"SWIFT Amount"];
    $data['remittanceTransfer'][] = ["name"=>"FIRC Amount"];
    $data['remittanceTransfer'][] = ["name"=>"SWIFT Remark"];

    $data['remittanceCredited'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['remittanceCredited'][] = ["name"=>"#","style"=>"width:5%;"]; 
    $data['remittanceCredited'][] = ["name"=>"FIRC Number"];
    $data['remittanceCredited'][] = ["name"=>"Remittance Date"];
    $data['remittanceCredited'][] = ["name"=>"Remitter Name"];
    $data['remittanceCredited'][] = ["name"=>"SWIFT Currency"];
    $data['remittanceCredited'][] = ["name"=>"FIRC Amount"];
    $data['remittanceCredited'][] = ["name"=>"Trans. Ref. No."];
    $data['remittanceCredited'][] = ["name"=>"Trans. Date"];
    $data['remittanceCredited'][] = ["name"=>"FIRC Trans."];
    $data['remittanceCredited'][] = ["name"=>"Net Credit INR"];

    /* Invoice Settlement Header */
    $data['invoiceUnsetlled'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['invoiceUnsetlled'][] = ["name"=>"#","style"=>"width:5%;"]; 
    $data['invoiceUnsetlled'][] = ["name"=>"Inv No."];
    $data['invoiceUnsetlled'][] = ["name"=>"Inv. Date"];
    $data['invoiceUnsetlled'][] = ["name"=>"Buyer Name"];
    $data['invoiceUnsetlled'][] = ["name"=>"Invoice Currency"];
    $data['invoiceUnsetlled'][] = ["name"=>"Invoice Amount"];
    $data['invoiceUnsetlled'][] = ["name"=>"Inco Terms"];
    $data['invoiceUnsetlled'][] = ["name"=>"BL/AWB Date"];
    $data['invoiceUnsetlled'][] = ["name"=>"Payment Due Date"];

    $data['swiftUnsetlled'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['swiftUnsetlled'][] = ["name"=>"#","style"=>"width:5%;"]; 
    $data['swiftUnsetlled'][] = ["name"=>"FIRC Number"];
    $data['swiftUnsetlled'][] = ["name"=>"Remittance Date"];
    $data['swiftUnsetlled'][] = ["name"=>"Remitter Name"];
    $data['swiftUnsetlled'][] = ["name"=>"SWIFT Currency"];
    $data['swiftUnsetlled'][] = ["name"=>"SWIFT Amount"];
    //$data['swiftUnsetlled'][] = ["name"=>"FIRC Amount"];
    $data['swiftUnsetlled'][] = ["name"=>"SWIFT Remark"];
    $data['swiftUnsetlled'][] = ["name"=>"Settled FC"];
    $data['swiftUnsetlled'][] = ["name"=>"Balance FC"];

    $data['invoiceSettled'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['invoiceSettled'][] = ["name"=>"#","style"=>"width:5%;"]; 
    $data['invoiceSettled'][] = ["name"=>"Inv No."];
    $data['invoiceSettled'][] = ["name"=>"Inv. Date"];
    $data['invoiceSettled'][] = ["name"=>"Buyer Name"];
    $data['invoiceSettled'][] = ["name"=>"Invoice Currency"];
    $data['invoiceSettled'][] = ["name"=>"Invoice Amount"];

    /* GR Closure Header */
    $data['grClosure'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['grClosure'][] = ["name"=>"#","style"=>"width:5%;"]; 
    $data['grClosure'][] = ["name"=>"SB No."];
    $data['grClosure'][] = ["name"=>"SB Date"];
    $data['grClosure'][] = ["name"=>"Port Code"];
    $data['grClosure'][] = ["name"=>"Invoice Currency"];
    $data['grClosure'][] = ["name"=>"SB Amount (FC)"];
    $data['grClosure'][] = ["name"=>"Inv. No."];
    $data['grClosure'][] = ["name"=>"INV. Date"];
    $data['grClosure'][] = ["name"=>"Buyer Name"];

    $data['unmappedFIRC'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['unmappedFIRC'][] = ["name"=>"#","style"=>"width:5%;"]; 
    $data['unmappedFIRC'][] = ["name"=>"FIRC Number"];
    $data['unmappedFIRC'][] = ["name"=>"SWIFT Currency"];
    $data['unmappedFIRC'][] = ["name"=>"FIRC Amount"];
    $data['unmappedFIRC'][] = ["name"=>"Remittance Date"];
    $data['unmappedFIRC'][] = ["name"=>"Remitter Name"];
    $data['unmappedFIRC'][] = ["name"=>"SWIFT Remark"];
    $data['unmappedFIRC'][] = ["name"=>"Mapped FIRC"];
    $data['unmappedFIRC'][] = ["name"=>"Balance FIRC"];

    /* BRC Detail Header */
    $data['brcDetail'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['brcDetail'][] = ["name"=>"#","style"=>"width:5%;"]; 
    $data['brcDetail'][] = ["name"=>"SB No."];
    $data['brcDetail'][] = ["name"=>"SB Date"];
    $data['brcDetail'][] = ["name"=>"Port Code"];
    $data['brcDetail'][] = ["name"=>"Inv Currency"];
    $data['brcDetail'][] = ["name"=>"SB Amount (FC)"];
    $data['brcDetail'][] = ["name"=>"Inv. No."];
    $data['brcDetail'][] = ["name"=>"FIRC Number"];
    $data['brcDetail'][] = ["name"=>"SWIFT Currency"];
    $data['brcDetail'][] = ["name"=>"Mapped FIRC"];
    $data['brcDetail'][] = ["name"=>"BRC Number"];
    $data['brcDetail'][] = ["name"=>"BRC Date"];
    $data['brcDetail'][] = ["name"=>"Request Ref. No."];
    $data['brcDetail'][] = ["name"=>"Bank Bill ID"];

    /* Tax Invoice Adjustment Header */
    $data['invUnadjusted'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['invUnadjusted'][] = ["name"=>"#","style"=>"width:5%;"]; 
    $data['invUnadjusted'][] = ["name"=>"Inv. No."];
    $data['invUnadjusted'][] = ["name"=>"Inv. Date"];
    $data['invUnadjusted'][] = ["name"=>"Buyer Name"];
    $data['invUnadjusted'][] = ["name"=>"Inv Currency"];
    $data['invUnadjusted'][] = ["name"=>"SB Amount (FC)"];
    $data['invUnadjusted'][] = ["name"=>"Tax Invoice Total"];

    $data['inrCreditUnadjusted'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['inrCreditUnadjusted'][] = ["name"=>"#","style"=>"width:5%;"]; 
    $data['inrCreditUnadjusted'][] = ["name"=>"FIRC No."];
    $data['inrCreditUnadjusted'][] = ["name"=>"Remittance Date"];
    $data['inrCreditUnadjusted'][] = ["name"=>"Remitter Name"];
    $data['inrCreditUnadjusted'][] = ["name"=>"Swift Currency"];
    $data['inrCreditUnadjusted'][] = ["name"=>"Transfer Ref. No."];
    $data['inrCreditUnadjusted'][] = ["name"=>"Transfer Date"];
    $data['inrCreditUnadjusted'][] = ["name"=>"FIRC Transfer Bal."];
    $data['inrCreditUnadjusted'][] = ["name"=>"INR Credit Bal."];

    $data['invAdjusted'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['invAdjusted'][] = ["name"=>"#","style"=>"width:5%;"]; 
    $data['invAdjusted'][] = ["name"=>"Inv. No."];
    $data['invAdjusted'][] = ["name"=>"Inv. Date"];
    $data['invAdjusted'][] = ["name"=>"Buyer Name"];
    $data['invAdjusted'][] = ["name"=>"Tax Invoice Total"];
    $data['invAdjusted'][] = ["name"=>"INR Credit Adj."];
    $data['invAdjusted'][] = ["name"=>"Ex. Gain/Loss INR"];

    return tableHeader($data[$page]);
}

/* Shipping Bill Table Data */
function getShippingBillData($data){
    if($data->tab_status == 0):
        $shippingBillParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'addShippingBill', 'fnEdit' : 'addShippingBill', 'title' : 'Add Shipping Bill'}";
        $shippingBill = '<a class="btn btn-primary btn-write" href="javascript:void(0)" datatip="Add Shipping Bill" flow="down" onclick="edit('.$shippingBillParam.');"><i class="fa fa-plus" ></i></a>';
        
        $action = getActionButton($shippingBill);
        return [$action,$data->sr_no,$data->doc_no,formatDate($data->doc_date),$data->party_name,$data->country_of_final_destonation];
    else:
        $editButton = $deleteButton = "";
        if(empty($data->sb_status)):
            $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'editShippingBill', 'title' : 'Update Shipping Bill'}";
            $editButton = '<a class="btn btn-success btn-edit" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

            $deleteParam = $data->id.",'Shipping Bill'";
            $deleteButton = '<a class="btn btn-danger btn-delete" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
        endif;

        $action = getActionButton($editButton.$deleteButton);
        return [$action,$data->sr_no,$data->doc_no,formatDate($data->doc_date),$data->party_name,$data->currency,$data->sb_amount,$data->port_code,$data->sb_number,formatDate($data->sb_date)];
    endif;
}

/* Bill of Loading Table Data */
function getLadingBillData($data){
    if($data->tab_status == 0):
        $blParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'addLadingBill', 'fnEdit' : 'addLadingBill', 'title' : 'Add Bill of Lading'}";
        $blBtn = '<a class="btn btn-primary btn-write" href="javascript:void(0)" datatip="Add Bill of Lading" flow="down" onclick="edit('.$blParam.');"><i class="fa fa-plus" ></i></a>';
        
        $action = getActionButton($blBtn);
        return [$action,$data->sr_no,$data->doc_no,formatDate($data->doc_date),$data->party_name,$data->country_of_final_destonation,$data->port_of_loading,$data->port_of_discharge];
    else:
        $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'editLadingBill', 'title' : 'Update Bill of Lading'}";
        $editButton = '<a class="btn btn-success btn-edit" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

        $deleteParam = $data->id.",'Bill of Lading'";
        $deleteButton = '<a class="btn btn-danger btn-delete" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

        if(empty(floatval($data->settled_fc))): $editButton = $deleteButton = ''; endif;
        if(!empty(floatval($data->total_mapped_firc))): $editButton = $deleteButton = ''; endif;
        if(!empty(floatval($data->net_credit_inr_adj))): $editButton = $deleteButton = ''; endif;

        $action = getActionButton($editButton.$deleteButton);
        return [$action,$data->sr_no,$data->doc_no,formatDate($data->doc_date),$data->party_name,$data->country_of_final_destonation,$data->port_of_loading,$data->port_of_discharge,$data->bl_awb_no,formatDate($data->bl_awb_date)];
    endif;
}

/* Export Incentives Table Data */
function getExportIncentivesData($data){
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editExportIncentives', 'fnEdit' : 'editExportIncentives', 'title' : 'Export Incentives'}";
    $editButton = '<a class="btn btn-primary btn-write" href="javascript:void(0)" datatip="Export Incentives" flow="down" onclick="edit('.$editParam.');"><i class="fa fa-plus" ></i></a>';
    
    $action = getActionButton($editButton);
    return [$action,$data->sr_no,$data->doc_no,formatDate($data->doc_date),$data->drawback_amount,formatDate($data->drawback_date),$data->igst_amount,formatDate($data->igst_refund_date),$data->igst_refund_error,$data->rodtep_amount,formatDate($data->rodtep_date)];
}

/* Swift Remittance Table Data */
function getSwiftRemittanceData($data){
    $editButton = $deleteButton = "";
    if(empty(floatval($data->transfer_amount))):
        $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editSwiftRemittance', 'title' : 'Update Swift Remittance'}";
        $editButton = '<a class="btn btn-success btn-edit" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

        $deleteParam = $data->id.",'Swift Remittance'";
        $deleteButton = '<a class="btn btn-danger btn-delete" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    endif;

    if(!empty(floatval($data->settled_amount))): $editButton = $deleteButton = ""; endif;
    if(!empty(floatval($data->mapped_firc_amount))): $editButton = $deleteButton = ""; endif;

    $action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->firc_number,formatDate($data->remittance_date),$data->remitter_name,$data->remitter_country,$data->swift_currency,$data->swift_amount,$data->firc_amount,$data->swift_remark];
}

/* Remittance Transfer Table Data */
function getRemittanceTransferData($data){
    if($data->tab_status == 0):
        $transferParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'remittanceTransfer', 'fnEdit' : 'remittanceTransfer', 'title' : 'Remittance Transfer'}";
        $transferBtn = '<a class="btn btn-primary btn-write" href="javascript:void(0)" datatip="Remittance Transfer" flow="down" onclick="edit('.$transferParam.');"><i class="fa fa-plus" ></i></a>';
        
        $action = getActionButton($transferBtn);

        return [$action,$data->sr_no,$data->firc_number,formatDate($data->remittance_date),$data->remitter_name,$data->remitter_country,$data->swift_currency,$data->swift_amount,$data->firc_amount,$data->swift_remark];
    else:
        $editButton = $deleteButton = "";

        if(empty(floatval($data->settled_amount))):
            $editParam = "{'id' : ".$data->swift_id.", 'modal_id' : 'modal-xl', 'form_id' : 'remittanceTransfer', 'fnEdit' : 'remittanceTransfer', 'title' : 'Update Remittance Transfer'}";
            $editButton = '<a class="btn btn-success btn-edit" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

            $deleteParam = $data->swift_id.",'Remittance Transfer'";
            $deleteButton = '<a class="btn btn-danger btn-delete" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
        endif;

        if(!empty(floatval($data->net_credit_inr_adj))): $editButton = $deleteButton = ""; endif;

        $action = getActionButton($editButton.$deleteButton);
        return [$action,$data->sr_no,$data->firc_number,formatDate($data->remittance_date),$data->remitter_name,$data->swift_currency,$data->firc_amount,$data->trans_ref_no,formatDate($data->trans_date),$data->firc_transfer,$data->net_credit_inr];
    endif;
}

/* Invoice Settlement Table Data */
function getInvoiceSettlementData($data){
    if($data->tab_status == 0):
        $settlementParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'invoiceSettlement', 'fnEdit' : 'invoiceSettlement', 'title' : 'Invoice Settlement'}";
        $settlementBtn = '<a class="btn btn-primary btn-write" href="javascript:void(0)" datatip="Invoice Settlement" flow="down" onclick="edit('.$settlementParam.');"><i class="fa fa-plus" ></i></a>';
        
        $action = getActionButton($settlementBtn);
        return [$action,$data->sr_no,$data->doc_no,formatDate($data->doc_date),$data->party_name,$data->currency,$data->net_amount,$data->inco_terms,formatDate($data->bl_awb_date),formatDate($data->payment_due_date)];
    elseif($data->tab_status == 1):
        return ["",$data->sr_no,$data->firc_number,formatDate($data->remittance_date),$data->remitter_name,$data->swift_currency,$data->swift_amount,/* $data->firc_amount, */$data->swift_remark,$data->settled_amount,$data->balance_amount];
    else:
        $deleteParam = $data->id.",'Invoice Settlement'";
        $deleteButton = '<a class="btn btn-danger btn-delete" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove Settlement" flow="down"><i class="ti-close"></i></a>';

        $viewParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'grClosure', 'fnEdit' : 'viewTransaction', 'title' : 'Invoice Settlement'}";
        $viewBtn = '<a class="btn btn-primary btn-write" href="javascript:void(0)" datatip="View Settlement" flow="down" onclick="edit('.$viewParam.');"><i class="fa fa-eye" ></i></a>';

        $action = getActionButton($viewBtn.$deleteButton);
        return [ $action,$data->sr_no,$data->doc_no,formatDate($data->doc_date),$data->party_name,$data->currency,$data->net_amount];
    endif;
}

/* GR Closure Table Data */
function getGrClosureData($data){
    if($data->tab_status == 1):
        return ["",$data->sr_no,$data->firc_number,$data->swift_currency,$data->firc_amount,formatDate($data->remittance_date),$data->remitter_name,$data->swift_remark,$data->mapped_firc_amount,$data->balance_amount];
    else:
        $grClosureParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'grClosure', 'fnEdit' : 'grClosure', 'title' : 'GR Closure'}";
        $grClosureBtn = '<a class="btn btn-primary btn-write" href="javascript:void(0)" datatip="GR Closure" flow="down" onclick="edit('.$grClosureParam.');"><i class="fa fa-plus" ></i></a>';
        
        $viewParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'grClosure', 'fnEdit' : 'viewTransaction', 'title' : 'GR Closure'}";
        $viewBtn = '<a class="btn btn-primary btn-write" href="javascript:void(0)" datatip="View GR Closure" flow="down" onclick="edit('.$viewParam.');"><i class="fa fa-eye" ></i></a>';

        $deleteParam = $data->id.",'GR Closure'";
        $deleteButton = '<a class="btn btn-danger btn-delete" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove GR Closure" flow="down"><i class="ti-close"></i></a>';

        if($data->tab_status == 0): $deleteButton = $viewBtn = ""; else: $grClosureBtn = ""; endif;

        $action = getActionButton($grClosureBtn.$viewBtn.$deleteButton);
        return [ $action,$data->sr_no,$data->sb_number,formatDate($data->sb_date),$data->port_code,$data->currency,$data->sb_amount,$data->doc_no,formatDate($data->doc_date),$data->party_name];
    endif;
}

/* BRC Detail Table Data */
function getBRCDetailData($data){
    $brcParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'brcDetail', 'fnEdit' : 'brcDetail', 'title' : 'BRC Detail'}";
    $brcBtn = '<a class="btn btn-primary btn-write" href="javascript:void(0)" datatip="Update BRC Detail" flow="down" onclick="edit('.$brcParam.');"><i class="fa fa-plus" ></i></a>';

    $action = getActionButton($brcBtn);
    return [$action,$data->sr_no,$data->sb_number,formatDate($data->sb_date),$data->port_code,$data->currency,$data->sb_amount,$data->doc_no,$data->firc_number,$data->swift_currency,$data->mapped_firc_amount,$data->brc_number,((!empty($data->brc_date))?formatDate($data->brc_date):""),$data->req_ref_no,$data->bank_bill_id];
}

/* Tax Invoice Adjustment Table Data */
function getTaxInvoiceAdjustmentData($data){
    
    if($data->tab_status == 0):
        $taxInvTotalParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'taxInvTotal', 'fnEdit' : 'taxInvoiceTotal', 'fnsave' : 'saveTaxInvTotal', 'title' : 'Update Tax Invoice Total'}";
        $taxInvTotalBtn = '<a class="btn btn-success btn-write" href="javascript:void(0)" datatip="Update Tax Invoice Total" flow="down" onclick="edit('.$taxInvTotalParam.');"><i class="ti-pencil-alt"></i></a>';

        $adjustmentBtn = "";
        if(!empty(floatval($data->tax_invoice_total))):
            $adjustmentParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'taxInvoiceAdjustment', 'fnEdit' : 'taxInvoiceAdjustment', 'title' : 'Tax Invoice Adjustment'}";
            $adjustmentBtn = '<a class="btn btn-primary btn-write" href="javascript:void(0)" datatip="Tax Invoice Adjustment" flow="down" onclick="edit('.$adjustmentParam.');"><i class="fa fa-plus" ></i></a>';
        endif;

        $action = getActionButton($taxInvTotalBtn.$adjustmentBtn);
        return [$action,$data->sr_no,$data->doc_no,formatDate($data->doc_date),$data->party_name,$data->currency,$data->sb_amount,$data->tax_invoice_total];
    elseif($data->tab_status == 1):
        

        $action = getActionButton("");
        return [$action,$data->sr_no,$data->firc_number,formatDate($data->remittance_date),$data->remitter_name,$data->swift_currency,$data->trans_ref_no,formatDate($data->trans_date),$data->firc_transfer_bal,$data->net_credit_inr_bal];
    else:
        $deleteParam = $data->bl_id.",'Invoice Adjustment'";
        $deleteButton = '<a class="btn btn-danger btn-delete" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove Adjustment" flow="down"><i class="ti-close"></i></a>';

        $viewParam = "{'id' : ".$data->bl_id.", 'modal_id' : 'modal-xl', 'form_id' : 'invoiceAdjustment', 'fnEdit' : 'viewTransaction', 'title' : 'Tax Invoice Adjustment'}";
        $viewBtn = '<a class="btn btn-primary btn-write" href="javascript:void(0)" datatip="View Adjustment" flow="down" onclick="edit('.$viewParam.');"><i class="fa fa-eye" ></i></a>';

        $action = getActionButton($viewBtn.$deleteButton);
        return [$action,$data->sr_no,$data->doc_no,formatDate($data->doc_date),$data->party_name,$data->tax_invoice_total,$data->net_credit_inr_adj,$data->ex_gain_loss_inr];
    endif;
}
?>