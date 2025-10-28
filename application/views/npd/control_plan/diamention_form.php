<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h4>
                            <u>Control Plan Dimension</u>
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="col-md-12">
                            <form id="fmeaForm">
                                <div class="row">
                                    <input type="hidden" name="trans_main_id" value="<?= !empty($cpData->id) ? $cpData->id : '' ?>">
                                    <input type="hidden" name="pfc_id" value="<?= !empty($cpData->ref_id) ? $cpData->ref_id : '' ?>">
                                    <input type="hidden" name="process_no" value="<?= !empty($cpData->process_no) ? $cpData->process_no : '' ?>">
                                    <input type="hidden" name="item_id" value="<?= !empty($cpData->item_id) ? $cpData->item_id : '' ?>">
                                    <input type="hidden" name="pfc_main_id" value="<?= !empty($cpData->pfc_main_id) ? $cpData->pfc_main_id : '' ?>">
                                    <div class="col-md-12 form-group">
                                        <div class="table-responsive">
                                            <table class="table jpExcelTable">
                                                <tr class="text-center" style="background:#eee;">
                                                    <th>Item</th>
                                                    <th>CP Number</th>
                                                    <th>Process No</th>
                                                    <th>Operation</th>
                                                </tr>
                                                <tr>
                                                    <td><?=$cpData->item_code.' '.$cpData->item_name?></td>
                                                    <td><?=$cpData->trans_number?></td>
                                                    <td><?=$cpData->process_no?></td>
                                                    <td><?=$cpData->product_param?></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-md-12 form-group">
                                        <button type="button" class="btn btn-outline-success btn-save float-right mt-30" onclick="addRow()"><i class="fa fa-plus"></i> Add</button>
                                    </div>
                                    <div class="error general_error"></div>
                                    <div class="table-responsive" style="height:50vh;overflow-y:scroll;">
                                        <table id="pfctbl" class="table jpExcelTable" style="font-size: 12px !important;" >
                                            <thead class="thead-info text-center">
                                                <tr>
                                                    <th rowspan="2">#</th>
                                                    <th rowspan="2" style="min-width:200px !important;">Product</th>
                                                    <th rowspan="2" style="min-width:200px !important;">Process</th>
                                                    <th rowspan="2" style="min-width:200px !important;">Special Char. Class</th>
                                                    <th rowspan="2" style="min-width:200px !important;">Type</th>
                                                    <th rowspan="2" style="min-width:200px !important;">Product / Process,Specification / Tolerance</th>
                                                    <th colspan="5" style="min-width:800px !important;">Operator</th>
                                                    <th colspan="5" style="min-width:800px !important;">IIR</th>
                                                    <th colspan="5" style="min-width:800px !important;">IPR</th>
                                                    <th colspan="5" style="min-width:800px !important;">SAR</th>
                                                    <th colspan="5" style="min-width:800px !important;">SPC</th>
                                                    <th colspan="5" style="min-width:800px !important;">FIR</th>
                                                    <th rowspan="2" style="min-width:50px !important;">Action</th>
                                                
                                                </tr>
                                                <tr>
                                                    <th>Measur. Tech.</th>
                                                    <th>Size</th>	
                                                    <th>Freq</th>
                                                    <th>Time</th>
                                                    <th>Freq Text</th>

                                                    <th>Measur. Tech.</th>
                                                    <th>Size</th>	
                                                    <th>Freq</th>
                                                    <th>Time</th>
                                                    <th>Freq Text</th>

                                                    <th>Measur. Tech.</th>
                                                    <th>Size</th>	
                                                    <th>Freq</th>
                                                    <th>Time</th>
                                                    <th>Freq Text</th>

                                                    <th>Measur. Tech.</th>
                                                    <th>Size</th>	
                                                    <th>Freq</th>
                                                    <th>Time</th>
                                                    <th>Freq Text</th>

                                                    <th>Measur. Tech.</th>
                                                    <th>Size</th>	
                                                    <th>Freq</th>
                                                    <th>Time</th>
                                                    <th>Freq Text</th>

                                                    <th>Measur. Tech.</th>
                                                    <th>Size</th>	
                                                    <th>Freq</th>
                                                    <th>Time</th>
                                                    <th>Freq Text</th>

                                                </tr>
                                            </thead>
                                            <tbody id="cpTbody">
                                                
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="col-md-12">
                            <button type="button" class="btn waves-effect waves-light btn-outline-success float-right save-form" onclick="saveCPDimension('fmeaForm','saveCPDimension');"><i class="fa fa-check"></i> Save</button>
                            <a href="<?= base_url($headData->controller . '/controlPlanList/' . $cpData->pfc_main_id ) ?>" class="btn waves-effect waves-light btn-outline-secondary float-right save-form" style="margin-right:10px;"><i class="fa fa-times"></i> Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<?php $this->load->view('includes/footer'); ?>

