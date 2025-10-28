<?php 
class QualityReportModel extends MasterModel
{
    private $stockTransaction = "stock_transaction";
    private $grnTrans = "grn_transaction";
	private $jobCard = "job_card";
    private $jobMaterialDispatch = "job_material_dispatch";
    private $jobRejection = "job_rejection";
    private $productKit = "item_kit";
    private $itemMaster = "item_master";
    private $processInspection = "process_inspection";
    private $rej_rw_management = "rej_rw_management";

    public function getBatchNoListForHistory(){
        $data['tableName'] = $this->stockTransaction;
        $data['select'] = 'batch_no';
        $data['order_by']['batch_no'] = 'ASC';
        $data['group_by'][] = 'batch_no';
        $result = $this->rows($data);
        return $result;
    }

    public function getBatchHistory($data){
        $queryData['tableName'] = $this->stockTransaction;
		$queryData['select'] = "stock_transaction.*,item_master.item_name";
		$queryData['join']['item_master'] = "stock_transaction.item_id = item_master.id";
        $queryData['where']['stock_transaction.batch_no'] = $data['batch_no'];
        $queryData['order_by']['ref_date'] = 'ASC';
		$result = $this->rows($queryData);
	   	return $result;
    }
	
    public function getBatchList(){
        $data['tableName'] = $this->stockTransaction;
		$data['select'] = "stock_transaction.batch_no,stock_transaction.item_id,item_master.item_name,item_master.item_type";
		$data['join']['item_master'] = "stock_transaction.item_id = item_master.id";
        $data['where']['item_master.item_type'] = 3;
        $data['group_by'][] = 'batch_no';
        $data['order_by']['batch_no'] = 'ASC';
        return $this->rows($data); 
    }
    
    public function getBatchListByItem($item_id){
        $data['tableName'] = $this->stockTransaction;
		$data['select'] = "stock_transaction.batch_no,stock_transaction.item_id,item_master.item_name,item_master.item_type";
		$data['join']['item_master'] = "stock_transaction.item_id = item_master.id";
        $data['where']['stock_transaction.item_id'] = $item_id;
        $data['where']['item_master.item_type'] = 3;
        $data['group_by'][] = 'batch_no';
        $data['order_by']['batch_no'] = 'ASC';
        return $this->rows($data); 
    }
	
    public function getBatchItemList($batch_no){
        $data['tableName'] = $this->stockTransaction;
		$data['select'] = "stock_transaction.batch_no,stock_transaction.item_id,item_master.item_name,item_master.item_type,item_master.item_code";
		$data['join']['item_master'] = "stock_transaction.item_id = item_master.id";
        $data['where']['stock_transaction.batch_no'] = $batch_no;
        $data['group_by'][] = 'stock_transaction.item_id';
        return $this->rows($data); 
    }

    public function getBatchTracability($data){
        $queryData['tableName'] = $this->stockTransaction;
		$queryData['select'] = "stock_transaction.*,item_master.item_name";
		$queryData['join']['item_master'] = "stock_transaction.item_id = item_master.id";
        $queryData['where']['stock_transaction.batch_no'] = $data['batch_no'];
        if(!empty($data['item_id'])){$queryData['where']['stock_transaction.item_id'] = $data['item_id'];}
        $queryData['order_by']['ref_date'] = 'ASC';
		$result = $this->rows($queryData);
	   	return $result;
    }

    public function getMIfgName($ref_id){
        $queryData['tableName'] = $this->stockTransaction;
		$queryData['select'] = "item_master.item_name,item_master.item_code,job_card.job_prefix,job_card.job_no, job_card.id as job_id";
		$queryData['join']['job_material_dispatch'] = "stock_transaction.ref_id = job_material_dispatch.id";
        $queryData['join']['job_card'] = "job_card.id = job_material_dispatch.job_card_id";
		$queryData['join']['item_master'] = "item_master.id = job_card.product_id";
        $queryData['where']['stock_transaction.ref_id'] = $ref_id;
		$result = $this->row($queryData);
	   	return $result; 
    }

