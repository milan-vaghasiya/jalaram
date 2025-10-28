<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row"> 
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-5">
                                <h4 class="card-title pageHeader"><?=$pageHeader?></h4>
                            </div>
                            <div class="col-md-7">  
                                <div class="input-group">
                                    <select id="party_id" name="party_id" class="form-control single-select req" style="width:30%;">
        								<option value="">Select Customer</option>
                                        <?php
    										foreach($customerList as $row):
    											if(!empty($row->party_code)){echo '<option value="'.$row->id.'">'.$row->party_code.'</option>';}
    										endforeach;  
                                        ?>
                                    </select>
									<select id="trans_status" name="trans_status" class="form-control single-select req" style="width:20%;">
        								<option value="ALL">All</option>
                                       	<option value="0">Pending</option>
                                       	<option value="1">Completed</option>
                                    </select>
                                    <input type="date" name="from_date" id="from_date" class="form-control" value="<?=date('Y-m-01')?>" style="width:15%;" />
                                    <div class="error fromDate"></div>
                                    <input type="date" name="to_date" id="to_date" class="form-control" value="<?=date('Y-m-d')?>" style="width:15%;" />
                                    <div class="input-group-append ml-2">
                                        <button type="button" class="btn waves-effect btn-block waves-light btn-success float-right loaddata" title="Load Data">
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
                            <table id='reportTable' class="table table-bordered jdt">
								<thead class="thead-info" id="theadData">
                                    <tr class="text-center">
                                        <th colspan="17">Customer's Order Monitoring Sheet & Summary Report</th>
                                    </tr>
									<tr class="clonTR">
										<th style="min-width:25px;">#</th>
										<th style="min-width:80px;">Cust. PO. Date</th>
										<th style="min-width:50px;">P.O. No</th>
										<th style="min-width:100px;">Customer Code</th>
										<th style="min-width:100px;">Part No.</th>
										<th style="min-width:50px;">Actual Order Qty.</th>
										<th style="min-width:80px;">Exp. Delivery Date</th>
										<th style="min-width:50px;">PPAP Level</th>
										<th style="min-width:50px;">OA No.</th>
										<th style="min-width:80px;">S.O. Entry Date</th>
										<th style="min-width:50px;">Entry by</th>
										<th style="min-width:50px;">Invoice No.</th>
										<th style="min-width:80px;">Actual Delivery Date</th>
										<th style="min-width:50px;">Actual Delivered Qty.</th>
										<th style="min-width:50px;">Total Qty. Delivered</th>
										<th style="min-width:50px;">Qty. Deviation</th>
										<th style="min-width:50px;">Deviation PR(%)</th>
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
	$('.jdt thead .clonTR').clone(true).insertAfter( '.jdt thead tr:eq(1)' );
	var lastIndex = -1;
    $('.jdt thead tr:eq(2) th').each( function (index,value) {
        var title = $(this).text(); //placeholder="'+title+'"
		if(index == lastIndex){$(this).html( '' );}else{$(this).html( '<input type="text" style="width:100%;"/>' );}
	});
	
    $(document).on('click','.loaddata',function(e){
		$(".error").html("");
		var valid = 1;
		var from_date = $('#from_date').val();
		var to_date = $('#to_date').val();
		var party_id = $('#party_id').val();
		var trans_status = $('#trans_status').val();
		if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
		if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
		if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}
		if(valid)
		{
            $.ajax({
                url: base_url + controller + '/getOrderMonitor',
                data: {from_date:from_date, to_date:to_date,party_id:party_id,trans_status:trans_status},
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
	$('.page-wrapper').resizer(function() {reportTable.columns.adjust().draw(); });
	
	//Datatable Column Filter
    $('.jdt thead tr:eq(2) th').each( function (i) {
		$( 'input', this ).on( 'keyup change', function () {
			if ( reportTable.column(i).search() !== this.value ) {reportTable.column(i).search( this.value ).draw();}
		});
	} );
	
	return reportTable;
}
</script>