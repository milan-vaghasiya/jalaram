<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h4><u>Raw Material Reciving Inspection Report</u></h4>
                    </div>
                    <div class="card-body">
                        <form autocomplete="off" id="InInspection">
                            <?php
                                $sample_size = (!empty($paramData)?$paramData[0]->iir_size:10);
                            ?>
                            <div class="col-md-12">
                                <input type="hidden" name="id" value="<?=(!empty($inInspectData->id))?$inInspectData->id:""?>" />
                                <input type="hidden" name="grn_id" value="<?=(!empty($dataRow->grn_id))?$dataRow->grn_id:""?>" />
                                <input type="hidden" name="grn_trans_id" id="grn_trans_id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>" />
                                <input type="hidden" name="party_id" value="<?=(!empty($dataRow->party_id))?$dataRow->party_id:""?>" />
                                <input type="hidden" name="item_id" id="item_id" value="<?=(!empty($dataRow->item_id))?$dataRow->item_id:""?>" />
                                <input type="hidden" name="trans_type" value="0" />
                                <input type="hidden" name="sampling_qty" id="sampling_qty" value="<?=$sample_size?>" id="sampling_qty">
                                <div class="row">
									<div class="col-md-3 form-group">
                                        <label for="grn_id"> GRN No : <?=(!empty($dataRow->grn_no))? getPrefixNumber($dataRow->grn_prefix,$dataRow->grn_no):"";?></label> 
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label for="grn_id">
                                            Item Name : <?=(!empty($dataRow->item_name))? $dataRow->item_name:"";?>
                                        </label>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label for="grn_id">
                                            Lot Qty : <?=(!empty($dataRow->qty))? $dataRow->qty:"";?>
                                        </label>
                                    </div>
                                    <hr>
                                    <div class="col-md-2 form-group">
                                        <label for="supplier_tc">Supplier TC</label>
                                        <select name="supplier_tc" id="supplier_tc" class="form-control">
                                            <option value="0" <?=(!empty($inInspectData) && $inInspectData->supplier_tc==0)?'selected':''?>>No</option>
                                            <option value="1" <?=(!empty($inInspectData) && $inInspectData->supplier_tc==1)?'selected':''?>>Yes</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2 form-group">
                                        <label for="sdr">SDR</label>
                                        <select name="sdr" id="sdr" class="form-control">
                                        <option value="0" <?=(!empty($inInspectData) && $inInspectData->sdr==0)?'selected':''?>>No</option>
                                            <option value="1" <?=(!empty($inInspectData) && $inInspectData->sdr==1)?'selected':''?>>Yes</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2 form-group">
                                        <label for="mill_tc">Mill TC</label>
                                        <select name="mill_tc" id="mill_tc" class="form-control">
                                            <option value="0" <?=(!empty($inInspectData) && $inInspectData->mill_tc==0)?'selected':''?>>No</option>
                                            <option value="1" <?=(!empty($inInspectData) && $inInspectData->mill_tc==1)?'selected':''?>>Yes</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label for="remark">Remark</label>
                                        <input type="text" name="remark" id="remark" class="form-control" value="<?=(!empty($inInspectData->remark))?$inInspectData->remark:''?>">
                                    </div>
                                    <div class="col-md-2 form-group">
                                        <label for="fg_item_id">Finish Good</label>
                                        <select name="fg_item_id" id="fg_item_id" class="form-control single-select">
                                            <option value="">Select FG</option>
                                            <?php
                                                if(!empty($fgList)){
                                                    foreach($fgList as $row){
                                                        $selected = (!empty($inInspectData->fg_item_id) && $inInspectData->fg_item_id==$row->item_id)?'selected':'';
                                                        echo '<option value="'.$row->item_id.'" '.$selected.'>'.$row->item_code.'</option>';
                                                    }
                                                }else{
                                                    
                                                    //NPD Product
                                                    
                                                    $selected = (!empty($inInspectData->fg_item_id) && $inInspectData->fg_item_id==11324)?'selected':'';
                                                    echo '<option value="11324" '.$selected.'>NPD</option>';
                                                }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="col-md-3 form-group">
                                        <label for="pfc_rev_no">PFC Revision</label>
                                        <select name="pfc_rev_no" id="pfc_rev_no" class="form-control single-select req" autocomplete="off">
                                            <option value="">Select Revision</option>
                                            <?php
                                            if (!empty($pfcRevList)) :
                                                foreach($pfcRevList as $row){
                                                    $selected = (!empty($inInspectData->pfc_rev_no) && $inInspectData->pfc_rev_no == $row->rev_no)?'selected':'';
                                                    ?>
                                                    <option value="<?=$row->rev_no?>" <?=$selected?>><?=$row->rev_no?></option>
                                                    <?php
                                                }
                                            endif;
                                            ?>
                                        </select>
                                        <div class="error pfc_rev_no"></div>
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
                                        
                                        <input type="hidden" name="sample_size" value="<?=$sample_size?>">
										<table id="preDispatchtbl" class="table table-bordered generalTable">
											<thead class="thead-info" id="theadData">
												<tr style="text-align:center;">
													<th rowspan="2" style="width:5%;">#</th>
                                                    <th rowspan="2">Product/Process Char.</th>
                                                    <th rowspan="2">Specification</th>
                                                    <th rowspan="2">Measurement Tech.</th>
													<th colspan="<?=$sample_size?>">Observation on Samples</th>
													<th rowspan="2">Status</th>
                                                </tr>
                                                <tr style="text-align:center;">
                                                <?php
                                                for($c=0;$c<$sample_size;$c++):
                                                   ?>
                                                   <th><?=$c+1?></th>
                                                   <?php
                                                endfor;
                                                ?>
													
                                                </tr>
                                            </thead>
                                            <tbody id="tbodyData">
                                                <?php
                                                    $tbodyData="";$i=1; 
                                                   
                                                    if(!empty($paramData)):
                                                        foreach($paramData as $row):
                                                            $obj = New StdClass;
                                                            $cls="";
                                                            if(!empty($row->lower_limit) OR !empty($row->upper_limit)):
                                                                $cls="floatOnly";
                                                            endif;
                                                            $diamention ='';
                                                            if($row->requirement==1){ $diamention = $row->min_req.'/'.$row->max_req ; }
                                                            if($row->requirement==2){ $diamention = $row->min_req.' '.$row->other_req ; }
                                                            if($row->requirement==3){ $diamention = $row->max_req.' '.$row->other_req ; }
                                                            if($row->requirement==4){ $diamention = $row->other_req ; }
                                                            if(!empty($inInspectData)):
                                                                $obj = json_decode($inInspectData->observation_sample); 
                                                            endif;
                                                            $inspOption = '';
                                                            $inspOption  = '<option value="Ok" '.((!empty($obj->{$row->id}) && ($obj->{$row->id}[$sample_size]=='Ok'))?'selected':'').' >Ok</option>
				                                            				<option value="Not Ok" '.((!empty($obj->{$row->id}) && ($obj->{$row->id}[$sample_size]=='Not Ok'))?'selected':'').'>Not Ok</option>';
				
                                                            $tbodyData.= '<tr>
                                                                        <td style="text-align:center;">'.$i++.'</td>
                                                                        <td>' . $row->product_param . '</td>
                                                                        <td>' . $diamention . '</td>
                                                                        <td>' . $row->iir_measur_tech . '</td>';
                                                            for($c=0;$c<$sample_size;$c++):
                                                                if(!empty($obj->{$row->id})):
                                                                    $tbodyData .= '<td><input type="text" name="sample'.($c+1).'_'.$row->id.'" id="sample'.($c+1).'_'.$i.'" class="form-control text-center parameter_limit'.$cls.'" value="'.$obj->{$row->id}[$c].'" data-min="'.$row->min_req.'" data-max="' . $row->max_req . '" data-requirement="' . $row->requirement . '" data-row_id ="'.$i.'" ></td>';
                                                                else:
                                                                    $tbodyData .= '<td><input type="text" name="sample'.($c+1).'_'.$row->id.'" id="sample'.($c+1).'_'.$i.'" class="form-control text-center parameter_limit'.$cls.'" value=""  data-min="'.$row->min_req.'" data-max="' . $row->max_req . '" data-requirement="' . $row->requirement . '" data-row_id ="'.$i.'"></td>';
                                                                endif;
                                                            endfor;
                                                            if(!empty($obj->{$row->id})):
                                                                $tbodyData .= '<td style="width:100px;"><select name="status_'.$row->id.'" id="status_'.$i.'" class="form-control  text-center" value="'.$obj->{$row->id}[$sample_size].'">'.$inspOption.'</select></td></tr>';
                                                            else:
                                                                $tbodyData .= '<td style="width:100px;"><select name="status_'.$row->id.'" id="status_'.$i.'" class="form-control  text-center" value="">'.$inspOption.'</select></td></tr>';
                                                            endif;
                                                        endforeach;
                                                    else:
                                                        $tbodyData .='<tr><th colspan="'.(5+$sample_size).'">No data available.</th></tr>';
                                                    endif;
                                                    echo $tbodyData;
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
                            <button type="button" class="btn waves-effect waves-light btn-outline-success float-right save-form" onclick="saveInInspection('InInspection','saveInInspection');" ><i class="fa fa-check"></i> Save</button>
                            <a href="<?=base_url('grn/rmIqc')?>" class="btn waves-effect waves-light btn-outline-secondary float-right save-form" style="margin-right:10px;"><i class="fa fa-times"></i> Cancel</a>
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
    $(document).on('change','#pfc_rev_no',function(e){
        var item_id = $('#fg_item_id').val();
        var pfc_rev_no = $('#pfc_rev_no').val();
        var grn_trans_id = $('#grn_trans_id').val();
        var sample_size = $('#sampling_qty').val();
        $("#tbodyData").html("");
        $("#theadData").html("");
        if(item_id && pfc_rev_no)
        {
            $.ajax({
                url: base_url + controller + '/inInspectionData',
                data: {item_id:item_id,pfc_rev_no:pfc_rev_no,grn_trans_id:grn_trans_id,sample_size:sample_size},
                type: "POST",
                dataType:'json',
                success:function(data){
                    $("#tbodyData").html(data.tbodyData);
                    $("#theadData").html(data.theadData);
                    $("#sampling_qty").html(data.sample_size);
                }
            });
        }
    });

    $(document).on('change','#fg_item_id',function(e){
        var item_id = $('#fg_item_id').val();
        $("#tbodyData").html("");
        if(item_id)
        {
            $.ajax({
                url: base_url + controller + '/getRevisionList',
                data: {item_id:item_id},
                type: "POST",
                dataType:'json',
                success:function(data){
                    $("#pfc_rev_no").html(data.options);
                    $("#pfc_rev_no").comboSelect();
                }
            });
        }
    });
	
	$(document).on('keyup change','.parameter_limit',function(){
        var requirement = $(this).data('requirement');
        var min = $(this).data('min');
        var max = $(this).data('max');
        var row_id = $(this).data('row_id');
        var parameter_value = $(this).val();       
        var sampling_qty = $("#sampling_qty").val();
		var valid = true;
        console.log(sampling_qty);
		for(var i=1;i<=sampling_qty;i++)
		{
			var sample_value = $("#sample"+i+"_"+row_id).val();
			var ele = $("#sample"+i+"_"+row_id);
			if(parseFloat(requirement) == 1){
				if(parseFloat(max) >= parseFloat(sample_value) && parseFloat(min) <= parseFloat(sample_value)){
					ele.removeClass('bg-danger');
				}else{
					if(parseFloat(sample_value) > 0){ele.addClass('bg-danger');}else{ele.removeClass('bg-danger');}
					valid=false;  
				}					
			}
			if(parseFloat(requirement) == 2){
				if(parseFloat(min) <= parseFloat(sample_value)){
					ele.removeClass('bg-danger');
				}else{
					if(parseFloat(sample_value) > 0){ele.addClass('bg-danger');}else{ele.removeClass('bg-danger');}
					valid=false; 						
				}
			}

			if(parseFloat(requirement) == 3){
				if(parseFloat(max) >= parseFloat(sample_value)){
					ele.removeClass('bg-danger');
				}else{
					if(parseFloat(sample_value) > 0){ele.addClass('bg-danger');}else{ele.removeClass('bg-danger');}
					valid=false;
				}
			}
			if(valid){$("#status_"+row_id).val("Ok");}else{$("#status_"+row_id).val("Not Ok");}	
		}

    });
    
    /* $(document).on('keyup change','.parameter_limit',function(){
        var requirement = $(this).data('requirement');
        var min = $(this).data('min');
        var max = $(this).data('max');
        var row_id = $(this).data('row_id');
        var parameter_value = $(this).val();
        
        if(parseFloat(requirement) == 1){
            if(parseFloat(max) >= parseFloat(parameter_value) && parseFloat(min) <= parseFloat(parameter_value)){
                $(this).removeClass('bg-danger');
                $("#result_"+row_id).val("Ok");
            }else{
                if(parseFloat(parameter_value) > 0){
                    $(this).addClass('bg-danger');
					$("#result_"+row_id).val("Not Ok");
                }else{
                    $(this).removeClass('bg-danger');
					$("#result_"+row_id).val("Ok");
                }            
            }
        }

        if(parseFloat(requirement) == 2){
            if(parseFloat(min) <= parseFloat(parameter_value)){
                $(this).removeClass('bg-danger');
                $("#result_"+row_id).val("Ok");
            }else{
                if(parseFloat(parameter_value) > 0){
                    $(this).addClass('bg-danger');
                    $("#result_"+row_id).val("Not Ok");
                }else{
                    $(this).removeClass('bg-danger');
                }            
            }
        }

        if(parseFloat(requirement) == 3){
            if(parseFloat(max) >= parseFloat(parameter_value)){
                $(this).removeClass('bg-danger');
                $("#result_"+row_id).val("Ok");
            }else{
                if(parseFloat(parameter_value) > 0){
                    $(this).addClass('bg-danger');
                    $("#result_"+row_id).val("Not Ok");
                }else{
                    $(this).removeClass('bg-danger');
                $("#result_"+row_id).val("Ok");
                }            
            }
        }
    }); */
});

