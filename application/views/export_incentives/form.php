<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>">

            <div class="col-md-6 form-group">
                <label for="trans_number">Invoice No.</label>
                <input type="text" id="trans_number" class="form-control" value="<?=(!empty($dataRow->doc_no))?$dataRow->doc_no:""?>" readonly>
            </div>

            <div class="col-md-6 form-group">
                <label for="trans_date">Invoice Date</label>
                <input type="text" id="trans_date" class="form-control" value="<?=(!empty($dataRow->doc_date))?formatDate($dataRow->doc_date):""?>" readonly>
            </div>

            <div class="col-md-12 form-group">
                <label for="drawback_date">Drawback Date</label>
                <input type="date" name="drawback_date" id="drawback_date" class="form-control" value="<?=(!empty($dataRow->drawback_date))?$dataRow->drawback_date:""?>">
            </div>

            <div class="col-md-12 form-group">
                <label for="igst_refund_date">IGST Refund Date</label>
                <input type="date" name="igst_refund_date" id="igst_refund_date" class="form-control" value="<?=(!empty($dataRow->igst_refund_date))?$dataRow->igst_refund_date:""?>">
            </div>

            <div class="col-md-12 form-group">
                <label for="igst_refund_error">IGST Refund Error</label>
                <input type="text" name="igst_refund_error" id="igst_refund_error" class="form-control" value="<?=(!empty($dataRow->igst_refund_error))?$dataRow->igst_refund_error:""?>">
            </div>

            <div class="col-md-12 form-group">
                <label for="rodtep_date">RODTEP Date</label>
                <input type="date" name="rodtep_date" id="rodtep_date" class="form-control" value="<?=(!empty($dataRow->rodtep_date))?$dataRow->rodtep_date:""?>">
            </div>
        </div>
    </div>
</form>