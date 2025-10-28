<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-5">
                                <h4 class="card-title pageHeader"><?=$pageHeader?></h4>
                            </div>       
                            <div class="col-md-3">   
                                <input type="date" name="from_date" id="from_date" class="form-control" value="<?=date('Y-m-01')?>" />
                                <div class="error fromDate"></div>
                            </div>     
                            <div class="col-md-4">  
                                <div class="input-group">
                                    <input type="date" name="to_date" id="to_date" class="form-control" value="<?=date('Y-m-d')?>" />
                                    <div class="input-group-append ml-2">
                                        <button type="button" class="btn waves-effect waves-light btn-success float-right loaddata" title="Load Data">
									        <i class="fas fa-sync-alt"></i> Load
								        </button>
                                    </div>
                                </div>
                                <div class="error toDate"></div>
                            </div>                  
                        </div>                                         
                    </div>
                    <div class="card-body reportDiv" style="min-height:75vh">
                        <div class="table-responsive">
                            <table id='reportTable' class="table table-bordered jpDataTable1 colSearch" data-ninput="[0]" data-srowposition="2">
								<thead class="thead-info" id="theadData">
                                    <tr class="text-center">
                                        <th colspan="14">Dispatch Plan </th>
                                        <th colspan="4">F PL 10 00/01.07.2021</th>
                                    </tr>
									<tr>
										<th style="min-width:25px;">#</th>
										<th style="min-width:80px;">PO Date</th>
										<th style="min-width:50px;">Party Code</th>
										<th style="min-width:100px;">Part Name</th>
										<th style="min-width:100px;">Dispatch date<br>(Challan)</th>
										<th style="min-width:100px;">Delivery date<br>(Job)</th>
										<th style="min-width:50px;">Price/Pcs in INR</th>
										<th style="min-width:80px;">Order QTY</th>
										<th style="min-width:50px;">Total Value</th>
										<th style="min-width:50px;">WIP QTY</th>
										<th style="min-width:80px;">Plan QTY</th>
										<th style="min-width:50px;">Plan Value</th>
										<th style="min-width:50px;">Dispatch QTY</th>
										<th style="min-width:80px;">Dispatch Value</th>
										<th style="min-width:50px;">Packing QTY</th>
										<th style="min-width:50px;">Packing VALUE</th>
										<th style="min-width:50px;">Pending QTY</th>
                                        <th style="min-width:50px;">Pending Value</th>
									</tr>
								</thead>
								<tbody id="tbodyData"></tbody>
                                <tfoot id="tfootData">
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tfoot>
							</table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>


<?php $this->load->view('includes/footer'); ?>
<?=$floatingMenu?>
<script>
$(document).ready(function(){
	reportTable();
    $(document).on('click','.loaddata',function(e){
		$(".error").html("");
		var valid = 1;
		var from_date = $('#from_date').val();
		var to_date = $('#to_date').val();
		if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
		if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
		if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}
		if(valid)
		{
            $.ajax({
                url: base_url + controller + '/getDispatchPlan',
                data: {from_date:from_date, to_date:to_date},
				type: "POST",
				dataType:'json',
				success:function(data){                    
					$('#reportTable').DataTable().clear().destroy();
					$("#tbodyData").html(data.tbody);
					$("#tfootData").html(data.tfoot);
					jpDataTable1 = reportTable('reportTable');
					setTimeout(function(){jpDataTable1.columns.adjust().draw();},200);
                }
            });
        }
    });   
});
function reportTable(tableId) {

	// Append Search Inputs
	var srowposition = $('#' + tableId).data('srowposition');
	if (!srowposition) {
		srowposition = 1;
	}
	var cloneFromTr = srowposition - 1;
	var headerRowCount = $('.colSearch thead tr').length;
	if (headerRowCount == srowposition) {
		$('.colSearch thead tr:eq(' + cloneFromTr + ')').clone(true).insertAfter('.colSearch thead tr:eq(' + cloneFromTr + ')');
		var ignorCols = $(".colSearch").data('ninput'); //.split(",");
		var lastIndex = $(".colSearch thead").find("tr:first th").length - 1;
		$(".colSearch thead tr:eq(" + srowposition + ") th").each(function(index, value) {
			if (jQuery.inArray(index, ignorCols) != -1) {
				$(this).html('');
			} else {
				if ((jQuery.inArray(-1, ignorCols) != -1) && index == lastIndex) {
					$(this).html('');
				} else {
					$(this).html('<input type="text" style="width:100%;"/>');
				}
			}
		});
	}

	var jpDataTable1 = $('.jpDataTable1').DataTable({
		"paging": true,
		responsive: true,
		"scrollY": '52vh',
		"scrollX": true,
		deferRender: true,
		scroller: true,
		destroy: true,
		'stateSave':false,
		"autoWidth": false,
		pageLength: 50,
		language: {
			search: ""
		},
		lengthMenu: [
			[10, 20, 25, 50, 75, 100, 250, 500, -1],
			['10 rows', '20 rows', '25 rows', '50 rows', '75 rows', '100 rows', '250 rows', '500 rows', 'Show All']
		],
		order: [],
		orderCellsTop: true,
		dom: "<'row'<'col-sm-7'B><'col-sm-5'f>>" + "<'row'<'col-sm-12't>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
		buttons: ['pageLength', 'copy', 'excel'],
		"fnInitComplete": function() {
			$('.dataTables_scrollBody').perfectScrollbar();
		},
		"fnDrawCallback": function(oSettings) {
			$('.dataTables_scrollBody').perfectScrollbar('destroy').perfectScrollbar();
		}
	});

	jpDataTable1.buttons().container().appendTo('#' + tableId + '_wrapper toolbar');
	$('.dataTables_filter').css("text-align", "left");
	$('#' + tableId + '_filter label').css("display", "block");
	$('.btn-group>.btn:first-child').css("border-top-right-radius", "0");
	$('.btn-group>.btn:first-child').css("border-bottom-right-radius", "0");
	$('#' + tableId + '_filter label').attr("id", "search-form");
	$('#' + tableId + '_filter .form-control-sm').css("width", "97%");
	$('#' + tableId + '_filter .form-control-sm').attr("placeholder", "Search.....");

	var state = jpDataTable1.state.loaded();
	$('.colSearch thead tr:eq(' + srowposition + ') th').each(function(i) {
		if (state) {
			var colSearch = state.columns[i].search;
			if (colSearch.search) {
				$('.colSearch thead tr:eq(' + srowposition + ') th:eq(' + i + ') input').val(colSearch.search);
			}
		}
		$('input', this).on('keyup change', function() {
			if (jpDataTable1.column(i).search() !== this.value) {
				jpDataTable1.column(i).search(this.value).draw();
			}
		});
	});

	$('.page-wrapper').resizer(function() {jpDataTable1.columns.adjust().draw();});
	return jpDataTable1;
}
</script>