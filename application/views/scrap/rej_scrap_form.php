<form id="scrap_form">
	<div class="col-md-12 col-6">
		<div class="row">
			<div class="col-md-6 form-group">
				<label for="ref_date">Date</label>
				<input type="date" id="ref_date" name="ref_date" class="form-control" value="<?= date("Y-m-d") ?>" max="<?= date("Y-m-d") ?>" readonly />
			</div>
			<div class="col-md-6 form-group">
				<label for="job_card_id">Jobcard</label>
				<input type="hidden" id="item_id" name="item_id">
				<select id="job_card_id" name="job_card_id" class="form-control model-select2 req">
					<option value="">Select Jobcard</option>
					<?php foreach ($locationList as $row) {
						if ($row->qty > 0) {
					?>
						<option value="<?= $row->job_card_id ?>" data-item_id="<?= $row->product_id ?>"><?= getPrefixNumber($row->job_prefix, $row->job_no). ' [ ' . $row->item_code . ' ]' ?></option>
					<?php }
					} ?>
				</select>
			</div>
			<div class="col-md-12 form-group">
				<div class="error general_error"></div>
				<div class="table-responsive">
					<table class="table table-bordered">
						<thead class="thead-info" id="theadData">
							<tr>
								<th style="width:10%;">#</th>
								<th style="width:70%;">Item Name</th>
								<th style="width:20%;">BOM Qty.</th>
							</tr>
						</thead>
						<tbody id="materialData">
							<tr>
								<td class="text-center" colspan="3">No Data Found.</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="col-md-12 form-group">
				<div class="error general_error"></div>
				<div class="error qty"></div>
				<div class="table-responsive">
					<table id='reportTable' class="table table-bordered jpDataTable1 colSearch">
						<thead class="thead-info" id="theadData">
							<tr>
								<th>#</th>
								<th>Batch</th>
								<th>Process</th>
								<th>Rej Reason</th>
								<th>Rej Stage</th>
								<th>Rej Belongs To</th>
								<th>Rej Qty</th>
								<th>Scrap Qty</th>
								<th>Supplier Rej. Qty</th>
								<th>OK Qty</th>
							</tr>
						</thead>
						<tbody id="batchData" class="scroll-tbody scrollable">
							<tr>
								<td class="text-center" colspan="10">No Data Found.</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</form>
<script src="<?php echo base_url(); ?>assets/js/custom/master-form.js?v=<?= time() ?>"></script>

<script>
	$(document).ready(function() {
		$('.model-select2').select2({
			dropdownParent: $('.model-select2').parent()
		});

		$("#job_card_id").change(function(e) {
		    e.stopImmediatePropagation();e.preventDefault();
		    
			var location_id = $(this).val();
			var item_id = $("#job_card_id :selected").data('item_id');

			$("#item_id").val(item_id);
			$("#batchData").html("");
			if (location_id != '') {
				$.ajax({
					url: base_url + controller + '/getRejectionBatchList',
					type: 'post',
					data: {
						job_card_id: location_id
					},
					dataType: 'json',
					success: function(data) {
						$('#reportTable').DataTable().clear().destroy();
						$("#batchData").html("");
						$("#batchData").html(data.tbody);
						$("#materialData").html("");
						$("#materialData").html(data.bomData)
						reportTable('reportTable');
					}
				});
			}
		});
		$(document).on('keyup change', ".batchQty", function() {

			var id = $(this).data('rowid');
			var pending_qty = $(this).data('pending_qty');
			var scrap_qty = $("#scrapQty"+id).val();
			var ok_qty = $("#okQty"+id).val();
			var supplier_rej_qty = $("#supplierRej"+id).val();
			
			$(".batch_qty" + id).html("");
			var totalQty = parseFloat(scrap_qty) + parseFloat(ok_qty) + parseFloat(supplier_rej_qty);
			if (totalQty > parseFloat(pending_qty)) {
				$(this).val(0);
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
			'stateSave': false,
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

		$('.page-wrapper').resizer(function() {
			jpDataTable1.columns.adjust().draw();
		});
		return jpDataTable1;
	}
</script>