<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
					<div class="card-header">
                        <div class="row">
							<div class="col-md-12 form-group">
                                <h4 class="card-title text-center">Outsource</h4>
                            </div>         
                        </div>     
						<hr style="margin:0.5px;">
						<div class="row">
							<div class="col-md-6 form-group">
								<ul class="nav nav-pills">
									<li class="nav-item"><a href="<?=base_url('production_v3/jobWorkVendor/pendingChallan')?>" class="btn btn-outline-info mr-1" style="border-radius:0px;">Pending</a></li>
                                    <li class="nav-item"> <a href="<?=base_url('production_v3/jobWorkVendor/vendorReceiveIndex')?>" class="btn waves-effect waves-light btn-outline-info  float-right mr-1">Vendor Receive</a> </li>
                                    <li class="nav-item"> <button onclick="statusTab('jobWorkTable',0);" class=" btn waves-effect waves-light btn-outline-info <?=(empty($status)?'active':'')?> mr-1" style="outline:0px" data-toggle="tab" aria-expanded="false " data-status="0">In process</button> </li>
                                    <li class="nav-item"> <button onclick="statusTab('jobWorkTable',1);" class=" btn waves-effect waves-light btn-outline-info <?=(!empty($status)?'active':'')?>" style="outline:0px" data-toggle="tab" aria-expanded="false" data-status="1">Completed</button> </li>
								</ul>
							</div>
							<div class="col-md-6 form-group">
                                <div class="input-group">
									<select id="vendor_id_filter" class="form-control single-select" style="width:20%">
										<option value="">Select All</option>
										<?php foreach ($vendorData as $row) :
												echo '<option value="' . $row->id . '">' . $row->party_name . '</option>';
											endforeach; 
										?>
									</select>
									<input type="date" name="from_date" id="from_date" class="form-control" value="<?=date('Y-m-01')?>" max="<?=$maxDate?>" />
									<div class="error fromDate"></div>
									<input type="date" name="to_date" id="to_date" class="form-control" value="<?=date('Y-m-d')?>" max="<?=$maxDate?>" />
									<div class="input-group-append">
										<button type="button" class="btn waves-effect waves-light btn-success float-right loaddata"><i class="fas fa-sync-alt"></i> Load</button>
									</div>
								</div>
							</div>  
						</div>                                    
                    </div>
					<!--<div class="row">
						<div class="col-md-12">
							<h4 class="card-title text-center">Job Work Vendor (New)</h4>
						</div> 
						<hr style="width:100%">
						<div class="col-md-4">
							<ul class="nav nav-pills">
								<li class="nav-item"> <button onclick="loadDataTable(0);" data-status="0" class=" btn waves-effect waves-light btn-outline-info active" style="outline:0px" data-toggle="tab" aria-expanded="false">Pending</button> </li>
								<li class="nav-item"> <button onclick="loadDataTable(1);" data-status="1" class=" btn waves-effect waves-light btn-outline-success" style="outline:0px" data-toggle="tab" aria-expanded="false">Completed</button> </li>
							</ul>
						</div>
							
						<div class="col-md-8">
							<div class="input-group">
								<select id="vendor_id_filter" class="form-control single-select" style="width:20%">
									<option value="">Select All</option>
									<?php 
										/*foreach ($vendorData as $row) :
											echo '<option value="' . $row->id . '">' . $row->party_name . '</option>';
										endforeach;*/
									?>
								</select>
								<input type="date" name="from_date" id="from_date" class="form-control" value="<?=date('Y-m-01')?>" min="<?=$startYearDate?>" max="<?=$maxDate?>" />
								<div class="error fromDate"></div>
								<input type="date" name="to_date" id="to_date" class="form-control" value="<?=date('Y-m-d')?>" min="<?=$startYearDate?>" max="<?=$maxDate?>" />
								<div class="input-group-append">
									<button type="button" class="btn waves-effect waves-light btn-success float-right loaddata"><i class="fas fa-sync-alt"></i> Load</button>
								</div>
							</div>
						</div>               
					</div>-->
                    <div class="card-body">
                        <input type="hidden" id="process_id" value="">
                        <div class="table-responsive">
                            <table id='jobWorkTable' class="table table-bordered ssTable" data-url='/getDTRows/<?=$status?>/<?=date('Y-m-01')."~".date('Y-m-d')?>'></table></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url(); ?>assets/js/custom/e-bill.js?v=<?= time() ?>"></script>