    public function getReturnfgName($ref_id){
        $queryData['tableName'] = $this->stockTransaction;
		$queryData['select'] = "item_master.item_name,item_master.item_code,job_card.job_prefix,job_card.job_no";
		$queryData['join']['job_return_material'] = "stock_transaction.ref_id = job_return_material.id";
        $queryData['join']['job_card'] = "job_card.id = job_return_material.job_card_id";
		$queryData['join']['item_master'] = "item_master.id = job_card.product_id";
        $queryData['where']['stock_transaction.ref_id'] = $ref_id;
		$result = $this->row($queryData);
	   	return $result; 
    }

    public function getSupplierRatingItems($data){
        $queryData['tableName'] = $this->grnTrans;
        $queryData['select'] = "item_master.id,item_master.item_name";
		$queryData['join']['grn_master'] = "grn_master.id = grn_transaction.grn_id";
        $queryData['join']['item_master'] = "item_master.id = grn_transaction.item_id";
        // $queryData['join']['purchase_order_trans'] = "purchase_order_trans.id = grn_transaction.po_trans_id";
        $queryData['where']['grn_master.party_id'] = $data['party_id'];
        $queryData['customWhere'][] = "grn_master.grn_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
        $queryData['where']['grn_master.order_id != '] = 0;
        $queryData['where']['grn_transaction.po_trans_id != '] = 0;
        $queryData['where']['grn_master.type'] = 1;
        $queryData['group_by'][] = 'item_master.id';
		$result = $this->rows($queryData);
	   	return $result;
    }

    public function getSupplierRating($data){
        $queryData['tableName'] = $this->grnTrans;
        $queryData['select'] = "grn_transaction.*, grn_master.order_id, grn_master.grn_prefix, grn_master.grn_no, grn_master.grn_date, grn_master.remark, purchase_order_trans.delivery_date";
		$queryData['join']['grn_master'] = "grn_master.id = grn_transaction.grn_id";
        $queryData['join']['purchase_order_trans'] = "purchase_order_trans.id = grn_transaction.po_trans_id";
        $queryData['where']['grn_transaction.item_id'] = $data['item_id'];
        $queryData['where']['grn_master.party_id'] = $data['party_id'];
        $queryData['customWhere'][] = "grn_master.grn_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
        $queryData['where']['grn_master.order_id != '] = 0;
        $queryData['where']['grn_transaction.po_trans_id != '] = 0;
        $queryData['where']['grn_master.type'] = 1;
		$result = $this->rows($queryData);
	   	return $result;
    }
   
	public function getInspectedMaterialGBJ($data){
        $queryData['tableName'] = $this->jobMaterialDispatch;
        $queryData['select'] = "job_material_dispatch.job_card_id,job_material_dispatch.dispatch_qty, job_card.product_id";
        $queryData['join']['job_card'] = "job_material_dispatch.job_card_id = job_card.id";
        $queryData['where']['job_material_dispatch.dispatch_item_id'] = $data['item_id'];
        // $queryData['where']['job_card.party_id'] = $data['party_id'];
        $queryData['customWhere'][] = "job_material_dispatch.dispatch_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
        $queryData['group_by'][] = 'job_material_dispatch.job_card_id';
		$result = $this->rows($queryData);
		
		$qtyData = New StdClass;
		$qtyData->rQty = 0; $qtyData->aQty = 0; $qtyData->udQty = 0;$qtyData->insQty = 0;
		if(!empty($result)):
			foreach($result as $row):
				
				$queryData = Array();
				$queryData['tableName'] = $this->jobRejection;
				$queryData['select'] = 'SUM(qty) as rejQty,SUM(pending_qty) as pendingRejQty';
				$queryData['where']['job_card_id'] = $row->job_card_id;
				$queryData['where']['rejection_type_id'] = -1;
				//$rejectionData = $this->row($queryData);
				
				$queryData = Array();
				$queryData['tableName'] = $this->productKit;
				$queryData['select'] = "item_kit.*";
				$queryData['where']['ref_item_id'] = $data['item_id'];
				$queryData['where']['item_id'] = $row->product_id;
				$kitData = $this->row($queryData);
				
				if(!empty($rejectionData) and !empty($kitData)):
					$qtyData->rQty += ($rejectionData->rejQty * $kitData->qty);
				endif;
				
				
				$qtyData->insQty += $row->dispatch_qty;
			endforeach;
		endif;
		$qtyData->aQty = $qtyData->insQty - $qtyData->rQty;
		
	   	return $qtyData;
    }

