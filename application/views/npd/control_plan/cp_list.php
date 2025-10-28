<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-5">
                                <h5 class="card-title"><a href="<?= base_url($headData->controller . '/pfcList/' . $pfcData->item_id) ?>">Control Plan :<small> <?=$pfcData->trans_number.' ['.$pfcData->item_code.'] '.'</small>'?></a></h5>
                            </div>
                            <div class="input-group float-left col-md-5">
                                <?php
                                if(!empty($pfcData)){
                                    ?>
                                    <input type="hidden" id="item_id" value="<?= $pfcData->item_id ?>">
                                    <input type="hidden" id="pfc_id" value="<?= $pfcData->id ?>">
                                    <input type="file" id="fmea_excel" name="fmea_excel" class="form-control-file  " style="width:50%" />
                                    <a href="javascript:void(0);" class="btn  btn-success  ml-0" type="button" onclick="uploadExc();"><i class="fa fa-upload"></i>&nbsp;<span class="btn-label" >Upload CP <i class="fa fa-file-excel"></i></span></a>
                                    <a href="<?= base_url($headData->controller . '/createExcelFmea/' . $pfc_id) ?>" class="btn  btn-info  mr-2" target="_blank"><i class="fa fa-download"></i> <span class="btn-label">  CP <i class="fa fa-file-excel"></i></span></a>
                                    <?php
                                }
                                ?>     
                            </div>
                            <div class="input-group justify-content-end col-md-2">
                                <a href="<?= base_url($headData->controller . "/linkCpRevision/" .  $pfc_id) ?>" class="btn btn-outline-primary waves-effect waves-light float-right"><i class=" fas fa-link "></i> Link Revision</a>
                                <!-- <button  style="width:50%" class="btn btn-outline-primary waves-effect waves-light float-right addNew" data-button="close" data-modal_id="modal-lg" data-function="addCPRevision/<?=  $pfcData->item_id ?>" data-form_title="Add New Rev" data-fnsave="saveRevision"><i class="fa fa-plus"></i> New Revision</button> -->
                            </div>
                            
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='fmeaTable' class="table table-bordered ssTable" data-url='/getControlPlanDTRows/<?= $pfc_id ?>'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="print_dialog" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog" style="min-width:30%;">
		<div class="modal-content animated zoomIn border-light">
			<div class="modal-header bg-light">
				<h5 class="modal-title text-dark"><i class="fa fa-print"></i> Print Options</h5>
				<button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form id="printModel" method="post" action="<?=base_url($headData->controller.'/cp_pdf')?>" target="_blank">
				<div class="modal-body">
					<div class="col-md-12">
						<div class="row">
                            <div class="form-group col-md-4 ">
                                <label for="rev_no">Revision No</label>
                                <select name="rev_no" id="rev_no" class="form-control single-select" required>
                                    <option value="">Select Revision</option>
                                    <?php
                                    if(!empty($revList)){
                                        foreach($revList as $row){
                                            ?>
                                            <option value="<?=$row->rev_no?>" data-rev_date = '<?=$row->rev_date?>'><?=$row->rev_no.' | PFC REV NO : '.$row->pfc_rev_no?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <input type="hidden" name="rev_date" id="rev_date" >
                            </div>
                            <div class="form-group col-md-4 ">
                                <label>PDF Type</label>
                                <select name="pdf_type" id="pdf_type" class="form-control single-select" required>
                                    <option value="1">Reguler</option>
                                    <option value="2">Shrink</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4 ">
                                <label>Layout</label>
                                <select name="layout" id="layout" class="form-control single-select" required> 
                                    <option value="L">Landscap</option>
                                    <option value="P">Portrait</option>
                                </select>
                                <input type="hidden" name="cp_id" id="cp_id" value="0">
							</div>
					    </div>
				    </div>
                </div>
				<div class="modal-footer">
					<a href="#" data-dismiss="modal" class="btn btn-secondary"><i class="fa fa-times"></i> Close</a>
					<button type="submit" class="btn btn-success" onclick="closeModal('print_dialog');"><i class="fa fa-print"></i> Print</button>
				</div>
			</form>
		</div>
	</div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script>
    $(document).ready(function() {
        $(document).on("click",".printCp",function(){
			$("#printModel").attr('action',base_url + controller + '/cp_pdf');
			$("#cp_id").val($(this).data('id'));
			$("#print_dialog").modal();
		});

        $(document).on('change', "#rev_no", function() {
            var rev_date = $('#rev_no :selected').data('rev_date');
            $('#rev_date').val(rev_date);
        });

        $(document).on('click',".approveCP",function(){
            var id = $(this).data('id');
            var val = $(this).data('val');
            var msg= $(this).data('msg');
            $.confirm({
                title: 'Confirm!',
                content: 'Are you sure want to '+ msg +' this Control Plan?',
                type: 'green',
                buttons: {   
                    ok: {
                        text: "ok!",
                        btnClass: 'btn waves-effect waves-light btn-outline-success',
                        keys: ['enter'],
                        action: function(){
                            $.ajax({
                                url: base_url + controller + '/approveCP',
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
                                        //window.location.reload();
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
    
    function uploadExc() {
        setPlaceHolder();
        var fd = new FormData();
        fd.append("fmea_excel", $("#fmea_excel")[0].files[0]);
        fd.append("item_id", $('#item_id').val());
        fd.append("pfc_id", $('#pfc_id').val());
        $.ajax({
            url: base_url + controller + '/importExcelCP',
            data: fd,
            type: "POST",
            processData: false,
            contentType: false,
            dataType: "json",
        }).done(function(data) {
            if (data.status === 0) {
                $(".error").html("");
                $.each(data.message, function(key, value) {
                    $("." + key).html(value);
                });
            } else if (data.status == 1) {
                initTable(1);$("#fmea_excel").val("");
                toastr.success(data.message, 'Success', {
                    "showMethod": "slideDown",
                    "hideMethod": "slideUp",
                    "closeButton": true,
                    positionClass: 'toastr toast-bottom-center',
                    containerId: 'toast-bottom-center',
                    "progressBar": true
                });
            } else {
                initTable(1);$("#fmea_excel").val("");
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

    function trashControlPlan(id, name = 'Record') {
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
                            url: base_url + controller + '/deleteControlPlan',
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
                                    initTable();
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
</script>