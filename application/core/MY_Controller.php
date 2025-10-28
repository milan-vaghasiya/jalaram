<?php 
defined( 'BASEPATH' )OR exit( 'No direct script access allowed' );
class MY_Controller extends CI_Controller{
	
	
	public function __construct(){
		parent::__construct();
		//echo '<br><br><hr><h1 style="text-align:center;color:red;">We are sorry!<br>Your ERP is Updating New Features</h1><hr><h2 style="text-align:center;color:green;">Thanks For Co-operate</h1>';exit;
		$this->isLoggedin();
		$this->data['headData'] = new StdClass;
		$this->load->library('form_validation');
		$this->load->library('fcm');
		
		$this->load->model('masterModel');
		$this->load->model('DropdownModel','dropdown');
		$this->load->model('DashboardModel','dashboard');
		$this->load->model('NotificationModel','notification');
		$this->load->model('EmailModel','mails');
		$this->load->model('MachineModel','machine');
		$this->load->model('TermsModel','terms');
		$this->load->model('MasterOptionsModel', 'masterOption');
		$this->load->model('StoreModel','store');
		$this->load->model('PartyModel','party');
		$this->load->model('ItemModel','item');
		$this->load->model('ItemCategoryModel','itemCategory');
		$this->load->model('PurchaseRequestModel','purchaseRequest');
		$this->load->model('PurchaseEnquiryModel','purchaseEnquiry');
		$this->load->model('PurchaseOrderModel','purchaseOrder');
		$this->load->model('GrnModel','grnModel');
		$this->load->model('PurchaseInvoiceModel','purchaseInvoice');
		$this->load->model('RejectionCommentModel','comment');
		$this->load->model('InstrumentModel','instrument');
		$this->load->model('InChallanModel','inChallan');
		$this->load->model('OutChallanModel','outChallan');

		$this->load->model('SalesEnquiryModel','salesEnquiry');
		$this->load->model('SalesOrderModel','salesOrder');
		$this->load->model('DeliveryChallanModel','challan');
		$this->load->model('SalesInvoiceModel','salesInvoice');
		$this->load->model('LeadModel','leads');
		$this->load->model('SalesQuotationModel','salesQuotation');
		$this->load->model('ReportModel','reportModel');
		$this->load->model('ProductReporModel','productReporModel');
		$this->load->model('TransactionMainModel','transModel');
		$this->load->model('ProformaInvoiceModel','proformaInv');

		$this->load->model('StockVerificationModel', 'stockVerify');
		$this->load->model('MachineTicketModel', 'ticketModel');
		$this->load->model('ShiftModel', 'shiftModel');
		$this->load->model('MachineActivitiesModel', 'activities');
		$this->load->model('PackingModel', 'packings');
		$this->load->model('ToolsIssueModel', 'toolsIssue');
		$this->load->model('StockJournalModel', 'stockJournal');
		$this->load->model('PackingInstructionModel', 'packingInstruction');
		$this->load->model('FeasibilityReasonModel','feasibilityReason');
		$this->load->model('CftAuthorizationModel', 'cftAuthorization');
		$this->load->model('FamilyGroupModel','familyGroup');
		
		$this->load->model('MainMenuConfModel','mainMenuConf');
		$this->load->model('SubMenuConfModel','subMenuConf');
		
		/*** Account Model ***/
		$this->load->model('LedgerModel','ledger');
		$this->load->model('PaymentTransactionModel','paymentTrans');
		$this->load->model('PaymentVoucherModel','paymentVoucher');
		$this->load->model('GroupModel','group');

		/***  Report Model ***/
		$this->load->model('report/ProductionReportModel','productionReports');
		$this->load->model('report/QualityReportModel','qualityReports');
		$this->load->model('report/StoreReportModel', 'storeReportModel');
		$this->load->model('report/SalesReportModel', 'salesReportModel');
		$this->load->model('report/PurchaseReportModel', 'purchaseReport');
		$this->load->model('report/AccountingReportModel', 'accountingReport');
		$this->load->model('report/ProductionReportNewModel','productionReportsNew');
		
		/*** HR Model ***/
		$this->load->model('hr/DepartmentModel','department');
		$this->load->model('hr/EmployeeModel','employee');
		$this->load->model('hr/AttendanceModel','attendance');
		$this->load->model('hr/LeaveModel','leave');
		$this->load->model('hr/LeaveSettingModel','leaveSetting');
		$this->load->model('hr/LeaveApproveModel','leaveApprove');
		$this->load->model('hr/PayrollModel','payroll');
		$this->load->model('PermissionModel','permission');
		$this->load->model('hr/ManualAttendanceModel','manualAttendance');
		$this->load->model('hr/ExtraHoursModel','extraHours');
		$this->load->model('hr/DesignationModel','designation');
		$this->load->model('hr/BiometricModel','biometric');
		$this->load->model('hr/EmpLoanModel','empLoan');
		$this->load->model('hr/AdvanceSalaryModel','advanceSalary');
		$this->load->model('hr/SkillMasterModel', 'skillMaster');
		$this->load->model('hr/SalaryStructureModel', 'salaryStructure');
		$this->load->model('visitPurposeModel','visitPurpose');

		$this->load->model('AssignInspectorModel','assignInspector');
		$this->load->model('MaterialGradeModel','materialGrade');
		$this->load->model('ExpenseMasterModel','expenseMaster');
		$this->load->model('TaxMasterModel','taxMaster');
		$this->load->model('DebitNoteModel','debitNote');
		$this->load->model('CreditNoteModel','creditNote');
		$this->load->model('AttendancePolicyModel', 'policy');
		$this->load->model('GstExpenseModel','gstExpense');
		$this->load->model('JournalEntryModel','journalEntry');
		$this->load->model('ContactDirectoryModel','contactDirectory');
		$this->load->model('NotifyPermissionModel','notify');
		$this->load->model('GeneralIssueModel','generalIssue');
		$this->load->model('InspectionTypeModel','inspectionType');
		$this->load->model('HsnMasterModel','hsnModel');
		$this->load->model('CommercialPackingModel','commercialPacking');
		$this->load->model('CommercialInvoiceModel','commercialInvoice');
		$this->load->model('SampleInvoiceModel','sampleInvoice');
		$this->load->model('CustomPackingModel','customPacking');
		$this->load->model('CustomInvoiceModel','customInvoice');
		$this->load->model('TransportModel','transport');
		$this->load->model('BankingModel','banking');

		$this->load->model('PackingRequestModel','packingRequest');
		$this->load->model('CategoryModel', 'category');
		$this->load->model('VisitorLogModel', 'visitorLog');
		$this->load->model('MergeModel', 'mergeModel'); 
		$this->load->model('PrintFormatModel','printFormat');
		$this->load->model('IotConfigModel','iotConfig');
		$this->load->model('SupplierRejectionModel','supplierRejection');
		$this->load->model('CostingModel','costingModel');
		$this->load->model('EwaybillModel','eway');
		$this->load->model('DispatchRequestModel','dispatchRequest');
		$this->load->model('StockTransactionModel','stockTransac');
		

		/*** Production */
		$this->load->model('ProcessModel','process');
		$this->load->model('RmProcessModel','rmProcessModel');
		$this->load->model('production_v3/JobV3Model','jobcard_v3');
		$this->load->model('production_v3/ProcessMovementV3Model','processMovement');
		$this->load->model('production_v3/JobWorkVendorV3Model','jobWorkVendor_v3');
		$this->load->model('production_v3/ProductionLogModel','productionLog');
		$this->load->model('RejectionLogModel','rejectionLog');
		$this->load->model('production_v3/NpdJobcardModel','npdJobcard');
		$this->load->model('NpdMaterialIssueModel','npdMaterialIssue');

		/****NPD****/
		$this->load->model('npd/ControlPlanV2Model','controlPlanV2');
		$this->load->model('npd/ReactionPlanModel','reactionPlan');
		$this->load->model('npd/ControlMethodModel','controlMethod');
		$this->load->model('npd/ResponsibilityModel', 'responsibility');
		$this->load->model('RtsQuestionModel', 'rtsQuestion');
		$this->load->model('production_v3/PirModel','pir');
		$this->load->model('FirModel','fir');
		$this->load->model('ToolsIssueModel','toolsIssue');
		$this->load->model('RegrindingReasonModel','regrindingReason');
		$this->load->model('InspectionModel','inspection');
		$this->load->model('StockTransactionModel','stockTransac');
		$this->load->model('MasterDetailModel','masterDetail');
		$this->load->model('npd/EcnModel','ecn');
		$this->load->model('production_v3/RqcModel','rqc');
		$this->load->model('GenerateScrapModel','generateScrap');
		$this->load->model('JobMaterialDispatchModel','jobMaterial');
		$this->load->model('LogSheetModel','logSheet');
		$this->load->model('JobWorkOrderModel','jobWorkOrder');
		$this->load->model('MaterialRequestModel','jobMaretialRequest'); 
		$this->load->model('ScrapModel','scrap');
		$this->load->model('npd/processCodeModel','processCode');
		
		$this->load->model('QCIndentModel', 'qcIndent');
		$this->load->model('QCPurchaseModel', 'qcPurchase');
		$this->load->model('QcPRModel', 'qcPRModel');
		$this->load->model('QcInstrumentModel','qcInstrument');
		$this->load->model('QcChallanModel','qcChallan');

		/* Export Models */
		$this->load->model("ShippingBillModel","shippingBill");
		$this->load->model("LadingBillModel","ladingBill");
		$this->load->model("SwiftRemittanceModel","swiftRemittance");
		$this->load->model("RemittanceTransferModel","remittanceTransfer");
		$this->load->model("InvoiceSettlementModel","invoiceSettlement");
		$this->load->model("GrClosureModel",'grClosure');
		$this->load->model("BrcDetailModel",'brcDetail');
		$this->load->model("TaxInvoiceAdjustmentModel",'invoiceAdjustment');
		
		$this->data['currentFormDate'] = $this->session->userdata("currentFormDate");
		$this->financialYearList = $this->getFinancialYearList($this->session->userdata('issueDate'));	

		$this->setSessionVariables('notification,dropdown,mails,machine,store,party,item,itemCategory,purchaseEnquiry,purchaseOrder,purchaseInvoice,comment,salesEnquiry,salesOrder,challan,salesInvoice,leads,reportModel,department,employee,attendance,leave,leaveSetting,leaveApprove,payroll,grnModel,purchaseRequest,transModel,masterOption,productionReports,qualityReports,inChallan,outChallan,stockVerify,ticketModel,shiftModel,proformaInv,storeReportModel,salesReportModel,activities,purchaseReport,permission,packings,toolsIssue,stockJournal,packingInstruction,ledger,paymentTrans,assignInspector,feasibilityReason,cftAuthorization,familyGroup,paymentVoucher,mainMenuConf,subMenuConf,group,expenseMaster,taxMaster,debitNote,creditNote,policy,biometric,designation,extraHours,manualAttendance,materialGrade,gstExpense,journalEntry,accountingReport,productionReportsNew,contactDirectory,notify,generalIssue,inspectionType,hsnModel,commercialPacking,commercialInvoice,customPacking,customInvoice,process,rmProcessModel,empLoan,advanceSalary,transport,banking,generateScrap,packingRequest,category,visitorLog,mergeModel,skillMaster,printFormat,eway,iotConfig,dashboard,costingModel,dispatchRequest,stockTransac,jobcard_v3,processMovement,jobWorkVendor_v3,productionLog,controlPlanV2,reactionPlan,controlMethod,responsibility,rtsQuestion,rtsQuestion,pir,fir,regrindingReason,inspection,masterDetail,ecn,rqc,jobMaterial,rejectionLog,logSheet,jobWorkOrder,jobMaretialRequest,scrap,qcIndent,qcPurchase,qcPRModel,qcInstrument,qcChallan,npdJobcard,npdMaterialIssue,shippingBill,ladingBill,swiftRemittance,remittanceTransfer,invoiceSettlement,grClosure,brcDetail,visitPurpose,invoiceAdjustment,processCode,sampleInvoice');
		
		$this->symbolArray = $this->data['symbolArray'] = [''=>'', 'operation'=>'Operation', 'oper_insp'=>'Oper. & Insp.', 'inspection'=>'Inspection', 'storage'=>'Storage', 'delay'=>'Delay', 'decision'=>'Decision', 'transport'=>'Transport', 'connector'=>'Connector'];	
        $this->classArray = $this->data['classArray'] = [''=>'', 'critical'=>'Critical Characteristic', 'major'=>'Major', 'minor'=>'Minor','pc'=>'process critical characteristics'];

		$this->data['stockTypes'] = [-1=>'Opening Stock',0=>'',1=>'GRN', 2=>'Purchase Invoice', 3=>'Material Issue', 4=>'Delivery Challan', 5=>'Sales Invoice', 6=>'Manual Manage Stock', 7=>'Production Finish', 8 =>'Visual Inspection', 9 =>'Store Transfer', 10=>'Return Stock From Production', 11=>'In Challan', 12=>'Out Challan', 13=>'Tools Issue', 14 =>'Stock Journal', 15 =>'Packing Material', 16 =>'Packing Product', 17 =>'Rejection Scrap', 18 =>'Production Scrap', 19 =>'Credit Note', 20 =>'Debit Note', 21=>'General Issue', 22 =>'Stock Verification', 23 =>'Process Movement', 24 =>'Production Rejection', 25=>'Production Rejection Scrap', 26=>'Move to Allocation RM Store', 27=>'Move To Received RM Store', 28=>'Move To Packing Area', 29=>'RM Process',30 => 'Regular/Export Packing', 31 => 'Jobwork Return', 32 => 'Job Short Closed',33=>'Item Transfer In Packing Area',34=>'Supplier Rejection',35=>'Final Packing',36=>'Packing',37=>'Tool Issue',38=>'Tool Issue Return',39=>'Regrinding',40=>'Receive Regrinding',41=>'Regrinding Coverted Item',42=>'Return Insp. Material',43=>'NPD Jobcard',44=>'NPD Jobcard', 99=>'Stock Adjustment',127=>'',999=>'Stock Adjustment'];	
	}

