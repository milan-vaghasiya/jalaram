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
								<select name="due_type" id="due_type" class="form-control single-select float-right">
									<option value="all">All</option>
									<option value="under_due">Invoice Under Due</option>
									<option value="over_due">Invoice Over Due</option>
								</select>
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
										<th style="min-width:5px;">#</th>
										<th style="min-width:11px;">Invoice Number</th>
										<th style="min-width:11px;">Invoice Date</th>
										<th style="min-width:13px;">Buyer Name</th>
										<th style="min-width:12px;">Invoice Currency</th>
										<th style="min-width:11px;">Comm. Inv. Amount</th>
										<th style="min-width:11px;">Inco Terms</th>
										<th style="min-width:11px;">BL / AWB Date</th>
										<th style="min-width:11px;">Payment Due Date</th>
										<th style="min-width:4px;">Due Days</th>
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
	var due_type = $('#due_type').val();
	var to_date = $('#to_date').val();
	if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
	if(valid){
		$.ajax({
			url: base_url + controller + '/getCollection',
			data: {due_type:due_type, to_date:to_date},
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