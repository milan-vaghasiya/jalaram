<?php
class GeneralIssueModel extends MasterModel
{
    private $jobMaterialDispatch = "job_material_dispatch";
    private $itemMaster = "item_master";

    public function getDTRows($data)
    {
        $data['tableName'] = $this->jobMaterialDispatch;
        $data['select']='job_material_dispatch.*,issue_by.emp_name as issue_by,collect_by.emp_name as collect_by';
        $data['leftJoin']['department_master'] = 'department_master.id = job_material_dispatch.dept_id';
        $data['leftJoin']['employee_master as issue_by'] = 'issue_by.id = job_material_dispatch.dispatch_by';
        $data['leftJoin']['employee_master as collect_by'] = 'collect_by.id = job_material_dispatch.collected_by';

        $data['where']['job_material_dispatch.dispatch_date >= '] = $this->startYearDate;
        $data['where']['job_material_dispatch.dispatch_date <= '] = $this->endYearDate;
        $data['where']['job_material_dispatch.issue_type'] = 2;

        $data['searchCol'][] = "DATE_FORMAT(job_material_dispatch.dispatch_date,'%d-%m-%Y')";
        $data['searchCol'][] = "issue_by.emp_name";
        $data['searchCol'][] = "collect_by.emp_name";
        
        $columns = array('', '', 'DATE_FORMAT(job_material_dispatch.dispatch_date,"%d-%m-%Y")', 'issue_by.emp_name', 'collect_by.emp_name','');
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }
        return $this->pagingRows($data);
    }



    public function getIssueBatchTrans($id)
    {
        $data['tableName'] = $this->toolsIssueTrans;
        $data['select'] = "job_material_dispatch.id as trans_id,job_material_dispatch.job_card_id,job_material_dispatch.tools_dispatch_id,job_material_dispatch.material_type,job_material_dispatch.collected_by,job_material_dispatch.process_id,job_material_dispatch.dispatch_date,job_material_dispatch.dispatch_item_id,job_material_dispatch.dispatch_qty as qty, job_material_dispatch.dept_id, department_master.name as dept_name, stock_transaction.location_id,stock_transaction.batch_no,location_master.store_name,location_master.location,item_master.item_name,itm.item_code as part_code";
        $data['leftJoin']['item_master'] = 'item_master.id = job_material_dispatch.dispatch_item_id';
        $data['leftJoin']['job_card'] = 'job_card.id = job_material_dispatch.job_card_id';
        $data['leftJoin']['item_master itm'] = 'itm.id = job_card.product_id';
        $data['leftJoin']['stock_transaction'] = 'job_material_dispatch.id = stock_transaction.ref_id';
        $data['leftJoin']['location_master'] = 'location_master.id = stock_transaction.location_id';
        $data['leftJoin']['department_master'] = 'department_master.id = job_material_dispatch.dept_id';

        $data['where']['stock_transaction.ref_no'] = $id;
        $data['where']['stock_transaction.ref_type'] = 21;
        $data['where']['stock_transaction.issue_type'] = 2;
        return $this->rows($data);
    }

    public function save($data)
    { 
        try {
            $this->db->trans_begin();
            foreach ($data['item_id'] as $key => $value) :
                if (empty($data['id'][$key])) :
                    $issueData = [
                        'id' => $data['id'][$key],
                        'material_type' => $data['material_type'],
                        'collected_by' => $data['collected_by'],
                        'dept_id' => $data['dept_id'],
                        'dispatch_date' => $data['dispatch_date'],
                        'dispatch_qty' => $data['batch_qty'][$key],
                        'dispatch_by' => $data['dispatch_by'],
                        'created_by'=>$data['created_by'],
                        'remark' => $data['remark'],
                        'issue_type' => 2,
                    ];
                    $saveIssueData = $this->store($this->jobMaterialDispatch, $issueData);
                    $issueId = $saveIssueData['insert_id'];
                    //foreach ($data['item_id'] as $key => $value) :
                        $stockTrans = [
                            'id' => "",
                            'location_id' => $data['location_id'][$key],
                            'batch_no' => $data['batch_no'][$key],
                            'trans_type' => 2,
                            'item_id' => $value,
                            'qty' => "-" . $data['batch_qty'][$key],
                            'ref_type' => 21,
                            'ref_id' => $saveIssueData['insert_id'],
                            'ref_no' => $saveIssueData['insert_id'],
                            'ref_date' => $data['dispatch_date'],
                            'created_by' => $data['created_by']
                        ];

                        $this->store('stock_transaction', $stockTrans);

                        $setData = array();
                        $setData['tableName'] = $this->itemMaster;
                        $setData['where']['id'] = $value;
                        $setData['set']['qty'] = 'qty, - ' . $data['batch_qty'][$key];
                        $this->setValue($setData);
                

                    $result = ['status' => 1, 'message' => 'Material Issue suucessfully.'];
                else :

                    $issueTransData = $this->getJobMaterial($data['id'][$key]);
                    $this->remove('stock_transaction', ['ref_id' => $data['id'][$key], 'ref_type' => 21]);

                    if (!empty($issueTransData->transData)) :
                        foreach ($issueTransData->transData as $row) {
                            $setData = array();
                            $setData['tableName'] = $this->itemMaster;
                            $setData['where']['id'] = $row->item_id;
                            $setData['set']['qty'] = 'qty, + ' . abs($row->qty);
                            $qryresult = $this->setValue($setData);
                        }
                    endif;

                    $issueData = [
                        'id' => $data['id'][$key],
                        'material_type' => $data['material_type'],
                        'collected_by' => $data['collected_by'],
                        'dept_id' => $data['dept_id'],
                        'dispatch_date' => $data['dispatch_date'],
                        'dispatch_qty' => $data['batch_qty'][$key],
                        'dispatch_by' => $data['dispatch_by'],
                        'remark' => $data['remark'],
                        'issue_type' => 2,
                    ];
                    $saveIssueData = $this->store($this->jobMaterialDispatch, $issueData);
                    //foreach ($data['item_id'] as $key => $value) :
                        $stockTrans = [
                            'id' => "",
                            'location_id' => $data['location_id'][$key],
                            'batch_no' => $data['batch_no'][$key],
                            'trans_type' => 2,
                            'item_id' => $value,
                            'qty' => "-" . $data['batch_qty'][$key],
                            'ref_type' => 21,
                            'ref_id' => $data['id'][$key],
                            'ref_no' => $data['id'][$key],
                            'ref_date' => $data['dispatch_date'],
                            'created_by' => $data['created_by']
                        ];

                        $this->store('stock_transaction', $stockTrans);

                        $setData = array();
                        $setData['tableName'] = $this->itemMaster;
                        $setData['where']['id'] = $value;
                        $setData['set']['qty'] = 'qty, - ' . $data['batch_qty'][$key];
                        $this->setValue($setData);
                    //endforeach;
                    if ($this->db->trans_status() !== FALSE) :
                        $this->db->trans_commit();
                        $result =  ['status' => 1, 'message' => 'Material Issue suucessfully.'];
                    endif;
                endif;
            endforeach;

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function delete($id)
    {
        try {
            $this->db->trans_begin();
            $issueTransData = $this->getJobMaterial($id);
            foreach ($issueTransData->trans_data as $row) :
                $setData = array();
                $setData['tableName'] = $this->itemMaster;
                $setData['where']['id'] = $row->item_id;
                $setData['set']['qty'] = 'qty, + ' . abs($row->qty);
                $this->setValue($setData);

            endforeach;
            $this->remove('stock_transaction', ['ref_no' => $id, 'ref_type' => 21]);

            $result = $this->trash($this->jobMaterialDispatch, ['id' => $id], 'Material Issue');
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }


    public function getJobMaterial($id)
    {
        $data['tableName'] = $this->jobMaterialDispatch;
        $data['select']='job_material_dispatch.*,issue_by.emp_name as issue_by,collect_by.emp_name as collect_by,department_master.name as dept_name';
        $data['leftJoin']['department_master'] = 'department_master.id = job_material_dispatch.dept_id';
        $data['leftJoin']['employee_master as issue_by'] = 'issue_by.id = job_material_dispatch.created_by';
        $data['leftJoin']['employee_master as collect_by'] = 'collect_by.id = job_material_dispatch.collected_by';
        $data['where']['job_material_dispatch.id'] = $id;
        $result = $this->row($data);

        $queryData['tableName'] = 'stock_transaction';
        $queryData['select'] = 'stock_transaction.*,location_master.store_name,location_master.location,item_master.item_name';
        $queryData['leftJoin']['location_master'] = "stock_transaction.location_id = location_master.id";
        $queryData['leftJoin']['item_master'] = "stock_transaction.item_id = item_master.id";
        $queryData['where']['stock_transaction.ref_id'] = $id;
        $queryData['where']['stock_transaction.ref_type'] = 21;
        $result->trans_data = $this->rows($queryData);
        return $result;
    }

    //Created By Karmi @08/08/2022
    public function getRequestedItemForReqNo($req_no){
        $data['tableName'] = $this->jobMaterialDispatch;
        $data['select']='job_material_dispatch.req_item_id as id,job_material_dispatch.id as req_id,item_master.item_name';
        $data['leftJoin']['item_master'] = "job_material_dispatch.req_item_id = item_master.id";
        $data['where']['job_material_dispatch.req_no'] = $req_no;
        $data['customWhere'][] = '(job_material_dispatch.req_qty - job_material_dispatch.dispatch_qty) > 0';
        $result = $this->rows($data);
        return $result;

    }
}
