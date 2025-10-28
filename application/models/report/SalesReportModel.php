<?php 
class SalesReportModel extends MasterModel
{
    private $stockTrans = "stock_transaction";
    private $transMain = "trans_main";
    private $transChild = "trans_child";
    private $packingMaster = "packing_master";
    private $packingTrans = "packing_transaction";
	
    /* Customer's Order Monitoring */
    public function getOrderMonitor($data){
        $queryData = array();
		$queryData['tableName'] = $this->transChild;
        $queryData['select'] = 'trans_child.*,trans_main.trans_prefix,trans_main.trans_no,trans_main.doc_no,trans_main.doc_date,trans_main.trans_date,trans_main.remark,trans_main.ref_by,trans_main.order_type,trans_main.sales_type,trans_main.delivery_date,party_master.party_code,employee_master.emp_name';
        $queryData['leftJoin']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
		$queryData['leftJoin']['party_master'] = "party_master.id = trans_main.party_id";
		$queryData['leftJoin']['employee_master'] = "employee_master.id = trans_main.created_by";
		$queryData['where']['trans_main.entry_type'] = 4;
		if(!empty($data['party_id'])){ $queryData['where']['trans_main.party_id'] = $data['party_id']; }
		//if($data['trans_status'] != 'ALL'){ $queryData['where']['trans_child.trans_status'] = $data['trans_status']; }
        $queryData['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['order_by']['trans_main.trans_date'] = 'ASC';
		$result = $this->rows($queryData);
		return $result;
    }
	
    public function getInvoiceData($data){
        if(!empty($data['sales_type']) && $data['sales_type'] == 2):
            $queryData = array();
            $queryData['tableName'] = "export_packing";
            $queryData['select'] = "SUM(si.qty) as dqty,trans_main.trans_date,trans_main.trans_no,trans_main.trans_prefix,trans_main.delivery_date,trans_main.id as refId,trans_main.trans_prefix as inv_prefix,trans_main.trans_no as inv_no";
            
            $queryData['leftJoin']['(SELECT * FROM `trans_child` WHERE is_delete=0 AND from_entry_type=0 AND entry_type=19) as comp'] = "export_packing.id = comp.ref_id";
            $queryData['leftJoin']['(SELECT * FROM `trans_child` WHERE is_delete=0 AND from_entry_type=19 AND entry_type=20) as cump'] = "cump.ref_id = comp.id";
            $queryData['leftJoin']['(SELECT * FROM `trans_child` WHERE is_delete=0 AND from_entry_type=20 AND entry_type=11) as cuinv'] = "cuinv.ref_id = cump.id";
            $queryData['leftJoin']['(SELECT * FROM `trans_child` WHERE is_delete=0 AND from_entry_type=11 AND entry_type=8) as si'] = "si.ref_id = cuinv.id";
            $queryData['leftJoin']['trans_main'] = "trans_main.id = si.trans_main_id";
            $queryData['where']['export_packing.so_trans_id'] = $data['id'];
            $queryData['where']['si.is_delete'] = 0;
            $queryData['where']['trans_main.trans_date >'] = $data['trans_date'];
            $queryData['where']['trans_main.entry_type'] = 8;
            $queryData['group_by'][] = "trans_main.trans_no";
            $queryData['group_by'][] = 'si.item_id';
            $result = $this->rows($queryData);
            return $result;
        else:
            $queryData = array();
            $queryData['tableName'] = $this->transChild;
            $queryData['select'] = 'SUM(trans_child.qty) as dqty,trans_main.trans_date,trans_main.trans_no,trans_main.trans_prefix,trans_main.delivery_date,transMain.id as refId,transMain.trans_prefix as inv_prefix,transMain.trans_no as inv_no';
            $queryData['leftJoin']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
            $queryData['leftJoin']['trans_main as transMain'] = "FIND_IN_SET(trans_main.id,transMain.ref_id) > 0 AND transMain.is_delete = 0 AND transMain.entry_type IN(6,7)";
            $queryData['where']['trans_child.ref_id'] = $data['id'];
            $queryData['where_in']['trans_child.entry_type'] = '5,6,7';
            $queryData['where']['trans_main.is_delete'] = 0;
            $queryData['group_by'][] = 'trans_main.trans_no';
            $queryData['group_by'][] = 'transMain.trans_no';
            $queryData['group_by'][] = 'trans_child.item_id';
            $result = $this->rows($queryData);
            return $result;
        endif;
    }
	
    public function getDeliveredQty($item_id,$trans_main_id){
        $data['tableName'] = $this->transChild;
        $data['select'] = 'SUM(trans_child.qty) as dqty';
        $data['where']['trans_child.item_id'] = $item_id;
        //$data['where']['trans_child.trans_main_id'] = $trans_main_id;
        $data['where']['trans_child.id'] = $trans_main_id;
        $data['group_by'][] = 'trans_child.trans_main_id';
        $data['group_by'][] = 'trans_child.item_id';
        return $this->row($data);
    }
    
    public function getDispatchPlan($data){
        $queryData = array();
		$queryData['tableName'] = $this->transChild;
        $queryData['select'] = 'trans_child.*,trans_main.id as so_id,trans_main.trans_no,trans_main.doc_no,trans_main.trans_date, trans_main.remark, trans_main.ref_by,trans_main.order_type,trans_main.sales_type,trans_main.delivery_date, party_master.party_code, party_master.currency, item_master.packing_qty as packingQty';
        $queryData['join']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
		$queryData['leftJoin']['party_master'] = "party_master.id = trans_main.party_id";
		$queryData['leftJoin']['item_master'] = "item_master.id = trans_child.item_id";
		$queryData['where']['trans_main.entry_type'] = 4;
        $queryData['where']['trans_child.trans_status'] = 0;
        //$queryData['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
        $queryData['customWhere'][] = "trans_child.cod_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['order_by']['trans_child.cod_date'] = 'ASC';
		return $this->rows($queryData);
    }
    
	public function getPackingPlan($data){
        $queryData = array();
		$queryData['tableName'] = 'packing_master';
        $queryData['select'] = 'packing_master.id,packing_master.item_id,SUM(packing_master.packing_qty) as packing_qty,packing_master.packing_date,party_master.party_code, party_master.currency,item_master.qty as totalStock,item_master.item_code,(item_master.price*currency.inrrate) as item_price';
		$queryData['join']['item_master'] = "item_master.id = packing_master.item_id";
		$queryData['leftJoin']['party_master'] = "party_master.id = item_master.party_id";
		$queryData['leftJoin']['currency'] = "currency.currency = party_master.currency";
	//	$queryData['where']['packing_master.item_id'] = 713;
        //$queryData['customWhere'][] = "packing_master.packing_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['order_by']['packing_master.packing_date'] = 'ASC';
		//$queryData['group_by'][] = 'packing_master.packing_date';
		$queryData['group_by'][] = 'packing_master.item_id';
		return $this->rows($queryData);
    }
	
    public function getRFDStock($item_id){
        $data['tableName'] = $this->stockTrans;
        $data['select'] = "SUM(qty) as rfd_qty";
        $data['where']['location_id'] = $this->RTD_STORE->id;
        $data['where']['item_id'] = $item_id;
        return $this->row($data);
    }
	
	public function getDispatchOnPacking($data){
        $queryData = array();
		$queryData['tableName'] = 'trans_child';
        $queryData['select'] = 'SUM(trans_child.qty) as dispatch_qty,AVG(trans_child.price) as dispatch_price,SUM(trans_child.disc_amount) as disc_amt,trans_child.item_id';
		$queryData['leftJoin']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
		$queryData['where_in']['trans_main.entry_type'] = '6,7,8';
		$queryData['where']['trans_child.item_id'] = $data['item_id'];
        //$queryData['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['group_by'][] = 'trans_child.item_id';
		return $this->row($queryData);
    }
	
	/* On Invoice Data */
	public function getDispatchMaterial($data){
        $queryData = array();
		$queryData['tableName'] = 'trans_child';
        $queryData['select'] = 'SUM(trans_child.qty) as dispatch_qty';
		$queryData['join']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
		$queryData['where_in']['trans_main.entry_type'] = '6,7,8';
		$queryData['where']['trans_child.item_id'] = $data['item_id'];
        // $queryData['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['where']['trans_main.trans_date >= '] = $data['from_date'];
		$dm = $this->row($queryData);//if($data['item_id'] = 1289){print_r($this->db->last_query());}
		return $dm;
    }
	
	public function getJobcardBySO($sales_order_id,$product_id){
		$queryData = array();
		$queryData['tableName'] = 'job_card';
		$queryData['where']['sales_order_id'] = $sales_order_id;
		$queryData['where']['product_id'] = $product_id;
		return $this->row($queryData);
	}
	
    public function getWIPQtyForDispatchPlan($data){
        $queryData['tableName'] = "job_card";
        $queryData['select'] = "SUM(job_card.qty) as qty";
        $queryData['where']['job_card.sales_order_id'] = $data['trans_main_id'];
        $queryData['where']['job_card.product_id'] = $data['item_id'];
        $queryData['where']['job_card.order_status !=']= 4;
		return $this->rows($queryData);
    }
	
    public function getCurrencyConversion($currency){ 
        $data['tableName'] = 'currency';
        $data['where']['currency'] = $currency;
        $result= $this->rows($data);
        return $result;
    }
    
    public function getDispatchSummary($data){
        $queryData = array();
		$queryData['tableName'] = $this->transChild;
        $queryData['select'] = 'trans_child.*,trans_main.trans_no,trans_main.trans_prefix,trans_main.trans_date,party_master.party_code,party_master.party_name,party_master.currency,transMain.trans_prefix as inv_prefix,transMain.trans_no as inv_no,transChild.price as inv_price';
        $queryData['join']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        $queryData['leftJoin']['trans_main as transMain'] = "transMain.ref_id = trans_main.id";
         $queryData['leftJoin']['trans_child as transChild'] = "transChild.ref_id = trans_child.id";
		$queryData['leftJoin']['party_master'] = "party_master.id = trans_main.party_id";
        $queryData['where']['trans_main.party_id'] = $data['party_id'];
        $queryData['where']['trans_main.entry_type'] = 5;
        if(!empty($data['item_id'])){$queryData['where_in']['trans_child.item_id'] = $data['item_id'];}
        $queryData['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['order_by']['trans_main.trans_date'] = 'ASC';
		$result = $this->rows($queryData);
		return $result;
    }
    
    public function getSalesEnquiry($data){
        $queryData = array();
		$queryData['tableName'] = $this->transChild;
        $queryData['select'] = 'trans_child.*,trans_main.trans_no,trans_main.trans_prefix,trans_main.trans_date,party_master.party_code,party_master.party_name,party_master.currency,rejection_comment.remark as reason';
        $queryData['join']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
		$queryData['leftJoin']['party_master'] = "party_master.id = trans_main.party_id";
		$queryData['leftJoin']['rejection_comment'] = "rejection_comment.id = trans_child.item_remark";
		$queryData['where']['trans_child.entry_type'] = 1;
		$queryData['where']['trans_child.feasible'] = 'No';
        if(!empty($data['reson_id']))    
            $queryData['where']['trans_child.item_remark'] = $data['reson_id'];
        if(!empty($data['party_id']))
            $queryData['where']['trans_main.party_id'] = $data['party_id'];
        $queryData['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['order_by']['trans_main.trans_date'] = 'ASC';
		$result = $this->rows($queryData);
		return $result;
    }
    
    /* Monthly Sales Report */
    public function getSalesData($data)
    {
        $queryData = array();
		$queryData['tableName'] = $this->transMain;
        $queryData['select'] = 'trans_main.*';
        $queryData['customWhere'][] = 'trans_main.entry_type IN(6,7,8)';
        $queryData['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['order_by']['trans_main.trans_date'] = 'ASC';
        if($data['party']!=0){ $queryData['where']['party_id'] = $data['party']; }
        if($data['product']!=0){
            $queryData['leftJoin']['trans_child'] = "trans_child.trans_main_id = trans_main.id";
            $queryData['where']['trans_child.item_id'] = $data['product'];
        }
		$result = $this->rows($queryData);
		return $result;
    }
    
    /* Dispatch Plan Summary */
    public function getDispatchPlanSummary($data){
        $queryData = array();
		$queryData['tableName'] = $this->transChild;
        $queryData['select'] = 'trans_child.*, trans_main.trans_prefix, trans_main.trans_no,trans_main.doc_no,party_master.party_code,party_master.party_name,party_master.currency,item_master.item_code,trans_main.sales_type';
        $queryData['leftJoin']['trans_main'] = "trans_child.trans_main_id = trans_main.id";
        $queryData['leftJoin']['party_master'] = "party_master.id = trans_main.party_id";
        $queryData['leftJoin']['item_master'] = "trans_child.item_id = item_master.id";
        if(!empty($data['party_id'])){ $queryData['where']['trans_main.party_id'] = $data['party_id']; }        
        if(!empty($data['sales_type'])){ $queryData['where']['trans_main.sales_type'] = $data['sales_type'];}
        $queryData['where']['trans_main.entry_type'] = 4;
        $queryData['where']['trans_main.is_approve != '] = 0;
        
        if(!empty($data['from_date']) && !empty($data['to_date'])){
            $queryData['customWhere'][] = "trans_child.cod_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		    $queryData['order_by']['trans_child.cod_date'] = 'ASC';
        }else{
            $queryData['customWhere'][] = "trans_child.prod_target_date BETWEEN '".$data['prod_from_date']."' AND '".$data['prod_to_date']."'";
            $queryData['order_by']['trans_child.prod_target_date'] = 'ASC';
        }
		$result = $this->rows($queryData);
		return $result;
    }
    
    /*
    * Create By : Karmi @06-12-2021
    * Updated By : 
    * Note : 
    */
    public function getEnquiryMonitoring($data){
		$queryData['tableName'] = $this->transMain;
        $queryData['select'] = 'trans_main.*';
		$queryData['where']['trans_main.entry_type'] = 1;
        $queryData['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['group_by'][] = 'trans_main.party_id';
        $result = $this->rows($queryData); 
        return $result;
    }
	
    public function getEnquiryCount($data){ 
		$result = new StdClass; $result->pending=0; $result->totalEnquiry=0; $result->quoted=0; $result->confirmSo=0; $result->pendingSo=0;
        $queryData = Array();
		$queryData['tableName'] = $this->transMain;
		$queryData['where']['party_id'] = $data['party_id'];
		$queryData['where']['entry_type'] = 1;
        $queryData['customWhere'][] = "trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$totalEnquiry = $this->rows($queryData);
        $result->totalEnquiry = count($totalEnquiry);
		
		$queryData = Array();
		$queryData['tableName'] = $this->transMain;
		$queryData['select'] = "trans_main.*";
        $queryData['join']['trans_child'] = 'trans_child.trans_main_id = trans_main.id';
		$queryData['where']['trans_main.party_id'] = $data['party_id'];
        $queryData['where']['trans_child.trans_status'] = 1;
		$queryData['where']['trans_main.entry_type'] = 1;
        $queryData['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['group_by'][] = 'trans_main.id';
        $quoted = $this->rows($queryData);
        $result->quoted = count($quoted);
        /*$queryData = Array();
		$queryData['tableName'] = $this->transMain;
		$queryData['select'] = "trans_main.*";
        $queryData['join']['trans_child'] = 'trans_child.trans_main_id = trans_main.id';
		$queryData['where']['trans_main.party_id'] = $data['party_id'];
        $queryData['where']['trans_child.trans_status != '] = 1;
		$queryData['where']['trans_main.entry_type'] = 1;
        $queryData['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
        $queryData['group_by'][] = 'trans_main.id';
        $pending = $this->rows($queryData);
		$result->pending = count($pending);*/
        $result->pending = $result->totalEnquiry - $result->quoted;
        
        $queryData = Array();
		$queryData['tableName'] = $this->transMain;
        $queryData['join']['trans_child'] = 'trans_child.trans_main_id = trans_main.id';
		$queryData['where']['trans_main.party_id'] = $data['party_id'];
		$queryData['where']['trans_child.from_entry_type'] = 1;
		$queryData['where_in']['trans_child.entry_type'] = '2,3';
		$queryData['where']['trans_child.trans_status'] = 1;
        $queryData['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
        $queryData['group_by'][] = 'trans_main.id';
        $confirmSo = $this->rows($queryData);
        $result->confirmSo = count($confirmSo);
       /* $queryData = Array();
		$queryData['tableName'] = $this->transMain;
        $queryData['join']['trans_child'] = 'trans_child.trans_main_id = trans_main.id';
		$queryData['where']['trans_main.party_id'] = $data['party_id'];
		$queryData['where']['trans_child.from_entry_type'] = 1;
		$queryData['where']['trans_child.trans_status != '] = 1;
        $queryData['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
        $queryData['group_by'][] = 'trans_main.id';
        $pendingSo = $this->rows($queryData);
        $result->pendingSo = count($pendingSo);*/
        $result->pendingSo = $result->totalEnquiry - $result->confirmSo;
		return $result;
	}
	
	 /**
     * Created By Mansee @ 13-12-2021
     */
    public function getSalesEnquiryByParty($data){
        $queryData = Array();
		$queryData['tableName'] = $this->transMain;
		$queryData['where']['party_id'] = $data['party_id'];
		$queryData['where']['entry_type'] = 1;
        $queryData['customWhere'][] = "trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$totalEnquiry = $this->rows($queryData);
        return $totalEnquiry;
    }
	
    /**
     * Created By Mansee @ 13-12-2021
     */
    public function getSalesQuotation($ref_id){
        $queryData = Array();
		$queryData['tableName'] = $this->transMain;
		$queryData['where']['ref_id'] = $ref_id;
		$queryData['where']['entry_type'] = 2;
		$return= $this->rows($queryData);
        return $return;
    }
	
    /**
     * Created By Mansee @ 13-12-2021
     */
    public function getSalesOrder($ref_id){
        $queryData = Array();
		$queryData['tableName'] = $this->transMain;
		$queryData['where']['ref_id'] = $ref_id;
		$queryData['where']['entry_type'] = 4;
		$return= $this->rows($queryData);
        return $return;
    }
	
    /* 
        Created By Avruti @ 30-12-2021
    */
    public function getSalesInvoiceTarget($postData){
        $fdate = date("Y-m-d",strtotime($postData['month']));
		$tdate  = date("Y-m-t",strtotime($postData['month']));
        $queryData = Array();
		$queryData['tableName'] = $this->transMain;
        $queryData['select'] = 'SUM(trans_main.net_amount) as totalInvoiceAmt';
		$queryData['where']['trans_main.party_id'] = $postData['party_id'];
		$queryData['where_in']['trans_main.entry_type'] = [6,7,8];
        $queryData['customWhere'][] = "trans_main.trans_date BETWEEN '".$fdate."' AND '".$tdate."'";
        //$queryData['group_by'][] = 'trans_main.party_id';
        $result = $this->row($queryData);
		return $result;  
    }
    
    public function getSalesOrderTarget($postData){
        $fdate = date("Y-m-d",strtotime($postData['month']));
		$tdate  = date("Y-m-t",strtotime($postData['month']));
        $queryData = Array();
		$queryData['tableName'] = $this->transMain;
        $queryData['select'] = 'SUM(trans_main.net_amount * inrrate) as totalOrderAmt';
		$queryData['where']['trans_main.party_id'] = $postData['party_id'];
		$queryData['where_in']['trans_main.entry_type'] = [4];
        $queryData['customWhere'][] = "trans_main.trans_date BETWEEN '".$fdate."' AND '".$tdate."'";
        //$queryData['group_by'][] = 'trans_main.party_id';
        $result = $this->row($queryData);
		return $result;  
    }
    
    public function getOrderSummary($data){
		$queryData['tableName'] = $this->transChild;
		$queryData['select'] = 'trans_child.*,trans_main.trans_date, trans_main.trans_prefix, trans_main.trans_no, trans_main.sales_type, party_master.party_name,party_master.currency, item_master.item_name, item_master.item_code';
        $queryData['join']['trans_main'] = 'trans_main.id = trans_child.trans_main_id';
        $queryData['leftJoin']['party_master'] = 'trans_main.party_id = party_master.id';
        $queryData['leftJoin']['item_master'] = 'trans_child.item_id = item_master.id';
		$queryData['where']['trans_main.entry_type'] = 4;
		$queryData['where']['trans_child.trans_status != '] = 2;
		if(!empty($data['party_id'])){$queryData['where']['trans_main.party_id'] = $data['party_id'];}
		if(!empty($data['item_id'])){$queryData['where']['trans_child.item_id'] = $data['item_id'];}
        $queryData['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
        $queryData['order_by']['trans_child.trans_main_id'] = "ASC";
        $queryData['order_by']['trans_child.item_id'] = "ASC";
        $queryData['order_by']['trans_child.id'] = "ASC";
		return $this->rows($queryData);
    }
	
    public function getOrderWiseDispatch($ref_id,$sales_type = ""){
        if($sales_type == 2):
            $queryData = array();
            $queryData['tableName'] = "export_packing";
            $queryData['select'] = "SUM(si.qty) as dispatch_qty";
            $queryData['leftJoin']['(SELECT * FROM `trans_child` WHERE is_delete=0 AND from_entry_type=0 AND entry_type=19) as comp'] = "export_packing.id = comp.ref_id";
            $queryData['leftJoin']['(SELECT * FROM `trans_child` WHERE is_delete=0 AND from_entry_type=19 AND entry_type=20) as cump'] = "cump.ref_id = comp.id";
            $queryData['leftJoin']['(SELECT * FROM `trans_child` WHERE is_delete=0 AND from_entry_type=20 AND entry_type=11) as cuinv'] = "cuinv.ref_id = cump.id";
            $queryData['leftJoin']['(SELECT * FROM `trans_child` WHERE is_delete=0 AND from_entry_type=11 AND entry_type=8) as si'] = "si.ref_id = cuinv.id";
            $queryData['where']['export_packing.so_trans_id'] = $ref_id;
            $result = $this->row($queryData);
            //$this->printQuery();exit;
            return $result;
        else:
            $queryData = array();
            $queryData['tableName'] = $this->transChild;
    		$queryData['select'] = 'SUM(trans_child.qty) as dispatch_qty';
    		$queryData['where']['trans_child.ref_id'] = $ref_id;
    		$queryData['where']['trans_child.from_entry_type'] = 4;
    		return $this->row($queryData);
    	endif;
    }
    
    public function getCustomerEnquiryRegister($data){
        $queryData = array();
		$queryData['tableName'] = $this->transChild;
        $queryData['select'] = 'trans_child.*,trans_main.trans_no,trans_main.trans_prefix,trans_main.trans_date,trans_main.doc_no,trans_main.doc_date,trans_main.ref_by,party_master.party_code,party_master.party_name,rejection_comment.remark as reason,employee_master.emp_name,transMain.trans_prefix as quote_prefix,transMain.trans_no as quote_no,transMain.inrrate as quote_inrrate,transMain.trans_date as quote_date,so.trans_prefix as so_prefix,so.trans_no as so_no,so.trans_date as so_date,quote_child.amount as quote_amount,executive.emp_name as sales_executive';
        
        $queryData['join']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
		$queryData['leftJoin']['party_master'] = "party_master.id = trans_main.party_id";
		$queryData['leftJoin']['rejection_comment'] = "rejection_comment.id = trans_child.item_remark";
		$queryData['leftJoin']['employee_master'] = "employee_master.id = trans_child.created_by";
		$queryData['leftJoin']['employee_master as executive'] = "executive.id = trans_main.sales_executive";
        $queryData['leftJoin']['trans_main as transMain'] = "transMain.ref_id = trans_main.id";
        $queryData['leftJoin']['trans_main as so'] = "so.ref_id = transMain.id AND so.entry_type=4";
        $queryData['leftJoin']['trans_child as quote_child'] = "quote_child.ref_id = trans_child.id AND quote_child.entry_type=2";
		$queryData['where']['trans_child.entry_type'] = 1;
        if(!empty($data['reson_id']))    
            $queryData['where']['trans_child.item_remark'] = $data['reson_id'];
        if(!empty($data['party_id']))
            $queryData['where']['trans_main.party_id'] = $data['party_id'];
        $queryData['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['order_by']['trans_main.trans_date'] = 'ASC';
		$result = $this->rows($queryData);
		return $result;
    }
    
    //Created By Karmi @05/07/2022
    public function getSalesQuotationMonitoring($data){
        $queryData = array();
		$queryData['tableName'] = $this->transChild;
        $queryData['select'] = 'trans_child.id,trans_child.trans_main_id,trans_child.trans_status, trans_child.cod_date,trans_child.confirm_by,trans_main.trans_prefix,trans_main.trans_no,trans_main.trans_date,trans_main.party_id,trans_main.party_name,trans_main.extra_fields';
        $queryData['join']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        $queryData['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
        if(!empty($data['party_id']))
            $queryData['where']['trans_main.party_id'] = $data['party_id'];
        $queryData['where']['trans_child.entry_type'] = 2;
        $queryData['where']['trans_child.trans_status != '] = 2;
		$queryData['group_by'][]='trans_child.trans_main_id';
		$resultData = $this->rows($queryData);
		return $resultData;
	}
	
	/* UPDATED BY : MILAN V. 12-09-2025 */
	public function getPackingHistory($data){
        $queryData = array();
        $queryData['tableName'] = $this->packingTrans;
        // $queryData['select'] = "packing_master.*,packing_transaction.so_trans_id,packing_transaction.id as ptrans_id,packing_transaction.total_box_qty,packing_transaction.dispatch_qty,ifnull(LEFT(item_master.item_code,5), '') as party_code,(CASE WHEN packing_transaction.so_trans_id = 0 THEN 'Self Packing' ELSE CONCAT(trans_main.trans_prefix,trans_main.trans_no) END) as so_no,item_master.item_code,item_master.price,item_party.currency";

        $queryData['select'] = "packing_master.*,
        packing_transaction.so_trans_id,
        packing_transaction.id AS ptrans_id,
        packing_transaction.total_box_qty,
        IFNULL((
            SELECT ABS(SUM(st.qty))
            FROM stock_transaction AS st
            WHERE st.batch_no = packing_master.trans_number
            AND st.item_id = packing_master.item_id
            AND st.size = packing_transaction.qty_box
            AND st.trans_type = 2 
            AND st.ref_type != 6
            AND st.is_delete = 0
        ),0) AS dispatch_qty,

        IFNULL(LEFT(item_master.item_code,5), '') AS party_code,
        
        CASE WHEN packing_transaction.so_trans_id = 0 THEN 'Self Packing' ELSE CONCAT(trans_main.trans_prefix, trans_main.trans_no) END AS so_no, item_master.item_code,item_master.price,item_party.currency";

        $queryData['leftJoin']['packing_master'] = "packing_master.id = packing_transaction.packing_id";
        $queryData['leftJoin']['trans_child'] = "packing_transaction.so_trans_id = trans_child.id";
        $queryData['leftJoin']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        $queryData['leftJoin']['item_master'] = "packing_master.item_id = item_master.id";
        $queryData['leftJoin']['party_master as item_party'] = "item_party.id = item_master.party_id";
        
        $queryData['customWhere'][] = "packing_master.trans_date < '".$data['to_date']."'";
        if(!empty($data['party_id'])){ $queryData['where']['item_party.id'] = $data['party_id']; }

        if($data['packing_type'] == 1)://Regular
            $queryData['where']['packing_master.entry_type'] = "Regular";
            $queryData['where']['packing_master.is_final'] = "1";
        elseif($data['packing_type'] == 2)://Tentative
            $queryData['where']['packing_master.entry_type'] = "Export";
            $queryData['where']['packing_master.is_final'] = "0";
        elseif($data['packing_type'] == 3)://Export
            $queryData['where']['packing_master.entry_type'] = "Export";
            $queryData['where']['packing_master.is_final'] = "1";
        endif;
            
        if($data['dispatch_status'] == 1)://Pending For Dispatch
            // $queryData['customWhere'][] = '(packing_transaction.total_box_qty - packing_transaction.dispatch_qty) > 0';   
            $queryData['having'][] = '(packing_transaction.total_box_qty - dispatch_qty) > 0';

        elseif($data['dispatch_status'] == 2)://Dispatched
            // $queryData['customWhere'][] = 'packing_transaction.dispatch_qty > 0';
            $queryData['having'][] = '(packing_transaction.total_box_qty - dispatch_qty) > 0';
        endif;

        $queryData['order_by']['packing_master.trans_date'] = 'ASC';
        
        return $this->rows($queryData);
    }
	
	/*
	public function getPackingHistory($data){
        $queryData = array();
        $queryData['tableName'] = $this->packingTrans;
        $queryData['select'] = "packing_master.*,packing_transaction.so_trans_id,packing_transaction.id as ptrans_id,packing_transaction.total_box_qty,packing_transaction.dispatch_qty,ifnull(party_master.party_code,ifnull(item_party.party_code,'')) as party_code,(CASE WHEN packing_transaction.so_trans_id = 0 THEN 'Self Packing' ELSE CONCAT(trans_main.trans_prefix,trans_main.trans_no) END) as so_no,item_master.item_code,item_master.price,item_party.currency";
        $queryData['leftJoin']['packing_master'] = "packing_master.id = packing_transaction.packing_id";
        $queryData['leftJoin']['trans_child'] = "packing_transaction.so_trans_id = trans_child.id";
        $queryData['leftJoin']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        $queryData['leftJoin']['party_master'] = "party_master.id = trans_main.party_id";
        $queryData['leftJoin']['item_master'] = "packing_master.item_id = item_master.id";
        $queryData['leftJoin']['party_master as item_party'] = "item_party.id = item_master.party_id";
        
        $queryData['customWhere'][] = "packing_master.trans_date < '".$data['to_date']."'";
        if(!empty($data['party_id'])){ $queryData['where']['item_party.id'] = $data['party_id']; }

        if($data['packing_type'] == 1)://Regular
            $queryData['where']['packing_master.entry_type'] = "Regular";
            $queryData['where']['packing_master.is_final'] = "1";
        elseif($data['packing_type'] == 2)://Tentative
            $queryData['where']['packing_master.entry_type'] = "Export";
            $queryData['where']['packing_master.is_final'] = "0";
        elseif($data['packing_type'] == 3)://Export
            $queryData['where']['packing_master.entry_type'] = "Export";
            $queryData['where']['packing_master.is_final'] = "1";
        endif;
            
        if($data['dispatch_status'] == 1)://Pending For Dispatch
            //$queryData['customWhere'][] = 'packing_transaction.dispatch_qty <= 0';
            $queryData['customWhere'][] = '(packing_transaction.total_box_qty - packing_transaction.dispatch_qty) > 0';
        elseif($data['dispatch_status'] == 2)://Dispatched
            $queryData['customWhere'][] = 'packing_transaction.dispatch_qty > 0';
        endif;

        $queryData['order_by']['packing_master.trans_date'] = 'ASC';
        return $this->rows($queryData);
    }
    */
    
    public function getOrderItemList(){
        $queryData = array();
        $queryData['tableName'] = $this->transChild;
        $queryData['select'] = "trans_child.item_id,item_master.item_code,item_master.item_name,SUM(trans_child.qty) as order_qty,SUM(trans_child.dispatch_qty) as dispatch_qty,SUM(trans_child.qty - trans_child.dispatch_qty) as pending_qty";
        $queryData['leftJoin']['item_master'] = "trans_child.item_id = item_master.id";
        $queryData['where']['trans_child.entry_type'] = 4;
        $queryData['having'][] = "SUM(trans_child.qty - trans_child.dispatch_qty) > 0";
        $queryData['group_by'][] = "trans_child.item_id";
        $result = $this->rows($queryData);
        return $result;
    }
    
    /*  Appointment Register Report*/
    public function getAppointmentRegister($data){
        $queryData = array();
        $queryData['tableName'] = "sales_logs";
        $queryData['select'] = "sales_logs.id,sales_logs.ref_date,sales_logs.notes,sales_logs.remark,sales_logs.log_type,sales_logs.updated_at,sales_logs.mode,sales_logs.lead_id,party_master.party_name";

        $queryData['leftJoin']['party_master '] = "party_master.id = sales_logs.lead_id";
        
        if(!empty($data['mode'])):
            $queryData['where']['sales_logs.mode'] = $data['mode'];
        endif;

        if($data['status'] == 1){
            $queryData['customWhere'][] = 'sales_logs.remark IS NULL';
        }elseif($data['status'] == 2){
            $queryData['customWhere'][] = 'sales_logs.remark IS NOT NULL';
        }elseif($data['status'] == 3){ 
            $queryData['customWhere'][] = 'DATE(sales_logs.ref_date) < DATE(sales_logs.updated_at)';
        }
        $queryData['customWhere'][] = "DATE(sales_logs.ref_date) BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
        $queryData['where']['sales_logs.log_type'] = 3;
        $queryData['order_by']['sales_logs.ref_date'] = 'ASC';
        $result = $this->rows($queryData);
        return $result;  
    }

    /*  FollowUp Register Report*/
    public function getFollowUpRegister($data){
        $queryData = array();
        $queryData['tableName'] = "sales_logs";
        $queryData['select'] = "sales_logs.id,sales_logs.created_at,sales_logs.notes,sales_logs.log_type,sales_logs.party_id,party_master.party_name";
        $queryData['leftJoin']['party_master'] = "party_master.id = sales_logs.lead_id";
        $queryData['customWhere'][] = "DATE(sales_logs.created_at) BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
        $queryData['where']['sales_logs.log_type'] = 2;
        return $this->rows($queryData);  
    }
}
?>