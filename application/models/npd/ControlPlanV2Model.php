<?php
class ControlPlanV2Model extends MasterModel
{
    private $qcFmea = "qc_fmea";
    private $pfcMaster = "pfc_master";
    private $pfcTrans = "pfc_trans";
    private $itemMaster = "item_master";
    private $pfc_rev_master = "pfc_rev_master";

    public function getProdOptDTRows($data,$type=0){
        $data['tableName'] = $this->itemMaster;
        $data['select'] = "item_master.*,pfc.total_pfc,revision.rev_count";
        $data['leftJoin']['(SELECT COUNT(id) AS rev_count,item_id FROM item_rev_master WHERE   is_delete = 0 GROUP BY item_id) AS revision'] = 'revision.item_id = item_master.id';
        $data['leftJoin']['(SELECT COUNT(id) AS total_pfc,item_id FROM pfc_master WHERE   is_delete = 0 GROUP BY item_id) AS pfc'] = 'pfc.item_id = item_master.id';
        $data['where']['item_type'] = 1;
        $data['order_by']['pfc.total_pfc'] = 'DESC';
        $data['order_by']['revision.rev_count'] = 'DESC';
        $data['order_by']['item_master.id'] = 'ASC';
        $columns = array();
        $data['searchCol'][] = "";
        $data['searchCol'][] = "item_master.item_code";
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        $columns =array('','','item_master.item_code','item_master.item_name','','');

        return $this->pagingRows($data);
    }

