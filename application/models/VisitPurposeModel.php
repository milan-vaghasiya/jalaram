
<?php
class VisitPurposeModel extends MasterModel{
    private $masterdetail = "master_detail";
	
    public function getDTRows($data){
        $data['tableName'] = $this->masterdetail;
        $data['where']['type'] = 9;
        $data['searchCol'][] = "title";
		$columns =array('','','title','');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function getVisitPurpose($id){
        $data['where']['id'] = $id;
        $data['tableName'] = $this->masterdetail;
        return $this->row($data);
    }

    public function save($data){
        return $this->store($this->masterdetail,$data,'Visit Purpose');
    }

    public function delete($id){
        return $this->trash($this->masterdetail,['id'=>$id],'Visit Purpose');
    }
	
}
?>