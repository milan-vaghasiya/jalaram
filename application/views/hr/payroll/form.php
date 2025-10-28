<?php $this->load->view('includes/header'); ?>
<style>
	.countSalary{width:100px;}
</style>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h4><u>Payroll Entry</u></h4>
                    </div>
                    <div class="card-body">
                        <form autocomplete="off" id="savePayRoll">
                            <div class="row">
                                <div class="col-md-2 form-group">
                                    <label for="dept_id">Department</label>
                                    <select name="dept_id" id="dept_id" class="form-control single-select req">
                                        <option value="">Select Department</option>
                                        <?php
                                            foreach($deptRows as $row):
                                                echo '<option value="'.$row->id.'">'.$row->name.'</option>';
                                            endforeach;
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-2 form-group">
                                    <label for="emp_type">Employee Type</label>
                                    <select name="emp_type" id="emp_type" class="form-control single-select req " >
                                        <option value="">Select Type</option>
                                        <!-- <option value="1">Permanent (Fix)</option> -->
                                        <option value="2">Permanent (Hourly)</option>
                                        <option value="3">Temporary</option>
                                    </select>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label for="month">Month</label>
                                    <div class="input-group">
                                        <select name="month" id="month" class="form-control single-select req" style="width:75%;">
                                            <option value="">Select Month</option>
                                            <?php
                                                foreach($monthList as $row):
                                                    $selected = (!empty($month) && $row == $month)?"selected":"";
                                                    echo '<option value="'.$row.'" '.$selected.'>'.date("F-Y",strtotime($row)).'</option>';
                                                endforeach;
                                            ?>
                                        </select>
                                        <div class="input-group-append">
                                            <button type="button" class="btn waves-effect waves-light btn-success float-right loadEmpSalaryData" title="Load Data">
    									        <i class="fas fa-sync-alt"></i> Load
    								        </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 form-group">
                                    <label for="ledger_id">Select Ledger</label>
                                    <select name="ledger_id" id="ledger_id" class="form-control single-select req" tabindex="-1">
                                        <option value="1">CASH IN HAND</option>
                                    </select>
                                </div>
                                <div class="col-md-2 form-group">
                                    <label>&nbsp;</label>
                                    <button type="button" class="btn waves-effect waves-light btn-outline-success btn-block save-form" onclick="savePayRoll('savePayRoll');" ><i class="fa fa-check"></i> Save</button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 form-group">
                                    <div class="row form-group">
                                        <div class="table-responsive ">
                                            <table id="empSalary" class="table table-striped table-borderless">
                                                <thead class="thead-info">
                                                    <tr>
                                                        <th style="width:30px;">#</th>
                                                        <th>Employee Name</th>
                                                        <th style="width:100px;">TWH</th>
                                                        <th style="width:100px;">Present <br> Days</th>
                                                        <th style="width:100px;">Absent <br> Days</th>
                                                        <th style="width:100px;">Total <br> Earning</th>
                                                        <th style="width:100px;">Total <br> Deduction</th>
                                                        <th style="width:100px;">Net <br> Salary</th>
                                                        <th style="width:100px;">Remark</th>
                                                        <th style="width:60px;" class="text-center">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="empSalaryData">
                                                    <tr>
                                                        <td id="noData" class="text-center" colspan="10">No data available in table</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="itemModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content animated slideDown">
			<div class="modal-header">
				<h4 class="modal-title">Add or Update Item</h4>
			</div>
			<div class="modal-body">
				<form id="invoiceItemForm">
					<div class="col-md-12">
                        <div id="itemInputs">
                            <input type="hidden" name="trans_id" id="trans_id" value="" />
                            <input type="hidden" name="row_index" id="row_index" value="">
                            <input type="hidden" name="emp_id" id="emp_id" value="">
                            <input type="hidden" name="emp_name" id="emp_name" value="">
                            <input type="hidden" name="total_wh" id="total_wh" value="">
                            <input type="hidden" name="present_days" id="present_days" value="">
                            <input type="hidden" name="working_days" id="working_days" value="">
                            <input type="hidden" name="absent_days" id="absent_days" value="">
                            <input type="hidden" name="basic_salary" id="basic_salary" value="">
                        </div>
						
						<div class="row form-group">
                            <div class="col-md-12 form-group">
                                <div class="table-responsive" id="salaryData">
                                </div>
							</div>
						</div>

                        <div class="row form-group">							
							<div class="col-md-12 form-group">
								<label for="remark">Remark</label>
								<input type="text" name="remark" id="remark" class="form-control" value="" />
							</div>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn waves-effect waves-light btn-outline-success saveItem btn-save" data-fn="save"><i class="fa fa-check"></i> Save</button>
				<button type="button" class="btn waves-effect waves-light btn-outline-warning saveItem btn-save-close" data-fn="save_close"><i class="fa fa-check"></i> Save & Close</button>
				<button type="button" class="btn waves-effect waves-light btn-outline-secondary btn-close" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
			</div>
		</div>
	</div>
</div>
<?php $this->load->view('includes/footer'); ?>

