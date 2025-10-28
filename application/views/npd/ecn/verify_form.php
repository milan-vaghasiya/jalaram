<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=$id?>" />

            <div class="col-md-12 form-group">
                <label for="status">Verification Status</label>
                <select name="status" id="status" class="form-control single-select req">
                    <option value="">Select Status</option>
                    <option value="1">Approved</option>
                    <option value="2">Rejected</option>
                    <option value="3">Hold</option>
                </select>
                <div class="error status"></div>
            </div>

            <div class="col-md-12 form-group">
                <label for="completion_date">Completion Date</label>
                <input type="date" name="completion_date" id="completion_date" class="form-control req" value="">
            </div>
        </div>
    </div>
</form>