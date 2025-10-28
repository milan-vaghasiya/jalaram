<input type="hidden" id="id" name="id" value="">
<input type="hidden" id="rev_id" name="rev_id" value="<?=$rev_id?>">

<table class="table">
    <tr>
        <td class="fs-18 text-center" style="letter-spacing: 2px;font-weight:bold;padding:0px !important;">CHECK POINT REVIEW</td>
    </tr>
</table>

<table class="table table-bordered" style="margin-top:2px;">
    <tr class="text-center">
        <th colspan="2">OLD Revision</th>
        <th colspan="2">New Revision</th>
    </tr>
    <tr>
        <th> Code</th>
        <td><?=$reviewData->item_code?></td>
        <th> Code</th>
        <td><?=$reviewData->item_code?></td>
    </tr>
    <tr>
        <th>Part No.</th>
        <td><?=$reviewData->part_no?></td>
        <th>Part No.</th>
        <td><?=$reviewData->part_no?></td>
    </tr>
    <tr>
        <th>Part Description</th>
        <td><?=$reviewData->item_name?></td>
        <th>Part Description</th>
        <td><?=$reviewData->item_name?></td>
    </tr>
    <tr>
        <th>Rev. No.</th>
        <td><?=(!empty($revData->rev_no) ? $revData->rev_no : '')?></td>
        <th>Rev. No.</th>
        <td><?=$reviewData->rev_no?></td>
    </tr>
    <tr>
        <th>Rev. Date</th>
        <td><?=(!empty($revData->rev_date) ? formatDate($revData->rev_date) : '')?></td>
        <th>Rev. Date</th>
        <td><?=formatDate($reviewData->rev_date)?></td>
    </tr>
    <tr>
        <th>ECO No.</th>
        <td><?=(!empty($revData->ecn_no) ? $revData->ecn_no : '')?></td>
        <th>ECO No.</th>
        <td><?=$reviewData->ecn_no?></td>
    </tr>
</table>

<h5><u>Effect Of Changes : </u></h5>

<div class="table-responsive">
    <table class="table table-bordered">
            <tr>
                <th>#</th>
                <th>Check Points</th>
                <th>Y/N/NA</th>
                <th>Description</th>
                <th>Responsibility</th>
                <th>Target Date</th>
                <th>Completion Date</th>
                <th>Status</th>
            </tr>

            <?php
            $i=1; $status='';
            if(!empty($reviewData->itemData)):
                foreach($reviewData->itemData as $row):

                    if($row->status == 1)
                    {
                        $status = '<span class="badge badge-success m-1">Approved</span>';
                    }
                    elseif($row->status == 2)
                    {
                        $status = '<span class="badge badge-danger m-1">Rejected</span>';
                    }
                    elseif($row->status == 3)
                    {
                        $status = '<span class="badge badge-info m-1">Hold</span>';
                    }
                    else
                    {
                        $status = "";
                    }
            ?>
                <tr>
                    <td><?=$i++?></td>
                    <td><?=$row->title?></td>
                    <td><?=$row->is_change?></td>
                    <td><?=$row->description?></td>
                    <td><?=$row->emp_name?></td>
                    <td><?=formatDate($row->ch_target_date)?></td>
                    <td><?=formatDate($row->completion_date)?></td>
                    <td><?=$status?></td>
                </tr>
            <?php
                endforeach;
            endif;
            ?>
    </table>
