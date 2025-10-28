<?php
class BiometricModel extends MasterModel{
    private $deviceMasterTable = "device_master";
    private $devicePunchesTable = "device_punches";
    private $attendanceLog = "attendance_log";
	
	/**** NEW STRUCTURE (attendance_log) | Created By JP @09-12-2022 ***/
	public function syncDevicePunches(){
		$ddQuery['tableName'] = $this->deviceMasterTable;
		$ddQuery['where']['id'] = 1;
		$deviceData = $this->rows($ddQuery);

		if (!empty($deviceData)) :
			foreach ($deviceData as $row) :
				$last_synced = (!empty($row->last_sync_at)) ? date('Y-m-d',strtotime($row->last_sync_at)) : date('Y-m-d',strtotime($row->issued_at));
				$begin = new DateTime( date( 'Y-m-d', strtotime( $last_synced . ' -1 day' ) ) );
				$end = new DateTime( date( 'Y-m-d' ));
				$end = $end->modify( '+1 day' ); 

				$row->Empcode = 'ALL';
				$row->FromDate = date("d/m/Y_00:01", strtotime($begin->format("Y-m-d")));
				$row->ToDate = date("d/m/Y_23:59", strtotime($end->format("Y-m-d")));

				$punchData = new StdClass();
				$punchData = $this->callDeviceApi($row);
				if (!empty($punchData)) {
					foreach ($punchData as $punch) {
					    
						$this->db->where('punch_type',1);
						//$this->db->where('device_id',$row->id);
						$this->db->where('punch_date',date('Y-m-d H:i:s', strtotime(strtr($punch->PunchDate, '/', '-'))));
						$this->db->where('emp_code',$punch->Empcode);
						$oldData = $this->db->get($this->attendanceLog)->row();
						//print_r($oldData);
						
						if (empty($oldData)) {
							$logData = array();
							$empData = $this->shiftModel->getEmpShiftByEmpcode($punch->Empcode);

							if (!empty($empData)) {
								$logData['id'] = "";
								$logData['punch_type'] = 1;
								$logData['device_id'] = $row->id;
								$logData['punch_date'] = date('Y-m-d H:i:s', strtotime(strtr($punch->PunchDate, '/', '-')));
								$logData['emp_id'] = $empData->id;
								$logData['emp_code'] = $punch->Empcode;
								$logData['created_at'] = date('Y-m-d H:i:s');
								$logData['created_by'] = $this->loginID;
                                
								$this->store($this->attendanceLog, $logData, 'Attandance Log');
							}
						};
					}
				}
                
				$updateSyncStatus = ['id' => $row->id, 'last_sync_at' => date('Y-m-d H:i:s')];
				$this->store($this->deviceMasterTable, $updateSyncStatus, 'Attandance');
			endforeach;
			return ['status' => 1, 'message' => 'Device Synced successfully.', 'lastSyncedAt' => date('j F Y, g:i a')];
		else :
			return ['status' => 0, 'message' => 'You have no any Devices!'];
		endif;
	}
	
