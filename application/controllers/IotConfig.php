<?php
class IotConfig extends MY_Controller
{
    private $indexPage = "iot/index";
    private $iotIdleTime = "iot/iot_idle_time";
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "IOT Config";
		$this->data['headData']->controller = "iotConfig";
		$this->data['headData']->pageUrl = "iotConfig";
	}

    public function index(){
        $this->data['tableHeader'] = getMaintenanceDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){        
        $data = $this->input->post(); 
        $result = $this->iotConfig->getDTRows($data,1);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getIotConfigData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function getIdleTime(){
        $data = $this->input->post();
        $this->data['machine_id'] = $data['id'];
        $this->data['dataRow'] = $this->iotConfig->getIotConfigData($data['id']);
        $this->load->view($this->iotIdleTime,$this->data);
    }
    public function saveIdleTime(){
        $data = $this->input->post();
		$errorMessage = array();
		if(empty($data['idle_time'])){$errorMessage['idle_time'] = "Idle Time is required.";}
		
		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
            if(empty($data['id'])){
                $data['created_by'] = $this->session->userdata('loginId');
            }else{
                $data['updated_by'] = $this->session->userdata('loginId'); 
            }
			$this->printJson($this->iotConfig->saveIdleTime($data));
		endif;
    }
}
?>