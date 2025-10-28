<form>
    <div class="col-md-12">
        <div class="row">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <th>Package No.</th>
                        <th style="width:10%;">Box Size (cm)</th>
                        <th>Item Name</th>
                        <th>Qty Per Box (Nos)</th>
                        <th>Total Box (Nos)</th>
                        <th>Total Qty. (Nos)</th>
                        <th>Net Weight Per Piece (kg)</th>
                        <th>Total Net Weight (kg)</th>
                        <th>Packing Weight (kg)</th>
                        <th>Item Gross Weight (kg)</th>
                        <th>Wooden Box Weight (kg)</th>
                        <th>Packing Gross Weight (kg)</th>
                    </tr>
                    <tr>
                        <?php
                            if (!empty($packingData)) {
                                $itemIds = array();
                                foreach ($packingData as $pack) {
                                    $itemIds = array();
                                    $transData = $pack->itemData; 
                                    $woodenWt = max(array_column($pack->itemData,'wooden_weight'));
                                    $itemGrWt =$transData[0]->netWeight + $transData[0]->pack_weight;
                                    $packGrWt = (array_sum(array_column($pack->itemData,'netWeight')) + array_sum(array_column($pack->itemData,'pack_weight'))) + $woodenWt;
                                    ?>
                                    <tr>
                                        <td rowspan="<?=count($pack->itemData)?>" class="text-center"><?= $pack->package_no ?></td>
                                        <td rowspan="<?=count($pack->itemData)?>" class="text-center"><?= $pack->wooden_size ?></td>
                                        <?php 
                                            if(!in_array($transData[0]->item_id,$itemIds)):
                                                $itemRowspan = 0;
                                                $itemRowspan = count(array_keys(array_column($transData,'item_id'), $transData[0]->item_id));
                                                $itemIds[] = $transData[0]->item_id;
                                                
                                                if($pdf_type == 0):
                                                    echo '<td class="text-left" rowspan="'.$itemRowspan.'">'.$transData[0]->item_code.'</td>';
                                                elseif($pdf_type == 1):
                                                    echo '<td class="text-left" rowspan="'.$itemRowspan.'">'.$transData[0]->part_no.' - '.$transData[0]->item_name.'</td>';
                                                elseif($pdf_type == 2):
                                                    echo '<td class="text-left" rowspan="'.$itemRowspan.'">'.$transData[0]->item_alias.'</td>';
                                                else:
                                                    echo '<td class="text-left" rowspan="'.$itemRowspan.'">'.$transData[0]->item_name.'</td>';
                                                endif;
                                            endif;
                                        ?>
                                        <td class="text-right"><?= round($transData[0]->qty_box,0) ?></td>
                                        <td class="text-right"><?= round($transData[0]->total_box,0) ?></td>
                                        <td class="text-right"><?= round($transData[0]->total_qty,0) ?></td>
                                        <td class="text-right"><?= $transData[0]->wpp?></td>
                                        <td class="text-right"><?= $transData[0]->netWeight ?></td>
                                        <td class="text-right"><?= $transData[0]->pack_weight ?></td>
                                        <td class="text-right"><?= sprintf("%.3f",$itemGrWt) ?></td>
                                        <td class="text-right" rowspan="<?= count($pack->itemData) ?>"><?= $woodenWt ?></td>
                                        <td class="text-right" rowspan="<?= count($pack->itemData) ?>"><?= sprintf("%.3f",$packGrWt) ?></td>
                                    </tr>
                                    <?php
                                }
                            }
                        ?>
                    </tr>
                </table>
               
                <table class="table mt-3">
                    <tr>
                        <td class="text-left"> <b> 1. Transport by : </b> <?=$dataRow->trans_way?></td>
                        <td class="text-left"> <b> 2. Delivery Terms : </b> <?=$dataRow->delivery_terms?></td>
                        <td class="text-left"> <b> 3. Container Type : </b> <?=$dataRow->container_type?></td>
                        <td class="text-left"> <b> 4. Packing Type : </b> <?=$dataRow->export_pck_type?></td>
                    </tr>
                </table>
            </div>
        </div>
        <hr>
        <div class="row">
            <!-- <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>" /> -->
            <input type="hidden" name="trans_no" id="trans_no" value="<?=(!empty($dataRow->trans_no))?$dataRow->trans_no:""?>" />
            <input type="hidden" name="packing_date" id="packing_date" value="<?=(!empty($dataRow->packing_date))?$dataRow->packing_date:""?>" />
            <div class="col-md-4 form-group">
                <label for="port_loading">Port of Loading</label>
                <input type="text" name="port_loading" id="port_loading" class="form-control " value="<?=(!empty($dataRow->port_loading))?$dataRow->port_loading:""?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="port_dispatch">Port of Dispatch</label>
                <input type="text" name="port_dispatch" id="port_dispatch" class="form-control " value="<?=(!empty($dataRow->port_dispatch))?$dataRow->port_dispatch:""?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="destination_country">Destination Country</label>
                <input type="text" name="destination_country" id="destination_country" class="form-control " value="<?=(!empty($dataRow->destination_country))?$dataRow->destination_country:""?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="nomination_agent">Nomination Agent</label>
                <input type="text" name="nomination_agent" id="nomination_agent" class="form-control " value="<?=(!empty($dataRow->nomination_agent))?$dataRow->nomination_agent:""?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="delivery_place">Place Of Delivery</label>
                <input type="text" name="delivery_place" id="delivery_place" class="form-control " value="<?=(!empty($dataRow->delivery_place))?$dataRow->delivery_place:""?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="delivery_payment_terms">Terms Of Delivery And Payment</label>
                <input type="text" name="delivery_payment_terms" id="delivery_payment_terms" class="form-control " value="<?=(!empty($dataRow->delivery_payment_terms))?$dataRow->delivery_payment_terms:""?>" />
            </div>
        </div>
    </div>
</form>