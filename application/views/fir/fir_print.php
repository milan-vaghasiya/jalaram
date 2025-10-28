<form id="firLotForm">
    <div class="row">
        <input type="hidden" id="id" name="id" value="<?= !empty($dataRow->id) ? $dataRow->id : '' ?>">
        <!-- Column -->
        <table class="table item-list-bb text-left" style="margin-top:2px;">
            <tr>
                <th style="width:15%;">Part Description:</th>
                <td style="width:20%;"><?= !empty($dataRow->item_name) ? $dataRow->item_name : '' ?></td>
                <th style="width:15%;">JJI Code:</th>
                <td style="width:20%;"><?= !empty($dataRow->item_code) ? $dataRow->item_code : '' ?></td>
                <th style="width:10%;">FIR Qty.:</th>
                <td style="width:20%;"><?= floatval(!empty($dataRow->qty) ? $dataRow->qty : '') ?></td>
                
            </tr>
            <tr>
                <th style="width:15%;">Part No.:</th>
                <td style="width:20%;"><?= !empty($dataRow->part_no) ? $dataRow->part_no : '' ?></td>
                <th style="width:15%;">FIR Date:</th>
                <td style="width:20%;"><?= !empty($dataRow->fir_date) ? formatDate($dataRow->fir_date) : '' ?></td>
                <th style="width:10%;">Job No:</th>
                <td style="width:20%;"><?= !empty($dataRow->job_no) ? getPrefixNumber($dataRow->job_prefix,$dataRow->job_no) : '' ?></td>
            </tr>
            <tr>
                <th>Latest Rev. Change Level.:</th>
                <td ><?= !empty($dataRow->rev_no) ? $dataRow->rev_no : '' ?></td>
                <th>FIR No.:</th>
                <td><?= !empty($dataRow->fir_number) ? $dataRow->fir_number : '' ?></td>
                <th>Lot No.:</th>
                <td><?= !empty($dataRow->fir_no) ? $dataRow->fir_no : '' ?></td>
            </tr>
        </table>
        <table class="table table-bordered-dark item-list-bb" style="margin-top: 2px;">
            <thead>
                <tr class="text-center bg-light">
                    <th style="width:3%;" rowspan="2">#</th>
                    <th class="text-left" rowspan="2">Special Char.</th>
                    <th class="text-left" rowspan="2">Product Parameter</th>
                    <th rowspan="2">Product Specification</th>
                    <th rowspan="2">Instrument</th>
                    <th rowspan="2">Sample Freq.</th>
                    <th colspan="4">Observation</th>
                    <th rowspan="2">Inspected By</th>
                </tr>
                <tr class="text-center bg-light">
                    <th>OK</th>
                    <th>Under Deviation</th>
                    <th>Rejection</th>
                    <th>Rework</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($firDimensionData)) :
                    $i = 1; $totalMcRejQty =0;;$totalUDQkQty =0;
                    foreach ($firDimensionData as $row) :
                        $diamention = '';
                        if ($row->requirement == 1) { $diamention = $row->min_req . '/' .  $row->max_req; }
                        if ($row->requirement == 2) {  $diamention = $row->min_req . ' ' .  $row->other_req;  }
                        if ($row->requirement == 3) { $diamention = $row->max_req . ' ' .  $row->other_req;  }
                        if ($row->requirement == 4) { $diamention = $row->other_req;  }
                        $totalMcRejQty +=$row->rej_qty;
                        $totalUDQkQty +=$row->ud_ok_qty;
                ?>
                        <tr class="text-center">
                            <td><?= $i ?></td>
                            <td class="text-left"><?php if (!empty($row->char_class)) { ?><img style="width:25px;display:inline-block;vertical-align:middle;" src="<?= base_url('assets/images/symbols/' . $row->char_class . '.png') ?>"><?php } ?></td>
                            <td><?= $row->product_param ?></td>
                            <td><?= $diamention ?></td>
                            <td><?= $row->fir_measur_tech ?></td>
                            <td><?= $row->fir_freq_text ?></td>
                            <td><?= !empty($row->ok_qty) ? floatval($row->ok_qty) : '' ?> </td>
                            <td><?= !empty($row->ud_ok_qty) ? floatval($row->ud_ok_qty) : '' ?></td>
                            <td><?= !empty($row->rej_qty) ? floatval($row->rej_qty) : '' ?></td>
                            <td><?= !empty($row->rw_qty) ? floatval($row->rw_qty) : '' ?></td>
                            <td><?= !empty($row->emp_name) ? $row->emp_name : '' ?> </td>
                        </tr>

                    <?php $i++;
                    endforeach;
                else : ?>
                    <tr>
                        <td colspan="10" class="text-center">No data available in table </td>
                    </tr>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3"><img style="width:25px;display:inline-block;vertical-align:middle;" src="<?= base_url('assets/images/symbols/critical.png') ?>"> <span style="">Critical Characteristic </span> </td>
                    <td><img style="width:25px;display:inline-block;vertical-align:middle;" src="<?= base_url('assets/images/symbols/major.png') ?>"> <span style="">Major </span></td>
                    <td><img style="width:25px;display:inline-block;vertical-align:middle;" src="<?= base_url('assets/images/symbols/minor.png') ?>"> <span style="">Minor</span></td>
                    <th class="text-left" > Total </th>
                    <th><?= (!empty(floatval($dataRow->total_ok_qty))) ? floatval($dataRow->total_ok_qty) : 0 ?></th>
                    <th><?= (!empty(floatval($totalUDQkQty))) ? floatval($totalUDQkQty) : 0 ?></th>
                    <th><?= (!empty(floatval($totalMcRejQty))) ? floatval($totalMcRejQty) : 0 ?></th>
                    <th><?= (!empty(floatval($dataRow->total_rw_qty))) ? floatval($dataRow->total_rw_qty) : 0 ?></th>
                    <th></th>
                </tr>
                <tr>
					<td style="width:50%;height:45" colspan="8"><b>Comment : </b><?= !empty($dataRow->remark) ? $dataRow->remark : '' ?></td>
					<td style="width:25%;vertical-align:bottom" colspan="3" class="text-center"><b>Verified By</b></td>
				</tr>
            </tfoot>
        </table>
    </div>
</form>