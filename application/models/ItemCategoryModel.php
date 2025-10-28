<?php
class ItemCategoryModel extends MasterModel{
    private $itemCategory = "item_category";
    
	public function getDTRows($data){
        $data['tableName'] = $this->itemCategory;
        $data['select'] = 'item_category.*, item_group.group_name';
        $data['join']['item_group'] = 'item_group.id = item_category.category_type';
		
        $data['searchCol'][] = "item_category.category_name";
        $data['searchCol'][] = "item_category.category_code";
        $data['searchCol'][] = "item_group.group_name";
        $data['searchCol'][] = "item_category.remark";
		
		$columns =array('','','category_name','category_code','group_name','remark');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getCategoryList($cat_type=""){
        $data['tableName'] = $this->itemCategory;
		if(!empty($cat_type)){ $data['where_in']['category_type'] = $cat_type; }
        return $this->rows($data);
    }

    public function getCategory($id){
        $data['where']['id'] = $id;
        $data['tableName'] = $this->itemCategory;
        return $this->row($data);
    }

    public function save($data){ 
        try{
            $this->db->trans_begin();
            
            if($this->checkDuplicate($data['category_name'],$data['id']) > 0):
                $errorMessage['category_name'] = "Category Name is duplicate.";
                $result = ['status'=>0,'message'=>$errorMessage];

            /*elseif($this->checkDuplicateCode($data['category_code'],$data['category_type'],$data['id']) > 0):
                $errorMessage['category_code'] = "Category Code is duplicate.";
                $result = ['status'=>0,'message'=>$errorMessage];*/
            else:
                $result = $this->store($this->itemCategory,$data,'Item Category');
                
                //Create Gauge & Instrument Item
                if($data['category_type'] == 6 || $data['category_type'] == 7):
                    if(empty($data['id'])):
                        $postData = [];
                        $postData['id'] = '';
                        $postData['item_code'] = $data['category_code'];
                        $postData['item_name'] = $data['category_name'];
                        $postData['item_type'] = $data['category_type'];
                        $postData['category_id'] = $result['insert_id'];
                        //$postData['gst_per'] = $data['gst_per'];
                        //$postData['hsn_code'] = $data['hsn_code'];
                        
                        $this->store('item_master',$postData,'');
                    else:
                        $postData = [];
                        $postData['item_code'] = $data['category_code'];
                        $postData['item_name'] = $data['category_name'];
                        $postData['item_type'] = $data['category_type'];
                        //$postData['gst_per'] = $data['gst_per'];
                        //$postData['hsn_code'] = $data['hsn_code'];
                        
                        $this->edit('item_master',['category_id'=>$data['id']],$postData);
                    endif;
                endif;
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
        $data['tableName'] = $this->itemCategory;
        $data['where']['category_name'] = $name;
        
        if(!empty($id))
            $data['where']['id !='] = $id;
        return $this->numRows($data);
    }

    public function checkDuplicateCode($code,$type,$id=""){
        $data['tableName'] = $this->itemCategory;
        $data['where']['category_code'] = $code;
        $data['where']['category_type'] = $type;

        if(!empty($id))
            $data['where']['id !='] = $id;
        $result = $this->numRows($data);
        return $result;
    }

    public function delete($id){
        try{
            $this->db->trans_begin();
            $result = $this->trash($this->itemCategory,['id'=>$id],'Item Category');
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
        $data['tableName'] = $this->itemCategory;
        return $this->numRows($data);
    }

    public function getItemCategoryList_api($limit, $start){
        $data['tableName'] = $this->itemCategory;
        $data['select'] = 'item_category.*, item_group.group_name';
        $data['join']['item_group'] = 'item_group.id = item_category.category_type';
        $data['length'] = $limit;
        $data['start'] = $start;
        return $this->rows($data);
    }

    //------ API Code End -------//
}
?>