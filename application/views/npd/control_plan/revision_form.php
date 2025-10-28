<div class="col-md-12">
    <form id="revForm">
        <div class="row">
            <input type="hidden" name="id" value=" ">
            <input type="hidden" name="rev_type" value="<?=$rev_type?> ">
            <input type="hidden" id="item_id" name="item_id" value="<?= !empty($itemData->item_id) ? $itemData->item_id : $item_id ?>">
       
            <div class="col-md-4 form-group">
                <label for="rev_date">Date</label>
                <input type="date" name="rev_date" id="rev_date" class="form-control req" value="<?=date("Y-m-d")  ?>">
            </div>
            <div class="col-md-4 form-group">
                <label for="rev_no">Rev. No.</label>
                <input type="text" name="rev_no" id="rev_no" class="form-control req" >
            </div>
            <div class="col-md-4 form-group">
                <label for="cust_rev_no">Cust. Rev. No.</label>
                <select name="cust_rev_no" id="cust_rev_no" class="form-control single-select req">
                    <option value="">Select Rev No</option>
                    <?php
                    if(!empty($revNoList)){
                        foreach($revNoList as $row){
                            $selected = (!empty($itemData->cust_rev_no) && $itemData->cust_rev_no == $row->rev_no)?'selected':''
                            ?><option value="<?=$row->rev_no?>" <?=$selected?>><?=$row->rev_no?></option><?php
                        }
                    }
                    ?>
                </select>
                <div class="error cust_rev_no"></div>
            </div>
            <div class="col-md-10 form-group">
                <label for="remark">Remark</label>
                <input type="text" name="remark" id="remark" class="form-control" >
            </div>

            <?php if($rev_type == 1): ?>
                <div class="col-md-2 form-group">
                    <button type="button" class="btn btn-block btn-success waves-light mt-30 save-form" onclick="saveRevision('addPfcRev');" ><i class="fa fa-plus"></i> Save</button>
                </div>
            <?php else: ?>
                <div class="col-md-2 form-group">
                    <button type="button" class="btn btn-block btn-success waves-light mt-30 save-form" onclick="saveRevision('addCPRevision');" ><i class="fa fa-plus"></i> Save</button>
                </div>
            <?php endif; ?>
            
        </div>
    </form>
    <hr>
    <div class="row">
        <div class="table-responsive">
            <table id="revisionTbl" class="table table-bordered align-items-center">
                <thead class="thead-info">
                    <tr>
                        <th style="width:5%;">#</th>
                        <th>Date</th>
                        <th>Rev. No.</th>
                        <th>Cust. Rev. No.</th>
                        <th>Remark</th>
                        <th class="text-center" style="width:10%;">Action</th>
                    </tr>
                </thead>
                <tbody id="revBody">
                    <?php
                        if(!empty($revisionData['tbody'])):
                            echo $revisionData['tbody'];
                        endif;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="<?php echo base_url();?>assets/js/custom/control-plan.js?v=<?=time()?>"></script>