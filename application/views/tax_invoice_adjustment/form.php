<form>
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-3 form-group">
                <label for="trans_number">Invoice No.</label>
                <input type="text" id="trans_number" class="form-control" value="<?=(!empty($ladingDetail->doc_no))?$ladingDetail->doc_no:""?>" readonly>
            </div>

            <div class="col-md-3 form-group">
                <label for="trans_date">Invoice Date</label>
                <input type="text" id="trans_date" class="form-control" value="<?=(!empty($ladingDetail->doc_date))?formatDate($ladingDetail->doc_date):""?>" readonly>
            </div>

            <div class="col-md-6 form-group">
                <label for="party_name">Buyer Name</label>
                <input type="text" id="party_name" class="form-control" value="<?=(!empty($ladingDetail->party_name))?$ladingDetail->party_name:""?>" readonly>
            </div>

            <div class="col-md-3 form-group">
                <label for="currency">Invoice Currency</label>
                <input type="text" id="currency" class="form-control" value="<?=(!empty($ladingDetail->currency))?$ladingDetail->currency:""?>" readonly>
            </div>

            <div class="col-md-3 form-group">
                <label for="sb_amount">SB Amount (FC)</label>
                <input type="text" name="sb_amount" id="sb_amount" class="form-control" value="<?=(!empty($ladingDetail->sb_amount))?$ladingDetail->sb_amount:""?>" readonly>
            </div>

            <div class="col-md-3 form-group">
                <label for="tax_invoice_total">Tax Inv. Total</label>
                <input type="text" name="tax_invoice_total" id="tax_invoice_total" class="form-control" value="<?=(!empty($ladingDetail->tax_invoice_total))?$ladingDetail->tax_invoice_total:""?>" readonly>
            </div>

            <div class="col-md-3 form-group">
                <label for="pening_amount">Pending Adjustment</label>
                <input type="text" id="pening_amount" class="form-control" value="<?=(!empty($ladingDetail->tax_invoice_total))?($ladingDetail->tax_invoice_total - $ladingDetail->igst_amount - $ladingDetail->net_credit_inr_adj):0?>" readonly>
            </div>

            
        </div>

        <hr>

        <div class="row">
            <div class="col-md-12">
                <div class="error adjustmentError"></div>
                <div class="table table-responsive">
                    <table class="table table-bordered">
                        <thead class="thead-info">
                            <tr>
                                <th>FIRC Number</th>
                                <th>Remitter Name</th>
                                <th>SWIFT Currency</th>
                                <th>Transfer Ref. No.</th>
                                <th>Transfer Date</th>
                                <th>FIRC Transfer Bal.</th>
                                <th>INR Credit Bal.</th>
                                <th style="width:15%;">FIRC Transfer Adj.</th>
                                <th style="width:15%;">INR Credit Adj.</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i=1;
                                foreach($unsetlledRemitTransfer as $row):
                                    echo '<tr>
                                        <td>'.$row->firc_number.'</td>
                                        <td>'.$row->remitter_name.'</td>
                                        <td>'.$row->swift_currency.'</td>
                                        <td>'.$row->trans_ref_no.'</td>
                                        <td>'.formatDate($row->trans_date).'</td>
                                        <td>'.sprintf("%.03f",($row->firc_transfer_bal)).'</td>
                                        <td>'.sprintf("%.03f",($row->net_credit_inr_bal)).'</td>
                                        <td>
                                            <input type="hidden" name="itemData['.$i.'][id]" value="">
                                            <input type="hidden" name="itemData['.$i.'][entry_type]" value="4">
                                            <input type="hidden" name="itemData['.$i.'][swift_id]" value="'.$row->id.'">
                                            <input type="hidden" name="itemData['.$i.'][bl_id]" value="'.$ladingDetail->id.'">

                                            <input type="text" name="itemData['.$i.'][firc_transfer_adj]" id="firc_transfer_adj_'.$i.'" data-row_id="'.$i.'" data-balance="'.round($row->firc_transfer_bal,3).'" class="firc_transfer_adj form-control floatOnly checkBalance"  value="0">
                                        </td>
                                        <td>
                                            <input type="text" name="itemData['.$i.'][net_credit_inr_adj]" id="net_credit_inr_adj_'.$i.'" data-row_id="'.$i.'" data-balance="'.round($row->net_credit_inr_bal,3).'" class="net_credit_inr_adj form-control floatOnly checkBalance"  value="0">
                                        </td>
                                    </tr>';
                                    $i++;
                                endforeach;
                            ?>
                        </tbody>
                        <tfoot class="thead-info">
                            <tr>
                                <th colspan="8">Ex. Gain/Loss INR</th>
                                <th>
                                    <input type="text" id="ex_gain_loss_inr" class="form-control" value="0" readonly>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</form>
<script>
$(document).ready(function(){
    calculateTotal();

    $(document).on('keyup change','.checkBalance',function(){
        var id = $(this).data('row_id');
        var balance = $(this).data('balance');
        var amount = $(this).val();

        if($(this).hasClass("firc_transfer_adj") == true){
            $(".firc_transfer_adj_"+id).html('');
            if(parseFloat(amount) > parseFloat(balance)){
                $(".firc_transfer_adj_"+id).html('Insufficient Balance.');
                $("#firc_transfer_adj_"+id).val(0);
            }
        }

        if($(this).hasClass("net_credit_inr_adj") == true){
            $(".net_credit_inr_adj_"+id).html('');
            if(parseFloat(amount) > parseFloat(balance)){
                $(".net_credit_inr_adj_"+id).html('Insufficient Balance.');
                $("#net_credit_inr_adj_"+id).val(0);
            }
        }

        calculateTotal();
    });

    $(document).on('keyup change','.net_credit_inr_adj',function(){
        calculateTotal();
    });
});

function calculateTotal(){
    var pending_amount = $("#pening_amount").val();

    var adjArray = $(".net_credit_inr_adj").map(function () { return $(this).val(); }).get();
    var adjSum = 0;
    $.each(adjArray, function () { adjSum += parseFloat(this) || 0; });

    var ex_gain_loss_inr = parseFloat(parseFloat(pending_amount) - parseFloat(adjSum)).toFixed(3);
    $("#ex_gain_loss_inr").val(ex_gain_loss_inr);

    if(parseFloat(ex_gain_loss_inr) >= 0){
        $("#ex_gain_loss_inr").attr('style','background-color: #bcffbc;');
    }else{
        $("#ex_gain_loss_inr").attr('style','background-color: #fab5bf;');
    }
}
</script>