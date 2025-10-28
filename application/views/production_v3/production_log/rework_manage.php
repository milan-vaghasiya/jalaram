<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-12">
                                <h4 class="card-title">Rework Management - <?=$pageTitle?></h4>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <div class="col-md-12">
                                <div class="row">
                                    <input type="hidden" name="id" id="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : ""; ?>" />
                                    <input type="hidden" name="job_card_id" id="job_card_id" value="<?= (!empty($dataRow->job_card_id) ? $dataRow->job_card_id : '') ?>">
                                    <input type="hidden" name="job_approval_id" id="job_approval_id" value="<?= (!empty($dataRow->job_approval_id) ? $dataRow->job_approval_id : '') ?>">
                                    <input type="hidden" name="process_id" id="process_id" value="<?= (!empty($dataRow->process_id) ? $dataRow->process_id : '') ?>">
                                    <input type="hidden" id="part_id" value="">

                                    <div class="col-md-12 form-group">
                                        <div class="table-responsive">
                                            <table id="commanTable" class="table table-bordered">
                                                <thead class="thead-info">
                                                    <tr>
                                                        <th style="width:5%;">#</th>
                                                        <th>Rework Qty.</th>
                                                        <th>Rework Reason</th>
                                                        <th>Rework Belong To</th>
                                                        <th>Rework From</th>
                                                        <th>Rework Remark</th>
                                                        <!-- <th>Ok Qty</th> -->
                                                        <th style="width:10%;">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="reworkReasonData">
                                                    <?php
                                                    if (!empty($rejRwData)) :
                                                        $j = 1;
                                                        $k = 0;
                                                        foreach ($rejRwData as $row) :
                                                            $hidden = ($row->qty == 0) ? 'hidden' : '';
                                                            
                                                            $rowData = json_encode($row);

                                                            echo '<tr ' . $hidden . '>
                                                                    <td>' . (($row->qty > 0) ? $j++ : '') . '  </td>
                                                                    <td id="rw_qty_html' . $row->id . '">' . $row->qty . '  </td>
                                                                    <td>' . $row->reason_name . ' </td>
                                                                    <td>' . $row->belongs_to_name . '</td>
                                                                    <td>' . $row->vendor_name . ' </td>
                                                                    <td>' . $row->remark . '</td>';
                                                            echo  "<td class='text-center'>
                                                                    <button type='button' onclick='convertToOKQty(" . $rowData . ",this)' style='margin-left:2px;' class='btn btn-outline-success waves-effect waves-light' datatip='Ok Qty'><i class='ti-check'></i></button>
                                                                    <button type='button' onclick='addRejQty(" . $rowData . ")' style='margin-left:2px;' class='btn btn-outline-warning waves-effect waves-light' datatip='Rejection Qty'><i class='ti-close'></i></button>
                                                                    </td>
                                                                </tr>";
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
                                                <div class="col-md-12 form-group">
                                                    <label for="remark">Rejection Remark</label>
                                                    <input type="text" id="remark" class="form-control" value="">
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

                            <div class="modal fade" id="reworkToRejQtyModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content animated slideDown">
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="exampleModalLabel1" style="width:100%;">Rejection</h4>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        </div>
                                        <div class="modal-body">

                                            <div class="error general_error col-md-12"></div>
                                            <div class="row">
                                                <div class="col-md-3 form-group">
                                                    <label for="rej_qty">Rejected Qty.</label>
                                                    <input type="text" id="rej_qty" class="form-control numericOnly qtyCal req" value="" min="0" />
                                                    <input type="hidden" id="rej_ref_id" class="form-control numericOnly qtyCal req" value="0" min="0" />
                                                    <input type="hidden" id="rej_type" class="form-control numericOnly qtyCal req" value="0" min="0" />
                                                    <input type="hidden" id="rowData">
                                                </div>
                                                <div class="col-md-3 form-group">
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
                                                <div class="col-md-12 form-group">
                                                    <label for="rej_remark">Rejection Remark</label>
                                                    <input type="text" id="rej_remark" class="form-control" value="">
                                                </div>
                                               
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn waves-effect waves-light btn-outline-secondary closeModal"><i class="fa fa-times"></i> Close</button>
                                            <button type="button" class="btn waves-effect waves-light btn-outline-success" onclick="saveRejectionQty()"><i class="fa fa-check"></i> Save</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>

