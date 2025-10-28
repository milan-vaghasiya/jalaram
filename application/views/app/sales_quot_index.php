<?php $this->load->view('app/includes/header'); ?>
<!-- Header -->
<header class="header">
	<div class="main-bar bg-primary-2">
		<div class="container">
			<div class="header-content">
				<div class="left-content">
					<a href="javascript:void(0);" class="back-btn">
						<svg height="512" viewBox="0 0 486.65 486.65" width="512"><path d="m202.114 444.648c-8.01-.114-15.65-3.388-21.257-9.11l-171.875-171.572c-11.907-11.81-11.986-31.037-.176-42.945.058-.059.117-.118.176-.176l171.876-171.571c12.738-10.909 31.908-9.426 42.817 3.313 9.736 11.369 9.736 28.136 0 39.504l-150.315 150.315 151.833 150.315c11.774 11.844 11.774 30.973 0 42.817-6.045 6.184-14.439 9.498-23.079 9.11z"></path><path d="m456.283 272.773h-425.133c-16.771 0-30.367-13.596-30.367-30.367s13.596-30.367 30.367-30.367h425.133c16.771 0 30.367 13.596 30.367 30.367s-13.596 30.367-30.367 30.367z"></path>
						</svg>
					</a>
					<h5 class="title mb-0 text-nowrap"><?=$headData->pageTitle?></h5>
				</div>
				<div class="mid-content"> </div>
				<div class="right-content headerSearch">
					<div class="jpsearch" id="qs1">
						<input type="text" class="input quicksearch qs1" placeholder="Search Here ..." />
						<button class="search-btn"><i class="fas fa-search"></i></button>
					</div>
				</div>
			</div>
		</div>
	</div>
</header>
<!-- Header -->
	
    <!-- Page Content -->
    <div class="page-content">
        <div class="content-inner pt-0">
			<div class="container bottom-content">
                <div class="dz-tab style-4">
    				<div class="tab-slide-effect">
    					<ul class="nav nav-tabs"  role="tablist" >
    						<li class="tab-active-indicator " style="width: 108.391px; transform: translateX(177.625px);"></li>
    						<li class="nav-item   active" role="presentation">
    							<button class="nav-link buttonFilter active" id="home-tab" data-status="0"  data-filter=".pending_req" data-bs-toggle="tab" data-bs-target="#reqList" type="button" role="tab" aria-controls="home-tab-pane" aria-selected="false" tabindex="-1">Pending</button>
    						</li>
    						<li class="nav-item " role="presentation">
    							<button class="nav-link buttonFilter" id="profile-tab" data-status="1" data-bs-toggle="tab" data-filter=".issued_req" data-bs-target="#reqList" type="button" role="tab" aria-controls="profile-tab-pane" aria-selected="false" tabindex="-1">Confirmed</button>
    						</li>
                            
    					</ul>
    				</div>  
					  
    				<div class="tab-content px-0 py-0 list-grid" id="myTabContent1"  data-isotope='{ "itemSelector": ".listItem" }'>
						<ul class="dz-list message-list" >
							<?=!empty($salesQuotList)?$salesQuotList:''?>
						</ul>
    				</div>
			    </div>
			</div>    
		</div>
    </div>    
    <!-- Page Content End-->
</div>  
<?php $this->load->view('app/includes/bottom_menu'); ?>
<?php $this->load->view('app/includes/footer'); ?>

