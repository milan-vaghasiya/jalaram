<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <div class="col-md-6 form-group">
                <label for="process_name">Process Name</label>
                <input type="text" name="process_name" class="form-control req" value="<?=(!empty($dataRow->process_name))?$dataRow->process_name:"";?>" />
            </div>
            <div class="col-md-6 form-group">
                <label for="dept_id">Department</label>
                <select id="mul_dept_id"  data-input_id="dept_id" class="form-control jp_multiselect req" multiple="multiple">
                    <option value="">Select Department</option>
                    <?php
                        foreach($deptRows as $row):
                            $selected = "";
                            if (!empty($dataRow->dept_id) && in_array($row->id,explode(',',$dataRow->dept_id))) {
                                $selected = "selected";
                            }
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->name.'</option>';
                        endforeach;
                    ?>
                </select>
                <input type="hidden" name="dept_id" id="dept_id" value="<?=(!empty($dataRow->dept_id) ? $dataRow->dept_id :"")?>">
            </div>
            <div class="col-md-4 form-group">
                <label for="is_machining">Is Machining?</label>
                <select name="is_machining" id="is_machining" class="form-control single-select" >
                    <option value="Yes" <?=(!empty($dataRow->is_machining) && $dataRow->is_machining == "Yes")?"selected":""?>>Yes</option>
                    <option value="No" <?=(!empty($dataRow->is_machining) && $dataRow->is_machining == "No")?"selected":""?>>No</option>
                </select>
            </div>
            <div class="col-md-4 form-group">
                <label for="is_pir">PIR Report</label>
                <select name="is_pir" id="is_pir" class="form-control single-select" >
                    <option value="1" <?=(!empty($dataRow->is_pir) && $dataRow->is_pir == "1")?"selected":""?>>Yes</option>
                    <option value="0" <?=(!empty($dataRow) && $dataRow->is_pir == "0")?"selected":""?>>No</option>
                </select>
            </div>
            <div class="col-md-4 form-group">
                <label for="mhr">M.H.R</label>
                <input type="text" name="mhr" class="form-control floatOnly" value="<?=(!empty($dataRow->mhr))?$dataRow->mhr:"";?>" />
            </div>
            <div class="col-md-12 form-group">
                <label for="remark">Remark</label>
                <textarea name="remark" id="remark" class="form-control"><?=(!empty($dataRow->remark))?$dataRow->remark:""?></textarea>
            </div>
        </div>
    </div>
</form>