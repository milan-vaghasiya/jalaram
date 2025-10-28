<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>" />

            <div class="col-md-3 form-group">
                <label for="issue_date">Issue Date</label>
                <input type="date" name="issue_date" id="issue_date" class="form-control" min="<?=(!empty($dataRow))?$dataRow->issue_date:$startYearDate?>" max="<?=$maxDate?>" value="<?=(!empty($dataRow))?$dataRow->issue_date:$maxDate?>">
            </div> 

            <div class="col-md-3 form-group">
                <label for="material_type">Material Type</label>
                <input type="text" class="form-control" value="Consumable" readonly />
                <input type="hidden" name="material_type" id="material_type" value="2" />             
            </div>

            <div class="col-md-3 form-group">
                <label for="collected_by">Material Collected By</label>
                <select name="collected_by" id="collected_by" class="form-control single-select req">
                    <option value="">Select Collected By</option>
                    <?php
                        foreach ($empData as $row) :
                            $selected = ((!empty($dataRow->collected_by) && $dataRow->collected_by == $row->id) ? "selected" : "");
                            echo '<option value="' . $row->id . '" '.$selected.'>['. $row->emp_code.'] ' . $row->emp_name . '</option>';
                        endforeach;
                    ?>
                </select>
            </div>

            <div class="col-md-3 form-group">
                <label for="party_id">Vendor</label>
                <select name="party_id" id="party_id" class="form-control single-select req">
                    <option value="">Select Party Name</option>
                    <option value="0" <?= (!empty($dataRow) && $dataRow->party_id == 0)?"selected":"" ?> data-party_name="In House">In House</option>
                    <?php
                        foreach($partyData as $row):
                            $selected = "";
                            if(!empty($dataRow->party_id) && $dataRow->party_id == $row->id){$selected = "selected";}
                            echo '<option value="'.$row->id.'" '.$selected.' data-party_name="'.$row->party_name.'" >'.$row->party_name.'</option>';
                        endforeach;
                    ?>
                </select>
                <input type="hidden" name="party_name" id="party_name" value="<?=(!empty($dataRow->party_name))?$dataRow->party_name:""?>" />
            </div>	

            <div class="col-md-3 form-group">
                <label for="">Job Card No.</label>
                <select name="job_card_id" id="job_card_id" class="form-control single-select">
                    <option value="">Select Job Card No.</option>
                    <option value="-1">General Issue</option>
                    <?php
                        foreach($jobCardData as $row):
                            $selected = "";
                            if(!empty($dataRow->job_card_id) && $dataRow->job_card_id == $row->id){$selected = "selected";}
                            echo '<option value="'.$row->id.'" '.$selected.'>['.$row->item_code.'] '.getPrefixNumber($row->job_prefix,$row->job_no).'</option>';
                         
                        endforeach;
                    ?>
                </select>                
            </div>    
            
            <div class="col-md-3 form-group">
                <label for="dept_id">Department</label>
                <select name="dept_id" id="dept_id" class="form-control single-select">
                    <option value="">Select Department</option>
                    <?php
                        foreach($deptData as $row):
                            $selected = ((!empty($dataRow->location) && $dataRow->location == $row->id) ? "selected" : "");
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->name.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>   

            <div class="col-md-3 form-group">
                <label for="machine_id">Machine</label>
                <select name="machine_id" id="machine_id" class="form-control single-select">
                    <option value="">Select Machine</option>
                    <?php
                        /*foreach ($machineList as $row) :
                            $selected = ((!empty($dataRow->machine_id) && $dataRow->machine_id == $row->id) ? "selected" : "");
                            echo '<option value="' . $row->id . '" '.$selected.'>['. $row->item_code.'] ' . $row->item_name . '</option>';
                        endforeach;*/
                    ?>
                </select>
            </div>            

            <div class="col-md-3 form-group">
                <label for="item_id">Issue Item Name</label>
                <select name="item_id" id="item_id" class="form-control single-select1 model-select2 req">
                    <option value="" selected>Select Item Name</option>
                    <?php                    
                        foreach($itemData as $row):
                            $selected = "";
                            if(!empty($dataRow->item_id) && $dataRow->item_id == $row->id){$selected = "selected";}
                            echo '<option value="'.$row->id.'" '.$selected.' data-item_name="'.$row->item_name.'" >'.$row->item_name.'</option>';
                        endforeach;
                    ?>
                </select>     
                <input type="hidden" name="item_name" id="item_name" value="<?=(!empty($dataRow->item_name))?$dataRow->item_name:""?>" />           
            </div>

            <div class="col-md-2 form-group">
                <label for="is_returnable">Is Returnable</label>
                <select name="is_returnable" id="is_returnable" class="form-control single-select">
                    <option value="">Select Option</option>
                    <option value="0">No</option>
                    <option value="1">Yes</option>
                </select>
            </div>

            <div class="col-md-10 form-group">
                <label for="remark">Remark</label>
                <input type="text" name="remark" id="remark" class="form-control" value="<?=(!empty($dataRow->remark))?$dataRow->remark:""?>">
            </div>
           
            <div class="batch_qty error"></div>
            <div class="col-md-12 form-group">
                <div class="error general_batch_no"></div>
                <div class="table-responsive ">
                    <table id="issueItems" class="table table-bordered">
                        <thead class="thead-info">
                            <tr>
                                <th>#</th>
                                <th>Location</th>
                                <th>Batch</th>
                                <th>Current Stock</th>
                                <th>Qty.</th>
                            </tr>
                        </thead>
                        <tbody id="tempItem">
                            <?php
                            if (!empty($batchWiseStock)) {
                                echo $batchWiseStock['batchData'];
                            } else {
                            ?>
                                <tr id="noData">
                                    <td class="text-center" colspan="5">No Data Found</td>
                                </tr>
                            <?php
                            }
                            ?>

                        </tbody>
                        <tfoot>
                            <tr>
                                <th class="text-right" colspan="4">
                                    Total Qty
                                </th>
                                <th id="totalQty">0.000</th>
                            </tr>
                        </tfoot>
                    </table>

                </div>
            </div>

        </div>
    </div>
</form>
<script>
$(document).ready(function(){
    $('#item_id').select2({
		dropdownParent: $('#item_id').parent()
	});
	$(document).on('change','#dept_id',function(){
		var id = $(this).val();
		if(id)
		{
			$.ajax({
				url: base_url + controller + '/getMachines',
				type:'post',
				data:{dept_id:id},
				dataType:'json',
				success:function(data){
					$("#machine_id").html(data.machineOpt);
					$("#machine_id").comboSelect();
				}
			});
		}
	});
});
</script>