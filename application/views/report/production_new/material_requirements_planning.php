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
                            <div class="col-md-6">
								<div class="input-group">
									<select name="item_id" id="item_id" class="form-control single-select" style="width:80%;">
										<option value="">Select Item</option>
                                        <?php
                                            foreach($itemList as $row):
                                                echo '<option value="'.$row->item_id.'" data-order_qty="'.$row->order_qty.'" data-dispatch_qty="'.$row->dispatch_qty.'" data-pending_qty="'.$row->pending_qty.'">'.$row->item_code.'</option>';
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
                        <div class="row">
                            <div class="col-md-12 form-group">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="thead-info" id="theadData">
                                            <tr>
                                                <th>Item Code</th>
                                                <th>Order Qty.</th>
                                                <th>Dispatch Qty.</th>
                                                <th>Stock Qty.</th>
                                                <th>WIP Qty.</th>
                                                <th>Req. Qty.</th>
                                            </tr>
                                        </thead>
                                        <tbody id="summaryData">
                                            <tr>
                                                <td id="item_code">-</td>
                                                <td id="ord_qty"></td>
                                                <td id="disp_qty"></td>
                                                <td id="stock_qty"></td>
                                                <td id="wip_qty"></td>
                                                <td id="req_qty"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="col-md-12 form-group">
                                <div class="table-responsive">
                                    <table id='stockDetails' class="table table-bordered">
                                        <thead class="thead-info" id="theadData">
                                            <tr class="text-center">
                                                <th colspan="4">Stock Details</th>
                                            </tr>
                                            <tr>
                                                <th style="min-width:25px;">#</th>
                                                <th style="min-width:100px;">Location</th>
                                                <th style="min-width:100px;">Batch No.</th>
                                                <th style="min-width:50px;">Qty</th>
                                            </tr>
                                        </thead>
                                        <tbody id="stockData">
                                            <tr>
                                                <td colspan="4" class="text-center">
                                                    No data available in table
                                                </td>
                                            </tr>
                                        </tbody>
                                        <tfoot class="thead-info" id="stockFooter">
                                            <tr>
                                                <th colspan="3" class="text-right">Total</th>
                                                <th>0</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            <div class="col-md-12 form-group">
                                <div class="table-responsive">
                                    <table id='wipDetails' class="table table-bordered">
                                        <thead class="thead-info" id="theadData">
                                            <tr class="text-center">
                                                <th colspan="3">WIP Details</th>
                                            </tr>
                                            <tr>
                                                <th style="min-width:25px;">#</th>
                                                <th style="min-width:100px;">Store</th>
                                                <th style="min-width:50px;">Qty</th>
                                            </tr>
                                        </thead>
                                        <tbody id="wipData">
                                            <tr>
                                                <td colspan="3" class="text-center">
                                                    No data available in table
                                                </td>
                                            </tr>
                                        </tbody>
                                        <tfoot class="thead-info" id="wipFooter">
                                            <tr>
                                                <th colspan="2" class="text-right">Total</th>
                                                <th>0</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            <div class="col-md-12 form-group">
                                <div class="table-responsive">
                                    <table id='materialDetails' class="table table-bordered">
                                        <thead class="thead-info" id="theadData">
                                            <tr class="text-center">
                                                <th colspan="4">Material Requirement Details [ Pen. Ord. Qty : <span id="reqQty">0</span> ]</th>
                                            </tr>
                                            <tr>
                                                <th style="min-width:25px;">#</th>
                                                <th style="min-width:100px;">Item Name</th>
                                                <th style="min-width:50px;">Req. Qty</th>
                                                <th style="min-width:50px;">Stock Qty</th>
                                            </tr>
                                        </thead>
                                        <tbody id="materialData">
                                            <tr>
                                                <td colspan="4" class="text-center">
                                                    No data available in table
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

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
    $(document).on('click','.loaddata',function(e){
		$(".error").html("");
		var valid = 1;
		var item_id = $('#item_id').val();
        var pending_qty = $("#item_id :selected").data('pending_qty');

        var item_code = $("#item_id :selected").text();
        var order_qty = $("#item_id :selected").data('order_qty');
        var dispatch_qty = $("#item_id :selected").data('dispatch_qty');
        $("#item_code").html("-");

        $("#reqQty").html(0);
		if($("#item_id").val() == ""){$(".item_id").html("Item is required.");valid=0;}
		if(valid){
            $.ajax({
                url: base_url + controller + '/getMaterialRequirementsPlanning',
                data: {item_id:item_id,pending_qty:pending_qty},
				type: "POST",
				dataType:'json',
				success:function(data){
                    $("#stockData").html(data.stockTbody);
                    $("#stockFooter").html(data.stockTfoot);
                    $("#wipData").html(data.wipTbody);
                    $("#wipFooter").html(data.wipTfoot);
                    $("#materialData").html(data.materialTbody);
                    $("#reqQty").html(data.req_qty);

                    $("#item_code").html(item_code);
                    $("#ord_qty").html(order_qty);
                    $("#disp_qty").html(dispatch_qty);
                    $("#stock_qty").html(data.totalStockQty);
                    $("#wip_qty").html(data.totalWIPQty);
                    $("#req_qty").html(data.req_qty);
                }
            });
        }
    });   
});
</script>