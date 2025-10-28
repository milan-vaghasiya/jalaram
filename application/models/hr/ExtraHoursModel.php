<?php
class ExtraHoursModel extends MasterModel{
    private $empAttendance = "attendance_log";

    public function getDTRows($data){
        $data['tableName'] = $this->empAttendance;
        $data['select'] = "attendance_log.*,employee_master.emp_name";
        $data['join']['employee_master'] = "employee_master.id = attendance_log.emp_id";
        $data['where']['attendance_log.punch_type'] = 3;
        $data['order_by']['attendance_log.punch_date'] = 'DESC';
        $data['order_by']['attendance_log.id'] = 'DESC';
        
        $data['searchCol'][] = "employee_master.emp_name";
        $data['searchCol'][] = "attendance_log.emp_code";
        $data['searchCol'][] = "attendance_log.punch_date";
        $data['searchCol'][] = "attendance_log.remark";
		$columns =array('','','emp_name','emp_code','punch_date','remark');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function getExtraHours($id){
        $data['where']['id'] = $id;
        $data['tableName'] = $this->empAttendance;
        return $this->row($data);
    }

    public function save($data){
        return $this->store($this->empAttendance,$data,'Extra Hours');
    }

    public function delete($id){
        return $this->trash($this->empAttendance,['id'=>$id],'Extra Hours');
    }
}
?>