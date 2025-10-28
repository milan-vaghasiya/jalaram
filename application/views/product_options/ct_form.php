<form>
    <div class="col-md-12">
        <div class="row">
            <h6 style="color:#ff0000;font-size:1rem;"><i>Note : Cycle Time Per Piece</i></h6>
            <table class="table excel_table table-bordered">
                <thead class="thead-info">
                    <tr>
                        <th style="width:5%;text-align:center;">#</th>
                        <th style="width:10%;">PFC Revision</th>
                        <th style="width:20%;">Process Name</th>
                        <th style="width:15%;">Time</th>
                        <th style="width:15%;">Weight</th>
                        <th style="width:17%;">Created At</th>
                        <th style="width:17%;">Updated At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        if (!empty($processData)) :
                            $i = 1;
                            $html = "";
                            foreach ($processData as $row) :
                                $pid = (!empty($row->id)) ? $row->id : "";
                                $ct = (!empty($row->cycle_time)) ? $row->cycle_time : "";
                                $costing = (!empty($row->costing)) ? $row->costing : "";
                                
                                echo '<tr id="' . $row->id . '">
                                    <td class="text-center">' . $i++ . '</td>
                                    <td>' . $row->pfc_rev_no . '</td>
                                    <td>' . $row->process_name . '</td>
                                    <td class="text-center">
                                        <input type="text" name="cycle_time[]" class="form-control inputmask-his" value="' . $ct . '" />
                                        <input type="hidden" name="costing[]" class="form-control floatOnly" value="0">
                                        <input type="hidden" name="id[]" value="' . $pid . '" />
                                        <input type="hidden" name="item_id[]" value="' . $row->item_id . '" />
                                        <input type="hidden" name="process_id[]" value="' . $row->process_id . '" />
                                    </td>
                                    <td class="text-center">
                                        <input type="text" name="finished_weight[]" class="form-control floatOnly" value="'.$row->finished_weight.'" />
                                    </td>
                                    <td class="text-center">'.(!empty($row->created_name) ? '<small>'.$row->created_name.'</small><br/>'.formatDate($row->created_at) : formatDate($row->created_at)).'</td>
                                    <td class="text-center">'.(!empty($row->updated_name) ? '<small>'.$row->updated_name.'</small><br/>'.formatDate($row->updated_at) : formatDate($row->updated_at)).'</td>
                                  </tr>';
                            endforeach;
                        else :
                            echo '<tr><td colspan="7" class="text-center">No Data Found.</td></tr>';
                        endif;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</form>
