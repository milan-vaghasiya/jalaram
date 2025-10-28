<?php 
class PurchaseReportModel extends MasterModel
{
    private $grnTrans = "grn_transaction";
    private $purchaseTrans = "purchase_order_trans";
    private $transMain = "trans_main";
    private $transChild = "trans_child";
    private $qc_instruments = "qc_instruments";

    public function getPurchaseMonitoring($data){
        $queryData = array();
		$queryData['tableName'] = $this->purchaseTrans;
		$queryData['select'] = 'purchase_order_trans.*,purchase_order_master.po_date,item_master.item_name,party_master.party_name,purchase_order_master.po_prefix,purchase_order_master.po_no,purchase_order_master.remark';
		$queryData['join']['purchase_order_master'] = 'purchase_order_master.id = purchase_order_trans.order_id';
		$queryData['leftJoin']['item_master'] = 'item_master.id = purchase_order_trans.item_id';
		$queryData['leftJoin']['party_master'] = 'party_master.id = purchase_order_master.party_id';
		if(!empty($data['item_type'])){ $queryData['where']['item_master.item_type'] = $data['item_type']; }
        $queryData['customWhere'][] = "purchase_order_master.po_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
        
        if($data['report_type'] == 1){
			$queryData['where']['purchase_order_master.order_type'] = 3;
		}else{
			$queryData['where_in']['purchase_order_master.order_type'] = "0,1";
		}
		
		$queryData['order_by']['purchase_order_master.po_date'] = 'ASC';
		return $this->rows($queryData);
    }

    public function getPurchaseReceipt($data){
        $queryData = array();
		$queryData['tableName'] = $this->grnTrans;
		$queryData['select'] = 'grn_transaction.*,grn_master.grn_prefix,grn_master.grn_no,grn_master.grn_date';
		$queryData['join']['grn_master'] = 'grn_master.id = grn_transaction.grn_id';
		$queryData['where']['grn_transaction.item_id'] = $data['item_id'];
		$queryData['where']['grn_transaction.po_trans_id'] = $data['grn_trans_id'];
        $queryData['customWhere'][] = "grn_master.grn_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['order_by']['grn_master.grn_date'] = 'ASC';
		return $this->rows($queryData);
    }
    
