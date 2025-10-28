<?php
class LadingBillModel extends MasterModel{
    private $ladingBill = "bill_of_lading";
    private $shippingBill = "shipping_bill";
    private $transMain = "trans_main";

    public function getDTRows($data){
        if($data['status'] == 0):
            $data['tableName'] = $this->shippingBill;

            $data['select'] = "shipping_bill.*,trans_main.trans_number,trans_main.trans_date,trans_main.doc_no,trans_main.doc_date,trans_main.party_name,JSON_UNQUOTE(JSON_EXTRACT(trans_main.extra_fields, '$.country_of_final_destonation')) AS country_of_final_destonation,JSON_UNQUOTE(JSON_EXTRACT(trans_main.extra_fields, '$.port_of_loading')) AS port_of_loading,JSON_UNQUOTE(JSON_EXTRACT(trans_main.extra_fields, '$.port_of_discharge')) AS port_of_discharge";

            $data['leftJoin']['trans_main'] = "trans_main.id = shipping_bill.com_inv_id";

            $data['where']['shipping_bill.sb_status'] = 0;

            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "trans_main.doc_no";
            $data['searchCol'][] = "DATE_FORMAT(trans_main.doc_date,'%d-%m-%Y')";
            $data['searchCol'][] = "trans_main.party_name";
            $data['searchCol'][] = "JSON_UNQUOTE(JSON_EXTRACT(trans_main.extra_fields, '$.country_of_final_destonation'))";
            $data['searchCol'][] = "JSON_UNQUOTE(JSON_EXTRACT(trans_main.extra_fields, '$.port_of_loading'))";
            $data['searchCol'][] = "JSON_UNQUOTE(JSON_EXTRACT(trans_main.extra_fields, '$.port_of_discharge'))";
        else:
            $data['tableName'] = $this->ladingBill;

            $data['select'] = "bill_of_lading.*,trans_main.trans_number,trans_main.trans_date,trans_main.doc_no,trans_main.doc_date,trans_main.party_name,JSON_UNQUOTE(JSON_EXTRACT(trans_main.extra_fields, '$.country_of_final_destonation')) AS country_of_final_destonation,JSON_UNQUOTE(JSON_EXTRACT(trans_main.extra_fields, '$.port_of_loading')) AS port_of_loading,JSON_UNQUOTE(JSON_EXTRACT(trans_main.extra_fields, '$.port_of_discharge')) AS port_of_discharge,(trans_main.net_amount - bill_of_lading.received_fc) as settled_fc,shipping_bill.total_mapped_firc,IFNULL(adj_trans.net_credit_inr_adj,0) as net_credit_inr_adj";

            $data['leftJoin']['trans_main'] = "trans_main.id = bill_of_lading.com_inv_id";
            $data['leftJoin']['shipping_bill'] = "shipping_bill.id = bill_of_lading.sb_id";
            $data['leftJoin']['(SELECT bl_id, SUM(net_credit_inr_adj) as net_credit_inr_adj FROM remittance_transfer WHERE is_delete = 0 AND entry_type = 4 GROUP BY bl_id) as adj_trans'] = "bill_of_lading.id = adj_trans.bl_id";

            $data['where']['bill_of_lading.bl_awb_date >='] = $this->startYearDate;
            $data['where']['bill_of_lading.bl_awb_date <='] = $this->endYearDate;

            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "trans_main.doc_no";
            $data['searchCol'][] = "DATE_FORMAT(trans_main.doc_date,'%d-%m-%Y')";
            $data['searchCol'][] = "trans_main.party_name";
            $data['searchCol'][] = "JSON_UNQUOTE(JSON_EXTRACT(trans_main.extra_fields, '$.country_of_final_destonation'))";
            $data['searchCol'][] = "JSON_UNQUOTE(JSON_EXTRACT(trans_main.extra_fields, '$.port_of_loading'))";
            $data['searchCol'][] = "JSON_UNQUOTE(JSON_EXTRACT(trans_main.extra_fields, '$.port_of_discharge'))";
            $data['searchCol'][] = "bill_of_lading.bl_awb_no";
            $data['searchCol'][] = "DATE_FORMAT(bill_of_lading.bl_awb_date,'%d-%m-%Y')";
        endif;

        $columns = array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }   

    public function save($data){
        try{
            $this->db->trans_begin();

            $result = $this->store($this->ladingBill,$data,'Bill of Lading');

            $setData = array();
            $setData['tableName'] = $this->shippingBill;
            $setData['where']['id'] = $data['sb_id'];
            $setData['update']['sb_status'] = 1;
            $this->setValue($setData);

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }

    public function getLadingBill($data){
        $queryData = array();
        $queryData['tableName'] = $this->ladingBill;
        $queryData['select'] = "bill_of_lading.*,trans_main.trans_number,trans_main.trans_date,trans_main.doc_no,trans_main.doc_date,trans_main.party_name,trans_main.currency,trans_main.net_amount,JSON_UNQUOTE(JSON_EXTRACT(trans_main.extra_fields, '$.country_of_final_destonation')),JSON_UNQUOTE(JSON_EXTRACT(trans_main.extra_fields, '$.port_of_loading')) AS port_of_loading,JSON_UNQUOTE(JSON_EXTRACT(trans_main.extra_fields, '$.port_of_discharge')) AS port_of_discharge";

        $queryData['leftJoin']['trans_main'] = "trans_main.id = bill_of_lading.com_inv_id";

        $queryData['where']['bill_of_lading.id'] = $data['id'];

        $result = $this->row($queryData);
        return $result;
    }

    public function delete($id){
        try{
            $this->db->trans_begin();

            $dataRow = $this->getLadingBill(['id'=>$id]);

            $result = $this->trash($this->ladingBill,['id'=>$id],'Bill of Lading');

            $setData = array();
            $setData['tableName'] = $this->shippingBill;
            $setData['where']['id'] = $dataRow->sb_id;
            $setData['update']['sb_status'] = 0;
            $this->setValue($setData);

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function getExportIncentivesDTRows($data){
        $data['tableName'] = $this->ladingBill;

        $data['select'] = "bill_of_lading.*,trans_main.trans_number,trans_main.trans_date,trans_main.doc_no,trans_main.doc_date,shipping_bill.drawback_amount,shipping_bill.igst_amount,shipping_bill.rodtep_amount";

        $data['leftJoin']['trans_main'] = "trans_main.id = bill_of_lading.com_inv_id";
        $data['leftJoin']['shipping_bill'] = "shipping_bill.id = bill_of_lading.sb_id";

        $data['where']['bill_of_lading.bl_awb_date >='] = $this->startYearDate;
        $data['where']['bill_of_lading.bl_awb_date <='] = $this->endYearDate;

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "trans_main.doc_no";
        $data['searchCol'][] = "DATE_FORMAT(trans_main.doc_date,'%d-%m-%Y')";
        $data['searchCol'][] = "shipping_bill.drawback_amount";
        $data['searchCol'][] = "bill_of_lading.drawback_date";
        $data['searchCol'][] = "shipping_bill.igst_amount";
        $data['searchCol'][] = "bill_of_lading.igst_refund_date";
        $data['searchCol'][] = "bill_of_lading.igst_refund_error";
        $data['searchCol'][] = "shipping_bill.rodtep_amount";
        $data['searchCol'][] = "bill_of_lading.rodtep_date";

        $columns = array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function saveExportIncentives($data){
        try{
            $this->db->trans_begin();

            $result = $this->store($this->ladingBill,$data,'Export Incentives');

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