function preDispatchtbl()
{
	var preDispatchtbl = $('#preDispatchtbl').DataTable( 
	{
		responsive: true,
		scrollY: '55vh',
        scrollCollapse: true,
		"scrollX": true,
		"scrollCollapse":true,
		//'stateSave':true,
		"autoWidth" : false,
		order:[],
		"columnDefs": 	[
							{ type: 'natural', targets: 0 },
							{ orderable: false, targets: "_all" }, 
							{ className: "text-left", targets: [0,1] }, 
							{ className: "text-center", "targets": "_all" } 
						],
		pageLength:25,
		language: { search: "" },
		lengthMenu: [
            [ 10, 25, 50, 100, -1 ],[ '10 rows', '25 rows', '50 rows', '100 rows', 'Show all' ]
        ],
		dom: "<'row'<'col-sm-7'B><'col-sm-5'f>>" +"<'row'<'col-sm-12't>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
		buttons: [ 'pageLength', 'excel', {text: 'Refresh',action: function ( e, dt, node, config ) {loadAttendanceSheet();}}]
	});
	preDispatchtbl.buttons().container().appendTo( '#preDispatchtbl_wrapper toolbar' );
	$('.dataTables_filter .form-control-sm').css("width","97%");
	$('.dataTables_filter .form-control-sm').attr("placeholder","Search.....");
	$('.dataTables_filter').css("text-align","left");
	$('.dataTables_filter label').css("display","block");
	$('.btn-group>.btn:first-child').css("border-top-right-radius","0");
	$('.btn-group>.btn:first-child').css("border-bottom-right-radius","0");
	return preDispatchtbl;
}

function saveInInspection(formId,fnsave){
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
            location.href = base_url + controller+'/rmIqc';
        }else{
			initTable();  $('#'+formId)[0].reset();$(".modal").modal('hide');   
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
            location.href = base_url + controller;
        }
	});
}

</script>