    public function getMeasuringDevice($type){
        $data['tableName'] = $this->itemMaster;
        $data['select'] = "item_master.*,item_category.category_name";
        $data['leftJoin']['item_category'] = "item_category.id = item_master.category_id";
		$data['where']['item_master.item_type'] = $type;
		return $this->rows($data);
    }
    
    /* NC Report */
    public function getNCReportData($data)
    {
        $data['tableName'] = "production_log";
        $data['select'] = "production_log.*,process_master.process_name,item_master.item_name,item_master.item_code,item_master.price,employee_master.emp_name,job_card.job_no,job_card.job_prefix,super.emp_name as supervisor,inspection_type.inspection_type as inspection_type_name";
        $data['leftJoin']['job_card'] = "job_card.id = production_log.job_card_id";
        $data['leftJoin']['item_master'] = "item_master.id = job_card.product_id";
        $data['leftJoin']['process_master'] = "process_master.id = production_log.process_id";
        $data['leftJoin']['employee_master'] = "employee_master.id = production_log.operator_id";
        $data['leftJoin']['employee_master as super'] = "super.id = production_log.created_by";
        $data['leftJoin']['inspection_type'] = "inspection_type.id = production_log.inspection_type";
        $data['where']['production_log.prod_type'] = 2;
        $data['customWhere'][] = "DATE(production_log.log_date) BETWEEN '" . $data['from_date'] . "' AND '" . $data['to_date'] . "'";
        if(!empty($data['job_id'])){ $data['where']['job_card.id'] = $data['job_id']; }
        return $this->rows($data);
    }
    
    //Vendor Gauge Report Data //*Created By Meghavi*//
    public function getVendorGaugeData($data){
        $queryData = array();
        $queryData['tableName'] = "in_out_challan_trans";
        $queryData['select'] = 'in_out_challan_trans.*,in_out_challan.party_name,in_out_challan.challan_date,in_out_challan.challan_no,in_out_challan.challan_prefix';
        $queryData['leftJoin']['in_out_challan'] = "in_out_challan.id = in_out_challan_trans.in_out_ch_id";
		$queryData['where']['in_out_challan_trans.is_returnable'] = 1;
        $queryData['where']['in_out_challan.party_id'] = $data['party_id'];
        $queryData['customWhere'][] = " in_out_challan.challan_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
        $queryData['order_by'][' in_out_challan_trans.return_date'] = 'DESC';
		$result = $this->rows($queryData);
		return $result;
    }
    
    //Raw Material Tesing  Report Data //*Created By Meghavi*//
    public function getRmTestingRegister($data){
        $queryData = array();
        $queryData['tableName'] = "grn_transaction";
        $queryData['select'] = 'grn_transaction.*,grn_master.party_id,grn_master.grn_no,grn_master.grn_prefix,grn_master.grn_date,party_master.party_name,item_master.item_name,item_master.material_grade,unit_master.unit_name,grn_test_report.test_report_no,grn_test_report.test_remark,grn_test_report.test_result,grn_test_report.inspector_name';
        $queryData['leftJoin']['grn_master'] = "grn_master.id = grn_transaction.grn_id";
        $queryData['leftJoin']['unit_master'] = "unit_master.id = grn_transaction.unit_id";
        $queryData['leftJoin']['item_master'] = "item_master.id = grn_transaction.item_id";
        $queryData['leftJoin']['party_master'] = "party_master.id = grn_master.party_id";
		$queryData['leftJoin']['grn_test_report'] = "grn_transaction.id = grn_test_report.grn_trans_id";
        $queryData['where']['grn_transaction.item_type'] = 3;
		if($data['trans_status'] != 'ALL'){ 
		    if($data['trans_status'] == 0){
		        $queryData['customWhere'][] = "(grn_transaction.name_of_agency IS NULL OR grn_transaction.name_of_agency = '')"; 
		    }
		    if($data['trans_status'] == 1){
		        $queryData['customWhere'][] = "(grn_transaction.name_of_agency IS NOT NULL OR grn_transaction.name_of_agency != '')"; 
		    }
		}
        $queryData['customWhere'][] = "grn_master.grn_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$result = $this->rows($queryData);
		return $result;
    }

