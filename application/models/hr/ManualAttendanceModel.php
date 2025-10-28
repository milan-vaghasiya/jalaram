<?php
class ManualAttendanceModel extends MasterModel{
    private $empAttendance = "attendance_log";

    public function getDTRows($data){
        $data['tableName'] = $this->empAttendance;
        $data['select'] = "attendance_log.*,employee_master.emp_name";
        $data['join']['employee_master'] = "employee_master.id = attendance_log.emp_id";
        $data['where']['punch_type'] = 2;
        
        $data['order_by']['attendance_log.id'] = "DESC";
        
        $data['searchCol'][] = "employee_master.emp_name";
        $data['searchCol'][] = "attendance_log.emp_code";
        $data['searchCol'][] = "attendance_log.punch_date";
        $data['searchCol'][] = "remark";

		$columns =array('','','employee_master.emp_name','attendance_log.emp_code','attendance_log.punch_date','remark');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		$result = $this->pagingRows($data);
	
		return $result;
    }

    public function getManualAttendance($id){
        $data['tableName'] = $this->empAttendance;
        $data['where']['id'] = $id;
        return $this->row($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();

            $result = $this->store($this->empAttendance,$data,'Manual Attendance');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function delete($id,$loginID){
        return $this->edit($this->empAttendance,['id'=>$id],['updated_by'=>$loginID,'is_delete'=>1],'Manual Attendance');
    }
}
?>