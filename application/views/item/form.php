<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <input type="hidden" name="item_type" value="<?=(!empty($dataRow->item_type))?$dataRow->item_type:$item_type; ?>" />

            <div class="col-md-4 form-group">
                <label for="item_code">Item Code</label>
                <input type="text" name="item_code" class="form-control" value="<?=(!empty($dataRow->item_code))?$dataRow->item_code:""; ?>" />
            </div>
            <?php 
                $itmtp = (!empty($dataRow->item_type))?$dataRow->item_type:$item_type;  
            ?>
            <div class="<?=($itmtp == 3)?'col-md-5':'col-md-8'?> form-group">
                <label for="item_name">Item Name</label>
				<?php if($itmtp == 3): ?>
                <div class="input-group">
                    <?php
                        $itmGroup = (!empty($dataRow->item_image))?explode('~@',$dataRow->item_image):"";
                    ?>
                    <input type="text" name="itmsize" id="insize" class="form-control" placeholder="Size" value="<?=(!empty($itmGroup[0]))?$itmGroup[0]:""?>" style="max-width:33%;" />
                    <input type="text" name="itmshape" id="insize" class="form-control noSpecialChar" placeholder="Shape" value="<?=(!empty($itmGroup[1]))?$itmGroup[1]:""?>" />
                    <input type="text" name="itmbartype" id="insize" class="form-control" placeholder="Bar Type" value="<?=(!empty($itmGroup[2]))?$itmGroup[2]:""?>" style="max-width:33%;" />
                </div>
                <div class="error item_name"></div>
				<?php else: ?>
					<input type="text" name="item_name" class="form-control req" value="<?=htmlentities((!empty($dataRow->item_name)) ? $dataRow->item_name : "")?>" />
				<?php endif; ?>                
            </div>
            <?php if($itmtp == 3): ?>
            <div class="col-md-3 form-group">
                <label for="itmmaterialtype">Material Grade</label>
                <select name="itmmaterialtype" id="itmmaterialtype" class="form-control single-select itmmaterialtype">
                    <option value="">Select Material Grade</option>
                    <?php
                        foreach($materialGrade as $row):
                            $selected = (!empty($dataRow->material_grade) && $dataRow->material_grade == $row->material_grade)?"selected":"";
                            echo '<option value="'.$row->material_grade.'" '.$selected.'>'.$row->material_grade.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <?php endif; ?>
            <div class="col-md-3 form-group">
                <label for="category_id">Category</label>
                <select name="category_id" id="category_id" class="form-control single-select req">
                    <option value="0">Select</option>
                    <?php
                        foreach ($categoryList as $row) :
                            $selected = (!empty($dataRow->category_id) && $dataRow->category_id == $row->id) ? "selected" : "";
                            echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->category_name . '</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="sub_group">Sub Group</label>
                <select name="sub_group" id="sub_group" class="form-control single-select">
                    <option value="0">Select</option>
                    <?php
                        foreach ($subGroup as $row) :
                            $selected = (!empty($dataRow->sub_group) && $dataRow->sub_group == $row->id) ? "selected" : "";
                            echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->sub_name . '</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="unit_id">Unit</label>
                <select name="unit_id" id="unit_id" class="form-control single-select req">
                    <option value="0">--</option>
                    <?php
                        foreach($unitData as $row):
                            $selected = (!empty($dataRow->unit_id) && $dataRow->unit_id == $row->id)?"selected":"";
                            echo '<option value="'.$row->id.'" '.$selected.'>['.$row->unit_name.'] '.$row->description.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="hsn_code">HSN Code</label>
                <input type="text" name="hsn_code" class="form-control" value="<?=(!empty($dataRow->hsn_code))?$dataRow->hsn_code:""?>" />
            </div>
            <!-- <div class="col-md-6 form-group">
                <label for="rm_type">Item Type</label>
                <select name="rm_type" id="rm_type" class="form-control">
                    <option value="0" <?=(!empty($dataRow->rm_type) && $dataRow->rm_type == 0)?"selected":""?>>Consumable</option>
                    <option value="1" <?=(!empty($dataRow->rm_type) && $dataRow->rm_type == 1)?"selected":""?>>Raw Material</option>
                </select>
            </div>
            <div class="col-md-6 form-group">
                <label for="opening_qty">Opening Qty</label>
                <input type="number" name="opening_qty" class="form-control floatOnly" min="0" value="<?=(!empty($dataRow->opening_qty))?$dataRow->opening_qty:""?>" />
            </div> -->
            <input type="hidden" name="opening_qty" class="form-control floatOnly" min="0" value="<?=(!empty($dataRow->opening_qty))?$dataRow->opening_qty:"0"?>" />
            <div class="col-md-3 form-group">
                <label for="gst_per">GST Per.</label>
                <select name="gst_per" id="gst_per" class="form-control single-select">
                    <?php
                        foreach($gstPercentage as $row):
                            $selected = (!empty($dataRow->gst_per) && floatval($dataRow->gst_per) == $row['rate'])?"selected":"";
                            echo '<option value="'.$row['rate'].'" '.$selected.'>'.$row['val'].'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="price">Price</label>
                <input type="text" name="price" id="price" min="0" class="form-control floatOnly" value="<?=(!empty($dataRow->price))?$dataRow->price:""?>" />
            </div>
			<div class="col-md-3 form-group">
                <label for="min_qty">Min. Qty.</label>
                <input type="text" name="min_qty" class="form-control floatOnly" value="<?= (!empty($dataRow->min_qty)) ? $dataRow->min_qty : "" ?>" />
            </div>
            <div class="col-md-3 form-group">
                <label for="wt_pcs">Weight Per Pcs.</label>
                <input type="text" name="wt_pcs" class="form-control floatOnly" value="<?=(!empty($dataRow->wt_pcs))?$dataRow->wt_pcs:""?>" />
            </div>
            
            <?php if($itmtp != 3): ?>
    			<div class="col-md-3 form-group">
                    <label for="material_grade">Grade</label>
                    <input type="text" name="material_grade" class="form-control" value="<?= (!empty($dataRow->material_grade)) ? $dataRow->material_grade : "" ?>" />
                </div>
            <?php else: ?>
                <input type="hidden" name="material_grade" id="material_grade" value="<?= (!empty($dataRow->material_grade)) ? $dataRow->material_grade : "" ?>">
            <?php endif; ?>

            
            <div class="col-md-3 form-group">
                <label for="stock_effect">Stock Effect</label>
                <select name="stock_effect" id="stock_effect" class="form-control">
                    <option value="1" <?=(!empty($dataRow->stock_effect) && $dataRow->stock_effect == 1)?"selected":""?>>Yes</option>
                    <option value="0" <?=(isset($dataRow->stock_effect) && $dataRow->stock_effect == 0)?"selected":""?>>No</option>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="batch_stock">Stock Type</label>
                <select name="batch_stock" id="batch_stock" class="form-control">
                    <option value="0" <?= (!empty($dataRow->batch_stock) && $dataRow->batch_stock == 0) ? "selected" : "" ?>>None</option>
                    <option value="1" <?= (!empty($dataRow->batch_stock) && $dataRow->batch_stock == 1) ? "selected" : "" ?>>Batchwise</option>
                    <option value="2" <?= (!empty($dataRow->batch_stock) && $dataRow->batch_stock == 2) ? "selected" : "" ?>>Serial No Stock</option>
                </select>
            </div>
            
            <?php if($itmtp == 3): ?>
                <div class="col-md-6 form-group">
                <label for="part_no">Std. Ref.</label>
                <input type="text" name="part_no" class="form-control" value="<?= (!empty($dataRow->part_no)) ? $dataRow->part_no : "" ?>" />
            </div>
            <div class="col-md-<?=($itmtp == 3)?'12':'3'?> form-group">
                <label for="cal_agency">Chemical</label>
                <input type="text" name="cal_agency" class="form-control" value="<?=(!empty($dataRow->cal_agency))?$dataRow->cal_agency:""?>" />
            </div>
                <div class="col-md-12 form-group">
                    <label for="other">Other</label>
                    <input type="text" name="other" class="form-control" value="<?= (!empty($dataRow->other)) ? $dataRow->other : "" ?>" />
                </div>
			<?php else: ?>
				<div class="col-md-3 form-group">
					<label for="tool_life">Life</label>
					<input type="text" name="tool_life" class="form-control" value="<?= (!empty($dataRow->tool_life)) ? $dataRow->tool_life : "" ?>" />
				</div>
				
				<div class="col-md-2 form-group">
					<label for="unit_of_life">Unit of Life</label>
					<select name="unit_of_life" id="unit_of_life" class="form-control single-select">
						<option value="0">--</option>
						<?php
							foreach($unitData as $row):
								$selected = (!empty($dataRow->unit_of_life) && $dataRow->unit_of_life == $row->id)?"selected":"";
								echo '<option value="'.$row->id.'" '.$selected.'>['.$row->unit_name.'] '.$row->description.'</option>';
							endforeach;
						?>
					</select>
				</div>
				<div class="col-md-2 form-group">
					<label for="no_of_corner">No. of Corner</label>
					<input type="text" name="no_of_corner" class="form-control" value="<?= (!empty($dataRow->no_of_corner)) ? $dataRow->no_of_corner : "" ?>" />
				</div>
				
				<div class="col-md-8 form-group">
					<div class="input-group">
						<label for="diameter" style="width:33%">Dia/SH. Dia (mm)</label>
						<label for="flute_length"  style="width:34%">Flute Length (mm)</label>
						<label for="length" style="width:33%">Overall Length (mm)</label>
					</div>
					<div class="input-group">
						<?php
						$diameter ='';$length ='';$flute_length ='';
						if(!empty($dataRow->size)){
							$size = explode("X",$dataRow->size);
							$diameter =!empty($size[0])?$size[0]:'';$flute_length =!empty($size[1])?$size[1]:'';$length =!empty($size[2])?$size[2]:'';
						}
						?>
						<input type="text" id="diameter" name="diameter" class="form-control floatOnly" value="<?=$diameter?>">
						<input type="text" id="flute_length" name="flute_length" class="form-control floatOnly"  value="<?=$flute_length?>">
						<input type="text" id="length" name="length" class="form-control floatOnly"  value="<?=$length?>">
					</div>
				</div>
            <?php endif; ?>
            
			<div class="col-md-12 form-group">
                <label for="description">Remark</label>
                <input type="text" name="description" class="form-control" value="<?= (!empty($dataRow->description)) ? $dataRow->description : "" ?>" />
            </div>
            
        </div>
    </div>
</form>

<script>
    $(document).ready(function(){
        $(document).on('change','.itmmaterialtype',function(){
            var material_grade = $(this).val();
            $("#material_grade").val(material_grade);
        });
    });
</script>