<!-- <script src="<?php echo base_url(); ?>assets/js/custom/rejection-log.js?v=<?= time() ?>"></script> -->
<script>
$(document).ready(function() {
    $(document).on("click", ".closeModal", function() {
        $('#reworkToOKQtyModel').modal('hide');
        $('#reworkToRejQtyModel').modal('hide');
    });
    initDataTable();

    $(document).on("change", "#rejection_stage", function () {
        var process_id = $(this).val();
        var part_id = $("#part_id").val();
        if (process_id) {
            var job_card_id = $("#job_card_id").val();
            $.ajax({
                url: base_url + controller + '/getRejFrom',
                type: 'post',
                data: {
                    process_id: process_id,
                    part_id: part_id,
                    job_card_id: job_card_id
                },
                dataType: 'json',
                success: function (data) {
                    $("#rej_from").html("");
                    $("#rej_from").html(data.rejOption);
                    $("#rej_from").comboSelect();
                }
            });
        } else{
            $("#rej_from").html("<option value=''>Select Rej. From</option>");
            $("#rej_from").comboSelect();
        }
    });
});
    
function convertToOKQty(data, button) {
    var row_index = $(button).closest("tr").index();
    $("#row_index").val(row_index);
    $("#rowData").val(JSON.stringify(data));
    $("#reworkToOKQtyModel").modal();
}

function saveReworkOkQty () {
    var rowData = JSON.parse($("#rowData").val());
    
    var rw_qty = $("#reworkOk_qty").val();
    var remark = $("#remark").val();
    var row_index = $("#row_index").val();
    var log_id = $("#id").val();
    var job_card_id = $("#job_card_id").val();
   

    if (parseFloat(rw_qty) > parseFloat(rowData.qty) || rw_qty == 0) {
        $(".reworkOk_qty").html("Invalid Qty");
    } else {

        var postData = {
            rw_qty: rw_qty,
            rw_reason: rowData.reason,
            rw_from: rowData.vendor_id,
            rework_reason: rowData.reason_name,
            rw_remark:remark,
            rw_party_name: rowData.vendor_name,
            rw_stage: rowData.belongs_to,
            rw_stage_name: rowData.belongs_to_name,
            row_index: row_index,
            log_id: log_id,
            job_card_id: job_card_id,
            trans_id: rowData.id
        };

        $.ajax({
            url: base_url + controller + '/saveReworkQty',
            data: postData,
            type: "POST",
            dataType: "json",
        }).done(function(data) {
            if (data.status === 0) {
                $(".error").html("");
                $.each(data.message, function(key, value) {
                    $("." + key).html(value);
                });
            } else if (data.status == 1) {
                $('#reworkToOKQtyModel').modal('hide');
                window.location.reload();
                toastr.success(data.message, 'Success', {
                    "showMethod": "slideDown",
                    "hideMethod": "slideUp",
                    "closeButton": true,
                    positionClass: 'toastr toast-bottom-center',
                    containerId: 'toast-bottom-center',
                    "progressBar": true
                });
            } else {
                initTable(srposition);
                $('#' + formId)[0].reset();
                $(".modal").modal('hide');
                toastr.error(data.message, 'Error', {
                    "showMethod": "slideDown",
                    "hideMethod": "slideUp",
                    "closeButton": true,
                    positionClass: 'toastr toast-bottom-center',
                    containerId: 'toast-bottom-center',
                    "progressBar": true
                });
            }

        });
    }
}

