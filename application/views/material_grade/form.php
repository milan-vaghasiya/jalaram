<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : ""; ?>" />
            <div class="col-md-12 form-group">
                <label for="material_grade">Material Grade</label>
                <input type="text" name="material_grade" id="material_grade" class="form-control req" value="<?= (!empty($dataRow->material_grade)) ? $dataRow->material_grade : "" ?>">
            </div>

            <div class="col-md-12 form-group">
                <label for="density">Density</label>
                <input type="text" name="density" id="density" class="form-control floatOnly" value="<?= (!empty($dataRow->density)) ? $dataRow->density : "" ?>">
            </div>
           
            <div class="col-md-12 form-group">
                <label for="scrap_group">Scrap Group</label>
                <select name="scrap_group" id="scrap_group" class="form-control single-select req">
                    <option value="">Select Scrap Group</option>
                    <?php
                        foreach ($scrapData as $row) :
                            $selected = (!empty($dataRow->scrap_group) && $dataRow->scrap_group == $row->id) ? "selected" : "";
                            echo '<option value="'. $row->id .'" '.$selected.'>'.$row->item_name.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            
            <div class="col-md-12 form-group">
                <label for="color_code">Colour Code</label>
                <select name="color_code" id="color_code" class="form-control single-select">
                    <option value="">Select</option>
                    <?php   
                        foreach($colorList as $color):
                            $selected = (!empty($dataRow->color_code) && $dataRow->color_code == $color) ? "selected" : "";
                            echo '<option value="' . $color . '" ' . $selected . '>' . $color . '</option>';
                        endforeach;                                        
                    ?>
                </select>
            </div>
            <div class="col-md-12 form-group">
                <label for="scrap_per">Scrap Recovery Per(%)</label>
                <input type="text" name="scrap_per" id="scrap_per" class="form-control req" value="<?= (!empty($dataRow->scrap_per)) ? $dataRow->scrap_per : "" ?>">
            </div>
        </div>
    </div>
</form>