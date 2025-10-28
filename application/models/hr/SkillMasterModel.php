<?php
class SkillMasterModel extends MasterModel{
    private $skillMaster = "skill_master";
	
    public function getDTRows($data){
        $data['tableName'] = $this->skillMaster;
        $data['select'] = "skill_master.*,department_master.name";
        $data['leftJoin']['department_master'] = "department_master.id = skill_master.dept_id";
       
        $data['searchCol'][] = "department_master.name";
        $data['serachCol'][] = "skill_master.skill";
		$columns =array('','','department_master.name','','skill_master.skill');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function getSkillMaster($id){
        $data['tableName'] = $this->skillMaster;
        $data['where']['id'] = $id;
        return $this->row($data);
    }

    public function save($data){
        return $this->store($this->skillMaster,$data,'skillMaster');
    }

    public function delete($id){
        return $this->trash($this->skillMaster,['id'=>$id],'skillMaster');
    }

    public function getDeptWiseSkill($id){
		$data['tableName'] = $this->skillMaster; 
		$data['where']['dept_id'] = $id;
		return $this->rows($data);
	}
}
?>