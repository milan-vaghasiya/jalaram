<?php $this->load->view('includes/header'); ?>
<style> 
	.typeahead.dropdown-menu{width:95.5% !important;padding:0px;border: 1px solid #999999;box-shadow: 0 2px 5px 0 rgb(0 0 0 / 26%);}
	.typeahead.dropdown-menu li{border-bottom: 1px solid #999999;}
	.typeahead.dropdown-menu li .dropdown-item{padding: 8px 1em;margin:0;}
</style>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title pageHeader">Update Ledger Opening</h4>
                            </div>  
                            <div class="col-md-5">
                                <select name="group_id" id="group_id" class="form-control single-select req">
                                    <option value="">Select Group</option>
                                    <?php
                                        foreach($grpData as $row):
                                            $selected = (!empty($dataRow->group_id) && $row->id == $dataRow->group_id)?"selected":"";
                                            echo "<option value='".$row->id."' data-row='".json_encode($row)."' ".$selected.">".$row->name."</option>";
                                        endforeach;
                                    ?>
                                </select>
                            </div>
                            <button type="button" class="btn waves-effect waves-light btn-success float-right loaddata" title="Load Data">
                                <i class="fas fa-sync-alt"></i> Load
                            </button>
                        </div>	
                    </div>
                    <div class="card-body">
                        <form autocomplete="off" id="saveLedgerOp">					
							<div class="col-md-12 mt-3">
								<div class="error op_data_error"></div>
								<div class="row form-group">
									<div class="table-responsive ">
										<table id="ledgerOpening" class="table table-bordered">
											<thead class="thead-info">
												<tr>
													<th style="width:5%;">#</th>
													<th>Pary Code</th>
													<th>Party Name</th>
													<th>Opening Balance</th>
													<th>New Opening Balance</th>
												</tr>
											</thead>
                                            <tbody id="ledgerOpeningData">
                                                <tr>
                                                    <td class="text-center" colspan="5">No data available in table</td>
                                                </tr>
                                            </tbody>
										</table>
									</div>
								</div>
							</div>
                        </form>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>

<div class="bottomBtn bottom-25 right-25 permission-write bulkSave">
    <!-- <button type="button`" class="btn btn-primary btn-rounded font-bold permission-write save-form " style="letter-spacing:1px;" onclick="storeLedgerOpening('saveLedgerOp','saveBulkOpeningBalance');">Save Opening</button> -->
</div>


<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){
    $(".bulkSave").hide();
    $(document).on('click','.loaddata',function(e){
		$(".error").html("");
		var group_id = $('#group_id').val();	
        $.ajax({
            url: base_url + controller + '/getGroupWiseLedger',
            data: {group_id:group_id},
            type: "POST",
            dataType:'json',
            success:function(data){
                $("#ledgerOpeningData").html(data.tbody);
                if(parseFloat(data.count) > 250){ $(".bulkSave").hide(); }else{ $(".bulkSave").show(); }
            }
        });
    }); 

    $(document).on('click','.saveOp',function(){
        var id = $(this).data('id');
        var balance_type = $("#balance_type_"+id).val();
        var opening_balance = $("#opening_balance_"+id).val();

        var fd = {id:id,balance_type:balance_type,opening_balance:opening_balance};
        $.confirm({
            title: 'Confirm!',
            content: 'Are you sure to update ledger opening balance?',
            type: 'orange',
            buttons: {   
                ok: {
                    text: "ok!",
                    btnClass: 'btn waves-effect waves-light btn-outline-success',
                    keys: ['enter'],
                    action: function(){
                        $.ajax({
                            url: base_url + controller + '/saveOpeningBalance',
                            data:fd,
                            type: "POST",
                            dataType:"json",
                        }).done(function(data){
                            if(data.status===0){
                                $(".error").html("");
                                $.each( data.message, function( key, value ) {$("."+key).html(value);});
                            }else if(data.status==1){

                                var cur_op = parseFloat(parseFloat(opening_balance) * parseFloat(balance_type)).toFixed(2);

                                var cur_op_text = '';
                                if(parseFloat(cur_op) > 0){
                                    cur_op_text = '<span class="text-success">'+cur_op+' CR</span>';
                                }else if(parseFloat(cur_op) < 0){
                                    cur_op_text = '<span class="text-danger">'+Math.abs(cur_op)+' DR</span>';
                                }else{
                                    cur_op_text = cur_op;
                                }
                                $("#cur_op_"+id).html(cur_op_text);

                                toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
                            }else{
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
    });
});

function storeLedgerOpening(formId,fnsave){
    setPlaceHolder();
	if(fnsave == "" || fnsave == null){fnsave="save";}
	var form = $('#'+formId)[0];
	var fd = new FormData(form);

    $.confirm({
		title: 'Confirm!',
		content: 'Are you sure to update ledger opening balance?',
		type: 'orange',
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
							toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
                            $(".loaddata").trigger('click');
						}else{
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