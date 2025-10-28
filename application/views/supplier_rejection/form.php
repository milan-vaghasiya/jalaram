<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="">
            <input type="hidden" name="party_id" id="party_id" value="<?=(!empty($dataRow->party_id))?$dataRow->party_id:""?>">
            <input type="hidden" name="item_id" id="item_id" value="<?=(!empty($dataRow->item_id))?$dataRow->item_id:""?>">
            <input type="hidden" name="location_id" id="location_id" value="<?=(!empty($dataRow->location_id))?$dataRow->location_id:""?>">

            <div class="col-md-4 form-group">
                <label for="batch_no">Batch No.</label>
                <input type="text" name="batch_no" id="batch_no" class="form-control" value="<?=(!empty($dataRow->batch_no))?$dataRow->batch_no:""?>" readonly />
            </div>

            <div class="col-md-4 form-group">
                <label for="stock_qty">Stock Qty</label>
                <input type="text" id="stock_qty" class="form-control" value="<?=(!empty($dataRow->stock_qty))?$dataRow->stock_qty:""?>" readonly />
            </div>

            <div class="col-md-4 form-group">
                <label for="qty">Rejection Qty</label>
                <input type="text" name="qty" id="qty" class="form-control floatOnly" value="" />
            </div>

            <div class="col-md-10 form-group">
                <label for="remark">Remark</label>
                <input type="text" name="remark" id="remark" class="form-control" value="">
            </div>

            <div class="col-md-2 form-group">
                <label for="">&nbsp;</label>
                <button type="button" class="btn btn-success btn-block" onclick="saveSupplierRejection('supplierRejection');"><i class="fa fa-check"></i> Save</button>
            </div>
        </div>
    </div>
</form>
<hr>
<div class="col-md-12">
    <div class="row">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="thead-info">
                    <tr>
                        <th class="text-center">#</th>
                        <th class="text-center">Date</th>
                        <th class="text-center">Qty</th>
                        <th>Remark</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody id="batchTransData">
                    <?=$stockTransData?>
                </tbody>
            </table>
        </div>
    </div>
</div>