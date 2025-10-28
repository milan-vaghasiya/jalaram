<?php
class LeadModel extends MasterModel{
    private $appointmentTable = "crm_appointments";
    private $partyMaster = "party_master";
    private $salesEnquiryMaster = "sales_enquiry";
    private $salesEnquiryTrans = "sales_enquiry_transaction";
    private $salesQuotation = "sales_quotation";
    private $salesQuotationTrans = "sales_quote_transaction";
    private $itemMaster = "item_master";
    private $countries = "countries";
    private $states = "states";
    private $cities = "cities";
    private $sales_logs = "sales_logs";


    public function getDTRows($data)
	{
		$data['tableName'] = $this->partyMaster;
		$data['select'] = 'party_master.*,countries.name as country_name';
		$data['leftJoin']['countries'] = 'countries.id = party_master.country_id';
		
        $data['where']['party_type'] = 2;

		$data['searchCol'][] = "";
		$data['searchCol'][] = "";
		$data['searchCol'][] = "party_name";
		$data['searchCol'][] = "contact_person";
		$data['searchCol'][] = "party_mobile";
		$data['searchCol'][] = "party_email";
		$data['searchCol'][] = "company_type";
		$data['searchCol'][] = "sector";
		$data['searchCol'][] = "source";
		$data['searchCol'][] = "countries.name";
		
		$columns = array('','','party_name','contact_person','party_mobile','party_email','company_type','sector','source','countries.name');

		if (isset($data['order'])) {
			$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
		}
		return $this->pagingRows($data);
	}

    public function getResponseDTRows($data)
	{
		$data['tableName'] = $this->sales_logs;
        $data['select'] = "sales_logs.*,party_master.party_name,party_master.contact_person,party_master.party_mobile";
        $data['leftJoin']['party_master'] = 'party_master.id = sales_logs.lead_id';
        $data['customWhere'][] = 'sales_logs.remark IS NULL';
        $data['where']['sales_logs.log_type'] = 3;

			$data['searchCol'][] = "";
			$data['searchCol'][] = "";
			$data['searchCol'][] = "party_master.party_name";
			$data['searchCol'][] = "party_master.contact_person";
			$data['searchCol'][] = "party_master.party_mobile";
			$data['searchCol'][] = "formatDate(sales_logs.ref_date)";
			$data['searchCol'][] = "sales_logs.reminder_time";
			$data['searchCol'][] = "sales_logs.mode";
            $data['searchCol'][] = "sales_logs.notes";

			$columns = array('', '', 'party_master.party_name', 'party_master.contact_person', 'party_master.party_mobile', 'formatDate(sales_logs.ref_date)','sales_logs.reminder_time','sales_logs.mode','sales_logs.notes');

		if (isset($data['order'])) {
			$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
		}
		$result =  $this->pagingRows($data);
        return $result;
    }

    public function getLead($id){
        $data['where']['id'] = $id;
        $data['tableName'] = $this->partyMaster;
        return $this->row($data);
    }    

    public function save($data){
        try{
            $this->db->trans_begin();
            if($this->checkDuplicate($data['party_name'],$data['id']) > 0):
                $errorMessage['party_name'] = "Company name is duplicate.";
                return ['status'=>0,'message'=>$errorMessage];
            else:
                if(empty($data['id'])):
    				$data['created_by'] = $this->loginId;
    			else:
    				$data['updated_by'] = $this->loginId;
    			endif;
			
                $result = $this->store($this->partyMaster,$data,'Lead');
            endif;
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
        return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function checkDuplicate($name,$id=""){
        $data['tableName'] = $this->partyMaster;
        $data['where']['party_name'] = $name;
        // $data['where']['party_category'] = 1;
        $data['where']['party_type'] = 2;
        
        if(!empty($id))
            $data['where']['id !='] = $id;
        return $this->numRows($data);
    }
	
    public function getAppointments($lead_id){
        $data['tableName'] = $this->appointmentTable;
        $data['where']['lead_id'] = $lead_id;
        return $this->rows($data);
    }

    public function setAppointment($data){
		$appointment_status = 0;
		$queryData['where']['lead_id'] = $data['lead_id'];
        $queryData['tableName'] = $this->appointmentTable;
        $prev_appointments = $this->rows($queryData);
        $appointment_status = count($prev_appointments);
        $result = $this->store($this->appointmentTable,$data,'Appointment');
		
		return  $result;
    }

    public function deleteAppointment($id){
        try{
            $this->db->trans_begin();
            
            $result = $this->trash($this->appointmentTable,['id'=>$id],'Appointment');
            
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function updateAppointmentStatus($data){
        try{
            $this->db->trans_begin();
            
            $result = $this->store($this->appointmentTable,$data,'Appointment');
       
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function saveSalesLog($data){
        try{
            $this->db->trans_begin();

            $result = $this->store($this->sales_logs,$data);

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }

    public function getSalesLog($data){
        $queryData = array();
        $queryData['tableName'] = $this->sales_logs;
        $queryData['select'] = "sales_logs.*,party_master.party_name,party_master.party_type";
        $queryData['leftJoin']['party_master'] = "party_master.id = sales_logs.lead_id AND party_type = 2";
        if(!empty($data['lead_id'])){ $queryData['where']['sales_logs.lead_id'] = $data['lead_id']; }
        if(!empty($data['log_type'])){ 
            $queryData['where']['sales_logs.log_type'] = $data['log_type'];
        }
        $queryData['order_by']['sales_logs.ref_date'] = 'ASC';
        $result =  $this->rows($queryData);
        return $result;
    }

    public function deleteSalesLog($id){
		try{
            $this->db->trans_begin();
			
			$result = $this->trash($this->sales_logs,['id'=>$id],'Sales Log');
			if ($this->db->trans_status() !== FALSE):
				$this->db->trans_commit();
				return $result;
			endif;
		}catch(\Exception $e){
			$this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
		}	
	}
}
?>