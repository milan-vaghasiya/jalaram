<?php
class Attendance extends MY_Controller
{
    private $indexPage = "hr/attendance/index";
    private $monthlyAttendance = "hr/attendance/month_attendance";
    private $attendanceForm = "hr/attendance/form";
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Attendance";
		$this->data['headData']->controller = "hr/attendance";
	}
	
	public function index(){
		$this->data['lastSyncedAt'] = "";
		$this->data['lastSyncedAt'] = $this->biometric->getDeviceData()[0]->last_sync_at;
		$this->data['lastSyncedAt'] = (!empty($this->data['lastSyncedAt'])) ? date('j F Y, g:i a',strtotime($this->data['lastSyncedAt'])) : "";
        $this->load->view($this->indexPage,$this->data);
    }

	public function monthlyAttendance(){
        $this->load->view($this->monthlyAttendance,$this->data);
    }

    public function loadAttendanceSheet(){
        $data = $this->input->post();
		$this->printJson($this->attendance->loadAttendanceSheet($data['month']));
    }

    public function syncDeviceData(){
		$this->printJson($this->biometric->syncDeviceData());
    }

    public function syncDevicePunches(){
		$this->printJson($this->biometric->syncDevicePunches());
    }
}
?>