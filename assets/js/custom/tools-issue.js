$(document).ready(function(){

	$(document).on('change','#party_id',function(){
		$("#party_name").val($("#party_id :selected").data('party_name'));
	});

	$(document).on("change","#location_id",function(){
		var itemId = $("#item_id").val();
        var location_id = $(this).val();
		$(".location_id").html("");
		$(".item_id").html("");
		$("#batch_stock").val("");
		
		if(itemId == "" || location_id == ""){
			if(itemId == ""){
				$(".item_id").html("Issue Item name is required.");
			}
			if(location_id == ""){
				$(".location_id").html("Location is required.");
			}
		}else{
			$.ajax({
				url:base_url + controller + '/getBatchNo',
				type:'post',
				data:{item_id:itemId,location_id:location_id},
				dataType:'json',
				success:function(data){
					$("#batch_no").html("");
					$("#batch_no").html(data.options);
					$("#batch_no").comboSelect();
					$("#batch_no").trigger('change');
				}
			});
		}
	});

    $(document).on("change","#item_id",function(){
        var itemId = $(this).val();
		$(".item_id").html("");
		$("#batch_stock").val("");
		$("#item_name").val($("#item_id :selected").data('item_name'));
		if(itemId == ""){
			$(".item_id").html("Issue Item name is required.");
		}else{
			$.ajax({
				url:base_url + controller + '/getItemLocation',
				type:'post',
				data:{item_id:itemId},
				dataType:'json',
			}).done(function(response) {
                $("#tempItem").html("");
                $("#tempItem").html(response.batchWiseStock);

            });
		}
    });

	$(document).on('change',"#batch_no",function(){
		$("#batch_stock").val("");
		$("#batch_stock").val($("#batch_no :selected").data('stock'));
	});

	$(document).on('click','.addRow',function(){
		var issue_date = $("#issue_date").val();
		var collected_by = $("#collected_by").val();
		var collected_byc = $("#collected_byc").val();
		var job_card_id = $("#job_card_id").val();
		var job_card_idc = $("#job_card_idc").val();
		var dept_id = $("#dept_id").val();
		var dept_idc = $("#dept_idc").val();
		var machine_id = $("#machine_id").val();
		var item_id = $("#item_id").val();
		var item_name = $("#item_id :selected").text();//$("#item_idc").val();
		var location_id = $("#location_id").val();
		var store_name = $("#location_id :selected").data('store_name');
		var location = $("#location_id :selected").text();
		var location_name = "[ "+store_name+" ] "+location;
		var batch_no = $("#batch_no").val();
		var stock = $("#batch_stock").val();
		var qty = $("#qty").val();
		var is_returnable = $("#is_returnable").val();
		
		$(".location_id").html("");
		$(".batch_no").html("");
		$(".qty").html("");
		if(item_id == "" || location_id == "" || batch_no == "" || qty == "" || qty == "0" || qty == "0.000"){
			if(item_id == ""){
				$(".item_id").html("Item Name is required.");
			}
			if(location_id == ""){
				$(".location_id").html("Location is required.");
			}
			if(batch_no == ""){
				$(".batch_no").html("Batch No. is required.");
			}
			if(qty == "" || qty == "0" || qty == "0.000"){
				$(".qty").html("Qty. is required.");
			}
		}else{

			var qtySum = 0;
			$(".qtyTotal").each(function(){
				qtySum += parseFloat($(this).text());
			});
			qtySum += parseFloat(qty);

			if(parseFloat(qty) > parseFloat(stock)){
				$(".qty").html("Stock not avalible.");
			}else{				
				var post = {id:"",issue_date:issue_date,collected_by:collected_by,collected_byc:collected_byc,job_card_id:job_card_id,job_card_idc:job_card_idc,dept_id:dept_id,dept_idc:dept_idc,machine_id:machine_id,item_id:item_id,item_name:item_name,location_id:location_id,location_name:location_name,batch_no:batch_no,qty:qty,is_returnable:is_returnable};						
				addRow(post);
				$("#job_card_id").val(""); $("#job_card_id").comboSelect();			
				//$("#dept_id").val(""); $("#dept_id").comboSelect();
				$("#collected_by").val(); $("#collected_by").comboSelect();
				$("#party_id").val(); $("#party_id").comboSelect();
				$("#machine_id").val(""); $("#machine_id").comboSelect();
				$("#batch_no").val(""); $("#batch_no").comboSelect();				
				$("#batch_stock").val("");
				$("#qty").val("");

				$('#location_id').select2('destroy');
				$("#location_id").html('<option value=""  data-store_name="">Select Location</option>');
				$('#location_id').select2({
					dropdownParent: $('#location_id').parent()
				});

				$('#item_id').select2('destroy');
				$("#item_id").val('');
				$('#item_id').select2({
					dropdownParent: $('#item_id').parent()
				});
			}
		}
	});

	receiveTable();	
    $(document).on('click','.returnItem',function(){
        var dataRow = $(this).data('row'); 

        $.ajax({
            url: base_url + controller + "/getReceiveItemTrans",
            type:'POST',
            data:{
				item_id:dataRow.item_id, 
				ref_id:dataRow.ref_id,
				trans_type:1,
				ref_no:dataRow.ref_no,
				location_id:dataRow.location_id,
				batch_no:dataRow.batch_no,
				item_name:dataRow.item_name
			}
		}).done(function(response){ 
			$("#modal-lg").modal();
			$("#modal-lg .modal-title").html('Return Item');
			$("#modal-lg .modal-body").html(response);
			$("#modal-lg .modal-footer .btn-close .btn-reciveclose").show();
			$("#modal-lg .modal-footer .btn-save").hide();
			$("#modal-lg .modal-footer .btn-save-close").hide();
			
			$(".single-select").comboSelect();
			$("#modal-lg .scrollable").perfectScrollbar({suppressScrollX: true});
			initMultiSelect();setPlaceHolder();
		});
	});
	
    $(document).on('click','.btn-close',function(){
		initMultiSelect();
	});

    $(document).on('keyup change', ".batchQty", function() {
        var batchQtyArr = $("input[name='batch_quantity[]']").map(function() {
            return $(this).val();
        }).get();
        var batchQtySum = 0;
        $.each(batchQtyArr, function() {
            batchQtySum += parseFloat(this) || 0;
        });
        $('#totalQty').html("");
        $('#totalQty').html(batchQtySum.toFixed(3));
        $("#booked_qty").val(batchQtySum.toFixed(3));

        var id = $(this).data('rowid');
        var cl_stock = $(this).data('cl_stock');
        var batchQty = $(this).val();
        $(".batch_qty" + id).html("");
        $(".qty").html();
        if (parseFloat(batchQty) > parseFloat(cl_stock)) {
            $(".batch_qty" + id).html("Stock not avalible.");
            $(this).val(0);
            $('#totalQty').html(batchQtySum - batchQty);
            $("#booked_qty").val(batchQtySum - batchQty);
        }
    });
});

