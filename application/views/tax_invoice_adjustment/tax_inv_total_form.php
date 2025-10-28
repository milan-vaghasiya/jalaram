<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>">

            <div class="col-md-12 form-group">
                <label for="tax_invoice_total">Tax Invoice Total</label>
                <input type="text" name="tax_invoice_total" id="tax_invoice_total" class="form-control floatOnly req" value="<?=(!empty($dataRow->tax_invoice_total))?floatval($dataRow->tax_invoice_total):""?>">
            </div>
        </div>
    </div>
</form>