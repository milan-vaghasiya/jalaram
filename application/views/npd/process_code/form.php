<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />

            <div class="col-md-12 form-group">
                <label for="party_name">Process Code</label>
                <input type="text" name="process_code" class="form-control  req" value="<?=(!empty($dataRow->process_code))?$dataRow->process_code:""; ?>" />
            </div>
            <div class="col-md-12 form-group">
                <label for="party_name">Description</label>
                <input type="text" name="description" class="form-control  req" value="<?=(!empty($dataRow->description))?$dataRow->description:""; ?>" />
            </div>
        </div>
    </div>
</form>