	public function getJobCardProcessList($job_card_id){
        $data['tableName'] = 'process_master';
		$data['select'] = 'process_master.*,job_card.process';
        $data['leftJoin']['job_card'] = "find_in_set(process_master.id,job_card.process) > 0";
		$data['where']['job_card.id'] = $job_card_id;
		$data['group_by'][] = "process_master.id";
        return $this->rows($data);
    }
    
    public function getLineInspectionForReport($data){
        $data['tableName'] = $this->processInspection;
		$data['where']['product_id'] = $data['item_id'];
		$data['where']['process_id'] = $data['process_id'];
		$data['where']['insp_date'] = $data['to_date'];
        return $this->rows($data);
    }
    
    public function getRejectionMonitoring($data)
	{
		$queryData = array();
		$queryData['tableName'] = "rej_rw_management";
		$queryData['select'] = 'rej_rw_management.*,production_log.log_date,production_log.machine_id,item_master.item_code, item_master.price,shift_master.shift_name,employee_master.emp_name,party_master.currency,process_master.process_name,job_used_material.batch_no';
		$queryData['leftJoin']['production_log'] = 'production_log.id = rej_rw_management.log_id';
		$queryData['leftJoin']['process_master'] = 'production_log.process_id = process_master.id';
		$queryData['leftJoin']['job_card'] = 'production_log.job_card_id = job_card.id';
		$queryData['leftJoin']['party_master'] = 'party_master.id = job_card.party_id';
		$queryData['leftJoin']['item_master'] = 'item_master.id = job_card.product_id';
		$queryData['leftJoin']['employee_master'] = 'employee_master.id = production_log.operator_id';
		$queryData['leftJoin']['shift_master'] = 'shift_master.id = production_log.shift_id';
		$queryData['leftJoin']['job_used_material'] = 'job_used_material.job_card_id = job_card.id';
		$queryData['where_in']['rej_rw_management.manag_type'] = 1;
	
		$queryData['customWhere'][] = "production_log.log_date BETWEEN '" . $data['from_date'] . "' AND '" . $data['to_date'] . "'";
		if (!empty($data['item_id'])) {
			$queryData['where_in']['job_card.product_id'] = $data['item_id'];
		}
		
		return $this->rows($queryData);;
	}
	
	public function getRejRwProdLogV2($data)
	{
		$queryData = array();
		$queryData['tableName'] = "production_log";
		$queryData['select'] = 'production_log.id,production_log.log_date,production_log.rej_qty,production_log.rw_qty,machine.item_code as machine_code,machine.item_name as machine_name,item_master.item_code, item_master.price,shift_master.shift_name,employee_master.emp_name,party_master.currency,process_master.process_name,job_card.job_no,job_card.job_prefix,SUM(rej_rw_management.qty) as rejrw_qty,rej_rw_management.reason_name,rej_rw_management.belongs_to_name,rej_rw_management.belongs_to_name,rej_rw_management.remark,rej_rw_management.vendor_name,rej_rw_management.manag_type';
		$queryData['leftJoin']['process_master'] = 'production_log.process_id = process_master.id';
		$queryData['leftJoin']['job_card'] = 'production_log.job_card_id = job_card.id';
		$queryData['leftJoin']['item_master'] = 'item_master.id = job_card.product_id';
		$queryData['leftJoin']['party_master'] = 'party_master.id = item_master.party_id';
		$queryData['leftJoin']['item_master machine'] = 'machine.id = production_log.machine_id';
		$queryData['leftJoin']['employee_master'] = 'employee_master.id = production_log.operator_id';
		$queryData['leftJoin']['shift_master'] = 'shift_master.id = production_log.shift_id';
		$queryData['leftJoin']['rej_rw_management'] = 'rej_rw_management.log_id = production_log.id AND rej_rw_management.is_delete = 0';
	
		$queryData['customWhere'][] = "production_log.log_date BETWEEN '" . $data['from_date'] . "' AND '" . $data['to_date'] . "'";
		
		if (!empty($data['item_id'])) { $queryData['where_in']['job_card.product_id'] = $data['item_id']; }
		if (!empty($data['job_id'])) { $queryData['where']['job_card.id'] = $data['job_id']; }
		
		$queryData['where_in']['rej_rw_management.manag_type'] = '1';
		$queryData['where']['production_log.prod_type !='] = '5';
		$queryData['group_by'][] = 'rej_rw_management.log_id,rej_rw_management.id';
		$queryData['having'][] = 'SUM(rej_rw_management.qty) > 0';
		
		return $this->rows($queryData);
	}
	
