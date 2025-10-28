<form autocomplete="off">
    <div class="col-md-12">
        <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
        <div class="row">
			<div class="col-md-12 form-group">
                <label for="leave_type">Leave Type</label>
                <input type="text" name="leave_type" class="form-control text-capitalize req" placeholder="Leave Type" value="<?=(!empty($dataRow->leave_type))?$dataRow->leave_type:""; ?>" />
            </div>
        </div>
		<div class="row">
			<div class="col-md-12 scrollable" style="height:400px;padding:15px;">
				<table class="table no-border table-striped">
					<tr>
						<th>Designation</th><th style="width:30%;">Leave Days</th>
					</tr>
					<?php
						if(!empty($empDesignations))
						{
							foreach($empDesignations as $row)
							{
								$leave_days=0;$emp_designation_id=$row->id;$m_or_y = '';$my='';
								if(!empty($dataRow->leave_quota))
								{
									$leave_quota = (array)json_decode($dataRow->leave_quota);									
									foreach($leave_quota as $key=>$value)
									{
										if($key == $row->id):
											$leave_days = $value->leave_days;
											// $my = $value->m_or_y;
										endif;
									}
								}
								// foreach($mory as $key => $value):
									// $selected = (!empty($my) && $key == $my)?"selected":"";
									// $m_or_y .= '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
								// endforeach;
								
								echo '<tr>';
									echo '<td>'.$row->title.'<input type="hidden" name="emp_designation_id[]"  value="'.$emp_designation_id.'" /></td>';
									echo '<td><input type="text" name="leave_days[]" class="form-control numericOnly" maxlength="3" value="'.$leave_days.'" />
									<input type="hidden" name="m_or_y[]" value="1" /></td>';
									// echo '<td><select name="m_or_y[]" id="m_or_y" class="form-control">'.$m_or_y.'</select></td>';
								echo '</tr>';
							}
						}
					?>
				</table>
			</div>
		</div>
    </div>
</form>