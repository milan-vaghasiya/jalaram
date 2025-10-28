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
                            <div class="col-md-3 ">
                                    <select id="process_id" class="form-control model-select2">
                                        <option value="" data-store_name="">Select Process</option>
										<optgroup label="Process">
                                            <?php
                                                foreach ($processData as $row) :
                                                   ?><option value="<?=$row->id?>" data-stock_type="PROCESS"><?=$row->process_name?></option><?php
                                                endforeach;
    										?>
										</optgroup>
										<optgroup label="Vendor">
    										<?php
        										foreach($vendorData as $row):
        											?><option value="<?=$row->id?>" data-stock_type="VENDOR"><?=$row->party_name?></option><?php
        										endforeach;
                                            ?>
										</optgroup>
                                    </select>
                                <div class="error process_id"></div>
							</div>    
                            <div class="col-md-4">  
                                <div class="input-group">
                                    <select id="party_id" class="form-control single-select" style="width:70%;">
                                        <option value="">Select All</option>
                                        <?php
                                            foreach($partyList as $row):
                                                echo '<option value="'.$row->id.'">'.$row->party_code.'</option>';    
                                            endforeach;
                                        ?>
                                    </select>
                                    <div class="input-group-append">
                                        <button type="button" class="btn waves-effect waves-light btn-success float-right loaddata" title="Load Data">
                                            <i class="fas fa-sync-alt"></i> Load
                                        </button>
                                    </div>
                                </div>
                            </div>                  
                        </div>                                         
                    </div>
                    <div class="card-body reportDiv" style="min-height:75vh">
                        <div class="table-responsive">
                            <table id='reportTable' class="table table-bordered">
								<thead class="thead-info" id="theadData">
                                    <tr class="text-center">
                                        <th colspan="6">In Process Stock Statement</th>
                                    </tr>
									<tr>
										<th>#</th>
										<th>Part No.</th>
										<th>Part Name</th>
										<th>Batch No.</th>
										<th>Stock Qty.</th>
										<th>Qty(kg)</th>
									</tr>
								</thead>
								<tbody id="tbodyData"></tbody>
								<tfoot id="tfootData">
								    <tr class="thead-info">
										<th class="text-right" colspan="4">Total</th>
										<th>0</th>
										<th>0</th>
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
<?=$floatingMenu?>
<script>
$(document).ready(function(){
	reportTable();
    $(document).on('click','.loaddata',function(e){
		$(".error").html("");
		var valid = 1;
		var process_id = $('#process_id').val();
		var stock_type = $("#process_id").find(":selected").data('stock_type');
		var party_id = $("#party_id :selected").val();
		if($("#process_id").val() == ""){$(".process_id").html("Process is required.");valid=0;}
		
		if(valid)
		{
            $.ajax({
                url: base_url + controller + '/getInProcessStockData',
                data: {process_id:process_id,stock_type:stock_type,party_id:party_id},
				type: "POST",
				dataType:'json',
				success:function(data){
                    $("#reportTable").dataTable().fnDestroy();
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
							{ className: "text-left", targets: [0,2] }, 
							{ className: "text-center", "targets": "_all" } 
						],
		pageLength:25,
		language: { search: "" },
		lengthMenu: [
            [ 10, 25, 50, 100, -1 ],[ '10 rows', '25 rows', '50 rows', '100 rows', 'Show all' ]
        ],
		dom: "<'row'<'col-sm-7'B><'col-sm-5'f>>" +"<'row'<'col-sm-12't>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
		buttons: [ 'pageLength', 'excel', {text: 'Refresh',action: function ( e, dt, node, config ) {$(".loaddata").trigger("click")}}]
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