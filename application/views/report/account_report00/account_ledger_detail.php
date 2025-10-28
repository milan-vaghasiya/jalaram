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
						    <input type="hidden" id="acc_id" value="<?=(!empty($acc_id))?$acc_id:''?>" />
							<div class="col-md-3">   
                                <input type="date" name="from_date" id="from_date" class="form-control" value="<?=$startDate?>" />
                                <div class="error fromDate"></div>
                            </div>     
                            <div class="col-md-4">  
                                <div class="input-group">
                                    <input type="date" name="to_date" id="to_date" class="form-control" value="<?=$endDate?>" />
                                    <div class="input-group-append ml-2">
                                        <button type="button" class="btn waves-effect waves-light btn-success float-right loaddata" title="Load Data">
									        <i class="fas fa-sync-alt"></i> Load
								        </button>
                                    </div>
                                </div>
                                <div class="error toDate"></div>
                            </div>              
                        </div>                                         
                    </div>
                    <div class="card-body reportDiv" style="min-height:75vh">
                        <div class="table-responsive" style="width: 100%;">
                            <table id='commanTableDetail' class="table table-bordered" style="width:100%;">
								<thead class="thead-info" id="theadData">
                                    <tr class="text-center">
										<th colspan="4" class="text-left">Ledger Name : <span id="ledger_name"></span></th>
										<th colspan="3" class="text-right">Report Period : <span id="report_date"></span></th>
									</tr>
                                    <tr class="text-center">
										<th colspan="7" class="text-right">Opning Balance : <span id="op_balance">0.00</span></th>
									</tr>
									<tr>
										<th>#</th>
										<th>Vou. Date</th>
										<th>Vou. No.</th>
										<th>Particulars</th>
										<th>Voucher Type</th>
										<th>Amount(CR.)</th>
										<th>Amount(DR.)</th>
									</tr>
								</thead>
								<tbody id="ledgerDetail">
								</tbody>  
                                <tfoot class="thead-info">
                                    <tr>
                                        <th colspan="5" class="text-right">Total</th>
                                        <th id="cr_balance">0.00</th>
                                        <th id="dr_balance">0.00</th>
                                    </tr>
                                    <tr>
                                        <th colspan="7" class="text-right">Closing Balance : <span id="cl_balance">0.00</span></th>
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
	loadData();
    $(document).on('click','.loaddata',function(){
		loadData();
	});  
});

function loadData(){
	$(".error").html("");
	var valid = 1;
	var acc_id = $('#acc_id').val();
	var from_date = $('#from_date').val();
	var to_date = $('#to_date').val();
	if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
	if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
	if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}
	if(valid){
		$.ajax({
			url: base_url + controller + '/getLedgerTransaction',
			data: {acc_id:acc_id,from_date:from_date, to_date:to_date},
			type: "POST",
			dataType:'json',
			success:function(data){              
				console.log(data.ledgerBalance);
                $("#commanTableDetail").DataTable().clear().destroy();
                $("#ledgerDetail").html("");
				$("#ledgerDetail").html(data.tbody);
                
                $("#cl_balance").html("");
                $("#cl_balance").html(Math.abs(data.ledgerBalance.cl_balance)+" "+data.ledgerBalance.cl_balance_type);
                $("#op_balance").html("");
                $("#op_balance").html(Math.abs(data.ledgerBalance.op_balance)+" "+data.ledgerBalance.op_balance_type);

                $("#cr_balance").html("");
                $("#cr_balance").html(data.ledgerBalance.cr_balance);
                $("#dr_balance").html("");
                $("#dr_balance").html(data.ledgerBalance.dr_balance);
                
                $("#ledger_name").html("");
                $("#ledger_name").html(data.ledgerBalance.account_name);
                
                $("#report_date").html("");
                $("#report_date").html(from_date+" to "+to_date);
                
                jpReportTable('commanTableDetail');

			}
		});
	}
}


</script>