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
        <title>REGISTRATION - <?=(!empty(SITENAME))?SITENAME:""?></title>
        <!-- Custom CSS -->
        <link href="<?=base_url()?>assets/css/style.css" rel="stylesheet">
        <link href="<?=base_url()?>assets/css/jp_helper.css" rel="stylesheet">
        <link href="<?=base_url()?>assets/libs/raty-js/lib/jquery.raty.css" rel="stylesheet">
        <link href="<?=base_url()?>assets/css/rating.css" rel="stylesheet">
    </head>
    
    <body>
        <div class="main-wrapper">
            <div class="preloader">
                <div class="lds-ripple">
                    <div class="lds-pos"></div>
                    <div class="lds-pos"></div>
                </div>
            </div>
            <img  class="backdrop" src="<?=base_url('assets/images/background/jji_bg.jpg')?>" alt="" />
            <section id="contact">
                <form id="partyRegistration">
                    <div class="contact-box">
                        <div class="contact-links">
                            <div>
                                <table>
                                    <td style="width:30;"><img src="<?=base_url('assets/images/logo_weight.png')?>" style="height:100px;" /></td>
                                    <td stype="width:70;"><span class="float-right text-right h2">SUPPLIER REGISTRATION FORM - F PU 02 (00/01.06.20)</span></td>
                                </table>
                            </div>
                        </div>
                        <div class="contact-form-wrapper">
                            <?php if(empty($dataRow->party_id)){ ?>
                                <div class="row">
                                    <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
                                    <input type="hidden" name="party_category" id="party_category" value="<?=(!empty($dataRow->party_category))?$dataRow->party_category:3; ?>" />
                                    <input type="hidden" name="party_type" id="party_type" value="<?=(!empty($dataRow->party_type))?$dataRow->party_type:1; ?>" />
                
                                    <div class="col-md-6 form-group">
                                        <label for="party_name">Company Name</label>
                                        <input type="text" name="party_name" class="form-control" value="<?=(!empty($dataRow->party_name))?$dataRow->party_name:""; ?>" />
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label for="scope_of_work">Scope Of Work</label>
                                        <input type="text" name="scope_of_work" class="form-control" value="<?=(!empty($dataRow->scope_of_work))?$dataRow->scope_of_work:""; ?>" />
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label for="company_type">Type Of Company</label>
                                        <select name="company_type" id="company_type" class="form-control">
    					                    <option value="Partnership">Partnership</option>
    					                    <option value="Public Limited">Public Limited</option>
    					                    <option value="Private Ltd.">Private Ltd.</option>
    					                    <option value="Proprietary">Proprietary</option>
    					                </select>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label for="contact_person">Contact Person</label>
                                        <input type="text" name="contact_person" class="form-control" value="<?=(!empty($dataRow->contact_person))?$dataRow->contact_person:""?>" />
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label for="party_mobile">Contact No.</label>
                                        <input type="text" name="party_mobile" class="form-control" value="<?=(!empty($dataRow->party_mobile))?$dataRow->party_mobile:""?>" />
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label for="party_email">Party Email</label>
                                        <input type="email" name="party_email" class="form-control" value="<?=(!empty($dataRow->party_email))?$dataRow->party_email:""; ?>" />
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label for="gstin">Party GSTIN</label>
                                        <input type="text" name="gstin" class="form-control" value="<?=(!empty($dataRow->gstin))?$dataRow->gstin:""; ?>" />
                                    </div>
                                    
                                    <div class="col-md-3 form-group">
                                        <label for="iso_certified">ISO 9001:2015 Certified company</label>
                                        <select name="iso_certified" id="iso_certified" class="form-control">
    					                    <option value="No">No</option>
    					                    <option value="Yes">Yes</option>
    					                </select>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label for="work_shift">Working Shift</label>
                                        <input type="text" name="work_shift" class="form-control" value="<?=(!empty($dataRow->work_shift))?$dataRow->work_shift:""; ?>" />
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label for="work_hrs">Working Hrs.</label>
                                        <input type="text" name="work_hrs" class="form-control" value="<?=(!empty($dataRow->work_hrs))?$dataRow->work_hrs:""; ?>" />
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label for="week_off">Weekly Holiday</label>
                                        <input type="text" name="week_off" class="form-control" value="<?=(!empty($dataRow->week_off))?$dataRow->week_off:""; ?>" />
                                    </div>
                                    
                                    <div class="col-md-9 form-group">
                                        <label for="party_address">Address</label>
                                        <textarea name="party_address" class="form-control" rows="1"><?=(!empty($dataRow->party_address))?$dataRow->party_address:""?></textarea>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label for="party_pincode">Address Pincode</label>
                                        <input type="text" name="party_pincode" class="form-control" value="<?=(!empty($dataRow->party_pincode))?$dataRow->party_pincode:""?>" />
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label for="machine_details">Details Of Machines</label>
                                        <input type="text" name="machine_details" class="form-control" value="<?=(!empty($dataRow->machine_details))?$dataRow->machine_details:""?>" />
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label for="instrument_details">Details Of Measuring Instruments</label>
                                        <input type="text" name="instrument_details" class="form-control" value="<?=(!empty($dataRow->instrument_details))?$dataRow->instrument_details:""?>" />
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label for="inspection_material">Details Of Inspection Before Dispatch Of Material</label>
                                        <input type="text" name="inspection_material" class="form-control" value="<?=(!empty($dataRow->inspection_material))?$dataRow->inspection_material:""?>" />
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label for="representative">Representative's Name</label>
                                        <input type="text" name="representative" class="form-control" value="<?=(!empty($dataRow->representative))?$dataRow->representative:""?>" />
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label for="designation">Designation</label>
                                        <input type="text" name="designation" class="form-control" value="<?=(!empty($dataRow->designation))?$dataRow->designation:""?>" />
                                    </div>
                            </div>
                                <center><button type="button" class="btn btn-success mt-4 saveFeedback">SUBMIT</button></center>
                            <?php } else { echo '<h2 class="text-center">Your registration has been successfully completed.</h2>'; }?>
                        </div>
                    </div>
                </form>
            </section>
        </div>
        <script src="<?=base_url()?>assets/libs/jquery/dist/jquery.min.js"></script>
        <script src="<?=base_url()?>assets/libs/bootstrap/dist/js/bootstrap.min.js"></script>
        <script src="<?=base_url()?>assets/libs/raty-js/lib/jquery.raty.js"></script>
        
        <script>
            var base_url = '<?=base_url();?>';
            $.fn.raty.defaults.path = 'https://jalaram.nativebittechnologies.com/';
            $(".preloader").fadeOut();
            
            $(document).ready(function() {
                $(document).on('click','.saveFeedback',function() {
                    var fd = $('#partyRegistration').serialize();
                    
                	$.ajax({
                		url: base_url + 'partyRegistration/saveRegistration',
                		data:fd,
                		type: "POST",
                		dataType:"json",
                	}).done(function(data){
                		if(data.status===0){
                			$(".error").html("");
                			$.each( data.message, function( key, value ) {
                				$("."+key).html(value);
                			});
                		}else if(data.status==1){
                		    window.location.reload();
                		}else{
                		
                		}		
                	});
                });
                /*$(document).on('click','.copyLink',function() {
                    var id= $(this).data('id');
                    var copyText = "https://jalaram.nativebittechnologies.com/partyRegistration/getRegistration/" + id;
                    copyText.execCommand("copy");
                });*/
            });
        </script>
    </body>
</html>