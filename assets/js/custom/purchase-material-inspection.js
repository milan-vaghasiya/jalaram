$(document).ready(function(){
    $(document).on('click',".getInspectedMaterial",function(){
        var trans_id = $(this).data('trans_id');
        var grn_id = $(this).data('grn_id');
        var grn_no = $(this).data('grn_no');
        var grn_date = $(this).data('grn_date');
        var item_name = $(this).data('item_name');

        $("#grn_id").val(grn_id);
        $("#grnNo").val(grn_no);
        $("#grnDate").val(grn_date);
        $("#itemName").val(item_name);
        $.ajax({
            url:base_url + controller + '/getInspectedMaterial',
                type:'post',
                data:{id:trans_id},
                dataType:'json',
                success:function(data){
                    $("#recivedItems").html("");
                    $("#recivedItems").html(data);
                    $('.floatOnly').keypress(function(event) {
                        if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
                            event.preventDefault();
                        }
                    });
                }
        });
    });

    /*$(document).on('click', ".approveInspection", function() {
        var id = $(this).data('id');
        var val = $(this).data('val');
        var msg = $(this).data('msg');
        $('#id').val(id);

        $('#approveInspectionModel').modal();
        $.ajax({
            url: base_url + controller + '/getInspectionData',
            data: {id: id},
            type: "POST",
            dataType: "json",
            success: function(data) {
                $('#inspectionDataBody').html(data);
            }
        });
    });*/
    
    $(document).on('click',".rejectInspection",function(){
        var id = $(this).data('id');
        var val = $(this).data('val');
        var msg= $(this).data('msg');
        $.confirm({
            title: 'Confirm!',
            content: 'Are you sure want to '+ msg +' this Inspection?',
            type: 'red',
            buttons: {   
                ok: {
                    text: "ok!",
                    btnClass: 'btn waves-effect waves-light btn-outline-success',
                    keys: ['enter'],
                    action: function(){
                        $.ajax({
                            url: base_url + controller + '/rejectInspection',
                            data: {id:id,val:val,msg:msg},
                            type: "POST",
                            dataType:"json",
                            success:function(data)
                            {
                                if(data.status==0)
                                {
                                    toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
                                }
                                else
                                {
                                    initTable(1); 
                                    toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
                                }
                            }
                        });
                    }
                },
                cancel: {
                    btnClass: 'btn waves-effect waves-light btn-outline-secondary',
                    action: function(){
    
                    }
                }
            }
        });
    });
});

function inspectedMaterialSave(formId){
	var fd = $('#'+formId).serialize();
	$.ajax({
		url: base_url + controller + '/inspectedMaterialSave',
		data:fd,
		type: "POST",
		dataType:"json",
		success:function(data){
			if(data.status===0){
				$(".error").html("");
				$.each( data.message, function( key, value ) {$("."+key).html(value);});
			}else{
                initTable(); $("#inspectionModel").modal('hide');
				toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
			}
		}
	});
}

/*function saveApproveRemarks() {
    var approval_remarks = $("#approval_remarks").val();
    var id = $("#id").val();
    $.ajax({
        url: base_url + controller + '/approveInspection',
        data: {
            id: id,
            val: '1',
            msg: 'Approve',
            approval_remarks: approval_remarks
        },
        type: "POST",
        dataType: "json",
        success: function(data) {
            if (data.status == 0) {
                toastr.error(data.message, 'Sorry...!', {
                    "showMethod": "slideDown",
                    "hideMethod": "slideUp",
                    "closeButton": true,
                    positionClass: 'toastr toast-bottom-center',
                    containerId: 'toast-bottom-center',
                    "progressBar": true
                });
            } else {
                initTable(1); $(".modal").modal('hide'); $("#approval_remarks").val("");
                toastr.success(data.message, 'Success', {
                    "showMethod": "slideDown",
                    "hideMethod": "slideUp",
                    "closeButton": true,
                    positionClass: 'toastr toast-bottom-center',
                    containerId: 'toast-bottom-center',
                    "progressBar": true
                });
                //window.location.reload();
            }
        }
    });
}*/

function updateTestReport(data){
	var button = data.button;if(button == "" || button == null){button="both";}
	var fnEdit = data.fnEdit;if(fnEdit == "" || fnEdit == null){fnEdit="edit";}
	var fnsave = data.fnsave;if(fnsave == "" || fnsave == null){fnsave="save";}
	
	$.ajax({ 
		type: "POST",   
		url: base_url + controller + '/' + fnEdit,   
		data: {id:data.id}
	}).done(function(response){
		$("#"+data.modal_id).modal();
		$("#"+data.modal_id+' .modal-title').html(data.title);
		$("#"+data.modal_id+' .modal-body').html(response);
		$("#"+data.modal_id+" .modal-body form").attr('id',data.form_id);
		if(button == "close"){
			$("#"+data.modal_id+" .modal-footer .btn-close").show();
			$("#"+data.modal_id+" .modal-footer .btn-save").hide();
			$("#"+data.modalId+" .modal-footer .btn-save-close").hide();
		}
		$(".single-select").comboSelect();
		$("#"+data.modal_id+" .scrollable").perfectScrollbar({suppressScrollX: true});
		initMultiSelect();setPlaceHolder();
	});
}

