<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-3">
                                <h4 class="card-title pageHeader"><?=$pageHeader?></h4>
                            </div>
                            <div class="col-md-9">  
                                <div class="input-group">
                                    <select name="itc_type" id="itc_type" class="form-control single-select float-right" style="width:26%">
                                        <option value="1">TABLE - 4</option>
                                        <option value="2">TABLE - 5A</option>
                                    </select>
                                    <input type="date" name="from_date" id="from_date" class="form-control" value="<?=$startDate?>" style="width:24%" />
                                    <div class="error fromDate"></div>
                                    <input type="date" name="to_date" id="to_date" class="form-control" value="<?=$endDate?>" style="width:24%" />
                                    <button type="button" class="btn waves-effect waves-light btn-success float-right loaddata" data-file_type='view' title="View Data" style="width:13%;border-radius:0px;"><i class="fas fa-eye"></i> View</button>
                                    <button type="button" class="btn waves-effect waves-light btn-warning float-right loaddata" data-file_type='excel' title="Download Excel" style="width:13%;border-radius:0px;"><i class="fas fa-file-excel"></i> EXCEL</button>
                                </div>
                                <div class="error toDate"></div>
                            </div>                  
                        </div>                                         
                    </div>
                    <div class="card-body reportDiv" style="min-height:75vh">
                        <div class="table-responsive">
                            <table id='commanTable' class="table table-bordered">
								<thead class="thead-info dataHead" id="theadData">
                                    <tr class="text-center"><th colspan="15"><?=$pageHeader?></th></tr>
									<tr>
										<th style="min-width:100px;">Job Worker</th>
										<th style="min-width:80px;">Job Worker GSTIN</th>
										<th style="min-width:50px;">State</th>
										<th style="min-width:100px;">Job Worker's Type</th>
										<th style="min-width:80px;">Challan Number</th>
										<th style="min-width:100px;">Challan Date</th>
										<th style="min-width:100px;">Types of Goods</th>
										<th style="min-width:100px;">Description of Goods</th>
										<th style="min-width:100px;">UQC</th>
										<th style="min-width:100px;">QTY</th>
										<th style="min-width:50px;">Taxable Value</th>
										<th style="min-width:50px;">IGST Rate</th>
										<th style="min-width:50px;">CGST Rate</th>
										<th style="min-width:50px;">SGST Rate</th>
										<th style="min-width:50px;">Cess</th>
									</tr>
								</thead>
								<tbody class="dataBody"></tbody>
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
	//loadData();
    $(document).on('click','.loaddata',function(){var file_type= $(this).data('file_type');loadData(file_type);});  
});

function loadData(file_type){
    
    	var valid = 1;var fname = '/getGSTITC4';
    	var itc_type = $('#itc_type').val();
    	var from_date = $('#from_date').val();
    	var to_date = $('#to_date').val();
    	if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
    	if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
    	if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}
    	if(itc_type==2){fname = '/getGSTITC5';}
    	if(valid)
    	{
    	    if(file_type == 'view')
            {
        		$.ajax({
        			url: base_url + controller + fname,
        			data: {from_date:from_date, to_date:to_date},
        			type: "POST",
        			dataType:'json',
        			success:function(data){
        				$("#commanTable").DataTable().clear().destroy();
        				$("#purchaseReportData").html("");
        				$(".dataHead").html(data.thead);
        				$(".dataBody").html(data.tbody);
        				jpReportTable('commanTable');
        			}
        		});
            }
            else
            {
                window.open(base_url + controller + fname +'/'+from_date+'/'+to_date+'/'+file_type, '_blank').focus();
            }
    	}
}
</script>