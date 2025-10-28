<style>
    .tbodyAlign tbody tr th{
      text-align:left !important;
    }
</style>
<table class="table" style="border-bottom:1px solid #000000;">
    <tr>
        <td class="text-uppercase text-center" style="font-size:1.3rem;font-weight:bold;width:40%;"><?=$companyData->company_name?><br><span style="font-weight:normal;">Product Costing</span></td>
    </tr>R
</table>
<table class="table item-list-bb" style="margin-top:2px;">
    <tr class="text-left">
        <th style="width:15%;" class="bg-light">Enq. No</th>
        <td style="width:35%"><?=(!empty($dataRow->enq_number) ? $dataRow->enq_number : '')?></td>
        <th style="width:15%;" class="bg-light">Enq Date</th>
        <td style="width:35%"><?=(!empty($dataRow->enq_date) ? formatDate($dataRow->enq_date) : '')?></td>
    </tr>
    <tr class="text-left">
        <th class="bg-light">Product Code </th><td><?=(!empty($dataRow->item_code) ? $dataRow->item_code : '')?></td>
        <th class="bg-light">Product Name</th><td><?=(!empty($dataRow->item_name) ? $dataRow->item_name : '')?></td>
    </tr>
    <tr class="text-left">
        <th class="bg-light">Customer </th><td><?=(!empty($dataRow->party_name) ? $dataRow->party_name : '')?></td>
        <th class="bg-light">MOQ</th><td><?=(!empty($dataRow->moq) ? $dataRow->moq : '')?></td>
    </tr>
</table>
<!-- RM COST -->
<table class="table item-list-bb tbodyAlign" style="margin-top:2px;">
    <thead class="bg-light">
        <tr>
            <th colspan="2" class="text-center bg-light">Raw Material Detail</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th style="width:40%;" >Material Specification</th>
            <td style="width:60%;"> <?=(!empty($dataRow->material_grade)?$dataRow->material_grade:'')?> </td>
        </tr>
        <tr>
            <th>Dimension</th>
            <td> <?=(!empty($dataRow->dimension)?$dataRow->dimension:'')?> </td>
        </tr>
        <tr>
            <th>Gross Weight Piece</th>
            <td> <?=$dataRow->gross_wt?> </td>
        </tr>
        <tr>
            <th>Req. Raw Material</th>
            <td> <?=$dataRow->gross_wt * $dataRow->moq?> </td>
        </tr>
        <tr>
            <th>RM Rate</th>
            <td> <?=(!empty($dataRow->rm_rate)? $dataRow->rm_rate : 0)?> </td>
        </tr>
        <tr>
            <th>Finish Weight</th>
            <td> <?=(!empty($dataRow->finish_wt)? $dataRow->finish_wt : 0)?> </td>
        </tr>
        <tr>
            <th>Scrap Weight</th>
            <td> <?=(!empty($dataRow->scrap_wt)? $dataRow->scrap_wt : ($dataRow->gross_wt - $dataRow->finish_wt))?> </td>
        </tr>
        <tr>
            <th>Scrap Rate</th>
            <td> <?=(!empty($dataRow->scrap_rate)? $dataRow->scrap_rate : '')?> </td>
        </tr>
        <tr>
            <th>RM Gross Value/Piece</th>
            <td> <?=$dataRow->rm_rate * $dataRow->gross_wt?> </td>
        </tr>
        <tr>
            <th>Scrap Rate/Rs</th>
            <td> <?=$dataRow->scrap_rate * $dataRow->scrap_wt?> </td>
        </tr>
        <tr>
            <th>Receiving Inspection + Chemical + Microstructure</th>
            <td> <?=(!empty($dataRow->rcv_insp_rate)? $dataRow->rcv_insp_rate : 0)?> </td>
        </tr>
        <tr>
            <th>RM Profit(%)</th>
            <td> <?=(!empty($dataRow->rm_profit)? $dataRow->rm_profit : 0)?> </td>
        </tr>
    </body>
    <tfoot >
        <tr>
            <th class="bg-light">I. Total Cost of RM</th>
            <th class="bg-light  "> <?=(!empty($dataRow->total_rm_cost)? $dataRow->total_rm_cost : 0)?>  </th>
        </tr>
    </tfoot>
