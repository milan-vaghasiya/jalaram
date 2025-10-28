<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-5">
                                <ul class="nav nav-pills">
									<li class="nav-item"> <a href="<?=base_url('production_v3/jobWorkVendor/pendingChallan')?>" class="btn waves-effect waves-light btn-outline-info float-right mr-1">Pending</a> </li>
                                    <li class="nav-item"> <a href="<?=base_url('production_v3/jobWorkVendor/vendorReceiveIndex')?>" class="btn waves-effect waves-light btn-outline-info active float-right mr-1">Vendor Receive</a> </li>
                                    <li class="nav-item"> <a href="<?=base_url('production_v3/jobWorkVendor/index/0')?>" class="btn waves-effect waves-light btn-outline-info  float-right mr-1">Inprocess</a> </li>
                                    <li class="nav-item"> <a href="<?=base_url('production_v3/jobWorkVendor/index/1')?>" class="btn waves-effect waves-light btn-outline-info  float-right ">Completed</a> </li>
                                </ul>
                            </div>
                            <div class="col-md-2 form-group">
                                <h4 class="card-title text-left">Vendor Receive</h4>
                            </div>   
                           <!--  <div class="col-md-5">
                                <div class="input-group">
                                    <select name="party_id" id="party_id" class="form-control single-select" style="width:70%;">
                                        <option value="">Select Vendor</option>
                                        <?php
                                        foreach ($vendorData as $row) :
                                            echo '<option value="' . $row->id . '">' . $row->party_name . '</option>';
                                        endforeach;
                                        ?>
                                    </select>
                                    <div class="input-group-append">
							            <?php if($shortYear == CURRENT_FYEAR): ?>
                                            <button type="button" class="btn waves-effect waves-light btn-success float-right createVendorChallan" title="Create Challan">
    									        <i class="fa fa-plus"></i> Create Challan
    								        </button>
    								    <?php endif; ?>
                                    </div>
                                </div>
                            </div>  -->
                            <!-- <div class="col-md-6">
                                <h4 class="card-title">Vendor Challan</h4>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <select name="party_id" id="party_id" class="form-control single-select" style="width:70%;">
                                        <option value="">Select Vendor</option>
                                        <?php
                                        foreach ($vendorData as $row) :
                                            echo '<option value="' . $row->id . '">' . $row->party_name . '</option>';
                                        endforeach;
                                        ?>
                                    </select>
                                    <div class="input-group-append">
							            <?php if($shortYear == CURRENT_FYEAR): ?>
                                            <button type="button" class="btn waves-effect waves-light btn-success float-right createVendorChallan" title="Create Challan">
    									        <i class="fa fa-plus"></i> Create Challan
    								        </button>
    								    <?php endif; ?>
                                    </div>
                                </div>
                            </div> -->
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row"> 
                            <div class="col-md-12"> 
                                <div class="table-responsive">
                                    <table id='vendorChallanTable' class="table table-bordered ssTable" data-url='/getVendorReceiveDTRows'></table>
                                </div>
                            </div>
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
function acceptChallan(data){
	var send_data = { ch_trans_id:data.ch_trans_id,in_challan_no : data.in_challan_no,trans_no : data.trans_no};
	$.confirm({
		title: 'Confirm!',
		content: 'Are you sure want to Accept this Challan?',
		type: 'red',
		buttons: {   
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function(){
					$.ajax({
						url: base_url + controller + '/acceptChallan',
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
								initTable(); initMultiSelect();
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