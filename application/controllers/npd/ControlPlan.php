<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ControlPlan extends MY_Controller
{
    private $indexPage = "npd/control_plan/index";
    private $inspectionForm = "npd/control_plan/form";
    private $pfcList = "npd/control_plan/pfc_list";
    private $pfcForm = "npd/control_plan/pfc_form";
    private $fmeaForm = "npd/control_plan/fmea_form";
    private $fmeaList = "npd/control_plan/fmea_list";
    private $diamention_list = "npd/control_plan/diamention_list";
    private $diamention_form = "npd/control_plan/diamention_form";
    private $cpList = "npd/control_plan/cp_list";
    private $cpForm = "npd/control_plan/cp_form";
    private $fmea_failure_view = "npd/control_plan/fmea_failure_view";
    private $potential_cause_form = "npd/control_plan/potential_cause_form";
    private $operation_list = "npd/control_plan/operation_list";
    private $failure_mode_form = "npd/control_plan/failure_mode_form";
    private $pfc_operation_view = "npd/control_plan/pfc_operation_view";
    private $cp_diamention_list = "npd/control_plan/cp_diamention_list";
    private $control_plan_method = "npd/control_plan/control_plan_method";
    private $cp_dimenstion_form  = "npd/control_plan/cp_dimenstion_form";
    private $cp_rev_form  = "npd/control_plan/cp_rev_form";
    private $revision_form  = "npd/control_plan/revision_form";
    private $pfc_rev_form  = "npd/control_plan/pfc_rev_form";
    private $pfcStage = [0=>'',1=>'IIR' , 2=>'Production', 3=>'FIR', 4=>'PDI',5=>'Packing',6=>'Dispatch',7=>'RQC',8=>'PFIR'];
    // private $processCode = ['MPIN','RMDI','RMMT','CUTT','TRAB','CLEN','DBRG','IINP','CNCT','VMCM','HMCM','MARK','FINP','PDIN','OIPA','ASSY','THRD','HONN'];

    public function __construct()
    {
        parent::__construct();
        $this->isLoggedin();
        $this->data['headData']->pageTitle = "PFC & Control Plan";
        $this->data['headData']->controller = "npd/controlPlan";
        $this->data['headData']->pageUrl = "npd/controlPlan";
    }

    public function index()
    { 
        $this->data['tableHeader'] = getQualityDtHeader('controlPlanV2');
        $this->load->view($this->indexPage, $this->data);
    }

    public function getDTRows()
    {
        $data = $this->input->post();
        $result = $this->controlPlanV2->getProdOptDTRows($data, 1);
        $sendData = array();
        $i = 1;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $sendData[] = getControlPlanDataV2($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    

    /********** PFC ***********/
    public function pfcList($id = "")
    {

        $this->data['tableHeader'] = getQualityDtHeader("pfc");
        $this->data['item_id'] = $id;
        $this->data['itemData'] = $this->item->getItem($id);
        $this->data['pfcData'] = $this->controlPlanV2->getCpDimensionData(['item_id'=>$id]);
        $this->data['revList'] = $this->ecn->getEcnRevList(['item_id'=>$id]);
        $this->load->view($this->pfcList, $this->data);
    }
    public function getPFCDTRows($item_id = "")
    {
        $data = $this->input->post();
        $data['item_id'] = $item_id;
        $result = $this->controlPlanV2->getPFCDTRows($data);
        $sendData = array();
        $i = 1;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getPFCData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addPfc($item_id)
    {

        $this->data['itemList'] = $this->item->getItemList(1);
        $this->data['item_id'] = $item_id;
        $this->data['empList'] = $this->employee->getEmpList();
        $this->data['reactionPlan'] = $this->reactionPlan->getTitleNames();
        $this->data['vendorList'] = $this->party->getVendorList();
        $this->data['supplierList'] = $this->party->getSupplierList();
        $this->data['machineTypes'] = [];//$this->machineType->getMachineTypeList();
        $this->data['processCodes'] = $this->controlPlanV2->getProcessCode();
        $this->load->view($this->pfcForm, $this->data);
    }

    public function getPFC()
    {
        $data = $this->input->post();
        $this->data['item_id'] = $data['id'];
        $this->data['pfc_number'] = 'PFC/' . $data['item_code'] . '/' . $data['app_rev_no'] . '/' . $data['rev_no'];
        $this->data['machineTypes'] = $this->machineType->getMachineTypeList();
        $this->data['pfcBodyData'] = $this->getItemWisePfcData(['item_id' => $data['id']]);
        $this->load->view($this->pfcForm, $this->data);
    }

    public function savePfc()
    {
        $data = $this->input->post();
        $errorMessage = array();$errorMessage['general_error'] = "";
        if (empty($data['item_id'])){
            $errorMessage['item_id'] = "Item is required.";
        }    
        /*if (empty($data['core_team']))
            $errorMessage['core_team'] = "Core Team is required.";*/
        if (!isset($data['process_no'])) {
            $errorMessage['general_error'] = "Add Process No ";
        } else {
            if (in_array("", $data['process_no'])) {
                $errorMessage['general_error'] = "Process No is required";
            }
            
        }
        if(empty($errorMessage['general_error'])){unset($errorMessage['general_error']);}
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $data['created_by'] = $this->session->userdata('loginId');
            $itemData = $this->item->getItem($data['item_id']);
            $data['pfc_number'] = 'PFC/' . $itemData->item_code  . '/' . $itemData->rev_no;
            $masterData = [
                'id' => $data['id'],
                'entry_type' => 1,
                'trans_number' => $data['pfc_number'],
                'item_id' => $data['item_id'],
                'product_phase' => $data['product_phase'],
                'eng_change_level' => $data['eng_change_level'],
                'created_by'=>$this->loginId
                
            ];
            $transData = [
                'id' => $data['trans_id'],
                'entry_type' => 1,
                'item_id' => $data['item_id'],
                'process_no' => $data['process_no'],
                'process_code' => $data['process_code'],
                'machine_tool' => $data['machine_tool'],
                'product_param' => $data['parameter'],
                'symbol_1' => $data['symbol_1'],
                'char_class' => $data['char_class'],
                'output_operation' => $data['output_operation'],
                'location' => $data['location'],
                // 'reaction_plan' => $data['reaction_plan'],
                'stage_type' => $data['stage_type'],
                'created_by' => $data['created_by']
            ];
            $this->printJson($this->controlPlanV2->savePfc($masterData, $transData));

        endif;
    }

    public function editPfc($id)
    {
        $pfcData = $this->controlPlanV2->getPfcData($id);
        $transData = $this->controlPlanV2->getPfcTransData($id);
        $this->data['dataRow'] =  $pfcData;
        $this->data['transData'] =  $transData;
        $this->data['itemList'] = $this->item->getItemList(1);
        $this->data['empList'] = $this->employee->getEmpList();
        $this->data['reactionPlan'] = $this->reactionPlan->getTitleNames();
        $this->data['vendorList'] = $this->party->getVendorList();
        $this->data['supplierList'] = $this->party->getSupplierList();
        $this->data['processCodes'] = $this->controlPlanV2->getProcessCode();
        $this->load->view($this->pfcForm, $this->data);
    }

    public function revisionPfc($id)
    {
        $pfcData = $this->controlPlanV2->getPfcData($id);
        $transData = $this->controlPlanV2->getPfcTransData($id);
        $this->data['dataRow'] =  $pfcData;
        $this->data['transData'] =  $transData;
        $this->data['revision'] =  1;
        $this->data['itemList'] = $this->item->getItemList(1);
        $this->data['empList'] = $this->employee->getEmpList();
        $this->data['reactionPlan'] = $this->reactionPlan->getTitleNames();
        $this->data['vendorList'] = $this->party->getVendorList();
        $this->data['machineTypes'] = $this->machineType->getMachineTypeList();
        $this->load->view($this->pfcForm, $this->data);
    }

    public function getItemWisePfcData()
    {
        $data = $this->input->post();
        $pfcData = $this->controlPlanV2->getItemWisePfcData($data['item_id']);
        $options = '<option value="">Select Process No</option>';
        if (!empty($pfcData)) {
            foreach ($pfcData as $row) {
                $options .= '<option value="' . $row->id . '" data-process_no="' . $row->process_no . '">[' . $row->process_no . '] ' . $row->parameter . '</option>';
            }
        }
        $this->printJson(['status' => 1, 'options' => $options]);
    }

    public function getOperationList()
    {
        $id = $this->input->post('id');
        $this->data['pfcTransData'] = $this->controlPlanV2->getPfcTransData($id);
        $this->data['pfcStage']  = $this->pfcStage;
        $this->load->view($this->pfc_operation_view, $this->data);
    }

    /*******************************/

    /*********** FMEA **************/

    public function fmeaList($item_id)
    {
        $this->data['tableHeader'] = getQualityDtHeader("fmea");
        $this->data['item_id'] = $item_id;
        $this->data['itemData'] = $this->item->getItem($item_id);
        $this->load->view($this->fmeaList, $this->data);
    }

    public function getFmeaDTRows($item_id = "")
    {
        $data = $this->input->post();
        $data['item_id'] = $item_id;
        $result = $this->controlPlanV2->getFmeaDTRows($data);
        $sendData = array();
        $i = 1;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getFMEAData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addFmea($item_id)
    {
        $this->data['itemList'] = $this->item->getItemList(1);
        $this->data['item_id'] = $item_id;
        $this->data['itemData'] = $this->item->getItem($item_id);
        $this->data['maxRevNo'] = $this->controlPlanV2->getMaxFmeaRevNo($item_id);
        $this->load->view($this->fmeaForm, $this->data);
    }

    public function saveFmeaMaster()
    {
        $data = $this->input->post();
        $data['created_by'] = $this->session->userdata('loginId');
        $this->printJson($this->controlPlanV2->saveFmeaMaster($data));
    }

    public function pfcOperationList($item_id)
    {
        $this->data['tableHeader'] = getQualityDtHeader("pfcOperation");
        $this->data['item_id'] = $item_id;
        $this->data['itemData'] = $this->item->getItem($item_id);
        $this->load->view($this->operation_list, $this->data);
    }

    public function getPFCOperationRows($item_id = "")
    {
        $data = $this->input->post();
        $data['item_id'] = $item_id;
        $result = $this->controlPlanV2->getPFCOperationRows($data);
        $sendData = array();
        $i = 1;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getPFCOperationData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function diamentionList($id)
    {
        $this->data['tableHeader'] = getQualityDtHeader("fmeaDiamention");
        $this->data['fmea_id'] = $id;
        $this->data['fmeaData'] = $this->controlPlanV2->getFmeaData($id);
        $this->load->view($this->diamention_list, $this->data);
    }

    public function getDiamentionDTRows($fmea_id)
    {
        $data = $this->input->post();
        $data['fmea_id'] = $fmea_id;
        $result = $this->controlPlanV2->getDiamentionDTRows($data);
        $sendData = array();
        $i = 1;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getFMEADiamentionData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addDiamention($fmea_id)
    {
        $this->data['fmea_id'] = $fmea_id;
        $this->data['fmeaData'] = $this->controlPlanV2->getFmeaData($fmea_id);
        $this->load->view($this->diamention_form, $this->data);
    }

    public function getFmea()
    {
        $data = $this->input->post();
        $itemData = $this->item->getItem($data['id']);
        $this->data['item_id'] = $data['id'];
        $this->data['fmea_number'] = 'FMEA/' . $itemData->item_code . '/' . $itemData->app_rev_no . '/' . $itemData->rev_no;
        $this->data['pfcData'] = $this->controlPlanV2->getItemWisePfc(['item_id' => $data['id']]);
        $this->data['machineTypes'] = $this->machineType->getMachineTypeList();
        $this->load->view($this->fmeaForm, $this->data);
    }

    public function saveFmea()
    {
        $data = $this->input->post();
        $errorMessage = array();
        if (empty($data['item_id']))
            $errorMessage['item_id'] = "Item is required.";
        if (empty($data['pfc_id']))
            $errorMessage['pfc_id'] = "Process No. is required.";

        if (!isset($data['parameter'])) {
            $errorMessage['general_error'] = "parameter is required ";
        } else {

            $i = 1;
            foreach ($data['parameter'] as $key => $value) {
                if (empty($value)) {
                    $errorMessage['parameter' . $i] = "parameter is required";
                }
                if (empty($value)) {
                    $errorMessage['requirement' . $i] = "requirement is required";
                }
                $i++;
            }
        }
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $masterData = [
                'id' => $data['trans_main_id'],
                'item_id' => $data['item_id'],
                'ref_id' => $data['pfc_id'],
                'edit_mode'=>$data['edit_mode']
            ];
            $transData = [
                'id' => $data['trans_id'],
                'parameter' => $data['parameter'],
                'requirement' => $data['requirement'],
                'min_req' => $data['min_req'],
                'max_req' => $data['max_req'],
                'other_req' => $data['other_req'],
                'char_class' => $data['char_class']
            ];
            $this->printJson($this->controlPlanV2->saveFmea($masterData, $transData));

        endif;
    }

    public function fmeaFailView($trans_id)
    {
        $this->data['trans_id'] = $trans_id;
        $this->data['fmeaData'] = $this->controlPlanV2->getFMEATrans($trans_id);
        $this->data['tableHeader'] = getQualityDtHeader("fmeaFail");
        $this->load->view($this->fmea_failure_view, $this->data);
    }

    public function getFMEAFailDTRows($id = "")
    {
        $data = $this->input->post();
        $data['id'] = $id;
        $result = $this->controlPlanV2->getFMEAFailDTRows($data);
        $sendData = array();
        $i = 1;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $sendData[] = getFMEAFailData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addFailureMode($id)
    {
        $this->data['trans_id'] = $id;
        $this->data['fmeaData'] = $this->controlPlanV2->getFMEATrans($id);
        $this->load->view($this->failure_mode_form, $this->data);
    }

    public function saveFailureMode()
    {
        $data = $this->input->post();
        $errorMessage = array();
        if (empty($data['item_id']))
            $errorMessage['item_id'] = "Item is required.";
        if (empty($data['pfc_id']))
            $errorMessage['pfc_id'] = "Process No. is required.";

        if (!isset($data['failure_mode'])) {
            $errorMessage['general_error'] = "Failure Mode data required ";
        } else {

            $i = 1;
            foreach ($data['failure_mode'] as $key => $value) {
                if (empty($value)) {
                    $errorMessage['failure_mode' . $i] = "Failure Mode is required";
                }
                if (!empty($data['customer'][$key]) && empty($data['cust_sev'][$key])) {
                    $errorMessage['cust_sev' . $i] = "Customer Sev is required";
                }
                if (!empty($data['manufacturer'][$key]) && empty($data['mfg_sev'][$key])) {
                    $errorMessage['mfg_sev' . $i] = "Mfg. Sev is required";
                }
                $i++;
            }
        }
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $data['created_by'] = $this->session->userdata('loginId');
            $fmeaData = [
                'id' => $data['trans_id'],
                'fmea_id' => $data['fmea_id'],
                'pfc_id' => $data['pfc_id'],
                'item_id' => $data['item_id'],
                'entry_type' => 2,
                'failure_mode' => $data['failure_mode'],
                'customer' => $data['customer'],
                'manufacturer' => $data['manufacturer'],
                'cust_sev' => $data['cust_sev'],
                'mfg_sev' => $data['mfg_sev'],
                'process_detection' => $data['process_detection'],
                'detec' => $data['detec'],
                'edit_mode'=>$data['edit_mode']
            ];
            $this->printJson($this->controlPlanV2->saveFailureMode($fmeaData));

        endif;
    }

    public function addPotentialCause()
    {
        $id = $this->input->post('id');
        $this->data['ref_id'] = $id;
        $this->data['qcFmeaData'] = $this->controlPlanV2->getQCFmeaFailData($id);
        $this->data['tbody'] = $this->getPotentialCauseData($id)['html'];
        $this->load->view($this->potential_cause_form, $this->data);
    }

    public function savePotentialCause()
    {
        $data = $this->input->post();
        $errorMessage = array();
        if (empty($data['potential_cause']))
            $errorMessage['potential_cause'] = "Cause is required.";

        if (empty($data['process_prevention']))
            $errorMessage['process_prevention'] = "Prevention is required.";

        if (empty($data['occur']))
            $errorMessage['occur'] = "Occure is required.";
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $data['created_by'] = $this->session->userdata('loginId');
            $result = $this->controlPlanV2->savePotentialCause($data);
            $this->printJson($this->getPotentialCauseData($data['ref_id']));

        endif;
    }

    public function getPotentialCauseData($ref_id)
    {
        $result = $this->controlPlanV2->getQcFmeaTblData($ref_id, 2);
        $html = '';
        if (!empty($result)) {
            $i = 1;
            foreach ($result as $row) {
                $editBtn ='<a class="btn btn-outline-success btn-edit permission-modify mr-2" href="javascript:void(0)" datatip="Edit" flow="down" onclick="editCause('.$row->id.',this);"><i class="ti-pencil-alt" ></i></a>';
                $deleteBtn ='<a class="btn btn-outline-danger btn-edit permission-remove" href="javascript:void(0)" datatip="Delete" flow="down" onclick="deleteCause('.$row->id.','.$row->ref_id.');"><i class="ti-trash" ></i></a>';
                $html .= '<tr>
                    <td>' . $i++ . '</td>
                    <td>' . $row->potential_cause . '</td>
                    <td>' . $row->occur . '</td>
                    <td>' . $row->process_prevention . '</td>
                    <td class="text-center">'.$editBtn.$deleteBtn.'</td>
                </tr>';
            }
        }
        return ['status' => 1, 'html' => $html];
    }

    public function editDiamention($fmea_id)
    {
        $this->data['cp_id'] = $fmea_id;
        $this->data['cpData'] = $this->controlPlanV2->getControlPlan($fmea_id);
        $this->data['dimensionData'] = $this->controlPlanV2->getCPTransData($fmea_id);
        $this->data['editMode'] = 1;
        $this->load->view($this->diamention_form, $this->data);
    }

    public function editFailureMode($id)
    {
        $this->data['trans_id'] = $id;
        $this->data['fmeaData'] = $this->controlPlanV2->getFMEATrans($id);
        $this->data['transData'] = $this->controlPlanV2->getQcFmeaTblData($id,1);
        $this->data['editMode'] = 1;
        $this->load->view($this->failure_mode_form, $this->data);
    }

    public function editPotentialCause(){
        $id = $this->input->post('id');
        $causeData = $this->controlPlanV2->getQCFmeaFailData($id);
        $this->printJson($causeData);
    }

    public function deletePotentialCause(){
        $data = $this->input->post();
        if (empty($data['id'])) :
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else :
            $result = $this->controlPlanV2->deleteFmeaQc($data['id']);
            $this->printJson($this->getPotentialCauseData($data['ref_id']));
        endif;
    }
    /*******************************/

    /**********Control Plan ************/
    public function controlPlanList($id = "")
    {
        if (empty($id)) :
            echo "<script>window.close();</script>";
        else :
            $this->data['tableHeader'] = getQualityDtHeader("cp");
            $this->data['pfc_id'] = $id;
            $this->data['pfcData'] = $pfcData = $this->controlPlanV2->getPfcData($id);
            $this->data['revList'] = $this->ecn->getCpRevData(['item_id'=>$pfcData->item_id,'status'=>3]);
            $this->load->view($this->cpList, $this->data);
        endif;
    }

    public function getControlPlanDTRows($pfc_id)
    {
        $data = $this->input->post();
        // $data['item_id'] = $item_id;
        $data['pfc_id'] = $pfc_id;
        $result = $this->controlPlanV2->getControlPlanDTRows($data);
        $sendData = array();
        $i = 1;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getCPData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addControlPlan($item_id)
    {
        $this->data['item_id'] = $item_id;
        $this->data['itemData'] = $this->item->getItem($item_id);
        $this->data['maxRevNo'] = $this->controlPlanV2->getMaxFmeaRevNo($item_id);
        $this->load->view($this->cpForm, $this->data);
    }

    public function saveCPMaster()
    {
        $data = $this->input->post();
        $data['created_by'] = $this->session->userdata('loginId');
        $this->printJson($this->controlPlanV2->saveCPMaster($data));
    }

    public function cpDiamentionList($id)
    {
        $this->data['tableHeader'] = getQualityDtHeader("cpDiamention");
        $this->data['cp_id'] = $id;
        $this->data['cpData'] = $this->controlPlanV2->getControlPlan($id);
        $this->load->view($this->cp_diamention_list, $this->data);
    }

    public function getCPDiamentionDTRows($cp_id)
    {
        $data = $this->input->post();
        $data['cp_id'] = $cp_id;
        $result = $this->controlPlanV2->getCPDiamentionDTRows($data);
        $sendData = array();
        $i = 1;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getCPDiamentionData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addControlMethod()
    {
        $id = $this->input->post('id');
        $this->data['ref_id'] = $id;
        $this->data['qcFmeaData'] = $this->controlPlanV2->getCPTrans($id);
        $this->data['controlMethod'] = $this->controlMethod->getControlMethodList();
        $this->data['tbody'] = $this->getControlMethodData($id)['html'];
        $this->load->view($this->control_plan_method, $this->data);
    }

    public function saveControlMethod()
    {
        $data = $this->input->post();
        $errorMessage = array();
        if (empty($data['potential_effect']) && empty($data['instrument_code']) )
            $errorMessage['instrument_code'] = "Measurement Technique is required.";
        if (empty($data['process_prevention']))
            $errorMessage['process_prevention'] = "Control Method is required.";

        if (empty($data['process_detection']))
            $errorMessage['process_detection'] = "Responsibility required.";

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $data['created_by'] = $this->session->userdata('loginId');
            $result = $this->controlPlanV2->saveControlMethod($data);
            $this->printJson($this->getControlMethodData($data['ref_id']));

        endif;
    }

    public function getControlMethodData($ref_id)
    {
        $result = $this->controlPlanV2->getControlMethodData($ref_id, 3);
        $html = '';
        if (!empty($result)) {
            $i = 1;
            foreach ($result as $row) {
                if(!empty($row->instrument_code)){
                    $ins = explode(",",$row->instrument_code);
                   
                        $ins1 =  explode("-",$ins[0]);
                        $catData1 = $this->instrument->getDataForGenerateCode(['category_code'=>(int)$ins1[0],'item_code'=>(int)$ins1[1]]);
                        $instrumentData1  = $catData1->category_name.'('.$ins[0].') Range ('.(($catData1->item_type == 6)?$catData1->instrument_range:$catData1->size).') '.(!empty($catData1->least_count)?'LC - '.$catData1->least_count:'');
                        $instrumentData2='';$specialChar='';
                        if(!empty($ins[1])){
                            $ins2 =  explode("-",$ins[1]);
                            $catData2 = $this->instrument->getDataForGenerateCode(['category_code'=>(int)$ins2[0],'item_code'=>(int)$ins2[1]]);
                            $instrumentData2  = $catData2->category_name.'('.$ins[1].') Range ('.(($catData2->item_type == 6)?$catData2->instrument_range:$catData2->size).') '.(!empty($catData2->least_count)?'LC - '.$catData2->least_count:'');
    
                            $specialChar = ($row->detec == 1)?' & ':' / ';
                        }
                        
                        $row->category_name = $instrumentData1.$specialChar.$instrumentData2;
                   
                }else{
                    $row->category_name =  $row->potential_effect;
                }

                $editBtn ='<a class="btn btn-outline-success btn-edit permission-modify mr-2" href="javascript:void(0)" datatip="Edit" flow="down" onclick="editControlMethod('.$row->id.',this);"><i class="ti-pencil-alt" ></i></a>';
                $deleteBtn ='<a class="btn btn-outline-danger btn-edit permission-remove" href="javascript:void(0)" datatip="Delete" flow="down" onclick="deleteControlMethod('.$row->id.','.$row->ref_id.');"><i class="ti-trash" ></i></a>';

                $html .= '<tr>
                    <td>' . $i++ . '</td>
                    <td>' . $row->category_name . '</td>
                    <td>' . $row->process_prevention . '</td>
                    <td>' . $row->process_detection . '</td>
                    <td>' . $row->sev . '</td>
                    <td>' . $row->potential_cause . '</td>
                    <td>'.$editBtn.$deleteBtn.'</td>
                </tr>';
            }
        }
        return ['status' => 1, 'html' => $html];
    }

    public function fatchDimensionForCP()
    {
        $id = $this->input->post('id');
        $this->printJson($this->controlPlanV2->fatchDimensionForCP($id));
    }

    public function addCPDiamention($cp_id)
    {
        $this->data['cp_id'] = $cp_id;
        $this->data['cpData'] = $this->controlPlanV2->getControlPlan($cp_id);
        $this->load->view($this->cp_dimenstion_form, $this->data);
    }

    
    public function saveCPDimension()
    {
        $data = $this->input->post();
        $errorMessage = array();
       
        if (empty($data['trans_main_id']))
            $errorMessage['trans_main_id'] = "Process No. is required.";

        if (!isset($data['product_param'])) {
            $errorMessage['general_error'] = "parameter is required ";
        } else {

            $i = 1;
            foreach ($data['product_param'] as $key => $value) {
                if (empty($value) && empty($data['process_param'][$key])) {
                    $errorMessage['parameter' . $i] = "parameter is required";
                }
                if (empty($data['other_req'][$key])) {
                    $errorMessage['requirement' . $i] = "requirement is required";
                }
                $i++;
            }
        }
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            
            // print_r($data);exit;
            $this->printJson($this->controlPlanV2->saveCPDimension($data));

        endif;
    }

    public function activeCPDiamention()
    {
        $data = $this->input->post();
        $this->printJson($this->controlPlanV2->activeCPDiamention($data));
    }


    public function editControlMethod(){
        $id = $this->input->post('id');
        $causeData = $this->controlPlanV2->getQCFmeaFailData($id);
        
        $this->printJson($causeData);
    }

    public function deleteControlMethod(){
        $data = $this->input->post();
        if (empty($data['id'])) :
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else :
            $result = $this->controlPlanV2->deleteFmeaQc($data['id']);
            $this->printJson($this->getControlMethodData($data['ref_id']));
        endif;
    }

    public function editCPProcessDiamention($cp_id)
    {
        $this->data['cp_id'] = $cp_id;
        $this->data['cpData'] = $this->controlPlanV2->getControlPlan($cp_id);
        $this->data['transData']=$this->controlPlanV2->getCPTransData($cp_id,2);
        $this->data['editMode']=1;
        $this->load->view($this->cp_dimenstion_form, $this->data);
    }
    /**************************************/
    /*************Excel****************/
    public function createExcelPFC($item_id)
    {
        $itemData = $this->item->getItem($item_id);
        $processCode = $this->controlPlanV2->getProcessCode();
        $processCodeArray = !empty($processCode)?array_column($processCode,'process_code'):[];
        // print_r("'".'"'.implode(',', $processCodeArray).'"'."'");exit;
        $table_column = array('Process_No','Process_Code', 'Machine_Tool', 'Parameter', 'Symbol_1', 'Char_Class', 'Output_Operation', 'Location', 'Stage_Type');
        $spreadsheet = new Spreadsheet();
        $inspSheet = $spreadsheet->getActiveSheet();
        $inspSheet = $inspSheet->setTitle('PFC');
        
        $inspSheet->setCellValue('A' . 1, 'Product Phase');
        $inspSheet->setCellValue('C' . 1, 'Engg. Change Level');
        $objValidation2 = $inspSheet->getCell('B' . 1)->getDataValidation();
        $objValidation2->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
        $objValidation2->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
        $objValidation2->setAllowBlank(false);
        $objValidation2->setShowInputMessage(true);
        $objValidation2->setShowDropDown(true);
        $objValidation2->setPromptTitle('Pick from list');
        $objValidation2->setPrompt('Please pick a value from the drop-down list.');
        $objValidation2->setErrorTitle('Input error');
        $objValidation2->setError('Value is not in list');
        $objValidation2->setFormula1('"PROTOTYPE,PRE LAUNCH,PRODUCTION"');
        $objValidation2->setShowDropDown(true);
        $xlCol = 'A';$rows = 2;
        foreach ($table_column as $tCols) {
            $inspSheet->setCellValue($xlCol . $rows, $tCols);
            $xlCol++;
        }

        for ($i = 3; $i <= 100; $i++) {

            /*** Process Code Drop down */
            $objValidation2 = $inspSheet->getCell('B' . $i)->getDataValidation();
            $objValidation2->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $objValidation2->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
            $objValidation2->setAllowBlank(false);
            $objValidation2->setShowInputMessage(true);
            $objValidation2->setShowDropDown(true);
            $objValidation2->setPromptTitle('Pick from list');
            $objValidation2->setPrompt('Please pick a value from the drop-down list.');
            $objValidation2->setErrorTitle('Input error');
            $objValidation2->setError('Value is not in list');
            // $objValidation2->setFormula1('"MPIN,RMDI,RMMT,CUTT,TRAB,CLEN,DBRG,IINP,CNCT,VMCM,HMCM,MARK,FINP,PDIN,OIPA,ASSY,THRD,HONN"');
            $objValidation2->setFormula1('"'.implode(',', $processCodeArray).'"');

            $objValidation2->setShowDropDown(true);

            /*** Sysmbol Drop Down */
            $objValidation2 = $inspSheet->getCell('E' . $i)->getDataValidation();
            $objValidation2->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $objValidation2->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
            $objValidation2->setAllowBlank(false);
            $objValidation2->setShowInputMessage(true);
            $objValidation2->setShowDropDown(true);
            $objValidation2->setPromptTitle('Pick from list');
            $objValidation2->setPrompt('Please pick a value from the drop-down list.');
            $objValidation2->setErrorTitle('Input error');
            $objValidation2->setError('Value is not in list');
            $objValidation2->setFormula1('"operation,oper_insp,inspection,storage,delay,decision,transport,mat_in,mat_out,connector"');
            $objValidation2->setShowDropDown(true);


            /*** Char Class Drop Down */
            $objValidation2 = $inspSheet->getCell('F' . $i)->getDataValidation();
            $objValidation2->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $objValidation2->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
            $objValidation2->setAllowBlank(false);
            $objValidation2->setShowInputMessage(true);
            $objValidation2->setShowDropDown(true);
            $objValidation2->setPromptTitle('Pick from list');
            $objValidation2->setPrompt('Please pick a value from the drop-down list.');
            $objValidation2->setErrorTitle('Input error');
            $objValidation2->setError('Value is not in list');
            $objValidation2->setFormula1('"critical,major,minor,pc"');
            $objValidation2->setShowDropDown(true);

            /*** Location Drop Down */
            $objValidation2 = $inspSheet->getCell('H' . $i)->getDataValidation();
            $objValidation2->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $objValidation2->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
            $objValidation2->setAllowBlank(false);
            $objValidation2->setShowInputMessage(true);
            $objValidation2->setShowDropDown(true);
            $objValidation2->setPromptTitle('Pick from list');
            $objValidation2->setPrompt('Please pick a value from the drop-down list.');
            $objValidation2->setErrorTitle('Input error');
            $objValidation2->setError('Value is not in list');
            $objValidation2->setFormula1('"inhouse,outsource"');
            $objValidation2->setShowDropDown(true);

            /*** Stage Type Drop Down */
            $objValidation2 = $inspSheet->getCell('I' . $i)->getDataValidation();
            $objValidation2->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $objValidation2->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
            $objValidation2->setAllowBlank(false);
            $objValidation2->setShowInputMessage(true);
            $objValidation2->setShowDropDown(true);
            $objValidation2->setPromptTitle('Pick from list');
            $objValidation2->setPrompt('Please pick a value from the drop-down list.');
            $objValidation2->setErrorTitle('Input error');
            $objValidation2->setError('Value is not in list');
            $objValidation2->setFormula1('"IIR,Production,FIR,PDI,Packing,Dispatch,RQC"');
            $objValidation2->setShowDropDown(true);
        }

        

        
        $fileDirectory = realpath(APPPATH . '../assets/uploads/pfc_excel');
        $fileName = '/pfc_' . $itemData->item_code . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        $writer->save($fileDirectory . $fileName);
        header("Content-Type: application/vnd.ms-excel");
        redirect(base_url('assets/uploads/pfc_excel') . $fileName);
    }

    public function importExcelPFC()
    {
        $data = $this->input->post();
        if (empty($data['rev_no'])) :
            $this->printJson(['status' => 0, 'message' => ['rev_no'=>'Rev No. is required']]);
        else :
            $postData = $this->input->post();
            $pfc_excel = '';
            if (isset($_FILES['pfc_excel']['name']) || !empty($_FILES['pfc_excel']['name'])) :
                $this->load->library('upload');
                $_FILES['userfile']['name']     = $_FILES['pfc_excel']['name'];
                $_FILES['userfile']['type']     = $_FILES['pfc_excel']['type'];
                $_FILES['userfile']['tmp_name'] = $_FILES['pfc_excel']['tmp_name'];
                $_FILES['userfile']['error']    = $_FILES['pfc_excel']['error'];
                $_FILES['userfile']['size']     = $_FILES['pfc_excel']['size'];

                $imagePath = realpath(APPPATH . '../assets/uploads/pfc_excel');
                $config = ['file_name' => date("Y_m_d_H_i_s") . "pfc_upload" . $_FILES['userfile']['name'], 'allowed_types' => '*', 'max_size' => 10240, 'overwrite' => FALSE, 'upload_path' => $imagePath];

                $this->upload->initialize($config);
                if (!$this->upload->do_upload()) :
                    $errorMessage['pfc_excel'] = $this->upload->display_errors();
                    $this->printJson(["status" => 0, "message" => $errorMessage]);
                else :
                    $uploadData = $this->upload->data();
                    $pfc_excel = $uploadData['file_name'];
                endif;
                if (!empty($pfc_excel)) {
                    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($imagePath . '/' . $pfc_excel);
                    $fileData = array($spreadsheet->getSheetByName('PFC')->toArray(null, true, true, true));

                

                    // Insert the sheet content
                    $fieldArray = array();
                    $row = 0;
                    if (!empty($fileData)) {
                        $itmData = $this->item->getItem($postData['item_id']);
                        $fieldArray = $fileData[0][2];$revArray = $fileData[0][1];
                        for ($i = 3; $i <= count($fileData[0]); $i++) {
                            $rowData = array();
                            $c = 'A';
                            foreach ($fileData[0][$i] as $key => $colData) :
                                if (!empty($colData)) :
                                    $rowData[strtolower($fieldArray[$c])] = $colData;
                                endif;
                                $c++;
                            endforeach;
                            $rowData['id'] = '';
                            $rowData['item_id'] = $postData['item_id'];
                            if (empty($rowData['location']) || $rowData['location'] == 'inhouse') {
                                $rowData['location'] = 1;
                            } else {
                                $rowData['location'] = 2;
                            }
                            
                            if (empty($rowData['process_no']) || empty($rowData['stage_type'])) {
                                $errorMsg = empty($rowData['process_no'])?'Process No. Not Found..!':'';
                                $errorMsg.=empty($rowData['stage_type'])?'stage type is reqired...':'';
                                $this->printJson(['status' => 2, 'message' => $errorMsg.'! Line No: ' . $row]);
                            }


                            $stageArray = ['IIR' => 1, 'Production' => 2, 'FIR' => 3, 'PDI' => 3, 'Packing' => 5, 'Dispatch' => 6, 'RQC' => 7];


                            $row++;
                            /*$reactionPlan = "";
                            if(!empty($rowData['process_code'])){
                                $reactionData = $this->reactionPlan->getPlanTransData(['type'=>1,'title'=>$rowData['process_code']]);
                                if(!empty( $reactionData)){
                                    $reactionPlan = implode(", ",array_column($reactionData,'description'));
                                }
                            }*/
                            $transData['id'][] = '';
                            $transData['process_no'][] = (!empty($rowData['process_no']) ? $rowData['process_no'] : '');
                            $transData['process_code'][] = (!empty($rowData['process_code']) ? $rowData['process_code'] : '');
                            $transData['product_param'][] = !empty($rowData['parameter']) ? $rowData['parameter'] : '';
                            $transData['machine_tool'][] = !empty($rowData['machine_tool']) ? $rowData['machine_tool'] : '';;
                            $transData['symbol_1'][] = !empty($rowData['symbol_1']) ? $rowData['symbol_1'] : '';
                            $transData['char_class'][] = !empty($rowData['char_class']) ? $rowData['char_class'] : '';
                            $transData['output_operation'][] = !empty($rowData['output_operation']) ? $rowData['output_operation'] : '';
                            $transData['location'][] = !empty($rowData['location']) ? $rowData['location'] : '';
                            $transData['stage_type'][] = !empty($rowData['stage_type']) ? $stageArray[$rowData['stage_type']] : 0;
                            // $transData['reaction_plan'][] = $reactionPlan;
                        }

                        $itemData = $this->item->getItem($postData['item_id']);
                        $pfc_number = 'PFC/' . $itemData->item_code;
                        $product_phase = "";
                        if(!empty($revArray['B'])){
                            if($revArray['B'] == 'PROTOTYPE'){
                                $product_phase = 1;
                            }elseif($revArray['B'] == 'PRE LAUNCH'){
                                $product_phase = 2;
                            }elseif($revArray['B'] == 'PRODUCTION'){
                                $product_phase = 3;
                            }
                        }
                        $masterData = [
                            'id' => '',
                            'entry_type' => 1,
                            'trans_number' => $pfc_number,
                            'item_id' => $postData['item_id'],
                            'app_rev_no' =>$data['rev_no'],
                            'eng_change_level' => (!empty($revArray['D']))?$revArray['D']:'',
                            'product_phase' =>$product_phase,
                            'created_by' => $this->session->userdata('loginId')
                        ];
                        if(!empty($transData)){
                            $this->controlPlanV2->savePfc($masterData, $transData);
                            unlink($imagePath . '/' . $pfc_excel);
                            $this->printJson(['status' => 1, 'message' => $row . ' Record updated successfully.']);
                        }else {
                            $this->printJson(['status' => 2, 'message' => 'Data not found...!']);
                        }
                    
                    }
                } else {
                    $this->printJson(['status' => 2, 'message' => 'Data not found...!']);
                }



            else :
                $this->printJson(['status' => 2, 'message' => 'Please Select File!']);
            endif; 
        endif;
    }

    public function createExcelFmea($pfc_id)
    {
        $pfcData = $this->controlPlanV2->getPfcTransData($pfc_id);
        // print_r($pfcData);
        $itemData = $this->item->getItem($pfcData[0]->item_id);
        $spreadsheet = new Spreadsheet();
        if (!empty($pfcData)) {
            
            $inspSheet = $spreadsheet->getActiveSheet();
            $sheet = 0;
            foreach ($pfcData as $pfc) {
                $table_column = array('Sr. No.', 'Product', 'Process', 'Char_Class', 'type', 'specification','IIR','OPR','IPR','SAR','SPC','FIR');
                if ($sheet > 0) {
                    $inspSheet = $spreadsheet->createSheet();
                }

                $styleArray = array(
                    'borders' => array(
                        'allBorders' => array(
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            // 'color' => array('argb' => 'FFFF0000'),
                        ),
                    ),
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                );
                $inspSheet = $inspSheet->setTitle($pfc->process_no);
                $xlCol = 'A';
                $rows = 1;
                $inspSheet->mergeCells('A1:A2');
                $inspSheet->mergeCells('B1:B2');
                $inspSheet->mergeCells('C1:C2');
                $inspSheet->mergeCells('D1:D2');
                $inspSheet->mergeCells('E1:E2');
                $inspSheet->mergeCells('F1:F2');
                $inspSheet->mergeCells('G1:k1');
                $inspSheet->mergeCells('L1:P1');
                $inspSheet->mergeCells('Q1:U1');
                $inspSheet->mergeCells('V1:Z1');
                $inspSheet->mergeCells('AA1:AE1');
                $inspSheet->mergeCells('AF1:AJ1');
                $colCount = 0;

                $meargeCol='A';
                foreach ($table_column as $tCols) {
                    $inspSheet->setCellValue($xlCol . $rows, $tCols);
                    if($colCount ==9 ){
                        $xlCol ='AA';
                    }elseif($colCount ==10 ){
                        $xlCol ='AF';
                    }elseif($colCount >=6 && $colCount < 9){
                        $xlCol++;$xlCol++;$xlCol++;$xlCol++;$xlCol++;
                    }else{
                        $xlCol++;
                    }
                    $colCount++;
                }
                $table_column2 = array('Measurement_Technique', 'Size', 'Freq', 'Time','frq_text');

                $row2 = 2;
                $xlCol = 'G';
                for($c=1;$c<=6;$c++){
                    foreach ($table_column2 as $tCols) {
                    
                        $inspSheet->setCellValue($xlCol . $row2, $tCols);
                        $xlCol++;
                    }
                }


                for ($i = 3; $i <= 4; $i++) {
                    $objValidation2 = $inspSheet->getCell('D'.$i)->getDataValidation();
                    $objValidation2->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                    $objValidation2->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
                    $objValidation2->setAllowBlank(false);
                    $objValidation2->setShowInputMessage(true);
                    $objValidation2->setShowDropDown(true);
                    $objValidation2->setPromptTitle('Pick from list');
                    $objValidation2->setPrompt('Please pick a value from the drop-down list.');
                    $objValidation2->setErrorTitle('Input error');
                    $objValidation2->setError('Value is not in list');
                    $objValidation2->setFormula1('"critical,major,minor,pc"');
                    $objValidation2->setShowDropDown(true);

                    $objValidation2 = $inspSheet->getCell('E'.$i)->getDataValidation();
                    $objValidation2->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                    $objValidation2->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
                    $objValidation2->setAllowBlank(false);
                    $objValidation2->setShowInputMessage(true);
                    $objValidation2->setShowDropDown(true);
                    $objValidation2->setPromptTitle('Pick from list');
                    $objValidation2->setPrompt('Please pick a value from the drop-down list.');
                    $objValidation2->setErrorTitle('Input error');
                    $objValidation2->setError('Value is not in list');
                    $objValidation2->setFormula1('"Range,Minimum,Maximum,Other"');
                    $objValidation2->setShowDropDown(true);
                    
                    $objValidation2 = $inspSheet->getCell('J'.$i)->getDataValidation();
                    $objValidation2->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                    $objValidation2->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
                    $objValidation2->setAllowBlank(false);
                    $objValidation2->setShowInputMessage(true);
                    $objValidation2->setShowDropDown(true);
                    $objValidation2->setPromptTitle('Pick from list');
                    $objValidation2->setPrompt('Please pick a value from the drop-down list.');
                    $objValidation2->setErrorTitle('Input error');
                    $objValidation2->setError('Value is not in list');
                    $objValidation2->setFormula1('"Hrs,%,Lot,Setup"');
                    $objValidation2->setShowDropDown(true);

                    $objValidation2 = $inspSheet->getCell('O'.$i)->getDataValidation();
                    $objValidation2->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                    $objValidation2->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
                    $objValidation2->setAllowBlank(false);
                    $objValidation2->setShowInputMessage(true);
                    $objValidation2->setShowDropDown(true);
                    $objValidation2->setPromptTitle('Pick from list');
                    $objValidation2->setPrompt('Please pick a value from the drop-down list.');
                    $objValidation2->setErrorTitle('Input error');
                    $objValidation2->setError('Value is not in list');
                    $objValidation2->setFormula1('"Hrs,%,Lot,Setup"');
                    $objValidation2->setShowDropDown(true);

                    $objValidation2 = $inspSheet->getCell('T'.$i)->getDataValidation();
                    $objValidation2->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                    $objValidation2->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
                    $objValidation2->setAllowBlank(false);
                    $objValidation2->setShowInputMessage(true);
                    $objValidation2->setShowDropDown(true);
                    $objValidation2->setPromptTitle('Pick from list');
                    $objValidation2->setPrompt('Please pick a value from the drop-down list.');
                    $objValidation2->setErrorTitle('Input error');
                    $objValidation2->setError('Value is not in list');
                    $objValidation2->setFormula1('"Hrs,%,Lot,Setup"');
                    $objValidation2->setShowDropDown(true);

                    $objValidation2 = $inspSheet->getCell('Y'.$i)->getDataValidation();
                    $objValidation2->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                    $objValidation2->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
                    $objValidation2->setAllowBlank(false);
                    $objValidation2->setShowInputMessage(true);
                    $objValidation2->setShowDropDown(true);
                    $objValidation2->setPromptTitle('Pick from list');
                    $objValidation2->setPrompt('Please pick a value from the drop-down list.');
                    $objValidation2->setErrorTitle('Input error');
                    $objValidation2->setError('Value is not in list');
                    $objValidation2->setFormula1('"Hrs,%,Lot,Setup"');
                    $objValidation2->setShowDropDown(true);
                    
                    $objValidation2 = $inspSheet->getCell('AD'.$i)->getDataValidation();
                    $objValidation2->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                    $objValidation2->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
                    $objValidation2->setAllowBlank(false);
                    $objValidation2->setShowInputMessage(true);
                    $objValidation2->setShowDropDown(true);
                    $objValidation2->setPromptTitle('Pick from list');
                    $objValidation2->setPrompt('Please pick a value from the drop-down list.');
                    $objValidation2->setErrorTitle('Input error');
                    $objValidation2->setError('Value is not in list');
                    $objValidation2->setFormula1('"Hrs,%,Lot,Setup"');
                    $objValidation2->setShowDropDown(true);

                    $objValidation2 = $inspSheet->getCell('AI'.$i)->getDataValidation();
                    $objValidation2->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                    $objValidation2->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
                    $objValidation2->setAllowBlank(false);
                    $objValidation2->setShowInputMessage(true);
                    $objValidation2->setShowDropDown(true);
                    $objValidation2->setPromptTitle('Pick from list');
                    $objValidation2->setPrompt('Please pick a value from the drop-down list.');
                    $objValidation2->setErrorTitle('Input error');
                    $objValidation2->setError('Value is not in list');
                    $objValidation2->setFormula1('"Hrs,%,Lot,Setup"');
                    $objValidation2->setShowDropDown(true);
                    

                    $inspSheet ->getStyle('A1:AJ15')->applyFromArray($styleArray); 
                    $inspSheet->getStyle('A1:AJ15')->getAlignment()->setWrapText(true);
                }
                $sheet++;
            }
        }
       
        
        $fileDirectory = realpath(APPPATH . '../assets/uploads/fmea_excel');
        $fileName = '/CP_' . $itemData->item_code . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        $writer->save($fileDirectory . $fileName);
        header("Content-Type: application/vnd.ms-excel");
        redirect(base_url('assets/uploads/fmea_excel') . $fileName);
    }

    public function importExcelCP()
    {
        $postData = $this->input->post();
        $itemData = $this->item->getItem($postData['item_id']);
        // print_r($itemData);exit;
        $fmea_excel = '';
        if (isset($_FILES['fmea_excel']['name']) || !empty($_FILES['fmea_excel']['name'])) :
            $this->load->library('upload');
            $_FILES['userfile']['name']     = $_FILES['fmea_excel']['name'];
            $_FILES['userfile']['type']     = $_FILES['fmea_excel']['type'];
            $_FILES['userfile']['tmp_name'] = $_FILES['fmea_excel']['tmp_name'];
            $_FILES['userfile']['error']    = $_FILES['fmea_excel']['error'];
            $_FILES['userfile']['size']     = $_FILES['fmea_excel']['size'];

            $imagePath = realpath(APPPATH . '../assets/uploads/fmea_excel');
            $config = ['file_name' => date("Y_m_d_H_i_s") . "upload" . $_FILES['userfile']['name'], 'allowed_types' => '*', 'max_size' => 10240000, 'overwrite' => FALSE, 'upload_path' => $imagePath];

            $this->upload->initialize($config);
            if (!$this->upload->do_upload()) :
                $errorMessage['fmea_excel'] = $this->upload->display_errors();
                $this->printJson(["status" => 0, "message" => $errorMessage]);
            else :
                $uploadData = $this->upload->data();
                $fmea_excel = $uploadData['file_name'];
            endif;
            if (!empty($fmea_excel)) {
                // $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($imagePath . '/' . $fmea_excel);
                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
                $reader->setReadDataOnly(true);
                $spreadsheet = $reader->load($imagePath . '/' . $fmea_excel);
                $pfcData = $this->controlPlanV2->getPfcTransData($postData['pfc_id']);
                $row = 0;
                foreach ($pfcData as $pfc) {
                    $cpDimentionDta = $this->controlPlanV2->getCPDimenstion(['pfc_id'=>$pfc->id,'item_id'=>$pfc->item_id]);
                    if(empty($cpDimentionDta)){
                        $file = $spreadsheet->getSheetByName($pfc->process_no); $fileData=array();
                        if(!empty($file)){
                            $fileData = array($file->toArray(null, true, true, true));
                        }
                        // Insert the sheet content
                        $fieldArray = array();
                        $r = 2;
                        $transArray = array();
                        if (!empty($fileData)) {
                            $fieldArray = $fileData[0][2];
                            for ($i = 3; $i <= count($fileData[0]); $i++) {
                                $rowData = array();
                                $c = 'A';$firstRow='';
                                foreach ($fileData[0][2] as $key => $col) :
                                    $colData = $fileData[0][$i][$key];
                                    // if (!empty($colData)) :
                                      if(!empty(strtolower($fieldArray[$c]))){
                                       if(!empty(strtolower($fileData[0][1][$c]))){
                                           $firstRow = strtolower($fileData[0][1][$c]);
                                       }
                                       $rowData[$firstRow.'_'.strtolower($fieldArray[$c])] = !empty($colData)?$colData:'';
                                      }else{
                                       $rowData[strtolower($fileData[0][1][$c])] = !empty($colData)?$colData:'';
                                      }
                                    // endif;
                                    $c++;
                                endforeach;
                                if (!empty($rowData)) {
                                    if (empty($rowData['product']) && empty($rowData['process'])) {                                        
                                        $this->printJson(['status' => 2, 'message' => 'Parameter Not Found..! Line No: ' . $r.' And Process No'.$pfc->process_no]);
                                    }
                                    $rowData['id'] = '';
                                    
                                    switch ($rowData['type']) {
                                        case 'Range':
                                            if(strpos($rowData['specification'],'/') != false){
                                                $exp = explode('/',$rowData['specification']);
                                                $rowData['min'] =(!empty($exp[0]))? TO_FLOAT($exp[0]):'';
                                                $rowData['max'] = !empty($exp[1])?TO_FLOAT($exp[1]):'';
                                            }elseif(strpos($rowData['specification'],'±') != false){
                                                $exp = explode('±',$rowData['specification']);
                                                $rowData['min'] =(!empty($exp[0]))? (TO_FLOAT($exp[0])-TO_FLOAT($exp[1])):'';
                                                $rowData['max'] = !empty($exp[1])?(TO_FLOAT($exp[0])+TO_FLOAT($exp[1])):'';
                                            }
                                            
                                            $rowData['type'] = 1;
                                            $rowData['other'] = $rowData['specification'];
                                            break;
                                        case 'Minimum':
                                            $rowData['type'] = 2;
                                            $rowData['min'] = TO_FLOAT($rowData['specification']);
                                            $rowData['max'] = '';
                                            $rowData['other'] = $rowData['specification'];
                                            break;
                                        case 'Maximum':
                                            $rowData['type'] = 3;
                                            $rowData['max'] =TO_FLOAT($rowData['specification']);
                                            $rowData['min'] = '';
                                            $rowData['other'] = $rowData['specification'];
                                            break;
                                        case 'Other':
                                            $rowData['type'] = 4;
                                            $rowData['min'] = $rowData['max'] = '';
                                            $rowData['other'] = $rowData['specification'];
                                            break;
                                        default:
                                            $rowData['type'] = 4;
                                            $rowData['min'] = $rowData['max'] = '';
                                            $rowData['other'] = $rowData['specification'];
                                            break;
                                    }

                                    $transData = [
                                        'id' => '',
                                        'pfc_id' => $pfc->id,
                                        'item_id' => $pfc->item_id,
                                        'product_param' => (!empty($rowData['product'])) ? $rowData['product'] : '',
                                        'process_param' => (!empty($rowData['process']) ? $rowData['process'] : ''),
                                        'requirement' => !empty($rowData['type']) ? $rowData['type'] : '',
                                        'min_req' => !empty($rowData['min']) ? $rowData['min'] : '',
                                        'max_req' => !empty($rowData['max']) ? $rowData['max'] : '',
                                        'other_req' => !empty($rowData['other']) ? $rowData['other'] : '',
                                        'char_class' => !empty($rowData['char_class']) ? $rowData['char_class'] : '',
                                        'pfc_id' =>$pfc->id,
                                        'process_no' => $pfc->process_no,


                                        'iir_measur_tech' => !empty($rowData['iir_measurement_technique']) ? $rowData['iir_measurement_technique'] : '',
                                        'iir_size' => !empty($rowData['iir_size']) ? $rowData['iir_size'] : '',
                                        'iir_freq' => !empty($rowData['iir_freq']) ? $rowData['iir_freq'] : '',
                                        'iir_freq_time' => !empty($rowData['iir_time']) ? $rowData['iir_time'] : '',
                                        'iir_freq_text' => !empty($rowData['iir_frq_text']) ? $rowData['iir_frq_text'] : '',

                                        'opr_measur_tech' => !empty($rowData['opr_measurement_technique']) ? $rowData['opr_measurement_technique'] : '',
                                        'opr_size' => !empty($rowData['opr_size']) ? $rowData['opr_size'] : '',
                                        'opr_freq' => !empty($rowData['opr_freq']) ? $rowData['opr_freq'] : '',
                                        'opr_freq_time' => !empty($rowData['opr_time']) ? $rowData['opr_time'] : '',
                                        'opr_freq_text' => !empty($rowData['opr_frq_text']) ? $rowData['opr_frq_text'] : '',

                                        'ipr_measur_tech' => !empty($rowData['ipr_measurement_technique']) ? $rowData['ipr_measurement_technique'] : '',
                                        'ipr_size' => !empty($rowData['ipr_size']) ? $rowData['ipr_size'] : '',
                                        'ipr_freq' => !empty($rowData['ipr_freq']) ? $rowData['ipr_freq'] : '',
                                        'ipr_freq_time' => !empty($rowData['ipr_time']) ? $rowData['ipr_time'] : '',
                                        'ipr_freq_text' => !empty($rowData['ipr_frq_text']) ? $rowData['ipr_frq_text'] : '',

                                        'sar_measur_tech' => !empty($rowData['sar_measurement_technique']) ? $rowData['sar_measurement_technique'] : '',
                                        'sar_size' => !empty($rowData['sar_size']) ? $rowData['sar_size'] : '',
                                        'sar_freq' => !empty($rowData['sar_freq']) ? $rowData['sar_freq'] : '',
                                        'sar_freq_time' => !empty($rowData['sar_time']) ? $rowData['sar_time'] : '',
                                        'sar_freq_text' => !empty($rowData['sar_frq_text']) ? $rowData['sar_frq_text'] : '',

                                        'spc_measur_tech' => !empty($rowData['spc_measurement_technique']) ? $rowData['spc_measurement_technique'] : '',
                                        'spc_size' => !empty($rowData['spc_size']) ? $rowData['spc_size'] : '',
                                        'spc_freq' => !empty($rowData['spc_freq']) ? $rowData['spc_freq'] : '',
                                        'spc_freq_time' => !empty($rowData['spc_time']) ? $rowData['spc_time'] : '',
                                        'spc_freq_text' => !empty($rowData['spc_frq_text']) ? $rowData['spc_frq_text'] : '',

                                        'fir_measur_tech' => !empty($rowData['fir_measurement_technique']) ? $rowData['fir_measurement_technique'] : '',
                                        'fir_size' => !empty($rowData['fir_size']) ? $rowData['fir_size'] : '',
                                        'fir_freq' => !empty($rowData['fir_freq']) ? $rowData['fir_freq'] : '',
                                        'fir_freq_time' => !empty($rowData['fir_time']) ? $rowData['fir_time'] : '',
                                        'fir_freq_text' => !empty($rowData['fir_frq_text']) ? $rowData['fir_frq_text'] : '',

                                        'created_by' => $this->session->userdata('loginId')
                                    ];
                                    
                                    $transArray[] =  $transData;
                                }
                                $r++;
                            }
                        }
                        if (!empty($transArray)) {
                            $masterData = [
                                'id' => '',
                                'trans_number' => $itemData->item_code  . '/' . $pfc->process_code,
                                // 'cust_rev_no' => $itemData->rev_no,
                                // 'app_rev_no' => $pfc->rev_no,
                                'item_id' => $postData['item_id'],
                                'ref_id' => $pfc->id,
                                'pfc_main_id'=>$postData['pfc_id']
                            ];

                            $masterData['dimensionData'] = $transArray;
                            $postPFCData[] = $masterData;
                            $row++;
                        }
                    }
                }
               
                if (!empty($postPFCData)) {
                    $this->controlPlanV2->saveCPForExcel($postPFCData);
                    unlink($imagePath . '/' . $fmea_excel);
                    $this->printJson(['status' => 1, 'message' => $row . ' Operation inserted successfully.']);
                } else {
                    $this->printJson(['status' => 2, 'message' => 'Data not found...!']);
                }
            } else {
                $this->printJson(['status' => 2, 'message' => 'Data not found...!']);
            }
        else :
            $this->printJson(['status' => 2, 'message' => 'Please Select File!']);
        endif;
    }
    /*********************************************/
    /*******************PDF********************/
    public function pfc_pdf($id)
    {
        $this->data['pfcData'] = $this->controlPlanV2->getPfcData($id);
        $this->data['revData'] = $this->ecn->getEcnRevList(['item_id'=>$this->data['pfcData']->item_id]);
        $pfcTransData = $this->controlPlanV2->getPfcTransData($id);
        $this->data['companyData'] = $this->controlPlanV2->getCompanyInfo();
        if (!empty($this->data['pfcData']->core_team)) {
            $emp = $this->employee->getEmployees($this->data['pfcData']->core_team);
            $this->data['pfcData']->core_team = !empty($emp) ? implode(",", array_column($emp, 'emp_alias')) : '';
        }
        if (!empty($this->data['pfcData']->supplier_id)) {
            $supplier = explode(",",$this->data['pfcData']->supplier_id);$party = array();
            foreach($supplier as $row){
                $party[] = $this->party->getParty($row)->party_name;
            }
            $this->data['pfcData']->supplier_id=implode(",",$party);
        }
        $pfcTransDataArray = array();
        /*foreach ($pfcTransData as $row) {
            $product = $this->controlPlanV2->getActiveDimension(['pfc_id' => $row->id, 'parameter_type' => 1]);
            $prd_char=[];$prd_class=[];$prd_size=[];
            foreach($product as $prd){
                $diamention ='';
                if($prd->requirement==1){ $diamention = $prd->min_req.'/'.$prd->max_req ; }
                if($prd->requirement==2){ $diamention = $prd->min_req.' '.$prd->other_req ; }
                if($prd->requirement==3){ $diamention = $prd->max_req.' '.$prd->other_req ; }
                if($prd->requirement==4){ $diamention = $prd->other_req ; }
                $prod_char_class=''; if(!empty($prd->char_class)){ $prod_char_class='<img src="' . base_url('assets/images/symbols/'.$prd->char_class.'.png') . '" style="width:15px;display:inline-block;" />'; }
                
                $prd_char [] = $prd->parameter;
                $prd_size [] = $diamention;
                $prd_class [] = $prod_char_class;
                
            }
            $row->prod_char = (!empty($prd_char)) ? implode('<hr>', $prd_char) : '';
            $row->prod_dimension = (!empty($prd_size)) ? implode('<hr>', $prd_size) : '';
            $row->prod_char_class = (!empty($prd_class)) ? implode('<hr>', $prd_class) : '';

            $process = $this->controlPlanV2->getActiveDimension(['pfc_id' => $row->id, 'parameter_type' => 2]);
            $prs_char=[];$prs_class=[];$prs_size=[];
            foreach($process as $prs){
                
                $diamention ='';
                if($prs->requirement==1){ $diamention = $prs->min_req.'/'.$prs->max_req ; }
                if($prs->requirement==2){ $diamention = $prs->min_req.' '.$prs->other_req ; }
                if($prs->requirement==3){ $diamention = $prs->max_req.' '.$prs->other_req ; }
                if($prs->requirement==4){ $diamention = $prs->other_req ; }
                $prod_char_class=''; if(!empty($prs->char_class)){ $prod_char_class='<img src="' . base_url('assets/images/symbols/'.$prs->char_class.'.png') . '" style="width:15px;display:inline-block;" />'; }
                
                $prs_char [] = $prs->parameter;
                $prs_size [] = $diamention;
                $prs_class [] = $prod_char_class;
            }
            $row->process_char = (!empty($prs_char)) ? implode('<hr>', $prs_char) : '';
            $row->process_dimension = (!empty($prs_size)) ? implode('<hr>', $prs_size) : '';
            $row->process_char_class = (!empty($prs_class)) ? implode('<hr>', $prs_class) : '';            
            $pfcTransDataArray[] = $row;
        }*/
        $this->data['pfcTransData'] =$pfcTransData;// $pfcTransDataArray;
        $logo = base_url('assets/images/logo.png');
        $pdfData = $this->load->view('npd/control_plan/printPFC', $this->data, true);//print_r($pdfData);exit;
        $htmlHeader = '';
        $htmlFooter = '';
        $htmlHeader  = '<table class="table">
                    <tr>
                        <td><img src="' . $logo . '" style="max-height:40px;"></td>
                        <td class="org_title text-center" style="font-size:1.5rem;">PROCESS FLOW CHART REPORT</td>
                        <td class="text-right fs-15">F PD 02, (00/01.06.20)</td>
                    </tr>
                </table>';

        // $mpdf = $this->m_pdf->load();
        $mpdf = new \Mpdf\Mpdf();
        $pdfFileName = 'pfc' . $id . '.pdf';
        $stylesheet = file_get_contents(base_url('assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css'));
        $stylesheet = file_get_contents(base_url('assets/css/style.css?v=' . time()));
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->SetWatermarkImage($logo, 0.03, array(120, 45));
        $mpdf->showWatermarkImage = false;
        $mpdf->SetProtection(array('print'));

        $mpdf->SetHTMLHeader($htmlHeader);
        $mpdf->SetHTMLFooter($htmlFooter);
        $mpdf->AddPage('P', '', '', '', '', 5, 5, 20, 25, 5, 5, '', '', '', '', '', '', '', '', '', 'A4-P');

        $mpdf->WriteHTML($pdfData);
        $mpdf->Output($pdfFileName, 'I');
    }

    public function fmea_pdf($id)
    {
        $this->data['fmeaData'] = $this->controlPlanV2->getFmeaData($id);
        if (!empty($this->data['fmeaData']->coreTeam)) {
            $emp = $this->employee->getEmployees($this->data['fmeaData']->coreTeam);
            $this->data['fmeaData']->core_team = !empty($emp) ? implode(",", array_column($emp, 'emp_alias')) : '';
        }
        $this->data['companyData'] = $this->controlPlanV2->getCompanyInfo();
        $dimensionData =$this->controlPlanV2->getFmeaTransData($id);
        $transDataArray = array();
        foreach ($dimensionData as $row) {
            $failMode = $this->controlPlanV2->getQcFmeaTblData($row->id,1);
            $failModeArray=[];
            foreach($failMode as $fail){
                $fail->causeArray = $this->controlPlanV2->getQcFmeaTblData($fail->id,2);
                $failModeArray[]=$fail;
            }
            $row->failModeArray=$failModeArray;
            $transDataArray[]=$row;
        }
        $this->data['fmeaTrans'] = $transDataArray;
        // print_r($transDataArray);exit;
        $logo = base_url('assets/images/logo.png');
        $pdfData = $this->load->view('npd/control_plan/printFMEA', $this->data, true);
        // print_r($pdfData);exit;
        $htmlHeader = '';
        $htmlFooter = '';
        $htmlHeader  = '<table class="table">
                        <tr>
                            <td><img src="' . $logo . '" style="max-height:40px;"></td>
                            <td class="org_title text-center" style="font-size:1.5rem;">Failure Mode & Effective Analysis (Process FMEA)</td>
                            <td class="text-right fs-15">R-NPD-04 (00/01.10.17)</td>
                        </tr>
                    </table>';

        // $mpdf = $this->m_pdf->load();
        $mpdf = new \Mpdf\Mpdf();
        $pdfFileName = 'fmea' . $id . '.pdf';
        $stylesheet = file_get_contents(base_url('assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css'));
        $stylesheet = file_get_contents(base_url('assets/css/style.css?v=' . time()));
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->SetWatermarkImage($logo, 0.03, array(120, 45));
        $mpdf->showWatermarkImage = false;
        $mpdf->SetProtection(array('print'));

        $mpdf->SetHTMLHeader($htmlHeader);
        $mpdf->SetHTMLFooter($htmlFooter);
        $mpdf->AddPage('P', '', '', '', '', 5, 5, 20, 25, 5, 5, '', '', '', '', '', '', '', '', '', 'A4-L');

        $mpdf->WriteHTML($pdfData);
        $mpdf->Output($pdfFileName, 'I');
    }

    public function cp_pdf()
    {
        $data = $this->input->post();
        $id = $data['cp_id'];

        $this->data['cpData'] = $cpData = $this->controlPlanV2->getControlPlan($id); 
        $this->data['cpData']->app_rev_no = $data['rev_no'];
        $this->data['cpData']->app_rev_date = $data['rev_date'];
        $this->data['companyData'] = $this->controlPlanV2->getCompanyInfo();
        $this->data['cpTrans'] = $dimensionData =$this->controlPlanV2->getCPTransData($id,$data['rev_no']); 
        $this->data['revData'] = $this->ecn->getCpRevData(['rev_no'=>$data['rev_no'],'item_id'=>$cpData->item_id,'single_row'=>1]);
        $this->data['newRevData'] = $this->ecn->getEcnRevList(['item_id'=>$cpData->item_id,'ecn_type'=>1,'single_row'=>1]);
        // Reaction Plan
        $this->data['reactionPlan'] = $this->reactionPlan->getPlanTransData(['title'=>$cpData->process_code,'type'=>1]); 

         
        $logo = base_url('assets/images/logo.png');
        $pdfData = $this->load->view('npd/control_plan/printCP', $this->data, true);
        $htmlHeader = ''; $htmlFooter = '';
        $htmlHeader  = '<table class="table">
                        <tr>
                            <td style="width:15%"><img src="' . $logo . '" style="max-height:40px;"></td>
                            <td class="org_title text-center" style="font-size:1.5rem;">Control Plan</td>
                            <td style="width:15%"></td>
                        </tr>
                    </table>';

        $mpdf = new \Mpdf\Mpdf();
        $pdfFileName = 'pfc' . $id . '.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->SetWatermarkImage($logo, 0.03, array(120, 45));
        $mpdf->showWatermarkImage = false;
        $mpdf->SetProtection(array('print'));
        $mpdf->shrink_tables_to_fit=1;

        $mpdf->SetHTMLHeader($htmlHeader);
        $mpdf->SetHTMLFooter($htmlFooter);
        $mpdf->AddPage('P', '', '', '', '', 5, 5, 20, 5, 5, 5, '', '', '', '', '', '', '', '', '', 'A4-'.$data['layout']);
        
        if($data['pdf_type'] == 2){
            $pdfData = '<table><tr><td>'.$pdfData.'</td></tr></table>';
        }
        
        $mpdf->WriteHTML($pdfData);
        $mpdf->Output($pdfFileName, 'I');
    }


    public function deletePfc()
    {
        $data = $this->input->post();
        if (empty($data['id'])) :
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else :
            $result = $this->controlPlanV2->deletePfc($data['id']);
            $this->printJson($result);
        endif;
    }
    public function deleteFmea()
    {
        $data = $this->input->post();
        if (empty($data['id'])) :
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else :
            $result = $this->controlPlanV2->deleteFmea($data['id']);
            $this->printJson($result);
        endif;
    }
    public function deleteControlPlan()
    {
        $data = $this->input->post();
        if (empty($data['id'])) :
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else :
            $result = $this->controlPlanV2->deleteControlPlan($data['id']);
            $this->printJson($result);
        endif;
    }

    
    /**** PFC Revision */

    public function addPfcRev($item_id)
    {
        $this->data['item_id'] = $item_id;
        $this->data['rev_type'] = 1;
        $this->data['itemData'] = $this->item->getItem($item_id);
        $this->data['materialGrades']=$this->materialGrade->getMaterialGrades();
        $this->data['revNoList'] = $this->item->activeRevList(); /* 16-08-2023 */
        $this->data['revisionData'] = $this->revisionHtml(['item_id'=>$item_id,'rev_type'=>1]);
        $this->load->view($this->revision_form, $this->data);
    }
    public function saveRevision(){
        $data = $this->input->post();
        $errorMessage = array();
        if (empty($data['cust_rev_no'])) {
            $errorMessage['cust_rev_no'] = "Cust. Rev. NO is required";
        }
        if (empty($data['rev_no'])) {
            $errorMessage['rev_no'] = "Revision NO is required";
        }else{
            $revCount = $this->controlPlanV2->checkDuplicateRev(['rev_type'=>$data['rev_type'],'item_id'=>$data['item_id'],'rev_no'=>$data['rev_no']]);
            if($revCount > 0){
                $errorMessage['rev_no'] = "Duplicate Revision Found";
            }
        }
        
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $this->controlPlanV2->saveRevision($data);
			$this->printJson($this->revisionHtml($data));
        endif;
    }


    
    public function linkPfcRevision($item_id)
    {
        $transData = $this->controlPlanV2->getItemWisePfcData($item_id);
        $this->data['revList'] = $this->ecn->getEcnRevList(['item_id'=>$item_id]);
        $this->data['transData'] =  $transData; 
        $this->data['itemList'] = $this->item->getItemList(1);
        $this->data['empList'] = $this->employee->getEmpList();
        $this->data['reactionPlan'] = $this->reactionPlan->getTitleNames();
        $this->data['vendorList'] = $this->party->getVendorList();
        $this->data['supplierList'] = $this->party->getSupplierList();
        $this->data['processCodes'] = $this->controlPlanV2->getProcessCode();
        $this->data['pfcStage'] = $this->pfcStage;
        $this->load->view($this->pfc_rev_form, $this->data);
    }

    public function savePFCRev(){
        $data = $this->input->post();
        $errorMessage = array();
        
        $i=1;
        foreach($data['trans_id'] as $key=>$id){
            if (empty($data['rev_no'][$key])) {
                $errorMessage['rev_no'.$i] = "Revision No is required. ";
            }
            $i++;
        }
        if(isset($data['new_trans_id'])){
            foreach ($data['new_trans_id'] as $key => $id) {
                if (empty($data['process_no'][$key])) {
                    $errorMessage['general_error'] = "Peocess NO is required. ";
                }elseif (empty($data['parameter'][$key])) {
                    $errorMessage['general_error'] = "New Parameter is required";
                }elseif (empty($data['stage_type'][$key])) {
                    $errorMessage['general_error'] = "Stage Type is required";
                }
            }
        }
        
       
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $result = $this->controlPlanV2->savePFCRev($data);
            $result['url'] = base_url($this->data['headData']->controller.'/pfcList/'.$data['item_id']);
            $this->printJson($result);
        endif;
    }


    /** Control Plan Revesion */
    public function addCPRevision($item_id){
        $this->data['item_id'] = $item_id;
        $this->data['rev_type'] = 2;
        $this->data['itemData'] = $this->item->getItem($item_id);
        $this->data['materialGrades']=$this->materialGrade->getMaterialGrades();
        $this->data['revNoList'] = $this->item->activeRevList(); /* 16-08-2023 */
        $this->data['revisionData'] = $this->revisionHtml(['item_id'=>$item_id,'rev_type'=>2]);
        $this->load->view($this->revision_form, $this->data);
    }


    public function linkCpRevision($pfc_id)
    {
        $this->data['pfcData'] =$pfcData= $this->controlPlanV2->getPfcData($pfc_id);
        $this->data['dimensionList'] = $this->controlPlanV2->getCPDimenstion(['pfc_main_id'=>$pfc_id,'item_id'=>$pfcData->item_id]);
        $this->data['processNoList'] = $this->controlPlanV2->getPfcTransData($pfc_id);
        $this->data['revList'] = $this->ecn->getCpRevData(['item_id'=>$pfcData->item_id,'status'=>3]);
        $this->load->view($this->cp_rev_form, $this->data);
    }

    public function saveCpRevision(){
        $data = $this->input->post();
        $errorMessage = array();
        if(isset($data['new_dimension_id'])){
            foreach ($data['new_dimension_id'] as $key => $id) {
                // if (empty($data['new_pfc_id'][$key])) {
                //     $errorMessage['general_error'] = "Peocess NO is required. ";
                // }
                if ( empty($data['product_param'][$key]) && empty($data['process_param'][$key])) {
                    $errorMessage['general_error'] = "New Parameter is required";
                }
            }
        }
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $result = $this->controlPlanV2->saveCpRevision($data);
            $result['url'] = base_url($this->data['headData']->controller.'/controlPlanList/'.$data['pfc_main_id']);
            $this->printJson($result);
        endif;
    }

     /* Created By :- Sweta @11-08-2023 */
     public function revisionHtml($data){
        $revData = $this->controlPlanV2->revisionList(['item_id'=>$data['item_id'],'rev_type'=>$data['rev_type']]);
        $i=1; $tbody=''; $activeButton="";
		if(!empty($revData)):
			
			foreach($revData as $row):
                if(empty($row->is_active)){
                    $activeParam = "{'id' : ".$row->id.",'is_active':'1','msg':'Are you sure you want to active this revision ?'}";
                    $activeButton = '<a class="btn btn-facebook btn-edit permission-modify" href="javascript:void(0)" datatip="Active" flow="down" onclick="activeRevision('.$activeParam.');"><i class="fas fa-check-circle" ></i></a>';
                }else{
                    $activeParam = "{'id' : ".$row->id.",'is_active':'0','msg':'Are you sure you want to deactive this revision ?'}";
                    $activeButton = '<a class="btn btn-danger btn-edit permission-modify" href="javascript:void(0)" datatip="Deactive" flow="down" onclick="activeRevision('.$activeParam.');"><i class="far fa-times-circle" ></i></a>';
                }

				$tbody.= '<tr>
						<td>'.$i++.'</td>
						<td>'.formatDate($row->rev_date).'</td>
						<td>'.$row->rev_no.'</td>
						<td>'.$row->cust_rev_no.'</td>
						<td>'.$row->remark.'</td>
						<td class="text-center">'.$activeButton.'</td>
					</tr>';
			endforeach;
		endif;
		return ['status'=>1,'tbody'=>$tbody];
    }


    /* Created By :- Sweta @16-08-2023 */
    public function activeRevision()
    {
        $data = $this->input->post();
        $this->printJson($this->controlPlanV2->activeRevision($data));
    }

    public function getDimensionList(){
        $data = $this->input->post();
        // print_r($data);
        $dimensionList = $this->controlPlanV2->getCPDimenstion(['pfc_id'=>$data['pfc_id'],'item_id'=>$data['item_id']]);
        $tbodyData = '';
        
        $revList = $this->ecn->getCpRevData(['item_id'=>$data['item_id'],'status'=>3]);
        if(!empty($dimensionList)){
            $i=1;
            foreach($dimensionList as $row){
                
                $cpRevSelect = ' <select  class="form-control jp_multiselect" name="cpRevSelect[]" data-pfc_id="'.$i.'" data-input_id="rev_no'.$i.'" multiple="multiple">';
                if (!empty($revList)) {
                    foreach ($revList as $rev) {
                        $selected = (!empty($row->rev_no) && in_array($rev->rev_no, explode(",", $row->rev_no))) ? 'selected' : '';

                        $cpRevSelect .='<option value="'. $rev->rev_no .'" '. $selected .'>'. $rev->rev_no .' | PFC REV : '.$rev->pfc_rev_no .'</option>';
                    }
                } 
                $cpRevSelect.='</select>';
                $diamention = '';
                if($row->requirement==1){ $diamention = $row->min_req.'/'.$row->max_req ; }
                if($row->requirement==2){ $diamention = $row->min_req.' '.$row->other_req ; }
                if($row->requirement==3){ $diamention = $row->max_req.' '.$row->other_req ; }
                if($row->requirement==4){ $diamention = $row->other_req ; }
                $copyBtn=" <a class='btn btn-outline-success btn-sm permission-modify' href='javascript:void(0)' onclick='addRow(".json_encode($row).");'><i class=' fas fa-copy'></i></a>";
                $tbodyData.='<tr>
                    <td class="text-center">'.$i.'</td>
                    <td>
                        <input type="hidden" name="dimension_id[]" value="'.$row->id.'">
                        '.$row->product_param.'
                    </td>
                    <td>'.$row->process_param.'</td>
                    <td>'.$row->char_class.'</td>
                    <td>'.$row->other_req.'</td>
                    <td>'.$row->opr_measur_tech.'</td>
                    <td>'.$row->opr_size.'</td>
                    <td>'.$row->opr_freq.'</td>
                    <td>'.$row->opr_freq_time.'</td>
                    <td>'.$row->opr_freq_text.'</td>
                    <td>'.$row->iir_measur_tech.'</td>
                    <td>'.$row->iir_size.'</td>
                    <td>'.$row->iir_freq.'</td>
                    <td>'.$row->iir_freq_time.'</td>
                    <td>'.$row->iir_freq_text.'</td>
                    <td>'.$row->ipr_measur_tech.'</td>
                    <td>'.$row->ipr_size.'</td>
                    <td>'.$row->ipr_freq.'</td>
                    <td>'.$row->ipr_freq_time.'</td>
                    <td>'.$row->ipr_freq_text.'</td>
                    <td>'.$row->sar_measur_tech.'</td>
                    <td>'.$row->sar_size.'</td>
                    <td>'.$row->sar_freq.'</td>
                    <td>'.$row->sar_freq_time.'</td>
                    <td>'.$row->sar_freq_text.'</td>
                    <td>'.$row->spc_measur_tech.'</td>
                    <td>'.$row->spc_size.'</td>
                    <td>'.$row->spc_freq.'</td>
                    <td>'.$row->spc_freq_time.'</td>
                    <td>'.$row->spc_freq_text.'</td>
                    <td>'.$row->fir_measur_tech.'</td>
                    <td>'.$row->fir_size.'</td>
                    <td>'.$row->fir_freq.'</td>
                    <td>'.$row->fir_freq_time.'</td>
                    <td>'.$row->fir_freq_text.'</td>
                    <td>
                        '.$cpRevSelect.'
                        <input type="hidden" name="rev_no[]" id="rev_no'.$i.'" value="'. (!empty($row->rev_no) ? $row->rev_no : '') .'">
                        <div class="error rev_no'.$i.'"></div>
                    </td>
                    <td  class="text-center">'.$copyBtn.'</td>
                </tr>';
                $i++;
            }
        }
        $this->printJson(['status'=>1,'tbodyData'=>$tbodyData]);
    }

    public function approveCP(){
		$data = $this->input->post();
		
		if(empty($data['id'])):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->controlPlanV2->approveCP($data));
		endif;
	}

}
