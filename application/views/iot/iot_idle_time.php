<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <input type="hidden" name="machine_id" value="<?=(!empty($dataRow->machine_id))?$dataRow->machine_id:$machine_id; ?>" />
			<div class="col-md-12 form-group">
                <label for="idle_time">Idle Time ( In Min. )</label>
                <input type="text" name="idle_time" class="form-control req" value="<?=(!empty($dataRow->idle_time))?$dataRow->idle_time:""?>" />
            </div>
        </div>
    </div>
</form>