<form id="quotForm" autocomplete="off">
    <div class="col-md-12">
        <div class="row">
            <div class="col mb-3"><label for="party_name">Customer Name : <span id="party_name"></span></label></div>
            <div class="col mb-3"><label for="enquiry_no">Quotation No. : <span id="quote_no"></span></label></div>
        </div>
        <div class="row">
            <div class="col mb-3"><label for="enquiry_date">Quotation Date : <span id="quotation_date"></span></label></div>
			<input type="hidden" name="id" id="id" value="" />
			<input type="hidden" name="quote_id" id="quote_id" value="" />
			<input type="hidden" name="customer_id" id="customer_id" value="" />
        </div>
        <div class="row">
			<div class="col mb-3">
				<label for="confirm_date">Confirm Date</label>
				<input type="date" id="confirm_date" name="confirm_date" class=" form-control req" placeholder="dd-mm-yyyy" aria-describedby="basic-addon2" value="<?=(!empty($dataRow->confirm_date))?$dataRow->confirm_date:date("Y-m-d")?>" />	
			</div>
        </div>
        <hr>
        <div class="error item_name_error"></div>
        <div class="table-responsive mb-5">
            <table class="table table-bordered align-items-center ">
                <tbody id="enquiryData">
                    <?php
                    if(!empty($quoteItems)):
                        $i=1; $html="";
                        foreach($quoteItems as $row):
                            $checked = ""; $disabled = "disabled";
                            if(!empty($row->confirm_by)):
                                $checked = "checked"; $disabled = "";
                            endif;
                                $html .= '<table class="table table-bordered">
                                                <tr>
                                                    <th colspan="3" >
                                                   
                                                        <input type="checkbox" id="md_checkbox'.$i.'" class="radio-label itemCheckCQ" data-rowid="'.$i.'" check="'.$checked.'" '.$checked.' />
                                                        <label for="md_checkbox'.$i.'" > '.$row->item_name.'</label>
                                                        <input type="hidden" name="item_id[]" id="item_id'.$i.'" class="form-control" value="'.$row->item_id.'" '.$disabled.' />
                                                        <input type="hidden" name="item_name[]" id="item_name'.$i.'" class="form-control" value="'.$row->item_name.'" '.$disabled.' />
                                                        <input type="hidden" name="trans_id[]" id="trans_id'.$i.'" class="form-control" value="'.$row->id.'" '.$disabled.' />
                                                        <input type="hidden" name="inq_trans_id[]" id="inq_trans_id'.$i.'" class="form-control" value="'.$row->ref_id.'" '.$disabled.' />
                                                        <input type="hidden" name="unit_id[]" id="unit_id'.$i.'" class="form-control" value="'.$row->unit_id.'" '.$disabled.' />
                                                        <input type="hidden" name="automotive[]" id="automotive'.$i.'" class="form-control" value="'.$row->automotive.'" '.$disabled.' />
                                                        <input type="hidden" name="qty[]" id="qty'.$i.'" class="form-control floatOnly" data-id="'.$i.'" value="'.$row->qty.'" min="0" '.$disabled.' />
                                                        <input type="hidden" name="price[]" id="price'.$i.'" class="form-control floatOnly" data-id="'.$i.'" value="'.$row->price.'" min="0" '.$disabled.' />
                                                        <div class="error price'.$row->id.'" ></div>
                                                        <div class="error qty'.$row->id.'"></div>
                                                   </th>
                                                </tr>
                                                <tr>
                                                    <th colspan="3"> Qty: '.$row->qty.'('.$row->unit_name.') | Price : '.$row->price.' </th>
                                                </tr>
                                                <tr>
                                                    <th>Confirm Price</th>
                                                    <th>Drg No</th>
                                                    <th>Rev No</th>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <input type="number" name="confirm_price[]" id="confirm_price'.$i.'" class="form-control floatOnly" data-id="'.$i.'" value="'.$row->org_price.'" min="0" '.$disabled.' />
                                                        <div class="error confirm_price'.$row->id.'"></div>
                                                    </td>			
                                                    <td>
                                                        <input type="text" name="drg_rev_no[]" id="drg_rev_no'.$i.'" class="form-control" value="'.$row->drg_rev_no.'" '.$disabled.' />
                                                    </td>
                                                    <td>
                                                        <input type="text" name="rev_no[]" id="rev_no'.$i.'" class="form-control" value="'.$row->rev_no.'" '.$disabled.' />
                                                    </td>		
                                                </tr>
                                        </table>';		
                           $i++;
                        endforeach;
                    else:
                        $html = '<tr><td colspan="4" class="text-center">No data available in table</td></tr>';
                    endif;
                    echo $html;
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    
    <div class="footer fixed ">
        <div class="container">
            <?php
                $param = "{'formId':'quotForm','fnsave':'saveConfirmQuotation','controller':'app/salesQuotation','res_function':'responseFunction'}";
            ?>
            <button type="button" class="btn btn-success btn-block" onclick="store(<?=$param?>)">Save</button>
        </div>
    </div>

