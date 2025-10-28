<?php 
class StoreReportModel extends MasterModel
{
    private $stockTrans = "stock_transaction";
    private $jobDispatch = "job_material_dispatch";
    private $itemMaster = "item_master";
	private $itemGroup = "item_group";
    private $locationMaster = "location_master";
    private $jobCard = "job_card";
    private $tools_issue = "tools_issue";

	/* Issue Register Data */
    public function getIssueRegister($data){
        $result = [];
        if(empty($data['issue_tpye'])){
            $queryData = array();
    		$queryData['tableName'] = $this->stockTrans;
    		$queryData['select'] = 'stock_transaction.*,job_material_dispatch.collected_by,job_material_dispatch.remark,job_material_dispatch.id as dispatch_id,item_master.item_name, item_master.price as itemPrice, department_master.name as dept_name';
    		$queryData['leftJoin']['job_material_dispatch'] = 'job_material_dispatch.id = stock_transaction.ref_id';
    		$queryData['leftJoin']['item_master'] = 'item_master.id = stock_transaction.item_id';
    		$queryData['leftJoin']['department_master'] = 'department_master.id = job_material_dispatch.dept_id';
    		$queryData['where_in']['stock_transaction.ref_type'] = '3,21';
    		if(!empty($data['item_type'])){$queryData['where']['item_master.item_type'] = $data['item_type'];}
    		if(!empty($data['category_id'])){$queryData['where_in']['item_master.category_id'] = $data['category_id'];}
    		if(!empty($data['dept_id'])){$queryData['where']['job_material_dispatch.dept_id'] = $data['dept_id'];}
            $queryData['customWhere'][] = "stock_transaction.ref_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
    		$queryData['order_by']['stock_transaction.ref_date'] = 'ASC';
    		$result = $this->rows($queryData);
        }else{
    		$queryData['tableName'] = $this->stockTrans;
    		$queryData['select'] = 'stock_transaction.*,SUM(abs(stock_transaction.qty)) as qty,tools_issue.collected_by,tools_issue.remark,tools_issue.id as dispatch_id,item_master.item_name, item_master.price as itemPrice, department_master.name as dept_name';
    		$queryData['leftJoin']['tools_issue'] = 'tools_issue.id = stock_transaction.ref_id';
    		$queryData['leftJoin']['item_master'] = 'item_master.id = stock_transaction.item_id';
    		$queryData['leftJoin']['department_master'] = 'department_master.id = tools_issue.dept_id';
    		$queryData['where']['stock_transaction.ref_type'] = 37;
    		if(!empty($data['item_type'])){$queryData['where']['item_master.item_type'] = $data['item_type'];}
    		if(!empty($data['category_id'])){$queryData['where_in']['item_master.category_id'] = $data['category_id'];}
    		if(!empty($data['dept_id'])){$queryData['where']['tools_issue.dept_id'] = $data['dept_id'];}
            $queryData['customWhere'][] = "stock_transaction.ref_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
    		$queryData['order_by']['stock_transaction.ref_date'] = 'ASC';
    		$queryData['group_by'][] = 'tools_issue.id';
    		$result = $this->rows($queryData);
        }
		return $result;
    }

    public function getIssueItemPrice($dispatch_id){
        $queryData = array();
		$queryData['tableName'] = $this->jobDispatch;
        $queryData['select'] = 'job_material_dispatch.*,grn_transaction.price as ItemPrice';
		$queryData['join']['grn_transaction'] = 'grn_transaction.item_id = job_material_dispatch.req_item_id';
        $queryData['where']['job_material_dispatch.id'] = $dispatch_id;
        $queryData['order_by']['job_material_dispatch.dispatch_date'] = 'ASC';
        $queryData['limit'] = 1;		
        $result = $this->rows($queryData);  
		return $result;
    }

