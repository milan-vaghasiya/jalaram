<form>
    <input type="hidden" name="process_id" value="<?=$id?>">
    <div class="row">
        <div class="col-md-12" >
            <div class="table-responsive" style="height:600px; overflow-y:auto;">
                <table class="table jpExcelTable"  >
                    <thead class="bg-light">
                        <tr>
                            <th>Material Grade</th>
                            <th>MHR</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $mhrArray = [];
                        if(!empty($mhrData)){
                            $mhrArray = array_reduce($mhrData, function($mhrArray, $grade) { 
                                $mhrArray[$grade->grade_id] = $grade; 
                                return $mhrArray; 
                            }, []);
                        }
                        if(!empty($gradeList)){
                            foreach($gradeList AS $row){
                                $mhr = ((!empty($mhrArray[$row->id]->mhr))?$mhrArray[$row->id]->mhr:0);
                                $id = ((!empty($mhrArray[$row->id]->id))?$mhrArray[$row->id]->id:0);
                                ?>
                                <tr>
                                    <td>
                                        <?=$row->material_grade?>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control floatOnly" name="mhr[]" value="<?=$mhr?>">
                                        <input type="hidden" name = "grade_id[]" value = "<?=$row->id?>">
                                        <input type="hidden" name = "id[]" value="<?=$id?>">
                                    </td>
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
</form>