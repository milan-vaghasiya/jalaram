<form enctype="multipart/form-data">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <input type="hidden" name="grn_trans_id" id="grn_trans_id" value="<?=(!empty($dataRow->grn_trans_id))?$dataRow->grn_trans_id:""; ?>"/>

            <div class="col-md-3 form-group" >
                <label for="test_report_no">Test Report No</label>
                <input type="text" name="test_report_no" class="form-control" value="<?=(!empty($dataRow->test_report_no))?$dataRow->test_report_no:''; ?>" />
            </div>
            <div class="col-md-6 form-group" >
                <label for="test_remark">Test Remark</label>
                <input type="text" name="test_remark" class="form-control" value="<?=(!empty($dataRow->test_remark))?$dataRow->test_remark:''; ?>" />
            </div>
            <div class="col-md-2 form-group" >
                <label for="test_result">Test Result</label>
                <select name="test_result" id="test_result" class="form-control single-select">
                    <option value="Ok" <?=(!empty($dataRow->test_result) && $dataRow->test_result == 'Ok')?'selected':''; ?>>Ok</option>
                    <option value="Not Ok" <?=(!empty($dataRow->test_result) && $dataRow->test_result == 'Not Ok')?'selected':''; ?>>Not Ok</option>
                </select>
            </div>
            <div class="col-md-3 form-group" >
                <label for="inspector_name">Inspector Name</label>
                <input type="text" name="inspector_name" class="form-control" value="<?=(!empty($dataRow->inspector_name))?$dataRow->inspector_name:''; ?>" />
            </div>
            <div class="col-md-6 form-group">
                <label for="tc_file">T.C. File</label>
                <input type="file" name="tc_file" id="tc_file" class="form-control-file"  />
            </div>
        </div>
    </div>
</form>
