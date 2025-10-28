
<!doctype html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#000000">
    <title>JAY JALARAM INDUSTRIES</title>
    <meta name="description" content="Finapp HTML Mobile Template">
    <meta name="keywords" content="bootstrap, wallet, banking, fintech mobile template, cordova, phonegap, mobile, html, responsive" />
    <link rel="icon" type="image/png" href="<?=base_url();?>assets/mobile_assets/img/favicon.png" sizes="32x32">
    <link rel="apple-touch-icon" sizes="180x180" href="<?=base_url();?>assets/mobile_assets/img/icon/192x192.png">
    <link rel="stylesheet" href="<?=base_url();?>assets/mobile_assets/css/style.css">
    <link rel="stylesheet" href="<?=base_url();?>assets/mobile_assets/css/panda_thanks.css">
    <!-- Combo Select 
    <link href="<?=base_url()?>assets/extra-libs/comboSelect/combo.select.css" rel="stylesheet" type="text/css">-->
</head>

<body>

    <!-- loader -->
    <div id="loader"><img src="<?=base_url();?>assets/mobile_assets/img/loading-icon.png" alt="icon" class="loading-icon"></div>
    <!-- * loader -->
	<div class="text-center"><img src="<?=base_url()?>assets/mobile_assets/img/logo.png" alt="icon" style="width:60%;padding-top:10px;"></div>
	<!--<div class="text-center mt-1" style="background: #005da9;color: #FFFFFF;">Please wait...Your request sent Successfully</div>-->
    <!-- App Capsule -->
    <div id="appCapsule" style="padding: 0px;">
        <div class="section">
    		<div class="ctn">
    			<div class="shadow"></div>
    
    			<div class="panda">
    				<div class="leg left-leg"></div>
    				<div class="leg right-leg"></div>
    
    				<div class="arm left-arm"></div>
    				<div class="arm right-arm"></div>
    
    				<div class="body">
    					<div class="stripe"><span class="panda_thanks">Thanks<br>For</span></div>
    					<span class="visit">Visit</span>
    				</div>
    				<div class="head">
    					<div class="ear"></div>
    					<div class="face">
    						<div class="eye left-eye"><div class="pupil"></div></div>
    						<div class="eye right-eye"><div class="pupil"></div></div>
    						<div class="nose"></div>
    						<div class="mouth"></div>
    					</div>
    				</div>
    			</div>
    		</div>
    	</div>
    </div>
    <!-- * App Capsule -->


    <!-- ========= JS Files =========  -->
     <!-- Bootstrap -->
    <script src="<?=base_url();?>assets/mobile_assets/js/lib/bootstrap.bundle.min.js"></script>
    <!-- Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <!-- Splide -->
    <script src="<?=base_url();?>assets/mobile_assets/js/plugins/splide/splide.min.js"></script>
    <!-- Base Js File -->
    <script src="<?=base_url();?>assets/mobile_assets/js/base.js"></script>
    <!-- Combo Select 
    <script src="<?=base_url()?>assets/extra-libs/comboSelect/jquery.combo.select.js"></script> -->
    <script src="<?=base_url()?>assets/libs/jquery/dist/jquery.min.js"></script>
    <script>
        var base_url = '<?=base_url();?>';
        var request_id = '<?=$request_id?>';
        $(document).ready(function(){
			// Panda Eye move
			$(document).on( "mousemove", function( event ) {
			  var dw = $(document).width() / 15;
			  var dh = $(document).height() / 15;
			  var x = event.pageX/ dw;
			  var y = event.pageY/ dh;
			  $('.eye-ball').css({
				width : x,
				height : y
			  });
			});
			
        });
    </script>

</body>

</html>
