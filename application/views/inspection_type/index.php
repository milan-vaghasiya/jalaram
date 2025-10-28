<?php $this->load->view('includes/header');
    $title = ($type == 1)?'Inspection Type':'Inspection Parameter';
?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title"><?= $title ?></h4>
                            </div>
                            <div class="col-md-6">
                                <button type="button" class="btn waves-effect waves-light btn-outline-primary float-right addNew permission-write" data-button="both" data-modal_id="modal-md" data-function="addInspectionType/<?=$type?>" data-form_title="Add <?= $title ?>"><i class="fa fa-plus"></i> Add <?= $title ?></button>
                            </div>                             
                        </div>                                          
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='categoryTable' class="table table-bordered ssTable" data-url='/getDTRows/<?=$type?>'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>