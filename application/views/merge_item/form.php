<form>
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-4 form-group">
                <label for="item_type">Item Type</label>
                <select name="item_type" id="item_type" class="form-control single-select req">
                    <option value="">Select Item Type</option>
                    <?php
                        foreach($fromItemType as $row):
                            echo '<option value="'.$row->id.'">'.$row->group_name.' </option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-4 form-group">
                <label for="from_item">From Item</label>
                <select name="from_item" id="from_item" class="form-control single-select req">
                    <option value="">Select From Item</option>
                </select>
            </div>

            <div class="col-md-4 form-group">
                <label for="to_item">To Item</label>
                <select name="to_item" id="to_item" class="form-control single-select req">
                    <option value="">Select To Item</option>
                </select>
            </div>
        </div>
    </div>
</form>