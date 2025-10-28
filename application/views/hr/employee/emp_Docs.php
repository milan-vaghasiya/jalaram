<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <input type="hidden" name="emp_id" id="emp_id" value="<?=(!empty($dataRow->emp_id))?$dataRow->emp_id:$emp_id; ?>" />
            
            <div class="col-md-6 form-group">
                <label for="old_uan_no">Old Uan No</label>
                <input type="text" name="old_uan_no" class="form-control req" value="<?=(!empty($dataRow->old_uan_no))?$dataRow->old_uan_no:""?>" />
            </div>
            <div class="col-md-6 form-group">
                <label for="old_pf_no">Old Pf No</label>
                <input type="text" name="old_pf_no" class="form-control" value="<?=(!empty($dataRow->old_pf_no))?$dataRow->old_pf_no:""?>" />
            </div>

            <hr style="width:100%;">
			<div class="col-md-12 row">
                <div class="col-md-12"><h5>Aadhar Details : </h5></div>
            </div>
            <div class="col-md-4 form-group">
                <label for="aadhar_name">Aadhar Name</label>
                <input type="text" name="aadhar_name" class="form-control" value="<?=(!empty($dataRow->aadhar_name))?$dataRow->aadhar_name:""?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="aadhar_no">Aadhar No</label>
                <input type="text" name="aadhar_no" class="form-control" value="<?=(!empty($dataRow->aadhar_no))?$dataRow->aadhar_no:""?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="aadhar_dob">Date of Birth <small>(aadhar)</small></label>
                <input type="date" name="aadhar_dob" class="form-control" value="<?=(!empty($dataRow->aadhar_dob))?$dataRow->aadhar_dob:""?>" />
            </div>

            <hr style="width:100%;">
			<div class="col-md-12 row">
                <div class="col-md-12"><h5>Pan Details : </h5></div>
            </div>
            <div class="col-md-4 form-group">
                <label for="pan_name">Pan Name</label>
                <input type="text" name="pan_name" class="form-control" value="<?=(!empty($dataRow->pan_name))?$dataRow->pan_name:""?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="pan_no">Pan No</label>
                <input type="text" name="pan_no" class="form-control" value="<?=(!empty($dataRow->pan_no))?$dataRow->pan_no:""?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="pan_dob">Date of Birth <small>(pan)</small></label>
                <input type="date" name="pan_dob" class="form-control" value="<?=(!empty($dataRow->pan_dob))?$dataRow->pan_dob:""?>" />
            </div>

            <hr style="width:100%;">
			<div class="col-md-12 row">
                <div class="col-md-12"><h5>Kyc Details : </h5></div>
            </div>
            <div class="col-md-4 form-group">
                <label for="kyc_name">Kyc Name</label>
                <input type="text" name="kyc_name" class="form-control" value="<?=(!empty($dataRow->kyc_name))?$dataRow->kyc_name:""?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="kyc_no">Kyc No</label>
                <input type="text" name="kyc_no" class="form-control" value="<?=(!empty($dataRow->kyc_no))?$dataRow->kyc_no:""?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="kyc_dob">Date of Birth <small>(kyc)</small></label>
                <input type="date" name="kyc_dob" class="form-control" value="<?=(!empty($dataRow->kyc_dob))?$dataRow->kyc_dob:""?>" />
            </div>
        </div>
    </div>
</form>  