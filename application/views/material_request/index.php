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
                                    <li class="nav-item"> <button onclick="statusTab('materialRequestTable',0);" class=" btn waves-effect waves-light btn-outline-info active" style="outline:0px" data-toggle="tab" aria-expanded="false">Pending</button> </li>
                                    <li class="nav-item"> <button onclick="statusTab('materialRequestTable',1);" class=" btn waves-effect waves-light btn-outline-success" style="outline:0px" data-toggle="tab" aria-expanded="false">Completed</button> </li>
                                    <li class="nav-item"> <button onclick="statusTab('materialRequestTable',2);" class=" btn waves-effect waves-light btn-outline-primary" style="outline:0px" data-toggle="tab" aria-expanded="false">Close</button> </li>
                                </ul>
                            </div>   
                            <div class="col-md-4">
                                <h4 class="card-title text-center">Material Request</h4>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn waves-effect waves-light btn-outline-primary float-right addNew permission-write" data-button="both" data-modal_id="modal-lg" data-function="addRequest" data-form_title="Material Request"><i class="fa fa-plus"></i> Request</button>
                            </div>                             
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='materialRequestTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url();?>assets/js/custom/material-request-form.js?v=<?=time()?>"></script>
<script>
$(document).ready(function(){
    $(document).on('change', '#fg_item_id', function () {
		var item_id = $(this).val();
		
		if (item_id == "") {
			$("#kitItems").html('<tr><td class="text-center" colspan="3">No Data Found.</td></tr>');
		} else {
			$.ajax({
				url: base_url + 'materialRequest/bomWiseItemData',
				data: {item_id:item_id},
				type: "POST",
				dataType: 'json',
				success: function (data) {
					$("#kitItems").html(data.tbodyData);
					$("#req_item_id").html("");
                    $("#req_item_id").html(data.options);
                    $("#req_item_id").comboSelect();
				}
			});
		}
	});
});
</script>