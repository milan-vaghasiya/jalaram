<form>
	<div class="col-md-12">
        <div class="row">
			<input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>" />
			
			<div class="col-md-8 form-group">
				<label for='title' class="control-label">Designation Name</label>
				<input type="text" id="title" name="title" class="form-control req" value="<?=(!empty($dataRow->title))?$dataRow->title:""?>">				
			</div>

			<div class="col-md-4 form-group">
				<label for="payroll_wages">Payroll Wages</label>
				<input type="text" name="payroll_wages" id="payroll_wages" class="form-control floatOnly" value="<?=(!empty($dataRow->payroll_wages))?floatVal($dataRow->payroll_wages):""?>">
			</div>
			
            <div class="col-md-12 form-group">
                <label for='description' class="control-label">Remark</label>
                <textarea name="description" class="form-control" rows="1"><?=(!empty($dataRow->description))?$dataRow->description:""?></textarea>
            </div>
		</div>
	</div>	
</form>
            