<script>
$(document).ready(function(){
    $(document).on('click','.loadEmpSalaryData',function(){
        var dept_id = $("#dept_id :selected").val();
        var emp_type = $("#emp_type :selected").val();
        var month = $("#month :selected").val();
        var valid = 1;

        if(dept_id == ""){ $(".dept_id").html("Department is required."); valid = 0; }
        if(emp_type == ""){ $(".emp_type").html("Employee Type is required."); valid = 0; }
        if(month == ""){ $(".month").html("Month is required."); valid = 0; }

        if(valid == 1){
            $.ajax({
                url:base_url + controller + '/getEmpSalaryData',
                type: 'post',
                data : {dept_id:dept_id, emp_type:emp_type, month:month},
                dataType:'json',
                success:function(data){
                    $("#empSalaryData").html("");
                    $("#empSalaryData").html(data.emp_salary_html);
                }
            });
        }
    });

    $(document).on('click','.saveItem',function(){
        var fd = $('#invoiceItemForm')[0];
        var formData = new FormData(fd);
        $.ajax({
            url: base_url + controller + '/getEmpSalaryJson',
            data:formData,
            processData: false,
            contentType: false,
            type: "POST",
            dataType:"json",
        }).done(function(data){
            AddRow(data.jsonData);
            $("#itemModel").modal('hide');
        });
    })

    $(document).on('keyup change','.calculateSalary',function(){
        var earningAmountArray = $(".earnings").map(function(){return $(this).val();}).get();
        var earningAmount = 0;
        $.each(earningAmountArray,function(){earningAmount += parseFloat(this) || 0;});
        $("#salaryData #total_earning").val(earningAmount.toFixed(2));	

        var deductionAmountArray = $(".deductions").map(function(){return $(this).val();}).get();
        var deductionAmount = 0;
        $.each(deductionAmountArray,function(){deductionAmount += parseFloat(this) || 0;});
        $("#salaryData #total_deduction").val(deductionAmount.toFixed(2));

        var netSalary = 0;
        netSalary = parseFloat(parseFloat(earningAmount) - parseFloat(deductionAmount)).toFixed(2);
        $("#salaryData #net_salary").val(netSalary);
    });

	/* $(document).on('keyup change','.countSalary',function(){
		var emp_id = $(this).data('emp_id');
		var basic_salary = parseFloat($('#basic_salary'+emp_id).val());
		var hra = parseFloat($('#hra'+emp_id).val());
		var ta = parseFloat($('#ta'+emp_id).val());
		var da = parseFloat($('#da'+emp_id).val());
		var oa = parseFloat($('#oa'+emp_id).val());
		var bonus_amount = parseFloat($('#bonus_amount'+emp_id).val());
		var pf_amount = parseFloat($('#pf_amount'+emp_id).val());
		var prof_tax = parseFloat($('#prof_tax'+emp_id).val());
		var other_deduction = parseFloat($('#other_deduction'+emp_id).val());
		var absent_days = parseFloat($('#absent_days'+emp_id).val());
		var leave_loss = parseFloat((basic_salary / 30) * absent_days).toFixed(0);
		var net_salary = basic_salary + hra + ta + da + oa + bonus_amount;
		net_salary = net_salary - pf_amount - prof_tax - other_deduction - leave_loss;
		$('#leave_loss'+emp_id).val(leave_loss);
		$('#net_salary'+emp_id).val(net_salary);
	}); */
});

