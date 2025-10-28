<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id"  value="<?=(!empty($dataRow->id))?$dataRow->id:''?>">
            <input type="hidden" name="item_id"  value="<?=(!empty($item_id))?$item_id:'';?>">
            <div class="col-md-6 form-group">
                <label for="cost_date">Date</label>
                <input type="date" name="cost_date" id="cost_date" class="form-control req" value="<?=(!empty($dataRow->cost_date))?$dataRow->cost_date:date('Y-m-d');?>">
            </div>
            <div class="col-md-6 form-group">
                <label for="moq">M. O. Qty.</label>
                <input type="text" name="moq" id="moq" class="form-control req numericOnly calcRm " value="<?=(!empty($dataRow->moq))?$dataRow->moq:'';?>">
            </div>
            
            <div class="col-md-12 form-group">
                <table class="table excel_table table-bordered">
                    <thead class="thead-info">
                        <tr>
                            <th colspan="2" class="text-center">Raw Material Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th style="width:30%;">Material Specification</th>
                            <td style="width:70%;">
                                <select name="rm_id" id="rm_id" class="form-control single-select calcRm req">
                                    <option value="" data-bom_qty="">Select Raw Material</option>
                                    <?php
                                        foreach($bomData as $row):
                                            $selected = (!empty($dataRow->rm_id) && $dataRow->rm_id == $row->ref_item_id)?'selected':'';
                                            echo '<option value="'.$row->ref_item_id.'" data-bom_qty="'.$row->qty.'" '.$selected.'>'.(!empty($row->item_code)? '['.$row->item_code.'] '.$row->item_name : $row->item_name).' (BOM Qty: '.$row->qty.')</option>';
                                        endforeach;
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>Req. Raw Material</th>
                            <td>
                                <input type="text" name="req_rm" id ="req_rm" value="<?=(!empty($dataRow->req_rm)? $dataRow->req_rm : 0)?>" class="form-control  floatOnly" readOnly>
                            </td>
                        </tr>
                        <tr>
                            <th>Finish+Blank Length</th>
                            <td>
                                <input type="text" name="finish_length" id ="finish_length" value="<?=(!empty($dataRow->finish_length)? $dataRow->finish_length : 0)?>" class="form-control  floatOnly">
                            </td>
                        </tr>
                        <tr>
                            <th>RM Rate/k.g.</th>
                            <td>
                                <input type="text" name="rm_rate" id ="rm_rate" value="<?=(!empty($dataRow->rm_rate)? $dataRow->rm_rate : 0)?>" class="form-control  calRmCost floatOnly">
                            </td>
                        </tr>
                        <tr>
                            <th>Gross Weight Piece</th>
                            <td>
                                <input type="text" name="wt_pcs" id ="wt_pcs" value="<?=(!empty($dataRow->wt_pcs)? $dataRow->wt_pcs : 0)?>" class="form-control  calWt calRmCost floatOnly">
                            </td>
                        </tr>
                        <tr>
                            <th>Job Finish Weight</th>
                            <td>
                                <input type="text" name="wt_finish" id ="wt_finish" value="<?=(!empty($dataRow->wt_finish)? $dataRow->wt_finish : 0)?>" class="form-control  calWt floatOnly">
                            </td>
                        </tr>
                        <tr>
                            <th>Scrap Recover/Piece</th>
                            <td>
                                <input type="text" name="scrap_per_pcs" id ="scrap_per_pcs" value="<?=(!empty($dataRow->scrap_per_pcs)? $dataRow->scrap_per_pcs : 0)?>" class="form-control  calRmCost floatOnly" readOnly>
                            </td>
                        </tr>
                        <tr>
                            <th>Current Scrap Market</th>
                            <td>
                                <input type="text" name="current_scrap_rate" id ="current_scrap_rate" value="<?=(!empty($dataRow->current_scrap_rate)? $dataRow->current_scrap_rate : 0)?>" class="form-control  calRmCost floatOnly">
                            </td>
                        </tr>
                        <tr>
                            <th>RM Gross Value/Piece</th>
                            <td>
                                <input type="text" name="gross_val_pcs" id ="gross_val_pcs" value="<?=(!empty($dataRow->gross_val_pcs)? $dataRow->gross_val_pcs : 0)?>" class="form-control calRmCost floatOnly" readOnly>
                            </td>
                        </tr>
                        <tr>
                            <th>Scrap Recover/Rs</th>
                            <td>
                                <input type="text" name="total_scrap_recover" id ="total_scrap_recover" value="<?=(!empty($dataRow->total_scrap_recover)? $dataRow->total_scrap_recover : 0)?>" class="form-control calRmCost floatOnly" readOnly>
                            </td>
                        </tr>
                        <tr>
                            <th>Receiving Inspection + Chemical +<br> Microstructure</th>
                            <td>
                                <input type="text" name="insp_cost" id ="insp_cost" value="<?=(!empty($dataRow->insp_cost)? $dataRow->insp_cost : 0)?>" class="form-control calRmCost  floatOnly">
                            </td>
                        </tr>
                    </body>
                    <tfoot class="thead-info">
                            <th>I. Total Land Cost of RM</th>
                            <td>
                                <input type="text" name="net_rm_cost" id ="net_rm_cost" value="<?=(!empty($dataRow->net_rm_cost)? $dataRow->net_rm_cost : 0)?>" class="form-control floatOnly" readOnly>
                            </td>
                    </tfoot>
                </table>
            </div>

            <?php
                $machineHtml = $heatHtml ="";
                $machineProcess = (!empty($dataRow->machining_process))?json_decode($dataRow->machining_process):'';
                $heatProcess = (!empty($dataRow->secondary_process))?json_decode($dataRow->secondary_process):'';
                if (!empty($processData)) :
                    foreach ($processData as $row) :
                        if($row->is_machining == 'Yes'):
                            $machineHtml .= '<tr>
                                    <th>' . $row->process_name . '</th>
                                    <td>
                                        <input type="text" class="form-control proCost floatOnly" name="mhr[]" id="mhr'.$row->id.'" data-rowid="'.$row->id.'" value="0">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control proCost floatOnly" name="c_time[]" id="c_time'.$row->id.'" data-rowid="'.$row->id.'" value="0">
                                    </td>
                                    <td>
                                        <input type="hidden" name="mprocess_id[]" id="id'.$row->id.'" value="'.$row->id.'" ">
                                        <input type="text" class="form-control totalProCost floatOnly" name="mprocess_cost[]" id="process_cost'.$row->id.'" value="0" readOnly>
                                    </td>
                                </tr>';
                        else:
                            $heatHtml .= '<tr>
                                <th>' . $row->process_name . '</th>
                                <td>
                                    <input type="hidden" name="hprocess_id[]" id="id'.$row->id.'" value="'.$row->id.'" ">
                                    <input type="text" class="form-control proCost floatOnly" name="hprocess_cost[]" id="process_cost'.$row->id.'" value="0">
                                </td>
                            </tr>';
                        endif;
                    endforeach;
                else :
                    $machineHtml .= $heatHtml .= '<tr><td colspan="3" class="text-center">No Data Found.</td></tr>';
                endif;
            ?>
            <div class="col-md-12 form-group">
                <table class="table excel_table table-bordered">
                    <thead class="thead-info">
                        <tr>
                            <th colspan="4" class="text-center">Manufacturing Proceses</th>
                        </tr>
                        <tr>
                            <th style="width:55%;">Process Name</th>
                            <th style="width:15%;">MHR</th>
                            <th style="width:15%;">Cycle time</th>
                            <th style="width:15%;">Costing</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php echo $machineHtml; ?>
                    </tbody>
                    <tfoot class="thead-info">
                        <th colspan="3">II. Machining Cost</th>
                        <td>
                            <input type="text" name="total_machine_cost" id ="total_machine_cost" value="<?=(!empty($dataRow->total_machine_cost)? $dataRow->total_machine_cost : 0)?>" class="form-control totalProCost floatOnly" readOnly>
                        </td>
                    </tfoot>
                </table>
                <table class="table excel_table table-bordered">
                    <thead class="thead-info">
                        <tr>
                            <th colspan="2" class="text-center">Heat Treatment/Surface Treatment</th>
                        </tr>
                        <tr>
                            <th style="width:40%;">Process Name</th>
                            <th style="width:12%;">Costing</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php echo $heatHtml; ?>
                    </tbody>
                    <tfoot class="thead-info">
                        <th>III. Secondary Process Cost</th>
                        <td>
                            <input type="text" name="total_secondary_cost" id ="total_secondary_cost" value="<?=(!empty($dataRow->total_secondary_cost)? $dataRow->total_secondary_cost : 0)?>" class="form-control totalProCost floatOnly" readOnly>
                        </td>
                    </tfoot>
                </table>
                <table class="table excel_table table-bordered">
                    <thead class="thead-info">
                        <tr>
                            <th colspan="2" class="text-center">RM + MACHINING + HEAT TREATMENT/SURFACE TREATMENT COST</th>
                            <th>
                                <input type="text" name="rm_process_cost" id ="rm_process_cost" value="<?=(!empty($dataRow->rm_process_cost)? $dataRow->rm_process_cost : 0)?>" class="form-control toolCost floatOnly" readOnly>
                            </th>
                        </tr>
                        <tr>
                            <th></th>
                            <th>Per(%)</th>
                            <th>Cost</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>Tool Maintenance</th>
                            <td>
                                <input type="text" name="tool_per" id ="tool_per" value="<?=(!empty($dataRow->tool_per)? $dataRow->tool_per : 0)?>" class="form-control toolCost floatOnly">
                            </td>
                            <td>
                                <input type="text" name="tool_cost" id ="tool_cost" value="<?=(!empty($dataRow->tool_cost)? $dataRow->tool_cost : 0)?>" class="form-control floatOnly" readOnly>
                            </td>
                        </tr>
                        <tr>
                            <th>Rejection Cost</th>
                            <td>
                                <input type="text" name="rej_per" id ="rej_per" value="<?=(!empty($dataRow->rej_per)? $dataRow->rej_per : 0)?>" class="form-control floatOnly">
                            </td>
                            <td>
                                <input type="text" name="rej_cost" id ="rej_cost" value="<?=(!empty($dataRow->rej_cost)? $dataRow->rej_cost : 0)?>" class="form-control floatOnly" readOnly>
                            </td>
                        </tr>
                        <tr>
                            <th>Overheads on Process</th>
                            <td>
                                <input type="text" name="overhead_process_per" id ="overhead_process_per" value="<?=(!empty($dataRow->overhead_process_per)? $dataRow->overhead_process_per : 0)?>" class="form-control floatOnly">
                            </td>
                            <td>
                                <input type="text" name="overhead_process" id ="overhead_process" value="<?=(!empty($dataRow->overhead_process)? $dataRow->overhead_process : 0)?>" class="form-control floatOnly" readOnly>
                            </td>
                        </tr>
                        <tr>
                            <th>ICC Cost</th>
                            <td>
                                <input type="text" name="icc_per" id ="icc_per" value="<?=(!empty($dataRow->icc_per)? $dataRow->icc_per : 0)?>" class="form-control floatOnly">
                            </td>
                            <td>
                                <input type="text" name="icc_cost" id ="icc_cost" value="<?=(!empty($dataRow->icc_cost)? $dataRow->icc_cost : 0)?>" class="form-control floatOnly" readOnly>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot class="thead-info">
                        <tr>
                            <th colspan="2">SUB TOTAL WITHOUT PROFIT</th>
                            <td>
                                <input type="text" name="sub_total" id ="sub_total" value="<?=(!empty($dataRow->sub_total)? $dataRow->sub_total : 0)?>" class="form-control floatOnly" readOnly>
                            </td>
                        </tr>
                        <tr>
                            <th>Profit Percentage</th>
                            <td>
                                <input type="text" name="profit_per" id ="profit_per" value="<?=(!empty($dataRow->profit_per)? $dataRow->profit_per : 0)?>" class="form-control floatOnly">
                            </td>
                            <td>
                                <input type="text" name="profit_pcs" id ="profit_pcs" value="<?=(!empty($dataRow->profit_pcs)? $dataRow->profit_pcs : 0)?>" class="form-control floatOnly" readOnly>
                            </td>
                        </tr>
                        <tr>
                            <th colspan="2">Total Profit Value Per Batch</th>
                            <td>
                                <input type="text" name="total_profit" id ="total_profit" value="<?=(!empty($dataRow->total_profit)? $dataRow->total_profit : 0)?>" class="form-control floatOnly" readOnly>
                            </td>
                        </tr>
                        <tr>
                            <th colspan="2">Total Cost Per Part</th>
                            <td>
                                <input type="text" name="total_cost_pcs" id ="total_cost_pcs" value="<?=(!empty($dataRow->total_cost_pcs)? $dataRow->total_cost_pcs : 0)?>" class="form-control floatOnly" readOnly>
                            </td>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</form>

