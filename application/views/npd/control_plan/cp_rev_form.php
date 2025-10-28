<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h4>
                            <u>Control Plan Revision</u>
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="col-md-12">
                            <form id="pfcForm">
                                <div class="row">
                                    <input type="hidden" name="trans_number" id="trans_number" class="form-control req" value="<?= !empty($pfcData->trans_number) ? $pfcData->trans_number : '' ?>" readonly>
                                    <input type="hidden" name="pfc_main_id" id="pfc_main_id" value="<?=$pfcData->id?>">
                                    <input type="hidden" id="item_id" name="item_id" value="<?=$pfcData->item_id?>">
                                    <table class="table table-bordered">
                                            <tr class="bg-light">
                                                <th>PFC No</th>
                                                <th>Item Code</th>
                                                <th>Item Name</th>
                                            </tr>
                                            <tr>
                                                <td><?=$pfcData->trans_number?></td>
                                                <td><?=$pfcData->item_code?></td>
                                                <td><?=$pfcData->item_name?></td>
                                            </tr>
                                    </table>
                                </div>
                                <hr>

                                <div class="row">
                                    <div class="col-md-5 form-group">
                                        <label for="pfc_id">CP Dimension</label>
                                        <select name="pfc_id" id="pfc_id" class="form-control single-select">
                                            <option value="">Select Operation</option>
                                            <?php
                                            if(!empty($processNoList)){
                                                foreach($processNoList as $row){
                                                    ?>
                                                    <option value="<?=$row->id?>"><?='['.$row->process_no.'] '.$row->product_param?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-5 form-group">
                                        <label for="pfcRevApply">CP Rev No</label>
                                        <div class="input-group-append">
                                            <select  id="cpRevApply" class="form-control single-select" style="width:60%">
                                                <option value="">Select Rev</option>
                                                <?php
                                                if(!empty($revList)){
                                                    foreach($revList as $row){
                                                        ?>
                                                        <option value="<?=$row->rev_no?>"><?=$row->rev_no?></option>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </select>
                                            <button type="button" class="btn btn-outline-success btn-save applyCpRev" style="width:40%"><i class="fa fa-plus"></i> Apply To All</button>
                                        </div>
                                    </div>
                                    <div class="col-md-2 form-group">
                                        <button type="button" class="btn waves-effect waves-light btn-outline-success float-right save-form mt-30" onclick="saveCpRevision('pfcForm','saveCpRevision');"><i class="fa fa-check"></i> Save</button>
                                    </div>
                                    
                                    <!-- <div class="col-md-4 form-group">
                                        <label for="cpRevApply">Rev No</label>
                                        <div class="input-group-append">
                                            <select  id="cpRevApply" class="form-control single-select" style="width:70%">
                                                <option value="">Select Rev</option>
                                                <?php
                                                // if(!empty($revList)){
                                                //     foreach($revList as $row){
                                                //         ?>
                                                //         <option value="<?=$row->rev_no?>"><?=$row->rev_no?></option>
                                                //         <?php
                                                //     }
                                                // }
                                                ?>
                                            </select>
                                            <button type="button" class="btn btn-outline-success btn-save applyCpRev" style="width:30%"><i class="fa fa-plus"></i> Apply To All</button>
                                        </div>
                                    </div> -->
                                    <div class="col-md-12 form-group">
                                        <div class="error general_error"></div>
                                        <div class="table-responsive" >
                                            <table id="pfctbl" class="table jpExcelTable" style="font-size: 12px !important;min-height:200px;" >
                                                <thead class="thead-info text-center">
                                                    <tr>
                                                        <th rowspan="2" style="min-width:100px  !important;">Process No.</th>
                                                        <th rowspan="2" style="min-width:200px !important;">Product</th>
                                                        <th rowspan="2" style="min-width:200px !important;">Process</th>
                                                        <th rowspan="2" style="min-width:200px !important;">Special Char. Class</th>
                                                        <th rowspan="2" style="min-width:200px !important;">Product / Process,Specification / Tolerance</th>
                                                        <th colspan="5" style="min-width:500px !important;">Operator</th>
                                                        <th colspan="5" style="min-width:500px !important;">IIR</th>
                                                        <th colspan="5" style="min-width:500px !important;">IPR</th>
                                                        <th colspan="5" style="min-width:500px !important;">SAR</th>
                                                        <th colspan="5" style="min-width:500px !important;">SPC</th>
                                                        <th colspan="5" style="min-width:500px !important;">FIR</th>
                                                        <th rowspan="2">CP Rev No</th>
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
                                    <div class="col-md-6 form-group">
                                        <h5>New Dimension : </h5>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <button type="button" class="btn btn-outline-success btn-save float-right" onclick="addRow()"><i class="fa fa-plus"></i> Add</button>
                                    </div>
                                    <div class="col-md-12 form-group">
                                        <div class="table-responsive" >
                                            <table id="newCpDimTable" class="table jpExcelTable" style="font-size: 12px !important;min-height:200px;" >
                                                <thead class="thead-info text-center">
                                                    <tr>
                                                        <th rowspan="2" style="min-width:100px  !important;">#</th>
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
                                                        <th rowspan="2">CP Rev No</th>
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
                                                <tbody id="newCpTbody">

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>


                                
                            </form>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="col-md-12">
                            
                            <a href="<?= base_url($headData->controller . '/controlPlanList/' . (!empty($pfcData->id) ? $pfcData->id : '') . '') ?>" class="btn waves-effect waves-light btn-outline-secondary float-right save-form" style="margin-right:10px;"><i class="fa fa-times"></i> Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<?php $this->load->view('includes/footer'); ?>

<script>
  
    $(document).ready(function(){
        $(document).on('change','#pfc_id',function(){
            var pfc_id = $("#pfc_id").val();
            var item_id = $("#item_id").val();
            $.ajax({
                url: base_url + controller + '/getDimensionList',
                data: {pfc_id:pfc_id,item_id:item_id},
                type: "POST",
                dataType:'json',
                success:function(data){
                    $("#cpTbody").html(data.tbodyData);
                    
                    initMultiSelect();
                }
            });
		});

        
       
        $(document).on('click','.applyCpRev',function(){
            var pfc_id = $("#pfc_id").val();
            if(pfc_id){
                var rev_no = $("#cpRevApply").val();
                $("select[name='cpRevSelect[]']").map(function () { 
                    console.log($(this).children());
                    $(this).children(" option[value='" + rev_no + "']").attr('selected', true);
                    reInitMultiSelect();
                    $("#rev_no"+$(this).data('pfc_id')).val($(this).val().toString());
                 }).get();
                

            }else{
                $(".pfc_id").html("Operation Requires");
            }
		});

    });

    function saveCpRevision(formId, fnsave) {
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
                $(".error").html("");
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
                // window.location = data.url;
                window.location.reload();

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
        $('table#newCpDimTable tr#noData').remove()
        //Get the reference of the Table's TBODY element.
        var tblName = "newCpDimTable";

        var tBody = $("#" + tblName + " > TBODY")[0];

        //Add Row.
        row = tBody.insertRow(-1);

        //Add index cell
        var countRow = $('#' + tblName + ' tbody tr:last').index() + 1;
       
        cell = $(row.insertCell(-1));
        var pfcIdIP = $("<input/>", {
            type: "hidden",
            name: "new_pfc_id[]",
            value :data.pfc_id
        });
        cell.html(countRow);
        var idIP = $("<input/>", {
            type: "hidden",
            name: "new_dimension_id[]",
        });
        cell.append(idIP);
        cell.append(pfcIdIP);
        cell.append("<div class='error parameter" + countRow + "'></div>");

        
        var productParamIP = $("<input/>", {
            type: "text",
            name: "product_param[]",
            value: data.product_param,
            class: "form-control"
        });
        cell = $(row.insertCell(-1));
        cell.html(productParamIP);
        cell.append(idIP);
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
        cell.append("<div class='error parameter" + countRow + "'></div>");

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

       
        var options = '<option value="">Select</option><option value="Hrs"  data-count_row = "' + countRow + '" >Hrs</option><option value="%"  data-count_row = "' + countRow + '">%</option><option value="Lot"  data-count_row = "' + countRow + '">Lot</option><option value="Setup"  data-count_row = "' + countRow + '">Setup</option>';
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

        var options = '<option value="">Select</option><option value="Hrs"  data-count_row = "' + countRow + '" >Hrs</option><option value="%"  data-count_row = "' + countRow + '">%</option><option value="Lot"  data-count_row = "' + countRow + '">Lot</option><option value="Setup"  data-count_row = "' + countRow + '">Setup</option>';
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

        var options = '<option value="">Select</option><option value="Hrs"  data-count_row = "' + countRow + '" >Hrs</option><option value="%"  data-count_row = "' + countRow + '">%</option><option value="Lot"  data-count_row = "' + countRow + '">Lot</option><option value="Setup"  data-count_row = "' + countRow + '">Setup</option>';
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

        var options = '<option value="">Select</option><option value="Hrs"  data-count_row = "' + countRow + '" >Hrs</option><option value="%"  data-count_row = "' + countRow + '">%</option><option value="Lot"  data-count_row = "' + countRow + '">Lot</option><option value="Setup"  data-count_row = "' + countRow + '">Setup</option>';
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

        var options = '<option value="">Select</option><option value="Hrs"  data-count_row = "' + countRow + '" >Hrs</option><option value="%"  data-count_row = "' + countRow + '">%</option><option value="Lot"  data-count_row = "' + countRow + '">Lot</option><option value="Setup"  data-count_row = "' + countRow + '">Setup</option>';
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

        var options = '<option value="">Select</option><option value="Hrs"  data-count_row = "' + countRow + '" >Hrs</option><option value="%"  data-count_row = "' + countRow + '">%</option><option value="Lot"  data-count_row = "' + countRow + '">Lot</option><option value="Setup"  data-count_row = "' + countRow + '">Setup</option>';
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
        var revNoIP = $("<select/>", {
            type: "text",
            id: "rev"+countRow,
            class: "form-control jp_multiselect",
            multiple: 'multiple'
        }).attr('data-input_id', 'new_rev_no' + countRow);
        var revList = <?php echo json_encode($revList); ?>;
        for (var i = 0; i < revList.length; i++) {
            $('<option />', {
                value: revList[i].rev_no,
                text: revList[i].rev_no+' | PFC REV : '+revList[i].pfc_rev_no,
            }).appendTo(revNoIP);
        }
        var revNoHiddenIp = $("<input/>", {
            type: "hidden",
            name: "new_rev_no[]",
            id: 'new_rev_no' + countRow,
            class:'new_rev_no'

        });
        cell = $(row.insertCell(-1));
        cell.html(revNoIP);
        cell.append(revNoHiddenIp);
        cell.append(' <div class="error rev_no'+countRow+'"></div>');

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
     
        var row = $(button).closest("TR");
        var table = $("#newCpDimTable")[0];
        table.deleteRow(row[0].rowIndex);
        // $('#pfctbl tbody tr td:nth-child(1)').each(function(idx, ele) {
        //     ele.textContent = idx + 1;
        // });

        
		//idx = idx -1;
        $('#newCpDimTable tbody tr td:nth-child(8) select').each(function(idx, ele) {
            let newIdx = parseFloat(idx) + 1;
            //$(this).attr('id', 'selectMeasur1' + newIdx);
            $(this).attr('data-input_id', 'measur_tech1' + newIdx);
            $(this).removeAttr('data-row_id');
            $(this).attr('data-row_id', newIdx);
			$(this).multiselect('rebuild');
        });
        $('#newCpDimTable tbody tr td:nth-child(8) .measur_tech1').each(function(idx, ele) {
            let newIdx = parseFloat(idx) + 1;
            $(this).attr('id', 'measur_tech1' + newIdx);
        });

        $('#newCpDimTable tbody tr td:nth-child(15) select').each(function(idx, ele) {
            let newIdx = parseFloat(idx) + 1;

            $(this).attr('id', 'selectMeasur2' + newIdx);
            $(this).attr('data-input_id', 'measur_tech2' + newIdx);
        });
        $('#newCpDimTable tbody tr td:nth-child(15) .measur_tech2').each(function(idx, ele) {
            let newIdx = parseFloat(idx) + 1;
            $(this).attr('id', 'measur_tech2' + newIdx);
        });

        $('#newCpDimTable tbody tr td:nth-child(22) select').each(function(idx, ele) {
            let newIdx = parseFloat(idx) + 1;

            $(this).attr('id', 'selectMeasur3' + newIdx);
            $(this).attr('data-input_id', 'measur_tech3' + newIdx);
        });
        $('#newCpDimTable tbody tr td:nth-child(22) .measur_tech3').each(function(idx, ele) {
            let newIdx = parseFloat(idx) + 1;
            $(this).attr('id', 'measur_tech3' + newIdx);
        });

        $('#newCpDimTable tbody tr td:nth-child(29) select').each(function(idx, ele) {
            let newIdx = parseFloat(idx) + 1;

            $(this).attr('id', 'pfc_rev' + newIdx);
            $(this).attr('data-input_id', 'new_pfc_rev_no' + newIdx);
        });

        $('#newCpDimTable tbody tr td:nth-child(29)  .new_pfc_rev_no').each(function(idx, ele) {

            let newIdx = parseFloat(idx) + 1;
            $(this).attr('id', 'new_pfc_rev_no' + newIdx);
        });

        $('#newCpDimTable tbody tr td:nth-child(30) select').each(function(idx, ele) {
            let newIdx = parseFloat(idx) + 1;

            $(this).attr('id', 'rev_no' + newIdx);
            $(this).attr('data-input_id', 'rev_no' + newIdx);
        });
        $('#newCpDimTable tbody tr td:nth-child(30)  .new_rev_no').each(function(idx, ele) {

            let newIdx = parseFloat(idx) + 1;
            $(this).attr('id', 'rev_no' + newIdx);
        });

        var countTR = $('#newCpDimTable tbody tr:last').index() + 1;
        if (countTR == 0) {
            $("#newCpTbody").html('<tr id="noData"><td colspan="39" align="center">No data available in table</td></tr>');
        }
        // initMultiSelect();        
        reInitMultiSelect();
    };

</script>
