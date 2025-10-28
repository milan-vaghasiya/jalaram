function AddRow() {
	var valid = 1;
	$(".error").html("");
	if($("#dept_id").val() == ""){$(".dept_id").html("Department is required.");valid = 0;}
	if($("#ref_item_id").val() == ""){$(".ref_item_id").html("Item is required.");valid = 0;}
	if($("#tool_life").val() == "" || $("#tool_life").val() == 0){$(".tool_life").html("Tool Life is required.");valid = 0;}
	if(valid)
	{
		$(".dept_id").html("");
		$(".ref_item_id").html("");
		$(".tool_life").html("");
		//Get the reference of the Table's TBODY element.
		$("#toolConsumption").dataTable().fnDestroy();
		var tblName = "toolConsumption";
		
		var tBody = $("#"+tblName+" > TBODY")[0];
		
		//Add Row.
		row = tBody.insertRow(-1);
		
		//Add index cell
		var countRow = $('#'+tblName+' tbody tr:last').index() + 1;
		var cell = $(row.insertCell(-1));
		cell.html(countRow);

		cell = $(row.insertCell(-1));
		cell.html($("#dept_idc").val() + '<input type="hidden" name="dept_id[]" value="'+$("#dept_id").val()+'">');

		var ops = '';var i=0;
		$("#operationSelect option:selected").each(function() {if(i==0){ops = this.text;}else{ops = ops + ', ' +this.text;}i++;});
		cell = $(row.insertCell(-1));
		cell.html(ops + '<input type="hidden" name="operation_id[]" value="'+$("#operation_id").val()+'">');
		
		cell = $(row.insertCell(-1));
		cell.html($("#ref_item_idc").val() + '<input type="hidden" name="ref_item_id[]" value="'+$("#ref_item_id").val()+'"><input type="hidden" name="id[]" value="">');

		cell = $(row.insertCell(-1));
		cell.html($("#setupc").val() + '<input type="hidden" name="setup[]" value="'+$("#setup").val()+'">');

		cell = $(row.insertCell(-1));
		cell.html($("#tool_life").val() + '<input type="hidden" name="tool_life[]" value="'+$("#tool_life").val()+'">');
		
		cell = $(row.insertCell(-1));
		cell.html($("#number_corner").val() + '<input type="hidden" name="number_corner[]" value="'+$("#number_corner").val()+'">');

		cell = $(row.insertCell(-1));
		cell.html($("#price").val() + '<input type="hidden" name="price[]" value="'+$("#price").val()+'">');
		
		//Add Button cell.
		cell = $(row.insertCell(-1));
		var btnRemove = $('<button><i class="ti-trash"></i></button>');
		btnRemove.attr("type", "button");
		btnRemove.attr("onclick", "Remove(this);");
		btnRemove.attr("class", "btn btn-sm btn-outline-danger waves-effect waves-light");
		cell.append(btnRemove);
		cell.attr("class","text-center");
		
		$("#ref_item_idc").val("");
		$("#ref_item_id").val("");
		$("#dept_idc").val("");
		$("#dept_id").val("");
		$("#setupc").val("");
		$("#setup").val("");
		$("#tool_life").val("");
		$("#number_corner").val("");
		$("#price").val("");
		$("#operationSelect").val("");
		$("#operation_id").val("");
		reInitMultiSelect();
	}
};

function Remove(button) {
	//Determine the reference of the Row using the Button.
	$("#toolConsumption").dataTable().fnDestroy();
	var row = $(button).closest("TR");
	var table = $("#toolConsumption")[0];
	table.deleteRow(row[0].rowIndex);
	$('#toolConsumption tbody tr td:nth-child(1)').each(function(idx, ele) {
        ele.textContent = idx + 1;
    });
};

function saveToolConsumption(formId){
    var fd = $('#'+formId).serialize();
	$.ajax({
		url: base_url + controller + '/saveToolConsumption',
		data:fd,
		type: "POST",
		dataType:"json",
	}).done(function(data){
		if(data.status===0){
			$(".error").html("");
			$.each( data.message, function( key, value ) {
				$("."+key).html(value);
			});
		}else if(data.status==1){
			initTable(0); $('#'+formId)[0].reset();$(".modal").modal('hide'); 
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}else{
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}		
	});
}