	public function getRejRwProdLog($data)
	{
		$queryData = array();
		$queryData['tableName'] = "production_log";
		$queryData['select'] = 'production_log.id,production_log.log_date,production_log.rej_qty,production_log.rw_qty,machine.item_code as machine_code,machine.item_name as machine_name,item_master.item_code, item_master.price,shift_master.shift_name,employee_master.emp_name,party_master.currency,process_master.process_name,job_used_material.batch_no,job_card.job_no,job_card.job_prefix';
		$queryData['leftJoin']['process_master'] = 'production_log.process_id = process_master.id';
		$queryData['leftJoin']['job_card'] = 'production_log.job_card_id = job_card.id';
		$queryData['leftJoin']['item_master'] = 'item_master.id = job_card.product_id';
		$queryData['leftJoin']['party_master'] = 'party_master.id = item_master.party_id';
		$queryData['leftJoin']['item_master machine'] = 'machine.id = production_log.machine_id';
		$queryData['leftJoin']['employee_master'] = 'employee_master.id = production_log.operator_id';
		$queryData['leftJoin']['shift_master'] = 'shift_master.id = production_log.shift_id';
		$queryData['leftJoin']['job_used_material'] = 'job_used_material.job_card_id = job_card.id';
		$queryData['customWhere'][] = '((production_log.rej_qty > 0) OR (production_log.rw_qty > 0) )';
	
		$queryData['customWhere'][] = "production_log.log_date BETWEEN '" . $data['from_date'] . "' AND '" . $data['to_date'] . "'";
		
		if (!empty($data['item_id'])) { $queryData['where_in']['job_card.product_id'] = $data['item_id']; }
		if (!empty($data['rtype']) AND $data['rtype']==1) { $queryData['where']['production_log.rej_qty > '] = 0; }
		
		return $this->rows($queryData);
	}
	
	public function getRejRwData($data)
	{
		$queryData = array();
		$queryData['tableName'] = "rej_rw_management";
		$queryData['select'] = "rej_rw_management.*,GROUP_CONCAT(department_master.name) AS dept_name";
		$queryData['leftJoin']['process_master'] = "process_master.id = rej_rw_management.belongs_to";
		$queryData['leftJoin']['department_master'] = "FIND_IN_SET(department_master.id, process_master.dept_id) > 0";
		$queryData['where']['rej_rw_management.log_id'] = $data['log_id'];
		$queryData['group_by'][] = "rej_rw_management.id";
		return $this->rows($queryData);
	}

