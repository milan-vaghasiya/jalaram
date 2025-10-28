<form id="followUpForm">
    <div class="col-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="" />
            <input type="hidden" name="lead_id" id="lead_id" value=<?=$lead_id?> />
            <input type="hidden" name="log_type" id="log_type" value="2" />

            <div class="col-md-12 form-group">
                <textarea type="text" rows="1" name="notes" id="notes" class="form-control" placeholder="Type a Message..."></textarea>
                <div class="error notes"></div>
            </div>
            <div class="col-md-12">
                <button type="button" class="btn btn-success btn-round btn-outline-dashed btn-block saveFollowUp" >Save Follow Up</button>
            </div>
        </div>
    </div>
</div>
</form>
<hr>
<div class="row">
    <div class="col-md-12 form-group">
        <div class="table-responsive">
            <table id="followUpTbl" class="table table-bordered align-items-center">
                <thead class="thead-info">
                    <tr>
                        <th style="width:5%;" rowspan="2">#</th>
                        <th>Message</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="followUpBody">
                    <?php echo $tbody;?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        
        $(document).on('click','.saveFollowUp',function(e){
            e.stopImmediatePropagation();e.preventDefault();
            var formId = "followUpForm";
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
                    $("#followUpBody").html(response.tbody);
                    $("#notes").val("");
                }
                else{$(".error").html("");$.each( response.message, function( key, value ) {$("."+key).html(value);});}
                window.scrollTo(0, document.body.scrollHeight);
            });
        });
     
	});

function trashFollowUp(id,lead_id,log_type,name='Record'){
	var send_data = { id:id,lead_id:lead_id,log_type:2};
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
                                $('#followUpBody').html("");
                                $('#followUpBody').html(data.tbody);
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