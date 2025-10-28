<?php
class ProductionReportNewModel extends MasterModel
{
	private $jobCard = "job_card";
	private $jobOutward = "job_outward";
	private $empMaster = "employee_master";
	private $jobTrans = "job_transaction";
	private $itemMaster = "item_master";
	private $JobWorkChallan = "jobwork_challan";
	private $itemKit = "item_kit";
	private $production_log = "production_log";	
	private $product_process = "product_process";
	private $stockTrans  ="stock_transaction";
	private $deviceLog = "device_log";
	
	public function getJobcardList($postData = array()){
		$data['tableName'] = $this->jobCard;
		$data['select'] = 'job_card.id,job_card.job_no,job_card.job_prefix,job_card.job_date,item_master.item_code,item_master.item_name';
		$data['leftJoin']['item_master'] = 'item_master.id = job_card.product_id';
		$data['where']['version'] = 2;

		if(!empty($postData['job_status'])):
			$data['where_in']['job_card.order_status'] = $postData['job_status'];
		endif;

		return $this->rows($data);
	}
	
	public function getJobWiseProduction($data)
	{
		$jobData = $this->jobcard_v3->getJobcard($data['job_id']);

		$thead = '<tr><th colspan="10">Job Card : ' . getPrefixNumber($jobData->job_prefix, $jobData->job_no) . '</th></tr>
				    <tr>
				    	<th>#</th>
				    	<th>Date</th>
				    	<th>Process Name</th>
				    	<th>OK Qty.</th>
				    	<th>Reject Qty.</th>
				    	<th>Rework Qty.</th>
				    	<th>Operator</th>
				    	<th>Machine</th>
				    </tr>';
		$tbody = '';
		$i = 1;

		$queryData = array();
		$queryData['tableName'] = $this->production_log;
		$queryData['select'] = " production_log.*,item_master.item_code,employee_master.emp_name,process_master.process_name";
		$queryData['leftJoin']['item_master'] = " production_log.machine_id = item_master.id";
		$queryData['join']['process_master'] = "process_master.id =  production_log.process_id";
		$queryData['join']['employee_master'] = "employee_master.id =  production_log.operator_id";
		$queryData['where']['production_log.job_card_id'] = $data['job_id'];
		$queryData['where_in']['production_log.prod_type'] = [1,3];
		$result = $this->rows($queryData);

		if (!empty($result)) {
			foreach ($result as $row) {
				$tbody .= '<tr>';
				$tbody .= '<td class="text-center">' . $i++ . '</td>';
				$tbody .= '<td>' . formatDate($row->log_date) . '</td>';
				$tbody .= '<td>' . $row->process_name . '</td>';
				$tbody .= '<td>' . floatVal($row->ok_qty) . '</td>';
				$tbody .= '<td>' . floatval($row->rej_qty) . '</td>';
				$tbody .= '<td>' . floatval($row->rw_qty) . '</td>';
				$tbody .= '<td>' . $row->emp_name . '</td>';
				$tbody .= '<td>' . $row->item_code . '</td>';
				$tbody .= '</tr>';
			}
		}

		return ['status' => 1, 'thead' => $thead, 'tbody' => $tbody];
	}

	public function getJobworkRegister($data)
	{
		$queryData['tableName'] = "vendor_challan_trans";
		$queryData['select'] = "vendor_challan_trans.*,vendor_challan.trans_date,vendor_challan.trans_number,vendor_challan.material_data,job_work_order.jwo_no,job_work_order.jwo_prefix,item_master.item_name,item_master.item_code,process_master.process_name,unit_master.unit_name as punit,prodLog.rej_qty,prodLog.log_date,job_card.process,party_master.party_name as vendor_name";
		$queryData['leftJoin']['item_master'] = "item_master.id = vendor_challan_trans.item_id";
		$queryData['leftJoin']['unit_master'] = "item_master.unit_id = unit_master.id";
		$queryData['leftJoin']['job_work_order'] = "job_work_order.id = vendor_challan_trans.jobwork_order_id";
		$queryData['leftJoin']['process_master'] = "process_master.id = vendor_challan_trans.process_id";
		$queryData['leftJoin']['(SELECT SUM(rej_qty) as rej_qty,log_date,id FROM production_log WHERE is_delete = 0) as prodLog'] = "prodLog.id = vendor_challan_trans.ref_id";
		$queryData['leftJoin']['vendor_challan'] = "vendor_challan.id = vendor_challan_trans.challan_id";
		$queryData['leftJoin']['party_master'] = "party_master.id = vendor_challan.vendor_id";
		$queryData['leftJoin']['job_card'] = "job_card.id = vendor_challan_trans.job_card_id";
		
		if(!empty($data['vendor_id']))$queryData['where']['vendor_challan.vendor_id'] = $data['vendor_id'];
		
		if(!empty($data['ref_id']))$queryData['where']['vendor_challan_trans.ref_id'] = $data['ref_id'];
	
		$queryData['where']['vendor_challan_trans.type'] = $data['prod_type'];
		
		
		if(!empty($data['from_date']) AND !empty($data['to_date'])){$queryData['customWhere'][] = "DATE(vendor_challan.trans_date) BETWEEN '" . $data['from_date'] . "' AND '" . $data['to_date'] . "'";}
		
		$result = $this->rows($queryData);
		
		return $result;
	}
	
	/* Used At :- AccountingReport */
    public function getVendorChallanOutDetail($data)
	{
		$queryData['tableName'] = "vendor_challan_trans";
		$queryData['select'] = "vendor_challan_trans.*,jwo.rate,jwo.rate_per,item_master.item_name,item_master.item_code,item_master.gst_per,process_master.process_name,party_master.party_name as vendor_name,party_master.gstin,IFNULL(states.name, '') as party_state,vendor_challan.trans_prefix as challan_prefix,vendor_challan.trans_no as challan_no,vendor_challan.trans_date as challan_date,unit_master.unit_name as punit";
		$queryData['select'] .= ",(CASE WHEN jwo.rate_per = 1 THEN jwo.qty ELSE jwo.qty_kg END) as ch_qty";
		$queryData['leftJoin']['vendor_challan'] = "vendor_challan.id = vendor_challan_trans.challan_id";
		$queryData['leftJoin']['item_master'] = "item_master.id = vendor_challan_trans.item_id";
		$queryData['leftJoin']['unit_master'] = "item_master.unit_id = unit_master.id";
		$queryData['leftJoin']['job_work_order jwo'] = "jwo.id = vendor_challan_trans.jobwork_order_id";
		$queryData['leftJoin']['process_master'] = "process_master.id = vendor_challan_trans.process_id";
		$queryData['leftJoin']['party_master'] = "party_master.id = vendor_challan.vendor_id";
		$queryData['leftJoin']['states'] = "states.id = party_master.state_id";
		$queryData['customWhere'][] = "DATE(vendor_challan.trans_date) BETWEEN '" . $data['from_date'] . "' AND '" . $data['to_date'] . "'";
		$queryData['where']['vendor_challan_trans.type'] = 1; 
		$result = $this->rows($queryData);
		
		return $result;
	}

	/* Used At :- AccountingReport */
    public function getVendorChallanInDetail($data)
	{
		//$queryData['tableName'] = "production_log";
		$queryData['tableName'] = "vendor_challan_trans";
		$queryData['select'] = "vendor_challan_trans.*,item_master.item_name,item_master.item_code,item_master.gst_per,process_master.process_name,party_master.party_name as vendor_name,party_master.gstin,IFNULL(states.name, '') as party_state,vendor_challan.trans_prefix as challan_prefix,vendor_challan.trans_no as challan_no,vendor_challan.trans_date as challan_date,unit_master.unit_name as punit";
		//$queryData['leftJoin']['vendor_challan_trans'] = "production_log.ref_id = vendor_challan_trans.id";
		$queryData['leftJoin']['vendor_challan'] = "vendor_challan.id = vendor_challan_trans.challan_id";
		$queryData['leftJoin']['item_master'] = "item_master.id = vendor_challan_trans.item_id";
		$queryData['leftJoin']['unit_master'] = "item_master.unit_id = unit_master.id";
		$queryData['leftJoin']['process_master'] = "process_master.id = vendor_challan_trans.process_id";
		$queryData['leftJoin']['party_master'] = "party_master.id = vendor_challan.vendor_id";
		$queryData['leftJoin']['states'] = "states.id = party_master.state_id";
		$queryData['customWhere'][] = "DATE(vendor_challan.trans_date) BETWEEN '" . $data['from_date'] . "' AND '" . $data['to_date'] . "'";
		$queryData['where']['vendor_challan_trans.type'] = 2; 
		$result = $this->rows($queryData);
		
		return $result;
	}
	
