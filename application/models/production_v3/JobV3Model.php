<?php
class JobV3Model extends MasterModel{
    private $jobCard = "job_card";
    private $jobBom = "job_bom";
    private $productionApproval = "production_approval";
    private $jobTrans = "production_transaction";
    private $transMain = "trans_main";
    private $productKit = "item_kit";
    private $productProcess = "product_process";    
    private $jobMaterialDispatch = "job_material_dispatch";
    private $itemMaster = "item_master";
    private $jobUsedMaterial = "job_used_material";
    private $jobReturnMaterial = "job_return_material";
    private $stockTrans = "stock_transaction";
    private $employee = "employee_master";
    private $jobcardLog = "jobcard_log";

    public function getNextJobNo($job_type = 0){
        $data['tableName'] = $this->jobCard;
        $data['select'] = "MAX(job_no) as job_no";
        $data['where']['job_date >= '] = $this->startYearDate;
        $data['where']['job_date <= '] = $this->endYearDate;
        $data['where']['job_category'] = $job_type;
        $maxNo = $this->specificRow($data)->job_no;
		$nextJobNo = (!empty($maxNo))?($maxNo + 1):1;
		return $nextJobNo;
    }

    public function getNextBatchNo(){
        $data['tableName'] = $this->jobCard;
        $data['select'] = "MAX(batch_no) as batch_no";
        $data['where']['job_date >= '] = $this->startYearDate;
        $data['where']['job_date <= '] = $this->endYearDate;
        $maxNo = $this->specificRow($data)->batch_no;
		$nextBatchNo = (!empty($maxNo))?($maxNo + 1):1;
		return $nextBatchNo;
    }

    public function getDTRows($data,$type=0){        
        $data['tableName'] = $this->jobCard;
        $data['select'] = "job_card.*,item_master.item_name,item_master.item_code,party_master.party_name,party_master.party_code";

        $data['leftJoin']['item_master'] = "item_master.id = job_card.product_id";
        $data['leftJoin']['party_master'] = "job_card.party_id = party_master.id";
        $data['where']['job_card.job_category'] = $type;
        $data['where']['job_card.version'] = 2;
        
        // Pending
        if(isset($data['status']) && $data['status'] == 0){
            $data['where_in']['job_card.order_status'] = [0,1,2];
            $data['where']['job_card.is_npd'] = 0;
        }
        // Completed
        if(isset($data['status']) && $data['status'] == 1){
            $data['where_in']['job_card.order_status'] = [4];
            $data['where']['job_card.job_date >= '] = $this->startYearDate;
            $data['where']['job_card.job_date <= '] = $this->endYearDate;
        }
        if(isset($data['status']) && $data['status'] == 2){
            $data['where_in']['job_card.order_status'] = [5,6];
            $data['where']['job_card.job_date >= '] = $this->startYearDate;
            $data['where']['job_card.job_date <= '] = $this->endYearDate;
        }
        if(isset($data['status']) && $data['status'] == 3){
            $data['where_in']['job_card.order_status'] = [3];
        }
        if(isset($data['status']) && $data['status'] == 4){
            $data['where_in']['job_card.order_status'] = [0,1,2];
            $data['where']['job_card.is_npd'] = 1;
        }
        
        
        $data['order_by']['job_card.job_date'] = "DESC";
        $data['order_by']['job_card.id'] = "DESC";

        $data['searchCol'][] = "CONCAT(job_card.job_prefix,job_card.job_no)";
        $data['searchCol'][] = "DATE_FORMAT(job_card.job_date,'%d-%m-%Y')";
        $data['searchCol'][] = "DATE_FORMAT(job_card.delivery_date,'%d-%m-%Y')";
        $data['searchCol'][] = "party_master.party_code";
        $data['searchCol'][] = "job_card.challan_no";
        $data['searchCol'][] = "item_master.item_code";
        $data['searchCol'][] = "job_card.qty";
        $data['searchCol'][] = "job_card.remark";

		$columns =array('','','job_card.job_no','job_card.job_date','job_card.delivery_date','','job_card.party_code','job_card.challan_no','item_master.item_code','job_card.qty','','job_card.remark');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		$result = $this->pagingRows($data);
        return $result;
    }

    public function getCustomerList(){
        $data['tableName'] = $this->transMain;
        $data['select'] = "trans_main.party_id,party_master.party_name,party_master.party_code";
        $data['join']['party_master'] = "party_master.id = trans_main.party_id";
        $data['where']['trans_main.trans_status'] = 0;
        $data['where']['trans_main.entry_type'] = 4;
        $data['group_by'][] = 'trans_main.party_id';
        return $this->rows($data);
    }

    public function getCustomerSalesOrder($party_id){
        $data['tableName'] = $this->transMain;
        $data['select'] = "trans_main.id,trans_main.trans_no,trans_main.trans_prefix,trans_main.trans_date";
        $data['where']['party_id'] = $party_id;
        $data['where']['trans_status'] = 0;
        $data['where']['entry_type'] = 4;
        return $this->rows($data);
    }

    public function getProductList($data){
        $html = '<option value="">Select Product</option>';$trans_date = '';
        if(empty($data['sales_order_id'])):
            $productData = $this->item->getItemList(1);
            if(!empty($productData)):
                foreach($productData as $row):
                    $selected = (!empty($data['product_id']) && $data['product_id'] == $row->id)?"selected":"";
                    $html .= '<option value="'.$row->id.'" data-delivery_date="'.date("Y-m-d").'" data-plan_date="'.date("Y-m-d").'" data-order_type="0" '.$selected.'>'.$row->item_code.'</option>';
                endforeach;
            endif;
        else:
            //trans_child
            $queryData['select'] = "trans_child.item_id,trans_child.qty,trans_child.cod_date,trans_child.prod_target_date,trans_main.trans_date,trans_main.order_type,item_master.item_code, item_master.item_name";
            $queryData['join']['item_master'] = "trans_child.item_id = item_master.id";
            $queryData['join']['trans_main'] = "trans_child.trans_main_id = trans_main.id";
            $queryData['where']['trans_child.trans_main_id'] = $data['sales_order_id'];
            $queryData['where']['trans_child.trans_status'] = 0;
            $queryData['where']['trans_child.entry_type'] = 4;
            $queryData['tableName'] = "trans_child";
            $productData = $this->rows($queryData);
            if(!empty($productData)):
                foreach($productData as $row):
                    $selected = (!empty($data['product_id']) && $data['product_id'] == $row->item_id)?"selected":"";
                    $jobType = ($row->order_type == 1)?0:1;
                    $html .= '<option value="'.$row->item_id.'" data-delivery_date="'.((!empty($row->cod_date))?$row->cod_date:date("Y-m-d")).'" data-plan_date="'.((!empty($row->prod_target_date))?$row->prod_target_date:'').'" data-order_type="'.$jobType.'" '.$selected.'>'.$row->item_code.'(Ord. Qty. : '.$row->qty.')</option>';
					$trans_date = (!empty($row->trans_date)) ? $row->trans_date : '';
                endforeach;
            endif;
        endif;
        return ['status'=>1,'htmlData'=>$html,'productData'=>$productData,'trans_date'=>$trans_date];
    }

    public function getProductProcess($data,$id=""){
        $jobCardData = array();
        if(!empty($id)):
            $jobCardData = $this->getJobcard($id);
        endif;

        $data['select'] = "product_process.process_id,process_master.process_name,product_process.sequence";
        $data['where']['product_process.item_id'] = $data['product_id'];
        $data['where']['product_process.pfc_rev_no'] = $data['pfc_rev_no'];
        $data['join']['process_master'] = "product_process.process_id = process_master.id";
        $data['order_by']['product_process.sequence']  = 'asc';
        $data['tableName'] = $this->productProcess;
        $processData = $this->rows($data);
        $html = "";
        if(!empty($processData)):
            $i=1;
            foreach($processData as $row):
                if(!empty($jobCardData)):
                    $process = explode(",",$jobCardData->process);
                    $checked = (in_array($row->process_id,$process))?"checked":"";
                    $html .= '<input type="checkbox" id="md_checkbox_'.$i.'" name="process[]" class="filled-in chk-col-success" value="'.$row->process_id.'" '.$checked.' ><label for="md_checkbox_'.$i.'" class="mr-3">'.$row->process_name.'</label>';
                else:
                    $html .= '<input type="checkbox" id="md_checkbox_'.$i.'" name="process[]" class="filled-in chk-col-success" value="'.$row->process_id.'" checked ><label for="md_checkbox_'.$i.'" class="mr-3">'.$row->process_name.'</label>';
                endif;
                $i++;
            endforeach;
        else:
            $html = '<div class="error">Product Process not found.</div>';
        endif;

        /* Updated By :- Sweta @28-08-2023 */
        $pfcList = $this->item->revisionList(['item_id'=>$data['product_id'],'rev_type'=>1,'is_active'=>1]);
        $options = '<option value="">Select Revision</option>';
        if(!empty($pfcList)){
            foreach($pfcList as $row){
                $options.='<option value="'.$row->rev_no.'">'.$row->rev_no.'</option>';
            }
        }

        return ['htmlData'=>$html,'processData'=>$processData,'options'=>$options];
    } 

