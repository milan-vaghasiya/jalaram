<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
						<div class="row">
                        	<div class="col-md-12">
                                <h4 class="card-title pageHeader text-center"><?=$pageHeader?></h4>
                            </div>  
                        </div>     
						<hr>
						<div class="row">
						    <div class="col-md-2">   
                                <label for="issue_tpye">Issue Type</label>
                                <select name="issue_tpye" id="issue_tpye" class="form-control single-select">
                                    <option value="0">General Issue</option>
                                    <option value="1">Tool Issue</option>
                                </select>
                            </div>
							<div class="col-md-2">
								<label for="dept_id">Department</label>
                                <select name="dept_id" id="dept_id" class="form-control single-select">
                                 <option value="">ALL</option>
                                    <?php
                                        foreach($deptData as $row):
                                          $selected = "";
                                             echo '<option value="'.$row->id.'" '.$selected.'>'.$row->name.'</option>';
                                     	endforeach;
                                    ?>
                                </select>
                                <div class="error dept_id"></div>
							</div>
							<div class="col-md-2">
								<label for="item_type">Item Type</label>
								<select id="item_type" class="from-control single-select">
									<option value="">ALL</option>
									<option value="2">Consumable</option>
									<option value="3">Raw Material</option>
								</select>
							</div>
							<div class="col-md-2">
							    <label for="dept_id">Item Category</label>
								<select name="catSelect" id="catSelect" data-input_id="category_id" class="form-control jp_multiselect req" multiple="multiple">
								    
                                </select>
                                <input type="hidden" name="category_id" id="category_id" value="" />
							</div>     
                            <div class="col-md-4">  
                                <div class="input-group">
								    <label for="from_date" style="width:40%;">From Date</label>
								    <label for="to_date" style="width:40%;"> To Date</label>
								</div>
                                <div class="input-group">
                                    <input type="date" name="from_date" id="from_date" class="form-control" max="<?=date('Y-m-01')?>" value="<?=date('Y-m-d')?>" style="width:40%;" />
                                    <input type="date" name="to_date" id="to_date" class="form-control" value="<?=date('Y-m-d')?>" style="width:40%;" />
                                    <div class="input-group-append">
                                        <button type="button" class="btn waves-effect waves-light btn-success float-right loaddata" title="Load Data">
									        <i class="fas fa-sync-alt"></i>
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
                                    <tr class="text-center" id="theadData">
                                        <th colspan="7">Issue Register (Consumable/Raw Material)</th>
                                        <th colspan="2">F ST 04 (00/01.06.20)</th>
                                    </tr>
									<tr>
										<th>#</th>
										<th>Date</th>
										<th>Item Description</th>
										<th>Department</th>
										<th>Issued Qty.</th>
										<th>Receiver's Name</th>
										<th>Remark</th>
										<th>Price</th>
										<th>Total</th>
									</tr>
								</thead>
								<tbody id="tbodyData"></tbody>
								<tfoot id="tfootData">
								    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
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
<?=$floatingMenu?>
<script>
$(document).ready(function(){
	reportTable();

	$(document).on('change','#item_type', function(e){
		var item_type = $(this).val();
		if(item_type){
			$.ajax({
                url: base_url + 'reports/purchaseReport/getCategoryList',
                data: {item_type:item_type},
				type: "POST",
				dataType:'json',
				success:function(data){
					$("#catSelect").html("");
					$("#catSelect").html(data.options);
					reInitMultiSelect();
                }
            });
		}
	});

    $(document).on('click','.loaddata',function(e){
		$(".error").html("");
		var valid = 1;
		var dept_id = $('#dept_id').val();
		var item_type = $('#item_type').val();
		var from_date = $('#from_date').val();
		var to_date = $('#to_date').val();
		var category_id = $('#category_id').val();
		var issue_tpye = $('#issue_tpye').val();
		if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
		if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
		if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}
		if(valid)
		{
            $.ajax({
                url: base_url + controller + '/getIssueRegister',
                data: {dept_id:dept_id,item_type:item_type,category_id:category_id,issue_tpye:issue_tpye,from_date:from_date, to_date:to_date},
				type: "POST",
				dataType:'json',
				success:function(data){
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