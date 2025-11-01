<form>
    <div class="col-md-12">
        <?php
        $scrapRate  = 0;
        if(!empty($dataRow->scrap_per)){
            $scrapRate = round((($dataRow->rm_rate * $dataRow->scrap_per)/100),2);
        }
        $rmProfit = 0;
        if(empty($dataRow->rm_profit) OR $dataRow->rm_profit == 0){
            $totalMfgCost = (!empty($mfgProcessList)?array_sum(array_column($mfgProcessList,'process_cost')):0);
            if($dataRow->rm_rate > $totalMfgCost){
                $rmProfit = 10;
            }else{
                $rmProfit = 20;
            }
        }else{
            $rmProfit = $dataRow->rm_profit;
        }
        ?>
        <div class="row">
            <input type="hidden" name="id"  value="<?=(!empty($dataRow->id))?$dataRow->id:''?>">
            <input type="hidden" name="item_id"  value="<?=(!empty($dataRow->item_id))?$dataRow->item_id:'';?>">
            <input type="hidden" name="ref_id"  value="<?=(!empty($dataRow->ref_id))?$dataRow->ref_id:'';?>">
            <input type="hidden" name="rev_no"  value="<?=(!empty($dataRow->rev_no))?$dataRow->rev_no:'';?>">
            <!-- <div class="col-md-6 form-group">
                <label for="cost_date">Date</label>
                <input type="date" name="cost_date" id="cost_date" class="form-control req" value="<?=(!empty($dataRow->cost_date))?$dataRow->cost_date:date('Y-m-d');?>">
            </div> -->
            <div class="col-md-6 form-group">
                <label for="moq">M. O. Qty.</label>
                <input type="text" name="moq" id="moq" class="form-control req numericOnly calcRm " value="<?=(!empty($dataRow->moq))?$dataRow->moq:'';?>" readonly>
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
                                <?=(!empty($dataRow->material_grade)?$dataRow->material_grade:'')?>
                            </td>
                        </tr>
                        <tr>
                            <th>Dimension</th>
                            <td>
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Shape</th>
                                        <td><?= $shape ?? '';?></td>
                                    </tr>

                                    <tr class="first_section">
                                        <th class="first_section_label">Diameter (mm)</th>
                                        <td><?= $dataRow->field1 ?? '';?></td> 
                                    </tr>
                                    
                                    <tr class="second_section d-none">
                                        <th class="second_section_label">Width (mm)</th>
                                        <td><?= $dataRow->field2 ?? '';?></td> 
                                    </tr>

                                    <tr class="third_section">
                                        <th class="third_section_label">Length (mm)</th>
                                        <td><?= $dataRow->field3 ?? '';?></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <th>Gross Weight Piece</th>
                            <td>
                                <input type="text" name="gross_wt" id="gross_wt" value="<?=$dataRow->gross_wt?>" class="form-control" readonly>
                            </td>
                        </tr>
                        <tr>
                            <th>Total Gross Weight</th>
                            <td>
                                <input type="text" name="total_gross_wt" id="total_gross_wt" value="<?=$dataRow->total_gross_wt?>" class="form-control" readonly>
                            </td>
                        </tr>
                        <tr>
                            <th>Req. Raw Material</th>
                            <td>
                                <input type="text"  id ="req_rm" value="<?=$dataRow->gross_wt * $dataRow->moq?>" class="form-control" readonly>
                            </td>
                        </tr>
                        
                        <tr>
                            <th>RM Rate</th>
                            <td>
                                <input type="text" name="rm_rate" id ="rm_rate" value="<?=(!empty($dataRow->rm_rate)? $dataRow->rm_rate : 0)?>" class="form-control  calRmCost floatOnly" readonly>
                            </td>
                        </tr>
                       
                        <tr>
                            <th>Finish Weight</th>
                            <td>
                                <input type="text" name="finish_wt" id ="finish_wt" value="<?=(!empty($dataRow->finish_wt)? $dataRow->finish_wt : 0)?>" class="form-control  calWt floatOnly">
                            </td>
                        </tr>
                        <tr>
                            <th>Scrap Weight</th>
                            <td>
                                <input type="text" name="scrap_wt" id ="scrap_wt" value="<?=(!empty($dataRow->scrap_wt)? $dataRow->scrap_wt : ($dataRow->gross_wt - $dataRow->finish_wt))?>" class="form-control  calRmCost floatOnly" readOnly>
                            </td>
                        </tr>
                        <tr>
                            <th>Scrap Rate</th>
                            <td>
                                <input type="text" name="scrap_rate" id ="scrap_rate" value="<?=(!empty($dataRow->scrap_rate)? $dataRow->scrap_rate : $scrapRate)?>" class="form-control  calRmCost floatOnly">
                            </td>
                        </tr>
                        <tr>
                            <th>RM Gross Value/Piece</th>
                            <td>
                                <input type="text"  id ="gross_val_pcs" value="<?=$dataRow->rm_rate * $dataRow->gross_wt?>" class="form-control calRmCost floatOnly" readOnly>
                            </td>
                        </tr>
                        <tr>
                            <th>Scrap Rate/Rs</th>
                            <td>
                                <input type="text" id ="total_scrap_recover" value="<?=$dataRow->scrap_rate * $dataRow->scrap_wt?>" class="form-control calRmCost floatOnly" readOnly>
                            </td>
                        </tr>
                        <tr>
                            <th>Receiving Inspection + Chemical +<br> Microstructure</th>
                            <td>
                                <input type="text" name="rcv_insp_rate" id ="rcv_insp_rate" value="<?=(!empty($dataRow->rcv_insp_rate)? $dataRow->rcv_insp_rate : 0)?>" class="form-control calRmCost  floatOnly">
                            </td>
                        </tr>
                         <tr>
                            <th>RM Profit(%)</th>
                            <td>
                                <input type="text" name="rm_profit" id ="rm_profit" value="<?=$rmProfit?>" class="form-control calRmCost  floatOnly">
                            </td>
                        </tr>
                    </body>
                    <tfoot class="thead-info">
                        <tr>
                            <th>I. Total Cost of RM</th>
                            <td>
                                <input type="text" name="total_rm_cost" id ="total_rm_cost" value="<?=(!empty($dataRow->total_rm_cost)? $dataRow->total_rm_cost : 0)?>" class="form-control floatOnly" readOnly>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            
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
                        <?php
                        $dataRow->mfg_process_cost = 0;
                        if(!empty($mfgProcessList)){
                            foreach($mfgProcessList AS $row){
                                ?>
                                <tr>
                                    <td><?=$row->process_name?></td>
                                    <td class="text-center"><?=(!empty($row->mhr)?$row->mhr:'')?></td>
                                    <td class="text-center"><?=(!empty($row->cycle_time)?$row->cycle_time:'')?></td>
                                    <td class="text-center"><?=$row->process_cost?></td>
                                </tr>
                                <?php
                                $dataRow->mfg_process_cost += $row->process_cost;
                            }
                        }
                        ?>
                    </tbody>
                    <tfoot class="thead-info">
                        <tr>
                            <th colspan="3">II. Manufacturing Cost</th>
                            <td class="text-center">
                                <input type="text" name="mfg_process_cost" id ="mfg_process_cost" value="<?=(!empty($dataRow->mfg_process_cost)? $dataRow->mfg_process_cost : 0)?>" class="form-control totalProCost floatOnly text-center" readOnly>
                            </td>
                        </tr>
                    </tfoot>
                </table>
                
                <table class="table excel_table table-bordered">
                    <thead class="thead-info">
                        <tr>
                            <th colspan="2" class="text-center">RM + Manufacturing  COST</th>
                            <th>
                                <input type="text" id ="rm_mfg_cost" value="<?=$dataRow->total_rm_cost+$dataRow->mfg_process_cost ?>" class="form-control toolCost floatOnly" readOnly>
                            </th>
                        </tr>
                        <tr>
                            <th>Overheads</th>
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
                                <input type="text" name="overheads_per" id ="overheads_per" value="<?=(!empty($dataRow->overheads_per)? $dataRow->overheads_per : 0)?>" class="form-control floatOnly">
                            </td>
                            <td>
                                <input type="text" name="overheads_cost" id ="overheads_cost" value="<?=(!empty($dataRow->overheads_cost)? $dataRow->overheads_cost : 0)?>" class="form-control floatOnly" readOnly>
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
                                <input type="text" id ="sub_total" value="<?=(!empty($dataRow->sub_total)? $dataRow->sub_total : 0)?>" class="form-control floatOnly" readOnly>
                            </td>
                        </tr>
                        <tr>
                            <th>Profit Percentage</th>
                            <td>
                                <input type="text" name="profit_per" id ="profit_per" value="<?=(!empty($dataRow->profit_per)? $dataRow->profit_per : 0)?>" class="form-control floatOnly">
                            </td>
                            <td>
                                <input type="text" name="profit_value" id ="profit_value" value="<?=(!empty($dataRow->profit_value)? $dataRow->profit_value : 0)?>" class="form-control floatOnly" readOnly>
                            </td>
                        </tr>
                        
                        <tr>
                            <th colspan="2">Final Cost Per Part</th>
                            <td>
                                <input type="text" name="final_cost" id ="final_cost" value="<?=(!empty($dataRow->final_cost)? $dataRow->final_cost : 0)?>" class="form-control floatOnly" readOnly>
                            </td>
                        </tr>
                        <tr>
                            <th colspan="2">Total Final Cost</th>
                            <td>
                                <input type="text"  id ="total_final_cost" value="<?=$dataRow->final_cost * $dataRow->moq?>" class="form-control floatOnly" readOnly>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</form>

