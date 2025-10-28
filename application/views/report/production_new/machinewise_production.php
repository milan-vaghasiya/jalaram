<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
	<div class="container-fluid bg-container">
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header">
						<div class="row">
							<div class="col-md-12 form-group">
								<h4 class="card-title text-center pageHeader"><?= $pageHeader ?></h4>
							</div>
						</div>
						<hr>
						<div class="row">
							<div class="col-md-3 form-group">
								<select name="dept_id" id="dept_id" class="form-control single-select">
									<option value="">Select Department</option>
									<?php
									foreach ($deptData as $row) :
										echo '<option value="' . $row->id . '">' . $row->name . '</option>';
									endforeach;
									?>
								</select>
								<div class="error dept_id"></div>
							</div>
							<div class="col-md-4 form-group">
								<select name="machine_id" id="machine_id" class="form-control single-select">
									<option value="">Select Machine</option>
								</select>
								<div class="error machine_id"></div>
							</div>
							<div class="col-md-2 form-group">
								<input type="date" name="from_date" id="from_date" class="form-control" value="<?= date('Y-m-d') ?>" />
								<div class="error fromDate"></div>
							</div>
							<div class="col-md-3 form-group">
								<div class="input-group">
									<input type="date" name="to_date" id="to_date" class="form-control" value="<?= date('Y-m-d') ?>" />
									<div class="input-group-append ml-2">
										<button type="button" class="btn waves-effect waves-light btn-success float-right loaddata" title="Load Data">
											<i class="fas fa-sync-alt"></i> Load
										</button>
									</div>
								</div>
								<div class="error toDate"></div>
							</div>
						</div>
					</div>
					<div class="card-body reportDiv" style="min-height:75vh">
						<div class="table-responsive">
							<?php
							$colspan = (!empty($idleReasonList) ? count($idleReasonList) : 0);
							?>
							<table id='reportTable' class="table table-bordered">
								<thead class="thead-info" id="theadData">
									<tr>
										<th colspan="<?=$colspan+14?>" id="mc_name">Operator</th>
									</tr>
									<tr>
										<th style="min-width:50px;" rowspan="2">#</th>
										<th style="min-width:100px;" rowspan="2">Date</th>
										<th style="min-width:80px;" rowspan="2">Shift</th>
										<!-- <th style="min-width:80px;" rowspan="2">M/C No.</th> -->
										<th style="min-width:110px;" rowspan="2">Part</th>
										<th style="min-width:100px;" rowspan="2">Setup</th>

										<th style="min-width:100px;" rowspan="2">Planned Pro.Time<br /><small>(In Min.)</small><br>(A)</th>
										<th style="min-width:100px;" rowspan="2">Plan Qty.<br>(B)</th>
										<th style="min-width:100px;" rowspan="2">Run Time<br /><small>(In Min.)</small><br>(C)</th>
										<th style="min-width:100px;" rowspan="2">Total Production (In Nos.)<br>(D)</th>
										<th style="min-width:100px;" rowspan="2">Break Down Time<br>(E)</th>
										<th style="min-width:100px;" colspan="<?= $colspan ?>">Break Down Reason</th>
										<th style="min-width:100px;" rowspan="2">Availability <br /><br>(F=C*100/A) </th>
										<th style="min-width:100px;" rowspan="2">Performance <br /><br>(G=D*100/B) </th>
										<th style="min-width:100px;" rowspan="2">OEE <br /><br>(F+G/2)</th>
									</tr>
									<tr>
										<?php
										if (!empty($idleReasonList)) {
											foreach ($idleReasonList as $row) {
										?>
												<th style="min-width:30px;"><?= $row->code ?></th>
										<?php
											}
										}
										?>
									</tr>
								</thead>
								<tbody id="tbodyData"></tbody>
								<tfoot id="tfootData">
									<tr>
										<th class="text-right" colspan="5">Total</th>
										<th style="min-width:100px;">0</th>
										<th style="min-width:100px;">0</th>
										<th style="min-width:100px;">0</th>
										<th style="min-width:100px;">0</th>
										<th style="min-width:100px;">0</th>
										<?php
										if (!empty($idleReasonList)) {
											foreach ($idleReasonList as $row) {
										?>
												<th style="min-width:30px;">0</th>
										<?php
											}
										}
										?>
										<th style="min-width:100px;">0</th>
										<th style="min-width:100px;">0</th>
										<th style="min-width:100px;">0</th>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<?php $this->load->view('includes/footer'); ?>
