<?php
class VendorLog extends MY_Apicontroller{
    
    public function __construct(){
        parent::__construct();        
        $this->data['headData']->pageTitle = "Employee";
        $this->data['headData']->pageUrl = "api/v2/employee";
        $this->data['headData']->base_url = base_url();
    }

    public function getChallanList(){
		$data = $this->input->post(); 
		$data['vendor_id'] = $this->party_id;
		
		if(empty($data['status']))
		{
            $this->data['challanList'] = $this->jobWorkVendor_v3->getPendingChallans($data);
		}
		if($data['status'] == 2){$data['start'] = 0;$data['length'] = 50; }
        $this->data['challanTransList'] = $this->jobWorkVendor_v3->getChallanTransList($data);
        $this->printJson(['status'=>1,'message'=>'Data Found.','data'=>$this->data]);
    }
    
    public function getChallanDispatchList(){
		$data = $this->input->post(); 
		
        //$this->data['challanDispatchList'] = $this->productionReportsNew->getJobworkRegister(['ref_id'=>$data['ref_id'],'prod_type'=>2]);
        $this->data['challanDispatchList'] = $this->jobWorkVendor_v3->getVendorReceive(['ch_trans_id'=>$data['ch_trans_id']]);
        
        $this->printJson(['status'=>1,'message'=>'Data Found.','data'=>$this->data]);
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
			// $result = $this->jobWorkVendor_v3->saveBulkReceive($data);
			$result = $this->jobWorkVendor_v3->saveVendorReceive($data);
            $this->printJson($result);
        endif;
	}
	
	public function changeChallanStatus(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $data['trans_status'] = 1;
            $this->printJson($this->jobWorkVendor_v3->changeChallanStatus($data));
        endif;
    }
    
	public function saveDispatch(){
		$postData = $this->input->post();
	
        $errorMessage = array();
		if(empty($postData['in_challan_no'])){
            $errorMessage['in_challan_no'] = "Challan No is required";
        }
		if(empty($postData['in_challan_date'])){
            $errorMessage['in_challan_date'] = "Date is required";
        }
        
        $processData = json_decode($postData['process_data'],true);
        $data['process_id'] = [];
        foreach($processData AS $key=>$row){
            $data['process_id'][] = $row['pid'];
            $row['wp_qty'] = ((!empty($row['wp_qty']))?$row['wp_qty']:0);
            if($key == 0){
                $data['production_qty'][] = $postData['production_qty'] - $row['wp_qty'];
                $data['without_prs_qty'][] =$row['wp_qty'];
            }else{
                $data['production_qty'][] = $data['production_qty'][$key-1] - $row['wp_qty'];
                $data['without_prs_qty'][] =$row['wp_qty'];
            }
        } 
        $data['in_challan_no'] = $postData['in_challan_no'];
        $data['in_challan_date'] = $postData['in_challan_date'];
        $data['challan_trans_id'] = $postData['challan_trans_id'];
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
			$data['entry_type'] = 'APP';
			$result = $this->jobWorkVendor_v3->saveVendorReceive($data);
            $this->printJson($result);
        endif;
	}
}
?>