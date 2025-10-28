$(document).ready(function () {

    $(document).on("keyup",".partCount", function(){
        var startPartCount=parseFloat($("#start_part_count").val()) || 0;
        var prdQty=parseFloat($("#production_qty").val()) || 0;
        $("#end_part_count").val(startPartCount+prdQty);
     });

     
    $(document).on("change", "#rejection_stage", function () {

        var process_id = $(this).val();
        var part_id = $("#part_id").val();
        $("#rej_from").html("<option value=''>Select Rej. From</option>");
        $("#rej_from").comboSelect();
        if (process_id) {
            var job_card_id = $("#job_card_id").val();
            $.ajax({
                url: base_url  + 'production_v3/productionLog/getRejFrom',
                type: 'post',
                data: {
                    process_id: process_id,
                    part_id: part_id,
                    job_card_id: job_card_id
                },
                dataType: 'json',
                success: function (data) {
                    $("#rej_from").html("");
                    $("#rej_from").html(data.rejOption);
                    $("#rej_from").comboSelect();


                }
            });
        } 
    });

    $(document).on("change", "#rework_stage", function () {
        var process_id = $(this).val();
        var part_id = $("#part_id").val();
        $("#rw_from").html("<option value=''>Select Rew. From</option>");
            $("#rw_from").comboSelect();
        if (process_id) {
            var job_card_id = $("#job_card_id").val();
            $.ajax({
                url: base_url + 'production_v3/productionLog/getRejFrom',
                type: 'post',
                data: {
                    process_id: process_id,
                    part_id: part_id,
                    job_card_id: job_card_id
                },
                dataType: 'json',
                success: function (data) {

                    $("#rw_from").html("");
                    $("#rw_from").html(data.rewOption);
                    $("#rw_from").comboSelect();
                }
            });
        }
    });

 
    $(document).on('click', "#addReworkRow", function () {
        var rw_qty = $("#rw_qty").val();
        var rw_reason = $("#rw_reason :selected").val();
        var rw_from = $("#rw_from :selected").val();
        var rw_reason_code = $("#rw_reason :selected").data('code');
        var rework_reason = $("#rw_reason :selected").data('reason');
        var rw_party_name = $("#rw_from :selected").data('party_name');
        var rw_remark = $("#rw_remark").val();
        var rw_stage = $("#rework_stage").val();
        var rw_stage_name = $("#rework_stage :selected").data('process_name');
        var row_index = $('#reworkReason tbody').find('tr').length;

        var valid = 1;

        $(".rw_qty").html("");
        if (parseFloat(rw_qty) <= 0 || rw_qty == '') {
            $(".rw_qty").html("Rework Qty is required.");
            valid = 0;
        }

        $(".rw_reason").html("");
        if (rw_reason == "") {
            $(".rw_reason").html("Rework Reason is required.");
            valid = 0;
        }

        $(".rw_from").html("");
        if (rw_from == "") {
            $(".rw_from").html("Rework From is required.");
            valid = 0;
        }

        $(".rework_stage").html("");
        if (rw_stage == "") {
            $(".rework_stage").html("Rework Belongs to is required.");
            valid = 0;
        }

        if (valid == 1) {

            var postData = {
                rw_qty: rw_qty,
                rw_reason: rw_reason,
                rw_from: rw_from,
                rw_reason_code: rw_reason_code,
                rework_reason: rework_reason,
                rw_remark: rw_remark,
                rw_party_name: rw_party_name,
                rw_stage: rw_stage,
                rw_stage_name: rw_stage_name,
                row_index: row_index,
                trans_id: ''
            };


            AddRowRework(postData);
            $("#rw_qty").val("0");
            $("#rw_reason").val("");
            $("#rw_reason").comboSelect();
            $("#rw_from").val("");
            $("#rw_from").comboSelect();
            $("#rework_stage").val("");
            $("#rework_stage").comboSelect();
            $("#rw_remark").val("");
            $("#rw_qty").focus();

        }
    });
    
    $(document).on('click', "#addRejectionRow", function () {
        var rej_qty = $("#rej_qty").val();
        var rej_ref_id = $("#rej_ref_id").val();
        var rej_type = $("#rej_type").val();
        var rej_by = $("#rej_by :selected").val();
        var rej_reason = $("#rej_reason :selected").val();
        var rej_from = $("#rej_from :selected").val();
        var rej_reason_code = $("#rej_reason :selected").data('code');
        var rejection_reason = $("#rej_reason :selected").data('reason');
        var rej_party_name = $("#rej_from :selected").data('party_name');
        var rej_remark = $("#rej_remark").val();
        var rej_stage = $("#rejection_stage").val();
        var rej_stage_name = $("#rejection_stage :selected").data('process_name');

        var valid = 1;

        $(".rej_qty").html("");
        if (parseFloat(rej_qty) <= 0 || rej_qty == '') {
            $(".rej_qty").html("Rejection Qty is required.");
            valid = 0;
        }

        $(".rej_reason").html("");
        if (rej_reason == "") {
            $(".rej_reason").html("Rejection Reason is required.");
            valid = 0;
        }

        $(".rej_from").html("");
        if (rej_from == "") {
            $(".rej_from").html("Rejection From is required.");
            valid = 0;
        }

        $(".rejection_stage").html("");
        if (rej_stage == "") {
            $(".rejection_stage").html("Rejection Belongs is required.");
            valid = 0;
        }

        if (valid == 1) {
            var postData = {
                rej_qty: rej_qty,
                rej_by:rej_by,
                rej_reason: rej_reason,
                rej_from: rej_from,
                rej_reason_code: rej_reason_code,
                rejection_reason: rejection_reason,
                rej_remark: rej_remark,
                rej_party_name: rej_party_name,
                rej_stage: rej_stage,
                rej_stage_name: rej_stage_name,
                trans_id: '',
                rej_ref_id: rej_ref_id,
                rej_type: rej_type,

            };
            AddRowRejection(postData);
            $("#rej_qty").val("0");
            $("#rej_reason").val("");
            $("#rej_reason").comboSelect();
            $("#rej_from").val("");
            $("#rej_from").comboSelect();
            $("#rejection_stage").val("");
            $("#rejection_stage").comboSelect();
            $("#rej_remark").val("");
            $("#rej_type").val(0);
            $("#rej_ref_id").val(0);
            $("#rej_qty").focus();

        }
    });

    $(document).on('click',"#addIdleRow",function(){
        var idle_time = $("#idle_time").val();
        var idle_reason_id = $("#idle_reason").val();
        var idle_reason_code = $("#idle_reason :selected").data('code');
        var idle_reason = $("#idle_reason :selected").data('reason');

        var valid = 1;

        $(".idle_time").html("");
        if(parseFloat(idle_time) <= 0){
            $(".idle_time").html("Idle Time is required.");valid=0;
        }

        $(".idle_reason").html("");
        if(idle_reason_id == ""){
            $(".idle_reason").html("Idle Reason is required.");valid=0;
        }

        if(valid == 1){
            var postData = {idle_time:idle_time,idle_reason_id:idle_reason_id,idle_reason_code:idle_reason_code,idle_reason:idle_reason};
            AddRowIdle(postData);
            $("#idle_time").val("0");
            $("#idle_reason").val("");
            $("#idle_reason").comboSelect();
            $("#idle_time").focus();
        }
    });

    $(document).on("keyup", ".qtyCal", function() {
        var rejSum = 0;
        $(".rej_sum").each(function() {
            rejSum += parseFloat($(this).val()) || 0;
        });
        var rwSum = 0;
        $(".rw_sum").each(function() {
            rwSum += parseFloat($(this).val()) || 0;
        });

        var production_qty = parseFloat($("#production_qty").val()) || 0;
        var hold_qty = parseFloat($("#hold_qty").val()) || 0;
        //var okQty = parseFloat($("#production_qty").val()) + rejSum + rwSum;
        
        if($("#prod_type").val() == 2){
            
            var ok_qty = parseFloat($("#ok_qty").val()) || 0;
            var prodQty = ok_qty + (rejSum + rwSum);
            $("#production_qty").val(prodQty);
        }else{
            var prodQty = production_qty - (rejSum + rwSum+hold_qty);
            $("#ok_qty").val(prodQty);
        }
        

        
    });

});

