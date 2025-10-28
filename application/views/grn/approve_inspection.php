<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($inInspectData->id))?$inInspectData->id:""?>" />
            <input type="hidden" name="grn_id" value="<?=(!empty($inInspectData->grn_id))?$inInspectData->grn_id:""?>" />
            <input type="hidden" name="grn_trans_id" id="grn_trans_id" value="<?=(!empty($inInspectData->grn_trans_id))?$inInspectData->grn_trans_id:""?>" />
            <input type="hidden" name="pfc_rev_no" value="<?=(!empty($inInspectData->pfc_rev_no))?$inInspectData->pfc_rev_no:""?>" />
            <input type="hidden" name="fg_item_id" id="fg_item_id" value="<?=(!empty($inInspectData->fg_item_id))?$inInspectData->fg_item_id:""?>" />
                            
            <?php
            $sample_size = (!empty($paramData)?$paramData[0]->iir_size:10);
                          
            ?>
            <input type="hidden" name="sample_size" value="<?=$sample_size?>">

            <table class="table table-bordered text-left" style="margin-top:2px;">
                <tr>
                    <td colspan="5" style="width:60%;vertical-align:top;">
                        <b>Suplier Name : <?=(!empty($inInspectData->party_name)) ? $inInspectData->party_name:""?></b> <br><br>
                        <b>Part Name :</b> <?=(!empty($inInspectData->item_name)) ? $inInspectData->item_name:""?> <br><br>
                        <b>Part No.:</b> <?=(!empty($inInspectData->fgCode)) ?$inInspectData->fgCode:""?><?php (!empty($inInspectData->charge_no)) ?'/'.$inInspectData->charge_no:""?> <br><br>
                        <b>Material Grade :</b> <?=(!empty($inInspectData->material_grade)) ? $inInspectData->material_grade:""?><br>
                    </td>
                    <td colspan="5" style="width:40%;vertical-align:top;">
                        <b>Receive Date :</b> <?=(!empty($inInspectData->grn_date)) ? formatDate($inInspectData->grn_date) : ""?> <br><br>
                        <b>Lot Qty.:</b> <?=(!empty($inInspectData->qty)) ? $inInspectData->qty:""?> <br><br>
                        <b>Batch No.:</b> <?=(!empty($inInspectData->batch_no)) ? $inInspectData->batch_no:""?> <br><br>
                        <b>Color Code:</b> <?=(!empty($inInspectData->color_code)) ? $inInspectData->color_code:""?><br>
                    </td>
                </tr>
            </table>
            <table id="testReport" class="table table-bordered align-items-center">
                <thead class="thead-info">
                    <tr>
                        <th style="width:5%;" rowspan="2">#</th>
                        <th>Name Of Agency</th>
                        <th>Test Description</th>
                        <th>Test Report No</th>
                        <th>T.C. File</th>
                    </tr>
                </thead>
                <tbody id="testReportBody">
                    <?php
                        $i=1; $tbodyData = "";
                      if (!empty($tcReportData)) :
                        foreach ($tcReportData as $row) :
                            $tdDownload = '';
                            if(!empty($row->tc_file)) {  $tdDownload = '<a href="'.base_url('assets/uploads/test_report/'.$row->tc_file).'" target="_blank"><i class="fa fa-download"></i></a>'; } 
                            $tbodyData .=  '<tr>
                                <td>' . $i++ . '</td>
                                <td>' . $row->name_of_agency . '</td>
                                <td>' . $row->test_description . '</td>
                                <td>' . $row->test_report_no . '</td>
                                <td>' . $tdDownload . '</td>
                            </tr>';
                        endforeach;
                    else :
                        $tbodyData .= '<tr><td colspan="5" style="text-align:center;">No Data Found</td></tr>';
                    endif;
                    echo $tbodyData;
                    ?>
                </tbody>
            </table>
            <table id="preDispatchtbl" class="table table-bordered generalTable">
				<thead class="thead-info">
                    <tr>
                        <th rowspan="2">Sr. No.</th>
                        <th rowspan="2">Product Characteristic</th>
                        <th rowspan="2">Product Specification / Tolerance</th>
                        <th rowspan="2">Evaluation / Measurement Technique</th>
                        <th colspan="<?= floatval($sample_size) ?>">Observation</th>
                        <th rowspan="2">Status</th>
                        <th rowspan="2">Decision</th>
                    </tr>
                    <?php
                        for($c=0;$c<$sample_size;$c++):
                            ?>
                            <th><?=$c+1?></th>
                            <?php
                        endfor;
                    ?>
                </thead>
                <tbody>
                <?php
                    $tbodyData="";$i=1; 
                    
                    if(!empty($paramData)):
                        foreach($paramData as $row):
                            $obj = New StdClass;
                            $cls="";
                            if(!empty($row->lower_limit) OR !empty($row->upper_limit)):
                                $cls="floatOnly";
                            endif;
                            $diamention ='';
                            if($row->requirement==1){ $diamention = $row->min_req.'/'.$row->max_req ; }
                            if($row->requirement==2){ $diamention = $row->min_req.' '.$row->other_req ; }
                            if($row->requirement==3){ $diamention = $row->max_req.' '.$row->other_req ; }
                            if($row->requirement==4){ $diamention = $row->other_req ; }
                            if(!empty($inInspectData)):
                                $obj = json_decode($inInspectData->observation_sample); 
                            endif;
                            $inspOption = '';
                            $inspOpt  = '<option value="Accepted" '.((!empty($obj->{$row->id}) && ($obj->{$row->id}[$sample_size]=='Accepted'))?'selected':'').' >Accepted</option>
                            <option value="Accepted UD" '.((!empty($obj->{$row->id}) && ($obj->{$row->id}[$sample_size]=='Accepted UD'))?'selected':'').'>Accepted UD</option>
                            <option value="Rejection" '.((!empty($obj->{$row->id}) && ($obj->{$row->id}[$sample_size]=='Rejection'))?'selected':'').'>Rejection</option>';
                                            
                            $tbodyData.= '<tr>
                                        <td style="text-align:center;">'.$i++.'</td>
                                        <td>' . $row->product_param . '</td>
                                        <td>' . $diamention . '</td>
                                        <td>' . $row->iir_measur_tech . '</td>';

                            for($c=0;$c<$sample_size;$c++):
                                if(!empty($obj->{$row->id})):
                                    $tbodyData .= '<td style="width:40px;"><input type="hidden" name="sample'.($c+1).'_'.$row->id.'" id="sample'.($c+1).'_'.$i.'" class="form-control text-center parameter_limit'.$cls.'" value="'.$obj->{$row->id}[$c].'" data-min="'.$row->min_req.'" data-max="' . $row->max_req . '" data-requirement="' . $row->requirement . '" data-row_id ="'.$i.'" >'.$obj->{$row->id}[$c].'</td>';
                                else:
                                    $tbodyData .= '<td><input type="hidden" name="sample'.($c+1).'_'.$row->id.'" id="sample'.($c+1).'_'.$i.'" class="form-control text-center parameter_limit'.$cls.'" value=""  data-min="'.$row->min_req.'" data-max="' . $row->max_req . '" data-requirement="' . $row->requirement . '" data-row_id ="'.$i.'"></td>';
                                endif;
                            endfor;
                            if(!empty($obj->{$row->id})):
                                $tbodyData .= '
                                <td style="width:60px;"><input type="hidden" name="status_'.$row->id.'" id="status_'.$i.'" class="form-control  text-center" value="'.$obj->{$row->id}[$sample_size].'">'.$obj->{$row->id}[$sample_size].'</td>

                                <td style="width:120px;"><select name="result_'.$row->id.'" id="result_'.$i.'" class="form-control  text-center" value="'.$obj->{$row->id}[$sample_size].'">'.$inspOpt.'</select></td>
                                </tr>';
                            else:
                                
                                $tbodyData .= ' <td style="width:60px;"><input type="hidden" name="status_'.$row->id.'" id="status_'.$i.'" class="form-control  text-center" value=""></td>
                                <td style="width:120px;"><select name="result_'.$row->id.'" id="result_'.$i.'" class="form-control  text-center" value="">'.$inspOpt.'</select></td>
                                </tr>';
                            endif;
                         
                        endforeach;
                    else:
                        $tbodyData .='<tr><th colspan="'.(5+$sample_size).'">No data available.</th></tr>';
                    endif;
                    echo $tbodyData;
                ?>
                </tbody>
            </table>
            <?php
            $chk = '<img src="' . base_url('assets/images/check-square.png') . '" style="width:20px;display:inline-block;vertical-align:middle;">';
            $unchk = '<img src="' . base_url('assets/images/uncheck-square.png') . '" style="width:20px;display:inline-block;vertical-align:middle;">';
            ?>
            <table class="table item-list-bb">
                <tr class="text-left">
                    <th>Status</th>
                    <td colspan="3"><?= (!empty($inInspectData->supplier_tc)) ? $chk : $unchk ?> Supplier TC </td>
                    <td><b>Checked By</b> : <?= ((!empty($inInspectData->create_name)) ? $inInspectData->create_name : "") ?></td>
                </tr>
                
            </table>
            <div class="col-md-12">
                <label for="approval_remarks">Comment</label>
                <input type="text" id="approval_remarks" name="approval_remarks" class="form-control" value ="<?=$inInspectData->approval_remarks?>"/>
            </div>
        </div>
    </div>
</form>