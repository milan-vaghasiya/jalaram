<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="" />
            <input type="hidden" name="ref_id" value="<?=$ref_id?>">
            <input type="hidden" name="item_id" value="<?=$item_id?>">
            <input type="hidden" name="ecn_note_no" value="<?=$rev_no?>">
            <input type="hidden" name="entry_type" value="2">

            <div class="col-md-12 form-group">
                <label for="rev_no">Control Plan Rev No</label>
                <input type="text" name="rev_no" value="<?=sprintf("CP%02d",$rev_no)?>" class="form-control req" readonly>
            </div>
            <div class="col-md-12 form-group">
                <label for="rev_date">Rev Date</label>
                <input type="date" name="rev_date" id="rev_date" class="form-control req" value="<?=date("Y-m-d")?>">
            </div>
            <div class="col-md-12 form-group">
                <label for="remark">Remark</label>
                <textarea name="remark" value="" class="form-control ">
                </textarea>
            </div>
        </div>
    </div>
</form>