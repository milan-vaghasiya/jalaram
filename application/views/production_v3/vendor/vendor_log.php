<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
					<div class="card-header">
                        <div class="row">
							<div class="col-md-6 form-group">
								<ul class="nav nav-pills">
                                   <li class="nav-item"> <button onclick="statusTab('jobWorkTable',0);" class=" btn waves-effect waves-light btn-outline-info <?=(empty($status)?'active':'')?> mr-1" style="outline:0px" data-toggle="tab" aria-expanded="false " data-status="0">Pending Accept</button> </li>

                                    <li class="nav-item"> <button onclick="statusTab('jobWorkTable',1);" class=" btn waves-effect waves-light btn-outline-info  mr-1" style="outline:0px" data-toggle="tab" aria-expanded="false " data-status="0">In process</button> </li>
                                    
                                    <li class="nav-item"> <button onclick="statusTab('jobWorkTable',2);" class=" btn waves-effect waves-light btn-outline-info" style="outline:0px" data-toggle="tab" aria-expanded="false" data-status="1">Completed</button> </li>
								</ul>
							</div>
							<div class="col-md-2 form-group">
                                <h4 class="card-title text-left">Outsource</h4>
                            </div> 
                        </div>     
						                
                    </div>
                    <div class="card-body">
                        <input type="hidden" id="process_id" value="">
                        <div class="table-responsive">
                            <table id='jobWorkTable' class="table table-bordered ssTable" data-url='/getDTRows/'></table></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script>
    function changeChallanStatus(postdata){
        var send_data = { id:postdata.id,trans_status:postdata.trans_status };
        $.confirm({
            title: 'Confirm!',
            content: postdata.msg,
            type: 'red',
            buttons: {   
                ok: {
                    text: "ok!",
                    btnClass: 'btn waves-effect waves-light btn-outline-success',
                    keys: ['enter'],
                    action: function(){
                        $.ajax({
                            url: base_url + controller + '/changeChallanStatus',
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
</script>

