<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <input type="hidden" name="is_npd" value="1" />
            <input type="hidden" name="job_category" value="2" />
			<div class="col-md-4 form-group">
				<label for="job_no">Job Card No.</label>
				<input type="text" id="job_no" class="form-control req" value="<?=(!empty($dataRow))?getPrefixNumber($dataRow->job_prefix,$dataRow->job_no):getPrefixNumber($jobPrefix,$jobNo)?>" readonly />
			</div>
			<div class="col-md-4 form-group">
				<label for="job_date">Job Card Date</label>
				<input type="date" id="job_date" name="job_date" class="form-control req" placeholder="dd-mm-yyyy" value="<?=(!empty($dataRow->job_date))?$dataRow->job_date:$maxDate?>" min="<?=$startYearDate?>" max="<?=$maxDate?>" />
			</div>
			<div class="col-md-4 form-group">
				<label for="item_id">Product Code</label>
				<select name="product_id" id="item_id" class="form-control single-select req" autocomplete="off">
					<option value="">Select Product</option>
					<?php 
					if(!empty($productData)){
						foreach($productData as $row):
							$selected = (!empty($dataRow->product_id) && $dataRow->product_id == $row->id)?"selected":"";
							echo '<option value="'.$row->id.'" '.$selected.'>'.$row->item_code.'</option>';
						endforeach;
					}
					?>
				</select>				
			</div>
			
			<div class="col-md-4 form-group">
				<label for="qty">Quatity</label>
				<input type="text" name="qty" id="qty" class="form-control numericOnly countWeight req" min="0" placeholder="Enter Qty." value="<?=(!empty($dataRow->qty))?$dataRow->qty:""?>" />				
			</div>

			<div class="col-md-8 form-group">
				<label for="remark">Remark</label>
				<input type="text" name="remark" id="remark" class="form-control" value="<?=(!empty($dataRow->remark))?$dataRow->remark:""?>" />
			</div>
        </div>
		<hr>
		<h3>Raw Material</h3>
		<div class="row">
			<div class="col-md-8 form-group">
				<label for="rm_item_id">Raw Material</label>
				<select name="rm_item_id" id="rm_item_id" class="form-control single-select req" autocomplete="off">
					<option value="">Select Product</option>
					<?php 
					if(!empty($rmList)){
						foreach($rmList as $row):
							$selected = (!empty($dataRow->rm_item_id) && $dataRow->rm_item_id == $row->id)?"selected":"";
							echo '<option value="'.$row->id.'" '.$selected.'>['.$row->item_code.'] '.$row->item_name.'</option>';
						endforeach;
					}
					?>
				</select>
			</div>
			<div class="col-md-4 form-group">
				<label for="rm_req_qty">Request Qty</label>
				<input type="text" name="rm_req_qty" id="rm_req_qty" class="form-control floatOnly" value="<?=(!empty($dataRow->rm_req_qty))?$dataRow->rm_req_qty:""?>">
			</div>
		</div>
    </div>
</form>
