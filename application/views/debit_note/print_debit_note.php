<div class="row">
	<div class="col-12">
		<table class="table"><tr><td class="fs-18 text-center" style="letter-spacing: 2px;font-weight:bold;padding:0px !important;">DEBIT NOTE </td></tr></table>
		
		<table class="table top-table-border" style="margin-top:2px;">
			<tr>
				<td rowspan="4" style="width:50%;vertical-align:top;">
					<b>M/S.<?= $dnData->party_name?></b> <br>
					<small><?=$partyData->party_address?></small><br><br>
					<b>Place Of Supply : </b> <?= $partyData->city_name ?> - <?=$partyData->party_pincode?><br><br><br>
					<b>GSTIN : </b><?= $partyData->gstin?>
				</td>
		
				<th style="width:25%;vertical-align:top;">Debit Note No.</th>
				<td style="width:25%;vertical-align:top;"><?=getPrefixNumber($dnData->trans_prefix,$dnData->trans_no)?></td>
			</tr>
			
			<tr>
				<th>Debit Note Date</th><td><?= formatDate($dnData->trans_date) ?></td>
			</tr>
			<tr>
				<th>Orignal Bill No.</th><td><?= $dnData->doc_no ?></td>
			</tr>
			<tr>
				<th>Orignal Bill Date</th><td><?= (!empty($invData->trans_date) ? formatDate($invData->trans_date) : '') ?></td>
			</tr>
			<tr>
				<td colspan="3" class="text-left" ><b>Reason For :</b> <?= $dnData->remark ?> </td>
			</tr>
		</table>
		<table class="table item-list-bb" style="margin-top:10px;">
			<tr>
				<th rowspan="2" style="width:40px;">Sr No.</th>
				<th rowspan="2">Product Name</th>
				<th rowspan="2">HSN/SAC Code</th>
				<th rowspan="2">Qty</th>
				<th rowspan="2">Rate</th>
				<th rowspan="2">Taxable Amount</th>
				<th rowspan="2">Gst %</th>
				<th colspan="2">Tax Amount</th>
				<th rowspan="2">Net Amount</th>
			</tr>
            <tr>
                <th>Central</th>
				<th>State/UT</th>
            </tr>
            <?php
				$i=1;$totalTA = 0;$totalNA = 0;$totalIGST = 0;$totalSGST = 0; $blnkRow=8;
				if(!empty($dnData->itemData)):
					foreach($dnData->itemData as $row):
						$igst = 0; $sgst = 0;
						$party_gstin = (!empty($partyData->gstin)) ? $partyData->gstin : '';
						$party_stateCode = (!empty($partyData->gstin)) ? substr($partyData->gstin, 0, 2) : '';
						
						if(!empty($party_gstin))
						{
							if($party_stateCode!="24")
							{
								$igst = $row->igst_amount;
							}else{
								$igst = $row->cgst_amount;
								$sgst = $row->sgst_amount;
							}
						}

						$taxableAmt = ($row->qty * $row->price);
					
						echo '<tr>';
							echo '<td class="text-center">'.$i++.'</td>';
							echo '<td>'.$row->item_name.'</td>';
							echo '<td class="text-center">'.$row->hsn_code.'</td>';
							echo '<td class="text-right">'.$row->qty.'</td>';
							echo '<td class="text-center">'.$row->price.'</td>';
							echo '<td class="text-center">'.$row->taxable_amount.'</td>';
							echo '<td class="text-right">'.$row->gst_per.'</td>';
							echo '<td class="text-right">'.$igst.'</td>';
							echo '<td class="text-right">'.$sgst.'</td>';
							echo '<td class="text-right">'.$row->net_amount.'</td>';
						echo '</tr>';
						$totalTA += $row->taxable_amount; $totalNA += $row->net_amount;$totalIGST += $igst;$totalSGST += $sgst;
					endforeach;
				endif;
			?>
			<tr>
				<th colspan="4"  class="text-left">Gst No :<?= $companyData->company_gst_no?></th>
                <td  class="text-left"><b>Total</b></td>
                <td  class="text-center"><b><?=sprintf('%.3f',$totalTA)?></b></td>
                <td  class="text-left"><b></b></td>
                <td  class="text-right"><b><?=sprintf('%.3f',$totalIGST)?></b></td>
                <td  class="text-right"><b><?=sprintf('%.3f',$totalSGST)?></b></td>
                <td  class="text-right"><b><?=sprintf('%.3f',$totalNA)?></b></td>
			</tr>
            
			<tr>
				<td rowspan="2" colspan="7" class="text-left"><b>Total Gst : </b><?=numToWordEnglish($igst + $sgst)?> <br><br><b>Bill Amount: </b><?=numToWordEnglish($dnData->net_amount)?></td>
                <td colspan="4" class="text-left"><b>P & F :</b><?= $dnData->packing_amount?><br><br><b>Round Off :</b><?= $dnData->round_off_amount?></td>
			</tr>
			<tr>
                <td colspan="4" class="text-left" style="font-weight:bold;font-size: 0.9em;"><b>Grand Total : <?= $dnData->net_amount ?></b></td>
            </tr>
		</table>
        <h4>Terms & Conditions :-</h4>
		<table class="table top-table" style="margin-top:10px;">
			<?php
				if(!empty($dnData->terms_conditions)):
					$terms = json_decode($dnData->terms_conditions);
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