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
							<div class="col-md-2">
								<select id="stock_type" class="form-control single-select req">
                                    <option value="">Select All</option>
                                    <option value="1">Without Zero</option>
                                    <option value="0">Only Zero</option>
                                </select>
							</div> 
							<input id="item_type" type="hidden" value="">
							<div class="col-md-2">
								<select id="item_type" data-input_id="item_type" class="form-control jp_multiselect req" multiple="multiple">
                                    <?php
										foreach($itemGroup as $row):
											echo '<option value="'.$row->id.'">'.$row->group_name.'</option>';
										endforeach;  
                                    ?>
                                </select>
								<input type="hidden" name="process_id" id="process_id" value="<?=(!empty($dataRow->process_id))?$dataRow->process_id:"" ?>" />
							</div> 
                       
                            <div class="col-md-3">  
                                <div class="input-group">
                                    <input type="date" name="to_date" id="to_date" class="form-control" value="<?=date('Y-m-d')?>" />
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
                                        <th colspan="7">Stock Register</th>
                                    </tr>
									<tr>
										<th>#</th>
										<th>Item Description</th>
										<th>Receipt Qty.</th>
										<th>Issued Qty.</th>
										<th>Balance Qty.</th>
										<th>Amount</th>
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
		var to_date = $('#to_date').val();
		var item_type = $('#item_type').val();   
		var stock_type = $('#stock_type').val();   
		if($("#item_type").val() == ""){$(".item_type").html("Item Type is required.");valid=0;}
		if($("#to_date").val() == ""){$(".toDate").html("Date is required.");valid=0;}
	
		if(valid)
		{
            $.ajax({
                url: base_url + controller + '/getStockRegister',
                data: {stock_type:stock_type,item_type:item_type,to_date:to_date},
				type: "POST",
				dataType:'json',
				success:function(data){
                    $("#reportTable").dataTable().fnDestroy();
					$("#tbodyData").html(data.tbody);
					$("#theadData").html(data.thead);
					$(".totalInventory").html(data.totalInventory);
					$(".totalUP").html(data.totalUP);
					$(".totalValue").html(data.totalValue);
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