<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-4">
                                <h4 class="card-title text-left">Tax Invoice Adjustment</h4>
                            </div>
                            <div class="col-md-8 float-right">
                                <ul class="nav nav-pills float-right">
                                    <li class="nav-item"> 
                                        <button onclick="statusTab('invoiceAdjTable',0,'getExportDtHeader','invUnadjusted');" class=" btn waves-effect waves-light btn-outline-danger active" style="outline:0px" data-toggle="tab" aria-expanded="false">Invoice Unadjusted</button>
                                    </li>
                                    <li class="nav-item"> 
                                        <button onclick="statusTab('invoiceAdjTable',1,'getExportDtHeader','inrCreditUnadjusted');" class=" btn waves-effect waves-light btn-outline-warning" style="outline:0px" data-toggle="tab" aria-expanded="false">INR Credit Unadjusted</button> 
                                    </li>
                                    <li class="nav-item"> 
                                        <button onclick="statusTab('invoiceAdjTable',2,'getExportDtHeader','invAdjusted');" class=" btn waves-effect waves-light btn-outline-success" style="outline:0px" data-toggle="tab" aria-expanded="false">Invoice Adjusted</button> 
                                    </li>
                                </ul>
                            </div>                  
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='invoiceAdjTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>