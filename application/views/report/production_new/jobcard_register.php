<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title pageHeader"><?=$pageHeader?></h4>
                            </div> 
                            <div class="col-md-3">   
                                <input type="date" name="from_date" id="from_date" class="form-control"  value="<?=date('Y-m-01')?>" />
                                <div class="error fromDate"></div>
                            </div>     
                            <div class="col-md-3">  
                                <div class="input-group">
                                    <input type="date" name="to_date" id="to_date" class="form-control" value="<?=date('Y-m-t')?>" />
                                    <div class="input-group-append">
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
                                    <tr class="text-center">
                                        <th colspan="11">Job Card Register</th>
                                        <th colspan="3">F PL 09 (00/01.06.2020)</th>
                                    </tr>
									<tr>
										<th style="min-width:25px;">#</th>
										<th style="min-width:80px;">PRC No.</th>
										<th style="min-width:80px;">Issue Date</th>
										<th style="min-width:100px;">Part No.</th>
										<th style="min-width:100px;">Customer</th>
										<th style="min-width:80px;">Challan No.<small>(Vendor)</small></th>
										<th style="min-width:80px;">Heat No.</th>
										<th style="min-width:50px;">Weight</th>
										<th style="min-width:50px;">Quantity</th>
										<th style="min-width:50px;">Ok Qty</th>
										<th style="min-width:50px;">Rejection Qty</th>
										<th style="min-width:50px;">Short Qty</th>
										<th style="min-width:80px;">Issued By</th>
										<th style="min-width:100px;">Remark</th>
									</tr>
								</thead>
								<tbody id="tbodyData">
									<?=$jobRegHtml?>
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
	
	$(document).on('click','.loaddata',function(e){
		
		
		var from_date = $('#from_date').val();
		var to_date = $('#to_date').val();
		var valid = 1;
		
		if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
		if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
		if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}
		
		if(valid)
		{
			$.ajax({
				url: base_url + controller + '/jobcardRegister',
				data: {from_date:from_date,to_date:to_date},
				type: "POST",
				dataType:'json',
				success:function(data){
					$("#reportTable").dataTable().fnDestroy();
					$("#tbodyData").html(data.tbody);
					reportTable();
				}
			});
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