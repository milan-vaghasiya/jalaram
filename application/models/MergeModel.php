<?php

class MergeModel extends MasterModel{
	
    private $mergeItem = "merge_item";	
	private $mergeItemTrans = "merge_item_trans";

	/* table not used */
	//['table_name'=>'item_stock_trans','id_field'=>'item_id','name_field'=>''], 
	//['table_name'=>'packing_kit','id_field'=>'item_id','name_field'=>''],
	//['table_name'=>'packing_master_07052022','id_field'=>'item_id','name_field'=>''],
	//['table_name'=>'packing_master_old','id_field'=>'item_id','name_field'=>''],
	//['table_name'=>'purchase_invoice_transaction','id_field'=>'item_id','name_field'=>''],
	//['table_name'=>'purchase_request_old','id_field'=>'item_id','name_field'=>''],
	private $tableNames = [
		['table_name'=>'control_plan','id_field'=>'item_id','name_field'=>'','item_code'=>''],		
		['table_name'=>'calibration','id_field'=>'item_id','name_field'=>'','item_code'=>''],
		['table_name'=>'fg_revisions','id_field'=>'item_id','name_field'=>'','item_code'=>''],
		['table_name'=>'grn_transaction','id_field'=>'item_id','name_field'=>'','item_code'=>''],		
		['table_name'=>'ic_inspection','id_field'=>'item_id','name_field'=>'','item_code'=>''],
		['table_name'=>'inspection_param','id_field'=>'item_id','name_field'=>'','item_code'=>''],
		['table_name'=>'in_out_challan_trans','id_field'=>'item_id','name_field'=>'item_name','item_code'=>''],		
		['table_name'=>'item_kit','id_field'=>'item_id','name_field'=>'','item_code'=>''],
		['table_name'=>'item_kit','id_field'=>'ref_item_id','name_field'=>'','item_code'=>''],		
		['table_name'=>'job_approval','id_field'=>'product_id','name_field'=>'','item_code'=>''],
		['table_name'=>'job_bom','id_field'=>'item_id','name_field'=>'','item_code'=>''],
		['table_name'=>'job_bom','id_field'=>'ref_item_id','name_field'=>'','item_code'=>''],
		['table_name'=>'job_card','id_field'=>'product_id','name_field'=>'','item_code'=>''],
		['table_name'=>'job_material_dispatch','id_field'=>'req_item_id','name_field'=>'','item_code'=>''],
		['table_name'=>'job_material_dispatch','id_field'=>'dispatch_item_id','name_field'=>'','item_code'=>''],
		['table_name'=>'job_return_material','id_field'=>'item_id','name_field'=>'','item_code'=>''],		
		['table_name'=>'job_transaction','id_field'=>'product_id','name_field'=>'','item_code'=>''],		
		['table_name'=>'job_transaction','id_field'=>'machine_id','name_field'=>'','item_code'=>''],		
		['table_name'=>'job_used_material','id_field'=>'product_id','name_field'=>'','item_code'=>''],
		['table_name'=>'job_used_material','id_field'=>'bom_item_id','name_field'=>'','item_code'=>''],
		['table_name'=>'job_work_order','id_field'=>'product_id','name_field'=>'','item_code'=>''],
		['table_name'=>'machine_idle_logs','id_field'=>'machine_id','name_field'=>'','item_code'=>''],
		['table_name'=>'machine_maintenance','id_field'=>'machine_id','name_field'=>'','item_code'=>''],
		['table_name'=>'machine_preventive','id_field'=>'machine_id','name_field'=>'','item_code'=>''],
		['table_name'=>'packing_request','id_field'=>'item_id','name_field'=>'','item_code'=>''],		
		['table_name'=>'packing_master','id_field'=>'item_id','name_field'=>'','item_code'=>''],
		['table_name'=>'export_packing','id_field'=>'item_id','name_field'=>'','item_code'=>''],
		['table_name'=>'packing_transaction','id_field'=>'box_item_id','name_field'=>'','item_code'=>''],
		['table_name'=>'predispatch_inspection','id_field'=>'item_id','name_field'=>'','item_code'=>''],
		['table_name'=>'production_approval','id_field'=>'product_id','name_field'=>'','item_code'=>''],
		['table_name'=>'production_log','id_field'=>'product_id','name_field'=>'','item_code'=>''],		
		['table_name'=>'production_log','id_field'=>'machine_id','name_field'=>'','item_code'=>''],
		['table_name'=>'production_transaction','id_field'=>'product_id','name_field'=>'','item_code'=>''],
		['table_name'=>'product_dimensions','id_field'=>'item_id','name_field'=>'','item_code'=>''],
		['table_name'=>'product_inspection','id_field'=>'item_id','name_field'=>'','item_code'=>''],
		['table_name'=>'product_process','id_field'=>'item_id','name_field'=>'','item_code'=>''],
		['table_name'=>'prod_setup_request','id_field'=>'product_id','name_field'=>'','item_code'=>'item_code'],
		['table_name'=>'prod_setup_request','id_field'=>'machine_id','name_field'=>'','item_code'=>''],
		['table_name'=>'purchase_enquiry_transaction','id_field'=>'item_id','name_field'=>'item_name','item_code'=>''],
		['table_name'=>'purchase_enquiry_transaction','id_field'=>'fgitem_id','name_field'=>'','item_code'=>'fgitem_name'],
		['table_name'=>'purchase_inspection','id_field'=>'item_id','name_field'=>'','item_code'=>''],
		['table_name'=>'process_inspection','id_field'=>'product_id','name_field'=>'','item_code'=>''],
		['table_name'=>'process_inspection','id_field'=>'machine_id','name_field'=>'','item_code'=>''],		
		['table_name'=>'purchase_order_trans','id_field'=>'item_id','name_field'=>'','item_code'=>''],
		['table_name'=>'purchase_order_trans','id_field'=>'fgitem_id','name_field'=>'','item_code'=>'fgitem_name'],
		['table_name'=>'purchase_request','id_field'=>'req_item_id','name_field'=>'req_item_name','item_code'=>''],
		['table_name'=>'purchase_request','id_field'=>'fg_item_id','name_field'=>'','item_code'=>''],
		['table_name'=>'rmstock','id_field'=>'item_id','name_field'=>'','item_code'=>''],		
		['table_name'=>'scrap_book','id_field'=>'item_id','name_field'=>'','item_code'=>''],
		['table_name'=>'stock_journal','id_field'=>'rm_item_id','name_field'=>'rm_name','item_code'=>''],
		['table_name'=>'stock_journal','id_field'=>'fg_item_id','name_field'=>'','item_code'=>'fg_name'],
		['table_name'=>'stock_transaction','id_field'=>'item_id','name_field'=>'','item_code'=>''],		
		['table_name'=>'stock_verification','id_field'=>'item_id','name_field'=>'','item_code'=>''],
		['table_name'=>'tool_consumption','id_field'=>'item_id','name_field'=>'','item_code'=>''],
		['table_name'=>'tool_consumption','id_field'=>'ref_item_id','name_field'=>'','item_code'=>''],
		['table_name'=>'tool_stock','id_field'=>'item_id','name_field'=>'','item_code'=>''],		
		['table_name'=>'trans_child','id_field'=>'item_id','name_field'=>'item_name','item_code'=>'item_code'],
		['table_name'=>'vendor_production_trans','id_field'=>'product_id','name_field'=>'','item_code'=>''],
		
		//['table_name' => 'production_log','name_field'=>'part_code','is_part_code'=>1], // update on part code
		//['table_name' => 'jobwork_challan','name_field'=>'material_data','is_json'=>1], // json data
		//['table_name' => 'grn_transaction','id_field'=>'fgitem_id','name_field'=>'','item_code'=>'fgitem_name','is_com'=>1] //fgitem_id and fgitem_name value comasaprated
	];
	
