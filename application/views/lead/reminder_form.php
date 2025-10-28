<form id="reminderForm">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="" />
            <input type="hidden" name="lead_id" id="lead_id" value=<?=$lead_id?> />
            <input type="hidden" name="log_type" id="log_type" value="3" />

            <div class="col-md-6 form-group">
                <label for="ref_date">Date</label>
                <input type="date" name="ref_date" id="ref_date" class="form-control req" value="<?=date("Y-m-d")?>" min="<?=date("Y-m-d")?>" />
                <div class="error ref_date"></div>
            </div>
            <div class="col-md-6 form-group">
                <label for="reminder_time">Time</label>
                <input type="time" name="reminder_time" id="reminder_time" class="form-control req" value="<?=date("h:i:s")?>" min="<?=date("h:i:s")?>" />
                <div class="error reminder_time"></div>
            </div>
            <div class="col-md-12 form-group">
                <label for="mode">Mode</label>
                <select name="mode" id="mode" class="form-control single-select req">
                    <?php
                        foreach($mode as $key=>$mode):
                            echo '<option value="'.$mode.'">'.$mode.'</option>';
                        endforeach;
                    ?>
                </select>
                <div class="error mode"></div>
            </div>
            <div class="col-md-12 form-group">
                <label for="notes">Notes</label>
                <textarea name="notes" class="form-control" rows="1"></textarea>
                <div class="error notes"></div>
            </div>
            <div class="col-md-12">
                <button type="button" class="btn btn-success btn-round btn-outline-dashed btn-block saveReminder" >Save Reminder</button>
            </div>
        </div>        
    </div>
</form>
<hr>
<div class="row">
    <div class="col-md-12 form-group">
        <div class="table-responsive">
            <table id="reminderTbl" class="table table-bordered align-items-center">
                <thead class="thead-info">
                    <tr>
                        <th style="width:5%;" rowspan="2">#</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Mode</th>
                        <th>Notes</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="reminderBody">
                    <?php echo $tbody; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
   

<script>
    $(document).ready(function(){
   
        $(document).on('click','.saveReminder',function(e){
            e.stopImmediatePropagation();e.preventDefault();
            var formId = "reminderForm";
            var form = $('#'+formId)[0];
            var fd = new FormData(form);

            $.ajax({
                url: base_url + controller + '/saveSalesLog',
                data:fd,
                type: "POST",
                global:false,
                processData:false,
                contentType:false,
                dataType:"json",
            }).done(function(response){
                if(response.status==1)
                {
                    $("#reminderBody").html(response.tbody);
                    $("#ref_date").val("");
                    $("#reminder_time").val("");
                    $("#email").val("");
                    $("#notes").val("");
                }
                else{$(".error").html("");$.each( response.message, function( key, value ) {$("."+key).html(value);});}
                window.scrollTo(0, document.body.scrollHeight);
                
            });
        });
    });

function trashReminder(id,lead_id,log_type,name='Record'){
	var send_data = { id:id,lead_id:lead_id,log_type:3};
	$.confirm({
		title: 'Confirm!',
		content: 'Are you sure want to delete this '+name+'?',
		type: 'red',
		buttons: {   
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function(){
					$.ajax({
						url: base_url + controller + '/deleteSaleslog',
						data: send_data,
						type: "POST",
						dataType:"json",
						success:function(data)
						{
							if(data.status==0){
                                toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}else{
                                $('#reminderBody').html("");
                                $('#reminderBody').html(data.tbody);
								toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}
						}
					});
				}
			},
			cancel: {
                btnClass: 'btn waves-effect waves-light btn-outline-secondary',
                action: function(){

				}
            }
		}
	});
}

</script>
