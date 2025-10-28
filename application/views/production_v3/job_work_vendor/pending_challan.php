<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-5">
                                <ul class="nav nav-pills">
									<li class="nav-item"> <a href="<?=base_url('production_v3/jobWorkVendor/pendingChallan')?>" class="btn waves-effect waves-light btn-outline-info active float-right mr-1">Pending</a> </li>
                                    
                                    <li class="nav-item"> <a href="<?=base_url('production_v3/jobWorkVendor/vendorReceiveIndex')?>" class="btn waves-effect waves-light btn-outline-info  float-right mr-1">Vendor Receive</a> </li>
                                    <li class="nav-item"> <a href="<?=base_url('production_v3/jobWorkVendor/index/0')?>" class="btn waves-effect waves-light btn-outline-info  float-right mr-1">Inprocess</a> </li>
                                    <li class="nav-item"> <a href="<?=base_url('production_v3/jobWorkVendor/index/1')?>" class="btn waves-effect waves-light btn-outline-info  float-right ">Completed</a> </li>
                                </ul>
                            </div>
                            <div class="col-md-2 form-group">
                                <h4 class="card-title text-left">Outsource</h4>
                            </div>   
                            <div class="col-md-5">
                                <div class="input-group">
                                    <select name="party_id" id="party_id" class="form-control single-select" style="width:70%;">
                                        <option value="">Select Vendor</option>
                                        <?php
                                        foreach ($vendorData as $row) :
                                            echo '<option value="' . $row->id . '">' . $row->party_name . '</option>';
                                        endforeach;
                                        ?>
                                    </select>
                                    <div class="input-group-append">
							            <?php if($shortYear == CURRENT_FYEAR): ?>
                                            <button type="button" class="btn waves-effect waves-light btn-success float-right createVendorChallan" title="Create Challan">
    									        <i class="fa fa-plus"></i> Create Challan
    								        </button>
    								    <?php endif; ?>
                                    </div>
                                </div>
                            </div> 
                            <!-- <div class="col-md-6">
                                <h4 class="card-title">Vendor Challan</h4>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <select name="party_id" id="party_id" class="form-control single-select" style="width:70%;">
                                        <option value="">Select Vendor</option>
                                        <?php
                                        foreach ($vendorData as $row) :
                                            echo '<option value="' . $row->id . '">' . $row->party_name . '</option>';
                                        endforeach;
                                        ?>
                                    </select>
                                    <div class="input-group-append">
							            <?php if($shortYear == CURRENT_FYEAR): ?>
                                            <button type="button" class="btn waves-effect waves-light btn-success float-right createVendorChallan" title="Create Challan">
    									        <i class="fa fa-plus"></i> Create Challan
    								        </button>
    								    <?php endif; ?>
                                    </div>
                                </div>
                            </div> -->
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row"> 
                            <div class="col-md-12"> 
                                <div class="table-responsive">
                                    <table id='vendorChallanTable' class="table table-bordered ssTable" data-url='/getPendingChallanDTRows'></table>
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
<script src="<?php echo base_url(); ?>assets/js/custom/e-bill.js?v=<?= time() ?>"></script>
<script>
$(document).ready(function(){
    $(document).on('click','.createVendorChallan',function(e){
		$(".error").html("");
		var valid = 1;
		var item_id = $('#item_id').val();
		var party_id = $('#party_id').val();
		var party_name = $('#party_idc').val();
		if($("#party_id").val() == "" || $("#party_id").val() == 0){$(".party_id").html("Party is required.");valid=0;}
        var modalId = 'modal-xl';
        var formId = 'VendorChallan';
		if(valid)
		{
            $.ajax({
                url : base_url + controller +'/createVendorChallan',
                type: 'post',
                data:{party_id:party_id},
                success:function(response){
                    $("#"+modalId).modal();
                    $("#"+modalId+' .modal-title').html('Create Challan For : '+party_name);
                    $("#"+modalId+' .modal-body').html("");
                    $("#"+modalId+' .modal-body').html(response);
                    $("#"+modalId+" .modal-body form").attr('id',formId);
                    $("#"+modalId+" .modal-footer .btn-save").attr('onclick',"store('"+formId+"','saveVendorChallan');");
                    $("#"+modalId+" .modal-footer .btn-close").show();
                    $("#"+modalId+" .modal-footer .btn-save").show();
                    $(".single-select").comboSelect();
                    $("#"+modalId+" .scrollable").perfectScrollbar({suppressScrollX: true});
                    setTimeout(function(){ initMultiSelect();setPlaceHolder(); }, 5);
                }
            });
        }
    });  
    
    $(document).on("change","#process_id",function(e){
        var process_id = $("#process_id").val();
        var vendor_id = $("#vendor_id").val();
        if(process_id){
            $.ajax({
                url : base_url + controller +'/getJobworkOrderList',
                type: 'post',
                data:{vendor_id:vendor_id,process_id,process_id},
                dataType: 'json',
                success:function(data){
                    // $("#jobwork_order_id").html("");
                    // $("#jobwork_order_id").html(data.options);
                    // $("#jobwork_order_id").comboSelect();

                    $("#challanTbody").html("");
                    $("#challanTbody").html(data.tbodyData);
                    $(".single-select").comboSelect();
		            initMultiSelect();
                }
            });
        }
    });

    $(document).on("click", ".challanCheck", function() {
        var id = $(this).data('rowid');
        $(".error").html("");
        if (this.checked) {
            $("#ch_qty" + id).removeAttr('disabled');            
            $("#w_pcs" + id).removeAttr('disabled');
            $("#weight" + id).removeAttr('disabled');
            $("#process_ids_" + id).removeAttr('disabled');
            $("#processSelect_" + id).removeAttr('disabled');
        } else {
            $("#ch_qty" + id).attr('disabled', 'disabled');
            $("#w_pcs" + id).attr('disabled', 'disabled');
            $("#weight" + id).attr('disabled', 'disabled');
            $("#process_ids_" + id).attr('disabled', 'disabled');
            $("#processSelect_" + id).attr('disabled', 'disabled');
        }
    });

    $(document).on("keyup", ".challanQty", function() {
        var id = $(this).data('rowid');
        var ch_qty = $("#ch_qty" + id).val();
        var out_qty = $("#out_qty" + id).val();
        if (parseFloat(ch_qty) > parseFloat(out_qty)) {
            $("#ch_qty" + id).val('0');
        }
    });
});

