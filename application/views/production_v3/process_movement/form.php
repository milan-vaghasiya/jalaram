<table class="table" style="border-radius:0px;box-shadow: 1px 2px 2px 0 rgb(0 0 0 / 70%);left:0;top:0px;position:absolute;">
	<tbody>
		<tr class="">
			<th class="text-center text-white" style="background:#aeaeae;padding:0.25rem 0.5rem;border-top-left-radius:0px;border-bottom-left-radius:0px;border:0px;">Job No.</th>
			<th class="text-left" style="background:#f3f2f2;padding:0.25rem 0.5rem;">
				<?=(!empty($dataRow->job_card_id))?$dataRow->job_prefix.$dataRow->job_no:""?>
			</th>
			<th class="text-center text-white" style="background:#aeaeae;padding:0.25rem 0.5rem;">Product</th>
			<th class="text-left" style="background:#f3f2f2;padding:0.25rem 0.5rem;">
				<?=(!empty($dataRow->product_code))?$dataRow->product_code:""?>
			</th>
			<th class="text-center text-white" style="background:#aeaeae;padding:0.25rem 0.5rem;">Process</th>
			<th class="text-left" style="background:#f3f2f2;padding:0.25rem 0.5rem;">
				<?=(!empty($dataRow->in_process_name))?$dataRow->in_process_name:""?> -> 
				<?=(!empty($dataRow->out_process_name))?$dataRow->out_process_name:"Store Location"?>
			</th>
			<th class="text-center text-white" style="background:#aeaeae;padding:0.25rem 0.5rem;">Qty.</th>
			<th class="text-left" id="pending_qty" style="background:#f3f2f2;padding:0.25rem 0.5rem;border-top-right-radius:0px; border-bottom-right-radius:0px;border:0px;"><?=(!empty($dataRow->pqty))?$dataRow->pqty:""?></th>
		</tr>
	</tbody>
</table>
<form style="padding-top:35px;">
    <div class="col-md-12">
        <div class="row">

            <input type="hidden" name="id" id="id" value="">
            <input type="hidden" name="w_pcs" id="w_pcs" value="<?= $dataRow->in_w_pcs ?>">
            <input type="hidden" name="total_weight" id="total_weight" value="<?= $dataRow->in_total_weight ?>">
            <input type="hidden" name="finished_weight" id="finished_weight" value="<?= $dataRow->finished_weight ?>">
            <input type="hidden" name="material_request" value="1">
            <input type="hidden" name="ref_id" id="ref_id" value="<?= $dataRow->id ?>">
			
			
			<input type="hidden" name="job_card_no" id="job_card_no" value="<?=(!empty($dataRow->job_card_id))?$dataRow->job_prefix.$dataRow->job_no:""?>">
            <input type="hidden" name="job_card_id" id="job_card_id" value="<?=(!empty($dataRow->job_card_id))?$dataRow->job_card_id:""?>" />
            <input type="hidden" id="delivery_date" value="<?=(!empty($dataRow->delivery_date))?date("d-m-Y",strtotime($dataRow->delivery_date)):""?>" />
			<input type="hidden" id="product_name" value="<?=(!empty($dataRow->product_code))?$dataRow->product_code:""?>" />
            <input type="hidden" name="product_id" id="product_id" value="<?=(!empty($dataRow->product_id))?$dataRow->product_id:""?>" />
			<input type="hidden" id="in_process_name" value="<?=(!empty($dataRow->in_process_name))?$dataRow->in_process_name:""?>" />
			<input type="hidden" name="in_process_id" id="in_process_id" value="<?=(!empty($dataRow->in_process_id))?$dataRow->in_process_id:"0"?>">
			<input type="hidden" name="out_process_id" id="out_process_id" value="<?=(!empty($dataRow->out_process_id))?$dataRow->out_process_id:""?>" />
			<input type="hidden" name="in_qty" id="in_qty" value="<?=(!empty($dataRow->in_qty))?$dataRow->in_qty:""?>" />
			<input type="hidden" id="out_process_name" value="<?=(!empty($dataRow->out_process_name))?$dataRow->out_process_name:""?>" />
			<input type="hidden" id="pqty" value="<?=(!empty($dataRow->pqty))?$dataRow->pqty:""?>" readonly />
            <input type="hidden" name="trans_ref_id" id="trans_ref_id" value="<?= (!empty($trans_ref_id)) ? $trans_ref_id : "" ?>">
            <input type="hidden" name="from_entry_type" id="from_entry_type" value="<?= (!empty($from_entry_type)) ? $from_entry_type : "" ?>">

            <div class="col-md-3 form-group">
                <label for="entry_date">Date</label>
                <input type="date" name="entry_date" id="entry_date" class="form-control" value="<?= $maxDate ?>"  min="<?=$startYearDate?>" max="<?=$maxDate?>">
            </div>
            <div class="col-md-3 form-group">
                <label for="out_qty">Out Qty. (Pcs)</label>
                <input type="number" name="out_qty" id="out_qty" class="form-control numericOnly req" <?=(!empty($dataRow->vp_trans_id) || !empty($trans_ref_id))?'readonly':''?> value="">

                <div class="error batch_stock_error"></div>
            </div>
           
            <div class="col-md-3 form-group">
                <label for="in_qty_kg">Out Qty. (Kg)</label>
                <input type="number" name="in_qty_kg" id="in_qty_kg" class="form-control floatOnly req" value="">
            </div>
            <div class="col-md-3 form-group">
                <label for="send_to">Send To</label>
                <select name="send_to" id="send_to" class="form-control single-select">
                    <option value="0">In House</option>
                    <option value="1">Vendor</option>
                </select>
            </div>
            <div class="col-md-12 form-group">
                <label for="remark">Remark</label>
                <input type="text" name="remark" id="remark" class="form-control" value="">
            </div>
            
        </div>
    </div>
</form>
<div class="col-md-12">
    <div class="row">
        <h5 style="width:100%;margin:0 auto;vertical-align:middle;border-top:1px solid #ccc;padding:5px 0px;">Process Transaction : 
			<button type="button" class="btn waves-effect waves-light btn-success float-right save-form" onclick="saveOutward('outWard');" style="padding:5px 40px;"><i class="fa fa-check"></i> Save</button>
		</h5>
        <div class="table-responsive">
            <table id='outwardTransTable' class="table table-bordered">
                <thead class="thead-info">
                    <tr>
                        <th style="width:5%;">#</th>
                        <th>Date</th>
                        <th>Vendor</th>
                        <th>Out Qty.</th>
                        <th>Remark</th>
                        <th style="width:10%;">Action</th>
                    </tr>
                </thead>
                <tbody id="outwardTransData">
                <?php
                    echo $outwardTrans;
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    $(document).on('keyup change',"#out_qty",function(){
    	var out_qty = $(this).val();
		var finished_weight = $("#finished_weight").val();
		var qty_kg = parseFloat(out_qty) * parseFloat(finished_weight)
		$("#in_qty_kg").val(qty_kg.toFixed(2));
	});
});
</script>