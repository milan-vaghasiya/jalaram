<form>
    <div class="col-md-12 form-group">
        <div class="table-responsive">
            <table class="jpExcelTable">
                <tr>
                    <th style="width:20%">Grade</th>
                    <td colspan="3"><?=$dataRow->material_grade?></td>
                </tr>
                <tr>
                    <th style="width:20%">Dimension</th>
                    <td style="width:30%"><?=$dataRow->dimension?></td>
                    <th style="width:20%">MOQ</th>
                    <td style="width:30%"><?=$dataRow->moq?></td>
                </tr>
                <tr>
                    <th>Gross Wt</th>
                    <td><?=$dataRow->gross_wt?></td>
                    <th>Required</th>
                    <td><?=($dataRow->gross_wt * $dataRow->moq)?></td>
                </tr>
            </table>
        </div>
    </div>
    <div class="col-md-12 form-group">
        <div class="row">
            <input type="hidden" name="id" value="<?=$dataRow->id?>">
            <div class="col-md-6 form-group">
                <label for="dimension">Dimension</label>
                <input type="text" name="dimension" id="dimension" class="form-control req" value="<?=(!empty($dataRow->dimension)?$dataRow->dimension:'')?>">
            </div>
            <div class="col-md-6 form-group">
                <label for="rm_rate">Rate</label>
                <input type="text" name="rm_rate" id="rm_rate" class="form-control req" value="<?=(!empty($dataRow->rm_rate)?$dataRow->rm_rate:'')?>">
            </div>
        </div>
    </div>
</form>