<script>
$(document).ready(function() {

    $(document).on("change keyup",'.calcRm', function(){
        var bom_qty = $('#rm_id :selected').data('bom_qty');
        var moq = $('#moq').val();
        if(moq != 0 && moq != '' && bom_qty !=0 && bom_qty != ''){
            $('#req_rm').val((parseFloat(moq) * parseFloat(bom_qty)).toFixed(3));
        }
    });

    $(document).on("keyup",'.calWt', function(){
        $('.error').html("");
        var wt_pcs = $('#wt_pcs').val();
        var wt_finish = $('#wt_finish').val();
        if(wt_pcs != 0 && wt_pcs != '' && wt_finish !=0 && wt_finish != ''){
            if(wt_finish < wt_pcs){
                $('#scrap_per_pcs').val((parseFloat(wt_pcs) - parseFloat(wt_finish)).toFixed(3));
            }else{
                $('#scrap_per_pcs').val(0);
                $('.wt_finish').html('InValid Finish Weight.');
            }
        }
    });

    $(document).on("keyup",'.calRmCost', function(){
        var gross_val = 0
        var wt_pcs = $('#wt_pcs').val();
        var rm_rate = $('#rm_rate').val();
        if(wt_pcs != 0 && wt_pcs != '' && rm_rate !=0 && rm_rate != ''){
            gross_val = (parseFloat(wt_pcs) * parseFloat(rm_rate)).toFixed(3);
            $('#gross_val_pcs').val(gross_val);
        }
        
        var total_scrap = 0;
        var scrap_per_pcs = $('#scrap_per_pcs').val();
        var current_scrap_rate = $('#current_scrap_rate').val();
        if(scrap_per_pcs != 0 && scrap_per_pcs != '' && current_scrap_rate !=0 && current_scrap_rate != ''){
            total_scrap = ((parseFloat(scrap_per_pcs) * parseFloat(current_scrap_rate)) % 70).toFixed(3);
            total_scrap = ((parseFloat(total_scrap) * 70) / 100).toFixed(3);
            $('#total_scrap_recover').val(total_scrap);
        }

        var insp_cost = $('#insp_cost').val();
        var net_rm_cost = ((parseFloat(gross_val) - parseFloat(total_scrap)) + parseFloat(insp_cost)).toFixed(3);
        $('#net_rm_cost').val(net_rm_cost);
        totalmachinecost();
    });

    $(document).on("keyup",'.proCost', function(){
        var rowid = $(this).data('rowid');
        var mhr = $('#mhr'+rowid).val();
        var c_time = $('#c_time'+rowid).val();
        var machineCost = 0;
        var process_cost = 0;

        if(mhr != 0 && mhr != '' && c_time !=0 && c_time != ''){
            var ctime = (parseFloat(c_time) / 3600);
            process_cost = (parseFloat(mhr) * parseFloat(ctime)).toFixed(2);
            $('#process_cost'+rowid).val(process_cost);
        }else{
            $('#process_cost'+rowid).val(0);
        }
        totalmachinecost();
    });

    $("input[type='text']").change( function() {
        var tool_per = $('#tool_per').val();
        var rm_process = $('#rm_process_cost').val();
        if(rm_process != 0 && rm_process != '' && tool_per !=0 && tool_per != ''){
            $('#tool_cost').val(((parseFloat(rm_process) * parseFloat(tool_per)) / 100).toFixed(2));
        }

        var rej_per = $('#rej_per').val();
        if(rm_process != 0 && rm_process != '' && rej_per !=0 && rej_per != ''){
            $('#rej_cost').val(((parseFloat(rm_process) * parseFloat(rej_per)) / 100).toFixed(2));
        }

        var overhead_process_per = $('#overhead_process_per').val();
        var total_machine_cost = $('#total_machine_cost').val();
        if(total_machine_cost != 0 && total_machine_cost != '' && overhead_process_per !=0 && overhead_process_per != ''){
            $('#overhead_process').val(((parseFloat(total_machine_cost) * parseFloat(overhead_process_per)) / 100).toFixed(2));
        }

        var icc_per = $('#icc_per').val();
        var wt_pcs = $('#wt_pcs').val();
        if(wt_pcs != 0 && wt_pcs != '' && icc_per !=0 && icc_per != ''){
            $('#icc_cost').val(((parseFloat(wt_pcs) * parseFloat(icc_per)) / 100).toFixed(2));
        }

        var rej_cost = $('#rej_cost').val();
        var overhead_process = $('#overhead_process').val();
        var icc_cost = $('#icc_cost').val();
        var sub_total = (parseFloat(rm_process) + parseFloat(rej_cost) + parseFloat(overhead_process) + parseFloat(icc_cost)).toFixed(3);
        $('#sub_total').val(sub_total);

        var profit_per = $('#profit_per').val();
        if(profit_per != 0 && profit_per != '' && sub_total !=0 && sub_total != ''){
            $('#profit_pcs').val(((parseFloat(profit_per) * parseFloat(sub_total)) / 100).toFixed(2));
        }
        var moq = $('#moq').val();
        var profit_pcs = $('#profit_pcs').val();
        if(moq != 0 && moq != '' && profit_pcs !=0 && profit_pcs != ''){
            $('#total_profit').val((parseFloat(moq) * parseFloat(profit_pcs)).toFixed(2));
        }

        var profit_pcs = $('#profit_pcs').val();
        if(sub_total != 0 && sub_total != '' && profit_pcs !=0 && profit_pcs != ''){
            $('#total_cost_pcs').val((parseFloat(sub_total) + parseFloat(profit_pcs)).toFixed(2));
        }
    });

    $(document).on("keyup",'.calNetRmCost',function() {
        var moq = $('#moq').val();
        var finish_length = $('#finish_length').val();
        var noof_bar = $('#noof_bar').val();
        var rm_rate = $('#rm_rate').val();
        var wt_pcs = $('#wt_pcs').val();
        var wt_finish = $('#wt_finish').val();
        var scrap_per_pcs = $('#scrap_per_pcs').val();
        var current_scrap_rate = $('#current_scrap_rate').val();
        var gross_val_pcs = $('#gross_val_pcs').val();
        var total_scrap_recover = $('#total_scrap_recover').val();
        var insp_cost = $('#insp_cost').val();



        //var otherGstAmtArray = $(".otherGstAmount").map(function(){return $(this).val();}).get();
        //var otherGstAmtSum = 0;
        //$.each(otherGstAmtArray,function(){otherGstAmtSum += parseFloat(this) || 0;});
    });
});

function totalmachinecost(){
    var mcostArray = $("input[name='mprocess_cost[]']").map(function(){return $(this).val();}).get();
    var mprocessSum = 0;
	$.each(mcostArray,function(){ mprocessSum += parseFloat(this) || 0;});
    $('#total_machine_cost').val((mprocessSum).toFixed(2));

    var hcostArray = $("input[name='hprocess_cost[]']").map(function(){return $(this).val();}).get();
    var hprocessSum = 0;
	$.each(hcostArray,function(){ hprocessSum += parseFloat(this) || 0;});
    $('#total_secondary_cost').val((hprocessSum).toFixed(2));

    var rm_cost = $('#net_rm_cost').val();
    $('#rm_process_cost').val((parseFloat(rm_cost) + parseFloat(hprocessSum) + parseFloat(mprocessSum)).toFixed(2));
}
</script>
