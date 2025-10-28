<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-9">
                                <h4 class="card-title pageHeader"><?=$pageHeader?></h4>
                            </div> 
                            <div class="col-md-3">
                                <select id="emp_status" class="form-control single-select">
                                    <option value="0">Select ALL</option>
                                    <option value="1">Active</option>
                                    <option value="2">In-Active</option>
                                    <option value="3">Relieved</option>
                                </select>
                            </div>
                        </div>                                         
                    </div>
                    <div class="card-body reportDiv" style="min-height:75vh">
                        <div class="table-responsive">
                            <table id='reportTable' class="table table-bordered">
								<thead class="thead-info" id="theadData">
                                    <tr class="text-center">
                                        <th colspan="6">Employee Report</th>
                                        <th colspan="2">Doc. No.: D HR 01</th>
                                        <th colspan="2">Rev. No. & Dt.: 00/01-06-20</th>
                                    </tr>
									<tr>
										<th>#</th>
										<th>Name of Employee</th>
										<th>Employee Code</th>
										<th>Designation</th>
										<th>Contact No</th>
										<th>DOB</th>
										<th>Education</th>
										<th>Experience</th>
										<th>Joining Date</th>
										<th>Resign Date</th>
									</tr>
								</thead>
								<tbody id="tbodyData">
									<?= $empData ?>
								</tbody>
							</table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>


<?php $this->load->view('includes/footer'); ?>
<?=$floatingMenu?>
<script>
$(document).ready(function(){
	reportTable();
	$(document).on('change', '#emp_status', function(e) {
	    var emp_status = $(this).val();
		$.ajax({
			url: base_url + controller + '/getEmployeeData',
			data: {emp_status: emp_status},
			type: "POST",
			dataType: 'json',
			success: function(data) {
				$("#reportTable").dataTable().fnDestroy();
				$("#tbodyData").html(data.tbody);
				reportTable();
			}
		});
	});
});
function reportTable(){
	var reportTable = $('#reportTable').DataTable( {
		responsive: true,
		"autoWidth" : false,
		order:[],
		"columnDefs": 	[
							{ type: 'natural', targets: 0 },
							{ orderable: false, targets: "_all" }, 
							{ className: "text-left", targets: [0,1] }, 
							{ className: "text-center", "targets": "_all" } 
						],
		pageLength:25,
		language: { search: "" },
		lengthMenu: [
            [ 10, 25, 50, 100, -1 ],[ '10 rows', '25 rows', '50 rows', '100 rows', 'Show all' ]
        ],
		dom: "<'row'<'col-sm-7'B><'col-sm-5'f>>" +"<'row'<'col-sm-12't>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
		buttons: [ 'pageLength', 'excel', {text: 'Refresh',action: function ( e, dt, node, config ) {loadAttendanceSheet();}}]
	});
	reportTable.buttons().container().appendTo( '#reportTable_wrapper toolbar' );
	$('.dataTables_filter .form-control-sm').css("width","97%");
	$('.dataTables_filter .form-control-sm').attr("placeholder","Search.....");
	$('.dataTables_filter').css("text-align","left");
	$('.dataTables_filter label').css("display","block");
	$('.btn-group>.btn:first-child').css("border-top-right-radius","0");
	$('.btn-group>.btn:first-child').css("border-bottom-right-radius","0");
	return reportTable;
}
</script>