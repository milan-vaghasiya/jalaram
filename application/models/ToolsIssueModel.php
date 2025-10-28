<?php
class ToolsIssueModel extends MasterModel{
    private $tools_issue = "tools_issue";
    private $stockTransaction = "stock_transaction";
    private $materialReturn = "material_return";

    public function getDTRows($data)
    {
        $data['tableName'] = 'tools_issue';
        $data['select'] = "stock_transaction.batch_no,SUM(abs(stock_transaction.qty)) as issue_qty,tools_issue.issue_number,tools_issue.id,tools_issue.is_returnable,tools_issue.qty,tools_issue.issue_no,tools_issue.issue_date,item_master.item_name,item_master.item_code,item_master.description,item_master.make_brand,unit_master.unit_name,item_master.qty as stock_qty,item_master.full_name,issueBy.emp_name as issue_by,IFNULL(material_return.return_qty,0) as return_qty,stock_transaction.size";

        $data['leftJoin']['stock_transaction'] = "tools_issue.id = stock_transaction.ref_id AND stock_transaction.ref_type = 37 AND stock_transaction.trans_type = 2";
        $data['leftJoin']['item_master'] = "item_master.id = tools_issue.item_id";
        $data['leftJoin']['item_category'] = "item_category.id = item_master.category_id";
        $data['leftJoin']['unit_master'] = "item_master.unit_id = unit_master.id";
        $data['leftJoin']['employee_master as issueBy'] = "issueBy.id = tools_issue.created_by";
        $data['leftJoin']['( SELECT SUM(qty) as return_qty,ref_id,batch_no FROM material_return WHERE  is_delete = 0 GROUP BY ref_id,batch_no) as material_return'] = "material_return.batch_no = stock_transaction.batch_no AND material_return.ref_id = tools_issue.id";

        $data['where']['issue_date >= '] = $this->startYearDate;
		$data['where']['issue_date <= '] = $this->endYearDate;

        $data['group_by'][] = 'tools_issue.id,stock_transaction.batch_no';
        $data['order_by']['tools_issue.issue_date'] = 'DESC';
        $data['order_by']['tools_issue.id'] = 'DESC';

        if($data['status'] == 0){ $data['where']['is_returnable'] = 0; }
        if($data['status'] == 1){ $data['where']['is_returnable'] = 1; }
        
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "DATE_FORMAT(tools_issue.issue_date,'%d-%m-%Y')";
        $data['searchCol'][] = "tools_issue.issue_number";
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "stock_transaction.batch_no";
        $data['searchCol'][] = "stock_transaction.qty";
        $data['searchCol'][] = "material_return.return_qty";
        $data['searchCol'][] = "";

		$columns =array('','','tools_issue.issue_date','tools_issue.issue_number','item_master.item_name','stock_transaction.batch_no','stock_transaction.qty','material_return.return_qty','');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function getIssueMaterialData($id)
    {
        $data['tableName'] = $this->tools_issue;
        $data['select'] = "SUM(qty) as qty";
        $data['where']['ref_id'] = $id;
        return $this->row($data);
    }

    public function nextIssueNo()
    {
        $data['tableName'] = $this->tools_issue;
        $data['select'] = "MAX(issue_no) as issue_no";
        $data['where']['DATE_FORMAT(tools_issue.issue_date,"%Y-%m-%d") >='] = $this->startYearDate;
        $data['where']['DATE_FORMAT(tools_issue.issue_date,"%Y-%m-%d") <='] = $this->endYearDate;
        $maxNo = $this->specificRow($data)->issue_no;
        $nextIndentNo = (!empty($maxNo)) ? ($maxNo + 1) : 1;
        return $nextIndentNo;
    }

    public function save($data)
    { 
        try{
            $this->db->trans_begin();

            $empData = $this->employee->getEmp($data['collected_by']); 
            
            $batch_qty = array();
            $batch_no = array();
            $location_id = array();
            $batch_qty = explode(",", $data['batch_qty']);
            $batch_no = explode(",", $data['batch_no']);
            $location_id = explode(",", $data['location_id']);
            $size = explode(",", $data['size']);

            if (!empty($data['id'])) 
            {
                $this->remove('stock_transaction', ['ref_id' => $data['id'], 'ref_type' => 13]);
                $issueTransData = $this->getIssueMaterialData($data['id']);
                if (!empty($issueTransData->qty) and $issueTransData->qty > 0) :
                    $setData = array();
                    $setData['tableName'] = $this->itemMaster;
                    $setData['where']['id'] = $issueTransData->item_id;
                    $setData['set']['qty'] = 'qty, + ' . $issueTransData->qty;
                    $qryresult = $this->setValue($setData);
                endif;
            }
            if (empty($data['id'])) {
                $data['issue_no'] = $this->nextIssueNo();
                $data['issue_number'] = getPrefixNumber($data['issue_prefix'],$data['issue_no']);
            }
            $data['qty'] = array_sum($batch_qty);            
            $saveIssueData = $this->store($this->tools_issue, $data);

            $issueId = (!empty($data['id']) ? $data['id'] : $saveIssueData['insert_id']);
            foreach ($batch_qty as $bk => $bv) :
                if (!empty($bv) && $bv > 0) {
                    $stockQueryData['id'] = "";
                    $stockQueryData['location_id'] = $location_id[$bk];
                    if (!empty($batch_no[$bk])) { $stockQueryData['batch_no'] = $batch_no[$bk]; }
                    
                    $size[$bk] = (!empty($size[$bk]))?$size[$bk]:null;
                    
                    $stockQueryData['trans_type'] = 2;
                    $stockQueryData['item_id'] = $data['item_id'];
                    $stockQueryData['qty'] = '-' . $bv;
                    $stockQueryData['ref_type'] = 37;
                    $stockQueryData['ref_id'] = $issueId;
                    $stockQueryData['ref_no'] = $data['issue_no'];
                    $stockQueryData['ref_date'] =  $data['issue_date'];
                    $stockQueryData['created_by'] = $data['created_by'];
                    $stockQueryData['size'] = $size[$bk];
                    $stockResult = $this->store('stock_transaction', $stockQueryData);                  

                    if($data['is_returnable']== 1)
                    {
                        $strQuery['tableName'] = "location_master";
                        $strQuery['where']['ref_id'] = $empData->emp_dept_id;
                        // $strQuery['where']['id'] = $data['dept_id'];
                        $strQuery['where']['store_type'] = 15;
                        $strResult = $this->row($strQuery);
                        $location = $strResult->id;
                        $bookItemQuery = [
                            'id' => '',
                            'location_id' => $location,
                            'trans_type' => 1,
                            'item_id' => $data['item_id'],
                            'qty' => $bv,
                            'ref_type' => 37,
                            'ref_id' => $issueId,
                            'ref_no' => $data['issue_no'],
                            'batch_no' => $batch_no[$bk],
                            'ref_date' => $data['issue_date'],
                            'size' => $size[$bk],
                            'trans_ref_id' => $stockResult['insert_id'],
                            'created_by' => $data['created_by'],
                            'stock_effect' => 0
                        ];
                        $this->store('stock_transaction', $bookItemQuery);
                    }
                }
            endforeach;            
            
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return ['status' => 1, 'message' => 'Material Issue suucessfully.','issue_id'=> $issueId];
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function delete($id)
    {
        try{
            $this->db->trans_begin();
            $issueTransData = $this->getJobMaterial($id);
            foreach($issueTransData as $row):
                $setData = Array();
                $setData['tableName'] = $this->itemMaster;
                $setData['where']['id'] = $row->dispatch_item_id;
                $setData['set']['qty'] = 'qty, + '.$row->dispatch_qty;
                $this->setValue($setData);

                $this->store($this->toolsIssueTrans,['id'=>$row->id,'is_delete'=>1]);
            endforeach;
            $this->remove('stock_transaction',['ref_no'=>$id,'ref_type'=>37]);
        
            $result = $this->trash($this->toolsIssueMaster,['id'=>$id],'Tools Issue');
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function batchWiseItemStock($data)
    { 
        $i = 1;
        $tbody = "";
        $queryData = array();
        $queryData['tableName'] = "stock_transaction";
        $queryData['select'] = "SUM(stock_transaction.qty) as qty,stock_transaction.size,stock_transaction.batch_no,location_id,location_master.store_name,location_master.location,item_master.item_type,stock_transaction.item_id,item_master.size as master_size";
        $queryData['leftJoin']['item_master'] = "item_master.id = stock_transaction.item_id";
        $queryData['join']['location_master'] = "location_master.id = stock_transaction.location_id";
        $queryData['where']['stock_transaction.item_id'] = $data['item_id'];
        $queryData['where']['stock_transaction.stock_effect'] = 1;
        $queryData['where']['location_master.store_type != '] = $this->SCRAP_STORE->store_type; // Not Scrap
        $queryData['customWhere'][] = " location_master.store_type NOT IN(".$this->SCRAP_STORE->store_type.",".$this->MIS_PLC_STORE->store_type.")"; // Not Scrap
        $queryData['order_by']['stock_transaction.id'] = "asc";
        $queryData['group_by'][] = "stock_transaction.location_id,stock_transaction.batch_no,stock_transaction.size";
        $result = $this->rows($queryData);
        
        $resultSet = array();
        if (!empty($result)) {
            $batch_no = array();
            foreach ($result as $row) 
            {                
                if($row->item_type == 2){$row->size = !empty($row->size)?$row->size:$row->master_size;}
                $resultSet[] =$row;
                $batch_no = (!empty($data['id'])) ? explode(",", $data['batch_no']) : $data['batch_no'];
                $batch_qty = (!empty($data['id'])) ? explode(",", $data['batch_qty']) : $data['batch_qty'];
                if ($row->qty > 0 || !empty($batch_no) && in_array($row->batch_no, $batch_no)) :
                    if (!empty($batch_no) && in_array($row->batch_no, $batch_no)) :
                        $arrayKey = array_search($row->batch_no, $batch_no);
                        $qty = $batch_qty[$arrayKey];
                        $cl_stock = (!empty($data['id'])) ? floatVal($row->qty + $batch_qty[$arrayKey]) : floatVal($row->qty);
                    else :
                        $qty = "";
                        $cl_stock = floatVal($row->qty);
                    endif;
                    $tbody .= '<tr>';
                    $tbody .= '<td class="text-center">' . $i . '</td>';
                    $tbody .= '<td>[' . $row->store_name . '] ' . $row->location . '</td>';
                    $tbody .= '<td>' . $row->batch_no. (!empty($row->size)?' ( '.$row->size.' )':'') .  '</td>';
                    $tbody .= '<td>' . floatVal($row->qty) . '</td>';
                    $tbody .= '<td>
                            <input type="text" name="batch_quantity[]" class="form-control batchQty floatOnly" data-rowid="' . $i . '" data-cl_stock="' . $cl_stock . '" min="0" value="' . $qty . '" />
                            <input type="hidden" name="batch_number[]" id="batch_number' . $i . '" value="' . $row->batch_no . '" />
                            <input type="hidden" name="location[]" id="location' . $i . '" value="' . $row->location_id . '" />
                            <input type="hidden" name="size[]" id="size' . $i . '" value="' . $row->size . '" />
                            <div class="error batch_qty' . $i . '"></div>
                        </td>';
                    $tbody .= '</tr>';
                    $i++;
                endif;
            }
        } else {
            $tbody = '<tr><td class="text-center" colspan="5">No Data Found.</td></tr>';
        }
        return ['status' => 1, 'batchData' => $tbody,'resultSet'=>$resultSet];
    }

    public function getIssueMaterialLog($id)
    {
        $data['tableName'] = $this->tools_issue;
        $data['where']['id'] = $id;
        return $this->row($data);
    }

    public function saveReturnMaterial($data)
    {
        try { 
            $this->db->trans_begin();
            $issueLog = $this->getIssueMaterialLog($data['ref_id']);
            $qty = 0;
            foreach ($data['used_qty'] as $key => $value) {
                $qty += ($value + $data['missed_qty'][$key] + $data['broken_qty'][$key] + $data['scrap_qty'][$key]+$data['regranding_qty'][$key]);
                $returnData = [
                    'id' => '',
                    'item_id' => $issueLog->item_id,
                    'ref_id' => $data['ref_id'],
                    'batch_no' => $data['batch_no'],
                    'qty' => $qty,
                    'used_qty' => $value,
                    'missed_qty' => $data['missed_qty'][$key],
                    'broken_qty' => $data['broken_qty'][$key],
                    'scrap_qty' => $data['scrap_qty'][$key],
                    'regranding_qty' => $data['regranding_qty'][$key],
                    'trans_date' => $data['trans_date'],
                    'trans_type' => $data['trans_type'],
                    'size' => (!empty($data['size']))?$data['size']:null,
                    'reason' => $data['reason'][$key],
                    'created_by' => $data['created_by'],
                ];
                $this->store($this->materialReturn, $returnData);  
            }

            $empData = $this->employee->getEmp($issueLog->collected_by);

            $strQuery['tableName'] = "location_master";
            $strQuery['where']['ref_id'] = $empData->emp_dept_id;
            $strQuery['where']['store_type'] = 15;
            $strResult = $this->row($strQuery);
            $stockMinusQuery = [
                'id' => '',
                'item_id' => $issueLog->item_id,
                'ref_id' => $data['ref_id'],
                'ref_type' => 38,
                'location_id' => $strResult->id,
                'batch_no' => $data['batch_no'],
                'trans_type' => 2,
                'qty' => '-' . $qty,
                'ref_id' => $data['ref_id'],
                'size' => (!empty($data['size']))?$data['size']:null,
                'ref_no' => $issueLog->issue_no,
                'stock_effect'=>0,
                'created_by'=>$this->session->userdata('loginId')
            ];

            $result = $this->store($this->stockTransaction, $stockMinusQuery);
            $stockPlusQuery = [
                'id' => '',
                'item_id' => $issueLog->item_id,
                'ref_id' => $data['ref_id'],
                'ref_type' => 38,
                'location_id' => $this->INSP_STORE->id,
                'batch_no' => $data['batch_no'],
                'trans_type' => 1,
                'qty' => $qty,
                'ref_id' => $data['ref_id'],
                'trans_ref_id' => $result['insert_id'],
                'size' => (!empty($data['size']))?$data['size']:null,
                'stock_effect'=>0,
                'created_by'=>$this->session->userdata('loginId')                
            ];

            $result = $this->store($this->stockTransaction, $stockPlusQuery);
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }
}
?>