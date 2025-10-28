<link href="<?=base_url();?>assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css" rel="stylesheet">
<link href="<?=base_url();?>assets/css/pdf_style.css?v=<?=time()?>" rel="stylesheet">
<div class="row">
	<div class="col-12">
		<table class="table" style="border-bottom:1px solid #000000;">
			<tr>
				<td class="text-uppercase text-center" style="font-size:1.3rem;font-weight:bold;width:40%;"><?=$companyData->company_name?><br><span style="font-weight:normal;">[ PROCESS ROUTE CARD ]</span></td>
			</tr>
		</table>
		<?php
		$drg_no = (!empty($jobData->drawing_no) ? $jobData->drawing_no : '');
		$rev_no = (!empty($jobData->rev_no) ? $jobData->rev_no : '');
		?>
		<table class="table item-list-bb" style="margin-top:2px;">
			<tr class="text-left">
				<th style="width:12%;" class="bg-light">Job Card No.</th><td style="width:48%"><?=(!empty($jobData->job_no) ? getPrefixNumber($jobData->job_prefix,$jobData->job_no) : '')?></td>
				<th style="width:12%;" class="bg-light">Job Date</th><td style="width:28%"><?=(!empty($jobData->job_date) ? formatDate($jobData->job_date) : '')?></td>
			</tr>
			<tr class="text-left">
				<th class="bg-light">SO No.</th><td><?=(!empty($jobData->trans_no) ? $jobData->trans_prefix.$jobData->trans_no : '')?></td>
				<th class="bg-light">Job Quantity</th><td><?=(!empty($jobData->qty) ? floatval($jobData->qty) : '')?></td>
			</tr>
			<tr class="text-left">
				<th class="bg-light">Product Code </th><td><?=(!empty($jobData->product_code) ? $jobData->product_code : '')?></td>
				<th class="bg-light">Drg. No.</th><td><?=((!empty($drg_no) && !empty($rev_no)) ? $drg_no.', '.$rev_no : $drg_no.$rev_no)?></td>
			</tr>
			<tr class="text-left">
				<th class="bg-light">Product Name</th><td><?=(!empty($jobData->product_name) ? $jobData->product_name : '')?></td>
				<th class="bg-light">Created By</th><td><?=(!empty($jobData->emp_name) ? $jobData->emp_name : '')?></td>
			</tr>
		</table>
		<h4 class="row-title">Material Detail:</h4>
		<table class="table item-list-bb pad5 tbl-fs-11">
			<tr class="thead-gray">
				<th>Item Description</th>
				<th class="text-center" style="width:15%;">Heat No</th>
				<th class="text-center" style="width:15%;">Issued Qty</th>
				<th class="text-center" style="width:12%;">UOM</th>
			</tr>
			<?php
				if(!empty($materialDetail)):
					foreach($materialDetail as $row):
						echo '<tr>';
							echo '<td>'.$row['item_name'].'</td>';
							echo '<td class="text-center">'.$row['heat_no'].'</td>';
							echo '<td class="text-center">'.floatVal($row['issue_qty']).'</td>';
							echo '<td class="text-center">'.$row['unit_name'].'</td>';
						echo '</tr>';
					endforeach;
				else:
					echo '<tr><th class="text-center" colspan="3">Record Not Found !</th></tr>';
				endif;
			?>
		</table>
		<h4 class="row-title">Inspection Detail:</h4>
		<table class="table item-list-bb pad5 tbl-fs-11">
			<tr class="text-center thead-gray">
				<th style="width:5%;">No.</th>
				<th class="text-left">Process Detail</th>
				<th style="width:12%;">Issued Qty</th>
				<th style="width:12%;">OK Qty</th>
				<th style="width:12%;">R/W Qty</th>
				<th style="width:12%;">Rej. Qty</th>
				<th style="width:12%;">Pending Qty</th>
			</tr>
			<?php
				if(!empty($processDetail)):
					$i=1;
					foreach($processDetail as $row):
						echo '<tr>';
							echo '<td class="text-center">'.$i++.'</td>';
							echo '<td>'.$row->process_name.'</td>';
							echo '<td class="text-right">'.floatVal($row->in_qty).'</td>';
							echo '<td class="text-right">'.floatVal($row->total_ok_qty).'</td>';
							echo '<td class="text-right">'.floatVal($row->rework_qty).'</td>';
							echo '<td class="text-right">'.floatVal($row->rejection_qty).'</td>';
							echo '<td class="text-right">'.floatVal($row->in_qty-$row->total_ok_qty-$row->rework_qty-$row->rejection_qty).'</td>';
						echo '</tr>';
					endforeach;
				else:
					echo '<tr><th class="text-center" colspan="6">Record Not Found !</th></tr>';
				endif;
			?>
			
		</table>
		
		<!-- Inhouse Production Data -->
		<?php if(!empty($inhouseProduction)): ?>
		<h4 class="row-title">Inhouse Production Detail :</h4>
		<table class="table item-list-bb pad5 tbl-fs-11">
			<tr class="text-center thead-gray">
				<th style="width:5%;">No.</th>
				<th>Process</th>
				<th style="width:15%;">Date</th>
				<th>Operator</th>
				<!-- <th>Shift</th> -->
				<th>Machine</th>
				<th>OK Qty.</th>
				<th>Rej. Qty.</th>
				<th>R/W Qty.</th>
				<!-- <th>Hours</th>
				<th>Remark</th> -->
			</tr>
			<?php
			$i=1;
			foreach($inhouseProduction as $row):
				echo '<tr>';
					echo '<td class="text-center">'.$i++.'</td>';
					echo '<td>'.$row->process_name.'</td>';
					echo '<td class="text-center">'.formatDate($row->log_date).'</td>';
					echo '<td>'.$row->emp_name.'</td>';
					echo '<td>'.$row->machine_name.'</td>';
					echo '<td class="text-right">'.floatVal($row->ok_qty).'</td>';
					echo '<td class="text-right">'.floatVal($row->rej_qty).'</td>';
					echo '<td class="text-right">'.floatVal($row->rw_qty).'</td>';
				echo '</tr>';
			endforeach;
			?>
		</table>
		<?php endif; ?>
		
		<!-- Vendor Production Data -->
		<?php if(!empty($vendorProduction)): ?>
		<h4 class="row-title">Vendor Production Detail :</h4>
		<table class="table item-list-bb pad5 tbl-fs-11">
			<tr class="text-center thead-gray">
				<th style="width:5%;">No.</th>
				<th>Process</th>
				<th style="width:15%;">Date</th>
				<th>Vendor</th>
				<th>OK Qty.</th>
				<th>Rej. Qty.</th>
				<th>R/W Qty.</th>
				<!-- <th>Remark</th> -->
			</tr>
			<?php
			$i=1;
			foreach($vendorProduction as $row):
				echo '<tr>';
					echo '<td class="text-center">'.$i++.'</td>';
					echo '<td>'.$row->process_name.'</td>';
					echo '<td class="text-center">'.formatDate($row->log_date).'</td>';
					echo '<td>'.$row->vendor_name.'</td>';
					echo '<td class="text-right">'.floatVal($row->ok_qty).'</td>';
					echo '<td class="text-right">'.floatVal($row->rej_qty).'</td>';
					echo '<td class="text-right">'.floatVal($row->rw_qty).'</td>';
				echo '</tr>';
			endforeach;
			?>
		</table>
		<?php endif; ?>
	</div>
</div>