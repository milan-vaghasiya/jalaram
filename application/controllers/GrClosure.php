<?php
class GrClosure extends MY_Controller{
    private $index = "gr_closure/index";
    private $form = "gr_closure/form";
    private $view = "gr_closure/view";

    public function __construct(){
        parent::__construct();
		$this->data['headData']->pageTitle = "GR Closure";
		$this->data['headData']->controller = "grClosure";
    }

    public function index(){
        $this->data['tableHeader'] = getExportDtHeader("grClosure");
        $this->load->view($this->index,$this->data);
    }

    public function getDTRows($status=0){
        $data = $this->input->post();$data['status'] = $status;
        $result = $this->grClosure->getDTRows($data);
		
        $sendData = array();$i=1;$count=0;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $row->tab_status = $status;
            $sendData[] = getGrClosureData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    function grClosure(){
        $data = $this->input->post();
        $this->data['shippingDetail'] = $this->shippingBill->getShippingBill($data);
        $this->data['unmappedSwifts'] = $this->swiftRemittance->getUnmappedSwifts();
        $this->load->view($this->form,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty(floatval($data['total_mapped_firc']))):
            $errorMessage['settlementError'] = "Please enter Mapped FIRC amount.";
        else:
            if($data['total_mapped_firc'] > $data['sb_amount']):
                $errorMessage['settlementError'] = "More than the amount of SB is mapped to FIRC. Please enter a mapping amount equal to or less than the SB amount.";
            endif;
            unset($data['sb_amount']);
        endif;

        if(empty($data['req_ref_no']))
            $errorMessage['req_ref_no'] = "Request Ref. No. is required.";
        if(empty($data['bank_bill_id']))
            $errorMessage['bank_bill_id'] = "Bank Bill Id is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->grClosure->save($data));
        endif;
    }

    public function viewTransaction(){
        $data = $this->input->post();
        $this->data['shippingDetail'] = $this->shippingBill->getShippingBill($data);
        $this->data['mappedSwifts'] = $this->grClosure->getMappedTransactions(['bl_id'=>$data['id']]);
        $this->load->view($this->view,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->grClosure->delete($id));
        endif;
    }
}
?>