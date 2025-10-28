<form>
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-12">
                <div class="error gerenal_error"></div>
            </div>
            <input type="hidden" name="item_id" class="item_id" value="<?=$item_id?>" />
            <input type="hidden" id="process_id" value="0">
            <!--<input type="hidden" id="process_id" value="<?=(!empty($process))?$process[0]->id:""?>">
             <div class="col-md-3">
                <label for="process_id">Process</label>
                <select id="process_id" class="form-control single-select req">
                    <option value="">Select Process</option>                    
                    <?php
                        foreach($process as $row):
                            echo '<option value="'.$row->id.'">'.$row->process_name.'</option>';
                        endforeach;
                    ?>
                    <option value="0">Other</option>
                </select>
            </div> -->
            <div class="col-md-2 form-group">
                <label for="pfc_rev_no_kit">PFC Revision</label>
                <select name="pfc_rev_no_kit" id="pfc_rev_no_kit" class="form-control single-select req">
                    <option value="">Select PFC Revision</option>
                    <?php
                    foreach ($revData as $row) :
                        echo '<option value="'.$row->rev_no.'" data-material_grade="'.$row->material_grade.'">' . $row->rev_no . '</option>';
                    endforeach;
                    ?>
                </select>
                <div class="error pfc_rev_no_kit"></div>
            </div>  
            <div class="col-md-4">
                <label for="kit_item_id">Raw Material Item</label>
                <select id="kit_item_id" class="form-control single-select req">
                    <option value="">Select Item</option>
                    <?php
                        foreach($rawMaterial as $row):
                            echo '<option value="'.$row->id.'" data-unit_id="'.$row->unit_id.'">'.$row->item_name.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="kit_item_qty">Quantity</label>
                <input type="number" id="kit_item_qty" class="form-control floatOnly req" value="" min="0" />
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-outline-success waves-effect waves-light mt-30 save-form" onclick="AddKitRow();" ><i class="fa fa-plus"></i> Add Item</button>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="table-responsive">
            <table id="productKit" class="table table-bordered align-items-center">
                <thead class="thead-info">
                    <tr>
                        <th style="width:5%;">#</th>
                        <!-- <th>Process</th> -->
                        <th>PFC Revision</th>
                        <th>Item Name</th>
                        <th>Qty</th>
                        <th class="text-center" style="width:10%;">Action</th>
                    </tr>
                </thead>
                <tbody id="kitItems">
                    <?php
                        if(!empty($productKitData)):
                            $i=1;
                            foreach($productKitData as $row):
                                echo '<tr>
                                            <td>'.$i++.'</td>
                                            <!--<td>
                                                '.$row->process_name.'
                                                <input type="hidden" name="process_id[]" value="'.$row->process_id.'">
                                            </td>-->
                                            <td>
                                                '.$row->pfc_rev_no.'
                                                <input type="hidden" name="pfc_rev_no_kit[]" value="'.$row->pfc_rev_no.'">
                                            </td>
                                            <td>
                                                '.$row->item_name.'
                                                <input type="hidden" name="ref_item_id[]" class="processItem'.$row->process_id.'" value="'.$row->ref_item_id.'">
                                                <input type="hidden" name="id[]" value="'.$row->id.'">
                                                <input type="hidden" name="process_id[]" value="'.$row->process_id.'">
                                            </td>
                                            <td>
                                                '.$row->qty.'
                                                <input type="hidden" name="qty[]" value="'.$row->qty.'">
                                            </td>
                                            <td class="text-center">
                                                <button type="button" onclick="RemoveKit(this);" class="btn btn-outline-danger waves-effect waves-light permission-remove"><i class="ti-trash"></i></button>
                                            </td>
                                        </tr>';
                            endforeach;
                        endif;
                    ?>
                </tbody>
            </table>
        </div>
        </div>
    </div>
</form>
<script>
$(document).ready(function(){
    // $(document).on('change',"#pfc_rev_no_kit",function(){
    //     var pfc_rev_no = $(this).val();
	// 	var material_grade = $("#pfc_rev_no_kit").find(":selected").data('material_grade');
    //     var item_id = $("#item_id").val();
    //     if(pfc_rev_no != ''){
    //         $.ajax({
    //             url:base_url + controller + "/getPfcWiseItem",
    //             type:'post',
    //             data:{pfc_rev_no:pfc_rev_no,item_id:item_id,material_grade:material_grade},
    //             dataType:'json',
    //             success:function(data){ 
    //                 $("#kit_item_id").html("");
    //                 $("#kit_item_id").html(data.options);
    //                 $("#kit_item_id").comboSelect();
    //             }
    //         });
    //     }
    // });
});
</script>