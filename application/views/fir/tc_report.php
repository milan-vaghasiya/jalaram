<form>
    <div class="col-md-12">
        <div class="row">
            <div class="table-responsive">
                <table class="table jpExcelTable" id="dimensionTable">
                    <thead>
                        <tr class="text-center">
                            <th style="width:3%">#</th>
                            <th class="text-left">Batch</th>
                            <th class="text-left">File Download</th>                           
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            if (!empty($testReportData)) {
                                $i = 1;
                                foreach ($testReportData as $row) {
                                    echo "<tr>";
                                    echo "<td>".$i++."</td>";
                                    echo "<td>".$row->mill_tc."</td>";
                                    echo "<td><a href='".base_url('assets/uploads/test_report/'.$row->tc_file)."' target='_blank'><i class='fa fa-download'></i></a></td>";
                                    echo "</tr>";
                                }
                            }else{
                                 echo "<tr><td class='text-center' colspan='3'>No Data Found</td></tr>";
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>