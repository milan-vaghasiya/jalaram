<?php
	$heat_no = '';
	if(!empty($reqMaterials['heat_no'])){
		$heat_no = substr($reqMaterials['heat_no'], 0, strrpos($reqMaterials['heat_no'], '/'));
	}
	$heat_no = (!empty($heat_no))?$heat_no:$reqMaterials['heat_no'];
	$supplier_name = (!empty($reqMaterials['supplier_name'])) ? '<br><small>('.$reqMaterials['supplier_name'].')</small>' : '';	
?>
<div class="row">
	<div class="col-12">
		<table class="table item-list-bb text-left" style="margin-top:2px;">
			<tr>
				<th>Part Code</th>
				<th>Part Name</th>
				<th>Jobcard</th>
				<th>Process</th>
				<th>Vendor</th>
				<th>RQC Date</th>
				<th>RQC No</th>
				<th>Heat No</th>
				<th>In Ch. No</th>
				<th>In Ch. Date</th>
				<th>In Ch. Qty</th>
			</tr>
			<tr>
				<td> <?= (!empty($rqcData->item_code)) ? $rqcData->item_code : "" ?></td>
				<td style="width:30%"><?= (!empty($rqcData->item_name)) ?$rqcData->item_name : "" ?></td>
				<td><?= (!empty($rqcData->job_no)) ?getPrefixNumber($rqcData->job_prefix,$rqcData->job_no) : "" ?></td>
				<td><?= (!empty($rqcData->process_name)) ? $rqcData->process_name : "" ?></td>
				<td> <?= ((!empty($rqcData->party_name)) ? $rqcData->party_name:'') ?> </td>
				<td> <?= ((!empty($rqcData->trans_date)) ? formatDate($rqcData->trans_date) : "") ?> </td>
				<td> <?= ((!empty($rqcData->trans_no)) ? $rqcData->trans_no : "") ?> </td>
				<td> <?= (!empty($heat_no)?$heat_no:'') ?> </td>
				<td> <?= ((!empty($rqcData->in_challan_no)) ? $rqcData->in_challan_no : "") ?> </td>
				<td> <?= ((!empty($rqcData->log_date)) ? formatDate($rqcData->log_date) : "") ?> </td>
				<td> <?= ((!empty($rqcData->production_qty)) ? $rqcData->production_qty : "") ?> </td>
			</tr>
		</table>
		<table class="table item-list-bb" style="margin-top:4px;">
			<tr>
				<th rowspan="2" style="width:20px;">Sr. No.</th>
				<th rowspan="2" style="width:40px;">Operation No</th>
				<th rowspan="2" style="width:80px;">Product Characteristic</th>
				<th rowspan="2" style="width:80px;">Product Specification / Tolerance</th>
				<th rowspan="2" style="width:80px;">Evaluation / Measurement Technique</th>
				<th rowspan="2" style="width:20px;">Size</th>
				<th rowspan="2" style="width:20px;">Freq.</th>
				<th colspan="<?=$rcount?>">Observation</th>
			</tr>
			<tr>
				<?php echo $theadData; ?>
			</tr>
			<?php echo $tbodyData; ?>
		</table>
	</div>
</div>