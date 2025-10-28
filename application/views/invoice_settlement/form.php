<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($ladingDetail->id))?$ladingDetail->id:""?>">

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
                <label for="net_amount">Invoice Amount</label>
                <input type="text" name="net_amount" id="net_amount" class="form-control" value="<?=(!empty($ladingDetail->net_amount))?$ladingDetail->net_amount:""?>" readonly>
            </div>

            <div class="col-md-3 form-group">
                <label for="bl_awb_date">BL / AWB Date</label>
                <input type="text" id="bl_awb_date" class="form-control" value="<?=(!empty($ladingDetail->bl_awb_date))?formatDate($ladingDetail->bl_awb_date):""?>" readonly>
            </div>

            <div class="col-md-3 form-group">
                <label for="payment_due_date">Payment Due Date</label>
                <input type="text" id="payment_due_date" class="form-control" value="<?=(!empty($ladingDetail->payment_due_date))?formatDate($ladingDetail->payment_due_date):""?>" readonly>
            </div>

            <div class="col-md-12 form-group">
                <label for="inco_terms">Inco Terms</label>
                <input type="text" id="inco_terms" class="form-control" value="<?=(!empty($ladingDetail->inco_terms))?$ladingDetail->inco_terms:""?>" readonly>
            </div>
        </div>

        <hr>

        <div class="row">
            <div class="col-md-12">
                <div class="error settlementError"></div>
                <div class="table table-responsive">
                    <table class="table table-bordered">
                        <thead class="thead-info">
                            <tr>
                                <th>FIRC Number</th>
                                <th>Remittance Date</th>
                                <th>Remitter Name</th>
                                <th>SWIFT Currency</th>
                                <th>Balance FC</th>
                                <th style="width:18%;">Settled FC</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i=1;
                                foreach($unsetlledSwifts as $row):
                                    echo '<tr>
                                        <td>'.$row->firc_number.'</td>
                                        <td>'.$row->remittance_date.'</td>
                                        <td>'.$row->remitter_name.'</td>
                                        <td>'.$row->swift_currency.'</td>
                                        <td>'.sprintf("%.03f",($row->swift_amount - $row->settled_amount)).'</td>
                                        <td>
                                            <input type="hidden" name="itemData['.$i.'][id]" value="">
                                            <input type="hidden" name="itemData['.$i.'][entry_type]" value="2">
                                            <input type="hidden" name="itemData['.$i.'][swift_id]" value="'.$row->id.'">
                                            <input type="hidden" name="itemData['.$i.'][bl_id]" value="'.$ladingDetail->id.'">
                                            <input type="text" name="itemData['.$i.'][settled_fc]" id="settled_fc_'.$i.'" data-row_id="'.$i.'" data-balance="'.round(($row->swift_amount - $row->settled_amount),3).'" class="settled_fc form-control floatOnly checkBalance"  value="0">
                                        </td>
                                    </tr>';
                                    $i++;
                                endforeach;
                            ?>
                        </tbody>
                        <tfoot class="thead-info">
                            <tr>
                                <th colspan="5">Short Received FC</th>
                                <th>
                                    <input type="text" name="short_received_fc" id="short_received_fc" class="settled_fc form-control floatOnly" value="0">
                                </th>
                            </tr>
                            <tr>
                                <th colspan="5">Total</th>
                                <th>
                                    <input type="text" name="received_fc" id="received_fc" class="form-control" value="0" readonly>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="col-md-12 form-group">
                <label for="settlement_remark">Settlement Remarks</label>
                <input type="text" name="settlement_remark" id="settlement_remark" class="form-control req" value="<?=(!empty($ladingDetail->settlement_remark))?$ladingDetail->settlement_remark:""?>">
            </div>
        </div>
    </div>
</form>
<script>
$(document).ready(function(){
    $(document).on('keyup change','.checkBalance',function(){
        var id = $(this).data('row_id');
        var balance = $(this).data('balance');
        var amount = $(this).val();

        $(".settled_fc_"+id).html('');
        if(parseFloat(amount) > parseFloat(balance)){
            $(".settled_fc_"+id).html('Insufficient Balance.');
            $("#settled_fc_"+id).val(0);
        }

        calculateTotal();
    });

    $(document).on('keyup change','.settled_fc',function(){
        calculateTotal();
    });
});

function calculateTotal(){
    var settledfcArray = $(".settled_fc").map(function () { return $(this).val(); }).get();
    var settledfcSum = 0;
    $.each(settledfcArray, function () { settledfcSum += parseFloat(this) || 0; });
    $("#received_fc").val(settledfcSum.toFixed(3));
}
</script>