<script>
    $(document).ready(function() {

        // $(".symbol-select").select2({
        //     templateResult: formatSymbol
        // });

        $(document).on('change', '#item_id', function() {
            var item_id = $(this).val();
            var itemData = $(this).find(":selected").data('row');
            var process_no = ($("#pfc_id").val()) ? $("#pfc_id").find(":selected").data('process_no') : '';
            var trans_number = 'FMEA/' + itemData.item_code + '/' + ((itemData.rev_no != null) ? itemData.rev_no : '') + '/' + process_no;
            $("#trans_number").val(trans_number);
            $.ajax({
                url: base_url + controller + '/getItemWisePfcData',
                data: {
                    item_id: item_id
                },
                type: "POST",
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    $("#pfc_id").html(data.options);
                    $("#pfc_id").comboSelect();
                }
            });
        });

        $(document).on('change', '#pfc_id', function() {
            var item_id = $(this).val();
            var itemData = $("#item_id").find(":selected").data('row');
            var process_no = $("#pfc_id").find(":selected").data('process_no');
            var trans_number = 'FMEA/' + itemData.item_code + '/' + ((itemData.rev_no != null) ? itemData.rev_no : '') + '/' + process_no;
            $("#trans_number").val(trans_number);
            $("#process_no").val(process_no);
        });


        $(document).on('change', ".requirement", function() {
            var countRow = $(this).find(":selected").data('count_row');
            var requirement = $(this).val();
            if (requirement == 1) {
                $('#min_req' + countRow).show();
                $('#max_req' + countRow).show();
                $('#other_req' + countRow).show();
            } else if (requirement == 2) {
                $('#min_req' + countRow).show();
                $('#max_req' + countRow).hide();
                $('#other_req' + countRow).show();
            } else if (requirement == 3) {
                $('#min_req' + countRow).hide();
                $('#max_req' + countRow).show();
                $('#other_req' + countRow).show();
            } else if (requirement == 4) {
                $('#min_req' + countRow).hide();
                $('#max_req' + countRow).hide();
                $('#other_req' + countRow).show();
            }
        });
    });


    function saveCPDimension(formId, fnsave) {
        // var fd = $('#'+formId).serialize();

        if (fnsave == "" || fnsave == null) {
            fnsave = "save";
        }
        var form = $('#' + formId)[0];
        var fd = new FormData(form);
        $.ajax({
            url: base_url + controller + '/' + fnsave,
            data: fd,
            type: "POST",
            processData: false,
            contentType: false,
            dataType: "json",
        }).done(function(data) {
            if (data.status === 0) {
                $(".errsor").html("");
                $.each(data.message, function(key, value) {
                    $("." + key).html(value);
                });
            } else if (data.status == 1) {
                toastr.success(data.message, 'Success', {
                    "showMethod": "slideDown",
                    "hideMethod": "slideUp",
                    "closeButton": true,
                    positionClass: 'toastr toast-bottom-center',
                    containerId: 'toast-bottom-center',
                    "progressBar": true
                });
                window.location = data.url;
                $("#parameter").val("");
                $("#requirement").val("");
                $("#requirement").comboSelect();
                $("#min_req").val("");
                $("#max_req").val("");
                $("#other_req").val("");

                $("#fmeaTbody").html("");

            } else {
                initTable(0);
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

        
    function addRow(data = {}){
        $('table#pfctbl tr#noData').remove()
        //Get the reference of the Table's TBODY element.
        var tblName = "pfctbl";

        var tBody = $("#" + tblName + " > TBODY")[0];

        //Add Row.
        row = tBody.insertRow(-1);

        //Add index cell
        var countRow = $('#' + tblName + ' tbody tr:last').index() + 1;
        cell = $(row.insertCell(-1));
        cell.html(countRow);
        var idIP = $("<input/>", {
            type: "hidden",
            name: "trans_id[]",
            value: data.id,
        });
        cell.append(idIP);
        
        var productParamIP = $("<input/>", {
            type: "text",
            name: "product_param[]",
            value: data.product_param,
            class: "form-control"
        });
        cell = $(row.insertCell(-1));
        cell.html(productParamIP);
        cell.append("<div class='error parameter" + countRow + "'></div>");

        var processParamIP = $("<input/>", {
            type: "text",
            name: "process_param[]",
            value: data.process_param,
            class: "form-control"
        });
        cell = $(row.insertCell(-1));
        cell.html(processParamIP);
        cell.append(idIP);

        var classIP = $("<select/>", {
            type: "text",
            name: "char_class[]",
            class: "form-control symbol-select"
        });
        var classArray = <?php echo json_encode($classArray); ?>;
        $.each(classArray, function(key, value) {
            if (key == '') {
                classIP.append('<option value="">Select Class</option>');
            } else {
                selected = (data.char_class == key) ? 'selected' : '';
                var options = '<option value="' + key + '" data-img_path="' + base_url + '/assets/images/symbols/' + key + '.png")" ' + selected + '>' + value + '</option>';
                classIP.append(options);
            }
        });

        cell = $(row.insertCell(-1));
        cell.html(classIP);

        var options = '<option value="">Select Requirement</option><option value="1" '+((data.requirement == 1) ? 'selected' : '')+' data-count_row = "' + countRow + '" >Range</option><option value="2" '+((data.requirement == 2) ? 'selected' : '')+' data-count_row = "' + countRow + '">Minimum</option><option value="3" '+((data.requirement == 3) ? 'selected' : '')+' data-count_row = "' + countRow + '">Maximum</option><option value="4" '+((data.requirement == 4) ? 'selected' : '')+' data-count_row = "' + countRow + '">Other</option>';
        var requirementIP = $("<select/>", {
            type: "text",
            name: "requirement[]",
            value: data.requirement,
            class: "form-control requirement"
        }).append(options);
        cell = $(row.insertCell(-1));
        cell.html(requirementIP);
        cell.append("<div class='error requirement" + countRow + "'></div>");

       

        var other_reqIP = $("<input/>", {
            type: "text",
            name: "other_req[]",
            value: data.other_req,
            id: "other_req"+countRow,
            class: "form-control other_req other_req" + countRow
        });
        cell = $(row.insertCell(-1));
        cell.html(other_reqIP);
        cell.append("<div class='error other_req" + countRow + "'></div>");

        /**** Operator */
        var oprMeasurTechIP = $("<input/>", {
            type: "text",
            name: "opr_measur_tech[]",
            value: data.opr_measur_tech,
            id: ""+countRow,
            class: "form-control  " + countRow
        });
        cell = $(row.insertCell(-1));
        cell.html(oprMeasurTechIP);
        cell.append("<div class='error other_req" + countRow + "'></div>");

        var oprSizeIP = $("<input/>", {
            type: "text",
            name: "opr_size[]",
            value: data.opr_size,
            id: ""+countRow,
            class: "form-control  " + countRow
        });
        cell = $(row.insertCell(-1));
        cell.html(oprSizeIP);
        cell.append("<div class='error other_req" + countRow + "'></div>");

        var oprFreqIP = $("<input/>", {
            type: "text",
            name: "opr_freq[]",
            value: data.opr_freq,
            id: ""+countRow,
            class: "form-control  " + countRow
        });
        cell = $(row.insertCell(-1));
        cell.html(oprFreqIP);
        cell.append("<div class='error other_req" + countRow + "'></div>");

    //    var selected = '';
    //    if((data.opr_freq_time == 'Hrs')){ selected = 'selected'; }
    //    else if((data.opr_freq_time == '%')){ selected = 'selected'; }
    //    else if((data.opr_freq_time == 'Lot')){ selected = 'selected'; }
    //    else if((data.opr_freq_time == 'Setup')){ selected = 'selected'; }
    //     var options = '<option value="">Select</option><option value="Hrs"  data-count_row = "' + countRow + '" '+selected+' >Hrs</option><option value="%"  data-count_row = "' + countRow + '" '+selected+'>%</option><option value="Lot" '+selected+'  data-count_row = "' + countRow + '">Lot</option><option value="Setup" '+selected+'  data-count_row = "' + countRow + '">Setup</option>';
        var options = '<option value="">Select</option><option value="Hrs" '+((data.opr_freq_time == 'Hrs')?'selected':'')+'  data-count_row = "' + countRow + '" >Hrs</option><option value="%" '+((data.opr_freq_time == '%')?'selected':'')+' data-count_row = "' + countRow + '">%</option><option value="Lot" '+((data.opr_freq_time == 'Lot')?'selected':'')+' data-count_row = "' + countRow + '">Lot</option><option value="Setup" '+((data.opr_freq_time == 'Setup')?'selected':'')+' data-count_row = "' + countRow + '">Setup</option>';
        var oprFreqTimeIP = $("<select/>", {
            type: "text",
            name: "opr_freq_time[]",
            value: data.opr_freq_time,
            class: "form-control "
        }).append(options);
        cell = $(row.insertCell(-1));
        cell.html(oprFreqTimeIP);
        cell.append("<div class='error other_req" + countRow + "'></div>");

        var oprFreqTextIP = $("<input/>", {
            type: "text",
            name: "opr_freq_text[]",
            value: data.opr_freq_text,
            id: ""+countRow,
            class: "form-control  " + countRow
        });
        cell = $(row.insertCell(-1));
        cell.html(oprFreqTextIP);
        cell.append("<div class='error other_req" + countRow + "'></div>");

        /** Operator End */

        /**** IIR */
        var iirMeasurTechIP = $("<input/>", {
            type: "text",
            name: "iir_measur_tech[]",
            value: data.iir_measur_tech,
            id: ""+countRow,
            class: "form-control  " + countRow
        });
        cell = $(row.insertCell(-1));
        cell.html(iirMeasurTechIP);
        cell.append("<div class='error other_req" + countRow + "'></div>");

        var iirSizeIP = $("<input/>", {
            type: "text",
            name: "iir_size[]",
            value: data.iir_size,
            id: ""+countRow,
            class: "form-control  " + countRow
        });
        cell = $(row.insertCell(-1));
        cell.html(iirSizeIP);
        cell.append("<div class='error other_req" + countRow + "'></div>");

        var iirFreqIP = $("<input/>", {
            type: "text",
            name: "iir_freq[]",
            value: data.iir_freq,
            id: ""+countRow,
            class: "form-control  " + countRow
        });
        cell = $(row.insertCell(-1));
        cell.html(iirFreqIP);
        cell.append("<div class='error other_req" + countRow + "'></div>");

        // var selected = '';
        // if((data.iir_freq_time == 'Hrs')){ selected = 'selected'; }
        // else if((data.iir_freq_time == '%')){ selected = 'selected'; }
        // else if((data.iir_freq_time == 'Lot')){ selected = 'selected'; }
        // else if((data.iir_freq_time == 'Setup')){ selected = 'selected'; }
        // var options = '<option value="">Select</option><option value="Hrs" '+selected+'  data-count_row = "' + countRow + '" >Hrs</option><option value="%" '+selected+'  data-count_row = "' + countRow + '">%</option><option value="Lot"  '+selected+' data-count_row = "' + countRow + '">Lot</option><option value="Setup" '+selected+'  data-count_row = "' + countRow + '">Setup</option>';
        var options = '<option value="">Select</option><option value="Hrs" '+((data.iir_freq_time == 'Hrs')?'selected':'')+'  data-count_row = "' + countRow + '" >Hrs</option><option value="%" '+((data.iir_freq_time == '%')?'selected':'')+' data-count_row = "' + countRow + '">%</option><option value="Lot" '+((data.iir_freq_time == 'Lot')?'selected':'')+' data-count_row = "' + countRow + '">Lot</option><option value="Setup" '+((data.iir_freq_time == 'Setup')?'selected':'')+' data-count_row = "' + countRow + '">Setup</option>';
        var iirFreqTimeIP = $("<select/>", {
            type: "text",
            name: "iir_freq_time[]",
            value: data.iir_freq_time,
            class: "form-control "
        }).append(options);
        cell = $(row.insertCell(-1));
        cell.html(iirFreqTimeIP);
        cell.append("<div class='error other_req" + countRow + "'></div>");

        var iirFreqTextIP = $("<input/>", {
            type: "text",
            name: "iir_freq_text[]",
            value: data.iir_freq_text,
            id: ""+countRow,
            class: "form-control  " + countRow
        });
        cell = $(row.insertCell(-1));
        cell.html(iirFreqTextIP);
        cell.append("<div class='error other_req" + countRow + "'></div>");

        /** IIR End */

        /**** IPR */
        var iprMeasurTechIP = $("<input/>", {
            type: "text",
            name: "ipr_measur_tech[]",
            value: data.ipr_measur_tech,
            id: ""+countRow,
            class: "form-control  " + countRow
        });
        cell = $(row.insertCell(-1));
        cell.html(iprMeasurTechIP);
        cell.append("<div class='error other_req" + countRow + "'></div>");

        var iprSizeIP = $("<input/>", {
            type: "text",
            name: "ipr_size[]",
            value: data.ipr_size,
            id: ""+countRow,
            class: "form-control  " + countRow
        });
        cell = $(row.insertCell(-1));
        cell.html(iprSizeIP);
        cell.append("<div class='error other_req" + countRow + "'></div>");

        var iprFreqIP = $("<input/>", {
            type: "text",
            name: "ipr_freq[]",
            value: data.ipr_freq,
            id: ""+countRow,
            class: "form-control  " + countRow
        });
        cell = $(row.insertCell(-1));
        cell.html(iprFreqIP);
        cell.append("<div class='error other_req" + countRow + "'></div>");

        var selected = '';
        // console.log(data.ipr_freq_time);
        // if((data.ipr_freq_time == 'Hrs')){ selected = 'selected';  console.log('Hrs');}
        // else if((data.ipr_freq_time == '%')){ selected = 'selected'; console.log('%'); }
        // else if((data.ipr_freq_time == 'Lot')){ selected = 'selected'; console.log('Lot');}
        // else if((data.ipr_freq_time == 'Setup')){ selected = 'selected'; console.log('Setup');}
        var options = '<option value="">Select</option><option value="Hrs" '+((data.ipr_freq_time == 'Hrs')?'selected':'')+'  data-count_row = "' + countRow + '" >Hrs</option><option value="%" '+((data.ipr_freq_time == '%')?'selected':'')+' data-count_row = "' + countRow + '">%</option><option value="Lot" '+((data.ipr_freq_time == 'Lot')?'selected':'')+' data-count_row = "' + countRow + '">Lot</option><option value="Setup" '+((data.ipr_freq_time == 'Setup')?'selected':'')+' data-count_row = "' + countRow + '">Setup</option>';
        var iprFreqTimeIP = $("<select/>", {
            type: "text",
            name: "ipr_freq_time[]",
            value: data.ipr_freq_time,
            class: "form-control "
        }).append(options);
        cell = $(row.insertCell(-1));
        cell.html(iprFreqTimeIP);
        cell.append("<div class='error other_req" + countRow + "'></div>");

        var iprFreqTextIP = $("<input/>", {
            type: "text",
            name: "ipr_freq_text[]",
            value: data.ipr_freq_text,
            id: ""+countRow,
            class: "form-control  " + countRow
        });
        cell = $(row.insertCell(-1));
        cell.html(iprFreqTextIP);
        cell.append("<div class='error other_req" + countRow + "'></div>");

        /** IPR End */

        /**** SAR */
        var sarMeasurTechIP = $("<input/>", {
            type: "text",
            name: "sar_measur_tech[]",
            value: data.sar_measur_tech,
            id: ""+countRow,
            class: "form-control  " + countRow
        });
        cell = $(row.insertCell(-1));
        cell.html(sarMeasurTechIP);
        cell.append("<div class='error other_req" + countRow + "'></div>");

        var sarSizeIP = $("<input/>", {
            type: "text",
            name: "sar_size[]",
            value: data.sar_size,
            id: ""+countRow,
            class: "form-control  " + countRow
        });
        cell = $(row.insertCell(-1));
        cell.html(sarSizeIP);
        cell.append("<div class='error other_req" + countRow + "'></div>");

        var sarFreqIP = $("<input/>", {
            type: "text",
            name: "sar_freq[]",
            value: data.sar_freq,
            id: ""+countRow,
            class: "form-control  " + countRow
        });
        cell = $(row.insertCell(-1));
        cell.html(sarFreqIP);
        cell.append("<div class='error other_req" + countRow + "'></div>");

        // var selected = '';
        // if((data.sar_freq_time == 'Hrs')){ selected = 'selected'; }
        // else if((data.sar_freq_time == '%')){ selected = 'selected'; }
        // else if((data.sar_freq_time == 'Lot')){ selected = 'selected'; }
        // else if((data.sar_freq_time == 'Setup')){ selected = 'selected'; }
        // var options = '<option value="">Select</option><option value="Hrs" '+selected+' data-count_row = "' + countRow + '" >Hrs</option><option value="%" '+selected+' data-count_row = "' + countRow + '">%</option><option value="Lot" '+selected+'  data-count_row = "' + countRow + '">Lot</option><option value="Setup" '+selected+'  data-count_row = "' + countRow + '">Setup</option>';
        var options = '<option value="">Select</option><option value="Hrs" '+((data.sar_freq_time == 'Hrs')?'selected':'')+'  data-count_row = "' + countRow + '" >Hrs</option><option value="%" '+((data.sar_freq_time == '%')?'selected':'')+' data-count_row = "' + countRow + '">%</option><option value="Lot" '+((data.sar_freq_time == 'Lot')?'selected':'')+' data-count_row = "' + countRow + '">Lot</option><option value="Setup" '+((data.sar_freq_time == 'Setup')?'selected':'')+' data-count_row = "' + countRow + '">Setup</option>';

        var sarFreqTimeIP = $("<select/>", {
            type: "text",
            name: "sar_freq_time[]",
            value: data.sar_freq_time,
            class: "form-control "
        }).append(options);
        cell = $(row.insertCell(-1));
        cell.html(sarFreqTimeIP);
        cell.append("<div class='error other_req" + countRow + "'></div>");

        var sarFreqTextIP = $("<input/>", {
            type: "text",
            name: "sar_freq_text[]",
            value: data.sar_freq_text,
            id: ""+countRow,
            class: "form-control  " + countRow
        });
        cell = $(row.insertCell(-1));
        cell.html(sarFreqTextIP);
        cell.append("<div class='error other_req" + countRow + "'></div>");

        /** SAR End */


        /**** SPC */
        var spcMeasurTechIP = $("<input/>", {
            type: "text",
            name: "spc_measur_tech[]",
            value: data.spc_measur_tech,
            id: ""+countRow,
            class: "form-control  " + countRow
        });
        cell = $(row.insertCell(-1));
        cell.html(spcMeasurTechIP);
        cell.append("<div class='error other_req" + countRow + "'></div>");

        var spcSizeIP = $("<input/>", {
            type: "text",
            name: "spc_size[]",
            value: data.spc_size,
            id: ""+countRow,
            class: "form-control  " + countRow
        });
        cell = $(row.insertCell(-1));
        cell.html(spcSizeIP);
        cell.append("<div class='error other_req" + countRow + "'></div>");

        var spcFreqIP = $("<input/>", {
            type: "text",
            name: "spc_freq[]",
            value: data.spc_freq,
            id: ""+countRow,
            class: "form-control  " + countRow
        });
        cell = $(row.insertCell(-1));
        cell.html(spcFreqIP);
        cell.append("<div class='error other_req" + countRow + "'></div>");


        // var selected = '';
        // if((data.spc_freq_time == 'Hrs')){ selected = 'selected'; }
        // else if((data.spc_freq_time == '%')){ selected = 'selected'; }
        // else if((data.spc_freq_time == 'Lot')){ selected = 'selected'; }
        // else if((data.spc_freq_time == 'Setup')){ selected = 'selected'; }
        // var options = '<option value="">Select</option><option value="Hrs" '+selected+' data-count_row = "' + countRow + '" >Hrs</option><option value="%" '+selected+' data-count_row = "' + countRow + '">%</option><option value="Lot" '+selected+' data-count_row = "' + countRow + '">Lot</option><option value="Setup" '+selected+' data-count_row = "' + countRow + '">Setup</option>';

        var options = '<option value="">Select</option><option value="Hrs" '+((data.spc_freq_time == 'Hrs')?'selected':'')+'  data-count_row = "' + countRow + '" >Hrs</option><option value="%" '+((data.spc_freq_time == '%')?'selected':'')+' data-count_row = "' + countRow + '">%</option><option value="Lot" '+((data.spc_freq_time == 'Lot')?'selected':'')+' data-count_row = "' + countRow + '">Lot</option><option value="Setup" '+((data.spc_freq_time == 'Setup')?'selected':'')+' data-count_row = "' + countRow + '">Setup</option>';
        var spcFreqTimeIP = $("<select/>", {
            type: "text",
            name: "spc_freq_time[]",
            value: data.spc_freq_time,
            class: "form-control "
        }).append(options);
        cell = $(row.insertCell(-1));
        cell.html(spcFreqTimeIP);
        cell.append("<div class='error other_req" + countRow + "'></div>");;

        var spcFreqTextIP = $("<input/>", {
            type: "text",
            name: "spc_freq_text[]",
            value: data.spc_freq_text,
            id: ""+countRow,
            class: "form-control  " + countRow
        });
        cell = $(row.insertCell(-1));
        cell.html(spcFreqTextIP);
        cell.append("<div class='error other_req" + countRow + "'></div>");

        /** SPC End */

        /**** FIR */
        var firMeasurTechIP = $("<input/>", {
            type: "text",
            name: "fir_measur_tech[]",
            value: data.fir_measur_tech,
            id: ""+countRow,
            class: "form-control  " + countRow
        });
        cell = $(row.insertCell(-1));
        cell.html(firMeasurTechIP);
        cell.append("<div class='error other_req" + countRow + "'></div>");

        var firSizeIP = $("<input/>", {
            type: "text",
            name: "fir_size[]",
            value: data.fir_size,
            id: ""+countRow,
            class: "form-control  " + countRow
        });
        cell = $(row.insertCell(-1));
        cell.html(firSizeIP);
        cell.append("<div class='error other_req" + countRow + "'></div>");

        var firFreqIP = $("<input/>", {
            type: "text",
            name: "fir_freq[]",
            value: data.fir_freq,
            id: ""+countRow,
            class: "form-control  " + countRow
        });
        cell = $(row.insertCell(-1));
        cell.html(firFreqIP);
        cell.append("<div class='error other_req" + countRow + "'></div>");

        // var selected = '';
        // if((data.fir_freq_time == 'Hrs')){ selected = 'selected'; }
        // else if((data.fir_freq_time == '%')){ selected = 'selected'; }
        // else if((data.fir_freq_time == 'Lot')){ selected = 'selected'; }
        // else if((data.fir_freq_time == 'Setup')){ selected = 'selected'; }
        // var options = '<option value="">Select</option><option value="Hrs" '+selected+' data-count_row = "' + countRow + '" >Hrs</option><option value="%" '+selected+' data-count_row = "' + countRow + '">%</option><option value="Lot" '+selected+' data-count_row = "' + countRow + '">Lot</option><option value="Setup" '+selected+' data-count_row = "' + countRow + '">Setup</option>';

        var options = '<option value="">Select</option><option value="Hrs" '+((data.fir_freq_time == 'Hrs')?'selected':'')+'  data-count_row = "' + countRow + '" >Hrs</option><option value="%" '+((data.fir_freq_time == '%')?'selected':'')+' data-count_row = "' + countRow + '">%</option><option value="Lot" '+((data.fir_freq_time == 'Lot')?'selected':'')+' data-count_row = "' + countRow + '">Lot</option><option value="Setup" '+((data.fir_freq_time == 'Setup')?'selected':'')+' data-count_row = "' + countRow + '">Setup</option>';

        var firFreqTimeIP = $("<select/>", {
            type: "text",
            name: "fir_freq_time[]",
            value: data.fir_freq_time,
            class: "form-control "
        }).append(options);
        cell = $(row.insertCell(-1));
        cell.html(firFreqTimeIP);
        cell.append("<div class='error other_req" + countRow + "'></div>");

        var firFreqTextIP = $("<input/>", {
            type: "text",
            name: "fir_freq_text[]",
            value: data.fir_freq_text,
            id: ""+countRow,
            class: "form-control  " + countRow
        });
        cell = $(row.insertCell(-1));
        cell.html(firFreqTextIP);
        cell.append("<div class='error other_req" + countRow + "'></div>");

        /** FIR End */
        

        //Add Button cell.
        cell = $(row.insertCell(-1));
        var btnRemove = $('<button sy><i class="ti-trash"></i></button>');
        btnRemove.attr("type", "button");
        btnRemove.attr("onclick", "Remove(this);");
        btnRemove.attr("class", "btn btn-outline-danger waves-effect waves-light");
        cell.append(btnRemove);

        // cell.attr("class", "text-center");
        // cell.attr("style", "width:5%;");

        setTimeout(function() {
            $('.symbol-select').select2({
                templateResult: formatSymbol
            })
        }, 300);
        $(".single-select").comboSelect();


        initMultiSelect();
    }

    function Remove(button) {
        //Determine the reference of the Row using the Button.
        $.confirm({
            title: 'Confirm!',
            content: 'Are you sure want to Remove this Record? <br> All related records will be removed and will not be recovered',
            type: 'red',
            buttons: {
                ok: {
                    text: "ok!",
                    btnClass: 'btn waves-effect waves-light btn-outline-success',
                    keys: ['enter'],
                    action: function() {
                        var row = $(button).closest("TR");
                        var table = $("#pfctbl")[0];
                        table.deleteRow(row[0].rowIndex);
                        $('#pfctbl tbody tr td:nth-child(1)').each(function(idx, ele) {
                            ele.textContent = idx + 1;
                        });

                       
                        var countTR = $('#pfctbl tbody tr:last').index() + 1;
                        if (countTR == 0) {
                            $("#cpTbody").html('<tr id="noData"><td colspan="10" align="center">No data available in table</td></tr>');
                        }
                    }
                },
                cancel: {
                    btnClass: 'btn waves-effect waves-light btn-outline-secondary',
                    action: function() {
                        
                    }
                }
            }
        });
        
    };
</script>
<?php
if (!empty($dimensionData)) {
    foreach ($dimensionData as $row) {
        
        echo "<script> addRow(" . json_encode($row) . ");</script>";
    }
}
?>