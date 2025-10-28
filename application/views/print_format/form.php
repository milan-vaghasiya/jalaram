<style>
.ui-sortable-handle{cursor: move;}
.ui-sortable-handle:hover{background-color: #daeafa;border-color: #9fc9f3;cursor: move;}
</style>
<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>">
            <input type="hidden" name="modual" id="modual" value="packing">
            <div class="col-md-6 form-group">
                <label for="format_name">Format Name</label>
                <input type="text" name="format_name" id="format_name" class="form-control" value="<?=(!empty($dataRow->format_name))?$dataRow->format_name:""?>">
            </div>
            <div class="col-md-3 form-group">
                <label for="height">Format Width</label>
                <input type="text" name="width" id="width" class="form-control numericOnly" value="<?=(!empty($dataRow->width))?$dataRow->width:""?>"> 
            </div>
            <div class="col-md-3 form-group">
            <label for="height">Format Height</label>
                <input type="text" name="height" id="height" class="form-control numericOnly" value="<?=(!empty($dataRow->height))?$dataRow->height:""?>"> 
            </div>
            <div class="col-md-12 form-group">
                <label for="remark">Remark</label>
                <input type="text" name="remark" id="remark" class="form-control" value="<?=(!empty($dataRow->remark))?$dataRow->remark:""?>">
            </div>
            <!-- <div class="col-md-12 form-group">
                <label>Select Format Fields</label>
                <div class="row">
                    <?php
                        /*foreach($packingField as $key => $label):
                            $checked = (!empty($dataRow->formate_field) && isset(json_decode($dataRow->formate_field)->{$key}))?"checked":"";
                            
                            echo '<div class="col-md-6"><input type="checkbox" id="'.$key.'" name="formate_field['.$key.']" class="filled-in chk-col-success" value="'.$label.'" '.$checked.'><label for="'.$key.'" class="mr-3">'.$label.'</label></div>';
                        endforeach;*/
                    ?>
                </div>
            </div> -->
            
            
        </div>
        <hr>
        <div class="row">
            <div class="col-md-6 form-group">
                <label>Select Format Fields</label>
                <select id="field" data-input_id="formate_field_list" class="form-control jp_multiselect" multiple="multiple">
                    <?php
                        $formate_field_list = array();
                        foreach($packingField as $key => $label):
                            $selected = (!empty($dataRow->formate_field) && isset(json_decode($dataRow->formate_field)->{$key})) ? "selected" : "";
                            if(!empty($dataRow->formate_field) && isset(json_decode($dataRow->formate_field)->{$key})): $formate_field_list[] = $key; endif;
                            echo '<option value="' . $key . '" ' . $selected . '>' . $label . '</option>';
                        endforeach;
                    ?>
                </select>
                <input type="hidden" id="formate_field_list" value="<?=(!empty($formate_field_list) ? implode(',',$formate_field_list):"")?>" />
                <div class="error formate_field"></div>
            </div>
            <div class="col-md-2 form-group">
                <label>&nbsp;</label>
                <button type="button" id="addField" class="btn btn-outline-info btn-block"><i class="fa fa-plus"></i> ADD</button>
            </div>
            <div class="col-md-12 form-group">
                <h6 style="color:#ff0000;font-size:1rem;"><i>Note : Drag & Drop Row to Change Field Sequance</i></h6>
            </div>
            <div class="col-md-12  form-group">
                <table id="selectedFields" class="table excel_table table-bordered">
                    <thead class="thead-info">
                        <tr>
                            <th style="width:10%;text-align:center;">#</th>
                            <th style="width:40%;">Field Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            if(!empty($dataRow->formate_field)):
                                $fielsList = json_decode($dataRow->formate_field);
                                $i = 1;
                                foreach($fielsList as $key=>$label):
                                    echo '<tr id="'.$key.'" class="ui-sortable-handle">
                                        <td>'.$i++.'</td>
                                        <td>
                                            '.$label.'
                                            <input type="hidden" name="formate_field['.$key.']" class="formate_field" data-key="'.$key.'" value="'.$label.'">
                                        </td>
                                    </tr>';
                                endforeach;
                            endif;
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>

<script>
$(document).ready(function(){
    $("#selectedFields tbody").sortable({
        items: 'tr',
        cursor: 'pointer',
        axis: 'y',
        dropOnEmpty: false,
        helper: fixWidthHelper,
        start: function (e, ui) {
            ui.item.addClass("selected");
        },
        stop: function (e, ui) {
            ui.item.removeClass("selected");
            $(this).find("tr").each(function (index) {
                $(this).find("td").eq(0).html(index+1);
            });
        },
        update: function () {
            var ids='';
            $(this).find("tr").each(function (index) {ids += $(this).attr("id")+",";});
            var lastChar = ids.slice(-1);
            if (lastChar == ',') {ids = ids.slice(0, -1);}
            
            /* $.ajax({
                url: base_url + controller + '/updateProductProcessSequance',
                type:'post',
                data:{id:ids},
                dataType:'json',
                global:false,
                success:function(data){}
            }); */
       }
    });

    $(document).on('click','#addField',function(){
        var fieldKey = $("#field :selected").map(function(i, el) { return $(el).val(); }).get();
        var fieldLabel = $("#field :selected").map(function(i, el) { return $(el).text(); }).get();

        var existFiledKeys = $(".formate_field").map(function(i, el) { return $(el).data('key'); }).get();
        if(existFiledKeys.length > 0){
            $.each(existFiledKeys,function(i,key){
                if($.inArray(key,fieldKey) == -1){
                    $("#"+key).remove();

                    var i = 0;
                    $('#selectedFields tbody').find("tr").each(function (index) {
                        $('#selectedFields tbody').find("td").eq(0).html(i+1);
                    });
                }
            });
        }else{
            existFiledKeys = [];
        }
        
        $.each(fieldKey,function(i,key){
            if($.inArray(key,existFiledKeys) == -1){
                var tBody = $("#selectedFields > TBODY")[0];
			
                //Add Row.
                row = tBody.insertRow(-1);
                $(row).attr('id',key);
                $(row).attr('class',"ui-sortable-handle");

                //Add index cell
                var countRow = $('#selectedFields tbody tr:last').index() + 1;
                var cell = $(row.insertCell(-1));
                cell.html(countRow);
                cell.attr("class","text-center");

                cell = $(row.insertCell(-1));
			    cell.html(fieldLabel[i] + '<input type="hidden" name="formate_field['+key+']" class="formate_field" data-key="'+key+'" value="'+fieldLabel[i]+'">');
            }            
        });
    });
});

function fixWidthHelper(e, ui) {
    ui.children().each(function() {
        $(this).width($(this).width());
    });
    return ui;
}
</script>