function storeTestReport(formId,fnsave,srposition=1){
	setPlaceHolder();
	if(fnsave == "" || fnsave == null){fnsave="save";}
	var form = $('#'+formId)[0];
	var fd = new FormData(form);
	$.ajax({
		url: base_url + controller + '/' + fnsave,
		data:fd,
		type: "POST",
		processData:false,
		contentType:false,
		dataType:"json",
	}).done(function(data){
		if(data.status===0){
			$(".error").html("");
			$.each( data.message, function( key, value ) {$("."+key).html(value);});
		}else if(data.status==1){
			initTable(srposition); //$('#'+formId)[0].reset();$(".modal").modal('hide');
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		
            $('#agency_id').val(""); $('#agency_id').comboSelect();
            $('#name_of_agency').val("");
            $('#test_description').val(""); $('#test_description').comboSelect();
            $('#sample_qty').val("");
            $('#test_report_no').val("");
            $('#test_remark').val("");
            //$('#test_result').val("");
            $('#inspector_name').val("");
            $('#tc_file').val("");
            ;

            $('#testReportBody').html("");
            $('#testReportBody').html(data.tcReportData);
        
        }else{
			initTable(srposition); //$('#'+formId)[0].reset();$(".modal").modal('hide');
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}
				
	});
}

function trashTestReport(id,grn_trans_id,name='Record'){
	var send_data = { id:id,grn_trans_id:grn_trans_id };
	$.confirm({
		title: 'Confirm!',
		content: 'Are you sure want to delete this '+name+'?',
		type: 'red',
		buttons: {   
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function(){
					$.ajax({
						url: base_url + controller + '/deleteTestReport',
						data: send_data,
						type: "POST",
						dataType:"json",
						success:function(data)
						{
							if(data.status==0){
								toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}else{
                                $('#testReportBody').html("");
                                $('#testReportBody').html(data.tcReportData);
								toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}
						}
					});
				}
			},
			cancel: {
                btnClass: 'btn waves-effect waves-light btn-outline-secondary',
                action: function(){

				}
            }
		}
	});
}

function updateTR(formId,fnsave,srposition=1,modal_id) {
    setPlaceHolder();
    if(fnsave == "" || fnsave == null){fnsave="save";}
    var form = $('#'+formId)[0];
    var fd = new FormData(form);
    $.ajax({
        url: base_url + controller + '/' + fnsave,
        data:fd,
        type: "POST",
        processData:false,
        contentType:false,
        dataType:"json",
    }).done(function(data){
        if(data.status===0){
            $(".error").html("");
            $.each( data.message, function( key, value ) {$("."+key).html(value);});
        }else if(data.status==1){
            initTable(srposition); //$('#'+formId)[0].reset();$(".modal").modal('hide');
            toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
        
            $('#agency_id').val(""); 
            $('#agency_id').comboSelect();
            $('#name_of_agency').val("");
            $('#test_description').val(""); $('#test_description').comboSelect();
            $('#sample_qty').val("");
            $('#test_report_no').val("");
            $('#test_remark').val("");
            //$('#test_result').val("");
            $('#inspector_name').val("");
            $('#tc_file').val("");

            $("#"+modal_id).modal('hide');
            $('#testReportBody').html("");
            $('#testReportBody').html(data.tcReportData);
        
        }else{
            initTable(srposition); //$('#'+formId)[0].reset();$(".modal").modal('hide');
            toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
        }
                
    });
}

function editTestReport(data){

    var button = data.button;if(button == "" || button == null){button="both";}
    var fnEdit = data.fnEdit;if(fnEdit == "" || fnEdit == null){fnEdit="edit";}
    var fnsave = data.fnsave;if(fnsave == "" || fnsave == null){fnsave="save";}
    
    $.ajax({ 
        type: "POST",   
        url: base_url + controller + '/' + fnEdit,
        data: {id:data.id}
    }).done(function(response){
        $("#"+data.modal_id).modal();
        $("#"+data.modal_id).css("z-index", "1100");
        $("#"+data.modal_id+' .modal-title').html(data.title);
        $("#"+data.modal_id+' .modal-body').html(response);
        $("#"+data.modal_id+" .modal-body form").attr('id',data.form_id);
        $("#"+data.modal_id+" .modal-footer .btn-save").attr('onclick',"updateTR('"+data.form_id+"','"+fnsave+"',1,'"+data.modal_id+"');");
        $("#"+data.modal_id+" .modal-footer .btn-save-close").attr('onclick',"updateTR('"+data.form_id+"','"+fnsave+"','save_close');");
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