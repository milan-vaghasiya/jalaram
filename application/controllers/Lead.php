<?php
class Lead extends MY_Controller
{
    private $indexPage = "lead/index";
    private $leadForm = "lead/lead_form";
    private $gstForm = "lead/gst_form";
	private $contactForm = "lead/contact_form";
	private $reminderForm = "lead/reminder_form";
	private $followUpForm = "lead/follow_up";
	private $reminder_response = "lead/reminder_response";
    private $appointmentMode = Array("Email","Online","Visit","Phone","Other");

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Lead";
		$this->data['headData']->controller = "lead";
		$this->data['headData']->pageUrl = "lead";
	}
	
	public function index($status = 0){
		$this->data['status'] = $status;
		if(empty($status)){
            $this->data['headData']->pageTitle = "Lead";
			$this->data['tableHeader'] = getSalesDtHeader("lead");
        }else{
			$this->data['headData']->pageTitle = "Pending Response";
			$this->data['tableHeader'] = getSalesDtHeader("pendingResponse");
		}
		$this->load->view($this->indexPage, $this->data);
    }

	public function getDTRows($status=0)
    {
        $data=$this->input->post();
		$data['status'] = $status;
	
		if(empty($status)):
			$result = $this->leads->getDTRows($data);
        else:
            $result = $this->leads->getResponseDTRows($data);
        endif;
        
        $sendData = array(); $i=1;
        
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
			$row->status = $status;
            
            if(empty($status)):
                $sendData[] = getLeadData($row);
			else:
                $sendData[] = getPendingResponseData($row);
			endif;
			
        endforeach;
        
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addLead(){
        $this->data['countryData'] = $this->party->getCountries();
        $this->load->view($this->leadForm,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['party_name']))
            $errorMessage['party_name'] = "Company name is required.";
        if(empty($data['contact_person']))
            $errorMessage['contact_person'] = "Contact Person is required.";
        if(empty($data['party_mobile']))
            $errorMessage['party_mobile'] = "Contact No. is required.";
        if(empty($data['country_id']))
			$errorMessage['country_id'] = 'Country is required.';
        if(empty($data['state_id']))
        {
            if(empty($data['statename']))
                $errorMessage['state_id'] = 'State is required.';
            else
                $data['state_id'] = $this->party->saveState($data['statename'],$data['country_id']);
        }
        unset($data['statename']);
        if(empty($data['city_id']))
        {
            if(empty($data['ctname']))
                $errorMessage['city_id'] = 'City is required.';
            else
                $data['city_id'] = $this->party->saveCity($data['ctname'],$data['state_id'],$data['country_id']);
        }
        unset($data['ctname']);
        if(empty($data['party_address']))
            $errorMessage['party_address'] = "Address is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['party_name'] = ucwords($data['party_name']);
            $this->printJson($this->leads->save($data));
        endif;
    }

    public function edit(){     
        $id = $this->input->post('id');
        $result = $this->leads->getLead($id);
        $result->state = $this->party->getStates($result->country_id,$result->state_id)['result'];
        $result->city = $this->party->getCities($result->state_id,$result->city_id)['result'];
        $this->data['dataRow'] = $result;
        $this->data['countryData'] = $this->party->getCountries();        
        $this->load->view($this->leadForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->party->delete($id));
        endif;
    }

    public function getStates(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
        else:
            $this->printJson($this->party->getStates($id));
        endif;
    }

    public function getCities(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
        else:
            $this->printJson($this->party->getCities($id));
        endif;
    }
	   
    public function getContactDetail(){
        $party_id = $this->input->post('id');
        $result = $this->party->getParty($party_id);
        $this->data['contact_detail'] = json_decode($result->contact_detail);
        $this->data['party_id'] = $party_id;
        $this->load->view($this->contactForm,$this->data);
    }

    public function saveContact(){
        $data = $this->input->post();
		$errorMessage = array();
		if(empty($data['person']))
			$errorMessage['person'] = "Contact Person is required.";
        if(empty($data['mobile']))
			$errorMessage['mobile'] = "Contact Mobile is required.";
        if(empty($data['email']))
			$errorMessage['email'] = "Contact Email is required.";
		
		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
            $data['contact_detail'] =  [
                'person' => $data['person'],
                'mobile' => $data['mobile'],
                'email' => $data['email']
            ];
            $response = $this->party->saveContact($data);

            $result = $this->party->getParty($data['party_id']);
            $contact_detail = json_decode($result->contact_detail);
            $i=1; $tbodyData=""; $arrCount = count($contact_detail);
            if(!empty($contact_detail)) :
                foreach ($contact_detail as $row) :
                    $tbodyData.= '<tr>
                            <td>'.$i.'</td>
                            <td>'.$row->person.'</td>
                            <td>'.$row->mobile.'</td>
                            <td>'.$row->email.'</td>
                            <td class="text-center">';
                    if($arrCount == $i):
                        $tbodyData.= '<a href="javascript:void(0);" class="btn btn-outline-danger btn-delete" onclick="trashContact();"><i class="ti-trash"></i></button>';
                    endif;
                    $tbodyData.='</td></tr>'; $i++;
                endforeach;
            else:
                $tbodyData.= '<tr><td colspan="5" style="text-align:center;">No Data Found</td></tr>';
            endif;
			$this->printJson(['status'=>1, "tbodyData"=>$tbodyData, "partyId"=>$data['party_id']]);
		endif;
    }

    public function deleteContact(){
        $party_id = $this->input->post('id');
        if(empty($party_id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->party->deleteContact($party_id);

            $result = $this->leads->getLead($party_id);
            $contact_detail = json_decode($result->contact_detail);
            $i=1; $tbodyData=""; $arrCount = count($contact_detail);
            if(!empty($contact_detail)) :
                foreach ($contact_detail as $row) :
                    $tbodyData.= '<tr>
                        <td>'.$i.'</td>
                        <td>'.$row->person.'</td>
                        <td>'.$row->mobile.'</td>
                        <td>'.$row->email.'</td>
                        <td class="text-center">';
                    if($arrCount == $i):
                        $tbodyData.= '<a href="javascript:void(0);" class="btn btn-outline-danger btn-delete" onclick="trashContact();"><i class="ti-trash"></i></button>';
                    endif;
                    $tbodyData.='</td></tr>'; $i++;
                endforeach;
            else:
                $tbodyData.= '<tr><td colspan="5" style="text-align:center;">No Data Found</td></tr>';
            endif;
			$this->printJson(['status'=>1, "tbodyData"=>$tbodyData, "partyId"=>$party_id]);
        endif;
    }

    public function getPartyContactDetail(){
        $party_id = $this->input->post('party_id');
        $result = $this->leads->getLead($party_id);
        $data['contact_detail'] = ""; $conArr = Array();
        $conArr[0] =  [
            'person' => $result->contact_person,
            'mobile' => $result->party_mobile,
            'email' => $result->contact_email
        ];

        if(!empty($result->contact_detail)):
            $cData = json_decode($result->contact_detail); $i=1;
            foreach($cData as $row):
                $conArr[$i] =  [
                    'person' => $row->person,
                    'mobile' => $row->mobile,
                    'email' => $row->email
                ]; $i++;
            endforeach;
        endif;

        $person = ""; $mobile=""; $email="";
        foreach($conArr as $row):
            if(!empty($row['person'])){ $person.='<option value="'.$row['person'].'">'.$row['person'].'</option>'; }
            if(!empty($row['mobile'])){ $mobile.='<option value="'.$row['mobile'].'">'.$row['mobile'].'</option>'; }
            if(!empty($row['email'])){ $email.='<option value="'.$row['email'].'">'.$row['email'].'</option>'; }
        endforeach;
        
        if(empty($person)){ $person = '<option value="">Select Contact person </option>'; }
        if(empty($mobile)){ $mobile = '<option value="">Select Contact mobile </option>'; }
        if(empty($email)){ $email = '<option value="">Select Contact Email </option>'; }
        
        $this->printJson(['status' => 1, 'person' => $person, 'mobile' => $mobile, 'email' => $email]);
    }
    
    //Created BY Mansee @ 25-12-2021
    public function getGstDetail()
    {
        $party_id = $this->input->post('id');
        $result = $this->leads->getLead($party_id);
        $this->data['json_data'] = json_decode($result->json_data);
        $this->data['party_id'] = $party_id;
        $this->load->view($this->gstForm, $this->data);
    }
    
    //Created BY Mansee @ 25-12-2021
    public function saveGst()
    {
        $data = $this->input->post();

        $errorMessage = array();
        if (empty($data['gstin']))
            $errorMessage['gstin'] = "GST is required.";
        if (empty($data['delivery_address']))
            $errorMessage['delivery_address'] = "Address is required.";
        if (empty($data['delivery_pincode']))
            $errorMessage['delivery_pincode'] = "Pincode is required.";

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :

            $response = $this->party->saveGst($data);

            $result = $this->leads->getLead($data['party_id']);
            $json_data = json_decode($result->json_data);
            $i = 1;
            $tbodyData = "";
            if (!empty($json_data)) :
                foreach ($json_data as $key => $row) :
                    $tbodyData .= '<tr>
                                <td>' .  $i++ . '</td>
                                <td>' . $key . '</td>
                                <td>' . $row->party_address . '</td>
                                <td>' . $row->party_pincode . '</td>
                                <td>' . $row->delivery_address . '</td>
                                <td>' . $row->delivery_pincode . '</td>
                                <td class="text-center">
                                <a href="javascript:void(0);" class="btn btn-outline-danger btn-delete" onclick="trashGst(\'' . $key . '\')"><i class="ti-trash"></i></a>
                                </td>
                            </tr> ';
                endforeach;
            else :
                $tbodyData .= '<tr><td colspan="5" style="text-align:center;">No Data Found</td></tr>';
            endif;
            $this->printJson(['status' => 1, "tbodyData" => $tbodyData, "partyId" => $data['party_id']]);
        endif;
    }
    
    //Created BY Mansee @ 25-12-2021
    public function deleteGst()
    {
        $party = $this->input->post();
        if (empty($party['id'])) :
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else :
            $this->party->deleteGst($party['id'], $party['gstin']);

            $result = $this->leads->getLead($party['id']);
            $json_data = json_decode($result->json_data);
            $i = 1;
            $tbodyData = "";
            if (!empty($json_data)) :
                foreach ($json_data as $key => $row) :
                    $tbodyData .= '<tr>
                                <td>' .  $i++ . '</td>
                                <td>' . $key . '</td>
                                <td>' . $row->party_address . '</td>
                                <td>' . $row->party_pincode . '</td>
                                <td>' . $row->delivery_address . '</td>
                                <td>' . $row->delivery_pincode . '</td>
                                <td class="text-center">
                                <a href="javascript:void(0);" class="btn btn-outline-danger btn-delete" onclick="trashGst(\'' . $key . '\');"><i class="ti-trash"></i></a>
                                </td>
                            </tr> ';
                endforeach;
            else :
                $tbodyData .= '<tr><td colspan="5" style="text-align:center;">No Data Found</td></tr>';
            endif;
            $this->printJson(['status' => 1, "tbodyData" => $tbodyData, "partyId" => $party['id']]);
        endif;
    }

	public function getAppointments(){
        $this->data['appointmentMode'] = $this->appointmentMode;
        $this->data['appintmentData'] = $this->leads->getAppointments($this->input->post('lead_id'));
        $this->load->view('lead/appointment_form',$this->data);
    }

    public function setAppointment(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['appointment_date']))
            $errorMessage['appointment_date'] = "Date is required.";
        if(empty($data['appointment_time']))
            $errorMessage['appointment_time'] = "Time is required.";
        if(empty($data['contact_person']))
            $errorMessage['contact_person'] = "Contact Person is required.";
		
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['contact_person'] = ucwords($data['contact_person']);
			$data['appointment_date'] = formatDate($data['appointment_date'],'Y-m-d');
			$data['appointment_time'] = formatDate($data['appointment_time'],'h:i:s');
            $this->printJson($this->leads->setAppointment($data));
        endif;
    }

    public function deleteAppointment(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->leads->deleteAppointment($id));
        endif;
    }

	public function getReminder(){
		$lead_id = $this->input->post('id');
		$this->data['lead_id'] = $lead_id;
		$this->data['mode'] = $this->appointmentMode;
		$this->data['tbody'] = $this->getReminderListHtml($lead_id);
		$this->load->view($this->reminderForm,$this->data);
	}

	public function getReminderListHtml($lead_id){
		$reminderData = $this->leads->getSalesLog(['lead_id'=>$lead_id,'log_type'=>3]);
		$tbody='';$i=1;
		if(!empty($reminderData)):
			foreach($reminderData as $row):
				$tbody .='<tr>
							<td>'.$i++.'</td>
							<td>'.$row->ref_date.'</td>
							<td>'.$row->reminder_time.'</td>
							<td>'.$row->mode.'</td>
							<td>'.$row->notes.'</td>
							<td class="text-center"><a class="btn btn-outline-danger btn-sm btn-delete" href="javascript:void(0)" onclick="trashReminder('.$row->id.','.$row->lead_id.','.$row->log_type.');" datatip="Remove" flow="left"><i class="ti-trash"></i></a></td>
						</tr>';
			endforeach;
		endif;
		return $tbody;
	}

	public function getFollowUp(){
		$lead_id = $this->input->post('id');
		$this->data['lead_id'] = $lead_id;
		$this->data['tbody'] = $this->getFollowUpHtml($lead_id);
		$this->load->view($this->followUpForm,$this->data);
	}

	public function getFollowUpHtml($lead_id){
		$followUpData = $this->leads->getSalesLog(['lead_id'=>$lead_id,'log_type'=>2]);
		$tbody='';$i=1;
		if(!empty($followUpData)):
			foreach($followUpData as $row):
				$tbody .='<tr>
					<td>'.$i++.'</td>
					<td>'.$row->notes.'</td>
					<td class="text-center"><a class="btn btn-outline-danger btn-sm btn-delete" href="javascript:void(0)" onclick="trashFollowUp('.$row->id.','.$row->lead_id.','.$row->log_type.');" datatip="Remove" flow="left"><i class="ti-trash"></i></a></td>
				</tr>';
			endforeach;
		endif;
		return $tbody;
	}
	
	public function saveSalesLog(){
		$postData = $this->input->post();
		$errorMessage = array();
		if($postData['log_type'] == 3){
            if(empty($postData['ref_date']))
                $errorMessage['ref_date'] = "Date is required.";
            if(empty($postData['reminder_time']))
                $errorMessage['reminder_time'] = "Time is required.";
            if(empty($postData['notes']))
                $errorMessage['notes'] = "Notes is required.";
        }
		if($postData['log_type'] == 2){
            if(empty($postData['notes']))
                $errorMessage['notes'] = "Notes is required.";
        }
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
			$postData['created_by'] = $this->session->userdata('loginId');
			$result = $this->leads->saveSalesLog($postData);
			if($postData['log_type'] == 3){
				$tbody = $this->getReminderListHtml($postData['lead_id']);
			}else{
				$tbody = $this->getFollowUpHtml($postData['lead_id']);
			}
			$this->printJson(['status'=>1,'tbody'=>$tbody]);
		endif;
	}

	public function deleteSaleslog(){
		$data = $this->input->post();
		if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
			$result =  $this->leads->deleteSalesLog($data['id']);
			if($data['log_type'] == 3){
				$tbody = $this->getReminderListHtml($data['lead_id']);
			}else{
				$tbody = $this->getFollowUpHtml($data['lead_id']);
			}
            $this->printJson(['status'=>1,'tbody'=>$tbody]);
        endif;
	}
	
	public function getResponse(){
        $id = $this->input->post('id');
        $this->data['id'] = $id;
        $this->load->view($this->reminder_response,$this->data);
    }

	public function saveResponse(){
		$postData = $this->input->post();
		$errorMessage = array();
		if($postData['log_type'] == 3){
            if(empty($postData['remark']))
                $errorMessage['remark'] = "response is required.";
        }
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
			$postData['updated_by'] = $this->session->userdata('loginId');
			$this->printJson($this->leads->saveSalesLog($postData));
		endif;
	}


}
?>