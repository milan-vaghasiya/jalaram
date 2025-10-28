<!--<link href="<?=base_url();?>assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css" rel="stylesheet">
<link href="<?=base_url();?>assets/css/style.css?v=<?=time()?>" rel="stylesheet">-->
<div class="row">
	<div class="col-12">
		<table class="table">
		    <tr>
		        <td style="width:25%;"></td>
                <td class="fs-18 text-center" style="letter-spacing: 2px;font-weight:bold;padding:0px !important;width:50%;" height="30">JOB WORK ORDER</td>
                <td style="width:25%;" class="fs-15 text-right">F PL 02 (00/1.06.20)</td>
		    </tr>
		</table>
		<?php if(empty($order_view)){ ?>
		
    		<table class="vendor_challan_table">
    			<tr>
    				<td rowspan="2" style="width:70%;vertical-align:top;">
    					<b>TO : <?=$jobData->party_name?></b><br>
    					<span style="font-size:12px;"><?=$jobData->party_address?><br><br>
    					<b>GSTIN No. :</b> <span style="letter-spacing:2px;"><?=$jobData->gstin?></span>
    				</td>
    				<td class="text-left" height="35"><b>Order No. :</b> <?=getPrefixNumber($jobData->jwo_prefix,$jobData->jwo_no)?> </td>
    			</tr>
    			<tr>
    				<td class="text-left" height="35"><b>Order Date :</b> <?=date("d-m-Y",strtotime($jobData->jwo_date))?> </td>
    			</tr>
    		</table>
    		
    	<?php }else{ ?>
    	
    	    <table class="vendor_challan_table">
    			<tr>
    				<td rowspan="3" style="width:50%;vertical-align:top;">
    					<b>TO : <?=$jobData->party_name?></b><br>
    					<span style="font-size:12px;"><?=$jobData->party_address?><br><br>
    					<b>GSTIN No. :</b> <span style="letter-spacing:2px;"><?=$jobData->gstin?></span>
    				</td>
    				<td class="text-right" height="35"><b>Order No. :</b> <?=getPrefixNumber($jobData->jwo_prefix,$jobData->jwo_no)?> </td>
    				<td class="text-right" height="35"><b>Order Date :</b> <?=date("d-m-Y",strtotime($jobData->jwo_date))?> </td>
    			</tr>
    			<tr>
    				<td class="text-right" height="35"><b>Approve Date :</b> </td>
    				<td class="text-right" height="35"><input type="date" class="form-control" id="approve_date" value="<?=date("Y-m-d")?>" min="<?=date("Y-m-d",strtotime($jobData->jwo_date))?>" /></td>
    			</tr>
    		</table>
    	
		<?php }
			$pdays = (!empty($jobData->production_days)) ? "+".$jobData->production_days." day" : "+0 day";
		
			$delivery_date = date('d-m-Y',strtotime($pdays, strtotime($jobData->jwo_date)));
			$itemList='';
			$itemList.='<table class="table table-bordered jobChallanTable">
						<tr class="text-center bg-light-grey">
							<th>Material Description</th><th style="width:15%;">'.(($jobData->rate_per == 1)?"Pcs.":"Kg.").'</th><th style="width:15%;">Rate</th><th style="width:15%;">Amount</th>
						</tr>
						<tr>
							<td style="vertical-align:top;height:50px;padding:5px;">
								<b>Item Code : </b>'.(($jobData->item_type == 1)?$jobData->item_code:$jobData->item_name).(($jobData->rate_per == 2)?' ('.sprintf('%0.0f', $jobData->qty).' Pcs.)':"").'
							</td>
							<td class="text-center" rowspan="3" style="vertical-align:top;padding:5px;">'.sprintf('%0.3f', ($jobData->rate_per == 1)?$jobData->qty:$jobData->qty_kg).'</td>
							<td class="text-center" rowspan="3" style="vertical-align:top;padding:5px;">'.sprintf('%0.2f', $jobData->rate).'</td>
							<td class="text-center" rowspan="3" style="vertical-align:top;padding:5px;">'.sprintf('%0.2f', $jobData->amount).'</td>
						</tr>
						<!--<tr>
							<td style="vertical-align:top;height:50px;padding:5px;"><b>Delivery Date : </b>'.$delivery_date.'</td>
						</tr>-->
						<tr>
							<td style="vertical-align:top;height:50px;padding:5px;"><b>Process : </b>'.$jobData->process.'</b></td>
						</tr>
						<!--<tr>
							<td style="vertical-align:top;height:50px;padding:5px;"><b>EWB Value : </b>'.$jobData->ewb_value.'</b></td>
						</tr>-->
						<tr>
							<td style="vertical-align:top;height:50px;padding:5px;"><b>Remarks : </b>'.$jobData->remark.'</td>
						</tr>';
				$itemList.='<tr class="bg-light-grey">';
					$itemList.='<th class="text-right" style="font-size:14px;">Total</th>';
					$itemList.='<th class="text-center" style="font-size:14px;">'.sprintf('%0.0f', ($jobData->rate_per == 1)?$jobData->qty:$jobData->qty_kg).'</th>';
					$itemList.='<th class="text-center" style="font-size:14px;">'.sprintf('%0.2f', $jobData->rate).'</th>';
					$itemList.='<th class="text-center" style="font-size:14px;">'.sprintf('%0.2f', $jobData->amount).'</th>';
				$itemList.='</tr>';		
			$itemList.='</table>';

			$terms='<h4>Terms & Conditions :-</h4>';
			$terms.='<table class="table top-table" style="margin-top:10px;">';
				if(!empty($jobData->terms_conditions)):
					$termData = json_decode($jobData->terms_conditions);
					foreach($termData as $row):
						$terms.='<tr>';
						$terms.='<th class="text-left fs-11" style="width:140px;">'.$row->term_title.'</th>';
						$terms.='<td class=" fs-11"> : '.$row->condition.'</td>';
						$terms.='</tr>';
					endforeach;
				endif;
			$terms.='</table>';

			echo $itemList.$terms;
		?>
	</div>
</div>