<?php
class MasterDetailModel extends MasterModel{
    private $masterDetail = "master_detail";
   
    
    public function getDTRows($data,$type){
        $data['tableName'] = $this->masterDetail;
        if($data['type'] == 1){$data['where']['type'] = 1;}
        if($data['type'] == 2){$data['where']['type'] = 2;}
        if($data['type'] == 3){$data['where']['type'] = 3;}
        if($data['type'] == 4){$data['where']['type'] = 4;}
        if($data['type'] == 5){$data['where']['type'] = 5;}
        if($data['type'] == 6){$data['where']['type'] = 6;}
        if($data['type'] == 7){$data['where']['type'] = 7;}
        $data['where']['type'] = $type;
		$data['searchCol'][] = "title";
        $data['searchCol'][] = "typeName";
        $data['searchCol'][] = "remark";
		$columns =array('','','title','typeName','remark');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getMasterDetail($id){
        $data['where']['id'] = $id;
        $data['tableName'] = $this->masterDetail;
        return $this->row($data);
    }

    public function save($data){
    
        return $this->store($this->masterDetail,$data,'Master Detail');
        
    }

    public function delete($id){
        return $this->trash($this->masterDetail,['id'=>$id],'Master Detail');
    }

    public function getTypeforItem($type){
        $data['tableName'] = $this->masterDetail;
		$data['where']['type'] = $type;        
        return $this->rows($data);
    }
    public function getMasterTypeList($type,$party_id=""){
        $data['tableName'] = $this->masterDetail;
        $data['where']['type'] = $type;
        if(!empty($party_id)){$data['where']['party_id'] = $party_id;}
		if($type==4){$data['order_by']['title'] = 'ASC';}
        return $this->rows($data);
    }
    
    public function getMasterDocsList($ids){
        $data['tableName'] = $this->masterDetail;
        if(!empty($ids)){$data['where_in']['master_detail.id'] = $ids;}
        else{$data['where']['master_detail.id'] = 0;}
        return $this->rows($data);
    }
    
    /*
        Created By :- Sweta @12-08-2023
        Used At :- Products/reviseCheckPoint
    */
    public function getRevChPointList(){
        $data['tableName'] = $this->masterDetail;
        $data['where']['type'] = 8;
        return $this->rows($data);
    }
}
?>