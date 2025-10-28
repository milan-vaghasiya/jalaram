<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title">Finish Good Stock Ledger</h4>
                                <small class="text-primary font-bold">RTD STOCK : <?=!empty($stockData->rtd_stock)?$stockData->rtd_stock:0?> | PAR STOCK : <?=!empty($stockData->par_stock)?$stockData->par_stock:0?></small>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <select id="stock_type" class="form-control single-select" style="width:50%;">
                                        <option value="0">Select All</option>
                                        <option value="1">Without Zero</option>
                                    </select>
                                    <select id="party_id" class="form-control single-select" style="width:50%;">
                                        <option value="0">Select All Customer</option>
                                        <?php
                                            foreach($partyList as $row):
                                                echo '<option value="'.$row->id.'">'.$row->party_code.'</option>';
                                            endforeach;
                                        ?>
                                    </select>
                                </div>
                            </div>   
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='fgledgerTable' class="table table-bordered ssTable" data-url='/getfgLedger/1'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){
    var item_type = 1;
    $("#fgledgerTable").attr("data-url",'/getfgLedger/'+item_type);
    initTable(0);
    
    $(document).on('change',"#party_id",function(){
        var item_type = 1;
        var party_id = $(this).val();
        var stock_type = $("#stock_type :selected").val();
        $("#fgledgerTable").attr("data-url",'/getfgLedger/'+item_type+'/'+party_id+'/'+stock_type);
        initTable(0);
    });
    
    $(document).on('change',"#stock_type",function(){
        var item_type = 1;
        var stock_type = $(this).val();
        var party_id = $("#party_id :selected").val();
        $("#fgledgerTable").attr("data-url",'/getfgLedger/'+item_type+'/'+party_id+'/'+stock_type);
        initTable(0);
    });
});
</script>