<script>
$(document).ready(function(){
	productionTable();

	$(document).on('change',"#vendor_id",function(){
		var vendor_id = $(this).val();
		var product_id = $("#product_id").val();
		var process_id = $("#out_process_id").val();
		var job_card_id = $("#outWard #job_card_id").val();
		if(vendor_id == "0"){
			$("#job_order_id").html('<option value="">Select Job Order No.</option>');
			$("#job_order_id").comboSelect();
			//$("#jobProcessSelect").html("");
			$("#job_process_ids").val("");
			$("#job_process_ids").comboSelect();
			//reInitMultiSelect();
		}else{
			$.ajax({
				url: base_url + 'production_v2/processApproval/getJobWorkOrderNoList',
				type:'post',
				data:{vendor_id:vendor_id,product_id:product_id,process_id:process_id},
				dataType:'json',
				success:function(data){
					$("#job_order_id").html("");
					$("#job_order_id").html(data.options);
					$("#job_order_id").comboSelect();
				}
			});

			$.ajax({
				url: base_url + 'production_v2/processApproval/getJobWorkOrderProcessList',
				type:'post',
				data:{job_order_id:"",process_id:process_id,job_card_id:job_card_id,vendor_id:vendor_id},
				dataType:'json',
				success:function(data){
					/* $("#job_process_ids").val(data.job_process);
					$("#jobProcessSelect").html("");
					$("#jobProcessSelect").html(data.options);
					reInitMultiSelect(); */

					$("#job_process_ids").html("");
					$("#job_process_ids").html(data.options);
					$("#job_process_ids").comboSelect();
				}
			});
		}
	});


    $(document).on('click','.loaddata',function(){ loadDataTable($('.nav-item .active').data('status')); });
	
	$(document).on('click','.btn-close',function(){
		initTable();
	});
	
	$(document).on('click','.close',function(){
		initTable();
	});
	
});

function loadDataTable(status){
    var fdate = $('#from_date').val();
    var tdate = $('#to_date').val();
	var vendor_id = $('#vendor_id_filter').val();
    $("#jobWorkTable").attr("data-url",'/getDTRows/'+status+'/'+fdate+'~'+tdate+'/'+vendor_id);
    ssTable.state.clear();initTable();
}

function productionTable(){
    var table = $('#productionTable').DataTable( {
		lengthChange: false,
		responsive: true,
		ordering: true,
		//'stateSave':true,
        'pageLength': 25,
		buttons: ['pageLength', 'copy', 'excel', 'colvis' ]
	});
	table.buttons().container().appendTo( '#productionTable_wrapper .col-md-6:eq(0)' );
}

function outward(data){
	var button = data.button;
	$.ajax({ 
		type: "POST",   
		url: base_url + 'production_v3/jobWorkVendor/addVendorLog',
		data: {id:data.id,ch_trans_id:data.ch_trans_id,challan_id:data.challan_id}
	}).done(function(response){
		$("#"+data.modal_id).modal();
		$("#"+data.modal_id+' .modal-title').html(data.title);
		$("#"+data.modal_id+' .modal-body').html(response);
		$("#"+data.modal_id+" .modal-body form").attr('id',data.form_id);
		$("#"+data.modal_id+" .modal-footer .btn-save").attr('onclick',"saveOutward('"+data.form_id+"');");
		if(data.button == "close"){
			$("#"+data.modal_id+" .modal-footer .btn-close").show();
			$("#"+data.modal_id+" .modal-footer .btn-save").hide();
		}else if(data.button == "save"){
			$("#"+data.modal_id+" .modal-footer .btn-close").hide();
			$("#"+data.modal_id+" .modal-footer .btn-save").show();
		}else{
			$("#"+data.modal_id+" .modal-footer .btn-close").show();
			$("#"+data.modal_id+" .modal-footer .btn-save").show();
		}
		$(".single-select").comboSelect();
		setPlaceHolder();
		initMultiSelect();
	});
}

