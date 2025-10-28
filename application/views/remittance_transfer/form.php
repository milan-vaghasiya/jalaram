<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="swift_id" id="swift_id" value="<?=(!empty($swiftData->id))?$swiftData->id:""?>">

            <div class="col-md-2 form-group">
                <label for="firc_number">FIRC Number</label>
                <input type="text" id="firc_number" class="form-control" value="<?=(!empty($swiftData->firc_number))?$swiftData->firc_number:""?>" readonly>
            </div>

            <div class="col-md-2 form-group">
                <label for="remittance_date">Remittance Date</label>
                <input type="date" id="remittance_date" class="form-control" value="<?=(!empty($swiftData->remittance_date))?$swiftData->remittance_date:date("Y-m-d")?>" readonly>
            </div>

            <div class="col-md-4 form-group">
                <label for="remitter_name">Remitter Name</label>
                <input type="text" id="remitter_name" class="form-control" value="<?=(!empty($swiftData->remitter_name))?$swiftData->remitter_name:""?>" readonly>
            </div>

            <div class="col-md-2 form-group">
                <label for="remitter_country">Remit. Country</label>
                <input type="text" id="remitter_country" class="form-control" value="<?=(!empty($swiftData->remitter_country))?$swiftData->remitter_country:""?>" readonly>
            </div>

            <div class="col-md-2 form-group">
                <label for="swift_currency">SWIFT Currency</label>
                <input type="text" id="swift_currency" class="form-control" value="<?=(!empty($swiftData->swift_currency))?$swiftData->swift_currency:""?>" readonly>
            </div>

            <div class="col-md-2 form-group">
                <label for="swift_amount">Swift Amount</label>
                <input type="text" id="swift_amount" class="form-control" value="<?=(!empty($swiftData->swift_amount))?$swiftData->swift_amount:""?>" readonly>
            </div>

            <div class="col-md-2 form-group">
                <label for="firc_amount">FIRC Amount</label>
                <input type="text" id="firc_amount" class="form-control" value="<?=(!empty($swiftData->firc_amount))?$swiftData->firc_amount:""?>" readonly>
            </div>

            <div class="col-md-8 form-group">
                <label for="swift_remark">Swift Remark</label>
                <input type="text" id="swift_remark" class="form-control" value="<?=(!empty($swiftData->swift_remark))?$swiftData->swift_remark:""?>" readonly>
            </div>
        </div>

        <hr>
        
        <div class="row" id="itemForm">       
            <div class="col-md-12 form-group">
                <div class="error itemFormError"></div>
            </div>     

            <input type="hidden" id="id" class="transferForm" value="">
            <input type="hidden" id="row_index" class="transferForm" value="">

            <div class="col-md-3 form-group">
                <label for="trans_ref_no">Trans. Ref. No.</label>
                <input type="text" id="trans_ref_no" class="transferForm form-control req" value="">
            </div>
            <div class="col-md-3 form-group">
                <label for="trans_date">Trans. Date</label>
                <input type="date" id="trans_date" class="transferForm form-control req"  min="<?=(!empty($swiftData->remittance_date))?$swiftData->remittance_date:date("Y-m-d")?>" max="<?=date("Y-m-d")?>" data-defualt_value="<?=date("Y-m-d")?>" value="<?=date("Y-m-d")?>">
            </div>
            <div class="col-md-3 form-group">
                <label for="firc_transfer">FIRC Transfer</label>
                <input type="text" id="firc_transfer" class="transferForm form-control req floatOnly" value="">
            </div>
            <div class="col-md-3 form-group">
                <label for="net_credit_inr">Net Credit INR</label>
                <input type="text" id="net_credit_inr" class="transferForm form-control req floatOnly" value="">
            </div>
            <div class="col-md-12 form-group">
                <label for="transfer_remark">Trans. Remark</label>
                <div class="input-group">
                    <input type="text" id="transfer_remark" class="transferForm form-control" value="">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-outline-success saveItem"><i class="fa fa-plus"></i> Add</button>
                    </div>
                </div>
            </div>
        </div>

        <hr>

        <div class="row">
            <div class="col-md-12">
                <div class="error transfer"></div>
                <div class="table table-responsive">
                    <table id="transferList" class="table table-bordered">
                        <thead class="thead-info">
                            <tr>
                                <th class="text-center">#</th>
                                <th>Trans. Ref. No.</th>
                                <th>Trans. Date</th>
                                <th>FIRC Transfer</th>
                                <th>Net Credit INR</th>
                                <th>Trans. Remark</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="transferData">
                            <tr id="noData">
                                <td class="text-center" colspan="7">No data available in table</td>
                            </tr>
                        </tbody>
                        <tfoot class="thead-info">
                            <tr>
                                <th colspan="3" class="text-right">Total</th>
                                <th id="totalFircTransfer">0</th>
                                <th id="totalCreditedInr">0</th>
                                <th colspan="2"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
