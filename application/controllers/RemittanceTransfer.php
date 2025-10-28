<?php
class RemittanceTransfer extends MY_Controller{
    private $index = "remittance_transfer/index";
    private $form = "remittance_transfer/form";

    public function __construct(){
        parent::__construct();
		$this->data['headData']->pageTitle = "Remittance Transfer";
		$this->data['headData']->controller = "remittanceTransfer";
    }

    public function index(){
        $this->data['tableHeader'] = getExportDtHeader($this->data['headData']->controller);
        $this->load->view($this->index,$this->data);
    }

    public function getDTRows($status=0){
        $data = $this->input->post();$data['status'] = $status;
        $result = $this->remittanceTransfer->getDTRows($data);
		
        $sendData = array();$i=1;$count=0;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $row->tab_status = $status;
            $sendData[] = getRemittanceTransferData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function remittanceTransfer(){
        $data = $this->input->post();
        $this->data['swiftData'] = $this->swiftRemittance->getSwiftRemittance($data);
        $this->data['dataRow'] = $this->remittanceTransfer->getRemittanceTransactions($data);
        $this->load->view($this->form,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['itemData']))
            $errorMessage['transfer'] = "Please add atleast one transfer.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->remittanceTransfer->save($data));
        endif;
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->remittanceTransfer->delete($id));
        endif;
    }
}
?>