<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-3">
                                <h4 class="card-title">PFC : <small><?=' ['.$itemData->item_code.'] '.'</small>'?></h4>
                            </div>
                          
                            
                            <div class="input-group justify-content-end col-md-9">
                                <?php
                                if(empty($pfcData)){
                                    ?>
                                        <a href="javascript:void(0)" class="btn btn-outline-primary waves-effect waves-light float-right" data-toggle="modal"  data-target="#uploadModal" >Upload PFC</a>
                                    <?php
                                }else{
                                    ?>
                                    <a href="<?= base_url($headData->controller . "/linkPfcRevision/" . $item_id) ?>" class="btn btn-outline-primary waves-effect waves-light float-right" ><i class=" fas fa-link "></i> Link Revision</a>
                                    <?php
                                }
                                ?>
                                
                            </div>
                               
                            
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='pfcTable' class="table table-bordered ssTable" data-url='/getPFCDTRows/<?= $item_id ?>'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="uploadModal" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog" style="min-width:30%;">
		<div class="modal-content animated zoomIn border-light">
			<div class="modal-header bg-light">
				<h5 class="modal-title text-dark"><i class="fa fa-upload"></i> Upload</h5>
				<button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form id="printModel" method="post" action="<?=base_url($headData->controller.'/jobworkOutChallan')?>" target="_blank">
				<div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 form-group justify-content-end">
                            <a href="<?= base_url($headData->controller . '/createExcelPFC/'. $item_id) ?>" class="btn  btn-info float-right" target="_blank"><i class="fa fa-download"></i> <span class="btn-label"> PFC Excel <i class="fa fa-file-excel"></i></span></a>
                        </div>
                        <div class="col-md-12 form-group">
                            <label for="rev_no">Revision No.</label>
                            <select name="rev_no" id="rev_no" class="form-control single-select">
                                <option value="">Select Revision</option>
                                <?php
                                if(!empty($revList)){
                                    foreach($revList as $row){
                                        ?>
                                        <option value="<?=$row->rev_no?>"><?=$row->rev_no?></option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="input-group col-md-12">
                            <input type="hidden" id="item_id" value="<?=$item_id?>">
                            <input type="file" id="pfc_excel" name="pfc_excel" class="form-control-file  " style="width:70%" />
                            <a href="javascript:void(0);" class="btn  btn-success  ml-0" type="button" style="width:30%"><i class="fa fa-upload"></i>&nbsp;<span class="btn-label" onclick="uploadExc();" >Upload PFC &nbsp;<i class="fa fa-file-excel"></i></span></a>
                        </div>
                    </div>
				</div>
				<div class="modal-footer">
					<a href="#" data-dismiss="modal" class="btn btn-secondary"><i class="fa fa-times"></i> Close</a>
				</div>
			</form>
		</div>
	</div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script>
    function trashPfc(id, name = 'Record') {
        var send_data = {
            id: id
        };
        $.confirm({
            title: 'Confirm!',
            content: 'Are you sure want to Remove this Record? <br> All related records will be removed and will not be recovered',
            type: 'red',
            buttons: {
                ok: {
                    text: "ok!",
                    btnClass: 'btn waves-effect waves-light btn-outline-success',
                    keys: ['enter'],
                    action: function() {
                        $.ajax({
                            url: base_url + controller + '/deletePfc',
                            data: send_data,
                            type: "POST",
                            dataType: "json",
                            success: function(data) {
                                if (data.status == 0) {
                                    toastr.error(data.message, 'Sorry...!', {
                                        "showMethod": "slideDown",
                                        "hideMethod": "slideUp",
                                        "closeButton": true,
                                        positionClass: 'toastr toast-bottom-center',
                                        containerId: 'toast-bottom-center',
                                        "progressBar": true
                                    });
                                } else {
                                    initTable(0);
                                    toastr.success(data.message, 'Success', {
                                        "showMethod": "slideDown",
                                        "hideMethod": "slideUp",
                                        "closeButton": true,
                                        positionClass: 'toastr toast-bottom-center',
                                        containerId: 'toast-bottom-center',
                                        "progressBar": true
                                    });
                                    $("#inspectionBody").html(data.tbodyData);
                                }
                            }
                        });
                    }
                },
                cancel: {
                    btnClass: 'btn waves-effect waves-light btn-outline-secondary',
                    action: function() {

                    }
                }
            }
        });
    }

    function uploadExc() {
        setPlaceHolder();
        var fd = new FormData();
        fd.append("pfc_excel", $("#pfc_excel")[0].files[0]);
        fd.append("item_id", $('#item_id').val());
        fd.append("rev_no", $('#rev_no').val());
        $.ajax({
            url: base_url + controller + '/importExcelPFC',
            data: fd,
            type: "POST",
            processData: false,
            contentType: false,
            dataType: "json",
        }).done(function(data) {
            $("#pfc_excel").val("");
            $("#rev_no").val("");
            $("#rev_no").comboSelect();
            if (data.status === 0) {
                $(".error").html("");
                $.each(data.message, function(key, value) {
                    $("." + key).html(value);
                });
            } else if (data.status == 1) {
                initTable(1);
                toastr.success(data.message, 'Success', {
                    "showMethod": "slideDown",
                    "hideMethod": "slideUp",
                    "closeButton": true,
                    positionClass: 'toastr toast-bottom-center',
                    containerId: 'toast-bottom-center',
                    "progressBar": true
                });
                $("#uploadModal").modal('hide');
            } else {
                initTable(1);
                toastr.error(data.message, 'Error', {
                    "showMethod": "slideDown",
                    "hideMethod": "slideUp",
                    "closeButton": true,
                    positionClass: 'toastr toast-bottom-center',
                    containerId: 'toast-bottom-center',
                    "progressBar": true
                });
            }

        });
    }
</script>