	/* Stock Register */
	public function getStockReceiptQty($data){
		$queryData = array();
		$queryData['tableName'] = $this->stockTrans;
		$queryData['select'] = 'SUM(stock_transaction.qty) as rqty';
		$queryData['where']['stock_transaction.item_id'] = $data['item_id'];
		$queryData['where']['stock_transaction.trans_type'] = 1;
		//$queryData['where']['stock_transaction.location_id'] = 11;
        $queryData['customWhere'][] = "stock_transaction.ref_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		return $this->row($queryData);
	}

	public function getStockIssuedQty($data){
		$queryData = array();
		$queryData['tableName'] = $this->stockTrans;
		$queryData['select'] = 'SUM(stock_transaction.qty) as iqty';
		$queryData['where']['stock_transaction.item_id'] = $data['item_id'];
		$queryData['where']['stock_transaction.trans_type'] = 2;
		//$queryData['where']['stock_transaction.location_id'] = 11;
        $queryData['customWhere'][] = "stock_transaction.ref_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		return $this->row($queryData);
	}

	/* Consumable */
    public function getConsumable(){
		$data['tableName'] = $this->itemMaster;
		$data['where']['item_master.item_type'] = 2;
		return $this->rows($data);
	}

	/* Raw Material */
    public function getRawMaterialReport(){
		$data['tableName'] = $this->itemMaster;
		$data['where']['item_master.item_type'] = 3;
		return $this->rows($data);
	}

	/* Group wise Item List */
    public function getItemsByGroup($data){
		$data['tableName'] = $this->itemMaster;
		$data['select'] = 'item_master.*,currency.inrrate';
		$data['leftJoin']['party_master'] = 'item_master.party_id=party_master.id';
		$data['leftJoin']['currency'] = 'currency.currency=party_master.currency';
		$data['where_in']['item_master.item_type'] = $data['item_type'];
		return $this->rows($data);
	}

	/* Inventory Monitoring */
	public function getItemGroup(){
		$data['tableName'] = $this->itemGroup;
		return $this->rows($data);
	}
	
	public function getFyearOpningStockQty($data){
		$queryData = array();
		$queryData['tableName'] = $this->stockTrans;
		$queryData['select'] = 'SUM(stock_transaction.qty) as fyosqty';
		$queryData['where']['stock_transaction.item_id'] = $data['item_id'];
		$queryData['where']['stock_transaction.ref_type'] = -1;
        $queryData['where']['stock_transaction.ref_date <= '] = date('Y-m-d', strtotime($this->dates[0]));
		return $this->row($queryData);
	}
	
	public function getOpningStockQty($data){
		//if($data['from_date'] == date('Y-m-d', strtotime($this->dates[0]))){$data['from_date'] = date('Y-m-d', strtotime('+1 day', strtotime($data['from_date'])));} 
		$queryData = array();
		$queryData['tableName'] = $this->stockTrans;
		$queryData['select'] = 'SUM(stock_transaction.qty) as osqty';
		$queryData['where']['stock_transaction.item_id'] = $data['item_id'];
        // $queryData['where']['stock_transaction.ref_date < '] = $data['from_date'];
        $queryData['customWhere'][] = "stock_transaction.ref_date BETWEEN '".date('Y-m-d', strtotime($this->dates[0]))."' AND '".date('Y-m-d', strtotime('-1 day', strtotime($data['from_date'])))."'";
		return $this->row($queryData);
	}

	public function getItemPrice($data){
        $queryData = array();
		$queryData['tableName'] = "grn_transaction";
        $queryData['select'] = 'SUM(grn_transaction.price * grn_transaction.qty) as amount';
		$queryData['join']['grn_master'] = 'grn_master.id = grn_transaction.grn_id';
        $queryData['where']['grn_transaction.item_id'] =  $data['item_id'];
        $queryData['customWhere'][] = "grn_master.grn_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		return $this->row($queryData);  
    }

    /* Stock Statement finish producct */
	public function getFinishProduct(){
		$queryData['tableName'] = $this->itemMaster;
		$queryData['select'] = 'item_master.*,party_master.party_name';
		$queryData['join']['party_master'] = 'party_master.id = item_master.party_id';
		$queryData['where']['item_master.item_type'] = 1;
		return $this->rows($queryData);
	}

