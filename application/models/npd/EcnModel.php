<?php
class EcnModel extends MasterModel{
    private $itemMaster = "item_master";
	private $stockTrans = "item_stock_trans";
	private $itemKit = "item_kit";
	private $item_rev_master = "item_rev_master";
	private $item_rev_check_point = "item_rev_check_point";
	private $item_rev_approve = "item_rev_approve";

	/*
		---------------------- Revision Check Point ----------------------
		Created By :- Sweta @14-08-2023
		Used At :- ECN
	*/
	public function getRevChDTRows($data){
        $data['tableName'] = $this->item_rev_master;
		$data['select'] = "item_rev_master.*,item_master.item_code,item_master.item_name";
		$data['leftJoin']['item_master'] = "item_master.id = item_rev_master.item_id";
		$data['where']['item_rev_master.entry_type'] =$data['entry_type'];
		if($data['status'] == 1) { $data['where']['status'] = 3; }
		else { $data['where']['status !='] = 3; }
		
		$data['searchCol'][] = "item_master.item_code";
		$data['searchCol'][] = "ecn_note_no";
        $data['searchCol'][] = "item_rev_master.rev_no";
        $data['searchCol'][] = "DATE_FORMAT(rev_date,'%d-%m-%Y')";
        $data['searchCol'][] = "ecn_drg_no";
        $data['searchCol'][] = "ecn_no";
        $data['searchCol'][] = "DATE_FORMAT(ecn_received_date,'%d-%m-%Y')";
        $data['searchCol'][] = "DATE_FORMAT(target_date,'%d-%m-%Y')";
        $data['searchCol'][] = "item_rev_master.material_grade";

		$columns = array('','','item_master.item_code','ecn_note_no','item_rev_master.rev_no','rev_date','ecn_drg_no','ecn_no','ecn_received_date','target_date','item_rev_master.material_grade');
		if(isset($data['order'])){ $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir']; }
        $result = $this->pagingRows($data);
		return $result;
    }

	public function getRevChPendingDTRows($data){
        $data['tableName'] = $this->item_rev_check_point;
		if($data['entry_type'] == 1){
			$data['select'] = "item_rev_check_point.*,master_detail.title,item_rev_master.ecn_no,item_rev_master.rev_no,item_rev_master.rev_date,item_rev_master.item_id,item_rev_master.status as mStatus,GROUP_CONCAT(employee_master.emp_name) as emp_name,item_master.full_name,item_master.item_code,item_master.item_name,item_rev_master.entry_type";
		}else{
			$data['select'] = "item_rev_check_point.*,master_detail.title,item_rev_master.ecn_no,item_rev_master.rev_no,item_rev_master.rev_date,item_rev_master.item_id,item_rev_master.status as mStatus,GROUP_CONCAT(employee_master.emp_name) as emp_name,item_master.full_name,item_master.item_code,item_master.item_name,pfcRev.rev_no as pfc_rev_no,item_rev_master.entry_type";
		}
		
		$data['leftJoin']['master_detail'] = "master_detail.id = item_rev_check_point.check_point_id";
		$data['leftJoin']['item_rev_master'] = "item_rev_master.id = item_rev_check_point.rev_id";
		$data['leftJoin']['employee_master'] = "FIND_IN_SET(employee_master.id,item_rev_check_point.responsibility) > 0";
		$data['leftJoin']['item_master'] = "item_master.id = item_rev_master.item_id";
		if($data['entry_type'] == 2){
			$data['leftJoin']['item_rev_master pfcRev'] = "pfcRev.id = item_rev_master.ref_id";
		}
		$data['group_by'][] = "item_rev_check_point.id";
		$data['where']['item_rev_check_point.is_change'] = "Y";
		$data['where']['item_rev_master.status !='] = 0;
		$data['where_in']['item_rev_check_point.status'] = "0,3,4";
		$data['where']['item_rev_master.entry_type'] =$data['entry_type'];
		if($data['status'] == 1) { $data['where_in']['item_rev_check_point.status'] = '1,2'; }
		else { $data['where_in']['item_rev_check_point.status'] = '0,3,4'; }

		if (!in_array($this->userRole,[-1,1])) {
            $data['customWhere'][] = '(item_rev_check_point.created_by=' . $this->loginID . ' OR  FIND_IN_SET(' . $this->loginID . ',item_rev_check_point.responsibility) > 0) ';
        }
		
        $data['searchCol'][] = "item_master.item_code";
		$data['searchCol'][] = "item_rev_master.ecn_no";
		$data['searchCol'][] = "item_rev_master.rev_no";
		$data['searchCol'][] = "DATE_FORMAT(item_rev_master.rev_date,'%d-%m-%Y')";
		$data['searchCol'][] = "master_detail.title";
        $data['searchCol'][] = "item_rev_check_point.description";
        $data['searchCol'][] = "responsibility";
        $data['searchCol'][] = "DATE_FORMAT(ch_target_date,'%d-%m-%Y')";
		
		$columns = array('','','item_master.item_code','item_rev_master.ecn_no','item_rev_master.rev_no','item_rev_master.rev_date','master_detail.title','item_rev_check_point.description','responsibility','ch_target_date');
		if(isset($data['order'])){ $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir']; }
        $result = $this->pagingRows($data);
		return $result;
    }

	public function getRevChReviewDTRows($data){

		$data['tableName'] = $this->item_rev_master;
		$data['select'] = 'item_rev_master.*,item_master.full_name,item_master.item_code,item_master.item_name,department_master.name as dept_name,item_rev_approve.approve_at,item_rev_approve.approve_by';
		$data['leftJoin']['item_master'] = "item_master.id = item_rev_master.item_id";
		$data['leftJoin']['department_master'] = 'find_in_set(department_master.id,item_rev_master.dept_id) > 0';
		$data['leftJoin']['item_rev_approve'] = 'item_rev_master.id =item_rev_approve.rev_id AND item_rev_approve.dept_id = department_master.id';		
		$data['where']['item_rev_master.status !='] = 0;
		$data['where']['item_rev_master.entry_type'] = $data['entry_type'];


		if(empty($data['status'])){
			$data['customWhere'][]="FIND_IN_SET(".$this->emp_dept_id.",item_rev_approve.dept_id)";
		}else{
			$data['customWhere'][]="FIND_IN_SET(".$this->emp_dept_id.",item_rev_approve.dept_id) > 0";
		}
		$data['where']['department_master.id'] = $this->emp_dept_id;

		$data['searchCol'][] = "item_master.item_code";
		$data['searchCol'][] = "ecn_note_no";
        $data['searchCol'][] = "rev_no";
        $data['searchCol'][] = "DATE_FORMAT(rev_date,'%d-%m-%Y')";
        $data['searchCol'][] = "ecn_drg_no";
        $data['searchCol'][] = "ecn_no";
        $data['searchCol'][] = "DATE_FORMAT(ecn_received_date,'%d-%m-%Y')";
        $data['searchCol'][] = "DATE_FORMAT(target_date,'%d-%m-%Y')";
        $data['searchCol'][] = "material_grade";
		
		$columns = array('','','item_master.item_code','ecn_note_no','rev_no','rev_date','ecn_drg_no','ecn_no','ecn_received_date','target_date','material_grade');
		if(isset($data['order'])){ $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir']; }
        $result = $this->pagingRows($data);
		return $result;
    }

	public function nextRevChNo($nentry_type =1,$item_id = ""){
        $data['select'] = "MAX(ecn_note_no) as ecn_note_no";
        $data['tableName'] = $this->item_rev_master;
		$data['where']['entry_type'] = $nentry_type;
		if(!empty($item_id)){ $data['where']['item_id'] = $item_id;  }
		$ecn_note_no = $this->specificRow($data)->ecn_note_no;
		$nextRevChNo = (!empty($ecn_note_no))?($ecn_note_no + 1):1;
		return $nextRevChNo;
    }

	public function saveRevChPoint($masterData,$transData){
		try{
            $this->db->trans_begin();

			$revChPointSave = $this->store($this->item_rev_master,$masterData);
			$masterDataId = empty($masterData['id']) ? $revChPointSave['insert_id'] : $masterData['id'];

			foreach($transData['check_point_id'] as $key=>$check_point_id):
				
				$itemData = [
					'id' => $transData['id'][$key],
					'rev_id' => $masterDataId,
					'check_point_id' => $check_point_id,
					'is_change' => $transData['is_change'][$key],
					'old_description' => $transData['old_description'][$key],
					'description' => $transData['description'][$key],
					'responsibility' => $transData['responsibility'][$key],
					'ch_target_date' => (!empty($transData['ch_target_date'][$key]) ? $transData['ch_target_date'][$key] : NULL),
					'created_by' => $masterData['created_by']
				];
				$this->store($this->item_rev_check_point,$itemData);

			endforeach;

			$result = ['status'=>1,'message'=>'Revision Checkpoint saved successfully.','url'=>base_url("npd/ecn/reviseCheckPoint")];
		    
		    if ($this->db->trans_status() !== FALSE):
    			$this->db->trans_commit();
    			return $result;
    		endif;
    	}catch(\Exception $e){
    		$this->db->trans_rollback();
    	   return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
	    }	
	}

	public function deleteEcn($id){
		try {
			$this->db->trans_begin();
			
			// Item Revision Check Point Delete
			$where['rev_id'] = $id;
			$this->trash($this->item_rev_check_point,$where);
			
			// Item Revision Master Delete
			$result = $this->trash($this->item_rev_master,['id'=>$id],'ECN');

			if ($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		} catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
	}

	public function startEcn($data){
		try{
            $this->db->trans_begin();

			$result = $this->store($this->item_rev_master,['id'=>$data['id'], 'status'=>1],'ECN');

			if ($this->db->trans_status() !== FALSE):
    			$this->db->trans_commit();
    			return $result;
    		endif;
    	}catch(\Exception $e){
    		$this->db->trans_rollback();
    	   return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
	    }	
	}

	public function saveVerification($data){
		try{
            $this->db->trans_begin();

			$resultData = [
				'id' => $data['id'],
				'status' => $data['status'],
				'completion_date' => $data['completion_date'],
				'verified_by' => $this->loginId,
				'verify_time' => date("Y-m-d H:i:s")
			];
			$result = $this->store($this->item_rev_check_point,$resultData,'Verification');
		    
		    if ($this->db->trans_status() !== FALSE):
    			$this->db->trans_commit();
    			return $result;
    		endif;
    	}catch(\Exception $e){
    		$this->db->trans_rollback();
    	   return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
	    }	
	}

	public function restartVerification($data){
		try{
            $this->db->trans_begin();

			$result = $this->store($this->item_rev_check_point,['id'=>$data['id'], 'status'=>4],'Verification');

			if ($this->db->trans_status() !== FALSE):
    			$this->db->trans_commit();
    			return $result;
    		endif;
    	}catch(\Exception $e){
    		$this->db->trans_rollback();
    	   return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
	    }	
	}

	public function getCheckPointReview($id){
        $data['tableName'] = $this->item_rev_master;
		$data['select'] = "item_rev_master.*,item_master.item_code,item_master.part_no,item_master.item_name as full_name,item_master.item_name,revDeptAprv.existing_qty,revDeptAprv.wh_qty,revDeptAprv.in_process_qty,revDeptAprv.rm_qty";
		$data['leftJoin']['item_master'] = "item_master.id = item_rev_master.item_id";
		$data['leftJoin']['(SELECT rev_id,SUM(CASE WHEN qty_id = 1 THEN qty ELSE 0 END) as existing_qty,SUM(CASE WHEN qty_id = 2 THEN qty ELSE 0 END) as wh_qty,SUM(CASE WHEN qty_id = 3 THEN qty ELSE 0 END) as in_process_qty,SUM(CASE WHEN qty_id = 4 THEN qty ELSE 0 END) as rm_qty FROM item_rev_approve WHERE item_rev_approve.is_delete = 0 GROUP BY item_rev_approve.rev_id) as revDeptAprv'] = "revDeptAprv.rev_id = item_rev_master.id";
        $data['where']['item_rev_master.id'] = $id;
		$result = $this->row($data);	
		return $result;
	}

	public function getCheckPointReviewTrans($id){
        $data['tableName'] = $this->item_rev_check_point;
		$data['select'] = "item_rev_check_point.*,master_detail.title,GROUP_CONCAT(employee_master.emp_name) as emp_name";
		$data['leftJoin']['master_detail'] = "master_detail.id = item_rev_check_point.check_point_id";
		$data['leftJoin']['employee_master'] = "FIND_IN_SET(employee_master.id,item_rev_check_point.responsibility) > 0";
		$data['group_by'][] = "item_rev_check_point.id";
        $data['where']['item_rev_check_point.rev_id'] = $id;
        return $this->rows($data);
	}

	public function getPrevRevisionData($postData){
        $data['tableName'] = $this->item_rev_master;
		$data['select'] = "item_rev_master.*,item_master.item_code,item_master.part_no,item_master.item_name as full_name,item_master.item_name";
		$data['leftJoin']['item_master'] = "item_master.id = item_rev_master.item_id";
        $data['where']['item_rev_master.item_id'] = $postData['item_id'];
        $data['where']['item_rev_master.rev_date <'] = $postData['rev_date'];
		if(!empty($postData['entry_type'])){ $data['where']['item_rev_master.entry_type'] =$postData['entry_type']; }
		else{ $data['where']['item_rev_master.entry_type'] =1;}
		$data['order_by']['item_rev_master.id'] = "DESC";
		$data['limit'] = 1;
		$result = $this->row($data);
		$rev = new stdClass();
		if(!empty($result))
		{
			$rev = $result;
		}
		return $result;
	}

	public function getWipQty($postData){
		$data['tableName'] = "production_approval";
		$data['select'] = "SUM(in_qty-total_out_qty) as wip_qty";
		$data['leftJoin']['job_card'] = "job_card.id = production_approval.job_card_id";
		$data['where']['job_card.order_status'] = 2;
		$data['where']['job_card.product_id'] = $postData['item_id'];
		return $this->row($data);
	}

	public function getRMQty($postData){
		
		$data['tableName'] = $this->itemKit;
		$data['select'] = "item_kit.ref_item_id,st.qty";
		$data['leftJoin']['item_master'] = "item_master.id = item_kit.ref_item_id";
		$data['leftJoin']['(SELECT SUM(qty) as qty, item_id FROM stock_transaction WHERE is_delete = 0 AND stock_effect = 1 AND location_id !='.$this->SCRAP_STORE->id.' GROUP BY item_id) as st'] = "st.item_id = item_kit.ref_item_id";		
		$data['where']['item_kit.item_id'] = $postData['item_id'];
		$data['where']['item_master.item_type'] = 3;
		$rmData = $this->row($data);
		if(empty($rmData)){
			$kitData = $this->item->getProductKitData($postData['item_id']);
			if(!empty($kitData)){
				$queryData['tableName'] = $this->itemKit;
				$queryData['select'] = "item_kit.ref_item_id,st.qty";
				$queryData['leftJoin']['item_master'] = "item_master.id = item_kit.ref_item_id";
				$queryData['leftJoin']['(SELECT SUM(qty) as qty, item_id FROM stock_transaction WHERE is_delete = 0 AND stock_effect = 1 AND location_id !='.$this->SCRAP_STORE->id.' GROUP BY item_id) as st'] = "st.item_id = item_kit.ref_item_id";	
				$queryData['where_in']['item_kit.item_id'] =implode(",",array_column($kitData,'ref_item_id')) ;
				$queryData['where']['item_master.item_type'] = 3;
				$rmData = $this->rows($queryData);
				$rmData = $rmData[0];
			}else{
				$rmData = [];
			}
			
		}
		return $rmData;
	}

	public function approveCheckPoint($data){
		try{
			$this->db->trans_begin();

			$resultData = [
				'id' => '',
				'rev_id' => $data['rev_id'],
				'approve_by' => $this->loginId,
				'dept_id' => $this->emp_dept_id,
				'approve_at' => date("Y-m-d H:i:s"),
				'qty_id' => !empty($data['qty_id'])?$data['qty_id']:0,
				'qty_label' => !empty($data['qty_label'])?$data['qty_label']:'',
				'qty' => !empty($data['qty'])?$data['qty']:'',
				'sys_qty' => !empty($data['sys_qty'])?$data['sys_qty']:'',
				'created_by' => $this->loginId
			];
			$result = $this->store($this->item_rev_approve, $resultData, 'Approve');

			if ($this->db->trans_status() !== FALSE):
				$this->db->trans_commit();
				return $result;
			endif;
		}catch(\Exception $e){
			$this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
		}	
	}

	public function activeRevision($data){
        try {
            $this->db->trans_begin();

            $result = $this->store($this->item_rev_master, ['id' => $data['id'], 'is_active' => $data['is_active']]);

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

	public function activeRevList(){
		$data['tableName'] = $this->item_rev_master;
		$data['where']['is_active'] = 1;
		return $this->rows($data);
	}

	public function saveFinalApprove($data){
		try{
            $this->db->trans_begin();

			$result = $this->store($this->item_rev_master,[
				'id' => $data['rev_id'],
				'status' => 3,
				'eff_impl_date' => (!empty($data['eff_impl_date']) ? $data['eff_impl_date'] : ''),
				'approved_by' => $this->loginId,
				'approved_at' => date("Y-m-d H:i:s")
			],'ECN');

			// /** Auto CP Revision */
			// $revData =$this->getCheckPointReview($data['rev_id']);
			// $revNo = $this->nextRevChNo(2,$revData->item_id);
			// $cpData = [
			// 	'id'=>'',
			// 	'ref_id'=>$data['rev_id'],
			// 	'entry_type'=>2,
			// 	'item_id'=>$revData->item_id,
			// 	'ecn_note_no'=>$revNo,
			// 	'rev_no'=>sprintf('CP%02d',$revNo),
			// 	'rev_date'=>date("Y-m-d"),
			// 	'created_by'=>$this->loginId,
			// 	'status' =>1
			// ];
			// $result = $this->store($this->item_rev_master,$cpData );
			if ($this->db->trans_status() !== FALSE):
    			$this->db->trans_commit();
    			return $result;
    		endif;
    	}catch(\Exception $e){
    		$this->db->trans_rollback();
    	   return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
	    }	
	}	

	public function getDeptReviewForPrint($postData){
		$queryData['tableName'] = $this->item_rev_master;
		$queryData['select'] = 'department_master.name as dept_name,employee_master.emp_name,item_rev_approve.approve_at';
		$queryData['leftJoin']['department_master'] = 'find_in_set(department_master.id,item_rev_master.dept_id) > 0';
		$queryData['leftJoin']['item_rev_approve'] = 'item_rev_approve.dept_id = department_master.id AND item_rev_approve.rev_id = item_rev_master.id';
		$queryData['leftJoin']['employee_master'] = 'item_rev_approve.approve_by = employee_master.id';
		$queryData['where']['item_rev_master.id'] = $postData['rev_id'];
		return $this->rows($queryData);
	}

	public function closeEcn($data){
		try{
            $this->db->trans_begin();

			$result = $this->store($this->item_rev_master,['id'=>$data['id'], 'status'=>2],'ECN');

			if ($this->db->trans_status() !== FALSE):
    			$this->db->trans_commit();
    			return $result;
    		endif;
    	}catch(\Exception $e){
    		$this->db->trans_rollback();
    	   return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
	    }	
	}

	/* ---------------------- End Revision Check Point ---------------------- */

	public function getEcnRevList($postData){
		$queryData['tableName'] = $this->item_rev_master;
		$queryData['select'] = 'item_rev_master.*';
		if(!empty($postData['item_id'])){$queryData['where']['item_rev_master.item_id'] = $postData['item_id'];}
		if(!empty($postData['ecn_type'])){$queryData['where']['item_rev_master.ecn_type'] = $postData['ecn_type'];}
		if(!empty($postData['is_active'])){$queryData['where']['item_rev_master.is_active'] = $postData['is_active'];}
		$queryData['where']['item_rev_master.entry_type'] =1;
		if(isset($postData['single_row']) && !empty($postData['single_row'])){
			return $this->row($queryData);
		}else{
			return $this->rows($queryData);
		}
	}

	/*** CP Revision */
	public function getCpRevDTRows($data){
        $data['tableName'] = $this->item_rev_master;
		$data['select'] = "item_rev_master.*,item_master.item_code,item_master.item_name,ecn.rev_no as pfc_rev_no";
		$data['leftJoin']['item_rev_master ecn'] = "ecn.id = item_rev_master.ref_id";
		$data['leftJoin']['item_master'] = "item_master.id = item_rev_master.item_id";
		$data['where']['item_rev_master.entry_type'] =2;
		$data['where']['item_rev_master.status'] = $data['status']; 
		
		$data['searchCol'][] = "item_master.item_code";
		$data['searchCol'][] = "item_rev_master.rev_no";
        $data['searchCol'][] = "DATE_FORMAT(item_rev_master.rev_date,'%d-%m-%Y')";
        $data['searchCol'][] = "ecn.rev_no";
        $data['searchCol'][] = "item_rev_master.remark";

		$columns = array('','','item_master.item_code','item_rev_master.rev_no','rev_date','item_rev_master.remark');
		if(isset($data['order'])){ $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir']; }
        $result = $this->pagingRows($data);
		return $result;
    }

	public function changeCpRevStatus($data){
		try{
            $this->db->trans_begin();
			$result = $this->store($this->item_rev_master,$data);
			if ($this->db->trans_status() !== FALSE):
    			$this->db->trans_commit();
    			return $result;
    		endif;
    	}catch(\Exception $e){
    		$this->db->trans_rollback();
    	   return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
	    }	
	}

	public function getCpRevData($postData){
		$queryData['tableName'] = $this->item_rev_master;
		$queryData['select'] = 'item_rev_master.*,ecn.rev_no as pfc_rev_no';
		$queryData['leftJoin']['item_rev_master ecn'] = "ecn.id = item_rev_master.ref_id";
		if(!empty($postData['item_id'])){$queryData['where']['item_rev_master.item_id'] = $postData['item_id'];}
		if(!empty($postData['id'])){$queryData['where']['item_rev_master.id'] = $postData['id'];}
		if(!empty($postData['rev_no'])){$queryData['where']['item_rev_master.rev_no'] = $postData['rev_no'];}
		if(!empty($postData['status'])){$queryData['where']['item_rev_master.status'] = $postData['status'];}
		if(!empty($postData['is_active'])){$queryData['where']['item_rev_master.is_active'] = $postData['is_active'];}
		if(!empty($postData['pfc_rev_no'])){ $queryData['where']['ecn.rev_no'] = $postData['pfc_rev_no'];  }
		$queryData['where']['item_rev_master.entry_type'] =2;
		if(isset($postData['single_row']) && !empty($postData['single_row'])){
			return $this->row($queryData);
		}else{
			return $this->rows($queryData);
		}
	}

	public function saveCpRevision($data){
		try{
            $this->db->trans_begin();
			$result = $this->store($this->item_rev_master,$data);
			if ($this->db->trans_status() !== FALSE):
    			$this->db->trans_commit();
    			return $result;
    		endif;
    	}catch(\Exception $e){
    		$this->db->trans_rollback();
    	   return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
	    }	
	}

	public function getNextJJIRevNo($item_id,$entry_type=1){
		if($entry_type == 2){
			$data['select'] = "MAX(CAST(SUBSTRING(rev_no, 4, length(rev_no)-3) AS UNSIGNED)) as rev_no";
		}else{
			$data['select'] = "MAX(CAST(SUBSTRING(rev_no, 3, length(rev_no)-1) AS UNSIGNED)) as rev_no";
		}
       
        $data['tableName'] = $this->item_rev_master;
		$data['where']['entry_type'] = $entry_type;
		$data['where']['item_id'] = $item_id;
		$rev_no = $this->specificRow($data)->rev_no;
		$nextNo = (!empty($rev_no))?($rev_no + 1):1;
		return $nextNo;
    }
}
?>