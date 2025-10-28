<?php $this->load->view('includes/header'); ?>

<style>
	.titleText {
		color: #000000;
		font-size: 1.2rem;
		text-align: center;
		padding: 5px;
		background: #45729f;
		color: #ffffff;
		font-weight: 600;
		letter-spacing: 1px;
	}

	.card-body {
		padding: 20px 10px;
	}

	.jpFWTab nav>div a.nav-item.nav-link.active:after {
		left: -18% !important;
	}

	.ui-sortable-handle {
		cursor: move;
	}

	.ui-sortable-handle:hover {
		background-color: #daeafa;
		border-color: #9fc9f3;
		cursor: move;
	}
</style>

<div class="page-wrapper">
	<div class="container-fluid bg-container">
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header">
						<div class="row">
							<div class="col-md-7 form-group">
								<h4 class="card-title pageHeader"><?= $pageHeader ?></h4>
							</div>

							<div class="col-md-5 form-group">
							    <div class="input-group">
                                    <select name="job_card_id" id="job_card_id" class="form-control single-select req" style="width:75%;">
                                        <option value="">Select Item</option>
                                        <?php
                                            foreach($jobCardList as $row):
                                                echo '<option value="'.$row->id.'">'.getPrefixNumber($row->job_prefix, $row->job_no).' ['.$row->item_code.']</option>';
                                            endforeach;
                                        ?>
                                    </select>
                                    <div class="input-group-append">
										<button type="button" class="btn waves-effect waves-light btn-success float-right loadData" title="Load Data">
											<i class="fas fa-sync-alt"></i> Load
										</button>
									</div>
								</div>
                                <div class="error job_card_id"></div>
							</div>

						</div>
					</div>

					<div class="card-body reportDiv" style="min-height:75vh">
                        <div class="col-md-12">
                            <div class="row" id="costingDetail">

                                <div class="col-lg-12 col-xlg-12 col-md-12">
                                    <div class="card">
                                        <div class="titleText">JOB DETAIL</div>
                                        <div class="card-body scrollable" style="height:20vh;border-bottom: 5px solid #45729f">
                                            <table class="table">
                                                <tr>
                                                    <th>Job Card No.</th>
                                                    <td>: </td>
                                                    <th>Product </th>
                                                    <td>: </td>
                                                    <th>Order Quatity </th>
                                                    <td>: </td>
                                                </tr>
                                                <tr>
                                                    <th>Material Name</th>
                                                    <td>: </td>
                                                    <th>Heat No. </th>
                                                    <td>: </td>
                                                    <th>Received Qty. </th>
                                                    <td>: </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-12 col-xlg-12 col-md-12">
                                    <div class="card">
                                        <div class="titleText">COSTING DETAIL</div>
                                        <div class="card-body scrollable" style="height:30vh;border-bottom: 5px solid #45729f">
                                            <div class="table-responsive">
                                                <table class="table table-striped table-bordered ">
                                                    <thead class="thead-info">
                                                        <tr class="text-center">
                                                            <th>#</th>
                                                            <th class="text-left">Process Name</th>
                                                            <th class="text-left">Vendor</th>
                                                            <th>Inward <br> Qty</th>
                                                            <th>Inward <br> Cost</th>
                                                            <th>Cost <br> Per Pcs</th>
                                                            <th>Dept <br> MHR</th>
                                                            <th>Cycle Time<br>(in minutes)</th>
                                                            <th>Dept Cost<br>addition<br>Per Pcs</th>
                                                            <th>Cumulative Cost<br>Per Pcs</th>
                                                            <th>Qty. Pend. For <br> Move</th>
                                                            <th>Cost of<br>Pend. for Move</th>
                                                            <th>Qty. Moved to <br>Next</th>
                                                            <th>Cost of <br> Moved to <br>Next</th>
                                                            <th>Reject <br> Found</th>
                                                            <th>Cost of<br>Rejection</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td colspan="16" class="text-center">No data available in table</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-12 col-xlg-12 col-md-12">
                                    <div class="card">
                                        <div class="titleText">FINAL COST DETAIL</div>
                                        <div class="card-body scrollable" style="height:30vh;border-bottom: 5px solid #45729f">
                                            <table class="table">
                                                <tr>
                                                    <th>Total Sellig Price</th>
                                                    <th>: </th>
                                                </tr>
                                                <tr>
                                                    <th>Total Production Cost</th>
                                                    <th>: </th>
                                                </tr>
                                                <tr>
                                                    <th>Total Item lost Cost</th>
                                                    <th>: </th>
                                                </tr>
                                                <tr>
                                                    <th>Total Rejection Cost</th>
                                                    <th>: </th>
                                                </tr>
                                                <tr>
                                                    <th>Profit for Job Card</th>
                                                    <th>: </th>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
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
<script>
$(document).ready(function() {
    //$("#job_card_id").val(313).comboSelect();

    $(document).on('click', '.loadData', function(e) {
        $(".error").html("");
		var valid = 1;
		var job_card_id = $('#job_card_id').val();		

		if(job_card_id == ""){$(".job_card_id").html("Job No. is required.");valid=0;}else{$(".job_card_id").html("");}        

        if (valid) {
            $.ajax({
                url: base_url + controller + '/getJobCostingData',
                data: {job_card_id:job_card_id},
                type: "POST",
                dataType: 'json',
                success: function(data) {
                    if(data.status == 1){
                        $("#costingDetail").html(data.html);
                    }else{
                        toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
                    }
                }
            });
        }
    });
});

</script>