<?php
class MachineTicket extends MY_Controller {
    private $indexPage = "machine_ticket/index";
    private $ticketForm = "machine_ticket/form";
    private $solutionPage = "machine_ticket/machine_solution";
    private $maintenanceLBReport = "report/maintenance_report/maintenanceLB_report";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Machine Ticket";
		$this->data['headData']->controller = "machineTicket";
		$this->data['headData']->pageUrl = "machineTicket";
		$this->data['floatingMenu'] = $this->load->view('report/hr_report/floating_menu',[],true);
	}
	
	public function index(){
        $this->data['tableHeader'] = getMaintenanceDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $result = $this->ticketModel->getDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getMachineTicketData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addMachineTicket(){
        $this->data['trans_prefix'] = "MT/".$this->shortYear."/";
        $this->data['nextTransNo'] = $this->ticketModel->nextTransNo();
        $this->data['machineData'] = $this->ticketModel->getMachineName();
        $this->data['deptData'] = $this->ticketModel->getDepartment();
        $this->load->view($this->ticketForm,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['trans_no']))
            $errorMessage['trans_no'] = "Trans. no. is required.";
        if(empty($data['machine_id']))
            $errorMessage['machine_id'] = "Machine is required.";
        if(empty($data['dept_id']))
            $errorMessage['dept_id'] = "Department is required.";
        if(empty($data['problem_date']))
            $errorMessage['problem_date'] = "Problem Date is required.";
        if(empty($data['problem_title']))
            $errorMessage['problem_title'] = "Problem Title is required.";
        if(empty($data['problem_detail']))
            $errorMessage['problem_detail'] = "Problem Detail is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->ticketModel->save($data));
        endif;
    }

    public function edit(){
        $id = $this->input->post('id');
        $this->data['trans_prefix'] = "MT/".$this->shortYear."/";
        $this->data['nextTransNo'] = $this->ticketModel->nextTransNo();
        $this->data['machineData'] = $this->ticketModel->getMachineName();
        $this->data['deptData'] = $this->ticketModel->getDepartment();
        $this->data['dataRow'] = $this->ticketModel->getMachineTicket($id);
        $this->load->view($this->ticketForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->ticketModel->delete($id));
        endif;
    }

    public function getMachineSolution(){
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->ticketModel->getMachineTicket($id);
        $this->load->view($this->solutionPage,$this->data);
    }

    public function saveMachineSolution(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['solution_by']))
            $errorMessage['solution_by'] = "Solution By is required.";
        if(empty($data['solution_date']))
            $errorMessage['solution_date'] = "Solution Date is required.";
        if(empty($data['solution_detail']))
            $errorMessage['solution_detail'] = "Solution Detail is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->ticketModel->save($data));
        endif;
    }

    /* AAVRUTI */
    public function maintenanceLogReport(){
        $this->data['pageHeader'] = 'MAINTENANCE LOG BOOK REPORT';
        $this->load->view($this->maintenanceLBReport,$this->data);
    }

    public function getMachineTicketListByDate(){
        $data = $this->input->post();
        $errorMessage = array();

		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $rejectionData = $this->ticketModel->getMachineTicketListByDate($data);
            $this->printJson($rejectionData);
        endif;
    }

    /*Maintenance Log Print Data */
    public function printMaintenanceLog($pdate){
        $data['from_date']=explode('~',$pdate)[0];
        $data['to_date']=explode('~',$pdate)[1];

        $mlogData = $this->ticketModel->getMachineTicketListByDate($data); 
        
        $logo=base_url('assets/images/logo.png');
		
		$topSectionO ='<table class="table">
						<tr>
							<td style="width:20%;"><img src="'.$logo.'" style="height:40px;"></td>
							<td class="org_title text-center" style="font-size:1rem;width:60%">Maintenance Log Book Data</td>
							<td style="width:20%;" class="text-right"><span style="font-size:0.8rem;">F ST 12<br>(00/01.08.2021)</td>
						</tr>
					</table>';
        $itemList='<table id="reportTable" class="table table-bordered align-items-center itemList">
								<thead class="thead-info" id="theadData">
									<tr class="text-center">
										<th rowspan="2">#</th>
										<th rowspan="2">Date</th>
										<th rowspan="2">Shift</th>
										<th rowspan="2">Machine No.</th>
                                        <th colspan="3">Time</th>
                                        <th rowspan="2">Problem Description</th>
                                        <th rowspan="2">Solution/Action taken</th>
                                        <th rowspan="2">M/C Status</th>
                                        <th rowspan="2">Attendent</th>
                                        <th rowspan="2">Incharge Sign</th>	
									</tr>
                                    <tr class="text-center">
                                        <th>From</th>
                                        <th>To</th>
                                        <th>Total</th>
                                    </tr>
								</thead>
                                <tbody id="tbodyData">'; 
                               
        $itemList.=$mlogData['tbody'].'</tbody></table>';

	    $originalCopy = '<div style="">'.$topSectionO.$itemList.'</div>';
		
		$pdfData = $originalCopy;
		
		$mpdf = new \Mpdf\Mpdf();
		$pdfFileName='JWO-REG-'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetProtection(array('print'));
		$mpdf->AddPage('L','','','','',5,5,5,5,5,5,'','','','','','','','','','A4-L');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
    }
}
?>