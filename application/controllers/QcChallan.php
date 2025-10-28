<?php
class QcChallan extends MY_Controller{
    private $indexPage = "qc_challan/index";
    private $formPage = "qc_challan/form";
    private $returnPage = "qc_challan/return_form";
    private $calibrationForm = "qc_gauges/calibration_form";
   
    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "QC Challan";
		$this->data['headData']->controller = "qcChallan";
		$this->data['headData']->pageUrl = "qcChallan";
	}
	
	public function index(){
        $this->data['tableHeader'] = getQualityDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status=0){
        $data = $this->input->post(); $data['qc_status'] = $status;
        $result = $this->qcChallan->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;  
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getQcChallanData($row);
        endforeach;        
        $result['data'] = $sendData;
        $this->printJson($result);
    }
    
    public function createChallan($id){
		$this->data['headData']->pageUrl = "qcChallan";
        $this->data['trans_prefix'] = 'QCH/'.$this->shortYear.'/';
        $this->data['trans_no'] = $this->qcChallan->nextTransNo(1); 
		$this->data['partyData'] = $this->party->getSupplierList();
        $this->data['deptData'] = $this->department->getDepartmentList();
        $this->data['itemData']  = $this->qcInstrument->getActiveInstruments();
        $this->data['challanItem'] = $this->qcChallan->getInstrumentForChallan($id);
        $this->load->view($this->formPage,$this->data);
    }

    public function addChallan(){
        $this->data['trans_prefix'] = 'QCH/'.$this->shortYear.'/';
        $this->data['trans_no'] = $this->qcChallan->nextTransNo(1);
		$this->data['partyData'] = $this->party->getSupplierList();
        $this->data['deptData'] = $this->department->getDepartmentList();
        $this->data['itemData']  = $this->qcInstrument->getActiveInstruments(); 
        $this->load->view($this->formPage,$this->data);
    }
    
	public function getPartyList(){
        $challan_type = $this->input->post('challan_type'); 
        $options = '<option value="0">IN-HOUSE</option>';
        if($challan_type == 2){
            $vendorData = $this->party->getVendorList();
            foreach($vendorData as $row):
                $options .= '<option value="'.$row->id.'">'.$row->party_name.'</option>';
            endforeach;
        }elseif($challan_type == 3){
            $supplierData = $this->party->getSupplierTypeWise('Services');
            foreach($supplierData as $row):
                $options .= '<option value="'.$row->id.'">'.$row->party_name.'</option>';
            endforeach;
        }else{
            $deptData = $this->department->getDepartmentList();
            foreach($deptData as $row):
                $options .= '<option value="'.$row->id.'">'.$row->name.'</option>';
            endforeach;
        }
        $this->printJson(['status'=>1,'options'=>$options]);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['trans_no']))
            $errorMessage['trans_no'] = "Challan No is required."; 
        if(empty($data['trans_date']))
            $errorMessage['trans_date'] = "Challan Date is required.";
        //if(empty($data['party_id']))
           // $errorMessage['party_id'] = "Issue From is required.";
        
        if(empty($data['item_id'][0]))
            $errorMessage['item_name_error'] = "Items is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $masterData = [
                'id' => $data['id'],
                'trans_prefix' => $data['trans_prefix'],  
                'trans_no' => $data['trans_no'],
                'challan_type' => $data['challan_type'],
                'trans_date' => $data['trans_date'],
                'party_id' => $data['party_id'],
                'remark' => $data['remark'],
                'item_type' => $data['item_type'],
                'created_by' => $this->session->userdata('loginId')
            ];

            $itemData = [
                'id' => $data['trans_id'],
                'item_id' => $data['item_id'],   
                'batch_no' => $data['batch_no'],
                'item_remark' => $data['item_remark'],
                'entry_type' => 1,
                'created_by' => $this->session->userdata('loginId')
            ];

            $this->printJson($this->qcChallan->save($masterData,$itemData));
        endif;
    }

    public function edit($id){
        $this->data['vendorData'] = $this->party->getVendorList();
        $this->data['supplierData'] = $this->party->getSupplierTypeWise('Services');
        $this->data['deptData'] = $this->department->getDepartmentList();
        $this->data['itemData']  = $this->qcInstrument->getActiveInstruments();
        $this->data['dataRow'] = $dataRow = $this->qcChallan->getQcChallan($id);
        $this->load->view($this->formPage,$this->data);
    }

    public function delete(){
		$id = $this->input->post('id');
		if(empty($id)):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->qcChallan->deleteChallan($id));
		endif;
	}

    public function returnChallan(){
		$id = $this->input->post('id');
        $this->data['dataRow'] = $this->qcChallan->getQcChallanTransRow($id);
        $this->data['locationList'] = $this->store->getStoreLocationListWithoutProcess();
        $this->load->view($this->returnPage,$this->data);
    }

    public function saveReturnChallan(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['receive_at']))
            $errorMessage['receive_at'] = "Receive date is required.";
        if(empty($data['to_location']))
            $errorMessage['to_location'] = "Receive Location is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['receive_by'] = $this->loginId;
            $this->printJson($this->qcChallan->saveReturnChallan($data));
        endif;
    }

    public function confirmChallan(){
        $data = $this->input->post();
		if(empty($data['id'])):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->qcChallan->confirmChallan($data));
		endif;
    }
	
	/*Created By : NYN @14-02-2023 */
    public function getCalibration(){ 
        $data = $this->input->post();
        $this->data['itemData'] = $this->qcInstrument->getInstGaugeList(2); 
        $this->data['dataRow'] = $result = $this->qcChallan->getQcChallanTransRow($data['id']);  
        $this->data['calData'] = $this->item->getCalibrationList($result->item_id); 
        $this->data['locationList'] = $this->store->getStoreLocationListWithoutProcess();
        $this->load->view($this->calibrationForm,$this->data);
    }

    public function saveCalibration(){
        $data = $this->input->post();
		$errorMessage = array();
		if(empty($data['cal_date']))
			$errorMessage['cal_date'] = "Date is required.";
		if(empty($data['cal_certi_no']))
			$errorMessage['cal_certi_no'] = "Certificate No. is required.";
        if(empty($data['to_location']))
            $errorMessage['to_location'] = "Receive Location is required.";

       
        if ($_FILES['certificate_file']['name'] != null || !empty($_FILES['certificate_file']['name'])) :
            $this->load->library('upload');
            $_FILES['userfile']['name']     = $_FILES['certificate_file']['name'];
            $_FILES['userfile']['type']     = $_FILES['certificate_file']['type'];
            $_FILES['userfile']['tmp_name'] = $_FILES['certificate_file']['tmp_name'];
            $_FILES['userfile']['error']    = $_FILES['certificate_file']['error'];
            $_FILES['userfile']['size']     = $_FILES['certificate_file']['size'];

            $imagePath = realpath(APPPATH . '../assets/uploads/gauges/');
            $config = ['file_name' => time() . "_certificate_file_" . $_FILES['userfile']['name'], 'allowed_types' => '*', 'max_size' => 10240, 'overwrite' => FALSE, 'upload_path'    => $imagePath];

            $this->upload->initialize($config);
            if (!$this->upload->do_upload()) :
                $errorMessage['certificate_file'] = $this->upload->display_errors();
                $this->printJson(["status" => 0, "message" => $errorMessage]);
            else :
                $uploadData = $this->upload->data();
                $data['certificate_file'] = $uploadData['file_name'];
            endif;
        else :
            unset($data['certificate_file']);
        endif;

		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
            $itemData = $this->qcInstrument->getItem($data['item_id']);  
            $data['next_cal_date'] = date('Y-m-d', strtotime($data['cal_date'] . "+".$itemData->cal_freq." months") );
            $data['created_by'] = $this->session->userdata('loginId');
            $response = $this->qcInstrument->saveCalibration($data);
			$this->printJson($response);
        endif;
    }

    public function deleteCalibration(){
        $id = $this->input->post('id');
        $item_id = $this->input->post('item_id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->item->deleteCalibration($id,$item_id);

            $result = $this->item->getCalibrationList($item_id);
            $i=1;$tbodyData=""; 
            if(!empty($result)) :
                foreach ($result as $row) :
                    $deleteParam = $row->id.",'Calibration'";
                    $tbodyData.= '<tr>
                            <td>'.$i.'</td>
                            <td>'.formatDate($row->cal_date).'</td>
                            <td>'.$row->cal_by.'</td>
                            <td>'.$row->cal_agency.'</td>
                            <td>'.$row->cal_certi_no.'</td>                                        
                            <td>'.((!empty($row->certificate_file))?'<a href="'.base_url('assets/uploads/gauges/'.$row->certificate_file).'" target="_blank"><i class="fa fa-download"></i></a>':"") .'</td>
                            <td class="text-center">';
                                $tbodyData.= '<a class="btn btn-outline-danger btn-sm btn-delete" href="javascript:void(0)" onclick="trashCalibration('.$deleteParam.');" datatip="Remove" flow="left"><i class="ti-trash"></i></a>';
                    $tbodyData.='</td></tr>'; $i++;
                endforeach;
            else:
                $tbodyData.= '<tr><td colspan="7" style="text-align:center;">No Data Found</td></tr>';
            endif;
			$this->printJson(['status'=>1, "tbodyData"=>$tbodyData, "itemId"=>$item_id]);
        endif;
    }

	// Created By Meghavi  @12/01/2024
    function printChallan($id){
        $this->data['challanData'] = $this->qcChallan->getQcChallan($id);
		$this->data['companyData'] = $this->purchaseOrder->getCompanyInfo();
		$response="";
		
        $logo = base_url('assets/images/logo.png');
        $this->data['letter_head'] = base_url('assets/images/letterhead_top.png');
		
	
		$pdfData = $this->load->view('qc_challan/print_challan',$this->data,true);
	
		
		$htmlHeader = '<img src="'.$this->data['letter_head'].'" class="img">';
		$htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;border-bottom:1px solid #000000;">
						<tr>
							<td style="width:50%;" rowspan="3"></td>
							<th colspan="2">For, '.$this->data['companyData']->company_name.'</th>
						</tr>
						<tr>
							<td style="width:25%;" class="text-center"></td>
							<td style="width:25%;" class="text-center"></td>
						</tr>
						<tr>
							<td style="width:25%;" class="text-center">Prepared By</td>
							<td style="width:25%;" class="text-center">Authorised By</td>
						</tr>
					</table>
					<table class="table top-table" style="margin-top:10px;">
						<tr>
							<td style="width:25%;">PO No. & Date : </td>
							<td style="width:25%;"></td>
							<td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
						</tr>
					</table>';
		
		$mpdf = new \Mpdf\Mpdf();
		$pdfFileName='DC-REG-'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetWatermarkImage($logo,0.03,array(120,45));
		$mpdf->showWatermarkImage = true;
		$mpdf->SetProtection(array('print'));
		
		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('P','','','','',5,5,30,30,5,5,'','','','','','','','','','A5-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
	}
}
?>