function AddRow(data){
    $('table#empSalaryData tr#noData').remove();

    var tblName = "empSalary";
	
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

    var empIdInput = $("<input/>",{type:"hidden",name:"salary_data["+(countRow + 1)+"][emp_id]",value:data.emp_id});
    var empNameInput = $("<input/>",{type:"hidden",name:"salary_data["+(countRow + 1)+"][emp_name]",value:data.emp_name});
    cell = $(row.insertCell(-1));
	cell.html(data.emp_name);
    cell.append(empIdInput);
	cell.append(empNameInput);

    var totalWhInput = $("<input/>",{type:"hidden",name:"salary_data["+(countRow + 1)+"][total_wh]",value:parseFloat(data.total_wh).toFixed(2)});
    cell = $(row.insertCell(-1));
	cell.html(parseFloat(data.total_wh).toFixed(2));
    cell.append(totalWhInput);

    var presentDaysInput = $("<input/>",{type:"hidden",name:"salary_data["+(countRow + 1)+"][present_days]",value:data.present_days});
    var workingDaysInput = $("<input/>",{type:"hidden",name:"salary_data["+(countRow + 1)+"][working_days]",value:data.working_days});
    cell = $(row.insertCell(-1));
	cell.html(data.present_days);
    cell.append(presentDaysInput);
    cell.append(workingDaysInput);

    var absentDaysInput = $("<input/>",{type:"hidden",name:"salary_data["+(countRow + 1)+"][absent_days]",value:data.absent_days});
    cell = $(row.insertCell(-1));
	cell.html(data.absent_days);
    cell.append(absentDaysInput);

    var totalEarningInput = $("<input/>",{type:"hidden",name:"salary_data["+(countRow + 1)+"][total_earning]",value:data.total_earning});
    var basicSalaryInput = $("<input/>",{type:"hidden",name:"salary_data["+(countRow + 1)+"][basic_salary]",value:data.basic_salary});
    var earningDataInput = $("<input/>",{type:"hidden",name:"salary_data["+(countRow + 1)+"][earning_data]",value:data.earning_data});
    cell = $(row.insertCell(-1));
	cell.html(data.total_earning);
    cell.append(totalEarningInput);
    cell.append(basicSalaryInput);
    cell.append(earningDataInput);

    var totalDeductionInput = $("<input/>",{type:"hidden",name:"salary_data["+(countRow + 1)+"][total_deduction]",value:data.total_deduction});
    var deductionDataInput = $("<input/>",{type:"hidden",name:"salary_data["+(countRow + 1)+"][deduction_data]",value:data.deduction_data});
    cell = $(row.insertCell(-1));
	cell.html(data.total_deduction);
    cell.append(totalDeductionInput);
    cell.append(deductionDataInput);

    var netSalaryInput = $("<input/>",{type:"hidden",name:"salary_data["+(countRow + 1)+"][net_salary]",value:data.net_salary});
    cell = $(row.insertCell(-1));
	cell.html(data.net_salary);
    cell.append(netSalaryInput);

    var remarkInput = $("<input/>",{type:"hidden",name:"salary_data["+(countRow + 1)+"][remark]",value:data.remark});
    cell = $(row.insertCell(-1));
	cell.html(data.remark);
    cell.append(remarkInput);

    var btnEdit = $('<button><i class="ti-pencil-alt"></i></button>');
    btnEdit.attr("type", "button");
    btnEdit.attr("onclick", "Edit("+JSON.stringify(data)+",this);");
    btnEdit.attr("class", "btn btn-outline-warning waves-effect waves-light");
    cell = $(row.insertCell(-1));
    cell.append(btnEdit);
    cell.attr("class","text-center");
	cell.attr("style","width:10%;");
}

function Edit(data,button){
    var row_index = $(button).closest("tr").index();
    $("#itemModel").modal();
    $(".btn-save").hide();

    var earningData = ""; var deductionData = "";
    $.each(data,function(key, value) {
        if(key=="earningData"){ earningData = value; }
		else if(key=="deductionData"){ deductionData = value; }
		else{$("#"+key).val(value);}
    }); 

    var earningHtml = "";
    var earningData = JSON.parse(earningData);
    $.each(earningData,function(key,row){
        earningHtml += '<tr>';
        earningHtml += '<td>'+row.head_name+' <input type="hidden" name="earningData['+key+'][head_name]" value="'+row.head_name+'"> <input type="hidden" name="earningData['+key+'][type]" value="'+row.type+'"></td>';
        earningHtml += '<td><input type="text" name="earningData['+key+'][amount]" class="form-control earnings calculateSalary" value="'+row.amount+'" readonly> </td>'; 
        earningHtml += '</tr>';
    });
    
    var deductionHtml = "";
    var deductionData = JSON.parse(deductionData);
    $.each(deductionData,function(key,row){
        var readonly = "readonly";
        if(key == "canteen" || key == "advance"){
            readonly = "";
        }
        deductionHtml += '<tr>';
        deductionHtml += '<td>'+row.head_name+' <input type="hidden" name="deductionData['+key+'][head_name]" value="'+row.head_name+'"> <input type="hidden" name="deductionData['+key+'][type]" value="'+row.type+'"></td>';
        deductionHtml += '<td><input type="text" name="deductionData['+key+'][amount]" class="form-control deductions calculateSalary" value="'+row.amount+'" '+readonly+'> </td>'; 
        deductionHtml += '</tr>';
    });

    var salaryTable = '';
    salaryTable += '<table class="table table-bordered">';
    salaryTable += '<tr class="thead-info"><th>Earning</th><th style="width:180px;">Amount</th></tr>';
    salaryTable += earningHtml;
    salaryTable += '<tr class="thead-info"><th>Total Earning</th><th><input type="text" name="total_earning" id="total_earning" class="form-control" value="'+data.total_earning+'" readonly></th></tr>';
    salaryTable += '<tr class="thead-info"><th>Deduction</th><th>Amount</th></tr>';
    salaryTable += deductionHtml;
    salaryTable += '<tr class="thead-info"><th>Total Deduction</th><th><input type="text" name="total_deduction" id="total_deduction" class="form-control" value="'+data.total_deduction+'" readonly></th></tr>';
    salaryTable += '<tr class="thead-info"><th>Net Salary</th><th><input type="text" name="net_salary" id="net_salary" class="form-control" value="'+data.net_salary+'" readonly></th></tr>';
    salaryTable += '</table>';

    $("#salaryData").html(salaryTable);
    $("#row_index").val(row_index);
}

function savePayRoll(formId){
	
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
            	window.location = base_url + controller;
		}else{
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}				
	});
}
</script>