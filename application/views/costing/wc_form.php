<style>
    .result { margin-top: 15px; font-weight: bold; text-align: center; color: #006600; }
</style>

<form>
    <div class="row">
        <input type="hidden" name="id" value="<?=$id?>">
        
        <div class="col-md-6 form-group">
            <label for="material">Material</label>
            <select name="material" id="material" class="form-control single-select req">
                <option value="0">Select Material</option>
                <option value="7.85">Steel (7.85)</option>
                <option value="7.90">Stainless Steel (7.90)</option>
                <option value="2.70">Aluminium (2.70)</option>
                <option value="8.73">Brass (8.73)</option>
                <option value="8.96">Copper (8.96)</option>
                <option value="7.14">Zinc (7.14)</option>
                <option value="11.34">Lead (11.34)</option>
                <option value="4.43">Titanium (4.43)</option>
                <option value="7.20">Cast Iron (7.20)</option>
                <option value="8.80">Bronze (8.80)</option>
                <option value="custom">Custom</option>
            </select>
        </div>
        
        <div class="col-md-6 form-group" id="customDensity">
            <label for="density">Custom Density (g/cm³)</label>
            <input type="text" id="density" class="form-control floatOnly req" value="" placeholder="Density">
        </div>
        
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
        
        <!-- Round Bar -->
        <div class="col-md-6 form-group dim round_dia">
            <label for="diameter">Diameter (mm)</label>
            <input type="text" id="diameter" class="form-control floatOnly req" value="" placeholder="Diameter"> 
        </div>
        
        <!-- Square / Rectangle -->
        <div class="col-md-6 form-group dim square rectangle sheet" style="display:none;">
            <label for="width">Width (mm)</label>
            <input type="text" id="width" class="form-control floatOnly req" value="" placeholder="Width"> 
        </div>
        <div class="col-md-6 form-group dim rectangle sheet" style="display:none;">
            <label for="height">Height / Thickness (mm)</label>
            <input type="text" id="height" class="form-control floatOnly req" value="" placeholder="Height / Thickness">
        </div>
        
        <!-- Pipe -->
        <div class="col-md-6 form-group dim pipe" style="display:none;">
            <label for="outer_dia">Outer Diameter (mm)</label>
            <input type="text" id="outer_dia" class="form-control floatOnly req" value="" placeholder="Outer Diameter"> 
        </div>
        <div class="col-md-6 form-group dim pipe" style="display:none;">
            <label for="inner_dia">Inner Diameter (mm)</label>
            <input type="text" id="inner_dia" class="form-control floatOnly req" value="" placeholder="Inner Diameter">
        </div>
        
        <div class="col-md-6 form-group dim hex" style="display:none;">
            <label for="flat">Flat to Flat (mm)</label>
            <input type="text" id="flat" class="form-control floatOnly req" value="" placeholder="Flat to Flat">
        </div>
        
        <div class="col-md-6 form-group">
            <label for="length">Length (mm)</label>
            <input type="text" id="length" class="form-control floatOnly req" value="" placeholder="Length">
        </div>
        
        <div class="col-md-12 form-group">
            <button type="button" id="calc" class="btn btn-primary btn-block" >Calculate Weight</button>
        </div>
        
        <div class="col-md-12 form-group">
            <div class="result" id="result"></div>
        </div>
        
    </div>
</form>

<script>
$(document).ready(function(){
    
    $('#shape').change(function(){
        let shape = $(this).val();
        $('.dim').hide();
        $('.' + shape).show();
    });
    
    $('#material').change(function(){
        let density = parseFloat($(this).val()) || 0;
        $('#density').val(density);
    });
    
    $('#calc').click(function(){
        let shape = $('#shape').val();
        let length = parseFloat($('#length').val()) / 1000; // mm to m
        let densityVal = $('#density').val();
        let density = parseFloat(densityVal) * 1000; // g/cm³ to kg/m³
        let volume = 0;
        
        if(shape === 'round_dia'){
            let d = parseFloat($('#diameter').val()) / 1000;
            volume = Math.PI * Math.pow(d/2, 2) * length;
        }
        else if(shape === 'square'){
            let a = parseFloat($('#width').val()) / 1000;
            volume = Math.pow(a, 2) * length;
        }
        else if(shape === 'rectangle'){
            let w = parseFloat($('#width').val()) / 1000;
            let h = parseFloat($('#height').val()) / 1000;
            volume = w * h * length;
        }
        else if(shape === 'pipe'){
            let od = parseFloat($('#outer_dia').val()) / 1000;
            let id = parseFloat($('#inner_dia').val()) / 1000;
            volume = Math.PI * (Math.pow(od/2,2) - Math.pow(id/2,2)) * length;
        }
        else if(shape === 'hex'){
            let f = parseFloat($('#flat').val()) / 1000;
            volume = 0.866 * Math.pow(f,2) * length;
        }
        else if(shape === 'sheet'){
            let w = parseFloat($('#width').val()) / 1000;
            let t = parseFloat($('#height').val()) / 1000;
            volume = w * t * length;
        }
        
        let totalWeight = volume * density; // kg
        let weightPerMeter = (volume / length) * density; // kg/m
        
        if(!isNaN(totalWeight) && totalWeight > 0){
            $('#result').html('Weight per meter: <b>' + weightPerMeter.toFixed(3) + ' kg/m</b><br>' + 'Total Weight: <b>' + totalWeight.toFixed(3) + ' kg</b>');
        } else {
            $('#result').text('Invalid inputs.');
        }
  });
});
</script>