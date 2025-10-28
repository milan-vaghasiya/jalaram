<?php
class TaxInvoiceAdjustmentModel extends MasterModel{
    private $ladingBill = "bill_of_lading";
    private $shippingBill = "shipping_bill";
    private $transMain = "trans_main";
    private $swiftRemittance = "swift_remittance";
    private $remittanceTrans = "remittance_transfer";

    public function getDTRows($data){
        if($data['status'] == 0):
            $data['tableName'] = $this->ladingBill;

            $data['select'] = "bill_of_lading.id,bill_of_lading.tax_invoice_total,trans_main.trans_number,trans_main.trans_date,trans_main.doc_no,trans_main.doc_date,trans_main.party_name,trans_main.currency,shipping_bill.sb_amount";

            $data['leftJoin']['trans_main'] = "trans_main.id = bill_of_lading.com_inv_id";
            $data['leftJoin']['shipping_bill'] = "shipping_bill.id = bill_of_lading.sb_id";
            $data['leftJoin']['(SELECT bl_id, SUM(net_credit_inr_adj) as net_credit_inr_adj FROM remittance_transfer WHERE is_delete = 0 AND entry_type = 4 GROUP BY bl_id) as adj_trans'] = "bill_of_lading.id = adj_trans.bl_id";

            //$data['customWhere'][] = "(((bill_of_lading.tax_invoice_total - shipping_bill.igst_amount - IFNULL(adj_trans.net_credit_inr_adj,0)) > 0) OR bill_of_lading.tax_invoice_total = 0)";
            $data['customWhere'][] = "(IFNULL(adj_trans.net_credit_inr_adj,0) = 0 OR bill_of_lading.tax_invoice_total = 0)";
            $data['where']['bill_of_lading.bl_awb_date <='] = $this->endYearDate;

            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "trans_main.doc_no";
            $data['searchCol'][] = "DATE_FORMAT(trans_main.doc_date,'%d-%m-%Y')";
            $data['searchCol'][] = "trans_main.party_name";
            $data['searchCol'][] = "trans_main.currency";
            $data['searchCol'][] = "shipping_bill.sb_amount";
            $data['searchCol'][] = "bill_of_lading.tax_invoice_total";
        elseif($data['status'] == 1):
            $data['tableName'] = $this->remittanceTrans;

            $data['select'] = "remittance_transfer.*,swift_remittance.firc_number,swift_remittance.remittance_date,swift_remittance.remitter_name,swift_remittance.swift_currency, IFNULL(adj_trans.firc_transfer_adj,0) as firc_transfer_adj, IFNULL(adj_trans.net_credit_inr_adj,0) as net_credit_inr_adj, (remittance_transfer.firc_transfer - IFNULL(adj_trans.firc_transfer_adj,0)) as firc_transfer_bal, (remittance_transfer.net_credit_inr - IFNULL(adj_trans.net_credit_inr_adj,0)) as net_credit_inr_bal";

            $data['leftJoin']['swift_remittance'] = "swift_remittance.id = remittance_transfer.swift_id";
            $data['leftJoin']['(SELECT swift_id, SUM(firc_transfer_adj) as firc_transfer_adj, SUM(net_credit_inr_adj) as net_credit_inr_adj FROM remittance_transfer WHERE is_delete = 0 AND entry_type = 4 GROUP BY swift_id) as adj_trans'] = "remittance_transfer.id = adj_trans.swift_id";

            $data['where']['remittance_transfer.entry_type'] = 1;
            $data['where']['(remittance_transfer.net_credit_inr - IFNULL(adj_trans.net_credit_inr_adj,0)) >'] = 0;
            $data['where']['swift_remittance.remittance_date <='] = $this->endYearDate;

            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "swift_remittance.firc_number";
            $data['searchCol'][] = "DATE_FORMAT(swift_remittance.remittance_date,'%d-%m-%Y')";
            $data['searchCol'][] = "swift_remittance.remitter_name";
            $data['searchCol'][] = "swift_remittance.swift_currency";
            $data['searchCol'][] = "remittance_transfer.trans_ref_no";
            $data['searchCol'][] = "DATE_FORMAT(remittance_transfer.trans_date,'%d-%m-%Y')";
            $data['searchCol'][] = "(remittance_transfer.firc_transfer - IFNULL(adj_trans.firc_transfer_adj,0))";
            $data['searchCol'][] = "(remittance_transfer.net_credit_inr - IFNULL(adj_trans.net_credit_inr_adj,0))";
        else:
            $data['tableName'] = $this->remittanceTrans;

            $data['select'] = "remittance_transfer.bl_id, SUM(remittance_transfer.net_credit_inr_adj) as net_credit_inr_adj, trans_main.doc_no, trans_main.doc_date, trans_main.party_name, bill_of_lading.tax_invoice_total, (bill_of_lading.tax_invoice_total - shipping_bill.igst_amount - SUM(remittance_transfer.net_credit_inr_adj)) as ex_gain_loss_inr";

            $data['leftJoin']['bill_of_lading'] = "bill_of_lading.id = remittance_transfer.bl_id";
            $data['leftJoin']['trans_main'] = "trans_main.id = bill_of_lading.com_inv_id";
            /* $data['leftJoin']['remittance_transfer as remit_transfer'] = "remit_transfer.id = remittance_transfer.swift_id";
            $data['leftJoin']['swift_remittance'] = "swift_remittance.id = remit_transfer.swift_id"; */
            $data['leftJoin']['shipping_bill'] = "shipping_bill.id = bill_of_lading.sb_id";

            $data['where']['remittance_transfer.entry_type'] = 4;
            $data['where']['remittance_transfer.trans_date >='] = $this->startYearDate;
            $data['where']['remittance_transfer.trans_date <='] = $this->endYearDate;

            $data['group_by'][] = "remittance_transfer.bl_id";

            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "trans_main.doc_no";
            $data['searchCol'][] = "DATE_FORMAT(trans_main.doc_date,'%d-%m-%Y')";
            $data['searchCol'][] = "trans_main.party_name";
            $data['searchCol'][] = "bill_of_lading.tax_invoice_total";
            $data['searchCol'][] = "SUM(remittance_transfer.net_credit_inr_adj)";
            $data['searchCol'][] = "(bill_of_lading.tax_invoice_total - shipping_bill.igst_amount - SUM(remittance_transfer.net_credit_inr_adj))";
        endif;

        $columns = array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data); //$this->printQuery();
    }

    public function saveTaxInvTotal($data){
        try{
            $this->db->trans_begin();

            $result = $this->store($this->ladingBill,$data,'Tax Invoice Total');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }

    public function getLadingBillDetail($data){
        $queryData = [];
        $queryData['tableName'] = $this->ladingBill;
        $queryData['select'] = "bill_of_lading.id,bill_of_lading.tax_invoice_total,IFNULL(adj_trans.net_credit_inr_adj,0) as net_credit_inr_adj,trans_main.trans_number,trans_main.trans_date,trans_main.doc_no,trans_main.doc_date,trans_main.party_name,trans_main.currency,shipping_bill.sb_amount,shipping_bill.igst_amount, (bill_of_lading.tax_invoice_total - shipping_bill.igst_amount - SUM(adj_trans.net_credit_inr_adj)) as ex_gain_loss_inr";

        $queryData['leftJoin']['trans_main'] = "trans_main.id = bill_of_lading.com_inv_id";
        $queryData['leftJoin']['shipping_bill'] = "shipping_bill.id = bill_of_lading.sb_id";
        $queryData['leftJoin']['(SELECT bl_id, SUM(net_credit_inr_adj) as net_credit_inr_adj FROM remittance_transfer WHERE is_delete = 0 AND entry_type = 4 AND bl_id = '.$data['id'].' GROUP BY bl_id) as adj_trans'] = "bill_of_lading.id = adj_trans.bl_id";

        $queryData['where']['bill_of_lading.id'] = $data['id'];

        $result = $this->row($queryData);
        return $result;
    }

    public function getUnsetlledRemitTransfer(){
        $queryData = [];
        $queryData['tableName'] = $this->remittanceTrans;

        $queryData['select'] = "remittance_transfer.*,swift_remittance.firc_number,swift_remittance.remittance_date,swift_remittance.remitter_name,swift_remittance.swift_currency, IFNULL(adj_trans.firc_transfer_adj,0) as firc_transfer_adj, IFNULL(adj_trans.net_credit_inr_adj,0) as net_credit_inr_adj, (remittance_transfer.firc_transfer - IFNULL(adj_trans.firc_transfer_adj,0)) as firc_transfer_bal, (remittance_transfer.net_credit_inr - IFNULL(adj_trans.net_credit_inr_adj,0)) as net_credit_inr_bal";

        $queryData['leftJoin']['swift_remittance'] = "swift_remittance.id = remittance_transfer.swift_id";
        $queryData['leftJoin']['(SELECT swift_id, SUM(firc_transfer_adj) as firc_transfer_adj, SUM(net_credit_inr_adj) as net_credit_inr_adj FROM remittance_transfer WHERE is_delete = 0 AND entry_type = 4 GROUP BY swift_id) as adj_trans'] = "remittance_transfer.id = adj_trans.swift_id";

        $queryData['where']['remittance_transfer.entry_type'] = 1;

        $queryData['where']['(remittance_transfer.net_credit_inr - IFNULL(adj_trans.net_credit_inr_adj,0)) >'] = 0;

        $result = $this->rows($queryData);
        return $result;
    }

    public function getAdjustedTransactions($data){
        $queryData['tableName'] = $this->remittanceTrans;
        $queryData['select'] = "remittance_transfer.id,swift_remittance.remitter_name,remit_transfer.trans_date,remittance_transfer.firc_transfer_adj,remittance_transfer.net_credit_inr_adj";

        $queryData['leftJoin']['remittance_transfer as remit_transfer'] = "remit_transfer.id = remittance_transfer.swift_id";
        $queryData['leftJoin']['swift_remittance'] = "swift_remittance.id = remit_transfer.swift_id";

        $queryData['where']['remittance_transfer.entry_type'] = 4;
        $queryData['where']['remittance_transfer.bl_id'] = $data['bl_id'];

        $result = $this->rows($queryData);
        return $result;
    }

    public function save($data){
        try{
            $this->db->trans_begin();
            
            $itemData = $data['itemData'];unset($data['itemData']);
            foreach($itemData as $row):
                $row['firc_transfer_adj'] = (!empty($row['firc_transfer_adj']))?$row['firc_transfer_adj']:0;
                $row['net_credit_inr_adj'] = (!empty($row['net_credit_inr_adj']))?$row['net_credit_inr_adj']:0;

                if(($row['firc_transfer_adj'] + $row['net_credit_inr_adj']) > 0):
                    $row['trans_date'] = date("Y-m-d");
                    $row['created_by'] = $this->loginId;
                    $row['is_delete'] = 0;
                    $result = $this->store($this->remittanceTrans,$row,'Tax Invoice Adjustment');
                endif;
            endforeach;

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function delete($id){
        try{
            $this->db->trans_begin();

            $result = $this->trash($this->remittanceTrans,['bl_id'=>$id,'entry_type'=>4]);
            $result['message'] = "Invoice Adjustment removed successfully.";

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }
}
?>