<form enctype="multipart/form-data">
    <div class="col-md-12">
        <div class="row">

            <input type="hidden" name="id" id="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : '' ?>" />
            <input type="hidden" name="party_id" id="party_id" value="<?= (!empty($dataRow->party_id)) ? $dataRow->party_id : $party_id ?>" />

            <div class="col-md-6 form-group">
                <label for="scope_of_work">Scope Of Work</label>
                <input type="text" name="scope_of_work" id="scope_of_work" class="form-control" value="<?=(!empty($dataRow->scope_of_work))?$dataRow->scope_of_work:""; ?>" />
            </div>

            <div class="col-md-6 form-group">
                <label for="company_type">Type Of Company</label>
                <select name="company_type" id="company_type" class="form-control">
                    <option value="Partnership" <?=((!empty($dataRow->company_type)) && $dataRow->company_type == "Partnership") ? "selected" : "" ?>>Partnership</option>
                    <option value="Public Limited" <?=((!empty($dataRow->company_type)) && $dataRow->company_type == "Public Limited") ? "selected" : "" ?>>Public Limited</option>
                    <option value="Private Ltd." <?=((!empty($dataRow->company_type)) && $dataRow->company_type == "Private Ltd.") ? "selected" : "" ?>>Private Ltd.</option>
                    <option value="Proprietary" <?=((!empty($dataRow->company_type)) && $dataRow->company_type == "Proprietary") ? "selected" : "" ?>>Proprietary</option>
                </select>
            </div>

            <div class="col-md-6 form-group addFile">
                <label for="iso_certified">ISO 9001:2015 Certified company</label>
                <select name="iso_certified" id="iso_certified" class="form-control">
                    <option value="No" <?=((!empty($dataRow->iso_certified)) && $dataRow->iso_certified == "No") ? "selected" : "" ?>>No</option>
                    <option value="Yes" <?=((!empty($dataRow->iso_certified)) && $dataRow->iso_certified == "Yes") ? "selected" : "" ?>>Yes</option>
                </select>
            </div>

            <div class="col-md-4 form-group isoFile">
				<label for="iso_file">ISO Certificate</label>
                <div class="input-group-append">
                    <input type="file" name="iso_file" id="iso_file" class="form-control-file" style="width:<?=((!empty($dataRow->iso_file) ? "90%" : "100%"))?>">
                    <?php
                    if(!empty($dataRow->iso_file)){
                        ?>
                        <a href="<?=base_url('assets/uploads/iso_certificate/'.$dataRow->iso_file)?>" class="btn btn-outline-primary" download><i class="fa fa-arrow-down" ></i></a>
                        <?php
                    }
                    ?>
                </div>
                <div class="iso_files text-danger"></div>
            </div>

            <div class="col-md-6 form-group addFile">
                <label for="work_shift">Working Shift</label>
                <input type="text" name="work_shift" id="work_shift" class="form-control" value="<?=(!empty($dataRow->work_shift))?$dataRow->work_shift:""; ?>" />
            </div>

            <div class="col-md-6 form-group">
                <label for="work_hrs">Working Hrs.</label>
                <input type="text" name="work_hrs" id="work_hrs" class="form-control" value="<?=(!empty($dataRow->work_hrs))?$dataRow->work_hrs:""; ?>" />
            </div>

            <div class="col-md-6 form-group">
                <label for="week_off">Weekly Holiday</label>
                <input type="text" name="week_off" id="week_off" class="form-control" value="<?=(!empty($dataRow->week_off))?$dataRow->week_off:""; ?>" />
            </div>

            <div class="col-md-6 form-group">
                <label for="representative">Representative's Name</label>
                <input type="text" name="representative" id="representative" class="form-control" value="<?=(!empty($dataRow->representative))?$dataRow->representative:""?>" />
            </div>
            
            <div class="col-md-6 form-group">
                <label for="designation">Designation</label>
                <input type="text" name="designation" id="designation" class="form-control" value="<?=(!empty($dataRow->designation))?$dataRow->designation:""?>" />
            </div>

            <div class="col-md-12 form-group">
                <label for="machine_details">Details Of Machines</label>
                <input type="text" name="machine_details" id="machine_details" class="form-control" value="<?=(!empty($dataRow->machine_details))?$dataRow->machine_details:""?>" />
            </div>

            <div class="col-md-12 form-group">
                <label for="instrument_details">Details Of Measuring Instruments</label>
                <input type="text" name="instrument_details" id="instrument_details" class="form-control" value="<?=(!empty($dataRow->instrument_details))?$dataRow->instrument_details:""?>" />
            </div>

            <div class="col-md-12 form-group">
                <label for="inspection_material">Details Of Inspection Before Dispatch Of Material</label>
                <input type="text" name="inspection_material" id="inspection_material" class="form-control" value="<?=(!empty($dataRow->inspection_material))?$dataRow->inspection_material:""?>" />
            </div>

        </div>
    </div>
</form>
<script>
    $(document).ready(function(){
        setTimeout(function(){
            $("#iso_certified").trigger('change');
        },10);

        $(document).on('change',"#iso_certified",function(){
            var iso_certified = $(this).val();

            if(iso_certified == 'No'){                
                $(".isoFile").hide();
                $(".addFile").attr("class","col-md-6 form-group addFile"); 
            }
            else{ 
                $(".isoFile").show();
                $(".addFile").attr("class","col-md-4 form-group addFile");
            }
        });
    });
</script>