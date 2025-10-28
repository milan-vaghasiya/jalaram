<?php
class PackingModel extends MasterModel{
    private $packingMaster = "packing_master";
    private $packingTrans = "packing_transaction";
    private $itemKit = "item_kit";
    private $stockTrans = "stock_transaction";
    private $itemMaster = "item_master";
    private $exportPacking = "export_packing";

    public function getNetxNo(){
        $data['tableName'] = $this->packingMaster;
        $data['select'] = "MAX(trans_no) as trans_no";
		$trans_no = $this->specificRow($data)->trans_no;
		$nextTransNo = (!empty($trans_no))?($trans_no + 1):1;
		return $nextTransNo; 
    }

    public function getPrefix(){
        $prefix = 'PCK/';
        return $prefix.$this->shortYear.'/';
    }

    public function getDTRows($data){ 
        $data['tableName'] = $this->packingTrans;
        $data['select'] = "packing_master.id,packing_master.trans_number,packing_master.trans_date,item_master.item_code,item_master.item_name,packing_transaction.qty_box,packing_transaction.total_box,packing_transaction.total_box_qty,packing_transaction.remark,(CASE WHEN packing_transaction.so_trans_id = 0 THEN 'Self Packing' ELSE CONCAT(trans_main.trans_prefix,trans_main.trans_no) END) as so_no,(packing_transaction.total_box_qty - packing_transaction.dispatch_qty) as pending_qty,packing_transaction.batch_detail";
        $data['leftJoin']['packing_master'] = "packing_transaction.packing_id = packing_master.id";
        $data['leftJoin']['item_master'] = "packing_master.item_id = item_master.id";
        $data['leftJoin']['trans_child'] = "packing_transaction.so_trans_id = trans_child.id";
        $data['leftJoin']['trans_main'] = "trans_main.id = trans_child.trans_main_id";

        if($data['status'] == 0):
            $data['where']['(packing_transaction.total_box_qty - packing_transaction.dispatch_qty) >'] = 0;
        else:
            $data['where']['(packing_transaction.total_box_qty - packing_transaction.dispatch_qty) <='] = 0;
        endif;
        
        $data['searchCol'][] = "packing_master.trans_number";
        $data['searchCol'][] = "DATE_FORMAT(packing_master.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "CONCAT('[',item_master.item_code,'] ',item_master.item_name)";
        $data['searchCol'][] = "packing_transaction.qty_box";
        $data['searchCol'][] = "packing_transaction.total_box";
        $data['searchCol'][] = "packing_transaction.total_box_qty";
        $data['searchCol'][] = "(packing_transaction.total_box_qty - packing_transaction.dispatch_qty)";
        $data['searchCol'][] = "packing_master.remark";
        
		/*$columns =array('','');
        foreach($data['searchCol'] as $key=>$value):
                $columns[] = $value;
        endforeach;*/
        $columns =array('','','packing_master.trans_number','packing_master.trans_date',"CONCAT('[',item_master.item_code,'] ',item_master.item_name)","packing_transaction.qty_box","packing_transaction.total_box","packing_transaction.total_box_qty","(packing_transaction.total_box_qty - packing_transaction.dispatch_qty)","packing_master.remark");
		
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		else{
		    $data['order_by']['packing_master.trans_no'] = "DESC";
		}
        return $this->pagingRows($data);
    }

    public function getItemCurrentStock($postData){ 
        $queryData['tableName'] = "stock_transaction";
        $queryData['select'] = "SUM(qty) as qty";
        $queryData['where']['item_id'] = $postData['item_id'];
        if(!empty($postData['location_id'])){$queryData['where']['location_id'] = $postData['location_id'];}
        if(!empty($postData['batch_no'])){$queryData['where']['batch_no'] = $postData['batch_no'];}
        return $this->row($queryData);
    }

    public function batchWiseItemStock($item_id){
		$data['tableName'] = "stock_transaction";
		$data['select'] = "SUM(stock_transaction.qty) as qty,stock_transaction.batch_no,stock_transaction.location_id,location_master.store_name,location_master.location";
        $data['leftJoin']['location_master'] = "stock_transaction.location_id = location_master.id";
		$data['where']['stock_transaction.item_id'] = $item_id;
		$data['order_by']['stock_transaction.id'] = "asc";
        $data['group_by'][] = "stock_transaction.location_id";
		$data['group_by'][] = "stock_transaction.batch_no";
		return $this->rows($data);
	}

    public function getPacking($id){
        $queryData['tableName'] = $this->packingMaster;
        $queryData['select'] = "packing_master.*,item_master.item_code,item_master.item_name,item_master.part_no, item_master.wt_pcs";   
        $queryData['leftJoin']['item_master'] = "packing_master.item_id = item_master.id";
        $queryData['where']['packing_master.id'] = $id;
        $result = $this->row($queryData);
        $item_id = (!empty($result->item_id)) ? $result->item_id : 0;
        $result->items = $this->getPackingTrans($id,$item_id);
        return $result;
    }

