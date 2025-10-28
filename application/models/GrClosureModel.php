<?php
class GrClosureModel extends MasterModel{
    private $shippingBill = "shipping_bill";
    private $transMain = "trans_main";
    private $swiftRemittance = "swift_remittance";
    private $remittanceTrans = "remittance_transfer";

    public function getDTRows($data){
        if($data['status'] == 1):
            $data['tableName'] = $this->swiftRemittance;

            $data['select'] = "swift_remittance.*,(firc_amount - mapped_firc_amount) as balance_amount";
            
            $data['where']['(firc_amount - mapped_firc_amount) > '] = 0;
            $data['where']['remittance_date <='] = $this->endYearDate;
    
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "firc_number";
            $data['searchCol'][] = "swift_currency";
            $data['searchCol'][] = "firc_amount";
            $data['searchCol'][] = "DATE_FORMAT(remittance_date,'%d-%m-%Y')";
            $data['searchCol'][] = "remitter_name";
            $data['searchCol'][] = "swift_remark";
            $data['searchCol'][] = "mapped_firc_amount";
            $data['searchCol'][] = "(firc_amount - mapped_firc_amount)";
        else:
            $data['tableName'] = $this->shippingBill;

            $data['select'] = "shipping_bill.*,trans_main.trans_number,trans_main.trans_date,trans_main.doc_no,trans_main.doc_date,trans_main.party_name,trans_main.currency";

            $data['leftJoin']['trans_main'] = "trans_main.id = shipping_bill.com_inv_id";

            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "shipping_bill.sb_number";
            $data['searchCol'][] = "DATE_FORMAT(shipping_bill.sb_date,'%d-%m-%Y')";
            $data['searchCol'][] = "shipping_bill.port_code";
            $data['searchCol'][] = "trans_main.currency";
            $data['searchCol'][] = "shipping_bill.sb_amount";
            $data['searchCol'][] = "trans_main.doc_no";
            $data['searchCol'][] = "DATE_FORMAT(trans_main.doc_date,'%d-%m-%Y')";
            $data['searchCol'][] = "trans_main.party_name";

            if($data['status'] == 0):
                $data['where']['shipping_bill.total_mapped_firc <='] = 0;
                $data['where']['shipping_bill.sb_date <='] = $this->endYearDate;
            else:
                $data['where']['shipping_bill.total_mapped_firc >'] = 0;
                $data['where']['shipping_bill.sb_date >='] = $this->startYearDate;
                $data['where']['shipping_bill.sb_date <='] = $this->endYearDate;
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

            $errorMessage = array();
            if($this->checkDuplicate(['id'=>$data['id'],'req_ref_no'=>$data['req_ref_no']]) > 0):
				$errorMessage['req_ref_no'] =  "Request Ref. No. is duplicate.";
			endif;

            if($this->checkDuplicate(['id'=>$data['id'],'bank_bill_id'=>$data['bank_bill_id']]) > 0):
				$errorMessage['bank_bill_id'] =  "Bank Bill Id is duplicate.";				
			endif;

            if(!empty($errorMessage)):
                return ['status'=>0,'message'=>$errorMessage];
            endif;

            $result = $this->store($this->shippingBill,$data,'GR Closure');

            foreach($itemData as $row):
                if(floatval($row['settled_fc']) > 0):
                    $row['trans_date'] = date("Y-m-d");
                    $row['created_by'] = $this->loginId;
                    $row['is_delete'] = 0;
                    $this->store($this->remittanceTrans,$row);

                    //update new Settlement amount
                    $setData = array();
                    $setData['tableName'] = $this->swiftRemittance;
                    $setData['where']['id'] = $row['swift_id'];
                    $setData['set']['mapped_firc_amount'] = 'mapped_firc_amount, + '.$row['settled_fc'];
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

    public function checkDuplicate($data){
        $queryData = array();
        $queryData['tableName'] = $this->shippingBill;
        
        if(!empty($data['req_ref_no']))
            $queryData['where']['req_ref_no'] = $data['req_ref_no'];

        if(!empty($data['bank_bill_id']))
            $queryData['where']['bank_bill_id'] = $data['bank_bill_id'];

        if(!empty($data['id']))
            $queryData['where']['id != '] = $data['id'];

        $result = $this->numRows($queryData);
        return $result;
    }

    public function getMappedTransactions($data){
        $queryData['tableName'] = $this->remittanceTrans;
        $queryData['select'] = "swift_remittance.firc_number,swift_remittance.remittance_date,swift_remittance.remitter_name,swift_remittance.swift_currency,swift_remittance.firc_amount,swift_remittance.mapped_firc_amount,remittance_transfer.id,remittance_transfer.swift_id,remittance_transfer.settled_fc";

        $queryData['leftJoin']['swift_remittance'] = "swift_remittance.id = remittance_transfer.swift_id";

        $queryData['where']['remittance_transfer.bl_id'] = $data['bl_id'];
        $queryData['where']['remittance_transfer.entry_type'] = 3;
        $result = $this->rows($queryData);
        return $result;
    }

    public function delete($id){
        try{
            $this->db->trans_begin();

            $transactions = $this->getMappedTransactions(['bl_id'=>$id]);

            foreach($transactions as $row):
                //update Settlement amount
                $setData = array();
                $setData['tableName'] = $this->swiftRemittance;
                $setData['where']['id'] = $row->swift_id;
                $setData['set']['mapped_firc_amount'] = 'mapped_firc_amount, - '.$row->settled_fc;
                $this->setValue($setData);

                $this->trash($this->remittanceTrans,['id'=>$row->id]);
            endforeach;

            $result = $this->store($this->shippingBill,['id'=>$id,'total_mapped_firc'=>0,'req_ref_no'=>"",'bank_bill_id'=>""],'GR Closure');
            $result['message'] = "GR Closure unmapped successfully.";

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