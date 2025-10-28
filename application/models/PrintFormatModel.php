<?php
class PrintFormatModel extends MasterModel{
    private $printFormat = "print_format";

    public function getDTRows($data){
        $data['tableName'] = $this->printFormat;
        $data['searchCol'][] = "formate_name";
        $data['serachCol'][] = "remark";
        $columns =array('','','formate_name','remark');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function getAllPrintFormats(){
        $data['tableName'] = $this->printFormat;
        return $this->rows($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();

            $result = $this->store($this->printFormat,$data,'Print Format');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
           return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function getPrintFormat($id){
        $queryData = array();
        $queryData['tableName'] = $this->printFormat;
        $queryData['where']['id'] = $id;
        return $this->row($queryData);
    }

    public function delete($id){
        try{
            $this->db->trans_begin();

            $result = $this->trash($this->printFormat,['id'=>$id],'Print Format');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }
}
?>