    /**************** PFC*****************/
    public function getPFCDTRows($data)
    {
        $data['tableName'] = $this->pfcMaster;
        $data['select'] = 'pfc_master.*,item_master.full_name,IFNULL(job.job_count,0) as job_count';
        $data['leftJoin']['item_master'] = 'item_master.id = pfc_master.item_id';
        $data['leftJoin']['(SELECT COUNT(id) as job_count,product_id FROM job_card WHERE is_delete =0 GROUP BY product_id) job'] = 'job.product_id = pfc_master.item_id';
        $data['where']['entry_type'] = 1;
        $data['where']['is_active'] = 1;
        $data['where']['item_id'] = $data['item_id'];

        $data['searchCol'][] = "pfc_master.trans_number";
        $data['searchCol'][] = "item_master.full_name";
        $data['searchCol'][] = "pfc_master.app_rev_no";
        $data['searchCol'][] = 'pfc_master.app_rev_date';
        $columns = array('', '', 'pfc_master.trans_number', 'item_master.full_name', 'pfc_master.app_rev_no', 'pfc_master.app_rev_date');
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }
        $result = $this->pagingRows($data);
        return $result;
    }


    public function savePfc($masterData, $transData)
    {
        try {
            $this->db->trans_begin();
            if (!empty($masterData['ref_id'])) {
                $this->edit($this->pfcMaster, ['item_id' => $masterData['item_id'], 'entry_type' => 1], ['is_active' => 0]);
            }
            $result = $this->store($this->pfcMaster, $masterData, 'Control Plan');
            $main_id = !empty($masterData['id']) ? $masterData['id'] : $result['insert_id'];
            if (!empty($masterData['id'])) :
                $pfcTrans = $this->getPfcTransData($masterData['id']);
                foreach ($pfcTrans as $row) {
                    if (!in_array($row->id, $transData['id'])) :
                        $queryData = array();
                        $queryData['tableName'] = $this->pfcMaster;
                        $queryData['where']['pfc_master.ref_id'] = $row->id;
                        $queryData['where']['pfc_master.entry_type'] = 2;
                        $cpData =  $this->row($queryData);
                        if(!empty($cpData)){
                            $this->deleteControlPlan($cpData->id);
                        }
                        $this->trash($this->pfcTrans, ['id' => $row->id]);
                    endif;
                }
            endif;
            foreach ($transData['process_no'] as $key => $value) :
                $childData = [
                    'id' => $transData['id'][$key],
                    'trans_main_id' => $main_id,
                    'entry_type' => $masterData['entry_type'],
                   
                    'item_id' => $masterData['item_id'],
                    'process_no' => $value,
                    'process_code' => $transData['process_code'][$key],
                    'machine_tool' => !empty($transData['machine_tool'][$key])?$transData['machine_tool'][$key]:'',
                    'product_param' => $transData['product_param'][$key],
                    'symbol_1' => $transData['symbol_1'][$key],
                    'char_class' => $transData['char_class'][$key],
                    'output_operation' => $transData['output_operation'][$key],
                    'stage_type' => $transData['stage_type'][$key],
                    'created_by' => $masterData['created_by']
                ];
                if(!empty($masterData['app_rev_no'])){
                    $childData['rev_no'] =$masterData['app_rev_no'];
                }
                $result = $this->store($this->pfcTrans, $childData, 'Control Plan');
            endforeach;
            
            $result = ['status' => 1, 'message' => "PFC saved successfully", 'url' => base_url("npd/controlPlan/pfcList/" . $masterData['item_id'])];
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getPfcData($id)
    {
        $queryData = array();
        $queryData['tableName'] = $this->pfcMaster;
        $queryData['select'] = "pfc_master.*,employee_master.emp_name,item_master.item_code,item_master.full_name,item_master.drawing_no,item_master.rev_no,item_master.part_no,item_master.item_code,item_master.item_name,item_master.party_id,party_master.vendor_code";
        $queryData['leftJoin']['employee_master'] = 'employee_master.id = pfc_master.created_by';
        $queryData['leftJoin']['item_master'] = 'item_master.id = pfc_master.item_id';
        $queryData['leftJoin']['party_master'] = 'party_master.id = item_master.party_id';
        $queryData['where']['pfc_master.id'] = $id;
        return $this->row($queryData);
    }

    public function getPfcTransData($id)
    {
        $queryData = array();
        $queryData['tableName'] = $this->pfcTrans;
        $queryData['select'] = "pfc_trans.*,party_master.party_name";
        $queryData['leftJoin']['party_master'] = 'party_master.id = pfc_trans.vendor_id';
        $queryData['where']['trans_main_id'] = $id;
        $queryData['order_by']['pfc_trans.process_no'] = 'ASC';
        return $this->rows($queryData);
    }

    public function getPfcTrans($id)
    {
        $queryData = array();
        $queryData['tableName'] = $this->pfcTrans;
        $queryData['select'] = "pfc_trans.*";
        $queryData['where']['pfc_trans.id'] = $id;
        return $this->row($queryData);
    }

    /* Updated By :- Sweta @28-08-2023 */
    public function getItemWisePfcData($item_id,$pfc_rev_no='',$not_in="")
    {
        $data['tableName'] = $this->pfcTrans;
        $data['select'] = "pfc_trans.*,pfc_master.trans_number,item_master.item_name,item_master.item_code";
        $data['leftJoin']['pfc_master'] = 'pfc_master.id = pfc_trans.trans_main_id';
        $data['leftJoin']['item_master'] = 'item_master.id = pfc_trans.item_id';
        $data['where']['pfc_trans.item_id'] = $item_id;
        $data['where']['pfc_master.entry_type'] = 1;
		if(!empty($pfc_rev_no)){ $data['customWhere'][] = "find_in_set('".$pfc_rev_no."',pfc_trans.rev_no) > 0"; }
        if(!empty($not_in)){
            $data['customWhere'][] = 'process_code NOT IN("","RMDI","RMMT","DISP")';
        }
        $data['order_by']['pfc_trans.process_no']='ASC';
        return $this->rows($data);
    }

    public function getPfcProcessWise($data)
    {
        $data['tableName'] = $this->pfcTrans;
        $data['select'] = "pfc_trans.*";
        $data['where']['pfc_trans.item_id'] = $data['item_id'];
        $data['where']['pfc_trans.process_no'] = $data['process_no'];
        return $this->row($data);
    }

    public function getPfcForProcess($ids)
    {
        $queryData['tableName'] = $this->pfcTrans;
        $queryData['where_in']['id'] = $ids;
        return $this->rows($queryData);
    }
   

    /*********Control Plan*********/
    public function getControlPlanDTRows($data)
    {
        $data['tableName'] = $this->pfcMaster;
        $data['select'] = 'pfc_master.*,item_master.full_name,pfc_trans.process_no,pfc_trans.process_code,pfc_trans.product_param,IFNULL(job.job_count,0) as job_count';
        $data['leftJoin']['pfc_trans'] = "pfc_trans.id = pfc_master.ref_id";
        $data['leftJoin']['item_master'] = 'item_master.id = pfc_master.item_id';
        $data['leftJoin']['(SELECT COUNT(id) as job_count,product_id FROM job_card WHERE is_delete =0 GROUP BY product_id) job'] = 'job.product_id = pfc_master.item_id';
        $data['where']['pfc_master.entry_type'] = 2;
        if(!empty($data['item_id'])){$data['where']['pfc_master.item_id'] = $data['item_id'];}
        if(!empty($data['pfc_id'])){$data['where']['pfc_trans.trans_main_id'] = $data['pfc_id'];}
        $data['order_by']['pfc_trans.process_no'] = 'ASC';

        $data['searchCol'][] = "pfc_master.trans_number";
        $data['searchCol'][] = "CONCATE(pfc_trans.process_no,pfc_trans.product_param)";
        $data['searchCol'][] = "pfc_master.app_rev_no";
        $data['searchCol'][] = 'pfc_master.app_rev_date';
        $data['searchCol'][] = "pfc_master.cust_rev_no";
        $columns = array('', '', 'pfc_master.trans_number', 'pfc_trans.process_no', 'product_param', 'pfc_master.app_rev_no', 'pfc_master.app_rev_date', 'pfc_master.cust_rev_no');
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }
        $result = $this->pagingRows($data);
        return $result;
    }

    
    public function getCPDiamentionDTRows($data)
    {
        $data['tableName'] = $this->pfcTrans;
        $data['select'] = 'pfc_trans.*,pfc_master.trans_number';
        $data['leftJoin']['pfc_master'] = 'pfc_trans.trans_main_id = pfc_master.id';
        $data['where']['pfc_master.entry_type'] = 2;
        // $data['where']['pfc_master.is_active'] = 1;
        $data['where']['pfc_trans.trans_main_id'] = $data['cp_id'];

        $data['searchCol'][] = "pfc_trans.product_param";
        $data['searchCol'][] = "pfc_trans.other_req";
        $data['searchCol'][] = "pfc_trans.iir_measur_tech";
        $data['searchCol'][] = "pfc_trans.iir_size";
        $data['searchCol'][] = "pfc_trans.iir_freq";
        $data['searchCol'][] = "pfc_trans.iir_freq_time";
        $data['searchCol'][] = "pfc_trans.iir_freq_text";
        $data['searchCol'][] = "pfc_trans.opr_measur_tech";
        $data['searchCol'][] = "pfc_trans.opr_size";
        $data['searchCol'][] = "pfc_trans.opr_freq";
        $data['searchCol'][] = "pfc_trans.opr_freq_time";
        $data['searchCol'][] = "pfc_trans.opr_freq_text";
        $data['searchCol'][] = "pfc_trans.ipr_measur_tech";
        $data['searchCol'][] = "pfc_trans.ipr_size";
        $data['searchCol'][] = "pfc_trans.ipr_freq";
        $data['searchCol'][] = "pfc_trans.ipr_freq_time";
        $data['searchCol'][] = "pfc_trans.ipr_freq_text";
        $data['searchCol'][] = "pfc_trans.sar_measur_tech";
        $data['searchCol'][] = "pfc_trans.sar_size";
        $data['searchCol'][] = "pfc_trans.sar_freq";
        $data['searchCol'][] = "pfc_trans.sar_freq_time";
        $data['searchCol'][] = "pfc_trans.sar_freq_text";
        $data['searchCol'][] = "pfc_trans.spc_measur_tech";
        $data['searchCol'][] = "pfc_trans.spc_size";
        $data['searchCol'][] = "pfc_trans.spc_freq";
        $data['searchCol'][] = "pfc_trans.spc_freq_time";
        $data['searchCol'][] = "pfc_trans.spc_freq_text";
        $data['searchCol'][] = "pfc_trans.fir_measur_tech";
        $data['searchCol'][] = "pfc_trans.fir_size";
        $data['searchCol'][] = "pfc_trans.fir_freq";
        $data['searchCol'][] = "pfc_trans.fir_freq_time";    
        $data['searchCol'][] = "pfc_trans.fir_freq_text";

        $columns = array('', 'pfc_trans.product_param','pfc_trans.min_req','pfc_trans.max_req','pfc_trans.iir_measur_tech', 'pfc_trans.iir_size','pfc_trans.iir_freq','pfc_trans.iir_freq_time','pfc_trans.iir_freq_text','pfc_trans.opr_measur_tech', 'pfc_trans.opr_size', 'pfc_trans.opr_freq','pfc_trans.opr_freq_time','pfc_trans.opr_freq_text', 'pfc_trans.ipr_measur_tech', 'pfc_trans.ipr_size','pfc_trans.ipr_freq','pfc_trans.ipr_freq_time','pfc_trans.ipr_freq_text','pfc_trans.sar_measur_tech','pfc_trans.sar_size','pfc_trans.sar_freq','pfc_trans.sar_freq_time','pfc_trans.sar_freq_text', 'pfc_trans.spc_measur_tech','pfc_trans.spc_size','pfc_trans.spc_freq', 'pfc_trans.spc_freq_time','pfc_trans.spc_freq_text','pfc_trans.fir_measur_tech','pfc_trans.fir_size', 'pfc_trans.fir_freq', 'pfc_trans.fir_freq_time','pfc_trans.fir_freq_text',"");
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }
        $result = $this->pagingRows($data);
        return $result;
    }

    public function getControlPlan($id="")
    {
        $queryData = array();
        $queryData['tableName'] = $this->pfcMaster;
        $queryData['select'] = "pfc_master.*,pfc_trans.process_no,pfc_trans.process_code,pfc_trans.machine_tool,pfc_trans.product_param,pfc_trans.process_param,pfc_trans.jig_fixture_no,employee_master.emp_name as preparedBy,approve.emp_name as approveBy,item_master.item_code,item_master.full_name,item_master.drawing_no,item_master.rev_no,item_master.part_no,item_master.party_id,party_master.vendor_code,pfcMaster.core_team as coreTeam,pfcMaster.eng_change_level,pfcMaster.product_phase,pfcMaster.id as pfc_main_id,item_master.material_grade,item_master.item_name,pfc_trans.trans_main_id";
        $queryData['leftJoin']['employee_master'] = 'employee_master.id = pfc_master.created_by';
        $queryData['leftJoin']['employee_master approve'] = 'approve.id = pfc_master.approved_by';
        $queryData['leftJoin']['item_master'] = 'item_master.id = pfc_master.item_id';
        $queryData['leftJoin']['party_master'] = 'party_master.id = item_master.party_id';
        $queryData['leftJoin']['pfc_trans'] = "pfc_trans.id = pfc_master.ref_id";
        $queryData['leftJoin']['pfc_master as pfcMaster'] = "pfcMaster.id = pfc_trans.trans_main_id";
        if(!empty($id)){$queryData['where']['pfc_master.id'] = $id;}
        return $this->row($queryData);
    }
    public function getCPTrans($id)
    {
        $queryData = array();
        $queryData['tableName'] = $this->pfcTrans;
        $queryData['select'] = "pfc_trans.*";
        $queryData['where']['pfc_trans.id'] = $id;
        return $this->row($queryData);
    }



    public function getCPTransData($id, $rev_no = '')
    {
        $queryData = array();
        $queryData['tableName'] = $this->pfcTrans;
        $queryData['select'] = "pfc_trans.*";
        $queryData['where']['trans_main_id'] = $id;
        if (!empty($rev_no)) {
            $queryData['customWhere'][] =  "find_in_set('".$rev_no."',pfc_trans.rev_no) > 0"; 
        }
        return $this->rows($queryData);
    }

    public function saveCPDimension($data)
    {
        try {
            $this->db->trans_begin();

            $pfcTrans = $this->getCPTransData($data['trans_main_id']);
            foreach ($pfcTrans as $row) {
                if (!in_array($row->id, $data['trans_id'])) :
                    $this->trash($this->pfcTrans, ['id' => $row->id]);
                endif;
            }
            foreach ($data['product_param'] as $key => $value) {
                $min = "";$max="";
                switch ($data['requirement'][$key]) {
                    case 1:
                        if(strpos($data['other_req'][$key],'/') != false){
                            $exp = explode('/',$data['other_req'][$key]);
                            $min =(!empty($exp[0]))? TO_FLOAT($exp[0]):'';
                            $max = !empty($exp[1])?TO_FLOAT($exp[1]):'';
                        }elseif(strpos($data['other_req'][$key],'±') != false){
                            $exp = explode('±',$data['other_req'][$key]);
                            $min =(!empty($exp[0]))? (TO_FLOAT($exp[0])-TO_FLOAT($exp[1])):'';
                            $max = !empty($exp[1])?(TO_FLOAT($exp[0])+TO_FLOAT($exp[1])):'';
                        }
                        break;
                    case 2:
                        $rowData['type'] = 2;
                        $min = TO_FLOAT($data['other_req']);
                        $max = '';
                        break;
                    case 3:
                        $rowData['type'] = 3;
                        $max =TO_FLOAT($data['other_req']);
                        $min = '';
                        break;
                    case 4:
                        $rowData['type'] = 4;
                        $min = $max = '';
                        break;
                }

                $childData = [
                    'id' => $data['trans_id'][$key],
                    'entry_type' => 2,
                    'trans_main_id' => $data['trans_main_id'],
                    'pfc_id' => $data['pfc_id'],
                    'process_no' => $data['process_no'],
                    'item_id' => $data['item_id'],
                    'product_param' => $data['product_param'][$key],
                    'process_param' => $data['process_param'][$key],
                    'requirement' => $data['requirement'][$key],
                    'min_req' => $min,
                    'max_req' => $max,
                    'other_req' => $data['other_req'][$key],
                    'char_class' => $data['char_class'][$key],

                    
                    'iir_measur_tech' => !empty($data['iir_measur_tech'][$key]) ? $data['iir_measur_tech'][$key] : '',
                    'iir_size' => !empty($data['iir_size'][$key]) ? $data['iir_size'][$key] : '',
                    'iir_freq' => !empty($data['iir_freq'][$key]) ? $data['iir_freq'][$key] : '',
                    'iir_freq_time' => !empty($data['iir_freq_time'][$key]) ? $data['iir_freq_time'][$key] : '',
                    'iir_freq_text' => !empty($data['iir_freq_text'][$key]) ? $data['iir_freq_text'][$key] : '',

                    'opr_measur_tech' => !empty($data['opr_measur_tech'][$key]) ? $data['opr_measur_tech'][$key] : '',
                    'opr_size' => !empty($data['opr_size'][$key]) ? $data['opr_size'][$key] : '',
                    'opr_freq' => !empty($data['opr_freq'][$key]) ? $data['opr_freq'][$key] : '',
                    'opr_freq_time' => !empty($data['opr_freq_time'][$key]) ? $data['opr_freq_time'][$key] : '',
                    'opr_freq_text' => !empty($data['opr_freq_text'][$key]) ? $data['opr_freq_text'][$key] : '',

                    'ipr_measur_tech' => !empty($data['ipr_measur_tech'][$key]) ? $data['ipr_measur_tech'][$key] : '',
                    'ipr_size' => !empty($data['ipr_size'][$key]) ? $data['ipr_size'][$key] : '',
                    'ipr_freq' => !empty($data['ipr_freq'][$key]) ? $data['ipr_freq'][$key] : '',
                    'ipr_freq_time' => !empty($data['ipr_freq_time'][$key]) ? $data['ipr_freq_time'][$key] : '',
                    'ipr_freq_text' => !empty($data['ipr_freq_text'][$key]) ? $data['ipr_freq_text'][$key] : '',

                    'sar_measur_tech' => !empty($data['sar_measur_tech'][$key]) ? $data['sar_measur_tech'][$key] : '',
                    'sar_size' => !empty($data['sar_size'][$key]) ? $data['sar_size'][$key] : '',
                    'sar_freq' => !empty($data['sar_freq'][$key]) ? $data['sar_freq'][$key] : '',
                    'sar_freq_time' => !empty($data['sar_freq_time'][$key]) ? $data['sar_freq_time'][$key] : '',
                    'sar_freq_text' => !empty($data['sar_freq_text'][$key]) ? $data['sar_freq_text'][$key] : '',

                    'spc_measur_tech' => !empty($data['spc_measur_tech'][$key]) ? $data['spc_measur_tech'][$key] : '',
                    'spc_size' => !empty($data['spc_size'][$key]) ? $data['spc_size'][$key] : '',
                    'spc_freq' => !empty($data['spc_freq'][$key]) ? $data['spc_freq'][$key] : '',
                    'spc_freq_time' => !empty($data['spc_freq_time'][$key]) ? $data['spc_freq_time'][$key] : '',
                    'spc_freq_text' => !empty($data['spc_freq_text'][$key]) ? $data['spc_freq_text'][$key] : '',

                    'fir_measur_tech' => !empty($data['fir_measur_tech'][$key]) ? $data['fir_measur_tech'][$key] : '',
                    'fir_size' => !empty($data['fir_size'][$key]) ? $data['fir_size'][$key] : '',
                    'fir_freq' => !empty($data['fir_freq'][$key]) ? $data['fir_freq'][$key] : '',
                    'fir_freq_time' => !empty($data['fir_freq_time'][$key]) ? $data['fir_freq_time'][$key] : '',
                    'fir_freq_text' => !empty($data['fir_freq_text'][$key]) ? $data['fir_freq_text'][$key] : '',
                    'created_by' => $this->session->userdata('loginId')
                ];
                $transResult = $this->store($this->pfcTrans, $childData, 'Control Plan');
            }
            $result = ['status' => 1, 'message' => "Dimension saved successfully", 'url' => base_url("npd/controlPlan/controlPlanList/" . $data['pfc_main_id'])];
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function saveCPForExcel($data)
    {
     
        try {
            $this->db->trans_begin();
            foreach ($data as $row) {
                
                /** Control Plan Master **/
                $masterDataCp = [
                    'id' => '',
                    'entry_type' => 2,
                    'trans_number' => 'CP/' . $row['trans_number'],
                    // 'cust_rev_no' => $row['cust_rev_no'],
                    // 'app_rev_no' => $row['app_rev_no'],
                    'item_id' => $row['item_id'],
                    'ref_id' => $row['ref_id'],
                ];
                $masterResultCp = $this->store($this->pfcMaster, $masterDataCp);

                $i = 1;
                foreach ($row['dimensionData'] as $dimension) {
                    $dimension['id'] = "";
                    $dimension['entry_type'] = 2;
                    $dimension['trans_main_id'] = $masterResultCp['insert_id'];
                    $dimension['created_by'] = $this->loginId;
                    $result = $this->store($this->pfcTrans, $dimension);
                }
            }
            // $revCount = $this->checkDuplicateRev(['rev_type'=>2,'pfc_id'=>$data[0]['pfc_main_id'],'rev_no'=>$data[0]['app_rev_no']]);
            // if(empty($revCount)){
            //     $pfcRevData=[
            //         'id'=>'',
            //         'rev_type'=>2,
            //         'pfc_id'=>$data[0]['pfc_main_id'],
            //         'rev_no'=>$data[0]['app_rev_no'],
            //         'item_id'=>$data[0]['item_id'],
            //         'rev_date'=>date("Y-m-d"),
            //         'created_by'=>$this->loginId
            //     ];
            //     $this->store($this->pfc_rev_master,$pfcRevData);
            // }
            $result = ['status' => 1, 'message' => "Dimension saved successfully"];
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getCPDimenstion($postData)
    {
        $data['tableName'] = $this->pfcTrans;
        $data['select'] = 'pfc_trans.*,pfc_master.trans_number,pfc.process_no,pfc.product_param as operation,pfc.rev_no AS main_pfc_rev_no';
        $data['join']['pfc_master'] = 'pfc_trans.trans_main_id = pfc_master.id';
        $data['leftJoin']['pfc_trans as pfc'] = 'pfc_master.ref_id = pfc.id';
        $data['where']['pfc_master.entry_type'] =2;
        
        if (!empty($postData['rmd'])) {
            if (empty($postData['pfc_id'])){$data['where']['pfc.process_no'] = 5;} // Used for RM Dimansion  
        } else {
            if (!empty($postData['process_no'])) {$data['where_in']['pfc.process_no'] = $postData['process_no'];}
        }
        
        if (!empty($postData['stage_type'])) {
            $data['where']['pfc.stage_type'] = $postData['stage_type'];
        }
        
        if (!empty($postData['ref_item_id'])) {
            $data['select'] = 'pfc_trans.*';
            $data['join']['( SELECT item_id FROM item_kit WHERE item_kit.ref_item_id='.$postData['ref_item_id'].' AND item_kit.is_delete=0 AND `item_kit`.`process_id` = 0 LIMIT 1) as kit'] = 'kit.item_id = pfc_master.item_id';
            $data['customWhere'][]="(pfc_trans.iir_measur_tech IS NOT NULL AND  pfc_trans.iir_measur_tech !='')";
        } else {
            $data['where']['pfc_master.item_id'] = $postData['item_id'];
        }

        if (!empty($postData['control_method'])) {
            if($postData['control_method'] == 'IIR'){
                $data['customWhere'][]="(pfc_trans.iir_measur_tech IS NOT NULL AND  pfc_trans.iir_measur_tech !='')";
            }

            if($postData['control_method'] == 'PIR'){
                $data['customWhere'][]="(pfc_trans.ipr_measur_tech IS NOT NULL AND  pfc_trans.ipr_measur_tech !='')";
            }

            if($postData['control_method'] == 'FIR'){
                $data['customWhere'][]="(pfc_trans.fir_measur_tech IS NOT NULL AND  pfc_trans.fir_measur_tech !='')";
            }

            if($postData['control_method'] == 'SAR'){
                $data['customWhere'][]="(pfc_trans.sar_measur_tech IS NOT NULL AND  pfc_trans.sar_measur_tech !='')";
            }

            if($postData['control_method'] == 'RQC'){
                $data['customWhere'][]="(pfc_trans.iir_measur_tech IS NOT NULL AND  pfc_trans.iir_measur_tech !='')";
            }
        }
        
        if (!empty($postData['pfc_id'])) {
            $data['where_in']['pfc_master.ref_id'] = $postData['pfc_id'];
        }

        if (!empty($postData['rev_no'])) {
            $data['customWhere'][] =  "find_in_set('".$postData['rev_no']."',pfc_trans.rev_no) > 0"; 
        }

        if (!empty($postData['pfc_main_id'])) {
            $data['where_in']['pfc.trans_main_id'] = $postData['pfc_main_id'];
            $data['order_by']['pfc.process_no'] = 'ASC';
        }

        $paramData =$this->rows($data);
        return $paramData;
    }

    public function activeCPDiamention($data)
    {
        try {
            $this->db->trans_begin();
            $result = $this->store($this->pfcTrans, ['id' => $data['id'], 'is_active' => $data['is_active']]);
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getActiveDimension($postData)
    {
        $data['tableName'] = $this->pfcTrans;
        $data['select'] = 'pfc_trans.*';
        $data['where']['pfc_trans.entry_type'] = 3;
        $data['where']['pfc_trans.pfc_id'] = $postData['pfc_id'];
        $data['where']['pfc_trans.parameter_type'] = $postData['parameter_type'];
        $data['where']['pfc_trans.is_active'] = 1;
        return $this->rows($data);
    }

    public function getCpDimensionData($postData)
    {
        $queryData['tableName'] = $this->pfcTrans;
        $queryData['select'] = 'pfc_trans.id';
        if(!empty($postData['entry_type'])){$queryData['where']['pfc_trans.entry_type'] = $postData['entry_type'];}
        if(!empty($postData['ref_id'])){$queryData['where']['pfc_trans.ref_id'] = $postData['ref_id'];}
        if(!empty($postData['parameter_type'])){$queryData['where']['pfc_trans.parameter_type'] = $postData['parameter_type'];}
        if(!empty($postData['item_id'])){$queryData['where']['pfc_trans.item_id'] = $postData['item_id'];}
        $cpResult = $this->row($queryData);
        return $cpResult;
    }

    public function deleteControlPlan($id)
    {
        try {
            $this->db->trans_begin();

            $pfcTrans = $this->getCPTransData($id);
            foreach ($pfcTrans as $row) {
                $this->trash($this->pfcTrans, ['id' => $row->id]);
            }
            $result = $this->trash($this->pfcMaster,['id'=>$id]);
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }


    public function deletePfc($id)
    {
        try {
            $this->db->trans_begin();
            /**CP DATA**/
            $pfcOperations = $this->getPfcTransData($id);
            foreach($pfcOperations as $row){
                $queryData = array();
                $queryData['tableName'] = $this->pfcMaster;
                $queryData['where']['pfc_master.ref_id'] = $row->id;
                $queryData['where']['pfc_master.entry_type'] = 2;
                $cpData =  $this->row($queryData);
                if(!empty($cpData)){
                    $this->deleteControlPlan($cpData->id);
                }
            }
            $this->trash($this->pfcTrans, ['trans_main_id' => $id], "Record");
            $result = $this->trash($this->pfcMaster, ['id' => $id], "Record");

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getSampleTitle($data){
        $data['tableName'] = 'reaction_plan';
        $data['where']['type'] = 3;
        $data['where']['control_method'] =$data['control_method'];
        return $this->row($data);
    }

	//Updated By: NYN 20012024
    public function checkPFCStage($postData){
        $queryData['tableName'] = $this->pfcTrans;
        $queryData['where_in']['process_no'] = $postData['pfc_id'];
        if(!empty($postData['item_id'])){ $queryData['where']['item_id'] = $postData['item_id']; }
        if(!empty($postData['rev_no'])){ 
			//$queryData['where']['rev_no'] = $postData['rev_no']; 
			$queryData['customWhere'][] = "FIND_IN_SET('".$postData['rev_no']."', rev_no)";
		}
        $queryData['entry_type'] = 1;
        $queryData['order_by']['id']='ASC';
        $queryData['limit']=1;
        return $this->row($queryData);
    }

    public function saveCpRevision($data){
        try {
            $this->db->trans_begin();

            if(isset($data['dimension_id'])){
                foreach($data['dimension_id'] as $key=>$id){
                    $this->edit($this->pfcTrans,['id'=>$id],['rev_no'=>$data['rev_no'][$key]]);
                }
            }
            
            $pfcData = $this->controlPlanV2->getPfcData($data['pfc_main_id']);
            if(isset($data['new_dimension_id']) && !empty($data['new_dimension_id'])){
                foreach($data['new_dimension_id'] as $key=>$id){
                    $pfcQuery['tableName']=$this->pfcMaster;
                    $pfcQuery['select'] ="id";
                    $pfcQuery['where']['ref_id'] = $data['pfc_id'];
                    $pfcQuery['where']['entry_type'] = 2;
                    $pfcOpData = $this->row($pfcQuery);
                    if(empty($pfcOpData)){
                        $opData = $this->getCPTrans($data['pfc_id']);
    
                        $masterData=[
                            'id'=>'',
                            'item_id'=>$pfcData->item_id,
                            'ref_id'=>$data['pfc_id'],
                            'app_rev_no'=>$data['new_rev_no'][$key],
                            'entry_type'=>2,
                            'trans_number'=>'CP/'.$pfcData->item_code.'/'.$opData->process_code
                        ];
                        $masterResult = $this->store($this->pfcMaster, $masterData, 'Control Plan');
                        $trans_main_id = $masterResult['insert_id'];
                    }else{
                        $trans_main_id = $pfcOpData->id;
                    }
                    $min = "";$max="";
                    switch ($data['requirement'][$key]) {
                        case 1:
                            if(strpos($data['other_req'][$key],'/') != false){
                                $exp = explode('/',$data['other_req'][$key]);
                                $min =(!empty($exp[0]))? TO_FLOAT($exp[0]):'';
                                $max = !empty($exp[1])?TO_FLOAT($exp[1]):'';
                            }elseif(strpos($data['other_req'][$key],'±') != false){
                                $exp = explode('±',$data['other_req'][$key]);
                                $min =(!empty($exp[0]))? (TO_FLOAT($exp[0])-TO_FLOAT($exp[1])):'';
                                $max = !empty($exp[1])?(TO_FLOAT($exp[0])+TO_FLOAT($exp[1])):'';
                            }
                            break;
                        case 2:
                            $rowData['type'] = 2;
                            $min = TO_FLOAT($data['other_req']);
                            $max = '';
                            break;
                        case 3:
                            $rowData['type'] = 3;
                            $max =TO_FLOAT($data['other_req']);
                            $min = '';
                            break;
                        case 4:
                            $rowData['type'] = 4;
                            $min = $max = '';
                            break;
                    }
    
                    $childData = [
                        'id' => '',
                        'entry_type' => 2,
                        'rev_no' => $data['new_rev_no'][$key],
                        'trans_main_id' => $trans_main_id,
                        'pfc_id' => $data['pfc_id'],
                        'item_id' => $pfcData->item_id,
                        'product_param' => $data['product_param'][$key],
                        'process_param' => $data['process_param'][$key],
                        'requirement' => $data['requirement'][$key],
                        'min_req' => $min,
                        'max_req' => $max,
                        'other_req' => $data['other_req'][$key],
                        'char_class' => $data['char_class'][$key],
    
                        
                        'iir_measur_tech' => !empty($data['iir_measur_tech'][$key]) ? $data['iir_measur_tech'][$key] : '',
                        'iir_size' => !empty($data['iir_size'][$key]) ? $data['iir_size'][$key] : '',
                        'iir_freq' => !empty($data['iir_freq'][$key]) ? $data['iir_freq'][$key] : '',
                        'iir_freq_time' => !empty($data['iir_freq_time'][$key]) ? $data['iir_freq_time'][$key] : '',
                        'iir_freq_text' => !empty($data['iir_freq_text'][$key]) ? $data['iir_freq_text'][$key] : '',
    
                        'opr_measur_tech' => !empty($data['opr_measur_tech'][$key]) ? $data['opr_measur_tech'][$key] : '',
                        'opr_size' => !empty($data['opr_size'][$key]) ? $data['opr_size'][$key] : '',
                        'opr_freq' => !empty($data['opr_freq'][$key]) ? $data['opr_freq'][$key] : '',
                        'opr_freq_time' => !empty($data['opr_freq_time'][$key]) ? $data['opr_freq_time'][$key] : '',
                        'opr_freq_text' => !empty($data['opr_freq_text'][$key]) ? $data['opr_freq_text'][$key] : '',
    
                        'ipr_measur_tech' => !empty($data['ipr_measur_tech'][$key]) ? $data['ipr_measur_tech'][$key] : '',
                        'ipr_size' => !empty($data['ipr_size'][$key]) ? $data['ipr_size'][$key] : '',
                        'ipr_freq' => !empty($data['ipr_freq'][$key]) ? $data['ipr_freq'][$key] : '',
                        'ipr_freq_time' => !empty($data['ipr_freq_time'][$key]) ? $data['ipr_freq_time'][$key] : '',
                        'ipr_freq_text' => !empty($data['ipr_freq_text'][$key]) ? $data['ipr_freq_text'][$key] : '',
    
                        'sar_measur_tech' => !empty($data['sar_measur_tech'][$key]) ? $data['sar_measur_tech'][$key] : '',
                        'sar_size' => !empty($data['sar_size'][$key]) ? $data['sar_size'][$key] : '',
                        'sar_freq' => !empty($data['sar_freq'][$key]) ? $data['sar_freq'][$key] : '',
                        'sar_freq_time' => !empty($data['sar_freq_time'][$key]) ? $data['sar_freq_time'][$key] : '',
                        'sar_freq_text' => !empty($data['sar_freq_text'][$key]) ? $data['sar_freq_text'][$key] : '',
    
                        'spc_measur_tech' => !empty($data['spc_measur_tech'][$key]) ? $data['spc_measur_tech'][$key] : '',
                        'spc_size' => !empty($data['spc_size'][$key]) ? $data['spc_size'][$key] : '',
                        'spc_freq' => !empty($data['spc_freq'][$key]) ? $data['spc_freq'][$key] : '',
                        'spc_freq_time' => !empty($data['spc_freq_time'][$key]) ? $data['spc_freq_time'][$key] : '',
                        'spc_freq_text' => !empty($data['spc_freq_text'][$key]) ? $data['spc_freq_text'][$key] : '',
    
                        'fir_measur_tech' => !empty($data['fir_measur_tech'][$key]) ? $data['fir_measur_tech'][$key] : '',
                        'fir_size' => !empty($data['fir_size'][$key]) ? $data['fir_size'][$key] : '',
                        'fir_freq' => !empty($data['fir_freq'][$key]) ? $data['fir_freq'][$key] : '',
                        'fir_freq_time' => !empty($data['fir_freq_time'][$key]) ? $data['fir_freq_time'][$key] : '',
                        'fir_freq_text' => !empty($data['fir_freq_text'][$key]) ? $data['fir_freq_text'][$key] : '',
                        'created_by' => $this->session->userdata('loginId')
                    ];
                    $transResult = $this->store($this->pfcTrans, $childData, 'Control Plan');
                }
            }
           
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return ['status'=>1,'message'=>'Revision saved successfully'];
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function checkDuplicateRev($postData){
		$data['tableName'] = $this->pfc_rev_master; 
		if(!empty($postData['pfc_id'])){  $data['where']['pfc_id'] = $postData['pfc_id']; }
		if(!empty($postData['item_id'])){  $data['where']['item_id'] = $postData['item_id']; }
		if(!empty($postData['rev_type'])){  $data['where']['rev_type'] = $postData['rev_type']; }
		if(!empty($postData['rev_no'])){  $data['where']['rev_no'] = $postData['rev_no']; }
			
		return $this->numRows($data);
    }

    public function revisionList($postData){
		$data['tableName'] = $this->pfc_rev_master; 
		if(!empty($postData['pfc_id'])){  $data['where']['pfc_id'] = $postData['pfc_id']; }
		if(!empty($postData['item_id'])){  $data['where']['item_id'] = $postData['item_id']; }
		if(!empty($postData['rev_type'])){  $data['where']['rev_type'] = $postData['rev_type']; }
		if(!empty($postData['rev_no'])){  $data['where']['rev_no'] = $postData['rev_no']; }
			
		return $this->rows($data);
    }
    /*** PFC Revision */

    public function saveRevision($data){
        try {
            $this->db->trans_begin();

            $pfcRevData=[
                'id'=>'',
                'item_id'=>$data['item_id'],
                'rev_date'=>$data['rev_date'],
                'rev_type'=>$data['rev_type'],
                'rev_no'=>$data['rev_no'],
                'cust_rev_no'=>$data['cust_rev_no'],
                'remark'=>$data['remark'],
                'created_by'=>$this->loginId
            ];
            $this->store($this->pfc_rev_master,$pfcRevData);
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return ['status'=>1,'message'=>'Revision saved successfully'];
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    
    public function savePFCRev($data){
        try {
            $this->db->trans_begin();

            foreach($data['trans_id'] as $key=>$id){
                $cpData = $this->getCPTrans($id);
                $this->edit($this->pfcTrans,['id'=>$id],['rev_no'=>$data['rev_no'][$key]]);
                // print_r($this->db->last_query());
            }
            $pfcData = $this->controlPlanV2->getPfcData($data['id']);
            if(isset($data['new_trans_id']) && !empty($data['new_trans_id'])){
                foreach($data['new_trans_id'] as $key=>$id){
                    $childData = [
                        'id' => '',
                        'trans_main_id' => $data['id'],
                        'entry_type' => 1,
                        'rev_no' => $data['rev_no'][$key],
                        'item_id' => $data['item_id'],
                        'process_no' => $data['process_no'][$key],
                        'process_code' => $data['process_code'][$key],
                        'product_param' => $data['parameter'][$key],
                        'symbol_1' => $data['symbol_1'][$key],
                        'char_class' => $data['char_class'][$key],
                        'output_operation' => $data['output_operation'][$key],
                        'stage_type' => $data['stage_type'][$key],
                        'created_by' => $this->loginId
                    ];
                    $result = $this->store($this->pfcTrans, $childData, 'Control Plan');
                 
                }
            }
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return ['status'=>1,'message'=>'Revision saved successfully'];
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }
    /* Created By :- Sweta @16-08-2023 */
    public function activeRevision($data)
    {
        try {
            $this->db->trans_begin();

            $result = $this->store($this->pfc_rev_master, ['id' => $data['id'], 'is_active' => $data['is_active']]);

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getPfcOperations($postData)
    {
        $queryData['tableName'] = $this->pfcTrans;
        $queryData['where_in']['process_no'] = $postData['process_no'];
        $queryData['where']['item_id'] = $postData['item_id'];
        $queryData['where']['entry_type'] = 1;
        return $this->rows($queryData);
    }

    public function approveCP($data) {
        try {
            $this->db->trans_begin();

            $this->store($this->pfcMaster, ['id'=> $data['id'], 'approved_by' => $this->loginId, 'approved_at' => date("Y-m-d H:i:s")]);

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return ['status' => 1, 'message' => 'Purchase Order ' . $data['msg'] . ' successfully.'];
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getProcessCode()
    {
        $queryData['tableName'] = 'cp_process_code';
        return $this->rows($queryData);
    }
}
