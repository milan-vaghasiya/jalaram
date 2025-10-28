<?php
class ProductionLogModel extends MasterModel
{
    private $productionLogMaster = "production_log";
    private $itemMaster = "item_master";
    private $processMaster = "process_master";
    private $empMaster = "employee_master";
    private $jobApproval = "production_approval";
    private $rejRwManagement = "rej_rw_management";
       
    public function getPendingLogDtRows($data){
        $data['tableName'] = 'production_approval';
        $data['select'] ="production_approval.*,IFNULL(rr_log.production_qty,0) as production_qty,IFNULL(rr_log.total_rej_qty,0) as total_rej_qty,IFNULL(rr_log.total_ok_qty,0) as totalOkQty,IFNULL(rr_log.total_rw_qty,0) as total_rw_qty,job_card.job_prefix,job_card.job_no,item_master.item_code,process_master.process_name,vendor_log.vendor_qty";

        $data['leftJoin']['(select job_approval_id,SUM(ok_qty + rej_qty + rw_qty) as production_qty,SUM(rej_qty) as total_rej_qty, SUM(ok_qty) as total_ok_qty,SUM(rw_qty) as total_rw_qty,process_id,job_card_id from production_log where is_delete = 0  AND prod_type IN(1,2,5) group by job_card_id,process_id) as rr_log'] = "rr_log.job_approval_id  = production_approval.id";
        
        $data['leftJoin']['(select SUM(production_qty) as vendor_qty,out_process_id,job_card_id from production_log where is_delete = 0 AND send_to = 1  AND prod_type =4 group by job_card_id,out_process_id) as vendor_log'] = "vendor_log.job_card_id = production_approval.job_card_id AND vendor_log.out_process_id  = production_approval.in_process_id";

        $data['leftJoin']['job_card'] = "job_card.id = production_approval.job_card_id";
        $data['leftJoin']['item_master'] = "item_master.id = job_card.product_id";
        $data['leftJoin']['process_master'] = "process_master.id = production_approval.in_process_id";
        $data['where']['production_approval.stage_type !='] = 3;
        $data['where']['production_approval.in_process_id !='] = 0;
        /*if($data['prod_type'] == 2){ 
            $data['where']['process_master.dept_id'] = 7; 
        }else{
            $data['where']['process_master.dept_id !='] = 7; 
        }*/
        $data['where']['job_card.order_status'] = 2;
       
        $data['customWhere'][] = '((production_approval.in_qty-IFNULL(rr_log.production_qty,0)-IFNULL(production_approval.ch_qty,0)) > 0)';
        
        $data['searchCol'][] ="";
        $data['searchCol'][] ="";
        $data['searchCol'][] = "CONCAT(SUBSTRING_INDEX(SUBSTRING_INDEX(job_card.job_prefix, '/', 1), '/', -1),'/',job_card.job_no,'/',SUBSTRING_INDEX(SUBSTRING_INDEX(job_card.job_prefix, '/', 2), '/', -1))";
        $data['searchCol'][] = "item_master.item_code";
        $data['searchCol'][] = "process_master.process_name";
        $data['searchCol'][] = "production_approval.in_qty";
        $data['searchCol'][] = "rr_log.total_ok_qty";
        $data['searchCol'][] = "rr_log.total_rej_qty";
        $data['searchCol'][] = "rr_log.total_rw_qty";
        $data['searchCol'][] = "(production_approval.in_qty -rr_log.total_ok_qty-rr_log.total_rej_qty)";

        $columns = array('', '', 'job_card.job_no',  "item_master.item_code", "process_master.process_name", "production_approval.in_qty", "rr_log.total_ok_qty", "rr_log.totalOkQty", "rr_log.total_rej_qty", "rr_log.total_rw_qty", "(production_approval.in_qty -rr_log.total_ok_qty-rr_log.total_rej_qty)");
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        } 
        $result =  $this->pagingRows($data);
        return $result;
    }

    public function getProductionDTRows($data){
        $data['tableName'] = $this->productionLogMaster;
        $data['select'] = "production_log.*,process_master.process_name,item_master.item_name,itm.item_code as product_name,item_master.item_code,employee_master.emp_name,job_card.job_no,job_card.job_prefix";
        $data['leftJoin']['job_card'] = "job_card.id = production_log.job_card_id";
        $data['leftJoin']['item_master'] = "item_master.id = production_log.machine_id";
        $data['leftJoin']['item_master itm'] = "itm.id = job_card.product_id";
        $data['leftJoin']['process_master'] = "process_master.id = production_log.process_id";
        $data['leftJoin']['employee_master'] = "employee_master.id = production_log.operator_id";
        $data['where_in']['production_log.prod_type'] = $data['prod_type'];
        
        if(isset($data['is_approve'])){
            $data['where_in']['production_log.is_approve'] = $data['is_approve'];
        }
        
        $data['where']['job_card.order_status'] = 2;

        $data['searchCol'][] ="";
        $data['searchCol'][] ="";
        $data['searchCol'][] = "CONCAT(job_card.job_prefix,job_card.job_no)";
        $data['searchCol'][] = "itm.item_code";
        $data['searchCol'][] = "DATE_FORMAT(production_log.log_date,'%d-%m-%Y')";
        $data['searchCol'][] = "process_master.process_name";
        $data['searchCol'][] = "item_master.item_code";
        $data['searchCol'][] = "employee_master.emp_name";
        $data['searchCol'][] = "production_log.production_qty";
        $data['searchCol'][] = "production_log.rej_qty";
        $data['searchCol'][] = "production_log.rw_qty";
        
        $columns = array('', '', 'job_card.job_no', 'itm.item_code','production_log.log_date','process_master.process_name', 'item_master.item_code', 'employee_master.emp_name', 'production_log.production_qty', 'production_log.rej_qty', 'production_log.rw_qty');
        if (isset($data['order'])) {
            
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }else{
            $data['order_by']['production_log.log_date'] = 'DESC';
            $data['order_by']['production_log.id'] = 'DESC';
        }
        return $this->pagingRows($data);
    }
	
    public function getProductionLogDTRows($data){
        $data['tableName'] = $this->productionLogMaster;
        $data['select'] = "production_log.*,process_master.process_name,item_master.item_name,itm.item_code as product_name,item_master.item_code,employee_master.emp_name,job_card.job_no,job_card.job_prefix,production_approval.total_ok_qty,production_approval.out_qty,IFNULL(rework_rej.qty,0) as rework_qty";
        $data['leftJoin']['job_card'] = "job_card.id = production_log.job_card_id";
        $data['leftJoin']['production_approval'] = "production_approval.id = production_log.job_approval_id";
        $data['leftJoin']['item_master'] = "item_master.id = production_log.machine_id";
        $data['leftJoin']['item_master itm'] = "itm.id = job_card.product_id";
        $data['leftJoin']['process_master'] = "process_master.id = production_log.process_id";
        $data['leftJoin']['employee_master'] = "employee_master.id = production_log.operator_id";
        $data['leftJoin']['(select SUM(rej_rw_management.qty) as qty,rej_rw_management.log_id from rej_rw_management where rej_rw_management.manag_type = 2 AND rej_rw_management.is_delete = 0 group by rej_rw_management.log_id) as rework_rej'] = "rework_rej.log_id = production_log.id";
        
        $data['where_in']['production_log.prod_type'] = $data['prod_type'];
        
        if(empty($data['is_approve'])){
            $data['where']['production_log.is_approve'] = 0;
		}else{
		    
		    $data['select'] .= ' ,apr.emp_name as approval_name';
			$data['leftJoin']['employee_master apr'] = "apr.id = production_log.is_approve";
			
			$data['where']['production_log.is_approve >'] = 0;
            $data['where']['DATE(production_log.log_date) >= '] = $this->startYearDate;
            $data['where']['DATE(production_log.log_date) <= '] = $this->endYearDate;
        }
        if($data['prod_type'] == 2){
            //$data['where']['production_log.rw_qty >'] = 0;
            $data['having'][] = "rework_qty > 0";
        }
        $data['where']['job_card.order_status'] = 2;

        $data['searchCol'][] ="";
        $data['searchCol'][] ="";
        $data['searchCol'][] = "CONCAT(job_card.job_prefix,job_card.job_no)";
        $data['searchCol'][] = "itm.item_code";
        $data['searchCol'][] = "DATE_FORMAT(production_log.log_date,'%d-%m-%Y')";
        $data['searchCol'][] = "process_master.process_name";
        $data['searchCol'][] = "item_master.item_code";
        $data['searchCol'][] = "employee_master.emp_name";
        $data['searchCol'][] = "production_log.ok_qty";
        
        if(!empty($data['is_approve'])){
            $data['searchCol'][] = "production_log.rej_qty";
            $data['searchCol'][] = "production_log.rw_qty";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "apr.emp_name";
            $columns = array('', '', 'job_card.job_no', 'itm.item_code','production_log.log_date','process_master.process_name', 'item_master.item_code', 'employee_master.emp_name', 'production_log.ok_qty',"production_log.rej_qty","production_log.rw_qty","","apr.emp_name");
        }else{
            $columns = array('', '', 'job_card.job_no', 'itm.item_code','production_log.log_date','process_master.process_name', 'item_master.item_code', 'employee_master.emp_name', 'production_log.ok_qty');
        }
        
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }else{
            $data['order_by']['production_log.log_date'] = 'DESC';
            $data['order_by']['production_log.id'] = 'DESC';
        }
        return $this->pagingRows($data);
    }

	public function getJobworkQty($job_card_id,$process_id){
        $vendorData = array();
        $vendorData['tableName'] ='production_log';
        $vendorData['select'] = "SUM(production_log.production_qty) as vendor_qty, vendor_trans.without_process_qty";
        
        $vendorData['leftJoin']['(select SUM(without_process_qty) as without_process_qty,process_id,job_card_id FROM vendor_challan_trans WHERE job_card_id = "'.$job_card_id.'" AND process_id = "'.$process_id.'" AND is_delete = 0) as vendor_trans'] = "vendor_trans.job_card_id = production_log.job_card_id AND vendor_trans.process_id = production_log.out_process_id";
        
        $vendorData['where']['production_log.job_card_id'] = $job_card_id;
        $vendorData['where']['production_log.out_process_id'] = $process_id;
        $vendorData['where']['production_log.send_to'] = 1;
        return $this->row($vendorData);
    }

    public function save($data, $reworkRejData = array()){ 
        try{
            $this->db->trans_begin();
            $rejectionData = (!empty($data['rejection_reason'])) ? $data['rejection_reason'] : array();
            $reworkData = (!empty($data['rework_reason'])) ? $data['rework_reason'] : array();
            unset($data['rework_reason'], $data['rejection_reason']);
            
            if(!empty($data['id']) && empty($reworkRejData)):
                $logData = $this->getLogs($data['id']);
                $rejRwData = $this->getRejRwData($data['id']);
                foreach ($rejRwData as $row) :
                    //if (!empty($rejectionData)) :
                        if (!in_array($row->id, array_column($rejectionData, 'trans_id')) && $row->manag_type == 1):
                            if ($row->rej_type == 1) {                               
                                //**  qty plus in rework log */
                                $setData = array();
                                $setData['tableName'] = $this->rejRwManagement;
                                $setData['where']['id'] = $row->ref_id;
                                $setData['set']['qty'] = 'qty, +' . $row->qty;
                                $this->setValue($setData);

                                //**  qty Plus in production_log log */
                                $setData = array();
                                $setData['tableName'] = 'production_log';
                                $setData['where']['id'] = $data['id'];
                                $setData['set']['rw_qty'] = 'rw_qty, + ' . $row->qty;
                                $this->setValue($setData);
                            }

                            $this->trash($this->rejRwManagement, ['id' => $row->id]);
                        endif;
                    //endif;
                    //if (!empty($reworkData)) :
                        if (!in_array($row->id, array_column($reworkData, 'trans_id')) && $row->manag_type == 2) :
                            $this->trash($this->rejRwManagement, ['id' => $row->id]);
                        endif;
                    //endif;
                endforeach;
            endif;
            $aprvData = $this->processMovement->getApprovalData(['id'=>$data['job_approval_id']]);
            $data['stage_type'] = $aprvData->stage_type;
            $result = $this->store($this->productionLogMaster,$data,'Production Log');
            $id=(!empty($data['id']))?$data['id']:$result['insert_id'];

            if (!empty($rejectionData)) {
                foreach ($rejectionData as $row) {
                    $rejArray = [
                        'id' => (!empty($row['trans_id'])?$row['trans_id']:''),
                        'manag_type' => 1,
                        'log_id' => $id,
                        'job_card_id' => $data['job_card_id'],
                        'qty' => $row['rej_qty'],
                        'ref_id' => (!empty($row['rej_ref_id'])?$row['rej_ref_id']:''),
                        'rej_type' => (!empty($row['rej_type'])?$row['rej_type']:0),
                        'reason' => $row['rej_reason'],
                        'rej_by' => (isset($row['rej_by']))?$row['rej_by']:0,
                        'reason_name' => $row['rejection_reason'],
                        'belongs_to' => $row['rej_stage'],
                        'belongs_to_name' => $row['rej_stage_name'],
                        'vendor_id' => $row['rej_from'],
                        'vendor_name' => $row['rej_party_name'],
                        'remark' => $row['rej_remark'],
                        'created_by' => $this->loginId,
                    ];
                    $this->store($this->rejRwManagement, $rejArray);
                }

                if (!empty($reworkRejData)) {
                    //** ok qty minus in rework log */
                    $setData = array();
                    $setData['tableName'] = $this->rejRwManagement;
                    $setData['where']['id'] = $reworkRejData['id'];
                    $setData['set']['qty'] = 'qty, - ' . $reworkRejData['rej_qty'];
                    $this->setValue($setData);

                    //** ok qty minus in production_log log */
                    $setData = array();
                    $setData['tableName'] = 'production_log';
                    $setData['where']['id'] = $id;
                    $setData['set']['rw_qty'] = 'rw_qty, - ' . $reworkRejData['rej_qty'];
                    $this->setValue($setData);
                    
                }
            }

            if (!empty($reworkData)) {
                foreach ($reworkData as $row) {
                    $rwArray = [
                        'id' => (!empty($row['trans_id'])?$row['trans_id']:''),
                        'manag_type' => 2,
                        'log_id' => $id,
                        'job_card_id' => $data['job_card_id'],
                        'qty' => $row['rw_qty'],
                        'reason' => $row['rw_reason'],
                        'reason_name' => $row['rework_reason'],
                        'belongs_to' => $row['rw_stage'],
                        'belongs_to_name' => $row['rw_stage_name'],
                        'vendor_id' => $row['rw_from'],
                        'vendor_name' => $row['rw_party_name'],
                        'remark' => $row['rw_remark'],
                        'created_by' => $this->loginId,
                    ];
                    $this->store($this->rejRwManagement, $rwArray);
                }
            }
        
            if(!empty($data['is_approve'])){
                $this->jobApprovalQtyEffect($data['job_approval_id']);
            }

            
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function getLogs($id){
        $queryData['tableName'] = $this->productionLogMaster;
        $queryData['select'] = "production_log.*,process_master.process_name,item_master.item_name as machine_name,item_master.item_code as machine_code,employee_master.emp_name,job_card.job_no,job_card.job_prefix,product.item_code,product.item_name,job_card.process,shift_master.shift_name";
        $queryData['leftJoin']['job_card'] = "job_card.id = production_log.job_card_id";
        $queryData['leftJoin']['item_master'] = "item_master.id = production_log.machine_id";
        $queryData['leftJoin']['item_master product'] = "product.id = job_card.product_id";
        $queryData['leftJoin']['process_master'] = "process_master.id = production_log.process_id";
        $queryData['leftJoin']['employee_master'] = "employee_master.id = production_log.operator_id";
        $queryData['leftJoin']['shift_master'] = "shift_master.id = production_log.shift_id";
        $queryData['where']['production_log.id'] = $id;
        return $this->row($queryData);
    }

    public function getRejRwData($log_id){
        $queryData['tableName'] = $this->rejRwManagement;
        $queryData['select'] = "rej_rw_management.*";
        $queryData['where']['log_id'] = $log_id;
        return $this->rows($queryData);
    }

    public function jobApprovalQtyEffect($job_approval_id){
        try{
            $this->db->trans_begin();
            $queryData['tableName'] = $this->productionLogMaster;
            $queryData['select'] = "SUM(ok_qty) as ok_qty,SUM(rej_qty) as rej_qty";
            $queryData['where']['production_log.job_approval_id'] = $job_approval_id;
            $queryData['where']['production_log.is_approve >'] = 0;
            $logData =  $this->row($queryData);
            $total_ok_qty = !empty($logData->ok_qty)?$logData->ok_qty:0;
            $total_rej_qty = !empty($logData->rej_qty)?$logData->rej_qty:0;
            $result = $this->store($this->jobApproval,['id'=>$job_approval_id,'total_ok_qty'=>$total_ok_qty,'total_rejection_qty'=>$total_rej_qty]);
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function getPrdLogOnProcessNJob($postData)
    {
        $queryData['tableName'] = $this->productionLogMaster;
        $queryData['select'] = 'SUM(rej_qty) as rejection_qty,SUM(rw_qty) as rework_qty,SUM(ok_qty) as ok_qty,SUM(production_qty) as production_qty';
        if(!empty($postData['job_card_id'])){$queryData['where']['production_log.job_card_id'] = $postData['job_card_id'];}
        if(!empty($postData['job_approval_id'])){$queryData['where']['production_log.job_approval_id'] = $postData['job_approval_id'];}
        if(!empty($postData['process_id'])){$queryData['where']['production_log.process_id'] = $postData['process_id'];}
        if(!empty($postData['prod_type'])){$queryData['where_in']['production_log.prod_type'] = $postData['prod_type'];}
        
        return $this->row($queryData);
    }

    public function delete($id){
        try{
            $this->db->trans_begin();
            $logData = $this->getLogs($id);

            $this->trash($this->rejRwManagement, ['log_id' => $id]);

            //If Vendor Log
            if($logData->prod_type == 3){
                $result = $this->jobWorkVendor_v3->deleteLog(['id'=>$id]);
            }else{
                $result = $this->trash($this->productionLogMaster, ['id' => $id], 'Production Log');
            }
            
            if(!empty($logData->is_approve)){
                $this->jobApprovalQtyEffect($logData->job_approval_id);
            }
            
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }	

    public function saveReworkQty($data)
    {
        try {
            $this->db->trans_begin();
            
            /** Rework to Ok qty Entry */
            $reworkData = [
                'id' => '',
                'manag_type' => 3,
                'log_id' => $data['log_id'],
                'job_card_id' => $data['job_card_id'],
                'qty' => $data['rw_qty'],
                'reason' => $data['rw_reason'],
                'reason_name' => $data['rework_reason'],
                'belongs_to' => $data['rw_stage'],
                'belongs_to_name' => $data['rw_stage_name'],
                'vendor_id' => $data['rw_from'],
                'vendor_name' => $data['rw_party_name'],
                'remark' => $data['rw_remark'],
                'created_by' => $data['created_by'],
                'ref_id' => $data['trans_id']
            ];
            $result = $this->store($this->rejRwManagement, $reworkData);

            //** ok qty minus in rework log */
            $setData = array();
            $setData['tableName'] = $this->rejRwManagement;
            $setData['where']['id'] = $data['trans_id'];
            $setData['set']['qty'] = 'qty, - ' . $data['rw_qty'];
            $this->setValue($setData);

            //** ok qty minus in production_log log */
            $setData = array();
            $setData['tableName'] = 'production_log';
            $setData['where']['id'] = $data['log_id'];
            $setData['set']['rw_qty'] = 'rw_qty, - ' . $data['rw_qty'];
            $setData['set']['ok_qty'] = 'ok_qty, + ' . $data['rw_qty'];
            $this->setValue($setData);
            
            $logData = $this->getLogs($data['log_id']);
            
            $this->jobApprovalQtyEffect($logData->job_approval_id);
            
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getRejectionData($log_id, $manag_type = "1")
    {
        $queryData['tableName'] = $this->rejRwManagement;
        $queryData['select'] = "rej_rw_management.*";
        $queryData['where']['log_id'] = $log_id;
        $queryData['where']['manag_type'] = $manag_type;
        return $this->rows($queryData);
    }

    public function getJobWorkVendorTransData($data)
    {
        $queryData['tableName'] = "vendor_challan_trans";
        $queryData['select'] = "vendor_challan.vendor_id,party_master.party_name";
        $queryData['join']['vendor_challan'] = "vendor_challan.id = vendor_challan_trans.challan_id";
        $queryData['join']['party_master'] = "party_master.id = vendor_challan.vendor_id";
        $queryData['where']['vendor_challan_trans.job_card_id'] = $data['job_card_id'];
        $queryData['where']['vendor_challan_trans.process_id'] = $data['process_id'];
        $queryData['group_by'] = ['vendor_challan.vendor_id'];
        return $this->rows($queryData);
    }

    public function saveHoldQtyLog($data){
       
        try{
            $this->db->trans_begin();
            $rejectionData = (!empty($data['rejection_reason'])) ? $data['rejection_reason'] : '';
            $reworkData = (!empty($data['rework_reason'])) ? $data['rework_reason'] : '';
            unset($data['rework_reason'], $data['rejection_reason']);
            // print_r($data);exit;
            
      

            if (!empty($rejectionData)) {
                foreach ($rejectionData as $row) {
                    $rejArray = [
                        'id' => (!empty($row['trans_id'])?$row['trans_id']:''),
                        'manag_type' => 1,
                        'log_id' => $data['id'],
                        'job_card_id' => $data['job_card_id'],
                        'qty' => $row['rej_qty'],
                        'ref_id' => (!empty($row['rej_ref_id'])?$row['rej_ref_id']:''),
                        'rej_type' => 2,
                        'reason' => $row['rej_reason'],
                        'rej_by' => (isset($row['rej_by']))?$row['rej_by']:0,
                        'reason_name' => $row['rejection_reason'],
                        'belongs_to' => $row['rej_stage'],
                        'belongs_to_name' => $row['rej_stage_name'],
                        'vendor_id' => $row['rej_from'],
                        'vendor_name' => $row['rej_party_name'],
                        'remark' => $row['rej_remark'],
                        'created_by' => $this->loginId,
                    ];
                    $this->store($this->rejRwManagement, $rejArray);
                }

                //** Hold Qty qty minus in production_log log */
                $setData = array();
                $setData['tableName'] = 'production_log';
                $setData['where']['id'] = $data['id'];
                $setData['set']['hold_qty'] = 'hold_qty, - ' . $row['rej_qty'];
                $setData['set']['rej_qty'] = 'rej_qty, + ' . $row['rej_qty'];
                $this->setValue($setData);
            }

            if (!empty($reworkData)) {
                foreach ($reworkData as $row) {
                    $rwArray = [
                        'id' => (!empty($row['trans_id'])?$row['trans_id']:''),
                        'manag_type' => 2,
                        'rej_type' => 2,
                        'log_id' => $data['id'],
                        'job_card_id' => $data['job_card_id'],
                        'qty' => $row['rw_qty'],
                        'reason' => $row['rw_reason'],
                        'reason_name' => $row['rework_reason'],
                        'belongs_to' => $row['rw_stage'],
                        'belongs_to_name' => $row['rw_stage_name'],
                        'vendor_id' => $row['rw_from'],
                        'vendor_name' => $row['rw_party_name'],
                        'remark' => $row['rw_remark'],
                        'created_by' => $this->loginId,
                    ];
                    $this->store($this->rejRwManagement, $rwArray);

                    //** Hold Qty qty minus in production_log log */
                    $setData = array();
                    $setData['tableName'] = 'production_log';
                    $setData['where']['id'] = $data['id'];
                    $setData['set']['hold_qty'] = 'hold_qty, - ' . $row['rw_qty'];
                    $setData['set']['rw_qty'] = 'rw_qty, + ' . $row['rw_qty'];
                    $this->setValue($setData);
                }
            }
            if($data['ok_qty'] > 0){
                $okArray = [
                    'id' => '',
                    'manag_type' => 4,
                    'log_id' => $data['id'],
                    'job_card_id' => $data['job_card_id'],
                    'qty' => $data['ok_qty'],
                    'created_by' => $this->loginId,
                ];
                $this->store($this->rejRwManagement, $okArray);

                $setData = array();
                $setData['tableName'] = 'production_log';
                $setData['where']['id'] = $data['id'];
                $setData['set']['hold_qty'] = 'hold_qty, - ' . $data['ok_qty'];
                $setData['set']['ok_qty'] = 'ok_qty, + ' . $data['ok_qty'];
                $this->setValue($setData);
            }
            
        
            $result = $this->jobApprovalQtyEffect($data['job_approval_id']);

            
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function getRejBelongsTo($job_card_id,$process_id)
    {
        $queryData['tableName'] = $this->rejRwManagement;
        $queryData['select'] = 'SUM(qty) as qty';
        $queryData['where']['rej_rw_management.job_card_id'] = $job_card_id;
        $queryData['where']['rej_rw_management.manag_type'] = 1;
        $queryData['where']['rej_rw_management.belongs_to'] = $process_id;
        $result = $this->row($queryData)->qty;
		
		return $result;
    }
    
    public function getMachineRuntime($data){
        $queryData['tableName'] = $this->productionLogMaster;
        $queryData['select'] = "production_log.log_date,SUM(production_log.production_time) as total_runtime";
        $queryData['where']['production_log.log_date'] = $data['log_date'];
        $queryData['where']['production_log.machine_id'] = $data['machine_id']; 
        $queryData['group_by'][] = 'production_log.log_date';
        $queryData['group_by'][] = 'production_log.machine_id';
        return $this->row($queryData);
    }

    public function getProductionLogList($postData = []){
        $queryData['tableName'] = 'production_log';
        $queryData['select'] = 'production_log.*';
        if(!empty($postData['prod_type'])){ 
            $queryData['where']['production_log.prod_type'] = $postData['prod_type'];
        }
        if(!empty($postData['job_approval_id'])){ 
            $queryData['where']['production_log.job_approval_id'] = $postData['job_approval_id'];
        }
        if(!empty($postData['ref_id'])){
            $queryData['where']['production_log.ref_id'] = $postData['ref_id'];
        }
        if(!empty($postData['single_row'])){
            return $this->row($queryData);
        }else{
            return $this->rows($queryData);
        }
        
    }
}
