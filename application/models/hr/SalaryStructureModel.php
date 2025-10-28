<?php
class SalaryStructureModel extends MasterModel{
    private $salaryHeads = "salary_heads";
    private $ctcFormat = "ctc_format";


    public function getDTRows($data){
        $data['tableName'] = $this->ctcFormat;
        
        $data['searchCol'][] = "format_name";
        $data['searchCol'][] = "format_no";
        $data['searchCol'][] = "salary_duration";
        $data['searchCol'][] = "pf_status";
        $data['searchCol'][] = "pf_per";
        $data['searchCol'][] = "gratuity_days";
        $data['searchCol'][] = "gratuity_per";
        $data['searchCol'][] = "effect_from";
        
		$columns =array('','','format_name','format_no','salary_duration','pf_per','gratuity_days','gratuity_per','effect_from');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }
    
    public function nextFormatNo(){
        $data['select'] = "MAX(format_no) as format_no";
        $data['tableName'] = $this->ctcFormat;
		$format_no = $this->specificRow($data)->format_no;
		$formatNo = (!empty($format_no))?($format_no + 1):1;
		return $formatNo;
    }
    
    public function getCtcFormat($id){
        $data['tableName'] = $this->ctcFormat;
		$data['where']['id'] = $id;
        return $this->row($data);
    }
    
    public function save($data){
        try {
            $this->db->trans_begin();
            $data['format_no'] = $this->nextFormatNo();
            $result = $this->store($this->ctcFormat,$data,'CTC Structure');
            
            if ($this->db->trans_status() !== FALSE) : 
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }
    
    public function checkDuplicateCtcFormat($format_name,$id){
        $data['tableName'] = $this->ctcFormat;
        $data['where']['format_name'] = trim($format_name);
        if(!empty($id))
            $data['where']['id !='] = $id;
        return $this->numRows($data);
    }
    
    public function delete($id){
        return $this->trash($this->ctcFormat,['id'=>$id],'CTC Structure');
    }


    

    public function getsalaryStructure($id){
        $data['tableName'] = $this->salaryHeads;
        $data['where']['ctc_id'] = $id;
        return $this->rows($data);
    }
    
    public function getsalaryStructureById($id){
        $data['tableName'] = $this->salaryHeads;
        $data['where']['id'] = $id;
        return $this->row($data);
    }
    
    public function deleteSalaryStructure($id){
        return $this->trash($this->salaryHeads,['id'=>$id],'Salary Structure');
    }
    
    public function saveSalaryStructure($data){    
        try {
            $this->db->trans_begin();
            
            $result = $this->store($this->salaryHeads,$data,'Salary Structure');
            
            if ($this->db->trans_status() !== FALSE) : 
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }
    
    public function checkDuplicateSalaryStructure($head_name,$id,$ctc_id){
        $data['tableName'] = $this->salaryHeads;
        $data['where']['head_name'] = trim($head_name);
        $data['where']['ctc_id'] = $ctc_id;
        if(!empty($id))
            $data['where']['id !='] = $id;
        return $this->numRows($data);
    }
    

    
    public function getCtc($postData = []){
        $data['tableName'] = $this->ctcFormat;
        $data['select'] = 'ctc_format.*,salary_heads.head_name,salary_heads.parent_head,salary_heads.type,salary_heads.cal_type';
		if(!empty($postData['format_name'])){$data['where']['format_name'] = $postData['format_name'];}
        $data['leftJoin']['salary_heads'] = 'ctc_format.id = salary_heads.ctc_id';
        return $this->rows($data);
    }
    

    /* Created At : 23-11-2022 [Milan Chauhan] */
    public function getCtcFormats(){
        $data['tableName'] = $this->ctcFormat;
        return $this->rows($data);
    }

    /* Created At : 23-11-2022 [Milan Chauhan] */
    public function getCtcFromat($id){
        $data['tableName'] = $this->ctcFormat;
        $data['where']['id'] = $id;
        return $this->row($data);
    }

    /* Created At : 23-11-2022 [Milan Chauhan] */
    public function getSalaryHeadsOnCtcFormat($formate_id){
        $data['tableName'] = $this->salaryHeads;
        $data['where']['ctc_id'] = $formate_id;
        return $this->rows($data);
    }

    /* Created At : 23-11-2022 [Milan Chauhan] */
    public function getProfessionTaxBaseOnGrossSalary($grossSalary){
        $queryData['tableName'] = "professional_tax";
        $queryData['where']['min_val <='] = $grossSalary;
        return $this->row($queryData);
    }

}
?>