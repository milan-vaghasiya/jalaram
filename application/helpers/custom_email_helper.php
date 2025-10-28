<?php
function sendEmail($postData){
    try{
        $CI =& get_instance(); // Get the CodeIgniter instance
        $CI->load->database(); // Load the database library

        //Load Email Library
        $CI->load->library('encrypt');
        $CI->load->library('email');

        //Set Sender Email ID and Name (Name is optional)
        $CI->email->from($postData['from'], $postData['fromName']);

        //Sets the email address(s) of the recipient(s). Can be a single e-mail, a comma-delimited list or an array
        $CI->email->to($postData['to']);

        //Sets the CC email address(s). Just like the “to”, can be a single e-mail, a comma-delimited list or an array.
        if(!empty($postData['cc'])):
            $CI->email->cc($postData['cc']);
        endif;

        //Sets the BCC email address(s). Just like the to() method, can be a single e-mail, a comma-delimited list or an array.
        if(!empty($postData['bcc'])):
            $CI->email->bcc($postData['bcc']);
        endif;

        //Sets the email subject
        $CI->email->subject($postData['subject']);

        //Sets the e-mail message body
        $CI->email->message($postData['message']);
        
        //Enables you to send an attachment. Put the file path/name in the first parameter. For multiple attachments use the method multiple times.
        if(!empty($postData['attachment'])):
            foreach($postData['attachment'] as $filePath):
                $CI->email->attach($filePath);
            endforeach;
        endif;

        $CI->email->set_header('X-Confirm-Reading-To', $postData['from']);

        //Sets the email tyle if required. [html -> HTML formatted email, text -> Plain text email]
        if(!empty($postData['mail_format'])):
		    $CI->email->set_mailtype($postData['mail_format']);
        endif;

        // Send email and handle response
        if ($CI->email->send()):
            $result = ['status' => 1, 'message' => 'Email sent successfully.'];
        else:
            $result = ['status' => 0, 'message' => $CI->email->print_debugger()];
        endif;

        //If you set the parameter to TRUE any attachments will be cleared as well
        $CI->email->clear(TRUE);

        //Save Email Log
        $mailData = [];
        $mailData['mail_type'] = $postData['mail_type'];
        $mailData['sender_email'] = $postData['from'];
        $mailData['receiver_email'] = $postData['to'];
        $mailData['subject'] = $postData['subject'];
        $mailData['mail_body'] = $postData['message'];
        $mailData['ref_id'] = (!empty($postData['ref_id']))?$postData['ref_id']:0;
        $mailData['ref_no'] = (!empty($postData['ref_no']))?$postData['ref_no']:"";
        $mailData['response'] = json_encode($result);
        $mailData['created_by'] = (isset($postData['created_by']))?$postData['created_by']:$CI->session->userdata('loginId');
        $CI->db->insert('email_logs',$mailData);

        return $result;
    }catch(\Exception $e){
        return ['status'=>0,'message'=>'Message:' .$e->getMessage()];
    }
}
?>