$(document).ready(function(){
    $(document).on('click','.restartVerification',function(){
		var id = $(this).data('id');	
		$.confirm({
			title: 'Confirm!',
			content: 'Are you sure want to Restart this Verification ?',
			type: 'green',
			buttons: {   
				ok: {
					text: "ok!",
					btnClass: 'btn waves-effect waves-light btn-outline-success',
					keys: ['enter'],
					action: function(){
						$.ajax({
							url: base_url + controller + '/restartVerification',
							data: {id:id},
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
									initTable(); 
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
	});

    $(document).on('click','.startEcn',function(){
		var id = $(this).data('id');	
		$.confirm({
			title: 'Confirm!',
			content: 'Are you sure want to Start this ECN ?',
			type: 'green',
			buttons: {   
				ok: {
					text: "ok!",
					btnClass: 'btn waves-effect waves-light btn-outline-success',
					keys: ['enter'],
					action: function(){
						$.ajax({
							url: base_url + controller + '/startEcn',
							data: {id:id},
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
									initTable(); 
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
	});

    $(document).on("change","#item_id",function(){
        var item_id = $(this).val();
        var rev_date = $("#rev_date").val();
        if(item_id == ""){
            $(".item_id").html("Please select item name.");
        }else{
            $.ajax({
                url:base_url + controller + "/getOldRevGradeByItem",
                type:'post',
                data:{item_id:item_id,rev_date:rev_date},
                dataType:'json',
                success:function(data){
                    $(".mtGrade").html("");
                    $(".mtGrade").html(data.gradeOption);
                    $("#material_grade").val(data.material_grade);
                    $("#rev_no").val(data.jji_rev_no);
                    reInitMultiSelect();
                }
            });
        }
    });

    $(document).on("change keyup","#rev_date",function(){
        var rev_date = $("#rev_date").val();
        let date = new Date(rev_date);
        date.setDate(date.getDate() + 10);
        $('#target_date').val(formatDate(date));
    });

    $(document).on('click','.closeEcn',function(){
		var id = $(this).data('id');	
		$.confirm({
			title: 'Confirm!',
			content: 'Are you sure want to Close this ECN ?',
			type: 'green',
			buttons: {   
				ok: {
					text: "ok!",
					btnClass: 'btn waves-effect waves-light btn-outline-success',
					keys: ['enter'],
					action: function(){
						$.ajax({
							url: base_url + controller + '/closeEcn',
							data: {id:id},
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
									initTable(); 
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
	});
});	

function activeRevision(data){        
    var send_data = { id:data.id,is_active:data.is_active,msg:data.msg };
    $.confirm({
        title: 'Confirm!',
        content: data.msg,
        type: 'red',
        buttons: {   
            ok: {
                text: "ok!",
                btnClass: 'btn waves-effect waves-light btn-outline-success',
                keys: ['enter'],
                action: function(){
                    $.ajax({
                        url: base_url + controller + '/activeRevision',
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

function revChTab(tableId,status,entry_type,fname,srnoPosition=1){
    $("#"+tableId).attr("data-url",'/'+fname+'/'+status+'/'+entry_type);
    ssTable.state.clear();initTable(srnoPosition);
}

function trashEcn(id,name='Record',fnDelete="delete"){
	var send_data = { id:id };
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
						url: base_url + 'npd/ecn/deleteEcn',
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

function approveEcn(id)
{
    $('#viewChPointModal').modal();
    $('#id').val(id);
    $.ajax({
            url:base_url + controller +'/checkPointView',
            type:'post',
            data:{id:id,view:1},
            dataType:'json',
            global:false,
            success:function(data)
            {
                $('#chView').html(data.pdfData)
            }
        });
}

function revChPendingTab(tableId,status,entry_type,srnoPosition=1){
    $("#"+tableId).attr("data-url",'/getRevChPendingDTRows/'+status+'/'+entry_type);
    ssTable.state.clear();initTable(srnoPosition);
}

function openView(id)
{
    $('#viewChPointModal').modal();
    $('#id').val(id);
    $.ajax({
            url:base_url + controller +'/checkPointView',
            type:'post',
            data:{id:id,view:2},
            dataType:'json',
            global:false,
            success:function(data)
            {
                $('#chView').html(data.pdfData)
            }
        });
}

function saveApprove(fnSave="approveCheckPoint") 
{
    var id = $("#id").val();
    var rev_id = $("#rev_id").val();
    var qty_id = $("#qty_id").val();
    var qty_label = $("#qty_label").val();
    var qty = $("#qty").val();
    var sys_qty = $("#sys_qty").val();
    var eff_impl_date = $("#eff_impl_date").val();

    $.ajax({
        url: base_url + controller + '/' + fnSave,
        data: {
            id: id,
            rev_id: rev_id,
            qty_id: qty_id,
            qty_label: qty_label,
            qty: qty,
            sys_qty: sys_qty,
            eff_impl_date: eff_impl_date
        },
        type: "POST",
        dataType: "json",
        success: function(data) {
            if (data.status == 0) {
                toastr.error(data.message, 'Sorry...!', {
                    "showMethod": "slideDown",
                    "hideMethod": "slideUp",
                    "closeButton": true,
                    positionClass: 'toastr toast-bottom-center',
                    containerId: 'toast-bottom-center',
                    "progressBar": true
                });
            } else {
                initTable();
                toastr.success(data.message, 'Success', {
                    "showMethod": "slideDown",
                    "hideMethod": "slideUp",
                    "closeButton": true,
                    positionClass: 'toastr toast-bottom-center',
                    containerId: 'toast-bottom-center',
                    "progressBar": true
                });
                window.location.reload();
            }
        }
    });
}

function revChReviewTab(tableId,status,entry_type,srnoPosition=1){
    $("#"+tableId).attr("data-url",'/getRevChReviewDTRows/'+status+'/'+entry_type);
    ssTable.state.clear();initTable(srnoPosition);
}

function saveRevChPoint(formId){
    var fd = $('#'+formId).serialize();
    $.ajax({
        url: base_url + controller + '/saveRevChPoint',
        data:fd,
        type: "POST",
        dataType:"json",
    }).done(function(data){
        if(data.status===0){
            $(".error").html("");
            $.each( data.message, function( key, value ) {
                $("."+key).html(value);
            });
        }else if(data.status==1){
            toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
            window.location = data.url;
        }else{
            toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
        }				
    });
}

function changeCpRevStatus(data)
{
    $.confirm({
        title: 'Confirm!',
        content: data.message,
        type: 'green',
        buttons: {   
            ok: {
                text: "ok!",
                btnClass: 'btn waves-effect waves-light btn-outline-success',
                keys: ['enter'],
                action: function(){
                    $.ajax({
                        url: base_url + controller + '/changeCpRevStatus',
                        data: data.postData,
                        type: "POST",
                        dataType:"json",
                        success:function(response)
                        {
                            if(response.status==0){
                                toastr.error(response.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
                            }
                            else
                            {
                                initTable(); 
                                toastr.success(response.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
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

function addCpRevision(data){
	var button = "";
	var button = data.button;if(button == "" || button == null){button="both";}
	var fnedit = data.fnedit;if(fnedit == "" || fnedit == null){fnedit="edit";}
	var fnsave = data.fnsave;if(fnsave == "" || fnsave == null){fnsave="save";}
	
	$.ajax({ 
		type: "POST",   
		url: base_url + controller + '/' + fnedit,   
		data: data.postData
	}).done(function(response){
		$("#"+data.modal_id).modal();
		$("#"+data.modal_id+' .modal-title').html(data.title);
		$("#"+data.modal_id+' .modal-body').html(response);
		$("#"+data.modal_id+" .modal-body form").attr('id',data.form_id);
		$("#"+data.modal_id+" .modal-footer .btn-save").attr('onclick',"store('"+data.form_id+"','"+fnsave+"');");
		$("#"+data.modal_id+" .modal-footer .btn-save-close").attr('onclick',"store('"+data.form_id+"','"+fnsave+"','save_close');");
		if(button == "close"){
			$("#"+data.modal_id+" .modal-footer .btn-close").show();
			$("#"+data.modal_id+" .modal-footer .btn-save").hide();
			$("#"+data.modalId+" .modal-footer .btn-save-close").hide();
		}else if(button == "save"){
			$("#"+data.modal_id+" .modal-footer .btn-close").hide();
			$("#"+data.modal_id+" .modal-footer .btn-save").show();
            $("#"+data.modalId+" .modal-footer .btn-save-close").show();
		}else{
			$("#"+data.modal_id+" .modal-footer .btn-close").show();
			$("#"+data.modal_id+" .modal-footer .btn-save").show();
            $("#"+data.modalId+" .modal-footer .btn-save-close").show();
		}
		$(".single-select").comboSelect();
		$("#"+data.modal_id+" .scrollable").perfectScrollbar({suppressScrollX: true});
		initMultiSelect();setPlaceHolder();
	});
}