<?php $this->load->view('includes/header'); ?>
<link href="<?= base_url() ?>assets/libs/chartist/dist/chartist.min.css" rel="stylesheet">
<link href="<?= base_url() ?>assets/js/pages/chartist/chartist-init.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="<?= base_url() ?>assets/extra-libs/c3/c3.min.css">
<div class="page-wrapper">
	<div class="container-fluid bg-container">
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header">
						<div class="row">
							<div class="col-md-3">
								<h4 class="card-title">Device No : <?=$device_no?></h4>
							</div>
							<div class="col-md-9 row">
							    <div class="col-md-3">
							        <select id="log_type" class="form-control">
							            <option value="0">Production Summary</option>
							            <option value="1">Production Log</option>
							            <option value="2">Job Change Log</option>
							            <!--<option value="3">Tool Change Log</option>
							            <option value="4">Idle Time Log</option>-->
							        </select>
							    </div>
								<div class="col-md-4">
									<input type="hidden" name="device_no" id="device_no" value="<?=$device_no?>">
									<input type="datetime-local" name="fromDate" id="fromDate" class="form-control" value="<?= date('Y-m-d\T00:00:01') ?>" />
									<div class="error fromDate"></div>
								</div>
								<div class="col-md-5">
									<div class="input-group float-right">
										<input type="datetime-local" name="toDate" id="toDate" class="form-control" value="<?= date('Y-m-d\T23:59:59') ?>" />
										<div class="input-group-append">
											<button type="button" class="btn waves-effect waves-light btn-success float-right loaddata" title="Load Data">
												<i class="fas fa-sync-alt"></i> Load
											</button>
										</div>
									</div>
									<div class="error toDate"></div>
								</div>
							</div>
						</div>
					</div>
					<div class="card-body">
						<div class="table-responsive">
						<table id="logTbl" class="table table-striped table-bordered" style="width:100%;">
							<thead id="theadData">
								<tr>
									<th style="width:8%;">#</th>
									<th>Entry Time</th>
									<th>Operator</th>
									<th>Job No</th>
									<th>Process No</th>
									<th>Part Count</th>
									<th>Rework Count</th>
									<th>Production Time</th>
									<th>Ex. Idle Time</th>
								</tr>
							</thead>
							<tbody id="tbodyData">
								<?php
								if(!empty($jobData)){
									$i=1;
									foreach($jobData as $row):
										$hours = floor($row->productionTime / 3600);
										$mins = floor(($row->productionTime - $hours*3600) / 60);
										$s = $row->productionTime - ($hours*3600 + $mins*60);
										$mins = ($mins<10?"0".$mins:"".$mins);
										$s = ($s<10?"0".$s:"".$s); 
										$productionTime = ($hours>0?$hours.":":"").$mins.":".$s;

										$hours = floor($row->xidealTime / 3600);
										$mins = floor(($row->xidealTime - $hours*3600) / 60);
										$s = $row->xidealTime - ($hours*3600 + $mins*60);
										$mins = ($mins<10?"0".$mins:"".$mins);
										$s = ($s<10?"0".$s:"".$s); 
										$idleTimeTime = ($hours>0?$hours.":":"0:").$mins.":".floor($s);
										echo '<tr>
											 <td align="center">'.$i++.'</td>
											 <td align="center">'.date('d-m-Y',strtotime($row->created_at)).'</td>
											 <td align="center">'.$row->created_by.'</td>
											 <td align="center">'.$row->job_no.'</td>
											 <td align="center">'.$row->process_no.'</td>
											 <td>'.$row->partCount.'</td>
											 <td>'.$row->rework_count.'</td>
											 <td align="center">'.$productionTime.'</td>
											 <td align="center">'.$idleTimeTime.'</td>
										</tr>';
									endforeach;
								}
								?>
							</tbody>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script>
	$(document).ready(function() {
		reportTable();
		$(document).on('click', '.loaddata', function(e) {
			$(".error").html("");
			var valid = 1;
            var fromDate = $('#fromDate').val();
			var toDate = $('#toDate').val();
			var logType = $('#log_type').val();

            if ($("#fromDate").val() == "") {
				$(".fromDate").html("From Date is required.");
				valid = 0;
			}
			if ($("#toDate").val() == "") {
				$(".toDate").html(" Date is required.");
				valid = 0;
			}
			var device_no = $("#device_no").val();
			if (valid) {
				$.ajax({
					url: base_url + controller + '/getProductionLogData',
					data: { fromDate:fromDate,toDate: toDate,device_no:device_no,log_type:logType },
					type: "POST",
					dataType: 'json',
					success: function(data) {
						$("#logTbl").dataTable().fnDestroy();
						$("#theadData").html(data.thead);
						$("#tbodyData").html(data.tbody);
						reportTable();
					}
				});
			}
		});
	});
	function reportTable(){
		var jpReportTable = $('#logTbl').DataTable({
		responsive: true,
		"scrollY": '52vh',
		"scrollX": true,
		deferRender: true,
		scroller: true,
		destroy: true,
		// 'stateSave':false,
		"autoWidth" : false,
		order: [],
		"columnDefs": [
		    {type: 'natural',targets: 0},
			{orderable: true,targets: "_all"},
			{className: "text-center",targets: [0, 1]},
			{className: "text-center","targets": "_all"}
		],
		pageLength: 25,
		language: {search: ""},
		lengthMenu: [
			[ 10, 20, 25, 50, 75, 100, 250,500 ],
			[ '10 rows', '20 rows', '25 rows', '50 rows', '75 rows', '100 rows','250 rows','500 rows' ]
		],
		dom: "<'row'<'col-sm-7'B><'col-sm-5'f>>" + "<'row'<'col-sm-12't>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
		buttons: ['pageLength', 'excel',{text: 'Pdf',action: function ( e, dt, node, config ) {loadData('pdf');}}],
		"fnInitComplete":function(){$('.dataTables_scrollBody').perfectScrollbar();},
	    "fnDrawCallback": function( oSettings ) {$('.dataTables_scrollBody').perfectScrollbar('destroy').perfectScrollbar();}
	});
	jpReportTable.buttons().container().appendTo('#logTbl_wrapper toolbar');
	$('.dataTables_filter .form-control-sm').css("width", "97%");
	$('.dataTables_filter .form-control-sm').attr("placeholder", "Search.....");
	$('.dataTables_filter').css("text-align", "left");
	$('.dataTables_filter label').css("display", "block");
	$('.btn-group>.btn:first-child').css("border-top-right-radius", "0");
	$('.btn-group>.btn:first-child').css("border-bottom-right-radius", "0");
	setTimeout(function(){ jpReportTable.columns.adjust().draw();}, 10);
	$('.page-wrapper').resizer(function() {jpReportTable.columns.adjust().draw(); });
	return jpReportTable;	
	}
</script>