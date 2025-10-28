<?php
class ShippingBill extends MY_Controller{
    private $index= "shipping_bill/index";
    private $form = "shipping_bill/form";

    public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "Shipping Bill";
		$this->data['headData']->controller = "shippingBill";
	}
	
	public function index(){
        $this->data['tableHeader'] = getExportDtHeader($this->data['headData']->controller);
        $this->load->view($this->index,$this->data);
    }

    public function getDTRows($status=0){
        $data = $this->input->post(); $data['status'] = $status;
        $result = $this->shippingBill->getDTRows($data);
		
        $sendData = array();$i=1;$count=0;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;         
            $row->tab_status = $status;
            $sendData[] = getShippingBillData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addShippingBill(){
        $data = $this->input->post();
        $dataRow = $this->commercialInvoice->getCommercialInvocieData($data['id'],0,0);
        $dataRow->com_inv_id = $dataRow->id; unset($dataRow->id,$dataRow->igst_amount);
        $this->data['dataRow'] = $dataRow;
        $this->load->view($this->form,$this->data);
    }

    public function save(){
        $data = $this->input->post();
		$errorMessage = array();		
        if(empty($data['sb_amount']))
			$errorMessage['sb_amount'] = "SB Amount is required.";
        if(empty($data['port_code'])):
			$errorMessage['port_code'] = "Port Code is required.";
        elseif(!empty($data['port_code']) && strlen($data['port_code']) < 6 || strlen($data['port_code']) > 6):
            $errorMessage['port_code'] = "Please Enter only 6 Character in Port Code";
        endif;
        if(empty($data['sb_number'])):
            $errorMessage['sb_number'] = "SB No. is required.";
        elseif(!empty($data['sb_number']) && strlen($data['sb_number']) < 7 || strlen($data['sb_number']) > 7):
            $errorMessage['sb_number'] = "Please Enter only 7 Character in SB No.";
        endif;
        if(empty($data['sb_date']))
            $errorMessage['sb_date'] = "SB Date is required.";
        if(empty($data['sb_fob_inr']))
            $errorMessage['sb_fob_inr'] = "SB FOB INR is required.";
        if(empty($data['sb_ex_rate']))
            $errorMessage['sb_ex_rate'] = "SB Ex. Rate is required.";
        if(empty($data['sb_remark']))
            $errorMessage['sb_remark'] = "SB Remark is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->shippingBill->save($data));
        endif;
    }

    public function edit(){     
        $id = $this->input->post();
        $this->data['dataRow'] = $this->shippingBill->getShippingBill($id);
        $this->load->view($this->form,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->shippingBill->delete($id));
        endif;
    }
}
?>