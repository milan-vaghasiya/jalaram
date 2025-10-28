<?php
class DropdownModel extends MasterModel{
    private $dropdownTbl = "dropdown_master";

    public function getDTRows($data){
        $data['tableName'] = $this->dropdownTbl;
        $data['searchCol'][] = "type";
        $data['searchCol'][] = "description";
        $data['searchCol'][] = "remark";
        
		$columns =array('','','type','description','description');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getDropdownDetail($id){
        $data['tableName'] = $this->dropdownTbl;
        $data['where']['id'] = $id;
        return $this->row($data);
    }

    public function getDropdownList($postData=[]){
        $data['tableName'] = $this->dropdownTbl;
        if(!empty($postData['type'])){$data['where']['type'] = $postData['type'];}
        if(!empty($postData['id'])){$data['where_in']['id'] = $postData['id'];}
        return $this->rows($data);
    }

    public function save($data){
		return $this->store($this->dropdownTbl,$data);
	}

    public function delete($id){
		
        return $this->trash($this->dropdownTbl,['id'=>$id]);
    }
	
}