	public function syncDeviceData()
	{
        $ddQuery['tableName'] = $this->deviceMasterTable;
        $ddQuery['where']['id'] = 1;
        $deviceData = $this->rows($ddQuery);
		if(!empty($deviceData)):
			foreach($deviceData as $row):
				$last_synced = (!empty($row->last_sync_at)) ? date('Y-m-d',strtotime($row->last_sync_at)) : date('Y-m-d',strtotime($row->issued_at));
				
				$begin = new DateTime( date( 'Y-m-d', strtotime( $last_synced . ' -1 day' ) ) );
				//$begin = new DateTime( date( 'Y-m-d', strtotime( $last_synced) ) );
				$end = new DateTime( date( 'Y-m-d' ));
				$end = $end->modify( '+1 day' ); 
				
				$interval = new DateInterval('P1D');
				$daterange = new DatePeriod($begin, $interval ,$end);
				
				foreach($daterange as $date){
					$currentDate =  date("Y-m-d",strtotime($date->format("Y-m-d")));
					$row->Empcode = 'ALL';
					$row->FromDate = date("d/m/Y_00:01",strtotime($date->format("Y-m-d")));
					$row->ToDate = date("d/m/Y_23:59",strtotime($date->format("Y-m-d")));
					
					$punchData = New StdClass();
					$punchData = $this->callDeviceApi($row);
					$pcData = Array();
					if(!empty($punchData)):
						$dd1Query['tableName'] = $this->devicePunchesTable;
						$dd1Query['where']['punch_date'] = $currentDate;
						$oldData = $this->row($dd1Query);
						$pnchData = Array();
						
						if(empty($oldData)):
							$pnchData = ['id'=>"",'device_id'=>$row->id, 'punch_date'=>$currentDate, 'punch_data'=>json_encode($punchData),'created_by'=>$this->loginID];
							$this->store($this->devicePunchesTable,$pnchData,'Attandance');
						else:
							$pnchData = ['id'=>$oldData->id, 'punch_date'=>$currentDate, 'punch_data'=>json_encode($punchData)];
							$this->store($this->devicePunchesTable,$pnchData,'Attandance');
						endif;
						
						// Add Records to new Table
						$res = $this->syncDeviceDataNew($punchData,$currentDate);
					endif;
				}
				$updateSyncStatus = ['id'=>$row->id,'last_sync_at'=>date( 'Y-m-d H:i:s')];
				$this->store($this->deviceMasterTable,$updateSyncStatus,'Attandance');
			endforeach;
			return ['status'=>1,'message'=>'Device Synced successfully.','lastSyncedAt'=>date('j F Y, g:i a')];
		else:
			return ['status'=>0,'message'=>'You have no any Devices!'];
		endif;
	}
	
	public function getAttendanceLog($FromDate,$ToDate,$empId)
	{
		$queryData['tableName'] = $this->attendanceLog;
		$queryData['customWhere'][] = 'punch_date BETWEEN "'.date('Y-m-d',strtotime($FromDate)).'" AND "'.date('Y-m-d',strtotime($ToDate)).'"';
 		$queryData['where']['emp_id'] = $empId;
        return $this->rows($queryData);
	}
	
	/*** Created By JP@10-12-2022 ***/
	public function getAttendanceLogByEmp($punchDate, $empId)
	{
		$toDate = date('Y-m-d',strtotime($punchDate . ' +1 day'));
		$queryData['tableName'] = 'attendance_log';
		$queryData['customWhere'][] = 'punch_date BETWEEN "' . date('Y-m-d H:i:s', strtotime($punchDate . ' 00:00:01')) . '" AND "' . date('Y-m-d H:i:s', strtotime($toDate . ' 23:59:59')) . '"';
		$queryData['where']['emp_id'] = $empId;
		$queryData['where_in']['punch_type'] = "1,2";
		return $this->row($queryData);
	}
		
