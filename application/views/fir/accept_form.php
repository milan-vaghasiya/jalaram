<form>
    
    <div class="col-md-12">
        <div class="row">
            <table class="table table-bordered">
                <tr>
                    <th>Job No</th>
                    <th>Item Code</th>
                    <th>Qty</th>
                </tr>
                <tr>
                    <td><?=getPrefixNumber($jobData->job_prefix,$jobData->job_no)?></td>
                    <td><?=$jobData->item_code?></td>
                    <td><?=($jobData->ok_qty - $jobData->accepted_qty)?></td>
                </tr>
            </table>
        </div>
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <input type="hidden" name="job_card_id" id="job_card_id" value="<?=(!empty($jobData->job_card_id))?$jobData->job_card_id:""; ?>">
            <input type="hidden" name="job_approval_id" id="job_approval_id" value="<?=(!empty($jobData->next_approval_id))?$jobData->next_approval_id:""; ?>">
            <input type="hidden" name="job_trans_id" id="job_trans_id" value="<?=(!empty($jobData->id))?$jobData->id:""; ?>">
            <input type="hidden" name="item_id" id="item_id" value="<?=(!empty($jobData->product_id))?$jobData->product_id:""; ?>">

            <div class="col-md-6 form-group">
				<label for="fir_date">Date</label>
				<input type="date" id="fir_date" name="fir_date" class="form-control req" placeholder="dd-mm-yyyy" value="<?=(!empty($dataRow->fir_date))?$dataRow->fir_date:date("Y-m-d")?>" max="<?=date('Y-m-d')?>" />
			</div>
            <div class="col-md-6 form-group">
                <label for="qty">Qty</label>
				<input type="text" id="qty" name="qty" class="form-control req floatOnly"  value="<?=(!empty($dataRow->ok_qty))?$dataRow->ok_qty:''?>" />
            </div>
        </div>
    </div>
</form>