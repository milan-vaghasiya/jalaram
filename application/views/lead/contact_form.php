<div class="col-md-12">
    <form>
        <div class="row">
            <input type="hidden" name="party_id" id="party_id" value="<?= (!empty($dataRow->party_id)) ? $dataRow->party_id : $party_id; ?>" />
            <div class="col-md-4 form-group">
                <label for="person">Contact Person</label>
                <input type="text" name="person" id="person" class="form-control req" value="" />
            </div>
            <div class="col-md-4 form-group">
                <label for="mobile">Contact Mobile</label>
                <input type="text" name="mobile" id="mobile" class="form-control req" value="" />
            </div>
            <div class="col-md-4 form-group">
                <label for="email">Contact Email</label>
                <input type="text" name="email" id="email" class="form-control req" value="" />
            </div>
        </div>
    </form>
    <div class="row">
        <div class="col-md-12 form-group">
            <button type="button" class="btn waves-effect waves-light btn-outline-success btn-save save-form float-right" onclick="storeContact('contactDetail','saveContact');"><i class="fa fa-check"></i> Save</button>
        </div>
    </div>
    <div class="row">
        <div class="table-responsive">
            <table id="contacttbl" class="table table-bordered align-items-center">
                <thead class="thead-info">
                    <tr>
                        <th style="width:5%;">#</th>
                        <th>Person</th>
                        <th>Mobile</th>
                        <th>Email</th>
                        <th class="text-center" style="width:10%;">Action</th>
                    </tr>
                </thead>
                <tbody id="contactBody">
                    <?php
                        if (!empty($contact_detail)) :
                            $i=1; $arrCount = count($contact_detail);
                            foreach ($contact_detail as $row) :
                                echo '<tr>
                                        <td>'.$i.'</td>
                                        <td>'.$row->person.'</td>
                                        <td>'.$row->mobile.'</td>
                                        <td>'.$row->email.'</td>
                                        <td class="text-center">';
                                        if($arrCount == $i):
                                            echo '<a href="javascript:void(0);" class="btn btn-outline-danger btn-delete" onclick="trashContact();"><i class="ti-trash"></i></button>';
                                        endif;
                                        echo '</td>
                                    </tr>'; $i++;
                            endforeach;
                        else:
                            echo '<tr><td colspan="5" style="text-align:center;">No Data Found</td></tr>';
                        endif;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>

function storeContact(formId,fnsave,srposition=1){
	setPlaceHolder();
	if(fnsave == "" || fnsave == null){fnsave="save";}
	var form = $('#'+formId)[0];
	var fd = new FormData(form);
	$.ajax({
		url: base_url + controller + '/' + fnsave,
		data:fd,
		type: "POST",
		processData:false,
		contentType:false,
		dataType:"json",
	}).done(function(data){
		if(data.status===0){
			$(".error").html("");
			$.each( data.message, function( key, value ) {$("."+key).html(value);});
		}else if(data.status==1){
			initTable(srposition); //$('#'+formId)[0].reset(); //$(".modal").modal('hide');
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
            $("#contactBody").html(data.tbodyData);
            $("#party_id").val(data.partyId);
            $("#person").val("");
            $("#mobile").val("");
            $("#email").val("");
        }else{
			initTable(srposition); //$('#'+formId)[0].reset(); //$(".modal").modal('hide');
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}
				
	});
}

function trashContact(){
    var partyId = $("#party_id").val();
	var send_data = { id:partyId }; 
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
						url: base_url + controller + '/deleteContact',
						data: send_data,
						type: "POST",
						dataType:"json",
						success:function(data)
						{
							if(data.status==0)
							{
								toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}
							else
							{
                                $("#contactBody").html(data.tbodyData);
                                $("#party_id").val(data.partyId);
                                $("#person").val("");
                                $("#mobile").val("");
                                $("#email").val("");
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