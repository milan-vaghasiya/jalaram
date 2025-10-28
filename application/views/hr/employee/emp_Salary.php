<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <input type="hidden" name="emp_id" id="emp_id" value="<?=(!empty($dataRow->emp_id))?$dataRow->emp_id:$emp_id; ?>"/>
            
            <div class="col-md-6 form-group">
                <label for="bank_name">Bank Name</label>
                <input type="text" name="bank_name" class="form-control" value="<?=(!empty($dataRow->bank_name))?$dataRow->bank_name:""?>" />
            </div>
            <div class="col-md-3 form-group">
                <label for="account_no">Account No</label>
                <input type="text" name="account_no" class="form-control" value="<?=(!empty($dataRow->account_no))?$dataRow->account_no:""?>" />
            </div>
            <div class="col-md-3 form-group">
                <label for="ifsc_code">Ifsc Code</label>
                <input type="text" name="ifsc_code" class="form-control" value="<?=(!empty($dataRow->ifsc_code))?$dataRow->ifsc_code:""?>" />
            </div>

            <div class="col-md-4 form-group">
                <label for="salary_basis">Salary Basis</label>
                <select name="salary_basis" id="salary_basis" class="form-control req">
                    <option value="M" <?=(!empty($dataRow->salary_basis) && $dataRow->salary_basis == 'M')?"selected":""?>>Monthly</option>
                    <option value="H" <?=(!empty($dataRow->salary_basis) && $dataRow->salary_basis == 'H')?"selected":""?>>Hourly</option>
                </select>
            </div>
            <div class="col-md-4 form-group">
                <label for="pf_no">Pf No</label>
                <input type="text" name="pf_no" class="form-control" value="<?=(!empty($dataRow->pf_no))?$dataRow->pf_no:""?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="basic_salary">Basic Salary</label>
                <input type="number" name="basic_salary" class="form-control floatOnly req" value="<?=(!empty($dataRow->basic_salary))?floatVal($dataRow->basic_salary):""?>" />
            </div>
       
            <div class="col-md-3 form-group">
                <label for="hra">HRA</label>
                <input type="number" name="hra" class="form-control floatOnly" value="<?=(!empty($dataRow->hra))?floatVal($dataRow->hra):""?>" />    
            </div>
            <div class="col-md-3 form-group">
                <label for="ta">Travelling Allowance</label>
                <input type="number" name="ta" class="form-control floatOnly" value="<?=(!empty($dataRow->ta))?floatVal($dataRow->ta):""?>" />    
            </div>
            <div class="col-md-3 form-group">
                <label for="da">Dearness Allowance</label>
                <input type="number" name="da" class="form-control floatOnly" value="<?=(!empty($dataRow->da))?floatVal($dataRow->da):""?>" />    
            </div>
            <div class="col-md-3 form-group">
                <label for="oa">Other Allowance</label>
                <input type="number" name="oa" class="form-control floatOnly" value="<?=(!empty($dataRow->oa))?floatVal($dataRow->oa):""?>" />    
            </div>
       
            <div class="col-md-4 form-group">
                <label for="pf_amount">Pf Amount</label>
                <input type="number" name="pf_amount" class="form-control floatOnly" value="<?=(!empty($dataRow->pf_amount))?floatVal($dataRow->pf_amount):""?>" />    
            </div>
            <div class="col-md-4 form-group">
                <label for="prof_tax">Professional Tax</label>
                <input type="number" name="prof_tax" class="form-control floatOnly" value="<?=(!empty($dataRow->prof_tax))?floatVal($dataRow->prof_tax):""?>" />    
            </div>
            <div class="col-md-4 form-group">
                <label for="other_deduction">Other Deduction</label>
                <input type="number" name="other_deduction" class="form-control floatOnly" value="<?=(!empty($dataRow->other_deduction))?floatVal($dataRow->other_deduction):""?>" />    
            </div>
        </div>
    </div>
</form>  