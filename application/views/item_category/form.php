<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
			<div class="col-md-12 form-group">
                <label for="category_name">Category Name</label>
                <input type="text" name="category_name" class="form-control req" value="<?=(!empty($dataRow->category_name))?$dataRow->category_name:""?>" />
            </div>
            <div class="col-md-12 form-group">
                <label for="category_code">Category Code</label>
                <input type="text" name="category_code" id="category_code" class="form-control" value="<?=(!empty($dataRow->category_code))?$dataRow->category_code:"";?>">
            </div>
			<div class="col-md-12 form-group">
                <label for="category_type">Item Group</label>
                <select name="category_type" id="category_type" class="form-control single-select req">
                    <option value="0">Select</option>
                    <?php
                        foreach ($itemGroup as $row) :
                            $selected = (!empty($dataRow->category_type) && $dataRow->category_type == $row->id) ? "selected" : "";
                            echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->group_name . '</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-12 form-group">
                <label for="remark">Remark</label>
                <textarea name="remark" class="form-control" rows="3" ><?=(!empty($dataRow->remark))?$dataRow->remark:"";?></textarea>
            </div>
            
        </div>
    </div>
</form>