function addRow(data){ 
	$('table#issueItems tr#noData').remove();
	//Get the reference of the Table's TBODY element.
	var tblName = "issueItems";
	
	var tBody = $("#"+tblName+" > TBODY")[0];
	
	//Add Row.
	row = tBody.insertRow(-1);
	
	//Add index cell
	var countRow = $('#'+tblName+' tbody tr:last').index() + 1;
	var cell = $(row.insertCell(-1));
	cell.html(countRow);
	cell.attr("style","width:5%;");

	var itemDataInput = $("<input />",{type:"hidden",name:"item_data[]",value:JSON.stringify(data)});
	cell = $(row.insertCell(-1));
	cell.html(data.item_name);
	cell.append(itemDataInput);
	
	cell = $(row.insertCell(-1));
	cell.html(data.job_card_idc);

	cell = $(row.insertCell(-1));
	cell.html(data.dept_idc);

	cell = $(row.insertCell(-1));
	cell.html(data.collected_byc);

	cell = $(row.insertCell(-1));
	cell.html(data.location_name);

	cell = $(row.insertCell(-1));
	cell.html(data.qty);
	cell.attr('class','qtyTotal');

	cell = $(row.insertCell(-1));
	var btnRemove = $('<button><i class="ti-trash"></i></button>');
	btnRemove.attr("type", "button");
	btnRemove.attr("onclick", "Remove(this);");
    btnRemove.attr("style","margin-left:4px;");
	btnRemove.attr("class", "btn btn-outline-danger waves-effect waves-light");
	cell.append(btnRemove);
	cell.attr("class","text-center");
	cell.attr("style","width:10%;");
	
}