</div>
<div class="table-responsive">
    <table class="table table-bordered">
        <tr>
            <th style="width:25%;">Existing Stock Qty.</th>
            <?php
            if(($empData->ecn_stock == 1)){
                ?>
                <td style="width:75%;">
                <input type="text" id="qty" name="qty1" class="form-control numericOnly" value="<?=(!empty(($ext_qty->qty)) ? floatval($ext_qty->qty) : 0)?>" <?=($empData->ecn_stock == 1)?'':'disabled="true"'?>>
                <input type="hidden" id="qty_id" name="qty_id" value="1" <?=($empData->ecn_stock == 1)?'':'disabled="true"'?>>
                <input type="hidden" id="qty_label" name="qty_label" value="Existing Stock Qty." <?=($empData->ecn_stock == 1)?'':'disabled="true"'?>>
                <input type="hidden" id="sys_qty" name="sys_qty" value="<?=(!empty(($ext_qty->qty)) ? floatval($ext_qty->qty) : 0)?>" <?=($empData->ecn_stock == 1)?'':'disabled="true"'?>>
                </td>
                <?php
            }else{
                echo '<td>'.(!empty(($ext_qty->qty)) ? floatval($ext_qty->qty) : 0).'</td>';
            }
            ?>
            <td><?=(!empty(($ext_qty->qty)) ? floatval($ext_qty->qty) : 0)?></td>
            
        </tr>
        <tr>
            <th style="width:25%;">WH & Intransit Qty.</th>
            <?php
                if(($empData->ecn_stock == 2)){
                    ?>
                        <td style="width:75%;">
                            <input type="text" id="qty" name="qty" class="form-control numericOnly" value="0" <?=($empData->ecn_stock == 2)?'':'disabled="true"'?>>
                            <input type="hidden" id="qty_id" name="qty_id" value="2" <?=($empData->ecn_stock == 2)?'':'disabled="true"'?>>
                            <input type="hidden" id="qty_label" name="qty_label" value="WH & Intransit Qty." <?=($empData->ecn_stock == 2)?'':'disabled="true"'?>>
                            <input type="hidden" id="sys_qty" name="sys_qty" value="0" <?=($empData->ecn_stock == 2)?'':'disabled="true"'?>>
                        </td>
                    <?php
                }else{
                    echo '<td>0</td>';
                }
                ?>
                <td>0</td>
            
        </tr>
        <tr>
            <th style="width:25%;">Inprocess Stock Qty.</th>
            <?php
            if(($empData->ecn_stock == 3)){
                ?>
                <td style="width:75%;">
                    <input type="text" id="qty" name="qty" class="form-control numericOnly" value="<?=(!empty(($wip_qty->wip_qty)) ? floatval($wip_qty->wip_qty) : 0)?>" <?=($empData->ecn_stock == 3)?'':'disabled="true"'?>>
                    <input type="hidden" id="qty_id" name="qty_id" value="3" <?=($empData->ecn_stock == 3)?'':'disabled="true"'?>>
                    <input type="hidden" id="qty_label" name="qty_label" value="Inprocess Stock Qty." <?=($empData->ecn_stock == 3)?'':'disabled="true"'?>>
                    <input type="hidden" id="sys_qty" name="sys_qty" value="<?=(!empty(($wip_qty->wip_qty)) ? floatval($wip_qty->wip_qty) : 0)?>" <?=($empData->ecn_stock == 3)?'':'disabled="true"'?>>
                </td>
                <?php
            }else{
                echo '<td>'.(!empty(($wip_qty->wip_qty)) ? floatval($wip_qty->wip_qty) : 0).'</td>';
            }
            ?>
            <td><?=(!empty(($wip_qty->wip_qty)) ? floatval($wip_qty->wip_qty) : 0)?></td>
        </tr>
        <tr>
            <th style="width:25%;">RM Qty.</th>
            <?php
            if(($empData->ecn_stock == 4)){
                ?>
                <td style="width:75%;">
                    <input type="text" id="qty" name="qty" class="form-control numericOnly" value="<?=(!empty(($rm_qty->qty)) ? floatval($rm_qty->qty) : 0)?>" <?=($empData->ecn_stock == 4)?'':'disabled="true"'?>>
                    <input type="hidden" id="qty_id" name="qty_id" value="4" <?=($empData->ecn_stock == 4)?'':'disabled="true"'?>>
                    <input type="hidden" id="qty_label" name="qty_label" value="RM Qty." <?=($empData->ecn_stock == 4)?'':'disabled="true"'?>>
                    <input type="hidden" id="sys_qty" name="sys_qty" value="<?=(!empty(($rm_qty->qty)) ? floatval($rm_qty->qty) : 0)?>" <?=($empData->ecn_stock == 4)?'':'disabled="true"'?>>
                </td>
                <?php
            }else{
                echo '<td>'.(!empty(($rm_qty->qty)) ? floatval($rm_qty->qty) : 0).'</td>';
            }
            ?>
            <td><?=(!empty(($rm_qty->qty)) ? floatval($rm_qty->qty) : 0)?></td>
            
        </tr>
    </table>
</div>
<?php if($view == 1): ?>
    <?php
        $deptTd = ''; $empSige = ''; $aprvDate = '';
        $width = (100/(count($deptReview)+1));
        foreach($deptReview as $row){
            $deptTd.='<th style="width:'.$width.'" class="text-center">'.$row->dept_name.'</th>';
            $empSige.='<td class="text-center">'.$row->emp_name.'</td>';
            $aprvDate.='<td class="text-center">'.(!empty($row->approve_at)?formatDate($row->approve_at):'').'</td>';
        }
    ?>
    <table class="table table-bordered" >
            <tr >
                <th style="width:<?=$width?>" class="text-left">Name</th>
                <?=$deptTd?>
            </tr>
            <tr >
                <th class="text-left">Sign</th>
                <?=$empSige?>
            </tr>
            <tr>
                <th class="text-left">Date</th>
                <?=$aprvDate?>
            </tr>
    </table>
    <h5><u>Implementation Details : </u></h5>
    <div class="col-md-3">
        <label for="eff_impl_date">Effective Implementation Date</label>
        <input type="date" name="eff_impl_date" id="eff_impl_date" class="form-control" value="<?=date("Y-m-d")?>">
    </div>
<?php endif; ?>