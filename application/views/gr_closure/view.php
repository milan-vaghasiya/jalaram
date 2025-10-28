
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
            <input type="text" id="sb_amount" class="form-control" value="<?=(!empty($shippingDetail->sb_amount))?$shippingDetail->sb_amount:""?>" readonly>
        </div>

        <div class="col-md-6 form-group">
            <label for="party_name">Buyer Name</label>
            <input type="text" id="party_name" class="form-control" value="<?=(!empty($shippingDetail->party_name))?$shippingDetail->party_name:""?>" readonly>
        </div>            

        <div class="col-md-3 form-group">
            <label for="req_ref_no">Req. Ref. No.</label>
            <input type="text" name="req_ref_no" id="req_ref_no" class="form-control req" value="<?=(!empty($shippingDetail->req_ref_no))?$shippingDetail->req_ref_no:""?>" readonly>
        </div>

        <div class="col-md-3 form-group">
            <label for="bank_bill_id">Bank Bill ID</label>
            <input type="text" name="bank_bill_id" id="bank_bill_id" class="form-control req" value="<?=(!empty($shippingDetail->bank_bill_id))?$shippingDetail->bank_bill_id:""?>" readonly>
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
                            <th>Mapped FIRC</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i=1;
                            foreach($mappedSwifts as $row):
                                echo '<tr>
                                    <td>'.$row->firc_number.'</td>
                                    <td>'.formatDate($row->remittance_date).'</td>
                                    <td>'.$row->remitter_name.'</td>
                                    <td>'.$row->swift_currency.'</td>
                                    <td>'.sprintf("%.03f",($row->firc_amount - $row->mapped_firc_amount)).'</td>
                                    <td>'.$row->settled_fc.'</td>
                                </tr>';
                                $i++;
                            endforeach;
                        ?>
                    </tbody>
                    <tfoot class="thead-info">
                        <tr>
                            <th colspan="5">Total</th>
                            <th><?=(!empty($shippingDetail->total_mapped_firc))?$shippingDetail->total_mapped_firc:0?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>
</div>