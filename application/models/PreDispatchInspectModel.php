<?php
class PreDispatchInspectModel extends MasterModel{
    private $preDispatch = "predispatch_inspection";
	
    public function getDTRows($data){
        $data['tableName'] = $this->preDispatch;
        $data['select'] = "predispatch_inspection.*,item_master.item_code";
		$data['join']['item_master'] = "item_master.id = predispatch_inspection.item_id";
        $data['where']['date >= '] = $this->startYearDate;
        $data['where']['date <= '] = $this->endYearDate;
		
        $data['searchCol'][] = "item_master.item_code";
        $data['searchCol'][] = "predispatch_inspection.param_count";
		
		$columns =array('','','item_master.item_code','predispatch_inspection.param_count');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function getPreInspection($id){
        $data['tableName'] = $this->preDispatch;
		$data['where']['id'] = $id;
        return $this->row($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();
        $result = $this->store($this->preDispatch,$data,'Predispatch Inspection');
        if ($this->db->trans_status() !== FALSE):
            $this->db->trans_commit();
            return $result;
        endif;
    }catch(\Exception $e){
        $this->db->trans_rollback();
       return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
    }	
    }

    public function delete($id){
        try{
            $this->db->trans_begin();
        $result = $this->trash($this->preDispatch,['id'=>$id],'Predispatch Inspection');
        if ($this->db->trans_status() !== FALSE):
            $this->db->trans_commit();
            return $result;
        endif;
    }catch(\Exception $e){
        $this->db->trans_rollback();
       return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
    }	
    }

    public function getPreInspectionForPrint($id){
        $data['tableName'] = $this->preDispatch;
        $data['select'] = "predispatch_inspection.*,item_master.item_code,item_master.item_name,item_master.part_no,party_master.party_name";
        $data['join']['item_master'] = "item_master.id = predispatch_inspection.item_id";
        $data['join']['party_master'] = "party_master.id = item_master.party_id";
		$data['where']['predispatch_inspection.id'] = $id;
        return $this->row($data);
    }
	
	/*  Create By : Avruti @29-11-2021 10:10 AM
    update by : 
    note : 
*/
    //---------------- API Code Start ------//

    public function getCount(){        
        $data['tableName'] = $this->preDispatch;
        return $this->numRows($data);
    }

    public function getPreDispatchInspectList_api($limit, $start){
        $data['tableName'] = $this->preDispatch;
        $data['select'] = "predispatch_inspection.*,item_master.item_code";
		$data['join']['item_master'] = "item_master.id = predispatch_inspection.item_id";
        $data['length'] = $limit;
        $data['start'] = $start;
        return $this->rows($data);
    }

    //------ API Code End -------//
}
?>