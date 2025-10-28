<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : ""; ?>" />
            <input type="hidden" name="type" value="9" />
            <div class="col-md-12 form-group">
                <label for="visit_purpose">Visit Purpose </label>
                <input type="text" name="title" id="title" class="form-control req" value="<?= (!empty($dataRow->title)) ? $dataRow->title : "" ?>">
            </div>
        </div>
    </div>
</form>