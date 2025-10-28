<form autocomplete="off">
    <div class="col-md-12 form-group">
        <div class="row">
            <input type="hidden" name="type" id="type" value="<?=$type?>">
            <input type="hidden" name="doc_type" id="doc_type" value="<?=$doc_type?>">
            <input type="hidden" name="party_id" id="party_id" value="<?=(!empty($invoiceData->party_id))?$invoiceData->party_id:""?>">
            <input type="hidden" name="ref_id" id="ref_id" value="<?=(!empty($invoiceData->id))?$invoiceData->id:""?>">
            <div class="col-md-3 form-group">
                <label for="supply_type">Supply Type</label>
                <select name="supply_type" id="supply_type" class="form-control req">
                    <option value="">Select Supply Type</option>
                    <option value="O" selected>Outward</option>
                    <option value="I">Inward</option>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="sub_supply_type">Sub Supply Type</label>
                <select name="sub_supply_type" id="sub_supply_type" class="form-control req">
                    <option value="">Select Sub Supply Type</option>
                    <option value="1">Supply</option>
                    <option value="2">Import</option>
                    <option value="3">Export</option>
                    <option value="4" selected>Job Work</option>
                    <option value="5">For Own Use</option>
                    <option value="6">Job Work Return</option>
                    <option value="7">Sales Return</option>
                    <option value="8">Others</option>
                    <option value="9">SKD/CKD</option>
                    <option value="10">Line Sales</option>
                    <option value="11">Recipient Not Known</option>
                    <option value="12">Exhibition or Fairs</option>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="trans_mode">Transport Mode</label>
                <select name="trans_mode" id="trans_mode" class="form-control req">
                    <option value="">Select</option>
                    <option value="1" selected>Road</option>
                    <option value="2">Rail</option>
                    <option value="3">Air</option>
                    <option value="4">Ship</option>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="trans_distance">Distance (Km.)</label>
                <input type="number" name="trans_distance" id="trans_distance" class="form-control req numericOnly" placeholder="Distance (In km)" min="0" maxlength="4" value="<?=(!empty($invoiceData->distance))?(int) $invoiceData->distance:""?>" />
            </div>
            <div class="col-md-3 form-group">
                <label for="transport_doc_no">Trans. Doc. No.</label>
                <input type="text" name="transport_doc_no" id="transport_doc_no" class="form-control" placeholder="Trans. Doc. No." maxlength="16" value="" />
                <div class="error transport_doc_no"></div>
            </div>
            <div class="col-md-3 form-group">
                <label for="transport_name">Transport Name</label>
                <select name="transport_name" id="transport_name" class="form-control single-select transport">
                    <option value="">Select Transport</option>
                    <?php
                        foreach($transportData as $row):
                            echo '<option value="'.$row->transport_name.'" data-val="'.$row->transport_id.'">'.$row->transport_name.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="transport_id">Transport Id</label>
                <input type="text" name="transport_id" id="transport_id" class="form-control" placeholder="Transport Id" maxlength="15" value="" />
            </div>
            <div class="col-md-3 form-group">
                <label for="transport_doc_date">Transport Doc. Date</label>
                <input type="date" class="form-control" name="transport_doc_date" id="transport_doc_date" value="<?=date('Y-m-d')?>" >
            </div>
            <div class="col-md-3 form-group">
                <label for="vehicle_no">Vehicle No.</label>
                <input type="text" name="vehicle_no" id="vehicle_no" class="form-control" placeholder="Vehicle No." maxlength="15" value="" />
            </div>
            <div class="col-md-3 form-group">
                <label for="vehicle_type">Vehicle Type</label>
                <select name="vehicle_type" id="vehicle_type" class="form-control req">
                    <option value="R">Regular</option>
                    <option value="O">ODC</option>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="transaction_type">Transaction Type</label>
                <select name="transaction_type" id="transaction_type" class="form-control req">
                    <option value="1">Regular</option>
                    <option value="2">Bill To - Ship To</option>
                    <option value="3">Bill From - Dispatch From</option>
                    <option value="4">Combination of 2 and 3</option>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="ship_pincode">Shipping Pincode</label>
                <input type="text" name="ship_pincode" id="ship_pincode" class="form-control req" placeholder="Shipping Pincode" maxlength="16" value="<?=(!empty($invoiceData->party_pincode))?$invoiceData->party_pincode:""?>" />
            </div>
            <div class="col-md-12 form-group">
                <label for="ship_address">Shipping Address</label>
                <input type="text" name="ship_address" id="ship_address" class="form-control req" placeholder="Shipping Address" maxlength="16" value="<?=(!empty($invoiceData->party_address))?$invoiceData->party_address:""?>" />
            </div>
        </div>
    </div>
</form>