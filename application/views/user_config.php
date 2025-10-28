<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title">User Configuration</h4>
                            </div>
                            <div class="col-md-6">
                                <!-- <button type="button" class="btn waves-effect waves-light btn-outline-success btn-save float-right save-form permission-write" onclick="store('userConfigForm','save');"><i class="fa fa-check"></i> Save</button> -->
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="userConfigForm">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-4 form-group">
                                        <label for="access_type">Access Type</label>
                                        <select name="access_type" id="access_type" class="form-conrtrol single-select">
                                            <option value="">Select Access Type</option>
                                            <option value="1">Only Factory Premises</option>
                                            <option value="2">2 Factor Authentication</option>
                                            <option value="3">All Access</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label for="emp_id">Employee Name</label>
                                        <select id="emp_id" data-input_id="empIds" class="form-control jp_multiselect req" multiple="multiple">
                                            <?php
                                                foreach ($empList as $row) :
                                                    echo '<option value="' . $row->id . '" >[ '.$row->emp_code.' ] '.$row->emp_name.'</option>';
                                                endforeach;
                                            ?>
                                        </select>
                                        <input type="hidden" name="emp_id" id="empIds" value="">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <button type="button" class="btn waves-effect waves-light btn-outline-success btn-save float-right save-form permission-write" onclick="store('userConfigForm','save');"><i class="fa fa-check"></i> Save</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title">Login Configuration</h4>
                            </div>
                            <div class="col-md-6">
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="loginConfigForm">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12 form-group error">
                                        Note : If you want to bypass static ip address then remove value from input and input your company start time and end time.
                                    </div>

                                    <div class="col-md-4 form-group">
                                        <label for="static_ip">Static IP Address <?=$this->config->item('STATIC_IP')?></label>
                                        <input type="text" name="static_ip" id="static_ip" class="form-control" value="<?=(!empty($companyInfo->static_ip))?$companyInfo->static_ip:""?>">
                                    </div>

                                    <div class="col-md-4 form-group">
                                        <label for="login_start_time">Company Start Time</label>
                                        <input type="time" name="login_start_time" id="login_start_time" class="form-control" value="<?=(!empty($companyInfo->login_start_time))?$companyInfo->login_start_time:""?>">
                                    </div>

                                    <div class="col-md-4 form-group">
                                        <label for="login_end_time">Company End Time</label>
                                        <input type="time" name="login_end_time" id="login_end_time" class="form-control" value="<?=(!empty($companyInfo->login_end_time))?$companyInfo->login_end_time:""?>">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <button type="button" class="btn waves-effect waves-light btn-outline-success btn-save float-right save-form permission-write" onclick="storeLoginConfig('loginConfigForm','saveLoginConfig');"><i class="fa fa-check"></i> Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>

<script>
$(document).ready(function(){
    $(document).on('change','#access_type',function(){
        var access_type = $(this).val();
        if(access_type != ""){
            $.ajax({
                url : base_url + controller + '/getLoginConfig',
                type : 'post',
                data : {access_type:access_type},
                dataType : 'json',
                success : function(response){
                    $("#emp_id").html(response.empOptions);
                    $("#empIds").val(response.empIds);
                    reInitMultiSelect();
                }
            });
        }else{
            $("#emp_id").html("");
            $("#empIds").val("");
            reInitMultiSelect();
        }
    });
});

function storeLoginConfig(formId,fnsave){
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
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}else{			
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}
				
	});
}
</script>