<script src="<?=base_url()?>assets/plugins/isotop/isotope.pkgd.min.js"></script>
<script>
var qsRegex;
var isoOptions ={};
var $grid = '';
$(document).ready(function(){

    initISOTOP();
	var $qs = $('.quicksearch').keyup( debounce( function() {qsRegex = new RegExp( $qs.val(), 'gi' );$grid.isotope();}, 200 ) );

	$(document).on( 'click', '.buttonFilter', function() {
		var status = $(this).data('status');
		$(".message-list").html("");	
		$.ajax({
		url: base_url  + 'app/salesQuotation/getSalesQuotationData',
		data:{'status':status},
		type: "POST",
		dataType:"json",
	}).done(function(response){
			$('.list-grid').isotope('destroy');
			$(".message-list").html(response.html);	
			initISOTOP();
							
		});
	});

	$(document).on('click','.confirmQuotation',function(){
        var functionName = $(this).data("function");
        var modalId = $(this).data('modal_id');
        var button = $(this).data('button');
		var title = $(this).data('form_title');
		var formId = functionName;

        var quote_id = $(this).data('id');
		var customerId = $(this).data('customer_id');
		var partyName = $(this).data('party');
		var quote_no = $(this).data('quote_no');
		var quotation_date = $(this).data('quotation_date');
		
        $.ajax({ 
            type: "POST",   
            url: base_url + 'app/salesQuotation/' + functionName,   
            data: {quote_id:quote_id}
        }).done(function(response){
			$('#offcanvasRight .cTitle').html(title);
			$('#offcanvasRight .cBody').html(response);
			$('#offcanvasRight').offcanvas('show');

            $("#customer_id").val(customerId);
            $("#party_name").html(partyName);
            $("#quote_no").html(quote_no);
            $("#quotation_date").html(quotation_date);
            $("#id").val(quote_id);$("#quote_id").val(quote_id);
            $(".modal-lg").attr("style","max-width: 70% !important;");
            $('.floatOnly').keypress(function(event) {
                if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
                    event.preventDefault();
                }
            });
        });
    });

	$(document).on("click",".itemCheckCQ",function(){
        var id = $(this).data('rowid');
        if($("#md_checkbox"+id).attr('check') == "checked"){
            $("#md_checkbox"+id).attr('check','');
            $("#md_checkbox"+id).removeAttr('checked');
            $("#qty"+id).attr('disabled','disabled');
            $("#unit_id"+id).attr('disabled','disabled');
            $("#price"+id).attr('disabled','disabled');
            $("#item_id"+id).attr('disabled','disabled');
            $("#automotive"+id).attr('disabled','disabled');
            $("#item_name"+id).attr('disabled','disabled');
            $("#inq_trans_id"+id).attr('disabled','disabled');
            $("#confirm_price"+id).attr('disabled','disabled');
            $("#trans_id"+id).attr('disabled','disabled');
            $("#drg_rev_no"+id).attr('disabled','disabled');
            $("#rev_no"+id).attr('disabled','disabled');

        }else{
            $("#md_checkbox"+id).attr('check','checked');
            $("#qty"+id).removeAttr('disabled');
            $("#price"+id).removeAttr('disabled');
            $("#unit_id"+id).removeAttr('disabled');
            $("#automotive"+id).removeAttr('disabled');
            $("#item_id"+id).removeAttr('disabled');
            $("#item_name"+id).removeAttr('disabled');
            $("#inq_trans_id"+id).removeAttr('disabled');
            $("#confirm_price"+id).removeAttr('disabled');
            $("#trans_id"+id).removeAttr('disabled');
            $("#drg_rev_no"+id).removeAttr('disabled');
            $("#rev_no"+id).removeAttr('disabled');
        }
    });
});

function searchItems(ele){
	console.log($(ele).val());
}

function debounce( fn, threshold ) {
  var timeout;
  threshold = threshold || 100;
  return function debounced() {
	clearTimeout( timeout );
	var args = arguments;
	var _this = this;
	
	function delayed() {fn.apply( _this, args );}
	timeout = setTimeout( delayed, threshold );
  };
}

function initISOTOP(){
    var isoOptions = {
		itemSelector: '.listItem',
		layoutMode: 'fitRows',
		filter: function() {return qsRegex ? $(this).text().match( qsRegex ) : true;}
	};
    $('.listItem').css('position', 'static');
	// init isotope
	$grid = $('.list-grid').isotope( isoOptions );
}

function responseFunction(response){
    if(response.status==0){
		Swal.fire( 'Sorry...!', response.message, 'error' );
	}else{
		Swal.fire({
            title: "Success",
            text: response.message,
            icon: "success",
            showCancelButton: false,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ok!"
        }).then((result) => {
            window.location = base_url + 'app/salesQuotation';
        });

	}	
}
</script>