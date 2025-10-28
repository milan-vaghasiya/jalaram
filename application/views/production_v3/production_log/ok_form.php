<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : ""; ?>" />
            <input type="hidden" name="prod_type" value="<?= (!empty($dataRow->prod_type)) ? $dataRow->prod_type : "1"; ?>" />
            <input type="hidden" name="log_type" value="0" />
            <input type="hidden" name="m_ct" id="m_ct" value="<?= (!empty($dataRow->m_ct)) ? $dataRow->m_ct : ""; ?>" />
            <input type="hidden" name="part_code" id="part_code" value="<?= (!empty($dataRow->item_code)) ? $dataRow->item_code : ""; ?>">
            <input type="hidden" id="part_id" name="product_id" value="<?= (!empty($dataRow->product_id)) ? $dataRow->product_id : ""; ?>">
            <input type="hidden" id="job_approval_id" name="job_approval_id" value="<?= (!empty($dataRow->job_approval_id)) ? $dataRow->job_approval_id : ""; ?>">
            <input type="hidden" name="job_card_id" id="job_card_id" value="<?= (!empty($dataRow->job_card_id)) ? $dataRow->job_card_id : ""; ?>">
            <input type="hidden" name="process_id" id="process_id" value="<?= (!empty($dataRow->process_id)) ? $dataRow->process_id : ""; ?>">

            
            <div class="col-md-2 form-group">
                <label for="log_date">Date</label>
                <input type="date" name="log_date" id="log_date" class="form-control req" min="<?=$startYearDate?>" max="<?=$maxDate?>"  value="<?= (!empty($dataRow->log_date)) ? date('Y-m-d', strtotime($dataRow->log_date)) : $maxDate; ?>" required>
            </div>
           
            <div class="col-md-2 form-group">
                <label for="cycle_time">Cycle Time <small>(In Sec.)</small> </label>
                <input type="text" name="cycle_time" id="cycle_time" class="form-control req floatOnly" value="<?= (!empty($dataRow->cycle_time)) ? $dataRow->cycle_time : ''; ?>">
            </div>
            <div class="col-md-2 form-group">
                <label for="load_unload_time">L./U.L. Time <small>(In Sec.)</small> </label>
                <input type="text" name="load_unload_time" id="load_unload_time" class="form-control req floatOnly" value="<?= (!empty($dataRow->load_unload_time)) ? $dataRow->load_unload_time : ''; ?>">
            </div>
            <hr style="width:100%">
            <div class="col-md-3 form-group">
                <label for="machine_id">Machine</label>
                <select name="machine_id" id="machine_id" class="form-control single-select req">
                    <?php if (empty($dataRow->machineOpt)) { ?> <option value="">Select Process</option> <?php } else {
                                                                                                            echo $dataRow->machineOpt;
                                                                                                        } ?>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="shift_id">Shift</label>
                <select name="shift_id" id="shift_id" class="form-control single-select11">
                    <option value="">Select Shift</option>
                    <?php
                    foreach ($shiftData as $row) :
                        $selected = (!empty($dataRow->shift_id) && $dataRow->shift_id == $row->id) ? "selected" : "";
                        $production_time = $row->production_hour * 60;
                        echo '<option value="' . $row->id . '" ' . $selected . ' data-production_time="' . $production_time . '">' . $row->shift_name . '</option>';
                    endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="operator_id">Operator</label>
                <select name="operator_id" id="operator_id" class="form-control single-select">
                    <option value="">Select Operator</option>
                    <?php
                    foreach ($operatorList as $row) :
                        $selected = (!empty($dataRow->operator_id) && $dataRow->operator_id == $row->id) ? "selected" : "";
                        echo '<option value="' . $row->id . '" ' . $selected . '>[' . $row->emp_code . '] ' . $row->emp_name . '</option>';
                    endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="production_time">Production Time(In Min.)</label>
                <input type="text" name="production_time" id="production_time" class="form-control numericOnly partCount " min="0" value="<?= (!empty($dataRow->production_time)) ? floatVal($dataRow->production_time) : "0" ?>">
            </div>
            <div class="col-md-3 form-group">
                <label for="start_part_count">Start Part Count</label>
                <input type="text" name="start_part_count" id="start_part_count" class="form-control numericOnly partCount req" min="0" value="<?= (!empty($dataRow->start_part_count)) ? floatVal($dataRow->start_part_count) : "0" ?>">
            </div>
            <div class="col-md-3 form-group">
                <label for="production_qty">Production Qty.</label>
                <input type="text" name="production_qty" id="production_qty" class="form-control numericOnly partCount  req" min="0" value="<?= (!empty($dataRow->production_qty)) ? floatVal($dataRow->production_qty) : "0" ?>">
                <input type="hidden" name="ok_qty" id="ok_qty">
                <div class="error general_error"></div>
            </div>
            <div class="col-md-3 form-group">
                <label for="end_part_count">End Part Count</label>
                <input type="text" name="end_part_count" id="end_part_count" class="form-control numericOnly req" min="0" value="<?= (!empty($dataRow->end_part_count)) ? floatVal($dataRow->end_part_count) : "0" ?>">
            </div>

            
        </div>

        <!-- Idle Time Section Start -->
        <div class="row form-group">
            <div class="col-md-2">
                <button type="button" class="btn btn-secondary btn-block" title="Click Me" data-toggle="collapse" href="#idle_time_section" role="button" aria-expanded="false" aria-controls="rework"> Idle Time Details</button>
            </div>
            <div class="col-md-10">
                <hr>
            </div>
        </div>

        <section class="collapse multi-collapse" id="idle_time_section">
            <div class="row">
                <div class="col-md-3 form-group">
                    <label for="idle_time">Idle Time (In Min.)</label>
                    <input type="text" id="idle_time" class="form-control numericOnly" value="0" />
                </div>
                <div class="col-md-4 form-group ">
                    <label for="idle_reason">Idle Reason</label>
                    <select id="idle_reason" class="form-control single-select req">
                        <option value="">Select Reason</option>
                        <?php
                        foreach ($idleReasonList as $row) :
                            $code = (!empty($row->code)) ? '[' . $row->code . '] - ' : '';
                            echo '<option value="' . $row->id . '" data-code="' . $row->code . '" data-reason="' . $row->remark . '" >' . $code . $row->remark . '</option>';
                        endforeach;
                        ?>
                    </select>
                </div>
                <div class="col-md-2 form-group">
                    <label for="">&nbsp;</label>
                    <button type="button" id="addIdleRow" class="btn btn-outline-info btn-block "><i class="fa fa-plus"></i> ADD</button>
                </div>
                <div class="col-md-12 form-group">
                    <div class="table-responsive">
                        <table id="idleReasons" class="table table-bordered">
                            <thead class="thead-info">
                                <tr>
                                    <th style="width:5%;">#</th>
                                    <th style="width:20%;">Idle Time (In Min.)</th>
                                    <th>Idle Reason</th>
                                    <th style="width:10%;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="idleReasonData">
                                <tr id="noData">
                                    <td colspan="4" class="text-center">No data available in table</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
        <!-- Idle time Section End -->
    </div>
</form>
<?php
if (!empty($dataRow->idle_reason)) :
    $idle_reason = json_decode($dataRow->idle_reason);
    if (!empty($idle_reason)) :
        foreach ($idle_reason as $row) :
            echo "<script>AddRowIdle(" . json_encode($row) . ");</script>";
        endforeach;
    endif;
endif;

if (!empty($dataRow->rej_reason)) :
    $rej_reason = json_decode($dataRow->rej_reason);
    if (!empty($rej_reason)) :
        foreach ($rej_reason as $row) :
            echo "<script>AddRowRejection(" . json_encode($row) . ");</script>";
        endforeach;
    endif;
endif;

if (!empty($dataRow->rw_reason)) :
    $rw_reason = json_decode($dataRow->rw_reason);
    if (!empty($rw_reason)) :
        foreach ($rw_reason as $row) :
            echo "<script>AddRowRework(" . json_encode($row) . ");</script>";
        endforeach;
    endif;
endif;
?>