    public function getRejectionSummary($data){
        $queryData = array();
        $queryData['tableName'] = $this->rej_rw_management;
        $queryData['select'] = "rej_rw_management.job_card_id,job_card.product_id,item_master.item_code,pl.production_qty,SUM(rej_rw_management.qty) as rej_qty";

        $queryData['leftJoin']['job_card'] = "rej_rw_management.job_card_id = job_card.id";
        $queryData['leftJoin']['item_master'] = "item_master.id = job_card.product_id";
        $queryData['leftJoin']['production_log'] = "production_log.id = rej_rw_management.log_id";
        $queryData['leftJoin']['(SELECT SUM(production_log.production_qty) as production_qty,production_log.job_card_id, job_card.product_id FROM production_log LEFT JOIN job_card ON production_log.job_card_id = job_card.id WHERE DATE_FORMAT(production_log.log_date,"%Y-%m-%d") >= "'.$data['from_date'].'" AND DATE_FORMAT(production_log.log_date,"%Y-%m-%d") <= "'.$data['to_date'].'" AND production_log.is_delete = 0 AND production_log.prod_type != 5 GROUP BY job_card.product_id ) as pl'] = "job_card.product_id = pl.product_id";

        $queryData['where']['DATE_FORMAT(production_log.log_date,"%Y-%m-%d") >= '] = $data['from_date'];
        $queryData['where']['DATE_FORMAT(production_log.log_date,"%Y-%m-%d") <= '] = $data['to_date'];
        $queryData['where']['rej_rw_management.manag_type'] = 1;
        $queryData['where']['rej_rw_management.reason >'] = 0;
        if(!empty($data['item_id']))
            $queryData['where']['job_card.product_id'] = $data['item_id'];
        if(!empty($data['process_id']))
            $queryData['where']['rej_rw_management.belongs_to'] = $data['process_id'];

        $queryData['having'][] = "SUM(rej_rw_management.qty) > 0";
        $queryData['group_by'][] = "job_card.product_id";
        $result = $this->rows($queryData);
        //$this->printQuery();
        return $result;
    }

    public function getRejectionTransaction($data){
        $queryData = array();
        $queryData['tableName'] = $this->rej_rw_management;
        $queryData['select'] = "rej_rw_management.*";

        $queryData['leftJoin']['production_log'] = "production_log.id = rej_rw_management.log_id AND production_log.prod_type != 5";
        $queryData['leftJoin']['job_card'] = "rej_rw_management.job_card_id = job_card.id";

        $queryData['where']['DATE_FORMAT(production_log.log_date,"%Y-%m-%d") >= '] = $data['from_date'];
        $queryData['where']['DATE_FORMAT(production_log.log_date,"%Y-%m-%d") <= '] = $data['to_date'];
        //$queryData['where']['rej_rw_management.job_card_id'] = $data['job_card_id'];
        $queryData['where']['rej_rw_management.manag_type'] = 1;
        $queryData['where']['rej_rw_management.reason >'] = 0;
        if(!empty($data['item_id']))
            $queryData['where']['job_card.product_id'] = $data['item_id'];
        if(!empty($data['process_id']))
            $queryData['where']['rej_rw_management.belongs_to'] = $data['process_id'];

        $result = $this->rows($queryData);
        return $result;
    }
    
