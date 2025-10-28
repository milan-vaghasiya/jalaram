<?php

class EmailModel extends MasterModel{
	
    private $emailLogs = "email_logs";
	
	public function sendMail($postData,$attachmentArray)
	{
	    //$attachmentArray = Array();
	    $this->load->library('encrypt');
	    $this->load->library('email');
		$this->email->clear(TRUE);

		/*$emailConfig = Array(
			'protocol' 	=> 'smtp',
			'smtp_host' => 'smtp.gmail.com',
			'smtp_port' => 587,
			'smtp_user' => 'nativebitoffice@gmail.com',
			'smtp_pass' => 'nboimmgnsxsmyqvn',
			'mailtype'  => 'html', 
			'charset'   => 'utf-8'
		);*/

		$this->email->set_newline("\r\n");
		$sent_to = explode(',',$postData['receiver_email']);

		if(!empty($postData['receiver_email'])){
			$this->email->from($postData['sender_email'], 'JAY JALARAM PRECISION COMPONENT LLP');
			$this->email->to($postData['receiver_email']);
			if(!empty($postData['cc_email'])){$this->email->cc($postData['cc_email']);}
			if(!empty($postData['bcc_email'])){$this->email->bcc($postData['bcc_email']);}

			$this->email->subject($postData['subject']);
			$this->email->message($postData['mail_body']);
			if(!empty($attachmentArray)){
				foreach($attachmentArray as $file_name){
					if(!empty($file_name)){
						$this->email->attach($file_name);
					}
				}
			}
            $this->email->set_header('X-Confirm-Reading-To', $postData['sender_email']);
			$this->email->set_mailtype('html');
			if($this->email->send()){
				$mailData['mail_type'] = $postData['mail_type'];
				$mailData['sender_email'] = $postData['sender_email'];
				$mailData['receiver_email'] = $postData['receiver_email'];
				$mailData['subject'] = $postData['subject'];
				$mailData['mail_body'] = $postData['mail_body'];
				$mailData['ref_id'] = $postData['ref_id'];
				$mailData['ref_no'] = $postData['ref_no'];
				$mailData['created_by'] = (isset($postData['created_by']))?$postData['created_by']:$this->loginID;
				$this->db->insert($this->emailLogs,$mailData);
				
				return ['status'=>1,"message"=>"Email has been successfully Sent to ".$mailData['receiver_email']];
			}else{
				return ['status'=>0,"message"=>$this->email->print_debugger()];
			}
		}else{return ['status'=>0,"message"=>"Receiver Email Not found"];}
	}
	
	public function getSignature($signData=Array())
	{
	    $companyData = $this->salesInvoice->getCompanyInfo();
	    $email_certificates = base_url('assets/images/email_footer.png');
	    $logoFile=(!empty($companyData->company_logo)) ? $companyData->company_logo : 'logo.png';
	    $logo=base_url('assets/images/'.$logoFile);
	    $sender_sign = '';
	    if(!empty($signData)):
    	    $sender_sign = '<div style="font-family: Verdana;font-size:12px;color:#0000CC;font-weight:bold;">';
    	    $sender_sign .= '<span style="font-size:13px;">'.$signData['sender_name'].'</span>';
    	    $sender_sign .= '<br>Mo. : +91 '.$signData['sender_contact'];
    	    $sender_sign .= '<br><span style="font-size:13px;">'.$signData['sender_designation'].'</span>';
    	    $sender_sign .= '<br>Global Supply Chain';
    	    $sender_sign .= '</div><br>';
    	endif;
	    
	    $signature = '';
	    //$signature .= $sender_sign;
	    $signature .= '<div style="font-family: Bookman Old Style;font-size:10pt;color:#44546a;">';
    	    $signature .= '<span style="font-size:12pt;">Thank you, Best Regards!</span><br><br>';
    	    $signature .= '<img src="'.$logo.'" style="width:180px;border:1px solid #000000;"><br><br>';
    	    $signature .= '<span style="font-size:12pt;"><b>'.$signData['sender_name'].'</b><br>';
    	    $signature .= $signData['sender_designation'].' | <b>'.$companyData->company_name.'</b></span><br>';
    	    $signature .= 'Mobile: '.$signData['sender_contact'].' | Email: '.$signData['sign_email'].' | Web: '.$companyData->company_website.'<br>';
    	    $signature .= 'Address: Plot no. 09-10/2, 11, Shivam Industrial Park-A, Kalawad road,<br>VI: Chhapra, Rajkot-360021, Gujarat – India.<br><br>';
    	    $signature .= '<span style="font-size:11pt;color:#007F40;"><b>Please do not print this e-mail unless you really need to...Go Green, Save Trees, Save Earth</span></b><br>';
    	    $signature .= '<img src="'.$email_certificates.'" style="width:300px;">';
	    $signature .= '</div>';
	    
	    return $signature;
	}
	
	
}
?>