<?= $floatingMenu ?>
<script>
	$(document).ready(function() {
		reportTable();

		$(document).on('change', '#dept_id', function(e) {
			var dept_id = $(this).val();
			if (dept_id) {
				$.ajax({
					url: base_url + controller + '/getMachineData',
					data: {
						dept_id: dept_id
					},
					type: "POST",
					dataType: 'json',
					success: function(data) {
						if (data.status === 0) {
							$(".error").html("");
							$.each(data.message, function(key, value) {
								$("." + key).html(value);
							});
						} else {
							$("#machine_id").val("");
							$("#machine_id").html(data.option);
							$("#machine_id").comboSelect();
						}
					}
				});
			}
		});

		$(document).on('click', '.loaddata', function(e) {
			$(".error").html("");
			var valid = 1;
			var dept_id = $('#dept_id').val();
			var machine_id = $('#machine_id').val();
			var from_date = $('#from_date').val();
			var to_date = $('#to_date').val();
			if ($("#dept_id").val() == "") {
				$(".dept_id").html("Department is required.");
				valid = 0;
			}
			if ($("#machine_id").val() == "") {
				$(".machine_id").html("Machine is required.");
				valid = 0;
			}
			if ($("#from_date").val() == "") {
				$(".fromDate").html("From Date is required.");
				valid = 0;
			}
			if ($("#to_date").val() == "") {
				$(".toDate").html("To Date is required.");
				valid = 0;
			}
			if (valid) {
				var machine_name = $("#machine_id option:selected").text();
				$("#mc_name").html(machine_name);
				$.ajax({
					url: base_url + controller + '/getMachineWiseProduction',
					data: {
						dept_id: dept_id,
						machine_id: machine_id,
						from_date: from_date,
						to_date: to_date
					},
					type: "POST",
					dataType: 'json',
					success: function(data) {
						$("#reportTable").dataTable().fnDestroy();
						$("#theadData").html(data.thead);
						$("#tbodyData").html(data.tbody);
						$("#tfootData").html(data.tfoot);
						reportTable();
					}
				});
			}
		});
	});

	function reportTable() {
		var reportTable = $('#reportTable').DataTable({
			responsive: true,
			scrollY: '55vh',
			scrollCollapse: true,
			"scrollX": true,
			"scrollCollapse": true,
			//'stateSave':true,
			"autoWidth": false,
			order: [],
			"columnDefs": [{
					type: 'natural',
					targets: 0
				},
				{
					orderable: false,
					targets: "_all"
				},
				{
					className: "text-left",
					targets: [0, 1]
				},
				{
					className: "text-center",
					"targets": "_all"
				}
			],
			pageLength: 25,
			language: {
				search: ""
			},
			lengthMenu: [
				[10, 25, 50, 100, -1],
				['10 rows', '25 rows', '50 rows', '100 rows', 'Show all']
			],
			dom: "<'row'<'col-sm-7'B><'col-sm-5'f>>" + "<'row'<'col-sm-12't>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
			buttons: ['pageLength', 'excel', {
				text: 'Refresh',
				action: function(e, dt, node, config) {
					loadAttendanceSheet();
				}
			}]
		});
		reportTable.buttons().container().appendTo('#reportTable_wrapper toolbar');
		$('.dataTables_filter .form-control-sm').css("width", "97%");
		$('.dataTables_filter .form-control-sm').attr("placeholder", "Search.....");
		$('.dataTables_filter').css("text-align", "left");
		$('.dataTables_filter label').css("display", "block");
		$('.btn-group>.btn:first-child').css("border-top-right-radius", "0");
		$('.btn-group>.btn:first-child').css("border-bottom-right-radius", "0");
		return reportTable;
	}
</script>