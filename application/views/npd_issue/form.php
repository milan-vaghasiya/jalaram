<form>
    <?php
        /** Updated By Mansee @ 19-02-22 */
    ?>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : "" ?>" />
            <input type="hidden" id="count_item" value="0">

            <div class="col-md-3 form-group">
                <label for="dispatch_date">Issue Date</label>
                <input type="date" name="dispatch_date" id="dispatch_date" class="form-control" min="<?= (!empty($dataRow)) ? $dataRow->req_date : $startYearDate ?>" max="<?=$maxDate?>" value="<?= (!empty($dataRow->dispatch_date)) ? $dataRow->dispatch_date : $maxDate ?>">
            </div>

            <div class="col-md-2 form-group">
                <label for="">Job Card No.</label>
                <input type="text" id="job_number" class="form-control" value="<?=(!empty($dataRow))?getPrefixNumber($dataRow->job_prefix,$dataRow->job_no):""?>" readonly>
                <input type="hidden" name="job_no" id="job_no" value="<?=(!empty($dataRow))?$dataRow->job_no:""?>">
                <input type="hidden" name="job_prefix" id="job_prefix" value="<?=(!empty($dataRow))?$dataRow->job_prefix:""?>">
                <input type="hidden" name="job_card_id" id="job_card_id" class="form-control"  value="<?=  (!empty($dataRow)) ? $dataRow->job_card_id : "" ?>">

            </div>

            <div class="col-md-3 form-group">
                <label for="dispatch_item_id">Issue Item Name</label>
                <input type="text" name="dispatch_item_id" id="dispatch_item_id" class="form-control"  value="<?=  (!empty($dataRow)) ? $dataRow->item_name : "" ?>" readonly>
                <input type="hidden" name="item_id" id="item_id" class="form-control"  value="<?=  (!empty($dataRow)) ? $dataRow->item_id : "" ?>" readonly>
            </div>

            <div class="col-md-2 form-group">
                <label for="req_qty">Request Qty.</label>
                <input type="number" id="req_qty" class="form-control" value="<?= (!empty($dataRow->req_qty)) ? $dataRow->req_qty : 0 ?>" readonly />
            </div>

            <div class="col-md-2 form-group">
                <label for="pending_qty">Pending Qty.</label>
                <input type="number" id="pending_qty" class="form-control" value="<?= (!empty($dataRow->req_qty)) ? ($dataRow->req_qty - $dataRow->dispatch_qty) : 0 ?>" readonly />
            </div>

            <div class="col-md-3 form-group lc">
                <label for="location_id">Store Location</label>
                <select id="location_id" class="form-control single-select1 model-select2 req">
                    <option value="" data-store_name="">Select Location</option>
                    <?php
                    foreach ($locationData as $lData) :
                        echo '<optgroup label="' . $lData['store_name'] . '">';
                        foreach ($lData['location'] as $row) :
                            $selected = (!empty($dataRow->location_id) && $dataRow->location_id == $row->id) ? "selected" : '';
                            $disabled = (!empty($dataRow->location_id) && $dataRow->location_id != $row->id) ? "disabled" : '';

                            echo '<option value="' . $row->id . '" data-store_name="' . $lData['store_name'] . '" ' . $selected . ' ' . $disabled . '>' . $row->location . ' </option>';
                        endforeach;
                        echo '</optgroup>';
                    endforeach;
                    ?>
                </select>
            </div>

            <div class="col-md-3 form-group">
                <label for="batch_no">Heat/Batch No.</label>
                <select id="batch_no" class="form-control select2 single-select11 req">
                    <option value="">Select Batch No.</option>
                    <?php
                    if (!empty($batchData)) :
                        foreach ($batchData as $row) :
                            if ($row->qty > 0) :
                                $selected = (!empty($dataRow->batch_no) && $dataRow->batch_no == $row->batch_no) ? "selected" : 'disabled';

                                echo '<option value="' . $row->batch_no . '" data-stock="' . $row->qty . '" ' . $selected . '>' . $row->batch_no . '</option>';
                            endif;
                        endforeach;
                    endif;
                    ?>
                </select>
            </div>

            <div class="col-md-2 form-group">
                <label for="batch_stock">Stock Qty.</label>
                <input type="text" id="batch_stock" class="form-control" value="" readonly />
            </div>

            <div class="col-md-2 form-group">
                <label for="dispatch_qty">Issue Qty.</label>
                <input type="numbet" id="dispatch_qty" name="dispatch_qty" class="form-control floatOnly req" value="<?=!empty($dataRow->dispatch_qty)?$dataRow->dispatch_qty:''?>" />

            </div>

            <div class="col-md-2 form-group">
                <label>&nbsp;</label>
                <button type="button" class="btn waves-effect waves-light btn-primary btn-block addRow"><i class="fas fa-plus"></i> Add</button>
            </div>

            <div class="col-md-12 form-group">
                <div class="error general_batch_no"></div>
                <div class="table-responsive ">
                    <table id="issueItems" class="table table-striped table-borderless">
                        <thead class="thead-info">
                            <tr>
                                <th style="width:5%;">#</th>
                                <th>Location</th>
                                <th>Batch No.</th>
                                <th>Qty.</th>
                                <th style="width:10%;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="tempItem">
                            <tr id="noData">
                                <td class="text-center" colspan="5">No Data Found</td>
                            </tr>
                        </tbody>
                    </table>
                    <?php
                    if (!empty($dataRow)) :
                        if (!empty($batchTrans)) :
                            foreach ($batchTrans as $row) :
                                echo '<script>var postData={id:"",batch_no:"' . $row->batch_no . '",qty:"' . abs($row->qty) . '",location_id:"' . $row->location_id . '",location_name:"[ ' . $row->store_name . ' ] ' . $row->location . '"}; addRow(postData); $("#count_item").val(parseFloat($("#count_item").val()) + 1);</script>';
                            endforeach;
                        endif;
                    endif;
                    ?>
                </div>
            </div>

            <div class="col-md-12 form-group">
                <label for="remark">Remark</label>
                <input type="text" name="remark" id="remark" class="form-control" value="<?= (!empty($dataRow->remark)) ? $dataRow->remark : "" ?>">
            </div>
        </div>
    </div>
</form>
<script>
    $(document).ready(function() {
        $('.model-select2').select2({
            dropdownParent: $('.model-select2').parent()
        });
        <?php
        if(!empty($batchData))
        {
            ?>
            $("#batch_no").trigger("change");
            <?php
        }
        ?>
    });
</script>