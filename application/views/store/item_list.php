<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-8">
                                <h4 class="card-title">Stock Ledger</h4>
                            </div>
                            <div class="col-md-4 float-right">
                                <select id="item_type" class="form-control">
                                    <option value="3">Raw Material</option>
                                    <option value="2">Consumable</option>
                                    <option value="9">Packing Material</option>
                                    <option value="10">Scrap</option>
                                    <!-- <option value="1">Finish Good</option> 
                                    <option value="4">Capital Goods</option>
                                    <option value="5">Machineries</option>
                                    <option value="6">Instruments</option>
                                    <option value="7">Gauges</option> -->
                                </select>
                            </div>                             
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='itemTable' class="table table-bordered ssTable" data-url='/itemList/1'></table>
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
    var item_type = $("#item_type").val();
    $("#itemTable").attr("data-url",'/itemList/'+item_type);
    initTable(0);
    
    $(document).on('change',"#item_type",function(){
        var item_type = $("#item_type :selected").val();
        var party_category = (item_type == 1)?1:3;
        $.ajax({
            url : base_url + controller + '/getPartyList',
            type:'post',
            data:{party_category:party_category},
            dataType:'json',
            success:function(data){
                $("#party_id").html(data.options);
                $("#party_id").comboSelect();
                $("#itemTable").attr("data-url",'/itemList/'+item_type);
                initTable(0);
            }
        });
    });
    
    $(document).on('change',"#party_id",function(){
        var item_type = $("#item_type :selected").val();
        var party_id = $("#party_id :selected").val();
        $("#itemTable").attr("data-url",'/itemList/'+item_type+'/'+party_id);
        initTable(0);
    });
});
</script>
