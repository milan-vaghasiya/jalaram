<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-8">
                                <ul class="nav nav-pills">
                                    <a href="<?=base_url($headData->controller."/rejectionIndex/1")?>" class="btn waves-effect waves-light btn-outline-primary permission-write <?=($prod_type==1)?'active':''?> mr-1"> Pending Inhouse</a>

                                    <a href="<?=base_url($headData->controller."/rejectionLog/1")?>" class="btn waves-effect waves-light btn-outline-primary permission-write mr-1"> Complete Inhouse</a>

                                    <a href="<?=base_url($headData->controller."/rejectionIndex/3")?>" class="btn waves-effect waves-light btn-outline-primary permission-write <?=($prod_type==3)?'active':''?> mr-1"> Pending Vendor </a>

                                    <a href="<?=base_url($headData->controller."/rejectionLog/3")?>" class="btn waves-effect waves-light btn-outline-primary permission-write mr-1"> Complete Vendor</a>

                                </ul>
                            </div>
                            <div class="col-md-4">
                                <h4 class="card-title">
                                <?=($prod_type==1)?'Inhouse':'Vendor'?> Process Approval
                                </h4>
                            </div>
                           
                            
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">                            
                            <table id='logsheetTable' class="table table-bordered ssTable ssTable-cf" data-ninput="[0,1]"  data-srowposition = "1" data-url='/getProductionLogDTRows/<?=$prod_type?>'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url(); ?>assets/js/custom/production-log.js?v=<?= time() ?>"></script>