    public function getPackingTrans($id,$item_id=""){
        $queryData['tableName'] = $this->packingTrans;
        $queryData['select'] = "packing_transaction.*,item_master.item_code as box_item_code,item_master.item_name as box_item_name";   
        $queryData['leftJoin']['item_master'] = "item_master.id = packing_transaction.box_item_id";
        if(!empty($item_id))
        {
            $queryData['select'] .= ", IFNULL(packing_kit.wt_per_box,0) as box_wt";
            $queryData['leftJoin']['packing_kit'] = "packing_kit.item_id = ".$item_id." AND packing_kit.box_id = packing_transaction.box_item_id";
        }else{$queryData['select'] .= ", (0) as box_wt";}
        $queryData['where']['packing_id'] = $id;
        $result = $this->rows($queryData);
        //$this->printQuery();
        return $result;
    }

    public function getPackingTransRow($id){
        $queryData['tableName'] = $this->packingTrans; 
        $queryData['select'] = "packing_transaction.*,item_master.item_code as box_item_code,item_master.item_name as box_item_name";     
        $queryData['leftJoin']['item_master'] = "packing_transaction.box_item_id = item_master.id";
        $queryData['where']['packing_transaction.id'] = $id;
        return $this->row($queryData);
    }
    
    // Get Job No & So From Packing Transaction | Created BY JP@14.07.24
    public function getPackingTransDetail($batch_no=""){
        $queryData = Array();
        $queryData['tableName'] = "stock_transaction";
        $queryData['select'] = "stock_transaction.batch_no as job_number,IFNULL(trans_main.doc_no,'') as cust_po_no";
        $queryData['leftJoin']['job_card'] = "job_card.id = stock_transaction.ref_id";
        $queryData['leftJoin']['trans_main'] = "trans_main.id = job_card.sales_order_id";
        $queryData['where']['stock_transaction.ref_type'] = 28;
        if(!empty($batch_no)){$queryData['where_inT']['stock_transaction.batch_no'] = $batch_no;}
        $queryData['where']['stock_transaction.location_id'] = $this->PROD_STORE->id;
        $queryData['group_by'][] = 'stock_transaction.batch_no';
        $result = $this->rows($queryData);
        //$this->printQuery();
        return $result;
    }

