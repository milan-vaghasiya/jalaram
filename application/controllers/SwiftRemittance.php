<?php
class SwiftRemittance extends MY_Controller{
    private $index = "swift_remittance/index";
    private $form = "swift_remittance/form";

    public function __construct(){
        parent::__construct();
		$this->data['headData']->pageTitle = "Swift Remittance";
		$this->data['headData']->controller = "swiftRemittance";
    }

    public function index(){
        $this->data['tableHeader'] = getExportDtHeader($this->data['headData']->controller);
        $this->load->view($this->index,$this->data);
    }

    public function getDTRows(){
        $data = $this->input->post();
        $result = $this->swiftRemittance->getDTRows($data);
		
        $sendData = array();$i=1;$count=0;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getSwiftRemittanceData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addSwiftRemittance(){
        $this->data['currencyList'] = $this->party->getCurrency();
        $this->data['countryList'] = $this->party->getCountries();
        $this->load->view($this->form,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['firc_number']))
            $errorMessage['firc_number'] = "FIRC No. is required.";
        if(empty($data['remittance_date']))
            $errorMessage['remittance_date'] = "Remittance Date is required.";
        if(empty($data['remitter_name']))
            $errorMessage['remitter_name'] = "Remittance Name is required.";
        if(empty($data['remitter_country']))
            $errorMessage['remitter_country'] = "Remittance Country is required.";
        if(empty($data['swift_currency']))
            $errorMessage['swift_currency'] = "Swift Currency is required.";
        if(empty($data['swift_amount']))
            $errorMessage['swift_amount'] = "Swift Amount is required.";
        if(empty($data['firc_amount']))
            $errorMessage['firc_amount'] = "FIRC Amount is required.";
        if(!empty($data['swift_amount']) && !empty($data['firc_amount'])):
            if(floatval($data['firc_amount']) > floatval($data['swift_amount']))
                $errorMessage['firc_amount'] = "Invalid FIRC Amount.";
        endif;

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->loginId;
            $this->printJson($this->swiftRemittance->save($data));
        endif;
    }

    public function edit(){
        $id = $this->input->post();
        $this->data['dataRow'] = $this->swiftRemittance->getSwiftRemittance($id);
        $this->data['currencyList'] = $this->party->getCurrency();
        $this->data['countryList'] = $this->party->getCountries();
        $this->load->view($this->form,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->swiftRemittance->delete($id));
        endif;
    }
}
?>