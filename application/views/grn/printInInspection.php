<div class="row">
	<div class="col-12">
		<?php
    		$pramIds = !empty($inInspectData->parameter_ids) ? explode(',', $inInspectData->parameter_ids) : '';
    		
    		$param = (array)$paramData;
		    $iir_lot_time = array_filter($param,function($item){ return $item->iir_freq_time == 'Lot'; }); 
		    $iir_size_column = array_column($iir_lot_time,'iir_size');
    		//$iir_size_column = array_column($paramData,'iir_size');
    		//$iir_size = max($iir_size_column);
    		
		    $iir_size = (!empty($iir_size_column))?max($iir_size_column):10; 
    		$smplingQty = (!empty($iir_size)?$iir_size:10);
		?>
		<table class="table item-list-bb text-left" style="margin-top:2px;">
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
		<table class="table item-list-bb" style="margin-top:2px;">
			<thead>
				<tr>
					<th rowspan="2">Sr. No.</th>
					<th rowspan="2">Product Characteristic</th>
					<th rowspan="2">Product Specification / Tolerance</th>
					<th rowspan="2">Evaluation / Measurement Technique</th>
					<th colspan="<?= floatval($smplingQty) ?>">Observation</th>
					<th rowspan="2">Status</th>
					<th rowspan="2">Decision</th>
				</tr>
				<tr>
					<?php
					for ($i = 1; $i <= $smplingQty; $i++) {
						echo '<th>' . $i . '</th>';
					}
					?>
				</tr>
			</thead>
			<tbody>
				<?php
				$i = 1;
				if (!empty($paramData)) {
					foreach ($paramData as $param) :
						if (in_array($param->id, $pramIds)) :
							$os = json_decode($inInspectData->observation_sample);
							$diamention = '';
							if ($param->requirement == 1) { $diamention = $param->min_req . '/' . $param->max_req; }
							if ($param->requirement == 2) { $diamention = $param->min_req . ' ' . $param->other_req; }
							if ($param->requirement == 3) { $diamention = $param->max_req . ' ' . $param->other_req; }
							if ($param->requirement == 4) { $diamention = $param->other_req; }
			
							echo '<tr>
								<td>'.$i.'</td>
								<td>'.$param->product_param.'</td>
								<td>'.$diamention.'</td>
								<td>'.$param->iir_measur_tech.'</td>';
								
								for ($j = 0; $j <= $smplingQty; $j++){
									echo '<td>'.$os->{$param->id}[$j].'</td>';
								} 
								$countPrm = count($os->{$param->id});
							
							if (!empty($inInspectData->is_approve)){
								echo '<td>'.$os->{$param->id}[$countPrm -1].'</td>';
							}else{
								echo '<td></td>';
							}
							echo'</tr>';
							
							$i++;
						endif;
					endforeach;
					$i = $i - 1;
				}
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
				<td><b>Checked By</b> : <?= ((!empty($inInspectData->create_name)) ? $inInspectData->create_name." (".formatDate($inInspectData->created_at).")" : "") ?></td>
			</tr>
			<tr class="text-left"> 
				<th>Comment</th>
				<td colspan="3"><?= (!empty($inInspectData->approval_remarks)) ? $inInspectData->approval_remarks : "" ?></td>
				<td><b>Approved By</b> : <?= (!empty($inInspectData->approve_name)) ? $inInspectData->approve_name." (".formatDate($inInspectData->approve_date).")" : "" ?></td>
			</tr>
		</table>
	</div>
</div>