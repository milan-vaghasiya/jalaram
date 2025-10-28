<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-4">
                                <h4 class="card-title pageHeader"><?=$pageHeader?></h4>
                            </div>
                            <div class="col-md-3">
								<select name="party_id" id="party_id" class="form-control single-select">
									<option value="0">Select All Party</option>
									<?php
										foreach ($customerData as $row) :
											echo "<option value='" . $row->id . "'>" . $row->party_name . "</option>";
										endforeach;
									?>
								</select>      
                            </div> 
							<div class="col-md-2">   
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
								<thead class="thead-info salesRegisterDataHead" id="theadData">
                                    <tr class="text-center"><th colspan="10"><?=$pageHeader?></th></tr>
									<tr>
										<th style="min-width:25px;">#</th>
										<th style="min-width:80px;">Vou. Date</th>
										<th style="min-width:50px;">Vou. No</th>
										<th style="min-width:100px;">Account Name</th>
										<th style="min-width:100px;">Taxable Amount</th>
										<th style="min-width:100px;">Cgst Amount</th>
										<th style="min-width:100px;">Sgst Amount</th>
										<th style="min-width:100px;">Igst Amount</th>
										<th style="min-width:100px;">Other Amount</th>
										<th style="min-width:50px;">Vou. Amount</th>
									</tr>
								</thead>
								<tbody id="salesRegisterData"></tbody>
								<tfoot id="salesRegisterDataFoot"></tfoot>
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
	var party_id = $('#party_id').val();
	var from_date = $('#from_date').val();
	var to_date = $('#to_date').val();
	if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
	if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
	if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}
	if(valid){
		$.ajax({
			url: base_url + controller + '/getSalesRegisterReport',
			data: {party_id:party_id,from_date:from_date, to_date:to_date},
			type: "POST",
			dataType:'json',
			success:function(data){
				$("#commanTable").DataTable().clear().destroy();
				$("#salesRegisterData").html("");$("#salesRegisterData").html(data.tbody);
				$(".salesRegisterDataHead").html("");$(".salesRegisterDataHead").html(data.thead);
				$("#salesRegisterDataFoot").html("");$("#salesRegisterDataFoot").html(data.tfoot);
				jpReportTable('commanTable');
			}
		});
	}
}
</script>