function AddRow() {
    var valid = 1;
	$(".error").html("");
    
    if($("#item_id").val() == ""){$(".item_id").html("Packing Material is required.");valid = 0;}
	if($("#out_qty").val() == "" || $("#out_qty").val() == 0){$(".out_qty").html("qty is required.");valid = 0;}
	
	if(valid)
	{
        $(".item_id").html("");
        $(".out_qty").html("");
        //Get the reference of the Table's TBODY element.
        $("#packingBom").dataTable().fnDestroy();
        var tblName = "packingBom";
        
        var tBody = $("#"+tblName+" > TBODY")[0];
        
        //Add Row.
        row = tBody.insertRow(-1);
        
        //Add index cell
        var countRow = $('#'+tblName+' tbody tr:last').index() + 1;
        var cell = $(row.insertCell(-1));
        cell.html(countRow);
        
        cell = $(row.insertCell(-1));
        cell.html($("#item_idc").val() + '<input type="hidden" name="item_id[]" value="'+$("#item_id").val()+'">');

       
        var out_qtyErrorDiv = $("<div></div>",{class:"error out_qty"+countRow});
        cell = $(row.insertCell(-1));
        cell.html($("#out_qty").val() + '<input type="hidden" name="out_qty[]" value="'+$("#out_qty").val()+'">');
	    cell.append(out_qtyErrorDiv);

        //Add Button cell.
        cell = $(row.insertCell(-1));
        var btnRemove = $('<button><i class="ti-trash"></i></button>');
        btnRemove.attr("type", "button");
        btnRemove.attr("onclick", "Remove(this);");
        btnRemove.attr("class", "btn btn-sm btn-outline-danger waves-effect waves-light");
        cell.append(btnRemove);
        cell.attr("class","text-center");
        $("#item_id").val("");
        $("#item_idc").val("");
        $("#out_qty").val("");
	}
};

function Remove(button) {
    //Determine the reference of the Row using the Button.
	var row = $(button).closest("TR");
	var table = $("#packingBom")[0];
	table.deleteRow(row[0].rowIndex);
	$('#packingBom tbody tr td:nth-child(1)').each(function(idx, ele) {
        ele.textContent = idx + 1;
    });
};
</script>