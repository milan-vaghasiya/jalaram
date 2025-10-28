<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-4">
                                <h4 class="card-title">Leave Approve</h4>
                            </div>                         
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
							<table id="leaveApproveTable" class="table table-bordered ssTable" data-url="/getDTRows"></table>
							<?php
								/* if($leave_auth > 0):
									echo '<table id="leaveApproveTable" class="table table-bordered ssTable" data-url="/getDTRows"></table>';
								else:
									echo '<h3 class="text-dark text-center">
											<i class="ti-face-sad text-warning font-5rem"></i><br>
											Sorry...!<br>You does not have Leave approval Authority
										</h3>';
								endif; */
							?>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>

<div class="modal fade" id="approveLeaveModal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content animated slideDown">
            <div class="modal-header">
                <h4 class="modal-title">Leave Action</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
				<form id="approveLeaveForm" autocomplete="off">
					<div class="col-md-12">
						<div class="row">
							<input type="hidden" name="id" id="id" value="" />
							<input type="hidden" name="leave_id" id="leave_id" value="" />
							<input type="hidden" name="leave_authority" id="leave_authority" value="" />
							<div class="col-md-12 form-group"><div class="error generalError"></div></div>
							<div class="col-md-6 form-group">
								<label for="approve_status">Status</label>
								<select name="approve_status" id="approve_status" class="form-control single-select req">
									<option value="">Select Status</option>
									<option value="1">Approve</option>
									<option value="2">Decline</option>
								</select>
							</div>
							<div class="col-md-6">
								<label for="approve_date">Approve Date</label>
								<input type="date" name="approve_date" id="approve_date" class="form-control req" value="<?=date("Y-m-d")?>"/>
							</div>
							<div class="col-md-12 form-group text-center"><h6 class="leave-days block font-14 font-medium bg-cyan text-white"></h6></div>
							<div class="col-md-12 form-group">
								<label for="comment">Dicision Comments</label>
								<textarea rows="2" name="comment" class="form-control" placeholder="Dicision Comments" ></textarea>
							</div>
						</div>
					</div>
				</form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn waves-effect waves-light btn-outline-secondary btn-close" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                <button type="button" class="btn waves-effect waves-light btn-outline-success btn-approveLeave"><i class="fa fa-check"></i> Save</button>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url();?>assets/js/custom/leave-approve.js?v=<?=time()?>"></script>