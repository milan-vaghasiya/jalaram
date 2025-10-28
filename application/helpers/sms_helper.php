<?php
// SMS IDEA
function sendSMS($postData){
    try{
        $CI =& get_instance(); // Get the CodeIgniter instance
        $CI->load->database(); // Load the database library

        //http Url to send sms.
        $url="http://sms.nativebittechnologies.com/smsstatuswithid.aspx";
        $fields = array(
            'mobile' => '9427235336',
            'pass' => '3aa17835825c4ee49f263aa48228a1a6',
            'senderid' => 'NBTERP',
            'to' => $postData['mobile_no'],
            'msg' => urlencode($postData['message'])
        );
       
        //url-ify the data for the POST
        $fields_string = '';
        foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
        rtrim($fields_string, '&');
        
        //open connection
        $curl = curl_init();
        
        //set the url, number of POST vars, POST data
        curl_setopt($curl,CURLOPT_URL, $url);
        curl_setopt($curl,CURLOPT_POST, count($fields));
        curl_setopt($curl,CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
        
        //execute post
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        //close connection
        curl_close($curl);

        $result = ['status'=>1,'httpCode'=>$httpCode,'message'=>$response];

        //Save SMS Log
        $logData = ['mobile' => $postData['mobile_no'], 'message' => $postData['message'], 'response' => json_encode($result), 'created_by' => (isset($postData['created_by']))?$postData['created_by']:$CI->session->userdata('loginId')];
		$CI->db->insert("sms_log",$logData);

        return $result;
    }catch(\Exception $e){
        return ['status'=>0,'message'=>'Message:' .$e->getMessage()];
    }
}
?>