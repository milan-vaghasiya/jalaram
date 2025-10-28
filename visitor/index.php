<?php ?>
<!doctype html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#000000">
    <title>JAY JALARAM PRECISION COMPONENT LLP</title>
    <meta name="description" content="Finapp HTML Mobile Template">
    <meta name="keywords" content="bootstrap, wallet, banking, fintech mobile template, cordova, phonegap, mobile, html, responsive" />
    <link rel="icon" type="image/png" href="assets/img/favicon.png" sizes="32x32">
    <link rel="apple-touch-icon" sizes="180x180" href="assets/img/icon/192x192.png">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

    <!-- loader -->
    <div id="loader"><img src="assets/img/loading-icon.png" alt="icon" class="loading-icon"></div>
    <!-- * loader -->
    <form style="height:100vh;background:#FFFFFF;">  
    <!-- App Capsule -->
    <div id="appCapsule" style="padding: 0px;">
            <div class="text-center"><img src="assets/img/logo.png" alt="icon" style="width:70%;padding-top:10px;"></div>
            <div class="text-center mt-1" style="background: #005da9;color: #FFFFFF;">Fill The Form To Take Appointment</div>
        <div class="section">
              
            <div class="form-group basic animated">
                <div class="input-wrapper">
                    <label class="label" for="vname">Your name</label>
                    <input type="text" class="form-control" id="vname" name="vname" placeholder="Your name">
                    <i class="clear-input"><ion-icon name="close-circle"></ion-icon></i>
                </div>
            </div>
            <div class="form-group basic animated">
                <div class="input-wrapper">
                    <label class="label" for="contact_no">Contact Number</label>
                    <input type="text" class="form-control" id="contact_no" name="contact_no" placeholder="Contact Number">
                    <i class="clear-input"><ion-icon name="close-circle"></ion-icon></i>
                </div>
            </div>
            <div class="form-group basic animated">
                <div class="input-wrapper">
                    <label class="label" for="company_name">Company name</label>
                    <input type="text" class="form-control" id="company_name" name="company_name" placeholder="Company name">
                    <i class="clear-input"><ion-icon name="close-circle"></ion-icon></i>
                </div>
            </div>
            <div class="form-group basic animated">
                <div class="input-wrapper">
                    <label class="label" for="vadd">Your Address</label>
                    <input type="text" class="form-control" id="vadd" name="cadd" placeholder="Your Address">
                    <i class="clear-input"><ion-icon name="close-circle"></ion-icon></i>
                </div>
            </div>
            <!--<div class="form-group basic animated">
                <div class="input-wrapper">
                    <label class="label" for="dept_id">Department</label>
                    <select class="form-control custom-selec" id="dept" name="dept_id">
                        <option value="">Select Department</option>
                        <option value="1">Administration</option>
                        <option value="2">Production</option>
                    </select>
                    <i class="clear-input"><ion-icon name="close-circle"></ion-icon></i>
                </div>
            </div>-->
            <div class="form-group basic animated">
                <div class="input-wrapper">
                    <label class="label" for="wtm">Whome To Meet?</label>
                    <select class="form-control custom-selec single-select" id="wtm" name="wtm">
                        <option value="">Select Person</option>
                        <option value="281">Rakeshbhai Kantariya</option>
                        <option value="282">Darshan Kantariya</option>
                    </select>
                    <i class="clear-input"><ion-icon name="close-circle"></ion-icon></i>
                </div>
            </div>

            <div class="form-group basic animated">
                <div class="input-wrapper">
                    <label class="label" for="purpose">Purpose</label>
                    <textarea id="purpose" name="purpose" rows="2" class="form-control" placeholder="Purpose"></textarea>
                    <i class="clear-input"><ion-icon name="close-circle"></ion-icon></i>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-1">
        <button type="button" class="btn btn-success btn-block" style="border-radius:0px;height:40px;">Get Appointment</button>
    </div>
    </form>
    <!-- * App Capsule -->


    <!-- ========= JS Files =========  -->
    <!-- Bootstrap -->
    <script src="assets/js/lib/bootstrap.bundle.min.js"></script>
    <!-- Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <!-- Splide -->
    <script src="assets/js/plugins/splide/splide.min.js"></script>
    <!-- Base Js File -->
    <script src="assets/js/base.js"></script>


</body>

</html>
