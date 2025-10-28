<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/* get Pagewise Table Header */
function getQualityDtHeader($page)
{	   
	//avruti 14-9-21
	/* Purchase Material Inspection Header */
    $data['materialInspection'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['materialInspection'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['materialInspection'][] = ["name"=>"Inv No."];
    $data['materialInspection'][] = ["name"=>"Inv Date"];
	$data['materialInspection'][] = ["name"=>"Challan No."];
    $data['materialInspection'][] = ["name"=>"Order No."];
    $data['materialInspection'][] = ["name"=>"Supplier/Customer"];
    $data['materialInspection'][] = ["name"=>"Item Name"];
    $data['materialInspection'][] = ["name"=>"Finish Goods"];
    $data['materialInspection'][] = ["name"=>"Received Qty"];
    $data['materialInspection'][] = ["name"=>"Batch/Heat No."];
    $data['materialInspection'][] = ["name"=>"Color Code"];
    $data['materialInspection'][] = ["name"=>"Status"];
    $data['materialInspection'][] = ["name"=>"Approval"];
    
    /* Final Inspection Header */
    $data['finalInspection'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['finalInspection'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['finalInspection'][] = ["name"=>"Rejection Type"];
    $data['finalInspection'][] = ["name"=>"Item Name"];
    $data['finalInspection'][] = ["name"=>"Qty."];
    $data['finalInspection'][] = ["name"=>"Pending Qty."];

    /* Job Work Inpection Header */
    $data['jobWorkInspection'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['jobWorkInspection'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
    $data['jobWorkInspection'][] = ["name"=>"Date"];
    $data['jobWorkInspection'][] = ["name"=>"Challan No."];
    $data['jobWorkInspection'][] = ["name"=>"Job No."];
    $data['jobWorkInspection'][] = ["name"=>"Vendor"];
    $data['jobWorkInspection'][] = ["name"=>"Part Code"];
    $data['jobWorkInspection'][] = ["name"=>"Charge No."];
    $data['jobWorkInspection'][] = ["name"=>"Process"];
    $data['jobWorkInspection'][] = ["name"=>"OK Qty."];
    $data['jobWorkInspection'][] = ["name"=>"UD Qty."];

	/* RM Inspection Data */
	$data['inspectionParam'][] = ["name"=>"#","style"=>"width:5%;","srnoPosition"=>0];
	$data['inspectionParam'][] = ["name"=>"Part Name"];
	$data['inspectionParam'][] = ["name"=>"Action","style"=>"width:10%;","textAlign"=>"center"];
      
	/* Rejection Header */
    $data['rejectionComments'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['rejectionComments'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"]; 
    $data['rejectionComments'][] = ["name"=>"Rejection/Rework Comment"];
    $data['rejectionComments'][] = ["name"=>"Type"];
	
    /* Gauge Header */
    $data['gauges'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['gauges'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['gauges'][] = ["name"=>"Gauge Size"];
    $data['gauges'][] = ["name"=>"Inst. Code No."];
    $data['gauges'][] = ["name"=>"Make"];
    $data['gauges'][] = ["name"=>"Thread Type"];
    $data['gauges'][] = ["name"=>"Required"];
    $data['gauges'][] = ["name"=>"Frequency <small>(In months)</small>"];
    $data['gauges'][] = ["name"=>"Location"];
    $data['gauges'][] = ["name"=>"Inhouse/Outside"];
	$data['gauges'][] = ["name"=>"Cal Date","style"=>"width:80px;"];
	$data['gauges'][] = ["name"=>"Due Date","style"=>"width:80px;"];
	$data['gauges'][] = ["name"=>"Plan Date","style"=>"width:80px;"];
	$data['gauges'][] = ["name"=>"Remark"];

	/* Pre Dispatch Inspect Header */
	$data['preDispatchInspect'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['preDispatchInspect'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
	$data['preDispatchInspect'][] = ["name"=>"Part Code"];
	$data['preDispatchInspect'][] = ["name"=>"Param. Count"];
    
	/* Instrument Header */
	$data['instrument'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['instrument'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['instrument'][] = ["name"=>"Description of Instrument","style"=>"width:200px !important;"];
	$data['instrument'][] = ["name"=>"Inst. Code No."];
	$data['instrument'][] = ["name"=>"Make"];
	$data['instrument'][] = ["name"=>"Range (mm)"];
	$data['instrument'][] = ["name"=>"Least Count"];
	$data['instrument'][] = ["name"=>"Permissible Error"];
	$data['instrument'][] = ["name"=>"Required"];
	$data['instrument'][] = ["name"=>"Frequency <small>(In months)</small>"];
	$data['instrument'][] = ["name"=>"Inhouse/Outside"];
	$data['instrument'][] = ["name"=>"Cal Date","style"=>"width:20%;"];
	$data['instrument'][] = ["name"=>"Due Date","style"=>"width:20%;"];
	$data['instrument'][] = ["name"=>"Plan Date","style"=>"width:20%;"];
	$data['instrument'][] = ["name"=>"Remark"];
   
    /* In Challan Header */
    $data['inChallan'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['inChallan'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['inChallan'][] = ["name"=>"Challan No."];
    $data['inChallan'][] = ["name"=>"Challan Date"];
    $data['inChallan'][] = ["name"=>"Party Name"];
    $data['inChallan'][] = ["name"=>"Item Name"];
    $data['inChallan'][] = ["name"=>"Qty."];
    $data['inChallan'][] = ["name"=>"Remark"];

    /* Out Challan Header */
    $data['outChallan'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['outChallan'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['outChallan'][] = ["name"=>"Challan No."];
    $data['outChallan'][] = ["name"=>"Challan Date"];
    $data['outChallan'][] = ["name"=>"Party Name"];
    $data['outChallan'][] = ["name"=>"Collected By"];
    $data['outChallan'][] = ["name"=>"Machine Name"];
    $data['outChallan'][] = ["name"=>"Item Name"];
    $data['outChallan'][] = ["name"=>"Qty."];
    $data['outChallan'][] = ["name"=>"Remark"];

    /* Assign Inspector Header */
    $data['assignInspector'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['assignInspector'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['assignInspector'][] = ["name"=>"Req. Date"];
    $data['assignInspector'][] = ["name"=>"Job Card No."];
    $data['assignInspector'][] = ["name"=>"Product Name"];
    $data['assignInspector'][] = ["name"=>"Process Name"];    
    $data['assignInspector'][] = ["name"=>"Machine No."];
    $data['assignInspector'][] = ["name"=>"Setter Name"];
    $data['assignInspector'][] = ["name"=>"Inspector Name"];
    $data['assignInspector'][] = ["name"=>"Status"];
    $data['assignInspector'][] = ["name"=>"Note"];

    /* Setup Inspection Header */
    $data['setupInspection'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['setupInspection'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
    $data['setupInspection'][] = ["name"=>"Req. Date"];
    $data['setupInspection'][] = ["name"=>"Status"];
    $data['setupInspection'][] = ["name"=>"Setup Type"];
    $data['setupInspection'][] = ["name"=>"Setter Name"];
    $data['setupInspection'][] = ["name"=>"Setter Note"];
    $data['setupInspection'][] = ["name"=>"Job No"];
    $data['setupInspection'][] = ["name"=>"Part Name"];
    $data['setupInspection'][] = ["name"=>"Process Name"];
    $data['setupInspection'][] = ["name"=>"Machine"];
    $data['setupInspection'][] = ["name"=>"Inspector Name"];
    $data['setupInspection'][] = ["name"=>"Start Date"];
    $data['setupInspection'][] = ["name"=>"End Date"];
    $data['setupInspection'][] = ["name"=>"Duration"];
    $data['setupInspection'][] = ["name"=>"Remark"];
    $data['setupInspection'][] = ["name"=>"Attachment","textAlign"=>"center"];

     /* Inspection Type Header */
    $data['inspectionType'][] = ["name" => "Action", "style" => "width:5%;"];
    $data['inspectionType'][] = ["name" => "#", "style" => "width:5%;"];
    $data['inspectionType'][] = ["name" => "Inspection Type"];

	/* Control Plan Data */
	$data['controlPlanV2'][] = ["name"=>"#","style"=>"width:5%;","srnoPosition"=>0];
	$data['controlPlanV2'][] = ["name"=>"Part Code"];
	$data['controlPlanV2'][] = ["name"=>"Part Name"];
	$data['controlPlanV2'][] = ["name"=>"PFC","textAlign"=>"center"];
	$data['controlPlanV2'][] = ["name"=>"Action","style"=>"width:10%;","textAlign"=>"center"];
	
	/* Reaction Plan Data */
    $data['reactionPlan'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['reactionPlan'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['reactionPlan'][] = ["name"=>"Process Code"];
	$data['reactionPlan'][] = ["name"=>"Action","textAlign"=>"center"];

    /* Sampling Plan Data */
    $data['samplingPlan'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['samplingPlan'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['samplingPlan'][] = ["name"=>"Title"];
    $data['samplingPlan'][] = ["name"=>"Control Method","textAlign"=>"center"];
   
    /* PFC Header */
    $data['pfc'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['pfc'][] = ["name" => "#", "style" => "width:4%;", "textAlign" => "center" ];
    $data['pfc'][] = ["name" => "PFC Number", "textAlign" => "center"];
    $data['pfc'][] = ["name" => "Item Name", "textAlign" => "center"];
    $data['pfc'][] = ["name" => "Revision No.", "textAlign" => "center"];
    $data['pfc'][] = ["name" => "Date", "textAlign" => "center"];
    
    /* Fmea Header */
    $data['fmea'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['fmea'][] = ["name" => "#", "style" => "width:4%;", "textAlign" => "center"];
    $data['fmea'][] = ["name" => "FMEA Number", "textAlign" => "center"];
    $data['fmea'][] = ["name" => "PFC Operation", "textAlign" => "center"];
    $data['fmea'][] = ["name" => "Revision No.", "textAlign" => "center"];
    $data['fmea'][] = ["name" => "Date", "textAlign" => "center"];
    $data['fmea'][] = ["name" => "Cust. Rev. No.", "textAlign" => "center"];

    /* FMEA Diamention Header */
    $data['fmeaDiamention'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['fmeaDiamention'][] = ["name" => "#", "style" => "width:4%;", "textAlign" => "center"];
    $data['fmeaDiamention'][] = ["name" => "Parameter", "textAlign" => "center"];
    $data['fmeaDiamention'][] = ["name" => "Dimension", "textAlign" => "center"];
    $data['fmeaDiamention'][] = ["name" => "Class", "textAlign" => "center"];

    /* PFC Operation Plan Data */
	$data['pfcOperation'][] = ["name"=>"#","style"=>"width:5%;","srnoPosition"=>0];
	$data['pfcOperation'][] = ["name"=>"Process No."];
	$data['pfcOperation'][] = ["name"=>"Parameter"];
	$data['pfcOperation'][] = ["name"=>"Rev No."];
	$data['pfcOperation'][] = ["name"=>"Rev Date"];
	$data['pfcOperation'][] = ["name"=>"Action","style"=>"width:10%;","textAlign"=>"center"];

    /* FMEA Failure Mode Header */
    $data['fmeaFail'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['fmeaFail'][] = ["name" => "#", "style" => "width:4%;", "textAlign" => "center" ];
    $data['fmeaFail'][] = ["name" => "Failure Mode	", "textAlign" => "center"];
    $data['fmeaFail'][] = ["name" => "Customer", "textAlign" => "center"];
    $data['fmeaFail'][] = ["name" => "Manufacturer", "textAlign" => "center"];
    $data['fmeaFail'][] = ["name" => "Customer Sev", "textAlign" => "center"];
    $data['fmeaFail'][] = ["name" => "Manufacturer Sev", "textAlign" => "center"];
    $data['fmeaFail'][] = ["name" => "Sev", "textAlign" => "center"];
    $data['fmeaFail'][] = ["name" => "Detection", "textAlign" => "center"];
    $data['fmeaFail'][] = ["name" => "Detec", "textAlign" => "center"];
    
    /* CP Operation Header */
    $data['cp'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['cp'][] = ["name" => "#", "style" => "width:4%;", "textAlign" => "center" ];
    $data['cp'][] = ["name" => "CP Number", "textAlign" => "left"];
    $data['cp'][] = ["name" => "Operation", "textAlign" => "left"];
    $data['cp'][] = ["name" => "Revision No.", "textAlign" => "center"];
    $data['cp'][] = ["name" => "Date", "textAlign" => "center"];
    $data['cp'][] = ["name" => "Cust. Rev. No.", "textAlign" => "center"];

  
    /* Control Method Header */
    $data['controlMethod'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['controlMethod'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['controlMethod'][] = ["name"=>"Control Method"];
    $data['controlMethod'][] = ["name"=>"Alias"];
   
    /* CP Diamention Header */
    $data['cpDiamention'][] = ["name" => "#", "style" => "width:4%;", "textAlign" => "center" , "srnoPosition" => 0];
    $data['cpDiamention'][] = ["name" => "Revision No.", "textAlign" => "center"];
    $data['cpDiamention'][] = ["name" => "Parameter", "textAlign" => "center"];
    $data['cpDiamention'][] = ["name" => "Diamention", "textAlign" => "center"];
    $data['cpDiamention'][] = ["name" => "IIR Measur.Tech", "textAlign" => "center"];
    $data['cpDiamention'][] = ["name" => "IIR Size", "textAlign" => "center"];
    $data['cpDiamention'][] = ["name" => "IIR Freq", "textAlign" => "center"];
    $data['cpDiamention'][] = ["name" => "IIR Freq Time", "textAlign" => "center"];
    $data['cpDiamention'][] = ["name" => "IIR Freq Text", "textAlign" => "center"];
    $data['cpDiamention'][] = ["name" => "OPR Measur.Tech", "textAlign" => "center"];
    $data['cpDiamention'][] = ["name" => "OPR Size", "textAlign" => "center"];
    $data['cpDiamention'][] = ["name" => "OPR Freq", "textAlign" => "center"];
    $data['cpDiamention'][] = ["name" => "OPR Freq Time", "textAlign" => "center"];
    $data['cpDiamention'][] = ["name" => "OPR Freq Text", "textAlign" => "center"];
    $data['cpDiamention'][] = ["name" => "IPR Measur.Tech", "textAlign" => "center"];
    $data['cpDiamention'][] = ["name" => "IPR Size", "textAlign" => "center"];
    $data['cpDiamention'][] = ["name" => "IPR Freq", "textAlign" => "center"];
    $data['cpDiamention'][] = ["name" => "IPR Freq Time", "textAlign" => "center"];
    $data['cpDiamention'][] = ["name" => "IPR Freq Text", "textAlign" => "center"];
    $data['cpDiamention'][] = ["name" => "SAR Measur.Tech", "textAlign" => "center"];
    $data['cpDiamention'][] = ["name" => "SAR Size", "textAlign" => "center"];
    $data['cpDiamention'][] = ["name" => "SAR Freq", "textAlign" => "center"];
    $data['cpDiamention'][] = ["name" => "SAR Freq Time", "textAlign" => "center"];
    $data['cpDiamention'][] = ["name" => "SAR Freq Text", "textAlign" => "center"];
    $data['cpDiamention'][] = ["name" => "SPC Measur.Tech", "textAlign" => "center"];
    $data['cpDiamention'][] = ["name" => "SPC Size", "textAlign" => "center"];
    $data['cpDiamention'][] = ["name" => "SPC Freq", "textAlign" => "center"];
    $data['cpDiamention'][] = ["name" => "SPC Freq Time", "textAlign" => "center"];
    $data['cpDiamention'][] = ["name" => "SPC Freq Text", "textAlign" => "center"];
    $data['cpDiamention'][] = ["name" => "FIR Measur.Tech", "textAlign" => "center"];
    $data['cpDiamention'][] = ["name" => "FIR Size", "textAlign" => "center"];
    $data['cpDiamention'][] = ["name" => "FIR Freq", "textAlign" => "center"];
    $data['cpDiamention'][] = ["name" => "FIR Freq Time", "textAlign" => "center"];
    $data['cpDiamention'][] = ["name" => "FIR Freq Text", "textAlign" => "center"];

    /*FIR  Header */
    $data['firInward'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['firInward'][] = ["name" => "#", "style" => "width:4%;", "textAlign" => "center" ];
    $data['firInward'][] = ["name" => "Date", "textAlign" => "center"];
    $data['firInward'][] = ["name" => "Job No", "textAlign" => "center"];
    $data['firInward'][] = ["name" => "Part", "textAlign" => "center"];
    $data['firInward'][] = ["name" => "Process", "textAlign" => "center"];
    $data['firInward'][] = ["name" => "Qty", "textAlign" => "center"];
    $data['firInward'][] = ["name" => "Accepted Qty", "textAlign" => "center"];
    $data['firInward'][] = ["name" => "Unaccepted Qty", "textAlign" => "center"];

    /*Pending FIR Header */
    $data['pendingFir'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['pendingFir'][] = ["name" => "#", "style" => "width:4%;", "textAlign" => "center" ];
    $data['pendingFir'][] = ["name" => "Date", "textAlign" => "center"];
    $data['pendingFir'][] = ["name" => "Job No", "textAlign" => "center"];
    $data['pendingFir'][] = ["name" => "Part", "textAlign" => "center"];
    $data['pendingFir'][] = ["name" => "Process", "textAlign" => "center"];
    $data['pendingFir'][] = ["name" => "Qty", "textAlign" => "center"];
    $data['pendingFir'][] = ["name" => "WIP", "textAlign" => "center"];
    $data['pendingFir'][] = ["name" => "Pending Qty", "textAlign" => "center"];

    
    /*Pending FIR Header */
    $data['fir'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['fir'][] = ["name" => "#", "style" => "width:4%;", "textAlign" => "center" ];
    $data['fir'][] = ["name" => "Date", "textAlign" => "center"];
    $data['fir'][] = ["name" => "FIR No", "textAlign" => "center"];
    $data['fir'][] = ["name" => "FG Batch No", "textAlign" => "center"];
    $data['fir'][] = ["name" => "Job No", "textAlign" => "center"];
    $data['fir'][] = ["name" => "Part", "textAlign" => "center"];
    $data['fir'][] = ["name" => "Qty", "textAlign" => "center"];
	
	/* Gauge Header */
    $masterGaugeSelect = '<input type="checkbox" id="masterGaugeSelect" class="filled-in chk-col-success BulkGaugeChallan" value=""><label for="masterGaugeSelect">ALL</label>';
	
	$data['qcGaugesChk'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['qcGaugesChk'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['qcGaugesChk'][] = ["name"=>$masterGaugeSelect,"style"=>"width:10%;","textAlign"=>"center","orderable"=>"false"];
	$data['qcGaugesChk'][] = ["name"=>"Gauge Size"];
    $data['qcGaugesChk'][] = ["name"=>"Gauge Code"];
    $data['qcGaugesChk'][] = ["name"=>"Category"];
    $data['qcGaugesChk'][] = ["name"=>"Make"];
    $data['qcGaugesChk'][] = ["name"=>"Required"];
    $data['qcGaugesChk'][] = ["name"=>"Frequency <br><small>(In months)</small>"];
    $data['qcGaugesChk'][] = ["name"=>"Location"];
	$data['qcGaugesChk'][] = ["name"=>"Cal Date","style"=>"width:80px;"];
	$data['qcGaugesChk'][] = ["name"=>"Due Date","style"=>"width:80px;"];
	$data['qcGaugesChk'][] = ["name"=>"Plan Date","style"=>"width:80px;"];
    $data['qcGaugesChk'][] = ["name"=>"Remark"];
	
	$data['qcGauges'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['qcGauges'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['qcGauges'][] = ["name"=>"Gauge Size"];
    $data['qcGauges'][] = ["name"=>"Gauge Code"];
    $data['qcGauges'][] = ["name"=>"Category"];
    $data['qcGauges'][] = ["name"=>"Make"];
    $data['qcGauges'][] = ["name"=>"Required"];
    $data['qcGauges'][] = ["name"=>"Frequency <br><small>(In months)</small>"];
    $data['qcGauges'][] = ["name"=>"Location"];
	$data['qcGauges'][] = ["name"=>"Cal Date","style"=>"width:80px;"];
	$data['qcGauges'][] = ["name"=>"Due Date","style"=>"width:80px;"];
	$data['qcGauges'][] = ["name"=>"Plan Date","style"=>"width:80px;"];
    $data['qcGauges'][] = ["name"=>"Remark"];

    /* Instrument Header */
    $masterInstSelect = '<input type="checkbox" id="masterInstSelect" class="filled-in chk-col-success BulkInstChallan" value=""><label for="masterInstSelect">ALL</label>';
    
	$data['qcInstrumentChk'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['qcInstrumentChk'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['qcInstrumentChk'][] = ["name"=>$masterInstSelect,"style"=>"width:10%;","textAlign"=>"center","orderable"=>"false"];
	$data['qcInstrumentChk'][] = ["name"=>"Description of Instrument","style"=>"width:200px !important;"];
	$data['qcInstrumentChk'][] = ["name"=>"Inst. Code"];
    $data['qcInstrumentChk'][] = ["name"=>"Category"];
	$data['qcInstrumentChk'][] = ["name"=>"Make"];
	$data['qcInstrumentChk'][] = ["name"=>"Range (mm)"];
	$data['qcInstrumentChk'][] = ["name"=>"Least Count"];
	$data['qcInstrumentChk'][] = ["name"=>"Permissible Error"];
	$data['qcInstrumentChk'][] = ["name"=>"Required"];
	$data['qcInstrumentChk'][] = ["name"=>"Frequency <br><small>(In months)</small>"];
	$data['qcInstrumentChk'][] = ["name"=>"Location"];
	$data['qcInstrumentChk'][] = ["name"=>"Cal Date","style"=>"width:20%;"];
	$data['qcInstrumentChk'][] = ["name"=>"Due Date","style"=>"width:20%;"];
	$data['qcInstrumentChk'][] = ["name"=>"Plan Date","style"=>"width:20%;"];
	$data['qcInstrumentChk'][] = ["name"=>"Remark"];
	    
	$data['qcInstrument'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['qcInstrument'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['qcInstrument'][] = ["name"=>"Description of Instrument","style"=>"width:200px !important;"];
	$data['qcInstrument'][] = ["name"=>"Inst. Code"];
    $data['qcInstrument'][] = ["name"=>"Category"];
	$data['qcInstrument'][] = ["name"=>"Make"];
	$data['qcInstrument'][] = ["name"=>"Range (mm)"];
	$data['qcInstrument'][] = ["name"=>"Least Count"];
	$data['qcInstrument'][] = ["name"=>"Permissible Error"];
	$data['qcInstrument'][] = ["name"=>"Required"];
	$data['qcInstrument'][] = ["name"=>"Frequency <br><small>(In months)</small>"];
	$data['qcInstrument'][] = ["name"=>"Location"];
	$data['qcInstrument'][] = ["name"=>"Cal Date","style"=>"width:20%;"];
	$data['qcInstrument'][] = ["name"=>"Due Date","style"=>"width:20%;"];
	$data['qcInstrument'][] = ["name"=>"Plan Date","style"=>"width:20%;"];
	$data['qcInstrument'][] = ["name"=>"Remark"];
	
	/* In Challan Header */
    $data['qcChallan'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['qcChallan'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['qcChallan'][] = ["name"=>"Ch. No."];
    $data['qcChallan'][] = ["name"=>"Ch. Date"];
    $data['qcChallan'][] = ["name"=>"Ch. Type"];
    $data['qcChallan'][] = ["name"=>"Code"];
    $data['qcChallan'][] = ["name"=>"Agency"];
    $data['qcChallan'][] = ["name"=>"Item Name"];
    $data['qcChallan'][] = ["name"=>"Remark"];
	
	/* QC Purchase Request Header */
    $data['qcPurchaseRequest'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['qcPurchaseRequest'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['qcPurchaseRequest'][] = ["name"=>"Request Date"];
    $data['qcPurchaseRequest'][] = ["name"=>"Request No"];
    $data['qcPurchaseRequest'][] = ["name"=>"Category"];
	$data['qcPurchaseRequest'][] = ["name"=>"Description"];
    $data['qcPurchaseRequest'][] = ["name"=>"Qty"];
    $data['qcPurchaseRequest'][] = ["name"=>"Size"];
    $data['qcPurchaseRequest'][] = ["name"=>"Make"];
    $data['qcPurchaseRequest'][] = ["name"=>"Required Date"];    
    $data['qcPurchaseRequest'][] = ["name"=>"Remark"];
	
	$masterQcCheckBox = '<input type="checkbox" id="masterQcSelect" class="filled-in chk-col-success BulkQcRequest" value=""><label for="masterQcSelect">ALL</label>';
    /* QC Indent Header */
    $data['qcIndent'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['qcIndent'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['qcIndent'][] = ["name"=>$masterQcCheckBox,"style"=>"width:10%;","textAlign"=>"center","orderable"=>"false"];
    $data['qcIndent'][] = ["name"=>"Request Date"];
    $data['qcIndent'][] = ["name"=>"Request No"];
	$data['qcIndent'][] = ["name"=>"Description"];
    $data['qcIndent'][] = ["name"=>"Category"];
    $data['qcIndent'][] = ["name"=>"Qty"];
    $data['qcIndent'][] = ["name"=>"Size"];
    $data['qcIndent'][] = ["name"=>"Make"];
    $data['qcIndent'][] = ["name"=>"Required Date"];  

    /* QC Purchase Header */
    $data['qcPurchase'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['qcPurchase'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['qcPurchase'][] = ["name"=>"Order No."];
    $data['qcPurchase'][] = ["name"=>"Order Date"];
    $data['qcPurchase'][] = ["name"=>"Supplier"];
    $data['qcPurchase'][] = ["name"=>"Category Name"];
    $data['qcPurchase'][] = ["name"=>"Size"];
    $data['qcPurchase'][] = ["name"=>"Rate"];
    $data['qcPurchase'][] = ["name"=>"Order Qty"];
    $data['qcPurchase'][] = ["name"=>"Received Qty"];
    $data['qcPurchase'][] = ["name"=>"Pending Qty"];
    $data['qcPurchase'][] = ["name"=>"P.O. Delivery Date"];
    $data['qcPurchase'][] = ["name"=>"GRN Date"];
    $data['qcPurchase'][] = ["name"=>"Status","textAlign"=>"center"]; 
	
	 /* Calibration Item Details*/
    $data['calibrationData'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['calibrationData'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['calibrationData'][] = ["name"=>"Calibration Agency"];
    $data['calibrationData'][] = ["name"=>"Calibration No."];
    $data['calibrationData'][] = ["name"=>"Certificate File"];
    $data['calibrationData'][] = ["name"=>"Remark"];

    return tableHeader($data[$page]);
}


/* RM Inspection Data */
function getInspectionParamData($data){
    $btn = '<button type="button" class="btn btn-twitter addInspectionOption permission-modify" data-id="'.$data->id.'" data-product_name="'.$data->item_name.'" data-button="both" data-modal_id="modal-md" data-function="getPreInspection" data-form_title="Product Inspection" data-srposition="0" datatip="Inspection" flow="left"><i class="fas fa-info"></i></button>';

    return [$data->sr_no,$data->item_name,$btn];
}

function getJobWorkInspectionData($data)
{
    $reportButton = '<a href="'.base_url('jobWorkInspection/inInspection/'.$data->id).'" type="button" class="btn btn-info " datatip="Incoming Inspection Report" flow="down"><i class="fa fa-file-alt"></i></a>';
    $pdfButton = '<a href="'.base_url('jobWorkInspection/inInspection_pdf/'.$data->id).'" type="button" class="btn btn-warning " datatip="Incoming Inspection Report Pdf" flow="down" target="_blank"><i class="fas fa-print"></i></a>';
    $action = getActionButton($reportButton.$pdfButton);
    return [$action,$data->sr_no,formatDate($data->entry_date),$data->challan_no,getPrefixNumber($data->job_prefix,$data->job_no),$data->party_name,$data->item_code,$data->charge_no,$data->process_name,$data->in_qty,$data->out_qty];
} 

/*
* Create By : 
* Updated By : NYN @04-11-2021 12:48 AM 
* Note : Reject BTN
*/
/* Purchase Material Inspection Table Data */
function getPurchaseMaterialInspectionData($data){
    $inspection = ''; $approve = ''; $reportButton = '';$testReport='';$tcButton='';$deviationButton="";$deviationprintBtn="";$pdfButton="";
    
    if($data->item_type != 3 && $data->inspected_qty == "0.000"){
        $inspection = '<a href="javscript:voide(0);" type="button" class="btn btn-success  getInspectedMaterial permission-modify" data-grn_id="'.$data->grn_id.'" data-trans_id="'.$data->id.'" data-grn_prefix="'.$data->grn_prefix.'" data-grn_no="'.$data->grn_no.'" data-grn_date="'.date("d-m-Y",strtotime($data->grn_date)).'" data-item_name="'.$data->item_name.'" data-toggle="modal" data-target="#inspectionModel" datatip="Inspection" flow="down"><i class="fas fa-search"></i></a>';
    }elseif($data->item_type == 3){
        if(!empty($data->ic_id)){
            if($data->inspected_qty == "0.000" && !empty($data->is_approve)){ $inspection = '<a href="javscript:voide(0);" type="button" class="btn btn-success  getInspectedMaterial permission-modify" data-grn_id="'.$data->grn_id.'" data-trans_id="'.$data->id.'" data-grn_prefix="'.$data->grn_prefix.'" data-grn_no="'.$data->grn_no.'" data-grn_date="'.date("d-m-Y",strtotime($data->grn_date)).'" data-item_name="'.$data->item_name.'" data-toggle="modal" data-target="#inspectionModel" datatip="Inspection" flow="down"><i class="fas fa-search"></i></a>';}
            
            if($data->is_approve == 0){
                $approveParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'approve', 'title' : 'Approve Inspection', 'fnEdit' : 'approveInspection', 'fnsave' : 'saveApproveInspection','button':'both'}";
                $approve = '<a class="btn btn-primary btn-salary permission-modify" href="javascript:void(0)" datatip="Approve Inspection" flow="down" onclick="edit('.$approveParam.');"><i class="fa fa-check"></i></a>';
            }else{
                $approve = '<a href="javascript:void(0)" class="btn btn-dark rejectInspection permission-approve" data-id="'.$data->id.'" data-val="2" data-msg="Reject" datatip="Reject" flow="down" ><i class="ti-close" ></i></a>';
            } 
        }   
        if($data->is_approve == 0){ $reportButton = '<a href="'.base_url('grn/inInspection/'.$data->id).'" type="button" class="btn btn-info " datatip="Incoming Inspection Report" flow="down"><i class="fa fa-file-alt"></i></a>'; }
        
        $pdfButton = '<a href="'.base_url('grn/inInspection_pdf/'.$data->id).'" type="button" class="btn btn-warning " datatip="Incoming Inspection Report Pdf" flow="down" target="_blank"><i class="fas fa-print"></i></a>';
        
        $testReport = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'testReport', 'title' : 'Test Report', 'fnEdit' : 'getTestReport', 'fnsave' : 'updateTestReport','button':'close'}";
        $tcButton = '<a class="btn btn-primary btn-salary permission-modify" href="javascript:void(0)" datatip="Test Report" flow="down" onclick="updateTestReport('.$testReport.');"><i class="sl-icon-bag"></i></a>';
        
        $deviationButton = '<a href="'.base_url('grn/deviationReport/'.$data->id).'" type="button" class="btn btn-danger " datatip="Deviation Report" flow="down"><i class="fa fa-file-alt"></i></a>'; 
        $deviationprintBtn = '<a class="btn btn-info btn-edit" href="'.base_url('grn/printDeviation/'.$data->id).'" target="_blank" datatip="Print Deviation" flow="down"><i class="fas fa-print" ></i></a>';
    }
    
    $order_no = (!empty($data->po_no) && !empty($data->po_prefix)) ? getPrefixNumber($data->po_prefix,$data->po_no) : "";
    
	$action = getActionButton($inspection.$reportButton.$approve.$tcButton.$pdfButton.$deviationprintBtn.$deviationButton);
    return [$action,$data->sr_no,getPrefixNumber($data->grn_prefix,$data->grn_no),date("d-m-Y",strtotime($data->grn_date)),$data->challan_no,$order_no,$data->party_name,$data->item_name,$data->product_code,$data->qty,$data->batch_no,$data->color_code, $data->status_label,(($data->item_type == 3)?$data->approve_status_label:'')];
}

/* get PreDispatch Inspect Data */
function getPreDispatchInspectData($data){
    $deleteParam = $data->id.",'PreDispatch Inspection'";
    $editButton = '<a href="'.base_url($data->controller.'/edit/'.$data->id).'" class="btn btn-success btn-edit permision-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->item_code,$data->param_count];
}

function getOutChallanData($data){
    $deleteParam = $data->trans_main_id.",'Challan'";

    $returnBtn = "";$edit =""; $delete = "";
    if($data->is_returnable == 1 && ($data->qty - $data->return_qty > 0)):
        $returnParams = ['item_name'=>htmlentities($data->item_name),'item_id'=>$data->item_id,'location_id'=>$data->location_id,'batch_no'=>$data->batch_no,'ref_no'=>getPrefixNumber($data->challan_prefix,$data->challan_no),'ref_id'=>$data->id,'pending_qty'=>($data->qty - $data->return_qty)];
        $returnBtn = "<a href='javascript:void(0)' class='btn btn-info returnItem permission-modify' datatip='Receive' flow='down' data-row='".json_encode($returnParams)."' ><i class='fas fa-reply'></i></a>";

        $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->trans_main_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';

        $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    elseif($data->is_returnable == 0):    
        $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->trans_main_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';

        $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    endif;

    $collected_name = (!empty($data->collected_code))? "[".$data->collected_code."] ".$data->collected_name : "";

    $action = getActionButton($returnBtn.$edit.$delete);
    return [$action,$data->sr_no,getPrefixNumber($data->challan_prefix,$data->challan_no),formatDate($data->challan_date),$data->party_name,$collected_name,$data->machineName,$data->item_name,$data->qty,$data->item_remark];
}

/* Get In Challan Data */
function getInChallanData($data){
    $deleteParam = $data->trans_main_id.",'Challan'";

    $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->trans_main_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';

    $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $returnBtn = "";
    if($data->is_returnable == 1):
        $returnParams = ['item_name'=>htmlentities($data->item_name),'item_id'=>$data->item_id,'location_id'=>$data->location_id,'batch_no'=>$data->batch_no,'ref_no'=>$data->doc_no,'ref_id'=>$data->id,'pending_qty'=>($data->qty - $data->return_qty)];
        $returnBtn = "<a href='javascript:void(0)' class='btn btn-info returnItem permission-modify' datatip='Return' flow='down' data-row='".json_encode($returnParams)."' ><i class='fas fa-share'></i></a>";
    endif;

    $action = getActionButton($returnBtn.$edit.$delete);
    return [$action,$data->sr_no,$data->doc_no,formatDate($data->challan_date),$data->party_name,$data->item_name,$data->qty,$data->item_remark];
}

/* Instrument Data */
function getInstrumentData($data){
    $deleteParam = $data->id.",'Instrument'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editInstrument', 'title' : 'Update Instrument', 'fnsave' : 'save'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    
    $printBtn = '<a class="btn btn-warning btn-edit permission-read" href="'.base_url('instrument/printInstrumentData/'.$data->id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';
    
    $calParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg','button' : 'close', 'form_id' : 'calibration', 'title' : 'Calibration ', 'fnEdit' : 'getCalibration', 'fnsave' : 'saveCalibration'}";
    $calibrationButton = '<a class="btn btn-info btn-contact permission-modify" href="javascript:void(0)" datatip="Calibration" flow="down" onclick="edit('.$calParam.');"><i class="fas fa-tachometer-alt"></i></a>';

    $action = getActionButton($printBtn.$calibrationButton.$editButton.$deleteButton);    
    
    $lcd = (!empty($data->last_cal_date)) ? date('d-m-Y',strtotime($data->last_cal_date)) : '';
    $ncd = (!empty($data->next_cal_date)) ? date('d-m-Y',strtotime($data->next_cal_date."-1 days")) : '';
    $pdate = (!empty($data->next_cal_date)) ? date('d-m-Y',strtotime($data->next_cal_date."-".($data->cal_reminder + 1)." days")) : '';
	
	if(!empty($ncd) AND (strtotime($ncd) <= strtotime(date('d-m-Y')))){$ncd = '<strong class="text-danger">'.$ncd.'</strong>';}
	if(!empty($pdate) AND (strtotime($pdate) <= strtotime(date('d-m-Y')))){$pdate = '<strong style="color:#ffbc34;">'.$pdate.'</strong>';}
	
    return [$action,$data->sr_no,$data->item_name,$data->item_code,$data->make_brand,$data->instrument_range,$data->least_count,$data->permissible_error,$data->cal_required,$data->cal_freq,$data->cal_agency,$lcd,$ncd,$pdate,$data->description];
} 

/* Gauge Data */
function getGaugeData($data){
    $deleteParam = $data->id.",'Gauge'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editGauge', 'title' : 'Update Gauge', 'fnsave' : 'save'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $printBtn = '<a class="btn btn-warning btn-edit permission-read" href="'.base_url('gauges/printGaugesData/'.$data->id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';
        
    $calParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl','button' : 'close', 'form_id' : 'calibration', 'title' : 'Calibration ', 'fnEdit' : 'getCalibration', 'fnsave' : 'saveCalibration'}";
    $calibrationButton = '<a class="btn btn-info btn-contact permission-modify" href="javascript:void(0)" datatip="Calibration" flow="down" onclick="edit('.$calParam.');"><i class="fas fa-tachometer-alt"></i></a>';

    $action = getActionButton($printBtn.$calibrationButton.$editButton.$deleteButton);     

    $lcd = (!empty($data->last_cal_date)) ? date('d-m-Y',strtotime($data->last_cal_date)) : '';
    $ncd = (!empty($data->next_cal_date)) ? date('d-m-Y',strtotime($data->next_cal_date. "-1 days")) : '';
    $pdate = (!empty($data->next_cal_date)) ? date('d-m-Y',strtotime($data->next_cal_date. "-".($data->cal_reminder+1)." days")) : '';
    if(!empty($ncd) AND (strtotime($ncd) <= strtotime(date('d-m-Y')))){$ncd = '<strong class="text-danger">'.$ncd.'</strong>';}
	if(!empty($pdate) AND (strtotime($pdate) <= strtotime(date('d-m-Y')))){$pdate = '<strong style="color:#ffbc34;">'.$pdate.'</strong>';}
    return [$action,$data->sr_no,$data->size,$data->item_code,$data->make_brand,$data->thread_type,$data->cal_required,$data->cal_freq,$data->location,$data->cal_agency,$lcd,$ncd,$pdate,$data->description];
}

function getFinalInspectionData($data){
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'finalInspection', 'title' : 'Final Inspection', 'product_name': '".trimQuotes($data->item_name)."' , 'pending_qty' : '".$data->pending_qty."','rejection_type_id': '".$data->rejection_type_id."', 'product_id': '".$data->product_id."', 'job_card_id' : '".$data->job_card_id."', 'job_inward_id' : '".$data->job_inward_id."', 'operator_id':'".$data->operator_id."', 'machine_id' : '".$data->machine_id."',  'button':'close'}";

    $edParam = [
        'id' => $data->id, 'modal_id' => 'modal-lg', 'form_id' => 'finalInspection', 'title' => 'Final Inspection', 'product_name'=> $data->item_name , 'pending_qty' => $data->pending_qty,'rejection_type_id'=> $data->rejection_type_id, 'product_id'=> $data->product_id, 'job_card_id' => $data->job_card_id, 'job_inward_id' => $data->job_inward_id, 'operator_id'=>$data->operator_id, 'machine_id' => $data->machine_id,  'button'=>'close'        
    ];

    $editButton = "<a class='btn btn-success btn-edit permission-modify' href='javascript:void(0)' datatip='Edit' flow='down' onclick='inspection(".json_encode($edParam).");'><i class='ti-pencil-alt' ></i></a>";

    $action = getActionButton($editButton);
    return [$action,$data->sr_no,(!empty($data->process_name))?$data->process_name:"Material Fault",$data->item_name,$data->qty,$data->pending_qty];
}

/* Rejection Comment Table Data */
function getRejectionCommentData($data){
    if($data->type == 1 || $data->type == 4):
        $rejection_type = ($data->type == 1 ? "Rejection": ($data->type == 4 ? "Rework":"Idle reason"));

        $deleteParam = $data->id.",".($data->type == 1 ? "Rejection": ($data->type == 4 ? "Rework":"Idle reason"));
        $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editRejectionComment', 'title' : 'Update Rejection/Rework Comment'}";
    
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

	    $action = getActionButton($editButton.$deleteButton);
        return [$action,$data->sr_no,$data->remark,$rejection_type];
    elseif($data->type == 2):
        $deleteParam = $data->id.",'Idle Reason'";
        $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editRejectionComment', 'title' : 'Update Idle Reason'}";
    
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
   
	    $action = getActionButton($editButton.$deleteButton);
        return [$action,$data->sr_no,$data->code,$data->remark];
    endif;
}


/* Assign Inspector Data */
function getAssignInspectorData($data){
    $editButton = "";
    if($data->status != 3):
        $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editAssignInspector', 'title' : 'Assign Inspector', 'fnEdit' : 'assignInspector'}";

        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Assign Inspector" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    endif;

    $action = getActionButton($editButton);
    return [$action,$data->sr_no,date("d-m-Y",strtotime($data->request_date)),getPrefixNumber($data->job_prefix,$data->job_no),$data->item_code,$data->process_name,$data->machine_no,$data->setter_name,$data->inspector_name,$data->assign_status,$data->remark];
}

/* Setup Inspector Data */
function getSetupInspectionData($data){
    $editButton = "";$attachmentLink = "";$acceptInspection = "";

    if(!empty($data->inspection_start_date)):
        if(!empty($data->setup_end_time) && !empty($data->qci_id)):
            $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editSetupInspection', 'title' : 'Setup Inspection', 'fnEdit' : 'setupInspection'}";

            $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Setup Inspection" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
        endif;
        
        if(!empty($data->attachment)):
            $attachmentLink = '<a href="'.base_url('assets/uploads/setup_ins_report/'.$data->attachment).'" class="btn btn-outline-info waves-effect waves-light"><i class="fa fa-arrow-down"> Download</a>';
        endif;
    else:
        if(!empty($data->qci_id)):
            $acceptInspection = '<a class="btn btn-success btn-start permission-modify" href="javascript:void(0)" datatip="Accept Inspection" flow="down" onclick="acceptInspection('.$data->id.');"><i class="fas fa-check" ></i></a>';
        endif;
    endif;

    $action = getActionButton($acceptInspection.$editButton);
    return [$action,$data->sr_no,date("d-m-Y",strtotime($data->request_date)),$data->status,$data->setup_type_name,$data->setter_name,$data->setter_note,getPrefixNumber($data->job_prefix,$data->job_no),$data->item_code,$data->process_name,(!empty($data->machine_code) || !empty($data->machine_name))?'[ '.$data->machine_code.' ] '.$data->machine_name:"",$data->inspector_name,(!empty($data->inspection_start_date))?date("d-m-Y h:i:s A",strtotime($data->inspection_start_date)):"",(!empty($data->inspection_date))?date("d-m-Y h:i:s A",strtotime($data->inspection_date)):"",$data->duration,$data->qci_note,$attachmentLink];
}

function getInspectionTypeData($data)
{
    $deleteParam = $data->id . ",'Inspection type'";
    $editParam = "{'id' : " . $data->id . ", 'modal_id' : 'modal-md', 'form_id' : 'editInspectionType', 'title' : 'Update Inspection Type'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit(' . $editParam . ');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash(' . $deleteParam . ');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $action = getActionButton($editButton . $deleteButton);

    return [$action, $data->sr_no, $data->inspection_type];
}

function getNCReportData($data){
    $action = "";
    $deleteParam = $data->id.",'Production Log'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'ncReport', 'title' : 'Update NC Report'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $action = getActionButton($editButton.$deleteButton);

    return [$action,$data->sr_no,getPrefixNumber($data->job_prefix, $data->job_no),$data->product_code,formatDate($data->log_date),$data->process_name,$data->emp_name,$data->production_qty,$data->rej_qty,$data->rw_qty];
}

// Created By Avruti @22/04/2022
function getRejectionLogData($data){
    $action = "";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'rejectionLog', 'title' : 'Update Rejection Log'}";
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteParam = $data->id.",'Rejection Log'";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    
	$rwQty = '<a href="'.base_url('rejectionLog/reworkmanagement/'.$data->id).'" target="_blank">'.$data->rw_qty.'</a>';
    $action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,getPrefixNumber($data->job_prefix, $data->job_no),$data->product_name,formatDate($data->log_date),$data->process_name,$data->item_code,$data->emp_name,$data->rej_qty,$rwQty];
}

/* Control Plan Data */
function getControlPlanData($data){
    $btn = '<div class="btn-group" role="group" aria-label="Basic example">
                <button type="button" class="btn btn-twitter addInspectionOption permission-modify" data-id="'.$data->id.'" data-product_name="'.$data->item_code.'"  data-button="both" data-modal_id="modal-lg" data-function="getInspectionParameter" data-form_title="Inspection Parameter" datatip="Inspection Parameter" flow="left"><i class="fas fa-info"></i></button>
                <button type="button" class="btn btn-info addInspectionOption permission-modify" data-id="'.$data->id.'" data-product_name="'.$data->item_code.'" data-button="both" data-modal_id="modal-md" data-function="addUploadFile" data-form_title="Upload Drawing File" datatip="Upload Drawing File" flow="down"><i class="ti-upload"></i></button>
            </div>';

    return [$data->sr_no,$data->item_code,$data->inspection,$data->process,$btn];
}

/* Line Inspect Data */
function getLineInspectData($data){
    $deleteParam = $data->id.",'Line Inspection'";
    $editButton = '<a href="'.base_url('controlPlan/edit/'.$data->id).'" class="btn btn-success btn-edit permision-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    $printBtn = '<a class="btn btn-info btn-edit" href="'.base_url('controlPlan/printLineInspection/'.$data->id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';
   
    $action = getActionButton($printBtn.$editButton.$deleteButton);
    return [$action,$data->sr_no,formatDate($data->insp_date),getPrefixNumber($data->job_prefix, $data->job_no),$data->item_code,$data->process_name,$data->sampling_qty,$data->emp_name];
}


/****** */

/* Control Plan Data */
function getControlPlanDataV2($data){
    $btn = '';
    if(!empty($data->rev_count)){
        $btn = '<a href="'.base_url("npd/controlPlan/pfcList/".$data->id).'" class="btn btn-twitter permission-modify" target="_blank" datatip="PFC" flow="left">PFC</a>';
    }
    $pfc = (!empty($data->total_pfc))?'<i class="fa fa-check text-primary"></i>':'';
    return [$data->sr_no,$data->item_code,$data->item_name,$pfc,$btn];
}

/* Title Data */
function getReactionPlanData($data){ 
    $deleteParam = $data->id.",'Reaction Plan'";
    $editParam = "{'id' : ".$data->plan_no.", 'modal_id' : 'modal-lg', 'form_id' : 'addDescription','button' : 'close', 'title' : 'Update  Reaction Plan','fnEdit':'editReactionPlan'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down" ><i class="ti-trash"></i></a>';

    $viewParam = "{'id' : ".$data->plan_no.", 'modal_id' : 'modal-lg', 'form_id' : 'addDescription','button' : 'close', 'title' : 'Reaction Plan','fnEdit':'getReactionPlanList'}";
    $viewBtn = '<a href="javascript:void(0)"  class="btn btn-primary  permission-read" data-id="'.$data->id.'" datatip="Reaction Plan List" flow="down" onclick="edit('.$viewParam.');"><i class="fa fa-list" ></i></a>';

    $action = getActionButton($editButton);  

    return [$action,$data->sr_no,$data->title,$viewBtn];
}

/* Description Data */
function getDescriptionData($data){ 
    $deleteParam = $data->id.",'Reaction Plan'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editDescription', 'title' : 'Update  Reaction Plan', 'fnsave' : 'save'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $action = getActionButton($editButton.$deleteButton);  

    return [$action,$data->sr_no,$data->title,$data->description];
}

/* Sampling Plan Data */
function getSamplingPlanData($data){ 
    $deleteParam = $data->id.",'Sampling Plan'";

    $editParam = "{'id' : '".$data->plan_no."', 'modal_id' : 'modal-lg', 'form_id' : 'addSamplingPlan', 'title' : 'Update SamplingPlan','fnEdit':'editSamplePlan'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $viewParam = "{'control_method' : '".$data->control_method."','sample_title' : '".$data->title."', 'modal_id' : 'modal-lg', 'form_id' : 'editSamplingPlan', 'title' : 'Update SamplingPlan','fnedit':'getSamplePlanList'}";

    $sampleList = '<a href="javascript:void(0)"  class="btn btn-primary  permission-read" data-id="'.$data->id.'" datatip="Item List" flow="down" onclick="viewSamplePlan('.$viewParam.');"><i class="fa fa-list" ></i></a>';
    
    $action = getActionButton($sampleList.$editButton);  

    return [$action,$data->sr_no,$data->title,$data->control_method];
}



function getPFCData($data){

    $editButton = "";$deleteButton="";
    //if($data->job_count == 0){
        $deleteParam = $data->id.",'Control Plan'";
        $editButton = '<a href="'.base_url($data->controller.'/editPfc/'.$data->id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
    
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trashPfc(' . $deleteParam . ');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    //}
    

    $revisionButton = '<a href="'.base_url($data->controller.'/revisionPfc/'.$data->id).'" class="btn btn-warning btn-edit permission-modify" datatip="Revision" flow="down"><i class=" fas fa-retweet"></i></a>';
    $printBtn = '<a class="btn btn-primary" href="'.base_url('npd/controlPlan/pfc_pdf/'.$data->id).'" target="_blank" datatip="Print PFC" flow="down"><i class="fas fa-print" ></i></a>';

    $operationParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'getItemList', 'title' : 'Operation List', 'fnEdit':'getOperationList','button':'close'}";

    $operationList = '<a class="btn btn-info btn-edit permission-modify" href="javascript:void(0)" datatip="Operation List" flow="down" onclick="edit('.$operationParam.');"><i class="fa fa-list" ></i></a>';

    $pfc_number = '<a href="'.base_url("npd/controlPlan/controlPlanList/".$data->id).'" class="permission-modify"  datatip="Control Plan" flow="left">'.$data->trans_number.'</a>';
    $action = getActionButton($printBtn.$operationList.$editButton.$deleteButton);  


    return [$action,$data->sr_no,$pfc_number,$data->full_name,$data->app_rev_no,formatDate($data->app_rev_date)];
}

function getFMEAData($data){

    
    $editButton = '<a href="'.base_url($data->controller.'/editDiamention/'.$data->id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
    $deleteParam = $data->id.",'Control Plan'";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trashFMEA(' . $deleteParam . ');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $printBtn = '<a class="btn btn-primary" href="'.base_url('npd/controlPlan/fmea_pdf/'.$data->id).'" target="_blank" datatip="Print FMEA" flow="down"><i class="fas fa-print" ></i></a>';
    $action = getActionButton($printBtn.$editButton.$deleteButton); 
    $fmeaNo = '<a href="'.base_url($data->controller."/diamentionList/".$data->id).'" target="_blank">'.$data->trans_number.'</a>';
    return [$action,$data->sr_no,$fmeaNo,'['.$data->process_no.'] '.$data->parameter,$data->app_rev_no,formatDate($data->app_rev_date),$data->cust_rev_no];
}

/** Diamention Data */
function getFMEADiamentionData($data){
    $deleteParam = $data->id.",'FMEA'";

    $editButton = "";$deleteButton = '';
	$parameter = '<a href="'.base_url($data->controller."/fmeaFailView/".$data->id).'" target="_blank">'.$data->parameter.'</a>';
    $editButton = '<a href="'.base_url($data->controller.'/editFailureMode/'.$data->id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';

    $char_class=''; if(!empty($data->char_class)){ $char_class='<img src="' . base_url('assets/images/symbols/'.$data->char_class.'.png') . '" style="width:15px;display:inline-block;" />'; }
    $action = getActionButton($editButton.$deleteButton);
    $diamention ='';
    if($data->requirement==1){ $diamention = $data->min_req.'/'.$data->max_req.' '.$data->other_req ; }
    if($data->requirement==2){ $diamention = $data->min_req.' '.$data->other_req ; }
    if($data->requirement==3){ $diamention = $data->max_req.' '.$data->other_req ; }
    if($data->requirement==4){ $diamention = $data->other_req ; }
    return [$action,$data->sr_no,$parameter,$diamention,$char_class];
}

/** Failure Mode Data */
function getFMEAFailData($data){

    // $deleteParam = $data->id.",'FMEA'";
    $causeParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'potential_cause_form', 'title' : 'Add Potential Cause', 'fnEdit':'addPotentialCause','fnsave' : 'savePotentialCause','button' : 'close'}";

    $causeBtn = "";
    $causeBtn = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Add Potential Cause" flow="down" onclick="edit('.$causeParam.');"><i class=" fas fa-plus" ></i></a>';

    // $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trashFmea(' . $deleteParam . ');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $action = getActionButton($causeBtn);  

    $char_class=''; if(!empty($data->class)){ $char_class='<img src="' . base_url('assets/images/symbols/'.$data->class.'.png') . '" style="width:15px;display:inline-block;" />'; }

    
    return [$action,$data->sr_no,$data->failure_mode,$data->customer,$data->manufacturer,$data->cust_sev,$data->mfg_sev,$data->sev,$data->process_detection,$data->detec];
}

/* PFC Operation Data */
function getPFCOperationData($data){
    $btn = '<a href="'.base_url("npd/controlPlan/diamentionList/".$data->item_id."/".$data->id).'" class="btn btn-info addFmea permission-modify" target="_blank" datatip="FMEA" flow="left">Add Diamantion</a>';

    return [$data->sr_no,$data->process_no,$data->parameter,$data->app_rev_no,formatDate($data->app_rev_date),$btn];
}

/*Control Method Data */
function getControlMethodData($data){ 
    $deleteParam = $data->id.",'Control Method'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editSamplingPlan', 'title' : 'Update SamplingPlan', 'fnsave' : 'save'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $action = getActionButton($editButton);  

    return [$action,$data->sr_no,$data->control_method,$data->cm_alias];
}

function getCPData($data){
    $editButton=""; $deleteButton=""; $approve="";

    $fmeaNo = '<a href="'.base_url($data->controller."/cpDiamentionList/".$data->id).'" >'.$data->trans_number.'</a>';
    $printBtn = '<a href="javascript:void(0)" class="btn btn-info btn-edit printCp " datatip="Control Plan Print" flow="down" data-id="'.$data->id.'" data-function="cp_pdf"><i class="fa fa-print"></i></a>';
    //if($data->job_count == 0):
        if(empty($data->approved_by)):
            $editButton = '<a href="'.base_url($data->controller.'/editDiamention/'.$data->id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
            $deleteParam = $data->id.",'Control Plan'";

            $deleteParam = $data->id.",'Control Plan'";
            $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trashControlPlan(' . $deleteParam . ');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

            $approve = '<a href="javascript:void(0)" class="btn btn-facebook approveCP permission-modify" data-id="'.$data->id.'" data-val="1" data-msg="Approve" datatip="Approve" flow="down" ><i class="fa fa-check" ></i></a>';
        endif;
    //endif;

    $action = getActionButton($approve.$printBtn.$editButton.$deleteButton);
    return [$action,$data->sr_no,$fmeaNo,'['.$data->process_no.'] '.$data->product_param,$data->app_rev_no,formatDate($data->app_rev_date),$data->cust_rev_no];
}

/** Diamention Data */
function getCPDiamentionData($data){
    $controlBtn = ""; $activeButton="";
    $controlParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'control_plan', 'title' : 'Add Control Method', 'fnedit':'addControlMethod','fnsave' : 'saveControlMethod','button' : 'close'}";
    

    $diamention ='';
    if($data->requirement==1){ $diamention = $data->min_req.'/'.$data->max_req ; }
    if($data->requirement==2){ $diamention = $data->min_req.' '.$data->other_req ; }
    if($data->requirement==3){ $diamention = $data->max_req.' '.$data->other_req ; }
    if($data->requirement==4){ $diamention = $data->other_req ; }

    return [$data->sr_no,$data->rev_no,$data->product_param,$diamention,$data->iir_measur_tech,$data->iir_size,$data->iir_freq,$data->iir_freq_time,$data->iir_freq_text,$data->opr_measur_tech,$data->opr_size,$data->opr_freq,$data->opr_freq_time,$data->opr_freq_text,$data->ipr_measur_tech,$data->ipr_size,$data->ipr_freq,$data->ipr_freq_time,$data->ipr_freq_text,$data->sar_measur_tech,$data->sar_size,$data->sar_freq,$data->sar_freq_time,$data->sar_freq_text,$data->spc_measur_tech,$data->spc_size,$data->spc_freq,$data->spc_freq_time,$data->spc_freq_text,$data->fir_measur_tech,$data->fir_size,$data->fir_freq,$data->fir_freq_time,$data->fir_freq_text];
}


/* FIR Header */
function getFIRInwardData($data){
    
    $acceptBtn ='' ;
    $param = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'aceptQty', 'title' : 'Accept Inspection Qty', 'fnEdit' : 'acceptFIR','fnsave' : 'saveInward'}";

    $acceptBtn = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Accept Inspection" flow="down" onclick="edit('.$param.');"><i class=" fas fa-check-circle" ></i></a>';

    $action = getActionButton($acceptBtn);
    return [ $action,$data->sr_no,formatDate($data->log_date),getPrefixNumber($data->job_prefix,$data->job_no),$data->item_code,$data->process_name,floatval($data->ok_qty),floatval(abs($data->accepted_qty)),floatval($data->ok_qty - $data->accepted_qty)];

}

/* FIR Header */
function getPendingFirData($data){
    
    // $lotBtn = '<a href="'.base_url($data->controller.'/addFirLot/'.$data->id).'" class="btn btn-success btn-edit permission-modify" datatip="Add Report" flow="down"><i class=" fas fa-plus-circle "></i></a>';
    $param = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'addLot', 'title' : 'Add FIR Lot', 'fnEdit' : 'addFirLot','fnsave' : 'saveLot'}";

    $lotBtn = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Add Lot" flow="down" onclick="edit('.$param.');"><i class="fas fa-plus" ></i></a>';

    $action = getActionButton($lotBtn);
    return [ $action,$data->sr_no,formatDate($data->log_date),getPrefixNumber($data->job_prefix,$data->job_no),$data->item_code,$data->process_name,floatval($data->accepted_qty),floatval(abs($data->fir_qty)),floatval($data->accepted_qty-$data->fir_qty)];

}

/* FIR Header */
function getFirData($data){
    $completeBtn="";$editBtn="";$movementBtn="";$deleteBtn = "";$setupBtn="";$seqBtn="";
    $pQty = $data->qty - $data->movement_qty-$data->total_rej_qty-$data->total_rw_qty;
    if(empty($data->status)){
        $editBtn = '<a href="'.base_url($data->controller.'/edit/'.$data->id).'" class="btn btn-success btn-edit permission-modify" datatip="FIR Report" flow="down"><i class=" fas fa-plus-circle "></i></a>';

       
        $deleteParam = $data->id.",'FIR'";
        $deleteBtn = '<a class="btn btn-danger btn-delete " href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

        $param = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'addLot', 'title' : 'Complete', 'fnedit' : 'completeFirView','fnsave' : 'completeFir'}";
        if($data->total_fir_ok > 0){
            $completeBtn = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Complete FIR" flow="down" onclick="completeFir('.$param.');"><i class="fas fa-check-circle" ></i></a>';
        }
        // $setupParam = "{'id' : " . $data->job_approval_id . ", 'modal_id' : 'modal-lg', 'form_id' : 'setupReq', 'title' : 'Setup Request','button' : 'close','fnsave' : 'setupRequestSave'}";
        // $setupBtn= '<a class="btn btn-dark btn-edit" href="javascript:void(0)" datatip="Setup Request" flow="down" onclick="setupRequest(' . $setupParam . ');"><i class=" fas fa-paper-plane"></i></a>';

        $seqParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'sequence', 'title' : 'Change Sequence', 'fnEdit' : 'changeDimensionSequence','button':'close'}";
        $seqBtn = '<a class="btn btn-warning btn-edit permission-modify" href="javascript:void(0)" datatip="Change Sequence" flow="down" onclick="edit('.$seqParam.');"><i class="fas fa-align-justify " ></i></a>';
        
    }else{
        /*if ($data->total_fir_ok  > 0 && !empty($data->out_process_id)) :
            $moveParam = "{'id' : " . $data->job_approval_id . ",'ref_id': " . $data->id . ",'p_qty': " .((!empty($pQty) && $pQty > 0 )?$pQty:0). ", 'modal_id' : 'modal-xl', 'form_id' : 'movement', 'title' : 'Move To Next Process','button':'close','fnsave' : 'saveProcessMovement', 'fnEdit' : 'processMovement','btnSave':'other'}";
            $movementBtn = '<a class="btn btn-warning btn-edit" datatip="Move to Next Process" flow="down" onclick="processMovement(' . $moveParam . ');"><i class="fa fa-step-forward"></i></a>';
        endif;
        if ($data->out_process_id == 0 && $data->total_fir_ok > 0) :
            $storeLocationParam = "{'id' : " . $data->job_card_id . ",'transid' : " . $data->job_approval_id . ",'ref_batch':" . $data->id . ",'remark':'FIR', 'modal_id' : 'modal-lg', 'form_id' : 'storeLocation', 'title' : 'Store Location','button' : 'close'}";
    
            $movementBtn= '<a class="btn btn-warning btn-edit" href="javascript:void(0)" datatip="Store Location" flow="down" onclick="storeLocation(' . $storeLocationParam . ');"><i class="fas fa-paper-plane"></i></a>';
        endif;*/
    }
    $deleteBtn = "";
    $pdfButton = '<a href="'.base_url('fir/fir_pdf/'.$data->id).'" type="button" class="btn btn-primary " datatip="Final Inspection Report Pdf" flow="down" target="_blank"><i class="fas fa-print"></i></a>';
    
    $tcParam = "{'id' : ".$data->job_card_id.", 'modal_id' : 'modal-md', 'form_id' : 'fileDownload', 'button' : 'close', 'title' : 'Test Report', 'fnEdit' : 'testReport'}";
    $tcBtn = '<a class="btn btn-warning permission-modify" href="javascript:void(0)" datatip="Test Report" flow="down" onclick="edit('.$tcParam.');"><i class="fa fa-file" ></i></a>';
    
    $action = getActionButton($pdfButton.$seqBtn.$editBtn.$completeBtn.$movementBtn.$setupBtn.$tcBtn.$deleteBtn);
    return [$action,$data->sr_no,formatDate($data->fir_date),$data->fir_number,$data->fg_batch_no,getPrefixNumber($data->job_prefix,$data->job_no),$data->item_code,floatval($data->qty)];

}

/* Gauge Data */
function getQcGaugeData($data){
    $deleteParam = $data->id.",'Gauge'";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="right"><i class="ti-trash"></i></a>';

    $inward=''; $reject=''; $editButton = '';
    if(empty($data->status)){
        $inwardParam = "{'id' : ".$data->id.", 'status' : '1', 'modal_id' : 'modal-lg', 'form_id' : 'inwardGauge', 'title' : 'Inward Gauge', 'fnEdit':'inwardGauge', 'fnsave' : 'save'}";
        $inward = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Inward Gauge" flow="right" onclick="inwardGauge('.$inwardParam.');"><i class="fas fa-reply" ></i></a>';
    }elseif($data->status == 1){
        $reject = '<a href="javascript:void(0)" class="btn btn-dark rejectGauge permission-modify" data-id="'.$data->id.'" data-gauge_code="'.$data->item_code.'" datatip="Reject" flow="down"><i class="ti-close" ></i></a>';
    
        $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editGauge', 'title' : 'Update Gauge', 'fnsave' : 'save'}";
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="right" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    }
    $printBtn = '<a class="btn btn-warning btn-edit permission-read" href="'.base_url('qcGauges/printGaugesData/'.$data->id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';
        
    $selectBox = '<input type="checkbox" name="ref_id[]" id="ref_id_'.$data->sr_no.'" class="filled-in chk-col-success BulkGaugeChallan" value="'.$data->id.'"><label for="ref_id_'.$data->sr_no.'"></label>';
    
    $deleteButton='';
    $action = getActionButton($printBtn.$reject.$inward.$editButton.$deleteButton);    
    
    $lcd = (!empty($data->last_cal_date)) ? date('d-m-Y',strtotime($data->last_cal_date)) : '';
    $ncd = (!empty($data->next_cal_date)) ? date('d-m-Y',strtotime($data->next_cal_date. "-1 days")) : '';
    $pdate = (!empty($data->next_cal_date)) ? date('d-m-Y',strtotime($data->next_cal_date. "-".($data->cal_reminder+1)." days")) : '';
    if(!empty($ncd) AND (strtotime($ncd) <= strtotime(date('d-m-Y')))){$ncd = '<strong class="text-danger">'.$ncd.'</strong>';}
	if(!empty($pdate) AND (strtotime($pdate) <= strtotime(date('d-m-Y')))){$pdate = '<strong style="color:#ffbc34;">'.$pdate.'</strong>';}
	$itemCode = '<a href="'.base_url("qcGauges/calibrationData/".$data->id).'" datatip="View Details">'.$data->item_code.'</a>';
	if(in_array($data->status,[1,5])){
		return [$action,$data->sr_no,$selectBox,$data->item_name.' '.$data->rej_status,$itemCode,$data->category_name,$data->make_brand,$data->cal_required,$data->cal_freq,$data->location_name,$lcd,$ncd,$pdate,$data->description];
	}else{
		return [$action,$data->sr_no,$data->item_name.' '.$data->rej_status,$itemCode,$data->category_name,$data->make_brand,$data->cal_required,$data->cal_freq,$data->location_name,$lcd,$ncd,$pdate,$data->description];
	}
}

/* Instrument Data */
function getQcInstrumentData($data){
    $deleteParam = $data->id.",'Instrument'";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="right"><i class="ti-trash"></i></a>';

    $inward=''; $reject=''; $editButton = '';
    if(empty($data->status)){
        $inwardParam = "{'id' : ".$data->id.", 'status' : '1', 'modal_id' : 'modal-lg', 'form_id' : 'inwardGauge', 'title' : 'Inward Instrument', 'fnEdit':'inwardGauge', 'fnsave' : 'save'}";
        $inward = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Inward Instrument" flow="right" onclick="inwardGauge('.$inwardParam.');"><i class="fas fa-reply" ></i></a>';
    }elseif($data->status == 1){
        $reject = '<a href="javascript:void(0)" class="btn btn-dark rejectGauge permission-modify" data-id="'.$data->id.'" data-gauge_code="'.$data->item_code.'" datatip="Reject" flow="down"><i class="ti-close" ></i></a>';
    
        $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editGauge', 'title' : 'Update Instrument', 'fnsave' : 'save'}";
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="right" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    }
	$printBtn = '<a class="btn btn-warning btn-edit permission-read" href="'.base_url('qcInstrument/printInstrumentData/'.$data->id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';
    
    
    $selectBox = '<input type="checkbox" name="ref_id[]" id="ref_id_'.$data->sr_no.'" class="filled-in chk-col-success BulkInstChallan" value="'.$data->id.'"><label for="ref_id_'.$data->sr_no.'"></label>';
    
    $deleteButton='';
    $action = getActionButton($printBtn.$reject.$inward.$editButton.$deleteButton);    
    
    $lcd = (!empty($data->last_cal_date)) ? date('d-m-Y',strtotime($data->last_cal_date)) : '';
    $ncd = (!empty($data->next_cal_date)) ? date('d-m-Y',strtotime($data->next_cal_date. "-1 days")) : '';
    $pdate = (!empty($data->next_cal_date)) ? date('d-m-Y',strtotime($data->next_cal_date. "-".($data->cal_reminder+1)." days")) : '';
    if(!empty($ncd) AND (strtotime($ncd) <= strtotime(date('d-m-Y')))){$ncd = '<strong class="text-danger">'.$ncd.'</strong>';}
	if(!empty($pdate) AND (strtotime($pdate) <= strtotime(date('d-m-Y')))){$pdate = '<strong style="color:#ffbc34;">'.$pdate.'</strong>';}

    $itemCode = '<a href="'.base_url("qcInstrument/calibrationData/".$data->id).'" datatip="View Details">'.$data->item_code.'</a>';

    if(in_array($data->status,[1,5])){
		return [$action,$data->sr_no,$selectBox,$data->item_name,$itemCode,$data->category_name,$data->make_brand,$data->size,$data->least_count,$data->permissible_error,$data->cal_required,$data->cal_freq,$data->location_name,$lcd,$ncd,$pdate,$data->description];
	}else{
		return [$action,$data->sr_no,$data->item_name,$itemCode,$data->category_name,$data->make_brand,$data->size,$data->least_count,$data->permissible_error,$data->cal_required,$data->cal_freq,$data->location_name,$lcd,$ncd,$pdate,$data->description];
	}
}

/* QcChallan Data */
function getQcChallanData($data){
    $returnBtn=''; $caliBtn=''; $edit=''; $delete='';
    
    if(empty($data->receive_by)){
        $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->challan_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
        $deleteParam = $data->challan_id.",'Challan'";
        $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trashQcChallan('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

        if($data->challan_type != 3){
            $rtnParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'button':'close', 'form_id' : 'returnChallan', 'title' : 'Return Challan', 'fnEdit' : 'returnChallan'}";
            $returnBtn = '<a href="javascript:void(0)" class="btn btn-info permission-modify" onclick="returnQcChallan('.$rtnParam.');" datatip="Return" flow="down"><i class="fas fa-reply"></i></a>';
        }else{
            $calParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl','button' : 'close', 'form_id' : 'calibration', 'title' : 'Calibration ', 'fnEdit' : 'getCalibration', 'fnsave' : 'saveCalibration'}";
            $caliBtn = '<a class="btn btn-info permission-modify" href="javascript:void(0)" datatip="Calibration" flow="down" onclick="edit('.$calParam.');"><i class="fas fa-tachometer-alt"></i></a>';
        }
    }/*else{
        $caliBtn = '<a class="btn btn-info confirmChallan permission-modify" data-id="'.$data->id.'" data-challan_id="'.$data->challan_id.'" data-item_id="'.$data->item_id.'" href="javascript:void(0)" datatip="Confirm" flow="down"><i class="ti-check"></i></a>';
    }*/

    $printBtn = '<a class="btn btn-warning btn-edit permission-read" href="'.base_url('qcChallan/printChallan/'.$data->challan_id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';

    $data->party_name = (!empty($data->party_name))?$data->party_name:'IN-HOUSE';
    $data->challan_type = (($data->challan_type==1)? 'IN-House Issue': (($data->challan_type==2) ? 'Vendor Issue':'Calibration'));
    
    $action = getActionButton($printBtn.$caliBtn.$returnBtn.$edit.$delete);
    return [$action,$data->sr_no,getPrefixNumber($data->trans_prefix,$data->trans_no),formatDate($data->trans_date),$data->challan_type,$data->item_code,$data->party_name,$data->item_name,$data->remark];
}

/* QC Purchase Table Data */
function getQCPRData($data){
    $deleteParam = $data->id.",'QC Purchase Request'";
    
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editQCPR', 'title' : 'Update QC PR'}";
    $edit = "";$delete = "";
    
    if($data->status == 0):       
        $edit = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt"></i></a>';
        $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    endif;
	
	$action = getActionButton($edit.$delete);
		
    return [$action,$data->sr_no,$data->req_date,$data->req_number,$data->category_name,$data->description,$data->qty,$data->size,$data->make,formatDate($data->delivery_date),$data->reject_reason];
}

/* Qc Indent Data  */
function getQCIndentData($data){
    $rejParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editQCPR', 'fnsave' : 'rejectQCPR', 'title' : 'Reject QC PR'}";
    $rejectBtn="";
    if($data->status == 0):       
        //$edit = '<a href="'.base_url($data->controller.'/edit/'.$data->id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
        //$rejectBtn = '<a class="btn btn-dark btn-edit permission-modify" href="javascript:void(0)" datatip="Reject QC PR" flow="down" onclick="edit('.$rejParam.');"><i class="ti-na"></i></a>';
    endif;
    $action = getActionButton($rejectBtn);
    $selectBox = '<input type="checkbox" name="ref_id[]" id="ref_id_'.$data->sr_no.'" class="filled-in chk-col-success BulkQcRequest" value="'.$data->id.'"><label for="ref_id_'.$data->sr_no.'"></label>';
    
    return [$action,$data->sr_no,$selectBox,$data->req_date,$data->req_number,$data->description,$data->category_name,$data->qty,$data->size,$data->make,formatDate($data->delivery_date)];
}

/* QC Purchase Table Data */
function getQCPurchaseData($data){
    $deleteParam = $data->order_id.",'QC Purchase'";
    $grn = "";$edit = "";$delete = ""; $receive = "";$shortClose = "";$approve = "";$mailBtn='';
    $ref_no = str_replace('/','_',getPrefixNumber($data->po_prefix,$data->po_no));
    $emailParam = $data->order_id.",'".$ref_no."'";
    if($data->order_status == 0):  
        $shortClose = '<a href="javascript:void(0)" class="btn btn-dark closePOrder permission-modify" data-id="'.$data->order_id.'" data-val="2" data-msg="Short Close" datatip="Short Close" flow="down" ><i class="ti-close" ></i></a>';
            
        if(empty($data->is_approve)){     
            $approve = '<a href="javascript:void(0)" onclick="openView('.$data->order_id.')" class="btn btn-facebook permission-approve" data-id="'.$data->order_id.'" data-val="1" data-msg="Approve" datatip="Approve Order" flow="down" ><i class="fa fa-check" ></i></a>';

            $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->order_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';

            $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
        } else{
            $receive = '<a href="javascript:void(0)" class="btn btn-primary purchaseReceive permission-modify" data-po_id="'.$data->order_id.'" datatip="Receive" flow="down"><i class="fas fa-reply" ></i></a>';
            $approve = '<a href="javascript:void(0)" class="btn btn-facebook approvePOrder permission-approve" data-id="'.$data->order_id.'" data-val="0" data-msg="Reject" datatip="Reject Order" flow="down" ><i class="fa fa-ban" ></i></a>';
            $mailBtn = '<a class="btn btn-warning permission-read" href="javascript:void(0)" onclick="sendMail('.$emailParam.')" datatip="Send Mail" flow="down"><i class="fas fa-envelope" ></i></a>';
        }
    endif;
	$printBtn = '<a class="btn btn-info btn-edit permission-modify" href="'.base_url($data->controller.'/printQP/'.$data->order_id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';
    $data->grn_date = (!empty($data->grn_date))?formatDate($data->grn_date):'';

	$action = getActionButton($shortClose.$approve.$printBtn.$mailBtn.$receive.$grn.$edit.$delete);
    return [$action,$data->sr_no,getPrefixNumber($data->po_prefix,$data->po_no),formatDate($data->po_date),$data->party_name,'['.$data->category_code.'] '.$data->category_name,$data->size,$data->price,$data->qty,$data->rec_qty,$data->pending_qty,formatDate($data->delivery_date),$data->grn_date,$data->order_status_label];
}

function getCalibration($data){ 
    $caliParam = "{'id' : ".$data->id.",'modal_id' : 'modal-lg', 'form_id' : 'editCalibrationData', 'title' : 'Calibration', 'button' : 'both','fnEdit' : 'editCalibrationData', 'fnsave' : 'saveCalibrationData'}";
    $caliBtn = '<a class="btn btn-success btn-contact permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$caliParam.');"><i class="ti-pencil-alt"></i></a>';
	
	$download = "";
	if(!empty($data->certificate_file)){
		if($data->item_type == 1){
			$download = ((!empty($data->certificate_file))?'<a href="'.base_url('assets/uploads/gauges/'.$data->certificate_file).'" target="_blank"><i class="fa fa-download"></i></a>':'');
		}else{
			$download = ((!empty($data->certificate_file))?'<a href="'.base_url('assets/uploads/gauges/'.$data->certificate_file).'" target="_blank"><i class="fa fa-download"></i></a>':'');
		}
	}
	$party_name = (($data->cal_agency == 0)?'In-House':(!empty($data->party_name)?$data->party_name:$data->cal_agency));
	
    $action = getActionButton($caliBtn);  
    return[$action,$data->sr_no,$party_name,$data->cal_certi_no,$download,$data->remark];
}
?>