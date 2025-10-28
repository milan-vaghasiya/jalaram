<?php
class InvoiceSettlement extends MY_Controller{
    private $index = "invoice_settlement/index";
    private $form = "invoice_settlement/form";
    private $view = "invoice_settlement/view";

    public function __construct(){
        parent::__construct();
		$this->data['headData']->pageTitle = "Invoice Settlement";
		$this->data['headData']->controller = "invoiceSettlement";
    }

    public function index(){
        $this->data['tableHeader'] = getExportDtHeader("invoiceUnsetlled");
        $this->load->view($this->index,$this->data);
    }

    public function getDTRows($status=0){
        $data = $this->input->post();$data['status'] = $status;
        $result = $this->invoiceSettlement->getDTRows($data);
		
        $sendData = array();$i=1;$count=0;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $row->tab_status = $status;
            $sendData[] = getInvoiceSettlementData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    function invoiceSettlement(){
        $data = $this->input->post();
        $this->data['ladingDetail'] = $this->ladingBill->getLadingBill($data);
        $this->data['unsetlledSwifts'] = $this->swiftRemittance->getUnsetlledSwifts();
        $this->load->view($this->form,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['received_fc'])):
            $errorMessage['settlementError'] = "Please enter settlement amount.";
        else:
            if(floatval($data['net_amount']) != floatval($data['received_fc'])):
                $errorMessage['settlementError'] = "Invoice Amount and Settlement amount mismatch.";
            endif;
            unset($data['net_amount']);
        endif;

        if(empty($data['settlement_remark']))
            $errorMessage['settlement_remark'] = "Settlement Remark is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->invoiceSettlement->save($data));
        endif;
    }

    public function viewTransaction(){
        $data = $this->input->post();
        $this->data['ladingDetail'] = $this->ladingBill->getLadingBill($data);
        $this->data['setlledSwifts'] = $this->invoiceSettlement->getSettlementTransactions(['bl_id'=>$data['id']]);
        $this->load->view($this->view,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->invoiceSettlement->delete($id));
        endif;
    }
}
?>