	public function getVendorRejectionSum($ref_id)
	{
		$queryData['tableName'] = $this->production_log;
		$queryData['select'] = "SUM(rej_qty) as rejectQty";
		$queryData['where']['ref_id'] = $ref_id;
		$queryData['where']['production_log.prod_type'] = 3;
		$result = $this->row($queryData);
		return $result;
	}

	public function getUsedMaterial($id)
	{
		$data['tableName'] = 'job_used_material';
		$data['select'] = "job_used_material.*,item_master.item_name,item_master.item_code,item_master.item_type,unit_master.unit_name";
		$data['leftJoin']['item_master'] = "item_master.id = job_used_material.bom_item_id";
		$data['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
		$data['where']['job_used_material.id'] = $id;
		return $this->row($data);
	}
	
	//Created By Karmi @05/07/2022 for JobWork Register Report
	public function getUsedMaterialByJobCardId($job_card_id){
		$data['tableName'] = 'job_used_material';
		$data['select'] = "job_used_material.*,item_master.item_name,item_master.item_code,item_master.item_type,unit_master.unit_name";
		$data['leftJoin']['item_master'] = "item_master.id = job_used_material.bom_item_id";
		$data['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
		$data['where']['job_used_material.job_card_id'] = $job_card_id;
		return $this->row($data);
	}

	/* Get Production Analysis Data */
	public function getProductionAnalysis($data)
	{
		$queryData = array();
		$queryData['tableName'] = $this->jobTrans;
		$queryData['select'] = 'job_transaction.*,process_master.process_name,item_master.item_name,item_master.item_code,employee_master.emp_name,shift_master.shift_name,job_card.id as job_id';
		$queryData['join']['process_master'] = 'process_master.id = job_transaction.process_id';
		$queryData['join']['job_card'] = 'job_card.id = job_transaction.job_card_id';
		$queryData['leftJoin']['item_master'] = 'item_master.id = job_card.product_id';
		//$queryData['leftJoin']['machine_master'] = 'machine_master.id = job_outward.machine_id';
		$queryData['leftJoin']['employee_master'] = 'employee_master.id = job_transaction.operator_id';
		$queryData['leftJoin']['shift_master'] = 'shift_master.id = job_transaction.shift_id';
		$queryData['customWhere'][] = "job_transaction.entry_date BETWEEN '" . $data['from_date'] . "' AND '" . $data['to_date'] . "'";
		$queryData['where']['job_transaction.out_qty != '] = 0;
		$queryData['where']['job_transaction.entry_type'] = 2;
		$queryData['order_by']['job_transaction.entry_date'] = 'ASC';
		$result = $this->rows($queryData);

		$i = 1;
		$prev_date = "";
		$pid = 0;
		$tbody = "";
		foreach ($result as $row) :
			$rjqty = 0;
			$rwqty = 0;
			$rjRatio = 0;
			$machineData = $this->machine->getMachine($row->machine_id);
			$machineNo = (!empty($machineData)) ? $machineData->item_code : "";
			if ($prev_date != $row->entry_date or $pid != $row->process_id) {
				$queryData = array();
				$queryData['select'] = "SUM(rejection_qty) as qty";
				$queryData['tableName'] = $this->jobTrans;
				$queryData['where']['job_card_id'] = $row->job_id;
				$queryData['where']['process_id'] = $row->process_id;
				$queryData['where']['entry_date'] = $row->entry_date;
				$queryData['where']['entry_type'] = 1;
				$rejectQty = $this->row($queryData);
				$rjqty = (!empty($rejectQty)) ? $rejectQty->qty : 0;

				$queryData = array();
				$queryData['select'] = "SUM(rework_qty) as qty";
				$queryData['tableName'] = $this->jobTrans;
				$queryData['where']['job_card_id'] = $row->job_id;
				$queryData['where']['process_id'] = $row->process_id;
				$queryData['where']['entry_date'] = $row->entry_date;
				$queryData['where']['entry_type'] = 1;
				$reworkQty = $this->row($queryData);
				$rwqty = (!empty($reworkQty)) ? $reworkQty->qty : 0;

				if (!empty($row->out_qty) and $row->out_qty > 0) :
					$rjRatio = round((($rjqty * 100) / $row->out_qty), 2);
				endif;
			}

			$tbody .= '<tr class="text-center">
						<td>' . $i++ . '</td>
						<td>' . formatDate($row->entry_date) . '</td>
						<td>' . $machineNo . '</td>
						<td>' . $row->shift_name . '</td>
						<td>' . $row->emp_name . '</td>
						<td>' . $row->item_code . '</td>
						<td>' . $row->production_time . '</td>
                        <td>' . $row->process_name . '</td>
                        <td>' . $row->cycle_time . '</td>
						<td>' . $row->out_qty . '</td>
						<td>' . $rwqty . '</td>
						<td>' . $rjqty . '</td>
						<td>' . $rjRatio . '%</td>
					</tr>';
			$prev_date = $row->entry_date;
			$pid = $row->process_id;
		endforeach;
		return ['status' => 1, 'tbody' => $tbody];
	}

	/* Machine Wise Production OEE */
	public function getDepartmentWiseMachine($dept_id)
	{
		$data['tableName'] = $this->itemMaster;
		$data['where']['item_master.location'] = $dept_id;
		$result = $this->rows($data);
		return $result;
	}

    public function getMachineWiseProduction($data, $dept_id)
	{
		$queryData = array();
		$queryData['tableName'] = $this->production_log;
		$queryData['select'] = 'SUM(production_log.ok_qty) as ok_qty,SUM(idle_time) as idle_time,SUM(production_time) as shift_hour,production_log.cycle_time as m_ct,production_log.log_date,production_log.shift_id,SUM(production_log.load_unload_time) as load_unload_time,shift_master.shift_name,process_master.process_name,item_master.item_code as product_code';
		$queryData['leftJoin']['shift_master'] = 'shift_master.id = production_log.shift_id';
		$queryData['leftJoin']['process_master'] = 'process_master.id = production_log.process_id';
		$queryData['leftJoin']['job_card'] = 'job_card.id = production_log.job_card_id';
		$queryData['leftJoin']['item_master'] = 'item_master.id = job_card.product_id';
		$queryData['where']['production_log.machine_id'] = $data['machine_id'];
		$queryData['where']['production_log.prod_type'] = 1;
		$queryData['customWhere'][] = "DATE(production_log.log_date) BETWEEN '" . $data['from_date'] . "' AND '" . $data['to_date'] . "'";
		$queryData['group_by'][] = "production_log.shift_id";
		$queryData['group_by'][] = "production_log.log_date";
		$queryData['group_by'][] = "production_log.process_id";
		$queryData['group_by'][] = "job_card.product_id";
		$queryData['order_by']['production_log.log_date'] = 'ASC';
		$result = $this->rows($queryData);

		return $result;
	}
	
    public function getIdleTimeReasonMachineWise($log_date, $shift_id, $machine_id)
	{
		$queryData = array();
		$queryData['tableName'] = $this->production_log;
		$queryData['select'] = 'production_log.idle_reason,product_process.setting_time';
		$queryData['leftJoin']['job_card']='job_card.id=production_log.job_card_id';
		$queryData['leftJoin']['product_process']='job_card.product_id =product_process.item_id AND production_log.process_id = product_process.process_id';
		$queryData['where']['DATE(production_log.log_date)'] = date("Y-m-d",strtotime($log_date));
		$queryData['where']['production_log.shift_id'] = $shift_id;
		$queryData['where']['production_log.machine_id'] = $machine_id;
		$queryData['where']['production_log.prod_type'] = 1;
		$result = $this->rows($queryData);
		$idleReasonList = $this->comment->getIdleReason();
		$td = '';$masterSettingTime=0;$settingTime=0;$breakDownTime=0;
		if (!empty($idleReasonList)) {
			foreach ($idleReasonList as $idl) {
				$idleTime = 0;
				foreach ($result as $row) {
					$idleReasonData = !empty($row->idle_reason) ? json_decode($row->idle_reason) : '';

					if (!empty($idleReasonData)) {
						foreach ($idleReasonData as $row1) {
							if ($row1->idle_reason_id == $idl->id) {
								$idleTime += $row1->idle_time;
								if($idl->id == 11){
									$masterSettingTime+=$row->setting_time;
									$settingTime+=$row1->idle_time*60;
								}
								if($idl->code == 'E'){
									$breakDownTime+=$row1->idle_time*60;
								}
							}
						}
					}
				}
				$cls=($idleTime > 0) ? 'bg-light' : '';
				$td .= '<td class="'.$cls.'">' . $idleTime . '</td>';
			}
		}
		$settingTime = ((!empty($settingTime)) && $settingTime < $masterSettingTime) ? $settingTime : $masterSettingTime;
		return ['td'=>$td,'setting_time' =>$settingTime,'breakdown_time'=>$breakDownTime];
	}

	/** Operator Wise OEE */
	public function getOperatorWiseProduction($data)
	{
		$queryData = array();
		$queryData['tableName'] = $this->production_log;
		$queryData['select'] = 'SUM(production_log.ok_qty - production_log.rej_qty) as ok_qty,SUM(idle_time) as idle_time,SUM(production_time) as shift_hour,production_log.cycle_time as m_ct,SUM(production_log.load_unload_time) as load_unload_time,production_log.log_date,production_log.shift_id,shift_master.shift_name,SUM(production_log.production_qty) as production_qty,production_log.cycle_time,process_master.process_name,item_master.item_code as product_code';
		$queryData['leftJoin']['shift_master'] = 'shift_master.id = production_log.shift_id';
		$queryData['leftJoin']['process_master'] = 'process_master.id = production_log.process_id';
		$queryData['leftJoin']['job_card'] = 'job_card.id = production_log.job_card_id';
		$queryData['leftJoin']['item_master'] = 'item_master.id = job_card.product_id';
		
		$queryData['where']['production_log.operator_id'] = $data['operator_id'];
		$queryData['where']['production_log.prod_type'] = 1;
		$queryData['customWhere'][] = "DATE(production_log.log_date) BETWEEN '" . $data['from_date'] . "' AND '" . $data['to_date'] . "'";
		$queryData['group_by'][] = "production_log.shift_id";
		$queryData['group_by'][] = "DATE(production_log.log_date)";
		$queryData['group_by'][] = "production_log.process_id";
		$queryData['group_by'][] = "job_card.product_id";
		$queryData['order_by']['production_log.log_date'] = 'ASC';
		$result = $this->rows($queryData);

		return $result;
	}

	/** General OEE Report */
	public function getOeeData($data)
	{		
		$queryData = array();
        $queryData['tableName'] = $this->production_log;
        $queryData['select'] = 'SUM(production_log.ok_qty) as ok_qty,pl.rej_qty,pl.rw_qty,SUM(production_log.production_qty) as production_qty,SUM(idle_time) as idle_time,SUM(production_time) as shift_hour,AVG(production_log.cycle_time) as m_ct,production_log.log_date,production_log.shift_id,production_log.operator_id,production_log.process_id,production_log.load_unload_time,production_log.cycle_time,production_log.machine_id,SUM(production_log.load_unload_time) as total_load_unload_time,production_log.part_code,shift_master.shift_name,item_master.item_name,item_master.item_code,machine.item_code as machine_code,job_card.product_id,employee_master.emp_name,process_master.process_name';
        
        $queryData['leftJoin']['shift_master'] = 'shift_master.id = production_log.shift_id';
        $queryData['leftJoin']['job_card'] = 'job_card.id = production_log.job_card_id';
        $queryData['leftJoin']['item_master'] = 'job_card.product_id = item_master.id';
        $queryData['leftJoin']['item_master as machine'] = 'production_log.machine_id = machine.id';
        $queryData['leftJoin']['employee_master'] = 'employee_master.id = production_log.operator_id';
        $queryData['leftJoin']['process_master'] = 'process_master.id = production_log.process_id';
        
        $queryData['leftJoin']['(select SUM(rej_qty) as rej_qty, SUM(rw_qty) as rw_qty,DATE(log_date) as log_date,shift_id,machine_id,operator_id,process_id,job_card_id from production_log where is_delete = 0 group by shift_id,DATE(log_date),machine_id,operator_id,process_id,job_card_id) as pl'] = "pl.shift_id = production_log.shift_id AND DATE(pl.log_date) = DATE(production_log.log_date) AND pl.machine_id = production_log.machine_id AND pl.operator_id = production_log.operator_id AND pl.process_id = production_log.process_id AND pl.job_card_id = production_log.job_card_id";
        
        $queryData['customWhere'][] = "DATE(production_log.log_date) BETWEEN '" . $data['from_date'] . "' AND '" . $data['to_date'] . "'";
        $queryData['where']['production_log.prod_type'] = 1;
        
        $queryData['group_by'][] = "production_log.shift_id";
        $queryData['group_by'][] = "DATE(production_log.log_date)";
        $queryData['group_by'][] = "production_log.machine_id";
        $queryData['group_by'][] = "production_log.operator_id";
        $queryData['group_by'][] = "production_log.process_id";
        $queryData['group_by'][] = "job_card.product_id";
        $result = $this->rows($queryData);
		return $result;
	}

	public function getIdleTimeReasonForOee($log_date, $shift_id, $machine_id, $process_id, $operator_id, $product_id)
	{
		$queryData = array();
		$queryData['tableName'] = $this->production_log;
		$queryData['select'] = 'production_log.idle_reason';
		$queryData['leftJoin']['job_card'] = 'job_card.id = production_log.job_card_id';
		$queryData['where']['production_log.log_date'] = $log_date;
		$queryData['where']['production_log.shift_id'] = $shift_id;
		$queryData['where']['production_log.machine_id'] = $machine_id;
		$queryData['where']['production_log.process_id'] = $process_id;
		$queryData['where']['production_log.operator_id'] = $operator_id;
		$queryData['where']['job_card.product_id'] = $product_id;
		$queryData['where']['production_log.prod_type'] = 1;
		$result = $this->rows($queryData);
		$idleReasonList = $this->comment->getIdleReason();
		$td = '';
		if (!empty($idleReasonList)) {
			foreach ($idleReasonList as $idl) {
				$idleTime = 0;
				foreach ($result as $row) {
					$idleReasonData = !empty($row->idle_reason) ? json_decode($row->idle_reason) : '';

					if (!empty($idleReasonData)) {
						foreach ($idleReasonData as $row1) {
							if ($row1->idle_reason_id == $idl->id) {
								$idleTime += $row1->idle_time;
							}
						}
					}
				}
				$cls=($idleTime > 0) ? 'bg-light' : '';
				$td .= '<td class="'.$cls.'">' . $idleTime . '</td>';
			}
		}
		return $td;
	}
	
	public function getTotaIdleTimeReasonForOee($from_date,$to_date,$machine_id='')
	{
		$queryData = array();
		$queryData['tableName'] = $this->production_log;
		$queryData['select'] = 'production_log.idle_reason';
		$queryData['leftJoin']['job_card'] = 'job_card.id = production_log.job_card_id';
		if(!empty($machine_id)){ $queryData['where']['production_log.machine_id'] = $machine_id; }
		$queryData['customWhere'][] = "DATE(production_log.log_date) BETWEEN '" . $from_date . "' AND '" . $to_date . "'";
		$queryData['where']['production_log.prod_type'] = 1;
		$result = $this->rows($queryData);
		$idleReasonList = $this->comment->getIdleReason();
		$td = '';
		if (!empty($idleReasonList)) {
			foreach ($idleReasonList as $idl) {
				$idleTime = 0;
				foreach ($result as $row) {
					$idleReasonData = !empty($row->idle_reason) ? json_decode($row->idle_reason) : '';

					if (!empty($idleReasonData)) {
						foreach ($idleReasonData as $row1) {
							if ($row1->idle_reason_id == $idl->id) {
								$idleTime += $row1->idle_time;
							}
						}
					}
				}
				$cls=($idleTime > 0) ? 'bg-light' : '';
				$td .= '<td class="'.$cls.'">' . number_format($idleTime/60,2) . '</td>';
			}
		}
		return $td;
	}

	/* Stage Wise Production */
	public function getProductList()
	{
		$queryData['tableName'] = $this->jobCard;
		$queryData['select'] = 'item_master.id,item_master.item_name, item_master.item_code';
		$queryData['join']['item_master'] = 'item_master.id= job_card.product_id';
		//$queryData['where_in']['job_card.order_status'] = [0,1,2,3];
		$queryData['group_by'][] = 'job_card.product_id';
		$queryData['order_by']['item_master.item_code'] = 'ASC';
		return $this->rows($queryData);
	}

	public function getJobs($data)
	{
		$queryData['tableName'] = $this->jobCard;
		$queryData['select'] = 'job_card.id,job_card.job_no,job_card.job_prefix,job_card.job_date,job_card.product_id,job_card.process, job_card.total_out_qty,item_master.item_name, item_master.item_code';
		$queryData['join']['item_master'] = 'item_master.id = job_card.product_id';
		$queryData['customWhere'][] = "job_card.job_date BETWEEN '" . $data['from_date'] . "' AND '" . $data['to_date'] . "'";
		if (!empty($data['item_id'])) {
			$queryData['where']['product_id'] = $data['item_id'];
		}
		$queryData['where_in']['job_card.order_status'] = [0, 1, 2, 3];
		return $this->rows($queryData);
	}

	public function getJobsByTrans($data)
	{
		$queryData['tableName'] = $this->jobCard;
		$queryData['select'] = 'job_card.id,job_card.job_no,job_card.job_prefix,job_card.job_date,job_card.product_id,job_card.process, job_card.total_out_qty, job_card.qty as job_qty,item_master.item_name, item_master.item_code';
		$queryData['join']['item_master'] = 'item_master.id = job_card.product_id';
		$queryData['join']['production_log'] = 'production_log.job_card_id = job_card.id';
		$queryData['customWhere'][] = "DATE(production_log.log_date) BETWEEN '" . $data['from_date'] . "' AND '" . $data['to_date'] . "'";
		if (!empty($data['item_id'])) {
			$queryData['where']['job_card.product_id'] = $data['item_id'];
		}
		//$queryData['where_in']['job_card.order_status'] = [0,1,2,3];
		$queryData['group_by'][] = 'job_card.id';
		return $this->rows($queryData);
	}

	public function getStageWiseProduction($data)
	{
		$jobData = $this->getJobsByTrans($data);
		$allProcess = array();
		if (!empty($jobData)) :
			foreach ($jobData as $row) :
				$allProcess = array_merge(explode(',', $row->process), $allProcess);
			endforeach;
		endif;
		$processList = array_unique($allProcess);
		return ['jobData' => $jobData, "processList" => $processList];
	}

	public function getProductionQty($job_card_id, $process_id)
	{
		$queryData['tableName'] = 'production_approval';
		$queryData['select'] = "SUM(total_ok_qty) as qty";
		$queryData['where']['job_card_id'] = $job_card_id;
		$queryData['where']['in_process_id'] = $process_id;
		$result = $this->row($queryData);
		return $result;
	}

	/* Job card Register */
	public function getJobcardRegister($postData=[])
	{
		$queryData = array();
		$queryData['tableName'] = $this->jobCard;
		$queryData['select'] = 'job_card.*,party_master.party_name,party_master.party_code,item_master.item_code,employee_master.emp_name,job_used_material.batch_no as heat_no,SUM(stock_transaction.qty) as total_ok_qty';
		$queryData['leftJoin']['party_master'] = 'party_master.id = job_card.party_id';
		$queryData['leftJoin']['item_master'] = 'item_master.id = job_card.product_id';
		$queryData['leftJoin']['employee_master'] = 'employee_master.id = job_card.created_by';
		$queryData['leftJoin']['job_used_material'] = 'job_used_material.job_card_id = job_card.id';
		$queryData['leftJoin']['stock_transaction'] = 'stock_transaction.item_id = job_card.product_id AND stock_transaction.ref_id = job_card.id AND stock_transaction.ref_type = 28';
		
		$queryData['customWhere'][] = "DATE(job_card.job_date) BETWEEN '" . $postData['from_date'] . "' AND '" . $postData['to_date'] . "'";
		$queryData['where']['job_card.job_category'] = 0;
		$queryData['where']['job_card.version'] = 2;
		$queryData['group_by'][] = 'job_card.id';
		return $this->rows($queryData);
	}
	
	public function getJobRejData($job_card_id){
	    $queryData['tableName'] = "rej_rw_management";
        $queryData['select'] = 'SUM(qty) as rejection_qty';
        $queryData['where']['job_card_id'] = $job_card_id;
        $queryData['where']['manag_type'] = 1;
        $queryData['where']['reason >'] = 0;
        return $this->row($queryData);
	}

	/* Operator Monitoring */
	public function getOperatorList()
	{
		$data['tableName'] = $this->empMaster;
		$data['select'] = "employee_master.id,employee_master.emp_code,employee_master.emp_name";
		$data['where']['employee_master.emp_role !='] = "-1";
		$data['where']['employee_master.emp_designation'] = 11;
		return $this->rows($data);
	}

	public function getOperatorMonitoring($data)
	{
		$queryData = array();
		$queryData['tableName'] = $this->production_log;
		$queryData['select'] = 'production_log.*,process_master.process_name,item_master.item_name,item_master.item_code,employee_master.emp_name,shift_master.shift_name,job_card.id as job_id, machine.item_code as machine_no';
		$queryData['join']['process_master'] = 'process_master.id = production_log.process_id';
		$queryData['join']['job_card'] = 'job_card.id = production_log.job_card_id';
		$queryData['leftJoin']['item_master'] = 'item_master.id = job_card.product_id';
		$queryData['leftJoin']['item_master as machine'] = 'machine.id = production_log.machine_id';
		$queryData['leftJoin']['employee_master'] = 'employee_master.id = production_log.operator_id';
		$queryData['leftJoin']['shift_master'] = 'shift_master.id = production_log.shift_id';
		$queryData['where']['production_log.prod_type'] = 1;
		$queryData['where']['employee_master.id'] = $data['emp_id'];
		if($data['type'] == 1){$queryData['where']['production_log.ok_qty != '] = 0;}
		if($data['type'] == 2){
			$queryData['customWhere'][] = "DATE(production_log.log_date) BETWEEN '" . $data['from_date'] . "' AND '" . $data['to_date'] . "' AND (production_log.rej_qty != 0 OR production_log.rw_qty != 0)";
		}else{
			$queryData['customWhere'][] = "DATE(production_log.log_date) BETWEEN '" . $data['from_date'] . "' AND '" . $data['to_date'] . "'";
		}
		$queryData['order_by']['production_log.log_date'] = 'ASC';
		return $this->rows($queryData);
	}

	public function getRejectionReworkMonitoring($data)
	{
		$queryData = array();
		$queryData['tableName'] = $this->jobTrans;
		$queryData['select'] = 'job_transaction.*,item_master.item_code, item_master.price, process_master.process_name,shift_master.shift_name,employee_master.emp_name,party_master.party_name,vendor.party_name as vendor_name';
		$queryData['leftJoin']['item_master'] = 'item_master.id = job_transaction.product_id';
		$queryData['leftJoin']['process_master'] = 'process_master.id = job_transaction.process_id';
		$queryData['leftJoin']['employee_master'] = 'employee_master.id = job_transaction.operator_id';
		$queryData['leftJoin']['shift_master'] = 'shift_master.id = job_transaction.shift_id';
		$queryData['leftJoin']['party_master'] = 'party_master.id = job_transaction.rework_from';
		$queryData['leftJoin']['party_master as vendor'] = 'vendor.id = job_transaction.rejection_from';
		$queryData['where_in']['job_transaction.entry_type'] = ['2,3'];
		if ($data['rtype'] == 2) {
			$queryData['where']['job_transaction.rejection_qty >'] = 0;
		}
		/*$queryData['where']['job_transaction.rejection_qty >'] = 0;
		$queryData['where']['job_transaction.rework_qty >'] = 0;*/
		$queryData['customWhere'][] = "job_transaction.entry_date BETWEEN '" . $data['from_date'] . "' AND '" . $data['to_date'] . "'";
		if (!empty($data['item_id'])) {
			$queryData['where_in']['job_transaction.product_id'] = $data['item_id'];
		}
		$jobtransaction = $this->rows($queryData);

		$tbody = '';
		$tfoot = '';
		$clspan = 13;
		if (!empty($jobtransaction)) :
			$i = 1;
			$totalRejectCost = 0;
			foreach ($jobtransaction as $row) :
				if ($row->rejection_qty > 0 || $row->rework_qty > 0) :
					$machine = (!empty($row->machine_id)) ? $this->item->getItem($row->machine_id)->item_code : '';
					$rejection_stage = (!empty($row->rejection_stage)) ? $this->process->getProcess($row->rejection_stage)->process_name : "";
					$rejection_reason = (!empty($row->rejection_reason)) ? $this->comment->getComment($row->rejection_reason)->remark : "";
					$rework_reason = (!empty($row->rework_reason)) ? $this->comment->getComment($row->rework_reason)->remark : "";

					$rework_stage = '';
					$reworkStage = (!empty($row->rework_process_id)) ? explode(',', $row->rework_process_id) : "";
					if (!empty($reworkStage)) {
						$r = 1;
						foreach ($reworkStage as $rework_process_id) :
							if ($r == 1) {
								$rework_stage .= $this->process->getProcess($rework_process_id)->process_name;
							} else {
								$rework_stage .= ', ' . $this->process->getProcess($rework_process_id)->process_name;
							}
							$r++;
						endforeach;
					}

					$item_price = $row->price;
					$jobCardData = $this->jobcard->getJobCard($row->job_card_id);
					if ($jobCardData->party_id > 0) :
						$partyData = $this->party->getParty($jobCardData->party_id);
						if ($partyData->currency != 'INR') :
							$inr = $this->salesReportModel->getCurrencyConversion($partyData->currency);
							if (!empty($inr)) : $item_price = $inr[0]->inrrate * $row->price;
							endif;
						else :
							$item_price = $row->price;
						endif;
					endif;

					$rejectCost = $row->rejection_qty * $item_price;

					$tbody .= '<tr>
                        <td>' . $i++ . '</td>
                        <td>' . formatDate($row->entry_date) . '</td>
                        <td>' . $row->item_code . '</td>
                        <td>' . $row->process_name . '</td>
                        <td>' . $row->shift_name . '</td>
                        <td>' . $machine . '</td>
                        <td>' . $row->emp_name . '</td>
                        <td>' . $row->issue_batch_no . '</td>';
					if ($data['rtype'] == 1) :
						$clspan = 18;
						$tbody .= ' <td>' . $row->rework_qty . '</td>
                        <td>' . $rework_reason . '</td>
                        <td>' . $row->rework_remark . '</td>
    					<td>' . $rework_stage . '</td>
    					<td>' . (!empty($row->party_name) ? $row->party_name : 'IN HOUSE') . '</td>';
					endif;
					$tbody .= '<td>' . $row->rejection_qty . '</td>
                        <td>' . $rejection_reason . '</td>
                        <td>' . $row->rejection_remark . '</td>
                        <td>' . $rejection_stage . '</td>
						<td>' . (!empty($row->vendor_name) ? $row->vendor_name : 'IN HOUSE') . '</td>
                        <td>' . $rejectCost . '</td>
    				</tr>';
					$totalRejectCost += $rejectCost;
				endif;
			endforeach;

			$tfoot .= '<tr class="thead-info">
				<th colspan="' . $clspan . '" class="text-right">Total Reject Cost</th>
				<th>' . $totalRejectCost . '</th>
			</tr>';
		endif;
		return ['status' => 1, 'tbody' => $tbody, 'tfoot' => $tfoot];
	}

	public function getOperatorPerformance($data)
	{
		$data['tableName'] = $this->production_log;
		$data['select'] = 'SUM(production_log.production_qty) as production_qty,SUM(production_log.production_qty) as ok_qty,SUM(idle_time) as idle_time,SUM(production_time) as production_time,AVG(production_log.m_ct) as m_ct,SUM(production_log.load_unload_time) as load_unload_time,production_log.log_date,production_log.shift_id,item_master.item_code, item_master.item_name,production_log.cycle_time';
		$data['join']['job_card'] = "job_card.id = production_log.job_card_id";
		$data['join']['item_master'] = "item_master.id = job_card.product_id";
		$data['where']['production_log.operator_id'] = $data['emp_id'];
		$data['where']['production_log.prod_type'] = 1;
		$data['group_by'][]='production_log.log_date';
		$data['group_by'][]='job_card.product_id';
		$data['customWhere'][] = "production_log.log_date BETWEEN '" . $data['from_date'] . "' AND '" . $data['to_date'] . "'";
		$performance = $this->rows($data);

		$tbody = '';
		$i = 1;
		// print_r($performance);exit;
		if (!empty($performance)) :
			foreach ($performance as $row) :

				$cycle_time=0;
				if (!empty($row->production_time) and !empty($row->cycle_time)) {
					$cycle_time=secondsToMinutes(($row->cycle_time*$row->production_qty));
					$planQty = round($row->production_time / ($cycle_time), 0);
					$productivity =  round((($row->production_qty * 100) / $row->production_time), 2);
				} else {
					$planQty = 0;
					$productivity = 0;
				}

				$tbody .= '<tr>
                    <td>' . $i++ . '</td>
                    <td>' . formatDate($row->log_date) . '</td>
                    <td>' . $row->item_code . '</td>
                    <td>' . $row->production_time . '</td>
                    <td>' . $cycle_time . '</td>
                    <td>' . $planQty . '</td>
                    <td>' . floatval($row->production_qty) . '</td>
                    <td>' . $productivity . ' %</td>
				</tr>';
			endforeach;
		endif;
		return ['status' => 1, 'tbody' => $tbody];
	}

	public function getJobChallan($id = '')
	{
		$data['tableName'] = $this->JobWorkChallan;
		$data['customWhere'][] = "FIND_IN_SET('" . $id . "', job_inward_id)";
		return $this->rows($data);
	}

	public function getItemWiseBom($data)
	{
		$queryData = array();
		$queryData['tableName'] = $this->itemKit;
		$queryData['select'] = 'item_kit.*,item_master.item_name,item_master.item_code';
		$queryData['join']['item_master'] = "item_kit.ref_item_id = item_master.id";
		if (!empty($data['item_id'])) { $queryData['where']['item_kit.item_id'] = $data['item_id']; }
		return $this->rows($queryData);
	}

	public function getProductionBomData($data)
	{
		$queryData = array();
		$queryData['tableName'] = $this->itemKit;
		$queryData['select'] = 'item_kit.*,item_master.item_name,item_master.item_code';
		$queryData['join']['item_master'] = "item_kit.item_id = item_master.id";
		if (!empty($data['ref_item_id'])) {
			$queryData['where']['item_kit.ref_item_id'] = $data['ref_item_id'];
		}
		return $this->rows($queryData);
	}

	public function getRmPlaning($data)
	{
		$queryData = array();
		$queryData['tableName'] = $this->itemKit;
		$queryData['select'] = 'item_kit.*,item_master.item_code,item_master.item_name,unit_master.unit_name,um.unit_name as uname,itm.qty as ref_qty';
		$queryData['join']['item_master'] = "item_kit.item_id = item_master.id";
		$queryData['join']['item_master itm'] = "item_kit.ref_item_id = itm.id";
		$queryData['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
		$queryData['leftJoin']['unit_master um'] = "um.id = itm.unit_id";
		if (!empty($data['ref_item_id'])) {
			$queryData['where']['item_kit.ref_item_id'] = $data['ref_item_id'];
		}
		$result = $this->rows($queryData);

		return $result;
	}

	public function getStockTrans($item_id, $location_id)
	{
		$queryData = array();
		$queryData['tableName'] = "stock_transaction";
		$queryData['select'] = "SUM(qty) as stock_qty,batch_no,location_id,ref_type";
		$queryData['where']['item_id'] = $item_id;
		$queryData['where']['location_id'] = $location_id;
		$queryData['order_by']['id'] = "asc";
		$queryData['group_by'][] = "batch_no";
		return $this->rows($queryData);
	}

	public function getJobcardWIPQty($item_id)
	{
		$queryData = array();
		$queryData['tableName'] = "job_card";
		$queryData['select'] = "SUM(qty) as qty";
		$queryData['where']['product_id'] = $item_id;
		$result = $this->row($queryData);
		return $result;
	}

	public function getPrdLogOnJob($job_card_id)
    {
        $queryData['tableName'] = "production_log";
        $queryData['select'] = 'SUM(rej_qty) as rejection_qty,SUM(rw_qty) as rework_qty,SUM(ok_qty) as ok_qty,SUM(production_qty) as production_qty';
        $queryData['where']['production_log.job_card_id'] = $job_card_id;
        $queryData['where_in']['production_log.prod_type'] = '0,3';
        return $this->row($queryData);
    }
    
    public function getIdleTimeReasonForDailyOee($fromDate, $to_date, $dept_id)
	{
		$queryData = array();
		$queryData['tableName'] = $this->production_log;
		$queryData['select'] = 'production_log.idle_reason';
		$queryData['leftJoin']['item_master'] = 'item_master.id = production_log.machine_id';
		//$queryData['where']['DATE(production_log.log_date)'] = $log_date;
		$queryData['customWhere'][] = "DATE(production_log.log_date) BETWEEN '" . $fromDate . "' AND '" . $to_date . "'";
		$queryData['where']['item_master.location'] = $dept_id;
		$queryData['where']['production_log.prod_type'] = 1;

		$result = $this->rows($queryData);
		$idleReasonList = $this->comment->getIdleReason();
		$td = '';$cls='';
		if (!empty($idleReasonList)) {
			foreach ($idleReasonList as $idl) {
				$idleTime = 0;$cls='';
				foreach ($result as $row) {
					$idleReasonData = !empty($row->idle_reason) ? json_decode($row->idle_reason) : '';

					if (!empty($idleReasonData)) {
						foreach ($idleReasonData as $row1) {
							if ($row1->idle_reason_id == $idl->id) {
								$idleTime += $row1->idle_time;
							}
						}
					}
				}
				$cls=($idleTime > 0) ? 'bg-light' : '';
				$td .= '<td class="'.$cls.'">' . number_format($idleTime/60,2) . '</td>';
			}
		}
		return $td;
	}

	public function getDepartmentWiseOee($data)
	{
		$queryData = array();
		$queryData['tableName'] = $this->production_log;
		$queryData['select'] = 'SUM(production_log.ok_qty) as ok_qty,SUM(production_log.rej_qty) as rej_qty,SUM(production_log.rw_qty) as rw_qty,SUM(production_log.production_qty) as production_qty,SUM(idle_time) as idle_time,SUM(production_time) as shift_hour,AVG(production_log.m_ct) as m_ct,production_log.log_date,production_log.shift_id,production_log.operator_id,production_log.process_id,production_log.load_unload_time,SUM(production_log.load_unload_time) as total_load_unload_time,production_log.cycle_time,production_log.machine_id,,production_log.part_code,shift_master.shift_name,item_master.item_name,item_master.item_code,machine.item_code as machine_code,machine.machine_hrcost,job_card.product_id';
		$queryData['leftJoin']['shift_master'] = 'shift_master.id = production_log.shift_id';
		$queryData['leftJoin']['job_card'] = 'job_card.id = production_log.job_card_id';
		$queryData['leftJoin']['item_master'] = 'job_card.product_id = item_master.id';
		$queryData['leftJoin']['item_master as machine'] = 'production_log.machine_id = machine.id';
		
		
		if(!empty($data['fromDate'])){ 
		    $queryData['customWhere'][] = "DATE(production_log.log_date) BETWEEN '" . $data['fromDate'] . "' AND '" . $data['date'] . "'"; 
		} else { 
		    $queryData['where']['DATE(production_log.log_date)'] = $data['date']; 
		}
		
		$queryData['where']['machine.location'] = $data['dept_id'];
		$queryData['where']['production_log.prod_type'] = 1;
		$queryData['group_by'][] = "production_log.shift_id";
		$queryData['group_by'][] = "DATE(production_log.log_date)";
		$queryData['group_by'][] = "production_log.machine_id";
		$queryData['group_by'][] = "production_log.operator_id";
		$queryData['group_by'][] = "production_log.process_id";
		$queryData['group_by'][] = "job_card.product_id";
		$result = $this->rows($queryData);
		return $result;
	}
	
	/* Bag/Caret Tracking */
	public function getVendorTrackingData($data)
	{
		$queryData = array();
		$queryData['tableName'] = $this->JobWorkChallan;
		$queryData['select'] ='jobwork_challan.*,party_master.party_name';
		$queryData['join']['party_master'] = 'party_master.id = jobwork_challan.vendor_id';
		if(!empty($data['vendor_id'])){ $queryData['where']['jobwork_challan.vendor_id'] = $data['vendor_id']; }
		$queryData['customWhere'][] = "DATE(jobwork_challan.challan_date) BETWEEN '" . $data['from_date'] . "' AND '" . $data['to_date'] . "'";
		return $this->rows($queryData);
	}
	
	/* Finish Good Tracking */
	public function getVendorGoodsTrackingData($data)
	{
		$queryData = array();
		$queryData['tableName'] = "vendor_production_trans";
		$queryData['select'] = "vendor_production_trans.*,jobwork_challan.challan_prefix,jobwork_challan.challan_no,jobwork_challan.challan_date,party_master.party_code,party_master.party_name,item_master.item_code,item_master.item_name";

		$queryData['leftJoin']['jobwork_challan'] = "FIND_IN_SET(vendor_production_trans.id,jobwork_challan.job_inward_id) > 0 AND jobwork_challan.is_delete = 0";
		$queryData['leftJoin']['party_master'] = "vendor_production_trans.vendor_id = party_master.id";
		$queryData['leftJoin']['item_master'] = "vendor_production_trans.product_id = item_master.id";

		$queryData['where']['vendor_production_trans.challan_status'] = 1;
		$queryData['customWhere'][] = "DATE(jobwork_challan.challan_date) BETWEEN '" . $data['from_date'] . "' AND '" . $data['to_date'] . "'";
		if(!empty($data['vendor_id']))
			$queryData['where']['vendor_production_trans.vendor_id'] = $data['vendor_id'];

		$queryData['order_by']['jobwork_challan.challan_no'] = "DESC";

		$result = $this->rows($queryData);
		return $result;
	}
	
	//Created By Karmi @10/08/2022
	public function getFGPlaning($data)
	{
		$queryData = array();
			$queryData['tableName'] = $this->itemKit;
			$queryData['select'] = 'item_kit.*,item_master.item_code,item_master.item_name,unit_master.unit_name';
			$queryData['join']['item_master'] = "item_kit.ref_item_id = item_master.id";
			$queryData['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
			if (!empty($data['fg_item_id'])) {
				$queryData['where']['item_kit.item_id'] = $data['fg_item_id'];
			}
			$result = $this->rows($queryData);

		return $result;
	}
	
    public function getJobCosting($data)
	{
		/*$queryData['tableName'] = "vendor_production_trans";
		$queryData['select'] = "vendor_production_trans.*,job_work_order.jwo_no,job_work_order.jwo_prefix,item_master.item_name,item_master.item_code,process_master.process_name,job_card.process,unit_master.unit_name as punit";
		$queryData['join']['item_master'] = "item_master.id = vendor_production_trans.product_id";
		$queryData['leftJoin']['unit_master'] = "item_master.unit_id = unit_master.id";
		$queryData['leftJoin']['job_work_order'] = "job_work_order.id = vendor_production_trans.job_order_id";
		$queryData['leftJoin']['process_master'] = "process_master.id = vendor_production_trans.process_id";
		$queryData['leftJoin']['production_log'] = "production_log.id = vendor_production_trans.ref_id";
		$queryData['leftJoin']['job_card'] = "job_card.id = vendor_production_trans.job_card_id";
		$queryData['where']['vendor_production_trans.vendor_id'] = $data['vendor_id'];
		$queryData['customWhere'][] = "DATE(production_log.entry_date) BETWEEN '" . $data['from_date'] . "' AND '" . $data['to_date'] . "'";
		$result = $this->rows($queryData);*/
		
		$result = Array();
		return $result;
	}
	
	//Created By Karmi @14/08/2022
    public function getCompletedJobcardList(){
        $data['tableName'] = $this->jobCard;
        $data['select'] = 'job_card.*,item_master.item_code,item_master.item_name';
        $data['join']['item_master'] = 'item_master.id = job_card.product_id';
        $data['where']['job_card.order_status'] = 4;
        $data['where']['job_card.job_date >= '] = $this->startYearDate;                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          
        $data['where']['job_card.job_date <= '] = $this->endYearDate;
        return $this->rows($data); 
    }
	
	//Created By Karmi @14/08/2022
	public function getJobCardWiseCosting($data,$process,$item_id)
	{
		$queryData = array();
		$queryData['tableName'] = $this->product_process;
		$queryData['select'] = 'product_process.*,process_master.process_name,SUM(production_log.production_qty) as outQty';
		$queryData['leftJoin']['production_log'] = "production_log.process_id = product_process.process_id";
 		$queryData['leftJoin']['process_master'] = "process_master.id = product_process.process_id";
		$queryData['where']['product_process.process_id'] = $process;
		$queryData['where']['product_process.item_id '] = $item_id;
		$queryData['where']['production_log.job_card_id '] = $data['job_id'];
		$queryData['where']['production_log.is_delete'] = 0;
		$queryData['group_by'][] = "production_log.process_id";		
		return $this->rows($queryData);
	}
	
    public function getJobWiseRequiredMaterial($postData=[])
	{ 
        $queryData = array();
        $queryData['tableName'] = $this->stockTrans;
        $queryData['select'] = "job_material_dispatch.dispatch_qty AS issue_qty,SUM(CASE WHEN  ref_type = 10 THEN stock_transaction.qty ELSE 0 END)as return_qty,SUM(CASE WHEN  ref_type = 18 THEN stock_transaction.qty ELSE 0 END)as scrap_qty,MAX(CASE WHEN  ref_type = 10 THEN stock_transaction.ref_date ELSE '' END)as return_date,job_bom.job_card_id,job_bom.id,job_bom.ref_item_id,job_bom.qty,job_bom.process_id,item_master.item_name,item_master.item_code,item_master.qty as stockQty,item_master.item_type,job_card.job_prefix,job_card.job_no,job_card.qty as job_qty,job_card.job_date,product.item_code as product_code,job_card.id as job_card_id,job_card.process,material_master.scrap_group";
		$queryData['leftJoin']['job_bom'] = "job_bom.job_card_id = stock_transaction.ref_id AND job_bom.ref_item_id = stock_transaction.item_id";
		$queryData['leftJoin']['job_material_dispatch'] = "job_material_dispatch.job_card_id = job_bom.job_card_id AND job_material_dispatch.dispatch_item_id = job_bom.ref_item_id";
		$queryData['leftJoin']['job_card'] = "stock_transaction.ref_id = job_card.id";
		$queryData['leftJoin']['item_master'] = "stock_transaction.item_id = item_master.id";
		$queryData['leftJoin']['item_master product'] = "job_card.product_id = product.id";
		$queryData['leftJoin']['material_master'] = 'item_master.material_grade = material_master.material_grade';
		$queryData['where']['job_bom.is_delete '] = 0;
		$queryData['where']['job_material_dispatch.is_delete '] = 0;
		
		if(!empty($postData['from_date']) AND !empty($postData['to_date'])){
			$queryData['where']['job_card.job_date >= '] = date('Y-m-d',strtotime($postData['from_date']));
			$queryData['where']['job_card.job_date <= '] = date('Y-m-d',strtotime($postData['to_date']));
		}else{
			$queryData['where']['job_card.job_date >= '] = $this->startYearDate;
			$queryData['where']['job_card.job_date <= '] = $this->endYearDate;
		}
		
		
        if(isset($postData['order_status']) && $postData['order_status'] == 0 && (!empty($postData['order_status']))){
            $queryData['where_in']['job_card.order_status'] = [0,1,2];
            $queryData['where']['job_card.is_npd'] = 0;
        }
        if(isset($postData['order_status']) && $postData['order_status'] == 1){
            $queryData['where_in']['job_card.order_status'] = [4];
        }
        if(isset($postData['order_status']) && $postData['order_status'] == 2){
            $queryData['where_in']['job_card.order_status'] = [5,6];
        }
        if(isset($postData['order_status']) && $postData['order_status'] == 3){
            $queryData['where_in']['job_card.order_status'] = [3];
        }
        if(isset($postData['order_status']) && $postData['order_status'] == 4){
            $queryData['where_in']['job_card.order_status'] = [0,1,2];
            $queryData['where']['job_card.is_npd'] = 1;
        }
		
		$queryData['order_by']['job_card.job_no'] = 'DESC';
		$queryData['group_by'][] = 'job_bom.job_card_id,job_bom.ref_item_id';
        $result = $this->rows($queryData);
        return $result;
    }
	
	public function getScrapQty($data) {
		$queryData = array();
		$queryData['tableName'] = 'stock_transaction';
		$queryData['select'] = "SUM(qty) as scrap_qty";
		$queryData['where']['ref_id'] = $data['ref_id'];
		$queryData['where']['item_id'] = $data['item_id'];
		$queryData['where']['ref_type'] = $data['ref_type'];
		$scrapData = $this->row($queryData);
		return $scrapData;
	}
	
    public function getFirsJobOutQty($job_card_id)
	{
        $queryData = array();
        $queryData['tableName'] = $this->jobCard;
        $queryData['select'] = "SUM(CASE WHEN pa.in_process_id = 0 THEN pa.out_qty ELSE 0 END) as used_qty, (CASE WHEN pa.in_process_id = 0 THEN (pa.in_qty - pa.out_qty - pa.total_rejection_qty) ELSE 0 END) as pend_qty";
	    $queryData['join']['production_approval pa'] = "job_card.id = pa.job_card_id";
		$queryData['where']['job_card.id '] = $job_card_id;
		$queryData['where']['pa.is_delete'] = 0;
        $result = $this->row($queryData);
        return $result;
    }
    
    public function getWIPStockData($product_id)
	{
		$queryData['tableName'] = "production_approval";
        $queryData['select'] = '(SUM(production_approval.in_qty)-SUM(production_approval.out_qty)-SUM(production_approval.total_rejection_qty)) as wip_qty,process_master.process_name';
		$queryData['leftJoin']['process_master'] = "process_master.id = production_approval.in_process_id";
		$queryData['leftJoin']['job_card'] = "job_card.id = production_approval.job_card_id";
		$queryData['where']['production_approval.product_id'] = $product_id;
		$queryData['where']['job_card.job_date >= '] = $this->startYearDate;
        $queryData['where']['job_card.job_date <= '] = $this->endYearDate;
        $queryData['customWhere'][] = '(production_approval.in_qty-production_approval.out_qty-production_approval.total_rejection_qty) > 0';
		$queryData['group_by'][] = 'production_approval.in_process_id';
		$result = $this->rows($queryData);
		return $result;
	}

	/** 
    *   Machine Log Summary Report 
    *   Created By : Chauhan Milan
    *   Created At : 04-04-2023
    *   Note : SYNC Log Data from Smarttrack.nativebittechnologies.com and import in device_log table
    **/
	public function syncDeviceData($data)
	{
		try {
            $this->db->trans_begin();

			$client_key = "c81e728d9d4c2f636f067f89cc14862c";
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://smarttrack.nativebittechnologies.com/api/v1/machineLogs/syncMachineLog/".$client_key."/".$data['from_date']."/".$data['to_date'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_SSL_VERIFYHOST => FALSE,
		        CURLOPT_SSL_VERIFYPEER => FALSE,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
				CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
                //CURLOPT_POSTFIELDS => json_encode($postData)
            ));

            $response = curl_exec($curl);
            $error = curl_error($curl);
            curl_close($curl);

            $ids = array();
            if($error):
                $result = ['status'=>0,'message'=>'Somthing is wrong. cURL Error #:'. $error]; 
            else:
                $response = json_decode($response); $machineIds = array();$ids=array();
				
				foreach($response->data->machineLogs as $row):
					$ids[] = $row->id;
					$employeeData = $this->employee->getEmployeeOnCode(['emp_code'=>$row->created_by]);
                    $processData = $this->process->getProcessOnProcessNo($row->process_no);
                    $machineData = $this->machine->getMachineOnDeviceNo($row->device_no);
					$commentData = array();
                    if(!empty($row->reason_no)):
						$type = ($row->log_type == 1)?1:2;
                        $commentData = $this->comment->getCommentOnCode($row->reason_no,$type);
                    endif;

					if(!empty($machineData)):
						$machineIds[] = $machineData->id;
					endif;

					$queryData = array();
					$queryData['tableName'] = "job_card";
					$queryData['where_in']['order_status'] = [0,1,2,3];
					$queryData['where']['job_no'] = $row->job_no;
					$jobData = $this->row($queryData);

					$row->machine_id = (!empty($machineData))?$machineData->id:0;
					$row->job_id = (!empty($jobData))?$jobData->id:0;
					$row->process_id = (!empty($processData))?$processData->id:0;
					$row->reason_id = (!empty($commentData))?$commentData->id:0;
					$row->operator_id = (!empty($employeeData))?$employeeData->id:0;
					$row->operator_code = (!empty($employeeData))?$employeeData->emp_code:0;
					$row->sync_id = $row->id;
					$row->created_by = $this->loginId;
					
					unset($row->id,$row->created_by,$row->client_id,$row->sync_status);					

					$oldData = array();
					$oldData['tableName'] = $this->deviceLog;
					$oldData['where']['sync_id'] = $row->sync_id;
					$oldRow = $this->row($oldData);
					$row->id = (!empty($oldRow))?$oldRow->id:"";

					$row = (array) $row;
					$this->store($this->deviceLog,$row);
					unset($row['id']);
				endforeach;
			endif;

			$result = ['status'=>1,'message'=>'Data sync sucessfully.','ids'=>$ids];

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
	}

	public function changeSyncStatus($ids)
	{  
		if(!empty($ids)):
			$postData['ids'] = $ids;
			$postData['client_key'] = "c81e728d9d4c2f636f067f89cc14862c";
			$curl = curl_init();
			curl_setopt_array($curl, array(
				CURLOPT_URL => "https://smarttrack.nativebittechnologies.com/api/v1/machineLogs/changeSyncStatus",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_SSL_VERIFYHOST => FALSE,
				CURLOPT_SSL_VERIFYPEER => FALSE,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
				CURLOPT_POSTFIELDS => json_encode($postData)
			));

			$response = curl_exec($curl);
			$error = curl_error($curl);
			curl_close($curl);
			$response = json_decode($response);
			return $response;
		else:
			return true;
		endif;
    }

	public function getMachineLogSummaryData($postData)
	{
		$from_date = $postData['from_date']." 00:00:00";
		$to_date = $postData['to_date']." 23:59:59";

		$queryData = array();
		$queryData['tableName'] = $this->deviceLog;
		if($postData['report_type'] == 1):
			$queryData['select'] = "CONCAT(
				DATE_FORMAT(MIN(device_log.entry_date), '%d-%m-%Y 00:00:00'), 
				' to ', 
				DATE_FORMAT(MAX(device_log.entry_date), '%d-%m-%Y %H:%i:%s') 
			) AS duration, 
			CAST(
				TIME_TO_SEC(
					TIMEDIFF(
						DATE_FORMAT(MAX(device_log.entry_date), '%Y-%m-%d %H:%i:%s'),
						DATE_FORMAT(MIN(device_log.entry_date), '%Y-%m-%d 00:00:00')
					)
				)  
			AS int) as sec_diff , 
			job_card.job_prefix,
			job_card.job_no, 
			machine_master.item_code as machine_code, 
			item_master.item_code, 
			process_master.process_name, 
			TIME_TO_SEC(product_process.cycle_time) as cycle_time, 
			employee_master.emp_name as operator_name, 
			employee_master.emp_code as operator_code,
			COUNT(device_log.part_count) as prod_qty";

			$queryData['group_by'][] = "device_log.job_id,device_log.machine_id,device_log.process_id,device_log.operator_id";
			$queryData['order_by']["device_log.entry_date"] = "ASC";
		elseif($postData['report_type'] == 2):
			$queryData['select'] = "CONCAT(
				DATE_FORMAT(MIN(device_log.entry_date), '%d-%m-%Y 00:00:00'), 
				' to ', 
				DATE_FORMAT(MAX(device_log.entry_date), '%d-%m-%Y %H:%i:%s') 
			) AS duration, 
			CAST(  
				TIME_TO_SEC(
					TIMEDIFF(
						DATE_FORMAT(MAX(device_log.entry_date), '%Y-%m-%d %H:%i:%s'),
						DATE_FORMAT(MIN(device_log.entry_date), '%Y-%m-%d 00:00:00')
					)
				)  
			AS int) as sec_diff , 
			job_card.job_prefix,
			job_card.job_no, 
			machine_master.item_code as machine_code, 
			item_master.item_code, 
			process_master.process_name, 
			TIME_TO_SEC(product_process.cycle_time) as cycle_time, 
			employee_master.emp_name as operator_name, 
			employee_master.emp_code as operator_code,
			COUNT(device_log.part_count) as prod_qty";

			$queryData['group_by'][] = "DATE_FORMAT(device_log.entry_date, '%d-%m-%Y'),device_log.job_id,device_log.machine_id,device_log.process_id,device_log.operator_id";

			$queryData['order_by']["device_log.entry_date"] = "ASC";
		else:
			$queryData['select'] = "CONCAT(
				DATE_FORMAT(device_log.entry_date, '%d-%m-%Y'), 
				' ', 
				DATE_FORMAT(device_log.entry_date, '%H:00:00'), 
				' to ', 
				(CASE WHEN DATE_FORMAT(device_log.entry_date, '%Y-%m-%d %H') = lr.max_hr THEN DATE_FORMAT(MAX(device_log.entry_date), '%H:%i:%s') ELSE DATE_FORMAT(DATE_ADD(DATE_FORMAT(device_log.entry_date, '%Y-%m-%d %H:00:00'), INTERVAL 3599 SECOND), '%H:%i:%s') END)
			) AS duration, 
			CAST( 
				TIME_TO_SEC(
					TIMEDIFF(
						(CASE WHEN DATE_FORMAT(device_log.entry_date, '%Y-%m-%d %H') = lr.max_hr THEN DATE_FORMAT(MAX(device_log.entry_date), '%Y-%m-%d %H:%i:%s') ELSE DATE_FORMAT(DATE_ADD(DATE_FORMAT(device_log.entry_date, '%Y-%m-%d %H:00:00'), INTERVAL 3599 SECOND), '%Y-%m-%d %H:%i:%s') END),
						
						DATE_FORMAT(device_log.entry_date, '%Y-%m-%d %H:00:00')
					)
				) 
			AS int) as sec_diff, 
			`job_card`.`job_prefix`, 
			`job_card`.`job_no`, 
			`machine_master`.`item_code` as `machine_code`, 
			`item_master`.`item_code`, 
			`process_master`.`process_name`, 
			TIME_TO_SEC(ifnull(product_process.cycle_time,'00:00:00')) as cycle_time, 
			`employee_master`.`emp_name` as `operator_name`, 
			`employee_master`.`emp_code` as `operator_code`, 
			COUNT(device_log.part_count) as prod_qty";

			$queryData['leftJoin'][" ( SELECT DATE_FORMAT(MIN(entry_date), '%Y-%m-%d %H') as min_hr,
			DATE_FORMAT(MAX(entry_date), '%Y-%m-%d %H') as max_hr,DATE_FORMAT(entry_date, '%Y-%m-%d') as e_date,
			job_id,machine_id,process_id,operator_id FROM device_log WHERE entry_date BETWEEN '".$from_date."' AND '".$to_date."' AND log_type = 1 AND job_id > 0 AND is_delete = 0 GROUP BY DATE_FORMAT(entry_date, '%Y-%m-%d'),job_id,machine_id,process_id,operator_id  ) as lr "] = "`device_log`.`job_id` = `lr`.`job_id` AND `device_log`.`machine_id` = `lr`.`machine_id` AND `device_log`.`process_id` = `lr`.`process_id` AND `device_log`.`operator_id` = `lr`.`operator_id` AND `lr`.`e_date` = DATE_FORMAT(device_log.entry_date, '%Y-%m-%d')";

			$queryData['group_by'][] = "DATE_FORMAT(device_log.entry_date, '%Y-%m-%d %H'),device_log.job_id,device_log.machine_id,device_log.process_id,device_log.operator_id";

			$queryData['order_by']["device_log.entry_date"] = "ASC";
		endif;

		$queryData['leftJoin']['job_card'] = "device_log.job_id = job_card.id";
		$queryData['leftJoin']['process_master'] = "device_log.process_id = process_master.id";
		$queryData['leftJoin']['item_master'] = "job_card.product_id = item_master.id";
		$queryData['leftJoin']['item_master as machine_master'] = "device_log.machine_id = machine_master.id";
		$queryData['leftJoin']['employee_master'] = "device_log.operator_id = employee_master.id";
		$queryData['leftJoin']['product_process'] = "job_card.product_id = product_process.item_id AND device_log.process_id = product_process.process_id AND product_process.is_delete = 0";

		$queryData['where']['device_log.log_type'] = 1;
		if(!empty($postData['machine_id'])):
			$queryData['where']['device_log.machine_id'] = $postData['machine_id'];
		endif;
		$queryData['where']['device_log.job_id >'] = 0;
		$queryData['where']['device_log.entry_date >='] = $from_date;
		$queryData['where']['device_log.entry_date <='] = $to_date;

		$result = $this->rows($queryData);
		return $result;
	}
	/* End Machine Log Summary */
	
