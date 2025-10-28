<div class="row">
	<div class="col-12">
	    
		<table class="table item-list-bb text-left" style="margin-top:2px;">
			
			<tr>
				<th>Key Contact</th>
				<td>DARSHAN KANTARIA</td>
				<th>Date (Org) </th>
				<td><?=!empty($newRevData->rev_date)?formatDate($newRevData->rev_date):''?></td>
				<th>Product Phase</th>
				<td>
					<?php
					$productPhase = [0=>"",1=>'Prototype',2=>'Pre Launch',3=>'Production'];
					echo  $productPhase[$cpData->product_phase];
					?>
				</td>
				
			</tr>
			<tr>
				<th>Material Grade</th>
				<td><?=$revData->material_grade?></td>
				<th>Part Description</th>
				<td><?= (!empty($cpData->item_name)) ? $cpData->item_name : "" ?></td>
				<th>Cust. Rev. & Part No.</th>
				<td><?= (!empty($revData->cust_rev_no) ? $revData->cust_rev_no : '').'/'.(!empty($cpData->part_no) ? $cpData->part_no : "")   ?></td>
			</tr>
			<tr>
				<th>Control Plan Number</th>
				<td><?= (!empty($cpData->trans_number)) ? $cpData->trans_number : "" ?></td>
				<th>CP Revision No. & Date</th>
                <td><?= ((!empty($cpData->app_rev_no) AND $cpData->app_rev_no != '')) ? $cpData->app_rev_no . '/' . formatDate($cpData->app_rev_date) : "" ?></td>
				<th>Engg.Change Level  </th>
				<td><?= (!empty($cpData->eng_change_level)) ? $cpData->eng_change_level : "" ?> </td>
			</tr>
			
			<tr>
				
				<th>Process NO & Name : </th>
				<td colspan="3"><?= (!empty($cpData->process_no)) ? '['.$cpData->process_no.'] ' : "" ?><?= (!empty($cpData->product_param)) ? $cpData->product_param : "" ?></td>
				<th>Machine, Device, Jig, Tools for mfg.: </th>
				<td><?= (!empty($cpData->machine_tool)) ? $cpData->machine_tool : "" ?> </td>
			</tr>
		</table>
		<table class="table item-list-bb text-left" style="margin-top:2px;">
			
		</table>
		<table class="table item-list-bb" style="margin-top:10px;" >
		    <thead>
				<tr>
					<th rowspan="2">Sr. No.</th>
					<th rowspan="2">Rev. No.</th>
					<th rowspan="2">Product</th>
					<th rowspan="2">Process</th>
					<th rowspan="2">Special Char. Class</th>
					<th rowspan="2">Product / Process,Specification / Tolerance</th>
					<th colspan="5">Operator</th>
					<th colspan="5">Inspector</th>
					<th rowspan="2">Stage Type</th>
				
				</tr>
				<tr>
					<th>Measur. Tech.</th>
					<th>Size</th>	
					<th>Freq</th>	
					<th>Time</th>	
					<th>Control Method</th>

					<th>Measur. Tech.</th>
					<th>Size</th>	
					<th>Freq</th>	
					<th>Time</th>	
					<th>Control Method</th>
				</tr>
			</thead>
			<tbody>
			<?php
			if (!empty($cpTrans)) {
				$i = 1; $style="style='margin:2px auto;important!'";
				foreach ($cpTrans as $row) {
					$diamention = '';
					if ($row->requirement == 1) {
						$diamention = $row->min_req . '/' . $row->max_req;
					}
					if ($row->requirement == 2) {
						$diamention = $row->min_req . ' ' . $row->other_req;
					}
					if ($row->requirement == 3) {
						$diamention = $row->max_req . ' ' . $row->other_req;
					}
					if ($row->requirement == 4) {
						$diamention = nl2br($row->other_req);
					}
					$diamention = nl2br($row->other_req);

					if(!empty($newRevData->rev_no)){
						$revArray = explode(",",$row->rev_no);
						if(($key = array_search($newRevData->rev_no, $revArray)) !== false) {
							unset($revArray[$key]);
						}
						$row->rev_no = implode(",",$revArray);
					}
			?>
					<tr>
						<td ><?= $i++ ?></td>
						<td ><?= $row->rev_no?></td>
						<td ><?=nl2br($row->product_param) ?></td>
						<td ><?=nl2br($row->process_param)?></td>
						<td >
						<?php if(!empty($row->char_class)){ ?><img style="width:25px;display:inline-block;vertical-align:middle;" src="<?= base_url('assets/images/symbols/'.$row->char_class.'.png')?>"><?php } ?>
						</td>
						<td ><?= $diamention ?></td>
						<td><?=$row->opr_measur_tech?></td>
						<td><?=$row->opr_size?></td>
						<td><?=$row->opr_freq?></td>
						<td><?=$row->opr_freq_time?></td>
						<td><?=$row->opr_freq_text?></td>

						<td >
							<?php
							$measurTechArray = array();$sizeArray = array();$freqArray = array();$controlMethod = array();$freq = array();$freqTime = array();
							if(!empty($row->iir_measur_tech)){
								$measurTechArray[]=$row->iir_measur_tech;
								$sizeArray[] = !empty($row->iir_size)?$row->iir_size:0;
								$freqArray[] = !empty($row->iir_freq_text)?$row->iir_freq_text:'-';
								$freq[] = !empty($row->iir_freq)?$row->iir_freq:'-';
								$freqTime[] = !empty($row->iir_freq_time)?$row->iir_freq_time:'-';
								$controlMethod[] = 'IIR';
							}
							if(!empty($row->ipr_measur_tech)){
								$measurTechArray[]=$row->ipr_measur_tech;
								$sizeArray[] = (!empty($row->ipr_size)?$row->ipr_size:'-');
								$freqArray[] = (!empty($row->ipr_freq_text)?$row->ipr_freq_text:'-');
								$freq[] = (!empty($row->ipr_freq)?$row->ipr_freq:'-');
								$freqTime[] = (!empty($row->ipr_freq_time)?$row->ipr_freq_time:'-');
								$controlMethod[] = 'IPR';
							}
							if(!empty($row->sar_measur_tech)){
								$measurTechArray[]=$row->sar_measur_tech;
								$sizeArray[] = (!empty($row->sar_size)?$row->sar_size:'-');
								$freqArray[] = (!empty($row->sar_freq_text)?$row->sar_freq_text:'-');
								$freq[] = (!empty($row->sar_freq)?$row->sar_freq:'-');
								$freqTime[] = (!empty($row->sar_freq_time)?$row->sar_freq_time:'-');
								$controlMethod[] = 'SAR';
							}

							if(!empty($row->spc_measur_tech)){
								$measurTechArray[]=$row->spc_measur_tech;
								$sizeArray[] = (!empty($row->spc_size)?$row->spc_size:'-');
								$freqArray[] = (!empty($row->spc_freq_text)?$row->spc_freq_text:'-');
								$freq[] = (!empty($row->spc_freq)?$row->spc_freq:'-');
								$freqTime[] = (!empty($row->spc_freq_time)?$row->spc_freq_time:'-');
								$controlMethod[] = 'SPC';
							}

							if(!empty($row->fir_measur_tech)){
								$measurTechArray[]=$row->fir_measur_tech;
								$sizeArray[] = (!empty($row->fir_size)?$row->fir_size.' '.$row->fir_freq_time:'-');
								$freqArray[] = (!empty($row->fir_freq_text)?$row->fir_freq_text:'-');
								$freq[] = (!empty($row->fir_freq)?$row->fir_freq:'-');
								$freqTime[] = (!empty($row->fir_freq_time)?$row->fir_freq_time:'-');
								$controlMethod[] = 'FIR';
							}

							?>
							<?=implode('<hr '.$style.'>',$measurTechArray)?>
						</td>
						
						<td><?=implode('<hr '.$style.'>',$sizeArray)?></td>
						<td><?=implode('<hr '.$style.'>',$freq)?></td>
						<td><?=implode('<hr '.$style.'>',$freqTime)?></td>
						<td><?=implode('<hr '.$style.'>',$freqArray)?></td>
						<td><?=implode('<hr '.$style.'>',$controlMethod)?></td>
					</tr>
				<?php
				}
			}
				?>
			</tbody>
		</table>
		<table class="table item-list-bb" style="margin-top:2px;">
			<tr>
				<td  style="border-right:0px">
					<b>Reaction Plan </b>
				</td>
				<td colspan="7" style="border-left:0px">
					<?php
					if(!empty($reactionPlan)){
						echo implode(",",(array_column($reactionPlan,"description")));
					}
					?>
				</td>
			</tr>
			<tr>
				<td rowspan="4">
					<b>Abbreviation</b>
				</td>
				<td>MM=MICROMETER</td>
				<td>SP = SURFACE PLATE</td>
				<td>VC = VERNIER CALLIPER (L.C. 0.010MM)</td>
				<td colspan="3">SRC = SURFACE ROUGHNESS COMPERATOR</td>
			</tr>
			<tr>
				<td>RG=RADIUS GAUGE</td>
				<td>CS = COMPERATOR STAND</td>
				<td>HG = HEIGHT GAUGE</td>
				<td>VB= V-BLOCK</td>
				<td rowspan="3"><img style="width:50px;display:inline-block;vertical-align:middle;" src="<?= base_url('assets/images/symbols/pc.png') ?>"></td>
				<td rowspan="3">Process Significant Characteristics (Specified by JJ)</td>
			</tr>
			<tr>
				<td>PPG=PLAIN PLUG GAUGES</td>
				<td>TPG=THREAD PLUG GAUGES</td>
				<td>TRG=THREAD RING GAUGES</td>
				<td>SGS=SLIP GAUGES SET</td>
			</tr>
			<tr>
				<td>SPG=SPECIAL GAUGES</td>
				<td>PRG=PLAIN RING GAUGES</td>
				<td>PMM=PITCH MICROMETER</td>
				<td>BMM=BLADE MICROMETER</td>
			</tr>
			<tr class="text-center">
				<th style="border-right:0px"></th>
				<th style="border-right:0px;border-left:0px;"></th>
				<th style="border-right:0px;border-left:0px;"></th>
				<td style="border-right:0px;border-left:0px;height:40;vertical-align:bottom" colspan="2">
					<?= !empty($cpData->preparedBy)?$cpData->preparedBy:''?><br>
					<b>Prepared By</b>
				</td>
				<td style="border-left:0px;vertical-align:bottom" colspan="2">
				<?= !empty($cpData->approveBy)?$cpData->approveBy:''?><br>
					<b>Approved By</b>
				</td>
			</tr>
		</table>
	</div>
</div>