<?php
class EmpLoan extends MY_Controller
{
	private $indexpage = "hr/emp_loan/index";
    private $form = "hr/emp_loan/form";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Employee Loan";
		$this->data['headData']->controller = "hr/empLoan";
        $this->data['headData']->pageUrl = "hr/empLoan";
	}

    //view table
	public function index(){    
        $this->data['tableHeader'] = getHrDtHeader('empLoan');
        $this->load->view($this->indexpage,$this->data);
    }

    public function getDTRows(){
        $result = $this->empLoan->getDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;         
            $sendData[] = getEmpLoanData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addLoan()
    {
        $this->data['empData'] = $this->empLoan->getEmployeeList();
        $this->data['ledgerData'] = $this->party->getPartyListOnGroupCode(['"BA"','"CS"']);
        $this->load->view($this->form,$this->data);
    }

    public function save()
    {
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['emp_id']))
            $errorMessage['emp_id'] = "Employee is required.";
        if(empty($data['vou_acc_id']))
            $errorMessage['vou_acc_id'] = "Ledger is required.";
        if(empty($data['amount']))
            $errorMessage['amount'] = "Amount is required.";
        if(empty($data['reason']))
            $errorMessage['reason'] = "Reason is required.";
        if(!empty($errorMessage)):
                $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $partyData = $this->party->getParty($data['vou_acc_id']);
            $accountData = $this->party->getPartyListOnSystemCode("LAC");
            $trans_no = $this->transModel->nextTransNo(22);
            $trans_prefix = $this->transModel->getTransPrefix(22);
            unset($data['empSelect']);
            $masterData = [ 
				'id' => $data['id'],
				'entry_type' => 22,
				'from_entry_type' => 0,
				'trans_prefix'=>$trans_prefix, 
				'trans_no'=>$trans_no, 
                'doc_no'=>'', 
				'trans_mode'=>'', 
				'trans_number'=>$trans_prefix.$trans_no,
				'trans_date' => date('Y-m-d',strtotime($data['date'])),  
                'doc_date' => date('Y-m-d',strtotime($data['date'])),  
				'party_id' => $data['vou_acc_id'],
                'opp_acc_id'=>$accountData->id,
                'vou_acc_id'=>$data['vou_acc_id'],
				'party_name' => $partyData->party_name,
				'sales_executive' => $data['emp_id'],
				'net_amount' => $data['amount'],
				'other_gst' => $data['total_emi'],
				'other_amount' => $data['emi_amount'],
				'remark' => $data['reason'],
                'created_by' => $this->session->userdata('loginId')
			];
            //print_r($masterData);exit;
            $this->printJson($this->empLoan->save($masterData));
        endif;
    }
    
    public function edit()
    {
        $id = $this->input->post('id'); 
        $this->data['dataRow'] = $this->empLoan->getEmpLoan($id);
        $this->data['empData'] = $this->empLoan->getEmployeeList();
        $this->data['ledgerData'] = $this->party->getPartyListOnGroupCode(['"BA"','"CS"']);
        $this->load->view($this->form,$this->data);
    }
    
    public function delete()
    {
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->empLoan->delete($id));
        endif;
    }
    function printLoan($id){
		$this->data['loanData'] = $this->empLoan->getEmpLoan($id);
		$this->data['companyData'] = $this->empLoan->getCompanyInfo();
        $this->data['empData'] = $this->employee->getEmp($this->data['loanData']->sales_executive);
		$response="";
		$logo=base_url('assets/images/logo.png');
		$this->data['letter_head']=base_url('assets/images/letterhead_top.png');
		$pdfData = $this->load->view('hr/emp_loan/printLoan',$this->data,true);
		
		$htmlHeader = '<img src="'.$this->data['letter_head'].'" class="img">';
		$htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;border-bottom:1px solid #000000;">
						<tr>
							<td style="width:50%;" rowspan="3"></td>
							<th colspan="2">For, '.$this->data['companyData']->company_name.'</th>
						</tr>
						<tr>
							<td style="width:25%;" class="text-center"></td>
							<td style="width:25%;" class="text-center">Authorised By</td>
						</tr>
					</table>
		 			';
		
		// $mpdf = $this->m_pdf->load();
		$mpdf = new \Mpdf\Mpdf();
		$pdfFileName='DC-REG-'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css'));
		$stylesheet = file_get_contents(base_url('assets/css/style.css?v='.time()));
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetWatermarkImage($logo,0.03,array(120,60));
		$mpdf->showWatermarkImage = true;
		$mpdf->SetProtection(array('print'));
		
		$mpdf->SetHTMLHeader($htmlHeader);
		//$mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('P','','','','',5,5,65,10,3,3,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
	}
}
?>