    /* Last Purchase Price */
	public function getLastPrice($data){
		$queryData = array();
		$queryData['tableName'] = 'trans_child';
		$queryData['select'] = 'trans_child.price';
		$queryData['leftJoin']['trans_main'] = 'trans_child.trans_main_id = trans_main.id';
		$queryData['where']['trans_child.item_id'] = $data['item_id'];
		$queryData['where']['trans_main.entry_type'] = 12;
		if(!empty($data['from_date']))
		{
			$queryData['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		}
		$queryData['order_by']['trans_main.trans_date'] = 'DESC';
		$queryData['limit']=1;
		
		$result = $this->row($queryData);
		
		return $result;
	}
	
	public function getPurchaseInward($data){
		$queryData = array();
		$queryData['tableName'] = $this->grnTrans;
		$queryData['select'] = 'grn_transaction.*,grn_master.grn_prefix,grn_master.grn_no,grn_master.grn_date,party_master.party_name,item_master.item_name,purchase_order_master.po_prefix,purchase_order_master.po_no,purchase_order_master.po_date';
		$queryData['join']['grn_master'] = 'grn_master.id = grn_transaction.grn_id';
		$queryData['join']['item_master'] = 'item_master.id = grn_transaction.item_id';
		$queryData['join']['party_master'] = 'party_master.id = grn_master.party_id';
		$queryData['leftJoin']['purchase_order_master'] = 'purchase_order_master.id = grn_master.order_id';
		if(!empty($data['item_type'])){ $queryData['where']['item_master.item_type'] = $data['item_type']; }
		if(!empty($data['category_id'])){ $queryData['where_in']['item_master.category_id'] = $data['category_id']; }
        $queryData['customWhere'][] = "grn_master.grn_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['order_by']['grn_master.grn_date'] = 'DESC';
		return $this->rows($queryData);
	}
	
	public function getItemLastPurchasePrice($data){
        $queryData = array();
		$queryData['tableName'] = $this->purchaseTrans;
		$queryData['where']['item_id'] = $data['item_id'];
		$queryData['order_by']['id'] = 'DESC';
		$queryData['limit']=1;
		$result = $this->row($queryData);
		return $result;
    }
    
    public function getItemLastGrnPrice($data){
        $queryData = array();
		$queryData['tableName'] = $this->grnTrans;
		$queryData['select'] = 'AVG(grn_transaction.price) AS price';
		$queryData['join']['grn_master'] = 'grn_master.id = grn_transaction.grn_id';
		$queryData['where']['grn_transaction.item_id'] = $data['item_id'];
		$queryData['where']['grn_master.grn_date <='] = $data['to_date'];
		$result = $this->row($queryData);
		return $result;
    }
    
    //Created By Avruti @08/08/2022
	public function getSupplierWiseItem($data){
        $queryData = array();
		$queryData['tableName'] = $this->grnTrans;
		$queryData['select'] = 'grn_transaction.*,grn_master.party_id,party_master.party_name,item_master.item_name,item_master.item_code';
		$queryData['leftJoin']['grn_master'] = 'grn_master.id = grn_transaction.grn_id';
		$queryData['leftJoin']['party_master'] = 'party_master.id = grn_master.party_id';
		$queryData['leftJoin']['item_master'] = 'item_master.id = grn_transaction.item_id';
		if(!empty($data['item_id'])){$queryData['where']['grn_transaction.item_id'] = $data['item_id'];}
		if(!empty($data['party_id'])){$queryData['where']['grn_master.party_id'] = $data['party_id'];}
        $queryData['group_by'][] = 'grn_master.party_id';
        $queryData['group_by'][] = 'grn_transaction.item_id';
		return $this->rows($queryData);
    }
    
    //Created By Avruti @09/08/2022
	public function getGrnTracking($data){
        $queryData = array();
		$queryData['tableName'] = $this->grnTrans;
		$queryData['select'] = 'grn_transaction.*,grn_master.grn_date,grn_master.grn_no,grn_master.grn_prefix,grn_master.party_id,party_master.party_name,item_master.item_name,item_master.item_code,purchase_inspection.inspection_date';
		$queryData['leftJoin']['grn_master'] = 'grn_master.id = grn_transaction.grn_id';
		$queryData['leftJoin']['party_master'] = 'party_master.id = grn_master.party_id';
		$queryData['leftJoin']['item_master'] = 'item_master.id = grn_transaction.item_id';
		$queryData['leftJoin']['purchase_inspection'] = 'purchase_inspection.ptrans_id = grn_transaction.id';
		$queryData['where']['grn_transaction.item_type'] = 3;
        $queryData['customWhere'][] = "grn_master.grn_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		if(!empty($data['party_id'])){$queryData['where']['grn_master.party_id'] = $data['party_id'];}
		return $this->rows($queryData);
    }
    
    public function getQcInstrumentData($data){
		$queryData = array();
		$queryData['tableName'] = $this->qc_instruments;
		$queryData['select'] = 'qc_instruments.*,purchase_order_master.po_date,purchase_order_master.po_prefix,purchase_order_master.po_no,item_master.item_name,party_master.party_name,purchase_order_trans.qty,purchase_order_trans.price,purchase_order_trans.rec_qty,purchase_order_trans.disc_amt,purchase_order_trans.disc_per';
		$queryData['leftJoin']['purchase_order_trans'] = 'purchase_order_trans.id = qc_instruments.ref_id';
		$queryData['leftJoin']['purchase_order_master'] = 'purchase_order_master.id = purchase_order_trans.order_id';
		$queryData['leftJoin']['item_master'] = 'item_master.id = purchase_order_trans.item_id';
		$queryData['leftJoin']['party_master'] = 'party_master.id = purchase_order_master.party_id';
		$queryData['where']['purchase_order_master.order_type'] = 3;
		
		if(!empty($data['item_type'])){$queryData['where']['item_master.item_type'] = $data['item_type'];}
		if(!empty($data['category_id'])){$queryData['where_in']['item_master.category_id'] = $data['category_id'];}
        $queryData['customWhere'][] = "qc_instruments.grn_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['group_by'][] = 'qc_instruments.ref_id';
		$queryData['order_by']['purchase_order_master.po_date'] = 'DESC';
		return $this->rows($queryData);
	}
	
	public function getQcInstrumentReceive($data){
		$queryData = array();
		$queryData['tableName'] = $this->qc_instruments;
		$queryData['select'] = 'qc_instruments.*,purchase_order_master.po_date,purchase_order_master.po_prefix,purchase_order_master.po_no,purchase_order_trans.qty,purchase_order_trans.price,purchase_order_trans.rec_qty';
		$queryData['leftJoin']['purchase_order_trans'] = 'purchase_order_trans.id = qc_instruments.ref_id';
		$queryData['leftJoin']['purchase_order_master'] = 'purchase_order_master.id = purchase_order_trans.order_id';
		$queryData['where']['purchase_order_master.order_type'] = 3;
		$queryData['where']['qc_instruments.item_id'] = 0;
		$queryData['where']['qc_instruments.ref_id'] = $data['grn_trans_id'];
		
        $queryData['customWhere'][] = "purchase_order_master.po_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['order_by']['purchase_order_master.po_date'] = 'DESC';
		return $this->rows($queryData);
	}
}
?>