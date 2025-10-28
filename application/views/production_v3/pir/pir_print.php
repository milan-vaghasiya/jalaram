<div class="row">
	<div class="col-12">
		<table class="table item-list-bb text-left" style="margin-top:2px;">
			<tr>
				<th>JJI Code</th>
				<th>Part Name</th>
				<th>Jobcard</th>
				<th>Process</th>
				<th>Machine</th>
				<th>Operator</th>
				<th>PIR Date</th>
				<th>PIR No</th>
			</tr>
			<tr>
				<td><?= (!empty($pirData->item_code)) ? $pirData->item_code : "" ?></td>
				<td style="width:30%"><?= (!empty($pirData->full_name)) ?$pirData->full_name : "" ?></td>
				<td><?= (!empty($pirData->job_no)) ?getPrefixNumber($pirData->job_prefix,$pirData->job_no) : "" ?></td>
				<td><?= (!empty($pirData->process_name)) ? $pirData->process_name : "" ?></td>
				<td><?= ((!empty($pirData->machine_code)) ? '['.$pirData->machine_code.'] ' : "").$pirData->machine_name ?> </td>
				<td><?= ((!empty($pirData->operator_code)) ? '['.$pirData->operator_code.'] ' : "").$pirData->operator_name ?> </td>
				<td><?= ((!empty($pirData->trans_date)) ? formatDate($pirData->trans_date) : "") ?> </td>
				<td><?= ((!empty($pirData->trans_no)) ? $pirData->trans_no : "") ?> </td>
			</tr>
		</table>
		<table class="table item-list-bb" style="margin-top:4px;">
			<tr>
				<th rowspan="2">Sr. No.</th>
				<th rowspan="2">Operation No</th>
				<th rowspan="2">Product Characteristic</th>
				<th rowspan="2">Product Specification / Tolerance</th>
				<th rowspan="2">Evaluation / Measurement Technique</th>
				<th rowspan="2">Size</th>
				<th rowspan="2">Freq.</th>
				<th colspan="<?=$rcount?>">Observation</th>
			</tr>
			<tr>
				<?php echo $theadData; ?>
			</tr>
			<?php echo $tbodyData; ?>
		</table>
	</div>
</div>