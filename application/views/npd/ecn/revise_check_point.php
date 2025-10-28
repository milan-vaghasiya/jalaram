<?php $this->load->view('includes/header'); ?>
<style> 
	.typeahead.dropdown-menu{width:95.5% !important;padding:0px;border: 1px solid #999999;box-shadow: 0 2px 5px 0 rgb(0 0 0 / 26%);}
	.typeahead.dropdown-menu li{border-bottom: 1px solid #999999;}
	.typeahead.dropdown-menu li .dropdown-item{padding: 8px 1em;margin:0;}
</style>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h4><u>Revision Checkpoint</u></h4>
                    </div>
                    <div class="card-body">
                        <form autocomplete="off" id="rev_check_point">
                            <div class="col-md-12">
								<div class="row form-group">
									
									<input type="hidden" name="rev_id" id="rev_id" value="<?=(!empty($dataRow->id) ? $dataRow->id : '')?>" />
									<input type="hidden" name="ref_id" id="ref_id" value="<?=(!empty($dataRow->ref_id) ? $dataRow->ref_id : '')?>">
									<input type="hidden" name="entry_type" id="entry_type" value="<?=(!empty($dataRow->entry_type) ? $dataRow->entry_type : 1)?>">

                                    <div class="col-md-2 form-group">
										<label for="ecn_note_no">ECN Note No.</label>
										<input type="text"  class="form-control req" value="<?=(!empty($dataRow->ecn_note_no) ?  $dataRow->ecn_prefix.$dataRow->ecn_note_no : $ecn_prefix.$ecn_note_no)?>" readonly />
										<input type="hidden"  id="ecn_note_no" name="ecn_note_no" class="form-control req" value="<?=(!empty($dataRow->ecn_note_no) ? $dataRow->ecn_note_no : $ecn_note_no)?>">
										<input type="hidden"  id="ecn_prefix" name="ecn_prefix" class="form-control req" value="<?=(!empty($dataRow->ecn_prefix) ? $dataRow->ecn_prefix : $ecn_prefix)?>">
									</div>

									<div class="col-md-2 form-group">
										<label for="ecn_type">Ecn Type</label>
										<select name="ecn_type" id="ecn_type" class="form-control">
											<option value="2" <?=(!empty($dataRow->ecn_type) && $dataRow->ecn_type == 2)?'selected':''?>>Revision</option>
											<option value="1" <?=(!empty($dataRow->ecn_type) && $dataRow->ecn_type == 1)?'selected':''?>>New</option>
										</select>
									</div>
									<div class="col-md-2 form-group">
										<label for="rev_no">Revision Type</label>
										<select name="rev_type" id="rev_type" class="form-control">
											<option value="1" <?=(!empty($dataRow->rev_type) && $dataRow->rev_type == 1)?'selected':''?>>Customer Revision</option>
											<option value="2" <?=(!empty($dataRow->rev_type) && $dataRow->rev_type == 2)?'selected':''?>>Internal Revision(Production)</option>
											<option value="3" <?=(!empty($dataRow->rev_type) && $dataRow->rev_type == 3)?'selected':''?>>Internal Revision(Department)</option>
										</select>
									</div>
									<div class="col-md-3 form-group">
										<label for="rev_no" style="width:50%">JJI Rev No.</label>
										<label for="rev_date" >JJI Rev Date</label>
										<div class="input-group-append">
											<input type="text" id="rev_no" name="rev_no" class="form-control req" value="<?=(!empty($dataRow->rev_no) ? $dataRow->rev_no : '')?>" style="width:50%" />
											<input type="date" id="rev_date" name="rev_date" class="form-control req" value="<?=(!empty($dataRow->rev_date) ? $dataRow->rev_date : date("Y-m-d"))?>" style="width:50%" />	
										</div>
									</div>

									<div class="col-md-3 form-group">
										<label for="cust_rev_no" style="width:50%">Cust. Rev No.</label>
										<label for="cust_rev_date" >Cust Rev Date</label>
										<div class="input-group-append">
											<input type="text" id="cust_rev_no" name="cust_rev_no" class="form-control req" value="<?=(!empty($dataRow->cust_rev_no) ? $dataRow->cust_rev_no : '')?>" style="width:50%"/>
											<input type="date" id="cust_rev_date" name="cust_rev_date" class="form-control req" value="<?=(!empty($dataRow->cust_rev_date) ? $dataRow->cust_rev_date : date("Y-m-d"))?>" style="width:50%" />	
										</div>
									</div>
									<div class="col-md-4 form-group">
										<label for="item_id">Item Name</label>
										<select name="item_id" id="item_id" class="form-control single-select req">
											<option value="">Select Item Name</option>
											<?php
											foreach ($itemData as $row) :
												$selected = (!empty($dataRow->item_id) && ($dataRow->item_id == $row->id) ? "selected" : "");
												echo '<option value="' . $row->id . '" '.$selected.'>[' . $row->item_code.'] '.$row->item_name . '</option>';
											endforeach;
											?>
										</select>
										<div class="error item_id"></div>
									</div>
                                    <div class="col-md-2 form-group">
                                        <label for="ecn_drg_no">Drawing No.</label>
                                        <input type="text" id="ecn_drg_no" name="ecn_drg_no" class="form-control" value="<?=(!empty($dataRow->ecn_drg_no) ? $dataRow->ecn_drg_no : '')?>" />
                                    </div>
                                    
                                    <div class="col-md-2 form-group">
                                        <label for="ecn_no">ECO No.</label>
                                        <input type="text" id="ecn_no" name="ecn_no" class="form-control" value="<?=(!empty($dataRow->ecn_no) ? $dataRow->ecn_no : '')?>" />
                                    </div>
                                    
                                    <div class="col-md-2 form-group">
                                        <label for="ecn_received_date">ECN Received Date</label>
                                        <input type="date" id="ecn_received_date" name="ecn_received_date" class="form-control req" value="<?=(!empty($dataRow->ecn_received_date) ? $dataRow->ecn_received_date : date("Y-m-d"))?>" />
                                    </div>
                                    
                                    <div class="col-md-2 form-group">
                                        <label for="target_date">Target Date</label>
                                        <input type="date" id="target_date" name="target_date" class="form-control req" value="<?=(!empty($dataRow->target_date) ? $dataRow->target_date : date("Y-m-d",strtotime("+10 Days")))?>" />
                                    </div>

									
                                    
                                    <div class="col-md-3 form-group">
                                        <label for="material_grade">Material Grade</label>
                                        <select name="gradeSelect" id="gradeSelect" data-input_id="material_grade" class="form-control jp_multiselect req mtGrade" multiple="multiple">
											<?php
											foreach ($mtGradeData as $row) :
												$selected = (!empty($dataRow->material_grade) && (in_array($row->material_grade,explode(",",$dataRow->material_grade))))?"selected":"";
												echo '<option value="' . $row->material_grade . '" '.$selected.'>' . $row->material_grade . '</option>';
											endforeach;
											?>
										</select>
										<input type="hidden" name="material_grade" id="material_grade" value="<?=!empty($dataRow->material_grade)?$dataRow->material_grade:''?>" />
										<div class="error material_grade"></div>
                                    </div>
                                    
                                    <div class="col-md-3 form-group">
										<label for="dept_id">Effect To Department</label>
										<?php
										$defaultDept = array();
										?>
										<select name="deptSelect" id="deptSelect" data-input_id="dept_id" class="form-control jp_multiselect req" multiple="multiple">
											<?php
												foreach ($deptData as $row) :
													$selected = '';
													if((!empty($row->ecn_stock)) || (!empty($dataRow->dept_id) && (in_array($row->id,explode(",",$dataRow->dept_id)))))
													{														
														$selected = "selected";
														$defaultDept[] = $row->id;
													}
													echo '<option value="' . $row->id . '" '.$selected.'>' . $row->name . '</option>';
												endforeach;
											?>
										</select>
										<input type="hidden" name="dept_id" id="dept_id" value="<?=!empty($defaultDept) ? implode(",",$defaultDept):''?>" />
										<div class="error dept_id"></div>
									</div>

									<div class="col-md-6 form-group">
										<label for="remark">Remark</label>
										<input type="text" name="remark" id="remark" class="form-control" placeholder="Enter Remark" value="<?=(!empty($dataRow->remark))?$dataRow->remark:""?>">
									</div>
									<div class="col-md-6 form-group">
										<label for="pfc_remark">PFC Remark</label>
										<input type="text" name="pfc_remark" id="pfc_remark" class="form-control" placeholder="Enter pfc_remark" value="<?=(!empty($dataRow->pfc_remark))?$dataRow->pfc_remark:""?>">
									</div>

								</div>
							</div>
							<hr>
                            <div class="col-md-12 row"><h5>Effect Of Changes : </h5></div>
							<div class="col-md-12 mt-3">
								<div class="error item_name_error"></div>
								<div class="row form-group">
									<div class="table-responsive">
										<table id="revTable" class="table table-bordered">
											<thead class="thead-info">
												<tr>
													<th style="width:3%;">#</th>
													<th style="width:15%;">Check Points</th>
													<th style="width:7%;">Y/N/NA</th>
													<th style="width:25%;">Old Description</th>
													<th style="width:25%;">New Description</th>
													<th style="width:15%;">Responsibility</th>
													<th style="width:10%;">Target Date</th>
												</tr>
											</thead>
											<tbody id="tempItem" class="temp_item">
												<?php 										
													if(!empty($revList)): 
													$i=1;
													foreach($revList as $row):
												?>
													<tr>
														<td>
															<?=$i?>
														</td>
														<td>
                                                            <?=$row->title?>
															<input type="hidden" id="rev_ch_id<?=$i?>" name="rev_ch_id[]" value="<?=(!empty($dataRow->id) ? $row->id : '')?>">
															<input type="hidden" id="check_point_id<?=$i?>" name="check_point_id[]" class="form-control" value="<?=(!empty($row->check_point_id) ? $row->check_point_id : $row->id)?>">
														</td>
														<td>
															<select name="is_change[]" id="is_change<?=$i?>" class="form-control">
																<option value="">Select</option>
																<option value="Y" <?=((!empty($row->is_change) && $row->is_change == "Y") ? "selected" : '')?>>Y</option>
																<option value="N" <?=((!empty($row->is_change) && $row->is_change == "N") ? "selected" : (empty($row->is_change) ? 'selected' : ''))?>>N</option>
																<option value="NA" <?=((!empty($row->is_change) && $row->is_change == "NA") ? "selected" : '')?>>NA</option>
															</select>
															<div class="error is_change<?=$i?>"></div>
														</td>
														<td>
															<input type="text" id="old_description<?=$i?>" name="old_description[]" class="form-control" value="<?=(!empty($row->old_description) ? $row->old_description : '')?>">
															<div class="error old_description<?=$i?>"></div>
														</td>
														<td>
															<input type="text" id="description<?=$i?>" name="description[]" class="form-control" value="<?=(!empty($row->description) ? $row->description : '')?>">
															<div class="error description<?=$i?>"></div>
														</td>
														<td>
															<select name="responsibility[]" id="responsibility<?=$i?>"  class="form-control single-select">
															<option value="">Select Option</option>
																<?php
																	foreach ($empData as $row1) :
																		$selected = (!empty($row->responsibility) && ($row->responsibility == $row1->id))?"selected":"";
																		echo '<option value="' . $row1->id . '" '.$selected.'>' . $row1->emp_name . '</option>';
																	endforeach;
																?>
															</select>
															<div class="error responsibility<?=$i?>"></div>
														</td>
														<td>
															<input type="date" id="ch_target_date<?=$i?>" name="ch_target_date[]" class="form-control" value="<?=(!empty($row->ch_target_date) ? $row->ch_target_date : '')?>">
															<div class="error ch_target_date<?=$i?>"></div>
														</td>
													</tr>
												<?php $i++; endforeach; else: ?>
												<tr id="noData">
													<td colspan="6" class="text-center">No data available in table</td>
												</tr>
												<?php endif; ?>
											</tbody>
											
										</table>
									</div>
								</div>
							</div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <div class="col-md-12">
                            <button type="button" class="btn waves-effect waves-light btn-outline-success float-right save-form" onclick="saveRevChPoint('rev_check_point');" ><i class="fa fa-check"></i> Save</button>
                            <a href="<?=base_url($headData->controller.'/reviseCheckPoint')?>" class="btn waves-effect waves-light btn-outline-secondary float-right save-form" style="margin-right:10px;"><i class="fa fa-times"></i> Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url();?>assets/js/custom/master-form.js?v=<?=time()?>"></script>
<script src="<?php echo base_url();?>assets/js/custom/ecn.js?v=<?=time()?>"></script>
