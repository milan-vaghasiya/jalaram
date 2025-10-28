<?php
class FirModel extends MasterModel
{
    private $fir_master = "fir_master";
    private $fir_dimension = "fir_dimension";
    private $job_approval ="production_approval";
    private $jobCard = "job_card";
    private $stockTransaction ="stock_transaction";
    private $production_log ="production_log";
    private $grnTestReport ="grn_test_report";
   
    public function getDTRows($data)
    {
        $data['tableName'] = "production_log";
        $data['select'] = "production_log.*,job_card.job_no,job_card.job_prefix,job_card.product_id,item_master.full_name,item_master.item_code,process_master.process_name,job_card.pfc_rev_no,job_card.cp_rev_no";
        $data['leftJoin']['job_card'] ='job_card.id = production_log.job_card_id';
        $data['leftJoin']['item_master'] ='item_master.id = production_log.product_id';
        $data['leftJoin']['process_master'] ='process_master.id = production_log.process_id';

        $data['where']['job_card.order_status'] = 2;
        $data['where']['production_log.stage_type'] = 3;
        $data['where']['production_log.prod_type'] = 4;
    
        $data['having'][] = '(production_log.ok_qty-production_log.accepted_qty) > 0';

        $data['searchCol'][] = "DATE_FORMAT(production_log.log_date,'%d-%m-%Y')";
        $data['searchCol'][] = "job_card.job_no";
        $data['searchCol'][] = "item_master.item_code";
        $data['searchCol'][] = "process_master.process_name";
        $data['searchCol'][] = "production_log.ok_qty";
        $data['searchCol'][] = "production_log.accepted_qty";

        $columns = array('', '', 'job_card.job_no', 'item_master.item_code','process_master.process_name', 'production_log.ok_qty', 'production_log.accepted_qty', '');
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }
        $result = $this->pagingRows($data);
        return $result;
    }

    public function saveInward($data){
        try {
            $this->db->trans_begin();
            $setData = array();
            $setData['tableName'] = $this->production_log;
            $setData['where']['id'] = $data['job_trans_id'];
            $setData['set']['accepted_qty'] = 'accepted_qty, + '.$data['qty'];
            $result = $this->setValue($setData);
            
            // $setData = array();
            // $setData['tableName'] = $this->job_approval;
            // $setData['where']['id'] = $data['job_approval_id'];
            // $setData['set']['in_qty'] = 'in_qty, + '.$data['qty'];
            // $result = $this->setValue($setData);

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function checkFIStock($postData){
        $queryData = array();
        $queryData['tableName'] = "stock_transaction";
        $queryData['select'] = "SUM(stock_transaction.qty) as qty";
        $queryData['where']['ref_type'] = 22;
        $queryData['where']['ref_id'] = $postData['job_card_id'];
        $queryData['where']['location_id'] = $this->INSP_STORE->id;
        $queryData['group_by'][] = "stock_transaction.ref_id";
        return $this->row($queryData);
    }

    public function getPendingFirDTRows($data)
    {
        $data['tableName'] = "production_log";
        $data['select'] = "production_log.*,job_card.job_no,job_card.job_prefix,job_card.product_id,item_master.full_name,item_master.item_code,process_master.process_name";
        $data['leftJoin']['job_card'] ='job_card.id = production_log.job_card_id';
        $data['leftJoin']['item_master'] ='item_master.id = production_log.product_id';
        $data['leftJoin']['process_master'] ='process_master.id = production_log.process_id';

        $data['where']['job_card.order_status'] = 2;
        $data['where']['production_log.stage_type'] = 3;
        $data['where']['production_log.prod_type'] = 4;
        $data['having'][] = '(production_log.accepted_qty-production_log.fir_qty) > 0';

        $data['searchCol'][] = "DATE_FORMAT(production_log.log_date,'%d-%m-%Y')";
        $data['searchCol'][] = "job_card.job_no";
        $data['searchCol'][] = "item_master.item_code";
        $data['searchCol'][] = "process_master.process_name";

        $columns = array('', '','production_log.log_date', 'job_card.job_no', 'item_master.item_code','process_master.process_name', '', '', '');
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }
        $result = $this->pagingRows($data);
        // print_r($this->db->last_query());exit;
        return $result;
    }

    public function getMaxLotNoJobcardWise($postData){
        $data['tableName'] = $this->fir_master;
        $data['select'] = "MAX(fir_no) as fir_no";
        $data['where']['job_card_id'] = $postData['job_card_id'];
        $data['where']['fir_type'] =1;
        $maxNo = $this->specificRow($data)->fir_no;
		$nextFirNo = (!empty($maxNo))?($maxNo + 1):1;
		return $nextFirNo;
    }
   
    public function getMaxFgNo($postData){
        $data['tableName'] = $this->fir_master;
        $data['select'] = "MAX(fg_no) as fg_no";
        $data['where']['item_id'] = $postData['item_id'];
        $data['where']['YEAR(fir_date)'] = date("Y");
        $data['where']['MONTH(fir_date)'] = date("m");
        $data['where']['fir_type'] =1;
        $maxNo = $this->specificRow($data)->fg_no;
		$nextFgNo = (!empty($maxNo))?($maxNo + 1):1;
		return $nextFgNo;
    }
   
    public function save($data){
        try {
            $this->db->trans_begin();
            $jobData = $this->processMovement->getApprovalData($data['job_approval_id']);
            $fir = $this->getFIRMasterDetail($data['fir_id']);
            /*** FIR Master Data  Remove previous Data*/
            $setData = array();
            $setData['tableName'] = $this->fir_master;
            $setData['where']['id'] = $data['fir_id'];
            $setData['set']['total_ok_qty'] = 'total_ok_qty, - '.$fir->total_ok_qty;
            $setData['set']['total_rej_qty'] = 'total_rej_qty, - '.$fir->total_rej_qty;
            $setData['set']['total_rw_qty'] = 'total_rw_qty, - '.$fir->total_rw_qty;
            $this->setValue($setData);

            $setData = array();
            $setData['tableName'] = $this->fir_master;
            $setData['where']['id'] = $data['fir_id'];
            $setData['set']['total_ok_qty'] = 'total_ok_qty, + '.(!empty($data['total_ok_qty'])?$data['total_ok_qty']:0);
            $setData['set']['total_rej_qty'] = 'total_rej_qty, + '.(!empty($data['total_rej_qty'])?$data['total_rej_qty']:0);
            $setData['set']['total_rw_qty'] = 'total_rw_qty, + '.(!empty($data['total_rw_qty'])?$data['total_rw_qty']:0);
            $this->setValue($setData);
            /*** Fir Dimention Data */
            if(!empty($data['dimension_id'])){
                foreach($data['dimension_id'] as $key=>$value){
                    $firDimension = [
                        'id'=>$data['trans_id'][$key],
                        'fir_id'=>$data['fir_id'],
                        'dimension_id'=>$value,
                        'job_card_id'=>$data['job_card_id'],
                        'trans_date'=>$data['trans_date'][$key],
                        'in_qty'=>$data['qty'],
                        'ok_qty'=>$data['ok_qty'][$key],
                        'ud_ok_qty'=>$data['ud_ok_qty'][$key],
                        'rej_qty'=>$data['rej_qty'][$key],
                        'rw_qty'=>$data['rw_qty'][$key],
                        'inspected_qty'=>((!empty($data['ok_qty'][$key])?$data['ok_qty'][$key]:0)+(!empty($data['ud_ok_qty'][$key])?$data['ud_ok_qty'][$key]:0)+(!empty($data['rej_qty'][$key])?$data['rej_qty'][$key]:0)+(!empty($data['rw_qty'][$key])?$data['rw_qty'][$key]:0)),
                        'inspector_id'=>$data['inspector_id'][$key],
                        'remark'=>$data['dim_remark'][$key],
                        'created_by'=>$data['created_by']
                    ];
                    $result = $trans = $this->store($this->fir_dimension,$firDimension);
                  
                }
            }else{
                return ['status' => 2, 'message' => "Control Plan  Dimension is required " ];
            }
           
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getFIRMasterDetail($id){
        $data['tableName'] = $this->fir_master;
        $data['select'] = "fir_master.*,item_master.item_name,item_master.full_name,item_master.item_code,item_master.part_no,item_master.rev_no,job_card.job_no,job_card.job_prefix,job_card.order_status,job_card.process,production_approval.in_process_id,production_approval.out_process_id,next_approval.id as next_approval_id";
        $data['leftJoin']['job_card'] = "job_card.id = fir_master.job_card_id";
        $data['leftJoin']['production_approval'] = "production_approval.id = fir_master.job_approval_id";
        $data['leftJoin']['production_approval as next_approval'] = "next_approval.in_process_id = production_approval.out_process_id AND next_approval.job_card_id=production_approval.job_card_id";
        $data['leftJoin']['item_master'] = "item_master.id = fir_master.item_id";
        $data['where']['fir_master.id'] = $id ;
        return $this->row($data);
    }
 
    public function getFIRDimensionData($postData){
        $data['tableName'] = $this->fir_dimension;
        $data['select'] = "fir_dimension.*,pfc_trans.product_param,pfc_trans.char_class,pfc_trans.requirement,pfc_trans.min_req,pfc_trans.max_req,pfc_trans.other_req,pfc_trans.fir_measur_tech,pfc_trans.fir_freq,pfc_trans.fir_freq_time,pfc_trans.fir_size,pfc_trans.fir_freq_text,employee_master.emp_name";
        $data['leftJoin']['pfc_trans'] = "pfc_trans.id = fir_dimension.dimension_id";
        $data['leftJoin']['employee_master'] = 'employee_master.id = fir_dimension.inspector_id';
        $data['where']['fir_dimension.fir_id'] = $postData['fir_id'];
        $data['order_by']['fir_dimension.sequence'] ='ASC';
        return $this->rows($data);
    }

    public function getFirDTRows($data)
    {
        $data['tableName'] = $this->fir_master;
        $data['select'] = "fir_master.*,fir_master.total_ok_qty as total_fir_ok,item_master.item_name,item_master.full_name,item_master.item_code,job_card.job_no,job_card.job_prefix,job_card.order_status,job_card.process,production_approval.total_ok_qty,production_approval.out_process_id";
        $data['leftJoin']['job_card'] = "job_card.id = fir_master.job_card_id";
        $data['leftJoin']['production_approval'] = "production_approval.id = fir_master.job_approval_id";
        $data['leftJoin']['item_master'] = "item_master.id = fir_master.item_id";
        $data['where']['fir_master.fir_type'] =1;
        if(empty($data['status'])){
            $data['where']['status'] = 0;
            // $data['having'][] ='fir_master.qty > (fir_master.total_rej_qty+fir_master.total_rw_qty+fir_master.movement_qty)';
        }
        if(!empty($data['status'])){
            $data['where']['status'] = 1;
            // $data['having'][] ='fir_master.qty <= (fir_master.total_rej_qty+fir_master.total_rw_qty+fir_master.movement_qty)';

            $data['where']['fir_master.fir_date >='] = $this->startYearDate;
            $data['where']['fir_master.fir_date <='] = $this->endYearDate;
        }

        $data['order_by']['fir_master.created_at'] = "DESC";
        $data['order_by']['fir_master.id'] = "DESC";

        $data['searchCol'][] = "DATE_FORMAT(fir_master.fir_date,'%d-%m-%Y')";
        $data['searchCol'][] = "fir_master.fir_number";
        $data['searchCol'][] = "job_card.batch_no";
        $data['searchCol'][] = "job_card.job_no";
        $data['searchCol'][] = "item_master.item_code";
        $data['searchCol'][] = "fir_master.qty";

        $columns = array('', '', 'fir_master.fir_date','fir_master.fir_number','job_card.batch_no' ,'job_card.job_no', 'item_master.item_code', 'fir_master.qty');
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }
        $result = $this->pagingRows($data);
        return $result;
    }
    
    public function delete($id){
        try {
           
            $this->db->trans_begin();
            $firData = $this->getFIRMasterDetail($id);
            $dimensionData = $this->getFIRDimensionData(['fir_id'=>$id]);

            $jobTrans = $this->getFirJobTrans($id);
            foreach($jobTrans as $row){
                $this->processMovement->delete($row->id);                   
            }
            $setData = array();
            $setData['tableName'] = $this->job_approval;
            $setData['where']['id'] = $firData->job_approval_id;
            $setData['set']['outward_qty'] = 'outward_qty, - '.$firData->qty;
            $this->setValue($setData);

            $this->trash($this->fir_dimension,['fir_id'=>$id]);
            $result = $this->trash($this->fir_master,['id'=>$id]);
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getFirJobTrans($fir_id){
        $data['tableName'] ='production_log';
        $data['where']['production_log.ref_id'] = $fir_id;
        $data['where_in']['production_log.prod_type'] =8;
        return $this->rows($data);
    }

    public function saveLot($data){
        try {
         
            $this->db->trans_begin();
            $jobData = $this->processMovement->getApprovalData(['id'=>$data['job_approval_id']]);
           
            $qty = array_sum($data['lot_qty']);
            $job_trans_id = implode(",",$data['job_trans_id']);
            /*** FIR Master Data */
            $firData=[
                'id'=>$data['id'],
                'fir_date'=>$data['fir_date'],
                'fir_type'=>1,
                'job_approval_id'=>$data['job_approval_id'],
                'job_trans_id'=>$data['job_trans_id'],
                'job_card_id'=>$data['job_card_id'],
                'item_id'=>$data['item_id'],
                'qty'=>$qty,
                'job_trans_id'=>$job_trans_id,
                'live_packing'=>$data['live_packing'],
                'created_by'=>$data['created_by']
            ];
            if(empty($data['id'])){
                $lot_no = $this->getMaxLotNoJobcardWise(['job_card_id'=>$data['job_card_id']]);
                $fir_number="FIR/".(getPrefixNumber($jobData->job_prefix,$jobData->job_no)).'/'.$lot_no;
                $firData['fir_no'] = $lot_no;
                $firData['fir_prefix'] = "FIR/";
                $firData['fir_number'] = $fir_number;
                $fg_no = $this->fir->getMaxFGNo(['item_id'=>$data['item_id']]);
                $year = n2y(date('Y'));
                $month = n2m(date('m'));
                $firData['fg_no'] = $fg_no;
                $firData['fg_batch_no'] =$year.$month.sprintf('%02d',$fg_no);
            }
            $result = $this->store($this->fir_master,$firData);
            $fir_id = !empty($data['id'])?$data['id']:$result['insert_id'];

            

            foreach($data['job_trans_id'] as $key=>$value){
                $setData = array();
                $setData['tableName'] = $this->production_log;
                $setData['where']['id'] = $value;
                $setData['set']['fir_qty'] = 'fir_qty, + '.$data['lot_qty'][$key];
                $this->setValue($setData);
            }
            


            $prsData = $this->item->getPrdProcessDataProductProcessWise(['item_id'=>$jobData->product_id,'process_id'=>$jobData->out_process_id,'pfc_rev_no'=>$jobData->pfc_rev_no]);
            $firDimensionData = $this->controlPlanV2->getCPDimenstion(['item_id'=>$jobData->product_id,'control_method'=>'FIR','responsibility'=>'INSP','rev_no'=>$jobData->cp_rev_no]);

            /*** Fir Dimention Data */
            if(!empty($firDimensionData)){
                $freq='';
                
                $lot_type = 1;
                if($firDimensionData[0]->fir_freq_time =='%'){
                    $freq = TO_FLOAT($firDimensionData[0]->fir_freq);
                    // $qty = $freq * $qty/100;
                    $lot_type = 1;
                }else if($firDimensionData[0]->fir_freq_time =='Lot'){
                    // $qty =$firDimensionData[0]->fir_size;
                    $lot_type = 2;
                }
                // $qty = ceil($qty);
                $this->store($this->fir_master,['id'=>$fir_id,'lot_type'=>$lot_type,'sample_qty'=>$qty]);
                $i=1;
                foreach($firDimensionData as $row){
                    $firDimension = [
                        'id'=>'',
                        'fir_id'=>$fir_id,
                        'dimension_id'=>$row->id,
                        'job_card_id'=>$data['job_card_id'],
                        'in_qty'=>(($i == 1) ? $qty:0),
                        'ok_qty'=>(($i == 1) ? $qty:0),
                        'sequence'=>$i,
                        'created_by'=>$data['created_by']
                    ];
                    $trans = $this->store($this->fir_dimension,$firDimension);
                    $i++;
                }
            }else{
                return ['status' => 2, 'message' => "Control Plan  Dimension is required " ];
            }
            
           
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function completeFir($data){
        try {
            $this->db->trans_begin();
            $id = $data['id'];
            $firData = $this->getFIRMasterDetail($id);
            $firDimensionData = $this->getFIRDimensionData(['fir_id'=>$id]);
            $jobData = $this->jobcard_v3->getJobcard($firData->job_card_id);
            /*** Fir Dimention Data */
            $totalRejQty =0; $totalRwQty =0; 
           
            $setData = array();
            $setData['tableName'] = $this->fir_master;
            $setData['where']['id'] = $firData->id;
            $setData['set']['total_rej_qty'] = 'total_rej_qty, + '.$data['total_rej_qty'];
            $setData['set']['total_rw_qty'] = 'total_rw_qty, + '.$data['total_rw_qty'];
            $result = $this->setValue($setData);
            $result = $this->store($this->fir_master,['id'=>$id,'status'=>1],"FIR Completed Successfully");
                                   
            /*** Production Log Entry */
            $okQty = $firData->qty - ($data['total_rej_qty'] + $data['total_rw_qty']);
            $logData = [
                    'id' => '',
                    'ref_id' =>$id,
                    'prod_type' => 2,
                    'm_ct' => '',
                    'part_code' => '',
                    'product_id' =>$firData->item_id,
                    'job_approval_id' => $firData->job_approval_id,
                    'job_card_id' => $firData->job_card_id,
                    'process_id' => $firData->in_process_id,
                    'out_process_id' => $firData->out_process_id,
                    'is_approve' => 1,
                    'log_date' => date("Y-m-d"),
                    'operator_id' => 0,
                    'inspection_type' => 0,
                    'shift_id' => 0,
                    'production_time' => 0,
                    'ok_qty' => $okQty,
                    'production_qty' =>$firData->qty,
                    'rej_qty' => $data['total_rej_qty'],
                    'rej_reason' =>$data['rej_reason'],
                    'rej_remark' => '',
                    'rejection_reason' => (!empty($data['rejection_reason'])?$data['rejection_reason']:''),
                    'rework_reason' => (!empty($data['rework_reason'])?$data['rework_reason']:''),
                    'rw_qty' => $data['total_rw_qty'],
                    'rw_reason' =>'',
                    'rw_remark' => '',
                    'approved_at' =>  date("Y-m-d"),
                    'created_by' =>  $this->loginId,
                ];
                // print_r($logData);exit;
                $this->productionLog->save($logData);
           
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getRejData($postData){
        $queryData['tableName'] = "rej_rw_management";
        $queryData['select']='ifnull(SUM(CASE WHEN rej_rw_management.rej_type = 1 THEN rej_rw_management.qty END),0) as mc_rej_qty,ifnull(SUM(CASE WHEN  rej_rw_management.rej_type = 2  THEN rej_rw_management.qty END),0) as rm_rej_qty,ifnull(SUM(CASE WHEN  rej_rw_management.operation_type = 3 THEN rej_rw_management.qty END),0) as hold_qty';
        $queryData['leftJoin']['production_log'] ="production_log.id = rej_rw_management.job_trans_id";
        $queryData['where']['production_log.rej_rw_manag_id'] = $postData['fir_trans_id'];
        $queryData['where_in']['rej_rw_management.prod_type'] ="3";
        $queryData['where_in']['rej_rw_management.operation_type'] =1;
        return $this->row($queryData);
    }

    public function saveDimension($data){
        try {
            $this->db->trans_begin();
            $result  = $this->store($this->fir_dimension,$data);
                $fir = $this->getFIRDimensionDetail(['id'=>$data['id']]);
                $totalQty = $fir->ok_qty+$fir->ud_ok_qty;                
                $totalRejQty = $fir->rej_qty;           
                $totalRwQty = $fir->rw_qty;

                $nxtDimension = $this->getFIRDimensionDetail(['fir_id'=>$fir->fir_id,'sequence'=>($fir->sequence+1)]);
                if(!empty($nxtDimension)){
                    $this->store($this->fir_dimension,['id'=>$nxtDimension->id,'in_qty'=>$totalQty,'ok_qty'=>$totalQty]);
                    $this->store($this->fir_master,['id'=>$fir->fir_id,'total_rej_qty'=>$totalRejQty,'total_rw_qty'=>$totalRwQty]);
                }else{
                    $this->store($this->fir_master,['id'=>$fir->fir_id,'total_ok_qty'=>$totalQty,'total_rej_qty'=>$totalRejQty,'total_rw_qty'=>$totalRwQty]);
                }
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getFIRDimensionDetail($postData){
        $data['tableName'] = $this->fir_dimension;
        $data['select'] = "fir_dimension.*,fir_master.sample_qty,fir_master.lot_type,fir_master.qty";
        $data['leftJoin']['fir_master'] ="fir_master.id = fir_dimension.fir_id";
        if(!empty($postData['id'])){ $data['where']['fir_dimension.id'] = $postData['id']; }
        if(!empty($postData['sequence'])){ $data['where']['fir_dimension.sequence'] = $postData['sequence']; }
        if(!empty($postData['fir_id'])){ $data['where']['fir_dimension.fir_id'] = $postData['fir_id']; }
        return $this->row($data);
    }

    public function updateDimensionSequance($data){
		try{
            $this->db->trans_begin();
    		$ids = explode(',', $data['id']);
            $queryData['tableName'] = $this->fir_dimension;
            $queryData['select'] = "fir_dimension.*";
            $queryData['where']['fir_id'] = $data['fir_id'];
            $queryData['where']['in_qty > '] = 0;
            $queryData['where']['inspected_qty'] = 0;
            $prevData = $this->row($queryData);

    		$i=($prevData->sequence+1);
    		foreach($ids as $pp_id):
    			$seqData=Array("sequence"=>$i++);
    			$this->edit($this->fir_dimension,['id'=>$pp_id],$seqData);
    		endforeach;
    		$result = ['status'=>1,'message'=>'Dimension Sequence updated successfully.'];
    		if ($this->db->trans_status() !== FALSE):
    			$this->db->trans_commit();
    			return $result;
    		endif;
    	}catch(\Exception $e){
    		$this->db->trans_rollback();
    	    return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
    	}	
	}

    public function getDimensionOnSequence($postData){
        $data['tableName'] = $this->fir_dimension;
        $data['select'] = "fir_dimension.*";
        $data['where']['fir_dimension.fir_id'] = $postData['fir_id'];
        $data['where']['fir_dimension.sequence <='] = $postData['sequence']; 
        return $this->rows($data);
    }

    public function getFIRPendingJobTrans($postData){
        $data['tableName'] = "production_log";
        $data['select'] = "production_log.*,job_card.job_no,job_card.job_prefix,process_master.process_name";
        $data['leftJoin']['job_card '] ='job_card.id = production_log.job_card_id';
        $data['leftJoin']['production_approval as crnt_approval'] ='crnt_approval.in_process_id = production_log.out_process_id AND crnt_approval.job_card_id = production_log.job_card_id';
        $data['leftJoin']['process_master '] ='process_master.id = production_log.process_id';
        $data['where']['production_log.job_card_id'] = $postData['job_card_id'];
        // $data['where']['production_log.vendor_id'] = $postData['vendor_id'];
        $data['where']['crnt_approval.stage_type'] = 3;
        $data['where']['production_log.prod_type'] = 4;
        $data['having'][] = '(production_log.accepted_qty-production_log.fir_qty) > 0';
        return $this->rows($data);
    }

    public function clearDimension($data){ 
        try {
            $this->db->trans_begin();
            $fir = $this->getFIRDimensionDetail(['id'=>$data['id']]);
            $result  = $this->store($this->fir_dimension,['id'=>$data['id'],'inspector_id'=>0,'inspected_qty'=>0]);
            $nxtDimension = $this->getFIRDimensionDetail(['fir_id'=>$fir->fir_id,'sequence'=>($fir->sequence+1)]);
            if(!empty($nxtDimension)){
                $this->store($this->fir_dimension,['id'=>$nxtDimension->id,'in_qty'=>0,'ok_qty'=>0,'rej_qty'=>0,'rw_qty'=>0,'inspector_id'=>0,'inspected_qty'=>0]);
                $this->store($this->fir_master,['id'=>$fir->fir_id,'total_ok_qty'=>0]);
            }
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function saveSampleQty($data){
        try {
            $this->db->trans_begin();
            $result = $this->store($this->fir_master,['id'=>$data['fir_id'],'sample_qty'=>$data['sample_qty']]);

            $dimensionData = $this->getFIRDimensionDetail(['sequence'=>1,'fir_id'=>$data['fir_id']]);
            $this->store($this->fir_dimension,['id'=>$dimensionData->id,'in_qty'=>$data['sample_qty'],'ok_qty'=>$data['sample_qty']]);
            
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }
    
    public function getTCData($data) {
        $queryData['tableName'] = $this->grnTestReport;
        $queryData['select'] = "grn_test_report.*";
        $queryData['leftJoin']['job_used_material'] = "job_used_material.batch_no = grn_test_report.mill_tc";
        if(!empty($data['job_card_id'])){$queryData['where']['job_used_material.job_card_id'] = $data['job_card_id'];}
        return $this->rows($queryData);
    }
}
