<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : ""; ?>" />
            <input type="hidden" name="prod_type" value="3" />
            <input type="hidden" name="log_type" value="2" />
            <input type="hidden" name="m_ct" id="m_ct" value="<?= (!empty($dataRow->m_ct)) ? $dataRow->m_ct : ""; ?>" />
            <input type="hidden" name="part_code" id="part_code" value="<?= (!empty($dataRow->item_code)) ? $dataRow->item_code : ""; ?>">
            <input type="hidden" id="part_id" name="product_id" value="<?= (!empty($dataRow->product_id)) ? $dataRow->product_id : ""; ?>">
            <input type="hidden" id="job_approval_id" name="job_approval_id" value="<?= (!empty($dataRow->job_approval_id)) ? $dataRow->job_approval_id : ""; ?>">
            <input type="hidden" name="job_card_id" id="job_card_id" value="<?= (!empty($dataRow->job_card_id)) ? $dataRow->job_card_id : ""; ?>">
            <input type="hidden" name="process_id" id="process_id" value="<?= (!empty($dataRow->process_id)) ? $dataRow->process_id : ""; ?>">
            <input type="hidden" name="ref_id" id="ref_id" value="<?= (!empty($dataRow->ref_id)) ? $dataRow->ref_id : ""; ?>">

            <div class="col-md-4 form-group">
                <label for="log_date">Date</label>
                <input type="date" name="log_date" id="log_date" class="form-control req" value="<?= (!empty($dataRow->log_date)) ? date('Y-m-d', strtotime($dataRow->log_date)) : date('Y-m-d'); ?>" min="<?= date("Y-m-d");?>">
            </div>
            <div class="col-md-4 form-group">
                <label for="in_challan_no">In Challan No</label>
                <input type="text" name="in_challan_no" id="in_challan_no" class="form-control req" value="<?= (!empty($dataRow->in_challan_no)) ? $dataRow->in_challan_no : ''; ?>">
            </div>
            <div class="col-md-4 form-group">
                <label for="production_qty">Production Qty.</label>
                <input type="text" name="production_qty" id="production_qty" class="form-control numericOnly partCount  req" min="0" value="<?= (!empty($dataRow->production_qty)) ? floatVal($dataRow->production_qty) : "0" ?>">
                <div class="error general_error"></div>
            </div>
        </div>
        
        <hr>
        
		<h4>Return Material : </h4>
		<div class="error genral_error"></div>
		<table id="packingBom" class="table table-bordered align-items-center">
			<thead class="thead-info">
				<tr>
					<th style="width:5%;">#</th>
					<th>Material</th>
					<th>Out Qty.</th>
					<th>In Qty.</th>
					<th>Pending Qty.</th>
				</tr>
			</thead>
			<tbody id="bomData">
				<?php
					$returnMaterial = !empty($return_material->material_data)?json_decode($return_material->material_data):[];
					if(!empty($returnMaterial)): $i=1;
						foreach($returnMaterial as $row):
							$item_name = "";
							if(!empty($row->item_id)){
							    $item_name = $this->item->getItem($row->item_id)->item_name;
							
    							$pendingQty = $row->out_qty - $row->in_qty;
    							if($pendingQty > 0):
    								echo '<tr>
    									<td style="width:5%;">'.$i++.'</td>
    									<td>
    										'.$item_name.'
    										<input type="hidden" name="item_id[]" value="'.$row->item_id.'">  
    									</td>
    									<td>
    										'.$row->out_qty.'
    										<input type="hidden" name="out_qty[]" value="'.$row->out_qty.'">
    										<input type="hidden" name="pre_in_qty[]" value="'.$row->in_qty.'">  
    									</td>
    									<td>
    										<input type="text" class="form-control floatOnly" name="in_qty[]" value="">                                
    									</td>
    									<td>
    										'.$pendingQty.'                                
    									</td>
    								</tr>';
    							endif;
							}
						endforeach;
					else:
						echo '<tr class="text-center"><td colspan="5">No Data Found</td></tr>';
					endif;
				?>
			</tbody>
		</table>
        
        <hr>
        
        <h4>Transactions : </h4>
        <div class="row">
            <div class="col-md-12 form-group">
                <div class="table-responsive">
                    <table class="table jpExcelTable">
                        <thead class="bg-light">
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>In Challan</th>
                                <th>Receive Qty</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="logTbody">
                            <?php
                                echo $transData;
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</form>
