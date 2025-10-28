<form>
    <input type="hidden" name="cost_id" id="cost_id" value="<?=$dataRow->id?>">
    <div class="col-md-12 form-group">
        <div class="row">
            <div class="col-md-6 form-group">
                <label for="process_id">Process</label>
                <select name="process_id" id="process_id" name="process_id" class="form-control single-select">
                    <option value="">Select Process</option>
                    <?php
                    if(!empty($processList)){
                        foreach($processList AS $row){
                            ?><option value="<?=$row->id?>" data-is_machining="<?=$row->is_machining?>"><?=$row->process_name?></option><?php
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-4 form-group ctDiv">
                <label for="cycle_time">Cycle Time(Sec)</label>
                <input type="text" id="cycle_time" name="cycle_time" class="form-control floatOnly">
            </div>
            <div class="col-md-4 form-group processCostDiv">
                <label for="process_cost">Process Cost</label>
                <input type="text" id="process_cost" name="process_cost" class="form-control floatOnly">
            </div>
            <div class="col-md-2 form-group">
            <?php
                $param = "{'formId':'addMfgCost','fnsave':'saveMfgCost'}";
            ?>
            <button type="button" class="btn waves-effect waves-light btn-success btn-save save-form float-right btn-block mt-30" onclick="storeProcess(<?=$param?>)" style="height:35px"><i class="fa fa-check"></i> Save</button>
            </div>
        </div>
    </div>
</form>
<div class="col-md-12">
    <div class="row">
        <h5 style="width:100%;margin:0 auto;vertical-align:middle;border-top:1px solid #ccc;padding:5px 0px;">Process Transaction :</h5>
        <div class="table-responsive  mb-3">
            <table id='logTransTable' class="table table-bordered jpExcelTable mb-5">
                <thead class="text-center thead-info">
                    <tr>
                        <th style="min-width:20px">#</th>
                        <th>Process</th>
                        <th>Cycle Time</th>
                        <th>Cost</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="logTbodyData">
                   
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    var tbodyData = false;
    $(document).ready(function(){
        setTimeout(function(){ $('#process_id').trigger('change'); }, 50);

        if(!tbodyData){
             var postData = {'postData':{'cost_id':$("#cost_id").val()},'table_id':"logTransTable",'tbody_id':'logTbodyData','tfoot_id':'','fnget':'getProcessHtml'};
            getProcessHtml(postData);
            tbodyData = true;
        }
        $(document).on("change","#process_id", function(e){
            e.stopImmediatePropagation();e.preventDefault();
            var is_machining = $("#process_id").find(":selected").data('is_machining');
            
            if(is_machining == 'Yes'){
                $(".ctDiv").show();
                $(".processCostDiv").hide();
            }else{
                $(".ctDiv").hide();
                $(".processCostDiv").show();
            }
        });
    });

    function storeProcess(postData){
        setPlaceHolder();
        
        var formId = postData.formId;
        var fnsave = postData.fnsave || "save";
        var controllerName = postData.controller || controller;
        var resFunctionName =$("#"+formId).data('res_function') || "";
        
        var form = $('#'+formId)[0];
        var fd = new FormData(form);
        $.ajax({
            url: base_url + controllerName + '/' + fnsave,
            data:fd,
            type: "POST",
            processData:false,
            contentType:false,
            dataType:"json",
        }).done(function(data){
            if(data.status==1){
                var postData = {'postData':{'cost_id':$("#cost_id").val()},'table_id':"logTransTable",'tbody_id':'logTbodyData','tfoot_id':'','fnget':'getProcessHtml'};
                getProcessHtml(postData);

                $('#'+formId)[0].reset(); 

            }else{
                $(".error").html("");
                $.each( data.message, function( key, value ) {$("."+key).html(value);});		
            }				
        });
    }

    function trashMfgCost(postdata){
        var send_data = { id:postdata.id,cost_id:postdata.cost_id };
        $.confirm({
            title: 'Confirm!',
            content: 'Are you sure want to delete this record?',
            type: 'red',
            buttons: {   
                ok: {
                    text: "ok!",
                    btnClass: 'btn waves-effect waves-light btn-outline-success',
                    keys: ['enter'],
                    action: function(){
                        $.ajax({
                            url: base_url + controller + '/deleteMfgCost',
                            data: send_data,
                            type: "POST",
                            dataType:"json",
                            success:function(data)
                            {
                                if(data.status==0)
                                {
                                    toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
                                }
                                else
                                {
                                    var postData = {'postData':{'cost_id':$("#cost_id").val()},'table_id':"logTransTable",'tbody_id':'logTbodyData','tfoot_id':'','fnget':'getProcessHtml'};
                                    getProcessHtml(postData);
                                    initTable(); initMultiSelect();
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

    function getProcessHtml(data){
        var postData = data.postData || {};
        var fnget = data.fnget || "";
        var controllerName = data.controller || controller;

        var table_id = data.table_id || "";
        var thead_id = data.thead_id || "";
        var tbody_id = data.tbody_id || "";
        var tfoot_id = data.tfoot_id || "";	

        if(thead_id != ""){
            $("#"+table_id+" #"+thead_id).html(data.thead);
        }
        
        $.ajax({
            url: base_url + controllerName + '/' + fnget,
            data:postData,
            type: "POST",
            dataType:"json",
            beforeSend: function() {
                if(table_id != ""){
                    var columnCount = $('#'+table_id+' thead tr').first().children().length;
                    $("#"+table_id+" #"+tbody_id).html('<tr><td colspan="'+columnCount+'" class="text-center">Loading...</td></tr>');
                }
            },
        }).done(function(res){
            $("#"+table_id+" #"+tbody_id).html('');
                $("#"+table_id+" #"+tbody_id).html(res.tbodyData);
                
                initTable();
                if(tfoot_id != ""){
                    $("#"+table_id+" #"+tfoot_id).html('');
                    $("#"+table_id+" #"+tfoot_id).html(res.tfootData);
                }
        });
    }
</script>