	public function getClosingStockQty($data){
		$queryData = array();
		$queryData['tableName'] = $this->stockTrans;
		$queryData['select'] = 'SUM(stock_transaction.qty) as csqty';
		$queryData['where']['stock_transaction.item_id'] = $data['item_id'];
		$queryData['customWhere'][] = "stock_transaction.ref_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		return $this->row($queryData);
	}
	
	public function getStockRegister($type){
		$data['tableName'] = $this->itemMaster;
		$data['where']['item_master.item_type'] = $type;
		return $this->rows($data);
	}
	
	/*Tool Issue Register Data */ 
    public function getToolIssueRegister($postData){		
		$data['tableName'] = $this->tools_issue;
        $data['select'] = "tools_issue.*,stock_transaction.batch_no,SUM(abs(stock_transaction.qty)) as issue_qty,item_master.item_name,item_master.item_code,item_master.price,department_master.name,job_card.job_no,job_card.job_prefix,job_card.product_id";
        $data['leftJoin']['stock_transaction'] = "tools_issue.id = stock_transaction.ref_id AND stock_transaction.ref_type = 37 AND stock_transaction.trans_type = 2";
        $data['leftJoin']['item_master'] = "item_master.id = tools_issue.item_id";
		$data['leftJoin']['department_master'] = 'department_master.id = tools_issue.dept_id';
        $data['leftJoin']['job_card'] = 'tools_issue.job_card_id = job_card.id';

        $data['group_by'][] = 'tools_issue.id';
		$data['order_by']['tools_issue.issue_date'] = 'ASC';

		if(!empty($postData['job_card_id'])){$data['where']['tools_issue.job_card_id'] = $postData['job_card_id'];}
		if(!empty($postData['dept_id'])){$data['where']['tools_issue.dept_id'] = $postData['dept_id'];}
        if(!empty($postData['from_date'])){$data['customWhere'][] = "tools_issue.issue_date BETWEEN '".$postData['from_date']."' AND '".$postData['to_date']."'";}
		
		$result = $this->rows($data);
		return $result;
    }
	
	/* Item Location */
	public function getItemLocation($item_id){
		$queryData['tableName'] = "stock_transaction";
		$queryData['select'] = "SUM(qty) as qty, location_id";
		$queryData['where']['item_id'] = $item_id;
		$queryData['having'][] = 'SUM(qty) > 0';
		$queryData['group_by'][] = 'location_id';
		return $this->row($queryData);
	}

	/**
	 * Created By Mansee @ 09-12-2021
	 */

	 /* Stock Statement Row Material Item */
    public function getRowMaterialScrapQty($data){
		$queryData['tableName'] = $this->stockTrans;
		$queryData['select']  = 'item_master.item_name,item_master.item_code,item_master.price';
		$queryData['select'] .= ',SUM(CASE WHEN stock_transaction.trans_type=1 AND stock_transaction.ref_date BETWEEN "'.$data['from_date'].'" AND "'.$data['to_date'].'" THEN stock_transaction.qty ELSE 0 END) as in_qty';
		$queryData['select'] .= ',SUM(CASE WHEN stock_transaction.trans_type=2 AND stock_transaction.ref_date BETWEEN "'.$data['from_date'].'" AND "'.$data['to_date'].'" THEN stock_transaction.qty ELSE 0 END) as out_qty';
		$queryData['select'] .= ',SUM(CASE WHEN stock_transaction.ref_date <= "'.$data['to_date'].'" THEN stock_transaction.qty ELSE 0 END) as stock_qty';
		$queryData['leftJoin']['item_master'] = 'stock_transaction.item_id = item_master.id';
		$queryData['where']['item_master.item_type'] = 10;
		if($data['scrap_group'] != 'ALL')
		{
			$queryData['where']['stock_transaction.item_id'] = $data['scrap_group'];
		}
		//$queryData['customWhere'][] = "stock_transaction.ref_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['where']['stock_transaction.ref_date <= '] = $data['to_date'];
		$queryData['group_by'][] = "stock_transaction.item_id";
		$result= $this->rows($queryData);
		//$this->printQuery();
		return $result;
	}
	
