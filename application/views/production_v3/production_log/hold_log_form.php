<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : ""; ?>" />
            <input type="hidden" name="m_ct" id="m_ct" value="<?= (!empty($dataRow->m_ct)) ? $dataRow->m_ct : ""; ?>" />
            <input type="hidden" name="part_code" id="part_code" value="<?= (!empty($dataRow->item_code)) ? $dataRow->item_code : ""; ?>">
            <input type="hidden" id="part_id" name="product_id" value="<?= (!empty($dataRow->product_id)) ? $dataRow->product_id : ""; ?>">
            <input type="hidden" id="job_approval_id" name="job_approval_id" value="<?= (!empty($dataRow->job_approval_id)) ? $dataRow->job_approval_id : ""; ?>">
            <input type="hidden" name="job_card_id" id="job_card_id" value="<?= (!empty($dataRow->job_card_id)) ? $dataRow->job_card_id : ""; ?>">
            <input type="hidden" name="process_id" id="process_id" value="<?= (!empty($dataRow->process_id)) ? $dataRow->process_id : ""; ?>">
            <input type="hidden" name="machine_id" id="machine_id" value="<?= (!empty($dataRow->machine_id)) ? $dataRow->machine_id : ""; ?>">
            <input type="hidden" name="operator_id" id="operator_id" value="<?= (!empty($dataRow->operator_id)) ? $dataRow->operator_id : ""; ?>">
            <div class="col-md-12">
                <table class="table">
                    <tr>
                        <th>Job Card No.</th>
                        <td>: <?= getPrefixNumber($dataRow->job_prefix, $dataRow->job_no) ?></td>
                        <th>Product </th>
                        <td>: <?= $dataRow->item_code ?></td>
                        <th>Process</th>
                        <td>: <?=$dataRow->process_name ?></td>
                    </tr>
                    <tr>
                        <th>Machine </th>
                        <td>:<?= $dataRow->machine_code ?></small></td>
                        <th>Operator </th>
                        <td>: <?= $dataRow->emp_name ?></td>
                        <th>Shift</th>
                        <td>: <?= $dataRow->shift_name ?></td>
                    </tr>
                 
                   
                </table>
            </div>
            <hr>
            <div class="col-md-4 form-group">
                <label for="production_qty">Hold Qty</label>
                <input type="text" name="production_qty" id="production_qty" class="form-control numericOnly partCount  req" min="0" value="<?= (!empty($dataRow->hold_qty)) ? floatVal($dataRow->hold_qty) : "0" ?>" readonly>
            </div>
            <div class="col-md-4 form-group">
                <label for="ok_qty">OK Qty.</label>
                <input type="text" name="ok_qty" id="ok_qty" class="form-control numericOnly req qtyCal" min="0" value="<?= (!empty($dataRow->hold_qty)) ? floatVal($dataRow->hold_qty) : "0" ?>" readonly> 
            </div>
           
            <hr style="width:100%">
            <div class="col-md-2 form-group">
                <label for="rej_qty">Rejected Qty.</label>
                <input type="text" id="rej_qty" class="form-control numericOnly qtyCal req" value="" min="0" />
                <input type="hidden" id="rej_ref_id" class="form-control numericOnly qtyCal req" value="0" min="0" />
                <input type="hidden" id="rej_type" class="form-control numericOnly qtyCal req" value="0" min="0" />
                <input type="hidden" name="production_time" id="production_time" class="form-control numericOnly qtyCal req" value="<?= (!empty($dataRow->production_time)) ? floatVal($dataRow->production_time) : "0" ?>"  />
            </div>
            <div class="col-md-2 form-group">
                <label for="rej_by">Rejection By</label>
                <select id="rej_by" class="form-control single-select req">
                    <option value="1">Operator</option>
                    <option value="2">Setter</option>
                </select>
            </div>
            <div class="col-md-2 form-group">
                <label for="rej_reason">Rejection Reason</label>
                <select id="rej_reason" class="form-control single-select req">
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
                <label for="rej_from">Rejection From <span class="text-danger">*</span></label>
                <select id="rej_from" class="form-control single-select req">
                    <option value="">Select Rej. From</option>
                </select>
            </div>
            <div class="col-md-11 form-group">
                <label for="rej_remark">Rejection Remark</label>
                <input type="text" id="rej_remark" class="form-control" value="">
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
                                            <td>
                                                <?= $row->qty ?>
                                                <input type="hidden" name="rejection_reason[<?= $i ?>][rej_qty]" value="<?= $row->qty ?>">
                                            </td>
                                            <td>
                                                <?= $row->reason_name ?>
                                                <input type="hidden" name="rejection_reason[<?= $i ?>][rej_by]" value="<?= $row->rej_by ?>"> 
                                                <input type="hidden" name="rejection_reason[<?= $i ?>][rej_reason]" value="<?= $row->reason ?>"> 
                                                <input type="hidden" name="rejection_reason[<?= $i ?>][rejection_reason]" value="<?= $row->reason_name ?>">
                                            </td>
                                            <td>
                                                <?= $row->belongs_to_name ?>
                                                <input type="hidden" name="rejection_reason[<?= $i ?>][rej_stage]" value="<?= $row->belongs_to ?>"> 
                                                <input type="hidden" name="rejection_reason[<?= $i ?>][rej_stage_name]" value="<?= $row->belongs_to_name ?>">
                                            </td>
                                            <td>
                                                <?= $row->vendor_name ?>
                                                <input type="hidden" name="rejection_reason[<?= $i ?>][rej_from]" value="<?= $row->vendor_id ?>"> 
                                                <input type="hidden" name="rejection_reason[<?= $i ?>][rej_party_name]" value="<?= $row->vendor_name ?>">
                                            </td>
                                            <td>
                                                <?= $row->remark ?>
                                                <input type="hidden" name="rejection_reason[<?= $i ?>][rej_remark]" value="<?= $row->remark ?>">
                                            </td>
                                            <td>
                                                <button type="button" onclick="RemoveRejection(this)" style="margin-left:4px;" class="btn btn-outline-danger waves-effect waves-light text-center"><i class="ti-trash"></i></button>
                                            </td>
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

            <div class="error general_error col-md-12"></div>
            <hr style="width:100%">
            <div class="col-md-3 form-group">
                <label for="rw_qty">Rework Qty.</label>
                <input type="text" id="rw_qty" class="form-control numericOnly qtyCal req" value="" min="0" />
            </div>
            <div class="col-md-3 form-group">
                <label for="rw_reason">Rework Reason</label>
                <select id="rw_reason" class="form-control single-select req">
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
                <select id="rw_from" class="form-control single-select req">
                    <?php if (empty($dataRow->rewOption)) { ?> <option value="">Select Process</option> <?php } else {
                                                                                                        echo $dataRow->rewOption;
                                                                                                    } ?>
                </select>
            </div>

            <div class="col-md-11 form-group">
                <label for="rw_remark">Rework Remark</label>
                <input type="text" id="rw_remark" class="form-control" value="">
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
    </div>
</form>
<div class="modal fade" id="reworkToOKQtyModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content animated slideDown">
          
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 form-group">
                        <label for="accept_in_qty">OK Qty</label>
                        <input type="hidden" id="row_index">
                        <input type="hidden" id="rowData">
                        <input type="text" id="reworkOk_qty" class="form-control req numericOnly" value="0">
                        <input type="hidden" id="button">
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn waves-effect waves-light btn-outline-secondary closeModal"><i class="fa fa-times"></i> Close</button>
                <button type="button" class="btn waves-effect waves-light btn-outline-success acceptJob" onclick="saveReworkOkQty()"><i class="fa fa-check"></i> Save</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $(document).on("click", ".closeModal", function() {
            $('#reworkToOKQtyModel').modal('hide');
        });
    });
</script>
