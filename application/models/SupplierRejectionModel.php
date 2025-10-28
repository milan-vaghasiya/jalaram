<?php
class SupplierRejectionModel extends MasterModel{
    private $stockTrans = "stock_transaction";
    private $grnItemTable = "grn_transaction";

    public function getItemStockOnGrn($data){
        $queryData = array();
        $queryData['tableName'] = $this->stockTrans;
        $queryData['select'] = "grn_master.grn_no,grn_master.grn_prefix,grn_master.grn_date,grn_master.challan_no,grn_master.party_id,purchase_order_master.po_no,purchase_order_master.po_prefix, party_master.party_name,stock_transaction.item_id,SUM(st.qty) as stock_qty, st.batch_no, st.location_id, st.location, st.store_name";

        $queryData['leftJoin']['grn_transaction'] = "stock_transaction.ref_id = grn_transaction.id";
        $queryData['leftJoin']['grn_master'] = "grn_transaction.grn_id = grn_master.id";
        $queryData['leftJoin']['party_master'] = "party_master.id = grn_master.party_id";
        $queryData['leftJoin']['purchase_order_master'] = "purchase_order_master.id = grn_master.order_id";

        $queryData['leftJoin']['( SELECT SUM(stock_transaction.qty) as qty, stock_transaction.batch_no, stock_transaction.location_id, lm.location, lm.store_name FROM stock_transaction LEFT JOIN location_master as lm ON lm.id=stock_transaction.location_id WHERE stock_transaction.is_delete = 0 AND stock_transaction.stock_effect = 1 AND stock_transaction.location_id != '.$this->SUP_REJ_STORE->id.' AND stock_transaction.item_id = '.$data['item_id'].' GROUP BY stock_transaction.location_id,stock_transaction.batch_no HAVING SUM(stock_transaction.qty) > 0 ) as st'] = "stock_transaction.location_id = st.location_id AND stock_transaction.batch_no = st.batch_no";

        $queryData['where']['stock_transaction.item_id'] = $data['item_id'];
        $queryData['where']['stock_transaction.ref_type'] = 1;
        $queryData['where']['stock_transaction.trans_type'] = 1;
        $queryData['group_by'][] = "stock_transaction.ref_id";
        $queryData['having'][] = 'SUM(st.qty) > 0';

        $result = $this->rows($queryData);
        return $result;
    }
    
    public function getPurchaseInspectData($data) {
		$queryData['tableName'] = $this->grnItemTable;
        $queryData['select'] = "grn_transaction.*,grn_master.party_id,item_master.item_name,grn_master.grn_no,grn_master.grn_prefix,grn_master.grn_date,grn_master.challan_no,party_master.party_name, purchase_inspection.id as pur_insp_id, purchase_inspection.reject_qty, purchase_inspection.inspection_date,purchase_inspection.reject_qty,purchase_order_master.po_no,purchase_order_master.po_prefix,location_master.location,location_master.store_name";
		$queryData['leftJoin']['grn_master'] = "grn_master.id = grn_transaction.grn_id";
		$queryData['leftJoin']['item_master'] = "item_master.id = grn_transaction.item_id";
		$queryData['leftJoin']['purchase_inspection'] = "purchase_inspection.ptrans_id = grn_transaction.id";
        $queryData['leftJoin']['party_master'] = "party_master.id = grn_master.party_id";
        $queryData['leftJoin']['purchase_order_master'] = "purchase_order_master.id = grn_master.order_id";
        $queryData['leftJoin']['location_master'] = "location_master.id = grn_transaction.location_id";
        $queryData['where']['purchase_inspection.inspection_status'] = $data['insp_status'];
		$queryData['where']['purchase_inspection.item_id'] = $data['item_id'];
		$queryData['where']['purchase_inspection.approve_by'] = 0;
		$result = $this->rows($queryData);
        return $result;
	}

    public function saveSupplierRejection($data){
        try{
            $this->db->trans_begin();

            $pur_insp_id = (isset($data['pur_insp_id']))?$data['pur_insp_id']:0;
            $party_id = $data['party_id'];
            
            unset($data['pur_insp_id'],$data['party_id']);

            if(empty($pur_insp_id)){
                /* Deduct From Currect Stock */
                $data['trans_type'] = 2;
                $data['ref_type'] = 34;
                $data['qty'] = ($data['qty'] * -1);
                $data['ref_date'] = date("Y-m-d");
                $data["ref_no"] = strtotime(date("Y-m-d H:i:s"));
                $data['remark'] = $party_id;
                $data["created_by"] = $this->loginId;
                $data["created_at"] = date('Y-m-d H:i:s');
    
                $this->store("stock_transaction",$data);
            }else{
                $this->edit("purchase_inspection",['id'=>$pur_insp_id],['approve_by'=>$this->loginId,'approve_at'=>date('Y-m-d H:i:s')]);
            }

            /* Add Stock to Supplier Rejection */
            $data['trans_type'] = 1;
            $data['ref_type'] = 34;
            $data['qty'] = abs($data['qty']);
            $data['location_id'] = $this->SUP_REJ_STORE->id;
            $this->store("stock_transaction",$data);

            $result = ['status'=>1,'message'=>"Supplier Rejection saved successfully."];
            if ($this->db->trans_status() !== FALSE):
    			$this->db->trans_commit();
    			return $result;
    		endif;
    	}catch(\Exception $e){
    		$this->db->trans_rollback();
    	   return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
    	}	
    }

    public function deleteSupplierRejection($id){
        try{
            $this->db->trans_begin();

            $this->trash("stock_transaction",['ref_no'=>$id,'ref_type'=>34]);

            $result = ['status'=>1,'message'=>"Supplier Rejection deleted successfully."];
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