// For Rework Data
function AddRowRework(data) {

    $('table#reworkReason tr#noData').remove();

    //Get the reference of the Table's TBODY element.
    var tblName = "reworkReason";

    var tBody = $("#" + tblName + " > TBODY")[0];
    var index = $('#' + tblName + ' tbody tr:last').index();
    if (data.row_index != "") {
        var trRow = data.row_index;
        $("#" + tblName + " tbody tr:eq(" + trRow + ")").remove();
        index = data.row_index;
    }

    // var index = (data.row_index == "") ? -1 : data.row_index;
    // row = tBody.insertRow(index);

    //Add index cell
    // console.log(data.row_index);
    // var countRow = (data.row_index == "") ? ($('#' + tblName + ' tbody tr:last').index() + 1) : (parseInt(data.row_index) + 1);

    row = tBody.insertRow(-1);

    var countRow = $('#' + tblName + ' tbody tr:last').index() + 1;
    var cell = $(row.insertCell(-1));
    cell.html(countRow);
    cell.attr("style", "width:5%;");

    var rework_qty_input = $("<input/>", {
        type: "hidden",
        name: "rework_reason[" + countRow + "][rw_qty]",
        value: data.rw_qty,
        class: "rw_sum"
    });
    cell = $(row.insertCell(-1));

    // cell.html('<a href="">'+data.rw_qty+'</a>');
    var rwQty = $('<a href="javascript:void(0)">' + data.rw_qty + '</a>');
    rwQty.attr("onclick", "convertToOKQty(" + JSON.stringify(data) + ",this);");
    cell.html(data.rw_qty);
    // cell.html("<a  class='convertToOKQty' data-rowData='"+JSON.stringify(data)+"' data-row='1' datatip='Convert To OK'  flow='down'  >"+data.rw_qty+"</a>");
    cell.append(rework_qty_input);
    cell.attr("style", "width:20%;");

    var rw_reason_input = $("<input/>", {
        type: "hidden",
        name: "rework_reason[" + countRow + "][rw_reason]",
        value: data.rw_reason
    });

    var transIdinput = $("<input/>", {
        type: "hidden",
        name: "rework_reason[" + countRow + "][trans_id]",
        value: data.trans_id
    });
    cell = $(row.insertCell(-1));
    cell.html(data.rework_reason);
    cell.append(rw_reason_input);
    cell.append(transIdinput);
    cell.attr("style", "width:20%;");

    var rw_stage_input = $("<input/>", {
        type: "hidden",
        name: "rework_reason[" + countRow + "][rw_stage]",
        value: data.rw_stage
    });
    var rw_stage_name_input = $("<input/>", {
        type: "hidden",
        name: "rework_reason[" + countRow + "][rw_stage_name]",
        value: data.rw_stage_name
    });
    cell = $(row.insertCell(-1));
    cell.html(data.rw_stage_name);
    cell.append(rw_stage_input);
    cell.append(rw_stage_name_input);
    cell.attr("style", "width:20%;");

    var rw_from_input = $("<input/>", {
        type: "hidden",
        name: "rework_reason[" + countRow + "][rw_from]",
        value: data.rw_from
    });
    var rw_party_name_input = $("<input/>", {
        type: "hidden",
        name: "rework_reason[" + countRow + "][rw_party_name]",
        value: data.rw_party_name
    });
    cell = $(row.insertCell(-1));
    cell.html(data.rw_party_name);
    cell.append(rw_from_input);
    cell.append(rw_party_name_input);
    // cell.attr("style", "width:20%;");

    var rw_remark_input = $("<input/>", {
        type: "hidden",
        name: "rework_reason[" + countRow + "][rw_remark]",
        value: data.rw_remark
    });
    var rework_reason_input = $("<input/>", {
        type: "hidden",
        name: "rework_reason[" + countRow + "][rework_reason]",
        value: data.rework_reason
    });
    cell = $(row.insertCell(-1));
    cell.html(data.rw_remark);
    cell.append(rw_remark_input);
    cell.append(rework_reason_input);
    cell.attr("style", "width:20%;");

    //Add Button cell.


    cell = $(row.insertCell(-1));
    var btnOk = "";
    var btnRej = "";
    // if (data.trans_id != '' || data.trans_id != 0) {
    //     btnOk = $('<button><i class="ti-check"></i></button>');
    //     btnOk.attr("type", "button");
    //     btnOk.attr("onclick", "convertToOKQty(" + JSON.stringify(data) + ",this);");
    //     btnOk.attr("style", "margin-left:2px;");
    //     btnOk.attr("class", "btn btn-outline-success waves-effect waves-light");

    //     btnRej = $('<button><i class="ti-close"></i></button>');
    //     btnRej.attr("type", "button");
    //     btnRej.attr("onclick", "addRejQty(" + JSON.stringify(data) + ");");
    //     btnRej.attr("style", "margin-left:2px;");
    //     btnRej.attr("class", "btn btn-outline-warning waves-effect waves-light");
    // }

    var btnRemove = $('<button><i class="ti-trash"></i></button>');
    btnRemove.attr("type", "button");
    btnRemove.attr("onclick", "RemoveRework(this);");
    btnRemove.attr("style", "margin-left:2px;");
    btnRemove.attr("class", "btn btn-outline-danger waves-effect waves-light");

    // cell.append(btnOk);
    // cell.append(btnRej);
    cell.append(btnRemove);
    cell.attr("class", "text-center");
    cell.attr("style", "width:5%;");

    $(".qtyCal").trigger('keyup');
}

