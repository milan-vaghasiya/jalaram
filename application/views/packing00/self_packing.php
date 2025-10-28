<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>" />
            <input type="hidden" name="dispatch_date" id="dispatch_date" value="<?=(!empty($dataRow->dispatch_date))?$dataRow->dispatch_date:""?>" />
            <!-- <input type="hidden" name="item_id" id="item_id" value="<?=(!empty($dataRow->item_id))?$dataRow->item_id:""?>" /> -->
            <input type="hidden" name="qty" id="qty" value="<?=(!empty($dataRow->qty))?$dataRow->qty:""?>" />
            <input type="hidden" name="pending_qty" id="pending_qty" value="<?=(!empty($pending_qty))?$pending_qty:""?>" />
            <input type="hidden" name="oldpqty" id="oldpqty" value="<?=(!empty($dataRow->packing_qty))?$dataRow->packing_qty:"0"?>" />
            <input type="hidden" name="trans_child_id" id="trans_child_id" value="<?=(!empty($dataRow->trans_child_id))?$dataRow->trans_child_id:"0"?>">

            <div class="col-md-4 form-group">
                <label for="packing_date">Packing Date</label>
                <input type="date" name="packing_date" id="packing_date" class="form-control req" value="<?=(!empty($dataRow->packing_date))?$dataRow->packing_date:$maxDate?>" min="<?=$startYearDate?>" max="<?=$maxDate?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="packing_type">Packing Type</label>
                <select name="packing_type" id="packing_type" class="form-control single-select">
                    <option value="Regular" <?php (!empty($dataRow->packing_type) && $dataRow->packing_type == "Regular")?"selected":"" ?>>Regular</option>
                    <option value="Export" <?php (!empty($dataRow->packing_type) && $dataRow->packing_type == "Export")?"selected":"" ?>>Export</option>
                </select>
            </div>
            <div class="col-md-4 form-group">
                <label for="trans_no">Package No.</label>
                <input type="text" name="trans_no" id="trans_no" class="form-control floatOnly req" value="<?=(!empty($dataRow->trans_no))?$dataRow->trans_no:""?>" />
            </div>
            <div class="col-md-4 form-group expField" style="display:none;">
                <label for="status">Packing Status</label>
                <select name="status" id="status" class="form-control req">
                    <option value="0" <?=(!empty($dataRow->id) && $dataRow->status == 0)?"selected":""?>>Tentative</option>
                    <option value="1" <?=(!empty($dataRow->status) && $dataRow->status == 1)?"selected":""?>>Final</option>
                </select>
            </div>
            <div class="col-md-8 form-group expField" style="display:none;">
                <label for="trans_main_id">Sales Order No.</label>
                <select name="trans_main_id" id="trans_main_id" class="form-control single-select req">
                    <option value="">Select Sales Order No.</option>
                </select>
            </div>
            <div class="col-md-4 form-group">
                <label for="wooden_wt">Wooden Box Weight</label>
                <input type="text" name="wooden_wt" id="wooden_wt" class="form-control floatOnly req" value="<?=(!empty($dataRow->wooden_wt))?$dataRow->wooden_wt:""?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="packing_wt">Packing Weight</label>
                <input type="text" name="packing_wt" id="packing_wt" class="form-control floatOnly req" value="<?=(!empty($dataRow->packing_wt))?$dataRow->packing_wt:""?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="wt_pcs">Weight Per/Pcs.</label>
                <input type="text" name="wt_pcs" id="wt_pcs" class="form-control floatOnly req" value="<?=(!empty($dataRow->wt_pcs))?$dataRow->wt_pcs:""?>" />
            </div>
            <div class="col-md-12 form-group itemDiv">
                <label>Product </label>
                <select name="item_id" id="item_id" class="form-control single-select itemList req">
                    <option value="" data-trans_child_id="0">Selec Product</option>
                    <?php
                        if(!empty($itemData)):
                            foreach($itemData as $row):
                                echo '<option value="'.$row->id.'" data-trans_child_id="0">'.$row->item_code.'</option>';
                            endforeach;
                        endif
                    ?>
                </select>
            </div>
            <div class="col-md-3 form-group tentativeQtyDiv" style="display:none;">
                <label for="tentative_qty">Packing Qty</label>
                <input type="number" name="tantetive_qty" id="tantetive_qty" class="form-control numericOnly req" value="<?=(!empty($dataRow->qty))?$dataRow->qty:""?>">
            </div>

            <div class="col-md-12 finalPacking">
                <div class="error location_id"></div>
                <div class="error qty"></div>
                <div class="error packing_qty"></div>
                <div class="table-responsive">
                    <table id='reportTable' class="table table-bordered">
                        <thead class="thead-info" id="theadData">
                            <tr>
                                <th>#</th>
                                <th>Location</th>	
                                <th>Batch</th>
                                <th>Current Stock</th>
                                <th>Qty.</th>
                            </tr>
                        </thead>
                        <tbody id="batchData">
                            
                        </tbody>
                        <tfoot>
                            <tr>
                                <th class="text-right" colspan="4">
                                    Total Qty
                                    <input type="hidden" name="packing_qty" id="packing_qty" value="<?=(!empty($dataRow->packing_qty))?$dataRow->packing_qty:"0"?>">
                                </th>
                                <th id="totalQty">0.000</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>				
            </div>
        </div>
    </div>
    <hr>
    <div class="col-md-12 row">
        <div class="col-md-12">
            <h4>Packing Material Details : </h4>
            <div class="error box_id_error"></div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-6 form-group">
                <label for="box_id">Packing Material</label>
                <select id="box_id" class="form-control single-select req">
                    <option value="">Packing Material</option>
                    <?php
                        foreach ($bagData as $row) :
                                echo '<option value="' . $row->id . '">' . $row->item_name . '</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-2 form-group">
                <label for="noof_box">No. Of Box</label>
                <input type="text" id="noof_box" class="form-control floatOnly req" value="" />
            </div>
            <div class="col-md-2 form-group">
                <label for="capacity">Capacity</label>
                <input type="text" id="capacity" class="form-control floatOnly req" value="" />
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-outline-success waves-effect waves-light float-right mt-30 save-form" onclick="AddRow();" ><i class="fa fa-plus"></i> Add</button>
            </div>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive ">
                <table id="packingBom" class="table table-striped table-borderless">
                    <thead class="thead-info">
                        <tr>
                            <th style="width:5%;">#</th>
                            <th>Packing Material</th>
                            <th>No. Of Box</th>
                            <th>Capacity</th>
                            <th class="text-center" style="width:10%;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="bomData">
                        <?php
                        if(!empty($packingData)): $i=1;
                            foreach($packingData as $row):
                        ?>
                        <tr>
                            <td style="width:5%;">
                                <?=$i?>
                            </td>
                            <td>
                                <?=$row->box_name?>
                                <input type="hidden" name="box_id[]" value="<?=$row->box_id?>">  
                            </td>
                            <td>
                                <?=$row->noof_box?>
                                <input type="hidden" name="noof_box[]" value="<?=$row->noof_box?>">
                                <div class="error noof_box<?=$i++?>"></div>
                                <input type="hidden" id="pqty" value="">
                            </td>
                            <td>
                                <?=$row->capacity?>
                                <input type="hidden" name="capacity[]" value="<?=$row->capacity?>">
                                <div class="error capacity<?=$i++?>"></div>
                            </td>
                            <td class="text-center" style="width:10%;">
                                <button type="button" onclick="Remove(this);" class="btn btn-sm btn-outline-danger m-l-2"><i class="ti-trash"></i></button>
                            </td>
                        </tr>
                        <?php endforeach; else: ?>
                            <tr id="noData">
                                <td colspan="5" class="text-center">No data available in table</td>
                            </tr>
                        <?php endif;?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>
<script src="<?php echo base_url();?>assets/js/custom/packingbom-form.js?v=<?=time()?>"></script>
<script>
$(document).ready(function(){
    $("#status").val(1);
});    
</script>