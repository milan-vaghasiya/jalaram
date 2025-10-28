<form>
    <input type="hidden" name="vendor_id" id="vendor_id" value="<?=$party_id?>" />
    <input type="hidden" name="id" id="id" value="" />
    <div class="row">
        <div class="col-md-3 form-group">
            <label for="trans_date"> Challan Date</label>
            <input type="date" name="trans_date" id="trans_date" class="form-control req" min="<?= date("Y-m-d");?>" value="<?=date("Y-m-d")?>" >
        </div>
        <div class="col-md-3 form-group">
            <label for="trans_number"> Challan No</label>
            <input type="text" name="trans_number" id="trans_number" class="form-control req" value="<?=getPrefixNumber($trans_prefix,$trans_no)?>" readonly >

            <input type="hidden" name="trans_no"  id="trans_no" value="<?=$trans_no?>" >
            <input type="hidden" name="trans_prefix"  id="trans_prefix" value="<?=$trans_prefix?>">
        </div>
        <div class="col-md-3 form-group">
            <label for="process_id"> Process</label>
            <select name="process_id" id="process_id" class="form-control single-select req">
                <option value=""> Select Process</option>
                <?php
                    if(!empty($processList)){
                        foreach ($processList as $row) :
                            echo '<option value="' . $row->id . '">' . $row->process_name . '</option>';
                        endforeach;
                    }
                ?>
            </select>
        </div>
        <div class="col-md-12 form-group">
            <!-- <div class="table-responsive"> -->
                <div class="error orderError"></div>
                <table id="jobWorkVendorTable" class="table table-bordered">
                    <thead class="thead-info">
                        <tr>
                            <th></th>
                            <th>Job No</th>
                            <th>Product</th>
                            <th>Process</th>
                            <th>Qty</th>
                            <th>Pending Qty</th>
                            <th>J.W. Order</th>
                            <th>Challan Qty</th>
                            <th>Weight</th>
                        </tr>
                    </thead>
                    <tbody id="challanTbody">
                    </tbody>
                </table>
            <!-- </div> -->

        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-12">
            <h4> Material Details : </h4>
        </div>
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="item_id"> Material</label>
                    <select name="item_id" id="item_id" class="form-control single-select req">
                        <option value=""> Material</option>
                        <?php
                        if(!empty($materialData)){
                            foreach ($materialData as $row) :
                                echo '<option value="' . $row->id . '">' . $row->item_name . '</option>';
                            endforeach;
                        }
                        
                        ?>
                    </select>
                </div>
                <div class="col-md-4 form-group">
                    <label for="out_qty">Qty</label>
                    <input type="text" name="out_qty" id="out_qty" class="form-control floatOnly req" value="" />
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-outline-success waves-effect waves-light float-right mt-30 save-form" onclick="AddRow();" ><i class="fa fa-plus"></i> Add</button>
                </div>
            </div>    
        </div>
        <hr>
        <div class="col-md-12">
            <div class="table-responsive">
                <div class="error packingError"></div>
                <table id="packingBom" class="table table-bordered align-items-center">
                    <thead class="thead-info">
                        <tr>
                            <th style="width:5%;">#</th>
                            <th>Packing Material</th>
                            <th>Qty</th>
                            <th class="text-center" style="width:10%;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="bomData">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>