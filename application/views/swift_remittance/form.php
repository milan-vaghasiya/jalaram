<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>">

            <div class="col-md-6 form-group">
                <label for="firc_number">FIRC Number</label>
                <input type="text" name="firc_number" id="firc_number" class="form-control req" value="<?=(!empty($dataRow->firc_number))?$dataRow->firc_number:""?>">
            </div>

            <div class="col-md-6 form-group">
                <label for="remittance_date">Remittance Date</label>
                <input type="date" name="remittance_date" id="remittance_date" class="form-control req" max="<?=date("Y-m-d")?>" value="<?=(!empty($dataRow->remittance_date))?$dataRow->remittance_date:date("Y-m-d")?>">
            </div>

            <div class="col-md-12 form-group">
                <label for="remitter_name">Remitter Name</label>
                <input type="text" name="remitter_name" id="remitter_name" class="form-control req" value="<?=(!empty($dataRow->remitter_name))?$dataRow->remitter_name:""?>">
            </div>

            <div class="col-md-6 form-group">
                <label for="remitter_country">Remitter Country</label>
                <select name="remitter_country" id="remitter_country" class="form-control single-select req">
                    <option value="">Select Country</option>
                    <?php
                        foreach($countryList as $row):
                            $selected = (!empty($dataRow->remitter_country) && $dataRow->remitter_country == $row->name)?"selected":"";
                            echo '<option value="'.$row->name.'" '.$selected.'>'.$row->name.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>

            <div class="col-md-6 form-group">
                <label for="swift_currency">SWIFT Currency</label>
                <select name="swift_currency" id="swift_currency" class="form-control single-select req">
                    <option value="">Select Currency</option>
                    <?php
                        foreach($currencyList as $row):
                            $selected = (!empty($dataRow->swift_currency) && $dataRow->swift_currency == $row->currency)?"selected":"";
                            echo '<option value="'.$row->currency.'" '.$selected.'>'.$row->currency.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>

            <div class="col-md-6 form-group">
                <label for="swift_amount">Swift Amount</label>
                <input type="text" name="swift_amount" id="swift_amount" class="form-control floatOnly req" value="<?=(!empty($dataRow->swift_amount))?$dataRow->swift_amount:""?>">
            </div>

            <div class="col-md-6 form-group">
                <label for="firc_amount">FIRC Amount</label>
                <input type="text" name="firc_amount" id="firc_amount" class="form-control floatOnly req" value="<?=(!empty($dataRow->firc_amount))?$dataRow->firc_amount:""?>">
            </div>

            <div class="col-md-12 form-group">
                <label for="swift_remark">Swift Remark</label>
                <input type="text" name="swift_remark" id="swift_remark" class="form-control" value="<?=(!empty($dataRow->swift_remark))?$dataRow->swift_remark:""?>">
            </div>
        </div>
    </div>
</form>