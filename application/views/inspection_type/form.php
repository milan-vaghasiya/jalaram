<form>
    <div class="col-md-12">
        <div class="row">
            <?php $e_type = (!empty($dataRow->entry_type))?$dataRow->entry_type:$type; ?>
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <input type="hidden" name="entry_type" value="<?= $e_type ?>" />
			<div class="col-md-12 form-group">
                <label for="inspection_type"><?= ($e_type == 1)?'Inspection Type':'Inspection Parameter'; ?></label>
                <input type="text" name="inspection_type" class="form-control req" value="<?=(!empty($dataRow->inspection_type))?$dataRow->inspection_type:""?>" />
            </div>
        </div>
    </div>
</form>