<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-4">
                                <h4 class="card-title pageHeader"><?=$pageHeader?></h4>
                            </div>   
                            <div class="col-md-3 form-group">
								<select name="job_id" id="job_id" class="form-control single-select">
                                    <option value="">Select ALL Job Card</option>
                                    <?php   
										foreach($jobcardData as $row): 
											echo '<option value="'.$row->id.'">'.getPrefixNumber($row->job_prefix,$row->job_no).' ['.$row->item_code.']</option>';
										endforeach; 
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-5">  
                                <div class="input-group">
                                    <input type="date" name="from_date" id="from_date" class="form-control" max="<?=date('Y-m-d')?>" value="<?=date('Y-m-d')?>" />
                                    <input type="date" name="to_date" id="to_date" class="form-control" max="<?=date('Y-m-d')?>" value="<?=date('Y-m-d')?>" />
                                    <div class="input-group-append">
                                        <button type="button" class="btn waves-effect waves-light btn-success float-right loaddata" title="Load Data" data-type="1">
									        <i class="fas fa-sync-alt"></i> Load
								        </button>
                                    </div>
                                </div>
                                <div class="error fromDate"></div>
                                <div class="error toDate"></div>
                            </div>                  
                        </div>                                         
                    </div>
                    <div class="card-body reportDiv" style="min-height:75vh">
                        <div class="table-responsive">
                            <table id='reportTable' class="table table-bordered">
								<thead class="thead-info" id="theadData">
                                    <tr class="text-center">
                                        <th colspan="13">Final Inspection NC Report</th>
										<th colspan="4">FQA 06 - 00(01/06/2020)</th>
                                    </tr>
									<tr>
										<th>Date</th>
										<th>Part</th>
										<th>Inspection Type</th>
										<th>Jobcard</th>
										<th>Inspected Qty.</th>
										<th>OK Qty.</th>
										<th>Rework Qty.</th>
                                        <th>Rework Reason</th>
                                        <th>Defect belong to Rework</th>
                                        <th>Total Rej.</th>
                                        <th>Rej. Qty</th>
                                        <th>Rej. Reason</th>
                                        <th>Defect belong to Rej.</th>
                                        <th>Inspector</th>
                                        <th>Supervisor</th>
                                        <th>Rej. Cost</th>
                                        <th>Inspection Time(In Hours.)</th>
									</tr>
								</thead>
								<tbody id="tbodyData"></tbody>
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
    $(document).on('click','.loaddata',function(e){
		$(".error").html("");
		var valid = 1;
		var from_date = $('#from_date').val();
		var to_date = $('#to_date').val();
		var type = $(this).data('type');
		var job_id = $('#job_id').val();
		if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
		if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
		if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}
		
		var sendData = {job_id:job_id,from_date:from_date, to_date:to_date, type:type};
		if(valid)
		{
		    if(type == 2){
				var url =  base_url + controller + '/getNCReportData/' + encodeURIComponent(window.btoa(JSON.stringify(sendData)));
				window.open(url);
			} else {
                $.ajax({
                    url: base_url + controller + '/getNCReportData',
                    data: sendData,
    				type: "POST",
    				dataType:'json',
    				success:function(data){
                        $("#reportTable").dataTable().fnDestroy();
    					$("#tbodyData").html(data.tbodyData);
    					reportTable();
                    }
                });
			}
        }
    });   
});
function reportTable()
{
	var reportTable = $('#reportTable').DataTable( 
	{
		responsive: true,
		scrollY: '55vh',
        scrollCollapse: true,
		"scrollX": true,
		"scrollCollapse":true,
		//'stateSave':true,
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
		buttons: [ 'pageLength', {text: 'Refresh',action: function ( e, dt, node, config ) { }}]
	});
	var excelBtn = '<button class="btn btn-outline-primary loaddata" data-type="2" type="button"><span>EXCEL</span></button>';
    reportTable.buttons().container().append(excelBtn);
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