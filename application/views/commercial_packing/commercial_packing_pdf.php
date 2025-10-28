<?php
    $itemHtml = '';$totalGrossWt = 0; $totalWoodWt = 0;
    if(!empty($dataRow->itemData))
    {
        $hsnDescArray = array_unique(array_column($dataRow->itemData,'hsn_desc'));
        $i=1;$j=1;
        foreach($hsnDescArray as $hsnDesc):
            $itemHtml .= '
                    <tr>
                        <th class="text-center">'.(($j==1)?"Sr.No.":"").'</th>
                        <th class="text-center">
                            Description Of Goods
                            <br>('.$hsnDesc.')
                        </th>
                        <th class="text-center">'.(($j==1)?"HSN Code":"").'</th>
                        <th class="text-center">'.(($j==1)?"Qty":"").'</th>
                        <th class="text-center">'.(($j==1)?"Net Weight <br> per pcs.(kg)":"").'</th>
                        <th class="text-center">'.(($j==1)?"Total Net <br> Weight (Kg)":"").'</th>
                        <th class="text-center" colspan="2">'.(($j==1)?"Total Gross <br> Weight (kg)":"").'</th>
                    </tr>';
            
            $p=0;$r=0;
            $itemRows = Array();$oldPack = '';
            foreach($dataRow->itemData as $row):
                if($row->hsn_desc == $hsnDesc):
                    if($row->package_no!=$oldPack)
                    {
                        $p = $r;
                        $itemRows[$p]['total_package'] = 0;
                        $itemRows[$p]['grossWt'] = $row->wooden_weight;
                        $totalWoodWt += $row->wooden_weight;
                    }
                    $itemRows[$r]['item_name'] = '<b>'.$row->PartNo.' - '.$row->item_name.'</b>';
                    $itemRows[$r]['hsn_code'] = $row->hsn_code;
                    $itemRows[$r]['qty'] = sprintf("%.3f",$row->qty);
                    $itemRows[$r]['price'] = sprintf("%.3f",$row->price);
                    $itemRows[$r]['amount'] = sprintf("%.3f",$row->amount);
                    $itemRows[$p]['total_package']++;
                    $itemRows[$p]['grossWt'] += ($row->totalPackWt + $row->pack_weight);
                    
                    $oldPack = $row->package_no;$r++;
                endif;
            endforeach;
            $r=1;$totalPackage = 1;
            foreach($itemRows as $row)
            {
                if($r==1){
                    $itemHtml .= '<tr>
                                    <td class="text-center">'.$i++.'</td>
                                    <td class="text-left">'.$row['item_name'].'</td>
                                    <td class="text-center">'.$row['hsn_code'].'</td>
                                    <td class="text-center">'.$row['qty'].'</td>
                                    <td class="text-center">'.$row['price'].'</td>
                                    <td class="text-center">'.$row['amount'].'</td>
                                    <td rowspan="'.$row['total_package'].'" class="text-center" colspan="2">'.$row['grossWt'].'</td>
                                </tr>';
                    $totalPackage = $row['total_package']; $totalGrossWt += $row['grossWt'];
                }else{
                    $itemHtml .= '<tr>
                                    <td class="text-center">'.$i++.'</td>
                                    <td class="text-left">'.$row['item_name'].'</td>
                                    <td class="text-center">'.$row['hsn_code'].'</td>
                                    <td class="text-center">'.$row['qty'].'</td>
                                    <td class="text-center">'.$row['price'].'</td>
                                    <td class="text-center">'.$row['amount'].'</td>
                                </tr>';
                }
                if($totalPackage==$r){$r=1;}else{$r++;}
            }
            //$itemHtml .= '</tbody>';
            $j++;
        endforeach;
    }
    else
    {
        $itemHtml .= '
                        <tr>
                            <th class="text-center">Sr.No.</th>
                            <th class="text-center">Description Of Goods</th>
                            <th class="text-center">HSN Code</th>
                            <th class="text-center">Qty</th>
                            <th class="text-center">Net Weight <br> per pcs.(kg)</th>
                            <th class="text-center">Total Net <br> Weight (Kg)</th>
                            <th class="text-center" colspan="2">Total Gross <br> Weight (kg)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr colspan="8" class="text-center">No item found.</tr>
                    </tbody>';
    }
