<!--<link href="<?=base_url();?>assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css" rel="stylesheet">
<link href="<?=base_url();?>assets/css/style.css?v=<?=time()?>" rel="stylesheet">-->
<div class="row">
	<div class="col-12">
		<table class="table"><tr><td class="fs-18 text-center" style="letter-spacing: 2px;font-weight:bold;padding:0px !important;">PURCHASE ORDER</td></tr></table>
		
		<table class="table top-table-border" style="margin-top:2px;">
			<tr>
				<td rowspan="3" style="width:60%;vertical-align:top;">
					<b>M/S. <?=$poData->party_name?></b> <br>
					<small><?=$poData->party_address?></small><br>
					<b>Kind. Attn. : </b><?=$poData->contact_person?><br>
					<b>Contact : </b><?=$poData->party_mobile?> <?=(!empty($poData->contact_email) ? " | ".$poData->contact_email : '')?><br>
					<b>GSTIN : </b><?=$poData->gstin ?>
				</td>
				<th style="width:12%;vertical-align:top;">PO No.</th>
				<td style="width:18%;vertical-align:top;"><?=getPrefixNumber($poData->po_prefix,$poData->po_no)?></td>
			</tr>
			<tr>
				<th>PO Date</th><td><?=formatDate($poData->po_date)?></td>
			</tr>
			<tr>
				<th>Ref. No.|Date</th><td><?=$poData->quotation_no?><?=(!empty($poData->quotation_date)) ? ' | '.formatDate($poData->quotation_date) : ""?></td>
			</tr>
		</table>
		
		<table class="table item-list-bb" style="margin-top:10px;">
			<tr>
				<th style="width:40px;">No.</th>
				<th class="text-left">Item Description</th>
				<th style="width:120px;">Size</th>
				<th style="width:60px;">Make</th>
				<th style="width:60px;">HSN/SAC</th>
				<th style="width:60px;">Qty</th>
				<th style="width:60px;">Rate</th>
				<th style="width:60px;">Disc</th>
				<th style="width:90px;">Amount</th>
			</tr>
			<?php
				$i=1;$totalQty = 0;$migst=0;$mcgst=0;$msgst=0;
				if(!empty($poData->itemData)):
					foreach($poData->itemData as $row):
						$itemDesc = '['.$row->category_code.'] '.$row->category_name;
						//if(!empty($row->size)){$itemDesc .= '<b>SIZE : </b>'.$row->size;}
						//if(!empty($row->size) AND !empty($row->make)){$itemDesc .= ' | ';}
						//if(!empty($row->make)){$itemDesc .= '<b>MAKE : </b>'.$row->make;}
						$itemDesc .= (!empty($row->delivery_date)) ? '<br><small>Delivery Date :'.formatDate($row->delivery_date).'</small>' : '';
						
						echo '<tr>';
							echo '<td class="text-center">'.$i++.'</td>';
							echo '<td>'.$itemDesc.'</td>';
							echo '<td class="text-center">'.$row->size.'</td>';
							echo '<td class="text-center">'.$row->make.'</td>';
							echo '<td class="text-center">'.$row->hsn_code.'898752</td>';
							echo '<td class="text-center">'.floatval($row->qty).' <small>Nos</small></td>';
							echo '<td class="text-right">'.$row->price.'</td>';
							echo '<td class="text-right">'.$row->disc_amt.'</td>';
							echo '<td class="text-right">'.$row->amount.'</td>';
						echo '</tr>';
						$totalQty += $row->qty;
						if($row->igst > $migst){$migst=$row->igst;$mcgst=$row->cgst;$msgst=$row->sgst;}
					endforeach;
				endif;
				
				$rwspan= 6;
				$gstRow='<tr>
            				<th colspan="2" class="text-right">CGST @'.$mcgst.'%</th>
            				<td class="text-right">'.sprintf('%.2f',$poData->cgst_amt).'</td>
            			</tr>';
				$gstRow.='<tr>
            				<th colspan="2" class="text-right">SGST @'.$msgst.'%</th>
            				<td class="text-right">'.sprintf('%.2f',$poData->sgst_amt).'</td>
            			</tr>';
				
				$party_gstin = (!empty($poData->gstin)) ? $poData->gstin : '';
        		$party_stateCode = (!empty($poData->gstin)) ? substr($poData->gstin, 0, 2) : '';
        		
        		if(!empty($party_gstin))
        		{
        			if($party_stateCode!="24")
        			{
        				$gstRow='<tr>
                				<th colspan="2" class="text-right">IGST @'.$migst.'%</th>
                				<td class="text-right">'.sprintf('%.2f',$poData->igst_amt).'</td>
                			</tr>';$rwspan = 5;
        			}
        		}
			?>
			<tr>
				<th colspan="5" class="text-right">Total Qty.</th>
				<th class="text-center"><?=$totalQty?></th>
				<th colspan="2" class="text-right">Sub Total</th>
				<th class="text-right"><?=sprintf('%.2f',$poData->amount)?></th>
			</tr>
			<tr>
				<th colspan="6" rowspan="<?=$rwspan?>">Amount In Words : <?=numToWordEnglish($poData->net_amount)?></th>
				<th colspan="2" class="text-right">P & F</th>
				<td class="text-right"><?=sprintf('%.2f',$poData->packing_charge)?></td>
			</tr>
			<!--<tr>
				<th colspan="2" class="text-right">Freight</th>
				<td class="text-right"><?=sprintf('%.2f',$poData->freight_amt)?></td>
			</tr>-->
			<tr>
				<th colspan="2" class="text-right">Taxable Amount</th>
				<th class="text-right"><?=sprintf('%.2f',$poData->taxableAmt)?></th>
			</tr>
			<?=$gstRow?>
			<tr>
				<th colspan="2" class="text-right">Round Off</th>
				<td class="text-right"><?=sprintf('%.2f',$poData->round_off)?></td>
			</tr>
			<tr>
				<th colspan="2" class="text-right">Grand Total</th>
				<th class="text-right"><?=sprintf('%.2f',$poData->net_amount)?></th>
			</tr>
		</table>
		<h4>Terms & Conditions :-</h4>
		<table class="table top-table" style="margin-top:10px;">
			<?php
				if(!empty($poData->terms_conditions)):
					$terms = json_decode($poData->terms_conditions);
					foreach($terms as $row):
						echo '<tr>';
							echo '<th class="text-left fs-11" style="width:140px;">'.$row->term_title.'</th>';
							echo '<td class=" fs-11"> : '.$row->condition.'</td>';
						echo '</tr>';
					endforeach;
				endif;
			?>
		</table>
		
		
	</div>
</div>