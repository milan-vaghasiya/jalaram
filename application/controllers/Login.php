<?php

class Login extends CI_Controller{
	public function __construct(){
		parent::__construct();
		$this->load->model('LoginModel','login_model');
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters( '<div class="error">', '</div>' );
		$loginSettings = $this->login_model->getLoginSettings(); //print_r(explode(",",$loginSettings->static_ip));exit;
		$this->config->set_item('STATIC_IP', explode(",",$loginSettings->static_ip));
		$this->config->set_item('login_start_time', $loginSettings->login_start_time);
		$this->config->set_item('login_end_time', $loginSettings->login_end_time);
	}
	
	public function index(){	
		if($this->session->userdata('LoginOk')):
			redirect( base_url('dashboard') );
		else:
			$this->load->view('login');
		endif;
	}
	
	public function auth(){
		if($this->session->userdata('LoginOk')):
			redirect( base_url('dashboard') );
		else:
			$data = $this->input->post();
		
			$this->form_validation->set_rules('user_name','Username','required|trim');
			$this->form_validation->set_rules('password','Password','required|trim');
			if($this->form_validation->run() == true):
				$result = $this->login_model->checkAuth($data);
				if($result['status'] == 1):
					/* return redirect( base_url('dashboard') ); */
					if($result['otp_status'] == 2):
						$this->load->view('page-403');
					elseif($result['otp_status'] == 1):
						redirect( base_url('login/verifyOTP'));
					else:
						return redirect( base_url('dashboard') );
					endif;
				else:
					$this->session->set_flashdata('loginError',$result['message']);
					redirect( base_url('login') , 'refresh');
				endif;
			else:
				$this->load->view('login');
			endif;
		endif;
	}

	public function verifyOTP(){
	    if($this->session->userdata('LoginOk')):
			redirect( base_url('dashboard') );
		else:
		    $data = $this->input->post();
			$this->form_validation->set_rules('emp_id','Emp ID','required|trim');
			$this->form_validation->set_rules('web_otp','OTP','required|trim');
			if($this->form_validation->run() == true):
			    $result = $this->login_model->verifyOTP($data);
				if($result['status'] == 1):
					return redirect( base_url('dashboard') );
				else:
					$data['otpError'] = $result['message'];
					$data['emp_id'] = $this->session->userdata('loginId');
				    $this->load->view('otp_verify',$data);
				endif;
			else:
			    $otpResponse = $this->sendOTP(1);
			    $data['emp_id'] = $this->session->userdata('loginId');
				$data['otpMessage'] = $otpResponse['message'];
				$this->load->view('otp_verify',$data);
			endif;
		endif;
	}

	public function sendOTP($resend=""){
	    $otp = rand(000000,999999);

		$empId = $this->session->userdata('loginId');
	    $empName = $this->session->userdata('emp_name');
		$userIP = $this->session->userdata('user_ip');

		//Send SMS
	    /* $message = "Dear ".$empName.", your login OTP is ".$otp.".-Nativebit Technologies";
        $mobileNo = $this->session->userdata('otp_mobile_no');

        $smsResponse = sendSMS(["mobile_no"=>$mobileNo,"message"=>$message]); */

		//Send Email
		$emailData['from'] = "support@nativebittechnologies.com";
		$emailData['fromName'] = "NATIVEBIT TECHNOLOGIES";
		$emailData['to'] = "rakeshkantaria74@gmail.com";
		$emailData['subject'] = "Verify New Login from an unauthorized location";

		$message = '<!DOCTYPE html><html>
			<head>
				<meta charset="UTF-8">
				<meta name="viewport" content="width=device-width, initial-scale=1">
				<title>Verify New Login</title>
				<style>
					body {
						font-family: Arial, sans-serif;
						background-color: #f4f4f4;
						margin: 0;
						padding: 0;
					}
					.container {
						max-width: 600px;
						background: #ffffff;
						margin: 20px auto;
						padding: 20px;
						border-radius: 10px;
						box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
					}
					.header {
						text-align: center;
						padding-bottom: 20px;
						border-bottom: 2px solid #ddd;
					}
					.header img {
						max-width: 150px;
					}
					.content {
						padding: 20px;
						text-align: center;
					}
					.code {
						font-size: 22px;
						font-weight: bold;
						background: #f8f9fa;
						padding: 10px;
						border-radius: 5px;
						display: inline-block;
						letter-spacing: 4px;
						margin-top: 10px;
						color: #333;
					}
					.footer {
						margin-top: 30px;
						padding-top: 20px;
						text-align: center;
						font-size: 14px;
						color: #777;
						border-top: 2px solid #ddd;
					}
					.footer strong {
						color: #5a0e5a;
					}
				</style>
			</head>
			<body>

			<div class="container">
				<div class="header">
					<img src="'.base_url("assets/images/nbt_logo.png").'" alt="NativeBit Technologies LLP">
				</div>
				
				<div class="content">
					<h2>Verify New Login from an Unauthorized Location</h2>
					<p>We are sending this email to inform you about a <b>NEW LOGIN</b> from an unauthorized location by <b>'.$empName.' (IP : '.$userIP.')</b>.</p>
					<p>To verify & grant access, enter the code below:</p>
					
					<div class="code">'.$otp.'</div>
					
					<p>This code will expire three hours after this email was sent.</p>
				</div>
				
				<div class="footer">
					<p><strong>Thanks & Regards,</strong></p>
					<p><strong>NATIVEBIT TECHNOLOGIES LLP</strong></p>
					<p>707, R K SUPREME, OPP. TWINS TOWER,<br>
					NANA MAVA CIRCLE, 150 FEET RING ROAD,<br>
					RAJKOT, Gujarat, 360001</p>
					<p>📞 +91 99789 85775 | +91 94272 35336</p>
				</div>
			</div>

			</body>
		</html>';

		$emailData['message'] = $message;
		$emailData['mail_format'] = "html";
		$emailData['mail_type'] = 8; //OTP Email.

		$sendEmailRes = sendEmail($emailData);

		$this->db->where('id',$empId)->update("employee_master",['OTP'=>$otp]);

        return ['status'=>1,'message'=>'OTP sent successfully to <strong>'.substr_replace($emailData['to'],"xxxxxxx",1,7).'</strong>'];
	}
	
