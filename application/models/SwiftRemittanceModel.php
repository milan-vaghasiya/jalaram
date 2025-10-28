<?php
class SwiftRemittanceModel extends MasterModel{
    private $swiftRemittance = "swift_remittance";

    public function getDTRows($data){
        $data['tableName'] = $this->swiftRemittance;
        $data['select'] = "swift_remittance.*";

        $data['where']['remittance_date >='] = $this->startYearDate;
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

        $columns = array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();

            if($this->checkDuplicate($data) > 0):
				$errorMessage['firc_number'] =  "FIRC No. is duplicate.";
				return ['status'=>0,'message'=>$errorMessage];
			endif;

            $result = $this->store($this->swiftRemittance,$data,'Swift Remittance');

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
        $queryData['tableName'] = $this->swiftRemittance;
        $queryData['where']['firc_number'] = $data['firc_number'];

        if(!empty($data['id']))
            $queryData['where']['id != '] = $data['id'];

        $result = $this->numRows($queryData);
        return $result;
    }

    public function getSwiftRemittance($data){
        $queryData = array();
        $queryData['tableName'] = $this->swiftRemittance;
        $queryData['where']['id'] = $data['id'];
        $result = $this->row($queryData);
        return $result;
    }

    public function getUnsetlledSwifts($data = array()){
        $queryData['tableName'] = $this->swiftRemittance;
        $queryData['where']['(swift_amount - settled_amount) > '] = 0;
        $result = $this->rows($queryData);
        return $result;
    }

    public function getUnmappedSwifts($data = array()){
        $queryData['tableName'] = $this->swiftRemittance;
        $queryData['where']['(firc_amount - mapped_firc_amount) > '] = 0;
        $result = $this->rows($queryData);
        return $result;
    }

    public function delete($id){
        try{
            $this->db->trans_begin();

            $dataRow = $this->getSwiftRemittance(['id'=>$id]);

            if(!empty(floatval($dataRow->transfer_amount))):
                return ['status'=>0,'message'=>'Remittance Transfer transactions found. You cannot delete swift remittance.'];
            endif;

            $result = $this->trash($this->swiftRemittance,['id'=>$id],'Swift Remittance');

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