	public function setSessionVariables($modelNames)
	{
		$this->data['dates'] = explode(' AND ',$this->session->userdata('financialYear'));
		$this->shortYear = $this->data['shortYear'] = date('y',strtotime($this->data['dates'][0])).'-'.date('y',strtotime($this->data['dates'][1]));
		$this->startYearDate = $this->data['startYearDate'] = date('Y-m-d',strtotime($this->data['dates'][0]));
		$this->endYearDate = $this->data['endYearDate'] = date('Y-m-d',strtotime($this->data['dates'][1]));
		$this->data['start_year'] = date('Y',strtotime($this->data['dates'][0]));
		$this->data['end_year'] = date('Y',strtotime($this->data['dates'][1]));
		$this->loginId = $this->session->userdata('loginId');
		$this->userRole = $this->session->userdata('role');
		$this->userName = $this->session->userdata('user_name');
		$this->emp_dept_id = $this->session->userdata('emp_dept_id');
		$this->party_id = $this->session->userdata('party_id');

		$this->RTD_STORE = $this->session->userdata('RTD_STORE');
		$this->PKG_STORE = $this->session->userdata('PKG_STORE');
		$this->SCRAP_STORE = $this->session->userdata('SCRAP_STORE');
		$this->PROD_STORE = $this->session->userdata('PROD_STORE');
		$this->GAUGE_STORE = $this->session->userdata('GAUGE_STORE');
		$this->ALLOT_RM_STORE = $this->session->userdata('ALLOT_RM_STORE');
		$this->RCV_RM_STORE = $this->session->userdata('RCV_RM_STORE');
		$this->HLD_STORE = $this->session->userdata('HLD_STORE');
		$this->RM_PRS_STORE = $this->session->userdata('RM_PRS_STORE');
		$this->MIS_PLC_STORE = $this->session->userdata('MIS_PLC_STORE');
		$this->SUP_REJ_STORE = $this->session->userdata('SUP_REJ_STORE');
		$this->INSP_STORE = $this->session->userdata('INSP_STORE');
		$this->REGRIND_STORE = $this->session->userdata('REGRIND_STORE');
		$this->data['ip_address'] = $this->get_client_ip();
		
		$models = explode(',',$modelNames);
		
		if($this->endYearDate <= date("Y-m-d")){$this->data['maxDate'] = $this->endYearDate;}else{$this->data['maxDate'] = date('Y-m-d');}
		foreach($models as $modelName):
			$modelName = trim($modelName);
			$this->{$modelName}->dates = $this->data['dates'];
			$this->{$modelName}->loginID = $this->session->userdata('loginId');
			$this->{$modelName}->userName = $this->session->userdata('user_name');
			$this->{$modelName}->userRole = $this->session->userdata('role');
			$this->{$modelName}->userRoleName = $this->session->userdata('roleName');
			$this->{$modelName}->emp_dept_id = $this->session->userdata('emp_dept_id');
			$this->{$modelName}->party_id = $this->session->userdata('party_id');

			$this->{$modelName}->shortYear = date('y',strtotime($this->data['dates'][0])).'-'.date('y',strtotime($this->data['dates'][1]));
			$this->{$modelName}->startYear = date('Y',strtotime($this->data['dates'][0]));
			$this->{$modelName}->endYear = date('Y',strtotime($this->data['dates'][1]));
			$this->{$modelName}->startYearDate = date('Y-m-d',strtotime($this->data['dates'][0]));
			$this->{$modelName}->endYearDate = date('Y-m-d',strtotime($this->data['dates'][1]));
			$this->{$modelName}->RTD_STORE = $this->session->userdata('RTD_STORE');
			$this->{$modelName}->PKG_STORE = $this->session->userdata('PKG_STORE');
			$this->{$modelName}->SCRAP_STORE = $this->session->userdata('SCRAP_STORE');
			$this->{$modelName}->PROD_STORE = $this->session->userdata('PROD_STORE');
			$this->{$modelName}->GAUGE_STORE = $this->session->userdata('GAUGE_STORE');
			$this->{$modelName}->ALLOT_RM_STORE = $this->session->userdata('ALLOT_RM_STORE');
			$this->{$modelName}->RCV_RM_STORE = $this->session->userdata('RCV_RM_STORE');
			$this->{$modelName}->HLD_STORE = $this->session->userdata('HLD_STORE');
			$this->{$modelName}->RM_PRS_STORE = $this->session->userdata('RM_PRS_STORE');
			$this->{$modelName}->MIS_PLC_STORE = $this->session->userdata('MIS_PLC_STORE');
			$this->{$modelName}->SUP_REJ_STORE = $this->session->userdata('SUP_REJ_STORE');
			$this->{$modelName}->INSP_STORE = $this->session->userdata('INSP_STORE');
			$this->{$modelName}->REGRIND_STORE = $this->session->userdata('REGRIND_STORE');
		endforeach;
		return true;
	}
	
