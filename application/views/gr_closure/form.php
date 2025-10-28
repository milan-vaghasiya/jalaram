<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($shippingDetail->id))?$shippingDetail->id:""?>">

            <div class="col-md-3 form-group">
                <label for="sb_number">SB No.</label>
                <input type="text" id="sb_number" class="form-control" value="<?=(!empty($shippingDetail->sb_number))?$shippingDetail->sb_number:""?>" readonly>
            </div>

            <div class="col-md-3 form-group">
                <label for="sb_date">SB Date</label>
                <input type="text" id="sb_date" class="form-control" value="<?=(!empty($shippingDetail->sb_date))?formatDate($shippingDetail->sb_date):""?>" readonly>
            </div>

            <div class="col-md-3 form-group">
                <label for="currency">Invoice Currency</label>
                <input type="text" id="currency" class="form-control" value="<?=(!empty($shippingDetail->currency))?$shippingDetail->currency:""?>" readonly>
            </div>

            <div class="col-md-3 form-group">
                <label for="sb_amount">SB Amount (FC)</label>
                <input type="text" name="sb_amount" id="sb_amount" class="form-control" value="<?=(!empty($shippingDetail->sb_amount))?$shippingDetail->sb_amount:""?>" readonly>
            </div>

            <div class="col-md-6 form-group">
                <label for="party_name">Buyer Name</label>
                <input type="text" id="party_name" class="form-control" value="<?=(!empty($shippingDetail->party_name))?$shippingDetail->party_name:""?>" readonly>
            </div>            

            <div class="col-md-3 form-group">
                <label for="req_ref_no">Req. Ref. No.</label>
                <input type="text" name="req_ref_no" id="req_ref_no" class="form-control req" value="<?=(!empty($shippingDetail->req_ref_no))?formatDate($shippingDetail->req_ref_no):""?>">
            </div>

            <div class="col-md-3 form-group">
                <label for="bank_bill_id">Bank Bill ID</label>
                <input type="text" name="bank_bill_id" id="bank_bill_id" class="form-control req" value="<?=(!empty($shippingDetail->bank_bill_id))?formatDate($shippingDetail->bank_bill_id):""?>">
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
                                <th>Balance FIRC</th>
                                <th style="width:18%;">Mapped FIRC</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i=1;
                                foreach($unmappedSwifts as $row):
                                    echo '<tr>
                                        <td>'.$row->firc_number.'</td>
                                        <td>'.$row->remittance_date.'</td>
                                        <td>'.$row->remitter_name.'</td>
                                        <td>'.$row->swift_currency.'</td>
                                        <td>'.sprintf("%.03f",($row->firc_amount - $row->mapped_firc_amount)).'</td>
                                        <td>
                                            <input type="hidden" name="itemData['.$i.'][id]" value="">
                                            <input type="hidden" name="itemData['.$i.'][entry_type]" value="3">
                                            <input type="hidden" name="itemData['.$i.'][swift_id]" value="'.$row->id.'">
                                            <input type="hidden" name="itemData['.$i.'][bl_id]" value="'.$shippingDetail->id.'">
                                            <input type="text" name="itemData['.$i.'][settled_fc]" id="settled_fc_'.$i.'" data-row_id="'.$i.'" data-balance="'.round(($row->firc_amount - $row->mapped_firc_amount),3).'" class="settled_fc form-control floatOnly checkBalance"  value="0">
                                        </td>
                                    </tr>';
                                    $i++;
                                endforeach;
                            ?>
                        </tbody>
                        <tfoot class="thead-info">
                            <tr>
                                <th colspan="5">Total</th>
                                <th>
                                    <input type="text" name="total_mapped_firc" id="total_mapped_firc" class="form-control" value="0" readonly>
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
    $("#total_mapped_firc").val(settledfcSum.toFixed(3));
}
</script>