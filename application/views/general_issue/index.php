<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-4">
								<a href="<?= base_url($headData->controller . "/index") ?>" class="btn waves-effect waves-light btn-outline-primary permission-write">Pending Request</a>
								<a href="<?= base_url($headData->controller . "/materialIssue") ?>" class="btn waves-effect waves-light btn-outline-primary permission-write active">Issue Material</a>
                            </div>
                            <div class="col-md-4">
                                <h4 class="card-title text-center">General Issue</h4>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn waves-effect waves-light btn-outline-primary float-right addNew permission-write" data-button="both" data-modal_id="modal-xl" data-function="issueMaterial" data-form_title="General Issue"><i class="fa fa-plus"></i> General Issue</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='generalIssueTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>

<script src="<?php echo base_url(); ?>assets/js/custom/general-material-issue.js?v=<?= time() ?>"></script>
<script>
    $(document).ready(function() {
        $(document).on('click', '.viewMaterialIssueTrans', function() {
            var button = "close";
            var id = $(this).data("id");
            console.log(id);
            $.ajax({
                type: "POST",
                url: base_url + controller + '/viewMaterialIssueTrans',
                data: {
                    id: id
                }
            }).done(function(response) {
                $("#modal-lg").modal();
                $('#modal-lg .modal-title').html("General Issue");
                $('#modal-lg .modal-body').html(response);
                $("#modal-lg  .modal-body form").attr('id', 'trans_form');

                if (button == "close") {
                    $("#modal-lg .modal-footer .btn-close").show();
                    $("#modal-lg .modal-footer .btn-save").hide();
                } else if (button == "save") {
                    $("#modal-lg .modal-footer .btn-close").hide();
                    $("#modal-lg .modal-footer .btn-save").show();
                } else {
                    $("#modal-lg .modal-footer .btn-close").show();
                    $("#modal-lg .modal-footer .btn-save").show();
                }
                $(".single-select").comboSelect();
                initMultiSelect();
                setPlaceHolder();
            });
        });
    });

    function dispatch(data) {
        var button = "";
        $.ajax({
            type: "POST",
            url: base_url + controller + '/edit',
            data: {
                id: data.id
            }
        }).done(function(response) {
            $("#" + data.modal_id).modal();
            $("#" + data.modal_id + ' .modal-title').html(data.title);
            $("#" + data.modal_id + ' .modal-body').html(response);
            $("#" + data.modal_id + " .modal-body form").attr('id', data.form_id);
            $("#" + data.modal_id + " .modal-footer .btn-save").attr('onclick', "store('" + data.form_id + "');");
            if (button == "close") {
                $("#" + data.modal_id + " .modal-footer .btn-close").show();
                $("#" + data.modal_id + " .modal-footer .btn-save").hide();
            } else if (button == "save") {
                $("#" + data.modal_id + " .modal-footer .btn-close").hide();
                $("#" + data.modal_id + " .modal-footer .btn-save").show();
            } else {
                $("#" + data.modal_id + " .modal-footer .btn-close").show();
                $("#" + data.modal_id + " .modal-footer .btn-save").show();
            }
            $(".single-select").comboSelect();
            initMultiSelect();
            setPlaceHolder();
        });
    }
</script>