<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>" />
            <input type="hidden" name="status" value="<?=(!empty($dataRow->status))?$dataRow->status:$status?>" />
            <input type="hidden" name="unit_id" value="25" />
            <input type="hidden" name="cat_code" id="cat_code" value="<?= (!empty($dataRow->cat_code)) ? $dataRow->cat_code : ""; ?>" />
   
            <div class="<?=(!empty($dataRow->id)) ? 'col-md-6' : 'col-md-4' ?>  form-group">
                <label for="category_id">Category</label>
                <select name="category_id" id="category_id" class="form-control single-select req">
                    <option value="">Select Category</option>
                    <?php
                        foreach ($categoryList as $row) :
                            $selected = (!empty($dataRow->category_id) && $dataRow->category_id == $row->id) ? "selected" : "";
                            echo '<option value="'. $row->id .'" data-cat_name="'.$row->category_name.'" data-cat_code="'.$row->category_code.'" '.$selected.'>'.((!empty($row->category_code))?'['.$row->category_code.'] '.$row->category_name:$row->category_name).'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
			<?php if(empty($dataRow->id)): ?>
            <div class="col-md-4 form-group">
                <label for="item_code">Inst. Code</label>
                <input type="text" name="item_code" id="item_code" class="form-control" value="<?=(!empty($dataRow->item_code))?$dataRow->item_code:""?>" />
            </div>
            <?php else: ?>
                <input type="hidden" name="item_code" id="item_code" value="<?=(!empty($dataRow->item_code))?$dataRow->item_code:""?>" />
            <?php endif;?>
            <div class="<?=(!empty($dataRow->id)) ? 'col-md-6' : 'col-md-4' ?>  form-group">
                <label for="item_name">Description</label>
                <input type="text" name="item_name" id="item_name" class="form-control" value="<?=(!empty($dataRow->item_name))?$dataRow->item_name:""?>" />
            </div> 
            <div class="col-md-4 form-group">
                <label for="make_brand">Make</label>
                <input type="text" name="make_brand" class="form-control" value="<?=(!empty($dataRow->make_brand))?$dataRow->make_brand:""?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="gauge_type">Inst. Type</label>
                <select name="gauge_type" id="gauge_type" class="form-control single-select req">
                    <option value="3" <?=(!empty($dataRow->gauge_type) && $dataRow->gauge_type == 3)?"selected":""?>>Range</option>
                    <option value="4" <?=(!empty($dataRow->gauge_type) && $dataRow->gauge_type == 4)?"selected":""?>>Other</option>
                </select>
            </div>

            <div class="col-md-4 form-group">
                <label for="size">Range (mm)</label>
                <div class="input-group range">
                    <?php
                        $minRange = '';$maxRange = '';
                        if(!empty($dataRow->size) && (!empty($dataRow->gauge_type) && $dataRow->gauge_type == 3)){
                            $range = explode("-",$dataRow->size);
                            $minRange = $range[0];$maxRange = $range[1];
                        }
                    ?>
                    <input type="text" name="min_range" id="min_range" value="<?= $minRange?>" class="form-control floatOnly size" placeholder="Min"   />
                    <input type="text" name="max_range" id="max_range" class="form-control floatOnly size" value="<?= $maxRange?>"  placeholder="Max" />
                </div>
                
                <input type="text" name="size" id="size" class="form-control other size" value="<?=(!empty($dataRow->size))?$dataRow->size:""?>" style="display:none;" />
            </div>
            
            <div class="col-md-4 form-group">
                <label for="least_count">Least Count</label>
                <input type="text" name="least_count" id="least_count" class="form-control" value="<?=(!empty($dataRow->least_count))?$dataRow->least_count:""?>" />
            </div>

           <div class="col-md-4 form-group">
                <label for="location_id">Location</label>
                <select name="location_id" id="location_id" class="form-control single-select">
					<option value="">Select Location</option>
                    <?php
						foreach($locationList as $lData):
							echo '<optgroup label="'.$lData['store_name'].'">';
							foreach($lData['location'] as $row):
								$selected = (!empty($dataRow->location_id) && $dataRow->location_id == $row->id) ? "selected" : "";
								echo '<option value="'.$row->id.'" data-location_name="[' .$lData['store_name']. '] '.$row->location.'" '.$selected.'>'.$row->location.' </option>';
							endforeach;
							echo '</optgroup>';
						endforeach;
                    ?>
                </select>
                <input type="hidden" name="location_name" id="location_name" class="form-control req" value="<?=(!empty($dataRow->location_name))?$dataRow->location_name:""?>" />
            </div>

            <div class="col-md-4 form-group">
                <label for="permissible_error">Permissible Error</label>
                <input type="text" name="permissible_error" class="form-control" value="<?=(!empty($dataRow->permissible_error))?$dataRow->permissible_error:""?>" />
            </div>
            
            <div class="col-md-4 form-group">
                <label for="mfg_sr">Serial No</label>
                <input type="text" name="mfg_sr" class="form-control" value="<?=(!empty($dataRow->mfg_sr))?$dataRow->mfg_sr:""?>" />
            </div>

            <div class="col-md-4 form-group">
                <label for="cal_required">Cal. Required</label>
                <select name="cal_required" id="cal_required" class="form-control single-select req" >
                    <option value="YES" <?=(!empty($dataRow->cal_required) && $dataRow->cal_required == "YES")?"selected":""?>>YES</option>
                    <option value="NO" <?=(!empty($dataRow->cal_required) && $dataRow->cal_required == "NO")?"selected":""?>>NO</option>
                </select>
            </div>

            <div class="col-md-4 form-group">
                <div class="input-group">
                    <label for="cal_freq" style="width: 50%;">Freq. <small>(Months)</small></label>
                    <label for="cal_reminder">Reminder <small>(Days)</small></label>
                </div>
                <div class="input-group">
                    <input type="text" name="cal_freq" class="form-control floatOnly"  value="<?=(!empty($dataRow->cal_freq))?$dataRow->cal_freq:""?>" <?=(!empty($dataRow->cal_freq))?"readOnly":""?> />
                    <input type="text" name="cal_reminder" class="form-control floatOnly" value="<?=(!empty($dataRow->cal_reminder))?$dataRow->cal_reminder:""?>" />
                </div>
            </div>
            
            <div class="col-md-12 form-group">
                <label for="description">Remark</label>
                <textarea name="description" id="description" class="form-control"><?=(!empty($dataRow->description))?$dataRow->description:""?></textarea>
            </div>
        </div>
    </div>
