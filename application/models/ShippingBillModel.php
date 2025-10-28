<?php
class ShippingBillModel extends MasterModel{
    private $shippingBill = "shipping_bill";
    private $transMain = "trans_main";

    public function getDTRows($data){
        if($data['status'] == 0):
            $data['tableName'] = $this->transMain;

            $data['select'] = "id,trans_number,trans_date,doc_no,doc_date,party_name,JSON_UNQUOTE(JSON_EXTRACT(extra_fields, '$.country_of_final_destonation')) AS country_of_final_destonation,JSON_UNQUOTE(JSON_EXTRACT(extra_fields, '$.port_of_loading')) AS port_of_loading,JSON_UNQUOTE(JSON_EXTRACT(extra_fields, '$.port_of_discharge')) AS port_of_discharge,currency,net_amount";

            $data['where']['vou_acc_id'] = 0;
            $data['where']['entry_type'] = 10;
            $data['where']['trans_date <='] = $this->endYearDate;

            $data['order_by']['trans_no'] = "ASC";

            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "doc_no";
            $data['searchCol'][] = "DATE_FORMAT(doc_date,'%d-%m-%Y')";
            $data['searchCol'][] = "party_name";
            $data['searchCol'][] = "JSON_UNQUOTE(JSON_EXTRACT(extra_fields, '$.country_of_final_destonation'))";
        else:
            $data['tableName'] = $this->shippingBill;

            $data['select'] = "shipping_bill.*,trans_main.trans_number,trans_main.trans_date,trans_main.doc_no,trans_main.doc_date,trans_main.party_name,trans_main.currency";

            $data['leftJoin']['trans_main'] = "trans_main.id = shipping_bill.com_inv_id";

            $data['where']['shipping_bill.sb_date >='] = $this->startYearDate;
            $data['where']['shipping_bill.sb_date <='] = $this->endYearDate;

            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "trans_main.doc_no";
            $data['searchCol'][] = "DATE_FORMAT(trans_main.doc_date,'%d-%m-%Y')";
            $data['searchCol'][] = "trans_main.party_name";
            $data['searchCol'][] = "trans_main.currency";
            $data['searchCol'][] = "shipping_bill.sb_amount";
            $data['searchCol'][] = "shipping_bill.port_code";
            $data['searchCol'][] = "shipping_bill.sb_number";
            $data['searchCol'][] = "DATE_FORMAT(shipping_bill.sb_date,'%d-%m-%Y')";
        endif;

        $columns = array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();

            $result = $this->store($this->shippingBill,$data,'Shipping Bill');

            //Commercial Invoice
            $setData = array();
            $setData['tableName'] = $this->transMain;
            $setData['where']['id'] = $data['com_inv_id'];
            $setData['update']['vou_acc_id'] = $result['id'];
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

    public function getShippingBill($data){
        $queryData = array();
        $queryData['tableName'] = $this->shippingBill;
        $queryData['select'] = "shipping_bill.*,trans_main.trans_number,trans_main.trans_date,trans_main.doc_no,trans_main.doc_date,trans_main.party_name,trans_main.currency,JSON_UNQUOTE(JSON_EXTRACT(trans_main.extra_fields, '$.country_of_final_destonation')),JSON_UNQUOTE(JSON_EXTRACT(trans_main.extra_fields, '$.port_of_loading')) AS port_of_loading,JSON_UNQUOTE(JSON_EXTRACT(trans_main.extra_fields, '$.port_of_discharge')) AS port_of_discharge";

        $queryData['leftJoin']['trans_main'] = "trans_main.id = shipping_bill.com_inv_id";

        $queryData['where']['shipping_bill.id'] = $data['id'];

        $result = $this->row($queryData);
        return $result;
    }

    public function delete($id){
        try{
            $this->db->trans_begin();

            $dataRow = $this->getShippingBill(['id'=>$id]);

            $result = $this->trash($this->shippingBill,['id'=>$id],'Shipping Bill');

            //Commercial Invoice
            $setData = array();
            $setData['tableName'] = $this->transMain;
            $setData['where']['id'] = $dataRow->com_inv_id;
            $setData['update']['vou_acc_id'] = 0;
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
}
?>