<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="">
            <input type="hidden" name="ref_type" id="ref_type" value="-1">
            <input type="hidden" name="ref_batch" id="ref_batch" value="OLDPACKSTOCK">
            
            <div class="col-md-4 form-group">
                <label for="ref_date">Date</label>
                <input type="date" name="ref_date" id="ref_date" class="form-control req" value="<?=date('Y-m-d')?>">
            </div>

            <div class="col-md-8 form-group">
                <label for="item_id">Item Name</label>
                <select name="item_id" id="item_id" class="form-control single-select req">
                    <option value="">Select Item</option>
                    <?php 
						foreach($itemList as $row):
							$item_name = (!empty($row->item_code)) ? "[".$row->item_code."] ".$row->item_name : $row->item_name;
							echo '<option value="'.$row->id.'">'.$item_name.'</option>';
						endforeach;
					?>
                </select>
            </div>
            <div class="col-md-6 form-group">
                <label for="batch_no">Batch No.</label>
                <input type="text" name="batch_no" id="batch_no" class="form-control req" value="">
            </div>
            <div class="col-md-6 form-group">
                <label for="qty">Qty</label>
                <input type="text" name="qty" id="qty" class="form-control floatOnly req" value="">
            </div>

            <div class="col-md-12 form-group">
                <label for="remark">Remark</label>
                <input type="text" name="remark" id="remark" class="form-control numericOnly" value="">
            </div>
        </div>
    </div>
</form>
<script>
function resItemDetail(response){
    if(response != ""){
        var itemDetail = response.data.itemDetail;
        $("#size").val(itemDetail.packing_standard);
    }else{
        $("#size").val("");
    }
}
</script>