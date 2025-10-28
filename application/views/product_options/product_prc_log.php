
<div class="col-md-12">
    <form id="getPreInspection">
        <div class="row">
            <div class="col-md-6 form-group">
                <select name="process_id" id="process_id" class="from-control single-select req">
                    <option value="">Select Process</option>
                    <?php
                        foreach($processData as $row):
                            echo '<option value="'.$row->process_id.'" data-item_id="'.$row->item_id.'">'.$row->process_name.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
        </div>
    </form>
    <hr>
    <div class="row">
        <div class="table-responsive">
            <table id="production_prc_log" class="table table-bordered align-items-center fhTable">
                <thead class="thead-info">
                    <tr>
                        <th style="width:5%;">#</th>
                        <th>PFC Revision</th>
                        <th>Process Name</th>
                        <th>Time</th>
                        <th>Weight</th>
                        <th>Created By</th>
                        <th>Created At</th>
                        <th>Updated By</th>
                        <th>Updated At</th>
                    </tr>
                </thead>
                <tbody id="tbodyData" class="scroll-tbody scrollable maxvh-60">
					<tr>
						<td class="text-center" colspan="9">No Data Found.</td>
					</tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
	$(document).on("change", "#process_id", function(){
		var process_id = $(this).val();
		var item_id = $(this).find(':selected').data('item_id');
		$.ajax({
            url: base_url + controller + '/getProductPrcLogData',
            data: {process_id:process_id,item_id:item_id},
            type: "POST",
            dataType: "json",
        }).done(function(data) {
            $("#inspectionBody").html("");
            if (data.status == 1) {
                // initTable();
                $("#tbodyData").html(data.tbody);
            }
        });
	});
});
</script>