?>
<!--<html>
    <head>
        <link rel="icon" type="image/png" sizes="16x16" href="<?=base_url('assets/images/favicon.png')?>">  
    </head>
    <body>
        <div class="row">
	        <div class="col-12">-->
                <table class="table item-list-bb" >
                    <thead>
                        <tr>
                            <th colspan="8" class="text-center" style="font-size:15px;"><b>Packing List</b></th>
                        </tr>
                        <tr>
                            <td colspan="4" rowspan="3" class="text-left">
                                <b>Exporter :</b>	<br>			
                                <b><?=$companyInfo->company_name?></b>	<br>	
                                <?=$companyInfo->company_address?>
                            </td>
                            <td  colspan="2" class="text-left">
                                <b style="padding-top: 1px;vertical-align:top;">Invoice No.:</b> <?=$dataRow->doc_no?><br>
                                <b>Date : </b> <?=(!empty($dataRow->doc_date))?date("d-m-Y",strtotime($dataRow->doc_date)):""?>
                            </td> 
                            <td  colspan="2" class="text-left">
                                <b style="padding-top: 1px;vertical-align:top;">Exporters's Ref. No. :</b><br>
                                IEC NO: <?=$companyInfo->company_pan_no?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-left">
                                <b style="padding-top: 1px;vertical-align:top;">Buyer's PO No : </b> <?=$dataRow->cust_po_no?><br>
                                <b>Date : </b><?=$dataRow->cust_po_date?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-left">
                                <b>Other References : </b>
                                GSTIN:  <?=$companyInfo->company_gst_no?> PAN : <?=$companyInfo->company_pan_no?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" rowspan="2" class="text-left">
                                <b>Consignee : </b><br>				
                                <b><?=$dataRow->party_name?></b><br>				
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
                                <b>Country of Origin</b><br>
                                <?=$companyInfo->company_country?>
                            </td> 
                            <td  colspan="2" class="text-center">
                                <b>Country of Final Destination</b><br>
                                <?=$dataRow->country_of_final_destonation?>
                            </td>
                        </tr>
                        <tr>
                            <td  colspan="2"  class="text-center">
                                <b>Pre- Carriage by</b><br>
                                <?=$dataRow->pre_carriage_by?>
                            </td> 
                            <td  colspan="2" class="text-center">
                                <b>Place of receipt by Pre-Carrier</b><br>
                                <?=$dataRow->place_of_rec_by_pre_carrier?>
                            </td>
                            <td  colspan="4" class="text-center">
                                <b>Terms Of Delivery And Payment</b><br>
                                <?=nl2br($dataRow->terms_conditions)?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2"  class="text-center">
                                <b>Vessel / Flight</b><br>
                                <?=$dataRow->vessel_flight?>
                            </td> 
                            <td  colspan="2" class="text-center">
                                <b >Port Of Loading</b><br>
                                <?=$dataRow->port_of_loading?>
                            </td>
                            <td  colspan="2" rowspan="2" class="text-right">
                                <b>Total Net Weight  : </b><br>
                                <b>Total Gross Weight : </b> 
                            </td> 
                            <td  colspan="2" rowspan="2" class="text-left">
                                <?=sprintf("%.3f",array_sum(array_column($dataRow->itemData,'amount')))?> kg<br>
                                <?=sprintf("%.3f",$totalGrossWt)?> Kg
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2"  class="text-center">
                                <b>Port Of Discharge</b><br>
                                <?=$dataRow->port_of_discharge?>
                            </td> 
                            <td  colspan="2" class="text-center">
                                <b>Place Of Delivery</b><br>
                                <?=$dataRow->place_of_delivery?>
                            </td>
                        </tr>
                    <!--</tbody>
                </table>
                <table class="table item-list-bb" repeat-header="1">-->
                    <?=$itemHtml?>
                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-left">
                                <?php
                                    //if($dataRow->no_of_wooden_box > 0):
                                        // echo "Total ".$dataRow->no_of_wooden_box." Wooden Boxes";
                                    //else:
                                        // $packageNo = array_unique(array_column($packageNum,'package_no'));
                                        // sort($packageNo);
                                        // $packageNo = array_values($packageNo);
                                    //endif;
                                    $packageNo = array_unique(array_column($packageNum,'package_no'));
                                    sort($packageNo);
                                    $packageNo = array_values($packageNo); 
                                    $packageNoCount = count($packageNo);
                                    
                                    echo "Total ".$packageNoCount.(($totalWoodWt > 0)? " Wooden Boxes" : " Boxes");
                                ?>
                            </th>
                            <th class="text-right">Total : </th>
                            <th class="text-center"><?=sprintf("%.3f",array_sum(array_column($dataRow->itemData,'amount')))?></th>
                            <th class="text-center" colspan="2"><?=sprintf("%.3f",$totalGrossWt)?></th>
                        </tr>
						<tr>
                            <td colspan="8" class="text-center">
                                <b>
                                    S/Marks :  <?=$partyData->smark?> - 
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
                                <?=$dataRow->remark?>
                            </td>
                        </tr>    
                        <tr>
                            <td colspan="4" class="text-left"><b>Declaration:</b><br><br>
                                We declare that this invoice shows the actual price <br>
                                of goods described and that all particulars are true & correct.</br><br>
                            </td>
                            <td colspan="4" class="text-left">
                                <b>For, <?=$companyInfo->company_name?></b><br>
                                <?=$authorise_sign?><br>
                                <b>SIGNATURE & DATE:</b>
                            </td>
                        </tr>
                    </tfoot>
                </table>
                <!--<table class="table item-list-bb" >
                    <tbody>
                        	
                    </tbody>
                </table>
            </div>
        </div> 
    </body>
</html>-->
