<style>
.ui-sortable-handle{cursor: move;}
.ui-sortable-handle:hover{background-color: #daeafa;border-color: #9fc9f3;cursor: move;}
</style>
<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id">
            <div class="col-md-2 form-group">
                <label for="pfc_rev_no">PFC Revision</label>
                <select name="pfc_rev_no" id="pfc_rev_no" class="form-control single-select req">
                    <option value="">Select PFC Revision</option>
                    <?php
                    foreach ($revData as $row) :
                        echo '<option value="' . $row->rev_no . '">' . $row->rev_no . '</option>';
                    endforeach;
                    ?>
                </select>
                <div class="error pfc_rev_no"></div>
            </div>
            <div class="col-md-4 form-group">
                <label for="process_id">Production Process</label>
                <select name="process_id" id="process_id" class="form-control single-select req">
                    <option value="">Select Production Process</option>
                    <?php
                    // foreach ($processData as $row) :
                    //     if(!in_array($row->id, array_column($prodProcessData, 'process_id'))){
                    //         echo '<option value="' . $row->id . '">' . $row->process_name . '</option>';
                    //     }
                    // endforeach;
                    ?>
                </select>
                <input type="hidden" name="item_id" id="item_id" value="<?=$item_id?>" />
            </div>           
            <div class="col-md-4 form-group">
                <label for="pfc_process">PFC Process</label>
                <select name="pfcSelect" id="pfcSelect" data-input_id="pfc_process" class="form-control jp_multiselect req" multiple="multiple">

                </select>
                <input type="hidden" name="pfc_process" id="pfc_process" value="" />
                <div class="error pfcSelect"></div>
            </div>          
            <div class="col-md-2 form-group">
                <label>&nbsp;</label>
                <button type="button" class="btn btn-success waves-effect add-process btn-block save-form" onclick="saveProdProcess()"><i class="fa fa-plus"></i> Add</a>
            </div>
        </div>
    </div>
</form>
<div class="col-md-12">
    <div class="row">
        <table id="prodProcessThead" class="table excel_table table-bordered">
            <thead class="thead-info">
                <tr>
                <th style="width:5%;text-align:center;">#</th>
                    <th style="width:15%;">PFC Revision</th>
                    <!-- <th style="width:20%;">Process Name</th>
                    <th style="width:50%;">PFC Process</th> -->
                    <th style="width:10%;" class="text-center">Action</th>
                </tr>
            </thead>
            <tbody id="prodProcessTbody">
                <?php echo $prodProcessTbody['resultHtml'] ?>
            </tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function(){
    initMultiSelect();
    $(document).on('change',"#pfc_rev_no",function(){
        var pfc_rev_no = $(this).val();
        var item_id = $("#item_id").val();
        if(pfc_rev_no != ''){
            $.ajax({
                url:base_url + controller + "/getItemWisePfc",
                type:'post',
                data:{pfc_rev_no:pfc_rev_no,item_id:item_id},
                dataType:'json',
                success:function(data){ 
                    $("#process_id").html("");
                    $("#process_id").html(data.pOption);
                    $("#process_id").comboSelect();
                    $("#pfcSelect").html("");
                    $("#pfcSelect").html(data.options);
                    reInitMultiSelect();
                }
            });
        }else{
            $("#process_id").html(""); comboSelect();
        }
    });
});

function saveProdProcess(){
    var pfc_rev_no = $('#pfc_rev_no :selected').val();
    var process_id = $('#process_id :selected').val();
    var pfc_process = $('#pfc_process').val();
    var item_id = $("#item_id").val();

    $(".error").html(""); valid = 1;
	if(pfc_rev_no == ""){$(".pfc_rev_no").html("Process required.");valid = 0;}
	if(process_id == ""){$(".process_id").html("Process required.");valid = 0;}
	if(pfc_process == ""){$(".pfcSelect").html("PFC Process required.");valid = 0;}

    if(valid){
         var id = $("#id").val();
        $.ajax({
            url: base_url + controller + '/saveProdProcess',
            data:{id:id,pfc_rev_no:pfc_rev_no, process_id:process_id, pfc_process:pfc_process,item_id:item_id },
            type: "POST",
            dataType:"json",
            success:function(data){
                if(data.status===0){
                    $(".error").html("");
                    $.each( data.message, function( key, value ) {$("."+key).html(value);});
                }else{
                    $("#pfcSelect").html("");
                    $("#pfcSelect").html(data.pfcOption);
                    reInitMultiSelect();

                    $("#process_id").html("");
                    $('#process_id').html(data.pOption);
                    $("#process_id").comboSelect();

                    $("#pfc_rev_no").val("");
                    $("#pfc_rev_no").comboSelect();

                    $('#prodProcessTbody').html("");
                    $('#prodProcessTbody').html(data.resultHtml);
                    $("#id").val("");
                    $("#pfc_process").val("");
                    toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
                }
            }
        });
    }
}

function trashProdProcess(id,item_id,name='Record'){
	var send_data = { id:id, item_id:item_id };
	$.confirm({
		title: 'Confirm!',
		content: 'Are you sure want to delete this '+name+'?',
		type: 'red',
		buttons: {   
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function(){
					$.ajax({
						url: base_url + controller + '/deleteProdProcess',
						data: send_data,
						type: "POST",
						dataType:"json",
						success:function(data)
						{
							if(data.status==0)
							{
								toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}else{
                                $("#pfcSelect").html("");
                                reInitMultiSelect();

                                $("#process_id").html("");
                                $('#process_id').html(data.pOption);
                                $("#process_id").comboSelect();

                                $("#pfc_rev_no").val("");
                                $("#pfc_rev_no").comboSelect();

								$('#prodProcessTbody').html("");
                                $('#prodProcessTbody').html(data.resultHtml);
								toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}
						}
					});
				}
			},
			cancel: {
                btnClass: 'btn waves-effect waves-light btn-outline-secondary',
                action: function(){

				}
            }
		}
	});
}

function editProdProcess(id,process_id,pfc_rev_no){
    var item_id = $("#item_id").val();
    $.ajax({
        url:base_url + controller + "/editProductProcess",
        type:'post',
        data:{process_id:process_id,id:id,item_id:item_id,pfc_rev_no:pfc_rev_no},
        dataType:'json',
        success:function(data){ 
            $("#pfcSelect").html("");
            $("#pfcSelect").html(data.pfcOptions);

            $("#process_id").html("");
            $('#process_id').html(data.processOptions);

            $("#pfc_process").val(data.pfc_process);
            $("#process_id").comboSelect();
            reInitMultiSelect();

            $("#id").val(id);    
            $("#pfc_rev_no").val(pfc_rev_no);
            $("#pfc_rev_no").comboSelect();
            $("#modal-lg").modal('hide');

        }
    });
        
}
</script>