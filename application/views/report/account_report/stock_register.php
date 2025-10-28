<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row form-group">
                            <div class="col-md-12">
                                <h4 class="card-title pageHeader text-center"><?=$pageHeader?></h4>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-4">  
                                <div class="input-group">
    								<select id="stock_type" class="form-control single-select req" style="width:30%;">
                                        <option value="">Select All</option>
                                        <option value="1">Without Zero</option>
                                        <option value="0">Only Zero</option>
                                    </select>
    								<select id="item_type" name="item_type" class="form-control single-select req" style="width:30%;">
        								<option value="">Select Item Type</option>
                                            <?php
        										foreach($itemGroup as $row):
        										    $group_name = ($row->id == 1) ? 'Ready To Dispatch':$row->group_name;
        											echo '<option value="'.$row->id.'">'.$group_name.'</option>';
        										endforeach;  
                                            ?>
        								<option value="-1">WIP RM</option>
        								<option value="-2">Packing Area</option>
                                    </select>
                                    <select id="party_id" name="party_id" class="form-control single-select req" style="width:40%;">
        								<option value="">Select Customer</option>
                                        <?php
    										foreach($customerList as $row):
    											if(!empty($row->party_code)){echo '<option value="'.$row->id.'">'.$row->party_code.'</option>';}
    										endforeach;  
                                        ?>
                                    </select>
                                </div>   
                            </div>
                            <div class="col-md-4">  
								    <select id="itemid" data-input_id="item_id" class="form-control jp_multiselect req" multiple="multiple">
        								
                                    </select>
								    <input type="hidden" name="item_id" id="item_id" value="" />
							</div>
                            <div class="col-md-4"> 	    
                                <div class="input-group">
                                    <input type="text" name="min_amt" id="min_amt" class="form-control floatOnly" value="" style="width:25%;" placeholder="Min Amount" />
                                    <input type="text" name="max_amt" id="max_amt" class="form-control floatOnly" value="" style="width:25%;" placeholder="Max Amount" />
                                    <input type="date" name="to_date" id="to_date" class="form-control" value="<?=date('Y-m-d')?>" style="width:30%;" />
                                    <button type="button" class="btn waves-effect waves-light btn-success float-right loaddata" title="Load Data" style="width:20%;" >
								        <i class="fas fa-sync-alt"></i>
							        </button>
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
										<th>Used Qty.</th>
										<th>Balance Qty.</th>
										<th>Amount</th>
									</tr>
								</thead>
								<tbody id="tbodyData"></tbody>
								<tfoot class="thead-info" id="tfootData">
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
	
	$(document).on('change','#item_type',function(){
	    var item_type = $(this).val();
	    
	    $(".error").html("");
		var valid = 1;
		if($("#item_type").val() == ""){$(".item_type").html("Item Type is required.");valid=0;}
		
	    if(valid){
            $.ajax({
                url: base_url + controller + '/getItemFromItemType',
                data: {item_type:item_type},
				type: "POST",
				dataType:'json',
				success:function(data){
					$("#itemid").html("");
					$("#itemid").html(data.options);
					reInitMultiSelect();
					//$("#item_id").comboSelect();
                }
            });
        }
	});
	
	
	$(document).on('change','#party_id',function(){
	    var party_id = $(this).val();
	    var item_type = $('#item_type').val();   
		
	    if(party_id){
            $.ajax({
                url: base_url + controller + '/getItemFromParty',
                data: {party_id:party_id,item_type:item_type},
				type: "POST",
				dataType:'json',
				success:function(data){
					$("#itemid").html("");
					$("#itemid").html(data.options);
					reInitMultiSelect();
					//$("#item_id").comboSelect();
                }
            });
        }
	});
	
    $(document).on('click','.loaddata',function(e){
		$(".error").html("");
		var valid = 1;
		var to_date = $('#to_date').val();
		var party_id = $('#party_id').val();   
		var item_type = $('#item_type').val();   
		var item_id = $('#item_id').val();   
		var min_amt = $('#min_amt').val();   
		var max_amt = $('#max_amt').val();   
		var stock_type = $('#stock_type').val();   
		if($("#item_type").val() == ""){$(".item_type").html("Item Type is required.");valid=0;}
		if($("#to_date").val() == ""){$(".toDate").html("Date is required.");valid=0;}
	
		if(valid)
		{
            $.ajax({
                url: base_url + controller + '/getStockRegister',
                data: {stock_type:stock_type,item_type:item_type,item_id:item_id,party_id:party_id,min_amt:min_amt,max_amt:max_amt,to_date:to_date},
				type: "POST",
				dataType:'json',
				success:function(data){
                    $("#reportTable").dataTable().fnDestroy();
					$("#tbodyData").html(data.tbody);
					$("#theadData").html(data.thead);
					$("#tfootData").html(data.tfoot);
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