$(document).ready(function(){
    $(document).on('click','.saveItem',function(e){
        e.preventDefault();
        e.stopImmediatePropagation();

        var formData = {};
        $.each($(".transferForm"),function(i, v) {
            formData[$(this).attr("id")] = $(this).val();
        });

        $("#itemForm .error").html("");

        if(formData.trans_ref_no == ""){
            $(".trans_ref_no").html("Trans. Ref. No. is required.");
        }
        if(formData.trans_date == ""){
            $(".trans_date").html("Trans. Date is required.");
        }
        if(formData.firc_transfer == "" || parseFloat(formData.firc_transfer) == 0){
            $(".firc_transfer").html("FIRC Transfer is required.");
        }
        if(formData.net_credit_inr == "" || parseFloat(formData.net_credit_inr) == 0){
            $(".net_credit_inr").html("Net Credited INR is required.");
        }

        //Check FIRC Transfer -> if FIRC Transfer SUM greater than FIRC Amount then return error
        var fircTransferArray = $(".fircTransfer").map(function () { return $(this).val(); }).get();
        var fircTransferSum = 0;
        $.each(fircTransferArray, function () { fircTransferSum += parseFloat(this) || 0; });
        if(formData.row_index != ""){ fircTransferSum = parseFloat(parseFloat(fircTransferSum) - parseFloat($(".fircTransAmt"+formData.row_index).val())); }
        fircTransferSum = parseFloat(parseFloat(fircTransferSum) + parseFloat(formData.firc_transfer));

        var fircAmount = $("#firc_amount").val();
        if(parseFloat(fircTransferSum) > parseFloat(fircAmount)){
            $(".itemFormError").html("FIRC Transfer greater then FIRC Amount. You can not add more remittance transfer.");
        }

        var errorCount = $('#itemForm .error:not(:empty)').length;
        if (errorCount == 0) {
            formData.swift_id = $("#swift_id").val();
            formData.entry_type = 1;
            AddRow(formData);

            $("#itemForm input:hidden").val('');
            $('#itemForm #row_index').val("");
            $('#itemForm input').val("");
            $('#itemForm #trans_date').val($('#itemForm #trans_date').data('defualt_value'));
            $("#itemForm #trans_ref_no").focus();
        }
    });
});

