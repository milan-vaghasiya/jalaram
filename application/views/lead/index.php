<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-4">
                                <ul class="nav nav-pills">
                                    <li class="nav-item">
                                        <a href="<?=base_url($headData->controller."/index/0")?>" class="nav-tab btn waves-effect waves-light btn-outline-success <?=($status == 0) ? "active" : "" ?>">Lead </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="<?=base_url($headData->controller."/index/1")?>" class="btn waves-effect waves-light btn-outline-danger <?=($status == 1) ? "active" : "" ?>">Pending Response</a>
                                    </li> 
                                </ul>
                            </div>
                            <div class="col-md-8">
                                <button type="button" class="btn waves-effect waves-light btn-outline-primary  float-right loadForm permission-write" data-button="both" data-modal_id="modal-xl" data-function="addLead" data-form_title="Add Lead"><i class="fa fa-plus"></i> Add Lead</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='leadTable' class="table table-bordered ssTable" data-url='/getDTRows/<?=$status?>'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){
    initPartyTable();

    $(document).on('click',".loadForm",function(){
            var functionName = $(this).data("function");
            var fnSave = $(this).data("fnSave");
            var modalId = $(this).data('modal_id');
            var button = $(this).data('button');
            var title = $(this).data('form_title');
            var formId = functionName;
            if(fnSave == "" || fnSave == null){fnSave="save";}
            $.ajax({ 
                type: "GET",   
                url: base_url + controller + '/' + functionName,   
                data: {}
            }).done(function(response){
                $("#"+modalId).modal();
                $("#"+modalId+' .modal-title').html(title);
                $("#"+modalId+' .modal-body').html(response);
                $("#"+modalId+" .modal-body form").attr('id',formId);
                $("#"+modalId+" .modal-footer .btn-save").attr('onclick',"storeLead('"+formId+"','"+fnSave+"');");
                if(button == "close"){
                    $("#"+modalId+" .modal-footer .btn-close").show();
                    $("#"+modalId+" .modal-footer .btn-save").hide();
                }else if(button == "save"){
                    $("#"+modalId+" .modal-footer .btn-close").hide();
                    $("#"+modalId+" .modal-footer .btn-save").show();
                }else{
                    $("#"+modalId+" .modal-footer .btn-close").show();
                    $("#"+modalId+" .modal-footer .btn-save").show();
                }
                $(".single-select").comboSelect();
                $("#processDiv").hide();
                $("#"+modalId+" .scrollable").perfectScrollbar({suppressScrollX: true});
                initMultiSelect();setPlaceHolder();
            });
        });	

        $(document).on('click','.leadAction',function(){
            var lead_id = $(this).data("id");
            var functionName = $(this).data("function");
            var fnSave = $(this).data("fnsave");
            var modalId = $(this).data('modal_id');
            var title = $(this).data('form_title');
            var formId = functionName;
            if(fnSave == "" || fnSave == null){fnSave="save";}
            $.ajax({ 
                type: "POST",   
                url: base_url + controller + '/' + functionName,   
                data: {lead_id:lead_id}
            }).done(function(response){
                $("#"+modalId).modal();
                $("#"+modalId+' .modal-title').html(title);
                $("#"+modalId+' .modal-body').html(response);
                $("#"+modalId+" .modal-body form").attr('id',formId);
                $("#lead_id").val(lead_id);
                $("#"+modalId+" .modal-footer .btn-save").attr('onclick',"storeLead('"+formId+"','"+fnSave+"');");
                $("#"+modalId+" .modal-footer .btn-close").show();
                $("#"+modalId+" .modal-footer .btn-save").show();
                $(".single-select").comboSelect();
                $("#processDiv").hide();
                $("#"+modalId+" .scrollable").perfectScrollbar({suppressScrollX: true});
                initMultiSelect();setPlaceHolder();
            });	
        });
        
        $(document).on('click','.leadActionStatic',function(){
        
            var send_data = { id:$(this).data('id'),lead_status:$(this).data('lead_status') };
            var fnSave = $(this).data('fnsave');
            $.confirm({
                title: 'Confirm!',
                content: 'Are you sure want to '+$(this).data('action_name')+ ' this Lead?',
                type: 'red',
                buttons: {   
                    ok: {
                        text: "ok!",
                        btnClass: 'btn waves-effect waves-light btn-outline-success',
                        keys: ['enter'],
                        action: function(){
                            $.ajax({
                                url: base_url + controller + '/' + fnSave,
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
                                        if(fnSave!='delete'){$(".modal").modal('hide');}$('.reloadLeads').trigger('click');
                                        toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
                                    }
                                }
                            });
                        }
                    },
                    cancel: {  btnClass: 'btn waves-effect waves-light btn-outline-secondary',action: function(){} }
                }
            });
        });
    });

