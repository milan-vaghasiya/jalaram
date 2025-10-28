<form>
    <div class="row">
        <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
        <input type="hidden" name="is_delete" id="is_delete" value="<?=(!empty($dataRow->is_delete))?$dataRow->is_delete:""; ?>" />

        <div class="col-md-4 form-group">
            <label for="emp_joining_date">Leaving Date</label>
            <input type="date" name="emp_joining_date" class="form-control" value="<?=($dataRow->is_delete==2)?'hidden':date("Y-m-d")?>" <?=($dataRow->is_delete==2)?'hidden':''?> />
            <input type="date" name="emp_relieve_date" class="form-control" value="<?=($dataRow->is_delete==0)?'hidden':date("Y-m-d")?>" <?=($dataRow->is_delete==0)?'hidden':''?> />
        </div>

        <div class="col-md-8 form-group" <?=($dataRow->is_delete==0)?'hidden':''?>>
            <label for="reason">Reason</label>
            <input type="text" name="reason" class="form-control req" value="" />
        </div>
    </div>
</form> 
<?php if (!empty($empFacility)){ ?>
<div class="table-responsive">
    <strong class="fs-16 text-primary">Facility Provided : </strong>
    <table id="empFacilitytbl" class="table table-bordered align-items-center">
        <thead class="thead-info">
            <tr>
                <th style="width:5%;">#</th>
                <th>Issue Date</th>
                <th>Time Till Issue</th>
                <th>Type</th>
                <th>Description </th>
                <th>Specification</th>
            </tr>
        </thead>
        <tbody id="empFacilityBody">
            <?php
                $i = 1;
                foreach ($empFacility as $row) :
                    $type="";$trCls = '';
                    if($row->type == 1){$type="Uniform";}elseif($row->type == 2){$type="Quater";}elseif($row->type == 3){$type="Mobile";}
                    
                    if(strtotime($row->issue_date) > strtotime('-6 months')){$trCls = 'text-danger';}
                    echo '<tr class="'.$trCls.'">
                        <td>' . $i++ . '</td>
                        <td>' . (formatDate($row->issue_date)) . '</td>
                        <td>' . (d2ymd_words($row->issue_date)) . '</td>
                        <td>' . $type . '</td>
                        <td>' . $row->description . ' </td>
                        <td>' . $row->specs . '</td>
                    </tr>';
                endforeach;
            ?>
        </tbody>
    </table>
</div>
<?php } ?>