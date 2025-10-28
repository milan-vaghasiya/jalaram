<?php
class MachineLogModel extends MasterModel{
    private $machineLog = "tpm_log";
    private $itemMaster = "item_master";
    
    public function save($data){
        try{
            $this->db->trans_begin();
            
            $data['id'] = "";
            $queryData = array();
            $queryData['tableName'] = $this->itemMaster;
            $querydata['where']['device_no'] = $data['device_no'];
            $machineData = $this->row($queryData);
            
            $data['machine_id'] = $machineData->id;
            //$data['created_by'] = $machineData->operator_id;
            //$data['job_card_id'] = $machineData->job_card_id;
            $data['created_at'] = (!empty($data['created_at'])) ? date('Y-m-d H:i:s',strtotime($data['created_at'])) : date("Y-m-d H:i:s") ;
            
            if($data['log_type'] != 3):
                $result = $this->store($this->machineLog,$data,'Log');
            else:
                $result = ['status'=>1,'message'=>'Log saved successfully.','insert_id'=>'999999999'];
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
    
    public function getMachineLogs($log_type = 1,$device_no=""){
        $queryData = array();
        $queryData['tableName'] = $this->machineLog;
        $queryData['where']['log_type'] = $log_type;
        $queryData['where']['created_at >= '] = "DATE_ADD(created_at, INTERVAL 3  DAY)";
        if(!empty($device_no)){$queryData['where']['device_no'] = $device_no;}
        $queryData['order_by']['created_at'] = "DESC";
        $queryData['order_by']['id'] = "DESC";
        $result = $this->rows($queryData);
        return $result;
    }
    
    public function getPreviusMachineLog($log_type = 1,$currentId,$deviceNo){
        $queryData = array();
        $queryData['tableName'] = $this->machineLog;
        $queryData['where']['log_type'] = $log_type;
        $queryData['where']['id < '] = $currentId;
        $queryData['where']['device_no'] = $deviceNo;
        $queryData['order_by']['id'] = "DESC";
        $queryData['limit'] = 1;
        $result = $this->row($queryData);
        return $result;
    }
    
    public function getMaxIdleTimeOfMachine($postData){
        $data['tableName'] = $this->itemMaster;
        $data['select'] = "fg_id as max_idle_time";
        $data['where']['item_type'] = 5;
        $data['where']['device_no'] = $postData['device_no'];
        return $this->row($data);
    }
}

?>