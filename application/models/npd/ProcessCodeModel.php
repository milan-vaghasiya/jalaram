<?php 
class ProcessCodeModel extends MasterModel{
	private $processcode = "cp_process_code";

	public function getDTRows($data){
		$data['tableName'] = $this->processcode;
		$data['select'] = "cp_process_code.*";

		
		$data['searchCol'][] = "";
		$data['searchCol'][] = "";
		$data['searchCol'][] = "is_gst_applicable";
		$data['searchCol'][] = "hsn_code";
		

		$columns =array('','','group_name','ledger_name','is_gst_applicable','hsn_code','opening_bal','cess_per');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
	}

	public function save($data){
		try{
            $this->db->trans_begin();
            
				$result = $this->store($this->processcode,$data,'process code');
			
			if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
        return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
	}


	public function getprocessCode($id){
        $data['where']['id'] = $id;
        $data['tableName'] = $this->processcode;
        return $this->row($data);
    }


	public function delete($id){
        try{
            $this->db->trans_begin();
            

            $result = $this->trash($this->processcode,['id'=>$id],'Process Code');

            if($this->db->trans_status() !== FALSE):
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