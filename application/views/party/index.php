<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title"><?=($party_category == 1 ? "Customer": ($party_category == 2 ? "Vendor":"Supplier"))?></h4>
                            </div>
                            <div class="col-md-6">
                                <button type="button" class="btn waves-effect waves-light btn-outline-primary float-right addNew permission-write" data-button="both" data-modal_id="modal-xl" data-function="addParty/<?=$party_category?>" data-form_title="Add <?=($party_category == 1 ? "Customer": ($party_category == 2 ? "Vendor":"Supplier"))?>"><i class="fa fa-plus"></i> Add <?=($party_category == 1 ? "Customer": ($party_category == 2 ? "Vendor":"Supplier"))?></button>
                                <?php if($party_category == 2): ?>
                                    <select name="process_id" id="process_id_search" class="form-control float-right" style="width:50%"> 
                                        <option value="">Select All</option>
                                        <?php
                                            if(!empty($processData)):
                                                foreach($processData as $row):  
                                                    echo '<option value="' . $row->id . '">' . $row->process_name . '</option>';
                                                endforeach;
                                            endif;
                                        ?>
                                    </select>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='partyTable' class="table table-bordered ssTable" data-url='/getDTRows/<?=$party_category?>'></table>
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
	$(document).on('change','#process_id_search',function(){ initPartyTable(); }); 
});

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
function customerTab(tableId,party_category,status){
    $("#"+tableId).attr("data-url",'/getDTRows/'+party_category+'/'+status);
    ssTable.state.clear();initTable();
}

function createUser(postData){
	var send_data = { id:postData.id };
	$.confirm({
		title: 'Confirm!',
		content: 'Are you sure you want to create user ?',
		type: 'red',
		buttons: {   
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function(){
					$.ajax({
						url: base_url + controller + '/createUser',
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
								initTable(); 
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
</script>