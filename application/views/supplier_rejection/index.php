<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-4">
                                <h4 class="card-title pageHeader">Supplier Rejection</h4>
                            </div>  
                            <div class="col-md-2 form-group">
                                <select id="item_type" class="form-control single-select">
                                    <option value="">Select Item Type</option>
                                    <!-- <option value="1">Finish Good</option> -->
                                    <option value="2">Consumable</option>
                                    <option value="3">Raw Material</option>
                                    <!-- <option value="4">Capital Goods</option>
                                    <option value="5">Machineries</option>
                                    <option value="6">Instruments</option>
                                    <option value="7">Gauges</option> -->
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <div class="input-group">
                                    <select id="item_id" class="form-control single-select float-right" style="width: 80%;">
                                        <option value="">Select Item</option>
                                    </select>
                                    <div class="input-group-append ml-2">
                                        <button type="button" class="btn waves-effect waves-light btn-success float-right loadData" title="Load Data">
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
                                    <tr>
                                        <th class="text-center" id="itemNameText" colspan="11"></th>
                                    </tr>
                                    <tr>
                                        <th class="text-center">Action</th>
                                        <th class="text-center">#</th>
                                        <th>GRN NO.</th>
                                        <th>GRN Date</th>
                                        <th>Party Name</th>
                                        <th>Challan No.</th>
                                        <th>PO. No.</th>
                                        <th>Store</th>
                                        <th>Location</th>
                                        <th>Batch No.</th>
                                        <th class="text-center">Current Stock Qty.</th>
                                    </tr>
                                </thead>
								<tbody id="tbodyData">

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
<script>
$(document).ready(function(){
	reportTable();

    $(document).on('change','#item_type',function(){
        var item_type = $(this).val();
        $(".item_type").html("");
        if(item_type != ""){
            $.ajax({
                url : base_url + controller + '/getItemList',
                type : 'post',
                data : {item_type: item_type},
                dataType : 'json',
                success:function(data){
                    $("#item_id").html(data.itemOption);
                    $("#item_id").comboSelect();
                }
            });
        }else{
            $(".item_type").html("Item type is required.");
        }
    });

    $(document).on('click','.loadData',function(e){
		$(".error").html("");
		var valid = 1;
		var item_id = $('#item_id :selected').val();
		if(item_id == ""){$(".item_id").html("Item Name is required.");valid=0;}

        $("#itemNameText").html("");
		if(valid){
            $("#itemNameText").html($("#item_id :selected").text());

            $.ajax({
                url: base_url + controller + '/getItemStock',
                data: {item_id:item_id},
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

function saveSupplierRejection(formId,fnsave = "save"){
    setPlaceHolder();
	var form = $('#'+formId)[0];
	var fd = new FormData(form);
	$.ajax({
		url: base_url + controller + '/' + fnsave,
		data:fd,
		type: "POST",
		processData:false,
		contentType:false,
		dataType:"json",
	}).done(function(data){
		if(data.status===0){
			$(".error").html("");
			$.each( data.message, function( key, value ) {$("."+key).html(value);});
		}else if(data.status==1){
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });

            var stock_qty = $("#"+formId +" #stock_qty").val();
            var qty = $("#"+formId +" #qty").val();

            $("#"+formId +" #qty").val("");
            $("#"+formId +" #remark").val("");
            $("#batchTransData").html(data.stockTransData);

            var current_stock = parseFloat(parseFloat(stock_qty) - parseFloat(qty)).toFixed(3);
            $("#"+formId +" #stock_qty").val(current_stock);

            $('.loadData').trigger('click');
		}else{
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}				
	});
}

function removeTrans(id,qty,name='Record'){
    var send_data = { id:id,item_id:$("#supplierRejection #item_id").val(),batch_no:$("#supplierRejection  #batch_no").val() };
	$.confirm({
		title: 'Confirm!',
		content: 'Are you sure want to delete this '+name+'?',
		type: 'red',
		buttons: {   
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function(){
					$.ajax({
						url: base_url + controller + '/delete',
						data: send_data,
						type: "POST",
						dataType:"json",
						success:function(data){
							if(data.status==0){
								toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}else{
								toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });

                                $("#batchTransData").html(data.stockTransData);
                                $('.loadData').trigger('click');

                                var stock_qty = $("#supplierRejection #stock_qty").val();
                                var current_stock = parseFloat(parseFloat(stock_qty) + parseFloat(qty)).toFixed(3);
                                $("#supplierRejection #stock_qty").val(current_stock);
							}
						}
					});
				}
			},
			cancel: {
                btnClass: 'btn waves-effect waves-light btn-outline-secondary',
                action: function(){

				}
            }
		}
	});
}

function rejectStockEffect(data){
	$.confirm({
		title: 'Confirm!',
		content: 'Are you sure want to Reject this '+name+'?',
		type: 'red',
		buttons: {   
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function(){
					$.ajax({
						url: base_url + controller + '/save',
						data: data,
						type: "POST",
						dataType:"json",
						success:function(data){
							if(data.status==0){
								toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}else{
								toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
                                $('.loadData').trigger('click');
							}
						}
					});
				}
			},
			cancel: {
                btnClass: 'btn waves-effect waves-light btn-outline-secondary',
                action: function(){

				}
            }
		}
	});
}

function reportTable(){
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
		buttons: [ 'pageLength', 'excel', {text: 'Refresh',action: function () {$('.loadData').trigger('click');}}]
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