    public function save($data){
        try {
            $this->db->trans_begin();

            if(!empty($data['id'])):
                /** Remove Stock Transaction **/
                
                $this->remove($this->stockTrans,['ref_id'=>$data['id'],'ref_type'=>36]);
                $this->trash($this->packingTrans,['packing_id'=>$data['id']]);
            endif;

            $materialData = $data['material_data'];unset($data['material_data']);
            $result = $this->store($this->packingMaster,$data,'Packing');
            $packingId = (empty($data['id']))?$result['insert_id']:$data['id'];

            foreach($materialData as $row):
                if(empty($row['id'])):
                    $row['created_by'] = $this->loginId;
                else:
                    $row['updated_by'] = $this->loginId;
                endif;
                $row['packing_id'] = $packingId;
                $row['is_delete'] = 0;

                $transResult = $this->store($this->packingTrans,$row);
                $transId = (empty($row['id']))?$transResult['insert_id']:$row['id'];

                /* Box Stock Deduction */
                $stockQueryData = array();
                $stockQueryData['id']="";
                $stockQueryData['location_id']=$this->PKG_STORE->id;
                //$stockQueryData['batch_no'] = "GB";
                $stockQueryData['trans_type']=2;
                $stockQueryData['item_id']=$row['box_item_id'];
                $stockQueryData['qty'] = ($row['total_box'] * -1);
                $stockQueryData['ref_type']=36;
                $stockQueryData['ref_id']=$packingId;
                $stockQueryData['trans_ref_id']=$transId;
                $stockQueryData['ref_no']=$data['trans_number'];
                $stockQueryData['ref_date']=$data['trans_date'];
                $stockQueryData['created_by']=$this->loginID; 
                $this->store($this->stockTrans,$stockQueryData);

                if(!empty($row['so_trans_id'])):
                    $setData = array();
                    $setData['tableName'] = "trans_child";
                    $setData['where']['id'] = $row['so_trans_id'];
                    $setData['set']['packing_qty'] = 'packing_qty, + ' . $row['total_box_qty'];
                    $this->setValue($setData);
                endif;
                $batchData = json_decode($row['batch_detail'],false);
                $i=1; $totalQty = 0;$stockReduceIds = [];
                foreach($batchData as $batchRow):
                    /* Product Stock Deduction From Packing Area */
                    $stockQueryData = array();
                    $stockQueryData['id']="";
                    $stockQueryData['location_id']=$batchRow->location_id;
                    $stockQueryData['batch_no'] = $batchRow->batch_no;
                    $stockQueryData['trans_type']=2;
                    $stockQueryData['item_id']=$data['item_id'];
                    $stockQueryData['qty'] = ($batchRow->batch_qty * -1);
                    $stockQueryData['ref_type']=36;
                    //$stockQueryData['size']=$row['qty_box'];
                    $stockQueryData['ref_id']=$packingId;
                    $stockQueryData['trans_ref_id']=$transId;
                    $stockQueryData['ref_no']=$data['trans_number'];
                    $stockQueryData['ref_batch']=$row['so_trans_id'];
                    $stockQueryData['ref_date']=$data['trans_date'];
                    $stockQueryData['created_by']=$this->loginID;
                    $stockReduce = $this->store($this->stockTrans,$stockQueryData);
                    
                    $stockReduceIds[] = $stockReduce['insert_id'];// Array of Stock Trans Id of Minus Stock
                    $totalQty += $batchRow->batch_qty;
                    
                    /* Product Stock Plus to Ready to Dispatch Store */
                    /*$stockQueryData['location_id']=$this->RTD_STORE->id;
                    $stockQueryData['batch_no'] = $batchRow->batch_no;
                    $stockQueryData['trans_type']=1;
                    $stockQueryData['qty'] = $batchRow->batch_qty;
                    $stockPlus = $this->store($this->stockTrans,$stockQueryData);*/
                endforeach;
            
                // Merge Multiple Batch to Packing Batch
                $stockQueryData = array();
                $stockQueryData['id']="";
                $stockQueryData['location_id']=$this->RTD_STORE->id;
                $stockQueryData['batch_no'] = $data['trans_number'];
                $stockQueryData['trans_type']=1;
                $stockQueryData['qty'] = $totalQty;
                $stockQueryData['item_id']=$data['item_id'];
                $stockQueryData['ref_type']=36;
                $stockQueryData['size']=floatval($row['qty_box']);
                $stockQueryData['ref_id']=$packingId;
                $stockQueryData['trans_ref_id']=$transId;
                $stockQueryData['ref_no']=$data['trans_number'];
                $stockQueryData['ref_batch']=implode(',',$stockReduceIds); // Stock Trans Id of Minus Stock
                $stockQueryData['ref_date']=$data['trans_date'];
                $stockQueryData['created_by']=$this->loginID;
                $stockReduce = $this->store($this->stockTrans,$stockQueryData);
            endforeach;
            
            
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function delete($id){
        $packingOrderData = $this->getPacking($id);

        if($packingOrderData->is_delete == 0):
            
            //Check stock used in transaction            
            $postData = ['item_id'=>$packingOrderData->item_id, 'location_id'=>$this->RTD_STORE->id, 'batch_no'=>$packingOrderData->trans_number, 'stock_required'=>1, 'single_row'=>1];
            $batchData = $this->store->getItemStockBatchWise($postData);
            
            if($batchData->qty < $packingOrderData->total_qty){
                return ['status' => 0 ,'message' => 'You cannot remove this record because its stock is already in use.'];
            }
            
            /** Remove Stock Transaction **/
            $this->remove($this->stockTrans,['ref_id'=>$id,'ref_type'=>36]);
            $this->edit($this->packingTrans,['packing_id'=>$id],['is_delete'=>1]);
            return $this->trash($this->packingMaster,['id'=>$id],"Packing");
        else:
            return ['status' => 1 ,'message' => 'Packing deleted successfully.'];
        endif;
    }

    public function getPackingTransBySo($sales_trans_id){
        $queryData['tableName'] = $this->packingTrans;
        $queryData['select'] = "packing_transaction.*,item_master.item_code as box_item_code,item_master.item_name as box_item_name";   
        $queryData['leftJoin']['item_master'] = "packing_transaction.box_item_id = item_master.id";
        $queryData['where']['so_trans_id'] = $sales_trans_id;
        return $this->rows($queryData);
    }

    public function getConsumable($category_id){
        $data['tableName'] = $this->itemMaster;    
        $data['where']['item_master.item_type'] = 2;
        $data['where']['item_master.category_id'] = $category_id;
        return $this->rows($data);
    }

    /* Created By NYN @16/11/2022 */
    public function savePackingStandard($data){
        try{
            $this->db->trans_begin();

            if ($this->checkDuplicateStandard($data['item_id'], $data['box_id']) > 0) :
                $errorMessage['gerenal_error'] = "Box already added.";
				$result = ['status' => 0, 'message' => $errorMessage];
            else:
                $this->edit($this->itemMaster,['id'=>$data['item_id']],['wt_pcs' => $data['wt_pcs']]); unset($data['wt_pcs']);
                $result = $this->store('packing_kit',$data,'Packing Standard');
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
    
    /* Created By NYN @16/11/2022 */
    public function checkDuplicateStandard($item_id, $box_id){
        $data['tableName'] = 'packing_kit';
		$data['where']['item_id'] = $item_id;
		$data['where']['box_id'] = $box_id;
		return $this->numRows($data);
    }

    /* Created By NYN @16/11/2022 */
    public function deletePackingStandard($id){
        try{
            $this->db->trans_begin();

            $result = $this->trash('packing_kit',['id'=>$id],'Packing Standard');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    /* Created By NYN @16/11/2022 */
    public function getProductPackStandard($data){
        $queryData['tableName'] = 'packing_kit';
        $queryData['select'] = "packing_kit.*,item_master.item_name,item_master.size,finish.wt_pcs";
        $queryData['leftJoin']['item_master'] = "packing_kit.box_id = item_master.id";
        $queryData['leftJoin']['item_master as finish'] = "packing_kit.item_id = finish.id";
        $queryData['where']['packing_kit.item_id'] = $data['item_id'];
        return $this->rows($queryData); 
    }
	
	public function saveDispatchMaterial($data){ 
		try{
            $this->db->trans_begin();
			/*** UPDATE STOCK TRANSACTION DATA ***/
			foreach($data['location'] as $key=>$value):
			    if($data['batch_quantity'][$key] > 0):
    				$stockQueryData['id']="";
    				$stockQueryData['location_id'] = $value;
    				if(!empty( $data['batch_number'][$key])){
    					$stockQueryData['batch_no'] = $data['batch_number'][$key];
    				}
    				$stockQueryData['trans_type']=2;
    				$stockQueryData['item_id']=$data['item_id'];
    				$stockQueryData['qty'] = "-".$data['batch_quantity'][$key];
    				$stockQueryData['ref_type'] = 4;
    				$stockQueryData['ref_id'] = $data['trans_main_id'];
    				$stockQueryData['trans_ref_id'] = $data['trans_child_id'];
    				$stockQueryData['size'] = floatval($data['qty_per_box'][$key]);
    				$stockQueryData['ref_no'] = getPrefixNumber($data['trans_prefix'],$data['trans_no']);
    				$stockQueryData['ref_date']=date('Y-m-d');
    				$stockQueryData['created_by']=$this->loginID;
    				$this->store($this->stockTrans,$stockQueryData);
    
    				$totalBatchStock = $data['batch_quantity'][$key];
                    
    				$qryData = array();
    				$qryData['tableName'] = $this->packingTrans; 
    				$qryData['select'] = 'packing_transaction.*';
    				$qryData['leftJoin']['packing_master'] = "packing_transaction.packing_id = packing_master.id";
    				$qryData['where']['packing_master.item_id'] = $data['item_id'];
    				$qryData['where']['packing_master.trans_number'] = $data['batch_number'][$key];
    				$qryData['customWhere'][] = 'packing_transaction.dispatch_qty < packing_transaction.total_box_qty';
    				$qryData['order_by']['packing_master.trans_date'] = 'ASC';
    				$packData = $this->rows($qryData);
    				foreach($packData as $pack){
    					$dispatchQty = 0;
    					$availableQty = ($pack->total_box_qty - $pack->dispatch_qty);
    					if($availableQty >= $totalBatchStock){
    						$dispatchQty = $totalBatchStock;
    					}else{
    						$dispatchQty = $availableQty;
    					}
    
    					$setData = Array();
    					$setData['tableName'] = $this->packingTrans;
    					$setData['where']['id'] = $pack->id;
    					$setData['set']['dispatch_qty'] = 'dispatch_qty, + '.$dispatchQty;
    					$this->setValue($setData);
    
    					$totalBatchStock -= $dispatchQty;
    					if($totalBatchStock == 0){ break;}
    				}
    				
    				$setData = Array();
    				$setData['tableName'] = 'trans_child';
    				$setData['where']['id'] = $data['trans_child_id'];
    				$setData['set']['packing_qty'] = 'packing_qty, + '.$data['batch_quantity'][$key];
    				$this->setValue($setData);
    				$this->edit('trans_child',['id'=>$data['trans_child_id']],['stock_eff'=>1]);
    			endif;
			endforeach;
			
			$result = ['status'=>1,'message'=>'Material Dispatch SuccessFully.'];
			if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function saveExportPacking($data){
        try {
            $this->db->trans_begin();
            if(!empty($data['id'])){
                $packData = $this->getExportData(['trans_no'=>$data['trans_no'],'packing_type'=>$data['packing_type']]);
                $itemData = array_column($data['item_data'],'id');
                foreach($packData as $row){
                    if (!in_array($row->id, $itemData)):
                        if($data['packing_type'] == 2){
                            $setData = Array();
                            $setData['tableName'] = 'packing_request';
                            $setData['where']['id'] = $row->req_id;
                            $setData['set']['dispatch_qty'] = 'dispatch_qty, - '.$row->total_qty;
                            $this->setValue($setData);
                            $this->remove($this->stockTrans,['ref_id'=>$row->id,'ref_type'=>35]);
                        }
                        $this->trash($this->exportPacking,['id'=>$row->id]);
                    endif;
                }
              
            }
            foreach($data['item_data'] as $row):
                if(empty($row['id'])):
                    $row['created_by'] = $this->loginId;
                else:
                    $exportData = $this->getExportDetail($row['id']);
                    if($data['packing_type'] == 2){
                        $setData = Array();
                        $setData['tableName'] = 'packing_request';
                        $setData['where']['id'] = $row['req_id'];
                        $setData['set']['dispatch_qty'] = 'dispatch_qty, - '.$exportData->total_qty;
                        $this->setValue($setData);
                        
                        $this->remove($this->stockTrans,['ref_id'=>$row['id'],'ref_type'=>35]);
                    }
                    $row['updated_by'] = $this->loginId;
                endif;
                $netWt = round(($row['total_qty'] * $row['wt_pcs']),3);
                
                $exportData = [
                    'id'=>$row['id'],
                    'packing_type'=>$data['packing_type'],
                    'export_pck_type'=>$data['export_pck_type'],
                    'item_id'=>$row['item_id'],
                    'trans_no'=>$data['trans_no'],
                    'trans_prefix'=>$data['trans_prefix'],
                    'package_no'=>$row['package_no'],
                    'packing_date'=>$data['packing_date'],
                    'req_id'=>$row['req_id'],
                    'party_id'=>$row['party_id'],
                    'so_id'=>$row['so_id'],
                    'so_trans_id'=>$row['so_trans_id'],
                    'qty_box'=>$row['qty_per_box'],
                    'total_box'=>$row['total_box'],
                    'total_qty'=>$row['total_qty'],
                    'wpp'=>$row['wt_pcs'],
                    'pack_weight'=>$row['packing_wt'],
                    'wooden_weight'=>$row['wooden_wt'],
                    'wooden_size'=>$row['box_size'],
                    'net_wt'=>$netWt,
                    'gross_wt'=>($netWt + $row['packing_wt'])
                ];
                //if(empty($row['id'])){ 
                    $exportData['created_by'] = $this->loginId; 
                //}else{ 
                    $exportData['updated_by'] = $this->loginId; 
                //}
                $result = $this->store($this->exportPacking,$exportData,'Packing');
                $packingId = (empty($row['id']))?$result['insert_id']:$row['id'];
  
                if($data['packing_type'] == 2){
                    /* Box Stock Deduction */
                    $stockQueryData = array();
                    $stockQueryData['id']="";
                    $stockQueryData['location_id']=$row['location_id'];
                    $stockQueryData['batch_no'] = $row['batch_no'];
                    $stockQueryData['trans_type']=2;
                    $stockQueryData['item_id']=$row['item_id'];
                    $stockQueryData['qty'] = '-'.$row['total_qty'];
                    $stockQueryData['size'] = floatval($row['qty_per_box']);
                    $stockQueryData['ref_type']=35;
                    $stockQueryData['ref_id']=$packingId;
                    $stockQueryData['trans_ref_id']=$row['req_id'];
                    $stockQueryData['ref_no']=$data['trans_number'];
                    $stockQueryData['ref_date']=$data['packing_date'];
                    $stockQueryData['created_by']=$this->loginID; 
                    $this->store($this->stockTrans,$stockQueryData);

                    $setData = Array();
                    $setData['tableName'] = 'packing_request';
                    $setData['where']['id'] = $row['req_id'];
                    $setData['set']['dispatch_qty'] = 'dispatch_qty, + '.$row['total_qty'];
                    $this->setValue($setData);
					
					$totalBatchStock = $row['total_qty'];
					$qryData = array();
    				$qryData['tableName'] = $this->packingTrans; 
    				$qryData['select'] = 'packing_transaction.*';
    				$qryData['leftJoin']['packing_master'] = "packing_transaction.packing_id = packing_master.id";
    				$qryData['where']['packing_master.item_id'] = $row['item_id'];
    				$qryData['where']['packing_master.trans_number'] = $row['batch_no'];
    				$qryData['customWhere'][] = 'packing_transaction.dispatch_qty < packing_transaction.total_box_qty';
    				$qryData['order_by']['packing_master.trans_date'] = 'ASC';
    				$packData = $this->rows($qryData);
    				foreach($packData as $pack){
    					$dispatchQty = 0;
    					$availableQty = ($pack->total_box_qty - $pack->dispatch_qty);
    					if($availableQty >= $totalBatchStock){
    						$dispatchQty = $totalBatchStock;
    					}else{
    						$dispatchQty = $availableQty;
    					}
    
    					$setData = Array();
    					$setData['tableName'] = $this->packingTrans;
    					$setData['where']['id'] = $pack->id;
    					$setData['set']['dispatch_qty'] = 'dispatch_qty, + '.$dispatchQty;
    					$this->setValue($setData);
    
    					$totalBatchStock -= $dispatchQty;
    					if($totalBatchStock == 0){ break;}
    				}
                }
               
                $this->store('packing_request',['id'=>$row['req_id'],'status'=>$data['packing_type']]);
            endforeach;

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getExportDTRows($data){
        $data['tableName'] = $this->exportPacking;
        $data['select'] = "export_packing.id,export_packing.so_id,export_packing.comm_pack_id,export_packing.req_id,export_packing.packing_type,export_packing.trans_no,export_packing.trans_prefix,CONCAT(export_packing.trans_prefix,export_packing.trans_no) as trans_number,export_packing.packing_date,item_master.item_code,item_master.item_name,export_packing.qty_box,export_packing.total_box,export_packing.total_qty,export_packing.remark, trans_main.trans_prefix as so_prefix, trans_main.trans_no as so_no,trans_main.doc_no,packing_request.trans_prefix as pr_prefix,packing_request.trans_no as pr_no,export_packing.export_pck_type,export_packing.port_loading,export_packing.port_dispatch,export_packing.destination_country,export_packing.nomination_agent";
        $data['leftJoin']['item_master'] = "export_packing.item_id = item_master.id";
        $data['leftJoin']['trans_child'] = "export_packing.so_trans_id = trans_child.id";
        $data['leftJoin']['trans_child'] = "export_packing.so_trans_id = trans_child.id";
        $data['leftJoin']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        $data['leftJoin']['packing_request'] = "export_packing.req_id = packing_request.id";
        $data['where']['export_packing.packing_type'] = $data['packing_type'];

        if($data['status'] == 0):
            $data['where']['export_packing.comm_pack_id'] = 0;
        else:
            $data['where']['export_packing.comm_pack_id >'] = 0;
        endif;
        
        $data['order_by']['export_packing.trans_no'] = "DESC";

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "CONCAT(export_packing.trans_prefix,export_packing.trans_no)";
        $data['searchCol'][] = "DATE_FORMAT(export_packing.packing_date,'%d-%m-%Y')";
        $data['searchCol'][] = "CONCAT(SUBSTRING_INDEX(SUBSTRING_INDEX(packing_request.trans_prefix, '/', 1), '/', -1),'/',packing_request.trans_no,'/',SUBSTRING_INDEX(SUBSTRING_INDEX(packing_request.trans_prefix, '/', 2), '/', -1))";
        $data['searchCol'][] = "CONCAT(trans_main.trans_prefix,trans_main.trans_no)";
        $data['searchCol'][] = "trans_main.doc_no";
        $data['searchCol'][] = "item_master.item_code";
        $data['searchCol'][] = "export_packing.qty_box";
        $data['searchCol'][] = "export_packing.total_box";
        $data['searchCol'][] = "export_packing.total_qty";
        $data['searchCol'][] = "export_packing.remark";
        
		$columns =array('','');
        foreach($data['searchCol'] as $key=>$value):
            $columns[] = $value;
        endforeach;

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getExportData($postData){
        $queryData = array();
        $queryData['tableName'] = $this->exportPacking;
        $queryData['select'] = "export_packing.*,item_master.item_type,item_master.item_code,item_master.item_name,item_master.description,item_master.item_alias,item_master.part_no,hsn_master.hsn_code,hsn_master.description as hsn_desc,som.currency,stock_transaction.batch_no,stock_transaction.location_id,packing_request.trans_way,packing_request.delivery_terms,packing_request.container_type,som.doc_no";
        $queryData['leftJoin']['item_master'] = "item_master.id = export_packing.item_id";
        $queryData['leftJoin']['packing_request'] = "export_packing.req_id = packing_request.id";
        $queryData['leftJoin']['hsn_master'] = "item_master.hsn_code = hsn_master.hsn_code";
        $queryData['leftJoin']['trans_main as som'] = "export_packing.so_id = som.id";
        $queryData['leftJoin']['stock_transaction'] = "export_packing.id = stock_transaction.ref_id AND stock_transaction.ref_type = 35 AND stock_transaction.trans_type=2";
        if(!empty($postData['trans_no'])){$queryData['where']['export_packing.trans_no'] = $postData['trans_no'];}
        if(!empty($postData['package_no'])){$queryData['where']['export_packing.package_no'] = $postData['package_no'];}
        if(!empty($postData['packing_type'])){$queryData['where']['export_packing.packing_type'] = $postData['packing_type'];}
        if(!empty($postData['req_id'])){$queryData['where']['export_packing.req_id'] = $postData['req_id'];}
        if(!empty($postData['item_id'])){$queryData['where']['export_packing.item_id'] = $postData['item_id'];}
        $queryData['order_by']['export_packing.package_no'] = "ASC";
        return $this->rows($queryData);
    }

    public function getExportDataForPrint($postData){
        $queryData = array();
        $queryData['tableName'] = $this->exportPacking;
        $queryData['select'] = "export_packing.*,SUM(export_packing.total_box) as total_box,SUM(export_packing.total_qty) as total_qty,SUM(export_packing.net_wt) as netWeight,SUM(export_packing.gross_wt) as grossWeight,SUM(export_packing.pack_weight) as pack_weight,item_master.item_type,item_master.item_code,item_master.item_name,item_master.description,item_master.item_alias,item_master.part_no,hsn_master.hsn_code,hsn_master.description as hsn_desc,som.currency,stock_transaction.batch_no,stock_transaction.location_id";
        $queryData['leftJoin']['item_master'] = "item_master.id = export_packing.item_id";
        $queryData['leftJoin']['hsn_master'] = "item_master.hsn_code = hsn_master.hsn_code";
        $queryData['leftJoin']['trans_main as som'] = "export_packing.so_id = som.id";
        $queryData['leftJoin']['stock_transaction'] = "export_packing.id = stock_transaction.ref_id AND stock_transaction.ref_type = 35 AND stock_transaction.trans_type=2";
        if(!empty( $postData['trans_no'])){$queryData['where']['export_packing.trans_no'] = $postData['trans_no'];}
        if(!empty( $postData['package_no'])){$queryData['where']['export_packing.package_no'] = $postData['package_no'];}
        if(!empty( $postData['packing_type'])){$queryData['where']['export_packing.packing_type'] = $postData['packing_type'];}
        if(!empty( $postData['req_id'])){$queryData['where']['export_packing.req_id'] = $postData['req_id'];}
        if(!empty( $postData['item_id'])){$queryData['where']['export_packing.item_id'] = $postData['item_id'];}
        $queryData['group_by'][] = 'export_packing.qty_box';
        $queryData['group_by'][] = 'export_packing.item_id';
        $queryData['order_by']['export_packing.item_id'] = "ASC";
        return $this->rows($queryData);
    }

    public function getExportDetail($id){
        $queryData = array();
        $queryData['tableName'] = $this->exportPacking;
        $queryData['select'] = "export_packing.*,item_master.item_type,item_master.item_code,item_master.item_name,item_master.description,item_master.item_alias,item_master.part_no,hsn_master.hsn_code,hsn_master.description as hsn_desc,som.currency,stock_transaction.batch_no,stock_transaction.location_id,party_master.party_code,party_master.smark,packing_request.trans_way,packing_request.delivery_terms,packing_request.container_type,packing_transaction.batch_detail,som.doc_no";
        $queryData['leftJoin']['item_master'] = "item_master.id = export_packing.item_id";
        $queryData['leftJoin']['packing_request'] = "export_packing.req_id = packing_request.id";
        $queryData['leftJoin']['hsn_master'] = "item_master.hsn_code = hsn_master.hsn_code";
        $queryData['leftJoin']['trans_main as som'] = "export_packing.so_id = som.id";
        $queryData['leftJoin']['party_master'] = "som.party_id = party_master.id";
        $queryData['leftJoin']['stock_transaction'] = "export_packing.id = stock_transaction.ref_id AND stock_transaction.ref_type = 35 AND stock_transaction.trans_type=2";
        $queryData['leftJoin']['packing_master'] = "packing_master.trans_number = stock_transaction.batch_no AND packing_master.item_id = stock_transaction.item_id";
        $queryData['leftJoin']['packing_transaction'] = "packing_transaction.packing_id = packing_master.id";
        $queryData['where']['export_packing.id'] = $id;
        return $this->row($queryData);
    }

    public function getNextExportNo($packing_type){
        $data['tableName'] = $this->exportPacking;
        $data['select'] = "MAX(trans_no) as trans_no";
        $data['where']['packing_type'] = $packing_type;
		$trans_no = $this->specificRow($data)->trans_no;
		$nextTransNo = (!empty($trans_no))?($trans_no + 1):1;
		return $nextTransNo; 
    }

    public function deleteExportPacking($data){
        try { 
            $this->db->trans_begin();
            $packData = $this->getExportData(['trans_no'=>$data['trans_no'],'packing_type'=>$data['packing_type']]);
            $prevRecord = $this->getExportData(['req_id'=>$data['req_id'],'packing_type'=>($data['packing_type']==2)?1:2]);
            foreach($packData as $row){
                if($data['packing_type'] == 2){
                    $setData = Array();
                    $setData['tableName'] = 'packing_request';
                    $setData['where']['id'] = $row->req_id;
                    $setData['set']['dispatch_qty'] = 'dispatch_qty, - '.$row->total_qty;
                    $this->setValue($setData);
                    
                    $this->remove($this->stockTrans,['ref_id'=>$row->id,'ref_type'=>35]);
                }
                $this->trash($this->exportPacking,['id'=>$row->id]);
            }
            if(!empty($prevRecord) && $data['packing_type'] ==2){
                $this->store( 'packing_request',['id'=>$data['req_id'],'status'=>1]);
            }
            if(empty($prevRecord) && ($data['packing_type'] ==1 || $data['packing_type'] ==2)){
                $this->store( 'packing_request',['id'=>$data['req_id'],'status'=>0]);
            }
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return ['status'=>1,'message'=>"Successfully Deleted"];
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }
    
    public function packingTransGroupByPackage($postData){
        $queryData = array();
        $queryData['tableName'] = $this->exportPacking;
        if(!empty( $postData['trans_no'])){$queryData['where']['export_packing.trans_no'] = $postData['trans_no'];}
        if(!empty( $postData['packing_type'])){$queryData['where']['export_packing.packing_type'] = $postData['packing_type'];}
        $queryData['group_by'][] = "export_packing.package_no";
        $queryData['order_by']['cast(export_packing.package_no as unsigned)'] = "ASC";
        $result = $this->rows($queryData);
        return $result;
    }
    
    public function getPackingData($transNo,$packing_type=2){
        $queryData = array();
        $queryData['tableName'] = $this->exportPacking;
        $queryData['where']['trans_no'] = $transNo;
        $queryData['where']['packing_type'] = $packing_type;
        $result = $this->rows($queryData);
        return $result;
    }

    public function getOldStockDTRows($data){
        $data['tableName'] = $this->stockTrans;
        $data['select'] = "stock_transaction.*,item_master.item_code,item_master.item_name";
        $data['leftJoin']['item_master'] = "item_master.id = stock_transaction.item_id";
		$data['where']['stock_transaction.location_id'] = $this->PROD_STORE->id; 
		$data['where']['stock_transaction.ref_batch'] = 'OLDPACKSTOCK'; 
		$data['where']['stock_transaction.ref_type'] = -1; 
		$data['where']['item_master.item_type'] = 1; 
        
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "item_master.item_code";
        $data['serachCol'][] = "item_master.item_name";
        $data['serachCol'][] = "stock_transaction.batch_no";
        $data['serachCol'][] = "stock_transaction.qty";
        $data['serachCol'][] = "DATE_FORMAT(stock_transaction.ref_date,'%d-%m-%Y')";
        $data['serachCol'][] = "stock_transaction.remark";

		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        
        return $this->pagingRows($data);
    }
	
	public function saveStock($data){
        try{
            $this->db->trans_begin();

            $result = $this->store($this->stockTrans,$data,'Stock');
        
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }
    
    public function saveDispatchAdvice($data){
        try{
            $this->db->trans_begin();
            $whereData = array(
                'trans_no' => $data['trans_no'],
                'packing_date' => $data['packing_date']
            );
            unset($data['trans_no']);unset($data['packing_date']);

            $result = $this->edit($this->exportPacking, $whereData, $data, 'Dispatch Advice');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }
    
    public function getTentativePackingListItems($postData){
        $queryData = array();
        $queryData['tableName'] = 'export_packing';

        $queryData['select'] = "item_master.item_code,item_master.item_name,trans_child.hsn_code,trans_child.net_amount as net_amount,trans_child.price as price,trans_child.hsn_desc,SUM(export_packing.total_qty) as qty,LPAD(export_packing.package_no, 2, '0') as package_no,item_master.part_no as PartNo,item_master.drawing_no as DrgNo,item_master.rev_no as RevNo,SUM((export_packing.total_qty * export_packing.wpp)) as totalPackWt,SUM(export_packing.pack_weight) as pack_weight,SUM(export_packing.wooden_weight) as wooden_weight,export_packing.wpp,export_packing.gross_wt,SUM(export_packing.net_wt) as netWeight";

        $queryData['leftJoin']['trans_child'] = "export_packing.so_trans_id = trans_child.id AND export_packing.is_delete=0";
        $queryData['leftJoin']['item_master'] = "item_master.id = export_packing.item_id AND item_master.is_delete = 0";

        $queryData['where']['export_packing.trans_no'] = $postData['trans_no'];
        $queryData['where']['export_packing.packing_type'] = $postData['packing_type'];

        if(!empty($postData['package_no'])){ $queryData['group_by'][] = "export_packing.package_no" ;}
        
        $queryData['group_by'][] = "trans_child.item_id";
        $queryData['order_by']['export_packing.package_no'] = "ASC";
        $queryData['order_by']['trans_child.id'] = "ASC";

        return $this->rows($queryData);
    }
}
?>