</table>

<!-- MFG COST -->
 <table class="table item-list-bb" style="margin-top:2px;">
    <thead >
        <tr>
            <th colspan="4" class="text-center bg-light">Manufacturing Proceses</th>
        </tr>
        <tr>
            <th style="width:55%;" class="bg-light">Process Name</th>
            <th style="width:15%;" class="bg-light">MHR</th>
            <th style="width:15%;" class="bg-light">Cycle time</th>
            <th style="width:15%;" class="bg-light">Costing</th>
        </tr>
    </thead>
    <tbody>
        <?php
            if(!empty($mfgProcessList)){
                foreach($mfgProcessList AS $row){
                    ?>
                    <tr>
                        <td><?=$row->process_name?></td>
                        <td class="text-center"><?=(!empty($row->mhr)?$row->mhr:'')?></td>
                        <td class="text-center"><?=(!empty($row->cycle_time)?$row->cycle_time:'')?></td>
                        <td class="text-center"><?=$row->process_cost?></td>
                    </tr>
                    <?php
                }
            }
        ?>
    </tbody>
    <tfoot >
        <tr>
            <th colspan="3" class="bg-light">II. Manufacturing Cost</th>
            <th class="text-center bg-light"> <?=(!empty($dataRow->mfg_process_cost)? $dataRow->mfg_process_cost : 0)?> </th>
        </tr>
        <tr>
            <th colspan="3" class="bg-light">RM + Manufacturing  COST</th>
            <th class="text-center bg-light"><?=$dataRow->total_rm_cost+$dataRow->mfg_process_cost ?></th>
        </tr>
    </tfoot>
</table>
<table>

</table>
<table class="table item-list-bb tbodyAlign" style="margin-top:2px;">
    <thead>
        <tr>
            <th colspan="3" class="text-center bg-light">Overheads Cost</th>
        </tr>
        <tr>
            <th class="bg-light">Overheads</th>
            <th class="bg-light">Per(%)</th>
            <th class="bg-light">Cost</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th>Tool Maintenance</th>
            <td class="text-center"><?=(!empty($dataRow->tool_per)? $dataRow->tool_per : 0)?></td>
            <td class="text-center"><?=(!empty($dataRow->tool_cost)? $dataRow->tool_cost : 0)?></td>
        </tr>
        <tr>
            <th>Rejection Cost</th>
            <td class="text-center"><?=(!empty($dataRow->rej_per)? $dataRow->rej_per : 0)?></td>
            <td class="text-center"><?=(!empty($dataRow->rej_cost)? $dataRow->rej_cost : 0)?></td>
        </tr>
        <tr>
            <th>Overheads on Process</th>
            <td class="text-center"><?=(!empty($dataRow->overheads_per)? $dataRow->overheads_per : 0)?></td>
            <td class="text-center"> <?=(!empty($dataRow->overheads_cost)? $dataRow->overheads_cost : 0)?></td>
        </tr>
        <tr>
            <th>ICC Cost</th>
            <td class="text-center"><?=(!empty($dataRow->icc_per)? $dataRow->icc_per : 0)?></td>
            <td class="text-center"><?=(!empty($dataRow->icc_cost)? $dataRow->icc_cost : 0)?></td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="2" class="bg-light text-right">SUB TOTAL WITHOUT PROFIT</th>
            <th class="bg-light"><?=(!empty($dataRow->sub_total)? $dataRow->sub_total : 0)?></th>
        </tr>
        <tr>
            <th class="text-left">Profit Percentage</th>
            <th><?=(!empty($dataRow->profit_per)? $dataRow->profit_per : 0)?></th>
            <th><?=(!empty($dataRow->profit_value)? $dataRow->profit_value : 0)?></th>
        </tr>
        <tr>
            <th colspan="2" class="bg-light text-right">Final Cost Per Part</th>
            <th class="bg-light"><?=(!empty($dataRow->final_cost)? $dataRow->final_cost : 0)?></th>
        </tr>
        <tr>
            <th colspan="2" class="bg-light text-right">Total Final Cost</th>
            <th class="bg-light"><?=$dataRow->final_cost * $dataRow->moq?></th>
        </tr>
    </tfoot>
</table>
