<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($brcDetail->id))?$brcDetail->id:""?>">
            <input type="hidden" name="brc_status" id="brc_status" value="<?=(!empty($brcDetail->brc_status))?$brcDetail->brc_status:"1"?>">

            <div class="col-md-6 form-group">
                <label for="sb_number">SB No.</label>
                <input type="text" id="sb_number" class="form-control" value="<?=(!empty($brcDetail->sb_number))?$brcDetail->sb_number:""?>" readonly>
            </div>

            <div class="col-md-6 form-group">
                <label for="sb_date">SB Date</label>
                <input type="text" id="sb_date" class="form-control" value="<?=(!empty($brcDetail->sb_date))?formatDate($brcDetail->sb_date):""?>" readonly>
            </div>

            <div class="col-md-6 form-group">
                <label for="port_code">Port Code</label>
                <input type="text" id="port_code" class="form-control" value="<?=(!empty($brcDetail->port_code))?$brcDetail->port_code:""?>" readonly>
            </div>

            <div class="col-md-6 form-group">
                <label for="firc_number">FIRC Number</label>
                <input type="text" id="firc_number" class="form-control" value="<?=(!empty($brcDetail->firc_number))?$brcDetail->firc_number:""?>" readonly>
            </div>

            <div class="col-md-6 form-group">
                <label for="swift_currency">SWIFT Currency</label>
                <input type="text" id="swift_currency" class="form-control" value="<?=(!empty($brcDetail->swift_currency))?$brcDetail->swift_currency:""?>" readonly>
            </div>

            <div class="col-md-6 form-group">
                <label for="mapped_firc_amount">Mapped FIRC</label>
                <input type="text" id="mapped_firc_amount" class="form-control" value="<?=(!empty($brcDetail->mapped_firc_amount))?$brcDetail->mapped_firc_amount:""?>" readonly>
            </div>

            <div class="col-md-6 form-group">
                <label for="brc_number">BRC Number</label>
                <input type="text" name="brc_number" id="brc_number" class="form-control" value="<?=(!empty($brcDetail->brc_number))?$brcDetail->brc_number:""?>">
            </div>

            <div class="col-md-6 form-group">
                <label for="brc_date">BRC Date</label>
                <input type="date" name="brc_date" id="brc_date" class="form-control" min="<?=(!empty($brcDetail->sb_date))?$brcDetail->sb_date:""?>" value="<?=(!empty($brcDetail->brc_date))?$brcDetail->brc_date:""?>">
            </div>

        </div>
    </div>
</form>