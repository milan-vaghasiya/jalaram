<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-4">
                                <ul class="nav nav-pills">
                                    <li class="nav-item"> <button onclick="statusTab('visitorsTable',0);" class=" btn waves-effect waves-light btn-outline-info active" style="outline:0px" data-toggle="tab" aria-expanded="false">Pending</button> </li>
                                    <li class="nav-item"> <button onclick="statusTab('visitorsTable',1);" class=" btn waves-effect waves-light btn-outline-success" style="outline:0px" data-toggle="tab" aria-expanded="false">Approved</button> </li>
                                    <li class="nav-item"> <button onclick="statusTab('visitorsTable',2);" class=" btn waves-effect waves-light btn-outline-primary" style="outline:0px" data-toggle="tab" aria-expanded="false">Rejected</button> </li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <h4 class="card-title text-center">Visitors</h4>
                            </div>                            
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='visitorsTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>
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
    $(document).on('click',".approveVisit",function(){
		var id = $(this).data('id');
		var val = $(this).data('val');
        var msg= $(this).data('msg');
		$.confirm({
			title: 'Confirm!',
			content: 'Are you sure want to '+ msg +' this Visit?',
			type: 'green',
			buttons: {   
				ok: {
					text: "ok!",
					btnClass: 'btn waves-effect waves-light btn-outline-success',
					keys: ['enter'],
					action: function(){
						$.ajax({
							url: base_url + controller + '/approveVisit',
							data: {id:id,val:val,msg:msg},
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
								    initTable(); 
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
	});

    $(document).on('click',".rejectVisit",function(){
        var id = $(this).data('id');
		var val = $(this).data('val');
        var msg= $(this).data('msg');
        $.confirm({
            title: 'Confirm!',
            content: '<lable for"reject_reason">Reject Reason</lable><textarea name="reject_reason" id="reject_reason" class="form-control m-input" placeholder="Enter Reject Reason" autocomplete="off"> </textarea> <div class="error action_error"></div>Are you sure want to '+ msg +' this Visit?',
            type: 'red',
            buttons: {   
                ok: {
                    text: "ok!",
                    btnClass: 'btn waves-effect waves-light btn-outline-success',
                    keys: ['enter'],
                    action: function(){
                        var reject_reason=$("#reject_reason").val();
                        var send_data = {id:id,val:val,msg:msg,reject_reason:reject_reason};
                        $.ajax({
							url: base_url + controller + '/approveVisit',
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
								    initTable(); 
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
    });

});
</script>