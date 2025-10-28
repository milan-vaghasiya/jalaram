<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-8">
                                <h4 class="card-title">Products</h4>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <select id="party_id_filture" class="form-control single-select" style="width:50%">
                                        <option value="0">Select ALL</option>
                                        <?php
                                            foreach ($customerList as $row) :
                                                if(!empty($row->party_code)){
                                                    echo '<option value="' . $row->id . '" >' . $row->party_code . '</option>';
                                                }
                                            endforeach;
                                        ?>
                                    </select>
                                    <button type="button" class="btn waves-effect waves-light btn-outline-primary float-right addNew permission-write" data-button="both" data-modal_id="modal-lg" data-function="addProduct" data-form_title="Add Product"><i class="fa fa-plus"></i> Add Product</button>
                                </div>
                            </div>                             
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='productTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url();?>assets/js/custom/product.js?v=<?=time()?>"></script>
<script src="<?php echo base_url();?>assets/js/custom/item-stock-update.js?v=<?=time()?>"></script>
<script>
$(document).ready(function(){
    initFGTable();
	$(document).on('change','#party_id_filture',function(){ initFGTable(); }); 
});

function initFGTable() {
    var party_id = $('#party_id_filture').val();
    $('.ssTable').DataTable().clear().destroy();
    var tableOptions = {
        pageLength: 25,
        'stateSave': false
    };
    var tableHeaders = {
        'theads': '',
        'textAlign': textAlign,
        'srnoPosition': 1
    };
    var dataSet = {
        party_id: party_id
    }
    ssDatatable($('.ssTable'), tableHeaders, tableOptions, dataSet);
}
</script>
