<?php
class ExportIncentives extends MY_Controller{
    private $index= "export_incentives/index";
    private $form = "export_incentives/form";

    public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "Export Incentives";
		$this->data['headData']->controller = "exportIncentives";
	}

    public function index(){
        $this->data['tableHeader'] = getExportDtHeader($this->data['headData']->controller);
        $this->load->view($this->index,$this->data);
    }

    public function getDTRows(){
        $data = $this->input->post();
        $result = $this->ladingBill->getExportIncentivesDTRows($data);
		
        $sendData = array();$i=1;$count=0;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getExportIncentivesData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function editExportIncentives(){
        $data = $this->input->post();
        $this->data['dataRow'] = $this->ladingBill->getLadingBill($data);
        $this->load->view($this->form,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $data['drawback_date'] = (!empty($data['drawback_date']))?$data['drawback_date']:NULL;
        $data['igst_refund_date'] = (!empty($data['igst_refund_date']))?$data['igst_refund_date']:NULL;
        $data['rodtep_date'] = (!empty($data['rodtep_date']))?$data['rodtep_date']:NULL;
        $this->printJson($this->ladingBill->saveExportIncentives($data));
    }
}
?>