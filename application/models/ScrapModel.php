<?php
class ScrapModel extends MasterModel
{
    private $stockTrans = "stock_transaction";
    private $scrapBook = "scrap_book";
    private $scrapBookTrans = "scrap_book_trans";
    private $rejRwManagement = "rej_rw_management";

    public function getDTRows($data)
    {
        $data['tableName'] = $this->scrapBook;
        $data['select'] = 'scrap_book.*,item_master.item_name,item_master.item_code,job_card.job_no,job_card.job_prefix';
        $data['leftJoin']['item_master'] = "item_master.id=scrap_book.item_id";
        $data['leftJoin']['job_card'] = "job_card.id=scrap_book.job_card_id";


        $data['searchCol'][] = "DATE_FORMAT(scrap_book.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "CONCAT('/',job_card.job_no)";
        $data['searchCol'][] = "item_master.item_code";
        $data['searchCol'][] = "scrap_book.scrap_qty";
        $data['searchCol'][] = "scrap_book.ok_qty";

        $columns = array('', '', 'scrap_book.trans_date', 'job_card.job_no', 'item_master.item_code', 'scrap_book.scrap_qty', 'scrap_book.ok_qty');
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }
        return $this->pagingRows($data);
    }

    public function saveProductionRejScrape($data)
    {
        try {
            $this->db->trans_begin();
            
            $kitData = $this->getScrapGroupData($data);
            if(empty($kitData->scrap_group)):
                return ['status' => 2, 'message' => "Scrap Group Not Found"];
            endif;

            $wtpcs = (!empty($kitData->wt_pcs) && $kitData->wt_pcs > 0) ? ($kitData->qty * $kitData->wt_pcs) : $kitData->qty; 
            
            $masterData = [
                'id' => "",
                'trans_date' => $data['ref_date'],
                'job_card_id' => $data['job_card_id'],
                'item_id' => $data['item_id'],
                'scrap_qty' => array_sum($data['scrap_qty']),
                'supplier_rej' => array_sum($data['supplier_rej']),
                'ok_qty' => array_sum($data['ok_qty']),
                'created_by' => $this->loginId
            ];
            
            $result = $this->store("scrap_book", $masterData);
            $jobData = $this->jobcard_v3->getJobcard($data['job_card_id']);
            
            $scrapQty=0;$supplierRejQty=0;
            foreach ($data['log_sheet_id'] as $key => $value) {
                if (!empty($data['scrap_qty'][$key]) || !empty($data['ok_qty'][$key]) || !empty($data['supplier_rej'][$key])) {
                    $logData=$this->logSheet->getLogs($value);
                    
                    $approveData=$this->jobcard_v3->getJobApprovalDetail($data['job_card_id'],$logData->process_id);
                    
                    $transData = [
                        'id' => '',
                        'scrap_id' => $result['insert_id'],
                        'log_id' => $value,
                        'rej_log_id' => $data['rej_log_id'][$key],
                        'scrap_qty' => $data['scrap_qty'][$key],
                        'ok_qty' => $data['ok_qty'][$key],
                        'supplier_rej' => $data['supplier_rej'][$key],
                        'rej_reason' => $data['rej_reason'][$key],
                        'rej_stage' => $data['rej_stage'][$key],
                        'rej_from' => $data['rej_from'][$key],
                        'wp_qty' => $kitData->qty
                    ];
                    $this->store("scrap_book_trans", $transData);
                    if (!empty($data['ok_qty'][$key])) {
                        $setData = array();
                        $setData['tableName'] = 'production_log';
                        $setData['where']['id'] = $value;
                        $setData['set']['rej_qty'] = 'rej_qty, - ' . $data['ok_qty'][$key];
                        $setData['set']['ok_qty'] = 'ok_qty, + ' . $data['ok_qty'][$key];
                        $this->setValue($setData);

                        $setData = array();
                        $setData['tableName'] = $this->rejRwManagement;
                        $setData['where']['id'] = $data['rej_log_id'][$key];
                        $setData['set']['qty'] = 'qty, - ' . $data['ok_qty'][$key];
                        $this->setValue($setData);

                        $this->productionLog->jobApprovalQtyEffect($approveData->id);
                    }

                    if (!empty($data['scrap_qty'][$key])) {
                        $setData = array();
                        $setData['tableName'] = $this->rejRwManagement;
                        $setData['where']['id'] = $data['rej_log_id'][$key];
                        $setData['set']['scrap_qty'] = 'scrap_qty, + ' . ($data['scrap_qty'][$key]);
                        $this->setValue($setData);
                        $scrapQty += $data['scrap_qty'][$key] * $wtpcs;
                    }
                    
                    if (!empty($data['supplier_rej'][$key])) {
                        $setData = array();
                        $setData['tableName'] = $this->rejRwManagement;
                        $setData['where']['id'] = $data['rej_log_id'][$key];
                        $setData['set']['scrap_qty'] = 'scrap_qty, + ' . ($data['supplier_rej'][$key]);
                        $this->setValue($setData);
                        $supplierRejQty += $data['supplier_rej'][$key] * $kitData->qty;
                    }
                }
            }
            
            if(!empty($scrapQty)){
                $stockPlusTrans = [
                    'id' => "",
                    'location_id' => $this->SCRAP_STORE->id,
                    'batch_no' => getPrefixNumber($jobData->job_prefix, $jobData->job_no),
                    'trans_type' => 1,
                    'item_id' => $kitData->scrap_group,
                    'qty'=>$scrapQty,
                    'ref_type' => 25,
                    'ref_no' => $kitData->ref_item_id,
                    'ref_batch' => $kitData->material_grade,
                    'ref_id' => $data['job_card_id'],
                    'trans_ref_id' => $result['insert_id'],
                    'ref_date' => $data['ref_date'],
                    'created_by' => $this->loginId
                ];
                $this->store($this->stockTrans, $stockPlusTrans);
            }
            
            if(!empty($supplierRejQty)){
                
                $jobUsedMtr = $this->jobcard_v3->getJobcardRowMaterial($data['job_card_id']);
                
                $supplier=[];
                if(!empty($jobUsedMtr->batch_no)){
                    $supplier = $this->getSupplierForUseMaterial(['batch_no' => $jobUsedMtr->batch_no]);
                }
                
                $stockPlusTrans = [
                    'id' => "",
                    'location_id' => $this->SUP_REJ_STORE->id,
                    'batch_no' => getPrefixNumber($jobData->job_prefix, $jobData->job_no),
                    'trans_type' => 1,
                    'item_id' => $kitData->ref_item_id,
                    'qty'=>$supplierRejQty,
                    'ref_type' => 25,
                    //'ref_no' => $kitData->ref_item_id,
                    'ref_batch' => $kitData->material_grade,
                    'ref_id' => $data['job_card_id'],
                    'trans_ref_id' => $result['insert_id'],
                    'ref_date' => $data['ref_date'],
                    'remark' => (!empty($supplier->party_id)) ? $supplier->party_id : 0,
                    'created_by' => $this->loginId
                ];
                $this->store($this->stockTrans, $stockPlusTrans);
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

    public function getRejectionStock($job_card_id)
    {
        $queryData['tableName'] = "rej_rw_management";
        $queryData['select'] = "rej_rw_management.*,process_master.process_name,production_log.job_card_id,job_card.job_no,job_card.job_prefix";
        $queryData['leftJoin']['production_log'] = "production_log.id = rej_rw_management.log_id";
        $queryData['leftJoin']['job_card'] = "production_log.job_card_id = job_card.id";
        $queryData['leftJoin']['process_master'] = "process_master.id = production_log.process_id";
        $queryData['where']['production_log.job_card_id'] = $job_card_id;
        $queryData['where']['manag_type']=1;
        $queryData['where']['production_log.is_delete']=0;
        $queryData['where']['production_log.prod_type !=']=5;
        $queryData['customWhere'][]='(rej_rw_management.scrap_qty) < rej_rw_management.qty';
        $result = $this->rows($queryData);
        return $result;
    }

    public function delete($id)
    {
        try {
            $this->db->trans_begin();

            $result = $this->getScrapBookData($id);

            $jobData = $this->jobcard_v3->getJobcard($result->job_card_id);
            $logData = $this->getScrapBookTransData($id);
            foreach ($logData as $row) {
                if (!empty($row->scrap_qty) || !empty($row->ok_qty) || !empty($row->supplier_rej)) {

                    $logSheetData = $this->logSheet->getLogs($row->log_id);
                    if (!empty($row->ok_qty)) {
                        $setData = array();
                        $setData['tableName'] = 'production_log';
                        $setData['where']['id'] = $row->log_id;
                        $setData['set']['rej_qty'] = 'rej_qty, + ' . $row->ok_qty;
                        $setData['set']['ok_qty'] = 'ok_qty, - ' . $row->ok_qty;
                        $this->setValue($setData);

                        $setData = array();
                        $setData['tableName'] = $this->rejRwManagement;
                        $setData['where']['id'] = $row->rej_log_id;
                        $setData['set']['qty'] = 'qty, + ' . $row->ok_qty;
                        $this->setValue($setData);

                        $this->productionLog->jobApprovalQtyEffect($logSheetData->job_approval_id);

                    }
                    if (!empty($row->scrap_qty)) {
                        $setData = array();
                        $setData['tableName'] = $this->rejRwManagement;
                        $setData['where']['id'] = $row->rej_log_id;
                        $setData['set']['scrap_qty'] = 'scrap_qty, - ' . $row->scrap_qty;
                        $this->setValue($setData);
                    }
                    if (!empty($row->supplier_rej)) {
                        $setData = array();
                        $setData['tableName'] = $this->rejRwManagement;
                        $setData['where']['id'] = $row->rej_log_id;
                        $setData['set']['scrap_qty'] = 'scrap_qty, - ' . $row->supplier_rej;
                        $this->setValue($setData);
                    }
                    $this->trash($this->scrapBookTrans, ['id' => $row->id]);
                }
            }
            $this->trash($this->scrapBook, ['id' => $id]);
            $this->remove($this->stockTrans, ['ref_type' => 25, 'ref_id' => $result->job_card_id, 'trans_ref_id' => $id]);

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getJobcardList()
    {
        $queryData['tableName'] = "rej_rw_management";
        $queryData['select'] = "job_card.id as job_card_id,job_card.job_no,job_card.job_prefix,job_card.product_id,item_master.item_name,item_master.item_code,SUM(rej_rw_management.qty-rej_rw_management.scrap_qty) as qty";
        $queryData['leftJoin']['production_log'] = "production_log.id = rej_rw_management.log_id AND production_log.prod_type != 5";
        $queryData['leftJoin']['job_card'] = "production_log.job_card_id = job_card.id";
        $queryData['leftJoin']['item_master'] = "item_master.id = job_card.product_id";
        $queryData['where']['job_date >= '] = $this->startYearDate;
        $queryData['where']['job_date <= '] = $this->endYearDate;
        $queryData['customWhere'][]='(rej_rw_management.scrap_qty) < rej_rw_management.qty';
        $queryData['group_by'][] = "job_card.id";
        $result = $this->rows($queryData);
        return $result;
    }

    public function getScrapBookData($id)
    {
        $data['tableName'] = $this->scrapBook;
        $data['select'] = 'scrap_book.*,item_master.item_name,item_master.item_code,job_card.job_no,job_card.job_prefix';
        $data['leftJoin']['item_master'] = "item_master.id=scrap_book.item_id";
        $data['leftJoin']['job_card'] = "job_card.id=scrap_book.job_card_id";
        $data['where']['scrap_book.id'] = $id;
        $result = $this->row($data);
        return $result;
    }

    public function getScrapBookTransData($id)
    {
        $data['tableName'] = $this->scrapBookTrans;
        $data['select'] = 'scrap_book_trans.*,process_master.process_name';
        $data['leftJoin']['production_log'] = "production_log.id=scrap_book_trans.log_id";
        $data['leftJoin']['process_master'] = "process_master.id=production_log.process_id";
        $data['where']['scrap_id'] = $id;
        $result = $this->rows($data);
        return $result;
    }

    public function getScrapBookTransRejData($id)
    {
        $data['tableName'] = $this->scrapBookTrans;
        $data['select'] = 'SUM(scrap_book_trans.scrap_qty) as qty';
        $data['where']['rej_log_id'] = $id;
        $result = $this->row($data);
        return $result;
    }
    
    public function getScrapGroupData($data=array()){
        $queryData['tableName'] = 'job_bom';
        $queryData['select'] = "job_bom.*,item_master.item_name,item_master.item_code,item_master.item_type,item_master.wt_pcs,material_master.scrap_group,material_master.material_grade";
        $queryData['leftJoin']['item_master'] = "job_bom.ref_item_id = item_master.id";
        $queryData['leftJoin']['material_master'] = "material_master.material_grade = item_master.material_grade";
        if(!empty($data['item_id'])){ $queryData['where']['job_bom.item_id'] = $data['item_id']; }
        if(!empty($data['job_card_id'])){ $queryData['where']['job_bom.job_card_id'] = $data['job_card_id']; }
        return $this->row($queryData);
    }

    public function getSupplierForUseMaterial($data){
        $queryData['tableName'] = 'grn_transaction';
        $queryData['select'] = "grn_master.party_id,party_master.party_name";
        $queryData['leftJoin']['grn_master'] = "grn_master.id = grn_transaction.grn_id";
        $queryData['leftJoin']['party_master'] = "party_master.id = grn_master.party_id";
        $queryData['where']['grn_transaction.batch_no'] = $data['batch_no'];
        $queryData['order_by']['grn_transaction.id'] = 'DESC';
        return $this->row($queryData);
    }
}
?>