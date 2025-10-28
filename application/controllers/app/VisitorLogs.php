<?php
class VisitorLogs extends MY_Controller
{
	private $indexPage = "app/visitor_log";

	public function __construct()
	{
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Visitor Log";
		$this->data['headData']->controller = "app/visitorLogs";		
		$this->data['headData']->appMenu = "app/visitorLogs";

	}

	
	public function index($status =0)
	{
        $this->data['status'] = $status;
		$this->data['appMenu'] ='visitorLogs';
		$this->data['logHtml'] = $this->getVisitorLogs(['status'=>0]);
		$this->load->view($this->indexPage, $this->data);
	}

    public function getVisitorLogs($parameter = []){
        $postData = !empty($parameter)?$parameter :  $this->input->post();
        $visitData = $this->visitorLog->getVisitLogForApp(['status'=>$postData['status']]);;
        $html="";
		if(!empty($visitData))
		{
			foreach($visitData as $row)
			{
			    $row->created_at = date("d-m-Y h:i:s",strtotime($row->created_at));
				
			    $aprvBtn='';$rejBtn='';$startBtn = '';$endBtn='';
				if(empty($row->approved_at) && empty($row->rejected_at) && $row->wtm == $this->loginId){
					$aprvBtn = '<a href="javascript:void(0)" class="dropdown-item  approveVisit" data-id="'.$row->id.'" data-val="1" data-msg="Approve" datatip="Approve Visit" flow="down" ><ion-icon name="checkmark-done-outline"></ion-icon> Approve</a>';
					$rejBtn = '<a href="javascript:void(0)" class="dropdown-item  approveVisit" data-id="'.$row->id.'" data-val="2" data-msg="Reject" datatip="Reject Visit" flow="down" ><ion-icon name="close-outline"></ion-icon> Reject</a>';

				}

				if(!empty($row->approved_at) && empty($row->visit_start_time)){
					$startBtn = '<a href="javascript:void(0)" class="dropdown-item  approveVisit" data-id="'.$row->id.'" data-val="3" data-msg="Start" datatip="Start Visit" flow="down" ><ion-icon name="caret-forward-circle-outline"></ion-icon> Start Visit</a>';

				}

				if(!empty($row->visit_start_time) && empty($row->visit_end_time)){
					$endBtn = '<a href="javascript:void(0)" class="dropdown-item  approveVisit" data-id="'.$row->id.'" data-val="4" data-msg="Visit End" datatip="Visit End" flow="down" ><ion-icon name="pause-circle-outline"></ion-icon> Visit End</a>';

				}
				$row->exit_at = !empty($row->exit_at)?date("d-m-Y h:i:s",strtotime($row->exit_at)):'';
				$row->visit_start_time = !empty($row->visit_start_time)?date("d-m-Y h:i:s",strtotime($row->visit_start_time)):'';
				$row->visit_end_time = !empty($row->visit_end_time)?date("d-m-Y h:i:s",strtotime($row->visit_end_time)):'';
				$viewBtn = "<a href='javascript:void(0)' class='dropdown-item '  flow='down'  onclick='openViewModal(".json_encode($row).")'> View</a>";
				
                $totalDuration = '';
				if($postData['status'] == 1 && !empty($row->exit_at)):
					$diff = date_diff(date_create($row->created_at),date_create($row->exit_at)); 
					$totalDuration =   sprintf("%02d",$diff->h).':'.sprintf("%02d",$diff->i);
				endif;

				$html .= '<li class=" grid_item listItem item transition position-static " data-category="transition">
                                    <a href="javascript:void(0)">
                                        <div class="media-content">
                                            <div>
                                                <h6 class="name">'.$row->vname.'</h6>
                                                <p class="my-1"> WHM :'.$row->emp_name.'</p>
                                                <p class="my-1"> Contac No :'.$row->contact_no.'</p>
                                                <p class="my-1">Company Name :'.$row->company_name.'</p>
                                                '.((!empty($totalDuration))?'<p class="my-1"><i class="far fa-clock"></i> '.$totalDuration.'</p>':'').'
                                            </div>
                                        </div>
										<div class="left-content w-auto">
										    <a class="dropdown-toggle lead-action float-end" data-bs-toggle="dropdown" href="#" role="button">
												<i class="mdi mdi-chevron-down fs-3"></i>
											</a>
											<div class="dropdown-menu">'. $aprvBtn.$rejBtn.$startBtn.$endBtn.$viewBtn.'</div>
										
										</div>                                        
                                    </a>
                                </li>';
			}
		}
		if(empty($parameter)){$this->printJson(['html'=>$html]);}
		else{return $html;}
    }
    
}
?>