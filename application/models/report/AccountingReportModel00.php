<?php
class AccountingReportModel extends MasterModel{

    public function getLedgerSummary($fromDate="1970-01-01",$toDate=""){
        $startDate = (!empty($fromDate))?$fromDate:$this->startYearDate;
        $endDate = (!empty($toDate))?$toDate:$this->endYearDate;
        $startDate = date("Y-m-d",strtotime($startDate));
        $endDate = date("Y-m-d",strtotime($endDate));

        $ledgerSummary = $this->db->query("SELECT lb.id as id, am.party_name as account_name,  CASE WHEN lb.op_balance > 0 THEN CONCAT(abs(lb.op_balance),' CR.') WHEN lb.op_balance < 0 THEN CONCAT(abs(lb.op_balance),' DR.') ELSE lb.op_balance END op_balance,am.group_name, lb.cr_balance, lb.dr_balance, CASE WHEN lb.cl_balance > 0 THEN CONCAT(abs(lb.cl_balance),' CR.') WHEN lb.cl_balance < 0 THEN CONCAT(abs(lb.cl_balance),' DR.') ELSE lb.cl_balance END as cl_balance 
        FROM (
            SELECT am.id, ((am.opening_balance) + SUM( CASE WHEN tl.trans_date < '".$startDate."' THEN (tl.amount * tl.p_or_m) ELSE 0 END )) as op_balance, 
            SUM( CASE WHEN tl.trans_date >= '".$startDate."' AND tl.trans_date <= '".$endDate."' THEN CASE WHEN tl.c_or_d = 'DR' THEN tl.amount ELSE 0 END ELSE 0 END) as dr_balance,
            SUM( CASE WHEN tl.trans_date >= '".$startDate."' AND tl.trans_date <= '".$endDate."' THEN CASE WHEN tl.c_or_d = 'CR' THEN tl.amount ELSE 0 END ELSE 0 END) as cr_balance,
            ((am.opening_balance) + SUM( CASE WHEN tl.trans_date <= '".$endDate."' THEN (tl.amount * tl.p_or_m) ELSE 0 END )) as cl_balance 
            FROM party_master as am 
            LEFT JOIN trans_ledger as tl ON am.id = tl.vou_acc_id 
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

    public function getLedgerDetail($fromDate,$toDate,$acc_id){
        $ledgerTransactions = $this->db->query ("SELECT 
        tl.trans_main_id AS id, 
        tl.entry_type AS ent_type, 
        tl.trans_date AS trans_date, 
        tl.trans_number AS trans_number, 
        tl.vou_name_s AS vou_name_s, 
        am.party_name AS account_name, 

        CASE WHEN tl.c_or_d = 'DR' THEN (tl.amount * tl.inrrate) ELSE 0 END AS dr_amount, 
        CASE WHEN tl.c_or_d = 'CR' THEN (tl.amount * tl.inrrate) ELSE 0 END AS cr_amount, 

        tl.remark AS remark 

        FROM ( trans_ledger AS tl LEFT JOIN party_master AS am ON am.id = tl.opp_acc_id ) 
        WHERE tl.vou_acc_id = ".$acc_id." 
        AND tl.trans_date >= '".$fromDate."' AND tl.trans_date <= '".$toDate."'
        ORDER BY tl.trans_date, tl.trans_number")->result();
        return $ledgerTransactions;
    }

    public function getLedgerBalance($fromDate,$toDate,$acc_id){
        $ledgerBalance = $this->db->query ("SELECT am.id,am.party_name AS account_name,((am.opening_balance * ccy.inrrate) + SUM( CASE WHEN tl.trans_date < '".$fromDate."' THEN ((tl.amount * tl.p_or_m) * tl.inrrate) ELSE 0 END )) as op_balance, 
        SUM( CASE WHEN tl.trans_date >= '".$fromDate."' AND tl.trans_date <= '".$toDate."' THEN CASE WHEN tl.c_or_d = 'DR' THEN (tl.amount * tl.inrrate) ELSE 0 END ELSE 0 END) as dr_balance,
        SUM( CASE WHEN tl.trans_date >= '".$fromDate."' AND tl.trans_date <= '".$toDate."' THEN CASE WHEN tl.c_or_d = 'CR' THEN (tl.amount * tl.inrrate) ELSE 0 END ELSE 0 END) as cr_balance,
        ((am.opening_balance * ccy.inrrate) + SUM( CASE WHEN tl.trans_date <= '".$toDate."' THEN ((tl.amount * tl.p_or_m) * tl.inrrate) ELSE 0 END )) as cl_balance 
        FROM party_master as am 
        LEFT JOIN trans_ledger as tl ON am.id = tl.vou_acc_id 
        LEFT JOIN currency as ccy ON am.currency = ccy.currency
        WHERE am.is_delete = 0 
        AND am.id = ".$acc_id."
        GROUP BY am.id, am.opening_balance")->row();
        $ledgerBalance->op_balance_type=(!empty($ledgerBalance->op_balance) && $ledgerBalance->op_balance > 0)?(($ledgerBalance->op_balance > 0)?'CR':''):(($ledgerBalance->op_balance < 0)?'DR':'');
        $ledgerBalance->cl_balance_type=(!empty($ledgerBalance->cl_balance) && $ledgerBalance->cl_balance > 0)?(($ledgerBalance->cl_balance > 0)?'CR':''):(($ledgerBalance->cl_balance < 0)?'DR':'');
        return $ledgerBalance;
    }

    public function getReceivable($fromDate,$toDate){
        $receivable = $this->db->query ("SELECT lb.id as id, am.party_name as account_name,am.group_name, abs(lb.cl_balance) as cl_balance
        FROM (
            SELECT am.id, ((am.opening_balance * ccy.inrrate) + SUM( CASE WHEN tl.trans_date < '".$fromDate."' THEN ((tl.amount * tl.p_or_m) * tl.inrrate) ELSE 0 END )) as op_balance,
            SUM( CASE WHEN tl.trans_date >= '".$fromDate."' AND tl.trans_date <= '".$toDate."' THEN CASE WHEN tl.c_or_d = 'DR' THEN (tl.amount * tl.inrrate) ELSE 0 END ELSE 0 END) as dr_balance,
            SUM( CASE WHEN tl.trans_date >= '".$fromDate."' AND tl.trans_date <= '".$toDate."' THEN CASE WHEN tl.c_or_d = 'CR' THEN (tl.amount * tl.inrrate) ELSE 0 END ELSE 0 END) as cr_balance,
            ((am.opening_balance * ccy.inrrate) + SUM( CASE WHEN tl.trans_date <= '".$toDate."' THEN ((tl.amount * tl.p_or_m) * tl.inrrate) ELSE 0 END )) as cl_balance 
            FROM party_master as am 
            LEFT JOIN trans_ledger as tl ON am.id = tl.vou_acc_id 
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
            LEFT JOIN trans_ledger as tl ON am.id = tl.vou_acc_id 
            LEFT JOIN currency as ccy ON am.currency = ccy.currency
            WHERE am.group_code IN ( 'SD','SC' ) AND am.is_delete = 0 GROUP BY am.id, am.opening_balance 
        ) as lb
        
        LEFT JOIN party_master as am ON lb.id = am.id 
        WHERE lb.cl_balance > 0 AND am.group_code IN ( 'SD','SC' ) AND am.is_delete = 0 ORDER BY am.party_name")->result();
        return $payable;
    }

    public function getBankCashBook($fromDate,$toDate,$groupCode){
        $bankCashBook = $this->db->query ("SELECT lb.id as id, am.party_name as account_name, am.group_name, lb.cr_balance, lb.dr_balance, 
        CASE WHEN lb.op_balance > 0 THEN CONCAT(abs(lb.op_balance),' CR.') WHEN lb.op_balance < 0 THEN CONCAT(abs(lb.op_balance),' DR.') ELSE lb.op_balance END op_balance,  
        CASE WHEN lb.cl_balance > 0 THEN CONCAT(abs(lb.cl_balance),' CR.') WHEN lb.cl_balance < 0 THEN CONCAT(abs(lb.cl_balance),' DR.') ELSE lb.cl_balance END as cl_balance 
        FROM (
            SELECT am.id, ((am.opening_balance * ccy.inrrate) + SUM( CASE WHEN tl.trans_date < '".$fromDate."' THEN ((tl.amount * tl.p_or_m) * tl.inrrate) ELSE 0 END )) as op_balance, 
            SUM( CASE WHEN tl.trans_date >= '".$fromDate."' AND tl.trans_date <= '".$toDate."' THEN CASE WHEN tl.c_or_d = 'DR' THEN (tl.amount * tl.inrrate) ELSE 0 END ELSE 0 END) as dr_balance,
            SUM( CASE WHEN tl.trans_date >= '".$fromDate."' AND tl.trans_date <= '".$toDate."' THEN CASE WHEN tl.c_or_d = 'CR' THEN (tl.amount * tl.inrrate) ELSE 0 END ELSE 0 END) as cr_balance,
            ((am.opening_balance * ccy.inrrate) + SUM( CASE WHEN tl.trans_date <= '".$toDate."' THEN ((tl.amount * tl.p_or_m) * tl.inrrate) ELSE 0 END )) as cl_balance 
            FROM party_master as am 
            LEFT JOIN trans_ledger as tl ON am.id = tl.vou_acc_id 
            LEFT JOIN currency as ccy ON am.currency = ccy.currency
            WHERE am.is_delete = 0 AND am.group_code IN ('".$groupCode."') GROUP BY am.id, am.opening_balance
            ) as lb 
        LEFT JOIN party_master as am ON lb.id = am.id WHERE am.is_delete = 0
        ORDER BY am.party_name")->result();
        return $bankCashBook;
    }

    public function getAccountReportData($fromDate,$toDate,$entry_type){ 
        $accountReport = $this->db->query ("SELECT id,trans_number,doc_no,trans_date,party_id,party_name,currency,net_amount,(net_amount * inrrate) as net_amount_inr,vou_name_s,taxable_amount,cgst_amount,sgst_amount,igst_amount,(taxable_amount * inrrate)  as taxable_amount_inr 
        FROM trans_main 
        WHERE is_delete = 0
        AND entry_type IN (".$entry_type.")
        AND trans_date BETWEEN '".$fromDate."' AND '".$toDate."'
        ORDER BY trans_date")->result();
        return $accountReport;
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
    public function getStockRegister($type,$stockType=""){
		$data['tableName'] = 'item_master';
		$data['select'] = 'item_master.id, item_master.item_name, item_master.item_code, item_master.item_type, item_master.price, currency.inrrate';
		$data['select'] .= ',SUM(CASE WHEN stock_transaction.trans_type = 1 THEN stock_transaction.qty ELSE 0 END) AS rqty';
		$data['select'] .= ',SUM(CASE WHEN stock_transaction.trans_type = 2 THEN stock_transaction.qty ELSE 0 END) AS iqty';
		if($type == 1){$data['select'] .= ',SUM(CASE WHEN stock_transaction.location_id = 11 THEN stock_transaction.qty ELSE 0 END) AS stockQty';}
		else{$data['select'] .= ',SUM(stock_transaction.qty) AS stockQty';}
		$data['leftJoin']['stock_transaction'] = 'stock_transaction.item_id=item_master.id';
		$data['leftJoin']['party_master'] = 'item_master.party_id=party_master.id';
		$data['leftJoin']['currency'] = 'currency.currency=party_master.currency';
		$data['where_in']['item_master.item_type'] = $type;
		if(!empty($stockType)){$data['having'][] = $stockType;}
		$data['group_by'][] = 'item_master.id';
		$result = $this->rows($data);
		//print_r($this->printQuery());
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
}
?>