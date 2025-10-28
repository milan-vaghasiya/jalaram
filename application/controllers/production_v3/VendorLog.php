<?php
class VendorLog extends MY_Controller{
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Production Log";
		$this->data['headData']->controller = "production_v3/vendorLog";
		// $this->data['headData']->pageUrl = "production_v3/jobWorkVendor";
	}
	
	public function index($status=0){
		$this->data['status'] = $status;
		$this->data['tableHeader'] = getProductionHeader("vendorEntry");
        
        $this->load->view('production_v3/vendor/vendor_log',$this->data);
    }

    public function getDTRows($status=0,$dates=''){
		$data = $this->input->post(); 
		$data['status'] = $status; 
		$data['vendor_id'] = $this->party_id;
		$result = $this->jobWorkVendor_v3->getVendorDTRows($data);
        
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
			$row->controller = $this->data['headData']->controller;		
			$row->status_per = round($row->status_per,2);
			$pendingDays='';
			if($row->status_per < 100){
				$challan_date = date_create($row->trans_date);
				$curr_date = date_create();
				$calculateDays = date_diff($challan_date, $curr_date);
				$pendingDays = (100 - $calculateDays->d).' Days';
			}
			
			if($row->status_per == 0):
				$row->status = '<span class="badge badge-pill badge-danger m-1 fs-12">Pending - '.$row->status_per.'% - '.$pendingDays.'</span>';
			elseif($row->status_per < 100):
				$row->status = '<span class="badge badge-pill badge-warning m-1 fs-12">In Process - '.$row->status_per.'% - '.$pendingDays.'</span>';
			else:
				$row->status = '<span class="badge badge-pill badge-success m-1 fs-12">Completed - '.$row->status_per.'%</span>';
			endif;	
			$sendData[] = getVendorChallanEntryData($row);	
        endforeach;
        $result['data'] =$sendData;
        $this->printJson($result);
    }

	public function outwardLog(){
		$data = $this->input->post();
		$this->data['dataRow'] = $this->jobWorkVendor_v3->getJobworkVendorTrans(['id'=>$data['id'],'signle_row'=>1]);
		$this->data['processList']= $this->process->getProcessList();
        $this->load->view('production_v3/vendor/vendor_out_form',$this->data);
	}

	public function save(){
		$data = $this->input->post();
        $errorMessage = array();
		if(empty($data['in_challan_no'])){
            $errorMessage['in_challan_no'] = "Challan No is required";
        }
		if(empty($data['in_challan_date'])){
            $errorMessage['in_challan_date'] = "Date is required";
        }
		foreach($data['process_id'] as $jobKey=>$process_id){

			$okQty = !empty($data['production_qty'][$jobKey])?$data['production_qty'][$jobKey]:0;
			$without_prs_qty = !empty($data['without_prs_qty'][$jobKey])?$data['without_prs_qty'][$jobKey]:0;

			$totalReceivedQty = $okQty+$without_prs_qty;

			if($jobKey == 0){
				$challanData = $this->jobWorkVendor_v3->getJobWorkVendorRow($data['challan_trans_id']);
				$pendingQty  =$challanData->pending_qty;
				if($totalReceivedQty == 0){
					$errorMessage['production_qty'.$process_id] = "Qty is required.";
				}elseif($totalReceivedQty > $pendingQty){
					$errorMessage['production_qty'.$process_id] = "Qty is invalid.";
				}
			}elseif($jobKey > 0){
				
				if($totalReceivedQty > $data['production_qty'][$jobKey-1]){
					$errorMessage['production_qty'.$process_id] = "Qty is invalid.";
				}
				elseif(!empty($data['production_qty'][$jobKey-1]) && $data['production_qty'][$jobKey-1] > 0 && $totalReceivedQty <$data['production_qty'][$jobKey-1]){
					$errorMessage['production_qty'.$process_id] = "Qty is invalid.";
				}
				elseif(!empty($data['production_qty'][$jobKey-1]) && $data['production_qty'][$jobKey-1] > 0 && $totalReceivedQty <= 0){
					$errorMessage['production_qty'.$process_id] = "Qty is required.";
				}
			}
		}

		if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            // print_r($data);exit;
			// $result = $this->jobWorkVendor_v3->saveBulkReceive($data);
			$data['entry_type'] = 'WEB';
			$result = $this->jobWorkVendor_v3->saveVendorReceive($data);
            $this->printJson($result);
        endif;
	}

	public function getVendorOutLog(){
		$data = $this->input->post();
		$this->data['ch_trans_id'] = $data['ch_trans_id'];
		$this->load->view('production_v3/vendor/vendor_out_log',$this->data);
	}
	public function getVendorOutHtml(){
		$data = $this->input->post();
		$transData = $this->jobWorkVendor_v3->getVendorReceive(['ch_trans_id'=>$data['ch_trans_id']]);
		$html = '';
		if(!empty($transData))
		{
			$processData = array_reduce($transData, function($processData, $process) { 
					$processData[$process->trans_no][] = $process; 
					return $processData; 
				}, []);
			foreach($processData AS $key=>$process){
				$firstRow=true;
				foreach($process AS $row){
					$html .= '<tr>';
					if($firstRow == true){
						$html .= '<td rowspan="'.count($process).'" class="text-center">'.$row->in_challan_no.'</td>';
						$html .= '<td rowspan="'.count($process).'" class="text-center">'.formatdate($row->in_challan_date).'</td>';
					}
					$html.='<td>'.$row->process_name.'</td>';
					$html.='<td class="text-center">'.floatval($row->production_qty).'</td>';
					$html.='<td class="text-center">'.floatval($row->without_prs_qty).'</td>';

					if($firstRow == true){
						$deleteBtn = '';
						if (empty($row->accepted_by)) {
							$deleteParam = "{'trans_no' : ".$row->trans_no.",'ch_trans_id' : ".$row->ch_trans_id.",'message' : 'Record','fndelete' : 'delete','res_function':'getOutwardTransHtml','controller':'production_v3/vendorLog'}";
							$deleteBtn = '<a class="btn btn-sm btn-outline-danger btn-delete" href="javascript:void(0)" onclick="trashVendorLog('.$deleteParam.');" datatip="Remove" flow="left"><i class="fas fa-trash"></i></a>';
						}
						$html.='<td rowspan="'.count($process).'" class="text-center">'.$deleteBtn.'</td>';
						$firstRow = false;
					}
					

					$html.='</tr>';
				}
			}
		}
		$this->printJson(['status'=>1,'tbodyData'=>$html]);
	}


	public function delete(){
        $data = $this->input->post();
        if(empty($data['trans_no'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->jobWorkVendor_v3->deleteVendorLog($data));
        endif;
    }

	public function changeChallanStatus(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->jobWorkVendor_v3->changeChallanStatus($data));
        endif;
    }
}
?>