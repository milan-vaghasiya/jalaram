<form>
    <div class="col-md-12 form-group">
        <div class="table-responsive">
            <table class="jpExcelTable">
                <tr>
                    <th style="width:20%">Grade</th>
                    <td colspan="3"><?=$dataRow->material_grade?></td>
                </tr>
                <tr>
                    <!-- <th style="width:20%">Dimension</th> -->
                    <!-- <td style="width:30%"><?//=$dataRow->dimension?></td> -->
                    <th style="width:20%">Shape</th>
                    <td style="width:30%"><?= $shape ?? '';?></td>
                    <th style="width:20%">MOQ</th>
                    <td style="width:30%"><?=$dataRow->moq?></td>
                </tr>
                <tr>
                    <th>Gross Wt</th>
                    <td><?=$dataRow->gross_wt?></td>
                    <th>Required</th>
                    <td><?=($dataRow->gross_wt * $dataRow->moq)?></td>
                </tr>
            </table>
        </div>
    </div>
    <div class="col-md-12 form-group">
        <div class="row">
            <input type="hidden" name="id" value="<?=$dataRow->id?>">

            <!-- <div class="col-md-6 form-group">
                <label for="dimension">Dimension</label>
                <input type="text" name="dimension" id="dimension" class="form-control req" value="<?=(!empty($dataRow->dimension)?$dataRow->dimension:'')?>">
            </div> -->

            <div class="col-md-6 form-group first_section">
                <label class="first_section_label" for="first_section_label">Diameter (mm)</label>
                <input type="text" name="field1" id="first_section_label" class="form-control floatOnly req" value="<?= $dataRow->field1 ?? '';?>" placeholder="Diameter (mm)"> 
            </div>
            
            <div class="col-md-6 form-group second_section d-none">
                <label class="second_section_label" for="second_section_label">Width (mm)</label>
                <input type="text" name="field2" id="second_section_label" class="form-control floatOnly req" value="<?= $dataRow->field2 ?? '';?>" placeholder="Width (mm)"> 
            </div>

            <div class="col-md-6 form-group third_section">
                <label class="third_section_label" for="third_section_label">Length (mm)</label>
                <input type="text" name="field3" id="third_section_label" class="form-control floatOnly req" value="<?= $dataRow->field3 ?? '';?>" placeholder="Length (mm)">
            </div>
            
            <div class="col-md-6 form-group">
                <label for="rm_rate">Rate</label>
                <input type="text" name="rm_rate" id="rm_rate" class="form-control req" value="<?=(!empty($dataRow->rm_rate)?$dataRow->rm_rate:'')?>">
            </div>

            <div class="col-md-6 form-group">
                <label for="gross_wt">Gross Weight (kg)</label>
                <input type="text" name="gross_wt" class="form-control floatOnly req" value="<?=$dataRow->gross_wt?>"> 
            </div>
        </div>
    </div>
</form>

<script>
    $(document).ready(function(){ 
        //Weight Calculation hide show fields   
        let shape = "<?= $dataRow->shape;?>";
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
</script>