	public function getcostingReportData($data){
        $queryData = array();
		$queryData['tableName'] = $this->jobCard;
		$queryData['select'] = "job_card.*, jobApproval.total_ok_qty, jobApproval.total_rejection_qty, prdProcess.process_costing, SUM(IFNULL(jobBom.mcost,0)) as rm_cost";
        $queryData['leftJoin']['job_bom'] = "job_bom.job_card_id = job_card.id";
        $queryData['leftJoin']['item_master'] = "item_master.id = job_bom.ref_item_id";
        
		$queryData['leftJoin']['(select job_card_id,total_ok_qty,total_rejection_qty from production_approval where out_process_id = 0 AND is_delete = 0) jobApproval'] = "jobApproval.job_card_id = job_card.id";
		
		$queryData['leftJoin']['(select SUM(costing) as process_costing, item_id from product_process group by item_id) as prdProcess'] = "prdProcess.item_id = job_bom.item_id";

		// $queryData['leftJoin']['(select (job_bom.qty * gt.price) as mcost, job_bom.job_card_id from job_bom left join (select price,item_id from grn_transaction where is_delete = 0 order by created_at desc limit 1) as gt on job_bom.ref_item_id = gt.item_id) as jobBom'] = "jobBom.job_card_id = job_card.id";
		$queryData['leftJoin']['(select (job_bom.qty * gt.price) as mcost, job_bom.job_card_id from job_bom left join (select price,item_id,row_number() over(partition by item_id order by created_at desc) as rn from grn_transaction where is_delete = 0) as gt on job_bom.ref_item_id = gt.item_id where rn=1 ) as jobBom'] = "jobBom.job_card_id = job_card.id";
		
		if(!empty($data['item_id'])){
            $queryData['where']['item_master.id'] = $data['item_id'];
        }
		
		$queryData['group_by'][] = "jobBom.job_card_id";
		$queryData['order_by']['job_card.id'] = 'DESC';
		$result = $this->rows($queryData);
        return $result;
    }

	public function getActualMfgCost($data){
		$queryData['tableName'] = "production_log";
		$queryData['select']  = "SUM(production_log.production_qty * production_log.mfg_cost) AS total+process_cost,SUM(production_log.rej_qty) AS total_rej_qty";
		$queryData['leftJoin']['production_approval'] = 'production_approval.id = production_log.job_approval_id';
		$queryData['leftJoin']['process_master'] = 'process_master.id = production_log.process_id';		
		$queryData['where_in']['production_log.prod_type'] = '1,2,3';
		$queryData['where']['production_log.job_card_id'] = $data['job_card_id'];
		$queryData['group_by'] = 'production_log.job_approval_id';
		$queryData['order_by']['production_approval.id'] = 'ASC';
		return $this->rows($queryData);
	}
}
?>