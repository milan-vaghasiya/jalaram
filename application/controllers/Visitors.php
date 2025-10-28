<?php
class Visitors extends MY_Controller
{
    private $indexPage = "visitors/index";
   
    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Visitors";
		$this->data['headData']->controller = "visitors";
		$this->data['headData']->pageUrl = "visitors";
	}
	
	public function index(){
        $this->data['tableHeader'] = getConfigDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status=0){
        $data = $this->input->post(); $data['status'] = $status;
        $result = $this->visitorLog->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $row->loginId = $this->loginId;
            if(empty($status)):
				$row->status_label = '<span class="badge badge-pill badge-danger m-1">Pendding</span>';
			elseif($status == 1):
				$row->status_label = '<span class="badge badge-pill badge-success m-1">Approved</span><br>'. date("d-m-Y H:i:s", strtotime($row->approved_at));
            elseif($status == 2):
                $row->status_label = '<span class="badge badge-pill badge-dark m-1">Rejected</span><br>'.$row->reject_reason;             
            endif;
            $sendData[] = getVisitorsData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }
    
    
    public function approveVisit(){
        $data = $this->input->post();
		
		if(empty($data['id'])):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->visitorLog->approveVisit($data));
		endif;
    }
}
?>