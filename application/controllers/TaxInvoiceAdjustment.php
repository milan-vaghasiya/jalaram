<?php
class TaxInvoiceAdjustment extends MY_Controller{
    private $index = "tax_invoice_adjustment/index";
    private $form = "tax_invoice_adjustment/form";
    private $view = "tax_invoice_adjustment/view";
    private $taxInvoiceTotalForm = "tax_invoice_adjustment/tax_inv_total_form";

    public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "Tax Invoice Adjustment";
		$this->data['headData']->controller = "taxInvoiceAdjustment";
	}

    public function index(){
        $this->data['tableHeader'] = getExportDtHeader("invUnadjusted");
        $this->load->view($this->index,$this->data);
    }

    public function getDTRows($status=0){
        $data = $this->input->post(); $data['status'] = $status;
        $result = $this->invoiceAdjustment->getDTRows($data);
		
        $sendData = array();$i=1;$count=0;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;         
            $row->tab_status = $status;
            $sendData[] = getTaxInvoiceAdjustmentData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function taxInvoiceTotal(){
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->ladingBill->getLadingBill(['id'=>$id]);
        $this->load->view($this->taxInvoiceTotalForm,$this->data);
    }

    public function saveTaxInvTotal(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['id']))
            $errorMessage['general_error'] = "Somthing is wrong.";
        if(empty($data['tax_invoice_total']))
            $errorMessage['tax_invoice_total'] = "Tax Inv. Total is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->invoiceAdjustment->saveTaxInvTotal($data));
        endif;
    }

    public function taxInvoiceAdjustment(){
        $data = $this->input->post();
        $this->data['ladingDetail'] = $this->invoiceAdjustment->getLadingBillDetail($data);
        $this->data['unsetlledRemitTransfer'] = $this->invoiceAdjustment->getUnsetlledRemitTransfer();
        $this->load->view($this->form,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['itemData']))
            $errorMessage['adjustmentError'] = "Adjustmnet data not found.";
        if(!empty($data['itemData']) && empty(array_sum(array_column($data['itemData'],'net_credit_inr_adj'))))
            $errorMessage['adjustmentError'] = "Please enter adjustmnet amount.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->invoiceAdjustment->save($data));
        endif;
    }

    public function viewTransaction(){
        $data = $this->input->post();
        $this->data['ladingDetail'] = $this->invoiceAdjustment->getLadingBillDetail($data);
        $this->data['adjustedTrans'] = $this->invoiceAdjustment->getAdjustedTransactions(['bl_id'=>$data['id']]);
        $this->load->view($this->view,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->invoiceAdjustment->delete($id));
        endif;
    }
}
?>