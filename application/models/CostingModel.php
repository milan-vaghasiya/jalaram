<?php
class CostingModel extends MasterModel{
    private $costMaster = "costing_master";
    private $itemMaster = "item_master";
	private $productProcess = "product_process";

	/** OLD COSTING */
		/*  Create By : Meghavi @13-08-2022
			note : Costing
		*/
		public function getCostingDTRows($data){
			$data['tableName'] = $this->itemMaster;
			$data['select'] = "item_master.*,unit_master.unit_name,item_category.category_name,party_master.party_code";
			$data['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
			$data['leftJoin']['item_category'] = "item_category.id = item_master.category_id";
			$data['leftJoin']['party_master'] = "party_master.id = item_master.party_id";
			$data['where']['item_master.item_type'] = 1;
			
			$columns = array();
			$data['searchCol'][] = "";
			$data['searchCol'][] = "item_master.item_code";
			$data['searchCol'][] = "";
			$data['searchCol'][] = "";
			
			$columns =array('','item_master.item_name','','');
			if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
			return $this->pagingRows($data);
		}

		public function saveCosting($data){
			try{
				$this->db->trans_begin();
				
				$this->store('costing_master',$data,'');

				$result = ['status'=>1,'message'=>'Costing Data Updated successfully.'];
				if ($this->db->trans_status() !== FALSE):
					$this->db->trans_commit();
					return $result;
				endif;
			}catch(\Exception $e){
				$this->db->trans_rollback();
				return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
			}	
		}

		public function checkCostingStatus($id)
		{
			$result = new StdClass;$result->process=0; $result->cstng=0; 	
			
			$queryData = Array();
			$queryData['tableName'] = $this->productProcess;
			$queryData['where']['item_id'] = $id;
			$processData = $this->rows($queryData);
			$result->process=count($processData);
			
			$queryData = Array();
			$queryData['tableName'] = $this->productProcess;
			$queryData['where']['item_id'] = $id;
			$queryData['where']['costing !='] = '00:00:00';
			$fwData = $this->rows($queryData);
			$result->cstng=count($fwData);
			
			return $result;
		}

		public function getProductCost($item_id){
			$data['tableName'] = $this->costMaster;
			$data['where']['item_id'] = $item_id;
			return $this->row($data);
		}
	/** END OLD COSTING */

	/**** New Costing  05/07/2025 */
	public function getPrdCostDTRows($data){
        $data['tableName'] = 'product_costing';
        $data['select'] = "product_costing.*,trans_child.item_name,trans_child.item_code,trans_main.trans_number AS enq_number,trans_main.party_name";
        $data['leftJoin']['trans_child'] = "trans_child.id = product_costing.enq_id";
        $data['leftJoin']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        $data['where']['product_costing.active_revision'] = 1;
		if($data['status'] == 0){
			$data['where']['product_costing.approve_by'] = 0;
		}else{
			$data['where']['product_costing.approve_by >'] = 0;
		}
        $columns = array();
    	$data['searchCol'][] = "";
    	$data['searchCol'][] = "";
        $data['searchCol'][] = "trans_main.trans_number";
        $data['searchCol'][] = "trans_main.party_name";
        $data['searchCol'][] = "trans_child.item_code";
    	$data['searchCol'][] = "trans_child.item_name";
    	$data['searchCol'][] = "";
		
		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		$result = $this->pagingRows($data);
        return $result;
    }

	public function getCostingData($data){
		$queryData['tableName'] = 'product_costing';
		$queryData['select'] = "product_costing.*,material_master.material_grade,material_master.scrap_per,trans_child.item_name,trans_child.item_code,trans_main.trans_number AS enq_number,trans_main.party_name,trans_main.trans_date AS enq_date";
        $queryData['leftJoin']['material_master'] = "material_master.id = product_costing.grade_id";
		$queryData['leftJoin']['trans_child'] = "trans_child.id = product_costing.enq_id";
        $queryData['leftJoin']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        
		if(!empty($data['id'])){
			$queryData['where']['product_costing.id'] = $data['id'];
		}

		if(!empty($data['enq_id'])){
			$queryData['where']['product_costing.enq_id'] = $data['enq_id'];
		}

		if(!empty($data['item_id'])){
			$queryData['where']['product_costing.item_id'] = $data['item_id'];
		}

		if(!empty($data['active_revision'])){
			$queryData['where']['product_costing.active_revision'] = $data['active_revision'];
		}

		if(!empty($data['single_row'])){
			$result = $this->row($queryData);
		}else{
			$result = $this->rows($queryData);
		}
		return $result;
	}

	public function saveRequest($data){
		try{
			$this->db->trans_begin();
			
			$result = $this->store('product_costing',$data);

			if ($this->db->trans_status() !== FALSE):
				$this->db->trans_commit();
				return $result;
			endif;
		}catch(\Exception $e){
			$this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
		}	
	}

	public function getCostReqDTRows($data){
        $data['tableName'] = 'product_costing';
        $data['select'] = "product_costing.*,trans_child.item_name,trans_child.item_code,trans_main.trans_number AS enq_number,trans_main.party_name,material_master.material_grade";
        $data['leftJoin']['trans_child'] = "trans_child.id = product_costing.enq_id";
        $data['leftJoin']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        $data['leftJoin']['material_master'] = "material_master.id = product_costing.grade_id";
        $data['where']['product_costing.active_revision'] = 1;
		if($data['req_type'] == 'RM'){
			if($data['status'] == 0){
				$data['where']['product_costing.rm_rate <='] = 0;
			}else{
				$data['where']['product_costing.rm_rate >'] =0 ;
			}
			$data['where']['product_costing.rm_cost_request'] = 1;
		}elseif($data['req_type'] == 'MFG'){
			if($data['status'] == 0){
				$data['where']['product_costing.mfg_process_cost <='] = 0;
			}else{
				$data['where']['product_costing.mfg_process_cost >'] =0 ;
			}
			$data['where']['product_costing.mfg_cost_request'] = 1;
		}
		
        $columns = array();
		$data['searchCol'][] = "";
		$data['searchCol'][] = "";
		$data['searchCol'][] = "trans_main.trans_number";
		$data['searchCol'][] = "trans_main.party_name";
		$data['searchCol'][] = "trans_child.item_code";
		$data['searchCol'][] = "trans_child.item_name";
		$data['searchCol'][] = "material_master.material_grade";
		// $data['searchCol'][] = "product_costing.dimension";
		$data['searchCol'][] = "product_costing.moq";
		$data['searchCol'][] = "product_costing.gross_wt";
		$data['searchCol'][] = "product_costing.finish_wt";
		$data['searchCol'][] = "";
		
		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		$result = $this->pagingRows($data);
        return $result;
    }

	public function saveCostDetail($data){
		try{
			$this->db->trans_begin();

			$result = $this->store('product_costing',$data);
			if(empty($data['id'])){
				$processData = $this->getProcessCostingData(['cost_id'=>$data['ref_id']]);
				foreach($processData AS $row){
					$costData = [
						'id'=>'',
						'cost_id'=>$result['insert_id'],
						'process_id'=>$row->process_id,
						'mhr'=>$row->mhr,
						'cycle_time'=>$row->cycle_time,
						'process_cost'=>$row->process_cost,
					];
					$this->store("product_cost_trans",$costData);
				}

				if(!empty($data['ref_id'])){
					$this->store('product_costing',['id'=>$data['ref_id'],'active_revision'=>2]);
				}
			}
			if ($this->db->trans_status() !== FALSE):
				$this->db->trans_commit();
				return $result;
			endif;
		}catch(\Exception $e){
			$this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
		}	
	}

	public function saveMfgCost($data){
		try{
			$this->db->trans_begin();
			$data['id'] = '';
			$result = $this->store('product_cost_trans',$data);
			$this->updateMfgCost(['cost_id'=>$data['cost_id']]);
			if ($this->db->trans_status() !== FALSE):
				$this->db->trans_commit();
				return $result;
			endif;
		}catch(\Exception $e){
			$this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
		}	
	}

	public function updateMfgCost($data){
		try{
			$this->db->trans_begin();
			$queryData['tableName'] = 'product_cost_trans';
			$queryData['select'] = 'SUM(product_cost_trans.process_cost) AS total_mfg_cost';
			$queryData['where']['product_cost_trans.cost_id'] = $data['cost_id'];
			$costData = $this->row($queryData);

			$mfg_process_cost = ((!empty($costData->total_mfg_cost))?$costData->total_mfg_cost:0);

			$result = $this->store("product_costing",['id'=>$data['cost_id'],'mfg_process_cost'=>$mfg_process_cost]);

			if ($this->db->trans_status() !== FALSE):
				$this->db->trans_commit();
				return $result;
			endif;
		}catch(\Exception $e){
			$this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
		}
	}

	public function getProcessCostingData($data){
		$queryData['tableName'] = 'product_cost_trans';
		$queryData['select'] = "product_cost_trans.*,process_master.process_name,process_master.is_machining";
        $queryData['leftJoin']['process_master'] = "process_master.id = product_cost_trans.process_id";
        
		if(!empty($data['cost_id'])){
			$queryData['where']['product_cost_trans.cost_id'] = $data['cost_id'];
		}

		if(!empty($data['single_row'])){
			$result = $this->row($queryData);
		}else{
			$result = $this->rows($queryData);
		}
		return $result;
	}

	public function deleteMfgCost($data){
		try{
			$this->db->trans_begin();
			$result = $this->trash('product_cost_trans',['id'=>$data['id']]);
			$this->updateMfgCost(['cost_id'=>$data['cost_id']]);
			if ($this->db->trans_status() !== FALSE):
				$this->db->trans_commit();
				return $result;
			endif;
		}catch(\Exception $e){
			$this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
		}	
	}

	public function approveCost($data){
		try{
			$this->db->trans_begin();
			
			$result = $this->store('product_costing',['id'=>$data['id'],'approve_by'=>$this->loginId,'approve_at'=>date("Y-m-d H:i:s")]);

			if ($this->db->trans_status() !== FALSE):
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