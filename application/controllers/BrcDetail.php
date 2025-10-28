<?php
class BrcDetail extends MY_Controller{
    private $index = "brc_detail/index";
    private $form = "brc_detail/form";

    public function __construct(){
        parent::__construct();
		$this->data['headData']->pageTitle = "BRC Detail";
		$this->data['headData']->controller = "brcDetail";
    }

    public function index(){
        $this->data['tableHeader'] = getExportDtHeader("brcDetail");
        $this->load->view($this->index,$this->data);
    }

    public function getDTRows($status=0){
        $data = $this->input->post();$data['status'] = $status;
        $result = $this->brcDetail->getDTRows($data);
		
        $sendData = array();$i=1;$count=0;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $row->tab_status = $status;
            $sendData[] = getBRCDetailData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function brcDetail(){
        $data = $this->input->post();
        $this->data['brcDetail'] = $this->brcDetail->getBRCDetail($data);
        $this->load->view($this->form,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $this->printJson($this->brcDetail->save($data));
    }
}
?>