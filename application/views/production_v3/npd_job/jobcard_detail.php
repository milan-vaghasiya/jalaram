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

	.jpFWTab nav>div a.nav-item.nav-link {
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
							<div class="col-md-6">
								<h4 class="card-title">Npd Job Card View </h4>
							</div>
							<div class="col-md-6">
								<a href="<?= base_url($headData->controller) ?>" class="btn waves-effect waves-light btn-outline-dark float-right"><i class="fa fa-arrow-left"></i> Back</a>								
							</div>
						</div>
					</div>
					<div class="card-body">
						<div class="col-md-12">
							<div class="row">
								<!-- Column -->
								<div class="col-lg-12 col-xlg-12 col-md-12">
									<div class="card">
										<div class="titleText">NPD JOB DETAIL</div>
										<div class="card-body scrollable" style="height:15vh;border-bottom: 5px solid #45729f">
											<table class="table">
												<tr>
													<th>Job Card No.</th>
													<td>: <?= (getPrefixNumber($dataRow->job_prefix,$dataRow->job_no)) ?></td>
													<th>Job Date </th>
													<td>: <?= date("d-m-Y", strtotime($dataRow->job_date)) ?></td>
													<th>Product </th>
													<td>: [<?= $dataRow->product_code ?>] <?= $dataRow->product_name ?></td>
													<th>Qty. </th>
													<td>: <?= $dataRow->qty ?></td>
												</tr>
												<tr>
													<th colspan="1">Remark </th>
													<td colspan="7">: <?= $dataRow->remark ?></td>
												</tr>
												<tr>
													<th>Material Name</th>
													<td colspan="3">: <?= (!empty($reqMaterials->dispatch_item_name))?$reqMaterials->dispatch_item_name:''; ?></td>
													<th>Heat No.</th>
													<td>: <?= (!empty($reqMaterials->stock_batch_no))?$reqMaterials->stock_batch_no:'';  ?></td>
													<th>Issue Qty.</th>
													<td>: <?= (!empty($reqMaterials->dispatch_qty))?$reqMaterials->dispatch_qty:''; ?></td>
												</tr>
											</table>
										</div>
									</div>
								</div>
								<div class="col-lg-12 col-xlg-12 col-md-12">
									<div class="card jpFWTab">
										<nav>
											<div class="nav nav-tabs nav-fill tabLinks" id="nav-tab" role="tablist">
												<a class="nav-item nav-link" data-toggle="tab" href="#req_material" role="tab" aria-controls="nav-profile" aria-selected="false"> Log Detail</a>
											</div>
										</nav>
										<div class="tab-content py-3 px-3 px-sm-0" id="pills-tabContent">
											<!-- Log Detail Start -->
											<div class="tab-pane fade scrollable" style="height:60vh;" id="req_material" role="tabpanel" aria-labelledby="pills-req_material-tab">
												<div class="card-body">
														<div class="col-md-12 form-group">
															<form id="job_bom_data">
																<div class="row">
																	<input type="hidden" name="id" id="id" value="">
																	<input type="hidden" name="job_card_id" id="job_card_id" value="<?= $dataRow->id ?>">
																	<input type="hidden" name="product_id" id="product_id" value="<?= $dataRow->product_id ?>">

																	<div class="col-md-3 form-group">
																		<label for="entry_date">Job Date</label>
																		<input type="date" id="entry_date" name="entry_date" class="form-control req" placeholder="dd-mm-yyyy" value="" />
																	</div>
																	<div class="col-md-3 form-group">
																		<label for="process_id">Process</label>
																		<select name="process_id" id="process_id" class="form-control single-select req">
																			<option value="">Select Process</option>
																			<?php
																			foreach ($processList as $row) :
																				echo '<option value="' . $row->id . '">' . $row->process_name . '</option>';
																			endforeach;
																			?>
																		</select>
																	</div>
																	<div class="col-md-3 form-group">
																		<label for="machine_id">Machine</label>
																		<select name="machine_id" id="machine_id" class="form-control single-select">
																			<option value="">Select Machine</option>
																			<?php
																			foreach ($machineList as $row) :
																				echo '<option value="' . $row->id . '">' . $row->item_name . '</option>';
																			endforeach;
																			?>
																		</select>
																	</div>
																	<div class="col-md-3 form-group">
																		<label for="operator_id">Operator</label>
																		<select name="operator_id" id="operator_id" class="form-control single-select">
																			<option value="">Select Operator</option>
																			<?php
																			foreach ($operatorList as $row) :
																				echo '<option value="' . $row->id . '">' . $row->emp_name . '</option>';
																			endforeach;
																			?>
																		</select>
																	</div>
																
																	<div class="col-md-3 form-group">
																		<label for="cycle_time">Cycle Time</label>
																		<input type="text" name="cycle_time" id="cycle_time" class="form-control" value="" />
																	</div>
																	
																	<div class="col-md-3 form-group">
																		<label for="production_time">Production Time</label>
																		<input type="text" name="production_time" id="production_time" class="form-control" value="" />
																	</div>
																	<div class="col-md-3 form-group">
																		<label for="ok_qty"> Ok Qty.</label>
																		<input type="text" name="ok_qty" id="ok_qty" class="form-control floatonly req" value="" />
																	</div>
																	<div class="col-md-3 form-group">
																		<label for="rejection_qty">Rejction Qty.</label>
																		<input type="text" name="rejection_qty" id="rejection_qty" class="form-control floatonly req" value="" />
																	</div>
																	<div class="col-md-10 form-group">
																		<label for="remark">Remark</label>
																		<input type="text" name="remark" id="remark" class="form-control " value="" />
																	</div>
																	<?php if($dataRow->order_status != 4): ?>
    																	<div class="col-md-2 form-group">
    																		<label for="">&nbsp;</label>
    																		<button type="button" id="addJobLog" class="btn btn-outline-success waves-effect btn-block"><i class="fa fa-plus"></i> Add</button>
    																	</div>
    																<?php endif; ?>
																</div>
															</form>
														</div>
													<div class="table-responsive">
														<table class="table table-bordered">
															<thead class="thead-info">
																<tr class="text-center">
																	<th>#</th>
																	<th>Job Date</th>
																	<th>Process </th>
																	<th>Machine</th>
																	<th>Operator</th>
																	<th>Cycle Time</th>
																	<th>Production Time</th>
																	<th>Ok Qty.</th>
																	<th>Rejection Qty.</th>
																	<th>Remark</th>
																	<th>Action</th>
																</tr>
															</thead>
															<tbody id="logItems">
																<?php
																	if(!empty($logData)):
																		$i=1;
																		foreach($logData as $row):
																			echo '<tr>
																					<td class="text-center">'.$i++.'</td>
																					<td class="text-center">'.formatdate($row->entry_date).'</td>
																					<td class="text-center">'.$row->process_name.'</td>
																					<td class="text-center">'.$row->item_name.'</td>
																					<td class="text-center">'.$row->emp_name.'</td>
																					<td class="text-center">'.$row->cycle_time.'</td>
																					<td class="text-center">'.$row->production_time.'</td>
																					<td class="text-center">'.$row->ok_qty.'</td>
																					<td class="text-center">'.$row->rejection_qty.'</td>
																					<td class="text-center">'.$row->remark.'</td>
																					<td class="text-center">';
																					if($dataRow->order_status != 4): 
																						echo '<button type="button" onclick="trashLogdetail('.$row->id.','.$row->job_card_id.');" class="btn btn-outline-danger waves-effect waves-light btn-delete permission-remove"><i class="ti-trash"></i></button>';
																				    endif;
																				echo '</td>
																				</tr>';
																		endforeach;
																	else:
																		echo '<tr><td colspan="11" style="text-align:center;">No Data Found</td></tr>';
																	endif;
                                                                ?>
															</tbody>
														</table>
													</div>
												</div>
											</div>
											<!-- Log Detail End -->
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
    $(document).ready(function(){
		$(document).on("click", '#addJobLog', function () {
		var form = $('#job_bom_data')[0];
		var fd = new FormData(form);
		$.ajax({
			url: base_url + controller + '/saveLogDetail',
			data: fd,
			type: "POST",
			processData: false,
			contentType: false,
			dataType: "json",
		}).done(function (data) {
			if (data.status === 0) {
				$(".error").html("");
				$.each(data.message, function (key, value) { $("." + key).html(value); });
			} else if (data.status == 1) {
				$(".error").html("");
				$("#process_id").val(""); $("#process_id").comboSelect();
				$("#machine_id").val(""); $("#machine_id").comboSelect();
				$("#operator_id").val(""); $("#operator_id").comboSelect();
				$("#entry_date").val('');
				$("#cycle_time").val('');
				$("#production_time").val('');
				$("#ok_qty").val('');
				$("#rejection_qty").val('');
				$("#remark").val('');
				$("#logItems").html("");
				$("#logItems").html(data.tbodyData);
				toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
			} else {
				toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
			}

		});
	});
});
function trashLogdetail(id,job_card_id,name='Record'){
	var send_data = { id:id, job_card_id:job_card_id };
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
						url: base_url + controller + '/deleteLogTransdetail',
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
								$("#logItems").html("");
								$("#logItems").html(data.tbodyData);
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