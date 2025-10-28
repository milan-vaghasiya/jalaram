<?php if($type != 2){ ?>
<div class="row">
	<div class="col-12">
		<table class="table">
		    <tr>
		        <td class="6 text-left" style="width:30%;font-weight:bold;padding:0px !important;"></td>
		        <td class="8 text-center" style="width:40%;letter-spacing: 2px;font-weight:bold;padding:0px !important;">GOODS RECEIPT NOTE</td>
		        <td class="6 text-right" style="width:30%;font-weight:bold;padding:0px !important;"></td>
		    </tr>
		</table>
		
		<table class="table top-table-border" style="margin-top:2px;">
			<tr>
				<td rowspan="4" style="width:65%;vertical-align:top;">
					<b>M/S. <?=$partyData->party_name?></b> <br>
					<small><?=$partyData->party_address?></small><br><br>
					Contact No. : <?=$partyData->party_mobile?><br>
					Email : <?=$partyData->contact_email?><br>
				</td>
				<th style="width:17%;vertical-align:top;">GRN No.</th>
				<td style="width:18%;vertical-align:top;"><?=getPrefixNumber($grnData->grn_prefix,$grnData->grn_no)?></td>
			</tr>
			<tr>
				<th>GRN Date</th><td><?=formatDate($grnData->grn_date)?></td>
			</tr>
			<tr>
				<th>Challan/Invoice No.: </th><td><?=$grnData->challan_no?></td>
			</tr>
            <tr>
				<th>PO No.: </th><td><?=(!empty($grnData->po_no)) ? getPrefixNumber($grnData->po_prefix,$grnData->po_no) : "" ;?></td>
			</tr>
		</table>
		
        <table class="table item-list-bb" style="margin-top:25px;">
			<tr>
				<th style="width:40px;">No.</th>
				<th style="width:110px;">Item Description</th>
				<th style="width:50px;">Qty(UOM)</th>
				<th style="width:50px;">Qty(kg)</th>
				<th style="width:110px;">F.G(Used In)</th>
				<th style="width:50px;">Heat/Batch No</th>
				<th style="width:110px;">Colour Code</th>
				<th style="width:110px;">Location</th>
				<th style="width:60px;">Price</th>
			</tr>
			<?php
				$i=1;
				if(!empty($grnData->itemData)):
					foreach($grnData->itemData as $row): 
						$item_name = str_replace(["\r\n", "\r", "\n"], "<br/>", $row->item_name);
                        $row->product_code = ''; $c=0;
                        if(!empty($row->fgitem_id)):
                            $la = explode(",",$row->fgitem_id);
                            if(!empty($la)){
                                foreach($la as $fgid){
                                    $fg = $this->grnModel->getFinishGoods($fgid);
                                    if(!empty($fg)):
                                        if($c==0){
                                            $row->product_code .= $fg->item_code;
                                        }else{
                                            $row->product_code .= '<br>'.$fg->item_code;
                                        }$c++;
                                    else:
                                        $row->product_code = "";
                                    endif;
                                }
                            }
                        endif;
						echo '<tr>';
							echo '<td class="text-center">'.$i++.'</td>';
							echo '<td>'.$item_name.'</td>';
							echo '<td class="text-right">'.$row->qty.'('.$row->unit_name.')</td>';
							echo '<td class="text-center">'.$row->qty_kg.'</td>';
							echo '<td class="text-center">'.$row->product_code.'</td>';
							echo '<td class="text-center">'.$row->batch_no.'</td>';
							echo '<td class="text-center">'.$row->color_code.'</td>';
							echo '<td class="text-center">'.$row->store_name.'</td>';
							echo '<td class="text-center">'.$row->price.'</td>';
						echo '</tr>';
					
					endforeach;
				endif;
			?>
		</table>
	</div>
</div>
<?php } if($type == 2){ ?>
    <table class="table item-list-bb" style="margin-top:25px;border:1px solid #000000;border-collapse:collapse;">
    	<tr>
    		<th style="width:40px;border:1px solid #000000;border-collapse:collapse;text-align:center;">No.</th>
    		<th style="width:110px;border:1px solid #000000;border-collapse:collapse;">Item Description</th>
    		<th style="width:50px;border:1px solid #000000;border-collapse:collapse;text-align:center;">Qty(UOM)</th>
    		<th style="width:50px;border:1px solid #000000;border-collapse:collapse;text-align:center;">Qty(kg)</th>
    		<th style="width:110px;border:1px solid #000000;border-collapse:collapse;text-align:center;">F.G(Used In)</th>
    		<th style="width:50px;border:1px solid #000000;border-collapse:collapse;text-align:center;">Heat/Batch No</th>
    		<th style="width:110px;border:1px solid #000000;border-collapse:collapse;text-align:center;">Colour Code</th>
    		<th style="width:110px;border:1px solid #000000;border-collapse:collapse;text-align:center;">Location</th>
    		<th style="width:60px;border:1px solid #000000;border-collapse:collapse;text-align:center;">Price</th>
    	</tr>
    	<?php
    		$i=1;
    		if(!empty($grnData->itemData)):
    			foreach($grnData->itemData as $row): 
    				$item_name = str_replace(["\r\n", "\r", "\n"], "<br/>", $row->item_name);
                    $row->product_code = ''; $c=0;
                    if(!empty($row->fgitem_id)):
                        $la = explode(",",$row->fgitem_id);
                        if(!empty($la)){
                            foreach($la as $fgid){
                                $fg = $this->grnModel->getFinishGoods($fgid);
                                if(!empty($fg)):
                                    if($c==0){
                                        $row->product_code .= $fg->item_code;
                                    }else{
                                        $row->product_code .= '<br>'.$fg->item_code;
                                    }$c++;
                                else:
                                    $row->product_code = "";
                                endif;
                            }
                        }
                    endif;
    				echo '<tr>';
    					echo '<td class="text-center" style="border:1px solid #000000;border-collapse:collapse;text-align:center;">'.$i++.'</td>';
    					echo '<td style="border:1px solid #000000;border-collapse:collapse;">'.$item_name.'</td>';
    					echo '<td class="text-right" style="border:1px solid #000000;border-collapse:collapse;text-align:center;">'.$row->qty.'('.$row->unit_name.')</td>';
    					echo '<td class="text-center" style="border:1px solid #000000;border-collapse:collapse;text-align:center;">'.$row->qty_kg.'</td>';
    					echo '<td class="text-center" style="border:1px solid #000000;border-collapse:collapse;text-align:center;">'.$row->product_code.'</td>';
    					echo '<td class="text-center" style="border:1px solid #000000;border-collapse:collapse;text-align:center;">'.$row->batch_no.'</td>';
    					echo '<td class="text-center" style="border:1px solid #000000;border-collapse:collapse;text-align:center;">'.$row->color_code.'</td>';
    					echo '<td class="text-center" style="border:1px solid #000000;border-collapse:collapse;">'.$row->store_name.'</td>';
    					echo '<td class="text-center" style="border:1px solid #000000;border-collapse:collapse;text-align:center;">'.$row->price.'</td>';
    				echo '</tr>';
    			
    			endforeach;
    		endif;
    	?>
    </table>
<?php } ?>