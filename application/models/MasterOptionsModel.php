<?php
class MasterOptionsModel extends MasterModel{
    private $masterOptions = "master_options";
    private $currency = "currency";
    private $auto_mail = "auto_mail";

	public function save($data){ 
        try{
            $this->db->trans_begin();
            $result = $this->store($this->masterOptions,$data,'Master Options');
            
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function saveGradeName($gradeName){
        try{
            $this->db->trans_begin();
            $material_grade = explode(',', $this->getMasterOptions()->material_grade);
            if(!in_array($gradeName, $material_grade))
            {
                $material_grade[] = $gradeName;
                $this->store($this->masterOptions,['id'=>1,'material_grade'=>implode(',', $material_grade)],'Master Options');
                $result = $gradeName;
            }
            $result = '';
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }
    
    public function saveInstUsed($inst_used){
        try{
            $this->db->trans_begin();
            $ins_instruments = explode(',', $this->getMasterOptions()->ins_instruments);
            if(!in_array($inst_used, $ins_instruments))
            {
                $ins_instruments[] = $inst_used;
                $this->store($this->masterOptions,['id'=>1,'ins_instruments'=>implode(',', $ins_instruments)],'Master Options');
                $result = $inst_used;
            }
            $result = '';
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function getCurrencyRows($data){
        $data['tableName'] = $this->currency;
        $data['searchCol'][] = "currency_name";
        $data['searchCol'][] = "currency";
        $data['searchCol'][] = "code2000";
        $data['searchCol'][] = "inrrate";

        $columns =array('','currency_name','currency','code2000','inrrate');

       if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
       return $this->pagingRows($data);    
	}

	public function saveCurrency($data){
		try{
        $this->db->trans_begin();
		foreach($data['id'] as $key=>$value):
		   $cData = ['id'=>$value,'inrrate'=>$data['inrrate'][$key]];
		   $result = $this->store($this->currency,$cData,'Currency');
		endforeach;
		if ($this->db->trans_status() !== FALSE):
			$this->db->trans_commit();
			return $result;
		endif;
		}catch(\Exception $e){
			$this->db->trans_rollback();
		   return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
		}	
   }
   
   /* Created at : 09-12-2021 [Milan Chauhan] */
   public function getMaterialGrades(){
       $queryData = array();
       $queryData['tableName'] = $this->masterOptions;
       $queryData['select'] = "material_grade";
       $queryData['where']['id'] = 1;
       $result = $this->row($queryData);
       $result = (!empty($result))?explode(",",$result->material_grade):array();
       return (object)$result;
   }
   
    //Created By Meghavi
    public function getAutomailRows($data){
        $data['tableName'] = $this->auto_mail;
        $data['searchCol'][] = "name";
        $data['searchCol'][] = "mail_id";
        $columns =array('','name','0');

        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);    
    }

    public function saveAutoMail($data){
		try{
        $this->db->trans_begin();
		foreach($data['id'] as $key=>$value):
		   $cData = ['id'=>$value,'mail_id'=>$data['mail_id'][$key],'updated_by'=>$data['updated_by']];
		   $result = $this->store($this->auto_mail,$cData,'Auto Mail');
		endforeach;
		if ($this->db->trans_status() !== FALSE):
			$this->db->trans_commit();
			return $result;
		endif;
		}catch(\Exception $e){
			$this->db->trans_rollback();
		   return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
		}	
   }

	/*  Create By : Avruti @26-11-2021 5:00 PM
		update by : 
		note : 
	*/

     //---------------- API Code Start ------//

     public function getCount(){
        $data['tableName'] = $this->currency;
        return $this->numRows($data);
    }

    public function getCurrencyList_api($limit, $start){
        $data['tableName'] = $this->currency;
        $data['length'] = $limit;
        $data['start'] = $start;
        return $this->rows($data);
    }

    //------ API Code End -------//
}
?>