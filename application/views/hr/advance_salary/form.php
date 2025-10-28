<form autocomplete="off">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />

            
            
            

            <div class="col-md-6">
                <label for="date"> Date</label>
                <input type="date" name="date" id="date" class="form-control" value="<?=(!empty($dataRow->trans_date))?$dataRow->trans_date:date("Y-m-d")?>" max="<?=(!empty($dataRow->trans_date))?$dataRow->trans_date:date("Y-m-d")?>" />
            </div>

            <div class="col-md-6 form-group">
                <label for="emp_id">Employee</label>
                <select name="emp_id" id="emp_id" class="form-control single-select req">
                    <option value="">Select Employee</option>
                    <?php
                        foreach($empData as $row):
                            $selected = (!empty($dataRow->sales_executive) && $row->id == $dataRow->sales_executive)?"selected":"";
                            echo '<option value="'.$row->id.'" '.$selected.'>['.$row->emp_code.'] '.$row->emp_name.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>

            <div class="col-md-6 form-group">
                <label>Ledger Name</label>
                <select name="vou_acc_id" id="vou_acc_id" class="form-control single-select">
                <option value="">Select Ledger</option>
                    <?php
                        foreach($ledgerData as $row):
                            $selected = ($row->id == $dataRow->party_id) ? "selected":"";                            
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->party_name.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>

            <div class="col-md-6 form-group">
                <label for="amount">Amount</label>
                <input type="number" name="amount" class="form-control numericOnly req" value="<?=(!empty($dataRow->net_amount))?$dataRow->net_amount:""?>" />
            </div>
            
            <div class="col-md-12 form-group">
                <label for="reason">Reason</label>
                <textarea name="reason" class="form-control req" style="resize:none;" ><?=(!empty($dataRow->remark))?$dataRow->remark:""?></textarea>
            </div>
    
        </div>
    </div>
</form>