<?php
class DashboardModel extends MasterModel{

    private $machineLog = "tpm_log";
    private $itemMaster = "item_master";

    public function getMachineLog(){
        $queryData = array();
        $queryData['tableName'] = $this->machineLog;
        $queryData['select']="tpm_log.*,mc.item_code as machine_code,mc.item_name as machine_name,item_master.item_code,iot_config.idle_time";
        $queryData['join']['item_master mc']="mc.device_no = tpm_log.device_no AND mc.item_type = 5";
        $queryData['leftJoin']['job_card']="job_card.job_no = tpm_log.job_no AND  job_card.is_delete = 0 AND job_card.version=2";
        $queryData['leftJoin']['item_master']="item_master.id = job_card.product_id";
        $queryData['leftJoin']['iot_config']="mc.id = iot_config.machine_id";
        //$queryData['where_in']['tpm_log.device_no'] = [50101,50102,50103,50105,50106,50108];
        $queryData['where_in']['tpm_log.device_no'] = [50101];
        $queryData['where']['tpm_log.log_type'] = 1;
        $queryData['customWhere'][]="tpm_log.id IN(SELECT MAX(id) FROM tpm_log WHERE log_type = 1 GROUP BY device_no)";
        // $queryData['group_by'][] = "tpm_log.device_no";
        $queryData['order_by']['tpm_log.id'] = "DESC";
       
        $result = $this->rows($queryData);
        // print_r($this->db->last_query());exit;
        return $result;
    }

    public function getMachineLogJobWise($data){
        $queryData = array();
        $queryData['tableName'] = $this->machineLog;
        $queryData['select']='tpm_log.created_by,tpm_log.created_at,tpm_log.job_no,tpm_log.process_no,SUM(CASE WHEN DATE(tpm_log.created_at) = "'.date("Y-m-d").'" THEN tpm_log.production_time END) as production_time,SUM(CASE WHEN DATE(tpm_log.created_at) = "'.date("Y-m-d").'" THEN tpm_log.xideal_time END) as xideal_time,SUM(CASE WHEN STRCMP(tpm_log.tool_no,product_process.tool_no)=0  AND rw_status = 1 THEN 1 ELSE 0 END) as partCount,SUM(CASE WHEN STRCMP(tpm_log.tool_no,product_process.tool_no)!=0 THEN 1 ELSE 0 END) as rework_count';
        $queryData['leftJoin']['job_card']="job_card.job_no = tpm_log.job_no AND  job_card.is_delete = 0 AND job_card.version=2";
        $queryData['leftJoin']['product_process']="product_process.item_id = job_card.product_id AND  product_process.process_id = tpm_log.process_no";
        $queryData['where']['log_type'] = 1;
        $queryData['where']['tpm_log.job_no'] =$data['job_no'];
        $queryData['where']['tpm_log.device_no'] =$data['device_no'];
        $result = $this->row($queryData);
        // print_r($this->db->last_query());exit;
        return $result;
    }

    public function getMachineLogListJobWise($data){
        if(empty($data['fromDate'])){
            $data['fromDate']=date("Y-m-d H:i:s");
            $data['toDate']=date("Y-m-d H:i:s");
        }else{
            $data['fromDate']=date("Y-m-d H:i:s",strtotime($data['fromDate']));
            $data['toDate']=date("Y-m-d H:i:s",strtotime($data['toDate']));
        }
        $queryData = array();
        $queryData['tableName'] = $this->machineLog;
        $queryData['select']='tpm_log.created_by,employee_master.emp_name as operator_name,tpm_log.created_at,tpm_log.job_no,tpm_log.process_no,item_master.item_code,process_master.process_name,
        SUM(tpm_log.production_time) as productionTime,
        SUM(tpm_log.xideal_time) as xidealTime,
        SUM(CASE WHEN STRCMP(tpm_log.tool_no,product_process.tool_no)=1 THEN 1 ELSE 0 END) as partCount,
        SUM(CASE WHEN STRCMP(tpm_log.tool_no,product_process.tool_no)!=1 THEN 1 ELSE 0 END) as rework_count';
        
        /*
        SUM(CASE WHEN STRCMP(tpm_log.tool_no,product_process.tool_no)=0 AND rw_status = 1 THEN 1 ELSE 0 END) as partCount,
        SUM(CASE WHEN STRCMP(tpm_log.tool_no,product_process.tool_no)!=0 THEN 1 ELSE 0 END) as rework_count';
        */
        
        $queryData['leftJoin']['job_card']="job_card.job_no = tpm_log.job_no AND  job_card.is_delete = 0 AND job_card.version=2";
        $queryData['leftJoin']['item_master'] = "item_master.id = job_card.product_id";
        $queryData['leftJoin']['product_process']="product_process.item_id = job_card.product_id AND  product_process.process_id = tpm_log.process_no";
        $queryData['leftJoin']['employee_master'] = "tpm_log.created_by = employee_master.emp_code";
        $queryData['leftJoin']['process_master'] = "process_master.id = tpm_log.process_no";
        $queryData['where']['tpm_log.log_type'] = $data['log_type'];
        $queryData['where']['tpm_log.device_no'] = $data['device_no'];
        $queryData['customWhere'][]="tpm_log.created_at BETWEEN '".$data['fromDate']."' AND '".$data['toDate']."'";
        $queryData['group_by'][]='tpm_log.job_no';
        $queryData['group_by'][]='tpm_log.process_no';
        $queryData['group_by'][]='tpm_log.created_by';
        $queryData['order_by']['tpm_log.id'] = 'DESC';
        $result = $this->rows($queryData);
        //print_r($this->printQuery());exit;
        return $result;
    }
    
