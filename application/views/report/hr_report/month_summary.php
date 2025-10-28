<?php 
	$this->load->view('includes/header'); 	
	$today = new DateTime();
	$today->modify('first day of this month');$first_day = date('Y-m-d');
	$today->modify('last day of this month');$last_day = date("t",strtotime($today->format('Y-m-d')));
	$monthArr = ['April'=>'04','May'=>'05','June'=>'06','July'=>'07','August'=>'08','September'=>'09','October'=>'10','November'=>'11','December'=>'12','January'=>'01','February'=>'02','March'=>'03'];
	
	$printString = '';
	for($r=1;$r<=5;$r++)
	{
		for($c=1;$c<=$r;$c++){$printString .= ($c + (($r + ($r-1)) * $c) - 1)." ";}
		$printString .= '<br>';
	}
?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <!--<div class="col-md-2"> <h4 class="card-title">Attendance Report</h4></div>-->
							<div class="col-md-12">
                                <div class="input-group">
									<select name="shift_id" id="shift_id" class="form-control single-select req" style="width:18%;">
									    <option value="ALL">All Shift</option>
										<?php
											foreach($shiftList as $row):
												echo '<option value="'.$row->latest_id.'" >'.$row->shift_name.'['.date('H:i',strtotime($row->shift_start)).' - '.date('H:i',strtotime($row->shift_end)).'] </option>';
											endforeach;
										?>
									</select>
									<select name="biomatric_id" id="biomatric_id" class="form-control single-select req" style="width:26%;">
										<?php
										    if(in_array($this->userRole,[-1,1,7])){ echo '<option value="ALL">All Employees</option>'; }
											foreach($empList as $row):
												if(!empty($row->biomatric_id)):
												    $selected='';
												    if($this->loginId != -1 AND $this->loginId != 1){ $selected = ($this->loginId == $row->id)?'selected':''; }
													echo '<option value="'.$row->biomatric_id.'" '.$selected.'>['.$row->emp_code.'] '.$row->emp_name.'</option>';
												endif;
											endforeach;
										?>
									</select>
									<input type="date" name="from_date" id="from_date" class="form-control" max="<?=date('Y-m-d')?>" value="<?=date('Y-m-d')?>" style="width:16%;" />
									<div class="error fromDate"></div>
									<input type="date" name="to_date" id="to_date" class="form-control" value="<?=date('Y-m-d')?>" style="width:16%;" />
									<button type="button" class="btn waves-effect waves-light btn-warning float-right" title="Load Data" style="padding: 0.3rem 0px;border-radius:0px;width:8%;" onclick="printMonthlySummary('excel');"><i class="fa fa-file-excel"></i> Excel</button>
									<button type="button" class="btn waves-effect waves-light btn-primary float-right" title="Load Data" style="padding: 0.3rem 0px;width:8%;border-top-left-radius:0px;border-bottom-left-radius:0px;" onclick="printMonthlySummary('pdf');"><i class="fa fa-file-pdf"></i> PDF</a>
									<button type="button" class="btn waves-effect waves-light btn-success float-right" title="Send Mail" style="padding: 0.3rem 0px;width:8%;border-top-left-radius:0px;border-bottom-left-radius:0px;" onclick="sendAttendaceMail('mail')" datatip="Send Mail" flow="down"><i class="fas fa-envelope" ></i> Mail</a>
								</div>
                            </div>                        
                        </div>                                         
                    </div>
                    <div class="card-body" style="min-height:50vh;">
                        <div class="table-responsive">
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<?=$floatingMenu?>
<script src="<?php echo base_url();?>assets/js/custom/month-attendance.js?v=<?=time()?>"></script>
<script>
    $(document).ready(function(){
	// setTimeout(function(){$('.getDailyAttendance').trigger('click') }, 10);
	attendanceSummaryTable();
	$('.jdt thead .clonTR').clone(true).insertAfter( '.jdt thead .clonTR' );
    $('.jdt thead tr:eq(1) th').each( function (i) {
        var title = $(this).text(); //placeholder="'+title+'"
		$(this).html( '<input type="text" style="width:100%;"/>' );
	});
	/* $(document).on('click',".attendanceInfo",function(){
        var attendance_id = $(this).data('id');
        var emp_name = $(this).data('emp_name');
        var emp_id = $(this).data('emp_id');

		$('#emp_id').val($(this).data('emp_id'));
		$('.emp_name').html(emp_name);
		$('.infotitle').html($(this).data('infotitle'));
		$('.totalhour').html($(this).data('totalhour'));
		$('.punch_in').html($(this).data('punch_in'));
		$('.punch_out').html($(this).data('punch_out'));
		$('.overtime').html($(this).data('overtime'));
		$('#attendanceInfo').modal();
    }); */
	$(document).on('click',".getDailyAttendance",function(){
        var report_date = $("#report_date").val();
		$.ajax({ 
            type: "POST",   
            url: base_url + 'reports/hrReport/getMismatchPunch',   
            data: {report_date : report_date},
			dataType:"json",
        }).done(function(response){
            $('#attendanceSummaryTable').dataTable().fnDestroy();
			$('.attendance-summary').html(response.tbody);
			
			attendanceSummaryTable();
        });
    });
	$(document).on('click',".manualAttendance",function(){
		var functionName = $(this).data("function");
		var modalId = $(this).data('modal_id');
		var button = $(this).data('button');
		var title = $(this).data('form_title');
		var emp_id = $(this).data('empid');
		var attendance_date = $(this).data('adate');
		var formId = functionName.split('/')[0];
		var fnsave = $(this).data("fnsave");if(fnsave == "" || fnsave == null){fnsave="save";}
		$.ajax({ 
			type: "GET",   
			url: base_url + 'hr/manualAttendance/' + functionName,   
			data: {}
		}).done(function(response){
			$("#"+modalId).modal({show:true});
			$("#"+modalId+' .modal-title').html(title);
			$("#"+modalId+' .modal-body').html("");
			$("#"+modalId+' .modal-body').html(response);
			$("#"+modalId+" .modal-body form").attr('id',formId);
			$("#"+modalId+" .modal-footer .btn-save").attr('onclick',"storeWithController('"+formId+"','"+fnsave+"','hr/manualAttendance/');");
			if(button == "close"){
				$("#"+modalId+" .modal-footer .btn-close").show();
				$("#"+modalId+" .modal-footer .btn-save").hide();
			}else if(button == "save"){
				$("#"+modalId+" .modal-footer .btn-close").hide();
				$("#"+modalId+" .modal-footer .btn-save").show();
			}else{
				$("#"+modalId+" .modal-footer .btn-close").show();
				$("#"+modalId+" .modal-footer .btn-save").show();
			}
			$('#emp_id').val(emp_id);
			$('#attendance_date').val(attendance_date);
			$(".single-select").comboSelect();
			$("#processDiv").hide();
			$("#"+modalId+" .scrollable").perfectScrollbar({suppressScrollX: true});
			setTimeout(function(){ initMultiSelect();setPlaceHolder(); }, 5);
		});
	});
});