function saveOutward(formId){
    var fd = $('#'+formId).serialize();
    $.ajax({
		url: base_url +'production_v3/jobWorkVendor/saveVendorLog',
		data:fd,
		type: "POST",
		dataType:"json",
	}).done(function(data){
		if(data.status===0){
			$(".error").html("");
			$.each( data.message, function( key, value ) {$("."+key).html(value);});
		}else if(data.status==1){
			
			initTable(1); $('#'+formId)[0].reset();$(".modal").modal('hide');
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}else{
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}
		initTable();				
	});
}

function trashLog(id,name='Record'){
	var send_data = { id:id };
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
						url: base_url + 'production_v3/jobWorkVendor/deleteLog',
						data: send_data,
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
								$("#logTbody").html(data.tbody);
								toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}
							initTable();
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

function jobWorkReturnSave(formId,fnsave){
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
			initTable(); $('#'+formId)[0].reset();
			$("#transHtmlData").html("");
			$("#transHtmlData").html(data.transHtml);
			$("#pending_qty").val(data.pending_qty);
			$("#ProductPendingQty").html(data.pending_qty);
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}else{
			initTable(); $('#'+formId)[0].reset();
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}
				
	});
}

function jobWorkReturn(data){
	var button = "";
	var fnEdit = data.fnEdit;if(fnEdit == "" || fnEdit == null){fnEdit="edit";}
	var fnsave = data.fnsave;if(fnsave == "" || fnsave == null){fnsave="save";}
	
	$.ajax({ 
		type: "POST",   
		url: base_url + controller + '/' + fnEdit,   
		data: data
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
			$("#"+data.modal_id+" .modal-footer .btn-save").hide();
            $("#"+data.modalId+" .modal-footer .btn-save-close").show();
		}
		$(".single-select").comboSelect();
		$("#"+data.modal_id+" .scrollable").perfectScrollbar({suppressScrollX: true});
		initMultiSelect();setPlaceHolder();
	});
}

function trashReturn(id,key,name="Transaction"){
	var send_data = { id:id,key:key };
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
						url: base_url + controller + '/deleteReturnTrans',
						data: send_data,
						type: "POST",
						dataType:"json",
						success:function(data)
						{
							if(data.status==0){
								toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}else{
								$("#transHtmlData").html("");
								$("#transHtmlData").html(data.transHtml);
								$("#pending_qty").val(data.pending_qty);
								$("#ProductPendingQty").html(data.pending_qty);
								toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}
							initTable();
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

/** Created By Mansee @ 23-03-2022 */
	function AddRowRejection(data) {
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
		cell = $(row.insertCell(-1));
		cell.html(data.rej_qty);
		cell.append(rejection_qty_input);
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
		cell = $(row.insertCell(-1));
		cell.html(data.rejection_reason);
		cell.append(rej_by_input);
		cell.append(rej_reason_input);
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
		cell.attr("style", "width:10%;");

		$(".qtyCal").trigger('keyup');
	}

	function RemoveRejection(button) {
		//Determine the reference of the Row using the Button.
		var row = $(button).closest("TR");
		var table = $("#rejectionReason")[0];
		table.deleteRow(row[0].rowIndex);
		$('#idleReasons tbody tr td:nth-child(1)').each(function(idx, ele) {
			ele.textContent = idx + 1;
		});
		var countTR = $('#idleReasons tbody tr:last').index() + 1;
		if (countTR == 0) {
			$("#idleReasonData").html('<tr id="noData"><td colspan="6" class="text-center">No data available in table</td></tr>');
		}
		$(".qtyCal").trigger('keyup');
	};
</script>