</form>



<!-- <div class="product-area">	
    <div class="col">
        <table class="table" style="border-radius:15px;">
            <tr class="bg-light">
                <th class="text-center">Customer Name</th>
                <th class="text-left" id="unstoredQty"><?= (!empty($approvalData->ok_qty)) ? $approvalData->ok_qty - $approvalData->total_out_qty : "" ?></th>
            </tr>
        </table>
    </div>
    <form id="jobForm">
        <div class="row">
        <input type="hidden" name="id" id="id" value="">
            <input type="hidden" name="job_approval_id" id="jobApprovalId" value="<?=$approvalData->id?>">
            <input type="hidden" id="pend_qty" value="<?= (!empty($approvalData->ok_qty)) ? $approvalData->ok_qty - $approvalData->total_out_qty : "" ?>">

            <div class="row">
                <div class="col mb-3">
                    <label class="form-label" for="entry_date">Date</label>
                    <input type="date" name="entry_date" id="entry_date" class="form-control" value="<?=date("Y-m-d")?>">
                </div>
                <div class="col mb-3">
                    <label class="form-label" for="send_to">Send To</label>
                    <select name="send_to" id="send_to" class="form-control select2">
                        <option value="0" <?=($send_to == 0)?"selected":""?>>In House</option>
                        <option value="1" <?=($send_to == 1)?"selected":""?>>Vendor</option>
                        <?php  if(!empty($approvalData->out_process_id) && !empty($approvalData->in_process_id)){  ?>
                                    <option value="2" <?=($send_to == 2)?"selected":""?>>Store Semi FG</option>
                        <?php } ?>
                    
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label" for="handover_to">Handover To</label>
                <select name="handover_to" id="handover_to" class="form-control select2">
                    <?=$handover_to?>
                </select>
            </div>
            <div class="mb-3" id="out_process_div" style="display: none;">
                <label for="out_process_ids">Vendor Process</label>
                <select id="out_process_ids" name="out_process_ids[]"  class="form-control select2" multiple>
                    <option value="">Select Process</option>
                </select>
                <div class="error out_process_ids"></div>
            </div>

            <div class=" mb-3">
                <label class="form-label" for="qty">Qty.</label>
                <input type="number" name="qty" id="qty" class="form-control floatOnly" value="">
            </div>
            <div class="mb-3">
                <label class="form-label" for="remark">Remark</label>
                <input type="text" name="remark" id="remark" class="form-control" value="">
            </div>
    </form>
</div>
<div class="footer fixed ">
    <div class="container">
        <?php
            $param = "{'formId':'jobForm','fnsave':'saveProcessMovement','controller':'production/processMovement','res_function':'saveJobRespose'}";
        ?>
        <button type="button" class="btn btn-primary btn-block" onclick="store(<?=$param?>)">Save</button>
    </div>
</div> -->