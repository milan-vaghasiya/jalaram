<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <input type="hidden" name="type" value="<?=(!empty($dataRow->type))?$dataRow->type:$type; ?>" />
          
            <?php
                if((!empty($dataRow->type) && $dataRow->type ==6) || ( !empty($type) && $type == 6)){?>
                    <div class="col-md-12 form-group">
                        <label for="title">Label</label>
                        <input type="text" name="title" class="form-control req" value="<?=(!empty($dataRow->title))?$dataRow->title:"";?>" />
                    </div>  
                    <div class="col-md-12 form-group">
                        <label for="party_id">Customer</label>
                        <select name="party_id" id="party_id" class="form-control single-select req">
                            <option value="">Select  Customer</option>
                            <?php
                                foreach ($customerData as $row) :
                                    $selected = (!empty($dataRow->party_id) && $dataRow->party_id == $row->id) ? "selected" : "";
                                    echo '<option value="'. $row->id .'" '.$selected.'>'.$row->party_name.'</option>';
                                endforeach;
                             ?>
                        </select>
                    </div>
            <?php } else if((!empty($dataRow->type) && $dataRow->type == 8) || ( !empty($type) && $type == 8)){ ?>
                    <div class="col-md-12 form-group">
                        <label for="title">Revision Checkpoint</label>
                        <input type="text" name="title" class="form-control req" value="<?=(!empty($dataRow->title))?$dataRow->title:"";?>" />
                    </div>      
            <?php }  else {?>
                    <div class="col-md-12 form-group">
                        <label for="title">Title</label>
                        <input type="text" name="title" class="form-control req" value="<?=(!empty($dataRow->title))?$dataRow->title:"";?>" />
                    </div>            
                    <div class="col-md-12 form-group">
                        <label for="remark">Remark</label>
                        <textarea name="remark" id="remark" class="form-control"><?=(!empty($dataRow->remark))?$dataRow->remark:""?></textarea>
                    </div>            
            <?php } ?>
        </div>
    </div>
</form>