    public function getMachineLogs($data){
        if(empty($data['fromDate'])){
            $data['fromDate']=date("Y-m-d H:i:s");
            $data['toDate']=date("Y-m-d H:i:s");
        }else{
            $data['fromDate']=date("Y-m-d H:i:s",strtotime($data['fromDate']));
            $data['toDate']=date("Y-m-d H:i:s",strtotime($data['toDate']));
        }
        
        $queryData = array();
        $queryData['tableName'] = $this->machineLog;
        $queryData['select']='tpm_log.*,employee_master.emp_name as operator_name,item_master.item_code,process_master.process_name,
        (CASE WHEN STRCMP(tpm_log.tool_no,product_process.tool_no)=1 THEN 1 ELSE 0 END) as partCount,
        (CASE WHEN STRCMP(tpm_log.tool_no,product_process.tool_no)!=1 THEN 1 ELSE 0 END) as rework_count,
        (CASE WHEN STRCMP(tpm_log.tool_no,product_process.tool_no)!=1 THEN "Yes" ELSE "NO" END) as rw_status';
        
        $queryData['leftJoin']['job_card']="job_card.job_no = tpm_log.job_no AND  job_card.is_delete = 0 AND job_card.version=2";
        $queryData['leftJoin']['item_master'] = "item_master.id = job_card.product_id";
        $queryData['leftJoin']['product_process']="product_process.item_id = job_card.product_id AND  product_process.process_id = tpm_log.process_no";
        $queryData['leftJoin']['employee_master'] = "tpm_log.created_by = employee_master.emp_code";
        $queryData['leftJoin']['process_master'] = "process_master.id = tpm_log.process_no";
        
        $queryData['where']['tpm_log.log_type'] = $data['log_type'];
        $queryData['where']['tpm_log.device_no'] =$data['device_no'];
        $queryData['customWhere'][]="tpm_log.created_at BETWEEN '".$data['fromDate']."' AND '".$data['toDate']."'";
        $queryData['order_by']['id'] = "DESC";
        $result = $this->rows($queryData);
        return $result;
    }
    
     /****** Get Dashboard Permission ****/
    public function getDashboardPermissions(){
        $data['tableName'] = "dashboard_permission";
        $data['select'] = 'GROUP_CONCAT(dashboard_permission.sys_class) AS widget_class';
        $data['where']['dashboard_permission.emp_id'] = $this->loginId;
        $data['where']['dashboard_permission.is_read'] = 1;
        $result = $this->row($data);
        return $result;
    }

    public function getTodaySales($data){
        $data['tableName'] = "trans_main";
        $data['select'] = 'SUM(trans_main.net_amount) AS net_amount';
        $data['where_in']['trans_main.entry_type'] = [6,7,8];

        if(!empty($data['from_date']) AND !empty($data['to_date'])){
            $data['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
        }

        if(empty($data['from_date']) AND !empty($data['to_date'])){
            $data['where']['trans_main.trans_date'] = date("Y-m-d");
        }

        return $this->row($data);
    }

    public function getPendingSQCount($data=[]){ 
        $data['tableName'] = "trans_main";
        $data['select'] = "COUNT(trans_main.id) AS total_count";
        $data['where']['trans_main.trans_status'] = 0;

        if(!empty($data['from_date']) AND !empty($data['to_date'])){
            $data['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
        }

        if(empty($data['from_date']) AND !empty($data['to_date'])){
            $data['where']['trans_main.trans_date'] = date("Y-m-d");
        }

        if(!empty($data['entry_type'])){
            $data['where']['trans_main.entry_type'] = $data['entry_type'];
        }

		$result = $this->row($data);
        return $result;
    } 

    public function getPendingSOCount($data=[]){ 
        $data['tableName'] = "trans_main";
        $data['select'] = "COUNT(trans_main.id) AS total_so";
        $data['where']['trans_main.entry_type'] = 4;
        if(!empty($data['is_approve'])){
            $data['where']['trans_main.is_approve'] = 0;
            $data['where']['trans_main.trans_status'] = 0;
        }else{
            $data['customWhere'][] = "trans_main.trans_date BETWEEN '".date('Y-m-01')."' AND '".date('Y-m-t')."'";
        }
		$result = $this->row($data);
        return $result;
    } 

    public function getDueSalesData(){
        $data['tableName'] = "trans_child";
        $data['select'] = 'trans_child.*,trans_main.trans_number,trans_main.doc_no,trans_main.party_id,item_master.item_name,item_master.item_code,party_master.party_code,party_master.party_name';
        $data['leftJoin']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        $data['leftJoin']['item_master'] = "item_master.id = trans_child.item_id";
        $data['leftJoin']['party_master'] = "trans_main.party_id = party_master.id";
        $data['where']['trans_child.trans_status != '] = 2;
        $data['where']['trans_child.entry_type'] = 4 ;
        $data['where']['trans_child.cod_date <= '] = date('Y-m-d',strtotime('+10 days'));
        $data['customWhere'][] = '(trans_child.qty - trans_child.dispatch_qty) > 0';

        return $this->rows($data);
    }
}
?>