    public function save($data){
        try{
            $this->db->trans_begin();  
            $jobCardData = array();  
            if(!empty($data['id'])):
                $jobCardData = $this->getJobCard($data['id']);
                if(!empty($jobCardData->md_status) && empty($jobCardData->ref_id)):
                    return ['status'=>2,'message'=>"Production In-Process. You can't update this job card."];
                endif;

                if(!empty($jobCardData->ref_id) && !empty($jobCardData->order_status)):
                    return ['status'=>2,'message'=>"Production In-Process. You can't update this job card."];
                endif;

            else:
                $data['job_prefix'] = ($data['job_category'] == 0) ? "JC/".$this->shortYear.'/' : "JW/".$this->shortYear.'/';
                $data['job_no'] = $this->getNextJobNo($data['job_category']);
            endif;
            $data['process'] = implode(',',$data['process']);  
            $saveJobCard = $this->store($this->jobCard,$data,'Job Card');

            //set job bom
            if(empty($data['id'])):
                $queryData = array();
                $queryData['tableName'] = $this->productKit;
                $queryData['where']['item_kit.item_id'] = $data['product_id'];
                $queryData['where']['item_kit.pfc_rev_no'] = $data['pfc_rev_no'];
                $kitData = $this->rows($queryData);
                if(!empty($kitData)){
                    foreach($kitData as $kit):
                        $kit->id = "";
                        $kit->job_card_id = $saveJobCard['insert_id'];
                        $kit->created_by = $data['created_by'];
                        unset($kit->pfc_rev_no); /* Updated By :- Sweta @28-08-2023 */
                        $jobBomArray = (Array) $kit;
                        $this->store($this->jobBom,$jobBomArray,'Job BOM');
                    endforeach;
                }else{ return ['status'=>2,'message'=>"Product Bom is not set. You can't save this job card."]; }
            else:
                if($data['product_id'] != $jobCardData->product_id):
                    $this->trash("job_bom",['job_card_id'=>$data['id']]);
                    $queryData = array();
                    $queryData['tableName'] = $this->productKit;
                    $queryData['where']['item_kit.item_id'] = $data['product_id'];
                    $queryData['where']['item_kit.pfc_rev_no'] = $data['pfc_rev_no'];
                    $kitData = $this->rows($queryData);
                    if(!empty($kitData)){
                        foreach($kitData as $kit):
                            $kit->id = "";
                            $kit->job_card_id = $data['id'];
                            $kit->created_by = $data['created_by'];
                            unset($kit->pfc_rev_no); /* Updated By :- Sweta @28-08-2023 */
                            $jobBomArray = (Array) $kit;
                            $this->store($this->jobBom,$jobBomArray,'Job BOM');
                        endforeach;
                    }else{ return ['status'=>2,'message'=>"Product Bom is not set. You can't save this job card."]; }
                else:
                    $jobbom = $this->getJobBomQty($data['id'],$data['product_id']);
                    if(!empty($jobbom)):
                        $queryData = array();
                        $queryData['tableName'] = $this->productKit;
                        $queryData['where']['item_kit.item_id'] = $data['product_id'];
                        $queryData['where']['item_kit.pfc_rev_no'] = $data['pfc_rev_no'];
                        $kitData = $this->rows($queryData);
                        foreach($kitData as $kit):
                            $kit->id = "";
                            $kit->job_card_id = $data['id'];
                            $kit->created_by = $data['created_by'];
                            unset($kit->pfc_rev_no); /* Updated By :- Sweta @28-08-2023 */
                            $jobBomArray = (Array) $kit;
                            $this->store($this->jobBom,$jobBomArray,'Job BOM');
                        endforeach;
                    endif;
                endif;
            endif;
            
            /* Send Notification */
            if(empty($data['id'])):
			    $jobNo = getPrefixNumber($data['job_prefix'],$data['job_no']);
			else:
			    $jobCardData = $this->getJobCard($data['id']);
			    $jobNo = getPrefixNumber($jobCardData->job_prefix,$jobCardData->job_no);
			endif;
			$notifyData['notificationTitle'] = (empty($data['id']))?"New Job Card":"Update Job Card";
			$notifyData['notificationMsg'] = (empty($data['id']))?"New Job Card Generated. JOB. No. : ".$jobNo:"Job Card updated. JOB No. : ".$jobNo;
			$notifyData['payload'] = ['callBack' => base_url('production_v2/jobcard')];
			$notifyData['controller'] = "'production_v2/jobcard'";
			$notifyData['action'] = (empty($data['id']))?"W":"M";
			$this->notify($notifyData);
				
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $saveJobCard;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	        
    }

    public function getJobcard($id){
        $data['tableName'] = $this->jobCard;
        $data['select'] = 'job_card.*,item_master.item_code as product_code,item_master.item_name as product_name,party_master.party_name,party_master.party_code,unit_master.unit_name,employee_master.emp_name,trans_main.trans_prefix,trans_main.trans_no,item_master.drawing_no,item_master.rev_no';
        $data['leftJoin']['item_master'] = 'item_master.id = job_card.product_id';
        $data['leftJoin']['party_master'] = 'party_master.id = job_card.party_id';
        $data['leftJoin']['unit_master'] = "item_master.unit_id = unit_master.id";
        $data['leftJoin']['employee_master'] = "job_card.created_by = employee_master.id";
        $data['leftJoin']['trans_main'] = "job_card.sales_order_id = trans_main.id";
        $data['where']['job_card.id'] = $id;
        return $this->row($data);
    }

    public function getJobcardList(){
        $data['tableName'] = $this->jobCard;
        $data['select'] = 'job_card.*,item_master.item_code,item_master.item_name';
        $data['join']['item_master'] = 'item_master.id = job_card.product_id';
        $data['where_in']['job_card.order_status'] = [0,1,2];
        $data['where']['job_card.job_category !='] = 2;
        $data['where']['job_card.job_date >= '] = date('Y-m-d',strtotime($this->startYearDate.' -1 YEAR'));
        $data['where']['job_card.job_date <= '] = $this->endYearDate;
        return $this->rows($data); 
    }

    public function jobCardNoList(){
        $data['tableName'] = $this->jobCard;
        $data['select'] = 'job_card.id,job_card.job_no,job_card.job_prefix,job_card.job_date,item_master.item_code,item_master.item_name';
        $data['join']['item_master'] = 'item_master.id = job_card.product_id';
        $data['where']['job_card.order_status != '] = 5;
        $data['where']['job_card.job_category !='] = 2;
        return $this->rows($data); 
    }

    public function delete($id){
        try{
            $this->db->trans_begin();
            $jobCardData = $this->getJobCard($id);
            if(!empty($jobCardData->md_status) && empty($jobCardData->ref_id)):
                $result = ['status'=>0,'message'=>"Production In-Process. You can't Delete this job card."];
            endif;

            if(!empty($jobCardData->ref_id) && !empty($jobCardData->order_status)):
                $result = ['status'=>0,'message'=>"Production In-Process. You can't Delete this job card."];
            endif;

            $this->trash($this->jobMaterialDispatch,['job_card_id'=>$id,'is_delete'=>0]);
            $this->trash($this->jobBom,['job_card_id'=>$id]);
            $result = $this->trash($this->jobCard,['id'=>$id],"Job Card");

                /* Send Notification */
                $jobNo = getPrefixNumber($jobCardData->job_prefix,$jobCardData->job_no);
                $notifyData['notificationTitle'] = "Delete Job Card";
                $notifyData['notificationMsg'] = "Job Card deleted. JOB No. : ".$jobNo;
                $notifyData['payload'] = ['callBack' => base_url('production_v2/jobcard')];
                $notifyData['controller'] = "'production_v2/jobcard'";
                $notifyData['action'] = "D";
                $this->notify($notifyData);

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function getProcessWiseRequiredMaterial($data){
        $queryData['tableName'] = $this->jobBom;
        $queryData['select'] = "job_bom.id,job_bom.ref_item_id,job_bom.qty,job_bom.process_id,item_master.item_name,item_master.item_code,item_master.qty as stockQty,item_master.item_type,unit_master.unit_name,job_used_material.batch_no,material_master.scrap_group";
        $queryData['join']['item_master'] = "job_bom.ref_item_id = item_master.id";
        $queryData['leftJoin']['material_master'] = 'item_master.material_grade = material_master.material_grade';
        $queryData['leftJoin']['job_used_material'] = "job_used_material.job_card_id = job_bom.job_card_id AND job_used_material.bom_item_id = job_bom.ref_item_id";
        $queryData['leftJoin']['unit_master'] = "item_master.unit_id = unit_master.id";
        $queryData['where']['job_bom.item_id'] = $data->product_id;
        $queryData['where']['job_bom.job_card_id'] = $data->id;
        $kitData = $this->rows($queryData);
            
        $resultData = array();
        if(!empty($kitData)):
          $i=1;$html="";
          foreach($kitData as $row):
            $issueQty=0;$inQty=0;$pendingQty=0;
            
            $queryData = array();
            $queryData['tableName'] = $this->stockTrans;
            $queryData['select'] = "SUM(CASE WHEN trans_type = 2 THEN abs(qty) ELSE 0 END) as issue_qty,SUM(CASE WHEN trans_type = 2 THEN qty ELSE 0 END)as used_qty";
            $queryData['where']['ref_id'] = $data->id;
            $queryData['where']['item_id'] = $row->ref_item_id;
            $queryData['where']['ref_type'] = 3;
            $result = $this->row($queryData);
            
            $queryData = array();
            $queryData['tableName'] = $this->jobMaterialDispatch;
            $queryData['select'] = "SUM(ex_qty) as ex_qty";
            $queryData['where']['job_card_id'] = $data->id;
            $queryData['where']['dispatch_item_id'] = $row->ref_item_id;
            $exQty = $this->row($queryData)->ex_qty;
            $exQty = round(((!empty($exQty))?$exQty:0),3);
            
            $issueQty = round(((!empty($result->issue_qty))?($result->issue_qty+$exQty):0),3);

            $queryData = array();
            $queryData['tableName'] = $this->productionApproval;
            $queryData['select'] = "out_qty as in_qty";
            $queryData['where']['job_card_id'] = $data->id;
            $queryData['where']['in_process_id'] = 0;
            $queryData['where']['trans_type'] = 0;
            $queryData['where']['is_delete'] = 0;
            $apData = $this->row($queryData);
            $in_qty = (!empty($apData))?$apData->in_qty:0;

            $queryData = array();
            $queryData['tableName'] = $this->stockTrans;
            $queryData['select'] = "SUM(CASE WHEN ref_type = 10 THEN qty ELSE 0 END) as return_qty,SUM(CASE WHEN ref_type = 18 THEN qty ELSE 0 END) as scrap_qty";
            $queryData['where']['ref_id'] = $data->id;
            $queryData['where']['item_id'] = $row->ref_item_id;
            $rQty = $this->row($queryData);
            $returnQty = (!empty($rQty->return_qty)?round($rQty->return_qty,3):0);
            
            //Scrap & End PCS
            $scrapData = [];
            if(!empty($row->scrap_group)){
                $queryData = array();
                $queryData['tableName'] = $this->stockTrans;
                $queryData['select'] = "SUM(CASE WHEN ref_type = 18 THEN qty ELSE 0 END) as scrap_qty";
                $queryData['where']['ref_id'] = $data->id;
                $queryData['where']['item_id'] = $row->scrap_group;
                $scrapData = $this->row($queryData);
            }
			$scrapQty = (!empty($scrapData->scrap_qty)?round($scrapData->scrap_qty,3):0);
            
            $inQty = (!empty($in_qty)?round(($row->qty*$in_qty),3):0);            
            $pendingQty = round(($result->issue_qty-(abs($result->used_qty))),3);
            $firstProcess = explode(",",$data->process)[0];
            $productName = $this->item->getItem($data->product_id)->item_name;
            $processName = $this->process->getProcess($firstProcess)->process_name;
            
            $pendQty=round(($issueQty-$inQty-$returnQty-$scrapQty-(!empty($rQty->scrap_qty)?$rQty->scrap_qty:0)),3);
            $pendingStockQty = (!empty($pendQty)?$pendQty:0);
            $button = "";
            
            $buttonScrap = '<a class="btn btn-outline-success openMaterialScrapModal" href="javascript:void(0)" datatip="Return Material Scrap" flow="left" data-ref_id="0" data-product_id="'.$data->product_id.'" data-in_process_id="'.$firstProcess.'" data-job_card_id="'.$data->id.'" data-product_name="'.$productName.'" data-process_name="'.$processName.'" data-pending_qty="'.$pendingStockQty.'" data-item_name="'.$row->item_name.'" data-item_id="'.$row->ref_item_id.'" data-wp_qty="'.$row->qty.'" data-modal_id="modal-lg"><i class="fas fa-reply" ></i></a>';

            $deleteBtn = "";
            if(isset($data->job_order_status) && $data->job_order_status == 0 && $row->item_type == 3 && empty($issueQty)):
                $deleteBtn = '<a class="btn btn-outline-danger" href="javascript:void(0)" datatip="Delete" flow="left" onclick="removeBomItem('.$row->id.','.$data->id.')"><i class="ti-trash"></i></a>';
            endif;

            $html .= '<tr class="text-center">
                        <td>'.$i++.'</td>
                        <td class="text-left">'.$row->item_name.'</td>
                        <td>'.$row->qty.'</td>
                        <td>'.($row->qty*$data->qty).'</td>
                        <td>'.$issueQty.'</td>
                        <td>'.($row->qty*$in_qty).'</td>
                        <td>'.$pendingStockQty.'</td>
                        <td>'.$button.$buttonScrap.$deleteBtn.'</td>
                      </tr>';
            $resultData[] = ['item_id'=>$row->ref_item_id,'item_name'=>$row->item_name,'bom_qty'=>$row->qty,'heat_no'=>$row->batch_no,'req_qty'=>($row->qty*$data->qty),'issue_qty'=>$issueQty,'pending_qty'=>$pendingQty,'pending_stock_qty'=>$pendingStockQty,'unit_name'=>$row->unit_name];
          endforeach;
          $result = $html;
        else:
            $result = '<tr><td colspan="8" class="text-center">No result found.</td></tr>';
        endif;
    
        return ['status'=>1,'message'=>'Data Found.','result'=>$result,"resultData"=>$resultData];
    }

    public function getSupplierDetailOnBatchNo($data){
        $queryData = [];
        $queryData['tableName'] = "grn_transaction";
        $queryData['select'] = "grn_master.party_id,party_master.party_name,grn_transaction.price";

        $queryData['leftJoin']['grn_master'] = "grn_master.id = grn_transaction.grn_id";
		$queryData['leftJoin']['party_master'] = "party_master.id = grn_master.party_id";

        $queryData['where']['grn_transaction.batch_no'] = $data['batch_no'];

        $queryData['order_by']['grn_transaction.id'] = "DESC";
        $queryData['limit'] = 1;

        $result = $this->row($queryData);
        return $result;
    }

    public function getRequestItemData($id,$process_id = 0){
        $jobCardData = $this->getJobcard($id);

        $queryData['tableName'] = $this->jobBom;
        $queryData['select'] = "job_bom.*,item_master.item_name,item_master.item_type,item_master.qty as stock_qty,unit_master.unit_name";
        $queryData['join']['item_master'] = "job_bom.ref_item_id = item_master.id";
        $queryData['join']['unit_master'] = "unit_master.id = item_master.unit_id";
        $queryData['where']['job_bom.item_id'] = $jobCardData->product_id;
        $queryData['where']['job_bom.process_id'] = $process_id;
        $queryData['where']['job_bom.job_card_id'] = $id;
        $kitData = $this->rows($queryData);

        $dataRows = array();
        foreach($kitData as $row):            
            $row->request_qty = $row->qty * $jobCardData->qty;
            $dataRows[] = $row;
        endforeach;

        return $dataRows;
    }

    public function saveMaterialRequest($data){
        try{
            $this->db->trans_begin();
            $jobCardData = $this->getJobcard($data['job_id']);
            $i=1;$wpcs = 0;$totalWeight = 0;
            foreach($data['bom_item_id'] as $key=>$value):  
                $materialDispatchData = [
                    'id' => "",
                    'material_type' => $data['material_type'][$key],
                    'job_card_id' => $data['job_id'],
                    'req_date' => formatDate($data['req_date'],'Y-m-d'),
                    'req_item_id' => $value,
                    'req_qty' => $data['request_qty'][$key],
                    'process_id' => explode(",",$jobCardData->process)[0],
                    'machine_id' => $data['machine_id'],
                    'location_id' => $data['location_id'][$key],
                    'batch_no' => $data['batch_no'][$key],
                    'created_by' => $data['created_by']
                ];
                $this->store($this->jobMaterialDispatch,$materialDispatchData);
                if($data['material_type'][$key] == 1):
                    if($i == 1):
                        $wpcs = $data['bom_qty'][$key];
                        $totalWeight = $data['request_qty'][$key];
                        $i++;
                    endif;
                endif;
            endforeach;
            
            
            $jobData = [
                'w_pcs' => $wpcs,
                'total_weight' => $totalWeight,
                'md_status'=>1
            ];
            $this->edit($this->jobCard,['id'=>$data['job_id']],$jobData); 
            $result = ['status'=>1,'message'=>'Material Request send successfully.'];
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function changeJobStatus($data){
        if($data['order_status'] == 1):
            $jobData = $this->getJobcard($data['id']);
            if($jobData->md_status != 2):
                return ['status'=>0,'message'=>"Required Material is not issued yet! Please Issue material before start"];
            endif;
        endif;
        $this->store($this->jobCard,$data);
        $msg ="";
        if($data['order_status'] == 1){
            $this->sendJobApproval($data['id']);
			$msg = "Start";
		}else if($data['order_status'] == 3){
			$msg = "Hold";
		}else if($data['order_status'] == 2){
			$msg = "Restart";
		}else if($data['order_status'] == 5){
			$msg = "Close";
		}else if($data['order_status'] == 4){
			$msg = "Reopen";
		}else if($data['order_status'] == 6){
            //$this->shortCloseJobcard($data['id']);
			$msg = "Close";
		}
        return ['status'=>1,'message'=>"Job Card ".$msg." successfully."];
    }

    public function sendJobApproval($id){
        try {
            $jobCardData = $this->getJobcard($id);
            $processIds = explode(",", "0,".$jobCardData->process);
            $counter = count($processIds);
            $preFinishedWeight = 0;
            for($i=0;$i<$counter;$i++):
                $finishedWeight = 0;$output_qty = 1; $rqc_stage='';
                if($i == 0):
                    $queryData = array();
                    $queryData['tableName'] = $this->jobBom;
                    $queryData['select'] = "job_bom.id,job_bom.ref_item_id,job_bom.qty,job_bom.process_id";
                    $queryData['where']['item_id'] = $jobCardData->product_id;
                    $queryData['where']['job_card_id'] = $jobCardData->id;
                    $queryData['order_by']['id'] = "ASC";
                    $kitData = $this->row($queryData);

                    $finishedWeight = $preFinishedWeight = (!empty($kitData))?$kitData->qty:0;
                else:
                    if(isset($processIds[$i])):
                        $queryData = array();
                        $queryData['tableName'] = $this->productProcess;
                        $queryData['where']['item_id'] = $jobCardData->product_id;
                        $queryData['where']['process_id'] = $processIds[$i];
                        $productProcessData = $this->row($queryData);

                        $pfcQuery = array();
                        $pfcQuery['tableName'] = 'pfc_trans';
                        $pfcQuery['select'] = 'MAX(output_operation) as output_qty';
                        $pfcQuery['where']['item_id'] = $jobCardData->product_id;
                        $pfcQuery['where_in']['process_no'] = $productProcessData->pfc_process;
                        $pfcData = $this->row($pfcQuery);
                        if(!empty($pfcData->output_qty) AND ($pfcData->output_qty > 0)){$output_qty = $pfcData->output_qty;}

                        if(!empty($productProcessData)){
                            $stgData = $this->controlPlanV2->checkPFCStage(['pfc_id'=>$productProcessData->pfc_process,'item_id'=>$jobCardData->product_id,'rev_no'=>$jobCardData->pfc_rev_no]);
                            $rqc_stage=(!empty($stgData))?$stgData->stage_type:'';
                        }

                        $finishedWeight = (!empty($productProcessData))?(($productProcessData->finished_weight>0)?$productProcessData->finished_weight:$preFinishedWeight):$preFinishedWeight;
                    else:
                        $finishedWeight = $preFinishedWeight;
                    endif;
                endif;

                $approvalData = [
                    'id' => "",
                    'entry_date' => date("Y-m-d"),
                    'job_card_id' => $jobCardData->id,
                    'product_id' => $jobCardData->product_id,
                    // 'in_process_id' => ($i == 0)?0:$processIds[($i - 1)],
                    'in_process_id' =>$processIds[$i],
                    'inward_qty' => ($i == 0)?$jobCardData->qty:0,
                    'in_qty' => ($i == 0)?$jobCardData->qty:0,
                    'total_ok_qty' => 0,
                    'out_qty' => 0,
                    'in_w_pcs' => ($i == 0)?$jobCardData->w_pcs:0,
                    'in_total_weight' => ($i == 0)?$jobCardData->total_weight:0,
                    'out_process_id' => (isset($processIds[$i+1]))?$processIds[$i+1]:0,
                    'pre_finished_weight' => ($i == 0)?$finishedWeight:$preFinishedWeight,
                    'finished_weight' => $finishedWeight,
                    'output_qty' => $output_qty,
                    'stage_type'=>$rqc_stage,
                    'created_by' => $jobCardData->created_by
                ];
                $preFinishedWeight = $finishedWeight;
                $this->store($this->productionApproval,$approvalData);
            endfor;  
            $result = $this->store($this->jobCard,['id'=>$jobCardData->id,'order_status'=>2]);      
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return true;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function saveJobBomItem($postData){
        try{
            $this->db->trans_begin();
            $result = $this->store("job_bom",$postData,'Bom Item');
            $jobData = $this->getJobcard($postData['job_card_id']);
            $jobData->job_order_status = $jobData->order_status;
            $result['result'] = $this->getProcessWiseRequiredMaterial($jobData)['result'];
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function deleteBomItem($id,$job_card_id){
        try{
            $this->db->trans_begin();
            $result = $this->trash("job_bom",['id'=>$id],'Bom Item');
            $jobData = $this->getJobcard($job_card_id);
            $jobData->job_order_status = $jobData->order_status;
            $result['result'] = $this->getProcessWiseRequiredMaterial($jobData)['result'];
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function getJobBomQty($jobCardId,$item_id){
        $queryData['tableName'] = $this->jobBom;
        $queryData['select'] = "qty";
        $queryData['where']['job_card_id'] = $jobCardId;
        $queryData['where']['item_id'] = $item_id;
        $result = $this->row($queryData);
        return $result;
    }
 
    public function addJobStage($data){ 
		$saveJobCard = Array();
        if(!empty($data['id'])):
            $jobCardData = $this->getJobCard($data['id']);
			$process = explode(",",$jobCardData->process);
			$process[] = $data['process_id'];
			$newProcesses = implode(',',$process); 
			
			$saveJobCard = $this->store($this->jobCard,['id'=>$data['id'],'process'=>$newProcesses],'Job Card');

            $queryData = array();
            $queryData['tableName'] = $this->productionApproval;
            $queryData['where']['trans_type'] = 0;
            $queryData['where']['job_card_id'] = $data['id'];
            $queryData['order_by']['id'] = "DESC";
            $approvalData = $this->row($queryData);

            if(!empty($approvalData)):
                $this->store($this->productionApproval,['id'=>$approvalData->id,'out_process_id'=>$data['process_id']]);            
                $this->store($this->productionApproval,['id'=>"",'entry_date'=>date("Y-m-d"),'job_card_id'=>$data['id'],'product_id'=>$approvalData->product_id,'in_process_id'=>$data['process_id'],'out_process_id'=>0,'created_by'=>$data['created_by']]);                        
            endif;
        endif;
        return $this->getJobStages($data['id']);
    }

    public function updateJobProcessSequance($data){
        try{
            $this->db->trans_begin();
            $saveJobCard = array();
            if(!empty($data['id'])):
                $newProcesses = $data['process_id']; 
                if(!empty($data['rnstages'])){
                    $newProcesses = $data['rnstages'] .','. $data['process_id'];
                }
                $saveJobCard = $this->store($this->jobCard,['id'=>$data['id'],'process'=>$newProcesses],'Job Card');
                

                $queryData = array();
                $queryData['tableName'] = $this->productionApproval;
                $queryData['where']['trans_type'] = 0;
                $queryData['where']['job_card_id'] = $data['id'];
                $approvalData = $this->rows($queryData);
                if(!empty($approvalData)):
                    $rnStage = (!empty($data['rnstages']))?explode(",",$data['rnstages']):[0];
                    $newProcessesStage = explode(",",$data['process_id']);
                    
                    $countRnStage = count($rnStage);
                    $i=0;$j=0;$previusSatge=0;$previusSatgeId=0;

                    foreach($approvalData as $row):
                        if($i > $previusSatge):                        
                            /* print_r(['id'=>$row->id,'in_process_id'=>$previusSatgeId,'out_process_id'=>(isset($newProcessesStage[$i]))?$newProcessesStage[$i]:0]);
                            print_r("---"); */
                            $this->store($this->productionApproval,['id'=>$row->id,'in_process_id'=>$previusSatgeId,'out_process_id'=>(isset($newProcessesStage[$i]))?$newProcessesStage[$i]:0]);
                            $previusSatgeId = (isset($newProcessesStage[$i]))?$newProcessesStage[$i]:0;
                            $previusSatge = $i;
                            $i++;
                        endif;
                        if($row->in_process_id == $rnStage[($countRnStage-1)]):
                            /* print_r(['id'=>$row->id,'out_process_id'=>$newProcessesStage[$i]]);
                            print_r("---"); */
                            $this->store($this->productionApproval,['id'=>$row->id,'out_process_id'=>$newProcessesStage[$i]]);  
                            $previusSatgeId = $newProcessesStage[$i];
                            $previusSatge = $i;                      
                            $i++;
                        endif;                    
                    endforeach;
                endif;
            endif;
            $result = $this->getJobStages($data['id']);
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
	}

    public function removeJobStage($data){ 
        try{
            $this->db->trans_begin();
            $saveJobCard = Array();
            if(!empty($data['id'])):
                $jobCardData = $this->getJobCard($data['id']);
                $process = explode(",",$jobCardData->process);
                $updateProcesses = Array();
                foreach ($process as $pid){if($pid != $data['process_id']){$updateProcesses[] = $pid;}}
                $newProcesses = implode(',',$updateProcesses); 
                
                $saveJobCard = $this->store($this->jobCard,['id'=>$data['id'],'process'=>$newProcesses],'Job Card');

                $queryData = array();
                $queryData['tableName'] = $this->productionApproval;
                $queryData['where']['trans_type'] = 0;
                $queryData['where']['job_card_id'] = $data['id'];
                $approvalData = $this->rows($queryData);
                if(!empty($approvalData)):
                    $jobProcess = explode(",","0,".$newProcesses.",0");
                    foreach($approvalData as $row):
                        $nextProcessId =((count($process)-1)!=array_search($data['process_id'],$process))? $process[array_search($data['process_id'],$process) + 1]:0;
                        if(!in_array($row->out_process_id,$jobProcess)):
                            $this->store($this->productionApproval,['id'=>$row->id,"out_process_id"=>$nextProcessId]);
                        endif;
                        if(!in_array($row->in_process_id,$jobProcess)):
                            $this->trash($this->productionApproval,['id'=>$row->id]);
                        endif;
                    endforeach;
                endif;
            endif;
            $result = $this->getJobStages($data['id']);
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function getJobStages($job_id){
		$stageRows="";$pOptions='<option value="">Select Stage</option>';
		$jobCardData = $this->getJobCard($job_id);
		$process = explode(",",$jobCardData->process);
		
		if (!empty($process)) :
			$i = 0;$inQty = 0;
			foreach ($process as $pid) :
				$process_name = (!empty($pid))?$this->process->getProcess($pid)->process_name:"Initial Stage";
				$jobProcessData = $this->processApprove_v2->getProcessWiseApprovalData($job_id,$pid);
                $inQty = (!empty($jobProcessData))?$jobProcessData->in_qty:0;
                $inwardQty = (!empty($jobProcessData))?$jobProcessData->inward_qty:0;
				if($inQty <= 0 and $inwardQty == 0  and $i > 0):
					$stageRows .= '<tr id="' . $pid . '">
						<td class="text-center">' . $i . '</td>
						<td>' . $process_name . '</td>
						<td class="text-center">' . ($i+1) . '</td>
						<td class="text-center">
							<button type="button" data-pid="'.$pid.'" class="btn btn-outline-danger waves-effect waves-light removeJobStage"><i class="ti-trash"></i></button>
						</td>
					  </tr>';
				endif;$i++;
			endforeach;
		else :
			$stageRows .= '<tr><td colspan="3" class="text-center">No Data Found.</td></tr>';
		endif;
		$processDataList = $this->process->getProcessList();
		foreach ($processDataList as $row):
			if(!empty($process) && (!in_array($row->id, $process))):
				$pOptions .= '<option value="' . $row->id . '">' . $row->process_name . '</option>';
			endif;
		endforeach;
		
		return [$stageRows,$pOptions];
	}

    public function getBatchNoForReturnMaterial($job_id,$item_id){
        $queryData = array();$dispatchIds = array();
        $queryData['tableName'] = $this->jobMaterialDispatch;
        $queryData['select'] = "id";
        $queryData['where']['dispatch_item_id'] = $item_id;
        $queryData['where']['job_card_id'] = $job_id;
        $dispatchIds = $this->rows($queryData);
        $issueIds=array();

        $options = '<option value="">Select Batch No.</option>';
        if(!empty($dispatchIds)):
            foreach($dispatchIds as $row):
                $issueIds[] = $row->id; 
            endforeach;

            $queryData = array();
            $queryData['tableName'] = "stock_transaction";
            $queryData['select'] = "batch_no";
            $queryData['where']['trans_type'] = 2;
            $queryData['where']['item_id'] = $item_id;
            $queryData['where']['ref_type'] = 3;
            $queryData['where_in']['ref_id'] = $job_id;
            $queryData['where_in']['trans_ref_id'] = $issueIds;
            $queryData['group_by'][] = "batch_no";
            $batchNoList = $this->rows($queryData);

            
            foreach($batchNoList as $row):
                $options .= '<option value="'.$row->batch_no.'">'.$row->batch_no.'</option>';
            endforeach;
        endif;

        return ['status'=>1,'options'=>$options];
    }

    public function materialReceived($data){
        $jobCardData = $this->getJobcard($data['id']);
        if($jobCardData->md_status != 2):
            return ['status'=>0,'message'=>'Job Material has been not dispatch from store.'];
        endif;

        $queryData = array();
        $queryData['tableName'] = "stock_transaction";
        $queryData['where']['trans_type'] = 1;
        $queryData['where']['ref_type'] = 26;
        $queryData['where']['ref_id'] = $data['id'];
        $queryData['where']['location_id'] = $this->ALLOT_RM_STORE->id;
        $queryData['group_by'][] = "batch_no";
        $issueRMList = $this->rows($queryData);

        $jobData=$this->getJobcard($data['id']);
        foreach($issueRMList as $row){
            $stockMinusTrans = [
                'id' => "",
                'location_id' =>$row->location_id,
                'batch_no' =>$row->batch_no,
                'trans_type' => 2,
                'item_id' => $row->item_id,
                'qty' =>'-'.$row->qty,
                'ref_type' => 26,
                'ref_id' =>$row->ref_id,
                'trans_ref_id' =>$row->trans_ref_id,
                'ref_no'=>$row->ref_no,
                'ref_date' =>date("Y-m-d"),
                'created_by' => $this->session->userdata('loginId'),
                'stock_effect'=>0
            ];

            $this->store('stock_transaction',$stockMinusTrans);

            $stockPlusTrans = [
                'id' => "",
                'location_id' =>$this->RCV_RM_STORE->id,
                'batch_no' =>$row->batch_no,
                'trans_type' => 1,
                'item_id' => $row->item_id,
                'qty' =>$row->qty,
                'ref_type' => 27,
                'ref_id' =>$row->ref_id,
                'trans_ref_id' =>$row->id,
                'ref_no'=>$row->ref_no,
                'ref_date' =>date("Y-m-d"),
                'created_by' => $this->session->userdata('loginId'),
                'stock_effect'=>0
            ];

            $this->store('stock_transaction',$stockPlusTrans);
        }

        $this->store($this->jobCard,$data);
        return ['status'=>1,'message'=>"Material received successfully."];
    }

    public function getLastActivitLog($id){
        $data['tableName'] = 'production_log';
        $data['where']['job_card_id'] = $id;
        $data['order_by']['created_at'] = "DESC";
        $data['order_by']['updated_at'] = "DESC";
        $data['limit'] = 5;
        $result = $this->rows($data);

        return $result;
    }

    public function getLastTrans($id){
        $data['tableName'] = "production_log";
        $data['select'] = 'MAX(updated_at) as updated_at, id';
        $data['where']['job_card_id'] = $id;
        $data['orderBy']['updated_at'] = "DESC";
        return $this->row($data);
    }

    public function getJobcardRowMaterial($job_card_id){
        $queryData['tableName'] = $this->jobUsedMaterial;
        $queryData['select'] = "job_used_material.bom_item_id as item_id,item_master.item_name,item_master.material_grade,job_used_material.batch_no";
        $queryData['join']['item_master'] = "job_used_material.bom_item_id = item_master.id";
        $queryData['where']['job_used_material.job_card_id'] = $job_card_id;
        return $this->row($queryData);
    }

    public function saveProductionScrape($data){
        try{
            $this->db->trans_begin();
            
            $scrap_status = ($data['qty'] > 0)?2:1;
            if($data['qty'] > 0):
                $result = $this->store($this->stockTrans,$data,'Scrape');
                $updateQuery['tableName'] = $this->itemMaster;
                $updateQuery['where']['id']=$data['item_id']; 
                $updateQuery['set']['qty']='qty, +'.$data['qty'];
                $this->setValue($updateQuery);
            endif;
            $result =$this->store($this->jobCard,['id'=>$data['ref_id'],'scrap_status'=>$scrap_status]);
            
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }
    
    public function getProcessWiseRequiredMaterials($data){
        $queryData['tableName'] = $this->jobBom;
        $queryData['select'] = "job_bom.id,job_bom.ref_item_id,job_bom.qty,job_bom.process_id,item_master.item_name,item_master.item_code,item_master.qty as stockQty,item_master.item_type";
        $queryData['join']['item_master'] = "job_bom.ref_item_id = item_master.id";
        $queryData['where']['job_bom.item_id'] = $data->product_id;
        $queryData['where']['job_bom.job_card_id'] = $data->id;
        $kitData = $this->rows($queryData);
            
        $resultData = array();
        if(!empty($kitData)):
          $i=1;$html="";
          foreach($kitData as $row):
            $issueQty=0;
            $queryData = array();
            $queryData['tableName'] = $this->jobMaterialDispatch;
            $queryData['select'] = "SUM(dispatch_qty + ex_qty) as issue_qty";
            $queryData['where']['job_card_id'] = $data->id;
            $queryData['where']['dispatch_item_id'] = $row->ref_item_id;
            $issueQty = $this->row($queryData)->issue_qty;
            $issueQty = round(((!empty($issueQty))?$issueQty:0),3);
    
            $batch_no='';
            $queryData = array();
            $queryData['tableName'] = $this->jobUsedMaterial;
            $queryData['where']['job_card_id'] = $data->id;
            $batchData = $this->row($queryData); 
            $batch_no = ((!empty($batchData))?$batchData->batch_no:'');
    
            $resultData[] = ['material_name'=>$row->item_name,'issue_qty'=>$issueQty,'heat_no'=>$batch_no];
          endforeach;
        endif;
    
        return ['status'=>1,'message'=>'Data Found.',"resultData"=>$resultData];
    }
	
	public function getJobApprovalDetail($job_card_id, $process_id)
    {
        $queryData['tableName'] = $this->productionApproval;
        $queryData['where']['production_approval.in_process_id'] = $process_id;
        $queryData['where']['production_approval.job_card_id'] = $job_card_id;
        return $this->row($queryData);
    }
    
    /* Created By avruti  @ 11/04/2022 */
    /* Process identification tag Print Data */
	public function getOutwardTransPrintV3($id){
        $data['tableName'] = 'production_log';
        $data['select'] = "production_log.*, production_approval.trans_type,job_card.job_no, job_card.job_prefix,item_master.item_name, item_master.item_code,process_master.process_name,process.process_name as next_process , department_master.name as dept_name,job_used_material.batch_no as job_batch_no";
        $data['leftJoin']['production_approval'] = "production_log.job_approval_id = production_approval.id";
        // $data['leftJoin']['party_master'] = "production_log.vendor_id = party_master.id";
        $data['leftJoin']['job_card'] = "job_card.id = production_log.job_card_id";
        $data['leftJoin']['item_master'] = "item_master.id = production_log.product_id";
        $data['leftJoin']['process_master as process'] = "process.id = production_approval.out_process_id";
        $data['leftJoin']['process_master'] = "process_master.id = production_log.process_id";
        $data['leftJoin']['department_master'] = "department_master.id = process_master.dept_id";
        $data['leftJoin']['job_used_material'] = "job_used_material.job_card_id = production_log.job_card_id";
        $data['where']['production_log.prod_type'] = 4;
        $data['where']['production_log.id'] = $id;
        $result = $this->row($data);
        return $result;
    }
    
	public function getMaterialIssueData($data){
        $queryData['tableName'] = $this->jobBom;
        $queryData['select'] = "job_bom.id,job_bom.ref_item_id,job_bom.qty,job_bom.process_id,item_master.item_name,item_master.item_code,item_master.qty as stockQty,item_master.item_type";
        $queryData['join']['item_master'] = "job_bom.ref_item_id = item_master.id";
        $queryData['where']['job_bom.item_id'] = $data->product_id;
        $queryData['where']['job_bom.job_card_id'] = $data->id;
        $kitData = $this->rows($queryData);
            
        $resultData = array();
        if(!empty($kitData)):
          $i=1;$html="";
          foreach($kitData as $row):
            $issueQty=0;
            $queryData = array();
            $queryData['tableName'] = $this->stockTrans;
            $queryData['select'] = "SUM(CASE WHEN trans_type = 1 THEN qty ELSE 0 END) as issue_qty,SUM(CASE WHEN trans_type = 2 THEN qty ELSE 0 END)as used_qty";
            $queryData['where']['ref_id'] = $data->id;
            $queryData['where']['item_id'] = $row->ref_item_id;
            $queryData['where']['ref_type'] = 27;
            $result = $this->row($queryData);
            $issueQty = round(((!empty($issueQty))?$issueQty:0),3);
            
            $queryData = array();
            $queryData['tableName'] = $this->jobUsedMaterial;
            $queryData['select'] = "job_used_material.batch_no, grn_master.party_id,party_master.party_name";
			$queryData['leftJoin']['grn_transaction'] = "grn_transaction.item_id = job_used_material.bom_item_id AND grn_transaction.batch_no = job_used_material.batch_no";
			$queryData['leftJoin']['grn_master'] = "grn_master.id = grn_transaction.grn_id";
			$queryData['leftJoin']['party_master'] = "party_master.id = grn_master.party_id";
            $queryData['where']['job_used_material.job_card_id'] = $data->id;
            $batchData = $this->row($queryData); 
            
            $batch_no = (!empty($batchData->batch_no)) ? $batchData->batch_no : '';
            $supplier_id = (!empty($batchData->party_id)) ? $batchData->party_id : 0;
            $supplier_name = (!empty($batchData->party_name)) ? $batchData->party_name : '';
            
            /*$supplier='';
            $queryData = array();
            $queryData['tableName'] = 'party_master';
            $queryData['select'] = "party_name";
            $queryData['where']['id'] = $supplier_id;
            $supplierData = $this->row($queryData);*/

            $resultData[] = ['material_name'=>$row->item_name,'issue_qty'=>$result->issue_qty,'used_qty'=>$result->used_qty,'heat_no'=>$batch_no,'supplier_name'=>$supplier_name];
          endforeach;
        endif;
    
        return ['status'=>1,'message'=>'Data Found.',"resultData"=>$resultData];
    }
    
    public function returnOrScrapeSave($data){
        try{
            $this->db->trans_begin();
            $jobData = $this->jobcard_v2->getJobcard($data['job_card_id']);
            /** Minus stock in received material */
            $queryData = array();
            $queryData['tableName'] = "stock_transaction";
            $queryData['where']['trans_type'] = 1;
            $queryData['where']['ref_type'] = 27;
            $queryData['where']['ref_id'] = $data['job_card_id'];
            $queryData['where']['location_id'] = $this->RCV_RM_STORE->id;
            $queryData['group_by'][] = "batch_no";
            $issueRMList = $this->rows($queryData);
            if(!empty($issueRMList)):
                foreach($issueRMList as $row):
                    $stockMinusTrans = [
                        'id' => "",
                        'location_id' =>$this->RCV_RM_STORE->id,
                        'batch_no' =>$row->batch_no,
                        'trans_type' => 2,
                        'item_id' => $row->item_id,
                        'qty' =>'-'.$data['qty'],
                        'ref_type' => 27,
                        'ref_id' =>$row->ref_id,
                        'ref_no'=>$row->ref_no,
                        'ref_date' =>(isset($data['entry_date']))?$data['entry_date']:date("Y-m-d"),
                        'created_by' => $this->session->userdata('loginId')
                    ];
                    $this->store('stock_transaction',$stockMinusTrans);

            $stockTrans = [
                'id' => "",
                'location_id' => $data['location_id'],
                'batch_no' => $data['batch_no'],
                'trans_type' => 1,
                'item_id' => $data['item_id'],
                'qty' => $data['qty'],
                'ref_type' => 10,
                'ref_id' => $data['job_card_id'],
                'ref_no' => getPrefixNumber($jobData->job_prefix,$jobData->job_no),
                'ref_date' => (isset($data['entry_date']))?$data['entry_date']:date("Y-m-d"),
                'created_by' => $data['created_by']
            ];
            $this->store("stock_transaction",$stockTrans);
                endforeach;
            endif;
            $msg = ($data['trans_type'] == 1)?"Return Stock":"Scrape";
            $result = ['status'=>1,'message'=>$msg." saved successfully.",'result'=>$this->getReturnOrScrapeTrans(['type'=>$data['trans_type'],'process_id'=>$data['process_id'],'ref_id'=>$data['ref_id'],'job_card_id'=>$data['job_card_id']])];

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
        return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function deleteRetuenOrScrapeItem($id){
        try{
            $this->db->trans_begin();
            $data['tableName'] = $this->jobMaterialReturn;
            $data['where']['id'] = $id;
            $returnData = $this->row($data);

            if($returnData->type == 1):
                $this->remove("stock_transaction",['ref_type'=>10,'ref_id'=>$id]);

                $setData = Array();
                $setData['tableName'] = $this->itemMaster;
                $setData['where']['id'] = $returnData->item_id;
                $setData['set']['qty'] = 'qty, + '.$returnData->qty;
                $qryresult = $this->setValue($setData);
            endif;

            $this->trash($this->jobMaterialReturn,['id'=>$id]);

            $msg = ($returnData->type == 1)?"Return Stock":"Scrape";
            $result = ['status'=>1,'message'=>$msg." deleted successfully.",'result'=>$this->getReturnOrScrapeTrans(['type'=>$returnData->type,'process_id'=>$returnData->process_id,'ref_id'=>$returnData->ref_id,'job_card_id'=>$returnData->job_card_id])];
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function getReturnOrScrapeTrans($data){
        $queryData['tableName'] = $this->stockTrans;
        $queryData['select'] = "stock_transaction.*,item_master.item_name,unit_master.unit_name";
        $queryData['join']['item_master'] = "item_master.id = stock_transaction.item_id";
        $queryData['join']['unit_master'] = "unit_master.id = item_master.unit_id";
        $queryData['where']['stock_transaction.ref_type'] = 10;
        $queryData['where']['stock_transaction.ref_id'] = $data['job_card_id'];
        $result = $this->rows($queryData);

        $i=1;$html="";$functionName = "";
        foreach($result as $row):
            $functionName = ($data['type'] == 1)?"deleteReturn":"deleteScrape";
            $button = '<button type="button" onclick="'.$functionName.'('.$row->id.','.$row->qty.');" class="btn btn-outline-danger waves-effect waves-light"><i class="ti-trash"></i></button>';
            $operatorName = "";
            if(!empty($row->operator_id)):
                $queryData=array();
                $queryData['where']['id'] = $row->operator_id;
                $queryData['tableName'] = $this->employee;
                $operatorName = $this->row($queryData)->emp_name;
            endif;	
			$html .= '<tr>
                        <td style="width:5%;">'.$i++.'</td>
                        <td>'.$row->item_name.'</td>
                        <td>'.$row->qty.' ('.$row->unit_name.')</td>
                        <td></td>
                        <td class="operatorCol">'.$operatorName.'</td>
						<td class="text-center" style="width:10%;">
							'.$button.'
						</td>
					</tr>';
		endforeach;

        $sendData['result'] = $result;
        $sendData['resultHtml'] = $html;
        // $sendData['itemOption'] = $this->getReturnOrScrapeItemList($data['job_card_id'],$data['process_id']);        
		return $sendData;
    }
    
    public function getJobPendingQty($job_card_id){
        $data['tableName'] = $this->productionApproval;
        $data['where']['in_process_id'] = 0;
        $data['where']['job_card_id'] = $job_card_id;
        return $this->row($data);
    } 

    public function getJobLogData($job_card_id){
        $data['tableName'] = $this->jobcardLog;
        $data['where']['job_card_id'] = $job_card_id;
        return $this->rows($data);
    }

    public function getJobLog($id){
        $data['tableName'] = $this->jobcardLog;
        $data['where']['id'] = $id;
        return $this->row($data);
    }

    public function saveJobQty($data){
        try{
            $operation = ($data['log_type'] == 1)?'+':'-';
            $logResult = $this->store($this->jobcardLog, $data, 'Jobcard Log');

            $updateQuery = array();
            $updateQuery['tableName'] = $this->jobCard;
            $updateQuery['where']['id'] = $data['job_card_id'];
            $updateQuery['set']['qty'] = 'qty,'.$operation . $data['qty'];
            $this->setValue($updateQuery);

            $updateQuery = array();
            $updateQuery['tableName'] = $this->productionApproval;
            $updateQuery['where']['job_card_id'] = $data['job_card_id'];
            $updateQuery['where']['in_process_id'] = 0;
            $updateQuery['set']['in_qty'] = 'in_qty, '.$operation . $data['qty'];
            $this->setValue($updateQuery);
            
            
            
                
            $jobUsedQuery['tableName']=$this->jobUsedMaterial;
            $jobUsedQuery['where']['job_card_id']=$data['job_card_id'];
            $jobRslt=$this->rows($jobUsedQuery);
            $jobData=$this->getJobcard($data['job_card_id']);
            if($operation == '+'){
                $queryData['tableName'] = $this->jobBom;
                $queryData['select'] = "job_bom.id,job_bom.ref_item_id,job_bom.qty,job_bom.process_id";
                $queryData['where']['job_bom.job_card_id'] = $data['job_card_id'];
                $kitData = $this->rows($queryData);
                foreach($kitData as $kit){
                    $setData = array();
                    $setData['tableName'] = $this->jobMaterialDispatch;
                    $setData['where']['job_card_id'] = $data['job_card_id'];
                    $setData['where']['req_item_id'] = $kit->ref_item_id;
                    $setData['set']['ex_qty'] = 'ex_qty, + ' . ($data['qty']*$kit->qty);
                    $this->setValue($setData);
                }
                
            }
            foreach($jobRslt as $row){
                $stockPlusTrans = [
                    'id' => "",
                    'location_id' =>$this->RCV_RM_STORE->id,
                    'batch_no' =>getPrefixNumber($jobData->job_prefix,$jobData->job_no),
                    'trans_type' => ($data['log_type'] == 1)?1:2,
                    'item_id' => $row->bom_item_id,
                    'qty' =>(($data['log_type'] == 1)?'':'-').($data['qty']*$row->wp_qty),
                    'ref_type' => 27,
                    'ref_id' =>$data['job_card_id'],
                    'trans_ref_id' =>$row->id,
                    'ref_batch' =>$logResult['insert_id'],
                    'ref_no'=>getPrefixNumber($jobData->job_prefix,$jobData->job_no),
                    'ref_date' =>date("Y-m-d"),
                    'created_by' => $this->session->userdata('loginId'),
                    'stock_effect'=>0
                ];
    
                $this->store('stock_transaction',$stockPlusTrans);
            }

            $result = $this->getJobLogData($data['job_card_id']);

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function deleteJobUpdateQty($id){
        try{
            $logData = $this->getJobLog($id);
            $operation = ($logData->log_type == 1)?'-':'+';
            $this->trash($this->jobcardLog, ['id' => $id], 'Jobcard Log');

            $updateQuery = array();
            $updateQuery['tableName'] = $this->jobCard;
            $updateQuery['where']['id'] = $logData->job_card_id;
            $updateQuery['set']['qty'] = 'qty,'.$operation . $logData->qty;
            $this->setValue($updateQuery);

            $updateQuery = array();
            $updateQuery['tableName'] = $this->productionApproval;
            $updateQuery['where']['job_card_id'] = $logData->job_card_id;
            $updateQuery['where']['in_process_id'] = 0;
            $updateQuery['set']['in_qty'] = 'in_qty, '.$operation . $logData->qty;
            $this->setValue($updateQuery);
            
            $this->remove($this->stockTrans,['ref_batch'=>$id,'ref_type'=>27,'ref_id'=>$logData->job_card_id]);
            $result = $this->getJobLogData($logData->job_card_id);
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif; 
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	 
    }
    
    /*****************************************/
    /* Material Return , Scrap , Used in Job */
    public function saveMaterialScrap($data){ 
        try{
            $this->db->trans_begin();
            
            $pendingdata = $this->getJobPendingQty($data['job_card_id']);
            $pendingQty = $pendingdata->in_qty - $pendingdata->out_qty;
            if($data['ref_type'] != 10){
                if($data['qty'] > ($pendingQty * $data['wp_qty'])){
                    $errorMessage['qty'] = "Qty is Invalid.";
                    return ['status'=>0,'message'=>$errorMessage];
                }
            }
            if($data['job_qty'] > $pendingQty){
                $errorMessage['job_qty'] = "Qty is Invalid.";
                return ['status'=>0,'message'=>$errorMessage];
            }
            
            $jobData=$this->getJobcard($data['job_card_id']);
            if($data['job_qty'] > 0){
                $jobcard = array();
                $jobcard['tableName'] = $this->jobCard;
                $jobcard['where']['id'] = $jobData->id;
                $jobcard['set']['qty'] = 'qty, - '.$data['job_qty'];
                $jobcard['set']['short_qty'] = 'short_qty, + '.$data['job_qty'];
                $this->setValue($jobcard);

                $jobApprove = array();
                $jobApprove['tableName'] = $this->productionApproval;
                $jobApprove['where']['id'] = $pendingdata->id;
                $jobApprove['set']['in_qty'] = 'in_qty, - '.$data['job_qty'];
                $jobApprove['set']['inward_qty'] = 'inward_qty, - '.$data['job_qty'];
                $this->setValue($jobApprove);

                $queryData = array();
                $queryData['tableName'] = $this->productionApproval;
                $queryData['where']['job_card_id'] = $jobData->id;
                $queryData['where']['out_process_id'] = 0;
                $lastPending = $this->row($queryData);
                
                
                $logData=$this->logSheet->getPrdLogOnProcessNJob($jobData->id);
                $totalQty = $logData->rejection_qty + $lastPending->out_qty; 
                if ($totalQty >= ($jobData->qty - $data['job_qty'])) :
                    $this->store($this->jobCard, ['id' => $jobData->id, 'order_status' => 4]);
                endif;
            }
            
            /** Minus stock in received material */
            $stockMinusTrans = [
                'id' => "",
                'location_id' =>$this->RCV_RM_STORE->id,
                'batch_no' =>getPrefixNumber($jobData->job_prefix,$jobData->job_no),
                'trans_type' => 2,
                'item_id' => $data['item_id'],
                'qty' =>'-'.$data['qty'],
                'ref_type' => 27,
                'ref_id' =>$data['job_card_id'],
                'ref_no' =>getPrefixNumber($jobData->job_prefix,$jobData->job_no),
                'ref_date' =>date("Y-m-d"),
                'created_by' => $this->session->userdata('loginId'),
                'stock_effect'=>0
            ];
            $result=$this->store('stock_transaction',$stockMinusTrans);
            if($data['ref_type'] != 27 && $data['ref_type'] != 18){
                $stockTrans = [
                    'id' => "",
                    'location_id' => $data['location_id'],
                    'batch_no' => (!empty($data['batch_no']) && $data['ref_type']==10)?$data['batch_no']:getPrefixNumber($jobData->job_prefix,$jobData->job_no),
                    'trans_type' => 1,
                    'item_id' => $data['item_id'],
                    'qty' => $data['qty'],
                    'ref_type' => $data['ref_type'],
                    'ref_id' => $data['job_card_id'],
                    'ref_no' => $result['insert_id'],
                    'ref_date' => date("Y-m-d"),
                    'ref_batch'=>$data['job_qty'],
                    'created_by' => $data['created_by'],
                ];
                $this->store("stock_transaction",$stockTrans);
            }
            if($data['ref_type'] == 18){
                $stockTrans = [
                    'id' => "",
                    'location_id' => $data['location_id'],
                    'batch_no' => (!empty($data['batch_no']))?$data['batch_no']:getPrefixNumber($jobData->job_prefix,$jobData->job_no),
                    'trans_type' => 1,
                    'item_id' => $data['scrap_id'],
                    'qty' => $data['qty'],
                    'ref_type' => $data['ref_type'],
                    'ref_id' => $data['job_card_id'],
                    'ref_no' => $result['insert_id'],
                    'ref_date' => date("Y-m-d"),
                    'ref_batch'=>$data['job_qty'],
                    'created_by' => $data['created_by'],
                ];
                $this->store("stock_transaction",$stockTrans);
            }
            if($data['ref_type'] == 10){
                $pendingOutQty=0;$updateJobQty=0;
			    if(!empty($pendingdata)){ $pendingOutQty = $pendingdata->in_qty - $pendingdata->out_qty; }
                if($pendingOutQty > 0  && $pendingOutQty > $data['pending_qty_pcs']){
                    $updateJobQty=$data['pending_qty_pcs'];
                }else{
                    $updateJobQty=$pendingOutQty;
                }
                $updateQuery = array();
                $updateQuery['tableName'] = $this->jobCard;
                $updateQuery['where']['id'] = $data['job_card_id'];
                $updateQuery['set']['qty'] = 'qty, -' . $updateJobQty;
                $this->setValue($updateQuery);

                $updateQuery = array();
                $updateQuery['tableName'] = $this->productionApproval;
                $updateQuery['where']['job_card_id'] = $data['job_card_id'];
                $updateQuery['where']['in_process_id'] = 0;
                $updateQuery['set']['in_qty'] = 'in_qty, -' .$updateJobQty;
                $updateQuery['set']['inward_qty'] = 'inward_qty, -' .$updateJobQty;
                $this->setValue($updateQuery);
                
                $jobCardData = $this->getJobCard($data['job_card_id']); 
                $logData=$this->logSheet->getPrdLogOnProcessNJob($data['job_card_id']);
                
                $queryData = array();
                $queryData['tableName'] = $this->productionApproval;
                $queryData['where']['out_process_id'] = 0;
                $queryData['where']['trans_type'] = 0;
                $queryData['where']['job_card_id'] = $data['job_card_id'];
                $approvalData = $this->row($queryData);

                $totalQty = $logData->rejection_qty + $approvalData->out_qty;
                if ($totalQty >= $jobCardData->qty):
                    $this->store($this->jobCard, ['id' => $jobCardData->id, 'order_status' => 4]);
                endif;
            }
            $result = ['status'=>1,'message'=>"Scrap saved successfully.",'result'=>$this->getScrapeTrans(['job_card_id'=>$data['job_card_id']])];

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function getScrapeTrans($data){
        $queryData['tableName'] = $this->stockTrans;
        $queryData['select'] = "stock_transaction.*,item_master.item_name,unit_master.unit_name";
        $queryData['join']['item_master'] = "item_master.id = stock_transaction.item_id";
        $queryData['join']['unit_master'] = "unit_master.id = item_master.unit_id";
        $queryData['where_in']['stock_transaction.ref_type'] = '10,18';
        $queryData['where']['stock_transaction.ref_id'] = $data['job_card_id'];
        $result = $this->rows($queryData);

        $i=1;$html="";$functionName = "";
        foreach($result as $row):
            $functionName = "deleteScrap";
            $button = '<button type="button" onclick="'.$functionName.'('.$row->id.','.$row->qty.');" class="btn btn-outline-danger waves-effect waves-light"><i class="ti-trash"></i></button>';
            
            $return_type = ($row-> ref_type == 10)? 'Material Return' : 'Scrap/End Pcs.';
			$html .= '<tr>
                        <td style="width:5%;">'.$i++.'</td>
                        <td>'.$row->item_name.'</td>
                        <td>'.$return_type.'</td>
                        <td>'.$row->qty.' ('.$row->unit_name.')</td>
                        <td>'.$row->ref_batch.'</td>
						<td class="text-center" style="width:10%;">
							'.$button.'
						</td>
					</tr>';
		endforeach;

        $sendData['result'] = $result;
        $sendData['resultHtml'] = $html;       
		return $sendData;
    }
    
    public function deleteScrap($id){
        try{
            $this->db->trans_begin();
            $queryData['tableName'] = $this->stockTrans;
            $queryData['where']['stock_transaction.id'] = $id;
            $returnData=$this->row($queryData);
            if($returnData->ref_type == 10){
                $kitQuery['tableName'] = $this->jobBom;
                $kitQuery['where']['job_bom.ref_item_id'] = $returnData->item_id;
                $kitQuery['where']['job_bom.job_card_id'] = $returnData->ref_id;
                $kitData = $this->row($kitQuery);

                $updateJobQty=round($returnData->qty/$kitData->qty);
                $updateQuery = array();
                $updateQuery['tableName'] = $this->jobCard;
                $updateQuery['where']['id'] = $returnData->ref_id;
                $updateQuery['set']['qty'] = 'qty, +' .$updateJobQty;
                $this->setValue($updateQuery);

                $updateQuery = array();
                $updateQuery['tableName'] = $this->productionApproval;
                $updateQuery['where']['job_card_id'] = $returnData->ref_id;
                $updateQuery['where']['in_process_id'] = 0;
                $updateQuery['set']['in_qty'] = 'in_qty, +' .$updateJobQty;
                $updateQuery['set']['inward_qty'] = 'inward_qty, +' .$updateJobQty;
                $this->setValue($updateQuery);
            }
            if($returnData->ref_type == 18){
                if($returnData->ref_batch > 0){
                    $jobcard = array();
                    $jobcard['tableName'] = $this->jobCard;
                    $jobcard['where']['id'] = $returnData->ref_id;
                    $jobcard['set']['qty'] = 'qty, + '.$returnData->ref_batch;
                    $jobcard['set']['short_qty'] = 'short_qty, - '.$returnData->ref_batch;
                    $this->setValue($jobcard);
    
                    $queryData = array();
                    $queryData['tableName'] = $this->productionApproval;
                    $queryData['where']['job_card_id'] = $returnData->ref_id;
                    $queryData['where']['in_process_id'] = 0;
                    $pendingdata = $this->row($queryData);

                    $jobApprove = array();
                    $jobApprove['tableName'] = $this->productionApproval;
                    $jobApprove['where']['id'] = $pendingdata->id;
                    $jobApprove['set']['in_qty'] = 'in_qty, + '.$returnData->ref_batch;
                    $jobApprove['set']['inward_qty'] = 'inward_qty, + '.$returnData->ref_batch;
                    $this->setValue($jobApprove);
    
                    $queryData = array();
                    $queryData['tableName'] = $this->productionApproval;
                    $queryData['where']['job_card_id'] = $returnData->ref_id;
                    $queryData['where']['out_process_id'] = 0;
                    $lastPending = $this->row($queryData);
                    
                    $jobData = $this->getJobcard($returnData->ref_id);
                    $logData=$this->logSheet->getPrdLogOnProcessNJob($returnData->ref_id);
                    $totalQty = $logData->rejection_qty + $lastPending->out_qty;
                    if ($totalQty <= $jobData->qty) :
                        $this->store($this->jobCard, ['id' =>$returnData->ref_id, 'order_status' => 2]);
                    endif;
                }
            }
            $this->remove("stock_transaction",['id'=>$returnData->ref_no]);
            $this->remove("stock_transaction",['id'=>$id]);
           
            $result = ['status'=>1,'message'=>"Scrap deleted successfully.",'result'=>$this->getScrapeTrans(['job_card_id'=>$returnData->ref_id])];
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }
    
    public function finishedWeightEffectOnRuningJobs($product_id){
        try{
            $this->db->trans_begin();

            $queryData = array();
            $queryData['tableName'] = $this->jobCard;
            $queryData['select'] = 'id,product_id,process';
            $queryData['where']['product_id'] = $product_id;
            $queryData['where_in']['order_status'] = [0,1,2,3];
            $result = $this->rows($queryData);

            foreach($result as $job):
                $queryData = array();
                $queryData['tableName'] = $this->productionApproval;
                $queryData['where']['job_card_id'] = $job->id;
                $approvalData = $this->rows($queryData);

                $preFinishedWeight = 0;$postData = array();
                foreach($approvalData as $row):
                    $finishedWeight = 0;
                    if($row->in_process_id == 0):
                        $queryData = array();
                        $queryData['tableName'] = $this->jobBom;
                        $queryData['select'] = "job_bom.id,job_bom.ref_item_id,job_bom.qty,job_bom.process_id";
                        $queryData['where']['job_bom.item_id'] = $job->product_id;
                        $queryData['where']['job_bom.job_card_id'] = $job->id;
                        $queryData['order_by']['id'] = "ASC";
                        $kitData = $this->row($queryData);
        
                        $finishedWeight = $preFinishedWeight = (!empty($kitData))?$kitData->qty:0;
                    else:
                        $queryData = array();
                        $queryData['tableName'] = $this->productProcess;
                        $queryData['where']['item_id'] = $job->product_id;
                        $queryData['where']['process_id'] = $row->in_process_id;
                        $productProcessData = $this->row($queryData);

                        $finishedWeight = (!empty($productProcessData))?(($productProcessData->finished_weight>0)?$productProcessData->finished_weight:$preFinishedWeight):$preFinishedWeight;
                    endif;
                    
                    $postData = [
                        'id' => $row->id,                    
                        'pre_finished_weight' => $preFinishedWeight,
                        'finished_weight' => $finishedWeight
                    ];
                    $preFinishedWeight = $finishedWeight;
                    $this->store($this->productionApproval,$postData);
                endforeach;
            endforeach;

            $result = ['status'=>1,'message'=>"Finished Weight updated successfully."];

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>0,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }

    public function shortCloseJobcard($id){
        try{
            $this->db->trans_begin();  
            $jobData=$this->getJobcard($id);

            $queryData['tableName'] = $this->productionApproval;
            $queryData['where']['job_card_id'] = $id;
            $approvalData = $this->rows($queryData);

            foreach($approvalData as $row){
                /** Remove Stock from process sore */
                if(!empty($row->in_process_id)){
                    $curentPrsStore = $this->processApprove_v2->getProcessStore($row->in_process_id);
                    $stockQuery['tableName']="stock_transaction";
                    $stockQuery['select']="SUM(qty) as qty,batch_no";
                    $stockQuery['where']['location_id']=$curentPrsStore->id;
                    $stockQuery['where']['item_id']=$jobData->product_id;
                    $stockQuery['where']['ref_id']=$id;
                    $stockQuery['where_in']['ref_type']='23,24';
                    $stockQuery['group_by'][]="batch_no";
                    $stockData=$this->rows($stockQuery);
                    if($stockData){
                       foreach($stockData as $stk){
                            if($stk->qty > 0){
                                $stockMinusTrans = [
                                    'id' => "",
                                    'location_id' => $curentPrsStore->id,
                                    'batch_no' => $stk->batch_no,
                                    'trans_type' => 2,
                                    'item_id' => $jobData->product_id,
                                    'qty' => '-' . $stk->qty,
                                    'ref_type' => 32,
                                    'ref_id' => $id,
                                    'ref_no' => getPrefixNumber($jobData->job_prefix, $jobData->job_no),
                                    'trans_ref_id' => $row->id,
                                    'ref_date' => date("Y-m-d"),
                                    'ref_batch'=>'Short Close',
                                    'created_by' => $this->loginId,
                                    'stock_effect' => 0
                                    ];
                                    $result=$this->store($this->stockTrans,$stockMinusTrans);
                            }
                       }
                    }
                }
            }
            /** Remove Stock from Hold area */
            $stockHldQuery['tableName']="stock_transaction";
            $stockHldQuery['select']="SUM(qty) as qty,batch_no";
            $stockHldQuery['where']['location_id']=$this->HLD_STORE->id;
            $stockHldQuery['where']['item_id']=$jobData->product_id;
            $stockHldQuery['where']['ref_id']=$id;
            $stockHldQuery['where']['ref_type']=23;
            $stockHldData=$this->row($stockHldQuery);
            if($stockHldData->qty > 0){
                $stockMinusTrans = [
                    'id' => "",
                    'location_id' => $this->HLD_STORE->id,
                    'batch_no' => $stockHldData->batch_no,
                    'trans_type' => 2,
                    'item_id' => $jobData->product_id,
                    'qty' => '-' . $stockHldData->qty,
                    'ref_type' => 32,
                    'ref_id' => $id,
                    'ref_no' => getPrefixNumber($jobData->job_prefix, $jobData->job_no),
                    'ref_date' => date("Y-m-d"),
                    'ref_batch'=>'Short Close',
                    'created_by' => $this->loginId,
                    'stock_effect' => 0
                    ];
                    $result=$this->store($this->stockTrans,$stockMinusTrans);
            }

            /******* Minus stock in received material ********/
            $process = explode(",",$jobData->process);
            $firstApprvData = $this->getJobApprovalDetail($id,$process[0]);
            $outQty = $firstApprvData->total_ok_qty + $firstApprvData->total_rejection_qty;
            if($outQty > 0){
                $queryData = array();
                $queryData['tableName'] = "stock_transaction";
                $queryData['where']['trans_type'] = 1;
                $queryData['where']['ref_type'] = 27;
                $queryData['where']['ref_id'] = $id;
                $queryData['where']['location_id'] = $this->RCV_RM_STORE->id;
                $queryData['group_by'][] = "batch_no";
                $issueRMList = $this->rows($queryData);
                if(!empty($issueRMList)):
                    foreach($issueRMList as $row):
                        $bomQuery['tableName'] = $this->jobBom;
                        $bomQuery['where']['ref_item_id'] = $row->item_id;
                        $bomQuery['where']['job_card_id'] = $id;
                        $bomData = $this->row($bomQuery);
    
                        $stockMinusTrans = [
                            'id' => "",
                            'location_id' =>$this->RCV_RM_STORE->id,
                            'batch_no' =>$row->batch_no,
                            'trans_type' => 2,
                            'item_id' => $row->item_id,
                            'qty' =>'-'.($bomData->qty*$outQty),
                            'ref_type' => 27,
                            'ref_id' =>$row->ref_id,
                            'trans_ref_id' =>'',
                            'ref_no'=>$row->ref_no,
                            'ref_date' =>date("Y-m-d"),
                            'created_by' => $this->session->userdata('loginId'),
                            'stock_effect' => 0
                        ];
                        $this->store('stock_transaction',$stockMinusTrans);
                    endforeach;
                endif;
            }
            
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return ['status'=>1,'message'=>'Jobcard Shortclose'];
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>0,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }

    public function getJobBomData($param = []){
        $queryData['tableName'] = $this->jobBom;
        $queryData['select'] = "job_bom.id,job_bom.ref_item_id,job_bom.qty,job_bom.process_id,item_master.item_name,item_master.item_code,item_master.qty as stockQty,item_master.item_type,scrap.item_name as scrap_name,material_master.scrap_group";
        $queryData['join']['item_master'] = "job_bom.ref_item_id = item_master.id";
        $queryData['join']['material_master'] = "material_master.material_grade = item_master.material_grade";
        $queryData['join']['item_master scrap'] = "material_master.scrap_group = scrap.id";
        if(!empty($param['item_id'])){$queryData['where']['job_bom.item_id'] =$param['item_id'];}
        if(!empty($param['job_card_id'])){$queryData['where']['job_bom.job_card_id'] =$param['job_card_id'];}
        if(!empty($param['item_type'])){$queryData['where']['item_master.item_type'] =$param['item_type'];}
        if(!empty($param['single_row'])){
            return $this->row($queryData);
        }else{
            return $this->rows($queryData);
        }
       
    }

    public function saveInProcessScrap($data){ 
        try{
            $this->db->trans_begin();
            $stock_trans_id = 0;
            if(!empty($data['qty'])){
                $stockTrans = [
                    'id' => "",
                    'location_id' => $this->SCRAP_STORE->id,
                    'batch_no' => $data['batch_no'],
                    'trans_type' => 1,
                    'item_id' => $data['item_id'],
                    'qty' => $data['qty'],
                    'ref_type' => 45,
                    'ref_id' => $data['job_card_id'],
                    'trans_ref_id' => $data['job_approval_id'],
                    'ref_no' => $data['job_number'],
                    'ref_date' => $data['ref_date'],
                    'remark' => $data['remark'],
                    'created_by' => $this->loginId,
                    'created_at' => date("Y-m-d h:i:s"),
                ];
                $result = $this->store("stock_transaction",$stockTrans);
                $stock_trans_id = $result['insert_id'];
            }

            if(!empty($data['scrap_qty'])){
                $jobApproveData=$this->processMovement->getApprovalData(['id'=>$data['job_approval_id']]);
                $logData = [
                    'id' => '',
                    'prod_type' => 5,
                    'm_ct' => '',
                    'part_code' => '',
                    'product_id' => $jobApproveData->product_id,
                    'job_approval_id' => $jobApproveData->id,
                    'job_card_id' => $jobApproveData->job_card_id,
                    'process_id' => $jobApproveData->in_process_id,
                    'log_date' =>  $data['ref_date'],
                    'cycle_time' =>'',
                    'load_unload_time' => '',
                    'machine_id' => '',
                    'shift_id' => '',
                    'operator_id' => '',
                    'production_time' => '',
                    'start_part_count' =>'',
                    'production_qty' => $data['scrap_qty'],
                    'ok_qty' => 0,
                    'end_part_count' => 0,
                    'rw_reason' => '',
                    'rw_qty' => 0,
                    'rej_qty' => $data['scrap_qty'],
                    'created_by' =>$this->loginId,
                    'is_approve' => $this->loginId,
                    'approved_at' => date("Y-m-d"),
                    'ref_id' =>$stock_trans_id
                ];
                $rejection_Reason = [
                                    'rej_qty' => $data['scrap_qty'],
                                    'rej_ref_id' => 0,
                                    'rej_type' => 0,
                                    'rej_reason' => -1,
                                    'rej_by' => $this->loginId,
                                    'trans_id' => 0,
                                    'rej_stage' => $jobApproveData->in_process_id,
                                    'rej_stage_name' => $jobApproveData->in_process_name,
                                    'rej_from' => 0,
                                    'rej_party_name' => 'In House',
                                    'rej_remark' =>'' ,
                                    'rejection_reason' => 'End Piece Rejection',
                                ];
                $logData['rejection_reason'][]=$rejection_Reason;
                $logData['rej_reason']=json_encode($rejection_Reason);
                $logResult = $this->productionLog->save($logData);

                $queryData = array();
                $queryData['tableName'] = $this->productionApproval;
                $queryData['where']['job_card_id'] = $data['job_card_id'];
                $queryData['where']['out_process_id'] = 0;
                $approvalData = $this->row($queryData);

                $jobCardData = $this->jobcard_v3->getJobCard($data['job_card_id']); 
                $logData=$this->logSheet->getPrdLogOnProcessNJob($data['job_card_id']);
                $totalQty = $logData->rejection_qty + $approvalData->out_qty;
                if ($totalQty >= $jobCardData->qty) :
                    $this->store($this->jobCard, ['id' => $jobCardData->id, 'order_status' => 4]);
                endif;
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

    public function deleteInprocessScrap($data){
        try{
            $this->db->trans_begin();
            $queryData['tableName'] = $this->stockTrans;
            $queryData['where']['stock_transaction.id'] = $data['id'];
            $returnData=$this->row($queryData);
            $stockData = $this->store->getItemStockGeneral(['item_id'=>$returnData->item_id,'location_id'=>$returnData->location_id,'batch_no'=>$returnData->batch_no]);
            
            if( $returnData->qty > $stockData->qty){
                return ['status'=>0,'message'=>"You can not delete this transaction"];
            }

            $endPcData = $this->productionLog->getProductionLogList(['prod_type'=>5,'ref_id'=>$data['id'],'single_row'=>1]);
            if(!empty($endPcData)){
                $this->productionLog->delete($endPcData->id);
                $this->edit($this->jobCard, ['id' => $endPcData->job_card_id, 'order_status' => 4],['order_status'=>2]);
            }
            $result = $this->remove("stock_transaction",['id'=>$data['id']]);
           
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }
    
	/* API Function Start */
    public function getJobCardListing($data){
        $queryData['tableName'] = $this->jobCard;
        $queryData['select'] = "job_card.*,(CASE WHEN job_card.order_status = 0 THEN 'Pending' WHEN job_card.order_status = 1 THEN 'Start' WHEN job_card.order_status = 2 THEN 'In-Process' WHEN job_card.order_status = 3 THEN 'On-Hold' WHEN job_card.order_status = 4 THEN 'Complete' ELSE 'Closed' END) order_status_name,item_master.item_name,item_master.item_code,party_master.party_name,party_master.party_code";

        $queryData['leftJoin']['item_master'] = "item_master.id = job_card.product_id";
        $queryData['leftJoin']['party_master'] = "job_card.party_id = party_master.id";

        if(!empty($data['job_category']))
            $queryData['where']['job_card.job_category'] = $data['job_category'];
        $queryData['where']['job_card.version'] = 2;   
        
        if(isset($data['order_status']) && $data['order_status'] == 0):
            $queryData['where_in']['job_card.order_status'] = [0,1,2];
        endif;
        if(isset($data['order_status']) && $data['order_status'] == 1):
            $queryData['where_in']['job_card.order_status'] = [4,5];
            $queryData['where']['job_card.job_date >= '] = $this->startYearDate;
            $queryData['where']['job_card.job_date <= '] = $this->endYearDate;
        endif;
        if(isset($data['order_status']) && $data['order_status'] == 2):
            $queryData['where_in']['job_card.order_status'] = [6];
            $queryData['where']['job_card.job_date >= '] = $this->startYearDate;
            $queryData['where']['job_card.job_date <= '] = $this->endYearDate;
        endif;
        if(isset($data['order_status']) && $data['order_status'] == 3):
            $queryData['where_in']['job_card.order_status'] = [3];
        endif;
        
        $queryData['order_by']['job_card.job_date'] = "DESC";
        $queryData['order_by']['job_card.id'] = "DESC";

        if(!empty($data['search'])):
            $queryData['like_or']["DATE_FORMAT(job_card.job_date,'%d-%m-%Y')"] = $data['search'];
            $queryData['like_or']['CONCAT(job_card.job_prefix,job_card.job_no)'] = $data['search'];
            $queryData['like_or']["DATE_FORMAT(job_card.delivery_date,'%d-%m-%Y')"] = $data['search'];
            $queryData['like_or']['job_card.challan_no'] = $data['search'];
            $queryData['like_or']['party_master.party_code'] = $data['search'];
            $queryData['like_or']['item_master.item_name'] = $data['search'];
            $queryData['like_or']['item_master.item_code'] = $data['search'];
        endif;

        $queryData['length'] = $data['limit'];
		$queryData['start'] = $data['off_set'];

		$result = $this->rows($queryData);
        return $result;
    }
    /* API Function End */
}
?>