function editLead(data){
	var button = "";
	var fnEdit = $(this).data("fnEdit");if(fnEdit == "" || fnEdit == null){fnEdit="edit";}
	var fnSave = $(this).data("fnSave");if(fnSave == "" || fnSave == null){fnSave="save";}
	$.ajax({ 
		type: "POST",   
		url: base_url + controller + '/' + fnEdit,   
		data: {id:data.id}
	}).done(function(response){
		$("#"+data.modal_id).modal();
		$("#"+data.modal_id+' .modal-title').html(data.title);
		$("#"+data.modal_id+' .modal-body').html(response);
		$("#"+data.modal_id+" .modal-body form").attr('id',data.form_id);
		$("#"+data.modal_id+" .modal-footer .btn-save").attr('onclick',"storeLead('"+data.form_id+"','"+fnSave+"');");
		if(button == "close"){
			$("#"+data.modal_id+" .modal-footer .btn-close").show();
			$("#"+data.modal_id+" .modal-footer .btn-save").hide();
		}else if(button == "save"){
			$("#"+data.modal_id+" .modal-footer .btn-close").hide();
			$("#"+data.modal_id+" .modal-footer .btn-save").show();
		}else{
			$("#"+data.modal_id+" .modal-footer .btn-close").show();
			$("#"+data.modal_id+" .modal-footer .btn-save").show();
		}
		$(".single-select").comboSelect();
		$("#"+data.modal_id+" .scrollable").perfectScrollbar({suppressScrollX: true});
		initMultiSelect();setPlaceHolder();
	});
}

function storeLead(formId,fnSave){
	var fd = $('#'+formId).serialize();
	$.ajax({
		url: base_url + controller + '/' + fnSave,
		data:fd,
		type: "POST",
		dataType:"json",
	}).done(function(data){
		if(data.status===0){
			$(".error").html("");
			$.each( data.message, function( key, value ) {
				$("."+key).html(value);
			});
		}else if(data.status==1){
			$(".modal").modal('hide');
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center' });
			$('.reloadLeads').trigger('click');
		}else{
			$(".modal").modal('hide');
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center' });
		}
				
	});
}

function trashLead(id,fnSave='delete',name='Record'){
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
						url: base_url + controller + '/' + fnSave,
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
								if(fnSave!='delete'){$(".modal").modal('hide');}$('.reloadLeads').trigger('click');
								toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}
						}
					});
				}
			},
			cancel: {  btnClass: 'btn waves-effect waves-light btn-outline-secondary',action: function(){} }
		}
	});
}

function initPartyTable() {
    var process_id = $('#process_id_search').val();
    $('.ssTable').DataTable().clear().destroy();
    var tableOptions = {
        pageLength: 25,
        'stateSave': false
    };
    var tableHeaders = {
        'theads': '',
        'textAlign': textAlign,
        'srnoPosition': 1
    };
    var dataSet = {
        process_id: process_id
    }
    ssDatatable($('.ssTable'), tableHeaders, tableOptions, dataSet);
}
</script>