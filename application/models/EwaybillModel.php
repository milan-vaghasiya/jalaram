<?php
class EwaybillModel Extends MasterModel
{
    private $ewaybillMaster = "eway_bill_master";
    private $ewaybillLog = "eway_bill_log";
    private $transMain = "trans_main";
    private $transChild = "trans_child";

    private $testMode = 0; // 1 = Testing/Development , 0 = Live

    public function loadFormData($id,$type="EWB"){
        $queryData = array();
        $queryData['tableName'] = $this->transMain;
        $queryData['select'] = "trans_main.id,trans_main.party_id,party_master.party_address,party_master.party_pincode,party_master.distance,party_master.city_id,party_master.state_id,party_master.country_id";
        $queryData['leftJoin']['party_master'] = "trans_main.party_id = party_master.id";
        if($type == "EWB"):
            $queryData['where']['trans_main.eway_status'] = 0;
        else:
            $queryData['where']['trans_main.e_inv_status'] = 0;
        endif;
        $queryData['where']['trans_main.id'] = $id;
        return $this->row($queryData);
    }   

    public function loadJCEWBFormData($id){
        $queryData = array();
        $queryData['tableName'] = "vendor_challan";
        $queryData['select'] = "vendor_challan.id,vendor_challan.vendor_id as party_id,party_master.party_address,party_master.party_pincode,party_master.distance,party_master.city_id,party_master.state_id,party_master.country_id";
        $queryData['leftJoin']['party_master'] = "vendor_challan.vendor_id = party_master.id";
        $queryData['where']['vendor_challan.id'] = $id;
        return $this->row($queryData);
        //$this->printQuery();
    }

    public function vehicleSearch(){
        $queryData = array();
        $queryData['tableName'] = $this->ewaybillMaster;
        $queryData['select'] = "vehicle_no";
        $queryData['group_by'][] = "vehicle_no";
        $result = $this->rows($queryData);

		$searchResult = array();
		foreach($result as $row){$searchResult[] = $row->vehicle_no;}
		return  $searchResult;
    }

    public function save($data){
        if($data['type'] == "EWB"):
            $jsonData = $this->ewbJsonSingle($data);
        elseif($data['type'] == "JCEWB"):
            $jsonData = $this->jobWorkChallanJson($data);
        else:
            $jsonData = $this->einvJson($data);
            $validate = $this->validateEinvoiceJson($jsonData);
            if($validate['status'] == 2):
                return $validate;
            endif;
        endif;
        unset($data['ewb_status']);

        $data['id'] = "";
        $data['json_data'] = json_encode($jsonData);
        $data['ref_id'] = $data['ref_id'];
        $data['transport_doc_date'] = (!empty($data['transport_doc_date']))?date('Y-m-d',strtotime($data['transport_doc_date'])):"";
        $data['created_by'] = $this->loginId;
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        $result = $this->store($this->ewaybillMaster,$data);
        $id = $result['insert_id'];
        return ['status'=>1,'message'=>'Json Generated successfully.','id'=>$id,'data'=>$jsonData];
    }

    public function validateEinvoiceJson($data){
		/* $data = json_decode(json_encode($data));
		
		// Validate
		$validator = new JsonSchema\Validator;
		$validator->validate($data, (object)['$ref' => 'file://' . realpath('e-invoice-json-schema.json')]);

		if ($validator->isValid()): */
			return ['status'=>1,'message'=>"The supplied JSON validates against the schema."];
		/* else:
			$errorMessage = array();
			foreach ($validator->getErrors() as $error):
				$errorMessage[] = $error['property']." -> ".$error['message'];
			endforeach;
			return ['status'=>2,'message'=>"JSON does not validate. Violations: ".implode(", ",$errorMessage)];
		endif; */
	}

    public function getEwbAuthToken($data){
        $fromGst = $data['fromGst'];
        $euser = $data['euser'];
        $epass = $data['epass'];

        $queryData = array();
        $queryData['tableName'] = $this->ewaybillLog;
        $queryData['where']['active_token'] = 1;
        $queryData['where']['type'] = 1;
        $queryData['order_by']['id'] = "DESC";
        $queryData['limit'] = 1;
        $ewbLog = $this->row($queryData);

        if(!empty($ewbLog) && $ewbLog->expired_at > date("Y-m-d H:i:s")):
            $dbResponse = json_decode($ewbLog->response_data);
            $result = ['status'=>1,'token'=>$dbResponse->Data->AuthToken];
        else:            
            $test_link = "https://gstsandbox.charteredinfo.com/eivital/dec/v1.04/auth?&aspid=1674891122&password=Jp@94272&Gstin=34AACCC1596Q002&user_name=TaxProEnvPON&eInvPwd=abc34*";

            $live_link = "https://einvapi.charteredinfo.com/eivital/dec/v1.04/auth?aspid=1674891122&password=Jp@94272&Gstin=$fromGst&user_name=$euser&eInvPwd=$epass";
           

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => ($this->testMode == 1)?$test_link:$live_link,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 120,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET"
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);