    public function getVendorChallanData($data){
        $queryData = array();
		$queryData['tableName'] = 'vendor_challan_trans';
		$queryData['select'] ='vendor_challan_trans.*,vendor_challan.vendor_id,vendor_challan.trans_date,item_master.item_name,item_master.item_code,prodLog.rej_qty,
		(SELECT 
		    SUM(vct_2.`qty`) as sum_qty 
		FROM `vendor_challan_trans` AS vct_2 
		LEFT JOIN `vendor_challan` ON `vendor_challan`.`id` = vct_2.`challan_id` 
		WHERE vct_2.`job_card_id` = `vendor_challan_trans`.`job_card_id` AND 
		    `vendor_challan`.`vendor_id` = '.$data['party_id'].' AND
		    `vendor_challan`.`trans_date` BETWEEN "'.$data['from_date'].'" AND "'.$data['to_date'].'" AND 
		    vct_2.`type` = 1 AND 
		    vct_2.`is_delete` = 0 
		GROUP BY vct_2.`process_id` LIMIT 1) AS in_qty,
		SUM( 
			CASE WHEN vendor_challan_trans.in_challan_date <=(SELECT DATE_ADD(vc.trans_date,INTERVAL (job_work_order.production_days) DAY) FROM vendor_challan vc WHERE vc.id=vendor_challan.id) 
			THEN vendor_challan_trans.qty END 
		) as in_time_qty,
		SUM( 
			CASE WHEN vendor_challan_trans.in_challan_date > (SELECT DATE_ADD(vc.trans_date,INTERVAL (job_work_order.production_days) DAY) FROM vendor_challan vc WHERE vc.id=vendor_challan.id) AND vendor_challan_trans.in_challan_date <=(SELECT DATE_ADD(vendor_challan.trans_date,INTERVAL (job_work_order.production_days +7) DAY) FROM vendor_challan vc WHERE vc.id=vendor_challan.id)
			THEN vendor_challan_trans.qty END 
		) as lt_qty,
		SUM( 
			CASE WHEN vendor_challan_trans.in_challan_date > (SELECT DATE_ADD(vc.trans_date,INTERVAL (job_work_order.production_days +7) DAY) FROM vendor_challan vc WHERE vc.id=vendor_challan.id) 
			THEN vendor_challan_trans.qty END 
		) as lt_beyond_qty';

        $queryData['leftJoin']['vendor_challan'] = "vendor_challan_trans.challan_id = vendor_challan.id";
        $queryData['leftJoin']['item_master'] = "item_master.id = vendor_challan_trans.item_id";
		$queryData['leftJoin']['(SELECT SUM(rej_qty) as rej_qty,id,production_qty FROM production_log WHERE is_delete = 0 AND prod_type = 3) as prodLog'] = "prodLog.id = vendor_challan_trans.ref_id";
        $queryData['leftJoin']['job_work_order'] = "job_work_order.id = vendor_challan_trans.jobwork_order_id";
        $queryData['where']['vendor_challan.vendor_id'] = $data['party_id'];
        $queryData['where']['vendor_challan_trans.type'] = 2;
        $queryData['customWhere'][] = "vendor_challan.trans_date BETWEEN '" . $data['from_date'] . "' AND '" . $data['to_date'] . "'";
        $queryData['group_by'][]='vendor_challan_trans.item_id,job_card_id';
		return $this->rows($queryData);
    }
	

    public function getVendorRejData($data){
        $queryData['tableName'] = 'rej_rw_management';
        $queryData['select'] = "SUM(qty) as rej_qty";
        $queryData['where']['vendor_id'] = $data['vendor_id'];
        $queryData['where']['job_card_id'] = $data['job_card_id'];
        $queryData['where']['manag_type'] = 1;
        $queryData['where']['rej_type'] =0;
        return $this->row($queryData);
    }

    public function getInspectionForPrint($postData){
        $data['tableName'] = 'ic_inspection';
        $data['select'] = "ic_inspection.*,item_master.item_code,process_master.process_name,employee_master.emp_name,shift_master.shift_name,job_card.job_prefix,job_card.job_no,mc.item_name as machine_name,mc.item_code as machine_code";
        $data['leftJoin']['item_master'] = "item_master.id = ic_inspection.item_id";
        $data['leftJoin']['item_master as mc'] = "mc.id = ic_inspection.party_id";
        $data['leftJoin']['process_master'] = "process_master.id = ic_inspection.grn_trans_id";
        $data['leftJoin']['employee_master'] = "employee_master.id = ic_inspection.created_by";
        $data['leftJoin']['shift_master'] = "shift_master.id = employee_master.shift_id";
        $data['leftJoin']['job_card'] = "job_card.id = ic_inspection.grn_id";
        if(!empty($postData['job_card_id'])) { $data['where']['ic_inspection.grn_id'] = $postData['job_card_id']; }
        if(!empty($postData['process_id'])) { $data['where']['ic_inspection.grn_trans_id'] = $postData['process_id']; }
        if(!empty($postData['item_id'])) { $data['where']['ic_inspection.item_id'] = $postData['item_id']; }
        if(!empty($postData['to_date'])) { $data['where']['ic_inspection.trans_date'] = $postData['to_date']; }

        if(!empty($postData['single_row'])){
            return $this->rows($data);
        }else{
            return $this->row($data);
        }
    }
}
?>