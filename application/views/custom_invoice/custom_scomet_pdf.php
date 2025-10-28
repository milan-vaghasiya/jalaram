<div class="row">
	<div class="col-12">
		<table class="table">
			<tr><td class="fs-18 text-center" style="letter-spacing: 1px;font-weight:bold;padding:0px !important;">Exports Statement on Sanction Warranty Against Exports to <?=$dataRow->country_of_final_destonation?></td></tr>
		</table>
    </div>

    <div>
        <p style="line-height:1.65em;">We,<b> <?=$companyInfo->company_name?>,</b>India Hereby Certify That We Are The Exporter Of The Goods Described Below And Accept The Responsibilities Of The Exporter By Signing This Statement To Confirm That The Shipment(S) Concerned Does Not Require A License.</p>

        <table class="table">
            <tr><td> <b> Name of Exporter : </b><?=$companyInfo->company_name?></td></tr>
            <tr><td> <b> Invoice No. : </b><?=$dataRow->doc_no?></td></tr>
            <tr><td> <b> Value USD / EURO etc. : </b> <?= $dataRow->currency ?> <?=$dataRow->net_amount?></td></tr>
            <tr><td> <b> Mode of Shipment : </b> <?= $shipment ?> </td></tr>
            <tr><td> <b> Port of Discharge : </b><?= $extraField->port_of_discharge?></td></tr>
            <tr><td><b> Place of Destination : </b><?= $extraField->place_of_delivery?></td></tr>
        </table>

        <p>We, <b> <?=$companyInfo->company_name?> </b> Further Undertake and Confirm</p>
        
        
        <?php
            $declaration = '';
            if(!empty($declarationPoints))
            {
                $declaration .= '<ul type="A" style="list-style-position: outside;padding-left: 5%;">';
                foreach($declarationPoints as $row)
                {
                    $declaration .= '<li style="padding-bottom:10px;font-size:13px;">'.$row->description.'</li>';
                }
                $declaration .= '</ul> ';
            }
            echo $declaration;
        ?>
        
        <p>Thank You,</p>
        <h5 style="margin-left:430px; padding-top:70px;"><?=$authorise_sign?><br><b>SIGNATURE OF THE EXPORTER</b></h5>
        
    </div>
		
	
</div>