	public function getFinancialYearList($issueDate){
		$startYear  = ((int)date("m",strtotime($issueDate)) >= 4) ? date("Y",strtotime($issueDate)) : (int)date("Y",strtotime($issueDate)) - 1;
		$endYear  = ((int)date("m") >= 4) ? date("Y") + 1 : (int)date("Y");
		
		$startDate = new DateTime($startYear."-04-01");
		$endDate = new DateTime($endYear."-03-31");
		$interval = new DateInterval('P1Y');
		$daterange = new DatePeriod($startDate, $interval ,$endDate);
		$fyList = array();$val="";$label="";
		foreach($daterange as $dates)
		{
			$start_date = date("Y-m-d H:i:s",strtotime("01-04-".$dates->format("Y")." 00:00:00"));
			$end_date = date("Y-m-d H:i:s",strtotime("31-03-".((int)$dates->format("Y") + 1)." 23:59:59"));
			
			$val = $start_date." AND ".$end_date;
			$label = 'Year '.date("Y",strtotime($start_date)).'-'.date("Y",strtotime($end_date));
			$fyList[] = ["label" => $label, "val" => $val];
		}
		return $fyList;
	}
	
	public function getMonthListFY(){
		$monthList = array();
		$start    = (new DateTime($this->startYearDate))->modify('first day of this month');
        $end      = (new DateTime($this->endYearDate))->modify('first day of next month');
        $interval = DateInterval::createFromDateString('1 month');
        $period   = new DatePeriod($start, $interval, $end);
        $i=0;
        foreach ($period as $dt) {
            $monthList[$i]['val'] = $dt->format("Y-m-d");
            $monthList[$i++]['label'] = $dt->format("F-Y");
        }
		return $monthList;
	}
	
