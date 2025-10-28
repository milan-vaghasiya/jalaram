<?php
class IotConfigModel extends MasterModel{
    
    private $iotConfig = "iot_config";
    private $itemMaster = "item_master";

    public function getDTRows($data){
        $data['tableName'] = $this->itemMaster;
        $data['select'] = 'item_master.*';
        $data['where']['item_master.item_type']=5;
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "item_master.item_code";
        $data['searchCol'][] = "item_category.remark";
		$columns =array('','','category_name','group_name','remark');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getIotConfigData($machine_id){
        $data['tableName'] = $this->iotConfig;
        $data['select'] = 'iot_config.*';
        $data['where']['iot_config.machine_id']=$machine_id;
        return $this->row($data);
    }

    public function saveIdleTime($data){
        try{
            $this->db->trans_begin();
    		$result = $this->store($this->iotConfig,$data);
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