            if($err):
                $logData['response_status'] = "Fail";
                $logData['response_data'] = json_encode(['error'=>'cURL Error #: ' . $err]);
                $result = ['status'=>2,'message'=>'Auth token not found. cURL Error #: ' . $err]; 
            else:
                $logData['response_data'] = $response;
                $responseData = json_decode($response);
                if($responseData->Status != 1):
                    $logData['response_status'] = "Fail";
                    $result = ['status'=>2,'message'=>implode(", ",array_column($responseData->ErrorDetails,'ErrorMessage')),'data' => $responseData ]; 
                else:
                    $this->edit($this->ewaybillLog,['active_token'=>1],['expired_at'=>"",'active_token'=>0]);

                    $logData['response_status'] = "Success";
                    $logData['active_token'] = 1;
                    $logData['expired_at'] = $responseData->Data->TokenExpiry;
                    $result =  ['status'=>1,'token'=>$responseData->Data->AuthToken];
                endif;
            endif;
            $logData['id'] = "";
            $logData['type'] = '1';
            $logData['created_by'] = $this->session->userdata('loginId');
            $logData['created_at'] = date('Y-m-d H:i:s');
            $logData['updated_at'] = date('Y-m-d H:i:s');
            $this->store($this->ewaybillLog,$logData);
        endif;
        return $result;
    }

    public function ewbJsonSingle($ewbData){
        $orgData = $this->getCompanyInfo();
		$ref_id = $ewbData['ref_id'];
        $billData=array();$itemList=array();

        $invData = $this->salesInvoice->getInvoice($ref_id);        
        $transData = $invData->itemData;
                
        $partyData = $this->db->get_where('party_master',['id'=>$invData->party_id])->row();
        $cityData = $this->db->get_where('cities',['id'=>$partyData->city_id])->row();
        $partyStateCode = (!empty($partyData->gstin))?substr($partyData->gstin,0,2):"";

        $hsnCode = "";
        foreach($transData as $trans)
        {
            $sgstRate=0;$cgstRate=0;$igstRate=0;
            if(!empty($trans->gst_per)){
                $igstRate = round($trans->gst_per,2);
                $cgstRate = $sgstRate = round(($igstRate/2),2);
                if(empty($partyData->gstin)):
                    $igstRate=0;
                else:
                    if($partyStateCode==$orgData->company_state_code):
                        $igstRate=0;
                    else:
                        $sgstRate=0;$cgstRate=0;
                    endif;
                endif;
            }
            
            $itemList[]= [
                "productName"=> $trans->item_name,
                "productDesc"=> "", 
                "hsnCode"=> (!empty($trans->hsn_code))?$trans->hsn_code:"", 
                "quantity"=> $trans->qty,
                "qtyUnit"=> $trans->unit_name, 
                "taxableAmount"=> $trans->amount, 
                "sgstRate"=> $sgstRate, 
                "cgstRate"=> $cgstRate,
                "igstRate"=> $igstRate, 
                "cessRate"=> 0, 
                "cessNonAdvol"=> 0
            ];

            $hsnCode = (!empty($trans->hsn_code))?$trans->hsn_code:"";
        }
        
        $billData["supplyType"] = $ewbData['supply_type'];
        $billData["subSupplyType"] = $ewbData['sub_supply_type'];
        $billData["subSupplyDesc"] = "";
        $billData["docType"] = $ewbData['doc_type'];
        $billData["docNo"] = getPrefixNumber($invData->trans_prefix,$invData->trans_no);
        $billData["docDate"] = date("d/m/Y",strtotime($invData->trans_date));
        $billData["fromGstin"] = $orgData->company_gst_no;
        $billData["fromTrdName"] = $orgData->company_name;
        $billData["fromAddr1"] = $orgData->company_address;
        $billData["fromAddr2"] = "";
        $billData["fromPlace"] = $orgData->company_city;
        $billData["fromPincode"] = $orgData->company_pincode;
        $billData["fromStateCode"] = $orgData->company_state_code;
        $billData["actFromStateCode"] = $orgData->company_state_code;
        $billData["toGstin"] = (!empty($partyData->gstin))?$partyData->gstin:"URP";
        $billData["toTrdName"] = $partyData->party_name;
        $billData["toAddr1"] = (!empty($ewbData['ship_address']))?$ewbData['ship_address']:$partyData->party_address;
        $billData["toAddr2"] = "";
        $billData["toPlace"] = $cityData->name;
        $billData["toPincode"] = $ewbData['ship_pincode']; 
        $billData["toStateCode"] = $partyStateCode;
        $billData["actToStateCode"] = $partyStateCode;
        $billData['transactionType'] = $ewbData['transaction_type'];
        $billData['dispatchFromGSTIN'] = "";
        $billData['dispatchFromTradeName'] = "";
        $billData['shipToGSTIN'] = "";
        $billData['shipToTradeName'] = "";
        $billData["otherValue"] = ($invData->net_amount - ($invData->taxable_amount + $invData->igst_amount));
        $billData["totalValue"] = $invData->taxable_amount;
        $billData["cgstValue"] = (empty($partyStateCode))?$invData->cgst_amount:(($partyStateCode==$orgData->company_state_code)?$invData->cgst_amount:0);
        $billData["sgstValue"] = (empty($partyStateCode))?$invData->sgst_amount:(($partyStateCode==$orgData->company_state_code)?$invData->sgst_amount:0);
        $billData["igstValue"] = (empty($partyStateCode))?0:(($partyStateCode!=$orgData->company_state_code)?$invData->igst_amount:0);
        $billData["cessValue"] = 0;
        $billData['cessNonAdvolValue'] = 0;
        $billData["totInvValue"] = $invData->net_amount;
        $billData["transporterId"] = $ewbData['transport_id'];
        $billData["transporterName"] = $ewbData['transport_name'];
        $billData["transDocNo"] = $ewbData['transport_doc_no'];
        $billData["transMode"] = $ewbData['trans_mode']; 
        $billData["transDistance"] = $ewbData['trans_distance'];
        $billData["transDocDate"] = date("d/m/Y",strtotime($ewbData['transport_doc_date']));
        $billData["vehicleNo"] = $ewbData['vehicle_no'];
        $billData["vehicleType"] = $ewbData['vehicle_type'];
        $billData['mainHsnCode'] = (!empty($orgData->main_hsn_code))?$orgData->main_hsn_code:((!empty($hsnCode))? $hsnCode:"");
        $billData['itemList']=$itemList;
        
		return $billData;
    } 
    
    public function jobWorkChallanJson($ewbData){
        $orgData = $this->getCompanyInfo();
		$ref_id = $ewbData['ref_id'];
        $billData=array();$itemList=array();

        $challanData = $this->jobWorkVendor_v3->getVendorChallan($ref_id);
        $queryData = array();
        $queryData['tableName'] = "vendor_challan_trans";
        $queryData['select'] = "job_work_order.rate,job_bom.ref_item_id,job_bom.qty as bom_qty,job_work_order.rate_per,vendor_challan_trans.item_id as product_id,vendor_challan_trans.qty,vendor_challan_trans.w_pcs,vendor_challan_trans.weight,item_master.item_name,item_master.hsn_code,unit_master.unit_name,rm.gst_per";
        $queryData['leftJoin']['job_work_order'] = "job_work_order.id = vendor_challan_trans.jobwork_order_id";
        $queryData['leftJoin']['job_bom'] = "job_bom.job_card_id = vendor_challan_trans.job_card_id";
        $queryData['leftJoin']['item_master'] = "item_master.id = vendor_challan_trans.item_id";
        $queryData['leftJoin']['item_master rm'] = "rm.id = job_bom.ref_item_id";
        $queryData['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
        $queryData['where']['vendor_challan_trans.jobwork_order_id > ']  = 0;
        $queryData['where']['vendor_challan_trans.type']  = 1;
        $queryData['where_in']['vendor_challan_trans.challan_id'] = $challanData->id;
        $queryData['group_by'][] = 'vendor_challan_trans.id';
        $challanItems = $this->rows($queryData);
                
        $partyData = $this->db->get_where('party_master',['id'=>$challanData->vendor_id])->row();
        $cityData = $this->db->get_where('cities',['id'=>$partyData->city_id])->row();
        $partyStateCode = (!empty($partyData->gstin))?substr($partyData->gstin,0,2):"";

        $challanData->taxable_amount = 0;
        $challanData->cgst_amount = 0;
        $challanData->sgst_amount = 0;
        $challanData->igst_amount = 0;
        
        $hsnCode = "";
        foreach($challanItems as $row):
            $grnData = $this->storeReportModel->getLastGrnPrice(['item_id' => $row->ref_item_id]);
            $mtr_rate = ((!empty($grnData->price) && $grnData->price > 0) ? $grnData->price : 0);
            
            
            $sgstRate=0;$cgstRate=0;$igstRate=0;
            
            /*if($row->rate_per == 2 && $row->bom_qty > 0):
                $row->amount = round($row->ewb_value,2); */
                
                $row->qty = round($row->weight,3); //round(($row->qty * $row->bom_qty),3);
                $row->amount = round(($row->qty * $mtr_rate),2);
                $row->unit_name = "KGS";
                
            /*else:
                $row->amount = round(($row->qty * $mtr_rate),2);
                //$row->amount = round($row->ewb_value,2);
                $row->unit_name = "PCS";
            endif;*/
    
            if(!empty($row->gst_per) && $row->gst_per > 0):
                $igstRate = round($row->gst_per,2);
                $cgstRate = $sgstRate = round(($igstRate/2),2);
                if(empty($partyData->gstin)):
                    $igstRate=0;
                    $challanData->cgst_amount += round(((($row->amount * $row->gst_per) / 100)/2),2);
                    $challanData->sgst_amount += round(((($row->amount * $row->gst_per) / 100)/2),2);
                else:
                    if($partyStateCode == $orgData->company_state_code):
                        $igstRate=0;
                        $challanData->cgst_amount += round(((($row->amount * $row->gst_per) / 100)/2),2);
                        $challanData->sgst_amount += round(((($row->amount * $row->gst_per) / 100)/2),2);
                    else:
                        $sgstRate=0;$cgstRate=0;
                        $challanData->igst_amount += round((($row->amount * $row->gst_per) / 100),2);
                    endif;
                endif;
            endif;            

            $itemList[]= [
                "productName"=> $row->item_name,
                "productDesc"=> "", 
                "hsnCode"=> (!empty($row->hsn_code))?$row->hsn_code:"", 
                "quantity"=> $row->qty,
                "qtyUnit"=> $row->unit_name, 
                "taxableAmount"=> $row->amount, 
                "sgstRate"=> $sgstRate, 
                "cgstRate"=> $cgstRate,
                "igstRate"=> $igstRate, 
                "cessRate"=> 0, 
                "cessNonAdvol"=> 0
            ];

            $hsnCode = (!empty($row->hsn_code))?$row->hsn_code:"";
            $challanData->taxable_amount += $row->amount;           
            
        endforeach;


        $totalNetAmount = ($challanData->taxable_amount + $challanData->cgst_amount + $challanData->sgst_amount + $challanData->igst_amount);        
        $challanData->net_amount = round($totalNetAmount,0,PHP_ROUND_HALF_UP);
        $challanData->other_amount = round(($challanData->net_amount - $totalNetAmount),2);
        $challanData->other_amount = ($challanData->other_amount > 0)?$challanData->other_amount:0;

        $billData["supplyType"] = $ewbData['supply_type'];
        $billData["subSupplyType"] = $ewbData['sub_supply_type'];
        $billData["subSupplyDesc"] = "";
        $billData["docType"] = $ewbData['doc_type'];
        $billData["docNo"] = $challanData->trans_number;
        $billData["docDate"] = date("d/m/Y",strtotime($challanData->trans_date));
        $billData["fromGstin"] = $orgData->company_gst_no;
        $billData["fromTrdName"] = $orgData->company_name;
        $billData["fromAddr1"] = $orgData->company_address;
        $billData["fromAddr2"] = "";
        $billData["fromPlace"] = $orgData->company_city;
        $billData["fromPincode"] = $orgData->company_pincode;
        $billData["fromStateCode"] = $orgData->company_state_code;
        $billData["actFromStateCode"] = $orgData->company_state_code;
        $billData["toGstin"] = (!empty($partyData->gstin))?$partyData->gstin:"URP";
        $billData["toTrdName"] = $partyData->party_name;
        $billData["toAddr1"] = (!empty($ewbData['ship_address']))?$ewbData['ship_address']:$partyData->party_address;
        $billData["toAddr2"] = "";
        $billData["toPlace"] = $cityData->name;
        $billData["toPincode"] = $ewbData['ship_pincode']; 
        $billData["toStateCode"] = $partyStateCode;
        $billData["actToStateCode"] = $partyStateCode;
        $billData['transactionType'] = $ewbData['transaction_type'];
        $billData['dispatchFromGSTIN'] = "";
        $billData['dispatchFromTradeName'] = "";
        $billData['shipToGSTIN'] = "";
        $billData['shipToTradeName'] = "";
        $billData["otherValue"] = $challanData->other_amount;
        $billData["totalValue"] = $challanData->taxable_amount;
        $billData["cgstValue"] = $challanData->cgst_amount;
        $billData["sgstValue"] = $challanData->sgst_amount;
        $billData["igstValue"] = $challanData->igst_amount;
        $billData["cessValue"] = 0;
        $billData['cessNonAdvolValue'] = 0;
        $billData["totInvValue"] = $challanData->net_amount;
        $billData["transporterId"] = $ewbData['transport_id'];
        $billData["transporterName"] = $ewbData['transport_name'];
        $billData["transDocNo"] = $ewbData['transport_doc_no'];
        $billData["transMode"] = $ewbData['trans_mode']; 
        $billData["transDistance"] = $ewbData['trans_distance'];
        $billData["transDocDate"] = date("d/m/Y",strtotime($ewbData['transport_doc_date']));
        $billData["vehicleNo"] = $ewbData['vehicle_no'];
        $billData["vehicleType"] = $ewbData['vehicle_type'];
        $billData['mainHsnCode'] = (!empty($hsnCode))? $hsnCode:"";
        $billData['itemList']=$itemList;
        
		return $billData;
    }

    public function generateEwayBill($data,$authData){        		
        $authToken = $authData['token'];
        $fromGst = $authData['fromGst'];
        $euser = $authData['euser'];

        $test_link = "http://gstsandbox.charteredinfo.com/ewaybillapi/dec/v1.03/ewayapi?action=GENEWAYBILL&aspid=1674891122&password=Jp@94272&gstin=34AACCC1596Q002&username=TaxProEnvPON&authtoken=$authToken";

        $live_link = "https://einvapi.charteredinfo.com/v1.03/dec/ewayapi?action=GENEWAYBILL&aspid=1674891122&password=Jp@94272&gstin=$fromGst&username=$euser&authtoken=$authToken";

        $curlEwaybill = curl_init();
        curl_setopt_array($curlEwaybill, array(
            CURLOPT_URL => ($this->testMode == 1)?$test_link:$live_link,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
            CURLOPT_POSTFIELDS => json_encode($data['ewbJson'])
        ));

        $response = curl_exec($curlEwaybill);
        $error = curl_error($curlEwaybill);
        curl_close($curlEwaybill);

        if($error):
            $this->trash($this->ewaybillMaster,['id'=>$data['ewb_id']]);
            $ewayLog = [
                'id' => '',
                'type' => 2,
                'response_status'=> "Fail",
                'response_data'=> $response,
                'created_by'=> $this->session->userdata('loginId'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            $this->store($this->ewaybillLog,$ewayLog);

            return ['status'=>2,'message'=>'Somthing is wrong. cURL Error #:'. $error]; 
        else:
            $responseEwaybill = json_decode($response);					
            
            if(isset($responseEwaybill->status_cd) && $responseEwaybill->status_cd == 0):

                $this->trash($this->ewaybillMaster,['id'=>$data['ewb_id']]);

                $ewayLog = [
                    'id'=>"",
                    'type' => 2,
                    'response_status'=> "Fail",
                    'response_data'=> $response,
                    'created_by'=> $this->session->userdata('loginId'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                $this->store($this->ewaybillLog,$ewayLog);
                
                return ['status'=>2,'message'=>'Somthing is wrong. E-way Bill Error #: '. $responseEwaybill->error->message,'data'=>$data['ewbJson'] ];
            else:						

                $ewayNo = $responseEwaybill->ewayBillNo;
                $ewayBillDate = str_replace("/","-",$responseEwaybill->ewayBillDate);
                $validUpto = str_replace("/","-",$responseEwaybill->validUpto);
                $ewayLog = [
                    'id' => "",
                    'type' => 2,
                    'response_status'=> "Success",
                    'response_data'=> $response,
                    'created_by'=> $this->session->userdata('loginId'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                $this->store($this->ewaybillLog,$ewayLog);
                
                $ewbMasterData = [
                    'id' => $data['ewb_id'],
                    'ewb_status' => "Generated",
                    'eway_bill_no' => $ewayNo,
                    'eway_bill_date' => date("d-m-Y h:i:s a",strtotime($ewayBillDate)),
                    'valid_up_to' => date("d-m-Y h:i:s a",strtotime($validUpto))
                ];
                $this->store($this->ewaybillMaster,$ewbMasterData);

                if($data['doc_type'] == "INV"):
                    $this->edit($this->transMain,['id'=>$data['ref_id']],['ewb_status'=>1,'eway_bill_no'=>$ewayNo]);
                else:
                    $this->edit("vendor_challan",['id'=>$data['ref_id']],['eway_bill_no'=>$ewayNo]);
                endif;

                return ['status'=>1,'message'=>'E-way Bill Generated successfully.'];
            endif;
        endif;
    }

    public function einvJson($data){
        $orgData = $this->getCompanyInfo();
		$ref_id = $data['ref_id'];

        $invData = $this->salesInvoice->getInvoice($ref_id);
                
        $partyData = $this->db->get_where('party_master',['id'=>$invData->party_id])->row();
        $cityData = $this->db->get_where('cities',['id'=>$partyData->city_id])->row();
        $partyStateCode = (!empty($partyData->gstin))?substr($partyData->gstin,0,2):"";

        $einvData = array();
        $einvData["Version"] = "1.1";

        $einvData["TranDtls"] = [
            "TaxSch" => "GST", 
            "SupTyp" => "B2B", 
            "RegRev" => "N", 
            "EcmGstin" => null, 
            "IgstOnIntra" => "N" 
        ];

        $einvData["DocDtls"] = [
            "Typ" => $data['doc_type'], 
            "No" => $invData->trans_number, 
            "Dt" => date("d/m/Y",strtotime($invData->trans_date))
        ];

        $orgData->company_address = str_replace(["\r\n", "\r", "\n"], " ", $orgData->company_address);
        $orgAdd1 = substr($orgData->company_address,0,100);
        $orgAdd2 = (strlen($orgData->company_address) > 100)?substr($orgData->company_address,100,200):"";
        $orgData->company_contact = str_replace(["+"," "],"",$orgData->company_contact);
        $einvData["SellerDtls"] = [
            "Gstin" => $orgData->company_gst_no, 
            "LglNm" => $orgData->company_name,
            "TrdNm" => $orgData->company_name, 
            "Addr1" => $orgAdd1, 
            "Loc" => $orgData->company_city,  
            "Pin" => (int) $orgData->company_pincode,
            "Stcd" => $orgData->company_state_code, 
            "Ph" => $orgData->company_contact,  
            "Em" => $orgData->company_email
        ];
        if(strlen($orgAdd2)):
            $einvData["SellerDtls"]['Addr2'] = $orgAdd2;
        endif;

        $partyData->party_address = str_replace(["\r\n", "\r", "\n"], " ", $partyData->party_address);
        $partyAdd1 = substr($partyData->party_address,0,100);
        $partyAdd2 = (strlen($partyData->party_address) > 100)?substr($partyData->party_address,100,200):"";
        $einvData["BuyerDtls"] = [
            "Gstin" => $partyData->gstin, 
            "LglNm" => $partyData->party_name, 
            "TrdNm" => $partyData->party_name,
            "Pos" => $partyStateCode, 
            "Addr1" => $partyAdd1, 
            "Loc" => $cityData->name, 
            "Pin" => (int) $partyData->party_pincode, 
            "Stcd" => $partyStateCode, 
            "Ph" => trim(str_replace("+","",$partyData->party_mobile)), 
            "Em" => $partyData->contact_email
        ];
        if(strlen($partyAdd2)):
            $einvData["BuyerDtls"]['Addr2'] = $partyAdd2;
        endif;

        $einvData["DispDtls"] = [
            "Nm" => $orgData->company_name,
            "Addr1" => $orgAdd1,  
            "Loc" => $orgData->company_city,  
            "Pin" => (int) $orgData->company_pincode,
            "Stcd" => $orgData->company_state_code, 
        ];
        if(strlen($orgAdd2)):
            $einvData["DispDtls"]['Addr2'] = $orgAdd2;
        endif;

        $shippingAddress = (!empty($data['ship_address']))?$data['ship_address']:$partyData->party_address;
        $shippingAddress = str_replace(["\r\n", "\r", "\n"], " ", $shippingAddress);
        $shipAdd1 = substr($shippingAddress,0,100);
        $shipAdd2 = (strlen($shippingAddress) > 100)?substr($shippingAddress,100,200):"";
        $shipCode = (!empty($data['ship_pincode']))?$data['ship_pincode']:$partyData->party_pincode;
        $einvData["ShipDtls"] = [
            "Gstin" => $partyData->gstin,
            "LglNm" => $partyData->party_name,
            "TrdNm" => $partyData->party_name, 
            "Addr1" => $shipAdd1, 
            "Loc" => $cityData->name,
            "Pin" => (int) $shipCode,  
            "Stcd" => $partyStateCode
        ];
        if(strlen($shipAdd2)):
            $einvData["ShipDtls"]['Addr2'] = $shipAdd2;
        endif;

        $i=1;
        foreach($invData->itemData as $row):
            $cgst_amount = 0;
            $sgst_amount = 0;
            $igst_amount = 0;

            if($invData->gst_type == 1):
                $cgst_amount = $row->cgst_amount;
                $sgst_amount = $row->sgst_amount;                
            elseif($invData->gst_type == 2):
                $igst_amount = $row->igst_amount;
            endif;

            $einvData["ItemList"][] = [
                "SlNo" => strval($i++), 
                "PrdDesc" => $row->item_name, 
                "IsServc" => ($invData->sales_type == 3)?"Y":(($row->item_type == 10)?"Y":"N"),//($row->item_type == 10)?"Y":"N", 
                "HsnCd" => $row->hsn_code, //"9613",
                // "Barcde" => "123456", 
                "Qty" => round($row->qty,2), 
                "FreeQty" => 0, 
                "Unit" => $row->unit_name, 
                "UnitPrice" => round($row->price,2), 
                "TotAmt" => round($row->amount,2), 
                "Discount" => round($row->disc_amount,2), 
                // "PreTaxVal" => 1, 
                "AssAmt" => round($row->taxable_amount,2), 
                "GstRt" => round($row->gst_per,2), 
                "IgstAmt" => round($igst_amount,2), 
                "CgstAmt" => round($cgst_amount,2), 
                "SgstAmt" => round($sgst_amount,2), 
                // "CesRt" => 5, 
                // "CesAmt" => 498.94, 
                // "CesNonAdvlAmt" => 10, 
                // "StateCesRt" => 12, 
                // "StateCesAmt" => 1197.46, 
                // "StateCesNonAdvlAmt" => 5, 
                // "OthChrg" => 10, 
                "TotItemVal" => round($row->net_amount,2), 
                // "OrdLineRef" => "3256", 
                // "OrgCntry" => "AG", 
                // "PrdSlNo" => "12345", 
                // "BchDtls" => [
                //     "Nm" => "123456", 
                //     "Expdt" => "01/08/2020", 
                //     "wrDt" => "01/09/2020" 
                // ], 
                // "AttribDtls" => [
                //     [
                //         "Nm" => "Rice", 
                //         "Val" => "10000" 
                //     ] 
                // ] 
            ];
        endforeach;        

        $einvData["ValDtls"] = [
            "AssVal" => round($invData->taxable_amount,2), 
            "CgstVal" => round($invData->cgst_amount,2), 
            "SgstVal" => round($invData->sgst_amount,2), 
            "IgstVal" => round($invData->igst_amount,2),
            // "CesVal" => 508.94, 
            // "StCesVal" => 1202.46, 
            // "Discount" => floatVal($row->disc_amount), 
            "OthChrg" => round((($invData->net_amount + ($invData->round_off_amount * -1)) - ($invData->taxable_amount + $invData->gst_amount)),2), 
            "RndOffAmt" => round($invData->round_off_amount,2), 
            "TotInvVal" => round($invData->net_amount,2), 
            // "TotInvValFc" => 12897.7
        ];

        /* $einvData["PayDtls"] = [
            "Nm" => "ABCDE", 
            "Accdet" => "5697389713210", 
            "Mode" => "Cash", 
            "Fininsbr" => "SBIN11000", 
            "Payterm" => "100", 
            "Payinstr" => "Gift", 
            "Crtrn" => "test", 
            "Dirdr" => "test", 
            "Crday" => 100, 
            "Paidamt" => 10000, 
            "Paymtdue" => 5000
        ]; */

        /* $einvData["RefDtls"] = [
            "InvRm" => "TEST", 
            "DocPerdDtls" => [
                "InvStDt" => "01/08/2020", 
                "InvEndDt" => "01/09/2020" 
            ], 
            "PrecDocDtls" => [
                [
                    "InvNo" => "DOC/002", 
                    "InvDt" => "01/08/2020", 
                    "OthRefNo" => "123456" 
                ] 
            ], 
            "ContrDtls" => [
                [
                    "RecAdvRefr" => "Doc/003", 
                    "RecAdvDt" => "01/08/2020", 
                    "Tendrefr" => "Abc001", 
                    "Contrrefr" => "Co123", 
                    "Extrefr" => "Yo456", 
                    "Projrefr" => "Doc-456", 
                    "Porefr" => "Doc-789", 
                    "PoRefDt" => "01/08/2020" 
                ] 
            ]
        ]; */

        /* $einvData["AddlDocDtls"] = [
            [
                "Url" => "https://einv-apisandbox.nic.in", 
                "Docs" => "Test Doc", 
                "Info" => "Document Test" 
            ]
        ]; */

        /* $einvData["ExpDtls"] = [
            "ShipBNo" => "A-248", 
            "ShipBDt" => "01/08/2020", 
            "Port" => "INABG1", 
            "RefClm" => "N", 
            "ForCur" => "AED", 
            "CntCode" => "AE"
        ]; */

        if($data['ewb_status'] == 1):
            $einvData["EwbDtls"] = [
                "TransId" => $data['transport_id'],
                "TransName" => $data['transport_name'],
                "Distance" => $data['trans_distance'],
                "TransDocNo" => $data['transport_doc_no'],
                "TransDocDt" => date("d/m/Y",strtotime($data['transport_doc_date'])),
                "VehNo" => $data['vehicle_no'],
                "VehType" => $data['vehicle_type'],
                "TransMode" => $data['trans_mode']
            ];
        endif;

        return $einvData;
    }

    public function generateEinvoice($data,$authData){
        $authToken = $authData['token'];
        $fromGst = $authData['fromGst'];
        $euser = $authData['euser'];

        $test_link = "https://gstsandbox.charteredinfo.com/eicore/dec/v1.03/Invoice?aspid=1674891122&password=Jp@94272&Gstin=34AACCC1596Q002&AuthToken=$authToken&user_name=TaxProEnvPON&QrCodeSize=250";

        $live_link = "https://einvapi.charteredinfo.com/eicore/dec/v1.03/Invoice?aspid=1674891122&password=Jp@94272&Gstin=$fromGst&AuthToken=$authToken&user_name=$euser&QrCodeSize=250";        		
        
        $curlEwaybill = curl_init();
        curl_setopt_array($curlEwaybill, array(
            CURLOPT_URL => ($this->testMode == 1)?$test_link:$live_link,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
            CURLOPT_POSTFIELDS => json_encode($data['einvData'])
        ));

        $response = curl_exec($curlEwaybill);
        $error = curl_error($curlEwaybill);
        curl_close($curlEwaybill);

        if($error):
            $this->trash($this->ewaybillMaster,['id'=>$data['einv_id']]);
            $ewayLog = [
                'id' => '',
                'type' => 3,
                'response_status'=> "Fail",
                'response_data'=> json_encode(['error'=>'cURL Error #: ' . $error]),
                'created_by'=> $this->session->userdata('loginId'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            $this->store($this->ewaybillLog,$ewayLog);

            return ['status'=>2,'message'=>'Somthing is wrong. cURL Error #:'. $error]; 
        else:
            $responseEinv = json_decode($response);					
            
            if($responseEinv->Status != 1):

                $this->trash($this->ewaybillMaster,['id'=>$data['einv_id']]);

                $ewayLog = [
                    'id'=>"",
                    'type' => 3,
                    'response_status'=> "Fail",
                    'response_data'=> $response,
                    'created_by'=> $this->session->userdata('loginId'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                $this->store($this->ewaybillLog,$ewayLog);
                
                return ['status'=>2,'message'=>'Somthing is wrong. E-Invoice Error #: '. implode(", ",array_column($responseEinv->ErrorDetails,'ErrorMessage')),'data'=>$data['einvData'] ];
            else:
                $responseEinvData = json_decode($responseEinv->Data);	
                $ewayBillDate = (!empty($responseEinvData->EwbDt))?date("d-m-Y h:i:s a",strtotime(str_replace("/","-",$responseEinvData->EwbDt))):"";

                $validUpto = (!empty($responseEinvData->EwbValidTill))?date("d-m-Y h:i:s a",strtotime(str_replace("/","-",$responseEinvData->EwbValidTill))):"";

                $ackDate = (!empty($responseEinvData->AckDt))?date("d-m-Y h:i:s a",strtotime(str_replace("/","-",$responseEinvData->AckDt))):"";

                $responseEinv->Data = $responseEinvData;
                $ewayLog = [
                    'id' => "",
                    'type' => 3,
                    'response_status'=> "Success",
                    'response_data'=> json_encode($responseEinv),
                    'created_by'=> $this->session->userdata('loginId'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                $this->store($this->ewaybillLog,$ewayLog);
                
                $ewbMasterData = [
                    'id' => $data['einv_id'],
                    'ewb_status' => "Generated",
                    'ack_no' => $responseEinvData->AckNo,
                    'ack_date' => $ackDate,
                    'irn' => $responseEinvData->Irn,
                    'signed_invoice' => $responseEinvData->SignedInvoice,
                    'signed_qr_code' => $responseEinvData->SignedQRCode,
                    'status' => $responseEinvData->Status,
                    'eway_bill_no' => $responseEinvData->EwbNo,
                    'eway_bill_date' => $ewayBillDate,
                    'valid_up_to' => $validUpto,
                    'response_json' => json_encode($responseEinv)
                ];
                $this->store($this->ewaybillMaster,$ewbMasterData);

                $transMainData = [
                    'e_inv_status' => 1,
                    'e_inv_no' => $responseEinvData->AckNo
                ];
                if(!empty($responseEinvData->EwbNo)):
                    $transMainData['ewb_status'] = 1;
                    $transMainData['eway_bill_no'] = $responseEinvData->EwbNo;
                endif;

                $this->edit($this->transMain,['id'=>$data['ref_id']],$transMainData);

                return ['status'=>1,'message'=>'E-Invoice Generated successfully.'];
            endif;
        endif;
    }

    public function getEInvData($ack_no){
        $queryData = array();
        $queryData['tableName'] = $this->ewaybillMaster;
        $queryData['select'] = "response_json,party_id";
        $queryData['where']['ack_no'] = $ack_no;
        $result = $this->row($queryData);        
        $result->response_json = json_decode($result->response_json);
        //print_r($einvData->Data);exit;
        return $result;
    }
}
?>