	public function logout(){
		$emp_id = $this->session->userdata('loginId');
		$this->login_model->webLogout($emp_id);
		$this->session->sess_destroy();
		return redirect(base_url());
	}

	public function setFinancialYear(){
		$year = $this->input->post('year');
		$this->login_model->setFinancialYear($year);
		echo json_encode(['status'=>1,'message'=>'Financial Year changed successfully.']);
	}

	public function testOtp(){
		$otp = rand(000000,999999);
	    $empName = $this->session->userdata('emp_name');
		$userIP = $this->session->userdata('user_ip');

	    /* $message = "Dear ".$empName.", your login OTP is ".$otp.".-Nativebit Technologies";
        $mobileNo = "9016929406";//$this->session->userdata('otp_mobile_no');

        $smsResponse = sendSMS(["mobile_no"=>$mobileNo,"message"=>$message]);

		print_r(['status'=>1,'message'=>'OTP sent successfully to <strong>'.substr_replace($mobileNo,"xxxxxxx",1,7).'</strong>','sms_res'=>$smsResponse]);exit; */

		$emailData['from'] = "info@nativebittechnologies.com";
		$emailData['fromName'] = "NATIVEBIT TECHNOLOGIES";
		$emailData['to'] = "milanchauhan134@gmail.com,jagdishpatelsoft@gmail.com";//"rakeshkantaria74@gmail.com";
		$emailData['subject'] = "Verify New Login from an unauthorized location";
		
		$empName = 'Milan Chauhan';//$this->session->userdata('emp_name');

		$message = '<!DOCTYPE html><html>
			<head>
				<meta charset="UTF-8">
				<meta name="viewport" content="width=device-width, initial-scale=1">
				<title>Verify New Login</title>
				<style>
					body {
						font-family: Arial, sans-serif;
						background-color: #f4f4f4;
						margin: 0;
						padding: 0;
					}
					.container {
						max-width: 600px;
						background: #ffffff;
						margin: 20px auto;
						padding: 20px;
						border-radius: 10px;
						box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
					}
					.header {
						text-align: center;
						padding-bottom: 20px;
						border-bottom: 2px solid #ddd;
					}
					.header img {
						max-width: 150px;
					}
					.content {
						padding: 20px;
						text-align: center;
					}
					.code {
						font-size: 22px;
						font-weight: bold;
						background: #f8f9fa;
						padding: 10px;
						border-radius: 5px;
						display: inline-block;
						letter-spacing: 4px;
						margin-top: 10px;
						color: #333;
					}
					.footer {
						margin-top: 30px;
						padding-top: 20px;
						text-align: center;
						font-size: 14px;
						color: #777;
						border-top: 2px solid #ddd;
					}
					.footer strong {
						color: #5a0e5a;
					}
				</style>
			</head>
			<body>

			<div class="container">
				<div class="header">
					<img src="'.base_url("assets/images/nbt_logo.png").'" alt="NativeBit Technologies LLP">
				</div>
				
				<div class="content">
					<h2>Verify New Login from an Unauthorized Location</h2>
					<p>We are sending this email to inform you about a <b>NEW LOGIN</b> from an unauthorized location by <b>'.$empName.' (IP : '.$userIP.')</b>.</p>
					<p>To verify & grant access, enter the code below:</p>
					
					<div class="code">'.$otp.'</div>
					
					<p>This code will expire three hours after this email was sent.</p>
				</div>
				
				<div class="footer">
					<p><strong>Thanks & Regards,</strong></p>
					<p><strong>NATIVEBIT TECHNOLOGIES LLP</strong></p>
					<p>707, R K SUPREME, OPP. TWINS TOWER,<br>
					NANA MAVA CIRCLE, 150 FEET RING ROAD,<br>
					RAJKOT, Gujarat, 360001</p>
					<p>📞 +91 99789 85775 | +91 94272 35336</p>
				</div>
			</div>

			</body>
		</html>';

		$emailData['message'] = $message;
		$emailData['mail_format'] = "html";
		$emailData['mail_type'] = 8; //OTP Email.

		//$emailData['attachment'] = [base_url("assets/images/nbt_logo.png"),base_url("assets/images/logo.png")];

		$sendEmailRes = sendEmail($emailData); 

		//$this->db->where('id',$this->session->userdata('loginId'))->update("employee_master",['OTP'=>$otp]);

        print_r(['status'=>1,'message'=>'OTP sent successfully to <strong>'.substr_replace($emailData['to'],"xxxxxxx",1,7).'</strong>','email_res'=>$sendEmailRes]);exit;
	}
}
?>