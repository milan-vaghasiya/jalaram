<?php
class DepartmentModel extends MasterModel{
    private $departmentMaster = "department_master";
    private $empMaster = "employee_master";
    
	public function getDTRows($data){
        $data['tableName'] = $this->departmentMaster;
        $data['searchCol'][] = "name";
		$columns =array('','','name','');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }
    
	public function getDepartmentList(){
        $data['tableName'] = $this->departmentMaster;
        return $this->rows($data);
    }
    
	public function getDepartmentForVisitor(){		
		$data['tableName'] = 'employee_master';
        $data['select'] = "department_master.*";
        $data['leftJoin']['department_master'] = "employee_master.emp_dept_id = department_master.id";
        $data['where']['employee_master.allowed_visitors'] = 1;
		return $this->rows($data);
    }

    public function getDepartment($id){
        $data['where']['id'] = $id;
        $data['tableName'] = $this->departmentMaster;
        return $this->row($data);
    }

    public function getEmployees($id=0){
		if(!empty($id))
		{
			$data['where']['id'] = $id;
			$data['tableName'] = $this->empMaster;
			return $this->row($data);
		}
		else
		{
			$data['order_by']['emp_name']='ASC';
			$data['tableName'] = $this->empMaster;
			return $this->rows($data);
		}
    }
	
    public function getLeaveAuthorities($emp_ids){
        $data['select'] = 'emp_name';
        $data['where_in']['id'] = $emp_ids;
        $data['tableName'] = $this->empMaster;
		$data['resultType']='resultRows';
        return $this->specificRow($data);
    }
	
    public function getLeaveAuthority($emp_id){
        $data['select'] = 'emp_name';
        $data['where']['id'] = $emp_id;
        $data['tableName'] = $this->empMaster;
        return $this->specificRow($data);
    }
	
    public function save($data){
        try{
            $this->db->trans_begin();
        if($this->checkDuplicate($data['name'],$data['id']) > 0):
            $errorMessage['name'] = "Department name is duplicate.";
            $result = ['status'=>0,'message'=>$errorMessage];
        else:
            $result = $this->store($this->departmentMaster,$data,'Department');
            /** Department added in store */
            $dept_id = !empty($data['id']) ? $data['id'] : $result['insert_id'];
            $strQuery['where']['ref_id'] = $dept_id;
            $strQuery['where']['store_type'] = 15;
            $strQuery['tableName'] = 'location_master';
            $strResult = $this->row($strQuery);
            if (empty($strResult)) {
                    $storeData = [
                        'id' => '',
                        'store_name' => "Department",
                        'location' => $data['name'],
                        'store_type' => 15,
                        'ref_id' => $dept_id
                    ];
                    $this->store('location_master',$storeData);
            }else{
                $storeData = [
                    'id' => $strResult->id,
                    'location' => $data['name'],
                ];
                $this->store('location_master',$storeData);
            }
        endif;
        if ($this->db->trans_status() !== FALSE):
            $this->db->trans_commit();
            return $result;
        endif;
    }catch(\Exception $e){
        $this->db->trans_rollback();
       return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
    }	
    }

    public function checkDuplicate($name,$id=""){
        $data['tableName'] = $this->departmentMaster;
        $data['where']['name'] = $name;
        
        if(!empty($id))
            $data['where']['id !='] = $id;
        return $this->numRows($data);
    }

    public function delete($id){
        try{
            $this->db->trans_begin();
        $result = $this->trash($this->departmentMaster,['id'=>$id],'Department');
        if ($this->db->trans_status() !== FALSE):
            $this->db->trans_commit();
            return $result;
        endif;
    }catch(\Exception $e){
        $this->db->trans_rollback();
       return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
    }	
    }

    public function getMachiningDepartment($category){
        $data['where']['category'] = $category;
        $data['tableName'] = $this->departmentMaster;
        return $this->rows($data);
    }
	
	/*  Create By : Avruti @26-11-2021 5:00 PM
		update by : 
		note : 
	*/

     //---------------- API Code Start ------//

     public function getCount(){
        $data['tableName'] = $this->departmentMaster;
        return $this->numRows($data);
    }

    public function getDepartmentList_api($limit, $start){
        $data['tableName'] = $this->departmentMaster;
        $data['length'] = $limit;
        $data['start'] = $start;
        return $this->rows($data);
    }

    //------ API Code End -------//
}
?>