<?php
 

class PackingRequestModel extends MasterModel {
    private $stockTransaction = "stock_transaction";
    private $transChild = "trans_child";
    private $packingMaster = "packing_master";
    private $packingTrans = "packing_transaction";
    private $packingRequest = "packing_request";
    private $itemMaster = "item_master";

    public function getDTRows($data){
        $data['tableName'] = $this->stockTransaction;
        $data['select'] = "stock_transaction.*,SUM(stock_transaction.qty) as current_stock,item_master.item_name,item_master.item_code,location_master.location,location_master.store_name";
        $data['leftJoin']['item_master'] = "item_master.id = stock_transaction.item_id";
        $data['leftJoin']['location_master'] = "location_master.id = stock_transaction.location_id";
        $data['where']['location_id'] = $this->PROD_STORE->id;
        $data['where']['item_master.item_type'] = 1;
        $data['where']['stock_transaction.stock_effect'] = 1; //26-09-2024
        $data['group_by'][] = "stock_transaction.item_id,stock_transaction.batch_no";
        $data['having'][] = 'SUM(stock_transaction.qty) > 0';
    
        $data['searchCol'][] = "CONCAT(item_master.item_code,item_master.item_name)";
        $data['searchCol'][] = "location_master.location";
        $data['searchCol'][] = "stock_transaction.batch_no";
        $data['searchCol'][] = "stock_transaction.qty";
		$columns =array('','','item_master.item_name','location_master.location','stock_transaction.batch_no','stock_transaction.qty');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getRequestedRows($data){ 
        $data['tableName'] = $this->packingRequest;
        $data['select'] = "packing_request.*,party_master.party_code,party_master.party_name,item_master.item_code,item_master.item_name,(packing_request.request_qty - packing_request.pack_link_qty) as pending_qty,trans_main.trans_prefix as so_prefix,trans_main.trans_no as so_no";
        $data['leftJoin']['item_master'] = "item_master.id = packing_request.item_id";
        $data['leftJoin']['party_master'] = "party_master.id = item_master.party_id";
        $data['leftJoin']['trans_main'] = "packing_request.trans_main_id = trans_main.id";
        if(empty($data['status'])){ $data['customWhere'][] = '(packing_request.request_qty - packing_request.dispatch_qty) > 0'; }
        else{ $data['customWhere'][] = '(packing_request.request_qty - packing_request.dispatch_qty) <= 0'; }
        if(!empty($data['party_id'])){$data['where']['item_master.party_id'] = $data['party_id'];}
        if(!empty($data['item_id'])){$data['where']['packing_request.item_id'] = $data['item_id'];}
		$data['order_by']['packing_request.req_date'] = "DESC";
        
		$data['searchCol'][] = "packing_request.req_date";
        $data['searchCol'][] = "packing_request.trans_no";
        $data['searchCol'][] = "CONCAT('/',trans_main.trans_no)";
        $data['searchCol'][] = "CONCAT(item_master.item_code,item_master.item_name)";
        $data['searchCol'][] = "CONCAT(party_master.party_code,party_master.party_name)";
        $data['searchCol'][] = "packing_request.request_qty";
		
		$columns =array('','','packing_request.entry_date','packing_request.trans_no','trans_main.trans_no','party_master.party_name','packing_request.item_name','packing_request.request_qty','');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getPackingReqData($id){
        $data['tableName'] = $this->packingRequest;
        $data['select'] = "packing_request.*,item_master.item_name,item_master.item_code";
        $data['leftJoin']['item_master'] = "item_master.id = packing_request.item_id";
        $data['where']['packing_request.id'] = $id;
        return $this->row($data);
    }
    
    public function getPackingReqDataForPacking($id){
        $data['tableName'] = $this->packingRequest;
        $data['select'] = "packing_request.id as req_id,packing_request.trans_no as req_no, packing_request.item_id,packing_request.trans_child_id,packing_request.trans_main_id";
        $data['where']['packing_request.id'] = $id;
        return $this->row($data);
    }

    public function getRequestEditData($trans_no){
        $data['tableName'] = $this->packingRequest;
        $data['select'] = "packing_request.*,item_master.item_code,item_master.item_name,trans_main.trans_prefix,trans_main.trans_no";
        $data['leftJoin']['item_master'] = "item_master.id = packing_request.item_id";
        $data['leftJoin']['trans_main'] = "trans_main.id = packing_request.trans_main_id";
        $data['where']['packing_request.trans_no'] = $trans_no;
        return $this->rows($data);
    }

    public function getSalesOrderList($party_id){  
        $data['tableName'] = $this->transChild;    
        $data['select'] = 'trans_child.id,trans_child.trans_main_id,trans_child.item_id,trans_child.item_code,trans_child.item_name,trans_child.item_alias,trans_child.trans_status,trans_child.qty,trans_main.trans_prefix,trans_main.trans_no,trans_child.cod_date as delivery_date,trans_child.dispatch_qty,ifnull(packing_request.request_qty,0) as request_qty';        
        $data['leftJoin']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        $data['leftJoin']['(SELECT trans_child_id,ifnull(SUM(request_qty),0) as request_qty FROM packing_request WHERE is_delete = 0 GROUP BY trans_child_id) as packing_request'] = "packing_request.trans_child_id = trans_child.id";
        $data['where']['trans_child.entry_type'] = 4;
        //$data['where']['trans_child.trans_status'] = 0;
        $data['where']['trans_main.party_id'] = $party_id;
        $data['where']['(trans_child.qty - ifnull(packing_request.request_qty,0)) >'] = 0;
        //$data['group_by'][] = "trans_main.trans_no";
        //$data['where']['trans_main.trans_date >= '] = $this->startYearDate;
        //$data['where']['trans_main.trans_date <= '] = $this->endYearDate;
        $result= $this->rows($data);
        //$this->printQuery();
        return $result;
    }

    public function savePackingRequest($data){ 
        try{
            $this->db->trans_begin();   
           
            $reqData = $this->getRequestEditData($data['trans_no']);
            if(!empty($reqData)){
                foreach($reqData as $key=>$value):
                    if(!in_array($value->id,$data['trans_id'])):		
                        $this->trash($this->packingRequest,['id'=>$value->id]);
                    endif;
                endforeach;
            }
          
            foreach($data['item_id'] as $key=>$value):
                $transData = [
                    'id' => $data['trans_id'][$key],
                    'trans_no' => $data['trans_no'],
                    'trans_prefix' => $data['trans_prefix'],
                    'req_date' => $data['req_date'],
                    'party_id' => $data['party_id'],
                    'item_id' => $value,
                    'trans_main_id' => $data['trans_main_id'][$key],
                    'trans_child_id' => $data['trans_child_id'][$key],
                    'request_qty' => $data['request_qty'][$key],
                    'delivery_date' => $data['delivery_date'][$key],
	                'trans_way' => $data['trans_way'][$key],
	                'remark' => $data['remark'][$key],
                    'created_by'  => $this->session->userdata('loginId')
                ];
                $this->store($this->packingRequest,$transData);
            endforeach;
          
            $result = ['status'=>1,'message'=>'Packing Request send successfully.'];
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
            }catch(\Exception $e){
                $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function delete($id){ 
        try{
            $this->db->trans_begin();
          
            $result = $this->trash($this->packingRequest,['id'=>$id],'Request');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
        return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function getItemList($party_id=0){
		$data['tableName'] = $this->itemMaster;
		if(!empty($party_id))
			$data['where']['party_id'] = $party_id;
		return $this->rows($data);
	}

    public function nextTransNo(){
        $data['tableName'] = $this->packingRequest;
        $data['select'] = "MAX(trans_no) as trans_no";
		$data['where']['req_date >= '] = $this->startYearDate;
        $data['where']['req_date <= '] = $this->endYearDate;
		$trans_no = $this->specificRow($data)->trans_no;
		$nextTransNo = (!empty($trans_no))?($trans_no + 1):1;
		return $nextTransNo;
    }	

    // Created By Meghavi @26/12/22
    public function getItemData($id){ 
        /* $queryData['tableName'] = $this->packingTrans;
        $queryData['select'] = "packing_transaction.*,packing_master.trans_no,packing_master.trans_prefix";
        $queryData['leftJoin']['packing_master'] = "packing_master.id = packing_transaction.packing_id";
        $queryData['customWhere'][] = 'find_in_set("' . $id . '", req_ids)';
        $resultData = $this->rows($queryData);  */

        $queryData = array();
        $queryData['tableName'] = $this->packingRequest;
        $queryData['select'] = "packing_master.trans_number,item_master.item_code,item_master.item_name,packing_request.pack_link_qty,packing_transaction.total_qty";
        $queryData['leftJoin']['packing_transaction'] = "find_in_set(packing_request.id,packing_transaction.req_ids) > 0";
        $queryData['leftJoin']['packing_master'] = "packing_master.id = packing_transaction.packing_id";
        $queryData['leftJoin']['item_master'] = "item_master.id = packing_request.item_id";
        $queryData['where']['packing_request.id'] = $id;
        $resultData = $this->rows($queryData);

        $html="";
        if(!empty($resultData)):
            $i=1;
            foreach($resultData as $row):         
                $html .= '<tr>
                    <td class="text-center">'.$i++.'</td>
                    <td class="text-center">['.$row->item_code.'] '.$row->item_name.'</td>
                    <td class="text-center">'.$row->trans_number.'</td>	
                    <td class="text-center">'.$row->total_qty.'</td>	
                    <td class="text-center">'.$row->pack_link_qty.'</td>	
                  </tr>';
               
            endforeach;
        else:
            $html = '<tr><td class="text-center" colspan="4">No Data Found</td></tr>';
        endif;
        return ['status'=>1,'htmlData'=>$html,'result'=>$resultData];
    }
}
?>