</form>
<script>
    $(document).ready(function(){
        setTimeout(function(){ $('#gauge_type').trigger('change'); }, 5);
        
		$(document).on('change',"#location_id",function(){
			var location_name = $(this).find(":selected").data('location_name');
			$('#location_name').val(location_name);
		});
        
        $("#category_id").on('change',function(){
            var cat_code = $(this).find(":selected").data('cat_code');
            var cat_name = $(this).find(":selected").data('cat_name');
            $('#cat_code').val(cat_code);
            $('#cat_name').val(cat_name);
            
			var id = $('#id').val();
			if(id == '' || id == 0){
				var category_id = $(this).val();
				$.ajax({
					url:base_url + controller + "/getQCInstrumentCode",
					type:'post',
					data:{category_id:category_id},
					dataType:'json',
					success:function(data){
						$("#item_code").val(data.item_code);
						$("#serial_no").val(data.serial_no);
					}
				});
			}
        });
        
        $("#gauge_type").on('change',function(){
            var inst_type = $(this).val();
            if(inst_type == 3){
                $('.range').show();
                $('.other').hide();
            }else{
                $('.range').hide();
                $('.other').show();
            }
        });

        $(".generateCode").on('click',function(){
            var cat_code = $("#category_id").find(":selected").data('cat_code');
            var category_id = $("#category_id").val();
            var min_range = $("#min_range").val();
            var max_range = $("#max_range").val();
            var least_count = $("#least_count").val();
            var valid = 1;
            if(cat_code == ''){
                $(".category_id").html("Category code is required");
                valid = 0;
            }
            if(max_range == ''){
                $(".max_range").html("Enter Range");
                valid = 0;
            }
            if(min_range == ''){
                $(".min_range").html("Enter Range");
                valid = 0;
            }
            if(least_count == ''){
                $(".least_count").html("Enter Least Count");
                valid = 0;
            }

            if(valid){
                $.ajax({
                    url:base_url + controller + "/getInstrumentCode",
                    type:'post',
                    data:{category_id:category_id,cat_code:cat_code,max_range:max_range,min_range:min_range,least_count:least_count},
                    dataType:'json',
                    success:function(data){
                        $("#item_code").val(data.item_code);
                        $("#store_id").val(data.part_no);
                        $("#cat_code").val(cat_code);
                    }
                });
            }
        });
        
        $('.size').typeahead({
    		source: function(query, result)
    		{
    		    var category_id = $("#category_id :selected").val();
    			$.ajax({
    				url:base_url + 'qcPurchaseRequest/sizeSearch',
    				data:{query:query,category_id:category_id},
    				type: "POST",
    				dataType:"json",
    				success:function(data){result($.map(data, function(size){return size;}));}
    			});
    		}
        });
    });
</script>