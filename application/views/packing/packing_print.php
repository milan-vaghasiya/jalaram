<div class="row">
    <div class="col-12">
        <table class="table item-list-bb" repeat-header="1">
            <thead>
                <tr>
                    <th colspan="2">
                        <?= $packingMasterData->trans_prefix.sprintf("%04d",$packingMasterData->trans_no) ?>
                    </th>
                    <th colspan="8">
                        Annexure A
                    </th>
                    <th colspan="2">
                        <?php 
                            if($pdf_type == 0):
                                echo 'Internal Copy';
                            elseif($pdf_type == 1):
                                echo 'Customer Copy';
                            elseif($pdf_type == 2):
                                echo 'Custom Copy';
                            else:
                                echo '';
                            endif;
                        ?>
                    </th>
                </tr>
                <tr>
                    <th>Package No.</th>
                    <th style="width:10%;">Box Size (cm)</th>
                    <th>Item Name</th>
                    <th>Qty Per Box (Nos)</th>
                    <th>Total Box (Nos)</th>
                    <th>Total Qty. (Nos)</th>
                    <th>Net Weight Per Piece (kg)</th>
                    <th>Total Net Weight (kg)</th>
                    <th>Packing Weight (kg)</th>
                    <th>Item Gross Weight (kg)</th>
                    <th>Wooden Box Weight (kg)</th>
                    <th>Packing Gross Weight (kg)</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $totalBoxQty = 0; $totalBoxNos = 0; $totalQty = 0; $totalpackGrWt =0;
            $totalNetWt = 0; $totalPackWt = 0; $totalWoodenWt = 0; $totalGrossWt = 0;
            if (!empty($packingData)) {
                $itemIds = array();
                foreach ($packingData as $pack) {
                    $itemIds = array();
                    $transData = $pack->itemData; 
                    $woodenWt = max(array_column($pack->itemData,'wooden_weight'));
                    $itemGrWt =$transData[0]->netWeight + $transData[0]->pack_weight;
                    $packGrWt = (array_sum(array_column($pack->itemData,'netWeight')) + array_sum(array_column($pack->itemData,'pack_weight'))) + $woodenWt;
            ?>
                    <tr>
                        <td rowspan="<?=count($pack->itemData)?>" class="text-center"><?= $pack->package_no ?></td>
                        <td rowspan="<?=count($pack->itemData)?>" class="text-center"><?= $pack->wooden_size ?></td>
                        <?php 
                            if(!in_array($transData[0]->item_id,$itemIds)):
                                $itemRowspan = 0;
                                $itemRowspan = count(array_keys(array_column($transData,'item_id'), $transData[0]->item_id));
                                $itemIds[] = $transData[0]->item_id;
                                
                                if($pdf_type == 0):
                                    echo '<td class="text-left" rowspan="'.$itemRowspan.'">'.$transData[0]->item_code.'</td>';
                                elseif($pdf_type == 1):
                                    echo '<td class="text-left" rowspan="'.$itemRowspan.'">'.$transData[0]->part_no.' - '.$transData[0]->item_name.'</td>';
                                elseif($pdf_type == 2):
                                    echo '<td class="text-left" rowspan="'.$itemRowspan.'">'.$transData[0]->item_alias.'</td>';
                                else:
                                    echo '<td class="text-left" rowspan="'.$itemRowspan.'">'.$transData[0]->item_name.'</td>';
                                endif;
                            endif;
                        ?>
                        <td class="text-right"><?= round($transData[0]->qty_box,0) ?></td>
                        <td class="text-right"><?= round($transData[0]->total_box,0) ?></td>
                        <td class="text-right"><?= round($transData[0]->total_qty,0) ?></td>
                        <td class="text-right"><?= $transData[0]->wpp?></td>
                        <td class="text-right"><?= $transData[0]->netWeight ?></td>
                        <td class="text-right"><?= $transData[0]->pack_weight ?></td>
                        <td class="text-right"><?= sprintf("%.3f",$itemGrWt) ?></td>
                        <td class="text-right" rowspan="<?= count($pack->itemData) ?>"><?= $woodenWt ?></td>
                        <td class="text-right" rowspan="<?= count($pack->itemData) ?>"><?= sprintf("%.3f",$packGrWt) ?></td>
                    </tr>

                    <?php
                    $i = 1;
                    foreach ($transData as $row) {
                        $itemGrWt = $row->netWeight + $row->pack_weight;
                        if ($i > 1) {
                    ?>
                            <tr>
                                <?php 
                                    if(!in_array($row->item_id,$itemIds)):
                                        $itemRowspan = 0;
                                        $itemRowspan = count(array_keys(array_column($transData,'item_id'), $row->item_id));
                                        $itemIds[] = $row->item_id;
                                        
                                        if($pdf_type == 0):
                                            echo '<td class="text-left" rowspan="'.$itemRowspan.'">'.$row->item_code.'</td>';
                                        elseif($pdf_type == 1):
                                            echo '<td class="text-left" rowspan="'.$itemRowspan.'">'.$row->part_no.' - '.$row->item_name.'</td>';
                                        elseif($pdf_type == 2):
                                            echo '<td class="text-left" rowspan="'.$itemRowspan.'">'.$row->item_alias.'</td>';
                                        else:
                                            echo '<td class="text-left" rowspan="'.$itemRowspan.'">'.$row->item_name.'</td>';
                                        endif;
                                    endif;
                                ?>
                                <td class="text-right"><?= round($row->qty_box,0) ?></td>
                                <td class="text-right"><?= round($row->total_box,0) ?></td>
                                <td class="text-right"><?= round($row->total_qty,0) ?></td>
                                <td class="text-right"><?= $row->wpp ?></td>
                                <td class="text-right"><?= $row->netWeight ?></td>
                                <td class="text-right"><?= $row->pack_weight ?></td>
                                <td class="text-right"><?= sprintf("%.3f",$itemGrWt) ?></td>
                            </tr>
            <?php
                        }
                        $i++;
                        $totalBoxQty += $row->qty_box;
                        $totalBoxNos += $row->total_box;
                        $totalQty += $row->total_qty;
                        $totalNetWt += $row->netWeight;
                        $totalPackWt += $row->pack_weight;
                        $totalGrossWt += $itemGrWt;
                    }
                    $totalWoodenWt += $woodenWt;
                    $totalpackGrWt += $packGrWt;
                    
                    
                }
            }
            ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="7" class="text-right">Total</th>
                    <th class="text-right"><?=sprintf("%.3f",$totalNetWt)?></th>
                    <th class="text-right"><?=sprintf("%.3f",$totalPackWt)?></th>
                    <th class="text-right"><?=sprintf("%.3f",$totalGrossWt)?></th>
                    <th class="text-right"><?=sprintf("%.3f",$totalWoodenWt)?></th>
                    <th class="text-right"><?=sprintf("%.3f",$totalpackGrWt)?></th>
                </tr>
                <?php if($pdf_type == 0 || $pdf_type == 1){ ?>
                    <tr>
                        <td class="text-left" colspan="12"> 
                            <b>1. Transport by : </b> <?=$packingMasterData->trans_way?><br>
                            <b>2. Delivery Terms : </b> <?=$packingMasterData->delivery_terms?><br>
                            <b>3. Container Type : </b> <?=$packingMasterData->container_type?><br>
                            <b>4. Packing Type : </b> <?=$packingMasterData->export_pck_type?><br>
                            <b>5. Port of Loading : </b> <?=$packingMasterData->port_loading?><br>
                            <b>6. Port of Dispatch : </b> <?=$packingMasterData->port_dispatch?><br>
                            <b>7. Destination Country : </b> <?=$packingMasterData->destination_country?><br>
                            <b>8. Nomination Agent : </b> <?=$packingMasterData->nomination_agent?><br><br>
                            
                            <b>Note: </b><br>
                            We are planning to dispatch the shipment as per above details. <br>
                            If you have any query / correction please inform us immediately. <br>
                            No amendment  / change will be possible once the invoice is generated on government portal. <br>
                        </td>
                    </tr>
                <?php } ?>
            </tfoot>
        </table>
    </div>
</div>