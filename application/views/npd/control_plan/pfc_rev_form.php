<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h4>
                            <u>PFC</u>
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="col-md-12">
                            <form id="pfcForm">
                                <div class="row">
                                    <?php
                                    $dataRow = $transData[0]; 
                                    ?>
                                    <input type="hidden" name="id" value="<?= (!empty($dataRow->trans_main_id)) ? $dataRow->trans_main_id : '' ?>">
                                    <input type="hidden" id="item_id" name="item_id" value="<?= !empty($dataRow->item_id) ? $dataRow->item_id : $item_id ?>">
                                   
                                    <table class="table table-bordered">
                                            <tr class="bg-light">
                                                <th>PFC No</th>
                                                <th>Item Code</th>
                                                <th>Item Name</th>
                                            </tr>
                                            <tr>
                                                <td><?=$dataRow->trans_number?></td>
                                                <td><?=$dataRow->item_code?></td>
                                                <td><?=$dataRow->item_name?></td>
                                            </tr>
                                    </table>
                                </div>
                                <hr>
                                <div class="row">

                                    <div class="col-md-4 form-group">
                                        <label for="pfcRevApply">Rev No</label>
                                        <div class="input-group-append">
                                            <select  id="revApply" class="form-control single-select" style="width:70%">
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
                                            <button type="button" class="btn btn-outline-success btn-save applyRev" style="width:30%"><i class="fa fa-plus"></i> Apply To All</button>
                                        </div>
                                    </div>

                                    <div class="col-md-8 form-group">
                                        <button type="button" class="btn btn-outline-success btn-save float-right mt-30" onclick="addRow()"><i class="fa fa-plus"></i> Add</button>
                                    </div>
                                    <div class="error general_error"></div>
                                    <div class="table-responsive" style="height:50vh;overflow-y:scroll;">
                                        <table id="pfctbl" class="table table-bordered " style="font-size: 11px !important;">
                                            <thead class="thead-info">
                                                <tr>
                                                    <th style="width:5px;">#</th>
                                                    <th >Process No.</th>
                                                    <th >Process Code</th>
                                                    <th >Process Description</th>
                                                    <th > Symbol 1</th>
                                                    <th >Special Char. Class</th>
                                                    <th >Output</th>
                                                    <th >Location</th>
                                                    <th>Stage type</th>
                                                    <th>PFC Revision</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="pfcTbody">
                                                <?php
                                                if (!empty($transData)) {
                                                    $i=1;
                                                    foreach ($transData as $row) {
                                                       ?>
                                                       <tr>
                                                            <td><?=$i?></td>
                                                            <td><?=$row->process_no?></td>
                                                            <td><?=$row->process_code?></td>
                                                            <td><?=$row->product_param?></td>
                                                            <td>
                                                                <?php
                                                                if(!empty($row->symbole_1)){
                                                                   echo '<img src="' . base_url('assets/images/symbols/'.$row->symbol_1.'.png') . '" style="width:20px;display:inline-block;" />';
                                                                }
                                                                ?>
                                                            </td>
                                                            <td>
                                                                <?php
                                                                if(!empty($row->char_class)){ echo '<img src="' . base_url('assets/images/symbols/'.$row->char_class.'.png') . '" style="width:20px;display:inline-block;" />'; }
                                                                ?>
                                                            </td>
                                                            <td><?=$row->output_operation?></td>
                                                            <td><?=($row->location == 1)?'Inhouse':'Outsource'?></td>
                                                            <td><?=$pfcStage[$row->stage_type]?></td>
                                                            <td>
                                                                <select name="cpRevSelect[]" class="form-control jp_multiselect"  data-input_id="rev_no<?=$i?>" multiple="multiple" data-row_id="<?=$i?>">
                                                                   
                                                                    <?php
                                                                    if (!empty($revList)) {
                                                                        foreach ($revList as $rev) {
                                                                            $selected = (!empty($row->rev_no) && in_array($rev->rev_no, explode(",", $row->rev_no))) ? 'selected' : ''

                                                                    ?><option value="<?= $rev->rev_no ?>" <?= $selected ?>><?= $rev->rev_no ?></option>
                                                                    <?php }
                                                                    } ?>
                                                                </select>
                                                                <input type="hidden" name="rev_no[]" id="rev_no<?=$i?>" value="<?= !empty($row->rev_no) ? $row->rev_no : '' ?>">
                                                                <input type="hidden" name="trans_id[]" value="<?=$row->id?>">
                                                                <div class="error rev_no<?=$i?>"></div>
                                                            </td>
                                                            <td></td>
                                                       </tr>
                                                       <?php
                                                       $i++;
                                                    }
                                                }
                                                ?>

                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="col-md-12">
                            <button type="button" class="btn waves-effect waves-light btn-outline-success float-right save-form" onclick="savePFCRev('pfcForm','savePFCRev');"><i class="fa fa-check"></i> Save</button>
                            <a href="<?= base_url($headData->controller . '/pfcList/' . (!empty($dataRow->item_id) ? $dataRow->item_id : $item_id) . '') ?>" class="btn waves-effect waves-light btn-outline-secondary float-right save-form" style="margin-right:10px;"><i class="fa fa-times"></i> Cancel</a>
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

        $(document).on('click','.applyRev',function(){
            var rev_no = $("#revApply").val();
            $("select[name='cpRevSelect[]']").map(function () { 
                $(this).children(" option[value='" + rev_no + "']").attr('selected', true);
                reInitMultiSelect();
                $("#rev_no"+$(this).data('row_id')).val($(this).val().toString());
                }).get();
		});

        $(document).on('change', '#item_id', function() {
            var itemData = $(this).find(":selected").data('row');
            console.log(itemData);
            var pfc_number = 'PFC/' + itemData.item_code + '/' + ((itemData.app_rev_no != null) ? itemData.app_rev_no : '') + '/' + ((itemData.rev_no != null) ? itemData.rev_no : '');
            $("#trans_number").val(pfc_number);
        });

        $(document).on('change', '.location_id', function() {
            var countRow = $(this).find(":selected").data('count_row');
            console.log(countRow);
            var location = $(this).val();
            if (location == 1) {
                console.log("vendor_select" + countRow);
                $("#vendor_select" + countRow).hide();
            } else {
                $("#vendor_select" + countRow).show();
            }

            initMultiSelect();
        });

    });


    function savePFCRev(formId, fnsave) {
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
                window.location = data.url;

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

    function addRow(data = {}) {
        $('table#pfctbl tr#noData').remove()
        //Get the reference of the Table's TBODY element.
        var tblName = "pfctbl";

        var tBody = $("#" + tblName + " > TBODY")[0];

        //Add Row.
        row = tBody.insertRow(-1);

        //Add index cell
        var countRow = $('#' + tblName + ' tbody tr:last').index() + 1;
        var cell = $(row.insertCell(-1));
        cell.html(countRow);

        var idIP = $("<input/>", {
            type: "hidden",
            name: "new_trans_id[]",
            value: data.id
        });
        var processNoIP = $("<input/>", {
            type: "text",
            name: "process_no[]",
            value: data.process_no,
            class: "form-control"
        });
        cell = $(row.insertCell(-1));
        cell.html(processNoIP);
        cell.append(idIP);


        var processCodeIP = $("<select/>", {
            type: "text",
            name: "process_code[]",
            class: "form-control single-select",

        });
        var processCode = <?php echo json_encode($processCodes); ?>;
        processCodeIP.append("<option value=''>Select Process Code</option>");
        for (var i = 0; i < processCode.length; i++) {
            // console.log(processCode[i]);
            var selected = (data.process_code && data.process_code == processCode[i].process_code) ? true : false
            $('<option />', {
                value: processCode[i].process_code,
                text: processCode[i].process_code,
                selected: selected
            }).appendTo(processCodeIP);
        }
        cell = $(row.insertCell(-1));
        cell.html(processCodeIP);

        var processDescrIP = $("<input/>", {
            type: "text",
            name: "parameter[]",
            value: data.product_param,
            class: "form-control"
        });
        cell = $(row.insertCell(-1));
        cell.html(processDescrIP);



        var symbol1IP = $("<select/>", {
            type: "text",
            name: "symbol_1[]",
            class: "form-control symbol-select"
        });

        var symbol2IP = $("<select/>", {
            type: "text",
            name: "symbol_2[]",
            class: "form-control symbol-select"
        });
        var symbol3IP = $("<select/>", {
            type: "text",
            name: "symbol_3[]",
            class: "form-control symbol-select"
        });
        var symbolArray = <?php echo json_encode($symbolArray); ?>;

        $.each(symbolArray, function(key, value) {
            if (key == '') {
                symbol1IP.append('<option value="">Select Symbol 1</option>');
                symbol2IP.append('<option value="">Select Symbol 2</option>');
                symbol3IP.append('<option value="">Select Symbol 3</option>');
            } else {
                selectedOpt1 = (data.symbol_1 == key) ? 'selected' : '';
                selectedOpt2 = (data.symbol_2 == key) ? 'selected' : '';
                selectedOpt3 = (data.symbol_3 == key) ? 'selected' : '';

                var options1 = '<option value="' + key + '" ' + selectedOpt1 + '  data-img_path="' + base_url + 'assets/images/symbols/' + key + '.png")">' + value + '</option>';
                var options2 = '<option value="' + key + '" ' + selectedOpt2 + '  data-img_path="' + base_url + 'assets/images/symbols/' + key + '.png")">' + value + '</option>';
                var options3 = '<option value="' + key + '" ' + selectedOpt3 + '  data-img_path="' + base_url + 'assets/images/symbols/' + key + '.png")">' + value + '</option>';

                symbol1IP.append(options1);
                //symbol2IP.append(options2);
                //symbol3IP.append(options3);
            }
        });
        cell = $(row.insertCell(-1));
        cell.html(symbol1IP);

        //cell = $(row.insertCell(-1));
        //cell.html(symbol2IP);

        //cell = $(row.insertCell(-1));
        //cell.html(symbol3IP);

        if ($('select').data('select2')) {
            $(".symbol-select").select2("destroy").select2();
        }
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

        var outputIP = $("<input/>", {
            type: "text",
            name: "output_operation[]",
            value: data.output_operation,
            class: "form-control"
        });
        cell = $(row.insertCell(-1));
        cell.html(outputIP);

        var locationIP = $("<select/>", {
            type: "text",
            name: "location[]",
            class: "form-control location_id"
        }).attr("data-countRow", countRow);
        locationIP.append('<option value="1" ' + ((data.location == 1) ? 'selected' : '') + ' data-count_row="' + countRow + '">Inhouse</option><option value="2" ' + ((data.location == 2) ? 'selected' : '') + '  data-count_row="' + countRow + '">Outsource</option>');


        cell = $(row.insertCell(-1));
        cell.html(locationIP);

        var stageIP = $("<select/>", {
            type: "text",
            name: "stage_type[]",
            class: "form-control"
        });
        stageIP.append('<option value="1" ' + ((data.stage_type == 1) ? 'selected' : '') + '">IIR</option><option value="2" ' + ((data.stage_type == 2) ? 'selected' : '') + ' >Production</option><option value="3" ' + ((data.stage_type == 3) ? 'selected' : '') + ' >FIR</option><option value="4" ' + ((data.stage_type == 4) ? 'selected' : '') + '  >PDI</option><option value="5" ' + ((data.stage_type == 5) ? 'selected' : '') + ' >Packing</option><option value="6" ' + ((data.stage_type == 6) ? 'selected' : '') + '>Dispatch</option><option value="7" ' + ((data.stage_type == 7) ? 'selected' : '') + '>RQC</option><option value="8" ' + ((data.stage_type == 8) ? 'selected' : '') + '>Pre FIR</option>');
        cell = $(row.insertCell(-1));
        cell.html(stageIP);

        var revNoIP = $("<select/>", {
            type: "text",
            id: "rev"+countRow,
            class: "form-control jp_multiselect",
            multiple: 'multiple'
        }).attr('data-input_id', 'rev_no' + countRow);
        var revList = <?php echo json_encode($revList); ?>;
        for (var i = 0; i < revList.length; i++) {
            $('<option />', {
                value: revList[i].rev_no,
                text: revList[i].rev_no,
            }).appendTo(revNoIP);
        }
        var revNoHiddenIp = $("<input/>", {
            type: "hidden",
            name: "rev_no[]",
            id: 'rev_no' + countRow,

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
        }, 1000);
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

                        $('#pfctbl tbody tr td:nth-child(12) select').each(function(idx, ele) {
                            let newIdx = parseFloat(idx) + 1;

                            $(this).attr('id', 'vendor_select' + newIdx);
                            $(this).attr('data-input_id', 'vendor_id' + newIdx);
                        });
                        $('#pfctbl tbody tr td:nth-child(12)  .vendor').each(function(idx, ele) {

                            let newIdx = parseFloat(idx) + 1;
                            $(this).attr('id', 'vendor_id' + newIdx);
                        });

                        var countTR = $('#pfctbl tbody tr:last').index() + 1;
                        if (countTR == 0) {
                            $("#pfcTbody").html('<tr id="noData"><td colspan="24" align="center">No data available in table</td></tr>');
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
// if (!empty($transData)) {
//     foreach ($transData as $row) {
//         $row->id = (empty($revision)) ? $row->id : '';
//         echo "<script>addRow(" . json_encode($row) . ");</script>";
//     }
// }
?>