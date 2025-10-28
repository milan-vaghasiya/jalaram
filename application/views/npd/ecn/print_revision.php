<div class="row">
	<div class="col-12">

        <table class="table item-list-bb">
            <tr class="text-center  bg-light">
                <th style="width:20%">ECN Received Date</th>
                <th style="width:20%">Target Date</th>
                <th style="width:20%">ECN Note No</th>
                <th style="width:20%">Code</th>
                <th style="width:20%">Part No.</th>
            </tr>
            <tr class="text-center">
                <td><?=formatDate($reviewData->ecn_received_date)?></td>
                <td><?=formatDate($reviewData->target_date)?></td>
                <td><?=$reviewData->ecn_prefix.$reviewData->ecn_note_no?></td>
                <td style="width:15%;"><?=$reviewData->item_code?></td>
                <td style="width:15%;"><?=$reviewData->part_no?></td>
            </tr>
            <tr>
                <th style="width:15%;" class="bg-light text-left">Part Description</th>
                <td style="width:30%;" class="bg-light text-left" colspan="4"><?=$reviewData->item_name?></td>
            </tr>
        </table>

        <table class="table item-list-bb" style="margin-top:5px;">
            <tr class="text-center">
                <th colspan="2" class="bg-light">OLD Revision</th>
                <th colspan="2" class="bg-light">New Revision</th>
            </tr>
            <tr  class="text-left">
                <th style="width:20%">Rev. No.</th>
                <td style="width:30%"><?=(!empty($oldRevData->rev_no) ? $oldRevData->rev_no : 'NA')?></td>
                <th style="width:20%">Rev. No.</th>
                <td style="width:30%"><?=$reviewData->rev_no?></td>
            </tr>
            <tr  class="text-left">
                <th>Org. Date/Rev. Date</th>
                <td><?=(!empty($oldRevData->rev_date) ? formatDate($oldRevData->rev_date) : 'NA')?></td>
                <th>Rev. Date</th>
                <td><?=formatDate($reviewData->rev_date)?></td>
            </tr>
            <!-- <tr  class="text-left">
                <th>ECO No.</th>
                <td><?=(!empty($oldRevData->ecn_no) ? $oldRevData->ecn_no : '')?></td>
                <th>ECO No.</th>
                <td><?=$reviewData->ecn_no?></td>
            </tr> -->
        </table>

        <table class="table item-list-bb" style="margin-top:5px;">
            <tr>
                <td  class="bg-light"> 
                    <b>[A] Details Of Change :-</b>
                </td>
            </tr>
            <tr>
                <td >
                    <?=$reviewData->remark?>
                </td>
            </tr>
        </table>

        <table class="table item-list-bb" style="margin-top:5px;">
            <tr>
                <td colspan="8" class="bg-light"><b>[B] Effect Of Changes :-</b></td>
            </tr>
            <tr>
                <th style="width:10%">Sr. No.</th>
                <th>Check Points</th>
                <th>Y/N/NA</th>
                <th>Completion Date</th>
                <th style="width:10%">Sr. No.</th>
                <th>Check Points</th>
                <th>Y/N/NA</th>
                <th>Completion Date</th>
            </tr>

            <?php
            $i=1;$j=1;
            if(!empty($reviewData->itemData)):
                $rowCount = ceil(count($reviewData->itemData)%2);
                foreach($reviewData->itemData as $row):
                    if($j==1){ ?><tr> <?php } ?>
                    <td class="text-center"><?=$i++?></td>
                    <td><?=!empty($row->title)?$row->title:''?></td>
                    <td class="text-center"><?=!empty($row->is_change)?$row->is_change:''?></td>
                    <td class="text-center"><?=!empty($row->completion_date)?formatDate($row->completion_date):''?></td>    
                <?php
                if($j%2 ==0){  ?></tr>  <?php
                    if($i <= count($reviewData->itemData)){ ?><tr><?php  }
                }elseif($j%2 !=0 && $i>count($reviewData->itemData)){ ?><td></td><td></td><td></td><td></td></tr> <?php }
                $j++;
                endforeach;
            endif;
            ?>
        </table>

        <table class="table item-list-bb text-left" style="margin-top:5px;">
            <tr>
                <td colspan="4" class="bg-light"><b>[C] Implementation Details :-</b></td>
            </tr>
            <tr>
                <th style="width:30%;">Existing Stock Qty.</th>
                <td style="width:20%;"><?=!empty($reviewData->existing_qty)?$reviewData->existing_qty:''?></td>
                <th style="width:30%;">WH & Intransit Qty.</th>
                <td style="width:20%;"><?=!empty($reviewData->wh_qty)?$reviewData->wh_qty:''?></td>
            </tr>
            <tr>
                <th >Inprocess Stock Qty.</th>
                <td ><?=!empty($reviewData->in_process_qty)?$reviewData->in_process_qty:''?></td>
                <th >RM Qty.</th>
                <td ><?=!empty($reviewData->rm_qty)?$reviewData->rm_qty:''?></td>
            </tr>
            <tr>
                <th>Effective Implementation Date</th>
                <td  colspan="3"><?=!empty($reviewData->eff_impl_date)?formatDate($reviewData->eff_impl_date):''?></td>
            </tr>
        </table>

        <table class="table item-list-bb" style="margin-top:5px;">
            <tr>
                <td colspan="6" class="bg-light"><b>[D] Action Plan & Status of Action Taken :-</b></td>
            </tr>
                <tr>
                    <th>Sr. No.</th>
                    <th>Old Description</th>
                    <th>New Description</th>
                    <th>Responsibility</th>
                    <th>Target Date</th>
                    <th>Verification Status</th>
                </tr>

                <?php
                $i=1; $status='';
                if(!empty($reviewData->itemData)):
                    foreach($reviewData->itemData as $row):
                        if(!empty($row->description) && $row->is_change == 'Y'):
                            if($row->status == 1) { $status = 'Approved'; }
                            elseif($row->status == 2) { $status = 'Rejected'; }
                            elseif($row->status == 3) { $status = 'Hold'; }
                            else  { $status = ""; }
                    ?>
                        <tr>
                            <td><?=$i++?></td>
                            <td><?=$row->old_description?></td>
                            <td><?=$row->description?></td>
                            <td><?=$row->emp_name?></td>
                            <td><?=formatDate($row->ch_target_date)?></td>
                            <td><?=$status?></td>
                        </tr>
                <?php
                        endif;
                    endforeach;
                endif;
                ?>
        </table>

        <!-- <table class="table item-list-bb" style="margin-top:5px;">
                <tr>
                    <td><b>Remark :-</b></td>
                </tr>
        </table> -->

        <table class="table item-list-bb" style="margin-top:5px;">
                <tr>
                    <td><b>Note :- ECN Note Complete Within 10 Days As Per Recevied Date.</b></td>
                </tr>
        </table>
            <?php
            $deptTd = ''; $empSige = ''; $aprvDate = '';
            $width = (100/(count($deptReview)+1));
            foreach($deptReview as $row){
                $deptTd.='<th style="width:'.$width.'">'.$row->dept_name.'</th>';
                $empSige.='<td class="text-center">'.$row->emp_name.'</td>';
                $aprvDate.='<td class="text-center">'.(!empty($row->approve_at)?formatDate($row->approve_at):'').'</td>';
            }
            ?>
        <table class="table item-list-bb" style="margin-top:5px;">
                <tr class="bg-light">
                    <th style="width:<?=$width?>" class="text-left">Name</th>
                    <?=$deptTd?>
                </tr>
                <tr >
                    <th class="text-left bg-light">Sign</th>
                    <?=$empSige?>
                </tr>
                <tr>
                    <th class="text-left bg-light">Date</th>
                    <?=$aprvDate?>
                </tr>
        </table>

    </div>
</div>