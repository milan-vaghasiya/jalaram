<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table jpExcelTable">
                <thead class="thead-info text-center">
                    <tr>
                        <th>Print</th>
                        <th>Rev No</th>
                        <th>Material Grade</th>
                        <th>Dimension</th>
                        <th>Final Cost</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        if(!empty($costList)){
                            foreach($costList AS $row){
                                ?>
                                <tr>
                                    <td class="text-center">
                                        <a href="<?=base_url('costing/printCostDetail/'.$row->id)?>" class="btn btn-instagram btn-sm" target="_blank"><i class="fa fa-print"></i></a>
                                    </td>
                                    <td class="text-center">  <?=$row->rev_no?> </td>
                                    <td>  <?=$row->material_grade?> </td>
                                    <td>  <?=$row->dimension?> </td>
                                    <td class="text-center">  <?=$row->final_cost?> </td>
                                </tr>
                                <?php
                            }
                        }   
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>