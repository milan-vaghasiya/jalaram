<?php
    $itemHtml = '';$totalGrossWt = 0;$totalWoodWt = 0; $ratePerPcs = 0;
    if(!empty($itemData))
    {
        $hsnDescArray = array_unique(array_column($itemData,'hsn_desc'));
        $i=1;$j=1;$totalNetWt = 0;
        foreach($hsnDescArray as $hsnDesc):
            $itemHtml .= '<thead>
                            <tr>
                                <th class="text-center">'.(($j==1)?"Sr.No.":"").'</th>
                                <th class="text-center">
                                    Description Of Goods
                                    <br>'.(!empty($hsnDesc)?'('.$hsnDesc.')':'').'
                                </th>
                                <th class="text-center">'.(($j==1)?"HSN Code":"").'</th>
                                <th class="text-center">'.(($j==1)?"Qty":"").'</th>
                                <th class="text-center">'.(($j==1)?"Rate Per Pcs. <br> (".$dataRow->currency.")":"").'</th>
                                <th class="text-center">'.(($j==1)?"Total Amt. <br> (".$dataRow->currency.")":"").'</th>
                            </tr>
                        </thead><tbody>';
                    
            $p=0;$r=0;
            $itemRows = Array();$oldPack = '';
            foreach($itemData as $row):
                if($row->hsn_desc == $hsnDesc):
                    if($row->package_no!=$oldPack)
                    {
                        $p = $r;
                        $itemRows[$p]['total_package'] = 0;
                        $itemRows[$p]['grossWt'] = $row->wooden_weight;
                        $totalWoodWt += $row->wooden_weight;
                    }
                    $itemRows[$r]['item_name'] = '<b>'.$row->item_name.'</b>';
                    $itemRows[$r]['hsn_code'] = $row->hsn_code;
                    $itemRows[$r]['qty'] = sprintf("%.3f",$row->qty);
                    $itemRows[$r]['price'] = sprintf("%.3f",$row->price);
                    
                    $itemRows[$r]['netWeight'] = sprintf("%.3f",$row->netWeight);
                    $itemRows[$p]['total_package']++;
                    $itemRows[$p]['grossWt'] += ($row->totalPackWt + $row->pack_weight);
                    
                    $oldPack = $row->package_no;$r++;
                endif;
            endforeach;
            $r=1;$totalPackage = 1;
            foreach($itemRows as $row)
            {
                $totalAmt = ($row['qty'] * $row['price']);
                $totalGrossWt += $totalAmt;
                $ratePerPcs += $row['price'];

                if($r==1)
                {
                    $itemHtml .= '<tr>
                                    <td class="text-center">'.$i++.'</td>
                                    <td class="text-left">'.$row['item_name'].'</td>
                                    <td class="text-center">'.$row['hsn_code'].'</td>
                                    <td class="text-center">'.$row['qty'].'</td>
                                    <td class="text-center">'.$row['price'].'</td>
                                    <td class="text-center">'.sprintf("%.3f",$totalAmt).'</td>
                                </tr>';
                    $totalPackage = $row['total_package'];
                }
                else
                {
                    $itemHtml .= '<tr>
                                    <td class="text-center">'.$i++.'</td>
                                    <td class="text-left">'.$row['item_name'].'</td>
                                    <td class="text-center">'.$row['hsn_code'].'</td>
                                    <td class="text-center">'.$row['qty'].'</td>
                                    <td class="text-center">'.$row['price'].'</td>
                                    <td class="text-center">'.sprintf("%.3f",$totalAmt).'</td>
                                </tr>';
                }
                if($totalPackage==$r){$r=1;}else{$r++;}
                $totalNetWt += $row['netWeight'];
            }
            $itemHtml .= '</tbody>';
            $j++;
        endforeach;
    }
    else
    {
        $itemHtml .= '<thead>
                            <tr>
                                <td class="text-center"><b>Sr.No.</b></td>
                                <td class="text-center"><b>Description Of Goods</b></td>
                                <td class="text-center"><b>HSN Code</b></td>
                                <td class="text-center"><b>Qty</b></td>
                                <td class="text-center"><b>Rate Per Pcs. <br> (<?=$dataRow->currency?>) </b></td>
                                <td class="text-center"><b>Total Amt. <br> (<?=$dataRow->currency?>)</b></td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr colspan="6" class="text-center">No item found.</tr>
                        </tbody>';
    }
?>
<table class="table item-list-bb" >
    <thead>
        <tr>
            <th colspan="8" class="text-center" style="font-size:15px;"><b>Invoice</b></th>
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
            <?=sprintf("%.3f",$totalNetWt)?> kg <br>
            <?=sprintf("%.3f",(array_sum(array_column($packageNum,'netWeight')) + array_sum(array_column($packageNum,'pack_weight')) + array_sum(array_column($packageNum,'wooden_weight'))))?> kg 
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
    </table>
    <table class="table item-list-bb" repeat-header="1">
        <?=$itemHtml?>
        <tfoot>
            <tr>
                <td colspan="2" class="text-left" style="border-bottom:none;"></td>
                <td colspan="3" class="text-center">Freight</td>
                <td class="text-center"><?=sprintf("%.3f",0)?></td>
            </tr>
            <tr>
                <td colspan="2" class="text-left" style="border-bottom:none;border-top:none;"></td>
                <td colspan="3" class="text-center">Insuracnce</td>
                <td class="text-center"><?=sprintf("%.3f",0)?></td>
            </tr>
            <tr>
                <th colspan="2" class="text-left" style="border-bottom:none;border-top:none;"></th>
                <th colspan="3" class="text-center">Total Amt. in <?= $dataRow->currency;?></th>
                <th class="text-center"><?=sprintf("%.3f",$totalGrossWt)?></th>
            </tr>
            <tr>
                <td colspan="6" class="text-left" style="border-bottom:none;border-top:none;"></td>
            </tr>
        </tfoot>
    </table>
    <table class="table item-list-bb" >
        <tbody>
            <tr>
                <th colspan="4" class="text-left">
                <?php
                        $packageNo = array_unique(array_column($packageNum,'package_no'));
                        sort($packageNo);
                        $packageNo = array_values($packageNo); 
                        $packageNoCount = count($packageNo);
                        
                        echo "Total ".$packageNoCount.(($totalWoodWt > 0)? " Wooden Boxes" : " Boxes");
                ?>
                </th>
                <th class="text-right">Total</th>
                <th class="text-center"><?php /*sprintf("%.3f",$ratePerPcs)*/?></th>
                <th class="text-center" colspan="2"><?php /*sprintf("%.3f",$totalGrossWt)*/?></th>
            </tr>
            <tr>
                <td colspan="8" class="text-center">
                    <b>
                        S/Marks :   - <?= $partyData->smark;?>
                        <?php
                            $packageNo = array_unique(array_column($packageNum,'package_no'));
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
        </tbody>
    </table>