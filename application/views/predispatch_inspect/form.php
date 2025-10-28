<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h4><u>PreDispatch Inspection Report</u></h4>
                    </div>
                    <div class="card-body">
                        <form autocomplete="off" id="savePreDispatchInspection">
                            <div class="col-md-12">
                                <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>" />

                                <div class="row">
									<div class="col-md-2 form-group">
                                        <label for="item_id">Product</label>
                                        <select name="item_id" id="item_id" class="form-control single-select req">
                                            <option value="">Select Item</option>
                                            <?php $i=0;
                                                foreach($itemData as $row):
                                                    $selected = (!empty($dataRow->item_id) && $dataRow->item_id == $row->id)?"selected":"";
							                        echo '<option value="'.$row->id.'" '.$selected.'>'.$row->item_code.'</option>';
                                                endforeach;
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-2 form-group">
                                        <label for="date">Inspection Date</label>
                                        <input type="date" name="date" id="date" class="form-control req" value="<?=(!empty($dataRow->date))?$dataRow->date:$maxDate?>" min="<?=$startYearDate?>" max="<?=$maxDate?>" />
                                    </div>
                                    <div class="col-md-2 form-group">
                                        <label for="dispatch_qty">Inspection Qty.</label>
                                        <input type="text" name="dispatch_qty" id="dispatch_qty" class="form-control floatOnly req" value="<?=(!empty($dataRow->dispatch_qty))?$dataRow->dispatch_qty:0?>" />
                                    </div>
                                    <div class="col-md-2 form-group">
                                        <label for="inv_no">Invoice No.</label>
                                        <input type="text" name="inv_no" id="inv_no" class="form-control" value="<?=(!empty($dataRow->inv_no))?$dataRow->inv_no:""?>" />
                                    </div>
                                    <div class="col-md-2 form-group">
                                        <label for="inv_date">Invoice Date</label>
                                        <input type="date" name="inv_date" id="inv_date" class="form-control" value="<?=(!empty($dataRow->inv_date))?$dataRow->inv_date:$maxDate?>"  min="<?=$startYearDate?>" max="<?=$maxDate?>" />
                                    </div>
                                    <div class="col-md-2 form-group">
                                        <label for="batch_no">Batch No.</label>
                                        <input type="text" name="batch_no" id="batch_no" class="form-control" value="<?=(!empty($dataRow->batch_no))?$dataRow->batch_no:""?>" />
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="col-md-12">
                                <div class="error general"></div>
                            </div>
							<div class="col-md-12 mt-3">
								<div class="row form-group">
									<div class="table-responsive">
										<table id="preDispatchtbl" class="table table-bordered generalTable">
											<thead class="thead-info">
												<tr style="text-align:center;">
													<th rowspan="2" style="width:5%;">#</th>
													<th rowspan="2">Parameter</th>
													<th rowspan="2">Specification</th>
													<th rowspan="2">Lower Limit</th>
													<th rowspan="2">Upper Limit</th>
													<th rowspan="2">Msrmnt. Technique</th>
													<th colspan="10">Observation on Samples</th>
													<th rowspan="2">Result</th>
                                                </tr>
                                                <tr style="text-align:center;">
													<th>1</th>
													<th>2</th>
													<th>3</th>
													<th>4</th>
													<th>5</th>
													<th>6</th>
													<th>7</th>
													<th>8</th>
													<th>9</th>
													<th>10</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbodyData">
                                                <?php
                                                    if(!empty($dataRow)):
                                                        $obj = json_decode($dataRow->observe_samples); $i=1; 
                                                        if(!empty($paramData)):
                                                            foreach($paramData as $row):
                                                                $c=0;
                                                                echo '<tr>
                                                                            <td style="text-align:center;">'.$i++.'</td>
                                                                            <td>'.$row->parameter.'</td>
                                                                            <td>'.$row->specification.'</td>
                                                                            <td>'.$row->lower_limit.'</td>
                                                                            <td>'.$row->upper_limit.'</td>
                                                                            <td>'.$row->measure_tech.'</td>';
                                                                for($c=0;$c<10;$c++):
                                                                    echo '<td><input type="text" name="sample'.($c+1).'_'.$row->id.'" class="xl_input maxw-60 text-center" value="'.$obj->{$row->id}[$c].'"></td>';
                                                                endfor;
                                                                echo '<td><input type="text" name="result_'.$row->id.'" class="xl_input maxw-150 text-center" value="'.$obj->{$row->id}[10].'"></td></tr>';
                                                            endforeach;
                                                        else:
                                                            echo '<tr><td colspan="17" style="text-align:center;">No Data Found</td></tr>';
                                                        endif;
                                                    else:
                                                        echo '<tr><td colspan="17" style="text-align:center;">No Data Found</td></tr>';
                                                    endif;
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <div class="col-md-12">
                            <button type="button" class="btn waves-effect waves-light btn-outline-success float-right save-form" onclick="savePreDispatch('savePreDispatchInspection');" ><i class="fa fa-check"></i> Save</button>
                            <a href="<?=base_url($headData->controller)?>" class="btn waves-effect waves-light btn-outline-secondary float-right save-form" style="margin-right:10px;"><i class="fa fa-times"></i> Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){
    $(document).on('change','#item_id',function(){
		var item_id = $(this).val();
        if(item_id)
		{
			$.ajax({
				url: base_url + controller + '/getPreDispatchInspection',
				data: {item_id:item_id},
				type: "POST",
				dataType:'json',
				success:function(data){
                    $("#tbodyData").html(data.tbodyData);
					$("#item_id").comboSelect();
				}
			});
		}
    });
});

function savePreDispatch(formId,fnsave){
    setPlaceHolder();
	if(fnsave == "" || fnsave == null){fnsave="save";}
	var form = $('#'+formId)[0];
	var fd = new FormData(form);
	$.ajax({
		url: base_url + controller + '/' + fnsave,
		data:fd,
		type: "POST",
		processData:false,
		contentType:false,
		dataType:"json",
	}).done(function(data){
		if(data.status===0){
			$(".error").html("");
			$.each( data.message, function( key, value ) {$("."+key).html(value);});
		}else if(data.status==1){
			initTable(); $('#'+formId)[0].reset();$(".modal").modal('hide');   
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
            location.href = base_url + '/preDispatchInspect';
        }else{
			initTable();  $('#'+formId)[0].reset();$(".modal").modal('hide');   
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
            location.href = base_url + '/preDispatchInspect';
        }
	});
}
</script>