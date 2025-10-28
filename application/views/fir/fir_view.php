
<form id="firLotForm">
    <div class="row">
        <!-- Column -->
        <?php
        $totalRejQty = !empty(($firDimensionData)) ? array_sum(array_column($firDimensionData,'rej_qty')) : 0;
        $totalRwQty = !empty(($firDimensionData)) ? array_sum(array_column($firDimensionData,'rw_qty')) : 0;
        ?>
        <input type="hidden" name="total_rej_qty" id="total_rej_qty" value="<?=$totalRejQty?>">
        <input type="hidden" name="total_rw_qty" id="total_rw_qty" value="<?=$totalRwQty?>">
        <input type="hidden" name="part_id" id="part_id" value="<?=$firData->product_id?>">
        <input type="hidden" name="job_card_id" id="job_card_id" value="<?=$firData->job_card_id?>">
        <div class="col-lg-12 col-xlg-12 col-md-12">
            <table class="table table-bordered item-list-bb">
                <tr class="bg-light">
                    <th>FIR No</th>
                    <th>Date</th>
                    <th>FG Batch No</th>
                    <th>Product </th>
                    <th>Job No </th>
                    <th>Qty </th>
                </tr>
                <tr>
                    <td><?= !empty($dataRow->fir_number) ? $dataRow->fir_number :'' ?></td>
                    <td><?= !empty($dataRow->fir_date) ? $dataRow->fir_date :'' ?></td>
                    <td><?= !empty($dataRow->fg_batch_no) ? $dataRow->fg_batch_no  :'' ?></td>
                    <td><?= !empty($dataRow->full_name) ? $dataRow->full_name :'' ?></td>
                    <td><?= !empty($dataRow->job_number) ? $dataRow->job_number :'' ?></td>
                    <td><?= floatval(!empty($dataRow->qty) ? $dataRow->qty : '') ?></td>
                </tr>
            </table>
        </div>
        <div class="col-lg-12 col-xlg-12 col-md-12">
            <table class="table table-bordered item-list-bb text-left">
                <tr >
                    <th style="width:10%">Total Ok</th>
                    <td><?= (!empty(floatval($dataRow->total_ok_qty))) ? floatval($dataRow->total_ok_qty) : 0 ?></td>
                    <th  style="width:15%">Total Rejection</th>
                    <td><?= $totalRejQty ?> </td>
                    <th  style="width:10%">Total Rework</th>
                    <td><?= $totalRwQty?></td>
                </tr>
            </table>
        </div>
        <input type="hidden" id="id" name="id" value="<?= !empty($dataRow->id) ? $dataRow->id : '' ?>">
        <div class="col-lg-12 col-xlg-12 col-md-12">
            <div class="table-responsive">
            <table class="table table-bordered item-list-bb">
                <thead>
                        <tr class="text-center bg-light">
                            <th style="width:3%;">#</th>
                            <th class="text-left">Special Char.</th>
                            <th class="text-left">Product Parameter</th>
                            <th>Product Specification</th>
                            <th>Instrument</th>
                            <th>Sample Freq.</th>
                            <th>Date</th>
                            <th>OK</th>
                            <th>UD OK</th>
                            <th>Rejection</th>
                            <th>Remark</th>
                            <th>Rework</th>
                            <th>Inspected By</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($firDimensionData)) :
                            $i = 1;
                            foreach ($firDimensionData as $row) :
                                $diamention = '';
                                if ($row->requirement == 1) { $diamention = $row->min_req . '/' .  $row->max_req; }
                                if ($row->requirement == 2) { $diamention = $row->min_req . ' ' .  $row->other_req; }
                                if ($row->requirement == 3) { $diamention = $row->max_req . ' ' .  $row->other_req; }
                                if ($row->requirement == 4) { $diamention = $row->other_req; }
                        ?>
                                <tr class="text-center">
                                    <td><?= $i ?></td>
                                    <td class="text-left"><?php if (!empty($row->char_class)) { ?><img style="width:25px;display:inline-block;vertical-align:middle;" src="<?= base_url('assets/images/symbols/' . $row->char_class . '.png') ?>"><?php } ?></td>
                                    <td><?= $row->product_param ?></td>
                                    <td><?= $diamention ?></td>
                                    <td><?= $row->fir_measur_tech ?></td>
                                    <td><?= $row->fir_freq_text ?></td>
                                    <td>  <?= !empty($row->trans_date) ? ($row->trans_date) :  "" ?> </td>
                                    <td><?= !empty($row->ok_qty) ? floatval($row->ok_qty) : '' ?> </td>
                                    <td><?= !empty($row->ud_ok_qty) ? floatval($row->ud_ok_qty) : '' ?></td>
                                    <td><?= !empty($row->rej_qty) ? floatval($row->rej_qty) : '' ?></td>
                                    <td><?= !empty($row->remark) ? floatval($row->remark) : '' ?></td>
                                    <td><?= !empty($row->rw_qty) ? floatval($row->rw_qty) : '' ?></td>
                                    <td><?= !empty($row->emp_name) ?$row->emp_name : '' ?> </td>
                                </tr>

                            <?php $i++; endforeach;
                        else : ?>
                            <tr>
                                <td colspan="12" class="text-center">No data available in table </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <hr style="width:100%">
    <div class="error general_error"></div>
    <div class="row">
        <div class="col-md-3 form-group">
            <label for="rej_qty">Rejected Qty.</label>
            <input type="text" name="rej_qty" id="rej_qty" class="form-control numericOnly qtyCal req" value="" min="0" />
        </div>

        <div class="col-md-3 form-group">
            <label for="rej_reason">Rejection Reason</label>
            <select name="rej_reason" id="rej_reason" class="form-control single-select req">
                <option value="">Select Reason</option>
                <?php
                foreach ($rejectionComments as $row) :
                    $code = (!empty($row->code)) ? '[' . $row->code . '] - ' : '';
                    echo '<option value="' . $row->id . '" data-code="' . $row->code . '" data-reason="' . $row->remark . '" >' . $code . $row->remark . '</option>';

                endforeach;
                ?>
            </select>
        </div>
        <div class="col-md-3 form-group">
            <label for="rejection_stage">Rejection Belong To</label>
            <select id="rejection_stage" class="form-control single-select req">
                <?php if (empty($dataRow->stage)) { ?> <option value="">Select Stage</option> <?php } else {
                                                                                                echo $dataRow->stage;
                                                                                            } ?>
            </select>
        </div>
        <div class="col-md-3 form-group">
            <label for="rej_from">Rejection From</label>
            <select name="rej_from" id="rej_from" class="form-control single-select req">
                <?php if (empty($dataRow->rejOption)) { ?> <option value="">Select Process</option> <?php } else {
                                                                                                    //echo $dataRow->rejOption;
                                                                                                } ?>
            </select>
        </div>

        <div class="col-md-11 form-group">
            <label for="rej_remark">Rejection Remark</label>
            <input type="text" name="rej_remark" id="rej_remark" class="form-control" value="">
        </div>
        <div class="col-md-1 form-group">
            <label for="">&nbsp;</label>
            <button type="button" id="addRejectionRow" class="btn btn-outline-info btn-block ">Add</button>
        </div>
        <div class="col-md-12 form-group">
            <div class="table-responsive">
                <table id="rejectionReason" class="table table-bordered">
                    <thead class="thead-info">
                        <tr>
                            <th style="width:5%;">#</th>
                            <th>Rejection Qty.</th>
                            <th>Rejection Reason</th>
                            <th>Rejection Belong To</th>
                            <th>Rejection From</th>
                            <th>Rejection Remark</th>
                            <th style="width:15%;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="rejectionReasonData">
                    <?php
                        $rwTable = "";
                        if (!empty($rejRwData)) :
                        $i = 0;
                        $j = 0;

                        foreach ($rejRwData as $row) :
                            if ($row->manag_type == 1) :
                    ?>
                                    <tr>
                                        <td><?= $i + 1 ?>
                                            <input type="hidden" name="rejection_reason[<?= $i ?>][trans_id]" value="<?= $row->id ?>">
                                            <input type="hidden" name="rejection_reason[<?= $i ?>][rej_ref_id]" value="<?= $row->ref_id ?>">
                                            <input type="hidden" name="rejection_reason[<?= $i ?>][rej_type]" value="<?= $row->rej_type ?>">
                                        </td>
                                        <td><?= $row->qty ?><input type="hidden" name="rejection_reason[<?= $i ?>][rej_qty]" value="<?= $row->qty ?>"></td>
                                        <td><?= $row->reason_name ?><input type="hidden" name="rejection_reason[<?= $i ?>][rej_reason]" value="<?= $row->reason ?>"> <input type="hidden" name="rejection_reason[<?= $i ?>][rejection_reason]" value="<?= $row->reason_name ?>"></td>
                                        <td><?= $row->belongs_to_name ?><input type="hidden" name="rejection_reason[<?= $i ?>][rej_stage]" value="<?= $row->belongs_to ?>"> <input type="hidden" name="rejection_reason[<?= $i ?>][rej_stage_name]" value="<?= $row->belongs_to_name ?>"></td>
                                        <td><?= $row->vendor_name ?><input type="hidden" name="rejection_reason[<?= $i ?>][rej_from]" value="<?= $row->vendor_id ?>"> <input type="hidden" name="rejection_reason[<?= $i ?>][rej_party_name]" value="<?= $row->vendor_name ?>"></td>
                                        <td><?= $row->remark ?><input type="hidden" name="rejection_reason[<?= $i ?>][rej_remark]" value="<?= $row->remark ?>"></td>
                                        <td><button type="button" onclick="RemoveRejection(this)" style="margin-left:4px;" class="btn btn-outline-danger waves-effect waves-light text-center"><i class="ti-trash"></i></button></td>
                                    </tr>

                        <?php
                                    $i++;
                                elseif ($row->manag_type == 2) :


                                    $row->trans_id = $row->id;
                                    $row->rw_qty = $row->qty;
                                    $row->rw_reason = $row->reason;
                                    $row->rework_reason = $row->reason_name;
                                    $row->rw_stage = $row->belongs_to;
                                    $row->rw_stage_name = $row->belongs_to_name;
                                    $row->rw_from = $row->vendor_id;
                                    $row->rw_party_name = $row->vendor_name;
                                    $row->rw_remark = $row->remark;
                                    // echo "<script> AddRowRework(" . json_encode($row) . ");</script>";
                                    $rowData = json_encode($row);
                                    $hidden = ($row->qty == 0) ? 'hidden' : '';
                                    $rwTable .= '<tr ' . $hidden . '>
                                <td>' . (($row->qty > 0) ? $j + 1 : '') . '  <input type="hidden" name="rework_reason[' . $j . '][trans_id]" value="' . $row->id . '"></td>
                                <td>' . $row->qty . '  <input type="hidden" name="rework_reason[' . $j . '][rw_qty]" value="' . $row->qty . '"></td>
                                <td>' . $row->reason_name . '  <input type="hidden" name="rework_reason[' . $j . '][rw_reason]" value="' . $row->reason . '"> <input type="hidden" name="rework_reason[' . $j . '][rework_reason]" value="' . $row->reason_name . '"></td>
                                <td>' . $row->belongs_to_name . '  <input type="hidden" name="rework_reason[' . $j . '][rw_stage]" value="' . $row->belongs_to . '"> <input type="hidden" name="rework_reason[' . $j . '][rw_stage_name]" value="' . $row->belongs_to_name . '"></td>
                                <td>' . $row->vendor_name . '  <input type="hidden" name="rework_reason[' . $j . '][rw_from]" value="' . $row->vendor_id . '"> <input type="hidden" name="rework_reason[' . $j . '][rw_party_name]" value="' . $row->vendor_name . '"></td>
                                <td>' . $row->remark . '  <input type="hidden" name="rework_reason[' . $j . '][rw_remark]" value="' . $row->remark . '"></td>
                                                                    ';
                                    $rwTable .= "
                                <td class='text-center'>
                                    <!--<button type='button' onclick='convertToOKQty(" . $rowData . ",this)' style='margin-left:2px;' class='btn btn-outline-success waves-effect waves-light' datatip='Ok Qty'><i class='ti-check'></i></button>

                                    <button type='button' onclick='addRejQty(" . $rowData . ")' style='margin-left:2px;' class='btn btn-outline-warning waves-effect waves-light' datatip='Rejection Qty'><i class='ti-close'></i></button>-->

                                <button type='button' onclick='RemoveRework(this)' style='margin-left:2px;' class='btn btn-outline-danger waves-effect waves-light'><i class='ti-trash'></i></button>



                                </td>
                            </tr>";
                                    $j++;
                                endif;

                            endforeach;
                        else :
                            echo '<tr id="noData">
                                <td colspan="7" class="text-center">No data available in table</td>
                                </tr>';
                        endif;
                        
                        ?>

                    </tbody>
                </table>
            </div>
        </div>
     </div>
    <hr style="width:100%">
    <div class="error rw_error"></div>
    <div class="row">
        <div class="col-md-3 form-group">
            <label for="rw_qty">Rework Qty.</label>
            <input type="text" name="rw_qty" id="rw_qty" class="form-control numericOnly qtyCal req" value="" min="0" />
        </div>
        <div class="col-md-3 form-group">
            <label for="rw_reason">Rework Reason</label>
            <select name="rw_reason" id="rw_reason" class="form-control single-select req">
                <option value="">Select Reason</option>
                <?php
                foreach ($reworkComments as $row) :
                    $code = (!empty($row->code)) ? '[' . $row->code . '] - ' : '';
                    echo '<option value="' . $row->id . '" data-code="' . $row->code . '" data-reason="' . $row->remark . '" >' . $code . $row->remark . '</option>';

                endforeach;
                ?>
            </select>
        </div>
        <div class="col-md-3 form-group">
            <label for="rework_stage">Rework Belong To</label>
            <select id="rework_stage" class="form-control single-select req">
                <?php if (empty($dataRow->stage)) { ?> <option value="">Select Stage</option> <?php } else {
                                                                                                echo $dataRow->stage;
                                                                                            } ?>

            </select>
        </div>
        <div class="col-md-3 form-group">
            <label for="rw_from">Rework From</label>
            <select name="rw_from" id="rw_from" class="form-control single-select req">
                <?php if (empty($dataRow->rewOption)) { ?> <option value="">Select Process</option> <?php } else {
                                                                                                    echo $dataRow->rewOption;
                                                                                                } ?>
            </select>
        </div>
        <div class="col-md-11 form-group">
            <label for="rw_remark">Rework Remark</label>
            <input type="text" name="rw_remark" id="rw_remark" class="form-control" value="">
        </div>
        <div class="col-md-1 form-group">
            <label for="">&nbsp;</label>
            <button type="button" id="addReworkRow" class="btn btn-outline-info btn-block ">ADD</button>
        </div>
        <div class="col-md-12 form-group">
            <div class="table-responsive">
                <table id="reworkReason" class="table table-bordered">
                    <thead class="thead-info">
                        <tr>
                            <th style="width:5%;">#</th>
                            <th>Rework Qty.</th>
                            <th>Rework Reason</th>
                            <th>Rework Belong To</th>
                            <th>Rework From</th>
                            <th>Rework Remark</th>
                            <th style="width:10%;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="reworkReasonData">
                        <?php
                        if (!empty($rwTable)) :
                            echo  $rwTable;
                        else :
                            echo '<tr id="noData">
                            <td colspan="7" class="text-center">No data available in table</td>
                        </tr>';
                        endif;
                        ?>
                    </tbody>
                </table>
            </div>
        </div> 
    </div> 
</form>
							