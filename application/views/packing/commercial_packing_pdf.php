<?php
    $tbodyData = "";
    $totalpackGrWt =0; $totalNetWt = 0; $totalWoodenWt = 0;
    if (!empty($packingData)) {
        $itemIds = array();
        foreach ($packingData as $pack) {
            $itemIds = array();
            $transData = $pack->itemData; 
            $woodenWt = max(array_column($pack->itemData,'wooden_weight'));
            $itemGrWt =$transData[0]->netWeight + $transData[0]->pack_weight;
            $packGrWt = (array_sum(array_column($pack->itemData,'netWeight')) + array_sum(array_column($pack->itemData,'pack_weight'))) + $woodenWt;

                $tbodyData  .= '<tr>
                    <td rowspan="'.count($pack->itemData).'" class="text-center">'.$pack->package_no.'</td>';

                    if(!in_array($transData[0]->item_id,$itemIds)):
                        $itemRowspan = 1;
                        $itemRowspan = count(array_keys(array_column($transData,'item_id'), $transData[0]->item_id));
                        $itemIds[] = $transData[0]->item_id;
                        
                        if($pdf_type == 0):
                            $tbodyData .= '<td class="text-left" rowspan="'.$itemRowspan.'">'.$transData[0]->item_name.'</td>';
                        elseif($pdf_type == 1):
                            $tbodyData .= '<td class="text-left" rowspan="'.$itemRowspan.'">'.$transData[0]->part_no.' - '.$transData[0]->item_name.'</td>';
                        elseif($pdf_type == 2):
                            $tbodyData .= '<td class="text-left" rowspan="'.$itemRowspan.'">'.$transData[0]->item_alias.'</td>';
                        else:
                            $tbodyData .= '<td class="text-left" rowspan="'.$itemRowspan.'">'.$transData[0]->item_name.'</td>';
                        endif;
                    endif;
                
                $tbodyData .= '<td class="text-right">'.$transData[0]->hsn_code.'</td>
                    <td class="text-right">'.round($transData[0]->total_qty,0).'</td>
                    <td class="text-right">'.$transData[0]->wpp.'</td>
                    <td class="text-right">'.$transData[0]->netWeight.'</td>
                    <td class="text-right" colspan="2" rowspan="'.count($pack->itemData).'">'.sprintf("%.3f",$packGrWt).'</td>
                </tr>';

                $i = 1;
                foreach ($transData as $row) {
                    $itemGrWt = $row->netWeight + $row->pack_weight;
                    if ($i > 1) {
                        $tbodyData  .='<tr>'; 
                                        if(!in_array($row->item_id,$itemIds)):
                                            $itemRowspan = 1;
                                            $itemRowspan = count(array_keys(array_column($transData,'item_id'), $row->item_id));
                                            $itemIds[] = $row->item_id;
                                            
                                            if($pdf_type == 0):
                                                $tbodyData  .= '<td class="text-left" rowspan="'.$itemRowspan.'">'.$row->item_name.'</td>';
                                            elseif($pdf_type == 1):
                                                $tbodyData  .= '<td class="text-left" rowspan="'.$itemRowspan.'">'.$row->part_no.' - '.$row->item_name.'</td>';
                                            elseif($pdf_type == 2):
                                                $tbodyData  .= '<td class="text-left" rowspan="'.$itemRowspan.'">'.$row->item_alias.'</td>';
                                            else:
                                                $tbodyData  .= '<td class="text-left" rowspan="'.$itemRowspan.'">'.$row->item_name.'</td>';
                                            endif;
                                        endif;
                                            
                        $tbodyData  .= '<td class="text-right">'.$row->hsn_code.'</td>
                                        <td class="text-right">'.round($row->total_qty,0).'</td>
                                        <td class="text-right">'.$row->wpp.'</td>
                                        <td class="text-right">'.$row->netWeight.'</td>
                                    </tr>';
                    }
                    $i++;
                    $totalNetWt += $row->netWeight;
                }
            $totalWoodenWt += $woodenWt;
            $totalpackGrWt += $packGrWt;
        }
    }
