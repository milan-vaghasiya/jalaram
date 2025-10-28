<?php $this->load->view('includes/header'); ?>
<link href="<?=base_url()?>assets/libs/chartist/dist/chartist.min.css" rel="stylesheet">
<link href="<?=base_url()?>assets/js/pages/chartist/chartist-init.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/extra-libs/c3/c3.min.css">
<div class="page-wrapper">
	<div class="container-fluid ssm-dashboard">
		<div class="row">
			<div class="col-lg-3 col-md-6">
				<div class="card">
					<div class="card-body p-2">
						<div class="d-flex align-items-center">
							<div>
								<span>OEE</span>
								<h4>78.60%</h4>
							</div>
							<div class="ml-auto">
								<div class="gaugejs-box">
									<canvas id="foo" class="gaugejs" height="60" width="100">guage</canvas>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-3 col-md-6">
				<div class="card">
					<div class="card-body p-2">
						<div class="d-flex align-items-center">
							<div>
								<span>Performance</span>
								<h4>80.30%</h4>
							</div>
							<div class="ml-auto">
								<div class="gaugejs-box">
									<canvas id="foo2" class="gaugejs" height="60" width="100">guage</canvas>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-3 col-md-6">
				<div class="card">
					<div class="card-body p-2">
						<div class="d-flex align-items-center">
							<div>
								<span>Availability</span>
								<h4>90.50%</h4>
							</div>
							<div class="ml-auto">
								<div class="gaugejs-box">
									<canvas id="foo3" class="gaugejs" height="60" width="100">guage</canvas>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-3 col-md-6">
				<div class="card">
					<div class="card-body p-2">
						<div class="d-flex align-items-center">
							<div>
								<span>OEE</span>
								<h4>78.60%</h4>
							</div>
							<div class="ml-auto">
								<div class="gaugejs-box">
									<canvas id="foo4" class="gaugejs" height="60" width="100">guage</canvas>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<div class="row">
			<?php
				if(!empty($logData)){
					$class="ssm-success";
					foreach($logData as $row){
						$time = date("Y-m-d H:i:s",strtotime($row->created_at.' +'.$row->idle_time));
						if(date("Y-m-d H:i:s") > $time){
							$class="ssm-warning";
						}
						?>
						<div class="col-md-3">
							<div class="ssm-card <?=$class?>">
								<div class="ssm-card-header"><a style=" color:inherit;" target="_blank" href="<?=base_url('dashboard/ssmJobDetail/'.$row->job_no.'/'.$row->device_no)?>"><h3 class="title"><?=$row->machine_code?></h3></a></div>
								<div class="ssm-card-content">
									<ul>
										<li><div class="inner-title">Part Code</div><div><?=$row->item_code?></div></li>
										<li><div class="inner-title">Part Count</div><div><?=$row->job_part_count?></div></li>
										<li><div class="inner-title">Job No.</div><div><?=$row->job_no?></div></li>
										<li><div class="inner-title">Operator</div><div><?=$row->created_by?></div></li>
										<li class="ssm-time"><div><?=$row->today_production_time?></div><div><?=$row->today_idle_item?></div></li>
									</ul>
								</div>
							</div>
						</div>
						<?php
					}
				}
			?>
			
			<!-- <div class="col-md-3">
				<div class="ssm-card ssm-info">
					<div class="ssm-card-header"><h3 class="title">CNC-07</h3></div>
					<div class="ssm-card-content">
						<ul>
							<li><div class="inner-title">Part Code</div><div>AA208</div></li>
							<li><div class="inner-title">Part Count</div><div>254</div></li>
							<li><div class="inner-title">Operator</div><div>10220</div></li>
							<li class="ssm-time"><div>14:00:00</div><div>02:00:00</div></li>
						</ul>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="ssm-card ssm-primary">
					<div class="ssm-card-header"><h3 class="title">CNC-07</h3></div>
					<div class="ssm-card-content">
						<ul>
							<li><div class="inner-title">Part Code</div><div>AA208</div></li>
							<li><div class="inner-title">Part Count</div><div>254</div></li>
							<li><div class="inner-title">Operator</div><div>10220</div></li>
							<li class="ssm-time"><div>14:00:00</div><div>02:00:00</div></li>
						</ul>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="ssm-card ssm-success">
					<div class="ssm-card-header"><h3 class="title">CNC-07</h3></div>
					<div class="ssm-card-content">
						<ul>
							<li><div class="inner-title">Part Code</div><div>AA208</div></li>
							<li><div class="inner-title">Part Count</div><div>254</div></li>
							<li><div class="inner-title">Operator</div><div>10220</div></li>
							<li class="ssm-time"><div>14:00:00</div><div>02:00:00</div></li>
						</ul>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="ssm-card ssm-warning">
					<div class="ssm-card-header"><h3 class="title">CNC-07</h3></div>
					<div class="ssm-card-content">
						<ul>
							<li><div class="inner-title">Part Code</div><div>AA208</div></li>
							<li><div class="inner-title">Part Count</div><div>254</div></li>
							<li><div class="inner-title">Operator</div><div>10220</div></li>
							<li class="ssm-time"><div>14:00:00</div><div>02:00:00</div></li>
						</ul>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="ssm-card ssm-success">
					<div class="ssm-card-header"><h3 class="title">CNC-07</h3></div>
					<div class="ssm-card-content">
						<ul>
							<li><div class="inner-title">Part Code</div><div>AA208</div></li>
							<li><div class="inner-title">Part Count</div><div>254</div></li>
							<li><div class="inner-title">Operator</div><div>10220</div></li>
							<li class="ssm-time"><div>14:00:00</div><div>02:00:00</div></li>
						</ul>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="ssm-card ssm-danger">
					<div class="ssm-card-header"><h3 class="title">CNC-07</h3></div>
					<div class="ssm-card-content">
						<ul>
							<li><div class="inner-title">Part Code</div><div>AA208</div></li>
							<li><div class="inner-title">Part Count</div><div>254</div></li>
							<li><div class="inner-title">Operator</div><div>10220</div></li>
							<li class="ssm-time"><div>14:00:00</div><div>02:00:00</div></li>
						</ul>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="ssm-card ssm-success">
					<div class="ssm-card-header"><h3 class="title">CNC-07</h3></div>
					<div class="ssm-card-content">
						<ul>
							<li><div class="inner-title">Part Code</div><div>AA208</div></li>
							<li><div class="inner-title">Part Count</div><div>254</div></li>
							<li><div class="inner-title">Operator</div><div>10220</div></li>
							<li class="ssm-time"><div>14:00:00</div><div>02:00:00</div></li>
						</ul>
					</div>
				</div>
			</div> -->
		</div>
	</div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script src="<?=base_url()?>assets/libs/chartist/dist/chartist.min.js"></script>
