<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>">
            <input type="hidden" name="com_inv_id" id="com_inv_id" value="<?=(!empty($dataRow->com_inv_id))?$dataRow->com_inv_id:""?>">

            <div class="col-md-3 form-group">
                <label for="trans_number">Invoice No.</label>
                <input type="text" id="trans_number" class="form-control" value="<?=(!empty($dataRow->doc_no))?$dataRow->doc_no:""?>" readonly>
            </div>

            <div class="col-md-2 form-group">
                <label for="trans_date">Invoice Date</label>
                <input type="text" id="trans_date" class="form-control" value="<?=(!empty($dataRow->doc_date))?formatDate($dataRow->doc_date):""?>" readonly>
            </div>

            <div class="col-md-4 form-group">
                <label for="party_name">Buyer Name</label>
                <input type="text" id="party_name" class="form-control" value="<?=(!empty($dataRow->party_name))?$dataRow->party_name:""?>" readonly>
            </div>

            <div class="col-md-3 form-group">
                <label for="party_name">Destination Country</label>
                <input type="text" id="party_name" class="form-control" value="<?=(!empty($dataRow->country_of_final_destonation))?$dataRow->country_of_final_destonation:""?>" readonly>
            </div>

            <div class="col-md-3 form-group">
                <label for="sb_amount">SB Amount (FC)</label>
                <input type="text" name="sb_amount" id="sb_amount" class="form-control floatOnly req" value="<?=(!empty($dataRow->sb_amount))?$dataRow->sb_amount:""?>">
            </div>

            <div class="col-md-3 form-group">
                <label for="port_code">Port Code</label>
                <input type="text" name="port_code" id="port_code" class="form-control req" minlength="6" maxlength="6" value="<?=(!empty($dataRow->port_code))?$dataRow->port_code:""?>">
            </div>

            <div class="col-md-3 form-group">
                <label for="sb_number">SB Number</label>
                <input type="text" name="sb_number" id="sb_number" class="form-control req" minlength="7" maxlength="7" value="<?=(!empty($dataRow->sb_number))?$dataRow->sb_number:""?>">
            </div>

            <div class="col-md-3 form-group">
                <label for="sb_date">SB Date</label>
                <input type="date" name="sb_date" id="sb_date" class="form-control req" max="<?=date("Y-m-d")?>" value="<?=(!empty($dataRow->sb_date))?$dataRow->sb_date:date("Y-m-d")?>">
            </div>

            <div class="col-md-3 form-group">
                <label for="sb_fob_inr">SB FOB INR</label>
                <input type="text" name="sb_fob_inr" id="sb_fob_inr" class="form-control floatOnly req" value="<?=(!empty($dataRow->sb_fob_inr))?$dataRow->sb_fob_inr:""?>">
            </div>

            <div class="col-md-3 form-group">
                <label for="sb_freight_inr">SB Freight INR</label>
                <input type="text" name="sb_freight_inr" id="sb_freight_inr" class="form-control floatOnly" value="<?=(!empty($dataRow->sb_freight_inr))?$dataRow->sb_freight_inr:""?>">
            </div>

            <div class="col-md-3 form-group">
                <label for="sb_insurance_inr">SB Insurance INR</label>
                <input type="text" name="sb_insurance_inr" id="sb_insurance_inr" class="form-control floatOnly" value="<?=(!empty($dataRow->sb_insurance_inr))?$dataRow->sb_insurance_inr:""?>">
            </div>

            <div class="col-md-3 form-group">
                <label for="drawback_amount">Drawback Amount</label>
                <input type="text" name="drawback_amount" id="drawback_amount" class="form-control floatOnly" value="<?=(!empty($dataRow->drawback_amount))?$dataRow->drawback_amount:""?>">
            </div>

            <div class="col-md-3 form-group">
                <label for="igst_amount">IGST Amount</label>
                <input type="text" name="igst_amount" id="igst_amount" class="form-control floatOnly" value="<?=(!empty($dataRow->igst_amount))?$dataRow->igst_amount:""?>">
            </div>

            <div class="col-md-3 form-group">
                <label for="rodtep_amount">RODTEP Amount</label>
                <input type="text" name="rodtep_amount" id="rodtep_amount" class="form-control floatOnly" value="<?=(!empty($dataRow->rodtep_amount))?$dataRow->rodtep_amount:""?>">
            </div>

            <div class="col-md-3 form-group">
                <label for="sb_ex_rate">SB Ex. Rate</label>
                <input type="text" name="sb_ex_rate" id="sb_ex_rate" class="form-control floatOnly req" value="<?=(!empty($dataRow->sb_ex_rate))?$dataRow->sb_ex_rate:""?>">
            </div>

            <div class="col-md-12 form-group">
                <label for="sb_remark">SB Remark</label>
                <input type="text" name="sb_remark" id="sb_remark" class="form-control req" value="<?=(!empty($dataRow->sb_remark))?$dataRow->sb_remark:""?>">
            </div>
        </div>
    </div>
</form>