?>
<table class="table item-list-bb" >
    <thead>
        <tr>
            <th colspan="8" class="text-center" style="font-size:15px;"><b>Packing List</b></th>
        </tr>
        <tr>
            <td colspan="4" rowspan="3" class="text-left">
                <b>Exporter :</b>	<br>			
                <b><?=$companyData->company_name?></b>	<br>	
                <?=$companyData->company_address?>
            </td>
            <td  colspan="2" class="text-left">
                <b style="padding-top: 1px;vertical-align:top;">Invoice No.:</b> xxxxxx<br>
                <b>Date : </b> xxxxxx 
            </td> 
            <td  colspan="2" class="text-left">
                <b style="padding-top: 1px;vertical-align:top;">Exporters's Ref. No. :</b><br>
                IEC NO: <?=$companyData->company_pan_no?>
            </td>
        </tr>
        <tr>
            <td colspan="4" class="text-left">
                <b style="padding-top: 1px;vertical-align:top;">Buyer's PO No : </b> <?= $dataRow->doc_no;?><br>
                <b>Date : </b> <?= formatDate($dataRow->packing_date)?>
            </td>
        </tr>
        <tr>
            <td colspan="4" class="text-left">
                <b>Other References : </b>
                GSTIN:  <?=$companyData->company_gst_no?> PAN : <?=$companyData->company_pan_no?>
            </td>
        </tr>
        <tr>
            <td colspan="4" rowspan="2" class="text-left">
                <b>Consignee : </b><br>				
                <b><?=$partyData->party_name?></b><br>				
                <?=$partyData->party_address?><br> 						
                Contact No. : <?=$partyData->party_mobile?>
            </td>
            <td colspan="4"  class="text-left">
                <b>Buyer (if Other Than Consignee) or Notify Party:<br>
                <b>Same as Consignee</b>
            </td>
        </tr>
        <tr>
            <td  colspan="2" class="text-center">
                <b>Country of Origin</b><br> India
                
            </td> 
            <td  colspan="2" class="text-center">
                <b>Country of Final Destination</b><br> <?= $dataRow->destination_country;?>
                
            </td>
        </tr>
        <tr>
            <td  colspan="2"  class="text-center">
                <b>Pre- Carriage by</b><br> -
                
            </td> 
            <td  colspan="2" class="text-center">
                <b>Place of receipt by Pre-Carrier</b><br> -
                
            </td>
            <td  colspan="4" class="text-center">
                <b>Terms Of Delivery And Payment</b><br> <?= $dataRow->delivery_payment_terms?>
                
            </td>
        </tr>
        <tr>
            <td colspan="2"  class="text-center">
                <b>Vessel / Flight</b><br> -
                
            </td> 
            <td  colspan="2" class="text-center">
                <b >Port Of Loading</b><br> <?= $dataRow->port_loading;?>
                
            </td>
            <td  colspan="2" rowspan="2" class="text-right">
                <b>Total Net Weight  : </b><br>
                <b>Total Gross Weight : </b> 
            </td> 
            <td  colspan="2" rowspan="2" class="text-left">
            <?=sprintf("%.3f",$totalNetWt)?> kg<br>
            <?=sprintf("%.3f",$totalpackGrWt)?> kg
            </td>
        </tr>
        <tr>
            <td colspan="2"  class="text-center">
                <b>Port Of Discharge</b><br> <?= $dataRow->port_dispatch?>
                
            </td> 
            <td  colspan="2" class="text-center">
                <b>Place Of Delivery</b><br> <?= $dataRow->delivery_place?>
                
            </td>
        </tr>
    </thead>
</table>
<table class="table item-list-bb" repeat-header="1">
    <thead>
        <tr>
            <th class="text-center" width="8%">Sr.No.</th>
            <th class="text-center" width="16%">Description Of Goods</th>
            <th class="text-center" width="14%">HSN Code</th>
            <th class="text-center" width="14%">Qty</th>
            <th class="text-center" width="16%">Net Weight <br> per pcs.(kg)</th>
            <th class="text-center" width="16%">Total Net <br> Weight (Kg)</th>
            <th class="text-center" width="16%" colspan="2">Total Gross <br> Weight (kg)</th>
        </tr>
    </thead>
    <tbody>
        <?= $tbodyData;?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="4" class="text-left">
               <?php
                    $packageNo = array_unique(array_column($packingData,'package_no'));
                    sort($packageNo);
                    $packageNo = array_values($packageNo); 
                    $packageNoCount = count($packageNo);
                    
                    echo "Total ".$packageNoCount.(($totalWoodenWt > 0)? " Wooden Boxes" : " Boxes");
               ?>
            </th>
            <th class="text-right">Total : </th>
            <th class="text-right"><?=sprintf("%.3f",$totalNetWt)?></th>
            <th class="text-right" colspan="2"><?=sprintf("%.3f",$totalpackGrWt)?></th>
        </tr>
        <tr>
            <td colspan="8" class="text-center">
                <b>
                    S/Marks :   - <?= $partyData->smark;?>
                    <?php
                        $packageNo = array_unique(array_column($packingData,'package_no'));
                        sort($packageNo);
                        $packageNo = array_values($packageNo); 
                        
                        $packageNoCount = count($packageNo);
                        $lastPackageNo = str_pad($packageNoCount, 2, '0', STR_PAD_LEFT);
                        if($packageNoCount > 2):                                        
                            echo str_pad($packageNo[0], 2, '0', STR_PAD_LEFT)."/".$lastPackageNo.", ".str_pad($packageNo[1], 2, '0', STR_PAD_LEFT)."/".$lastPackageNo." Upto ".str_pad($packageNo[($packageNoCount - 1)], 2, '0', STR_PAD_LEFT)."/".$lastPackageNo;
                        else:
                            echo str_pad($packageNo[0], 2, '0', STR_PAD_LEFT)."/".$lastPackageNo;
                            if(isset($packageNo[1])):
                                echo ", ".str_pad($packageNo[1], 2, '0', STR_PAD_LEFT)."/".$lastPackageNo;
                            endif;
                        endif;
                    ?>
                </b>
            </td> 
        </tr> 
        <tr>
            <td colspan="8" class="text-center">
                
            </td>
        </tr>    
        <tr>
            <td colspan="4" class="text-left"><b>Declaration:</b><br><br>
                We declare that this invoice shows the actual price <br>
                of goods described and that all particulars are true & correct.</br><br>
            </td>
            <td colspan="4" class="text-left">
                <b>For, <?=$companyData->company_name?></b><br>
                <?= $authorise_sign?><br>
                <b>SIGNATURE & DATE:</b>
            </td>
        </tr>
    </tfoot>
</table>