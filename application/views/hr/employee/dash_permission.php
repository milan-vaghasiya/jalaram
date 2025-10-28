<?php $this->load->view('includes/header'); ?>
<form id="empPermission">
    <div class="page-wrapper">
        <div class="container-fluid bg-container">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <ul class="nav nav-pills">
                                        <a href="<?= base_url($headData->controller . "/empPermission/") ?>" class="btn waves-effect waves-light btn-outline-primary  permission-write"> General Permission</a>
                                        <a href="<?= base_url($headData->controller . "/empPermissionReport/") ?>" class="btn waves-effect waves-light btn-outline-warning permission-write"> Report Permission</a>
                                        <a href="<?= base_url($headData->controller . "/dashPermission/") ?>" class="btn waves-effect waves-light btn-outline-info permission-write active"> Dashboard Permission</a>
                                        <a href="<?= base_url($headData->controller . "/appPermission/") ?>" class="btn waves-effect waves-light btn-outline-dark permission-write"> App Permission</a>
                                        <button type="button" class="btn waves-effect waves-light btn-outline-success float-center copyPermission permission-write" data-button="save" data-modal_id="modal-md" data-function="copyPermission" data-form_title="Copy Permission">Copy Permission</button>
                                    </ul>                                    
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-8">
                                    <h4 class="card-title pageHeader">Employee Permission</h4>
                                </div>                                
                                <div class="col-md-4">
                                    <select name="emp_id" id="emp_id" class="form-control single-select">
                                        <option value="">Select Employee</option>
                                        <option value="1">Admin</option>
                                        <?php
                                        foreach ($empList as $row) :
                                            echo '<option value="' . $row->id . '">[' . $row->emp_code . '] ' . $row->emp_name . '</option>';
                                        endforeach;
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="card-body reportDiv" style="min-height:75vh">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="thead-info">
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th class="text-center">Menu/Page Name</th>
                                            
                                        </tr>
                                    </thead>
                                    <tbody >
                                        <tr>
                                            <?php
                                            if(!empty($dashPermisson)):
                                                $i=1;
                                                foreach($dashPermisson as $row):
                                                        echo '<tr>';
                                                        echo '<td class="text-center">
                                                                    <input type="checkbox" id="is_read'.$row->id.'" name="is_read_'.$row->id.'" class="filled-in chk-col-success" value="1" ><label for="is_read'.$row->id.'" class="mr-3"></label>
                                                                    <input type="hidden" name="widget_id[]" id="widget_id' . $row->id . '" value="' . $row->id . '">

                                                                </td>';
                                                        echo '  <td class="text-center">'.$row->widget_name.'</td>';
                                                        echo ' </tr>';
                                                endforeach;
                                            endif;
                                            ?>                                              
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

</form>

<div class="bottomBtn bottom-25 right-25 permission-write">
    <button type="button" class="btn btn-primary btn-rounded font-bold permission-write save-form" style="letter-spacing:1px;" onclick="saveDashPermission('empPermission');">SAVE PERMISSION</button>
</div>

<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url();?>assets/js/custom/emp-permission.js?v=<?=time()?>"></script>

<script>
    $(document).ready(function() {
        $('.collapse.in').prev('.panel-heading').addClass('active');
        $('#bs-collapse').on('show.bs.collapse', function(a) {
            $(a.target).prev('.panel-heading').addClass('active');
        }).on('hide.bs.collapse', function(a) {
            $(a.target).prev('.panel-heading').removeClass('active');
        });
        
        $(document).on('change',"#emp_id",function(){
            var emp_id = $(this).val();
            $("#empPermission")[0].reset();
            $(".error").html("");
            $(this).val(emp_id);
            $(this).comboSelect();
            $(".chk-col-success").removeAttr("checked");
            
            $.ajax({
                type: "POST",   
                url: base_url + controller + '/editDashPermission',   
                data: {emp_id:emp_id},
                dataType:"json"
            }).done(function(response){
                var permission = response.empPermission;
                if(permission.length > 0){
                    $.each(response.empPermission,function(key, value) {
                        $("#"+value).attr("checked","checked");
                    }); 
                }
            });
        });

        $(document).on('click', '.checkAll', function() {
			
            if ($(this).prop('checked') == true) {
                $("input[name='is_read[]']").prop('checked', true);
            } else {
                $("input[name='is_read[]']").prop('checked', false);
            }
		});
		

    });

function saveDashPermission(formId){
	var fd = $('#'+formId)[0];
    var formData = new FormData(fd);
    
	$.ajax({
		url: base_url + controller + '/saveDashPermission',
		data:formData,
        processData: false,
        contentType: false,
		type: "POST",
		dataType:"json",
	}).done(function(data){
		if(data.status===0){
			$(".error").html("");
			$.each( data.message, function( key, value ) {
				$("."+key).html(value);
			});
		}else if(data.status==1){
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
            $("#empPermission")[0].reset();
            $(".chk-col-success").removeAttr("checked");
		}else{
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}				
	});
}
</script>