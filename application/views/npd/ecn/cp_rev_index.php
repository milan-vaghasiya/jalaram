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
                                    <li class="nav-item"> 
                                        <a href="<?=base_url('npd/ecn/reviseCheckPoint/0')?>" class="btn waves-effect waves-light btn-outline-info mr-2" style="outline:0px"  aria-expanded="false">Pending ECN</a> 
                                    </li>
                                    <li class="nav-item"> 
                                        <a a href="<?=base_url('npd/ecn/reviseCheckPoint/1')?>"  class="btn waves-effect waves-light btn-outline-info mr-2" style="outline:0px"  aria-expanded="false">Approved ECN</a> 
                                    </li>

                                    <li class="nav-item"> <button onclick="revChTab('revChTable',1,'getCpRevDTRows');" class="btn waves-effect waves-light btn-outline-info mr-2 <?=($status == 1)?'active':''?>" style="outline:0px" data-toggle="tab" aria-expanded="false">Pending CP</button> </li>
                                    <li class="nav-item"> <button onclick="revChTab('revChTable',3,'getCpRevDTRows');" class="btn waves-effect waves-light btn-outline-info <?=($status == 3)?'active':''?>" style="outline:0px" data-toggle="tab" aria-expanded="false">Approved  CP</button> </li>
                                </ul>
                            </div>
                           
                            <div class="col-md-4">
                                <a href="<?=base_url('npd/ecn/addReviseCheckPoint')?>" class="btn btn-outline-primary btn-edit permission-modify float-right" datatip="Revision" flow="down"><i class="fa fa-plus"></i> Add Revision</a>
                            </div>                             
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='revChTable' class="table table-bordered ssTable" data-url="/getCpRevDTRows/<?=$status?>"></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>

<div class="modal fade" id="viewChPointModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content animated slideDown">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel1">Check Point Review</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="party_so" method="post" action="">
                <div class="modal-body"  >
                    <div class="col-md-12" id="chView"></div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn waves-effect waves-light btn-outline-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                    <button type="button" class="btn waves-effect waves-light btn-outline-success btn-save save-form" onclick="saveApprove('saveFinalApprove')"><i class="fa fa-check"></i> Approve</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url();?>assets/js/custom/ecn.js?v=<?=time()?>"></script>