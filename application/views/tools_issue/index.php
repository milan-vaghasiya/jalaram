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
                                    <li class="nav-item"> <button onclick="statusTab('toolsIssueTable',0);" class="nav-link btn waves-effect waves-light btn-outline-info active mr-2" data-toggle="tab" aria-expanded="false">Non Returnable</button> </li>
                                    <li class="nav-item"> <button onclick="statusTab('toolsIssueTable',1);" class="nav-link btn waves-effect waves-light btn-outline-info" data-toggle="tab" aria-expanded="false">Returnable</button> </li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <h4 class="card-title text-center">Tools Issue</h4>
                            </div>
                            <div class="col-md-4">                            
                                <button type="button" class="btn waves-effect waves-light btn-outline-primary float-right addNew permission-write" data-button="both" data-modal_id="modal-xl" data-function="addDispatch" data-form_title="Tools Issue"><i class="fa fa-plus"></i> Tools Issue</button>
                            </div>                             
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='toolsIssueTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url();?>assets/js/custom/tools-issue.js?v=<?=time()?>"></script>