function Remove(button) {
    //Determine the reference of the Row using the Button.
	var row = $(button).closest("TR");
	var table = $("#issueItems")[0];
	table.deleteRow(row[0].rowIndex);
	$('#issueItems tbody tr td:nth-child(1)').each(function(idx, ele) {
        ele.textContent = idx + 1;
    });
	var countTR = $('#issueItems tbody tr:last').index() + 1;
	if(countTR == 0){
		$("#tempItem").html('<tr id="noData"><td colspan="5" align="center">No data available in table</td></tr>');
	}	
};

function dispatch(data){
	var button = "";
	$.ajax({ 
		type: "POST",   
		url: base_url + controller + '/dispatch',   
		data: {id:data.id}
	}).done(function(response){
		$("#"+data.modal_id).modal();
		$("#"+data.modal_id+' .modal-title').html(data.title);
		$("#"+data.modal_id+' .modal-body').html(response);
		$("#"+data.modal_id+" .modal-body form").attr('id',data.form_id);
		$("#"+data.modal_id+" .modal-footer .btn-save").attr('onclick',"store('"+data.form_id+"');");
		if(button == "close"){
			$("#"+data.modal_id+" .modal-footer .btn-close").show();
			$("#"+data.modal_id+" .modal-footer .btn-save").hide();
		}else if(button == "save"){
			$("#"+data.modal_id+" .modal-footer .btn-close").hide();
			$("#"+data.modal_id+" .modal-footer .btn-save").show();
		}else{
			$("#"+data.modal_id+" .modal-footer .btn-close").show();
			$("#"+data.modal_id+" .modal-footer .btn-save").show();
		}
		$(".single-select").comboSelect();		
		initMultiSelect();setPlaceHolder();
		$('.model-select2').select2({ dropdownParent: $('.model-select2').parent() });
	});
}

