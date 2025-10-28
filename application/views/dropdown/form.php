<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            
            <div class="col-md-12 form-group">
                <label for="type">Type</label>
                <select name="type" id="type" class="form-control single-select req">
                    <?php
                        if(!empty($dropdownType))
                        {
                            foreach($dropdownType as $key=>$val)
                            {
                                if(!empty($val))
                                {
                                    $selected = (!empty($dataRow->type) AND $dataRow->type == $key) ? 'selected' : '' ;
                                    echo '<option value="'.$key.'" '.$selected.' >'.$val.'</option>';
                                }
                            }
                        }
                    ?>
                </select> 
                <div class="error type"></div>
            </div>
            <div class="col-md-12 form-group">
                <label for="description">Description</label>
                <textarea rows="3" name="description" class="form-control req" ><?=(!empty($dataRow->description))?$dataRow->description:""?></textarea>
            </div>
            <div class="col-md-12 form-group">
                <label for="remark">Remark</label>
                <textarea rows="2" name="remark" class="form-control" ><?=(!empty($dataRow->remark))?$dataRow->remark:""?></textarea>
            </div>
        </div>
    </div>
</form>