function addRejQty(rowData) {
    $("#rowData").val(JSON.stringify(rowData)); console.log(rowData);
    $("#rej_qty").val(rowData.qty);
    $("#rej_ref_id").val(rowData.id);
    $("#rej_type").val(1);

    $("#rej_reason").val(rowData.reason);
    $("#rej_reason").comboSelect();

    $("#rejection_stage").val(rowData.belongs_to);
    $("#rejection_stage").comboSelect();
    $("#rejection_stage").trigger('change');
    
    $("#rej_from").val(rowData.vendor_id);
    $("#rej_from").comboSelect();
    $("#reworkToRejQtyModel").modal();
}

function saveRejectionQty() {
    var rowData = JSON.parse($("#rowData").val());
    console.log(rowData);
    var row_index = $("#row_index").val();
    var log_id = $("#id").val();
    var job_card_id = $("#job_card_id").val();
    var job_approval_id = $("#job_approval_id").val();
    var rej_qty = $("#rej_qty").val();
    var rej_ref_id = $("#rej_ref_id").val();
    var rej_type = $("#rej_type").val();
    var rej_reason = $("#rej_reason :selected").val();
    var rej_from = $("#rej_from :selected").val();
    var rejection_reason = $("#rej_reason :selected").data('reason');
    var rej_party_name = $("#rej_from :selected").data('party_name');
    var rej_remark = $("#rej_remark").val();
    var rej_stage = $("#rejection_stage").val();
    var rej_stage_name = $("#rejection_stage :selected").data('process_name');
    
    if (parseFloat(rej_qty) > parseFloat(rowData.qty) || rej_qty == '' || rej_qty == 0) {
        $(".rej_qty").html("Invalid Qty");
    }
    else if (rej_reason == '') {
        $(".rej_reason").html("Rejection required");
    }
    else {

        var process_id = $("#process_id").val();
        var postData = {
            rej_qty: rej_qty,
            rej_reason: rej_reason,
            rej_from: rej_from,
            rejection_reason_name: rejection_reason,
            rej_remark: rej_remark,
            rej_party_name: rej_party_name,
            rej_stage: rej_stage,
            rej_stage_name: rej_stage_name,
            trans_id: rowData.trans_id,
            rej_ref_id: rej_ref_id,
            rej_type: rej_type,
            job_card_id: rowData.job_card_id,
            job_approval_id: job_approval_id,
            id: rowData.log_id,
            process_id: process_id
        };
        // console.log(postData);
        $.ajax({
            url: base_url + controller + '/saveRejectionQty',
            data: postData,
            type: "POST",
            dataType: "json",
        }).done(function (data) {
            if (data.status === 0) {
                $(".error").html("");
                $.each(data.message, function (key, value) {
                    $("." + key).html(value);
                });
            } else if (data.status == 1) {
                // var reworkQty = rowData.rw_qty - rej_qty;
                // $("#rej_qty" + rowData.trans_id).val(reworkQty);
                // $("#rw_qty" + rowData.trans_id).val(reworkQty);
                // $("#rw_qty_html" + rowData.trans_id).html(reworkQty);
                $('#reworkToOKQtyModel').modal('hide');
                window.location.reload();
                toastr.success(data.message, 'Success', {
                    "showMethod": "slideDown",
                    "hideMethod": "slideUp",
                    "closeButton": true,
                    positionClass: 'toastr toast-bottom-center',
                    containerId: 'toast-bottom-center',
                    "progressBar": true
                });
            } else {
                initTable(srposition);
                $('#' + formId)[0].reset();
                $(".modal").modal('hide');
                toastr.error(data.message, 'Error', {
                    "showMethod": "slideDown",
                    "hideMethod": "slideUp",
                    "closeButton": true,
                    positionClass: 'toastr toast-bottom-center',
                    containerId: 'toast-bottom-center',
                    "progressBar": true
                });
            }

        });
    }
}

</script>