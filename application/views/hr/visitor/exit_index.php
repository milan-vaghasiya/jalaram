
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
    <!-- Combo Select 
    <link href="<?=base_url()?>assets/extra-libs/comboSelect/combo.select.css" rel="stylesheet" type="text/css">-->
</head>

<body>

    <!-- loader -->
    <div id="loader"><img src="<?=base_url();?>assets/mobile_assets/img/loading-icon.png" alt="icon" class="loading-icon"></div>
    <!-- * loader -->
    <!-- App Capsule -->
    <div id="appCapsule" style="padding: 0px;">
           
        <div class="section mt-4 text-center">
            <h1>Enter Contact No</h1>
        </div>
        <div class="section mb-5 p-2">
            <form >
                <div class="card">
                    <div class="card-body pb-1">

                        <div class="form-group basic">
                            <div class="input-wrapper">
                                <!-- <label class="label" for="contact_no"> Contact No</label> -->
                                <input type="text" class="form-control" id="contact_no" placeholder="Your Contact Number">
                                <i class="clear-input">
                                    <ion-icon name="close-circle" role="img" class="md hydrated" aria-label="close circle"></ion-icon>
                                </i>
                                <div class="error contact_no"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-button-group transparent">
                    <button type="button" class="btn btn-primary btn-block square me-1 btn-lg exitBtn">EXIT</button>
                </div>

            </form>
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
        $(document).ready(function(){
            //$(".single-select").comboSelect();
			$(document).on('change','#dept_id',function(){
				var dept_id = $(this).val();
				$.ajax({
					url: base_url + 'api/v1/visitorLogs/getEmployeeList',
					data:{dept_id:dept_id},
					type: "POST",
					dataType:"json",
				}).done(function(data){
					$('#wtm').html('');$('#wtm').html(data.empList);
				});
			});
            $(document).on('click','.appointmentForm',function(){
                var contact_no = $("#contact_no").val();
                if(contact_no == ''){
                    $(".contact_no").html("Required");
                }else{
                    var sendData = {contact_no:contact_no};
                    var url =  base_url + '/api/v1/visitorLogs/appointmentForm/' + encodeURIComponent(window.btoa(JSON.stringify(sendData)));
                    window.open(url,'_self');
                }
                
				
			});

            $(document).on('click','.exitBtn',function(){
                var contact_no = $("#contact_no").val();
                if(contact_no == ''){
                    $(".contact_no").html("Required");
                }else{
                    var sendData = {contact_no:contact_no};
                    $.ajax({
                        url: base_url + 'api/v1/visitorLogs/exitCompany',
                        data:sendData,
                        type: "POST",
                        // processData:false,
                        // contentType:false,
                        dataType:"json",
                    }).done(function(data){
                        if(data.status===0){
                            $(".error").html("");
                            $.each( data.message, function( key, value ) {$("."+key).html(value);});
                        }else{
                            window.location.href = base_url + 'api/v1/visitorLogs/exitPage/' + data.id;
                        }
                                
                    });
                }
                
				
			});
        });
        function store(formId){
        	// var fd = $('#'+formId).serialize();
        	var form = $('#'+formId)[0];
        	var fd = new FormData(form);
        	$.ajax({
        		url: base_url + 'api/v1/visitorLogs/save',
        		data:fd,
        		type: "POST",
        		processData:false,
        		contentType:false,
        		dataType:"json",
        	}).done(function(data){
        		if(data.status===0){
        			$(".error").html("");
        			$.each( data.message, function( key, value ) {$("."+key).html(value);});
        		}else{
        			window.location.href = base_url + 'api/v1/visitorLogs/waitingPage/' + data.insert_id;
        		}
        				
        	});
        }
    </script>

</body>

</html>