	public function getJobcardList(){
        $data['tableName'] = $this->jobCard;
        $data['select'] = 'job_card.*,item_master.item_code,item_master.item_name';
        $data['join']['item_master'] = 'item_master.id = job_card.product_id';
		$data['where']['job_card.job_date >= '] = $this->startYearDate;
		$data['where']['job_card.job_date <= '] = $this->endYearDate;
        return $this->rows($data); 
    }
    
    /* Avruti @21-04-2022 */
	public function getStoreLocationList(){
        $data['tableName'] = $this->locationMaster;
        return  $this->rows($data);
    }

	public function getStoreWiseStockReport($data)
    {
        $data['tableName'] = $this->stockTrans;
        $data['select'] = 'stock_transaction.*,item_master.item_name,item_master.item_code,SUM(stock_transaction.qty) as qty';
        $data['leftJoin']['item_master'] = "item_master.id = stock_transaction.item_id";
		$data['where']['stock_transaction.location_id'] = $data['location_id'];
		if(!empty($data['party_id']))
		    $data['where']['item_master.party_id'] = $data['party_id'];
		$data['group_by'][] = 'stock_transaction.item_id,stock_transaction.batch_no';
		$data['order_by']['item_master.item_code'] = 'ASC';
		return  $this->rows($data);
    }
    
	public function getInventoryMonitor($postData){
        $data['tableName'] = 'item_master';
		$data['select'] = 'item_master.id, item_master.item_name, item_master.item_code, item_master.item_type, item_master.price, item_master.rev_no, item_master.drawing_no,item_master.min_qty,item_master.max_qty, currency.inrrate,party_master.party_name';
		$data['select'] .= ',SUM(CASE WHEN stock_transaction.ref_date >="'.$postData['from_date'].'" AND stock_transaction.ref_date<="'.$postData['to_date'].'" AND stock_transaction.trans_type = 1 THEN stock_transaction.qty ELSE 0 END) AS rqty';
		$data['select'] .= ',SUM(CASE WHEN stock_transaction.ref_date >="'.$postData['from_date'].'" AND stock_transaction.ref_date<="'.$postData['to_date'].'" AND stock_transaction.trans_type = 2 THEN stock_transaction.qty ELSE 0 END) AS iqty';
		$data['leftJoin']['stock_transaction'] = 'stock_transaction.item_id=item_master.id';
		$data['leftJoin']['party_master'] = 'item_master.party_id=party_master.id';
		$data['leftJoin']['currency'] = 'currency.currency=party_master.currency';
		$data['select'] .= ',SUM(CASE WHEN stock_transaction.ref_date <"'.$postData['from_date'].'" THEN stock_transaction.qty ELSE 0 END) AS opening_qty';
		//$data['where_in']['item_master.item_type'] = $postData['item_type'];
		$data['where_in']['item_master.id'] = $postData['item_id'];
		$data['where']['stock_transaction.stock_effect'] = 1;
		$data['where']['stock_transaction.is_delete'] = 0;
		$data['order_by']['item_master.item_code'] = 'ASC';
		$data['group_by'][] = 'stock_transaction.item_id';
		return $this->rows($data);
    }
    
    public function getMisplacedItemList(){
		$queryData['tableName'] = $this->stockTrans;
        $queryData['select'] = 'stock_transaction.*,item_master.item_code,item_master.item_name';
        $queryData['leftJoin']['item_master'] = "item_master.id = stock_transaction.item_id";
		$queryData['where']['stock_transaction.location_id'] = $this->MIS_PLC_STORE->id;
		$queryData['where']['stock_transaction.ref_type'] = 9;
		$queryData['where']['stock_transaction.trans_type'] = 1;
        $queryData['group_by'][] = 'stock_transaction.item_id';
		$result = $this->rows($queryData);
		return $result;
	}
	
