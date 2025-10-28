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
                            <div class="col-md-4">
                                <select name="job_id" id="job_id" class="form-control single-select">
                                    <option value="">Select Job Card</option>
                                    <?php   
										foreach($jobCardList as $row): 
											echo '<option value="'.$row->id.'" data-item_id="'.$row->product_id.'">'.getPrefixNumber($row->job_prefix,$row->job_no).' ['.$row->item_code.']</option>';
										endforeach; 
                                    ?>
                                </select>
                            </div>
							<div class="col-md-5 form-group">
							    <div class="input-group">
                                    <select name="cost_id" id="cost_id" class="form-control single-select req" style="width:80%;">
                                        <option value="">Select Master Cost</option>
                                        
                                    </select>
                                    <div class="input-group-append" style="width:20%;">
										<button type="button" class="btn btn-block waves-effect waves-light btn-success float-right loadData" title="Load Data" >
											<i class="fas fa-sync-alt"></i> Load
										</button>
									</div>
								</div>
							</div>                           
                        </div>                                         
                    </div>
                    <div class="card-body reportDiv" style="min-height:75vh">
						<div class="row">
							<div class="col-md-6"  id="master_cost_detail">
								
							</div>
							<div class="col-md-6" id="job_cost_detail">
								
							</div>
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
	$(document).on('change','#job_id',function(e){
		var item_id = $("#job_id").find(":selected").data('item_id');
		$("#cost_id").html("");$("#cost_id").comboSelect();
		if(item_id)
		{
			$.ajax({
				url: base_url + controller + '/getCostList',
				data: {item_id:item_id},
				type: "POST",
				dataType:'json',
				success:function(data){
					$("#cost_id").html(data.option);
					$("#cost_id").comboSelect();
				}
			});
		}
	});

	$(document).on('click','.loadData',function(e){
		var valid = 1;
		var job_card_id = $("#job_id").val();
		var cost_id = $("#cost_id").val();
		if(job_card_id == ""){$(".job_id").html("Job is required.");valid=0;}
		if(cost_id == ""){$(".cost_id").html("Cost is required.");valid=0;}

		if(valid)
		{
			$.ajax({
				url: base_url + controller + '/getJobCostingComparisonData',
				data: {cost_id:cost_id,job_card_id:job_card_id},
				type: "POST",
				dataType:'json',
				success:function(data){
					$("#master_cost_detail").html(data.master_cost_detail);
					$("#job_cost_detail").html(data.job_cost_detail);
				}
			});
		}
	});
});
</script>