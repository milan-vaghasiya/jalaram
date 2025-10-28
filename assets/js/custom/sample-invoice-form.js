$(document).ready(function(){
    $(document).on('change','#party_id',function(){
        var party_id = $(this).val();
        $('.party_id').html("");
        if(party_id){
            var partyData = $(this).find(":selected").data('row');	
            var gstin = partyData.gstin;
            var stateCode = "";
			if(gstin != ""){
				stateCode = gstin.substr(0, 2);
			}
            $("#party_name").val($("#party_idc").val());
			$("#party_state_code").val(stateCode);

            $("#tempItem").html('');
            $("#tempItem").html('<tr id="noData"><td colspan="8" align="center">No data available in table</td></tr>');

            $.ajax({
                url: base_url + controller + '/getPackingNoList',
                type:'post',
                data:{party_id:party_id},
                dataType:'json',
                success:function(data){
                    $("#ref_id").html();
                    $("#ref_id").html(data.packingList);
                    $("#ref_id").comboSelect();
                }
            });
        }else{
            $('.party_id').html("Party Name is required.");
        }

        claculateColumn();
    });

    $(document).on('change','#ref_id',function(){
        var packing_id = $(this).val();
        var party_id = $("#party_id").val();
        
        $('.party_id').html("");
        if(party_id){
            if(packing_id){
                $("#tempItem").html('');
                $("#tempItem").html('<tr id="noData"><td colspan="8" align="center">No data available in table</td></tr>');

                $.ajax({
                    url: base_url + controller + '/getPackingItemList',
                    type:'post',
                    data:{packing_id:packing_id},
                    dataType:'json',
                    success:function(data){

                        if(data.itemList.length > 0){
                            $.each(data.itemList,function(key , row){
								if(row.comm_pack_id == 0){
									row.row_index = "";
									row.ref_id = row.id;
									row.id = "";
                                    row.drg_rev_no = row.amount;
                                    row.rev_no = row.net_amount;

                                    row.price = row.item_price;
                                    row.amount = parseFloat(parseFloat(row.qty) * parseFloat(row.item_price)).toFixed(2);
                                    row.taxable_amount = row.amount;
                                    row.net_amount = row.amount;

									AddRow(row);
								}
                            });

							var currency = data.itemList.map(function(res) {
								return res.currency;
							});

							currency = Array.from(new Set(currency));
							$("#currency").val("");
							$("#currency").val(currency[0]);
                        }
                    }
                });
            }else{
                $('.ref_id').html("Packing No. is required.");
            }
        }else{
            $('.party_id').html("Party Name is required.");
        }
    });

    $(document).on('click','.saveItem',function(){        
        var fd = $('#sampleItemForm').serializeArray();
        var formData = {};
        $.each(fd,function(i, v) { formData[v.name] = v.value; });
        
        $("#sampleItemForm .error").html("");

        if(formData.hsn_code == ""){
            $(".hsn_code").html("HSN Code is required.");
        }
        if(formData.hsn_desc == ""){
            $(".hsn_desc").html("HSN Description is required.");
        }
        if(formData.price == "" || formData.price == "0"){
            $(".price").html("Price is required.");
		}

        var errorCount = $('#sampleItemForm .error:not(:empty)').length;

        if (errorCount == 0) {
            formData.amount = parseFloat(parseFloat(formData.qty) * parseFloat(formData.price)).toFixed(2);
			formData.taxable_amount = formData.amount;
			formData.net_amount = formData.amount;

            AddRow(formData);
            $('#sampleItemForm')[0].reset();
            if($(this).data('fn') == "save"){
                $("#row_index").val($('#invoiceItems tbody').find('tr').length);
            }else if($(this).data('fn') == "save_close"){
                $("#itemModel").modal('hide');
            }  
		}
    });

    $(document).on('keyup','#freight_amount',function(){ claculateColumn(); });
    $(document).on('keyup','#other_amount',function(){ claculateColumn(); });
});