    /*** Created By : JP@27.12.2022***/
	public function getEmpPunchesByDate($empId,$currentDate,$shift_id="")
	{
		$day = date('d',strtotime($currentDate));$month = date('m',strtotime($currentDate));$year = date('Y',strtotime($currentDate));
		$shiftCondition = (!empty($shift_id)) ? ' AND emp_shiftlog.d'.intval($day).' = '.$shift_id : '';
		$attendanceData = $this->db->query("SELECT emp_shiftlog.emp_id,ROUND(TIME_TO_SEC(shift_master.total_shift_time)) as ts_time, ROUND(TIME_TO_SEC(shift_master.total_lunch_time)) as lunch_time,shift_master.lunch_start,shift_master.lunch_end, shift_master.shift_type,shift_master.shift_name,CAST(CONCAT('".$currentDate."', ' ', shift_master.shift_start) as datetime) as shift_start_time,
		CAST(DATE_SUB(CONCAT('".$currentDate."', ' ', shift_master.shift_start), INTERVAL 3 HOUR) as datetime) as shiftStart,
		CAST(DATE_ADD(CONCAT('".$currentDate."', ' ', shift_master.shift_start), INTERVAL 75559 SECOND) as datetime) as shiftEnd,
		(
			SELECT GROUP_CONCAT(al.punch_date) FROM attendance_log as al WHERE al.emp_id = ".$empId." AND al.punch_type IN (1,2) AND al.punch_date >= shiftStart AND al.punch_date <= shiftEnd AND al.is_delete=0
		) as punch_date,
		(
			SELECT GROUP_CONCAT(al.id) FROM attendance_log as al WHERE al.emp_id = ".$empId." AND al.punch_type IN (1,2) AND al.punch_date >= shiftStart AND al.punch_date <= shiftEnd AND al.is_delete=0
		) as punch_id,
		(
			SELECT GROUP_CONCAT(al.punch_type) FROM attendance_log as al WHERE al.emp_id = ".$empId." AND al.punch_type IN (1,2) AND al.punch_date >= shiftStart AND al.punch_date <= shiftEnd AND al.is_delete=0
		) as punch_type,
		(
			SELECT SUM(((ex_hours*60)+ex_mins)*xtype) as ex_mins FROM attendance_log as al WHERE al.emp_id = ".$empId." AND al.punch_type=3 AND DATE_FORMAT(al.punch_date, '%Y-%m-%d') = '".$currentDate."' AND al.is_delete=0
		) as xmins
        FROM emp_shiftlog
		LEFT JOIN shift_master ON shift_master.id = emp_shiftlog.d".intval($day)."
		WHERE emp_shiftlog.emp_id = ".$empId." AND MONTH(emp_shiftlog.month) = ".$month." AND YEAR(emp_shiftlog.month) = ".$year.$shiftCondition)->result();
		//$this->printQuery();exit;
        return $attendanceData;
	}
	
    /*** Created By : JP@27.12.2022***/
	public function getEmpPunchesByDateAlog($empId,$currentDate,$shift_id="")
	{
		$day = date('d',strtotime($currentDate));$month = date('m',strtotime($currentDate));$year = date('Y',strtotime($currentDate));
		$shiftCondition = (!empty($shift_id)) ? ' AND emp_shiftlog.d'.intval($day).' = '.$shift_id : '';
		$attendanceData = $this->db->query("SELECT emp_shiftlog.emp_id,ROUND(TIME_TO_SEC(shift_master.total_shift_time)) as ts_time, ROUND(TIME_TO_SEC(shift_master.total_lunch_time)) as lunch_time,shift_master.lunch_start,shift_master.lunch_end, shift_master.shift_type,shift_master.shift_name,CAST(CONCAT('".$currentDate."', ' ', shift_master.shift_start) as datetime) as shift_start_time,
		CAST(DATE_SUB(CONCAT('".$currentDate."', ' ', shift_master.shift_start), INTERVAL 3 HOUR) as datetime) as shiftStart,
		CAST(DATE_ADD(CONCAT('".$currentDate."', ' ', shift_master.shift_start), INTERVAL 75559 SECOND) as datetime) as shiftEnd,
		(
			SELECT GROUP_CONCAT(al.punch_date) FROM attendance_log as al WHERE al.emp_id = ".$empId." AND al.punch_type IN (1,2) AND al.punch_date >= shiftStart AND al.punch_date <= shiftEnd AND al.is_delete=0
		) as punch_date,
		(
			SELECT SUM(((ex_hours*60)+ex_mins)*xtype) as ex_mins FROM attendance_log as al WHERE al.emp_id = ".$empId." AND al.punch_type=3 AND DATE_FORMAT(al.punch_date, '%Y-%m-%d') = '".$currentDate."' AND al.is_delete=0
		) as xmins
        FROM emp_shiftlog
		LEFT JOIN shift_master ON shift_master.id = emp_shiftlog.d".intval($day)."
		WHERE emp_shiftlog.emp_id = ".$empId." AND MONTH(emp_shiftlog.month) = ".$month." AND YEAR(emp_shiftlog.month) = ".$year.$shiftCondition)->result();
		//$this->printQuery();exit;
        return $attendanceData;
	}
	
    /*** Created By : JP@27.12.2022***/
	public function getEmpShiftLog($postData = [])
	{
		$queryData['tableName'] = 'emp_shiftlog';
		$queryData['select'] = 'emp_shiftlog.*,employee_master.emp_code,employee_master.emp_name, department_master.name as dept_name, emp_designation.title as emp_dsg, emp_category.category';		
		$queryData['join']['employee_master'] = "employee_master.id = emp_shiftlog.emp_id";
		$queryData['leftJoin']['emp_designation'] = "emp_designation.id = employee_master.emp_designation";
        $queryData['leftJoin']['department_master'] = "employee_master.emp_dept_id = department_master.id";
        $queryData['leftJoin']['emp_category'] = "employee_master.emp_category = emp_category.id";
		if(!empty($postData['emp_id'])){$queryData['where']['emp_shiftlog.emp_id'] = $postData['emp_id'];}
		if(!empty($postData['biomatric_id'])){$queryData['where']['employee_master.biomatric_id'] = $postData['biomatric_id'];}
		if(!empty($postData['dept_id'])){$queryData['where']['employee_master.emp_dept_id'] = $postData['dept_id'];}
		$queryData['where_in']['MONTH(emp_shiftlog.month)'] = date('m',strtotime($postData['month']));
		$queryData['where_in']['YEAR(emp_shiftlog.month)'] = date('Y',strtotime($postData['month']));
		//$queryData['where']['employee_master.is_active'] = 1;
		$queryData['where']['employee_master.attendance_status'] = 1;
		
		if(!empty($postData['record_limit'])){
		    $postData['record_limit'] = (($postData['record_limit'] == 1) ? 0 : $postData['record_limit']);
			$queryData['length'] = 20;
			$queryData['start'] = $postData['record_limit'];
		}
		
		$result = $this->rows($queryData);
		//$this->printQuery();
		return $result;
	}
	
	/*** Created By : JP@28.12.2022***/
	public function getMissedPunch($currentDate)
	{
		$day = date('d',strtotime($currentDate));$month = date('m',strtotime($currentDate));$year = date('Y',strtotime($currentDate));
		$attendanceData = $this->db->query("SELECT emp_shiftlog.emp_id,shift_master.shift_name ,employee_master.emp_code, employee_master.emp_name, department_master.name as dept_name, emp_designation.title as emp_dsg,
		CAST(DATE_SUB(CONCAT('".$currentDate."', ' ', shift_master.shift_start), INTERVAL 3 HOUR) as datetime) as shiftStart,
		CAST(DATE_ADD(CONCAT('".$currentDate."', ' ', shift_master.shift_start), INTERVAL 75559 SECOND) as datetime) as shiftEnd,
		(
			SELECT COUNT(*) FROM attendance_log as al WHERE al.emp_id = emp_shiftlog.emp_id AND al.punch_type IN (1,2) AND al.punch_date >= shiftStart AND al.punch_date <= shiftEnd AND al.is_delete=0
		) as punchCount
        FROM emp_shiftlog
		LEFT JOIN shift_master ON shift_master.id = emp_shiftlog.d".intval($day)."
		LEFT JOIN employee_master ON employee_master.id = emp_shiftlog.emp_id
		LEFT JOIN department_master ON department_master.id = employee_master.emp_dept_id
		LEFT JOIN emp_designation ON emp_designation.id = employee_master.emp_designation
		WHERE employee_master.attendance_status = 1 AND MONTH(emp_shiftlog.month) = ".$month." AND YEAR(emp_shiftlog.month) = ".$year." GROUP BY emp_shiftlog.emp_id HAVING (((punchCount%2) = 1) OR (punchCount > 4))")->result();
		//$this->printQuery();
        return $attendanceData;
	}
	
	/*** Created By : JP@04.01.2022 ***/
	public function getAllPunches($postData = [])
	{
		$report_date = $postData['report_date'];
		$day = date('d',strtotime($report_date));$month = date('m',strtotime($report_date));$year = date('Y',strtotime($report_date));
		$countCondition = '';$punchTypeCondition = '';
		
		if(empty($postData['punch_status'])){$countCondition = ' HAVING punchCount > 0';}
		else
		{
			// Missed Punches Odd OR > 4
			//if($postData['punch_status'] == 1){$countCondition = ' HAVING (((punchCount%2) = 1) OR (punchCount > 4))';}
			// Missed Punches Only Odd
			if($postData['punch_status'] == 1){$countCondition = ' HAVING ((punchCount%2) = 1)';}
			// Absent Punch
			if($postData['punch_status'] == 2){$countCondition = ' HAVING punchCount = 0';}
		}
		
		$attendanceData = $this->db->query("SELECT emp_shiftlog.emp_id,shift_master.shift_name, shift_master.shift_type, employee_master.emp_code, employee_master.emp_name, department_master.name as dept_name, emp_designation.title as emp_dsg,
		CAST(DATE_SUB(CONCAT('".$report_date."', ' ', shift_master.shift_start), INTERVAL 3 HOUR) as datetime) as shiftStart,
		CAST(DATE_ADD(CONCAT('".$report_date."', ' ', shift_master.shift_start), INTERVAL 75559 SECOND) as datetime) as shiftEnd,
		(
			SELECT GROUP_CONCAT(al.punch_date) FROM attendance_log as al WHERE al.emp_id = emp_shiftlog.emp_id AND al.punch_type IN (1,2) AND al.punch_date >= shiftStart AND al.punch_date <= shiftEnd AND al.is_delete=0
		) as punch_date,
		(
			SELECT COUNT(*) FROM attendance_log as al WHERE al.emp_id = emp_shiftlog.emp_id AND al.punch_type IN (1,2) AND al.punch_date >= shiftStart AND al.punch_date <= shiftEnd AND al.is_delete=0
		) as punchCount
        FROM emp_shiftlog
		LEFT JOIN shift_master ON shift_master.id = emp_shiftlog.d".intval($day)."
		LEFT JOIN employee_master ON employee_master.id = emp_shiftlog.emp_id
		LEFT JOIN department_master ON department_master.id = employee_master.emp_dept_id
		LEFT JOIN emp_designation ON emp_designation.id = employee_master.emp_designation
		WHERE employee_master.attendance_status = 1 AND MONTH(emp_shiftlog.month) = ".$month." AND YEAR(emp_shiftlog.month) = ".$year." GROUP BY emp_shiftlog.emp_id".$countCondition)->result();
		//$this->printQuery();
        return $attendanceData;
	}
	
	/*** Created By : JP@02.01.2023***/
	public function getAbsentReport($currentDate)
	{
		$day = date('d',strtotime($currentDate));$month = date('m',strtotime($currentDate));$year = date('Y',strtotime($currentDate));
		$attendanceData = $this->db->query("SELECT emp_shiftlog.emp_id,shift_master.shift_name ,employee_master.emp_code, employee_master.emp_name, department_master.name as dept_name, emp_designation.title as emp_dsg,
		CAST(DATE_SUB(CONCAT('".$currentDate."', ' ', shift_master.shift_start), INTERVAL 3 HOUR) as datetime) as shiftStart,
		CAST(DATE_ADD(CONCAT('".$currentDate."', ' ', shift_master.shift_start), INTERVAL 75559 SECOND) as datetime) as shiftEnd,
		(
			SELECT COUNT(*) FROM attendance_log as al WHERE al.emp_id = emp_shiftlog.emp_id AND al.punch_type IN (1,2) AND al.punch_date >= shiftStart AND al.punch_date <= shiftEnd AND al.is_delete=0
		) as punchCount
        FROM emp_shiftlog
		LEFT JOIN shift_master ON shift_master.id = emp_shiftlog.d".intval($day)."
		LEFT JOIN employee_master ON employee_master.id = emp_shiftlog.emp_id
		LEFT JOIN department_master ON department_master.id = employee_master.emp_dept_id
		LEFT JOIN emp_designation ON emp_designation.id = employee_master.emp_designation
		WHERE employee_master.attendance_status = 1 AND MONTH(emp_shiftlog.month) = ".$month." AND YEAR(emp_shiftlog.month) = ".$year." GROUP BY emp_shiftlog.emp_id HAVING punchCount = 0 ORDER BY department_master.name")->result();
		//$this->printQuery();
        return $attendanceData;
	}
	
	/*** Created By : JP@30.12.2022***/
	public function getTotalPunchesByDate($currentDate)
	{
		$day = date('d',strtotime($currentDate));$month = date('m',strtotime($currentDate));$year = date('Y',strtotime($currentDate));
		$attendanceData = $this->db->query("SELECT emp_shiftlog.emp_id,shift_master.shift_name ,employee_master.emp_code, employee_master.emp_name, department_master.name as dept_name, emp_designation.title as emp_dsg,
		CAST(DATE_SUB(CONCAT('".$currentDate."', ' ', shift_master.shift_start), INTERVAL 3 HOUR) as datetime) as shiftStart,
		(
			SELECT GROUP_CONCAT(al.punch_date) FROM attendance_log as al WHERE al.emp_id = emp_shiftlog.emp_id AND al.punch_type IN (1,2) AND al.punch_date >= shiftStart AND al.punch_date <= shiftEnd AND al.is_delete=0
		) as punch_date
        FROM emp_shiftlog
		LEFT JOIN shift_master ON shift_master.id = emp_shiftlog.d".intval($day)."
		LEFT JOIN employee_master ON employee_master.id = emp_shiftlog.emp_id
		LEFT JOIN department_master ON department_master.id = employee_master.emp_dept_id
		LEFT JOIN emp_designation ON emp_designation.id = employee_master.emp_designation
		WHERE MONTH(emp_shiftlog.month) = ".$month." AND YEAR(emp_shiftlog.month) = ".$year." GROUP BY emp_shiftlog.emp_id")->result();
		//$this->printQuery();
        return $attendanceData;
	}
	
	/*** Created By : JP@29.12.2022***/
	public function getPunchByDate($currentDate)
	{
		$day = date('d',strtotime($currentDate));$month = date('m',strtotime($currentDate));$year = date('Y',strtotime($currentDate));
		$attendanceData = $this->db->query("SELECT emp_shiftlog.emp_id,shift_master.shift_name, shift_master.shift_type,  shift_master.shift_start, employee_master.emp_code, employee_master.emp_name, department_master.name as dept_name, emp_designation.title as emp_dsg,
		CAST(CONCAT('".$currentDate."', ' ', shift_master.shift_start) as datetime) as shift_start,
		CAST(DATE_SUB(CONCAT('".$currentDate."', ' ', shift_master.shift_start), INTERVAL 2 HOUR) as datetime) as shiftStart,
		CAST(DATE_ADD(CONCAT('".$currentDate."', ' ', shift_master.shift_start), INTERVAL 79199 SECOND) as datetime) as shiftEnd,
		(
			SELECT GROUP_CONCAT(al.punch_date) FROM attendance_log as al WHERE al.emp_id = emp_shiftlog.emp_id AND al.punch_type IN (1,2) AND al.punch_date >= shiftStart AND al.punch_date <= shiftEnd AND al.is_delete=0
		) as punch_date
        FROM emp_shiftlog
		LEFT JOIN shift_master ON shift_master.id = emp_shiftlog.d".intval($day)."
		LEFT JOIN employee_master ON employee_master.id = emp_shiftlog.emp_id
		LEFT JOIN department_master ON department_master.id = employee_master.emp_dept_id
		LEFT JOIN emp_designation ON emp_designation.id = employee_master.emp_designation
		WHERE MONTH(emp_shiftlog.month) = ".$month." AND YEAR(emp_shiftlog.month) = ".$year." AND employee_master.is_active=1 AND employee_master.attendance_status=1 GROUP BY emp_shiftlog.emp_id")->result();
		//$this->printQuery();
        return $attendanceData;
	}
	
	/*** Created By : JP@29.12.2022***/
	public function getShiftByDate($postData)
	{
		$currentDate = $postData['shift_date'];$deptCondition = '';$newEmpCondition = '';
		if(!empty($postData['dept_id'])){$deptCondition = 'AND employee_master.emp_dept_id = '.$postData['dept_id'];}
		if(!empty($postData['shift_status'])){$newEmpCondition = 'AND employee_master.shift_id = 0';}
		$day = date('d',strtotime($currentDate));$month = date('m',strtotime($currentDate));$year = date('Y',strtotime($currentDate));
		$attendanceData = $this->db->query("SELECT emp_shiftlog.id,emp_shiftlog.emp_id,shift_master.shift_name, shift_master.shift_type,  employee_master.emp_code, employee_master.emp_name, department_master.name as dept_name, emp_designation.title as emp_dsg
        FROM emp_shiftlog
		LEFT JOIN shift_master ON shift_master.id = emp_shiftlog.d".intval($day)."
		LEFT JOIN employee_master ON employee_master.id = emp_shiftlog.emp_id
		LEFT JOIN department_master ON department_master.id = employee_master.emp_dept_id
		LEFT JOIN emp_designation ON emp_designation.id = employee_master.emp_designation
		WHERE MONTH(emp_shiftlog.month) = ".$month." AND YEAR(emp_shiftlog.month) = ".$year." AND employee_master.attendance_status = 1 ".$deptCondition." ".$newEmpCondition." GROUP BY emp_shiftlog.emp_id")->result();
		//$this->printQuery();
        return $attendanceData;
	}
	
	public function getPunchData($FromDate,$ToDate,$device_id=2)
	{
		$queryData['tableName'] = $this->devicePunchesTable;
		$queryData['customWhere'][] = 'punch_date BETWEEN "'.date('Y-m-d',strtotime($FromDate)).'" AND "'.date('Y-m-d',strtotime($ToDate)).'"';
// 		$queryData['where']['device_id'] = $device_id;
        return $this->rows($queryData);
	}
	
	public function getPunchData1($FromDate,$ToDate,$device_id=2)
	{
		$queryData['tableName'] = $this->devicePunchesTable;
		$queryData['customWhere'][] = 'punch_date BETWEEN "'.date('Y-m-d',strtotime($FromDate)).'" AND "'.date('Y-m-d',strtotime($ToDate)).'"';
// 		$queryData['where']['device_id'] = $device_id;
        return $this->rows($queryData);
	}
	
	public function getDeviceData($device_id=1)
	{
		$ddQuery['tableName'] = $this->deviceMasterTable;
		$ddQuery['limit'] = 1;
        return $this->rows($ddQuery);
	}
	
	public function callDeviceApi($deviceData)
	{
		$punchData = New StdClass();
		$curl = curl_init();
		$api_url = "https://api.etimeoffice.com/api/DownloadPunchData?Empcode=".$deviceData->Empcode."&FromDate=".$deviceData->FromDate."&ToDate=".$deviceData->ToDate;
		curl_setopt_array($curl, array(
			CURLOPT_URL => $api_url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_HTTPHEADER => array('Authorization: Basic '.$deviceData->device_token),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);
		
		if ($err) {echo "cURL Error #:" . $err;exit;}
		else 
		{
			$resultapi = json_decode($response);
			$punchData = $resultapi->PunchData;
		}
		return $punchData;
	}
	
	public function addEmpDevice($deviceData)
	{
		$punchData = New StdClass();
		$curl = curl_init();
		$api_url = "https://api.etimeoffice.com/api/AddEmployee?Empcode=".$deviceData->Empcode."&EmpName=".$deviceData->emp_name."&DeviceSerialNo=".$deviceData->device_srno;
		
		
		curl_setopt_array($curl, array(
			CURLOPT_URL => $api_url,
			CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_HTTPHEADER => array('Authorization: Basic '.$deviceData->device_token),
            CURLOPT_POSTFIELDS => ['Empcode'=>$deviceData->Empcode,'EmpName'=>$deviceData->emp_name,'DeviceSerialNo'=>$deviceData->device_srno]
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);
		
		if ($err):
		    return ['status'=>0,'result'=>$err];
		else:
		    return ['status'=>1,'result'=>$response];
		endif;
		
	}
	
	public function removeEmpDevice($deviceData)
	{
		$punchData = New StdClass();
		$curl = curl_init();
		$api_url = "https://api.etimeoffice.com/api/DeleteEmployee?Empcode=".$deviceData->Empcode."&EmpName=".$deviceData->emp_name."&DeviceSerialNo=".$deviceData->device_srno;
		
		curl_setopt_array($curl, array(
			CURLOPT_URL => $api_url,
			CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_HTTPHEADER => array('Authorization: Basic '.$deviceData->device_token),
            CURLOPT_POSTFIELDS => ['Empcode'=>$deviceData->Empcode,'EmpName'=>$deviceData->emp_name,'DeviceSerialNo'=>$deviceData->device_srno]
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);
		
		if ($err):
		    return ['status'=>0,'result'=>$err];
		else:
		    return ['status'=>1,'result'=>$response];
		endif;
	}

}
?>