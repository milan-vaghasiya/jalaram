<?php
class MaintenanceReport extends MY_Controller
{
    private $indexPage = "report/maintenance_report/index";
    private $machine_report = "report/maintenance_report/machine_report";

    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Maintenance Report";
		$this->data['headData']->controller = "reports/maintenanceReport";
		$this->data['floatingMenu'] = '';//$this->load->view('report/maintenance_report/floating_menu',[],true);
	}
	
	public function index(){
		$this->data['headData']->pageTitle = $this->data['pageHeader'] = 'MAINTENANCE REPORT';
        $this->load->view($this->indexPage,$this->data);
    }

	public function machineReport(){
        $this->data['headData']->pageTitle = $this->data['pageHeader'] = 'MACHINES REPORT';
        $this->data['machineData'] = $this->machine->getMachineForReport();
        $this->load->view($this->machine_report,$this->data);
    }
}
?>