<!-- ============================================================== -->
<!-- Header -->
<!-- ============================================================== -->
    <?php $this->load->view('includes/header'); ?>
    <link href="<?=base_url()?>assets/libs/chartist/dist/chartist.min.css" rel="stylesheet">
    <link href="<?=base_url()?>assets/js/pages/chartist/chartist-init.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/extra-libs/c3/c3.min.css">
<!-- ============================================================== -->
<!-- End Header  -->
<!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Page wrapper  -->
        <!-- ============================================================== -->
        <div class="page-wrapper">
            <!-- ============================================================== -->
            <!-- End Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            <div class="container-fluid">
                <!-- ============================================================== -->
                <!-- Sales Summery -->
                <!-- ============================================================== -->
                <div class="row">
					<div class="col-lg-3 PRT d-none">
						<div class="card bg-orange text-white">
							<div class="card-body">
								<div id="cc1" class="carousel slide" data-ride="carousel">
									<div class="carousel-inner">
										<div class="carousel-item flex-column active">
											<div class="d-flex no-block align-items-center">
												<a href="JavaScript: void(0);"><i class="display-6 fas fa-user text-white" title="Present"></i></a>
												<div class="m-l-15 m-t-10">
													<h4 class="font-medium m-b-0">Present</h4>
                                                    <h5><?=!empty($presentEmp)?($presentEmp):'0'?></h5>

												</div>
											</div>
										</div>
										<div class="carousel-item flex-column">
											<div class="d-flex no-block align-items-center">
												<a href="JavaScript: void(0);"><i class="display-6 fas fa-user text-white" title="Absent"></i></a>
												<div class="m-l-15 m-t-10">
													<h4 class="font-medium m-b-0">Absent</h4>
                                                    <h5><?=!empty($absentEmp)?($absentEmp):'0'?></h5>
												</div>
											</div>
										</div>
                                        <div class="carousel-item flex-column">
											<div class="d-flex no-block align-items-center">
												<a href="JavaScript: void(0);"><i class="display-6 fas fa-user text-white" title="Absent"></i></a>
												<div class="m-l-15 m-t-10">
													<h4 class="font-medium m-b-0">Late</h4>
                                                    <h5><?=!empty($lateEmp)?($lateEmp):'0'?></h5>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-3 TSA d-none">
						<div class="card bg-success text-white">
							<div class="card-body">
								<div id="myCarousel22" class="carousel slide" data-ride="carousel">
									<div class="carousel-inner">
										<div class="carousel-item flex-column active">
											<div class="d-flex no-block align-items-center">
												<a href="JavaScript: void(0);"><i class="display-6 icon-Receipt-3 text-white" title="BTC"></i></a>
												<div class="m-l-15 m-t-10">
													<h4 class="font-medium m-b-0">Today's Sales</h4>
                                                    <h5><?=!empty($tdSales->net_amount)?($tdSales->net_amount):'0'?></h5>
												</div>
											</div>
										</div>
										<div class="carousel-item flex-column">
											<div class="d-flex no-block align-items-center">
												<a href="JavaScript: void(0);"><i class="display-6 icon-Receipt-3 text-white" title="BTC"></i></a>
												<div class="m-l-15 m-t-10">
													<h4 class="font-medium m-b-0">Current Month</h4>
                                                    <h5><?=!empty($cmSales->net_amount)?numberFormatIndia($cmSales->net_amount):'0'?></h5>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-3 PSE d-none">
						<div class="card bg-cyan text-white">
							<div class="card-body">
								<div id="myCarousel45" class="carousel slide" data-ride="carousel">
									<div class="carousel-inner">
										<div class="carousel-item flex-column active">
											<div class="d-flex no-block align-items-center">
												<a href="JavaScript: void(0);"><i class="display-6 icon-Shopping-Basket text-white" title="BTC"></i></a>
												<div class="m-l-15 m-t-10">
													<h4 class="font-medium m-b-0">Pending Enquiry</h4>
													<h5><?=!empty($pSeCount)?$pSeCount->total_count:0?></h5>
												</div>
											</div>
										</div>
										<!-- <div class="carousel-item flex-column">
											<div class="d-flex no-block align-items-center">
												<a href="JavaScript: void(0);"><i class="display-6 icon-Shopping-Basket text-white" title="BTC"></i></a>
												<div class="m-l-15 m-t-10">
													<h4 class="font-medium m-b-0">Pending RFQ</h4>
                                                    <h5></h5>

												</div>
											</div>
										</div> -->
                                        <div class="carousel-item flex-column">
											<div class="d-flex no-block align-items-center">
												<a href="JavaScript: void(0);"><i class="display-6 icon-Shopping-Basket text-white" title="BTC"></i></a>
												<div class="m-l-15 m-t-10">
													<h4 class="font-medium m-b-0">Total RFQ Day/Month</h4>
                                                    <h5><?=((!empty($cmSQ) && ($tdSQ))?$tdSQ->total_count. '/' .$cmSQ->total_count:0)?></h5>

												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-3 PSO d-none">
						<div class="card bg-dark text-white">
							<div class="card-body">
								<div id="myCarousel33" class="carousel slide" data-ride="carousel">
									<div class="carousel-inner">
										<div class="carousel-item flex-column active">
											<div class="d-flex no-block align-items-center">
												<a href="JavaScript: void(0);"><i class="display-6 fas fa-arrow-left text-white" title="BTC"></i></a>
												<div class="m-l-15 m-t-10">
													<h4 class="font-medium m-b-0">Current Month S.O.</h4>
                                                    <h5><?=!empty($approvedSO)?$approvedSO->total_so:0?></h5>

												</div>
											</div>
										</div>
										<div class="carousel-item flex-column">
											<div class="d-flex no-block align-items-center">
												<a href="JavaScript: void(0);"><i class="display-6 fas fa-arrow-left text-white" title="BTC"></i></a>
												<div class="m-l-15 m-t-10">
													<h4 class="font-medium m-b-0">Unapproved Sales Order</h4>
                                                    <h5><?=!empty($unapprovedSO)?$unapprovedSO->total_so:0?></h5>

												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>				
				
				<!-- ============================================================== -->
                <!-- Sales Order, Sales and Stock / Exchange -->
                <!-- ============================================================== -->
				<div class="row">
                    <div class="col-lg-8 DS d-none">
						<div class="card">
							<div class="card-header bg-dark-blue text-white">
								<div class=" card-title"><h4 class="card-title">Due Sales <span class="badge badge-warning font-bold float-right">Total Due : <?= count($soData) ?></span></h4></div>
                            </div>
							<div class="scrollable" style="height:350px;">
								<table class="table email-table no-wrap table-hover v-middle">
    								<?php
        								if(!empty($soData)){
    										foreach($soData as $row){
    											$pendingQty= floatVal($row->qty - $row->dispatch_qty);
    											$workDone = round((($pendingQty * 100)/$row->qty),2);
    											$highlight = ($workDone > 50) ? 'bg-peach' : 'bg-light-blue';
    											?>
    											
                                                    <tr class="unread">
                                                        <th class="text-center <?=$highlight?>" rowspan="2"><?= $workDone ?>%<br><small><?=formatDate($row->cod_date,'d M Y')?></small></th>
                                                        <td class="user-name">
                                                            <h6 class="m-b-0"><?= $row->trans_number.' ['.$row->party_code.']'?></h6>
                                                        </td>
                                                        <td class="max-texts"><span class="label label-info m-r-10" style="min-width:80px;">Order : <?= floatVal($row->qty) ?></span></td> 
                                                        <td class="max-texts"><span class="label label-success m-r-10" style="min-width:80px;">Dispatch : <?=floatVal($row->dispatch_qty) ?></span></td>
                                                        <td class="max-texts"><span class="label label-danger m-r-10" style="min-width:80px;">Pending : <?= $pendingQty ?></span></td>
                                                    </tr>
                                                    <tr class="unread">
                                                        <td colspan="4" style="border-top:0px;"><small><?=$row->item_code?></small></td> 
                                                    </tr>
    											<?php
    										}
    								    }
    								?>
                                </table>
							</div>
						</div>
					</div>
                    <div class="col-lg-4 TBL d-none">
						<div class="card">
							<div class="card-header bg-dark-blue text-white">
                                <div class="row card-title">
                                    <div class="col-lg-12 text-center"><h4>List of today's birthday</h4></div>
                                </div>
                            </div>
                            <div class="card-body" style="padding:0.4rem;<?=((!empty($todayBirthdayList)) ? 'background: #171618;' : '')?>">
                                <div class="sales_track scrollable" style="height:330px;">
                                    <?php
                                        if(!empty($todayBirthdayList)):
                                            foreach($todayBirthdayList as $row):
                                    ?>
                                                <div class="col-md-12 row m-b-2 bd_div" style="margin:0.25rem 0.05rem;padding:0.5rem;width: 99%;">
                                                    <span class="border-span"> </span>
                                                	<span class="border-span"> </span>
                                                	<span class="border-span"> </span>
                                                	<span class="border-span"> </span>
                                                    <div class="col-md-3 text-center">
                                                        <img src="<?=base_url()?>assets/images/bday.png" style="width:80%;">
                                                    </div>
                                                    <div class="col-md-9">
                                                        <h5 class="fs-15 lightning_text"><?=$row->emp_name?></h5>
                                                        <small><?=$row->emp_dsg?> (<?=$row->dept_name?>)</small>
                                                    </div>
                                                </div>
                                    <?php
                                            endforeach;
                                        else:
                                    ?>
                                        <div class="text-center">
                                            <img src="<?=base_url()?>assets/images/nbd.png" style="width:75%;">
                                        </div>
                                    <?php    
                                        endif;
                                    ?>
								</div>
                            </div>
						</div>
                    </div>
                </div>
                <!-- ============================================================== -->
                <!-- Task, Feeds -->
                <!-- ============================================================== -->
              
            </div>
            <!-- ============================================================== -->
            <!-- Trade history / Exchange -->
            <!-- ============================================================== -->
        </div>
        <!-- ============================================================== -->
        <!-- End Container fluid  -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- footer -->
        <!-- ============================================================== -->
