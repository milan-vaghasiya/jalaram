<?php
class VisitorLogModel extends MasterModel{
    private $visitorLog = "visitor_log";
    private $itemMaster = "item_master";
        
    public function getNextVisitNumber(){		
		$queryData['select'] = "MAX(visit_no) as visit_no";
        $queryData['tableName'] = $this->visitorLog;
		$visit_no = $this->specificRow($queryData)->visit_no;
		$nextVisitsNo = (!empty($visit_no))?($visit_no + 1):1;
		return $nextVisitsNo;
    }
    
    public function getDTRows($data){
        $data['tableName'] = $this->visitorLog;
        $data['select'] = 'visitor_log.*,employee_master.emp_name';
        $data['leftJoin']['employee_master'] ="employee_master.id = visitor_log.wtm";
        if(!in_array($this->userRole,[-1,1])){$data['where']['visitor_log.wtm'] = $this->loginId;}
        if($data['status'] == 1){
            $data['where']['visitor_log.approved_at != '] = null;
        }elseif($data['status'] == 2){
            $data['where']['visitor_log.rejected_at != '] = null;
        }else{
            $data['where']['visitor_log.approved_at'] = null;
            $data['where']['visitor_log.rejected_at'] = null;
        }
        $data['searchCol'][] = "employee_master.emp_name";
        $data['searchCol'][] = "visitor_log.vname";
        $data['searchCol'][] = "visitor_log.company_name";
        $data['searchCol'][] = "visitor_log.contact_no";
        $data['searchCol'][] = "visitor_log.address";
        $data['searchCol'][] = "visitor_log.purpose";
        $data['searchCol'][] = "concat(visitor_log.visit_start_time,visitor_log.visit_end_time)";
        $data['searchCol'][] = "concat(visitor_log.created_at,visitor_log.exit_at)";
		$columns =array('','','vname','company_name','contact_no','address','purpose','','','','');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }
	
    public function save($data){
        try{
            $this->db->trans_begin();
			
			$data['visit_no'] = $this->getNextVisitNumber(); 
            $length = strlen($data['visit_no']);
			$data['visit_number'] =  'V' . (($length == 1)?lpad($data['visit_no'],2):(($length == 2)?lpad($data['visit_no'],3):$data['visit_no']));
			//print_r($data);exit;
            $data['created_at'] = date("Y-m-d H:i:s");
            $result = $this->store($this->visitorLog,$data,'Visitor Log');

             /* Send Notification */
            /*$empList = $this->employee->getEmpIdByRole(['emp_role'=>'-1,1']);
            $notifyEmp = array_column($empList,'id');
            $notifyEmp[]=$data['wtm'];
            $notifyData['emp_role'] = [-1,1];
            $notifyData['emp_id'] = $notifyEmp;
            $notifyData['notificationTitle'] = "Visitor wants to meet you";
            $notifyData['notificationMsg'] = "Visitor : ".$data['vname'];
            $notifyData['payload'] = ['callBack' => base_url("visitors")];
            $notifyData['controller'] = "'visitors'";
            $notifyData['action'] = (empty($data['id']))?"W":"M";
            $this->notify($notifyData);*/
            
            if ($this->db->trans_status() !== FALSE):
    			
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
           return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }
    
    public function checkForApproval($postData){
        $queryData = array();
        $queryData['tableName'] = $this->visitorLog;
        $queryData['select'] = 'DATE_FORMAT(approved_at, "%d-%m-%Y %h:%i:%s %p") as approved_at,visit_number';
        $queryData['where']['id'] = $postData['id'];
        $queryData['where']['approved_at > '] = 0;
        $result = $this->row($queryData);
        return $result;
    }
    
    public function getVisitorLogs(){
        $queryData = array();
        $queryData['tableName'] = $this->visitorLog;
        $queryData['order_by']['id'] = "DESC";
        $result = $this->rows($queryData);
        return $result;
    }

    public function approveVisit($data) {
        if($data['val'] == 1){
            $this->store($this->visitorLog, ['id'=> $data['id'], 'approved_at' => date('Y-m-d H:i:s')]);
        }elseif($data['val'] == 2){
            $this->store($this->visitorLog, ['id'=> $data['id'], 'rejected_at' => date('Y-m-d H:i:s'), 'reject_reason' => trim($data['reject_reason'])]);
        }elseif($data['val'] == 3){
            $this->store($this->visitorLog, ['id'=> $data['id'], 'visit_start_time' => date('Y-m-d H:i:s')]);
        }elseif($data['val'] == 4){
            $this->store($this->visitorLog, ['id'=> $data['id'], 'visit_end_time' => date('Y-m-d H:i:s')]);
        }
        return ['status' => 1, 'message' => 'Visit ' . $data['msg'] . ' successfully.'];
    }

    public function getLastVisitData($data){
        $queryData = array();
        $queryData['tableName'] = $this->visitorLog;
        $queryData['where']['contact_no'] = $data['contact_no'];
        if(!empty($data['visit_date'])){
            $queryData['where']['DATE(created_at)'] = $data['visit_date'];
            $queryData['customWhere'][] = 'exit_at IS NULL';
        }
        $queryData['order_by']['id'] = "DESC";
        $result = $this->row($queryData);
        return $result;
    }

    public function exitCompany($data){
        try{
            $this->db->trans_begin();
            $result = $this->store($this->visitorLog,['id'=>$data['id'],'exit_at'=>date("Y-m-d H:i:s")],'Visitor Log');
            $result['id'] = $data['id'];
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
           return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }

    public function getVisitLogForApp($data){
        $data['tableName'] = $this->visitorLog;
        $data['select'] = 'visitor_log.*,employee_master.emp_name';
        $data['leftJoin']['employee_master'] ="employee_master.id = visitor_log.wtm";
        if(!in_array($this->userRole,[-1,1])){$data['where']['visitor_log.wtm'] = $this->loginId;}
        if($data['status'] == 1){
            $data['where']['visitor_log.approved_at != '] = null;
        }elseif($data['status'] == 2){
            $data['where']['visitor_log.rejected_at != '] = null;
        }else{
            $data['where']['visitor_log.approved_at'] = null;
            $data['where']['visitor_log.rejected_at'] = null;
        }
        $data['order_by']['visitor_log.created_at'] = "ASC";
        return $this->rows($data);
    }
}

?>