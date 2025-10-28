<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-3 form-group">
                                <h4 class="card-title text-left pageHeader"><?= $pageHeader ?></h4>
                            </div>
                            <div class="col-md-3">  
                                <select id="order_status" class="form-control single-select">
                                    <option value="">Select All</option>
                                    <option value="0">Pending</option>
                                    <option value="4">NPD</option>
                                    <option value="3">On-Hold</option>
                                    <option value="1">Complete</option>
                                    <option value="2">Short Close</option>
                                </select>
                            </div>
                            <div class="col-md-6">  
                                <div class="input-group">
                                    <input type="date" name="from_date" id="from_date" class="form-control"  value="<?=date('Y-m-01')?>" />
                                    <input type="date" name="to_date" id="to_date" class="form-control" value="<?=date('Y-m-d')?>" />
                                    <div class="input-group-append">
                                        <button type="button" class="btn waves-effect waves-light btn-success float-right" onclick="loadData()" title="Load Data"> <i class="fas fa-sync-alt"></i> Load</button>
                                    </div>
                                </div>
                                <div class="error fromDate toDate"></div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body reportDiv" style="min-height:75vh">
                        <div class="table-responsive">
                            <table id='reportTable' class="table table-bordered">
                                <thead class="thead-info" id="theadData">
                                    <tr>
                                        <th style="min-width:50px;">#</th>
                                        <th style="min-width:100px;">Jobcard</th>
                                        <th style="min-width:100px;">Job. Date</th>
                                        <th>Product</th>
                                        <th>Job Qty.</th>
                                        <th>Pend. Job Qty</th>
                                        <th>Material</th>
                                        <th>W/pcs</th>
                                        <th>Required Qty</th>
                                        <th>Issue Qty</th>
                                        <th>Used Qty</th>
                                        <th>Scrap Qty</th>
                                        <th>Return Qty(RM)</th>
                                        <th>Return Date</th>
                                        <th>Stock</th>
                                        <th>Last Activity</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyData"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?php $this->load->view('includes/footer'); ?>
<?= $floatingMenu ?>
<script>
    $(document).ready(function() {
        reportTable();
		//setTimeout(function(){  loadData(); }, 50);		
    });

    function loadData() {
		var from_date = $('#from_date').val();
		var to_date = $('#to_date').val();
		var valid = 1;
		var order_status = $('#order_status :selected').val();
		if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
		if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
		if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}
		if(valid)
		{
			$.ajax({
				url: base_url + controller + '/getJobMaterialTrackingData',
				data: {from_date:from_date,to_date:to_date,order_status:order_status},
				type: "POST",
				dataType:'json',
				success:function(data){
					$("#reportTable").dataTable().fnDestroy();
                    $("#tbodyData").html(data.tbody);
                    reportTable();
				}
			});
		}
    }

    function reportTable() {
        var reportTable = $('#reportTable').DataTable({
            responsive: true,
            scrollY: '55vh',
            scrollCollapse: true,
            "scrollX": true,
            "scrollCollapse": true,
            //'stateSave':true,
            "autoWidth": false,
            order: [],
            "columnDefs": [{
                    type: 'natural',
                    targets: 0
                },
                {
                    orderable: false,
                    targets: "_all"
                },
                {
                    className: "text-left",
                    targets: [0, 1]
                },
                {
                    className: "text-center",
                    "targets": "_all"
                }
            ],
            pageLength: 25,
            language: {
                search: ""
            },
            lengthMenu: [
                [10, 25, 50, 100, -1],
                ['10 rows', '25 rows', '50 rows', '100 rows', 'Show all']
            ],
            dom: "<'row'<'col-sm-7'B><'col-sm-5'f>>" + "<'row'<'col-sm-12't>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            buttons: ['pageLength', 'excel', {
                text: 'Refresh',
                action: function(e, dt, node, config) {
                    loadData();
                }
            }]
        });
        reportTable.buttons().container().appendTo('#reportTable_wrapper toolbar');
        $('.dataTables_filter .form-control-sm').css("width", "97%");
        $('.dataTables_filter .form-control-sm').attr("placeholder", "Search.....");
        $('.dataTables_filter').css("text-align", "left");
        $('.dataTables_filter label').css("display", "block");
        $('.btn-group>.btn:first-child').css("border-top-right-radius", "0");
        $('.btn-group>.btn:first-child').css("border-bottom-right-radius", "0");
        return reportTable;
    }
</script>