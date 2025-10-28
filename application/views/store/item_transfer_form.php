<form>
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-6 form-group">
                <label for="batch_no">Batch No.</label>
                <input type="text" name="batch_no" id="batch_no" class="form-control" value="<?=(!empty($dataRow))?$dataRow['batch_no']:""?>" readonly />
                <input type="hidden" name="from_location_id" id="from_location_id" value="<?=(!empty($dataRow))?$dataRow['location_id']:""?>">
                <input type="hidden" name="item_id" id="item_id" value="<?=(!empty($dataRow))?$dataRow['item_id']:""?>">
            </div>
            <div class="col-md-6 form-group">
                <label for="stock_qty">Stock Qty.</label>
                <input type="text" id="stock_qty" class="form-control" value="<?=(!empty($dataRow))?$dataRow['stock_qty']:""?>" readonly />
            </div>
            <div class="col-md-6 form-group">
                <label for="new_item_id">Item</label>
                <select name="new_item_id" id="new_item_id" class="form-control single-select req">
                    <option value="">Select Item</option>
                    <?php
                        foreach($itemList as $row):  
                            if($dataRow['item_id'] != $row->id){
                                echo '<option value="'.$row->id.'" >'.(!empty($row->item_code)?'[ '.$row->item_code.' ] ':'').$row->item_name.' </option>';
                            }
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-6 form-group">
                <label for="transfer_qty">Qty.</label>
                <input type="text" name="transfer_qty" id="transfer_qty" class="form-control floatOnly req" value="" />
            </div>
        </div>
    </div>
</form>