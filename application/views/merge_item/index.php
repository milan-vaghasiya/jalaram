<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title">Merge Item</h4>
                            </div>
                            <div class="col-md-6">
                                <button type="button" class="btn btn-outline-primary float-right mergeItemForm" data-button="both" data-modal_id="modal-xl" data-function="addMergeItem" data-form_title="Merge Item"><i class="fa fa-plus"></i> Merge Item</button>
                            </div>                             
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='mergeTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>
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
	$(document).on('change','#item_type',function(){
		var item_type = $(this).val();
		if(item_type){
			$.ajax({
				url:base_url + controller + '/getItemListForSelect',
				type:'post',
				data:{item_type:item_type,option_label:'Select From Item'},
				dataType:'json',
				success:function(data){
					$("#from_item").html("");
					$("#from_item").html(data.options);
					$("#from_item").comboSelect();

					$("#to_item").html("");
					$("#to_item").html(data.options);
					$("#to_item").comboSelect();
				}
			});
		} else {
			$("#from_item").html("<option value=''>Select From Item</option>");
			$("#from_item").comboSelect();

			$("#to_item").html("<option value=''>Select To Item</option>");
			$("#to_item").comboSelect();
		}
    });

	$(document).on('click',".mergeItemForm",function(){
        var functionName = $(this).data("function");
        var modalId = $(this).data('modal_id');
        var button = $(this).data('button');
		var title = $(this).data('form_title');
		var formId = functionName.split('/')[0];
		var fnsave = $(this).data("fnsave");if(fnsave == "" || fnsave == null){fnsave="save";}
        $.ajax({ 
            type: "GET",   
            url: base_url + controller + '/' + functionName,   
            data: {}
        }).done(function(response){
            $("#"+modalId).modal({show:true});
			$("#"+modalId+' .modal-title').html(title);
			$("#"+modalId+' .modal-body').html("");
            $("#"+modalId+' .modal-body').html(response);
            $("#"+modalId+" .modal-body form").attr('id',formId);
			$("#"+modalId+" .modal-footer .btn-save").attr('onclick',"mergeItem('"+formId+"','"+fnsave+"');");
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
			setTimeout(function(){ initMultiSelect();setPlaceHolder(); }, 5);
        });
    });
});

function mergeItem(formId,fnsave,srposition=1){	
	setPlaceHolder();
	if(fnsave == "" || fnsave == null){fnsave="save";}
	var form = $('#'+formId)[0];
	var fd = new FormData(form);

	$.confirm({
		title: 'Confirm!',
		content: 'This step can not be reversed. <br> Are you sure to merge this item?',
		type: 'red',
		buttons: {   
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function(){
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
							initTable(srposition); $('#'+formId)[0].reset();$(".modal").modal('hide');
							toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
						}else{
							initTable(srposition); $('#'+formId)[0].reset();$(".modal").modal('hide');
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
</script>