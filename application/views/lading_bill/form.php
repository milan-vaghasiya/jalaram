<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>">
            <input type="hidden" name="sb_id" id="sb_id" value="<?=(!empty($dataRow->sb_id))?$dataRow->sb_id:""?>">
            <input type="hidden" name="com_inv_id" id="com_inv_id" value="<?=(!empty($dataRow->com_inv_id))?$dataRow->com_inv_id:""?>">

            <div class="col-md-3 form-group">
                <label for="trans_number">Invoice No.</label>
                <input type="text" id="trans_number" class="form-control" value="<?=(!empty($dataRow->doc_no))?$dataRow->doc_no:""?>" readonly>
            </div>

            <div class="col-md-3 form-group">
                <label for="trans_date">Invoice Date</label>
                <input type="text" id="trans_date" class="form-control" value="<?=(!empty($dataRow->doc_date))?formatDate($dataRow->doc_date):""?>" readonly>
            </div>

            <div class="col-md-6 form-group">
                <label for="party_name">Buyer Name</label>
                <input type="text" id="party_name" class="form-control" value="<?=(!empty($dataRow->party_name))?$dataRow->party_name:""?>" readonly>
            </div>

            <div class="col-md-4 form-group">
                <label for="country_of_final_destonation">Destination Country</label>
                <input type="text" id="country_of_final_destonation" class="form-control" value="<?=(!empty($dataRow->country_of_final_destonation))?$dataRow->country_of_final_destonation:""?>" readonly>
            </div>

            <div class="col-md-4 form-group">
                <label for="port_of_loading">Port of Loading</label>
                <input type="text" id="port_of_loading" class="form-control" value="<?=(!empty($dataRow->port_of_loading))?$dataRow->port_of_loading:""?>" readonly>
            </div>

            <div class="col-md-4 form-group">
                <label for="port_of_discharge">Port of Discharge</label>
                <input type="text" id="port_of_discharge" class="form-control" value="<?=(!empty($dataRow->port_of_discharge))?$dataRow->port_of_discharge:""?>" readonly>
            </div>

            <div class="col-md-2 form-group">
                <label for="inco_terms">Inco Terms</label>
                <input type="text" name="inco_terms" id="inco_terms" class="form-control req" minlength="3" maxlength="3" value="<?=(!empty($dataRow->inco_terms))?$dataRow->inco_terms:""?>">
            </div>

            <div class="col-md-2 form-group">
                <label for="cha_fa">CHA & FA</label>
                <input type="text" name="cha_fa" id="cha_fa" class="form-control req" value="<?=(!empty($dataRow->cha_fa))?$dataRow->cha_fa:""?>">
            </div>

            <div class="col-md-2 form-group">
                <label for="bl_awb_no">BL/AWB No.</label>
                <input type="text" name="bl_awb_no" id="bl_awb_no" class="form-control req" value="<?=(!empty($dataRow->bl_awb_no))?$dataRow->bl_awb_no:""?>">
            </div>

            <div class="col-md-3 form-group">
                <label for="bl_awb_date">BL/AWB Date</label>
                <input type="date" name="bl_awb_date" id="bl_awb_date" class="form-control req" max="<?=date("Y-m-d")?>" value="<?=(!empty($dataRow->bl_awb_date))?$dataRow->bl_awb_date:date("Y-m-d")?>">
            </div>

            <div class="col-md-3 form-group">
                <label for="payment_due_date">Payment Due Date</label>
                <input type="date" name="payment_due_date" id="payment_due_date" class="form-control req" min="<?=date("Y-m-d")?>" value="<?=(!empty($dataRow->payment_due_date))?$dataRow->payment_due_date:date("Y-m-d")?>">
            </div>

            <div class="col-md-12 form-group">
                <label for="bl_remark">BL Remark</label>
                <input type="text" name="bl_remark" id="bl_remark" class="form-control req" value="<?=(!empty($dataRow->bl_remark))?$dataRow->bl_remark:""?>">
            </div>
        </div>
    </div>
</form>
<script>
$(document).ready(function(){
    $(document).on('change','#bl_awb_date',function(){
        var bl_date = $(this).val();
        $("#payment_due_date").val(bl_date).attr('min',bl_date);
    });
});
</script>