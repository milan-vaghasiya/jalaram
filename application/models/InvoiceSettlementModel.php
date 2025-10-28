<?php
class InvoiceSettlementModel extends MasterModel{
    private $ladingBill = "bill_of_lading";
    private $shippingBill = "shipping_bill";
    private $transMain = "trans_main";
    private $swiftRemittance = "swift_remittance";
    private $remittanceTrans = "remittance_transfer";

    public function getDTRows($data){
        if($data['status'] == 1):
            $data['tableName'] = $this->swiftRemittance;

            $data['select'] = "swift_remittance.*,(swift_amount - settled_amount) as balance_amount";
            
            $data['where']['(swift_amount - settled_amount) > '] = 0;
            $data['where']['remittance_date <='] = $this->endYearDate;
    
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "firc_number";
            $data['searchCol'][] = "DATE_FORMAT(remittance_date,'%d-%m-%Y')";
            $data['searchCol'][] = "remitter_name";
            $data['searchCol'][] = "swift_currency";
            $data['searchCol'][] = "swift_amount";
            //$data['searchCol'][] = "firc_amount";
            $data['searchCol'][] = "swift_remark";
            $data['searchCol'][] = "settled_amount";
            $data['searchCol'][] = "(swift_amount - settled_amount)";
        else:
            $data['tableName'] = $this->ladingBill;

            $data['select'] = "bill_of_lading.*,trans_main.trans_number,trans_main.trans_date,trans_main.doc_no,trans_main.doc_date,trans_main.party_name,trans_main.currency,trans_main.net_amount";

            $data['leftJoin']['trans_main'] = "trans_main.id = bill_of_lading.com_inv_id";

            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "trans_main.doc_no";
            $data['searchCol'][] = "DATE_FORMAT(trans_main.doc_date,'%d-%m-%Y')";
            $data['searchCol'][] = "trans_main.party_name";
            $data['searchCol'][] = "trans_main.currency";
            $data['searchCol'][] = "trans_main.net_amount";

            if($data['status'] == 0):
                $data['where']['(trans_main.net_amount - bill_of_lading.received_fc) >'] = 0;
                $data['where']['bill_of_lading.bl_awb_date <='] = $this->endYearDate;

                $data['searchCol'][] = "bill_of_lading.inco_terms";
                $data['searchCol'][] = "DATE_FORMAT(bill_of_lading.bl_awb_date,'%d-%m-%Y')";
                $data['searchCol'][] = "DATE_FORMAT(bill_of_lading.payment_due_date,'%d-%m-%Y')";
            else:
                $data['where']['(trans_main.net_amount - bill_of_lading.received_fc) ='] = 0;
                $data['where']['bill_of_lading.bl_awb_date >='] = $this->startYearDate;
                $data['where']['bill_of_lading.bl_awb_date <='] = $this->endYearDate;
            endif;            
        endif;

        $columns = array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();
            
            $itemData = $data['itemData'];unset($data['itemData']);
            $result = $this->store($this->ladingBill,$data,'Invoice Settlement');

            foreach($itemData as $row):
                if(!empty($row['settled_fc'])):
                    $row['trans_date'] = date("Y-m-d");
                    $row['created_by'] = $this->loginId;
                    $row['is_delete'] = 0;
                    $this->store($this->remittanceTrans,$row);

                    //update new Settlement amount
                    $setData = array();
                    $setData['tableName'] = $this->swiftRemittance;
                    $setData['where']['id'] = $row['swift_id'];
                    $setData['set']['settled_amount'] = 'settled_amount, + '.$row['settled_fc'];
                    $this->setValue($setData);
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

    public function getSettlementTransactions($data){
        $queryData['tableName'] = $this->remittanceTrans;
        $queryData['select'] = "swift_remittance.firc_number,swift_remittance.remittance_date,swift_remittance.remitter_name,swift_remittance.swift_currency,swift_remittance.swift_amount,swift_remittance.settled_amount,remittance_transfer.id,remittance_transfer.swift_id,remittance_transfer.settled_fc";

        $queryData['leftJoin']['swift_remittance'] = "swift_remittance.id = remittance_transfer.swift_id";

        $queryData['where']['remittance_transfer.bl_id'] = $data['bl_id'];
        $queryData['where']['remittance_transfer.entry_type'] = 2;
        $result = $this->rows($queryData);
        return $result;
    }

    public function delete($id){
        try{
            $this->db->trans_begin();

            $transactions = $this->getSettlementTransactions(['bl_id'=>$id]);

            foreach($transactions as $row):
                //update Settlement amount
                $setData = array();
                $setData['tableName'] = $this->swiftRemittance;
                $setData['where']['id'] = $row->swift_id;
                $setData['set']['settled_amount'] = 'settled_amount, - '.$row->settled_fc;
                $this->setValue($setData);

                $this->trash($this->remittanceTrans,['id'=>$row->id]);
            endforeach;

            $result = $this->store($this->ladingBill,['id'=>$id,'short_received_fc'=>0,'received_fc'=>0,'settlement_remark'=>""],'Invoice Settlement');
            $result['message'] = "Invoice Settlement removed successfully.";

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