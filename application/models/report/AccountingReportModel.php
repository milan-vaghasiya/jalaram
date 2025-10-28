<?php
class AccountingReportModel extends MasterModel{
    private $stockTransaction = "stock_transaction";

    public function getLedgerSummary($fromDate="",$toDate=""){
        $startDate = (!empty($fromDate))?$fromDate:$this->startYearDate;
        $endDate = (!empty($toDate))?$toDate:$this->endYearDate;
        $startDate = date("Y-m-d",strtotime($startDate));
        $endDate = date("Y-m-d",strtotime($endDate));
        
        $ledgerSummary = $this->db->query("SELECT lb.id as id, am.party_code as account_code, am.party_name as account_name, CASE WHEN lb.op_balance > 0 THEN CONCAT(abs(lb.op_balance),' CR.') WHEN lb.op_balance < 0 THEN CONCAT(abs(lb.op_balance),' DR.') ELSE lb.op_balance END op_balance,am.group_name, lb.cr_balance, lb.dr_balance, CASE WHEN lb.cl_balance > 0 THEN CONCAT(abs(lb.cl_balance),' CR.') WHEN lb.cl_balance < 0 THEN CONCAT(abs(lb.cl_balance),' DR.') ELSE lb.cl_balance END as cl_balance 
        FROM (
            SELECT am.id, ((am.opening_balance) + SUM( CASE WHEN (tl.trans_date < '".$startDate."') THEN (tl.amount * tl.p_or_m) ELSE 0 END )) as op_balance, 
            SUM( CASE WHEN tl.trans_date >= '".$startDate."' AND tl.trans_date <= '".$endDate."' THEN CASE WHEN tl.c_or_d = 'DR' THEN tl.amount ELSE 0 END ELSE 0 END) as dr_balance,
            SUM( CASE WHEN tl.trans_date >= '".$startDate."' AND tl.trans_date <= '".$endDate."' THEN CASE WHEN tl.c_or_d = 'CR' THEN tl.amount ELSE 0 END ELSE 0 END) as cr_balance,
            ((am.opening_balance) + SUM( CASE WHEN (tl.trans_date <= '".$endDate."') THEN (tl.amount * tl.p_or_m) ELSE 0 END )) as cl_balance 
            FROM party_master as am 
            LEFT JOIN trans_ledger as tl ON am.id = tl.vou_acc_id  AND tl.is_delete = 0
            WHERE am.is_delete = 0 GROUP BY am.id, am.opening_balance) as lb 
        LEFT JOIN party_master as am ON lb.id = am.id WHERE am.is_delete = 0
        ORDER BY am.party_name")->result();
        return $ledgerSummary;

        /* $queryData = array();
        $queryData['tableName'] = "(SELECT am.id, ((am.opening_balance) + SUM( CASE WHEN tl.trans_date < '".$startDate."' THEN (tl.amount * tl.p_or_m) ELSE 0 END )) as op_balance, SUM( CASE WHEN tl.trans_date >= '".$startDate."' AND tl.trans_date <= '".$endDate."' THEN CASE WHEN tl.c_or_d = 'DR' THEN tl.amount ELSE 0 END ELSE 0 END) as dr_balance, SUM( CASE WHEN tl.trans_date >= '".$startDate."' AND tl.trans_date <= '".$endDate."' THEN CASE WHEN tl.c_or_d = 'CR' THEN tl.amount ELSE 0 END ELSE 0 END) as cr_balance, ((am.op_balance) + SUM( CASE WHEN tl.trans_date <= '".$endDate."' THEN (tl.amount * tl.p_or_m) ELSE 0 END )) as cl_balance FROM party_master as am LEFT JOIN trans_ledger as tl ON am.id = tl.vou_acc_id WHERE am.is_delete = 0 GROUP BY am.id, am.opening_balance ) as lb";

        $queryData['select'] = "lb.id as id, am.name as account_name, lb.op_balance , lb.cr_balance , lb.dr_balance , abs(lb.cl_balance) as cl_balance";

        $queryData['leftJoin']['party_master as am'] = "lb.id = am.id";
        $queryData['order_by']['am.name'] = "ACS"; 
        $ledgerSummary = $this->rows($queryData);*/
    }

    public function getReceivable($fromDate,$toDate){
        $receivable = $this->db->query ("SELECT lb.id as id, am.party_name as account_name,am.group_name, abs(lb.cl_balance) as cl_balance
        FROM (
            SELECT am.id, ((am.opening_balance * ccy.inrrate) + SUM( CASE WHEN tl.trans_date < '".$fromDate."' THEN ((tl.amount * tl.p_or_m) * tl.inrrate) ELSE 0 END )) as op_balance,
            SUM( CASE WHEN tl.trans_date >= '".$fromDate."' AND tl.trans_date <= '".$toDate."' THEN CASE WHEN tl.c_or_d = 'DR' THEN (tl.amount * tl.inrrate) ELSE 0 END ELSE 0 END) as dr_balance,
            SUM( CASE WHEN tl.trans_date >= '".$fromDate."' AND tl.trans_date <= '".$toDate."' THEN CASE WHEN tl.c_or_d = 'CR' THEN (tl.amount * tl.inrrate) ELSE 0 END ELSE 0 END) as cr_balance,
            ((am.opening_balance * ccy.inrrate) + SUM( CASE WHEN tl.trans_date <= '".$toDate."' THEN ((tl.amount * tl.p_or_m) * tl.inrrate) ELSE 0 END )) as cl_balance 
            FROM party_master as am 
            LEFT JOIN trans_ledger as tl ON am.id = tl.vou_acc_id AND tl.is_delete = 0
            LEFT JOIN currency as ccy ON am.currency = ccy.currency
            WHERE am.group_code IN ( 'SD','SC' ) AND am.is_delete = 0 GROUP BY am.id, am.opening_balance 
        ) as lb
        LEFT JOIN party_master as am ON lb.id = am.id 
        WHERE lb.cl_balance < 0 AND am.group_code IN ( 'SD','SC' ) AND am.is_delete = 0 ORDER BY am.party_name")->result();
        return $receivable;
    }

    public function getPayable($fromDate,$toDate){
        $payable = $this->db->query ("SELECT lb.id as id, am.party_name as account_name,am.group_name, abs(lb.cl_balance) as cl_balance

        FROM (
            SELECT am.id, ((am.opening_balance * ccy.inrrate) + SUM( CASE WHEN tl.trans_date < '".$fromDate."' THEN ((tl.amount * tl.p_or_m) * tl.inrrate) ELSE 0 END )) as op_balance,
            SUM( CASE WHEN tl.trans_date >= '".$fromDate."' AND tl.trans_date <= '".$toDate."' THEN CASE WHEN tl.c_or_d = 'DR' THEN (tl.amount * tl.inrrate) ELSE 0 END ELSE 0 END) as dr_balance,
            SUM( CASE WHEN tl.trans_date >= '".$fromDate."' AND tl.trans_date <= '".$toDate."' THEN CASE WHEN tl.c_or_d = 'CR' THEN (tl.amount * tl.inrrate) ELSE 0 END ELSE 0 END) as cr_balance,
            ((am.opening_balance * ccy.inrrate) + SUM( CASE WHEN tl.trans_date <= '".$toDate."' THEN ((tl.amount * tl.p_or_m) * tl.inrrate) ELSE 0 END )) as cl_balance 
            FROM party_master as am 
            LEFT JOIN trans_ledger as tl ON am.id = tl.vou_acc_id  AND tl.is_delete = 0
            LEFT JOIN currency as ccy ON am.currency = ccy.currency
            WHERE am.group_code IN ( 'SD','SC' ) AND am.is_delete = 0 GROUP BY am.id, am.opening_balance 
        ) as lb
        
        LEFT JOIN party_master as am ON lb.id = am.id 
        WHERE lb.cl_balance > 0 AND am.group_code IN ( 'SD','SC' ) AND am.is_delete = 0 ORDER BY am.party_name")->result();
        return $payable;
    }

    //CREATED BY MEGHAVI 15-03-2022
    public function getStockRegister1($type){
		$data['tableName'] = 'item_master';
		$data['select'] = 'item_master.*,currency.inrrate';
		$data['leftJoin']['party_master'] = 'item_master.party_id=party_master.id';
		$data['leftJoin']['currency'] = 'currency.currency=party_master.currency';
		$data['where_in']['item_master.item_type'] = $type;
		return $this->rows($data);
	}
	
    //CREATED BY JP @ 19-04-2022
    public function getStockRegister($postData,$stockType=""){
		$data['tableName'] = 'item_master';
		$data['select'] = 'item_master.id, item_master.item_name, item_master.item_code, item_master.item_type, item_master.price, item_master.rev_no, item_master.drawing_no, currency.inrrate,party_master.party_name, (SELECT price FROM grn_transaction WHERE `grn_transaction`.`item_id` = `item_master`.`id` ORDER BY `grn_transaction`.`id` DESC LIMIT 1) AS last_price'; //,grn_transaction.price AS last_price
		$data['select'] .= ',SUM(CASE WHEN (stock_transaction.trans_type = 1 AND stock_transaction.is_delete=0) THEN stock_transaction.qty ELSE 0 END) AS rqty';
		$data['select'] .= ',SUM(CASE WHEN (stock_transaction.trans_type = 2 AND stock_transaction.is_delete=0) THEN stock_transaction.qty ELSE 0 END) AS iqty';

		$data['leftJoin']['stock_transaction'] = 'stock_transaction.item_id=item_master.id';
		$data['leftJoin']['party_master'] = 'item_master.party_id=party_master.id';
		$data['leftJoin']['currency'] = 'currency.currency=party_master.currency';
		//$data['leftJoin']['(SELECT grn_transaction.price,grn_transaction.item_id FROM grn_transaction WHERE grn_transaction.is_delete = 0 ORDER BY grn_transaction.id DESC LIMIT 1) as grn'] = "grn.item_id = item_master.id";
		// $data['leftJoin']['grn_transaction'] = 'grn_transaction.item_id = item_master.id';
		
		if(!empty($postData['item_id'])){ $data['where_in']['item_master.id'] = $postData['item_id']; }
		
		if($postData['item_type'] == 1){ // FG STOCK
		    
		    $data['select'] .= ',SUM(CASE WHEN stock_transaction.location_id = '.$this->RTD_STORE->id.' THEN stock_transaction.qty ELSE 0 END) AS stockQty';
            $data['where']['item_master.item_type'] = $postData['item_type'];
		    if(!empty($postData['party_id'])){$data['where']['item_master.party_id'] = $postData['party_id'];}
		    
		}
		elseif($postData['item_type'] == -1) { // WIP RM STOCK
		    
            //$data['select'] .= ',SUM(stock_transaction.qty) AS stockQty';
            $data['select'] .= ',(SELECT SUM((CASE WHEN stock_transaction.trans_type = 2 THEN abs(stock_transaction.qty) ELSE 0 END) + (job_material_dispatch.ex_qty)) FROM stock_transaction JOIN job_material_dispatch ON stock_transaction.ref_id = job_material_dispatch.job_card_id WHERE item_master.id = stock_transaction.item_id AND stock_transaction.ref_type = 3 AND stock_transaction.is_delete = 0 AND job_material_dispatch.is_delete = 0 AND stock_transaction.ref_id != 0 GROUP BY stock_transaction.item_id) AS stockQty';
            $data['select'] .= ',(SELECT IFNULL(SUM(production_approval.total_ok_qty * job_bom.qty),0) from job_bom JOIN job_card ON job_card.id=job_bom.job_card_id JOIN production_approval ON job_card.id=production_approval.job_card_id WHERE job_bom.ref_item_id = item_master.id AND production_approval.out_process_id = 0 AND job_bom.is_delete = 0 AND job_card.is_delete = 0 AND job_card.order_status NOT IN(5,6) AND production_approval.is_delete = 0 GROUP BY stock_transaction.item_id) AS moved_qty';
            $data['select'] .= ',(SELECT SUM(stock_transaction.qty) FROM job_bom JOIN job_card ON job_card.id = job_bom.job_card_id JOIN material_master ON item_master.material_grade = material_master.material_grade LEFT JOIN stock_transaction ON stock_transaction.ref_id = job_card.id WHERE job_bom.ref_item_id = item_master.id AND job_bom.is_delete = 0 AND job_card.is_delete = 0 AND job_card.order_status NOT IN(5,6) AND stock_transaction.ref_type IN(18,25) AND stock_transaction.item_id = material_master.scrap_group GROUP BY stock_transaction.item_id) AS scrap_qty';
            $data['where']['item_master.item_type'] = 3;
            //$data['where']['stock_transaction.stock_effect'] = 0;
            
        }
        elseif($postData['item_type'] == -2) { // PACKING AREA STOCK
            
            $data['select'] .= ',SUM(CASE WHEN stock_transaction.location_id = '.$this->PROD_STORE->id.' THEN stock_transaction.qty ELSE 0 END) AS stockQty';
            $data['where']['item_master.item_type'] = 1;
            $data['where']['stock_transaction.stock_effect'] = 1;
		    if(!empty($postData['party_id'])){$data['where']['item_master.party_id'] = $postData['party_id'];}
		    
        } elseif($postData['item_type'] == 3) { // RM STOCK
            
            $data['select'] .= ',SUM(stock_transaction.qty) AS stockQty';
            $data['where']['item_master.item_type'] = $postData['item_type'];
            $data['where']['stock_transaction.stock_effect'] = 1;
            
        } else {
            $data['select'] .= ',SUM(stock_transaction.qty) AS stockQty';
            $data['where']['item_master.item_type'] = $postData['item_type'];
        }
        // IGNORE MISPLACED & SCRAP STOCK
        // $data['customWhere'][] = "stock_transaction.location_id NOT IN('".$this->MIS_PLC_STORE->id."','".$this->SCRAP_STORE->id."')";

		// $data['where']['stock_transaction.ref_date <= '] = $postData['to_date'];
        $data['customWhere'][] = '(stock_transaction.ref_date <= "'.$postData['to_date'].'" OR stock_transaction.ref_date IS NULL)';
		$data['where']['stock_transaction.is_delete'] = 0;
		$data['order_by']['item_master.item_code'] = 'ASC';
		// $data['order_by']['grn_transaction.id'] = 'DESC';
		if(!empty($stockType)){$data['having'][] = $stockType;}
		$data['group_by'][] = 'stock_transaction.item_id';
		$result = $this->rows($data);
		//$this->printQuery();
		return $result;
	}

    //CREATED BY MEGHAVI 15-03-2022
    public function getStockReceiptQty($data){
		$queryData = array();
		$queryData['tableName'] = 'stock_transaction';
		$queryData['select'] = 'SUM(stock_transaction.qty) as rqty';
		$queryData['where']['stock_transaction.item_id'] = $data['item_id'];
		//$queryData['where']['stock_transaction.location_id'] = 11;
		$queryData['where']['stock_transaction.trans_type'] = 1;
		$queryData['where']['stock_transaction.ref_date < '] = $data['to_date'];
		return $this->row($queryData);
	}

    //CREATED BY MEGHAVI 15-03-2022
	public function getStockIssuedQty($data){
		$queryData = array();
		$queryData['tableName'] = 'stock_transaction';
		$queryData['select'] = 'SUM(stock_transaction.qty) as iqty';
		$queryData['where']['stock_transaction.item_id'] = $data['item_id'];
		$queryData['where']['stock_transaction.trans_type'] = 2;
		$queryData['where']['stock_transaction.ref_date < '] = $data['to_date'];
        return $this->row($queryData);
	}

    /************************************************************************************/
    //Created By Karmi @27/05/2022
    public function getStockReportData($postData){
        $data['tableName'] = $this->stockTransaction;
        $data['select'] = 'stock_transaction.*,SUM(stock_transaction.qty) as current_stock,location_master.store_name,location_master.location,item_master.item_name,item_master.item_code,item_category.category_name';
        $data['join']['location_master'] = 'location_master.id = stock_transaction.location_id';
        $data['join']['item_master'] = 'item_master.id = stock_transaction.item_id';
        $data['leftJoin']['item_category'] = "item_category.id = item_master.category_id";
        $data['where']['stock_transaction.location_id'] = $postData['location_id'];
        $data['group_by'][] = 'stock_transaction.item_id';
        $data['group_by'][] = 'stock_transaction.location_id';
        if(!empty($postData['from_date']) AND !empty($postData['to_date']))
            $data['customWhere'][] = "stock_transaction.ref_date BETWEEN '".$postData['from_date']."' AND '".$postData['to_date']."'"; 
        if($postData['stock_type'] == 1){ 
            $data['having'][] = 'SUM(stock_transaction.qty) > 0';
        }elseif($postData['stock_type'] == 2){
            $data['having'][] = 'SUM(stock_transaction.qty) <= 0';
        }
        return $this->rows($data);
    }
    
	public function getOutstanding($postData){
		$os_type = ($postData['os_type']=="R") ? '<' : '>';
		$daysCondition = ',';$daysFields = '';
		if(!empty($postData['days_range']))
		{
		    $i=1;$rangeLength = count($postData['days_range']);$ele=1;
		    $daysCondition = ($rangeLength > 0) ? ',' : '';
		    foreach($postData['days_range'] as $days){
		        
		        if($i == 1){$daysCondition .='(SUM( CASE WHEN DATEDIFF(DATE_ADD( tl.trans_date,INTERVAL am.credit_days day),NOW()) <= '.$days.' THEN (tl.amount * tl.p_or_m) ELSE 0 END )) as d'.$ele++.',';}
		        if($i == $rangeLength){$daysCondition .='(SUM( CASE WHEN DATEDIFF(DATE_ADD( tl.trans_date,INTERVAL am.credit_days day),NOW()) > '.$days.' THEN (tl.amount * tl.p_or_m) ELSE 0 END )) as d'.$ele++.',';}
		        if($i < $rangeLength){$daysCondition .='(SUM( CASE WHEN DATEDIFF(DATE_ADD( tl.trans_date,INTERVAL am.credit_days day),NOW()) BETWEEN '.($days + 1).' AND '.$postData['days_range'][$i].' THEN (tl.amount * tl.p_or_m) ELSE 0 END )) as d'.$ele++.',';}
		        $i++;
		    }
		    for($x=1;$x<=($rangeLength+1);$x++){$daysFields .= ',abs(lb.d'.$x.') as d'.$x;}
		}
		
		//if($postData['report_type'])
		$dueDaysBetween = "AND DATEDIFF(DATE_ADD( lb.trans_date,INTERVAL am.credit_days day),NOW()) >= 16 AND DATEDIFF(DATE_ADD( lb.trans_date,INTERVAL am.credit_days day),NOW()) <= 30";

        $receivable = $this->db->query ("SELECT lb.id as id, am.party_name as account_name,am.group_name,am.contact_person, am.party_mobile, ct.name as city_name, abs(lb.cl_balance) as cl_balance ".$daysFields.", lb.trans_date,  DATE_ADD( lb.trans_date,INTERVAL am.credit_days day) as due_date, DATEDIFF(DATE_ADD( lb.trans_date,INTERVAL am.credit_days day),NOW()) as pending_days
        FROM (
            SELECT am.id, (am.opening_balance + SUM( CASE WHEN tl.trans_date < '".$postData['from_date']."' THEN (tl.amount * tl.p_or_m) ELSE 0 END )) as op_balance,
            SUM( CASE WHEN tl.trans_date >= '".$postData['from_date']."' AND tl.trans_date <= '".$postData['to_date']."' THEN CASE WHEN tl.c_or_d = 'DR' THEN tl.amount ELSE 0 END ELSE 0 END) as dr_balance,
            SUM( CASE WHEN tl.trans_date >= '".$postData['from_date']."' AND tl.trans_date <= '".$postData['to_date']."' THEN CASE WHEN tl.c_or_d = 'CR' THEN tl.amount ELSE 0 END ELSE 0 END) as cr_balance,
            (am.opening_balance + SUM( CASE WHEN tl.trans_date <= '".$postData['to_date']."' THEN (tl.amount * tl.p_or_m) ELSE 0 END )) as cl_balance ".$daysCondition."
            tl.trans_date           
            FROM party_master as am 
            LEFT JOIN trans_ledger as tl ON am.id = tl.vou_acc_id  AND tl.is_delete = 0          
            WHERE am.group_code IN ( 'SD','SC' ) AND am.is_delete = 0 GROUP BY am.id, am.opening_balance 
        ) as lb
        LEFT JOIN party_master as am ON lb.id = am.id 
        LEFT JOIN cities as ct ON ct.id = am.city_id
        WHERE lb.cl_balance ".$os_type." 0 AND am.group_code IN ( 'SD','SC' ) AND am.is_delete = 0 ORDER BY am.party_name")->result();
        //$this->printQuery();
        return $receivable;
    }

    public function getBankCashBook($fromDate,$toDate,$groupCode){
        $bankCashBook = $this->db->query ("SELECT lb.id as id, am.party_name as account_name, am.group_name, lb.cr_balance, lb.dr_balance, 
        CASE WHEN lb.op_balance > 0 THEN CONCAT(abs(lb.op_balance),' CR.') WHEN lb.op_balance < 0 THEN CONCAT(abs(lb.op_balance),' DR.') ELSE lb.op_balance END op_balance,  
        CASE WHEN lb.cl_balance > 0 THEN CONCAT(abs(lb.cl_balance),' CR.') WHEN lb.cl_balance < 0 THEN CONCAT(abs(lb.cl_balance),' DR.') ELSE lb.cl_balance END as cl_balance 
        FROM (
            SELECT am.id, (am.opening_balance + SUM( CASE WHEN tl.trans_date < '".$fromDate."' THEN (tl.amount * tl.p_or_m) ELSE 0 END )) as op_balance, 
            SUM( CASE WHEN tl.trans_date >= '".$fromDate."' AND tl.trans_date <= '".$toDate."' THEN CASE WHEN tl.c_or_d = 'DR' THEN tl.amount ELSE 0 END ELSE 0 END) as dr_balance,
            SUM( CASE WHEN tl.trans_date >= '".$fromDate."' AND tl.trans_date <= '".$toDate."' THEN CASE WHEN tl.c_or_d = 'CR' THEN tl.amount  ELSE 0 END ELSE 0 END) as cr_balance,
            (am.opening_balance + SUM( CASE WHEN tl.trans_date <= '".$toDate."' THEN (tl.amount * tl.p_or_m) ELSE 0 END )) as cl_balance 
            FROM party_master as am 
            LEFT JOIN trans_ledger as tl ON am.id = tl.vou_acc_id  AND tl.is_delete = 0
            WHERE am.is_delete = 0 AND am.group_code IN ($groupCode) GROUP BY am.id, am.opening_balance
            ) as lb 
        LEFT JOIN party_master as am ON lb.id = am.id WHERE am.is_delete = 0
        ORDER BY am.party_name")->result();
        return $bankCashBook;
    }

    public function getLedgerDetail($fromDate,$toDate,$acc_id,$apiData = array()){
        $limitText = ""; $likeText = "";
        if(!empty($apiData)):
            $limitText = "LIMIT ".$apiData['off_set'].",".$apiData['limit']." "; 
            $likeText = (!empty($apiData['search']))?"AND (am.party_name LIKE '%".$apiData['search']."%' OR tl.trans_number LIKE '%".$apiData['search']."%' OR tl.trans_date LIKE '%".$apiData['search']."%' OR tl.amount LIKE '%".$apiData['search']."%')":"";
        endif;

        //tl.trans_number AS trans_number, 
        $ledgerTransactions = $this->db->query ("SELECT 
        tl.trans_main_id AS id, 
        tl.entry_type AS ent_type, 
        tl.trans_date AS trans_date, 
        (CASE WHEN tl.entry_type = 12 THEN tl.doc_no ELSE tl.trans_number END ) as trans_number,
        tl.vou_name_s AS vou_name_s, 
        tl.amount AS amount,
        tl.c_or_d,
        tl.p_or_m,
        am.party_name AS account_name, 
        CASE WHEN tl.c_or_d = 'DR' THEN tl.amount ELSE 0 END AS dr_amount, 
        CASE WHEN tl.c_or_d = 'CR' THEN tl.amount ELSE 0 END AS cr_amount, 
        tl.remark AS remark 
        FROM ( trans_ledger AS tl LEFT JOIN party_master AS am ON am.id = tl.opp_acc_id ) 
        WHERE tl.vou_acc_id = ".$acc_id." 
        AND tl.trans_date >= '".$fromDate."' AND tl.trans_date <= '".$toDate."' 
        AND tl.is_delete = 0
        ".$likeText."
        ORDER BY tl.trans_date, tl.trans_number ".$limitText)->result();
        return $ledgerTransactions;
    }

    public function getLedgerBalance($fromDate,$toDate,$acc_id){
        
        $ledgerBalance = $this->db->query ("SELECT am.id, am.party_name AS account_name, am.party_mobile AS contact_no, (am.opening_balance + SUM( CASE WHEN tl.trans_date < '".$fromDate."' THEN (tl.amount * tl.p_or_m) ELSE 0 END )) as op_balance, 
        SUM( CASE WHEN tl.trans_date >= '".$fromDate."' AND tl.trans_date <= '".$toDate."' THEN CASE WHEN tl.c_or_d = 'DR' THEN tl.amount ELSE 0 END ELSE 0 END) as dr_balance,
        SUM( CASE WHEN tl.trans_date >= '".$fromDate."' AND tl.trans_date <= '".$toDate."' THEN CASE WHEN tl.c_or_d = 'CR' THEN tl.amount ELSE 0 END ELSE 0 END) as cr_balance,
        (am.opening_balance  + SUM( CASE WHEN tl.trans_date <= '".$toDate."' THEN (tl.amount * tl.p_or_m) ELSE 0 END )) as cl_balance 
        FROM party_master as am 
        LEFT JOIN trans_ledger as tl ON am.id = tl.vou_acc_id  AND tl.is_delete = 0
        WHERE am.is_delete = 0 
        AND am.id = ".$acc_id."
        GROUP BY am.id, am.opening_balance")->row();
        $ledgerBalance->op_balance_type=(!empty($ledgerBalance->op_balance) && $ledgerBalance->op_balance >= 0)?(($ledgerBalance->op_balance > 0)?'CR':''):(($ledgerBalance->op_balance < 0)?'DR':'');
        $ledgerBalance->cl_balance_type=(!empty($ledgerBalance->cl_balance) && $ledgerBalance->cl_balance >= 0)?(($ledgerBalance->cl_balance > 0)?'CR':''):(($ledgerBalance->cl_balance < 0)?'DR':'');
        return $ledgerBalance;
    }

    public function getAccountReportData($fromDate, $toDate, $entry_type, $party_id='', $state_code='')
    {
        $data['tableName'] = 'trans_main';
        $data['select'] = 'trans_main.id,trans_main.trans_number,trans_main.doc_no,trans_main.trans_date,trans_main.party_name,trans_main.currency,trans_main.net_amount,trans_main.vou_name_s,trans_main.taxable_amount,trans_main.party_state_code,trans_main.total_amount,cgst_amount,sgst_amount,igst_amount,cess_amount,gst_amount,states.name as state_name,trans_main.doc_no,party_master.gstin';
        $data['leftJoin']['party_master'] = 'party_master.id=trans_main.party_id';
        $data['leftJoin']['states'] = 'states.id=party_master.state_id';
        $data['where_in']['entry_type']=$entry_type;
        $data['customWhere'][] = "trans_date BETWEEN '" .$fromDate . "' AND '" . $toDate . "'";

        if (!empty($party_id)) {
            $data['where']['trans_main.party_id']=$party_id;
        }
        if (!empty($state_code)) {
            if ($state_code == 1) {
                $data['where']['trans_main.party_state_code']=24;
            }
            if ($state_code == 2) {
                $data['where']['trans_main.party_state_code !=']=24;
            }
        }
        if(!empty($entry_type) && $entry_type == 14):
            $data['order_by']['trans_no']='ASC';
        else:
            $data['order_by']['trans_date']='ASC';
        endif;
        return $this->rows($data);
    }

    public function getGstData($postData)
    {
        $data['tableName'] = 'trans_main';
        $data['select'] = 'trans_main.id,trans_main.trans_number,trans_main.doc_no,trans_main.trans_date,trans_main.party_name,trans_main.currency,trans_main.net_amount,trans_main.vou_name_s,trans_main.taxable_amount,trans_main.party_state_code,trans_main.total_amount,cgst_amount,sgst_amount,igst_amount,cess_amount,gst_amount,states.name as state_name,trans_main.doc_no,party_master.gstin';
        $data['leftJoin']['party_master'] = 'party_master.id=trans_main.party_id';
        $data['leftJoin']['states'] = 'states.id=party_master.state_id';
        $data['where_in']['entry_type']= $postData['entry_type'];
        if(!empty($postData['sales_type'])){$data['where']['sales_type']= $postData['sales_type'];}
        $data['customWhere'][] = "trans_date BETWEEN '" .$postData['from_date'] . "' AND '" . $postData['to_date'] . "'";

        if (!empty($postData['party_id'])) {
            $data['where']['trans_main.party_id']=$postData['party_id'];
        }
        if (!empty($postData['state_code'])) {
            if ($postData['state_code'] == 1) {$data['where']['trans_main.party_state_code']=24;}
            if ($postData['state_code'] == 2) {$data['where']['trans_main.party_state_code !=']=24;}
        }
        $data['order_by']['trans_main.trans_no']='ASC';
        return $this->rows($data);
    }
    
    public function _productOpeningAndClosingAmount($data){
        $from_date = date("Y-m-d",strtotime($data['from_date']));
        $to_date = date("Y-m-d",strtotime($data['to_date']));

        /* $result = $this->db->query("SELECT pm.id, pm.item_name as product_name, ifnull(pl.op_amount,0) as op_amount, ifnull(pl.cl_amount,0) as cl_amount 
        FROM ( 
            SELECT pm.id, (pm.avg_price * ost.stock_qty) as op_amount, (pm.avg_price * cst.stock_qty) as cl_amount FROM  item_master AS pm 
            LEFT JOIN (	
                SELECT SUM(qty) AS stock_qty, item_id FROM stock_transaction WHERE is_delete = 0 AND ref_date < '$from_date' GROUP BY item_id 
            ) AS ost ON ost.item_id = pm.id 
            LEFT JOIN ( 
                SELECT SUM(qty) AS stock_qty, item_id FROM stock_transaction WHERE is_delete = 0 AND ref_date <= '$to_date' GROUP BY item_id 
            ) AS cst ON cst.item_id = pm.id 
            WHERE pm.is_delete = 0 GROUP BY pm.id 
        ) as pl 
        LEFT JOIN item_master AS pm ON pl.id = pm.id 
        WHERE ( pl.op_amount <> 0 OR pl.cl_amount <> 0 ) 
        AND pm.is_delete = 0 ORDER BY pm.item_name")->result(); */

        $result = $this->db->query("SELECT pm.item_type, (CASE WHEN pm.item_type = 1 THEN 'Finish Goods' WHEN pm.item_type = 2 THEN 'Consumable' WHEN pm.item_type = 3 THEN 'Raw Material' ELSE '' END) as ledger_name, ifnull(SUM(pl.op_amount),0) as op_amount, ifnull(SUM(pl.cl_amount),0) as cl_amount 
        FROM (
        
            SELECT pm.id, pm.item_type,  ost.stock_qty as op_stock, (pm.avg_price * ost.stock_qty) as op_amount, cst.stock_qty as cl_stock,(pm.avg_price * cst.stock_qty) as cl_amount 
            FROM  item_master AS pm 
            LEFT JOIN (	
                SELECT SUM(qty) AS stock_qty, item_id FROM stock_transaction WHERE is_delete = 0 AND ref_date < '$from_date' GROUP BY item_id 
            ) AS ost ON ost.item_id = pm.id 
            LEFT JOIN ( 
                SELECT SUM(qty) AS stock_qty, item_id FROM stock_transaction WHERE is_delete = 0 AND ref_date <= '$to_date' GROUP BY item_id 
            ) AS cst ON cst.item_id = pm.id 
            WHERE pm.is_delete = 0 and pm.item_type IN (1,2,3) and (ost.stock_qty <> 0 OR cst.stock_qty <> 0) GROUP BY pm.id
            
        ) as pl 
        LEFT JOIN item_master AS pm ON pl.id = pm.id 
        WHERE ( pl.op_amount <> 0 OR pl.cl_amount <> 0 ) 
        AND pm.is_delete = 0 
        GROUP BY pl.item_type")->result();
        
        return $result;
    }

    public function _accountWiseDetail($data){
        $from_date = date("Y-m-d",strtotime($data['from_date']));
        $to_date = date("Y-m-d",strtotime($data['to_date']));
        $nature = $data['nature'];
        $bs_type_code = $data['bs_type_code'];
        $balance_type = $data['balance_type'];
        $balance = ($data['balance_type'] == "lb.cl_balance > 0")?"lb.cl_balance":"abs(lb.cl_balance) AS cl_balance";

        $result = $this->db->query("SELECT lb.id,lb.name,lb.group_id,lb.group_name,lb.nature, $balance
        FROM (
            SELECT am.id as id,am.party_name AS name,am.group_id,am.group_name,gm.nature, ((am.opening_balance) + SUM( CASE WHEN tl.trans_date <= '$to_date' THEN (tl.amount * tl.p_or_m) ELSE 0 END )) as cl_balance 
            FROM party_master as am 
            LEFT JOIN trans_ledger as tl ON am.id = tl.vou_acc_id 
            LEFT JOIN group_master AS gm ON gm.id = am.group_id
            WHERE am.is_delete = 0
            AND tl.is_delete = 0
            AND gm.nature IN ($nature)
            AND gm.bs_type_code IN ($bs_type_code)
            GROUP BY am.id
        ) AS lb
        WHERE $balance_type")->result();
        
        return $result;
    }

    public function _groupWiseSummary($data){
        $from_date = date("Y-m-d",strtotime($data['from_date']));
        $to_date = date("Y-m-d",strtotime($data['to_date']));
        $nature = $data['nature'];
        $bs_type_code = $data['bs_type_code'];
        $balance_type = $data['balance_type'];
        $balance = ($data['balance_type'] == "gs.cl_balance > 0")?"SUM(gs.cl_balance) AS cl_balance":"SUM(abs(gs.cl_balance)) AS cl_balance";

        $result = $this->db->query("SELECT gs.id, gs.group_name, gs.nature, gs.bs_type_code, gs.seq, $balance
        FROM (
            SELECT gm.id, gm.name AS group_name, gm.nature, gm.bs_type_code, gm.seq, ((am.opening_balance) + SUM( CASE WHEN tl.trans_date <= '$to_date' THEN (tl.amount * tl.p_or_m) ELSE 0 END )) as cl_balance 
            FROM party_master as am 
            LEFT JOIN trans_ledger as tl ON am.id = tl.vou_acc_id
            LEFT JOIN group_master AS gm ON gm.id = am.group_id
            WHERE am.is_delete = 0
            AND tl.is_delete = 0
            AND gm.nature IN ($nature)
            AND gm.bs_type_code IN ($bs_type_code)            
            GROUP BY am.id
        ) AS gs
        WHERE $balance_type
        GROUP BY gs.id ORDER BY gs.seq")->result();

        return $result;
    }

    public function _netPnlAmount($data){
        $closingStockAmount = (!empty($data['closingAmount']))?$data['closingAmount']:0;
        $openingStockAmount = (!empty($data['openingAmount']))?$data['openingAmount']:0;
        $from_date = date("Y-m-d",strtotime($data['from_date']));
        $to_date = date("Y-m-d",strtotime($data['to_date']));
        $extraWhere = (!empty($data['extra_where']))?" AND ".$data['extra_where']:" ";

        $result = $this->db->query("SELECT ($closingStockAmount + ifnull(pnl.income,0)) - ($openingStockAmount + ifnull((CASE WHEN pnl.expense < 0 THEN abs(pnl.expense) ELSE pnl.expense * -1 END),0)) as net_pnl_amount 
        FROM ( 
            SELECT SUM(am.opening_balance) + SUM( CASE WHEN gm.nature = 'Income' AND tl.trans_date <= '$to_date' THEN (tl.amount * tl.p_or_m) ELSE 0 END) as income, 
            SUM(am.opening_balance) + SUM( CASE WHEN gm.nature = 'Expenses' AND tl.trans_date <= '$to_date' THEN (tl.amount * tl.p_or_m) ELSE 0 END) as expense 
            FROM ( ( party_master as am LEFT JOIN trans_ledger as tl ON am.id = tl.vou_acc_id ) 
            LEFT JOIN group_master gm ON am.group_id = gm.id ) 
            WHERE am.is_delete = 0 AND tl.is_delete = 0 $extraWhere 
        ) as pnl")->row();

        return $result;
    }

    public function _trailAccountSummary($data){
        $from_date = date("Y-m-d",strtotime($data['from_date']));
        $to_date = date("Y-m-d",strtotime($data['to_date']));

        $result = $this->db->query("SELECT am.party_name as name, am.group_name, am.group_id,
        ifnull((CASE WHEN lb.cl_balance < 0 THEN abs(lb.cl_balance) ELSE 0 END),0) as debit_amount,
        ifnull((CASE WHEN lb.cl_balance > 0 THEN lb.cl_balance ELSE 0 END),0) as credit_amount,
        ifnull(lb.cl_balance,0) as cl_balance
        FROM ( party_master am LEFT JOIN group_master gm ON am.group_id = gm.id ) 
        LEFT JOIN ( 
            SELECT am.id as id, ((am.opening_balance) + SUM( CASE WHEN tl.trans_date <= '$to_date' THEN (tl.amount * tl.p_or_m) ELSE 0 END )) as cl_balance FROM party_master as am LEFT JOIN trans_ledger as tl ON am.id = tl.vou_acc_id WHERE am.is_delete = 0 AND tl.is_delete = 0 GROUP BY am.id 
        ) as lb ON am.id = lb.id 
        WHERE am.is_delete = 0 
        AND lb.cl_balance <> 0
        ORDER BY gm.bs_type_code, am.group_name, am.party_name")->result();

        return $result;
    }

    public function _trailSubGroupSummary($data){
        $from_date = date("Y-m-d",strtotime($data['from_date']));
        $to_date = date("Y-m-d",strtotime($data['to_date']));
        $extraWhere = (!empty($data['extra_where']))?" AND ".$data['extra_where']:" ";

        $result = $this->db->query("SELECT gm.id,gm.name as group_name, gm.nature ,gm.bs_type_code, gm.base_group_id, gm.under_group_id,
        (CASE WHEN gm.base_group_id = 0 THEN gm.under_group_id ELSE gm.base_group_id END) as bs_id,
        ifnull((CASE WHEN gs.cl_balance < 0 THEN abs(gs.cl_balance) ELSE 0 END),0) as debit_amount,
        ifnull((CASE WHEN gs.cl_balance > 0 THEN gs.cl_balance ELSE 0 END),0) as credit_amount,
        ifnull(gs.cl_balance,0) as cl_balance
        FROM  group_master as gm 
        LEFT JOIN ( 
            SELECT am.group_id,(SUM(am.opening_balance) + SUM( CASE WHEN tl.trans_date <= '$to_date' THEN (tl.amount * tl.p_or_m) ELSE 0 END )) as cl_balance FROM ( party_master as am LEFT JOIN trans_ledger as tl ON am.id = tl.vou_acc_id) WHERE  am.is_delete = 0 AND tl.is_delete = 0  GROUP BY am.group_id
        ) AS gs on gm.id = gs.group_id 
        WHERE  gm.is_delete = 0 
        $extraWhere
        ORDER BY gm.seq")->result();

        return $result;
    }

    public function _trailMainGroupSummary($data){
        $from_date = date("Y-m-d",strtotime($data['from_date']));
        $to_date = date("Y-m-d",strtotime($data['to_date']));
        $extraWhere = (!empty($data['extra_where']))?" AND ".$data['extra_where']:" ";

        $result = $this->db->query("SELECT bsgm.id,bsgm.name as group_name, bsgm.nature, sugm.debit_amount, sugm.credit_amount, ifnull((sugm.credit_amount - sugm.debit_amount),0) as cl_balance
        FROM group_master as bsgm
        LEFT JOIN (
            SELECT (CASE WHEN gm.base_group_id = 0 THEN gm.under_group_id ELSE gm.base_group_id END) as bs_id,
            ifnull(SUM((CASE WHEN gs.cl_balance < 0 THEN abs(gs.cl_balance) ELSE 0 END)),0) as debit_amount,
            ifnull(SUM((CASE WHEN gs.cl_balance > 0 THEN gs.cl_balance ELSE 0 END)),0) as credit_amount
            FROM  group_master as gm 
            LEFT JOIN ( 
                SELECT am.id,am.group_id,
                (SUM(am.opening_balance) + SUM( CASE WHEN tl.trans_date <= '$to_date' THEN (tl.amount * tl.p_or_m) ELSE 0 END )) as cl_balance FROM ( party_master as am LEFT JOIN trans_ledger as tl ON am.id = tl.vou_acc_id) WHERE  am.is_delete = 0 AND tl.is_delete = 0 GROUP BY am.id
            ) AS gs on gm.id = gs.group_id 
            WHERE  gm.is_delete = 0 
            $extraWhere
            GROUP BY (CASE WHEN gm.base_group_id = 0 THEN gm.under_group_id ELSE gm.base_group_id END)
            ORDER BY gm.seq
        ) as sugm ON bsgm.id = sugm.bs_id
        WHERE bsgm.is_delete = 0
        AND ( sugm.debit_amount <> 0 OR sugm.credit_amount <> 0)
        ORDER BY bsgm.seq")->result();

        return $result;
    }
    
    public function getFundManagementData($data){
        $from_date = $data['from_date'];
        $to_date = $data['to_date'];

        $result = $this->db->query("SELECT tm.party_id, am.party_name, tm.entry_type, tm.trans_date, tm.credit_period, (CASE WHEN tm.credit_period = 0 THEN '$from_date' WHEN DATE_ADD(tm.trans_date,INTERVAL tm.credit_period DAY) < '$from_date' THEN '$from_date'  ELSE DATE_ADD(tm.trans_date,INTERVAL tm.credit_period DAY) END) as due_date, 
        SUM((CASE WHEN tm.entry_type IN (6,7,8,14,16) THEN ((tm.net_amount - tm.paid_amount) * 1) ELSE ((tm.net_amount - tm.paid_amount) * -1) END)) as due_amount
        FROM trans_main as tm
        LEFT JOIN party_master as am ON tm.party_id = am.id
        WHERE tm.entry_type IN (6,7,8,12,13,14,18,15,16) 
        AND tm.is_delete = 0 
        AND (tm.net_amount - tm.paid_amount) <> 0 
        AND (CASE WHEN tm.credit_period = 0 THEN '$from_date' WHEN DATE_ADD(tm.trans_date,INTERVAL tm.credit_period DAY) < '$from_date' THEN '$from_date'  ELSE DATE_ADD(tm.trans_date,INTERVAL tm.credit_period DAY) END) >= '$from_date'
        AND (CASE WHEN tm.credit_period = 0 THEN '$from_date' WHEN DATE_ADD(tm.trans_date,INTERVAL tm.credit_period DAY) < '$from_date' THEN '$from_date'  ELSE DATE_ADD(tm.trans_date,INTERVAL tm.credit_period DAY) END) <= '$to_date'
        GROUP BY tm.party_id,(CASE WHEN tm.credit_period = 0 THEN '$from_date' WHEN DATE_ADD(tm.trans_date,INTERVAL tm.credit_period DAY) < '$from_date' THEN '$from_date'  ELSE DATE_ADD(tm.trans_date,INTERVAL tm.credit_period DAY) END)
        ORDER BY (CASE WHEN tm.credit_period = 0 THEN '$from_date' WHEN DATE_ADD(tm.trans_date,INTERVAL tm.credit_period DAY) < '$from_date' THEN '$from_date'  ELSE DATE_ADD(tm.trans_date,INTERVAL tm.credit_period DAY) END);")->result();

        return $result;
    }
    
    //CREATED BY Raj Fatepara 14-05-2024
	public function getExportIncentives($data){
	    $queryData['tableName'] = "bill_of_lading";
		$queryData['select'] = "bill_of_lading.*,trans_main.trans_number,trans_main.trans_date,trans_main.doc_no,trans_main.doc_date,shipping_bill.drawback_amount,shipping_bill.igst_amount,shipping_bill.rodtep_amount,trans_main.extra_fields,trans_main.currency,trans_main.net_amount,trans_main.party_name,shipping_bill.sb_amount,shipping_bill.port_code,shipping_bill.sb_number,shipping_bill.sb_date,shipping_bill.sb_fob_inr,shipping_bill.sb_freight_inr,shipping_bill.sb_insurance_inr,shipping_bill.sb_ex_rate,shipping_bill.sb_remark";
        $queryData['leftJoin']['trans_main'] = "trans_main.id = bill_of_lading.com_inv_id";
        $queryData['leftJoin']['shipping_bill'] = "shipping_bill.id = bill_of_lading.sb_id";
		$queryData['where']["trans_main.doc_date >="] = $data['from_date'];
        $queryData['where']["trans_main.doc_date <="] = $data['to_date'];
		
		$result = $this->rows($queryData);

		foreach($result as $key=>$value):
			if(!empty($value->extra_fields)):
				$jsonData = json_decode($value->extra_fields);
				foreach($jsonData as $key1=>$value1):
					$result[$key]->{$key1} = $value1;
				endforeach;
			endif;
        endforeach;
		
		return $result;
    }
	
	//CREATED BY Raj Fatepara 15-05-2024
	public function getExportCollection($data){
		$queryData['tableName'] = "bill_of_lading";
		$queryData['select'] = "bill_of_lading.*,trans_main.trans_number,trans_main.trans_date,trans_main.doc_no,trans_main.doc_date,shipping_bill.drawback_amount,shipping_bill.igst_amount,shipping_bill.rodtep_amount,trans_main.extra_fields,trans_main.currency,trans_main.net_amount,trans_main.party_name,shipping_bill.sb_amount,shipping_bill.port_code,shipping_bill.sb_number,shipping_bill.sb_date,shipping_bill.sb_fob_inr,shipping_bill.sb_freight_inr,shipping_bill.sb_insurance_inr,shipping_bill.sb_ex_rate,shipping_bill.sb_remark,IF(DATEDIFF('".$data['to_date']."',bill_of_lading.payment_due_date) > 0, DATEDIFF('".$data['to_date']."',bill_of_lading.payment_due_date) , '-') as due_days";

		$queryData['leftJoin']['trans_main'] = "trans_main.id = bill_of_lading.com_inv_id";
        $queryData['leftJoin']['shipping_bill'] = "shipping_bill.id = bill_of_lading.sb_id";

        $queryData['where']['(trans_main.net_amount - bill_of_lading.received_fc) >'] = 0;
		
		if($data['due_type'] == "over_due"):
			$queryData['where']["DATEDIFF('".$data['to_date']."',bill_of_lading.payment_due_date) >"] = 0;
		elseif($data['due_type'] == "under_due"):
			$queryData['where']["DATEDIFF('".$data['to_date']."',bill_of_lading.payment_due_date) <="] = 0;
		else:
			$queryData['customWhere'][] = "(DATEDIFF('".$data['to_date']."',bill_of_lading.payment_due_date) > 0 OR DATEDIFF('".$data['to_date']."',bill_of_lading.payment_due_date) <= 0)";
		endif;
		
		return $this->rows($queryData);
    }
}
?>