function AddRow(data) {
	$('table#invoiceItems tr#noData').remove();
	//Get the reference of the Table's TBODY element.
	var tblName = "invoiceItems";
	
	var tBody = $("#"+tblName+" > TBODY")[0];
	
	//Add Row.
	if(data.row_index != ""){
		var trRow = data.row_index;
		//$("tr").eq(trRow).remove();
		$("#"+tblName+" tbody tr:eq("+trRow+")").remove();
	}
	
	var ind = (data.row_index == "")?-1:data.row_index;
	row = tBody.insertRow(ind);
	
	//Add index cell
	var countRow = (data.row_index == "")?($('#'+tblName+' tbody tr:last').index() + 1):(parseInt(data.row_index) + 1);
	var cell = $(row.insertCell(-1));
	cell.html(countRow);
	cell.attr("style","width:5%;");	
	
    var transIdInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][id]",value:data.id});
	var refIdInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][ref_id]",value:data.ref_id});
	var itemIdInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][item_id]",value:data.item_id});
	var itemNameInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][item_name]",value:data.item_name});
	var itemDescInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][item_desc]",value:data.item_desc});
	var itemAliasInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][item_alias]",value:data.item_alias});	
	var itemTypeInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][item_type]",value:data.item_type});
	var itemCodeInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][item_code]",value:data.item_code});
	var netWtInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][drg_rev_no]",value:data.drg_rev_no});//net_wt
	var grossWtInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][rev_no]",value:data.rev_no});//gross_wt
	cell = $(row.insertCell(-1));
	cell.html(data.item_name);
    cell.append(transIdInput);
	cell.append(refIdInput);
	cell.append(itemIdInput);
	cell.append(itemNameInput);
    cell.append(itemDescInput);
	cell.append(itemAliasInput);	
	cell.append(itemTypeInput);
	cell.append(itemCodeInput);
	cell.append(netWtInput);
	cell.append(grossWtInput);
	

	var hsnCodeInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][hsn_code]",value:data.hsn_code});
    cell = $(row.insertCell(-1));
	cell.html(data.hsn_code);
	cell.append(hsnCodeInput);

    var hsnDescInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][hsn_desc]",value:data.hsn_desc});
    cell = $(row.insertCell(-1));
	cell.html(data.hsn_desc);
	cell.append(hsnDescInput);
	
	var qtyInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][qty]",value:data.qty});
	cell = $(row.insertCell(-1));
	cell.html(data.qty);
	cell.append(qtyInput);
	
	var priceInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][price]",value:data.price});
	cell = $(row.insertCell(-1));
	cell.html(data.price);
	cell.append(priceInput);	
	
    var amountInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][amount]",class:"amt",value:data.amount});
	var taxableAmountInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][taxable_amount]",value:data.taxable_amount});
	var netAmtInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][net_amount]",class:"net_amt",value:data.net_amount});
	cell = $(row.insertCell(-1));
	cell.html(data.net_amount);
    cell.append(amountInput);
	cell.append(taxableAmountInput);
	cell.append(netAmtInput);
	
	//Add Button cell.
	cell = $(row.insertCell(-1));
	var btnRemove = $('<button><i class="ti-trash"></i></button>');
	btnRemove.attr("type", "button");
	btnRemove.attr("onclick", "Remove(this);");
    btnRemove.attr("style","margin-left:4px;");
	btnRemove.attr("class", "btn btn-outline-danger waves-effect waves-light");

    var btnEdit = $('<button><i class="ti-pencil-alt"></i></button>');
    btnEdit.attr("type", "button");
    btnEdit.attr("onclick", "Edit("+JSON.stringify(data)+",this);");
    btnEdit.attr("class", "btn btn-outline-warning waves-effect waves-light");

    cell.append(btnEdit);
	cell.append(btnRemove);
	cell.attr("class","text-center");
	cell.attr("style","width:10%;");

	claculateColumn();
};

function Edit(data,button){
	var row_index = $(button).closest("tr").index();
	$("#itemModel").modal();
    
	$.each(data,function(key, value) { $("#sampleItemForm #"+key).val(value); });

	$("#sampleItemForm #row_index").val(row_index);
    $(".btn-save").hide();	
}

function Remove(button) {
	//Determine the reference of the Row using the Button.
	var row = $(button).closest("TR");
	var table = $("#invoiceItems")[0];
	table.deleteRow(row[0].rowIndex);
	$('#invoiceItems tbody tr td:nth-child(1)').each(function(idx, ele) {
        ele.textContent = idx + 1;
    });
	var countTR = $('#invoiceItems tbody tr:last').index() + 1;
	if(countTR == 0){		
		$("#tempItem").html('<tr id="noData"><td colspan="8" align="center">No data available in table</td></tr>');
	}	
	claculateColumn();
};

function claculateColumn(){			
	var amountArray = $(".amt").map(function(){return $(this).val();}).get();
    var amountSum = 0;
	$.each(amountArray,function(){amountSum += parseFloat(this) || 0;});

    var netAmtArray = $(".net_amt").map(function(){return $(this).val();}).get();
    var netAmtSum = 0;
	$.each(netAmtArray,function(){netAmtSum += parseFloat(this) || 0;});
    
    var freight_amount = $("#freight_amount").val();
    netAmtSum += (parseFloat(freight_amount) > 0)?parseFloat(freight_amount):0;

    var other_amount = $("#other_amount").val();
    netAmtSum += (parseFloat(other_amount) > 0)?parseFloat(other_amount):0;

    $("#total_amount").val(parseFloat(amountSum).toFixed(3));
    $("#net_amount").val(parseFloat(netAmtSum).toFixed(3));
}

function saveSampleInvoice(formId){
	var fd = $('#'+formId)[0];
    var formData = new FormData(fd);
	$.ajax({
		url: base_url + controller + '/save',
		data:formData,
        processData: false,
        contentType: false,
		type: "POST",
		dataType:"json",
	}).done(function(data){
		if(data.status===0){
			$(".error").html("");
			$.each( data.message, function( key, value ) {
				$("."+key).html(value);
			});
		}else if(data.status==1){
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
            window.location = data.url;
		}else{
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}				
	});
}