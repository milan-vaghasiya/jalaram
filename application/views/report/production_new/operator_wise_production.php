<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
	<div class="container-fluid bg-container">
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header">
						<div class="row">
							<div class="col-md-4 form-group">
								<h4 class="card-title text-left pageHeader"><?= $pageHeader ?></h4>
							</div>
							<div class="col-md-3 form-group ">
								<select name="emp_id" id="emp_id" class="form-control single-select">
									<option value="">Select Operator</option>
									<?php
									foreach ($empData as $row) :
										echo '<option value="' . $row->id . '">' . $row->emp_name . '</option>';
									endforeach;
									?>
								</select>
								<div class="error emp_id"></div>
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

							<table id='reportTable' class="table table-bordered">
								<thead class="thead-info" id="theadData">
									<tr>
										<th id="op_name" colspan="13">Operator</th>
									</tr>
									<tr>
										<th style="min-width:50px;">#</th>
										<th style="min-width:100px;">Date</th>
										<th style="min-width:80px;">Shift</th>
										<th style="min-width:80px;">Part</th>
										<th style="min-width:80px;">Setup</th>
										<th style="min-width:100px;">Planned Pro.Time<br /><small>(In Min.)</small><br>(A)</th>
										<th style="min-width:100px;">Plan Qty.<br>(B)</th>
										<th style="min-width:100px;">Run Time<br /><small>(In Min.)</small><br>(C)</th>
										<th style="min-width:100px;">Ok Qty.<br>(D)</th>
										<th style="min-width:100px;">Break Down Time<br>(E)</th>
										<th style="min-width:100px;">Availability <br /><br>(F=C*100/A) </th>
										<th style="min-width:100px;">Performance <br /><br>(G=D*100/B) </th>
										<th style="min-width:100px;">OEE <br /><br>(F+G/2)</th>
									</tr>
								</thead>
								<tbody id="tbodyData"></tbody>
								<tfoot class="tfoot-info" id="tfootData">
									<tr>
										<th colspan="5" class="text-right">Total</th>
										<th style="min-width:100px;">0</th>
										<th style="min-width:100px;">0</th>
										<th style="min-width:100px;">0</th>
										<th style="min-width:100px;">0</th>
										<th style="min-width:100px;">0</th>
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


		$(document).on('click', '.loaddata', function(e) {
			$(".error").html("");
			var valid = 1;
			var operator_id = $('#emp_id').val();
			var from_date = $('#from_date').val();
			var to_date = $('#to_date').val();
			if ($("#operator_id").val() == "") {
				$(".dept_id").html("operator_id is required.");
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
				var operator_name = $("#emp_id option:selected").text();
				$("#op_name").html(operator_name);
				$.ajax({
					url: base_url + controller + '/getOperatorWiseProduction',
					data: {
						operator_id: operator_id,
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