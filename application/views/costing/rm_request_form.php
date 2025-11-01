<form>
    <div class="row">
        <input type="hidden" name="id" value="<?=$dataRow->id?>">
        <input type="hidden" name="density" value="<?= $dataRow->density ?? '';?>">

        <div class="col-md-12 form-group">
            <label for="grade_id">Grade</label>
            <select name="grade_id" id="grade_id" class="form-control single-select req">
                <option value="">Select Grade</option>
                <?php
                    if(!empty($gradeList)){
                        foreach($gradeList AS $row){
                            $selected = ((!empty($dataRow->grade_id) && $dataRow->grade_id == $row->id)?"selected":"");
                        ?> <option value="<?=$row->id?>" <?=$selected?> data-density="<?= $row->density;?>"><?=$row->material_grade?> (<?= $row->density;?>)</option> <?php
                        }
                    }
                ?>
            </select>
        </div>
        <!-- <div class="col-md-12 form-group">
            <label for="dimension">Dimension</label>
            <input type="text" name="dimension" class="form-control req" value="<?=$dataRow->dimension?>"> 
        </div> -->

        <div class="col-md-6 form-group">
            <label for="shape">Shape</label>
            <select name="shape" id="shape" class="form-control single-select req">
                <option value="round_dia">Round Bar</option>
                <option value="square">Square Bar</option>
                <option value="rectangle">Rectangle Bar</option>
                <option value="pipe">Pipe / Tube</option>
                <option value="hex">Hexagonal Bar</option>
                <option value="sheet">Sheet / Plate</option>
            </select>
        </div>

        <div class="col-md-6 form-group first_section">
            <label class="first_section_label" for="first_section_label">Diameter (mm)</label>
            <input type="text" name="field1" id="first_section_label" class="form-control floatOnly req" placeholder="Diameter (mm)"> 
            <div class="error field1"></div>
        </div>
        
        <div class="col-md-6 form-group second_section d-none">
            <label class="second_section_label" for="second_section_label">Width (mm)</label>
            <input type="text" name="field2" id="second_section_label" class="form-control floatOnly req" placeholder="Width (mm)"> 
            <div class="error field2"></div>
        </div>

        <div class="col-md-6 form-group third_section">
            <label class="third_section_label" for="third_section_label">Length (mm)</label>
            <input type="text" name="field3" id="third_section_label" class="form-control floatOnly req" placeholder="Length (mm)">
            <div class="error field3"></div>
        </div>

        <div class="col-md-6 form-group">
            <label for="moq">MOQ</label>
            <input type="text" name="moq" class="form-control numericOnly req" value="<?=$dataRow->moq?>"> 
        </div>
        <div class="col-md-6 form-group">
            <label for="gross_wt">Gross Weight (kg)</label>
            <input type="text" name="gross_wt" class="form-control floatOnly req" value="<?=$dataRow->gross_wt?>"> 
        </div>

        <div class="col-md-6 form-group">
            <label for="total_gross_wt">Total Gross Weight (kg)</label>
            <input type="text" name="total_gross_wt" class="form-control floatOnly req" value="<?=$dataRow->total_gross_wt ?? '';?>"> 
        </div>

        <div class="col-md-6 form-group">
            <label>&nbsp;</label><br>
            <button type="button" id="calc" class="btn btn-primary btn-block" >Calculate Weight</button>
        </div>

        <div class="col-md-12 form-group">
            <div class="result error" id="result"></div>
        </div>
    </div>
</form>

