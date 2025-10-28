<form>
	<div class="col-md-12">
        <div class="row">
			<input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>" />
			
			<div class="col-md-12 form-group">
				<label for='name' class="control-label">Department Name</label>
				<input type="text" id="name" name="name" class="form-control req" value="<?=(!empty($dataRow->name))?$dataRow->name:""?>">
			</div>
			<div class="col-md-12 form-group">
				<label for="category">Category</label>
				<select name="category" id="category" class="form-control single-select req">
                    <?php
                        foreach($categoryData as  $key => $value):
							$selected = (!empty($dataRow->category) && $key == $dataRow->category)?"selected":"";
                            echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
                        endforeach;
                    ?>
                </select>
			</div>
			<div class="col-md-12 form-group">
				<label for="ecn_stock">ECN Stock</label>
                <select name="ecn_stock" id="ecn_stock" class="form-control single-select">
                    <option value="">Select ECN Stock</option>
                    <option value="1" <?=((!empty($dataRow->ecn_stock) && $dataRow->ecn_stock == 1) ? "selected" : "")?>>Existing Stock Qty</option>
                    <option value="2" <?=((!empty($dataRow->ecn_stock) && $dataRow->ecn_stock == 2) ? "selected" : "")?>>WH & Intransit Qty</option>
                    <option value="3" <?=((!empty($dataRow->ecn_stock) && $dataRow->ecn_stock == 3) ? "selected" : "")?>>Inprocess Stock Qty</option>
                    <option value="4" <?=((!empty($dataRow->ecn_stock) && $dataRow->ecn_stock == 4) ? "selected" : "")?>>RM Qty</option>
                </select>
			</div>
		</div>
	</div>	
</form>
            
