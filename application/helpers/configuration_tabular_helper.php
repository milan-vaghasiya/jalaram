<?php
    if (!defined('BASEPATH')) exit('No direct script access allowed');
/* get Pagewise Table Header */
function getConfigDtHeader($page)
{
    /* terms header */
    $data['terms'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['terms'][] = ["name"=>"#","style"=>"width:5%;"]; 
    $data['terms'][] = ["name"=>"Title"];
    $data['terms'][] = ["name"=>"Type"];
    $data['terms'][] = ["name"=>"Conditions"];
	
    /* Shift Header */
    $data['shift'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['shift'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['shift'][] = ["name"=>"Shift Name"];
	$data['shift'][] = ["name"=>"Start Time"];
	$data['shift'][] = ["name"=>"End Time"];
	$data['shift'][] = ["name"=>"Production Time"];
	$data['shift'][] = ["name"=>"Lunch Time"];
	$data['shift'][] = ["name"=>"Shift Hour"];
	
    /* Currency Header*/
    $data['currency'][] = ["name"=>"#","style"=>"width:5%;","srnoPosition"=>0];
    $data['currency'][] = ["name"=>"Currency Name"];
    $data['currency'][] = ["name"=>"Code"];
    $data['currency'][] = ["name"=>"Symbol"];
    $data['currency'][] = ["name"=>"Rate in INR"];
    
    /* Material Grade header */
    $data['materialGrade'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['materialGrade'][] = ["name"=>"#","style"=>"width:5%;"]; 
    $data['materialGrade'][] = ["name"=>"Material Grade"];
    $data['materialGrade'][] = ["name"=>"scrap Group"];
    $data['materialGrade'][] = ["name"=>"Colour Code"];
    $data['materialGrade'][] = ["name"=>"Density"];
	
    /* Attendance Policy Header */
    $data['attendancePolicy'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['attendancePolicy'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['attendancePolicy'][] = ["name"=>"Policy Name"];
    $data['attendancePolicy'][] = ["name"=>"Early In"];
    $data['attendancePolicy'][] = ["name"=>"No. Early In"];
    $data['attendancePolicy'][] = ["name"=>"Early Out"];
    $data['attendancePolicy'][] = ["name"=>"No. Early Out"];
    $data['attendancePolicy'][] = ["name"=>"Short Leave Hour"];
    $data['attendancePolicy'][] = ["name"=>"No. Short Leave"];
     
    /* Main Menu Header */
    $data['mainMenuConf'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['mainMenuConf'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
    $data['mainMenuConf'][] = ["name"=>"Menu Icon"];
    $data['mainMenuConf'][] = ["name"=>"Menu Name"];
    $data['mainMenuConf'][] = ["name"=>"Menu Sequence"];
    $data['mainMenuConf'][] = ["name"=>"Is Master"];
	
    /* Sub Menu Header */
    $data['subMenuConf'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['subMenuConf'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
    $data['subMenuConf'][] = ["name"=>"Sub Menu Sequence"];
    $data['subMenuConf'][] = ["name"=>"Sub Menu Icon"];
    $data['subMenuConf'][] = ["name"=>"Sub Menu Name"];
    $data['subMenuConf'][] = ["name"=>"Sub Menu Contoller Name"];
    $data['subMenuConf'][] = ["name"=>"Main Menu"];
    $data['subMenuConf'][] = ["name"=>"Is Report"];
	
    /* Tax Master Header */
    $data['taxMaster'][] = ["name" => "Action", "style" => "width:5%;"];
    $data['taxMaster'][] = ["name" => "#", "style" => "width:5%;"];
    $data['taxMaster'][] = ["name" => "Tax Name"];
    $data['taxMaster'][] = ["name" => "Tax Type"];
    $data['taxMaster'][] = ["name" => "Calcu. Type"];
    $data['taxMaster'][] = ["name" => "Ledger Name"];
    $data['taxMaster'][] = ["name" => "Is Active"];
    $data['taxMaster'][] = ["name" => "Add/Deduct"];
	
    /* Expense Master Header */
    $data['expenseMaster'][] = ["name" => "Action", "style" => "width:5%;"];
    $data['expenseMaster'][] = ["name" => "#", "style" => "width:5%;"];
    $data['expenseMaster'][] = ["name" => "Exp. Name"];
    $data['expenseMaster'][] = ["name" => "Entry Name"];
    $data['expenseMaster'][] = ["name" => "Sequence"];
    $data['expenseMaster'][] = ["name" => "Calcu. Type"];
    $data['expenseMaster'][] = ["name" => "Ledger Name"];
    $data['expenseMaster'][] = ["name" => "Is Active"];
    $data['expenseMaster'][] = ["name" => "Add/Deduct"];
    
	/* terms header */
	$data['contactDirectory'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['contactDirectory'][] = ["name"=>"#","style"=>"width:5%;"]; 
	$data['contactDirectory'][] = ["name"=>"Company Name"];
	$data['contactDirectory'][] = ["name"=>"Contact Person"];
	$data['contactDirectory'][] = ["name"=>"Contact No."];
	$data['contactDirectory'][] = ["name"=>"Email"];   
	$data['contactDirectory'][] = ["name"=>"Service"];
	$data['contactDirectory'][] = ["name"=>"Remark"];
   
    /* HSN Master header */
	$data['hsnMaster'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['hsnMaster'][] = ["name"=>"#","style"=>"width:5%;"]; 
	$data['hsnMaster'][] = ["name"=>"HSN Code"];
	$data['hsnMaster'][] = ["name"=>"GST Per."];
	$data['hsnMaster'][] = ["name"=>"Description"]; 
	
	/* Transport Header*/
    $data['transport'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['transport'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['transport'][] = ["name"=>"Transport Name"];
    $data['transport'][] = ["name"=>"Transport ID"];
    $data['transport'][] = ["name"=>"Address"];
	
    /* Banking Header*/
    $data['banking'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['banking'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['banking'][] = ["name"=>"Bank Name"];
    $data['banking'][] = ["name"=>"Branch Name"];
    $data['banking'][] = ["name"=>"IFSC Code"];
    $data['banking'][] = ["name"=>"Address"];
    
    /* category Header */
    $data['category'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['category'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['category'][] = ["name"=>"Category Name"];
    $data['category'][] = ["name"=>"Over Time"];

    /* visitors Header */
    $data['visitors'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['visitors'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['visitors'][] = ["name"=>"Whome To Meet"];
    $data['visitors'][] = ["name"=>"Visitor Name"];
    $data['visitors'][] = ["name"=>"Company Name"];
    $data['visitors'][] = ["name"=>"Contact No."];
    $data['visitors'][] = ["name"=>"Address"];
    $data['visitors'][] = ["name"=>"Purpose"];
    $data['visitors'][] = ["name"=>"Start/End"];
    $data['visitors'][] = ["name"=>"Entry/Exit"];
    $data['visitors'][] = ["name"=>"Total Duration","textAlign"=>"center"];
    $data['visitors'][] = ["name"=>"Status"];
    
    /* Auto Mail Header*/
    $data['autoMail'][] = ["name"=>"#","style"=>"width:5%;","srnoPosition"=>0];
    $data['autoMail'][] = ["name"=>"Menu Name"];
    $data['autoMail'][] = ["name"=>"E-mail"];

    /* Merge Item Header*/
    $data['mergeItem'][] = ["name"=>"#","style"=>"width:5%;","srnoPosition"=>0];
    $data['mergeItem'][] = ["name"=>"From Item"];
    $data['mergeItem'][] = ["name"=>"To Item"];
    
    /* Print Format Headre */
    $data['printFormat'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['printFormat'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['printFormat'][] = ["name"=>"Format Name"];
    $data['printFormat'][] = ["name"=>"Remark"];
	
    /* Dropdown Options Header*/
    $data['dropdownOptions'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['dropdownOptions'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['dropdownOptions'][] = ["name"=>"Type"];
    $data['dropdownOptions'][] = ["name"=>"Description"];
    $data['dropdownOptions'][] = ["name"=>"Remark"];
    
    /* Master Detail header */
    $data['masterDetail'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['masterDetail'][] = ["name"=>"#","style"=>"width:5%;"]; 
    $data['masterDetail'][] = ["name"=>"Title"];
    $data['masterDetail'][] = ["name"=>"Type"];
    $data['masterDetail'][] = ["name"=>"Remark"];

    /* Visit Purpose Header*/
    $data['visitPurpose'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['visitPurpose'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['visitPurpose'][] = ["name"=>"Purpose"];
    
	return tableHeader($data[$page]);
}

/* Terms Table Data */
function getTermsData($data){
    $deleteParam = $data->id.",'Terms'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editTerms', 'title' : 'Update Terms'}";
    $editButton = '<a class="btn btn-success btn-edit" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->title,str_replace(',',', ',$data->type),$data->conditions];
}

/* Currency Data */
function getCurrencyData($data){
    return [$data->sr_no,$data->currency_name,$data->currency,$data->code2000,$data->inrinput];
}
  
/* Material Grade Table Data */
function getMaterialData($data){
    $deleteParam = $data->id.",'Material Grade'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'edit', 'title' : 'Update Material Grade'}";
    $editButton = '<a class="btn btn-success btn-edit" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->material_grade,$data->group_name,$data->color_code,$data->density];
}

/* get Attendance Policy Data */
function getAttendancePolicyData($data){
    $deleteParam = $data->id.",'Attendance Policy'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editAttendancePolicy', 'title' : 'Update Attendance Policy'}";
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->policy_name,$data->early_in,$data->no_early_in,$data->early_out,$data->no_early_out,$data->short_leave_hour,$data->no_short_leave];
}

/* Main Menu Table Data */
function getMainMenuConfData($data){
    $deleteParam = $data->id.",'MainMenuConf'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editmainMenuConf', 'title' : 'Update MainMenuConf'}";
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->menu_icon,$data->menu_name,$data->menu_seq,$data->is_master];
}

/* Sub Menu Table Data */
function getSubMenuConfData($data){
    $deleteParam = $data->id.",'SubMenuConf'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editsubMenuConf', 'title' : 'Update SubMenuConf'}";
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	$isReport=($data->is_report == 0)?"No":"Yes";
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->sub_menu_seq,$data->sub_menu_icon,$data->sub_menu_name,$data->sub_controller_name,$data->menu_name,$isReport];
}

/* Expense Master Table Data */
function getExpenseMasterData($data){
    $deleteParam = $data->id.",'Expense'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editExpense', 'title' : 'Update Expense'}";
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    $action = getActionButton($editButton.$deleteButton);    
    return [$action,$data->sr_no,$data->exp_name,$data->entry_name,$data->seq,$data->calc_type_name,$data->party_name,$data->is_active_name,$data->add_or_deduct_name];
}

function getTaxMasterData($data){
    $deleteParam = $data->id.",'Tax'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editTax', 'title' : 'Update Tax'}";
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    $action = getActionButton($editButton.$deleteButton);    
    return [$action,$data->sr_no,$data->name,$data->tax_type_name,$data->calc_type_name,$data->acc_name,$data->is_active_name,$data->add_or_deduct_name];
}

function getContactDirectoryData($data){
    $deleteParam = $data->id.",'Contact'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editTerms', 'title' : 'Update Terms'}";
    $editButton = '<a class="btn btn-success btn-edit" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->comapny_name,$data->contact_person,$data->contact_number,$data->email,$data->service,$data->remark];
}
 
/* HSN Master Table Data */
function getHSNMasterData($data){
    $deleteParam = $data->id.",'HSN Master'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editHsnMaster', 'title' : 'HSN Master'}";
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->hsn_code,$data->gst_per,$data->description];
}

/* Transport Data */
function getTransportData($data){
	$deleteParam = $data->id.",'Transport'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editShift', 'title' : 'Update Transport'}";
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->transport_name,$data->transport_id,$data->address];
}

/* Banking Data */
function getBankingData($data){
	$deleteParam = $data->id.",'Banking'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editShift', 'title' : 'Update Banking Details'}";
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->bank_name,$data->branch_name,$data->ifsc_code,$data->address];
}

/** Visitor Data */
function getVisitorsData($data){
    $approveButton="";$rejectButton="";$startBtn = '';$endBtn='';
    if($data->wtm == $data->loginId && empty($data->approved_at) && empty($data->rejected_at)){
        $approveButton = '<a href="javascript:void(0)" class="btn btn-info approveVisit" data-id="'.$data->id.'" data-val="1" data-msg="Approve" datatip="Approve Visit" flow="down" ><i class="fa fa-check" ></i></a>';
        $rejectButton = '<a href="javascript:void(0)" class="btn btn-dark rejectVisit" data-id="'.$data->id.'" data-val="2" data-msg="Reject" datatip="Reject Visit" flow="down" ><i class="ti-close" ></i></a>';
    }
    if(!empty($data->approved_at) && empty($data->visit_start_time)){
        $startBtn = '<a href="javascript:void(0)" class="btn btn-info approveVisit" data-id="'.$data->id.'" data-val="3" data-msg="Start" datatip="Start Visit" flow="down" ><i class=" fas fa-hourglass-start"></i> </a>';

    }

    if(!empty($data->visit_start_time) && empty($data->visit_end_time)){
        $endBtn = '<a href="javascript:void(0)" class="btn btn-facebook approveVisit" data-id="'.$data->id.'" data-val="4" data-msg="End" datatip="Visit End" flow="down" ><i class=" fas fa-hourglass-end"></i></a>';

    }
    
    $action = getActionButton($approveButton.$rejectButton.$startBtn.$endBtn);
    $entry_exit = date("d-m-Y H:i:s",strtotime($data->created_at));
    $totalDuration='';
    if(!empty($data->exit_at)){
        $entry_exit .= '<hr style="margin:0rem;">'.date("d-m-Y H:i:s",strtotime($data->exit_at));
        $diff = date_diff(date_create($data->created_at),date_create($data->exit_at)); 
        $totalDuration =   sprintf("%02d",$diff->h).':'.sprintf("%02d",$diff->i);
        
    }

    $start_end = !empty($data->visit_start_time)?date("d-m-Y H:i:s",strtotime($data->visit_start_time)):'';
    if(!empty($data->visit_end_time)){
        $start_end .= '<hr style="margin:0rem;">'.date("d-m-Y H:i:s",strtotime($data->visit_end_time));
    }
    return [$action,$data->sr_no,$data->emp_name,$data->vname,$data->company_name,$data->contact_no,$data->address,$data->purpose,$start_end,$entry_exit,$totalDuration,$data->status_label];
}

/* Auto Mail Data */
function getAutoMailData($data){
    return [$data->sr_no,$data->name,$data->mailinput];
}

function getMergeItemData($data){
    return [$data->sr_no,$data->from_item_name,$data->to_item_name];
}

function getPrintFormatData($data){
    $deleteParam = $data->id.",'Print Format'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editCategory', 'title' : 'Update Print Format'}";
    $editButton = '<a class="btn btn-success btn-edit" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->format_name,$data->remark];
}


/* Banking Data */
function getDropdowngData($data){
	$deleteParam = $data->id.",'Dropdown Option'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editDropdown', 'title' : 'Update Dropdown Option'}";
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->dropdownType,$data->description,$data->remark];
}

/* Master Detail Table Data */
function getMasterDetailData($data){
    $deleteParam = $data->id.",'Master Detail'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editMasterDetail', 'title' : 'Master Detail'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->title,$data->typeName,$data->remark];
}


/* Visit Purpose Data */
function getVisitPurposeData($data){
	$deleteParam = $data->id.",'Scrap Group'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'edit', 'title' : 'Update Visit Purpose'}";
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->title];
}

?>