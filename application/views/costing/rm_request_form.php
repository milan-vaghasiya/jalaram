<form>
    <div class="row">
        <input type="hidden" name="id" value="<?=$dataRow->id?>">
        <div class="col-md-12 form-group">
            <label for="grade_id">Grade</label>
            <select name="grade_id" id="grade_id" class="form-control single-select req">
                <option value="">Select Grade</option>
                <?php
                if(!empty($gradeList)){
                    foreach($gradeList AS $row){
                        $selected = ((!empty($dataRow->grade_id) && $dataRow->grade_id == $row->id)?"selected":"");
                    ?> <option value="<?=$row->id?>" <?=$selected?>><?=$row->material_grade?></option> <?php
                    }
                }
                ?>
            </select>
        </div>
        <div class="col-md-12 form-group">
            <label for="dimension">Dimension</label>
            <input type="text" name="dimension" class="form-control req" value="<?=$dataRow->dimension?>"> 
        </div>
        <div class="col-md-6 form-group">
            <label for="moq">MOQ</label>
            <input type="text" name="moq" class="form-control numericOnly req" value="<?=$dataRow->moq?>"> 
        </div>
        <div class="col-md-6 form-group">
            <label for="gross_wt">Gross Weight</label>
            <input type="text" name="gross_wt" class="form-control floatOnly req" value="<?=$dataRow->gross_wt?>"> 
        </div>
    </div>
</form>