function RemoveRework(button) {
    //Determine the reference of the Row using the Button.
    var row = $(button).closest("TR");
    var table = $("#reworkReason")[0];
    table.deleteRow(row[0].rowIndex);
    $('#idleReasons tbody tr td:nth-child(1)').each(function (idx, ele) {
        ele.textContent = idx + 1;
    });
    var countTR = $('#idleReasons tbody tr:last').index() + 1;
    if (countTR == 0) {
        $("#idleReasonData").html('<tr id="noData"><td colspan="6" class="text-center">No data available in table</td></tr>');
    }
    $(".qtyCal").trigger('keyup');
};

// For Rejection Data
function AddRowRejection(data) {
    //console.log(data.rej_qty);
    $('table#rejectionReason tr#noData').remove();

    //Get the reference of the Table's TBODY element.
    var tblName = "rejectionReason";

    var tBody = $("#" + tblName + " > TBODY")[0];
    row = tBody.insertRow(-1);

    var index = $('#' + tblName + ' tbody tr:last').index();
    var countRow = $('#' + tblName + ' tbody tr:last').index() + 1;
    var cell = $(row.insertCell(-1));
    cell.html(countRow);
    cell.attr("style", "width:5%;");

    var rejection_qty_input = $("<input/>", {
        type: "hidden",
        name: "rejection_reason[" + index + "][rej_qty]",
        value: data.rej_qty,
        class: "rej_sum"
    });

    var rejection_ref_input = $("<input/>", {
        type: "hidden",
        name: "rejection_reason[" + index + "][rej_ref_id]",
        value: data.rej_ref_id,

    });

    var rejection_type_input = $("<input/>", {
        type: "hidden",
        name: "rejection_reason[" + index + "][rej_type]",
        value: data.rej_type,

    });

    var rejection_qty_input = $("<input/>", {
        type: "hidden",
        name: "rejection_reason[" + index + "][rej_qty]",
        value: data.rej_qty,
        class: "rej_sum"
    });
    cell = $(row.insertCell(-1));
    cell.html(data.rej_qty);
    cell.append(rejection_qty_input);
    cell.append(rejection_ref_input);
    cell.append(rejection_type_input);
    cell.attr("style", "width:20%;");

    var rej_by_input = $("<input/>", {
        type: "hidden",
        name: "rejection_reason[" + index + "][rej_by]",
        value: data.rej_by
    });
    var rej_reason_input = $("<input/>", {
        type: "hidden",
        name: "rejection_reason[" + index + "][rej_reason]",
        value: data.rej_reason
    });
    var transIdinput = $("<input/>", {
        type: "hidden",
        name: "rejection_reason[" + index + "][trans_id]",
        value: data.trans_id
    });
    cell = $(row.insertCell(-1));
    cell.html(data.rejection_reason);
    cell.append(rej_reason_input);
    cell.append(rej_by_input);
    cell.append(transIdinput);
    cell.attr("style", "width:20%;");

    var rej_stage_input = $("<input/>", {
        type: "hidden",
        name: "rejection_reason[" + index + "][rej_stage]",
        value: data.rej_stage
    });
    var rej_stage_name_input = $("<input/>", {
        type: "hidden",
        name: "rejection_reason[" + index + "][rej_stage_name]",
        value: data.rej_stage_name
    });
    cell = $(row.insertCell(-1));
    cell.html(data.rej_stage_name);
    cell.append(rej_stage_input);
    cell.append(rej_stage_name_input);
    cell.attr("style", "width:20%;");

    var rej_from_input = $("<input/>", {
        type: "hidden",
        name: "rejection_reason[" + index + "][rej_from]",
        value: data.rej_from
    });
    var rej_party_name_input = $("<input/>", {
        type: "hidden",
        name: "rejection_reason[" + index + "][rej_party_name]",
        value: data.rej_party_name
    });
    cell = $(row.insertCell(-1));
    cell.html(data.rej_party_name);
    cell.append(rej_from_input);
    cell.append(rej_party_name_input);
    cell.attr("style", "width:20%;");

    var rej_remark_input = $("<input/>", {
        type: "hidden",
        name: "rejection_reason[" + index + "][rej_remark]",
        value: data.rej_remark
    });
    var rejection_reason_input = $("<input/>", {
        type: "hidden",
        name: "rejection_reason[" + index + "][rejection_reason]",
        value: data.rejection_reason
    });
    cell = $(row.insertCell(-1));
    cell.html(data.rej_remark);
    cell.append(rej_remark_input);
    cell.append(rejection_reason_input);
    cell.attr("style", "width:20%;");

    //Add Button cell.
    cell = $(row.insertCell(-1));


    var btnRemove = $('<button><i class="ti-trash"></i></button>');
    btnRemove.attr("type", "button");
    btnRemove.attr("onclick", "RemoveRejection(this);");
    btnRemove.attr("style", "margin-left:4px;");
    btnRemove.attr("class", "btn btn-outline-danger waves-effect waves-light");


    cell.append(btnRemove);
    cell.attr("class", "text-center");
    cell.attr("style", "width:15%;");

    $(".qtyCal").trigger('keyup');
}