<script>
$(document).ready(function() {
    setTimeout(function(){ $("#moq").trigger("change"); }, 50);
    
    $(document).on("keyup",'.calWt', function(){
        $('.error').html("");
        var gross_wt = parseFloat($('#gross_wt').val());
        var finish_wt = parseFloat($('#finish_wt').val());
        if(gross_wt != 0 && gross_wt != '' && finish_wt !=0 && finish_wt != ''){
            if(finish_wt < gross_wt ){
                $('#scrap_wt').val((gross_wt - finish_wt).toFixed(3));
            }else{
                $('#scrap_wt').val(0);
                $('.finish_wt').html('InValid Finish Weight.');
            }
        }
    });
    
    $("input[type='text']").change( function() {
        var gross_val = 0
        var gross_wt = $('#gross_wt').val();
        var rm_rate = $('#rm_rate').val();
        //Calculate Gross Value gross_wt * rm_rate
        if(gross_wt != 0 && gross_wt != '' && rm_rate !=0 && rm_rate != ''){
            gross_val = (parseFloat(gross_wt) * parseFloat(rm_rate)).toFixed(3);
            $('#gross_val_pcs').val(gross_val);
        }
        
        //Scrap Price Calculation
        var total_scrap = 0;
        var scrap_per_pcs = $('#scrap_wt').val();
        var current_scrap_rate = $('#scrap_rate').val();
        if(scrap_per_pcs != 0 && scrap_per_pcs != '' && current_scrap_rate !=0 && current_scrap_rate != ''){
            total_scrap = ((parseFloat(scrap_per_pcs) * parseFloat(current_scrap_rate))).toFixed(3);
            
            $('#total_scrap_recover').val(total_scrap);
        }

        var insp_cost = $('#rcv_insp_rate').val();

        var rm_cost = ((parseFloat(gross_val) - parseFloat(total_scrap)) + parseFloat(insp_cost)).toFixed(3);
        //RM Profit
        var rm_profit = $('#rm_profit').val();
        var rm_profit_val = 0;
        if(rm_profit != 0 && rm_profit != '' && sub_total !=0 && sub_total != ''){
            var rm_profit_val = ((parseFloat(rm_profit) * rm_cost) / 100).toFixed(2)
        }

        //Total RM Cost Calculation
        var total_rm_cost = parseFloat(rm_cost) + parseFloat(rm_profit_val);
        $('#total_rm_cost').val(total_rm_cost);

        //RM COST + MFG COST
        var rm_mfg_cost = (parseFloat(total_rm_cost) + parseFloat($("#mfg_process_cost").val()));
        $('#rm_mfg_cost').val(rm_mfg_cost);

        //Tool Cost Calculation
        var tool_per = $('#tool_per').val();
        var rm_mfg_cost = $('#rm_mfg_cost').val();
        if(rm_mfg_cost != 0 && rm_mfg_cost != '' && tool_per !=0 && tool_per != ''){
            $('#tool_cost').val(((parseFloat(rm_mfg_cost) * parseFloat(tool_per)) / 100).toFixed(2));
        }

        //Rej Cost calculation
        var rej_per = $('#rej_per').val();
        if(rm_mfg_cost != 0 && rm_mfg_cost != '' && rej_per !=0 && rej_per != ''){
            $('#rej_cost').val(((parseFloat(rm_mfg_cost) * parseFloat(rej_per)) / 100).toFixed(2));
        }

        //Oberhead Cost calculation
        var overheads_per = $('#overheads_per').val();
        if(rm_mfg_cost != 0 && rm_mfg_cost != '' && overheads_per !=0 && overheads_per != ''){
            $('#overheads_cost').val(((parseFloat(rm_mfg_cost) * parseFloat(overheads_per)) / 100).toFixed(2));
        }

        //ICC Cost Calculation
        var icc_per = $('#icc_per').val();
        if(rm_mfg_cost != 0 && rm_mfg_cost != '' && icc_per !=0 && icc_per != ''){
            $('#icc_cost').val(((parseFloat(rm_mfg_cost) * parseFloat(icc_per)) / 100).toFixed(2));
        }

        var rej_cost = $('#rej_cost').val();
        var overheads_cost = $('#overheads_cost').val();
        var icc_cost = $('#icc_cost').val();
        var tool_cost = $('#tool_cost').val();

        //All COST SUM and find sub total
        var sub_total = (parseFloat(rm_mfg_cost) + parseFloat(rej_cost) + parseFloat(overheads_cost) + parseFloat(icc_cost) + parseFloat(tool_cost)).toFixed(3);
        $('#sub_total').val(sub_total);

        //Calculate Profit
        var profit_per = $('#profit_per').val();
        if(profit_per != 0 && profit_per != '' && sub_total !=0 && sub_total != ''){
            $('#profit_value').val(((parseFloat(profit_per) * parseFloat(sub_total)) / 100).toFixed(2));
        }
        
        //Calculate Final Cost per pc With profit
        var profit_value = $('#profit_value').val();
        $('#final_cost').val((parseFloat(sub_total) + parseFloat(profit_value)).toFixed(2));
        
        //Calulate MOQ Wise final cost
        var moq = $('#moq').val();
        var total_final_cost = (parseFloat(sub_total) + parseFloat(profit_value)) * parseFloat(moq);
        $("#total_final_cost").val(total_final_cost.toFixed(2));
    });

    //Weight Calculation hide show fields
    var shape = "<?= $dataRow->shape;?>";
    if(shape == 'round_dia'){
        $('.first_section_label').text('Diameter (mm)');
        $('.second_section').addClass('d-none');
    }
    else if(shape == 'square'){
        $('.first_section_label').text('Width (mm)');
        $('.second_section').addClass('d-none');
    }
    else if(shape == 'rectangle'){
        $('.first_section_label').text('Width (mm)');
        $('.second_section_label').text('Height / Thickness (mm)');
        $('.second_section').removeClass('d-none');
    }
    else if(shape == 'pipe'){
        $('.first_section_label').text('Outer Diameter (mm)');
        $('.second_section_label').text('Inner Diameter (mm)');
        $('.second_section').removeClass('d-none');
    }
    else if(shape == 'hex'){
        $('.first_section_label').text('Flat to Flat (mm)');
        $('.second_section').addClass('d-none');
    }
    else if(shape == 'sheet'){
        $('.first_section_label').text('Width (mm)');
        $('.second_section_label').text('Height / Thickness (mm)');
        $('.second_section').removeClass('d-none');
    }
});
</script>
