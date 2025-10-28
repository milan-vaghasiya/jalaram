<?php
class JobMaterialDispatchModel extends MasterModel{
    private $jobCard = "job_card";
    private $jobMaterialDispatch = "job_material_dispatch";
    private $jobUsedMaterial = "job_used_material";
    private $purchaseTrans = "purchase_invoice_transaction";
    private $itemMaster = "item_master";
    private $purchaseRequest ="purchase_request";
    private $jobBom = "job_bom";

	// Updated By Meghavi 01-12-2021
    public function getDTRows($data){
        $data['tableName'] = $this->jobMaterialDispatch;
        $data['select'] = "job_material_dispatch.*,job_card.job_no,job_card.job_prefix,job_card.product_id,item_master.item_name as dispatch_item_name,item_master.item_type,job_card.mr_status";
        $data['leftJoin']['job_card'] = "job_material_dispatch.job_card_id = job_card.id";
        $data['leftJoin']['item_master'] = "job_material_dispatch.dispatch_item_id = item_master.id";
        if(!empty($data['material_type'])){$data['where']['job_material_dispatch.material_type'] = $data['material_type'];}
        $data['where']['job_material_dispatch.tools_dispatch_id'] = 0;
        $data['where']['job_material_dispatch.issue_type != '] = 2;
        $data['order_by']['job_card.job_no'] = 'DESC';		
        
        if($data['status'] == 0){
            $data['customWhere'][] = '(job_material_dispatch.req_qty - job_material_dispatch.dispatch_qty) > 1';
            $data['where']['job_material_dispatch.md_status'] = 0;
        }
		if($data['status'] == 1){
		    $data['customWhere'][] = '(job_material_dispatch.req_qty - job_material_dispatch.dispatch_qty) <= 1';
		    $data['where']['job_material_dispatch.md_status'] = 0;
            $data['where']['job_material_dispatch.req_date >= '] = $this->startYearDate;
		    $data['where']['job_material_dispatch.req_date <= '] = $this->endYearDate;
		}
		if($data['status'] == 2){
            $data['where']['job_material_dispatch.md_status'] = 1;
            $data['where']['job_material_dispatch.req_date >= '] = $this->startYearDate;
		    $data['where']['job_material_dispatch.req_date <= '] = $this->endYearDate;
        }

        $data['searchCol'][] = "CONCAT(job_card.job_prefix,job_card.job_no)";
        $data['searchCol'][] = "DATE_FORMAT(job_material_dispatch.req_date,'%d-%m-%Y')";
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "job_material_dispatch.req_qty";
        $data['searchCol'][] = "DATE_FORMAT(job_material_dispatch.dispatch_date,'%d-%m-%Y')";
        $data['searchCol'][] = "job_material_dispatch.dispatch_qty";

        $columns =array('','','CONCAT(job_card.job_prefix,job_card.job_no)','job_material_dispatch.req_date','job_material_dispatch.req_item_id','job_material_dispatch.req_qty','job_material_dispatch.dispatch_date','job_material_dispatch.dispatch_item_id','job_material_dispatch.dispatch_qty');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getJobMaterial($id){
        $data['tableName'] = $this->jobMaterialDispatch;
        $data['where']['id'] = $id;
        return $this->row($data);
    }

    public function getIssueBatchTrans($id){
        $data['tableName'] = "stock_transaction";
        $data['select'] = "stock_transaction.*,location_master.store_name,location_master.location";
        $data['leftJoin']['location_master'] = 'location_master.id = stock_transaction.location_id';
        $data['where']['stock_transaction.trans_ref_id'] = $id;
        $data['where']['stock_transaction.ref_type'] = 3;
        $data['where']['stock_transaction.trans_type'] = 2;
        return $this->rows($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();
            $productId = 0;
            $jobData['where']['id'] = $data['job_card_id'];
            $jobData['tableName'] = $this->jobCard;
            $jobData=$this->row($jobData);
            $productId = (!empty($jobData->product_id)?$jobData->product_id:0);
            
            if(empty($data['id'])):
                $issueData = [
                    'id' => "",
                    'job_card_id' => $data['job_card_id'],
                    'material_type' => $data['material_type'],
                    'collected_by' => $data['collected_by'],
                    'dept_id' => $data['dept_id'],
                    'process_id' => $data['process_id'],
                    'dispatch_date' => $data['dispatch_date'],
                    'dispatch_item_id' => $data['dispatch_item_id'],
                    'dispatch_qty' => array_sum($data['batch_qty']),
                    'dispatch_by' => $data['dispatch_by'],
                    'remark' => $data['remark'],
                    'req_type' => 2,
                    'created_by'  => $data['created_by'],
                ];
                $saveIssueData = $this->store($this->jobMaterialDispatch,$issueData);
                $issueId = $saveIssueData['insert_id'];

                foreach($data['batch_no'] as $key=>$value):
                    $stockTrans = [
                        'id' => "",
                        'location_id' =>$data['location_id'][$key],
                        'batch_no' => $value,
                        'trans_type' => 2,
                        'item_id' => $data['dispatch_item_id'],
                        'qty' => "-".$data['batch_qty'][$key],
                        'ref_type' => 3,
                        'ref_id' =>$data['job_card_id'],
                        'trans_ref_id' => $saveIssueData['insert_id'],
                        'ref_no'=>getPrefixNumber($jobData->job_prefix,$jobData->job_no),
                        'ref_date' => $data['dispatch_date'],
                        'created_by' => $data['created_by']
                    ];

                    $this->store('stock_transaction',$stockTrans);

                    $stockPlusTrans = [
                        'id' => "",
                        'location_id' => $this->ALLOT_RM_STORE->id,
                        'batch_no' => getPrefixNumber($jobData->job_prefix,$jobData->job_no),
                        'trans_type' => 1,
                        'item_id' => $data['dispatch_item_id'],
                        'qty' =>$data['batch_qty'][$key],
                        'ref_type' => 26,
                        'ref_id' =>$data['job_card_id'],
                        'trans_ref_id' => $saveIssueData['insert_id'],
                        'ref_no'=>getPrefixNumber($jobData->job_prefix,$jobData->job_no),
                        'ref_date' => $data['dispatch_date'],
                        'created_by' => $data['created_by'],
                        'stock_effect'=>0
                    ];

                    $this->store('stock_transaction',$stockPlusTrans);
                endforeach;

                $setData = Array();
                $setData['tableName'] = $this->itemMaster;
                $setData['where']['id'] = $data['dispatch_item_id'];
                $setData['set']['qty'] = 'qty, - '.array_sum($data['batch_qty']);
                $qryresult = $this->setValue($setData);
                
                //job card status update
                if(!empty($data['job_card_id'])):
                    $this->edit($this->jobCard,['id'=>$data['job_card_id']],['md_status'=>2]);  			
                endif;

                if ($this->db->trans_status() !== FALSE):
                    $this->db->trans_commit();
                    return ['status'=>1,'message'=>'Material Issue suucessfully.'];
                endif;
            else:
                $this->remove('stock_transaction',['ref_id'=>$data['job_card_id'],'trans_ref_id'=>$data['id'],'ref_type'=>3]);
                if(!empty($data['job_card_id'])):
                    $this->trash($this->jobUsedMaterial,['ref_id'=>$data['id'],'job_card_id' => $data['job_card_id']]);
                endif;
                $issueTransData = $this->getJobMaterial($data['id']);
                
                if(!empty($issueTransData->dispatch_qty) and $issueTransData->dispatch_qty > 0):
                    $setData = Array();
                    $setData['tableName'] = $this->itemMaster;
                    $setData['where']['id'] = $issueTransData->dispatch_item_id;
                    $setData['set']['qty'] = 'qty, + '.$issueTransData->dispatch_qty;
                    $qryresult = $this->setValue($setData);
                endif;

                $issueData = [
                    'id' => $data['id'],
                    'job_card_id' => $data['job_card_id'],
                    'material_type' => $data['material_type'],
                    'collected_by' => $data['collected_by'],
                    'dept_id' => $data['dept_id'],
                    'process_id' => $data['process_id'],
                    'dispatch_date' => $data['dispatch_date'],
                    'dispatch_item_id' => $data['dispatch_item_id'],
                    'dispatch_qty' => array_sum($data['batch_qty']),
                    'dispatch_by' => $data['dispatch_by'],
                    'remark' => $data['remark'],
                    'req_type' => 2,
                ];
                $saveIssueData = $this->store($this->jobMaterialDispatch,$issueData);

                $insertUsedMaterial = array();$updateUsedMaterial = array(); $issueBatchQty = 0;$updateWhere = array();
                foreach($data['batch_no'] as $key=>$value):
                    $stockTrans = [
                        'id' => "",
                        'location_id' => $data['location_id'][$key],
                        'batch_no' => $value,
                        'trans_type' => 2,
                        'item_id' => $data['dispatch_item_id'],
                        'qty' => "-".$data['batch_qty'][$key],
                        'ref_type' => 3,
                        'ref_id' =>$data['job_card_id'],
                        'trans_ref_id' => $data['id'],
                        'ref_no'=>getPrefixNumber($jobData->job_prefix,$jobData->job_no),
                        'ref_date' => $data['dispatch_date'],
                        'created_by' => $data['dispatch_by']
                    ];

                    $this->store('stock_transaction',$stockTrans);

                    $stockPlusTrans = [
                        'id' => "",
                        'location_id' => $this->ALLOT_RM_STORE->id,
                        'batch_no' => getPrefixNumber($jobData->job_prefix,$jobData->job_no),
                        'trans_type' => 1,
                        'item_id' => $data['dispatch_item_id'],
                        'qty' => $data['batch_qty'][$key],
                        'ref_type' => 26,
                        'ref_id' =>$data['job_card_id'],
                        'trans_ref_id' => $data['id'],
                        'ref_no'=>getPrefixNumber($jobData->job_prefix,$jobData->job_no),
                        'ref_date' => $data['dispatch_date'],
                        'created_by' => $data['dispatch_by'],
                        'stock_effect'=>0
                    ];

                    $this->store('stock_transaction',$stockPlusTrans);
                    
                    /*** Insert record in Job Used Material For Job Card Material Tracking ***/
                    if(!empty($data['job_card_id'])):
                        $wp_qty = 0;
                        $kitQuery['where']['job_bom.ref_item_id'] = $data['dispatch_item_id'];
                        $kitQuery['where']['job_bom.item_id'] = $productId;
                        $kitQuery['where']['job_bom.job_card_id'] = $data['job_card_id'];
                        $kitQuery['leftJoin']['item_master'] = 'item_master.id = job_bom.ref_item_id';
                        $kitQuery['select'] = 'job_bom.qty,item_master.item_type';
                        $kitQuery['tableName'] = 'job_bom';
                        $kitData = $this->row($kitQuery);
                        
                        if(!empty($kitData)):
                            if($kitData->item_type == 3):
                                $queryData = array();
                                $queryData['tableName'] = $this->jobUsedMaterial;
                                $queryData['where']['job_card_id'] = $data['job_card_id'];
                                $queryData['where']['process_id'] = $data['process_id'];
                                $queryData['where']['bom_item_id'] = $data['dispatch_item_id'];
                                $queryData['where']['ref_id'] = $data['id'];
                                $queryData['where']['location_id'] = $data['location_id'][$key];
                                $queryData['where']['batch_no'] = $value;
                                $queryData['where']['product_id'] = $productId;
                                $countRow = $this->numRows($queryData,0);
                                
                                $userMaterial = [
                                    'job_card_id' => $data['job_card_id'],
                                    'ref_id' => $data['id'],
                                    'process_id' => $data['process_id'],
                                    'product_id' => $productId,
                                    'bom_item_id' => $data['dispatch_item_id'],
                                    'bom_item_type' => $kitData->item_type,
                                    'wp_qty' => $kitData->qty,
                                    'issue_qty' => $data['batch_qty'][$key],
                                    'used_qty' => 0,
                                    'location_id' => $data['location_id'][$key],
                                    'batch_no' => $value,
                                    'created_by' => $data['dispatch_by'],
                                    'is_delete' => 0
                                ];
                                
                                if($countRow > 0):	
                                    $issueBatchQty += $data['batch_qty'][$key];	
                                    $userMaterial['issue_qty'] = $issueBatchQty;
                                    unset($userMaterial['used_qty']);
                                    $updateUsedMaterial[] = $userMaterial;

                                    $where['job_card_id'] = $data['job_card_id'];
                                    $where['process_id'] = $data['process_id'];
                                    $where['bom_item_id'] = $data['dispatch_item_id'];
                                    $where['ref_id'] = $data['id'];
                                    $where['location_id'] = $data['location_id'][$key];
                                    $where['batch_no'] = $value;
                                    $where['product_id'] = $productId;
                                    
                                    $updateWhere[] = $where;
                                    /* $this->edit($this->jobUsedMaterial,$where,$userMaterial); */
                                else:                                
                                    $userMaterial['id'] = "";
                                    $insertUsedMaterial[] = $userMaterial;
                                    /* $this->store($this->jobUsedMaterial,$userMaterial); */
                                endif;
                            endif;
                        endif;
                    endif;
                endforeach;

                if(!empty($insertUsedMaterial)):
                    foreach($insertUsedMaterial as $key=>$value):
                        $this->store($this->jobUsedMaterial,$value);
                    endforeach;
                endif;

                if(!empty($updateUsedMaterial)):
                    foreach($updateUsedMaterial as $key=>$value):
                        $this->edit($this->jobUsedMaterial,$updateWhere[$key],$value);
                    endforeach;
                endif;

                $issueQty = (!empty($data['batch_qty']))?array_sum($data['batch_qty']):0;
                $setData = Array();
                $setData['tableName'] = $this->itemMaster;
                $setData['where']['id'] = $data['dispatch_item_id'];
                $setData['set']['qty'] = 'qty, - '.$issueQty;
                $qryresult = $this->setValue($setData);
                
                if(!empty($data['job_card_id'])):
                    $this->edit($this->jobCard,['id'=>$data['job_card_id']],['md_status'=>2]);   
                endif;
                
                if ($this->db->trans_status() !== FALSE):
                    $this->db->trans_commit();
                    return ['status'=>1,'message'=>'Material Issue suucessfully.'];
                endif;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	        
    }

    public function delete($id){
        try{
            $this->db->trans_begin();
            $jobMaterialData = $this->getJobMaterial($id);

            $this->remove('stock_transaction',['ref_id'=>$id,'ref_type'=>3]);
            $this->remove('stock_transaction',['trans_ref_id'=>$id,'ref_id'=>$jobMaterialData->job_card_id,'ref_type'=>26]);
            $itemDataOld = $this->item->getItem($jobMaterialData->dispatch_item_id);
            $this->edit($this->itemMaster,['id'=>$jobMaterialData->dispatch_item_id],['qty'=>($itemDataOld->qty + $jobMaterialData->dispatch_qty)]);

            if(!empty($jobMaterialData->job_card_id)):
                //job card statua update
                $this->edit($this->jobCard,['id'=>$jobMaterialData->job_card_id],['md_status'=>0]);   
            endif;

            $result = $this->trash($this->jobMaterialDispatch,['id'=>$id],'Material Issue');
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    //Change By Avruti @16/08/2022
    public function savePurchaseRequest($data,$itemData){ 
        try{
            $this->db->trans_begin();
            foreach($itemData['req_item_id'] as $key=>$value):
				$data['req_no'] = $this->jobMaretialRequest->nextReqNo();
                $transData = [
                                'id' => "",
                                'job_card_id' => $data['job_card_id'],
                                'material_type' => $data['material_type'],
                                'req_date' => $data['req_date'],
                                'req_no' => $data['req_no'],
                                'req_item_id' => $value,
                                'req_item_name' => $itemData['req_item_name'][$key],
                                'req_qty' => $itemData['req_qty'][$key],
                                'fg_item_id' => (!empty($data['fg_item_id'])) ? $data['fg_item_id'] : 0,
                                'remark' => $data['remark'],
                                'created_by'  => $this->session->userdata('loginId')
                            ];
                $this->store($this->purchaseRequest,$transData);
            endforeach;
            $result =  ['status'=>1,'message'=>'Material Issue suucessfully.'];
            if($data['prtype'] == 0){$this->store($this->jobMaterialDispatch, ['id' => $data['id'],'req_status'=>1]);}
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
        return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function getToolConsumption($item_id){
        $data['tableName'] = "tool_consumption";
        $data['select'] = "tool_consumption.*,item_master.item_name,item_master.price";
        $data['join']['item_master'] = 'item_master.id = tool_consumption.ref_item_id';
        $data['where']['tool_consumption.item_id'] = $item_id;
        return $this->rows($data);
    }
    
    public function getJobBomQty($jobCardId,$refItemId){
        $queryData['tableName'] = $this->jobBom;
        $queryData['select'] = "qty";
        $queryData['where']['job_card_id'] = $jobCardId;
        $queryData['where']['ref_item_id'] = $refItemId;
        $result = $this->row($queryData);
        return $result;
    }

    /* public function save($data){
        if(empty($data['id'])):
            if(!empty($data['ptrans_id'])):
                $purchaseItemData = $this->purchaseInvoice->getPurchaseItemRow($data['ptrans_id']);
                $itemData = $this->item->getItem($data['dispatch_item_id']);
                if($data['dispatch_qty'] > $purchaseItemData->remaining_qty):
                    return ['status'=>0,'message'=>['dispatch_qty'=>"Stock not avalible."]];
                endif;
            else:
                $itemData = $this->item->getItem($data['dispatch_item_id']);
                if($data['material_type'] == 1):
                    if($data['dispatch_qty'] > $itemData->opening_remaining_qty):
                        return ['status'=>0,'message'=>['dispatch_qty'=>"Stock not avalible."]];
                    endif;
                else:
                    if($data['dispatch_qty'] > $itemData->qty):
                        return ['status'=>0,'message'=>['dispatch_qty'=>"Stock not avalible."]];
                    endif;
                endif;
            endif;

            if(!empty($data['ptrans_id'])):
                //new material lot wise stock update
                $this->edit($this->purchaseTrans,['id'=>$data['ptrans_id']],['remaining_qty'=>($purchaseItemData->remaining_qty - $data['dispatch_qty'])]);
            endif;

            if($data['material_type'] == 1 && empty($data['ptrans_id'])):
                $this->edit($this->itemMaster,['id'=>$data['dispatch_item_id']],['opening_remaining_qty'=>($itemData->opening_remaining_qty - $data['dispatch_qty'])]);         
            endif;

            //item stock update
            $this->edit($this->itemMaster,['id'=>$data['dispatch_item_id']],['qty'=>($itemData->qty - $data['dispatch_qty'])]);         
            
            if(!empty($data['job_card_id'])):
                //job card statua update
                $this->edit($this->jobCard,['id'=>$data['job_card_id']],['md_status'=>1]);   
            endif;           
        else:
            $jobMaterialData = $this->getJobMaterial($data['id']);
            if($data['dispatch_item_id'] == $jobMaterialData->dispatch_item_id):
                if(!empty($data['ptrans_id'])):
                    $purchaseItemData = $this->purchaseInvoice->getPurchaseItemRow($data['ptrans_id']);
                    $itemData = $this->item->getItem($data['dispatch_item_id']);
                    if($data['dispatch_qty'] > ($purchaseItemData->remaining_qty + $jobMaterialData->dispatch_qty)):
                        return ['status'=>0,'message'=>['dispatch_qty'=>"Stock not avalible."]];
                    endif;
                else:
                    $itemData = $this->item->getItem($data['dispatch_item_id']);
                    if($data['material_type'] == 1):
                        if($data['dispatch_qty'] > ($itemData->opening_remaining_qty + $jobMaterialData->dispatch_qty)):
                            return ['status'=>0,'message'=>['dispatch_qty'=>"Stock not avalible."]];
                        endif;
                    else:
                        if($data['dispatch_qty'] > ($itemData->qty + $jobMaterialData->dispatch_qty)):
                            return ['status'=>0,'message'=>['dispatch_qty'=>"Stock not avalible."]];
                        endif;
                    endif;
                endif;
            else:
                if(!empty($data['ptrans_id'])):
                    $purchaseItemData = $this->purchaseInvoice->getPurchaseItemRow($data['ptrans_id']);
                    $itemData = $this->item->getItem($data['dispatch_item_id']);
                    if($data['dispatch_qty'] > $purchaseItemData->remaining_qty):
                        return ['status'=>0,'message'=>['dispatch_qty'=>"Stock not avalible."]];
                    endif;
                else:
                    $itemData = $this->item->getItem($data['dispatch_item_id']);
                    if($data['material_type'] == 1):
                        if($data['dispatch_qty'] > $itemData->opening_remaining_qty):
                            return ['status'=>0,'message'=>['dispatch_qty'=>"Stock not avalible."]];
                        endif;
                    else:
                        if($data['dispatch_qty'] > $itemData->qty):
                            return ['status'=>0,'message'=>['dispatch_qty'=>"Stock not avalible."]];
                        endif;
                    endif;
                endif;
            endif;

            if(!empty($jobMaterialData->ptrans_id)):
                //old material lot wise stock update
                $oldPurchaseItemData = $this->purchaseInvoice->getPurchaseItemRow($jobMaterialData->ptrans_id);
                $this->edit($this->purchaseTrans,['id'=>$jobMaterialData->ptrans_id],['remaining_qty'=>($oldPurchaseItemData->remaining_qty + $jobMaterialData->dispatch_qty)]);
            endif;

            $oldItemData = $this->item->getItem($jobMaterialData->dispatch_item_id); 
            if(!empty($jobMaterialData->dispatch_item_id)):                
                $this->edit($this->itemMaster,['id'=>$jobMaterialData->dispatch_item_id],['qty'=>($oldItemData->qty + $jobMaterialData->dispatch_qty)]);  
            endif;

            if($jobMaterialData->material_type == 1 && empty($jobMaterialData->ptrans_id)): 
                if(!empty($jobMaterialData->dispatch_item_id)):     
                    $this->edit($this->itemMaster,['id'=>$jobMaterialData->dispatch_item_id],['opening_remaining_qty'=>($oldItemData->opening_remaining_qty + $jobMaterialData->dispatch_qty)]); 
                endif;        
            endif; 

            if(!empty($data['ptrans_id'])):
                //new material lot wise stock update
                $purchaseItemData = $this->purchaseInvoice->getPurchaseItemRow($data['ptrans_id']);
                $this->edit($this->purchaseTrans,['id'=>$data['ptrans_id']],['remaining_qty'=>($purchaseItemData->remaining_qty - $data['dispatch_qty'])]);
            endif;

            $itemData = $this->item->getItem($data['dispatch_item_id']);
            if($data['material_type'] == 1 && empty($data['ptrans_id'])):                
                $this->edit($this->itemMaster,['id'=>$data['dispatch_item_id']],['opening_remaining_qty'=>($itemData->opening_remaining_qty - $data['dispatch_qty'])]);         
            endif;            

            //item stock update
            $this->edit($this->itemMaster,['id'=>$data['dispatch_item_id']],['qty'=>($itemData->qty - $data['dispatch_qty'])]);  

            if(!empty($jobMaterialData->job_card_id)):
                //job card statua update
                $this->edit($this->jobCard,['id'=>$jobMaterialData->job_card_id],['md_status'=>0]);   
            endif;  

            if(!empty($data['job_card_id'])):
                //job card statua update
                $this->edit($this->jobCard,['id'=>$data['job_card_id']],['md_status'=>1]);   
            endif;  
        endif;

        $this->store($this->jobMaterialDispatch,$data);        
        return ['status'=>1,'message'=>"Job Card Material Dispatched."];
    } */

    /* public function delete($id){
        $jobMaterialData = $this->getJobMaterial($id);

        if(!empty($jobMaterialData->dispatch_item_id)):
            if(!empty($jobMaterialData->ptrans_id)):
                //old material lot wise stock update
                $oldPurchaseItemData = $this->purchaseInvoice->getPurchaseItemRow($jobMaterialData->ptrans_id);
                $this->edit($this->purchaseTrans,['id'=>$jobMaterialData->ptrans_id],['remaining_qty'=>($oldPurchaseItemData->remaining_qty + $jobMaterialData->dispatch_qty)]);
            endif;

        
            $oldItemData = $this->item->getItem($jobMaterialData->dispatch_item_id); 
            $this->edit($this->itemMaster,['id'=>$jobMaterialData->dispatch_item_id],['qty'=>($oldItemData->qty + $jobMaterialData->dispatch_qty)]);

            if($jobMaterialData->material_type == 1 && empty($jobMaterialData->ptrans_id)):     
                $this->edit($this->itemMaster,['id'=>$jobMaterialData->dispatch_item_id],['opening_remaining_qty'=>($oldItemData->opening_remaining_qty + $jobMaterialData->dispatch_qty)]);         
            endif;
        endif; 

        if(!empty($jobMaterialData->job_card_id)):
            //job card statua update
            $this->edit($this->jobCard,['id'=>$jobMaterialData->job_card_id],['md_status'=>0]);   
        endif;

        return $this->trash($this->jobMaterialDispatch,['id'=>$id],'Material Dispatched');
    } */
	
	// Created By Meghavi 01-12-2021
    public function closeMaterialRequest($data){
       
        $this->store($this->jobMaterialDispatch, ['id'=>$data['id'],'md_status'=>$data['md_status']]);
       
        return ['status'=>1,'message'=>"jobMaterial Dispatch Close successfully."];
    }
    
    //Created By Karmi @03/08/2022
    public function getGeneralRequestDTRows($data)
    {
        $data['tableName'] = $this->jobMaterialDispatch;
        $data['select'] = "job_material_dispatch.*,item_master.item_name";
        $data['leftJoin']['item_master'] = "job_material_dispatch.req_item_id = item_master.id";
        $data['where']['job_material_dispatch.material_type'] = 2;
        $data['where']['job_material_dispatch.issue_type'] = 2;
        $data['where']['job_material_dispatch.created_by'] = $this->loginID;
        if($data['status'] == 1){
            $data['customWhere'][] = '(job_material_dispatch.req_qty - job_material_dispatch.dispatch_qty) > 0';            
        }
		if($data['status'] == 2){
		    $data['customWhere'][] = '(job_material_dispatch.req_qty - job_material_dispatch.dispatch_qty) < 1';		   
		}

       
        $data['searchCol'][] = "DATE_FORMAT(job_material_dispatch.req_date,'%d-%m-%Y')";
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "job_material_dispatch.req_qty";
        $data['searchCol'][] = "";

        $columns =array('','','job_material_dispatch.req_date','item_master.item_name','','job_material_dispatch.req_qty','');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);

    }

    //Created By Karmi @03/08/2022
    public function saveGeneralRequest($data)
    {
        foreach($data['item_id'] as $key=>$value):
            $reqData = [
                'id'=>$data['id'],
                'req_no'=>$data['req_no'],
                'issue_type'=>$data['issue_type'],
                'material_type'=>$data['material_type'],
                'req_item_id'=>$value,
                'req_date'=>$data['req_date'],
                'req_qty'=>$data['req_qty'][$key],
                'remark'=>$data['remark'],
                'created_by' => $data['created_by']
            ];
            $this->store($this->jobMaterialDispatch, $reqData);
        endforeach;
        return $result = ['status' => 1, 'message' => 'General Request saved successfully.'];

    }
    //Created By Karmi @03/08/2022
    public function deleteGeneralRequest($id)
    {
        return $this->trash($this->jobMaterialDispatch,['id'=>$id],'General Material Request');
    }

    //Created By Karmi @03/08/2022
    public function getGeneralPendingRequestDTRows($data)
    {
        $data['tableName'] = $this->jobMaterialDispatch;
        $data['select'] = "job_material_dispatch.*,item_master.item_name,unit_master.unit_name";
        $data['leftJoin']['item_master'] = "job_material_dispatch.req_item_id = item_master.id";
        $data['leftJoin']['unit_master'] = "item_master.unit_id = unit_master.id";
        $data['where']['job_material_dispatch.material_type'] = 2;
        $data['where']['job_material_dispatch.issue_type'] = 2;
        $data['customWhere'][] = '(job_material_dispatch.req_qty - job_material_dispatch.dispatch_qty) > 0';            
        
       
        $data['searchCol'][] = "DATE_FORMAT(job_material_dispatch.req_date,'%d-%m-%Y')";
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "job_material_dispatch.req_qty";
        $data['searchCol'][] = "job_material_dispatch.dispatch_qty";
        $data['searchCol'][] = "DATE_FORMAT(job_material_dispatch.dispatch_date,'%d-%m-%Y')";
        $data['searchCol'][] = "";

        $columns =array('','','job_material_dispatch.req_date','','','','','','');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    //Created By Karmi @08/08/2022
    public function getMaxRequestNo($issue_type){
        $data['select'] = "MAX(req_no) as req_no";
        $data['where']['issue_type'] = $issue_type;
        $data['tableName'] = $this->jobMaterialDispatch;
		$req_no = $this->specificRow($data)->req_no;
		$nextReqNo = (!empty($req_no))?($req_no + 1):1;
		return $nextReqNo;
    }
    
    public function getJobMaterialIssueNpd($postData = array()){
		$data['tableName'] = $this->jobMaterialDispatch;
        $data['select'] = "job_material_dispatch.*,job_card.job_no,job_card.job_prefix,job_card.product_id,item_master.item_name as dispatch_item_name,item_master.item_type,job_card.mr_status,stockTrans.stock_batch_no";
        
		$data['leftJoin']['job_card'] = "job_material_dispatch.job_card_id = job_card.id";
        $data['leftJoin']['item_master'] = "job_material_dispatch.dispatch_item_id = item_master.id";
		$data['leftJoin']['(select stock_transaction.trans_ref_id, GROUP_CONCAT(stock_transaction.batch_no) as stock_batch_no from stock_transaction WHERE stock_transaction.ref_type = 43 group by stock_transaction.ref_id) stockTrans'] = "stockTrans.trans_ref_id = job_material_dispatch.id";
		
		if(!empty($postData->id)){			
			$data['where']['job_material_dispatch.job_card_id'] = $postData->id;
		}
	   
        $data['where']['job_material_dispatch.tools_dispatch_id'] = 0;
        $data['where']['job_material_dispatch.issue_type != '] = 2;
		$data['where']['job_material_dispatch.md_status'] = 0;
		$data['where']['job_material_dispatch.req_date >= '] = $this->startYearDate;
		$data['where']['job_material_dispatch.req_date <= '] = $this->endYearDate;
		$data['customWhere'][] = '(job_material_dispatch.req_qty - job_material_dispatch.dispatch_qty) <= 1';
		
        return $this->row($data);
	}

    
	/*  Create By : Avruti @27-11-2021 1:00 PM
    update by : 
    note : 
    */
    //----------------------------- API Function Start -------------------------------------------//

    public function getCount($status = 0, $type = 0){
        $data['tableName'] = $this->jobMaterialDispatch;
        if(!empty($data['material_type'])){$data['where']['job_material_dispatch.material_type'] = $data['material_type'];}
        $data['where']['job_material_dispatch.tools_dispatch_id'] = 0;
        if($status == 0){$data['customWhere'][] = '(job_material_dispatch.req_qty - job_material_dispatch.dispatch_qty) > 1';}
        if($status == 1){$data['customWhere'][] = '(job_material_dispatch.req_qty - job_material_dispatch.dispatch_qty) <= 1';}

        return $this->numRows($data);
    }

    public function getMaterialIssueList_api($limit, $start, $status = 0, $type = 0){
		$data['tableName'] = $this->jobMaterialDispatch;
        $data['select'] = "job_material_dispatch.*,job_card.job_no,job_card.job_prefix,job_card.product_id,
		item_master.item_name as dispatch_item_name";
        $data['leftJoin']['job_card'] = "job_material_dispatch.job_card_id = job_card.id";
        $data['leftJoin']['item_master'] = "job_material_dispatch.dispatch_item_id = item_master.id";
        //$data['where']['job_material_dispatch.material_type'] = 3;
        //$data['where']['job_material_dispatch.req_type != '] = 2;
        if(!empty($data['material_type'])){$data['where']['job_material_dispatch.material_type'] = $data['material_type'];}
        $data['where']['job_material_dispatch.tools_dispatch_id'] = 0;
        $data['order_by']['job_card.job_no'] = 'DESC';
        if($status == 0){$data['customWhere'][] = '(job_material_dispatch.req_qty - job_material_dispatch.dispatch_qty) > 1';}
        if($status == 1){$data['customWhere'][] = '(job_material_dispatch.req_qty - job_material_dispatch.dispatch_qty) <= 1';}

        $data['length'] = $limit;
        $data['start'] = $start;
        return $this->rows($data);
    }

    //------------------------------ API Function End --------------------------------------------//
}
?>