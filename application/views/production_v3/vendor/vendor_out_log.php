
<div class="col-md-12">
    <input type="hidden"  id="ch_trans_id" name="ch_trans_id" value="<?=$ch_trans_id?>">
 
    <div class="row">
        <div class="table-responsive">
            <table id='outwardTransTable' class="table table-bordered  fs-12">
                <thead class="thead-info text-center">
                    <tr>
                        <th >Challan No</th>
                        <th >Challan Date</th>
                        <th>Process</th>
                        <th>Production Qty.</th>
                        <th>Without Process Qty.</th>
                        <th >Action</th>
                    </tr>
                </thead>
                <tbody id="outwardTransData">
                    
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
var tbodyData = false;
$(document).ready(function(){
    if(!tbodyData){
        var postData = {'postData':{'ch_trans_id':$("#ch_trans_id").val()},'table_id':"outwardTransTable",'tbody_id':'outwardTransData','tfoot_id':'','fnget':'getVendorOutHtml','controller':'production_v3/vendorLog'};
        getLogTransHtml(postData);
        tbodyData = true;
    }
});
function getOutwardTransHtml(data,formId="outWard"){ 
    if(data.status==1){
        initTable();
        var postData = {'postData':{'ch_trans_id':$("#ch_trans_id").val()},'table_id':"outwardTransTable",'tbody_id':'outwardTransData','tfoot_id':'','fnget':'getVendorOutHtml','controller':'production_v3/vendorLog'};
        getLogTransHtml(postData);
    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) {$("."+key).html(value);});
        }else{
            initTable();
            Swal.fire({ icon: 'error', title: data.message });
        }			
    }
}


function getLogTransHtml(data){
	var postData = data.postData || {};
	var fnget = data.fnget || "";
	var controllerName = data.controller || controller;
	var resFunctionName = data.res_function || "";

	var table_id = data.table_id || "";
	var thead_id = data.thead_id || "";
	var tbody_id = data.tbody_id || "";
	var tfoot_id = data.tfoot_id || "";	

	if(thead_id != ""){
		$("#"+table_id+" #"+thead_id).html(data.thead);
	}
	
	$.ajax({
		url: base_url + controllerName + '/' + fnget,
		data:postData,
		type: "POST",
		dataType:"json",
		beforeSend: function() {
			if(table_id != ""){
				var columnCount = $('#'+table_id+' thead tr').first().children().length;
				$("#"+table_id+" #"+tbody_id).html('<tr><td colspan="'+columnCount+'" class="text-center">Loading...</td></tr>');
			}
		},
	}).done(function(res){
		if(resFunctionName != ""){
			window[resFunctionName](response);
		}else{
			$("#"+table_id+" #"+tbody_id).html('');
			$("#"+table_id+" #"+tbody_id).html(res.tbodyData);
			if(tfoot_id != ""){
				$("#"+table_id+" #"+tfoot_id).html('');
				$("#"+table_id+" #"+tfoot_id).html(res.tfootData);
			}
		}
	});
}


function trashVendorLog(data,name='Record'){
	var send_data = { ch_trans_id:data.ch_trans_id,trans_no:data.trans_no };
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
						url: base_url + controller + '/delete',
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
                                getOutwardTransHtml(data);
								initTable(); initMultiSelect();
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