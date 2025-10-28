<html>
	<head>
		<title>E-Invoice PDF</title>
	</head>
	<body style="margin: 10px;padding: 5px;">
		<div style="border: 2px solid #a2a1a1; border-radius:0.3rem;">
			<table>
				<tr>
					<td >
						<h1><?=$einvData->Data->ExtractedSignedInvoiceData->SellerDtls->Gstin?></h1>
						<h1><?=$einvData->Data->ExtractedSignedInvoiceData->SellerDtls->LglNm?></h1>
					</td>
					<td rowspan="2" class="text-right">
						<img width="150" src="data:image/png;base64,<?=$einvData->Data->QrCodeImage?>">
					</td>
				</tr>
			</table>

			<div class="col-md-12 row" style="margin:3px; border: 2px solid #a2a1a1; border-radius:0.3rem;display:inline;">
				<div style="border-bottom: 2px solid #a2a1a1; padding:5px;">
					<b>1.e-Invocie Details</b>
				</div>
				<table>
					<tr>
						<th class="text-left">INR :</th>
						<td style="width:30%;">
							<?=$einvData->Data->Irn?>
						</td>
						<th class="text-left ml-3">Ack. No :</th>
						<td >
							<?=$einvData->Data->AckNo?>
						</td>
						<th class="text-left ml-3">Ack. Date</th>
						<td>
							<?=date("d-m-Y H:i:s",strtotime($einvData->Data->AckDt))?>
						</td>
					</tr>
				</table>
			</div>

			<div class="col-md-12 row" style="margin:3px; border: 2px solid #a2a1a1; border-radius:0.3rem;display:inline;">
				<div style="border-bottom: 2px solid #a2a1a1; padding:5px;">
					<b>2.Transaction Details</b>
				</div>
				<table>
					<tr>
						<th class="text-left">Supply Type Code :</th>
						<td>
							<?=$einvData->Data->ExtractedSignedInvoiceData->TranDtls->SupTyp?>
						</td>
						<th class="text-left ml-3">Document No :</th>
						<td >
							<?=$einvData->Data->ExtractedSignedInvoiceData->DocDtls->No?>
						</td>
						<th class="text-left ml-3">IGST applicable despite Supplier and Recipient located in same State : <?=($einvData->Data->ExtractedSignedInvoiceData->TranDtls->IgstOnIntra == "N")?"NO":"YES"?></th>
						<td style="width:1%;"></td>
					</tr>
					<tr>
						<th class="text-left" style="border-top: 2px solid #a2a1a1; border-bottom: 2px solid #a2a1a1; padding:5px;">
							Place of Supply : 
						</th>
						<td class="text-left" colspan="5" style="border-top: 2px solid #a2a1a1; border-bottom: 2px solid #a2a1a1; padding:5px;">
							<?=strtoupper($stateData->name)?>
						</td>
					</tr>
					<tr>
						<th class="text-left">Document Type :</th>
						<td>
							Tax Invoice
						</td>
						<th class="text-left ml-3">Document Date :</th>
						<td >
							<?=str_replace("/","-",$einvData->Data->ExtractedSignedInvoiceData->DocDtls->Dt)?>
						</td>
						<th></th>
						<td></td>
					</tr>
				</table>
			</div>

			<div class="col-md-12 row" style="margin:3px; border: 2px solid #a2a1a1; border-radius:0.3rem;display:inline;">
				<div style="border-bottom: 2px solid #a2a1a1; padding:5px;">
					<b>3.Party Details</b>
				</div>
				<table>
					<tr>
						<td style="width:50%; border-right: 2px solid #a2a1a1;">
							<b>Supplier</b><br>
							GSTIN : <?=$einvData->Data->ExtractedSignedInvoiceData->SellerDtls->Gstin?><br>
							<?=$einvData->Data->ExtractedSignedInvoiceData->SellerDtls->LglNm?><br>
							<?=$einvData->Data->ExtractedSignedInvoiceData->SellerDtls->Addr1." ".$einvData->Data->ExtractedSignedInvoiceData->SellerDtls->Addr2?><br>
							<?=$einvData->Data->ExtractedSignedInvoiceData->SellerDtls->Loc?><br>
							<?=$einvData->Data->ExtractedSignedInvoiceData->SellerDtls->Pin." ".strtoupper($companyData->company_state)?><br>
							<?=$einvData->Data->ExtractedSignedInvoiceData->SellerDtls->Ph." ".$einvData->Data->ExtractedSignedInvoiceData->SellerDtls->Em?><br>
						</td>
						<td>
							<b>Recipient</b><br>
							GSTIN : <?=$einvData->Data->ExtractedSignedInvoiceData->BuyerDtls->Gstin?><br>
							<?=$einvData->Data->ExtractedSignedInvoiceData->BuyerDtls->LglNm?><br>
							<?=$einvData->Data->ExtractedSignedInvoiceData->BuyerDtls->Addr1." ".$einvData->Data->ExtractedSignedInvoiceData->BuyerDtls->Addr2?><br>
							<?=$einvData->Data->ExtractedSignedInvoiceData->BuyerDtls->Loc." Place of Supply : ".strtoupper($stateData->name)?><br>
							<?=$einvData->Data->ExtractedSignedInvoiceData->BuyerDtls->Pin." ".strtoupper($stateData->name)?><br>
							<?=$einvData->Data->ExtractedSignedInvoiceData->BuyerDtls->Ph." ".$einvData->Data->ExtractedSignedInvoiceData->BuyerDtls->Em?><br>
						</td>
					</tr>
					<tr>
						<td style="width:50%; border-right: 2px solid #a2a1a1;">
							<b>Despatch From</b><br>
							<?=$einvData->Data->ExtractedSignedInvoiceData->DispDtls->Nm?><br>
							<?=$einvData->Data->ExtractedSignedInvoiceData->DispDtls->Addr1." ".$einvData->Data->ExtractedSignedInvoiceData->DispDtls->Addr2?><br>
							<?=$einvData->Data->ExtractedSignedInvoiceData->DispDtls->Loc?><br>
							<?=$einvData->Data->ExtractedSignedInvoiceData->DispDtls->Pin." ".strtoupper($companyData->company_state)?><br>
						</td>
						<td>
							<b>Ship To</b><br>
							<?=$einvData->Data->ExtractedSignedInvoiceData->ShipDtls->LglNm?><br>
							<?=$einvData->Data->ExtractedSignedInvoiceData->ShipDtls->Addr1." ".$einvData->Data->ExtractedSignedInvoiceData->ShipDtls->Addr2?><br>
							<?=$einvData->Data->ExtractedSignedInvoiceData->ShipDtls->Loc?><br>
							<?=$einvData->Data->ExtractedSignedInvoiceData->ShipDtls->Pin." ".strtoupper($stateData->name)?><br>
						</td>
					</tr>
				</table>
			</div>

			<div class="col-md-12 row" style="margin:3px; border: 2px solid #a2a1a1; border-radius:0.3rem;display:inline;">
				<div style="border-bottom: 2px solid #a2a1a1; padding:5px;">
					<b>4.Details of Goods / Services</b>
				</div>
				<div style="padding:5px;">
					<table class="table table-bordered" style="margin-bottom: 0px;">
						<thead>
							<tr>
								<th>SlNo</th>
								<th>Item Description</th>
								<th>HSN Code</th>
								<th>Quantity</th>
								<th>Unit</th>
								<th>Unit Price(Rs)</th>
								<th>Discount(Rs)</th>
								<th>Taxable Amount(Rs)</th>
								<th>Tax Rate (GST+Cess | State Cess+Cess Non.Advol)</th>
								<th>Other charges(Rs)</th>
								<th>Total</th>
							</tr>						
						</thead>
						<tbody>
							<?php
								foreach($einvData->Data->ExtractedSignedInvoiceData->ItemList as $row):
							?>
							<tr>
								<td class="text-right"><?=$row->SlNo?></td>
								<td class="text-right"><?=$row->PrdDesc?></td>
								<td class="text-right"><?=$row->HsnCd?></td>
								<td class="text-right"><?=$row->Qty?></td>
								<td class="text-right"><?=$row->Unit?></td>
								<td class="text-left"><?=$row->UnitPrice?></td>
								<td class="text-left"><?=$row->Discount?></td>
								<td class="text-left"><?=$row->AssAmt?></td>
								<td class="text-left"><?=$row->GstRt."+".$row->CesRt." | ".$row->StateCesRt."+".$row->CesNonAdvlAmt?></td>
								<td class="text-left"><?=$row->OthChrg?></td>
								<td class="text-left"><?=$row->TotItemVal?></td>
							</tr>
							<?php
								endforeach;
							?>
						</tbody>
					</table>
					<table class="table table-bordered" style="margin-top: 10px;">
						<thead>
							<tr>
								<th>Tax'ble Amt</th>
								<th>CGST Amt</th>
								<th>SGST Amt</th>
								<th>IGST Amt</th>
								<th>CESS Amt</th>
								<th>State CESS Amt</th>
								<th>Discount</th>
								<th>Other Charges</th>
								<th>Round off Amt</th>
								<th>Total Inv. Amt</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td class="text-right"><?=$einvData->Data->ExtractedSignedInvoiceData->ValDtls->AssVal?></td>
								<td class="text-right"><?=$einvData->Data->ExtractedSignedInvoiceData->ValDtls->CgstVal?></td>
								<td class="text-right"><?=$einvData->Data->ExtractedSignedInvoiceData->ValDtls->SgstVal?></td>
								<td class="text-right"><?=$einvData->Data->ExtractedSignedInvoiceData->ValDtls->IgstVal?></td>
								<td class="text-right"><?=$einvData->Data->ExtractedSignedInvoiceData->ValDtls->CesVal?></td>
								<td class="text-right"><?=$einvData->Data->ExtractedSignedInvoiceData->ValDtls->StCesVal?></td>
								<td class="text-right"><?=$einvData->Data->ExtractedSignedInvoiceData->ValDtls->Discount?></td>
								<td class="text-right"><?=$einvData->Data->ExtractedSignedInvoiceData->ValDtls->OthChrg?></td>
								<td class="text-right"><?=$einvData->Data->ExtractedSignedInvoiceData->ValDtls->RndOffAmt?></td>
								<td class="text-right"><?=$einvData->Data->ExtractedSignedInvoiceData->ValDtls->TotInvVal?></td>
							</tr>
						</tbody>
					</table>
				</div>				
			</div>

			<div class="col-md-12 row" style="margin:3px; border: 2px solid #a2a1a1; border-radius:0.3rem;display:inline;">
				<table>
					<tr>
						<td class="text-left">
							<b>Generated By : </b><?=$einvData->Data->ExtractedSignedInvoiceData->SellerDtls->Gstin?><br>
							<b>Print Date : </b><?=date("d-m-Y H:i:s")?>
						</td>
						<td class="text-center">
							<barcode code="<?=$einvData->Data->AckNo?>" type="C128A" height="0.7" text="1" /><br>
							<?=$einvData->Data->AckNo?>
						</td>
						<td class="text-center">
							<!--<img width="80" height="50" src="data:image/png;<?=urlencode($einvData->Data->SignedInvoice)?>"><br>-->
							<img height="35" src="<?=base_url('assets/images/esingn_nic.png')?>"><br>
							Digitally Signed by NIC-IRP	on: <?=date("d-m-Y H:i:s",strtotime($einvData->Data->AckDt))?>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</body>
</html>