	//['table_name'=>'production_log','id_field'=>'','name_field'=>'','item_code'=>'part_code'],
	//jobwork_challan , material_data [json]
	//['table_name'=>'grn_transaction','id_field'=>'fgitem_id','name_field'=>'','item_code'=>'fgitem_name'], //fgitem_id and fgitem_name value comasaprated

	public function getDTRows($data){
		$data['tableName'] = $this->mergeItem;
		$data['select'] = "merge_item.*, item_master.item_name as from_item_name, toItem.item_name as to_item_name";
		$data['leftJoin']['item_master'] = "item_master.id = merge_item.from_item";
		$data['leftJoin']['item_master as toItem'] = "toItem.id = merge_item.to_item";

		$data['searchCol'][] = "item_master.item_name";
		$data['searchCol'][] = "toItem.item_name";

		$columns = array('', 'item_master.item_name', 'toItem.item_name');
		if (isset($data['order'])) { $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir']; }
		return $this->pagingRows($data);
	}

	public function save($data){
		try{
            $this->db->trans_begin();

			$data['id'] = "";
			$data['created_by'] = $this->loginId;
			$data['created_at'] = date("Y-m-d H:i:s");
			$data['trans_date'] = date("Y-m-d H:i:s");
			$saveData = $this->store($this->mergeItem,$data);
			$refId = $saveData['insert_id'];

			foreach($this->tableNames as $table):
				$select = array();
				$select[] = "id";
				if(!empty($table['id_field'])):
					$select[] = $table['id_field'];
				endif;
				if(!empty($table['name_field'])):
					$select[] = $table['name_field'];
				endif;
				if(!empty($table['item_code'])):
					$select[] = $table['item_code'];
				endif;
				$select = implode(',',$select);

				$queryData = array();
				$queryData['tableName'] = $table['table_name'];
				$queryData['select'] = $select;
				$queryData['where'][$table['id_field']] = $data['from_item'];
				$oldData = $this->rows($queryData);

				if(!empty($oldData)):
					$transData = [
						'id' => '',
						'ref_id' => $refId,
						'table_name' => $table['table_name'],
						'old_data' => json_encode($oldData),
						'created_by' => $data['created_by'],
						'created_at' => $data['created_at']
					];
					$this->store($this->mergeItemTrans,$transData);

					$queryData = array();
					$queryData['tableName'] = 'item_master';
					$queryData['select'] = 'id,item_name,item_code';
					$queryData['where']['id'] = $data['to_item'];
					$itemData = $this->row($queryData);

					$updateData = array();
					if(!empty($table['id_field'])):
						$updateData[$table['id_field']] = $itemData->id;
					endif;
					if(!empty($table['name_field'])):
						$updateData[$table['name_field']] = $itemData->item_name;
					endif;
					if(!empty($table['item_code'])):
						$updateData[$table['item_code']] = $itemData->item_code;
					endif;

					$this->edit($table['table_name'],[$table['id_field']=>$data['from_item']],$updateData);
				endif;
			endforeach;

			$result = $this->_updateDataOnPartCode($data,$refId);
			if($result['status'] == 2):
				$this->db->trans_rollback();
				return $result;
			endif;

			$result = $this->_updateJobworkChallanItems($data,$refId);
			if($result['status'] == 2):
				$this->db->trans_rollback();
				return $result;
			endif;

			$result = $this->_updateGrnTransactionItems($data,$refId);
			if($result['status'] == 2):
				$this->db->trans_rollback();
				return $result;
			endif;

			$result = ['status'=>1,'message'=>'Item Merged Successfully.'];
			if ($this->db->trans_status() !== FALSE):
				$this->db->trans_commit();
				return $result;
			endif;
		}catch(\Exception $e){
			$this->db->trans_rollback();
		   return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
		}	
	}

	public function _updateDataOnPartCode($data,$refId){
		try{
            $this->db->trans_begin();

			$queryData = array();
			$queryData['tableName'] = "item_master";
			$queryData['where']['id'] = $data['from_item'];
			$formItemData = $this->row($queryData);

			$queryData = array();
			$queryData['tableName'] = "production_log";
			$queryData['select'] = "id,part_code";
			$queryData['where']['part_code'] = $formItemData->item_code;
			$oldData = $this->rows($queryData);

			if(!empty($oldData)):
				$transData = [
					'id' => '',
					'ref_id' => $refId,
					'table_name' => "production_log",
					'old_data' => json_encode($oldData),
					'created_by' => $data['created_by'],
					'created_at' => $data['created_at']
				];
				$this->store($this->mergeItemTrans,$transData);

				$queryData = array();
				$queryData['tableName'] = 'item_master';
				$queryData['select'] = 'id,item_name,item_code';
				$queryData['where']['id'] = $data['to_item'];
				$itemData = $this->row($queryData);

				$this->edit("production_log",['part_code' => $formItemData->item_code],['part_code' => $itemData->item_code]);
			endif;

			if ($this->db->trans_status() !== FALSE):
				$this->db->trans_commit();
				return ['status'=>1,'message'=>"success"];
			endif;
		}catch(\Exception $e){
			$this->db->trans_rollback();
		   return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
		}	
	}

	public function _updateJobworkChallanItems($data,$refId){
		try{
            $this->db->trans_begin();

			$queryData = array();
			$queryData['tableName'] = "jobwork_challan";
			$queryData['select'] = "id,material_data";
			$queryData['where']['material_data !='] = "";
			$result = $this->rows($queryData);

			if(!empty($result)):
				$transData = [
					'id' => '',
					'ref_id' => $refId,
					'table_name' => "jobwork_challan",
					'old_data' => json_encode($result),
					'created_by' => $data['created_by'],
					'created_at' => $data['created_at']
				];
				$this->store($this->mergeItemTrans,$transData);

				foreach($result as $row):
					$materialData = json_decode($row->material_data);
					$newMaterialData = array();
					foreach($materialData as $trans):
						if($trans->item_id == $data['from_item']):
							$trans->item_id = $data['to_item_id'];
							$newMaterialData[] = $trans;
						else:
							$newMaterialData[] = $trans;
						endif;
					endforeach;
					$newMaterialData = json_encode($newMaterialData);
					$this->edit("jobwork_challan",['id'=>$row->id],['material_data' => $newMaterialData]);
				endforeach;
			endif;

			if ($this->db->trans_status() !== FALSE):
				$this->db->trans_commit();
				return ['status'=>1,'message'=>"success"];
			endif;
		}catch(\Exception $e){
			$this->db->trans_rollback();
		   return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
		}	
	}

	public function _updateGrnTransactionItems($data,$refId){
		try{
            $this->db->trans_begin();

			$queryData = array();
			$queryData['tableName'] = "grn_transaction";
			$queryData['select'] = "id,fgitem_id,fgitem_name";
			$queryData['where']['fgitem_id >'] = 0;
			$result = $this->rows($queryData);

			$queryData = array();
			$queryData['tableName'] = 'item_master';
			$queryData['select'] = 'id,item_name,item_code';
			$queryData['where']['id'] = $data['to_item'];
			$itemData = $this->row($queryData);

			if(!empty($result)):
				$transData = [
					'id' => '',
					'ref_id' => $refId,
					'table_name' => "grn_transaction",
					'old_data' => json_encode($result),
					'created_by' => $data['created_by'],
					'created_at' => $data['created_at']
				];
				$this->store($this->mergeItemTrans,$transData);

				foreach($result as $row):
					$fgItemId = explode(',',$row->fgitem_id);
					$fgItemName = explode(',',$row->fgitem_name);
					$itemId = array();$itemName = array();
					foreach($fgItemId as $key => $item_id):
						if($data['from_item'] == $item_id):
							$itemId[$key] = $data['to_item'];
							$itemName[$key] = $itemData->item_code;
						else:
							$oldItemData = array();
							$this->db->where('id',$item_id);
							$this->db->select('id,item_name,item_code');
							$oldItemData = $this->db->get('item_master')->row();
							
							$itemId[$key] = $item_id;
							$itemName[$key] = $oldItemData->item_code;
						endif;
					endforeach;

					$itemId = implode(',',$itemId);
					$itemName = implode(',',$itemName);
					$this->edit('grn_transaction',['id'=>$row->id],['fgitem_id'=>$itemId,'fgitem_name'=>$itemName]);
				endforeach;
			endif;

			if ($this->db->trans_status() !== FALSE):
				$this->db->trans_commit();
				return ['status'=>1,'message'=>"success"];
			endif;
		}catch(\Exception $e){
			$this->db->trans_rollback();
		   return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
		}	
	}
}
?>