<script src="<?=base_url()?>assets/libs/chartist/dist/chartist-plugin-legend.js"></script>
<script src="<?=base_url()?>assets/js/pages/chartist/chartist-plugin-tooltip.js"></script>
<script src="<?=base_url()?>assets/js/pages/chartist/chartist-init.js"></script>
<script src="<?=base_url()?>assets/libs/gaugeJS/dist/gauge.min.js"></script>
<script src="<?=base_url()?>assets/js/pages/widget/gauge-charts.js"></script>
<script src="<?=base_url()?>assets/js/pages/dashboards/dashboard3.js"></script>
<script>
	$(document).ready(function(){
		// ct-animation-chart
		var ssm_legendDiv = document.getElementById('ssm_legendDiv');
		var chart = new Chartist.Line('.ssm-oee', {
			labels: ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10'],
			series: [
						{ "name": "Order", "data": [15, 4, 6, 8, 5, 4, 6, 2, 3, 3] },
						{ "name": "Sales", "data": [4, 5, 3, 7, 3, 5, 5, 3, 4, 4] },
						{ "name": "Stock", "data": [1, 3, 4, 5, 6, 10, 3, 4, 5, 6] }
					]
			// series: [ [15, 4, 6, 8, 5, 4, 6, 2, 3, 3], [4, 5, 3, 7, 3, 5, 5, 3, 4, 4], [1, 3, 4, 5, 6, 10, 3, 4, 5, 6] ]
		},
		{
			chartPadding: {right: 20, left: 0, top: 20, bottom: 0 },
			fullWidth: true,
			low: 0,
			showArea: true,
			plugins: [Chartist.plugins.legend({ position : ssm_legendDiv })]
		});
		/*
		var opts = {
			angle: 0, // The span of the gauge arc
			lineWidth: 0.32, // The line thickness
			radiusScale: 1, // Relative radius
			pointer: {
			  length: 0.44, // // Relative to gauge radius
			  strokeWidth: 0.04, // The thickness
			  color: '#000000' // Fill color
			},
			limitMax: false, // If false, the max value of the gauge will be updated if value surpass max
			limitMin: false, // If true, the min value of the gauge will be fixed unless you set it manually
			colorStart: '#24d2b5', // Colors
			colorStop: '#24d2b5', // just experiment with them
			strokeColor: '#E0E0E0', // to see which ones work best for you
			generateGradient: true,

			highDpiSupport: true // High resolution support
		};
		var og_id = document.getElementById('oee_gauge');
		var oee_gauge = new Gauge(og_id).setOptions(opts);
		oee_gauge.maxValue = 100;
		oee_gauge.setMinValue(0);
		oee_gauge.animationSpeed = 45;
		oee_gauge.set(50);
		var av_id = document.getElementById('avail_gauge');
		var avail_gauge = new Gauge(av_id).setOptions(opts);
		avail_gauge.maxValue = 100;
		avail_gauge.setMinValue(0);
		avail_gauge.animationSpeed = 45;
		avail_gauge.set(50);
		var prf_id = document.getElementById('perf_gauge');
		var perf_gauge = new Gauge(prf_id).setOptions(opts);
		perf_gauge.maxValue = 100;
		perf_gauge.setMinValue(0);
		perf_gauge.animationSpeed = 45;
		perf_gauge.set(50);
		var qc_id = document.getElementById('quality_gauge');
		var quality_gauge = new Gauge(qc_id).setOptions(opts);
		quality_gauge.maxValue = 100;
		quality_gauge.setMinValue(0);
		quality_gauge.animationSpeed = 45;
		quality_gauge.set(50);*/
	});
</script>