function RemoveRejection(button) {

    //Determine the reference of the Row using the Button.
    var row = $(button).closest("TR");
    var table = $("#rejectionReason")[0];
    table.deleteRow(row[0].rowIndex);
    $('#idleReasons tbody tr td:nth-child(1)').each(function (idx, ele) {
        ele.textContent = idx + 1;
    });
    var countTR = $('#idleReasons tbody tr:last').index() + 1;
    if (countTR == 0) {
        $("#idleReasonData").html('<tr id="noData"><td colspan="6" class="text-center">No data available in table</td></tr>');
    }
    $(".qtyCal").trigger('keyup');
};

function openLogForm(data){
	var button = "";
	var button = data.button;if(button == "" || button == null){button="both";}
	var fnEdit = data.fnEdit;if(fnEdit == "" || fnEdit == null){fnEdit="edit";}
	var fnsave = data.fnsave;if(fnsave == "" || fnsave == null){fnsave="save";}
	
	$.ajax({ 
		type: "POST",   
		url: base_url + controller + '/' + fnEdit,   
		data: {id:data.id,log_type:data.log_type}
	}).done(function(response){
		$("#"+data.modal_id).modal();
		$("#"+data.modal_id+' .modal-title').html(data.title);
		$("#"+data.modal_id+' .modal-body').html(response);
		$("#"+data.modal_id+" .modal-body form").attr('id',data.form_id);
		$("#"+data.modal_id+" .modal-footer .btn-save").attr('onclick',"store('"+data.form_id+"','"+fnsave+"');");
		$("#"+data.modal_id+" .modal-footer .btn-save-close").attr('onclick',"store('"+data.form_id+"','"+fnsave+"','save_close');");
		if(button == "close"){
			$("#"+data.modal_id+" .modal-footer .btn-close").show();
			$("#"+data.modal_id+" .modal-footer .btn-save").hide();
			$("#"+data.modalId+" .modal-footer .btn-save-close").hide();
		}else if(button == "save"){
			$("#"+data.modal_id+" .modal-footer .btn-close").hide();
			$("#"+data.modal_id+" .modal-footer .btn-save").show();
            $("#"+data.modalId+" .modal-footer .btn-save-close").show();
		}else{
			$("#"+data.modal_id+" .modal-footer .btn-close").show();
			$("#"+data.modal_id+" .modal-footer .btn-save").show();
            $("#"+data.modalId+" .modal-footer .btn-save-close").show();
		}
		$(".single-select").comboSelect();
		$("#"+data.modal_id+" .scrollable").perfectScrollbar({suppressScrollX: true});
		initMultiSelect();setPlaceHolder();
	});
}

