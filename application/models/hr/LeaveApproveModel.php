<?php
class LeaveApproveModel extends MasterModel{
    private $leaveMaster = "leave_master";
	private $leaveType = "leave_type";
    private $empDesignation = "emp_designation";
    private $empMaster = "employee_master";
    private $leaveApproval = "leave_approval";
	private $empRole = ["1"=>"Admin","2"=>"Production Manager","3"=>"Accountant","4"=>"Sales Manager","5"=>"Purchase Manager"];
	
	public function getDTRows($data){
		
		$emp1 = $this->leaveApprove->getEmpData($data['login_emp_id']);
		
		$data['tableName'] = $this->leaveMaster;
        $data['searchCol'][] = "emp_name";
        $data['searchCol'][] = "emp_code";
        $data['searchCol'][] = "title";
        $data['searchCol'][] = "leave_type";
        $data['searchCol'][] = "leave_reason";
        $data['searchCol'][] = "start_date";
        $data['searchCol'][] = "end_date";
        $data['searchCol'][] = "total_days";
		
		$data['select'] = "leave_master.*,employee_master.emp_name,employee_master.emp_designation,employee_master.emp_profile, emp_designation.title,employee_master.emp_code";
        $data['join']['employee_master'] = "employee_master.id = leave_master.emp_id";
        $data['join']['emp_designation'] = "emp_designation.id = employee_master.emp_designation";
        $data['join']['leave_authority'] = "leave_authority.id = leave_master.next_level";
        $data['where']['leave_authority.desi_id'] = $emp1->emp_designation;
        $data['where']['leave_authority.dept_id'] = $emp1->emp_dept_id;
		
        return $this->pagingRows($data);
    }

    public function getLeaveType(){
        $data['tableName'] = $this->leaveType;
        $leaveType = $this->rows($data);
		return $leaveType;
    }
	
    public function checkAuthority($id){
		$data['select'] = "employee_master.*, department_master.leave_authorities";
        $data['join']['department_master'] = "department_master.id = employee_master.emp_dept_id";
        $data['customWhere'][] = 'FIND_IN_SET('.$id.',department_master.leave_authorities) <> 0';
        $data['where']['employee_master.id'] = $id;
		$data['resultType']='numRows';
        $data['tableName'] = $this->empMaster;
        return $this->specificRow($data);
    }
	
    public function getEmpData($id){
		$data['where']['id'] = $id;
        $data['tableName'] = $this->empMaster;
        return $this->row($data);
    }
	
    public function getLeave($id){
        $data['where']['id'] = $id;
        $data['tableName'] = $this->leaveMaster;
        return $this->row($data);
    }
	
    public function getEmpLeaves($emp_id,$leave_type_id,$start_date,$end_date){
		
		$emp_leaves = Array();
		
        $empData = $this->getEmpData($emp_id);
		if(!empty($leave_type_id)){$data['where']['id'] = $leave_type_id;}
		$data['tableName'] = $this->leaveType;
		$leaveType = $this->rows($data);
		if(!empty($leaveType))
		{
			foreach($leaveType as $row)
			{
				$lq=array();$max_leave=0;$leave_period=1;
				$data1['select'] = "SUM(total_days) as total_days";
				$data1['where']['emp_id'] = $emp_id;
				$data1['where']['approve_status'] = 1;
				$data1['where']['start_date>='] = $start_date;
				$data1['where']['end_date<='] = $end_date;
				$data1['where']['leave_type_id'] = $row->id;
				$data1['tableName'] = $this->leaveMaster;
				$used_leaves = $this->specificRow($data1)->total_days;
				if(empty($used_leaves)){$used_leaves=0;}
				if(!empty($row->leave_quota))
				{
					$leave_quota = (array)json_decode($row->leave_quota);									
					foreach($leave_quota as $key=>$value){if($key == $row->id){$max_leave = $value->leave_days;$leave_period = $value->m_or_y;}}
				}
				$lq['emp_id'] = $emp_id;
				$lq['leave_type_id'] = $row->id;
				$lq['leave_type'] = $row->leave_type;
				$lq['emp_designation_id'] = $empData->emp_designation;
				$lq['designation'] = $this->db->where('id',$empData->emp_designation)->get($this->empDesignation)->row()->title;
				$lq['max_leave'] = $max_leave;
				$lq['leave_period'] = $leave_period;
				$lq['used_leaves'] = $used_leaves;
				$lq['remain_leaves'] = $max_leave - $used_leaves;
				$emp_leaves[] = $lq;
			}
		}
		return $emp_leaves;
    }

	public function save($data){
		
		$approvalData = Array();
		$laQuery['where']['leave_id'] = $data['leave_id'];
        $laQuery['tableName'] = $this->leaveApproval;
        $oldApprovals = $this->rows($laQuery);
		if(!empty($oldApprovals)):
			foreach($oldApprovals as $row)
			{
				$approvalData['ref_id'] = $row->approve_by;
				if($this->loginID == $row->approve_by){$data['id'] = $row->id;}
			}
		endif;
		
		$approvalData['id'] = $data['id'];
		$approvalData['leave_id'] = $data['leave_id'];
		$approvalData['approve_by'] = $data['approved_by'];
		$approvalData['approve_date'] = $data['approve_date'];
		$approvalData['approve_status'] = $data['approve_status'];
		$approvalData['comment'] = $data['comment'];
		$approvalData['created_by'] = $data['approved_by'];
		$this->store($this->leaveApproval,$approvalData,'Leave Approve');
		
		$result = Array();
		/*** Update Status/Next Level in Leave Master ***/
		$leaveData = Array();
		if($data['approve_status'] == 2):
			$leaveData['id'] = $data['leave_id'];
			$leaveData['approve_status'] = $data['approve_status'];
			$result = $this->store($this->leaveMaster,$leaveData,'Leave');
		endif;
		
		$leaveData = Array();
		$lAuth = explode(',',$data['leave_authority']);	
		if(count($lAuth) > count($oldApprovals) AND ($data['approve_status'] != 2)):
			$leaveData['id'] = $data['leave_id'];
			$leaveData['next_level'] = $lAuth[count($oldApprovals)];
			$result = $this->store($this->leaveMaster,$leaveData,'Leave');
		endif;
		
		if(count($lAuth) == count($oldApprovals)):
			$leaveData['id'] = $data['leave_id'];
			$leaveData['approve_status'] = $data['approve_status'];
			$result = $this->store($this->leaveMaster,$leaveData,'Leave');
		endif;
		
		return $result;
	}
}
?>