var itemCount = 0;
function AddRow(data){
    var tblName = "transferList";

    //Remove blank line.
	$('table#'+tblName+' tr#noData').remove();

	//Get the reference of the Table's TBODY element.
	var tBody = $("#" + tblName + " > TBODY")[0];

	//Add Row.
	if (data.row_index != "") {
		var trRow = data.row_index;
		//$("tr").eq(trRow).remove();
		$("#" + tblName + " tbody tr:eq(" + trRow + ")").remove();
	}
	var ind = (data.row_index == "") ? -1 : data.row_index;
	row = tBody.insertRow(ind);
	$(row).attr('id',itemCount);

    //Add index cell
	var countRow = (data.row_index == "") ? ($('#' + tblName + ' tbody tr:last').index() + 1) : (parseInt(data.row_index) + 1);
	var cell = $(row.insertCell(-1));
	cell.html(countRow);
    cell.attr("class", "text-center");
	cell.attr("style", "width:5%;");

    var idInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][id]", value: data.id });
    var entryTypeInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][entry_type]", value: data.entry_type });
    var swiftIdInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][swift_id]", value: data.swift_id });
    var transRefNoInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][trans_ref_no]", value: data.trans_ref_no });
    cell = $(row.insertCell(-1));
    cell.html(data.trans_ref_no);
    cell.append(idInput, entryTypeInput, swiftIdInput, transRefNoInput);

    var transDateInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][trans_date]", value: data.trans_date });
	cell = $(row.insertCell(-1));
	cell.html(formatDate(data.trans_date,'d-m-Y'));
	cell.append(transDateInput);

    var fircInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][firc_transfer]", class:"fircTransfer fircTransAmt"+itemCount , value: data.firc_transfer });
	cell = $(row.insertCell(-1));
	cell.html(data.firc_transfer);
	cell.append(fircInput);

    var creditedINRInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][net_credit_inr]", class:"netCreditInr", value: data.net_credit_inr });
	cell = $(row.insertCell(-1));
	cell.html(data.net_credit_inr);
	cell.append(creditedINRInput);

    var remarkInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][transfer_remark]", value: data.transfer_remark });
	cell = $(row.insertCell(-1));
	cell.html(data.transfer_remark);
	cell.append(remarkInput);

    //Add Button cell.
	cell = $(row.insertCell(-1));
	var btnRemove = $('<button><i class="ti-trash"></i></button>');
	btnRemove.attr("type", "button");
	btnRemove.attr("onclick", "Remove(this);");
	btnRemove.attr("style", "margin-left:4px;");
	btnRemove.attr("class", "btn btn-outline-danger waves-effect waves-light");

	var btnEdit = $('<button><i class="ti-pencil-alt"></i></button>');
	btnEdit.attr("type", "button");
	btnEdit.attr("onclick", "Edit(" + JSON.stringify(data) + ",this);");
	btnEdit.attr("class", "btn btn-outline-warning waves-effect waves-light");

	cell.append(btnEdit);
	cell.append(btnRemove);
	cell.attr("class", "text-center");
	cell.attr("style", "width:10%;");

    itemCount++;
    calculateTotal();
}

function Edit(data, button) {
    $("#itemForm .error").html("");
	var row_index = $(button).closest("tr").index();
	$.each(data, function (key, value) {
		$("#itemForm #" + key).val(value);
	});

	$("#itemForm #row_index").val(row_index);
	$("#itemForm #trans_ref_no").focus();
}

function Remove(button) {
    var tableId = "transferList";
	//Determine the reference of the Row using the Button.
	var row = $(button).closest("TR");
	var table = $("#"+tableId)[0];
	table.deleteRow(row[0].rowIndex);
	$('#'+tableId+' tbody tr td:nth-child(1)').each(function (idx, ele) {
		ele.textContent = idx + 1;
	});
	var countTR = $('#'+tableId+' tbody tr:last').index() + 1;
	if (countTR == 0) {
		$("#transferData").html('<tr id="noData"><td colspan="7" class="text-center">No data available in table</td></tr>');
	}

    calculateTotal();
}

function calculateTotal(){
    var fircTransferArray = $(".fircTransfer").map(function () { return $(this).val(); }).get();
    var fircTransferSum = 0;
    $.each(fircTransferArray, function () { fircTransferSum += parseFloat(this) || 0; });
    $("#totalFircTransfer").html(fircTransferSum.toFixed(3));

    var netCreditInrArray = $(".netCreditInr").map(function () { return $(this).val(); }).get();
    var netCreditInrSum = 0;
    $.each(netCreditInrArray, function () { netCreditInrSum += parseFloat(this) || 0; });
    $("#totalCreditedInr").html(netCreditInrSum.toFixed(3));
}
</script>

<?php
if(!empty($dataRow)):
    foreach($dataRow as $row):
        $row->row_index = "";
        $row = json_encode($row);
        echo '<script>AddRow('.$row.');</script>';
    endforeach;
endif;
?>