<!DOCTYPE html>
<html dir="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="<?=base_url()?>assets/images/favicon.png">
    <title>Login - <?=(!empty(SITENAME))?SITENAME:""?></title>
    <!-- Custom CSS -->
    <link href="<?=base_url()?>assets/css/style.css" rel="stylesheet">
    <link href="<?=base_url()?>assets/css/jp_helper.css" rel="stylesheet">
</head>

<body>
    <div class="main-wrapper">
        <!-- Preloader - style you can find in spinners.css -->
        <div class="preloader">
            <div class="lds-ripple">
                <div class="lds-pos"></div>
                <div class="lds-pos"></div>
            </div>
        </div>

        <!-- Login box.scss -->
        <div class="auth-wrapper d-flex no-block justify-content-center align-items-center" style="background:url(<?=base_url()?>assets/images/background/login_bg1.png) no-repeat center center;background-size:100% 100%;">
            <div class="auth-box on-sidebar">
                <div id="loginform">
                    <div class="logo">
                        <span class="db"><img src="<?=base_url()?>assets/images/logo.png" alt="logo" width="80%" /></span>
                        <h5 class="font-medium bg-light-grey  pad-5" style="margin:10px -20px 20px -20px;">Verify OTP</h5>
                    </div>
                    <!-- Form -->
                    <div class="row">
                        <div class="col-12">
                            <form class="form-horizontal m-t-20" id="otpform" action="<?=base_url('login/verifyOTP');?>" method="post">
                                <?php if(!empty($otpError)): ?>
                                    <div class="error errorMsg"><?=$otpError?></div>
                                <?php endif; ?>
                                <?php if(!empty($otpMessage)): ?>
                                    <div class="text-center text-success"><?=$otpMessage?></div>
                                <?php endif; ?>
                                
                                <div class="input-group mt-3">
                                    <input type="text" name="web_otp" id="web_otp" class="form-control form-control-lg" placeholder="Enter OTP" aria-label="OTP" aria-describedby="basic-addon1">
                                    <input type="hidden" name="emp_id" id="emp_id" value="<?=$emp_id?>" >
                                </div>
								 <?=form_error('web_otp')?>
								 
								<div class="form-group text-center">
								    <span id="otp_timer"></span>
								</div>
                                <div class="form-group text-center">
                                    <div class="col-xs-12 p-b-20">
                                        <button class="btn btn-success waves-effect btn-rounded waves-light btn-block" type="submit"> Submit</button>
                                    </div>
                                </div>                                
							</form>
                        </div>
                    </div>
                </div>
				<div class="login-poweredby font-medium bg-grey pad-5">Powered By : SCUBE ERP</div>
            </div>
        </div>
    </div>
    <!-- All Required js -->
    <script src="<?=base_url()?>assets/libs/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap tether Core JavaScript -->
    <script src="<?=base_url()?>assets/libs/popper.js/dist/umd/popper.min.js"></script>
    <script src="<?=base_url()?>assets/libs/bootstrap/dist/js/bootstrap.min.js"></script>
    <script>
        $('[data-toggle="tooltip"]').tooltip();
        $(".preloader").fadeOut();
        
        let timerOn = true;

        function timer(remaining) {
            var m = Math.floor(remaining / 60);
            var s = remaining % 60;
            
            m = m < 10 ? '0' + m : m;
            s = s < 10 ? '0' + s : s;
            document.getElementById('otp_timer').innerHTML = m + ':' + s;
            remaining -= 1;
            
            if(remaining >= 0 && timerOn) {
                setTimeout(function() {
                    timer(remaining);
                }, 1000);
                return;
            }
            
            if(!timerOn) {
                // Do validate stuff here
                return;
            }
            
            // Do timeout stuff here
            //alert('Timeout for otp');
            document.getElementById('otp_timer').innerHTML = '<a href="javascript:void(0)" onclick="resendOTP();">Resend OTP</a>';
        }
        
        timer(60);
        
        function resendOTP(){
            timer(60);
            $.ajax({
                url : '<?=base_url('login/sendOTP')?>',
                type: "GET",
                success:function(data){ console.log(data); }
            });
        }
    </script>
</body>

</html>