	public function getMisplacedItemHistory($data){
		
		$queryData['tableName'] = $this->stockTrans;
        $queryData['select'] = 'stock_transaction.*,item_master.item_code,item_master.item_name,employee_master.emp_name';
        $queryData['leftJoin']['item_master'] = "item_master.id = stock_transaction.item_id";
        $queryData['leftJoin']['employee_master'] = "employee_master.id = stock_transaction.created_by";
        if(!empty($data['item_id'])){$queryData['where']['stock_transaction.item_id'] = $data['item_id'];}
		$queryData['where']['stock_transaction.location_id'] = $this->MIS_PLC_STORE->id;
		$queryData['where']['stock_transaction.ref_type'] = 9;
		$queryData['where']['stock_transaction.trans_type'] = 1;
        $queryData['customWhere'][] = "stock_transaction.ref_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
        $queryData['order_by']['stock_transaction.ref_date'] = 'ASC';
        $queryData['order_by']['stock_transaction.id'] = 'ASC';
		$result = $this->rows($queryData);
		return $result;
	}
	
	public function getInProcessStockData($process_id,$party_id = ""){
		$queryData['tableName'] = "production_approval";
        $queryData['select'] = 'production_approval.in_qty , production_approval.out_qty , SUM(production_log.rej_qty) as rej_qty,item_master.item_code,item_master.item_name,job_card.job_no,job_card.job_prefix';
        $queryData['leftJoin']['production_log'] = "production_approval.in_process_id = production_log.process_id AND production_approval.job_card_id = production_log.job_card_id AND production_log.is_delete = 0";
        $queryData['leftJoin']['job_card'] = "job_card.id = production_approval.job_card_id";
        $queryData['leftJoin']['item_master'] = "item_master.id = production_approval.product_id";
		$queryData['where']['production_approval.in_process_id'] = $process_id;
		if(!empty($party_id))
		    $queryData['where']['item_master.party_id'] = $party_id;
		$queryData['where']['job_card.job_date >= '] = $this->startYearDate;
        $queryData['where']['job_card.job_date <= '] = $this->endYearDate;
		$queryData['group_by'][] = 'production_approval.job_card_id';
		$queryData['order_by']['job_card.job_no'] = 'DESC';
		$result = $this->rows($queryData);
		return $result;
	}
	
	public function getInProcessStockVendorWise($vendor_id,$party_id = ""){
		$queryData['tableName'] = "vendor_production_trans";
        $queryData['select'] = 'SUM(vendor_production_trans.out_qty - vendor_production_trans.in_qty) as qty,SUM((vendor_production_trans.out_qty - vendor_production_trans.in_qty)*vendor_production_trans.w_pcs) as qty_kg ,item_master.item_code,item_master.item_name,job_card.job_no,job_card.job_prefix';
        $queryData['leftJoin']['job_card'] = "job_card.id = vendor_production_trans.job_card_id";
        $queryData['leftJoin']['item_master'] = "item_master.id = vendor_production_trans.product_id";
		$queryData['where']['vendor_production_trans.vendor_id'] = $vendor_id;
		if(!empty($party_id))
		    $queryData['where']['item_master.party_id'] = $party_id;
		$queryData['where']['job_card.job_date >= '] = $this->startYearDate;
        $queryData['where']['job_card.job_date <= '] = $this->endYearDate;
		$queryData['group_by'][] = 'vendor_production_trans.job_card_id';
		$queryData['order_by']['job_card.job_no'] = 'DESC';
		$result = $this->rows($queryData);
		return $result;
	}
	
	public function getLastGrnPrice($data){
        $queryData = array();
		$queryData['tableName'] = 'grn_transaction';
		$queryData['select'] = 'grn_transaction.price';
		$queryData['where']['grn_transaction.item_id'] = $data['item_id'];
		$queryData['order_by']['grn_transaction.id'] = 'DESC';
		$queryData['limit']=1;
		$result = $this->row($queryData);
		return $result;
    }
	
}
?>