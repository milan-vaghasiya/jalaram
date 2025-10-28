<?php
class RemittanceTransferModel extends MasterModel{
    private $swiftRemittance = "swift_remittance";
    private $remittanceTrans = "remittance_transfer";

    public function getDTRows($data){
        if($data['status'] == 0):
            $data['tableName'] = $this->swiftRemittance;

            $data['select'] = "swift_remittance.*";
            
            $data['where']['(firc_amount - transfer_amount) > '] = 0;
            $data['where']['remittance_date <='] = $this->endYearDate;
    
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "firc_number";
            $data['searchCol'][] = "DATE_FORMAT(remittance_date,'%d-%m-%Y')";
            $data['searchCol'][] = "remitter_name";
            $data['searchCol'][] = "remitter_country";
            $data['searchCol'][] = "swift_currency";
            $data['searchCol'][] = "swift_amount";
            $data['searchCol'][] = "firc_amount";
            $data['searchCol'][] = "swift_remark";
        else:
            $data['tableName'] = $this->remittanceTrans;

            $data['select'] = "remittance_transfer.*,swift_remittance.firc_number,swift_remittance.remittance_date,swift_remittance.remitter_name,swift_remittance.swift_currency,swift_remittance.firc_amount,swift_remittance.settled_amount,IFNULL(adj_trans.net_credit_inr_adj,0) as net_credit_inr_adj";

            $data['leftJoin']['swift_remittance'] = "swift_remittance.id = remittance_transfer.swift_id";
            $data['leftJoin']['(SELECT swift_id, SUM(firc_transfer_adj) as firc_transfer_adj, SUM(net_credit_inr_adj) as net_credit_inr_adj FROM remittance_transfer WHERE is_delete = 0 AND entry_type = 4 GROUP BY swift_id) as adj_trans'] = "remittance_transfer.id = adj_trans.swift_id";

            $data['where']['remittance_transfer.entry_type'] = 1;
            $data['where']['swift_remittance.remittance_date >='] = $this->startYearDate;
            $data['where']['swift_remittance.remittance_date <='] = $this->endYearDate;

            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "swift_remittance.firc_number";
            $data['searchCol'][] = "DATE_FORMAT(swift_remittance.remittance_date,'%d-%m-%Y')";
            $data['searchCol'][] = "swift_remittance.remitter_name";
            $data['searchCol'][] = "swift_remittance.swift_currency";
            $data['searchCol'][] = "swift_remittance.firc_amount";
            $data['searchCol'][] = "remittance_transfer.trans_ref_no";
            $data['searchCol'][] = "DATE_FORMAT(remittance_transfer.trans_date,'%d-%m-%Y')";
            $data['searchCol'][] = "remittance_transfer.firc_transfer";
            $data['searchCol'][] = "remittance_transfer.net_credit_inr";
        endif;

        $columns = array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function getRemittanceTransactions($data){
        $queryData = [];
        $queryData['tableName'] = $this->remittanceTrans;
        $queryData['where']['swift_id'] = $data['id'];
        $queryData['where']['entry_type'] = 1;
        $result = $this->rows($queryData);
        return $result;
    }

    public function save($data){
        try{
            $this->db->trans_begin();
            
            $this->delete($data['swift_id']);       

            foreach($data['itemData'] as $row):
                $row['created_by'] = $this->loginId;
                $row['is_delete'] = 0;
                $result = $this->store($this->remittanceTrans,$row,'Remittance Transfer');

                //update new transfer amount
                $setData = array();
                $setData['tableName'] = $this->swiftRemittance;
                $setData['where']['id'] = $row['swift_id'];
                $setData['set']['transfer_amount'] = 'transfer_amount, + '.$row['firc_transfer'];
                $this->setValue($setData);
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

            //update transfer amount
            $setData = array();
            $setData['tableName'] = $this->swiftRemittance;
            $setData['where']['id'] = $id;
            $setData['update']['transfer_amount'] = 0;
            $this->setValue($setData);

            //remove remittance transaction
            $result = $this->trash($this->remittanceTrans,['swift_id'=>$id,'entry_type'=>1],'Remittance Transfer');

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