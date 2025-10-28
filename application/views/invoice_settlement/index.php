<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-4">
                                <h4 class="card-title text-left">Invoice Settlement</h4>
                            </div>
                            <div class="col-md-8 float-right">
                                <ul class="nav nav-pills float-right">
                                    <li class="nav-item"> 
                                        <button onclick="statusTab('invoiceSettlementTable',0,'getExportDtHeader','invoiceUnsetlled');" class=" btn waves-effect waves-light btn-outline-danger active" style="outline:0px" data-toggle="tab" aria-expanded="false">Invoice Unsetlled</button>
                                    </li>
                                    <li class="nav-item"> 
                                        <button onclick="statusTab('invoiceSettlementTable',1,'getExportDtHeader','swiftUnsetlled');" class=" btn waves-effect waves-light btn-outline-warning" style="outline:0px" data-toggle="tab" aria-expanded="false">SWIFT Unsettled</button> 
                                    </li>
                                    <li class="nav-item"> 
                                        <button onclick="statusTab('invoiceSettlementTable',2,'getExportDtHeader','invoiceSettled');" class=" btn waves-effect waves-light btn-outline-success" style="outline:0px" data-toggle="tab" aria-expanded="false">Invoice Settled</button> 
                                    </li>
                                </ul>
                            </div>                  
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='invoiceSettlementTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>