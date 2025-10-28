<?php
class LeaveApprove extends MY_Controller
{
    private $indexPage = "hr/leave/leave_approve";
    private $leaveForm = "hr/leave/leave_approve_form";
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Leave Approve";
		$this->data['headData']->controller = "hr/leaveApprove";
	}
	
	public function index(){
        $this->data['leave_auth'] = $this->leaveApprove->checkAuthority($this->session->userdata('loginId'));
		$this->data['tableHeader'] = getHrDtHeader('leaveApprove');
        $this->load->view($this->indexPage,$this->data);
    }
	
	public function getDTRows(){
		$postData = $this->input->post();
		$postData['login_emp_id']=$this->session->userdata('loginId');
		
		$sendData = array();$i=1;$count=0;
		$result = $this->leaveApprove->getDTRows($postData);			
		foreach($result['data'] as $row):
			$row->sr_no = $i++;
			$row->start_date = date('d-m-Y',strtotime($row->start_date));
			$row->end_date = date('d-m-Y',strtotime($row->end_date));
			$row->total_days = $row->total_days. ($row->total_days>1) ? $row->total_days.' Days' : $row->total_days.' Day';
			$row->emp_name = $row->emp_name .'<br><small>'.$row->title.'</small>';
			if($row->approve_status == 2):
				$row->status = '<span class="badge badge-pill badge-danger m-1">Declined</span>';$row->approveButtonLabel = 'Declined';
			elseif($row->approve_status == 1):
				$row->status = '<span class="badge badge-pill badge-success m-1">Approved</span>';$row->approveButtonLabel = 'Approved';
			else:
				$row->status = '<span class="badge badge-pill badge-info m-1">Pending</span>';$row->approveButtonLabel = 'Pending';
			endif;
			$row->showLeaveAction = false;
			if($row->emp_id != $this->session->userdata('loginId'))
			{
				$emp1 = $this->leaveApprove->getEmpData($this->session->userdata('loginId'));
				$emp2 = $this->leaveApprove->getEmpData($row->emp_id);
				if($emp1->emp_dept_id == $emp2->emp_dept_id)
				{
					
				}
			}
			$sendData[] = getLeaveApproveData($row);
		endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function approveLeave(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['leave_id']))
            $errorMessage['generalError'] = "Leave is not defined.";
		if(empty($data['approve_date']))
            $errorMessage['approve_date'] = "Approve Date is required.";
		if(empty($data['approve_status']))
            $errorMessage['approve_status'] = "Status is required.";
			
		if(!empty($errorMessage)):
				$this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
			$data['approved_by'] = $this->session->userdata('loginId');
			$this->printJson($this->leaveApprove->save($data));
        endif;
    }

}
?>