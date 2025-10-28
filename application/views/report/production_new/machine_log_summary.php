<?php 
/* 
*   Created By : Chauhan Milan
*   Created At : 04-04-2023
*/
$this->load->view('includes/header'); 
?>
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
								<select name="report_type" id="report_type" class="form-control single-select">
									<option value="">Select Report Type</option>
                                    <option value="1">Summary Report</option>
                                    <option value="2">Daily Report</option>
                                    <option value="3">Hourly Report</option>
								</select>
								<div class="error dept_id"></div>
							</div>
							<div class="col-md-4 form-group">
								<select name="machine_id" id="machine_id" class="form-control single-select">
									<option value="">Select ALL</option>
                                    <?php
                                        foreach($machineList as $row):
                                            echo '<option value="'.$row->id.'">['.$row->item_code.'] '.$row->item_name.' - '.$row->device_no.'</option>';
                                        endforeach;
                                    ?>
								</select>
								<div class="error machine_id"></div>
							</div>
							<div class="col-md-2 form-group">
								<input type="date" name="from_date" id="from_date" class="form-control" value="<?= date('Y-m-d') ?>" />
								<div class="error from_date"></div>
							</div>
							<div class="col-md-3 form-group">
								<div class="input-group">
									<input type="date" name="to_date" id="to_date" class="form-control" value="<?= date('Y-m-d') ?>" />
									<div class="input-group-append ml-2">
										<button type="button" class="btn waves-effect waves-light btn-success float-right loadData" title="Load Data">
											<i class="fas fa-sync-alt"></i> Load
										</button>
									</div>
								</div>
								<div class="error to_date"></div>
							</div>
						</div>
					</div>
					<div class="card-body reportDiv" style="min-height:75vh">
						<div class="table-responsive">
							<table id='reportTable' class="table table-bordered">
								<thead class="thead-info" id="theadData">
									<tr>
										<th colspan="16" class="text-center">Production Summary</th>
									</tr>
                                    <tr>
                                        <th>#</th>
                                        <th>Duration</th>
                                        <th>Job Card No</th>
                                        <th>Item Name</th>
                                        <th>Machine</th>
                                        <th>Process</th>
                                        <th>Operator</th>
                                        <th>Cycle Time <br> (Sec.)</th>
                                        <th>L/U Time <br> (Sec.)</th>
                                        <th>Total CT <br> (Sec.)</th>
                                        <th>Plan Qty.</th>
                                        <th>Prod. Qty.</th>
                                        <th>Rej Qty.</th>
                                        <th>RW Qty.</th>
                                        <th>Quality <br> Ratio (%)</th>
                                        <th>Prod. Loss <br> (Qty.)</th>
                                    </tr>
								</thead>
								<tbody id="tbodyData"></tbody>
								<tfoot class="thead-info" id="tfootData">
									<tr>
										<th class="text-right" colspan="10">Total</th>
										<th class="text-center">0</th>
										<th class="text-center">0</th>
										<th class="text-center">0</th>
										<th class="text-center">0</th>
										<th class="text-center">0</th>
										<th class="text-center">0</th>
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

    $(document).on('click', '.loadData', function(e) {
        $(".error").html("");
        var valid = 1;
        var report_type = $('#report_type').val();
        var machine_id = $('#machine_id').val();
        var from_date = $('#from_date').val();
        var to_date = $('#to_date').val();
        if ($("#report_type").val() == "") {
            $(".report_type").html("Report type is required.");
            valid = 0;
        }
        /* if ($("#machine_id").val() == "") {
            $(".machine_id").html("Machine is required.");
            valid = 0;
        } */
        if ($("#from_date").val() == "") {
            $(".from_date").html("From Date is required.");
            valid = 0;
        }
        if ($("#to_date").val() == "") {
            $(".to_date").html("To Date is required.");
            valid = 0;
        }
        if (valid) {
            $.ajax({
                url: base_url + controller + '/getMachineLogSummaryData',
                data: {
                    report_type: report_type,
                    machine_id: machine_id,
                    from_date: from_date,
                    to_date: to_date
                },
                type: "POST",
                dataType: 'json',
                success: function(data) {
                    $("#reportTable").DataTable().clear().destroy();
                    if(data.status == 1){
                        $("#tbodyData").html(data.tbody);
                        $("#tfootData").html(data.tfoot);
                    }else{
                        toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
                    }
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
                $(".loadData").trigger('click');
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