
<div class="row">
	<div class="col-12">
		<table class="table item-list-bb" style="margin-top:2px;">
			<tr>
				<th class="text-left">PFC No./ JJI No.</th>
				<td><?=(!empty($pfcData->trans_number)) ?$pfcData->trans_number:""?></td>
				<th class="text-left">Cust. Part No.</th>
				<td><?=(!empty($pfcData->part_no)) ?$pfcData->part_no:""?></td>
				<th class="text-left">Date (Org)</th>
				<td><?=(!empty($pfcData->created_at)) ?formatDate($pfcData->created_at,'d.m.Y'):""?></td>
			</tr>
			<tr>
				<th class="text-left">Part Description</th>
				<td><?=(!empty($pfcData->item_name)) ?$pfcData->item_name:""?></td>
				<th class="text-left">Rev. No.</th>
				<td><?=(!empty($revData)) ?$revData[count($revData)-1]->rev_no:""?></td>
				<th class="text-left">Rev. Date</th>
				<td><?=(!empty($revData)) ?formatDate($revData[count($revData)-1]->rev_date):""?></td>
			</tr>
		</table>

        <table class="table item-list-bb" style="margin-top:5px;">
			<tr>
				<th style="width: 20%;">Revision No.</th>
				<th style="width: 10%;">Symbol</th>
				<th style="width: 10%;">Process Number</th>
				<th style="width: 50%;" >Process Description</th>
				<th style="width: 10%;">Process Code</th>
				<!--<th style="width: 16%;" >Location</th>-->
			</tr>
			<?php
				$tbodyData="";$i=1;
				if(!empty($pfcTransData)):
                    foreach($pfcTransData as $row):
                        $location='';if($row->location == 1){ $location='In House'; }elseif($row->location == 2 && !empty($row->party_name)){ $location=$row->party_name; }elseif($row->location == 2 && empty($row->party_name)){ $location='Outsource'; }
                        
                        $symbol_1=''; if(!empty($row->symbol_1)){ $symbol_1='<img src="' . base_url('assets/images/symbols/'.$row->symbol_1.'.png') . '" style="width:20px;display:inline-block;" />'; }
                        //$symbol_2=''; if(!empty($row->symbol_2)){ $symbol_2='<img src="' . base_url('assets/images/symbols/'.$row->symbol_2.'.png') . '" style="width:20px;display:inline-block;" />'; }
                        //$symbol_3=''; if(!empty($row->symbol_3)){ $symbol_3='<img src="' . base_url('assets/images/symbols/'.$row->symbol_3.'.png') . '" style="width:20px;display:inline-block;" />'; }
                        $char_class=''; if(!empty($row->char_class)){ $char_class='<img src="' . base_url('assets/images/symbols/'.$row->char_class.'.png') . '" style="width:20px;display:inline-block;" />'; }
                        
						
                        $tbodyData .= '<tr>
							<td class="text-center">' . $row->rev_no . '</td>
                            <td class="text-center" style="width: 4%;">'.$symbol_1.'</td>
                            <td class="text-center">' . $row->process_no . '</td>
                            <td class="text-left">' . $row->product_param . '</td>
                            <td class="text-center">' . $row->process_code . '</td>
							<!--<td>' . $location . '</td>-->
                        </tr>';
                        $i++;
                    endforeach;
                    for($j=$i;$j<15;$j++){$tbodyData .= '<tr class="text-center"><td>&nbsp;</td><td></td><td></td><td></td><td></td></tr>';}
				endif;
                echo $tbodyData;
			?>
		</table>

        <table class="table item-list-bb" style="margin-top:2px;border: 1px solid #000000;border-collapse:collapse !important;">
			<tr>
                <td><img style="width:12px;display:inline-block;vertical-align:middle;" src="<?=base_url('assets/images/symbols/delay.png')?>"> Material Hold</td>
				<td style="width:95px;"><img style="width:12px;display:inline-block;vertical-align:middle;" src="<?=base_url('assets/images/symbols/operation.png')?>"> Operation</td>
				<td style="width:95px;"><img style="width:12px;display:inline-block;vertical-align:middle;" src="<?=base_url('assets/images/symbols/mat_out.png')?>"> Material Out</td>
				<td style="border-left:1px solid;width:200px;"><b>Approved By : </b><?=(!empty($pfcData->emp_name)) ?$pfcData->emp_name:""?></td>
			</tr>
            <tr>
                <td style="width:80px;"><img style="width:12px;display:inline-block;vertical-align:middle;" src="<?=base_url('assets/images/symbols/storage.png')?>"> Storage</td>
                <td style="width:80px;"><img style="width:12px;display:inline-block;vertical-align:middle;" src="<?=base_url('assets/images/symbols/inspection.png')?>"> Inspection</td>
				<td style="width:95px;"><img style="width:12px;display:inline-block;vertical-align:middle;" src="<?=base_url('assets/images/symbols/mat_in.png')?>"> Material In</td>
				<td style="width:90px;"><img style="width:12px;display:inline-block;vertical-align:middle;" src="<?=base_url('assets/images/symbols/oper_insp.png')?>"> Oper. & Insp</td>
			</tr>
		</table>

		<table class="table item-list-bb" style="margin-top:5px;">
			<tr class="text-center">
				<th>#</th>
				<th>Revision No.</th>
				<th>Revision Description</th>
				<th>Revision Date</th>
			</tr>
			<?php
			if(!empty($revData))
			{
				$i=1;
				foreach($revData as $row)
				{
					echo '<tr class="text-center">
							<td>'.$i++.'</td>
							<td>'.$row->rev_no.'</td>
							<td>'.$row->pfc_remark.'</td>
							<td>'.formatDate($row->rev_date).'</td>
						</tr>';
				}
			}
			?>
		</table>

	</div>
</div>


