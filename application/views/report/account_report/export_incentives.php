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
							<div class="col-md-3">   
                                <input type="date" name="from_date" id="from_date" class="form-control" value="<?=$startDate?>" />
                                <div class="error fromDate"></div>
                            </div>     
                            <div class="col-md-3">  
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
                        <div class="table-responsive">
                            <table id='commanTable' class="table table-bordered">
								<thead class="thead-info" id="theadData">
									<tr>
										<th style="min-width:3px;">#</th>
										<th style="min-width:3px;">Invoice Number</th>
										<th style="min-width:3px;">Invoice Date</th>
										<th style="min-width:3px;">Buyer Name</th>
										<th style="min-width:3px;">Destination Country</th>
										<th style="min-width:3px;">Port of Loading</th>
										<th style="min-width:3px;">Port of Discharge</th>
										<th style="min-width:3px;">Invoice Currency</th>
										<th style="min-width:3px;">Comm. Invoice Amount</th>
										<th style="min-width:3px;">SB Amount (FC)</th>
										<th style="min-width:3px;">Port Code</th>
										<th style="min-width:3px;">SB Number</th>
										<th style="min-width:3px;">SB Date</th>
										<th style="min-width:3px;">SB FOB INR</th>
										<th style="min-width:3px;">SB Freight INR</th>
										<th style="min-width:3px;">SB Insurance INR</th>
										<th style="min-width:3px;">SB Ex. Rate</th>
										<th style="min-width:3px;">SB Remarks</th>
										<th style="min-width:3px;">Inco Terms</th>
										<th style="min-width:3px;">CHA & FA</th>
										<th style="min-width:3px;">BL / AWB No.</th>
										<th style="min-width:3px;">BL / AWB Date</th>
										<th style="min-width:3px;">Payment Due Date</th>
										<th style="min-width:3px;">BL Remarks</th>
										<th style="min-width:3px;">Drawback Amount</th>
										<th style="min-width:3px;">Drawback Date</th>
										<th style="min-width:3px;">IGST Amount</th>
										<th style="min-width:3px;">IGST Refund Date</th>
										<th style="min-width:3px;">IGST Refund Error</th>
										<th style="min-width:3px;">RODTEP Amount</th>
										<th style="min-width:3px;">RODTEP Date</th>
									</tr>
								</thead>
								<tbody id="receivableData"></tbody>
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
	var from_date = $('#from_date').val();
	var to_date = $('#to_date').val();
	if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
	if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
	if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}
	if(valid){
		$.ajax({
			url: base_url + controller + '/getIncentives',
			data: {from_date:from_date, to_date:to_date},
			type: "POST",
			dataType:'json',
			success:function(data){
				$("#commanTable").DataTable().clear().destroy();
				$("#receivableData").html("");
				$("#receivableData").html(data.tbody);
				jpReportTable('commanTable');
			}
		});
	}
} 
</script>