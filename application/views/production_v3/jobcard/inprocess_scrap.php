<table class="table" style="border-radius:0px;box-shadow: 1px 2px 2px 0 rgb(0 0 0 / 70%);left:0;top:0px;position:absolute;">
    <tr class="">
        <th class="text-center text-white" style="background:#aeaeae;padding:0.25rem 0.5rem;border-top-left-radius:0px;border-bottom-left-radius:0px;border:0px;">Material Name</th>
        <th class="text-left" style="background:#f3f2f2;padding:0.25rem 0.5rem;" id="materialName"><?= !empty($bomData->scrap_name)?$bomData->scrap_name:$approvalData->item_code.' '.$approvalData->item_name?></th>
        <th class="text-center text-white" style="background:#aeaeae;padding:0.25rem 0.5rem;">Qty</th>
        <th class="text-left" id="pending_production" style="background:#f3f2f2;padding:0.25rem 0.5rem;border-top-right-radius:0px; border-bottom-right-radius:0px;border:0px;" id="pending_production"></th>
    </tr>
</table>
<form>
    <div class="col-md-12 mt-5">
        <input type="hidden" name="item_id" id="item_id" value="<?= !empty($bomData->scrap_group)?$bomData->scrap_group:$approvalData->product_id?>" />
        <input type="hidden" name="job_card_id" id="job_card_id" value="<?= $approvalData->job_card_id ?>" />
        <input type="hidden" name="job_approval_id" id="job_approval_id" value="<?= $approvalData->id ?>" />
        <input type="hidden" id="job_number" name="job_number" value="<?=getPrefixNumber($approvalData->job_prefix,$approvalData->job_no)?>"> 
    </div>
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-3 form-group">
                <label for="ref_date">Date</label>
                <input type="date" name="ref_date" id="ref_date" class="form-control" value="<?=date("Y-m-d")?>">
            </div>
            <?php if(!empty($bomData->scrap_group)) : ?>
            <div class="col-md-3 form-group">
                <label for="batch_no">Batch No.</label>
                <select id="batch_no" name="batch_no" class="form-control req" name="batch_no">
                    <?= $batchData ?>
                </select>
            </div>
            <?php else : ?>
                <input type="hidden" id="batch_no" name="batch_no" value="<?=getPrefixNumber($approvalData->job_prefix,$approvalData->job_no)?>"> 
            <?php endif; ?>
            <div class="col-md-3 form-group">
                <label for="qty">Qty(Kgs).</label>
                <input type="text" name="qty" id="qty" class="form-control floatOnly req" value="0" min="0" />
            </div>
            <div class="col-md-3 form-group">
                <label for="scrap_qty">Scrap Qty</label>
                <input type="text" name="scrap_qty" id="scrap_qty" class="form-control floatOnly req" value="0" min="0" />
            </div>
            <div class="col-md-10 form-group">
                <label for="remark">Remark</label>
                <input type="text" name="remark" id="remark" class="form-control">
            </div>
            <div class="col-md-2 form-group">
                <label>&nbsp;</label>
                <button type="button" class="btn btn-primary waves-effect waves-light btn-block" onclick="saveInProcessScrap();"><i class="fa fa-check"></i> Save</button>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 mt-10">
                <div class="error item_stock mb-3"></div>
                <div class="table-responsive">
                    <table id="inPrsScrapTable" class="table table-bordered align-items-center" style="width:100%;">
                        <thead class="thead-info">
                            <tr>
                                <th style="width:5%;">#</th>
                                <th>Date</th>
                                <th>Batch no</th>
                                <th>Qty (Kg)</th>
                                <th>Scrap Qty</th>
                                <th>Remark</th>
                                <th class="text-center" style="width:10%;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="inPrsScrapTbody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </div>
</form>
<script>
   var tbodyData = false;
$(document).ready(function(){
    if(!tbodyData){
        getInProcessScrapHtml();
        tbodyData = true;
    }
});
function getInProcessScrapHtml(){ 
    var postData = {'job_card_id':$("#job_card_id").val(),'job_approval_id':$("#job_approval_id").val()}
	var table_id = "inPrsScrapTable";
	var tbody_id = "inPrsScrapTbody";

	$.ajax({
		url: base_url + 'production_v3/jobcard/getInProcessScrapHtml',
		data:postData,
		type: "POST",
		dataType:"json",
		beforeSend: function() {
			if(table_id != ""){
				var columnCount = $('#'+table_id+' thead tr').first().children().length;
				$("#"+table_id+" #"+tbody_id).html('<tr><td colspan="'+columnCount+'" class="text-center">Loading...</td></tr>');
			}
		},
	}).done(function(res){
		$("#"+table_id+" #"+tbody_id).html('');
        $("#"+table_id+" #"+tbody_id).html(res.tbodyData);
        $("#pending_production").html(res.pending_production);
        $(".single-select").comboSelect();
        setPlaceHolder();
	});
}
</script>