<html>
	<head>
		<title><?=(empty($packingData->status))?"Tentative Packing Annexure":"Final Packing Annexure"?></title>
	</head>
	<body>
		<div class="row" style="padding:1rem;">
			<div class="col-12">
				<table class="table item-list-bb" >
					<tbody>
						<tr>
							<td colspan="7" class="text-center" style="font-size:1rem;"><b><?=(empty($packingData->status))?"Tentative Packing Annexure":"Final Packing Annexure"?></b></td>
						</tr>
				
						<tr>
							<td class="text-center"><b>Package No.</b></td>
							<td class="text-center"><b>Box Size (cm)</b></td>
							<td class="text-center"><b>Item Name</b></td>
							<td class="text-center"><b>Total Qty. (No.s)</b></td>
							<td class="text-center"><b>Net Weight Per Piece (kg)</b></td>
							<td class="text-center"><b>Total Net Weight (kg)</b></td>
							<td class="text-center"><b>Total Gross Weight (kg)</b></td>
						</tr>
						<?php echo $tableBody; ?>
						
						
					</tbody>
					<tfoot>
						<tr>
							<th colspan="5" class="text-right">Total : </th>
							<th  class="text-center"><?= $net_wt ?></th>
							<th class="text-center"><?= $gross_wt ?></th>
						</tr>
					</tfoot>
				</table>	
			</div>
		</div>
	</body>
</html>
