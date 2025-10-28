<?php
class Scrap extends MY_Controller
{
    private $indexPage = "scrap/index";
    private $productionRejScrap = "scrap/rej_scrap_form";

    public function __construct()
    {
        parent::__construct();
        $this->isLoggedin();
        $this->data['headData']->pageTitle = "scrap";
        $this->data['headData']->controller = "scrap";
    }

    public function index()
    {
        $this->data['headData']->pageUrl = "scrap";
        $this->data['tableHeader'] = getProductionHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage, $this->data);
    }

    public function getDTRows()
    {
        $result = $this->scrap->getDTRows($this->input->post());
        $sendData = array();
        $i = 1;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $sendData[] = getScrapData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }


    public function generateRejScrap()
    {
        $data = $this->input->post();
        $this->data['locationList'] = $this->scrap->getJobcardList();

        $this->load->view($this->productionRejScrap, $this->data);
    }

    public function saveProductionRejScrape()
    {
        $data = $this->input->post();

        $errorMessage = array();
        if (empty($data['item_id']))
            $errorMessage['item_id'] = "Item is required.";
        if (empty($data['log_sheet_id'][0]))
            $errorMessage['general_error'] = "Scrap data is required.";

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            if (empty(array_sum($data['scrap_qty'])) && empty(array_sum($data['supplier_rej'])) && empty(array_sum($data['ok_qty']))) :
                $errorMessage['general_error'] = "Scrap Or Ok Qty is required.";
                $this->printJson(['status' => 0, 'message' => $errorMessage]);
            else :
                $this->printJson($this->scrap->saveProductionRejScrape($data));
            endif;
        endif;
    }

    public function getRejectionBatchList()
    {
        $data = $this->input->post();
        $batchData = $this->scrap->getRejectionStock($data['job_card_id']);
        
        $tbody = '';
        
        if (!empty($batchData)) {
            $i = 1;
            foreach ($batchData as $row) {
                
                if(!empty($row->belongs_to_name)){
                    if($row->belongs_to_name == 'Row Material'){
                        $jobUsedMtr = $this->jobcard_v3->getJobcardRowMaterial($data['job_card_id']);

                        if(!empty($jobUsedMtr->batch_no)){
                            $supplierData = $this->scrap->getSupplierForUseMaterial(['batch_no'=>$jobUsedMtr->batch_no]);
                            
                            if(!empty($supplierData->party_name)){
                                $row->vendor_name = $supplierData->party_name;
                            }
                        } 
                    }
                }
                
                $tbody .= '<tr>
                    <td>' . $i . '</td>
                    <td>' . getPrefixNumber($row->job_prefix,$row->job_no) . '</td>
                    <td>' . $row->process_name . '</td>
                    <td>' . (!empty($row->reason) ? $row->reason_name : '') . '<input type="hidden" name="rej_reason[]" value="' . $row->reason . '"></td>
                    <td>' . (!empty($row->belongs_to_name) ? $row->belongs_to_name : '') . '<input type="hidden" name="rej_stage[]" value="' . (!empty($row->belongs_to) ? $row->belongs_to_name : '') . '"></td>
                    <td>' . (!empty($row->vendor_name) ? $row->vendor_name : $row->vendor_name) . '<input type="hidden" value="' . $row->vendor_id . '" name="rej_from[]"></td>
                    <td>' .$row->qty . '</td>
                    <td>
                        <input type="hidden" name="rej_log_id[]"  value="'.$row->id.'">
                        <input type="hidden" name="log_sheet_id[]" value="' . (!empty($row->log_id) ? $row->log_id : '') . '">
                        
                        <input type="text" name="scrap_qty[]" class="form-control numericOnly batchQty" data-pending_qty="'.$row->qty.'" data-rowid="' . $i . '"  id="scrapQty'.$i.'" value="0">
                      
                        <div class="error batch_qty' . $i . '"></div>
                    </td>
                    <td>
                        <input type="text" name="supplier_rej[]" data-pending_qty="'.$row->qty.'" class="form-control numericOnly batchQty" data-rowid="' . $i . '" id="supplierRej'.$i.'" value="0">
                    </td>
                    <td>
                        <input type="text" name="ok_qty[]" data-pending_qty="'.$row->qty.'" class="form-control numericOnly batchQty" data-rowid="' . $i . '" data-rej_qty="' . $row->qty . '" id="okQty'.$i.'" value="0">
                    </td>
                </tr>';
                
                $i++;    
            }
        }
        
        $bomData ='';
        $materialData = $this->jobcard_v3->getRequestItemData($data['job_card_id']);
        if (!empty($materialData)) {
            $i = 1;
            foreach ($materialData as $row) {
                $bomData .= '<tr>
                    <td>'.$i++.'</td>
                    <td>'.$row->item_name.'</td>
                    <td>'.$row->qty.'</td>
                </tr>';
            }
        }

        $this->printJson(['status' => 1, 'tbody' => $tbody, 'bomData' => $bomData]);
    }

    public function delete()
    {
        $id = $this->input->post('id');
        if (empty($id)) :
            $this->printJson(['status' => 0, 'message' => 'Something went wrong...Please try again.']);
        else :
            $this->printJson($this->scrap->delete($id));
        endif;
    }

    public function getTransList(){
        $data=$this->input->post();
        $scrapData = $this->scrap->getScrapBookTransData($data['id']);
        
        $htmlData=''; $i=1;
        foreach($scrapData as $row){
            $comment=$this->comment->getComment($row->rej_reason);
            $process_name=!empty($row->rej_stage)?$this->process->getProcess($row->rej_stage)->process_name:'Raw Material';
            $vendor_name=!empty($row->rej_from)?$this->party->getParty($row->rej_from)->party_name:'In House';
            $htmlData.='<tr>
                <td>'.$i++.'</td>
                <td>'.$row->process_name.'</td>
                <td>'.$comment->remark.'</td>
                <td>'.$process_name.'</td>
                <td>'.$vendor_name.'</td>
                <td>'.$row->scrap_qty.'</td>
                <td>'.$row->supplier_rej.'</td>
                <td>'.$row->ok_qty.'</td>
            </tr>';
        }
        $this->printJson(['status'=>1,'htmlData'=>$htmlData]);
    }
}
