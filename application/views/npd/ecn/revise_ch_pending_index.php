<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
							<div class="col-md-6">
                                <ul class="nav nav-pills">
                                    <li class="nav-item"> <button onclick="revChPendingTab('revChPendingTable',0,1);" class="btn waves-effect waves-light btn-outline-info mr-2 active" style="outline:0px" data-toggle="tab" aria-expanded="false">Pending ECN</button> </li>
                                    <li class="nav-item"> <button onclick="revChPendingTab('revChPendingTable',1,1);" class="btn waves-effect waves-light btn-outline-info mr-2" style="outline:0px" data-toggle="tab" aria-expanded="false">Verified ECN</button> </li>
                                    <li class="nav-item"> <button onclick="revChPendingTab('revChPendingTable',0,2);" class="btn waves-effect waves-light btn-outline-info mr-2 " style="outline:0px" data-toggle="tab" aria-expanded="false">Pending CP</button> </li>
                                    <li class="nav-item"> <button onclick="revChPendingTab('revChPendingTable',1,2);" class="btn waves-effect waves-light btn-outline-info mr-2" style="outline:0px" data-toggle="tab" aria-expanded="false">Verified CP</button> </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h4 class="card-title text-left">ECN Verification</h4>
                            </div>
                                                        
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='revChPendingTable' class="table table-bordered ssTable" data-url="/getRevChPendingDTRows"></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url();?>assets/js/custom/ecn.js?v=<?=time()?>"></script>