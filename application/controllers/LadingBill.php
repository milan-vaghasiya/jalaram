<?php
class LadingBill extends MY_Controller{
    private $index= "lading_bill/index";
    private $form = "lading_bill/form";

    public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "Bill of Lading";
		$this->data['headData']->controller = "ladingBill";
	}
	
	public function index(){
        $this->data['tableHeader'] = getExportDtHeader($this->data['headData']->controller);
        $this->load->view($this->index,$this->data);
    }

    public function getDTRows($status=0){
        $data = $this->input->post(); $data['status'] = $status;
        $result = $this->ladingBill->getDTRows($data);
		
        $sendData = array();$i=1;$count=0;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;         
            $row->tab_status = $status;
            $sendData[] = getLadingBillData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addLadingBill(){
        $data = $this->input->post();
        $dataRow = $this->shippingBill->getShippingBill($data);
        $dataRow->sb_id = $dataRow->id; unset($dataRow->id);
        $this->data['dataRow'] = $dataRow;
        $this->load->view($this->form,$this->data);
    }

    public function save(){
        $data = $this->input->post();
		$errorMessage = array();	
        if(empty($data['inco_terms'])):
        	$errorMessage['inco_terms'] = "Inco Terms is required.";
        elseif(!empty($data['inco_terms']) && strlen($data['inco_terms']) < 3 || strlen($data['inco_terms']) > 3):
            $errorMessage['inco_terms'] = "Please Enter only 3 Character in Inco Terms";
        endif;
        if(empty($data['cha_fa']))
            $errorMessage['cha_fa'] = "CH & FA is required.";
        if(empty($data['bl_awb_no']))
			$errorMessage['bl_awb_no'] = "BL/AWB No. is required.";
        if(empty($data['bl_awb_date']))
            $errorMessage['bl_awb_date'] = "BL/AWB Date is required.";
        if(empty($data['payment_due_date']))
            $errorMessage['payment_due_date'] = "Payment Due Date is required.";
        if(empty($data['bl_remark']))
            $errorMessage['bl_remark'] = "BL Remark is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->ladingBill->save($data));
        endif;
    }

    public function edit(){     
        $id = $this->input->post();
        $this->data['dataRow'] = $this->ladingBill->getLadingBill($id);
        $this->load->view($this->form,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->ladingBill->delete($id));
        endif;
    }
}
?>