<?php $this->load->view('includes/footer'); ?>

<script src="<?=base_url()?>assets/libs/chartist/dist/chartist.min.js"></script>
<script src="<?=base_url()?>assets/libs/chartist/dist/chartist-plugin-legend.js"></script>
<script src="<?=base_url()?>assets/js/pages/chartist/chartist-plugin-tooltip.js"></script>
<script src="<?=base_url()?>assets/js/pages/chartist/chartist-init.js"></script>
<script src="<?=base_url()?>assets/js/pages/c3-chart/bar-pie/c3-stacked-column.js"></script>
<script src="<?=base_url()?>assets/js/pages/dashboards/dashboard3.js"></script>

<script src="<?=base_url()?>assets/libs/raphael/raphael.min.js"></script>
<script src="<?=base_url()?>assets/libs/morris.js/morris.min.js"></script>
        <!-- ============================================================== -->
        <!-- End footer -->
        <!-- ============================================================== -->
<script>
	$(document).ready(function(){
		var wp = '<?=(!empty($widgetPermission) ? $widgetPermission->widget_class : '')?>';
		var wpArr = wp.split(',');
		$.each(wpArr , function(index, val) {
		    $('.'+val).removeClass('d-none');
		    $('.'+val).addClass('d-block');
        });
	});
 
</script>        