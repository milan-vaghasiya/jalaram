<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
	<div class="container-fluid bg-container">
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header">
						<div class="row">
							<div class="col-md-6 form-group">
								<h4 class="card-title pageHeader"><?= $pageHeader ?></h4>
							</div>
							<div class="col-md-6 form-group">
							    <div class="input-group">
                                    <select name="item_id" id="item_id" class="form-control single-select req" style="width:70%;">
                                        <option value="">Select Item</option>
                                        <?php
                                            foreach($itemList as $row):
                                                $item_name = (!empty($row->item_code)) ? '['.$row->item_code.'] '.$row->item_name : $row->item_name;
                                                echo '<option value="'.$row->id.'">'.$item_name.'</option>';
                                            endforeach;
                                        ?>
                                    </select>
                                    <div class="input-group-append">
										<button type="button" class="btn waves-effect waves-light btn-success float-right loadData" title="Load Data">
											<i class="fas fa-sync-alt"></i> Load
										</button>
									</div>
								</div>
                                <div class="error item_id"></div>
							</div>
						</div>
					</div>
					<div class="card-body reportDiv" style="min-height:75vh">
						<div class="table-responsive">
                            <table id='reportTable' class="table table-bordered">
                                <thead class="thead-info" id="theadData">
                                    <tr class="text-center">
                                        <th colspan="14">Costing Report</th>
                                    </tr>
                                    <tr class="text-center">
                                        <th>#</th>
                                        <th>Job No</th>
                                        <th>Ok Qty</th>
                                        <th>Rej. Qty</th>
                                        <th>RM Cost</th>
                                        <th>Process Cost</th>
                                        <th>Total Cost</th>
                                        <th>CPP</th>
                                        <th>ACPP</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyData"> </tbody>
								
							</table>
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

    $(document).on('click', '.loadData', function(e) {
        $(".error").html("");
		var valid = 1;
		var item_id = $('#item_id').val();
		
		if($("#item_id").val() == ""){$(".item_id").html("Item is required.");valid=0;}else{$(".item_id").html("");}

        
        if (valid) {
            $.ajax({
                url: base_url + controller + '/getcostingReport',
                data: {item_id:item_id},
                type: "POST",
                dataType: 'json',
                success: function(data) {
                    $("#reportTable").DataTable().clear().destroy();
                    if(data.status == 1){
                        $("#tbodyData").html(data.tbody);
                    }else{
                        toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
                    }
                    reportTable();
                }
            });
        }
    });
});

function reportTable() {
    var reportTable = $('#reportTable').DataTable({
        responsive: true,
        scrollY: '55vh',
        scrollCollapse: true,
        "scrollX": true,
        "scrollCollapse": true,
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
                $(".loadData").trigger('click');
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