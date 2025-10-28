$(document).ready(function(){
    $("#vehicle_no").attr("autocomplete","off");
	$("#transport_namec").addClass("text-uppercase");
	$(document).on("change",".transport",function(){
		var transVal = $(this).val();
		var transId = $(this).children('option:selected').data('val');
		$("#transport_id").val(transId);
	});

	$('#vehicle_no').typeahead({
		source: function(query, result){
			$.ajax({
				url:base_url + 'ewaybill/vehicleSearch',
				method:"POST",
				global:false,
				data:{query:query},
				dataType:"json",
				success:function(data){result($.map(data, function(item){return item;}));}
			});
		}
	});
});

function ewaybill(data){
	var button = "";
	var fnEdit = data.fnEdit;if(fnEdit == "" || fnEdit == null){fnEdit="edit";}
	var fnsave = data.fnsave;if(fnsave == "" || fnsave == null){fnsave="save";}
	var fnonclick = data.fnonclick;if(fnonclick == "" || fnonclick == null){fnsave="store";}
	
	$.ajax({ 
		type: "POST",   
		url: base_url + 'ewaybill/' + fnEdit,   
		data: {id:data.id}
	}).done(function(response){
		$("#"+data.modal_id).modal();
		$("#"+data.modal_id+' .modal-title').html(data.title);
		$("#"+data.modal_id+' .modal-body').html(response);
		$("#"+data.modal_id+" .modal-body form").attr('id',data.form_id);
		$("#"+data.modal_id+" .modal-footer .btn-save").attr('onclick',fnonclick+"('"+data.form_id+"','"+fnsave+"');");
		$("#"+data.modal_id+" .modal-footer .btn-save-close").attr('onclick',fnonclick+"('"+data.form_id+"','"+fnsave+"','save_close');");
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

function generateNewEway(formId,fnsave,srposition=1){	
	setPlaceHolder();
	if(fnsave == "" || fnsave == null){fnsave="save";}
	var form = $('#'+formId)[0];
	var fd = new FormData(form);

	var transport_name = $("#transport_name").val();
	var vehicle_no = $("#vehicle_no").val();
	var ewbPreview = '<b>Transport :</b> ' + transport_name + '<br><b>Vehicle No. :</b> ' + vehicle_no + '<br><br>Are you sure to generate E-way Bill?';
	$.confirm({
		title: 'Confirm!',
		content: ewbPreview,
		type: 'green',
		buttons: {   
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function(){
					$.ajax({
						url: base_url + 'ewaybill/' + fnsave,
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
							initTable(1); $('#'+formId)[0].reset();$(".modal").modal('hide');
							toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
						}else{
							initTable(1);
							toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
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

function generateNewEinvoice(formId,fnsave,srposition=1){	
	setPlaceHolder();
	if(fnsave == "" || fnsave == null){fnsave="save";}
	var form = $('#'+formId)[0];
	var fd = new FormData(form);

	var einvPreview = 'Are you sure to generate E-Invoice?';
	$.confirm({
		title: 'Confirm!',
		content: einvPreview,
		type: 'green',
		buttons: {   
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function(){
					$.ajax({
						url: base_url + 'ewaybill/' + fnsave,
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
							initInvTable(); $('#'+formId)[0].reset();$(".modal").modal('hide');
							toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
						}else{
							initInvTable();
							toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
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