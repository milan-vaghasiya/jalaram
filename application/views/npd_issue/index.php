<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-4">
								<a href="<?= base_url($headData->controller . "/index/0") ?>" class="btn waves-effect waves-light btn-outline-primary permission-write <?=($status == 0)?'active':''?>">Pending Request</a>
								<a href="<?= base_url($headData->controller . "/index/1") ?>" class="btn waves-effect waves-light btn-outline-primary permission-write  <?=($status == 1)?'active':''?>">Issue Material</a>
                            </div>
                            <div class="col-md-4">
                                <h4 class="card-title text-center">NPD Issue</h4>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn waves-effect waves-light btn-outline-primary float-right addNew permission-write mr-2" data-button="both" data-modal_id="modal-lg" data-function="addPurchaseRequest" data-form_title="Purchase Request" data-fnsave="savePurchaseRequest"><i class="fa fa-plus"></i> Purchase Request</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='generalIssueTable' class="table table-bordered ssTable" data-url='/getDTRows/<?=$status?>'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>

<!-- <script src="<?php echo base_url(); ?>assets/js/custom/general-material-issue.js?v=<?= time() ?>"></script> -->
<script>
    $(document).ready(function() {
        $(document).on('click', '.viewMaterialIssueTrans', function() {
            var button = "close";
            var id = $(this).data("id");
            console.log(id);
            $.ajax({
                type: "POST",
                url: base_url + controller + '/viewMaterialIssueTrans',
                data: {
                    id: id
                }
            }).done(function(response) {
                $("#modal-lg").modal();
                $('#modal-lg .modal-title').html("General Issue");
                $('#modal-lg .modal-body').html(response);
                $("#modal-lg  .modal-body form").attr('id', 'trans_form');

                if (button == "close") {
                    $("#modal-lg .modal-footer .btn-close").show();
                    $("#modal-lg .modal-footer .btn-save").hide();
                } else if (button == "save") {
                    $("#modal-lg .modal-footer .btn-close").hide();
                    $("#modal-lg .modal-footer .btn-save").show();
                } else {
                    $("#modal-lg .modal-footer .btn-close").show();
                    $("#modal-lg .modal-footer .btn-save").show();
                }
                $(".single-select").comboSelect();
                initMultiSelect();
                setPlaceHolder();
            });
        });

        $(document).on("change","#location_id",function(){
            var item_id = $("#item_id").val();
            var location_id = $(this).val();
            $(".location_id").html("");
            $(".req_item_id").html("");
            $("#batch_stock").val("");
            
            if(location_id == ""){
              
                if(location_id == ""){
                    $(".location_id").html("Location is required.");
                }
            }else{
                $.ajax({
                    url:base_url + controller + '/getBatchNo',
                    type:'post',
                    data:{item_id:item_id,location_id:location_id},
                    dataType:'json',
                    success:function(data){
                        $("#batch_no").html("");
                        $("#batch_no").html(data.options);
                        //$("#batch_no").comboSelect();
                        $("#batch_no").select2();
                    }
                });
            }
        });
        $(document).on('change', "#batch_no", function () {
            $("#batch_stock").val("");
            $("#batch_stock").val($("#batch_no :selected").data('stock'));
        });

        $(document).on('click','.addRow',function(){
		var location_id = $("#location_id").val();
		var store_name = $("#location_id :selected").data('store_name');
		var location = $("#location_id :selected").text();
		var location_name = "[ "+store_name+" ] "+location;
		var batch_no = $("#batch_no").val();
		var stock = $("#batch_stock").val();
		var qty = $("#dispatch_qty").val();
		var count_item = $("#count_item").val();
		var job_card_id = $("#job_card_id").val();
		var item_type = $("#dispatch_item_id :selected").data('item_type');
		
		$(".location_id").html("");
		$(".batch_no").html("");
		$(".dispatch_qty").html("");
		$('.general_batch_no').html("");
    		if(location_id == "" || batch_no == "" || qty == "" || qty == "0" || qty == "0.000"){
    			if(location_id == ""){
    				$(".location_id").html("Location is required.");
    			}
    			if(batch_no == ""){
    				$(".batch_no").html("Batch No. is required.");
    			}
    			if(qty == "" || qty == "0" || qty == "0.000"){
    				$(".dispatch_qty").html("Qty. is required.");
    			}
    		}else{
    			var batchNos = $("input[name='batch_no[]']").map(function(){return $(this).val();}).get();
    				if(parseFloat(qty) > parseFloat(stock)){
    					$(".dispatch_qty").html("Stock not avalible.");
    				}else{
    					var qtySum = 0;
    					$(".qtyTotal").each(function(){
    						qtySum += parseFloat($(this).val());
    					});
    					qtySum += parseFloat(qty);
    					var pendingQty = $("#pending_qty").val();
    					var reqQty = $("#req_qty").val();
    					if(parseFloat(reqQty) != 0 && parseFloat(qtySum).toFixed(3) > parseFloat(reqQty).toFixed(3) && item_type == 3){
    						$(".dispatch_qty").html("Invalid Issue qty.");
    					}else{
    						var post = {id:"",batch_no:batch_no,qty:qty,location_id:location_id,location_name:location_name};						
    						addRow(post);
    						$("#count_item").val(parseFloat(count_item) + 1);
    						if(parseFloat(reqQty) != 0){
    							$("#pending_qty").val(parseFloat(parseFloat(pendingQty) - parseFloat(qty)).toFixed(3));
    						}
    						$("#dispatch_qty").val(parseFloat(qtySum).toFixed(3));
    						$("#batch_no").val("");
    						$("#batch_no").select2();
    						$("#batch_stock").val("");
    						$("#dispatch_qty").val("");
    					}
    				}
    			// }
    		}
		//}
	});
	
    });

    function dispatch(data) {
        var button = "";
        $.ajax({
            type: "POST",
            url: base_url + controller + '/edit',
            data: {
                id: data.id
            }
        }).done(function(response) {
            $("#" + data.modal_id).modal();
            $("#" + data.modal_id + ' .modal-title').html(data.title);
            $("#" + data.modal_id + ' .modal-body').html(response);
            $("#" + data.modal_id + " .modal-body form").attr('id', data.form_id);
            $("#" + data.modal_id + " .modal-footer .btn-save").attr('onclick', "store('" + data.form_id + "');");
            if (button == "close") {
                $("#" + data.modal_id + " .modal-footer .btn-close").show();
                $("#" + data.modal_id + " .modal-footer .btn-save").hide();
            } else if (button == "save") {
                $("#" + data.modal_id + " .modal-footer .btn-close").hide();
                $("#" + data.modal_id + " .modal-footer .btn-save").show();
            } else {
                $("#" + data.modal_id + " .modal-footer .btn-close").show();
                $("#" + data.modal_id + " .modal-footer .btn-save").show();
            }
            $(".single-select").comboSelect();
            initMultiSelect();
            setPlaceHolder();
        });
    }

    function addRow(data){
	$('table#issueItems tr#noData').remove();
	var tblName = "issueItems";
	
	var tBody = $("#"+tblName+" > TBODY")[0];
	
	//Add Row.
	row = tBody.insertRow(-1);
	
	//Add index cell
	var countRow = $('#'+tblName+' tbody tr:last').index() + 1;
	var cell = $(row.insertCell(-1));
	cell.html(countRow);
	cell.attr("style","width:5%;");

	cell = $(row.insertCell(-1));
	cell.html(data.location_name + '<input type="hidden" name="location_id[]" value="'+data.location_id+'">');

	cell = $(row.insertCell(-1));
	cell.html(data.batch_no + '<input type="hidden" name="batch_no[]" value="'+data.batch_no+'"><input type="hidden" name="trans_id[]" value="'+data.id+'" />');

	cell = $(row.insertCell(-1));
	cell.html(data.qty + '<input type="hidden" class="qtyTotal" name="dispatch_qty[]" value="'+data.qty+'">');

	cell = $(row.insertCell(-1));
	// var btnRemove = $('<button><i class="ti-trash"></i></button>');
	// btnRemove.attr("type", "button");
	// btnRemove.attr("onclick", "Remove(this,'"+data.qty+"');");
    // btnRemove.attr("style","margin-left:4px;");
	// btnRemove.attr("class", "btn btn-outline-danger waves-effect waves-light");
	// cell.append(btnRemove);
	cell.attr("class","text-center");
	cell.attr("style","width:10%;");
	
}
</script>