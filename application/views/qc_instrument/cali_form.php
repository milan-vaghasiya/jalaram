<form enctype="multipart/form-data">
	<div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : ""; ?>" />   
			<div class="col-md-6 form-group vndrList">
                <label for="cal_agency">Calibration Agency</label>
                <select  name="cal_agency" id="cal_agency" class="form-control single-select">
                    <option value="0">IN-HOUSE</option>
                    <?php
						if(!empty($supVndrList)){
							foreach($supVndrList as $row){
								$selected = (!empty($dataRow->cal_agency) && $dataRow->cal_agency == $row->id) ? "selected" : "";
								echo '<option value="'.$row->id.'" '.$selected.'>'.$row->party_name.'</option>';
							}
						}
                    ?>
                </select>
				<input type="hidden" name="cal_agency_name" value="<?= (!empty($dataRow->cal_agency_name)) ? $dataRow->cal_agency_name : "" ?>" />
            </div>
            <div class="col-md-6 form-group">
                <label for="cal_certi_no">Certificate No.</label>
                <input type="text" name="cal_certi_no" class="form-control req" value="<?= (!empty($dataRow->cal_certi_no)) ? $dataRow->cal_certi_no : "" ?>" />
            </div>
            <div class="col-md-6 form-group">
                <label for="certificate_file">Certificate File</label>
                <input type="file" name="certificate_file" class="form-control-file" />
            </div>
            <div class="col-md-6 form-group">
                <label for="remark">Remark</label>
                <input type="text" name="remark" class="form-control" value="<?= (!empty($dataRow->remark)) ? $dataRow->remark : "" ?>" />
            </div>
        </div>
    </div>
</form>