// For Idle Data
function AddRowIdle(data){
    $('table#idleReasons tr#noData').remove();

    //Get the reference of the Table's TBODY element.
	var tblName = "idleReasons";
	
	var tBody = $("#"+tblName+" > TBODY")[0];
    row = tBody.insertRow(-1);

    var index =  $('#'+tblName+' tbody tr:last').index();
    var countRow = $('#'+tblName+' tbody tr:last').index() + 1;
    var cell = $(row.insertCell(-1));
	cell.html(countRow);
	cell.attr("style","width:5%;");

    var idle_time_input = $("<input/>",{type:"hidden",name:"idle_reason["+index+"][idle_time]",value:data.idle_time});
    cell = $(row.insertCell(-1));
	cell.html(data.idle_time);
	cell.append(idle_time_input);
    cell.attr("style","width:20%;");

    var idle_reason_id_input = $("<input/>",{type:"hidden",name:"idle_reason["+index+"][idle_reason_id]",value:data.idle_reason_id});
    var idle_reason_code_input = $("<input/>",{type:"hidden",name:"idle_reason["+index+"][idle_reason_code]",value:data.idle_reason_code});
    var idle_reason_input = $("<input/>",{type:"hidden",name:"idle_reason["+index+"][idle_reason]",value:data.idle_reason});
    cell = $(row.insertCell(-1));
	cell.html("["+data.idle_reason_code+"] - "+data.idle_reason);
	cell.append(idle_reason_id_input);
	cell.append(idle_reason_code_input);
	cell.append(idle_reason_input);

    //Add Button cell.
	cell = $(row.insertCell(-1));
	var btnRemove = $('<button><i class="ti-trash"></i></button>');
	btnRemove.attr("type", "button");
	btnRemove.attr("onclick", "RemoveIdle(this);");
    btnRemove.attr("style","margin-left:4px;");
	btnRemove.attr("class", "btn btn-outline-danger waves-effect waves-light");
    cell.append(btnRemove);
    cell.attr("class","text-center");
	cell.attr("style","width:10%;");
}