function request(id){
    $.confirm({
		title: 'Confirm!',
		content: 'Are you sure want to send purchase request?',
		type: 'green',
		buttons: {   
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function(){
	                $.ajax({
	                	url: base_url + 'jobMaterialDispatch/purchaseRequest',
	                	data:{
                            'id':'',
                            'dispatch_id':id
                        },
	                	type: "POST",
	                	dataType:"json",
	                }).done(function(data){
	                	if(data.status===0){
	                		$(".error").html("");
	                		$.each( data.message, function( key, value ) {
	                			$("."+key).html(value);
	                		});
	                	}else if(data.status==1){
	                		initTable(); $(".modal").modal('hide');
	                		toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
	                	}else{
	                		initTable(); $(".modal").modal('hide');
	                        toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
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

function consumption(data){
	var button = "";
	$.ajax({ 
		type: "POST",   
		url: base_url + controller + '/consumption',   
		data: {product_id:data.id,job_card_id:data.job_card_id}
	}).done(function(response){
		$("#"+data.modal_id).modal();
		$("#"+data.modal_id+' .modal-title').html(data.title);
		$("#"+data.modal_id+' .modal-body').html(response);
		$("#"+data.modal_id+" .modal-body form").attr('id',data.form_id);
				
	});
}

function saveReceiveItem(frm){
    var fd = new FormData(frm);
    var qty = $("#qty").val();
    $.ajax({
		url: base_url + controller + '/saveReceiveItem',
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
			$("#qty").val("");
            $("#receiveItemTable").dataTable().fnDestroy();
            $("#receiveItemTableData").html("");				
            $("#receiveItemTableData").html(data.resultHtml);
            receiveTable(); 
            var pending_qty = 0;
            pending_qty = parseFloat(parseFloat($("#ProductPendingQty").html()) - parseFloat(qty)).toFixed(3);
            $("#ProductPendingQty").html(pending_qty);
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}else{			
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}				
	});
}

function trashReceiveItem(id,qty,name='Record'){
    var send_data = { id:id };
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
						url: base_url + controller + '/deleteReceiveItem',
						data: send_data,
						type: "POST",
						dataType:"json",
						success:function(data)
						{
							if(data.status==0)
							{
								toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}
							else
							{
								toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });

								$("#receiveItemTable").dataTable().fnDestroy();
                                $("#receiveItemTableData").html("");				
                                $("#receiveItemTableData").html(data.resultHtml);
                                receiveTable();

                                var pending_qty = 0;
                                pending_qty = parseFloat(parseFloat($("#ProductPendingQty").html()) + parseFloat(qty)).toFixed(3);
                                $("#ProductPendingQty").html(pending_qty);
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

function receiveTable(){
	var receiveTable = $('#receiveItemTable').DataTable( {
		lengthChange: false,
		responsive: true,
		'stateSave':true,
		retrieve: true,
		buttons: [ 'pageLength','copy', 'excel']
	});
	receiveTable.buttons().container().appendTo( '#receiveItemTable_wrapper .col-md-6:eq(0)' );
	return receiveTable;
};

function returnMaterial(data){
    var button = data.button;if(button == "" || button == null){button="both";};
	var fnEdit = data.fnedit;if(fnEdit == "" || fnEdit == null){fnEdit="edit";}
	var fnsave = data.fnsave;if(fnsave == "" || fnsave == null){fnsave="save";}
	var savebtn_text = data.savebtn_text;if(savebtn_text == "" || savebtn_text == null){savebtn_text="Save";}
	var sendData = {id:data.id,batch_no:data.batch_no,pending_qty:data.pending_qty,size:data.size};
	if(data.approve_type){sendData = {id:data.id,approve_type:data.approve_type};}
	$.ajax({ 
		type: "POST",   
		url: base_url + controller + '/' + fnEdit,   
		data: sendData,
	}).done(function(response){
		$("#"+data.modal_id).modal();
		$("#"+data.modal_id+' .modal-body').html('');
		$("#"+data.modal_id+' .modal-title').html(data.title);
		$("#"+data.modal_id+' .modal-body').html(response);
		$("#"+data.modal_id+" .modal-body form").attr('id',data.form_id);
		//$("#"+data.modal_id+" .modal-footer .btn-save").html(savebtn_text);
		$("#"+data.modal_id+" .modal-footer .btn-save").attr('onclick',"store('"+data.form_id+"','"+fnsave+"');");
		$("#"+data.modal_id+" .modal-footer .btn-save-close").attr('onclick',"store('"+data.form_id+"','"+fnsave+"','save_close');");
		$("#"+data.modal_id+" .modal-footer .btn-close").attr('data-modal_id',data.form_id);
		if(button == "close"){
			$("#"+data.modal_id+" .modal-footer .btn-close").show();
			$("#"+data.modal_id+" .modal-footer .btn-save").hide();
			$("#"+data.modalId+" .modal-footer .btn-save-close").hide();
		}else if(button == "save"){
			$("#"+data.modal_id+" .modal-footer .btn-close").hide();
			$("#"+data.modal_id+" .modal-footer .btn-save").show();
            $("#"+data.modalId+" .modal-footer .btn-save-close").show();
		}else{
			$("#"+data.modal_id+" .modal-footer .btn-close").show();
			$("#"+data.modal_id+" .modal-footer .btn-save").show();
            $("#"+data.modalId+" .modal-footer .btn-save-close").show();
		}
		initModalSelect();
		$(".single-select").comboSelect();
		$('.model-select2').select2({ dropdownParent: $('.model-select2').parent() });
		$("#"+data.modal_id+" .scrollable").perfectScrollbar({suppressScrollX: true});
		initMultiSelect();setPlaceHolder();
	});
}