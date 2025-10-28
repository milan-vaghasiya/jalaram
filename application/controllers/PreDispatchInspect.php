<?php
class PreDispatchInspect extends MY_Controller
{
    private $indexPage = "predispatch_inspect/index";
    private $formPage = "predispatch_inspect/form";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Pre Dispatch Inspect";
		$this->data['headData']->controller = "preDispatchInspect";
		$this->data['headData']->pageUrl = "preDispatchInspect";
	}

    public function index(){
        $this->data['tableHeader'] = getQualityDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $data = $this->input->post();
        $result = $this->preDispatch->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getPreDispatchInspectData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addPreDispatch(){
        $this->data['itemData'] = $this->item->getItemLists(1);
        $this->load->view($this->formPage,$this->data);
    }

    public function getPreDispatchInspection(){
        $data = $this->input->post();
        $paramData = $this->item->getPreInspectionParam($data['item_id']);   /* '.$obj[$row->id][0].' */
        $tbodyData="";$i=1; 
        if(!empty($paramData)):
            foreach($paramData as $row):
                $tbodyData.= '<tr>
                            <td style="text-align:center;">'.$i++.'</td>
                            <td>'.$row->parameter.'</td>
                            <td>'.$row->specification.'</td>
                            <td>'.$row->lower_limit.'</td>
                            <td>'.$row->upper_limit.'</td>
                            <td>'.$row->measure_tech.'</td>
                            <td><input type="text" name="sample1_'.$row->id.'" class="form-control" value=""></td>
                            <td><input type="text" name="sample2_'.$row->id.'" class="form-control" value=""></td>
                            <td><input type="text" name="sample3_'.$row->id.'" class="form-control" value=""></td>
                            <td><input type="text" name="sample4_'.$row->id.'" class="form-control" value=""></td>
                            <td><input type="text" name="sample5_'.$row->id.'" class="form-control" value=""></td>
                            <td><input type="text" name="sample6_'.$row->id.'" class="form-control" value=""></td>
                            <td><input type="text" name="sample7_'.$row->id.'" class="form-control" value=""></td>
                            <td><input type="text" name="sample8_'.$row->id.'" class="form-control" value=""></td>
                            <td><input type="text" name="sample9_'.$row->id.'" class="form-control" value=""></td>
                            <td><input type="text" name="sample10_'.$row->id.'" class="form-control" value=""></td>
                            <td><input type="text" name="result_'.$row->id.'" class="form-control" value=""></td>
                        </tr>';
            endforeach;
        else:
            $tbodyData.= '<tr><td colspan="17" style="text-align:center;">No Data Found</td></tr>';
        endif;
        $this->printJson(['status'=>1,"tbodyData"=>$tbodyData]);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = Array();

        if(empty($data['item_id']))
            $errorMessage['item_id'] = "Item is required.";
        if(empty($data['dispatch_qty']))
            $errorMessage['dispatch_qty'] = "Inspection Qty. is required.";
        if(empty($data['date']))
            $errorMessage['date'] = "Date is required.";

        $insParamData = $this->item->getPreInspectionParam($data['item_id']);

        if(count($insParamData) <= 0)
            $errorMessage['general'] = "Item Parameter is required.";

        $pre_inspection = Array();
        if(!empty($insParamData)):
            foreach($insParamData as $row):
                $param = Array();
                for($j = 1; $j <= 10; $j++):
                    $param[] = $data['sample'.$j.'_'.$row->id];
                    unset($data['sample'.$j.'_'.$row->id]);
                endfor;
                $param[] = $data['result_'.$row->id];
                $pre_inspection[$row->id] = $param;
                unset($data['result_'.$row->id]);
            endforeach;
        endif;

        $data['observe_samples'] = json_encode($pre_inspection);
        $data['param_count'] = count($insParamData);

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->preDispatch->save($data));
        endif;
    }

    public function edit($id){
        $this->data['dataRow'] = $this->preDispatch->getPreInspection($id);
        $this->data['paramData'] = $this->item->getPreInspectionParam($this->data['dataRow']->item_id);
        $this->data['itemData'] = $this->item->getItemLists(1);
        $this->load->view($this->formPage,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->preDispatch->delete($id));
        endif;
    }

    public function preInspection_pdf($id){
        $this->data['inInspectData'] = $this->preDispatch->getPreInspectionForPrint($id);
        $this->data['paramData'] = $this->item->getPreInspectionParam($this->data['inInspectData']->item_id);
		$this->data['companyData'] = $this->purchaseOrder->getCompanyInfo();
		$response="";
		$logo=base_url('assets/images/logo.png');
		$this->data['letter_head']=base_url('assets/images/letterhead_top.png');
		
		$pdfData = $this->load->view('predispatch_inspect/printPreInspection',$this->data,true);
		
		$inInspectData = $this->data['inInspectData'];
		
		$htmlHeader = '<img src="'.$this->data['letter_head'].'" class="img">';
		$htmlFooter = '<table class="table table-top" style="margin-top:10px;border-bottom:1px solid #000000;">
						<tr>
							<td style="width:50%;"></td>
							<td style="width:25%;" class="text-center">Prepared By</td>
							<td style="width:25%;" class="text-center">Approved By</td>
						</tr>
					</table>
					<table class="table top-table" style="margin-top:10px;">
						<tr>
							<!--<td style="width:25%;">PO No. & Date : </td>-->
							<td style="width:25%;"></td>
							<td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
						</tr>
					</table>';
		
		$mpdf = new \Mpdf\Mpdf();
		$pdfFileName='DC-REG-'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css'));
		$stylesheet = file_get_contents(base_url('assets/css/style.css?v='.time()));
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetWatermarkImage($logo,0.03,array(120,60));
		$mpdf->showWatermarkImage = true;
		$mpdf->SetProtection(array('print'));
		
		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('P','','','','',5,5,58,25,5,5,'','','','','','','','','','A4-L');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');		
	}
}
?>