function RemoveIdle(button) {
	//Determine the reference of the Row using the Button.
	var row = $(button).closest("TR");
	var table = $("#idleReasons")[0];
	table.deleteRow(row[0].rowIndex);
	$('#idleReasons tbody tr td:nth-child(1)').each(function(idx, ele) {
        ele.textContent = idx + 1;
    });
	var countTR = $('#idleReasons tbody tr:last').index() + 1;
	if(countTR == 0){
		$("#idleReasonData").html('<tr id="noData"><td colspan="4" class="text-center">No data available in table</td></tr>');
	}	
};

function editLog(data){
	var button = "";
	var button = data.button;if(button == "" || button == null){button="both";}
	var fnEdit = data.fnEdit;if(fnEdit == "" || fnEdit == null){fnEdit="edit";}
	var fnsave = data.fnsave;if(fnsave == "" || fnsave == null){fnsave="save";}
	
	$.ajax({ 
		type: "POST",   
		url: base_url + controller + '/' + fnEdit,   
		data: {id:data.id,log_type:data.log_type}
	}).done(function(response){
		$("#"+data.modal_id).modal();
		$("#"+data.modal_id+' .modal-title').html(data.title);
		$("#"+data.modal_id+' .modal-body').html(response);
		$("#"+data.modal_id+" .modal-body form").attr('id',data.form_id);
		$("#"+data.modal_id+" .modal-footer .btn-save").attr('onclick',"store('"+data.form_id+"','"+fnsave+"');");
		$("#"+data.modal_id+" .modal-footer .btn-save-close").attr('onclick',"store('"+data.form_id+"','"+fnsave+"','save_close');");
		if(button == "close"){
			$("#"+data.modal_id+" .modal-footer .btn-close").show();
			$("#"+data.modal_id+" .modal-footer .btn-save").hide();
			$("#"+data.modalId+" .modal-footer .btn-save-close").hide();
		}else if(button == "save"){
			$("#"+data.modal_id+" .modal-footer .btn-close").hide();
			$("#"+data.modal_id+" .modal-footer .btn-save").show();
            $("#"+data.modalId+" .modal-footer .btn-save-close").show();
		}else{
			$("#"+data.modal_id+" .modal-footer .btn-close").show();
			$("#"+data.modal_id+" .modal-footer .btn-save").show();
            $("#"+data.modalId+" .modal-footer .btn-save-close").show();
		}
		$(".single-select").comboSelect();
		$("#"+data.modal_id+" .scrollable").perfectScrollbar({suppressScrollX: true});
		initMultiSelect();setPlaceHolder();
	});
}
