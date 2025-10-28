<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>" />
            <input type="hidden" name="req_no" id="req_no" value="<?=(!empty($dataRow->req_no))?$dataRow->req_no:$nextReqNo?>" />
            <input type="hidden" name="process_id" id="process_id" value="" />
            
            <div class="col-md-6 form-group">
                <label for="req_date">Request Date</label>
                <input type="date" name="req_date" id="req_date" class="form-control req" value="<?=(!empty($dataRow->id))?$dataRow->req_date:$maxDate?>" min="<?=$startYearDate?>" max="<?=$maxDate?>" >
            </div>
            <div class="col-md-6 form-group">
                <label for="material_type">Material Type</label>
                <input type="text" class="form-control" value="Raw Material" readonly />
                <input type="hidden" name="material_type" id="material_type" value="3" />         
            </div>
            <div class="col-md-12 form-group">
                <label for="">Product</label>
                <select name="fg_item_id" id="fg_item_id" class="form-control single-select">
                    <option value="">Select FinishGood</option>
                    <?php
                        foreach($fgList as $row):
                            $selected = (!empty($dataRow->id) && $dataRow->fg_item_id == $row->id)?"selected":"";                           
                            echo '<option value="'.$row->id.'" '.$selected.'>'. $row->item_code .'</option>';
                        endforeach;
                    ?> 
                </select>
            </div>
            
            <div class="col-md-12">
                <div class="table-responsive">
                    <table id="productKit" class="table excelTable">
                        <thead class="thead-info">
                            <tr>
                                <th>Item Name</th>
                                <th>Qty</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white" id="kitItems">
                            <?php
                                if(!empty($productKitData)):
                                    $i=1;
                                    foreach($productKitData as $row):
                                        echo '<tr>
                                                <td>'.$row->item_name.'</td>
                                                <td>'.$row->qty.' ('.$row->unit_name.')</td>
                                            </tr>';
                                    endforeach;
                                else:
                                    echo '<tr><td colspan="3" style="text-align:center;">No Data Found</td></tr>';
                                endif;
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-12 form-group">
                <label for="req_item_id">Request Item Name</label>
                <select name="req_item_id" id="req_item_id" class="form-control single-select req">
                    <option value="">Select Item Name</option>
                    <?php      
                        foreach($productKitData as $row):
                            $itmStock = $this->store->getItemStock($row->ref_item_id); $stock_qty = 0;
                            if(!empty($itmStock->qty)){$stock_qty = $itmStock->qty;}
                			echo '<option value="'.$row->ref_item_id.'" data-stock="'.$stock_qty.' '.$row->unit_name.'">'.$row->item_name.'</option>'; 
                        endforeach;
                    ?>
                </select>      
            </div>
            <div class="col-md-4 form-group">
                <label for="stock_qty">Stock Qty.</label>
                <input type="text" id="stock_qty" placeholder="Item Stock Qty." class="form-control" value="" readonly />
            </div>
            <div class="col-md-4 form-group">
                <label for="req_qty">Request Qty.</label>
                <input type="text" id="req_qty" class="form-control floatOnly req" min="0" value="">
            </div>
            <div class="col-md-4 form-group">
                <label for="dispatch_date">Delivery Date</label>
                <input type="date" id="dispatch_date" class="form-control req" value="<?=date("Y-m-d")?>" >  
            </div>
            <div class="col-md-10 form-group">
                <label for="remark">Remark</label>
                <input type="text" name="remark" id="remark" class="form-control" value="">
            </div>
            <div class="col-md-2 form-group">
                <label>&nbsp;</label>
                <button type="button" class="btn waves-effect waves-light btn-primary btn-block addRow"><i class="fas fa-plus"></i> Add</button>
            </div>

            <div class="col-md-12 form-group">
                <div class="error general_item"></div>
                <div class="table-responsive ">
                    <table id="reqItems" class="table table-striped table-borderless">
                        <thead class="thead-info">
                            <tr>
                                <th style="width:5%;">#</th>
                                <th>Req. Item</th>
                                <th>Delivery Date</th>
                                <th>Req. Qty.</th>
                                <th style="width:10%;">Action</th>
                            </tr>
                        </thead>
                      
                        <tbody id="tempItem">
                        <?php
                            if (!empty($editData)) :
                                foreach ($editData as $row) :
                            ?>
                                <tr>
                                    <td style="width:5%;">
                                        <?= $i++ ?>
                                    </td>
                                    <td>
                                        <?=$row->req_item_name?>
                                        <input type="hidden" name="req_item_id[]" value="<?= $row->req_item_id ?>">
                                        <input type="hidden" name="req_item_name[]" value="<?= $row->req_item_name ?>">
                                        <input type="hidden" name="trans_id[]" value="<?= $row->id ?>">
                                    </td>
                                    <td>
                                        <?= formatDate($row->dispatch_date) ?>
                                        <input type="hidden" name="dispatch_date[]" value="<?= $row->dispatch_date ?>">
                                    </td>
                                    <td>
                                        <?= $row->req_qty ?>
                                        <input type="hidden" name="req_qty[]" value="<?= $row->req_qty ?>">
                                    </td>
									<td><button type="button" onclick="Remove(this);" class="btn btn-outline-danger waves-effect waves-light"><i class="ti-trash"></i></button></td>

                                </tr>
                           
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</form>