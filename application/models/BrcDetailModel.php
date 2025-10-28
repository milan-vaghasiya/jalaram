<?php
class BrcDetailModel extends MasterModel{
    private $shippingBill = "shipping_bill";
    private $swiftRemittance = "swift_remittance";
    private $remittanceTrans = "remittance_transfer";

    public function getDTRows($data){        
        $data['tableName'] = $this->remittanceTrans;

        $data['select'] = "remittance_transfer.id,shipping_bill.sb_number,shipping_bill.sb_date,shipping_bill.port_code,trans_main.currency,shipping_bill.sb_amount,trans_main.doc_no,swift_remittance.firc_number,swift_remittance.swift_currency,remittance_transfer.settled_fc as mapped_firc_amount,remittance_transfer.brc_number,remittance_transfer.brc_date,shipping_bill.req_ref_no,shipping_bill.bank_bill_id";

        $data['leftJoin']['swift_remittance'] = "swift_remittance.id = remittance_transfer.swift_id";
        $data['leftJoin']['shipping_bill'] = "shipping_bill.id = remittance_transfer.bl_id";
        $data['leftJoin']['trans_main'] = "trans_main.id = shipping_bill.com_inv_id";
        
        $data['where']['remittance_transfer.entry_type'] = 3;

        if($data['status'] == 0):
            $data['where']['remittance_transfer.brc_status'] = 0;
            $data['where']['shipping_bill.sb_date <='] = $this->endYearDate;
        else:
            $data['where']['remittance_transfer.brc_status'] = 1;
            $data['where']['shipping_bill.sb_date >='] = $this->startYearDate;
            $data['where']['shipping_bill.sb_date <='] = $this->endYearDate;
        endif;

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "shipping_bill.sb_number";
        $data['searchCol'][] = "DATE_FORMAT(shipping_bill.sb_date,'%d-%m-%Y')";
        $data['searchCol'][] = "shipping_bill.port_code";
        $data['searchCol'][] = "trans_main.currency";
        $data['searchCol'][] = "shipping_bill.sb_amount";
        $data['searchCol'][] = "trans_main.doc_no";
        $data['searchCol'][] = "swift_remittance.firc_number";
        $data['searchCol'][] = "swift_remittance.swift_currency";
        $data['searchCol'][] = "remittance_transfer.settled_fc";
        $data['searchCol'][] = "remittance_transfer.brc_number";
        $data['searchCol'][] = "remittance_transfer.brc_date";
        $data['searchCol'][] = "shipping_bill.req_ref_no";
        $data['searchCol'][] = "shipping_bill.bank_bill_id";

        $columns = array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function getBRCDetail($data){
        $queryData['tableName'] = $this->remittanceTrans;

        $queryData['select'] = "remittance_transfer.id,shipping_bill.sb_number,shipping_bill.sb_date,shipping_bill.port_code,trans_main.currency,shipping_bill.sb_amount,trans_main.doc_no,swift_remittance.firc_number,swift_remittance.swift_currency,swift_remittance.mapped_firc_amount,remittance_transfer.brc_number,remittance_transfer.brc_date,shipping_bill.req_ref_no,shipping_bill.bank_bill_id";

        $queryData['leftJoin']['swift_remittance'] = "swift_remittance.id = remittance_transfer.swift_id";
        $queryData['leftJoin']['shipping_bill'] = "shipping_bill.id = remittance_transfer.bl_id";
        $queryData['leftJoin']['trans_main'] = "trans_main.id = shipping_bill.com_inv_id";

        $queryData['where']['remittance_transfer.id'] = $data['id'];

        $result = $this->row($queryData);
        return $result;
    }

    public function save($data){
        try{
            $this->db->trans_begin();

            if(!empty($data['brc_number'])):
                if($this->checkDuplicate($data) > 0):
                    $errorMessage['brc_number'] =  "BRC Number is duplicate.";
                    return ['status'=>0,'message'=>$errorMessage];
                endif;
            endif;

            $data['brc_date'] = (!empty($data['brc_date']))?$data['brc_date']:NULL;
            $result = $this->store($this->remittanceTrans,$data,'BRC Detail');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }

    public function checkDuplicate($data){
        $queryData = array();
        $queryData['tableName'] = $this->remittanceTrans;
        $queryData['where']['entry_type'] = 3;

        if(!empty($data['brc_number']))
            $queryData['where']['brc_number'] = $data['brc_number'];

        if(!empty($data['id']))
            $queryData['where']['id != '] = $data['id'];

        $result = $this->numRows($queryData);
        return $result;
    }
}
?>