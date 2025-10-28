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
                                   <li class="nav-item"> 
                                       <button onclick="statusTab('packingTable',0);" class=" btn waves-effect waves-light btn-outline-primary active" style="outline:0px" data-toggle="tab" aria-expanded="false">Tentative Packing</button> 
                                    </li>
                                   <li class="nav-item"> 
                                       <button onclick="statusTab('packingTable',1);" class=" btn waves-effect waves-light btn-outline-info" style="outline:0px" data-toggle="tab" aria-expanded="false">Final Packing</button>
                                    </li>
                               </ul>
                            </div>
                            <div class="col-md-2">
                                <h4 class="card-title">Packing</h4>
                            </div>
                            <div class="col-md-5">
                                <button type="button" class="btn waves-effect waves-light btn-outline-primary float-right addNew permission-write" data-button="both" data-modal_id="modal-lg" data-fnsave="saveSelfPacking" data-function="addSelfPacking" data-form_title="Add Self Packing"><i class="fa fa-plus"></i> New Packing</button>
                            </div>
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='packingTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>

<div class="modal fade" id="print_dialog" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog" style="min-width:30%;">
		<div class="modal-content animated zoomIn border-light">
			<div class="modal-header bg-light">
				<h5 class="modal-title text-dark"><i class="fa fa-print"></i> Print Options</h5>
				<button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form id="printModel" method="post" action="<?=base_url($headData->controller.'/packing_pdf')?>" target="_blank">
				<div class="modal-body">
					<div class="col-md-12">
						<div class="row">
							<!-- <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12"> -->
                            <div class="col-md-12 form-group">
                                <label>Print For</label>
                                <select name="cust_id" id="cust_id" class="form-control single-select">
                                    <option value="1" <?= (!empty($dataRow->cust_id) && $dataRow->cust_id == "Custom")?"selected":"" ?>>Custom Print</option>
                                    <option value="2" <?= (!empty($dataRow->cust_id) && $dataRow->cust_id == "Customer")?"selected":"" ?>>Customer Print</option>
                                </select>
								<input type="hidden" name="printsid" id="printsid" value="0">

                            </div>
							<!-- <div class="col-lg-7 col-md-7 col-sm-12 col-xs-12">
								<label>No. of Extra Copy</label>
								<input type="text" name="extra_copy" id="extra_copy" class="form-control" value="0">
								<input type="hidden" name="printsid" id="printsid" value="0">
								<label class="error_extra_copy text-danger"></label>
							</div> -->
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<a href="#" data-dismiss="modal" class="btn btn-secondary"><i class="fa fa-times"></i> Close</a>
					<button type="submit" class="btn btn-success" onclick="closeModal('print_dialog');"><i class="fa fa-print"></i> Print</button>
				</div>
			</form>
		</div>
	</div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){

	<?php if(!empty($printID)): ?>
		$("#printModel").attr('action',base_url + controller + '/packing_pdf');
		$("#printsid").val(<?=$printID?>);
		$("#print_dialog").modal();
	<?php endif; ?>

	$(document).on("click",".printPacking",function(){
		$("#printModel").attr('action',base_url + controller + '/packing_pdf');
		$("#printsid").val($(this).data('id'));
		$("#print_dialog").modal();
	});		
});

function closeModal(modalId)
{
	$("#"+ modalId).modal('hide');
	
	<?php if(!empty($printID)): ?>
		window.location = base_url + controller;
	<?php endif; ?>
}		
</script>