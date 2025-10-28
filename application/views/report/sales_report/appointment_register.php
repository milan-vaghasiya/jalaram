<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-3">
                                <h4 class="card-title pageHeader"><?=$pageHeader?></h4>
                            </div> 
                            <div class="col-md-2">
                                <select id="status" class="form-control single-select">
                                    <option value="0">All</option>
                                    <option value="1">Pending</option>
                                    <option value="2">Completed</option>
                                    <option value="3">Delay</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                            <select name="mode" id="mode" class="form-control single-select">
                            <option value="0">All Mode</option>
                            <?php
                                foreach($mode as $key=>$mode):
                                    echo '<option value="'.$mode.'">'.$mode.'</option>';
                                endforeach;
                            ?>
                            </select>
                            </div>
                            <div class="col-md-2">   
                                <input type="date" name="from_date" id="from_date" class="form-control" value="<?=date('Y-m-01')?>" />
                                <div class="error fromDate"></div>
                            </div>     
                            <div class="col-md-3">  
                                <div class="input-group">
                                    <input type="date" name="to_date" id="to_date" class="form-control" value="<?=date('Y-m-d')?>" />
                                    <div class="input-group-append ml-1">
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
                                <thead class="thead-info">
                                    <tr>
                                        <th>Reminder Date</th>
                                        <th>Party Name</th>
                                        <th>Mode</th>
                                        <th>Notes</th>
                                        <th>Response</th>
                                        <th>Response Date</th>
                                        <th>Due Days</th>
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
<script>
$(document).ready(function(){
	reportTable();
    $(document).on('click','.loaddata',function(e){
		$(".error").html("");
		var valid = 1;
		var status = $('#status').val();
		var mode = $('#mode').val();
        var from_date = $('#from_date').val();
	    var to_date = $('#to_date').val();
        if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
	    if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
	    if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}

        var sendData = {status:status,mode:mode, from_date:from_date, to_date:to_date};

		if(valid){
            $.ajax({
                url: base_url + controller + '/getAppointmentRegister',
                data: sendData,
                type: "POST",
                dataType:'json',
                success:function(data){
                    $("#reportTable").DataTable().clear().destroy();
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
		buttons: [ 'pageLength', 'excel'],
		"initComplete": function(settings, json) {$('body').find('.dataTables_scrollBody').addClass("ps-scrollbar");}
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