function attendanceSummaryTable()
{
	var attendanceSummaryTable = $('#attendanceSummaryTable').DataTable( 
	{
		responsive: true,
		//'stateSave':true,
		"autoWidth" : false,
		order:[],
		"columnDefs": 	[
							{ type: 'natural', targets: 0 },
							{ orderable: false, targets: "_all" }, 
							{ className: "text-left", targets: [0,1] }, 
							{ className: "text-center", "targets": "_all" } 
						],
		pageLength:25,
		language: { search: "" },
		lengthMenu: [
            [ 10, 25, 50, 100, -1 ],[ '10 rows', '25 rows', '50 rows', '100 rows', 'Show all' ]
        ],
		dom: "<'row'<'col-sm-7'B><'col-sm-5'f>>" +"<'row'<'col-sm-12't>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
		buttons: [ 'pageLength', 'excel', {text: 'Refresh',action: function ( e, dt, node, config ) {loadAttendanceSheet();}}]
	});
	attendanceSummaryTable.buttons().container().appendTo( '#attendanceSummaryTable_wrapper toolbar' );
	$('#attendanceSummaryTable_filter .form-control-sm').css("width","97%");
	$('#attendanceSummaryTable_filter .form-control-sm').attr("placeholder","Search.....");
	$('.dataTables_filter').css("text-align","left");
	$('#attendanceSummaryTable_filter label').css("display","block");
	$('.btn-group>.btn:first-child').css("border-top-right-radius","0");
	$('.btn-group>.btn:first-child').css("border-bottom-right-radius","0");

	//Datatable Column Filter
    $('.jdt thead tr:eq(1) th').each( function (i) {
		$( 'input', this ).on( 'keyup change', function () {
			if ( attendanceSummaryTable.column(i).search() !== this.value ) {attendanceSummaryTable.column(i).search( this.value ).draw();}
		});
	} );
	return attendanceSummaryTable;
}

function sendAttendaceMail(file_type)
{
    var from_date = $("#from_date").val();
    var to_date = $("#to_date").val();
    var biomatric_id = $('#biomatric_id').val();
    var shift_id = $('#shift_id').val();
    $.confirm({
		title: 'Confirm!',
		content: 'Are you sure want to Send Email ?',
		type: 'green',
		buttons: {   
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function(){
                    $.ajax({
            			url:base_url + controller + '/sendAttendaceMail',
            			type:'post',
            			data:{dates:from_date+'~'+to_date,biomatric_id:biomatric_id,shift_id:shift_id,file_type:file_type},
            			dataType:'json',
            			global:false,
            			success:function(data)
            			{
            				if(data.status==1){
                    			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
                    		}else{
                    			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
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