<script>
$(document).ready(function(){    
    $(document).on('change', "[name='shape']", function() {
        $('.error').text('');
        let shape = $('[name="shape"] option:selected').val();

        if(shape == 'round_dia'){
            $('.first_section_label').text('Diameter (mm)');
            $('.first_section_label').next('input').attr('placeholder','Diameter (mm)');
            $('.second_section').addClass('d-none');
        }
        else if(shape == 'square'){
            $('.first_section_label').text('Width (mm)');
            $('.first_section_label').next('input').attr('placeholder','Width (mm)');

            $('.second_section').addClass('d-none');
        }
        else if(shape == 'rectangle'){
            $('.first_section_label').text('Width (mm)');
            $('.second_section_label').text('Height / Thickness (mm)');
            $('.first_section_label').next('input').attr('placeholder','Width (mm)');
            $('.second_section_label').next('input').attr('placeholder','Height / Thickness (mm)');

            $('.second_section').removeClass('d-none');
        }
        else if(shape == 'pipe'){
            $('.first_section_label').text('Outer Diameter (mm)');
            $('.second_section_label').text('Inner Diameter (mm)');
            $('.first_section_label').next('input').attr('placeholder','Outer Diameter (mm)');
            $('.second_section_label').next('input').attr('placeholder','Inner Diameter (mm)');

            $('.second_section').removeClass('d-none');
        }
        else if(shape == 'hex'){
            $('.first_section_label').text('Flat to Flat (mm)');
            $('.first_section_label').next('input').attr('placeholder','Flat to Flat (mm)');

            $('.second_section').addClass('d-none');
        }
        else if(shape == 'sheet'){
            $('.first_section_label').text('Width (mm)');
            $('.second_section_label').text('Height / Thickness (mm)');
            $('.first_section_label').next('input').attr('placeholder','Width (mm)');
            $('.second_section_label').next('input').attr('placeholder','Height / Thickness (mm)');

            $('.second_section').removeClass('d-none');
        }
    });
    
    $(document).on('change', "[name='grade_id']", function() {
        let density = $("[name='grade_id'] option:selected").attr('data-density');
        $('[name="density"]').val(density);
    });
    $("[name='grade_id']").trigger('change');
        
    $('#calc').click(function(){
        let shape = $('#shape').val();
        let length = parseFloat($('[name="field3"]').val()) / 1000; // mm to m
        let densityVal = $('[name="density"]').val();
        let density = parseFloat(densityVal) * 1000; // g/cm³ to kg/m³
        let volume = 0;
        
        if(shape === 'round_dia'){
            let d = parseFloat($('[name="field1"]').val()) / 1000;
            volume = Math.PI * Math.pow(d/2, 2) * length;
        }
        else if(shape === 'square'){
            let a = parseFloat($('[name="field1"]').val()) / 1000;
            volume = Math.pow(a, 2) * length;
        }
        else if(shape === 'rectangle'){
            let w = parseFloat($('[name="field1"]').val()) / 1000;
            let h = parseFloat($('[name="field2"]').val()) / 1000;
            volume = w * h * length;
        }
        else if(shape === 'pipe'){
            let od = parseFloat($('[name="field1"]').val()) / 1000;
            let id = parseFloat($('[name="field2"]').val()) / 1000;
            volume = Math.PI * (Math.pow(od/2,2) - Math.pow(id/2,2)) * length;
        }
        else if(shape === 'hex'){
            let f = parseFloat($('[name="field1"]').val()) / 1000;
            volume = 0.866 * Math.pow(f,2) * length;
        }
        else if(shape === 'sheet'){
            let w = parseFloat($('[name="field1"]').val()) / 1000;
            let t = parseFloat($('[name="field2"]').val()) / 1000;
            volume = w * t * length;
        }
        
        let totalWeight = volume * density; // kg
        let weightPerMeter = (volume / length) * density; // kg/m

        let moq = parseFloat($('[name="moq"]').val());
        let totalGrossWeight = totalWeight * moq;
        
        if(!isNaN(totalWeight) && totalWeight > 0){
            $('[name="gross_wt"]').val(totalWeight.toFixed(3));
            $('[name="total_gross_wt"]').val(totalGrossWeight.toFixed(3));
            $('#result').text('');
        } else {
            $('[name="gross_wt"]').val(0);
            $('[name="total_gross_wt"]').val(0);
            $('#result').text('Invalid inputs.');
        }
    });
});
</script>