	public function isLoggedin(){
		if(!$this->session->userdata("LoginOk")):
			//redirect( base_url() );
			echo '<script>window.location.href="'.base_url().'";</script>';
		endif;
		return true;
	}
	
	public function printJson($data){
		print json_encode($data);exit;
	}

	public function printDecimal($val){
		return number_format($val,0,'','');
	}
	
	public function checkGrants($url){
		$empPer = $this->session->userdata('emp_permission');
		if(!array_key_exists($url,$empPer)):
			redirect(base_url('error_403'));
		endif;
		return true;
	}
	
	public function get_client_ip() {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
           $ipaddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }
    
    public function get_client_ip1() {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }
    
    /**** Generate QR Code ****/
	public function getQRCode($qrData,$dir,$file_name){
		if(isset($qrData) AND isset($file_name))
		{
			$file_name .= '.png';
			/* Load QR Code Library */
			$this->load->library('ciqrcode');
			
			if (!file_exists($dir)) {mkdir($dir, 0775, true);}

			/* QR Configuration  */
			$config['cacheable']    = true;
			$config['imagedir']     = $dir;
			$config['quality']      = true;
			$config['size']         = '1024';
			$config['black']        = array(255,255,255);
			$config['white']        = array(255,255,255);
			$this->ciqrcode->initialize($config);
	  
			/* QR Data  */
			$params['data']     = $qrData;
			$params['level']    = 'L';
			$params['size']     = 10;
			$params['savename'] = FCPATH.$config['imagedir']. $file_name;
			
			$this->ciqrcode->generate($params);

			return $dir. $file_name;
        }
		else
		{
			return '';
		}
	}

	public function getTableHeader(){
		$data = $this->input->post();

		$response = call_user_func_array($data['hp_fn_name'],[$data['page']]);
		
		$result['theads'] = (isset($response[0])) ? $response[0] : '';
		$result['textAlign'] = (isset($response[1])) ? $response[1] : '';
		$result['srnoPosition'] = (isset($response[2])) ? $response[2] : 1;
		$result['sortable'] = (isset($response[3])) ? $response[3] : '';

		$this->printJson(['status'=>1,'data'=>$result]);
	}

}
?>