<form>
    <div class="row">
        <input type="hidden" name="challan_trans_id" value="<?=$dataRow->id?>">
        <div class="col-md-6 form-group">
            <label for="in_challan_date">Date</label>
            <input type="date" name="in_challan_date" value="<?=date("Y-m-d")?>"  class="form-control req">
        </div>
        <div class="col-md-6 form-group">
            <label for="in_challan_no">Challan No</label>
            <input type="text" name="in_challan_no" id="in_challan_no" class="form-control req"> 
        </div>
       
    </div>
    
    <table class="table">
        <thead>
        <tr>
            <th>Process</th>
            <th>Production Qty</th>
            <th>Without Process</th>
        </tr>
        </thead>
        <tbody>
            <?php
            $processArray = explode(",",$dataRow->process_ids);
            $processMaster = array_reduce($processList, function($processMaster, $process) { 
					$processMaster[$process->id] = $process; 
					return $processMaster; 
				}, []);
            foreach($processArray AS $process){
                ?>
                <tr>
                    <td><?=$processMaster[$process]->process_name?></td>
                    <td>
                        <input type = "text" class="form-control" name="production_qty[]">
                        <input type="hidden" name="process_id[]" value="<?=$process?>">
                        <div class="error production_qty<?=$process?>">
                    </td>
                    <td>
                        <input type = "text" class="form-control" name="without_prs_qty[]">
                    </td>
                </tr>
                <?php

            }
            ?>
        </tbody>

    </table>

</form>