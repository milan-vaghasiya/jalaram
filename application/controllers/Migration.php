<?php
class Migration extends MY_Controller{

    /*  public function __construct(){
        parent::__construct();
    } */ 

    /*public function jobApprovalProcessIdsUpdate(){
        try{
            $this->db->trans_begin();

            $jobCardData = $this->db->where('is_delete',0)->get('job_card')->result();
            $processIds = array();
            $mismatchId = array();
            foreach($jobCardData as $job):
                $processIds = explode(",",$job->process);
                $countProcess = count($processIds) + 1;
                $this->db->where('is_delete',0);
                $this->db->where('job_card_id',$job->id);
                $this->db->where('trans_type',0);
                $approvalData = $this->db->get('job_approval')->result();
                if(!empty($approvalData)):
                    $countApprovalProcess = count($approvalData);
                    if($countApprovalProcess == $countProcess):
                        $i=0;$postData = array();
                        foreach($approvalData as $row):
                            $postData = [
                                'in_process_id' => ($i==0)?0:$processIds[($i - 1)],
                                'out_process_id' => (isset($processIds[$i]))?$processIds[$i]:0,
                            ];
                            $this->db->where('id',$row->id);
                            $this->db->update('job_approval',$postData);

                            if(isset($processIds[$i])):
                                $this->db->where('process_id',$processIds[$i]);
                                $this->db->where('job_card_id',$job->id);
                                $this->db->where('is_delete',0);
                                $this->db->update("job_transaction",['job_approval_id'=>$row->id]);
                            endif;
                            $i++;
                        endforeach;
                    else:
                        $mismatchId[] = $job->id;
                    endif;
                endif;
            endforeach;            

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "Approval Process Migration Success. mismatch job ids : ".((!empty($mismatchId))?implode(",",$mismatchId):"none");
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }*/

    /* public function jobApprovalInwardQty(){
        try{
            $this->db->trans_begin();

            $jobCardData = $this->db->where('is_delete',0)->get('job_card')->result();
            $processIds = array();
            foreach($jobCardData as $job):
                $processIds = explode(",","0,".$job->process);
                foreach($processIds as $value):
                    if($value == 0):
                        $this->db->where('job_card_id',$job->id);
                        $this->db->where('in_process_id',$value);
                        $this->db->where('trans_type',0);
                        $this->db->where('is_delete',0);
                        $this->db->update('job_approval',['inward_qty'=>$job->qty]);
                    else:
                        $this->db->select('SUM(rejection_qty) as rejection_qty,SUM(rework_qty) as rework_qty');
                        $this->db->where('process_id',$value);
                        $this->db->where('job_card_id',$job->id);
                        $this->db->where('is_delete',0);
                        $this->db->where('entry_type',2);
                        $jobTrans = $this->db->get('job_transaction')->row();

                        $this->db->where('job_card_id',$job->id);
                        $this->db->where('out_process_id',$value);
                        $this->db->where('trans_type',0);
                        $this->db->where('is_delete',0);
                        $approvedQty = $this->db->get('job_approval')->row();
                        $outQty = ((!empty($approvedQty->out_qty)) && $approvedQty->out_qty > 0)?$approvedQty->out_qty:0;

                        $this->db->where('job_card_id',$job->id);
                        $this->db->where('in_process_id',$value);
                        $this->db->where('trans_type',0);
                        $this->db->where('is_delete',0);
                        $this->db->update('job_approval',['inward_qty'=>$outQty,'total_rejection_qty'=>((!empty($jobTrans))?$jobTrans->rejection_qty:0),'total_rework_qty'=>((!empty($jobTrans))?$jobTrans->rework_qty:0)]);
                    endif;
                endforeach;
            endforeach;

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "Inward Qty Migration Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    } */   

    /* public function jobTransactionTable(){
        try{
            $this->db->trans_begin();

            $this->db->where('entry_type',2);
            $this->db->where('is_delete',0);
            $jobTransaction = $this->db->get('job_transaction')->result();

            foreach($jobTransaction as $row):

                if($row->rework_qty > 0):
                    if(!in_array($row->process_id,explode(",",$row->rework_process_id))):
                        $row->rework_process_id = $row->rework_process_id.",".$row->process_id;

                        $this->db->where('id',$row->id);
                        $this->db->update('job_transaction',['rework_process_id'=>$row->rework_process_id]);
                    endif;
                endif;

                $transData = [
                    'entry_type' => 3,
                    'ref_id' => $row->id,
                    'entry_date' => $row->entry_date,
                    'job_card_id' => $row->job_card_id,
                    'job_approval_id' => $row->job_approval_id,
                    'job_order_id' => $row->job_order_id,
                    'vendor_id' => $row->vendor_id,
                    'process_id' => $row->process_id,
                    'product_id' => $row->product_id,
                    'material_used_id' => $row->material_used_id,
                    'issue_batch_no' => $row->issue_batch_no,
                    'issue_material_qty' => $row->issue_material_qty,
                    'in_qty' => $row->in_qty,
                    'in_w_pcs' => $row->in_w_pcs,
                    'in_total_weight' => $row->in_total_weight,
                    'rework_qty' => $row->rework_qty,
                    'rejection_qty' => $row->rejection_qty,
                    'rej_scrap_qty' => $row->rej_scrap_qty,
                    'out_qty' => $row->out_qty,
                    'ud_qty' => $row->ud_qty,
                    'w_pcs' => $row->w_pcs,
                    'total_weight' => $row->total_weight,
                    'rejection_reason' => $row->rejection_reason,
                    'rejection_remark' => $row->rejection_remark,
                    'rejection_stage' => $row->rejection_stage,
                    'remark' => $row->remark,
                    'challan_prefix' => $row->challan_prefix,
                    'challan_no' => $row->challan_no,
                    'in_challan_no' => $row->in_challan_no,
                    'charge_no' => $row->charge_no,
                    'challan_status' => $row->challan_status,
                    'operator_id' => $row->operator_id,
                    'machine_id' => $row->machine_id,
                    'shift_id' => $row->shift_id,
                    'production_time' => $row->production_time,
                    'cycle_time' => $row->cycle_time,
                    'job_process_ids' => $row->job_process_ids,
                    'rework_process_id' => $row->rework_process_id,
                    'rework_reason' => $row->rework_reason,
                    'rework_remark' => $row->rework_remark,
                    'material_issue_status' => $row->material_issue_status,
                    'setup_status' => $row->setup_status,
                    'inspection_by' => $row->created_by,
                    'created_by' => $row->created_by
                ];
                $this->db->insert('job_transaction',$transData);
                $transSave = $this->db->insert_id();

                $this->db->where('id',$row->id);
                $this->db->update('job_transaction',["inspection_by"=>$row->created_by,'inspected_qty'=>($row->out_qty + $row->rejection_qty + $row->rework_qty)]);

                if(!empty($row->rework_process_id)):
                    $processIds = explode(",",$row->rework_process_id);
                    $counter = count($processIds);
                    for($i=0;$i<$counter;$i++):
                        $approvalData = [
                            'entry_date' => $row->entry_date,
                            'trans_type' => 1,
                            'ref_id' => $transSave,
                            'job_card_id' => $row->job_card_id,
                            'product_id' => $row->product_id,
                            'in_process_id' => ($i == 0)?$row->process_id:$processIds[($i - 1)],
                            'inward_qty' => ($i == 0)?$row->rework_qty:0,
                            'in_qty' => ($i == 0)?$row->rework_qty:0,
                            'in_w_pcs' => ($i == 0)?$row->w_pcs:0,
                            'in_total_weight' => ($i == 0)?$row->total_weight:0,
                            'out_process_id' => (isset($processIds[$i]))?$processIds[$i]:0,
                            'created_by' => $row->created_by
                        ];
                        $this->db->insert("job_approval",$approvalData);
                    endfor; 
                endif;                
            endforeach;

            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "Job Transaction table Migration Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    } */ 

    /* public function partyMasterCurrencyUpdate(){
        try{
            $this->db->trans_begin();
            
            $this->db->where('currency !=',"INR");
            $invoiceData = $this->db->get('party_master')->result();
            
            foreach($invoiceData as $row):
                $row->currency = (!empty($row->currency))?$row->currency:"INR";
                $row->currency = str_replace("$","",$row->currency);
                $row->currency = str_replace("€","",$row->currency);
                
                $this->db->where('id',$row->id);
                $this->db->update("party_master",['currency'=>trim($row->currency)]);
            endforeach;

            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "Party Master Migration Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    } */

    /*public function partyMaster(){
        try{
            $this->db->trans_begin();
            $partyData = $this->db->get('party_master')->result();
            foreach($partyData as $row):
                $data = array();
                $groupCode = ($row->party_category == 1)?"SD":"SC";
                $groupData = $this->db->where('group_code',$groupCode)->get('group_master')->row();

                $data['group_id'] = $groupData->id;
                $data['group_name'] = $groupData->name;
                $data['group_code'] = $groupData->group_code;

                $data['balance_type'] = ($row->balance_type == "C")?1:-1;
                $data['cl_balance'] = $data['opening_balance'] = $row->opening_balance * $data['balance_type'];
                
                $this->db->where('id',$row->id);
                $this->db->update('party_master',$data);
            endforeach;
            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "Party Master table Migration Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }*/

    /*public function transMainCurrencyUpdate(){
        try{
            $this->db->trans_begin();

            $this->db->where('is_delete',0);
            $this->db->where_in('entry_type',[6,7,8,12]);
            $invoiceData = $this->db->get('trans_main')->result();
            
            foreach($invoiceData as $row):
                $this->db->select('party_master.*,currency.inrrate');
                $this->db->where('party_master.id',$row->party_id);
                $this->db->join('currency','currency.currency = party_master.currency','left');
                $partyData = $this->db->get('party_master')->row();

                $this->db->where('id',$row->id);
                $this->db->update('trans_main',['currency'=>(!empty($partyData->currency))?trim($partyData->currency):"INR",'inrrate'=>($partyData->inrrate > 0)?$partyData->inrrate:1]);
                
                $this->db->where('trans_main_id',$row->id);
                $this->db->where('is_delete',0);
                $transItems = $this->db->get('trans_child') ->result();
                foreach($transItems as $itm):
                    $this->db->where('id',$itm->id);
                    $this->db->update('trans_child',['currency'=>(!empty($partyData->currency))?trim($partyData->currency):"INR",'inrrate'=>($partyData->inrrate > 0)?$partyData->inrrate:1]);
                endforeach;
            endforeach;

            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "Trans Main and Child table Migration Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }*/

    /*public function defualtLedger(){
        $accounts = [
            ['name' => 'Sales Account', 'group_name' => 'Sales Account', 'group_code' => 'SA', 'system_code' => 'SALESACC'],
            
            ['name' => 'Sales Account GST', 'group_name' => 'Sales Account', 'group_code' => 'SA', 'system_code' => 'SALESGSTACC'],
            
            ['name' => 'Sales Account Tax Free', 'group_name' => 'Sales Account', 'group_code' => 'SA', 'system_code' => 'SALESTFACC'],
            
            ['name' => 'CGST (O/P)', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => 'CGSTOPACC'],
            
            ['name' => 'SGST (O/P)', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => 'SGSTOPACC'],
            
            ['name' => 'IGST (O/P)', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => 'IGSTOPACC'],
            
            ['name' => 'UTGST (O/P)', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => 'UTGSTOPACC'],
            
            ['name' => 'CESS (O/P)', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => ''],
            
            ['name' => 'TCS ON SALES', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => ''],
            
            ['name' => 'Purchase Account', 'group_name' => 'Purchase Account', 'group_code' => 'PA', 'system_code' => 'PURACC'],
            
            ['name' => 'Purchase Account GST', 'group_name' => 'Purchase Account', 'group_code' => 'PA', 'system_code' => 'PURGSTACC'],
            
            ['name' => 'Purchase Account Tax Free', 'group_name' => 'Purchase Account', 'group_code' => 'PA', 'system_code' => 'PURTFACC'],
            
            ['name' => 'CGST (I/P)', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => 'CGSTIPACC'],
            
            ['name' => 'SGST (I/P)', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => 'SGSTIPACC'],
            
            ['name' => 'IGST (I/P)', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => 'IGSTIPACC'],
            
            ['name' => 'UTGST (I/P)', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => 'UTGSTIPACC'],
            
            ['name' => 'CESS (I/P)', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => ''],
            
            ['name' => 'TCS ON PURCHASE', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => ''],
            
            ['name' => 'TDS PAYABLE', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => ''],
            
            ['name' => 'TDS RECEIVABLE', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => ''],
            
            ['name' => 'GST PAYABLE', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => ''],
            
            ['name' => 'GST RECEIVABLE', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => ''],
            
            ['name' => 'ROUNDED OFF', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => 'ROFFACC'],
            
            ['name' => 'CASH ACCOUNT', 'group_name' => 'Cash-In-Hand', 'group_code' => 'CS', 'system_code' => 'CASHACC'],
            
            ['name' => 'ELECTRICITY EXP', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'OFFICE RENT EXP', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'GODOWN RENT EXP', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'TELEPHONE AND INTERNET CHARGES', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'PETROL EXP', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'SALES INCENTIVE', 'group_name' => 'Expenses (Direct)', 'group_code' => 'ED', 'system_code' => ''],
            
            ['name' => 'INTEREST PAID', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'INTEREST RECEIVED', 'group_name' => 'Income (Indirect)', 'group_code' => 'II', 'system_code' => ''],
            
            ['name' => 'SAVING BANK INTEREST', 'group_name' => 'Income (Indirect)', 'group_code' => 'II', 'system_code' => ''],
            
            ['name' => 'DISCOUNT RECEIVED', 'group_name' => 'Income (Indirect)', 'group_code' => 'II', 'system_code' => ''],
            
            ['name' => 'DISCOUNT PAID', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'SUSPENSE A/C', 'group_name' => 'Suspense A/C', 'group_code' => 'AS', 'system_code' => ''],
            
            ['name' => 'PROFESSIONAL FEES PAID', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'AUDIT FEE', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'ACCOUNTING CHARGES PAID', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'LEGAL FEE', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'SALARY', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'WAGES', 'group_name' => 'Expenses (Direct)', 'group_code' => 'ED', 'system_code' => ''],
            
            ['name' => 'FREIGHT CHARGES', 'group_name' => 'Expenses (Direct)', 'group_code' => 'ED', 'system_code' => ''],
            
            ['name' => 'PACKING AND FORWARDING CHARGES', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'REMUNERATION TO PARTNERS', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'TRANSPORTATION CHARGES', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'DEPRICIATION', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'PLANT AND MACHINERY', 'group_name' => 'Fixed Assets', 'group_code' => 'FA', 'system_code' => ''],
            
            ['name' => 'FURNITURE AND FIXTURES', 'group_name' => 'Fixed Assets', 'group_code' => 'FA', 'system_code' => ''],
            
            ['name' => 'FIXED DEPOSITS', 'group_name' => 'Deposits (Assets)', 'group_code' => 'DA', 'system_code' => ''],
            
            ['name' => 'RENT DEPOSITS', 'group_name' => 'Deposits (Assets)', 'group_code' => 'DA', 'system_code' => '']	
        ];

        try{
            $this->db->trans_begin();
            $accounts = (object) $accounts;
            foreach($accounts as $row):
                $row = (object) $row;
                $groupData = $this->db->where('group_code',$row->group_code)->get('group_master')->row();
                $ledgerData = [
                    'party_category' => 4,
                    'group_name' => $groupData->name,
                    'group_code' => $groupData->group_code,
                    'group_id' => $groupData->id,
                    'party_name' => $row->name,                    
                    'system_code' => $row->system_code
                ];

                $this->db->where('party_name',$row->name);
                $this->db->where('is_delete',0);
                $this->db->where('party_category',4);
                $checkLedger = $this->db->get('party_master');

                if($checkLedger->num_rows() > 0):
                    $id = $checkLedger->row()->id;
                    $this->db->where('id',$id);
                    $this->db->update('party_master',$ledgerData);
                else:
                    $this->db->insert('party_master',$ledgerData);
                endif;
            endforeach;

            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "Defualt Ledger Migration Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }*/

    /* public function migrateStock($item_id=""){
		try{
            $this->db->trans_begin();
            $i=0;
            if(!empty($item_id)){$this->db->where('item_master.id',$item_id);}
            //$this->db->where('item_master.id <= ',3500);
			$this->db->where('is_delete',0);
            $result = $this->db->get("item_master")->result();
            foreach($result as $row):
                $this->db->select("SUM(qty) as qty");
				$this->db->join('location_master','location_master.id = stock_transaction.location_id','left');
				$this->db->where('stock_transaction.is_delete',0);
				$this->db->where('location_master.ref_id',0);
                $this->db->where('stock_transaction.item_id',$row->id);
                $stockData = $this->db->get('stock_transaction')->row();
                
                //update Item Master
                if(!empty($stockData->qty)):
                    $data=['qty'=>$stockData->qty];
                    $this->db->where('id',$row->id);
                    $this->db->update('item_master',$data);
                else:
                    $data=['qty'=>0];
                    $this->db->where('id',$row->id);
                    $this->db->update('item_master',$data);
                endif;
                $i++;
                //echo $this->db->last_query().'<br>';
            endforeach;
            
            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo $i. ' Records Updated';
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
        
    } */
    
    /*public function migratePackingStock(){
		try{
            $this->db->trans_begin();
            $i=0;
            $this->db->where('ref_type',16);
            $this->db->where('is_delete',0);
            $result = $this->db->get("stock_transaction")->result();
            foreach($result as $row):
                $this->db->select("packing_date");
                $this->db->where('id',$row->ref_id);
                $this->db->where('is_delete',0);
                $packingData = $this->db->get('packing_master')->row();
                
                //update Item Master
                if(!empty($packingData->packing_date)):
                    $data=['ref_date'=>$packingData->packing_date];
                    $this->db->where('id',$row->id);
                    $this->db->update('stock_transaction',$data);
                    echo $this->db->last_query().'<br>';
                endif;
                $i++;
            endforeach;
            
            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo $i. ' Records Updated';
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
        
    }*/

    /*public function migrateMCTLogsheet(){
		try{
            $this->db->trans_begin();
            $i=0;
            $this->db->where('is_delete',0);
            $result = $this->db->get("production_log")->result();
            foreach($result as $row):
                $this->db->select("product_process.cycle_time");
                $this->db->where('item_id',$row->item_id);
                $this->db->where('process_id',$row->process_id);
                $this->db->where('item_id',$row->item_id);
                $this->db->join('currency','currency.currency = party_master.currency','left');
                $this->db->where('is_delete',0);
                $packingData = $this->db->get('product_process')->row();
                
                //update Item Master
                if(!empty($packingData->packing_date)):
                    $data=['ref_date'=>$packingData->packing_date];
                    $this->db->where('id',$row->id);
                    $this->db->update('stock_transaction',$data);
                    echo $this->db->last_query().'<br>';
                endif;
                $i++;
            endforeach;
            
            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo $i. ' Records Updated';
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
        
    }*/

    /*public function processWiseStore(){
		try{
            $this->db->trans_begin();
			
			$this->db->where('is_delete',0);
			$processMaster = $this->db->get('process_master')->result();
				
			foreach($processMaster as $row):
				$this->db->where('ref_id',$row->id);
				$this->db->where('is_delete',0);
				$storeResult = $this->db->get('location_master')->row();
				
				if(empty($storeResult)):
					$this->db->select("(CASE WHEN MAX(store_type) < 10 THEN 10 ELSE (MAX(store_type) + 1) END) as store_type");
					$this->db->where('is_delete',0);
					$store_type = $this->db->get('location_master')->row()->store_type;
					
					$storeData = [
                        'store_name' => "Production",
                        'location' => $row->process_name,
                        'store_type' => $store_type,
                        'ref_id' => $row->id
                    ];
					$this->db->insert("location_master",$storeData);
				else:
					$storeData = [
                        'store_name' => "Production",
                        'location' => $row->process_name,
                        'ref_id' => $row->id
                    ];
					$this->db->where('id',$storeResult->id)->update("location_master",$storeData);
				endif;
			endforeach;
				
			if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo 'Store saved successfully.';
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
	}*/

    /*public function updateRejectionQtyInMovement(){
        try{
            $this->db->trans_begin();

            $this->db->where('is_delete',0);
            $this->db->where('version',2);
            $this->db->where('id != ',561);
            $result = $this->db->get('job_card')->result();

            $approvalIds = array();
            foreach($result as $row):
                $this->db->where('is_delete',0);
                $this->db->where('job_card_id',$row->id);
                $approvalData = $this->db->get('production_approval')->result();

                $totalRejQty = 0;
                foreach($approvalData as $apTrans):
                    if(!empty($apTrans->in_process_id)):
                        $this->db->select("SUM(rej_qty) as total_rej_qty");
                        $this->db->where('is_delete',0);
                        $this->db->where_in('prod_type',[1,3]);
                        $this->db->where('job_card_id',$row->id);
                        $this->db->where('process_id',$apTrans->in_process_id);
                        $this->db->having('SUM(rej_qty) > ',0);
                        $rejectionLog = $this->db->get('production_log')->row();

                        $totalRejQty += (!empty($rejectionLog)) ? $rejectionLog->total_rej_qty : 0;
                        if($apTrans->total_ok_qty > 0):
                            $this->db->where('id',$apTrans->id);
                            $this->db->update('production_approval',['total_ok_qty'=>($apTrans->total_ok_qty - $totalRejQty),'out_qty'=>($apTrans->out_qty - $totalRejQty)]);

                            $this->db->where('production_approval_id',$apTrans->id);
                            $this->db->where('job_card_id',$row->id);
                            $this->db->where('out_qty >=',$totalRejQty);
                            $this->db->where('is_delete',0);
                            $this->db->order_by('id','ASC');
                            $this->db->limit(1);
                            $transRow = $this->db->get('production_transaction')->row();

                            if(!empty($transRow)):
                                $this->db->where('id',$transRow->id);
                                $this->db->update('production_transaction',["in_qty"=>($transRow->in_qty - $totalRejQty),"out_qty"=>($transRow->out_qty - $totalRejQty)]);
                            endif;

                            if(!empty($apTrans->out_process_id)):
                                $this->db->set('inward_qty','inward_qty - '.$totalRejQty,false);
                                $this->db->set('in_qty','in_qty - '.$totalRejQty,false);
                                $this->db->where('in_process_id',$apTrans->out_process_id);
                                $this->db->where('job_card_id',$row->id);
                                $this->db->where('is_delete',0);
                                $this->db->update('production_approval');
                            endif;
                        else:
                            $approvalIds[] = $apTrans->id;
                        endif;
                    endif;
                endforeach;
            endforeach;
				
			if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo 'Rejection stock migrated in production movement. Pending for update ids : '.implode(",",$approvalIds);
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }*/

    /*public function productionStockTransaction(){
		try{
            $this->db->trans_begin();

            $this->db->where('is_delete',0);
            $this->db->where('out_process_id',0);
            $this->db->set('`out_qty`','`total_ok_qty`',false);
            $this->db->update('production_approval');

            $this->db->where('version',2);
            $this->db->where('is_delete',0);
            $this->db->update('job_card',['unstored_qty'=>0]);
			
			$this->db->where('is_delete',0);
			$result = $this->db->get('production_transaction')->result();
				
			foreach($result as $row):
				$jobCardData = $this->db->where('id',$row->job_card_id)->get('job_card')->row();
                $processApprovalData = $this->db->where('id',$row->production_approval_id)->get('production_approval')->row();
				
				if(!empty($processApprovalData->in_process_id)):
					$this->db->where('ref_id',$processApprovalData->in_process_id);
					$this->db->where('is_delete',0);
					$locationData = $this->db->get('location_master')->row(); 

                    $stockMinusTrans = [
                        'location_id' => $locationData->id,
                        'batch_no' => getPrefixNumber($jobCardData->job_prefix, $jobCardData->job_no),
                        'trans_type' => 2,
                        'item_id' => $row->product_id,
                        'qty' => '-' . $row->out_qty,
                        'ref_type' => 23,
                        'ref_id' => $row->job_card_id,
                        'ref_no' => getPrefixNumber($jobCardData->job_prefix, $jobCardData->job_no),
                        'trans_ref_id' => $row->id,
                        'ref_date' => date("Y-m-d")
                    ];
                    $this->db->insert('stock_transaction',$stockMinusTrans);
				endif;

                if(!empty($processApprovalData->out_process_id)):
                    $this->db->where('ref_id',$processApprovalData->out_process_id);
					$this->db->where('is_delete',0);
					$nxtPrsStore = $this->db->get('location_master')->row(); 

                    $stockPlusTrans = [
                        'location_id' => $nxtPrsStore->id,
                        'batch_no' => getPrefixNumber($jobCardData->job_prefix, $jobCardData->job_no),
                        'trans_type' => 1,
                        'item_id' => $row->product_id,
                        'qty' =>  $row->out_qty,
                        'ref_type' => 23,
                        'ref_no' => getPrefixNumber($jobCardData->job_prefix, $jobCardData->job_no),
                        'ref_id' => $row->job_card_id,
                        'trans_ref_id' => $row->id,
                        'ref_date' => date("Y-m-d")
                    ];

                    $this->db->insert('stock_transaction',$stockPlusTrans);
                else:

                    $this->db->where('ref_id',$processApprovalData->in_process_id);
					$this->db->where('is_delete',0);
					$curentPrsStore = $this->db->get('location_master')->row(); 

                    $stockPlusTrans = [
                        'location_id' => $curentPrsStore->id,
                        'batch_no' => getPrefixNumber($jobCardData->job_prefix, $jobCardData->job_no),
                        'trans_type' => 1,
                        'item_id' => $row->product_id,
                        'qty' =>  $row->out_qty,
                        'ref_type' => 7,
                        'ref_no' => getPrefixNumber($jobCardData->job_prefix, $jobCardData->job_no),
                        'ref_id' => $row->job_card_id,
                        'trans_ref_id' => $row->production_approval_id,
                        'ref_date' => date("Y-m-d"),
                        'ref_batch' => 23
                    ];

                    $this->db->insert('stock_transaction',$stockPlusTrans);   
                    
                    $this->db->where('id',$row->job_card_id);
                    $this->db->set('unstored_qty','unstored_qty + '.$row->out_qty,false);
                    $this->db->update('job_card');
                endif;
			endforeach;

            $this->db->where('is_delete',0);
            $this->db->where('out_process_id',0);
            $approvalData = $this->db->get('production_approval')->result();

            $unstoredQty = 0;$pendingQty = 0;
            foreach($approvalData as $row):
            
                $this->db->where('is_delete',0);
                $this->db->where('ref_type',7);
                $this->db->where('ref_id',$row->job_card_id);
                $this->db->where('trans_ref_id',$row->id);
                $this->db->where('trans_type',1);
                $this->db->where('ref_batch',NULL);
                $stockTrans = $this->db->get('stock_transaction')->result();

                $jobCardData = $this->db->where('id',$row->job_card_id)->get('job_card')->row();
                $unstoredQty = $jobCardData->unstored_qty;$pendingQty = 0;
                foreach($stockTrans as $trans):
                    $pendingQty = $unstoredQty;
                    $unstoredQty = $unstoredQty - $trans->qty;

                    $transQty = ($unstoredQty > 0)?$trans->qty:$pendingQty;                   
                    
                    if($transQty > 0):
                        $this->db->where('ref_id',$row->in_process_id);
                        $this->db->where('is_delete',0);
                        $curentPrsStore = $this->db->get('location_master')->row(); 
                        $stockMinusTrans = [
                            'location_id' => $curentPrsStore->id,
                            'batch_no' => getPrefixNumber($jobCardData->job_prefix, $jobCardData->job_no),
                            'trans_type' => 2,
                            'item_id' => $jobCardData->product_id,
                            'qty' => '-' . $transQty,
                            'ref_type' => 7,
                            'ref_id' => $jobCardData->id,
                            'ref_no' => getPrefixNumber($jobCardData->job_prefix, $jobCardData->job_no),
                            'trans_ref_id' => $row->id,
                            'ref_date' => date("Y-m-d")
                        ];
                        $this->db->insert('stock_transaction',$stockMinusTrans); 
                        
                        $stockPusTrans = [
                            'location_id' => 136,
                            'batch_no' => getPrefixNumber($jobCardData->job_prefix, $jobCardData->job_no),
                            'trans_type' => 1,
                            'item_id' => $jobCardData->product_id,
                            'qty' => $transQty,
                            'ref_type' => 7,
                            'ref_id' => $jobCardData->id,
                            'ref_no' => getPrefixNumber($jobCardData->job_prefix, $jobCardData->job_no),
                            'trans_ref_id' => $row->id,
                            'ref_date' => date("Y-m-d")
                        ];
                        $this->db->insert('stock_transaction',$stockPusTrans);     
                        
                        $this->db->where('id',$row->job_card_id);
                        $this->db->set('unstored_qty','unstored_qty - '.$transQty,false);
                        $this->db->update('job_card');
                        
                    endif;
                    $this->db->where('id',$trans->id);
                    $this->db->delete('stock_transaction');
                endforeach;
                

                $this->db->where('id',$row->job_card_id);
                $jobData = $this->db->get('job_card')->row();

                $this->db->select('SUM(rej_qty) as rejection_qty');
                $this->db->where('job_card_id',$row->job_card_id);
                $this->db->where_in('prod_type',[1,3]);
                $this->db->having('SUM(rej_qty) > ',0);
                $this->db->where('is_delete',0);
                $logData = $this->db->get('production_log')->row();

                $totalJobQty = ((!empty($logData))?$logData->rejection_qty:0) + $row->total_ok_qty;
                if ($totalJobQty >= $jobData->qty) :
                    $this->db->where('id', $jobCardData->id);
                    $this->db->update('job_card', ['order_status' => 4]);
                endif;
            endforeach;
				
			if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo 'Stock Transaction migrated successfully.';
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
	}*/

    /*public function rejectionStockTransaction(){
        try{
            $this->db->trans_begin();
			
			$this->db->where('is_delete',0);
            $this->db->where('rej_qty >',0);
            $this->db->where_in('prod_type',[1,3]);
            $rejectionLog = $this->db->get('production_log')->result();

            foreach($rejectionLog as $row):
                $jobCardData = $this->db->where('id',$row->job_card_id)->get('job_card')->row();

                $this->db->where('ref_id',$row->process_id);
                $this->db->where('is_delete',0);
                $locationData = $this->db->get('location_master')->row(); 

                $stockMinusTrans = [
                    'id' => "",
                    'location_id' => $locationData->id,
                    'batch_no' => getPrefixNumber($jobCardData->job_prefix, $jobCardData->job_no),
                    'trans_type' => 2,
                    'item_id' => $jobCardData->product_id,
                    'qty' =>  "-".$row->rej_qty,
                    'ref_type' => 23,
                    'ref_no' => getPrefixNumber($jobCardData->job_prefix, $jobCardData->job_no),
                    'ref_id' => $row->job_card_id,
                    'ref_date' => date("Y-m-d"),
                    'ref_batch'=> $row->id
                ];
                $this->db->insert("stock_transaction",$stockMinusTrans);

                $stockPlusTrans = [
                    'location_id' => $locationData->id,
                    'batch_no' => getPrefixNumber($jobCardData->job_prefix, $jobCardData->job_no)."-R",
                    'trans_type' => 1,
                    'item_id' => $jobCardData->product_id,
                    'qty' =>  $row->rej_qty,
                    'ref_type' => 24,
                    'ref_no' => 'REJ',
                    'ref_id' => $row->job_card_id,
                    'trans_ref_id' => $row->id,
                    'ref_date' => date("Y-m-d")
                ];
                $this->db->insert("stock_transaction",$stockPlusTrans);                
            endforeach;
				
			if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo 'Rejection stock migrated successfully.';
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }*/

    /*public function reverseRejectionQtyInMovement(){
        try{
            $this->db->trans_begin();

            $this->db->where('is_delete',0);
            $this->db->where('version',2);
            $this->db->where('id != ',561);
            $result = $this->db->get('job_card')->result();

            $approvalIds = array();
            foreach($result as $row):
                $this->db->where('is_delete',0);
                $this->db->where('job_card_id',$row->id);
                $approvalData = $this->db->get('production_approval')->result();

                foreach($approvalData as $apTrans):
                    if(!empty($apTrans->in_process_id)):
                        $this->db->select("SUM(rej_qty) as total_rej_qty");
                        $this->db->where('is_delete',0);
                        //$this->db->where_in('prod_type',[1,3]);
                        $this->db->where('job_card_id',$row->id);
                        $this->db->where('process_id',$apTrans->in_process_id);
                        $this->db->having('SUM(rej_qty) > ',0);
                        $rejectionLog = $this->db->get('production_log')->row();

                        if(!empty($rejectionLog)):
                            if($apTrans->out_qty > 0):
                                $this->db->where('id',$apTrans->id);
                                $this->db->update('production_approval',['total_ok_qty'=>($apTrans->total_ok_qty + $rejectionLog->total_rej_qty),'out_qty'=>($apTrans->out_qty + $rejectionLog->total_rej_qty)]);

                                $this->db->where('production_approval_id',$apTrans->id);
                                $this->db->where('job_card_id',$row->id);
                                $this->db->where('out_qty >=',$rejectionLog->total_rej_qty);
                                $this->db->where('is_delete',0);
                                $this->db->order_by('id','ASC');
                                $this->db->limit(1);
                                $transRow = $this->db->get('production_transaction')->row();

                                if(!empty($transRow)):
                                    $this->db->where('id',$transRow->id);
                                    $this->db->update('production_transaction',["in_qty"=>($transRow->in_qty + $rejectionLog->total_rej_qty),"out_qty"=>($transRow->out_qty + $rejectionLog->total_rej_qty)]);
                                endif;

                                $this->db->set('inward_qty','inward_qty + '.$rejectionLog->total_rej_qty,false);
                                $this->db->set('in_qty','in_qty + '.$rejectionLog->total_rej_qty,false);
                                $this->db->where('in_process_id',$apTrans->out_process_id);
                                $this->db->where('job_card_id',$row->id);
                                $this->db->where('is_delete',0);
                                $this->db->update('production_approval');
                            else:
                                $approvalIds[] = $apTrans->id;
                            endif;
                        endif;
                    endif;
                endforeach;
            endforeach;
				
			if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo 'Rejection stock migrated in production movement. Pending for update ids : '.implode(",",$approvalIds);
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }*/

    /*public function stockTransReverseForProduction(){
		try{
            $this->db->trans_begin();

            $this->db->where_in('ref_type',[23,24]);
            $this->db->delete('stock_transaction');

            $this->db->where('is_delete',0);
            $this->db->where('out_process_id',0);
            $approvalData = $this->db->get('production_approval')->result();
			
			foreach($approvalData as $row):
				$this->db->where('ref_id',$row->in_process_id);
				$this->db->where('is_delete',0);
				$curentPrsStore = $this->db->get('location_master')->row(); 
				
				$this->db->where('location_id' , $curentPrsStore->id);
				$this->db->where('trans_type' , 1);
				$this->db->where('ref_type' , 7);
				$this->db->where('ref_id' , $row->job_card_id);
				$this->db->where('trans_ref_id' , $row->id);
				$this->db->where('ref_batch' , 23);
				$this->db->delete('stock_transaction');

                $this->db->where('is_delete',0);
                $this->db->where('ref_type',7);
                $this->db->where('ref_id',$row->job_card_id);
                $this->db->where('trans_ref_id',$row->id);
                $this->db->where('trans_type',1);
                $this->db->where('ref_batch',NULL);
                $stockTrans = $this->db->get('stock_transaction')->result();
				
				foreach($stockTrans as $trans):
                    $this->db->where('ref_id',$row->in_process_id);
					$this->db->where('is_delete',0);
					$curentProcessStore = $this->db->get('location_master')->row(); 
					
					$this->db->where('location_id' , $curentProcessStore->id);
					$this->db->where('trans_type' , 2);
					$this->db->where('ref_type' , 7);
					$this->db->where('ref_id' , $trans->ref_id);
					$this->db->where('trans_ref_id' , $trans->trans_ref_id);
					$this->db->where('ref_batch' , NULL);
					$this->db->delete('stock_transaction');
					   
                endforeach;
			endforeach;
			
			if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo 'Stock Transaction reversed successfully.';
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
	}*/

    /*public function updatePackingDateInStock(){
	    try{
            $this->db->trans_begin();

            $this->db->where('is_delete',0);
            $this->db->where('ref_type',16);
            $stockData = $this->db->get('stock_transaction')->result();
			
			foreach($stockData as $row):
			    $this->db->where('is_delete',0);
                $this->db->where('id',$row->ref_id);
                $packingData = $this->db->get('packing_master')->row();
                if(!empty($packingData->packing_date)):
				    $this->db->where('id',$row->id);
                    $this->db->update('stock_transaction',["ref_date"=>$packingData->packing_date]);
                endif;
			endforeach;
			
			if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo 'Packing Date updated successfully.';
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
	}*/

    /* public function shortCloseJobCardV1(){
        try{
            $this->db->trans_begin();

            $jobNos = [486, 485, 484, 483, 482, 481, 479, 478, 477, 474, 473, 465, 464, 463, 460, 456, 455, 450, 448, 451, 447, 446, 445, 444, 443, 442, 437, 431, 427, 426, 425, 423, 416, 408, 406, 405, 401, 390, 389, 388, 387, 384, 382, 381, 379, 376, 375, 371, 370, 368, 363, 359, 354, 353, 347, 362, 335, 333, 332, 330, 323, 321, 320, 310, 303, 302, 293, 290, 289, 279, 278, 247, 254, 250, 98, 417, 439, 424, 339, 411, 402, 342, 286, 341, 472, 452, 361, 288, 273, 216, 347, 325, 346, 326, 306, 302, 299, 217];

            $notClosed = array();
            foreach($jobNos as $row):
                $this->db->where('job_no',$row);
                $this->db->where('is_delete',0);
                $result = $this->db->get('job_card')->row();

                if(!empty($result)):
                    $this->db->where('id',$result->id);
                    $this->db->update('job_card',['order_status'=>6]);
                else:
                    $notClosed[] = $row;
                endif;
            endforeach;

            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo 'V1 Job Card Short Close successfully. Job Card not Found : '.implode(", ",$notClosed);
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    } */

    /* public function oldJobStockTransRemove(){
		try{
            $this->db->trans_begin();
			
			$this->db->where('version',1);
			$this->db->where('is_delete',0);
			$result = $this->db->get('job_card')->result();
			
			foreach($result as $row):
				$this->db->where('ref_type',7);
				$this->db->where('ref_id',$row->id);
				$this->db->where('location_id',178);
				$this->db->where('item_id',$row->product_id);
				$this->db->update('stock_transaction',['is_delete'=>1,'ref_batch'=>'M']);
			endforeach;
			
			if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo 'Stock updated successfully.';
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        } 
	} */

    /* public function fgStockUpdate(){
        try{
            $this->db->trans_begin();

            $this->db->select('stock_transaction.location_id,stock_transaction.batch_no,stock_transaction.item_id,SUM(stock_transaction.qty) as stock_qty');
            $this->db->join('item_master','item_master.id = stock_transaction.item_id','left');
            $this->db->join('location_master','location_master.id = stock_transaction.location_id','left');
            $this->db->where('stock_transaction.is_delete',0);
            $this->db->where('stock_transaction.ref_date <=',"2022-03-31");
            $this->db->where('item_master.item_type',1);
            $this->db->where('location_master.ref_id',0);
			//$this->db->having('SUM(stock_transaction.qty) !=',0);
            $this->db->group_by('location_id');
            $this->db->group_by('batch_no');
			$this->db->group_by('item_id');
            $result = $this->db->get('stock_transaction')->result();
			//print_r($this->db->last_query());exit;
            
			$i=0;
			foreach($result as $row):
				$stockTrans=array();
				if($row->stock_qty > 0):
					$stockTrans = [
						'location_id' => $row->location_id,
						'batch_no' => $row->batch_no,
						'trans_type' => 2,
						'item_id' => $row->item_id,
						'qty' => "-".$row->stock_qty,
						'ref_no' => 'Stock Adjustment',
						'ref_type' => 99,
						'ref_date' => "2022-03-31",						
					];
					$this->db->insert("stock_transaction",$stockTrans);
					$i++;
				elseif($row->stock_qty < 0):
					$stockTrans = [
						'location_id' => $row->location_id,
						'batch_no' => $row->batch_no,
						'trans_type' => 1,
						'item_id' => $row->item_id,
						'qty' => abs($row->stock_qty),
						'ref_type' => 99,
						'ref_no' => 'Stock Adjustment',
						'ref_date' => "2022-03-31",						
					];
					$this->db->insert("stock_transaction",$stockTrans);
					$i++;
				endif;					
			endforeach;

            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo 'FG Stock updated successfully. '.$i.' Recored updated';
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        } 
    } */	
	
	/* public function updateOpeningStock(){
		try{
            $this->db->trans_begin();
			
			$itemCodes = ['J00110000001','J00110000002','J00110000004','J00110000005','J00110000007','J00110000008','J00110000010','J00010000079','J00010000078','J00010000112','J00010000229','J00010000133','J00020000075','J00030000004','J00030000008','J00040000001','J00040000003','J00040000005','J00040000007','J00040000008','J00040000011','J00040000013','J00040000014','J00040000018','J00040000020','J00040000023','J00040000029','J00040000034','J00050000001','J00060000003','J00100000001','J00100000002','J00100000003','J00100000006','J00160000001','J00160000002','J00160000006','J00160000008','J00160000018','J00160000026','J00180000001','J00180000003','J00220000001','J00240000001','J00260000001','J00260000002','J00280000002','J00280000003','J00290000001','J00290000002','J00290000003','J00310000012','J00340000014','J00340000020','J00340000035','J00440000001','J00440000009','J00440000006','J00440000007','J00440000013','J00440000016','J00440000017','J00440000049','J00440000050','J00450000011','J00450000015','J00450000005','J00450000006','J00450000007','J00450000008','J00450000009','J00450000012','J00450000013','J00450000014','J00450000017','J00450000018','J00450000019','J00450000020','J00450000010','J00460000001','J00460000005','J00460000006','J00460000009','J00460000011','J00460000012','J00470000003','J00490000001','J00490000002','J00500000001','J00500000002','J00500000004','J00500000005','J00500000006','J00500000008','J00500000009','J00500000014','J00500000015','J00500000016','J00500000017','J00500000019','J00530000005','J00530000006','J00550000001','J00550000003','J00560000001','J00560000008','J00560000009','J00560000015','j00560000004','j00560000005','J00560000006','J00560000016','J00560000017','J00560000018','J00560000019','J00560000020','J00560000021','J00560000022','J00560000030','J00580000001','J00600000001','J00660000001','J00660000002','J00660000003','J00660000004','J00660000005','J00660000006','J00660000007','J00660000010','J00660000011','J00660000012','J00660000013','J00660000014','J00660000017','J00660000018','J00660000019','J00690000001','J00690000002','J00690000003','J00700000002','J00700000003','J00720000001','J00720000002','J00720000004','J00720000005','J00740000001','J00750000002','J00750000003','J00750000004','J00750000005','J00750000006','J00750000007','J00750000008','J00750000009','J00750000010','J00750000011','J00750000013','J00750000014','J00750000015','J00750000016','J00750000017','J00750000018','J00750000019','J00750000020','J00750000021','J00750000022','J00750000023','J00750000024','J00750000025','J00750000026','J00750000027','J00750000028','J00750000029','J00750000030','J00750000031','J00750000033','J00750000034','J00760000001','J00760000002','J00760000003','J00760000004','J00760000005'];
			
			$itemQty = [1341, 2160, 1583, 2700, 3430, 8261, 3557, 6, 8, 724, 30, 1285, 90, 7200, 900, 191, 1052, 90, 478, 267, 407, 177, 1158, 877, 81, 295, 92, 86, 10750, 119, 23, 24, 22, 14, 1283, 1083, 1194, 79, 70, 748, 6, 112, 30, 14, 10, 10, 25, 14, 116, 197, 3003, 23000, 57, 2, 50, 6, 9, 13, 7, 82, 25, 12, 1, 7, 22, 28, 44, 40, 4, 31, 48, 16, 8, 10, 7, 69, 20, 7, 17, 13, 428, 44, 2854, 19514, 11, 9, 15, 25, 28, 2800, 154, 30, 31, 2484, 2531, 150, 531, 271, 281, 224, 3, 13, 71, 603, 776, 20, 1326, 10, 79, 153, 585, 1, 21, 100, 108, 199, 3, 9, 6, 3, 75, 78, 94, 19, 31, 11, 1, 231, 3, 11, 13, 967, 2, 27, 7, 12, 2640, 2228, 5, 34, 112, 16267, 73, 60, 1457, 49, 7, 6, 5, 98, 7, 4, 234, 10, 9, 103, 9, 8, 7, 10, 6, 640, 1, 7, 394, 409, 593, 334, 7, 10, 411, 490, 264, 383, 299, 9, 10, 227, 72, 16, 15, 20];
			
			$notFound = array();
			foreach($itemCodes as $key=>$itemCode):
				$this->db->where('item_code',$itemCode);
				$itemData = $this->db->get('item_master')->row();
				
				if(!empty($itemData)):
					$stockTrans = [
						'location_id' => 11,
						'batch_no' => "OS/22-23",
						'trans_type' => 1,
						'item_id' => $itemData->id,
						'qty' => $itemQty[$key],
						'ref_type' => -1,
						'ref_no' => 'OP Stock Adjustment',
						'ref_date' => "2022-04-01",						
					];
					$this->db->insert("stock_transaction",$stockTrans);
					
				else:
					$notFound[] = $itemCode;
				endif;
			endforeach;
			
			if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo 'Stock updated successfully. not found : <br> '.implode(", ",$notFound);
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        } 
	} */

    /* public function migrateHSNCodes(){
        try{
            $this->db->trans_begin();

            $this->db->where('is_delete',0);
            $this->db->select("hsn_code");
            $this->db->where('hsn_code !=',"");
            $this->db->group_by('hsn_code');
            $result = $this->db->get('item_master')->result();

            $i=0;
            foreach($result as $row):
                $row->hsn_code = trim($row->hsn_code);

                $this->db->where('is_delete',0);
                $this->db->where('hsn_code',$row->hsn_code);
                $check = $this->db->get('hsn_master');

                if($check->num_rows() > 0):
                    $hsnData = $check->row();
                    $this->db->where('id',$hsnData->id);
                    $this->db->update('hsn_master',['hsn_code'=>$row->hsn_code]);
                else:
                    $this->db->insert('hsn_master',['hsn_code'=>$row->hsn_code]);
                endif;
                $i++;
            endforeach;

            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo $i.' HSN Codes updated successfully.';
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        } 
    } */
    
    /* public function updateJobNoInStockTrans(){
		try{
            $this->db->trans_begin();
			
			$this->db->where('ref_type',24);
			$this->db->where('created_by',0);
			$this->db->where('is_delete',0);
			$result = $this->db->get('stock_transaction')->result();
			$i=1;
			foreach($result as $row):
				$this->db->where('id',$row->ref_id);
				$jobData = $this->db->get('job_card')->row();
				$this->db->reset_query();
				if(!empty($jobData))
				{
					$prfx = explode('/',$jobData->job_prefix);
					$jobno = $prfx[0].'/'.$jobData->job_no.'/'.$prfx[1].'-R';
					$this->db->where('id',$row->id);
					$updateData = ['batch_no'=>$jobno];
					print_r(json_encode($updateData).' *** '.$i++.'<br>');
					//$this->db->update('stock_transaction',$updateData);
					$this->db->reset_query();
				}
				else{print_r($row->ref_id.',');}
			endforeach;
			
			if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo 'Job Number updated successfully.';
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        } 
	} */
	
	
    /* public function migrateRejRw(){
        try{
            $this->db->trans_begin();

            $this->db->where('is_delete',0);
            $result = $this->db->get('production_log')->result();

            $i=0;
            foreach($result as $row):
                $rejArr = Array();
				if(!empty($row->rej_reason))
				{
					$rejArr = json_decode($row->rej_reason);
					if(!empty($rejArr))
					{
						foreach($rejArr as $rjrow)
						{
							//[{"rej_qty":"4","rej_reason":"5","rej_reason_code":"","rejection_reason":"Damage","rej_remark":""}]
							//[{"rej_qty":"3","rej_reason":"68","rej_stage":"0","rej_stage_name":"Row Material","rej_from":"0","rej_party_name":"In House","rej_remark":"","rejection_reason":"Material Crack"}]
							
							$rjData = Array();
							//$rjData['id']="";
							$rjData['log_id']=$row->id;
							$rjData['manag_type']=1;
							$rjData['job_card_id']=$row->job_card_id;
							$rjData['qty']=$rjrow->rej_qty;
							$rjData['reason']=$rjrow->rej_reason;
							$rjData['reason_name']=$rjrow->rejection_reason;
							$rjData['belongs_to']=(!empty($rjrow->rej_stage)) ? $rjrow->rej_stage : '';
							$rjData['belongs_to_name']=(!empty($rjrow->rej_stage_name)) ? $rjrow->rej_stage_name : '';
							$rjData['vendor_id']=$rjrow->rej_from;
							$rjData['vendor_name']=$rjrow->rej_party_name;
							$rjData['remark']=$rjrow->rej_remark;
							$rjData['created_by']=1;
							$rjData['created_at']=date('Y-m-d H:i:s');
							
							//print_r($rjData);print_r('***'.$row->id.'<br>');
							//$this->db->insert('rej_rw_management',$rjData);$i++;
						}
					}
				}
				
                $rwArr = Array();
				if(!empty($row->rw_reason))
				{
					$rwArr = json_decode($row->rw_reason);
					if(!empty($rwArr))
					{
						foreach($rwArr as $rwrow)
						{
							//[{"rej_qty":"4","rej_reason":"5","rej_reason_code":"","rejection_reason":"Damage","rej_remark":""}]
							//[{"rw_qty":"25","rw_reason":"107","rw_stage":"-1","rw_stage_name":"Handling Movement","rw_from":"0","rw_party_name":"In House","rw_remark":"","rework_reason":"Damage"}]
							
							$rwData = Array();
							//$rwData['id']="";
							$rwData['log_id']=$row->id;
							$rwData['manag_type']=2;
							$rwData['job_card_id']=$row->job_card_id;
							$rwData['qty']=$rwrow->rw_qty;
							$rwData['reason']=$rwrow->rw_reason;
							$rwData['reason_name']=$rwrow->rework_reason;
							$rwData['belongs_to']=(!empty($rwrow->rw_stage)) ? $rwrow->rw_stage : '';
							$rwData['belongs_to_name']=(!empty($rwrow->rw_stage_name)) ? $rwrow->rw_stage_name : '';
							$rwData['vendor_id']=$rwrow->rw_from;
							$rwData['vendor_name']=$rwrow->rw_party_name;
							$rwData['remark']=$rwrow->rw_remark;
							$rwData['created_by']=1;
							$rwData['created_at']=date('Y-m-d H:i:s');
							
							//print_r($rwData);print_r('***'.$row->id.'<br>');
							//$this->db->insert('rej_rw_management',$rwData);$i++;
						}
					}
				}
				
                
            endforeach;

            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo $i.' Rej Rw Migrated successfully.';
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        } 
    } */
    
    // Stock By Jayveer @22-06-2022
	public function rmStockUpdate(){
        try{
            $this->db->trans_begin();
            
            $this->db->reset_query();
            $this->db->where('is_delete',0);
            $result = $this->db->get('rmstock')->result();
			//print_r($this->db->last_query());exit;
            
			$i=0;
			foreach($result as $row):
				$stockTrans=array();
				$trans_type = 0;
				if($row->stock_qty > 0){$trans_type = 1;}
				if($row->stock_qty < 0){$trans_type = 2;}
				$stockTrans = [
					'location_id' => $row->location_id,
					'batch_no' => $row->batch_no,
					'trans_type' => $trans_type,
					'item_id' => $row->item_id,
					'qty' => $row->stock_qty,
					'ref_no' => 'Stock Adjustment',
					'ref_type' => 99,
					'ref_date' => date('Y-m-d')						
				];	$i++;
				//print_r($stockTrans);print_r('<br>');
				//$this->db->insert("stock_transaction",$stockTrans);
			endforeach;

            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo 'RM Stock updated successfully. '.$i.' Recored updated';
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        } 
    }
    
    /* public function migrateStockEffectInStockTransaction($item_type){
		try{
            $this->db->trans_begin();
			
			if(!empty($item_type))
			{
				$this->db->select('stock_transaction.id,stock_transaction.item_id,stock_transaction.stock_effect, stock_transaction.ref_type');
				$this->db->where('item_master.item_type',$item_type);
				$this->db->where('stock_transaction.is_delete',0);
                $this->db->join('item_master','item_master.id = stock_transaction.item_id','left');
				$result = $this->db->get('stock_transaction')->result();
				$i=1;
				foreach($result as $row):
					$refType = [7,8,23,24,26,27];
					if(!in_array($row->ref_type,$refType))
					{
						$updateData = ['stock_effect'=>1];
						print_r(json_encode($updateData).' *** '.$i++.'<br>');
						$this->db->reset_query();
						//$this->db->where('id',$row->id);
						//$this->db->update('stock_transaction',$updateData);
					}
				endforeach;
				
				if($this->db->trans_status() !== FALSE):
					$this->db->trans_commit();
					echo 'Stock Effect updated successfully.';
				endif;
			}
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        } 
	} */
	
	/* public function migratePartyOpening(){
        try{
            $this->db->trans_begin();

            $this->db->where('opening_balance <>',0);
            $this->db->where('is_delete',0);
            $partyData = $this->db->get("party_master")->result();
            foreach($partyData as $row): 
                //get ledger trans amount total
                $this->db->select("SUM(amount * p_or_m) as ledger_amount");
                $this->db->where('trans_date <=',"2022-03-31");
                $this->db->where('vou_acc_id',$row->id);
                $this->db->where('is_delete',0);
                $ledgerTrans = $this->db->get('trans_ledger')->row();

                $ledgerAmount = (!empty($ledgerTrans->ledger_amount))?$ledgerTrans->ledger_amount:0;

                //update colsing balance
                $this->db->set("opening_balance","`opening_balance` - ".$ledgerAmount,FALSE);
                $this->db->where('id',$row->id);
                $this->db->update('party_master');
            endforeach;

            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "Opening Balance Migration Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    } */

    public function updateLedgerClosingBalance(){
        try{
            $this->db->trans_begin();

            $partyData = $this->db->where('is_delete',0)->get("party_master")->result();
            foreach($partyData as $row):
                //Set oprning balance as closing balance
                $this->db->where('id',$row->id);
                $this->db->update('party_master',['cl_balance'=>'opening_balance']);

                //get ledger trans amount total
                $this->db->select("SUM(amount * p_or_m) as ledger_amount");
                $this->db->where('vou_acc_id',$row->id);
                $this->db->where('is_delete',0);
                $ledgerTrans = $this->db->get('trans_ledger')->row();

                $ledgerAmount = (!empty($ledgerTrans->ledger_amount))?$ledgerTrans->ledger_amount:0;

                //update colsing balance
                $this->db->set("cl_balance","`cl_balance` + ".$ledgerAmount,FALSE);
                $this->db->where('id',$row->id);
                $this->db->update('party_master');
            endforeach;

            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "Closing Balance Migration Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }
    
    
    /* public function updateGrn(){
        try{
            $this->db->trans_begin();

            $this->db->select('id');
            $this->db->where('grn_date <','2022-04-01');
            $this->db->where('is_delete',0);
            $result = $this->db->get('grn_master')->result();

            foreach($result as $row):
                $this->db->where('grn_id',$row->id);
                $this->db->where('is_delete',0);
                $this->db->update('grn_transaction',['trans_status'=>1]);

                $this->db->where('id',$row->id)->update('grn_master',['trans_status'=>1]);
            endforeach;

            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "GRN Migration Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    } */

    /* public function updateTransChildInStockTrans(){
        try{
            $this->db->trans_begin();

            $this->db->select('trans_child.*,trans_main.trans_prefix,trans_main.trans_no,trans_main.trans_date');
            $this->db->join('trans_main','trans_main.id = trans_child.trans_main_id','left');
            $this->db->where_in('trans_child.trans_main_id',[6294,6411,6423]);
            $this->db->where('trans_child.is_delete',0);
            $result = $this->db->get('trans_child')->result();

            foreach($result as $row):
                $this->db->where('ref_id',$row->id);
                $this->db->where('trans_type',2);
                $this->db->where('ref_type',4);
                $this->db->update('stock_transaction',['is_delete'=>1]);

                $location_id = array();$batch_no=array();$batch_qty=array();$rev_no=array();
                $location_id = explode(",",$row->location_id);
                $batch_no = explode(",",$row->batch_no);
                $batch_qty = explode(",",$row->batch_qty);
                $rev_no = explode(",",$row->rev_no);

                foreach($batch_qty as $bk=>$bv):
                    $stockQueryData['location_id']=$location_id[$bk];
                    $stockQueryData['batch_no'] = $batch_no[$bk];
                    $stockQueryData['trans_type']=2;
                    $stockQueryData['item_id']=$row->item_id;
                    $stockQueryData['qty'] = "-".$bv;
                    $stockQueryData['ref_type']=4;
                    $stockQueryData['ref_id']=$row->id;
                    $stockQueryData['ref_no']=getPrefixNumber($row->trans_prefix,$row->trans_no);
                    $stockQueryData['ref_date']=$row->trans_date;
                    $stockQueryData['created_by']=$row->created_by;
                    $this->db->insert('stock_transaction',$stockQueryData);
                endforeach;
            endforeach;

            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "Trans Child Item Stock Migration Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    } */

    /* public function migrateCreditDaysTransMain(){
        try{
            $this->db->trans_begin();
            
            $this->db->select('trans_main.id,party_master.gstin,party_master.credit_days');
            $this->db->join('party_master','party_master.id = trans_main.party_id','LEFT');
			//$this->db->where('trans_main.is_delete',0);
			$invoiceData = $this->db->get("trans_main")->result();
            if(!empty($invoiceData))
            {
                foreach($invoiceData as $row)
                {
                    $updateData = Array();
                    if(!empty($row->gstin))
                    {
                        $updateData['gstin'] = $row->gstin;
                        $updateData['party_state_code'] = substr($row->gstin, 0, 2);
                    }
                    $updateData['credit_period'] = $row->credit_days;
                    //if($row->cm_id==1){$updateData['memo_type'] = 'Cash';}
                    print_r($updateData);print_r('<br>');
                    $this->db->where('id',$row->id);
                    //$this->db->update('trans_main',$updateData);
                }
            }
            
            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "Org Price Migration Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    } */

    public function migratePaymentTransMain(){
        try{
            $this->db->trans_begin();
            
            $this->db->update('trans_main',['paid_amount'=>0]);
            
            $this->db->where('is_delete',0);
			$this->db->where('ref_id != ','');
			$this->db->where_in('entry_type',[15,16]);
			$paymentData = $this->db->get("trans_main")->result();

            if(!empty($paymentData)):
                foreach($paymentData as $row):                    
                    $this->db->where('id',$row->ref_id);
                    $this->db->where("net_amount != paid_amount");
                    $invData = $this->db->get('trans_main')->row();

                    if(!empty($invData)):
                        $pendingAmount = 0;
                        $pendingAmount = $row->net_amount - $invData->net_amount;
                        $adAmount = 0;
                        if($pendingAmount > 0):
                            $adAmount = $invData->net_amount;
                        elseif($pendingAmount < 0):
                            $adAmount = $row->net_amount;
                        else:
                            $adAmount = $invData->net_amount;
                        endif;

                        $refData = array();
                        $refData[] = ['trans_main_id'=>$row->ref_id,'ad_amount'=>$adAmount];

                        $this->db->where('id',$row->ref_id);
                        $this->db->set('paid_amount','`paid_amount` + '.$adAmount,false);
                        $this->db->update('trans_main');

                        $this->db->where('id',$row->id);
                        $this->db->set('extra_fields',json_encode($refData));
                        $this->db->set('paid_amount','`paid_amount` + '.$adAmount,false);
                        $this->db->update('trans_main');
                    endif;
                endforeach;
            endif;
            
            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "Payment Adjustment migrated successfully.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }

    public function updateTransNumber(){
        try{
            $this->db->trans_begin();

            $this->db->where('is_delete',0);
			$this->db->where_in('entry_type',[15,16]);
			$paymentData = $this->db->get("trans_main")->result();

            foreach($paymentData as $row):
                $this->db->where('trans_main_id',$row->id);
                $this->db->update('trans_ledger',['entry_type'=>$row->entry_type,'trans_date'=>$row->trans_date,'trans_number'=>getPrefixNumber($row->trans_prefix,$row->trans_no),'doc_date'=>$row->doc_date,'doc_no'=>$row->doc_no,'trans_mode'=>$row->trans_mode,'vou_name_s'=>getVoucherNameShort($row->entry_type),'vou_name_l'=>getVoucherNameLong($row->entry_type)]);
            endforeach;

            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "Payment Trans Number migrated successfully.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }

    /* public function updatePackingTransDispatchQty(){
        try{
            $this->db->trans_begin();

            $this->db->where_in('entry_type',[5,6,7,8]);
            $this->db->where('is_delete',0);
            $this->db->where('rev_no !=','');
            $result = $this->db->get('trans_child')->result();

            $this->db->where('version',2);
            $this->db->update('packing_transaction',['dispatch_qty'=>0]);
            foreach($result as $row):
                $packingIds = explode(",",$row->rev_no);
                $disc_qty = explode(",",$row->batch_qty);

                foreach($packingIds as $key => $ptid):
                    $this->db->where('id',$ptid);
                    $this->db->set('dispatch_qty','`dispatch_qty` + '.$disc_qty[$key],false);
                    $this->db->update('packing_transaction');
                endforeach;
            endforeach;

            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                $this->db->trans_rollback();
                echo "Packing Dispatch Qty Migration Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    } */

    /* public function migrateShortCloseJobStockTrans(){
        try{
            $this->db->trans_begin();

            //find manual adjustment [Jayveersinh]
            //SELECT * FROM `stock_transaction` WHERE ref_type = 22 AND batch_no LIKE "%JOB%"
            //SELECT ref_id FROM `stock_transaction` WHERE ref_type = 22 AND batch_no LIKE "%JOB%" GROUP BY ref_id
            //SELECT * FROM `job_card` WHERE id IN (103,104,105,120,153,154,155,158,159,162,164) AND order_status = 6

            $this->db->where('order_status',6);
            $this->db->where('version',2);
            $this->db->where_not_in('id',[105,153,154,155,164]);
            $this->db->where('is_delete',0);
            $result = $this->db->get('job_card')->result();

            foreach($result as $job):
                $this->db->where('job_card_id',$job->id);
                $this->db->where('is_delete',0);
                $approvalData = $this->db->get('production_approval')->result();

                foreach($approvalData as $row):
                    if(!empty($row->in_process_id)):
                        $this->db->where('ref_id',$row->in_process_id);
                        $curentPrsStore = $this->db->get('location_master')->row();

                        $this->db->select('SUM(qty) as qty,batch_no');
                        $this->db->where('location_id',$curentPrsStore->id);
                        $this->db->where('item_id',$job->product_id);
                        $this->db->where('ref_id',$job->id);
                        //$this->db->where_in('ref_type',[23,24,31]);
                        $this->db->group_by('batch_no,location_id,item_id');
                        $this->db->having('SUM(qty) <>',0);
                        $stockData = $this->db->get('stock_transaction')->result();

                        if(!empty($stockData)):
                            foreach($stockData as $stk):
                                    $stockMinusTrans = [
                                        'id' => "",
                                        'location_id' => $curentPrsStore->id,
                                        'batch_no' => $stk->batch_no,
                                        'trans_type' => (($stk->qty < 0)?1:2),
                                        'item_id' => $job->product_id,
                                        'qty' => $stk->qty * -1,
                                        'ref_type' => 32,
                                        'ref_id' => $job->id,
                                        'ref_no' => getPrefixNumber($job->job_prefix, $job->job_no),
                                        'trans_ref_id' => $row->id,
                                        'ref_date' => date("Y-m-d"),
                                        'ref_batch'=>'Short Close Migration'.(($stk->qty < 0)?" < 0":" > 0"),
                                        'created_by' => 0,
                                        'stock_effect' => 0
                                    ];
                                    //print_r($stockMinusTrans);print_r("<hr>");
                                    //$this->db->insert('stock_transaction',$stockMinusTrans);
                            endforeach;
                        endif;
                    endif;
                endforeach;

                ///Remove Stock from Hold area
                $this->db->select('SUM(qty) as qty,batch_no');
                $this->db->where('location_id',211);
                $this->db->where('item_id',$job->product_id);
                $this->db->where('ref_id',$job->id);
                //$this->db->where('ref_type',23);
                $this->db->where('is_delete',0);
                $this->db->having('SUM(qty) <>',0);
                $stockHldData = $this->db->get('stock_transaction')->row();

                if(!empty($stockHldData)):
                    $stockMinusTrans = [
                        'id' => "",
                        'location_id' => 211,
                        'batch_no' => $stockHldData->batch_no,
                        'trans_type' => (($stockHldData->qty < 0)?1:2),
                        'item_id' => $job->product_id,
                        'qty' => $stockHldData->qty * -1,
                        'ref_type' => 32,
                        'ref_id' => $job->id,
                        'ref_no' => getPrefixNumber($job->job_prefix, $job->job_no),
                        'ref_date' => date("Y-m-d"),
                        'ref_batch'=>'Short Close Migration'.(($stockHldData->qty < 0)?" < 0":" > 0"),
                        'created_by' => 0,
                        'stock_effect' => 0
                    ];
                    //$this->db->insert('stock_transaction',$stockMinusTrans);
                endif;
            endforeach; 
                      
            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                $this->db->trans_rollback();
                echo "Short Close Job Stock trans Migration Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    } */
    
    /* public function migrateShortCloseJobPacking(){
        try{
            $this->db->trans_begin();

            $this->db->where('order_status',6);
            $this->db->where('version',2);
            $this->db->where_not_in('id',[105,153,154,155,164]);
            $this->db->where('is_delete',0);
            $result = $this->db->get('job_card')->result();

            foreach($result as $job):
                /* $this->db->where('location_id',136);
                $this->db->where('item_id',$job->product_id);
                $this->db->where('ref_id',$job->id);
                $this->db->where('ref_type',7);
                $this->db->where('is_delete',0);
                //$this->db->update('stock_transaction',['is_delete'=>1,'ref_batch'=>'Short Close Migration 136']);
                
                $stockHldData = $this->db->get('stock_transaction')->row();
                print_r($stockHldData);print_r("<hr>"); */

    /*echo $job->id.', ';
                
            endforeach; 
                      
            exit;
            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                //$this->db->trans_rollback();
                echo "Short Close Migration [location_id = 136] Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    } */
    
    public function migrateRejectionToScrap(){
        try{
            $this->db->trans_begin();
    
            $this->db->select('job_card.id as job_card_id,job_card.job_no,job_card.job_prefix,job_card.product_id');
            $this->db->join('job_card','stock_transaction.ref_id = job_card.id','left');
            $this->db->where('stock_transaction.ref_type',24);
            $this->db->where('stock_transaction.is_delete',0);
            $this->db->group_by('stock_transaction.ref_id');            
            $jobCard = $this->db->get('stock_transaction')->result();

            foreach($jobCard as $job):
                $this->db->select('SUM(stock_transaction.qty) as qty,stock_transaction.trans_ref_id,stock_transaction.batch_no,stock_transaction.ref_no,location_master.location,stock_transaction.location_id,stock_transaction.item_id,stock_transaction.ref_id,item_master.item_name,production_log.*');
                $this->db->join('location_master','stock_transaction.location_id = location_master.id','left');
                $this->db->join('item_master','stock_transaction.item_id = item_master.id','left');
                $this->db->join('production_log','production_log.id = stock_transaction.trans_ref_id','left');
                $this->db->where('stock_transaction.ref_id',$job->job_card_id);
                $this->db->where('stock_transaction.ref_type',24);
                $this->db->where('stock_transaction.is_delete',0);
                $this->db->where('stock_transaction.trans_ref_id >',0);
                $this->db->having('SUM(stock_transaction.qty) > 0');
                $this->db->group_by('stock_transaction.trans_ref_id');
                $stockTrans = $this->db->get('stock_transaction')->result();

                foreach($stockTrans as $st):
                    $this->db->where('log_id',$st->trans_ref_id);
                    $this->db->where('manag_type',1);
                    $this->db->where('is_delete',0);
                    $logData = $this->db->get('rej_rw_management')->result();

                    $transData=array();
                    foreach($logData as $log):
                        $this->db->select('SUM(scrap_book_trans.scrap_qty) as qty');
                        $this->db->where('is_delete',0);
                        $this->db->where('rej_log_id',$log->id);
                        $scrapData = $this->db->get('scrap_book_trans')->row();

                        $pending_qty=0;
                        $pending_qty=($log->qty - $scrapData->qty);
                        if ($pending_qty > 0):
                            $this->db->where('id',$st->id);
                            $logSheetData = $this->db->get('production_log')->row();

                            $this->db->where('is_delete',0);
                            $this->db->where('in_process_id',$logSheetData->process_id);
                            $this->db->where('job_card_id',$job->job_card_id);
                            $approveData = $this->db->get('production_approval')->row();

                            $transData[] = [
                                'log_id' => $st->id,
                                'rej_log_id' => $log->id,
                                'scrap_qty' => $pending_qty,
                                'ok_qty' => 0,
                                'rej_reason' => $log->reason,
                                'rej_stage' => $log->belongs_to_name,
                                'rej_from' => $log->vendor_id,
                                'wp_qty' => $approveData->finished_weight,
                                'location_id' => $st->location_id,
                                'ref_no' => $st->ref_no,
                                'ref_id' => $st->ref_id,
                            ];
                            print_r($transData);
                        endif;
                    endforeach;  
                endforeach;
                
                /* if(!empty($transData)):
                    $masterData = array();
                    $masterData = [
                        'trans_date' => date("Y-m-d"),
                        'job_card_id' => $job->job_card_id,
                        'item_id' => $job->product_id,
                        'scrap_qty' => array_sum(array_column($transData,'scrap_qty')),
                        'ok_qty' => 0,
                        'created_by' => 9999
                    ];
                    $this->db->insert('scrap_book',$masterData);
                    $masterId = $this->db->insert_id();
                    
                    $scrapQty=0;$location_id = 0;$ref_no = "";$ref_id = 0;
                    foreach($transData as $row):
                        $row['scrap_id'] = $masterId;
                        $location_id = $row['location_id'];$ref_no = $row['ref_no'];$ref_id = $row['ref_id'];
                        unset($row['location_id'],$row['ref_no'],$row['ref_id']);
                        $this->db->insert('scrap_book_trans',$row);

                        if(!empty($row['scrap_qty'])):
                            $stockMinusTrans = array();
                            $stockMinusTrans = [
                                'location_id' => $location_id,
                                'batch_no' => getPrefixNumber($job->job_prefix, $job->job_no) . '-R',
                                'trans_type' => 2,
                                'item_id' => $job->product_id,
                                'qty' =>  "-" . $row['scrap_qty'],
                                'ref_type' => 24,
                                'ref_no' => $ref_no,
                                'ref_id' => $ref_id,
                                'trans_ref_id' => $row['log_id'],
                                'ref_date' => date("Y-m-d"),
                                'created_by' => 9999,
                                'stock_effect'=>0
                            ];
                            $this->db->insert('stock_transaction',$stockMinusTrans);
                            $scrapQty+=$row['scrap_qty'] * $row['wp_qty'];
                        endif;
                    endforeach;

                    if(!empty($scrapQty)):
                        $this->db->select('job_bom.*,item_master.item_name,item_master.item_type,item_master.qty as stock_qty,material_master.scrap_group,material_master.material_grade');
                        $this->db->join('item_master','job_bom.ref_item_id = item_master.id','left');
                        $this->db->join('material_master','material_master.material_grade = item_master.material_grade','left');
                        $this->db->where('job_bom.item_id',$job->product_id);
                        $this->db->where('job_bom.job_card_id',$job->job_card_id);
                        $this->db->where('job_bom.is_delete',0);
                        $kitData = $this->db->get('job_bom')->row();

                        $stockPlusTrans = [
                            'id' => "",
                            'location_id' => 134,
                            'batch_no' => getPrefixNumber($job->job_prefix, $job->job_no),
                            'trans_type' => 1,
                            'item_id' => $kitData->scrap_group,
                            'qty'=>$scrapQty,
                            'ref_type' => 25,
                            'ref_no' => $kitData->ref_item_id,
                            'ref_batch' => $kitData->material_grade,
                            'ref_id' => $job->job_card_id,
                            'trans_ref_id' => $masterId,
                            'ref_date' => date("Y-m-d"),
                            'created_by' => 9999
                        ];
                        $this->db->insert('stock_transaction',$stockPlusTrans);
                    endif;
                endif; */
            endforeach;exit;

            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                $this->db->trans_rollback();
                echo "Rejection to Scrap Migration Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }
    
    public function addEmployeeInDevice(){
        try{
            $this->db->trans_begin();
            
            $this->db->select('employee_master.id,employee_master.emp_code,employee_master.emp_name');
            //$this->db->where('is_delete',0);
            //$this->db->where('emp_role != ',-1); 
            $this->db->where('emp_code',20345);
            //$this->db->where('attendance_status',1);   
            //$this->db->order_by('emp_code','ASC');  
            //$this->db->limit(20, 80);
            $empList = $this->db->get('employee_master')->result();
    		$i=0;
            foreach($empList as $empData):
                if(!empty($empData->emp_code)):
                    $this->db->where('id',1);
                    $deviceData = $this->db->get('device_master')->row();
                    
                    $empCode = $deviceData->Empcode = trim($empData->emp_code);
                    $empName = $deviceData->emp_name = str_replace(" ","%20",$empData->emp_name);
                    
                    print_r($deviceData);print_r('<hr>');
                    
                    $deviceResponse = $this->biometric->addEmpDevice($deviceData);
                    
                    if($deviceResponse['status'] == 0):
                        print_r('cURL Error #: ' . $deviceResponse['result']);
                    else:
                        $responseData = json_decode($deviceResponse['result']);
                       
                        if(!empty($responseData)):
                            if($responseData->Error == false):
                                $i++;
                            else:
                               print_r('cURL Error #: ' . $deviceResponse['result']);
                            endif;
                        else:
                            print_r('cURL Error #: ' . $deviceResponse['result']);
                        endif;
                    endif;
                endif;
            endforeach;
            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                //$this->db->trans_rollback();
                echo $i." Employee Added Successfully Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }
    
    public function removeProdMovement(){
        try{
            $this->db->trans_begin();

            $this->db->where('ref_id > ',0);
            $this->db->where('is_delete',0);
            $result = $this->db->get('location_master')->result();
        $total =0;
            foreach($result as $row):
                $this->db->where('location_id',$row->id);
                $this->db->where('is_delete',0);
                //$this->db->update('stock_transaction',['ref_batch'=>'remove_prod_move_by_jp']);
                //$stockData = $this->db->get('stock_transaction')->result();
                //$total += count($stockData);
                //print_r(count($stockData));print_r("<hr>".$total.'<br>');
                
                
                //$this->db->update('stock_transaction',['is_delete'=>1,'ref_batch'=>'remove_prod_move_by_jp']);
                
                //$stockHldData = $this->db->get('stock_transaction')->row();
                

                //echo $job->id.', ';
                
            endforeach; 
                      
            
            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                //$this->db->trans_rollback();
                echo "Migration Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    } 
    
    public function transMainUpdatePartyDetailPOM(){
        try{
            $this->db->trans_begin();

            $this->db->where('is_delete',0);
            $invoiceData = $this->db->get('purchase_order_master')->result();
            
            foreach($invoiceData as $row):
                $this->db->select('party_master.*');
                $this->db->where('party_master.id',$row->party_id);
                $partyData = $this->db->get('party_master')->row();

                $partyDetail['party_name'] = $partyData->party_name;
                $partyDetail['contact_person'] = $partyData->contact_person;
                $partyDetail['contact_email'] = $partyData->party_email;
                $partyDetail['party_mobile'] = $partyData->party_mobile;
                //$partyDetail['credit_period'] = $partyData->credit_days;
                
                print_r($partyDetail);print_r('<hr>');
                $this->db->where('id',$row->id);
                $this->db->update('purchase_order_master',$partyDetail);
                
            endforeach;

            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "Trans Main Party Detail Migration Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }
	
    /*public function transMainUpdatePartyDetail(){
        try{
            $this->db->trans_begin();

            $this->db->where('is_delete',0);
            $invoiceData = $this->db->get('trans_main')->result();
            
            foreach($invoiceData as $row):
                $this->db->select('party_master.*');
                $this->db->where('party_master.id',$row->party_id);
                $partyData = $this->db->get('party_master')->row();

                $partyDetail['party_name'] = $partyData->party_name;
                $partyDetail['contact_person'] = $partyData->contact_person;
                $partyDetail['contact_email'] = $partyData->contact_email;
                $partyDetail['party_mobile'] = $partyData->party_mobile;
                $partyDetail['credit_period'] = $partyData->credit_days;
                
                print_r($partyDetail);print_r('<hr>');
                //$this->db->where('id',$row->id);
                //$this->db->update('trans_main',$partyDetail);
                
            endforeach;

            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                echo "Trans Main Party Detail Migration Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }*/
    
    /*public function contactDetailUpdate(){
        try{
            $this->db->trans_begin();

            $this->db->where('is_delete',0);
            $this->db->where('party_category',1);
            $this->db->where('party_type',1);
            $partyData = $this->db->get('party_master')->result();
            $i=1;
            foreach($partyData as $row):
                if(!empty($row->contact_email)){
                    $contact_detail = array();
                    $contact_detail = json_decode($row->contact_detail);
                    $contact_detail[] = [
                        'person' => (!empty($row->contact_person))?trim($row->contact_person):"",
                        'mobile' => (!empty($row->party_mobile))?$row->party_mobile:"",
                        'email' => (!empty($row->contact_email))?$row->contact_email:""
                    ];
                    $contact_json = json_encode($contact_detail);
                    print_r($contact_json);print_r($i++.'*<hr>');
                    //$this->db->where('id',$row->id);
                    //$this->db->update('party_master',['contact_detail'=> $contact_json]);
                }
            endforeach;
            
                    
            
            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                echo "Contact Detail Migration Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }*/
    
    /*** Created By Jp@10-09-2022 update Old Stock to zero  Migration/toolStockUpdate ***/
    public function toolStockUpdate(){
        try{
            $this->db->trans_begin();
            
			$i=0;
		    $this->db->reset_query();
		    $this->db->select("SUM(stock_transaction.qty) as qty,stock_transaction.location_id,stock_transaction.batch_no,stock_transaction.item_id");
            $this->db->join('tool_stock','tool_stock.item_id = stock_transaction.item_id AND tool_stock.created_by = 3');
			$this->db->where('stock_transaction.is_delete',0);
			$this->db->where('tool_stock.qty >',0);
			$this->db->where('tool_stock.created_by',3);
			$this->db->where('tool_stock.item_id !=',0);
            $this->db->group_by('stock_transaction.item_id');
            $this->db->group_by('stock_transaction.location_id');
            $this->db->group_by('stock_transaction.batch_no');
            $stockData = $this->db->get('stock_transaction')->result();

            foreach($stockData as $row):
                //update Old stock
                if($row->qty != 0):
                    $stockTrans=array();
    				$trans_type = 0;$stock_qty=0;
    				if($row->qty > 0){$trans_type = 2;$stock_qty = ($row->qty * -1);}
    				if($row->qty < 0){$trans_type = 1;$stock_qty = abs($row->qty);}
    				$stockTrans = [
    					'location_id' => $row->location_id,
    					'batch_no' => $row->batch_no,
    					'trans_type' => $trans_type,
    					'item_id' => $row->item_id,
    					'qty' => $stock_qty,
    					'remark' => 'STOCK_ADJUST_BY_NYN',
    					'ref_type' => 999,
    					'ref_date' => date('Y-m-d')
    				];	$i++;
    				print_r($stockTrans);print_r('<hr>'); $i++;
    				$this->db->reset_query();
    				//$this->db->insert("stock_transaction",$stockTrans);
                endif;
		    endforeach;
            exit;
            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                echo 'RM Stock updated successfully. '.$i.' Recored updated';
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        } 
    }
    
    /*** Created By Jp@10-09-2022 Update current stock Migration/toolStockUpdateOP***/
    public function toolStockUpdateOP(){
        try{
            $this->db->trans_begin();

            $this->db->reset_query();
			$this->db->where('qty > ',0);
			$this->db->where('item_id !=',0);
			$this->db->where('created_by',4);
            $result = $this->db->get('tool_stock')->result();
			//print_r($this->db->last_query());exit;
            
			$i=0;
			foreach($result as $row):
			    
                /*$this->db->reset_query();
			    $this->db->where('is_delete',0);
			    $this->db->where('item_type',2);
                $this->db->where('item_name',trim($row->item_name));
                $itemData = $this->db->get('item_master')->row();
                
                if(!empty($itemData)):
                     $this->db->reset_query();
                     $this->db->where('id',$row->id);
                     $this->db->update('tool_stock',['item_id'=> $itemData->id]);
                 endif;*/
                
                $this->db->reset_query();
                if($row->qty != 0):
                    $stockTrans=array();
    				$stockTrans = [
    					'location_id' => $row->location_id,
    					'batch_no' => 'OS/17042023',
    					'trans_type' => 1,
    					'item_id' => $row->item_id,
    					'qty' => $row->qty,
    					'remark' => 'STOCK_OP_BY_NYN',
    					'ref_type' => -1,
    					'ref_date' => date('Y-m-d')
    				];	$i++;
    				print_r($stockTrans);print_r('<hr>');
    				//$this->db->insert("stock_transaction",$stockTrans);
                endif;
			    
				
			endforeach;
            exit;
            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                echo 'RM Stock updated successfully. '.$i.' Recored updated';
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        } 
    }
    
    public function updateDispacthQtyInPacking(){
        try{
            $this->db->trans_begin();

            $this->db->where('status',1);
            $result = $this->db->get('temp_table')->result();
            
            $i=1;$ids = array();
            foreach($result as $row):
                
                $packing = array();
                if(!empty($row->ptrans_id)):
                    $this->db->where('id',$row->ptrans_id);
                    $packing = $this->db->get('packing_transaction')->row();
                else:
                    /*$this->db->select('packing_transaction.id');
                    $this->db->join('packing_master','packing_master.id = packing_transaction.packing_id','left');
                    $this->db->where('packing_master.entry_date',date("Y-m-d",strtotime($row->packing_date)));
                    $this->db->where('packing_master.trans_number',trim($row->packing_no));
                    $this->db->where('packing_transaction.item_code',$row->item_code);
                    $packing = $this->db->get('packing_transaction')->row();*/
                endif;
                //$ids[] = $packing->id;
                print_r($packing->id);print_r("<hr>");
                
                if(!empty($row->ptrans_id)):
                    $this->db->where('id',$packing->id);
                    //$this->db->update('packing_transaction',['dispatch_qty'=>$packing->total_qty]);
                else:
                    //$this->db->where('id',$packing->id);
                    //$this->db->update('packing_transaction',['dispatch_qty'=>0]);
                    //$this->db->update('packing_transaction',['dispatch_qty'=>$row->dispatch_qty]);
                endif;
                $i++;
            endforeach;
            //print_r(implode(", ",$ids));
            exit;
            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                $this->db->trans_rollback();
                echo $i." Records Migration Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    } 

    public function updateDispatchQtyOfRegularPacking(){
        try{
            $this->db->trans_begin();

            $this->db->select("packing_transaction.id,packing_transaction.packing_id,packing_transaction.ref_id,packing_transaction.item_id,packing_transaction.item_code,packing_transaction.total_qty,packing_transaction.export_qty,packing_transaction.dispatch_qty,packing_transaction.version");
            $this->db->join('packing_master','packing_master.id = packing_transaction.packing_id','left');
            $this->db->where('packing_master.entry_type','Export');
            $this->db->where('packing_transaction.is_delete ',0);
            $this->db->where('packing_transaction.ref_id > ',0);
            $result = $this->db->get('packing_transaction')->result();

            foreach($result as $row):
                $refData = $this->db->where('id',$row->ref_id)->get('packing_transaction')->row();
                
                if(($refData->total_qty - $refData->dispatch_qty) >= $row->total_qty):
                    /* print_r("id : ".$row->ref_id." Qty. : ".$row->total_qty."<hr>");
                    print_r($refData);print_r("<hr>"); */

                    /*$this->db->set('dispatch_qty',"`dispatch_qty` + ".$row->total_qty,false);
                    $this->db->where('id',$row->ref_id);
                    $this->db->update('packing_transaction');*/
                endif;
            endforeach;
            exit;

            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                //$this->db->trans_rollback();
                echo "Records Migration Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }
    
    public function packingRequestMigration(){
        try{
            $this->db->trans_begin();

            //$this->db->where('status',0);
            $this->db->where('is_delete',0);
            $result = $this->db->get('packing_request')->result();

            foreach($result as $row):
                $this->db->where('trans_child_id',$row->trans_child_id);
                $this->db->where('is_delete',0);
                $transData = $this->db->get('packing_transaction')->result();

                if(!empty($transData)):
                    $this->db->where('id',$row->id)->update('packing_request',['pack_link_qty'=>0,'status'=>0]);
                endif;
            endforeach;


            foreach($result as $row):
                $this->db->where('trans_child_id',$row->trans_child_id);
                $this->db->where('is_delete',0);
                $transData = $this->db->get('packing_transaction')->result();

                if(!empty($transData)):
                    foreach($transData as $trans):
                        //print_r($trans);print_r("<hr>");

                        $this->db->where('id',$trans->id);
                        $this->db->update("packing_transaction",['req_link_qty'=>$trans->total_qty,'req_ids'=>$row->id]);

                        $this->db->where('id',$trans->packing_id);
                        $this->db->update('packing_master',['req_no'=>$row->trans_no]);

                        $this->db->set('pack_link_qty','`pack_link_qty` + '.$trans->total_qty,false);
                        $this->db->where('id',$row->id);
                        $this->db->update("packing_request");
                    endforeach;
                //else:
                    //print_r("Not Found. Req Id : ".$row->id);print_r("<hr>");
                endif;

                $this->db->where('id',$row->id);
                $reqData = $this->db->get('packing_request')->row();

                if($reqData->pack_link_qty >= $reqData->request_qty):
                    $this->db->where('id',$row->id)->update('packing_request',['status'=>1]);
                endif;
            endforeach;

            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                //$this->db->trans_rollback();
                echo "Records Migration Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }
    
    // Update Supplier/Vendor Code By JP @17-11-2022
	public function upgradeSupplierCode(){
        try{
            $this->db->trans_begin();
			$this->db->where('is_delete',0);
            //$this->db->where('party_category',2); // Vendor
			$this->db->where('party_category',3); // Supplier
			$this->db->where('party_type',1);
            $partyData = $this->db->get('party_master')->result();
			$i=1;
            foreach($partyData as $row):
                $data = array();
                //$data['party_id'] = $row->id;   
                //$data['party_code'] = 'JV'.str_pad($i, 4, '0', STR_PAD_LEFT); // Vendor
                $data['party_code'] = 'JS'.str_pad($i, 4, '0', STR_PAD_LEFT); // Supplier
				print_r($data);print_r('<br>');
				$this->db->reset_query();
                //$this->db->where('id',$row->id);
                //$this->db->update('party_master',$data);
				$i++;
            endforeach;
			
            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                echo $i. " Records Upgraded Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }
    
    //Migration/migrateTestReport
    /*public function migrateTestReport(){
        try{
            $this->db->trans_begin();
			$this->db->where('is_delete',0);
			$this->db->where('agency_id != ',0);
            $grnData = $this->db->get('grn_transaction')->result();
			$i=1;
            foreach($grnData as $row):
                if(!empty($row->agency_id)):
                    $testData = [
                        'id' => '',
                        'grn_id' => $row->grn_id,
                        'grn_trans_id' => $row->id,
                        'agency_id' => $row->agency_id,
                        'name_of_agency' => $row->name_of_agency,
                        'test_description' => $row->test_description,
                        'sample_qty' => $row->sample_qty,
                        'test_report_no' => $row->test_report_no,
                        'test_remark' => $row->test_remark,
                        'test_result' => $row->test_result,
                        'inspector_name' => $row->inspector_name,
                        'mill_tc' => $row->mill_tc,
                        'created_by' => $row->created_by
                    ];
                    $this->db->insert('grn_test_report',$testData);
                    $i++;
                endif;
            endforeach;
			
            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                echo $i. " Records Upgraded Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }*/
    
    public function updatePackingTransId(){
        try{
            $this->db->trans_begin();

            $this->db->where('is_delete',0);
            $this->db->where('entry_type',5);
            $this->db->where('rev_no !=','');
            $result = $this->db->get('trans_child')->result();

            foreach($result as $row):
                $this->db->where('is_delete',0);
                $this->db->where('from_entry_type',5);
                $this->db->where('ref_id',$row->id);
                $invData = $this->db->get('trans_child')->result();

                foreach($invData as $inv):
                    /* print_r("dc : ".$row->rev_no.", inv : ".$inv->rev_no);
                    print_r("<hr>"); */
                    //$this->db->where('id',$inv->id);
                    //$this->db->update('trans_child',['rev_no'=>$row->rev_no]);
                endforeach;
            endforeach;

            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                echo "Records Upgraded Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }
    
    public function migrateTransNumber(){
        try{
            $this->db->trans_begin();

            $this->db->select("id,trans_prefix,trans_no,trans_number");
            $this->db->where('is_delete',0);
            $result = $this->db->get('trans_main')->result();

            foreach($result as $row):
                print_r('old trans : '.$row->trans_number.' new : '.getPrefixNumber($row->trans_prefix,$row->trans_no));
                print_r('<hr>');
                /* $this->db->where('id',$row->id);
                $this->db->update('trans_main',['trans_number'=>getPrefixNumber($row->trans_prefix,$row->trans_no)]); */
            endforeach;

            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                echo "Records Upgraded Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }
    
    public function migrateRefIdsAndRefNo(){
        try{
            $this->db->trans_begin();

            $this->db->select("id,entry_type,from_entry_type,ref_id");
            $this->db->where('is_delete',0);
            $this->db->where('from_entry_type >',0);
            $this->db->where_in('entry_type',[5,6,7]);
            $this->db->order_by('id','ASC');
            //$this->db->where('id',10221);
            $result = $this->db->get('trans_main')->result();

            foreach($result as $row):
                $this->db->reset_query();

                /* Get Trans Main Ref Ids */
                /*$this->db->select('GROUP_CONCAT(DISTINCT(rtc.trans_main_id) SEPARATOR ", ") as ref_id');
                $this->db->join("trans_child as rtc",'rtc.id = trans_child.ref_id AND rtc.is_delete = 0','left');
                $this->db->where('trans_child.trans_main_id',$row->id);
                $this->db->where('trans_child.is_delete',0);
                $transData = $this->db->get('trans_child')->row();
                
                $this->db->where('id',$row->id);
                $this->db->update('trans_main',['ref_id'=>$transData->ref_id]);*/

                /* Get Trans Main Ref Numbers on Ref Ids */
                $this->db->select("GROUP_CONCAT(DISTINCT(trans_main.trans_number) SEPARATOR ', ') as trans_number,GROUP_CONCAT(DISTINCT(tm.trans_number) SEPARATOR ', ') as ref_trans_number");
                $this->db->join('trans_main as tm',"FIND_IN_SET(tm.id, trans_main.ref_id) > 0",'left');
                $this->db->where_in('trans_main.id',explode(",",$row->ref_id));
                $this->db->where('trans_main.is_delete',0);
                $this->db->group_by('trans_main.id');
                $refData = $this->db->get('trans_main')->row();
                //print_r($refData);

                $transMainData = array();
                if($row->entry_type == 5):
                    $transMainData['doc_no'] = (!empty($refData->trans_number))?$refData->trans_number:"";
                else:
                    if($row->from_entry_type == 5):
                        $transMainData['doc_no'] = (!empty($refData->ref_trans_number))?$refData->ref_trans_number:"";
                        $transMainData['challan_no'] = (!empty($refData->trans_number))?$refData->trans_number:"";
                    else:
                        $transMainData['doc_no'] = (!empty($refData->trans_number))?$refData->trans_number:"";
                        $transMainData['challan_no'] = "";
                    endif;
                endif;

                $this->db->where('id',$row->id);
                $this->db->update('trans_main',$transMainData);

                /* print_r($transMainData);
                print_r("<hr>"); */
            endforeach;exit;
            
            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                echo "Records Upgraded Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }

	/*** By : JP @05.01.2023 ***/
	public function migrateOldShiftLog(){
        try{
            $this->db->trans_begin();
            $this->db->reset_query();
            $cmonth = date('m',strtotime(date('2022-06-01')));
            $cyear = date('Y',strtotime(date('2022-06-01')));
            $this->db->where('is_delete',0);
            $this->db->where('attendance_date >= ',date('2023-01-01'));
            $this->db->where('attendance_date <= ',date('2023-01-31'));
            //$this->db->where('MONTH(attendance_date)',$cmonth);
            $this->db->order_by('attendance_date','ASC');
            $osLog = $this->db->get('emp_shift_log')->result();
			
			if(!empty($osLog))
			{
				foreach($osLog as $row)
				{
					$punchData = Array();$empList = Array();
					if(!empty($row->shift_id))
					{
						$cmonth = date('m',strtotime($row->attendance_date));
						$cyear = date('Y',strtotime($row->attendance_date));
						
						$this->db->reset_query();
						$this->db->where('id',$row->shift_id);
						$shiftData = $this->db->get('shift_master')->row();
						
						$shiftData->latest_id = (!empty($shiftData->latest_id)) ? $shiftData->latest_id : 0;
						$day = date('d',strtotime($row->attendance_date));
		
						$prevData=Array();$empShiftLog = Array();
						$this->db->where('MONTH(month)',$cmonth);
						$this->db->where('YEAR(month)',$cyear);
						$this->db->where('emp_id',$row->emp_id);
						$this->db->where('is_delete',0);
						$prevData = $this->db->get('emp_shiftlog')->row();
						
						for($fkey=intVal($day);$fkey<=intVal(date('t',strtotime(date($cyear.'-'.$cmonth.'-01'))));$fkey++)
						{
							$empShiftLog['d'.$fkey]=$shiftData->latest_id;
						}
						$empShiftLog['created_by']=1;
						$empShiftLog['created_at']=date('Y-m-d H:i:s');
						
						/*
						$this->db->reset_query();
						if(empty($prevData)):
							$empShiftLog['month']=date($cyear.'-'.$cmonth.'-01');$empShiftLog['emp_id']=$row->emp_id;
							$this->db->insert('emp_shiftlog',$empShiftLog);//$inserted++;
						else:
							$this->db->where('id',$prevData->id);
							$this->db->update('emp_shiftlog',$empShiftLog);//$updated++;
						endif;*/
					}
				}
			}
			
            exit;
            
            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                echo "Shift Migration Successfully.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }
    
    public function migrateOldShiftLog2(){
        
		//$day = date('d',strtotime('2022-10-01')); $cmonth = date('m',strtotime('2022-10-01')); $cyear = date('Y',strtotime('2022-10-01'));$inserted=0;$updated=0;
		//$day = date('d',strtotime('2022-12-01'));$cmonth = date('m',strtotime('2022-12-01'));$cyear = date('Y',strtotime('2022-12-01'));$inserted=0;$updated=0;
		$day = date('d',strtotime('2023-01-01'));$cmonth = date('m',strtotime('2023-01-01'));$cyear = date('Y',strtotime('2023-01-01'));$inserted=0;$updated=0;
		
		$prevData=Array();
		$this->db->select('id,d10');
		$this->db->where('MONTH(month)',$cmonth);
		$this->db->where('YEAR(month)',$cyear);
		//$this->db->where('emp_id',$row->emp_id);
		//$this->db->where('is_delete',0);
		$prevData = $this->db->get('emp_shiftlog')->result();
		$newshift[0] = 0;$newshift[1] = 5;$newshift[2] = 4;$newshift[3] = 5;$newshift[4] = 4;$newshift[5] = 5;$newshift[6] = 7;$newshift[7] = 7;
        if(!empty($prevData)):
			foreach($prevData as $row):
			    $empShiftLog = Array();
				for($fkey=5;$fkey<=9;$fkey++)
				{
				    $field = 'd'.$fkey;
				    $empShiftLog['d'.$fkey]=$row->d10;
				}
				
				//print_r($empShiftLog);print_r('<br>');
				
				//$empShiftLog['created_by']=1;
				//$empShiftLog['created_at']=date('Y-m-d H:i:s');
				
				//$this->db->where('id',$row->id);
			    //$this->db->update('emp_shiftlog',$empShiftLog);$updated++;
				
				/*if(empty($prevData)):
					$empShiftLog['month']=date('Y-m-01',strtotime(date($cyear.'-'.$cmonth.'-01')));
					$empShiftLog['emp_id']=$row->id;
    				$this->db->insert('emp_shiftlog',$empShiftLog);$inserted++;
    			else:
				    $this->db->where('id',$prevData->id);
				    $this->db->update('emp_shiftlog',$empShiftLog);$updated++;
    			endif;*/
			endforeach;			
        endif;
		echo "INSERTED : ".$inserted." | UPDATED : ".$updated;
    }
    
    public function migrateOldShiftLog1(){
        
		//$day = date('d',strtotime('2022-11-01')); $cmonth = date('m',strtotime('2022-11-01')); $cyear = date('Y',strtotime('2022-11-01'));$inserted=0;$updated=0;
		//$day = date('d',strtotime('2022-12-01'));$cmonth = date('m',strtotime('2022-12-01'));$cyear = date('Y',strtotime('2022-12-01'));$inserted=0;$updated=0;
		$day = date('d',strtotime('2023-01-01'));$cmonth = date('m',strtotime('2023-01-01'));$cyear = date('Y',strtotime('2023-01-01'));$inserted=0;$updated=0;
		
        $empShiftLog = Array();
		//for($fkey=1;$fkey<=8;$fkey++){$empShiftLog['d'.$fkey]=7;}
		//for($fkey=8;$fkey<=31;$fkey++){$empShiftLog['d'.$fkey]=0;}
		
		//print_r($empShiftLog);print_r('<br>');
		
		//$this->db->where('emp_id',112);
		//$this->db->where('MONTH(month)',$cmonth);
		//$this->db->where('YEAR(month)',$cyear);
	    //$this->db->update('emp_shiftlog',$empShiftLog);$updated++;
		echo "INSERTED : ".$inserted." | UPDATED : ".$updated;
    }
	
    public function migrateExportInvSoNo(){
        try{
            $this->db->trans_begin();

            $this->db->where('is_delete',0);
            $this->db->where('entry_type',8);
            $this->db->where('from_entry_type',11);
            $this->db->select('id,ref_id');
            $result = $this->db->get('trans_main')->result();

            $i=1;
            foreach($result as $row):
                $this->db->select('id,extra_fields');
                $this->db->where('id',$row->ref_id);
                $cumInv = $this->db->get('trans_main')->row();

                $jsonData = json_decode($cumInv->extra_fields);
                $so_ids = explode(",",$jsonData->so_id);

                $this->db->where_in('id',$so_ids);
                $this->db->select("GROUP_CONCAT(DISTINCT(trans_main.trans_number) SEPARATOR ', ') as trans_number");
                $so_nos = $this->db->get('trans_main')->row();

                //$this->db->where('id',$row->id);
                //$this->db->update('trans_main',['doc_no'=>$so_nos->trans_number]);
                //print_r($so_nos);print_r("<hr>");
                $i++;
            endforeach;

            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                echo $i." Records Upgraded Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }

    public function migrateCurrencyInCustom(){
        try{
            $this->db->trans_begin();

            $this->db->reset_query();
            $this->db->select('id');
            $this->db->where('is_delete',0);
            $this->db->where('entry_type',19);
            $result = $this->db->get('trans_main')->result();

            foreach($result as $row):
                //$this->db->reset_query();

                $this->db->select('trans_child.id,som.id as so_id,som.currency');
                $this->db->join('packing_transaction','packing_transaction.id = trans_child.ref_id');
                $this->db->join('trans_main as som','packing_transaction.trans_main_id = som.id');
                $this->db->where('trans_child.trans_main_id',$row->id);
                $transData = $this->db->get('trans_child')->result();

                $currency = array_values(array_filter(array_unique(array_column($transData,'currency'))));
                if(!empty($currency)):
                    $currency = $currency[0];
                else:
                    $currency = "USD";
                endif;

                //$this->db->reset_query();

                $this->db->where('id',$row->id);
                $this->db->update('trans_main',['currency'=>$currency]);
            endforeach;

            $this->db->reset_query();
            $this->db->select('id,ref_id');
            $this->db->where('is_delete',0);
            $this->db->where('entry_type',10);
            $result = $this->db->get('trans_main')->result();

            foreach($result as $row):
                $this->db->reset_query();

                $this->db->select('currency');
                $this->db->where('id',$row->ref_id);
                $transData = $this->db->get('trans_main')->row();
                //print_r($transData->currency);print_r("<hr>");

                $this->db->where('id',$row->id);
                $this->db->update('trans_main',['currency'=>$transData->currency]);
            endforeach;

            $this->db->reset_query();
            $this->db->select('id,ref_id');
            $this->db->where('is_delete',0);
            $this->db->where('entry_type',20);
            $result = $this->db->get('trans_main')->result();

            foreach($result as $row):
                $this->db->reset_query();

                $this->db->select('currency');
                $this->db->where('id',$row->ref_id);
                $transData = $this->db->get('trans_main')->row();
                //print_r($transData->currency);print_r("<hr>");

                $this->db->where('id',$row->id);
                $this->db->update('trans_main',['currency'=>$transData->currency]);
            endforeach;

            $this->db->reset_query();
            $this->db->select('id,ref_id');
            $this->db->where('is_delete',0);
            $this->db->where('entry_type',11);
            $result = $this->db->get('trans_main')->result();

            foreach($result as $row):
                $this->db->reset_query();

                $this->db->select('currency');
                $this->db->where('id',$row->ref_id);
                $transData = $this->db->get('trans_main')->row();
                //print_r($transData->currency);print_r("<hr>");

                $this->db->where('id',$row->id);
                $this->db->update('trans_main',['currency'=>$transData->currency]);
            endforeach;

            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "Records Upgraded Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }

	/* 
		Update Consumable items Batch to General Batch
		https://jalaram.nativebittechnologies.com/migration/updateConsumableBatchNo
	*/
    public function updateConsumableBatchNo(){
        try{
            $this->db->trans_begin();
			
			$this->db->reset_query();
            $this->db->select('stock_transaction.id,stock_transaction.batch_no,stock_transaction.remark');
			$this->db->join('item_master','item_master.id = stock_transaction.item_id');
			$this->db->where('stock_transaction.is_delete',0);
			$this->db->where_in('item_master.category_id',[1,2,3]);
			$stockData = $this->db->get('stock_transaction')->result();
			$i=0;
            foreach($stockData as $row):
				$updateData = Array();
				$updateData['batch_no'] = 'General Batch';
				$updateData['remark'] = $row->batch_no.'@JP-03.04.2023';
                if($row->batch_no != 'General Batch'){print_r($updateData);print_r('<hr>');}
				/*$this->db->reset_query();
                $this->db->where('id',$row->id);
                $this->db->update('stock_transaction',$updateData);*/
				$i++;
            endforeach;
			exit;
            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                echo $i." Records Upgraded Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }
	
	public function migrateScrapGroupIdRM(){
		try{
            $this->db->trans_begin();
			
            $i=0;$this->db->reset_query();
            $this->db->select("stock_transaction.id,stock_transaction.item_id, material_master.scrap_group");
            $this->db->where('item_master.item_type',3);
			$this->db->where('item_master.is_delete',0);
			$this->db->where('stock_transaction.is_delete',0);
			$this->db->where('material_master.scrap_group IS NOT NULL');
			$this->db->where('stock_transaction.location_id',134);
			$this->db->join('item_master','item_master.id = stock_transaction.item_id');
			$this->db->join('material_master','material_master.material_grade = item_master.material_grade');
            $result = $this->db->get("stock_transaction")->result();
            foreach($result as $row):
				if(!empty($row->scrap_group))
				{
					$updateData = Array();
					$updateData['remark'] = 'Main_Item#'.$row->item_id;
					$updateData['item_id'] = $row->scrap_group;
					
					print_r($updateData);print_r('<hr>');
					// update Item Master
					$this->db->reset_query();
					$this->db->where('id',$row->id);
					$this->db->update('stock_transaction',$updateData);
					$i++;
				}
            endforeach;
            //exit;
            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo $i. ' Records Updated';
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
        
    }
	
	public function migrateScrapGroupIdFG(){
		try{
            $this->db->trans_begin();
			
            $i=0;$this->db->reset_query();
            $this->db->select("stock_transaction.id,stock_transaction.item_id, material_master.scrap_group");
            $this->db->where('item_master.item_type',1);
			$this->db->where('item_master.is_delete',0);
			$this->db->where('stock_transaction.is_delete',0);
			$this->db->where('material_master.scrap_group IS NOT NULL');
			$this->db->where('stock_transaction.location_id',134);
			$this->db->join('item_master','item_master.id = stock_transaction.item_id');
			$this->db->join('material_master','material_master.material_grade = item_master.material_grade');
            $result = $this->db->get("stock_transaction")->result();
            foreach($result as $row):
				if(!empty($row->scrap_group))
				{
					$updateData = Array();
					$updateData['remark'] = 'Main_Item#'.$row->item_id;
					$updateData['item_id'] = $row->scrap_group;
					
					print_r($updateData);print_r('<hr>');
					//update Item Master
				// 	$this->db->reset_query();
				// 	$this->db->where('id',$row->id);
				// 	$this->db->update('stock_transaction',$updateData);
					$i++;
				}
            endforeach;
            exit;
            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                echo $i. ' Records Updated';
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }        
    }

	public function migratePackingRequestStockDomestic(){
		try{
            $this->db->trans_begin();
			
            $i=0;$this->db->reset_query();
            $this->db->select("trans_child.id,trans_child.item_id,trans_child.request_id,trans_child.packing_qty");
            $this->db->where('trans_child.entry_type',5);
			$this->db->where('trans_child.is_delete',0);
			$this->db->where('trans_child.request_id > ',0);
            $result = $this->db->get("trans_child")->result();
            foreach($result as $row):
				if(!empty($row->request_id))
				{
					$updateData = Array();
					$updateData['id'] = $row->request_id;
					$updateData['trans_id'] = $row->id;
					$updateData['item_id'] = $row->item_id;
					$updateData['packing_qty'] = $row->packing_qty;
					
					print_r($updateData);print_r('<hr>');
					//update Item Master
				    $this->db->reset_query();
					$this->db->where('id',$row->request_id);
					$this->db->set('dispatch_qty','dispatch_qty + '.$row->packing_qty,false);
                    $this->db->update('packing_request');
					$i++;
				}
            endforeach;
            //exit;
            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo $i. ' Records Updated';
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }        
    }

	public function migratePackingRequestStockExport(){
		try{
            $this->db->trans_begin();
			
            $i=0;$this->db->reset_query();
			$this->db->where('export_packing.is_delete',0);
			$this->db->where('export_packing.req_id > ',0);
            $result = $this->db->get("export_packing")->result();
            foreach($result as $row):
				if(!empty($row->req_id))
				{
					$updateData = Array();
					//$updateData['id'] = $row->req_id;
					$updateData['item_id'] = $row->item_id;
					$updateData['dispatch_qty'] = $row->total_qty;
					
					print_r($updateData);print_r('<hr>');
					//update Item Master
					$this->db->reset_query();
					$this->db->where('id',$row->req_id);
					$this->db->where('item_id',$row->item_id);
					$this->db->set('dispatch_qty','dispatch_qty + '.$row->total_qty,false);
                    $this->db->update('packing_request');
					$i++;
				}
            endforeach;
            //exit;
            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo $i. ' Records Updated';
            endif;
        }catch(\Exception $e){
            //$this->db->trans_rollback();
            echo $e->getMessage();exit;
        }        
    }

    /*** Created By : NYN @18.08.2023 Migration/migrateRmStock ***/
    public function migrateRmStock(){
        try{
            $this->db->trans_begin();
            
			$i=0;
		    $this->db->reset_query();
		    $this->db->select("SUM(stock_transaction.qty) as qty,stock_transaction.location_id,stock_transaction.batch_no,stock_transaction.item_id,rmstock.id as rm_id");
            $this->db->join('stock_transaction','rmstock.item_id = stock_transaction.item_id AND rmstock.batch_no = stock_transaction.batch_no');
			$this->db->where('stock_transaction.is_delete',0);
            $this->db->where('stock_transaction.location_id',3);
            $this->db->group_by('stock_transaction.item_id');
            $this->db->group_by('stock_transaction.location_id');
            $this->db->group_by('stock_transaction.batch_no');
            $stockData = $this->db->get('rmstock')->result();

            foreach($stockData as $row):
                //update Old stock
                if($row->qty != 0):
                    $stockTrans=array();
    				$trans_type = 0;$stock_qty=0;
    				if($row->qty > 0){ $trans_type = 2; $stock_qty = ($row->qty * -1); }
    				if($row->qty < 0){ $trans_type = 1; $stock_qty = abs($row->qty); }
    				$stockTrans = [
    					'location_id' => $row->location_id,
    					'batch_no' => $row->batch_no,
    					'trans_type' => $trans_type,
    					'item_id' => $row->item_id,
    					'qty' => $stock_qty,
    					'remark' => 'STOCK_ADJUST_BY_NYN_18082023',
    					'ref_type' => 99,
    					'ref_date' => date('Y-m-d')
    				];	$i++;
    				print_r($stockTrans); print_r('<hr>'); $i++;
    				
    				$this->db->reset_query();
    				//$this->db->insert("stock_transaction",$stockTrans);
    				
    				$this->db->reset_query();
					$this->db->where('id',$row->rm_id);
					$this->db->set('status',1);
                    //$this->db->update('rmstock');
                endif;
		    endforeach;
		    //echo $i.' Records';
            exit;
            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                echo 'RM Stock updated successfully. '.$i.' Recored updated';
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        } 
    }
    
    /*** Created By : NYN @18.08.2023 Migration/migrateMinusRmStock ***/
    public function migrateMinusRmStock(){
        try{
            $this->db->trans_begin();
            
			$i=0;
		    $this->db->reset_query();
		    $this->db->select("SUM(stock_transaction.qty) as stock_qty,stock_transaction.location_id,stock_transaction.batch_no,stock_transaction.item_id");
            $this->db->join('item_master','item_master.id = stock_transaction.item_id');
			$this->db->where('stock_transaction.is_delete',0);
            $this->db->where('item_master.item_type',3);
            $this->db->group_by('stock_transaction.item_id');
            $this->db->group_by('stock_transaction.location_id');
            $this->db->group_by('stock_transaction.batch_no');
            $stockData = $this->db->get('stock_transaction')->result();

            foreach($stockData as $row):
                if($row->stock_qty < 0):
                    $stockTrans=array();
    				$trans_type = 0;$stock_qty=0;
    				if($row->stock_qty > 0){ $trans_type = 2; $stock_qty = ($row->stock_qty * -1); }
    				if($row->stock_qty < 0){ $trans_type = 1; $stock_qty = abs($row->stock_qty); }
    				$stockTrans = [
    					'location_id' => $row->location_id,
    					'batch_no' => $row->batch_no,
    					'trans_type' => $trans_type,
    					'item_id' => $row->item_id,
    					'qty' => $stock_qty,
    					'remark' => 'STOCK_ADJUST_MINUS_BY_NYN_18082023',
    					'ref_type' => 99,
    					'ref_date' => date('Y-m-d')
    				];	$i++;
    				print_r($stockTrans);print_r('<hr>'); $i++;
    				
    				$this->db->reset_query();
    				//$this->db->insert("stock_transaction",$stockTrans);
                endif;
		    endforeach;
		    //echo $i.' Records';
            exit;
            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                echo 'RM Stock updated successfully. '.$i.' Recored updated';
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        } 
    }
    
    /*** Created By : NYN @18.08.2023 Migration/migrateRMStockUpdate ***/
    public function migrateRMStockUpdate(){
        try{
            $this->db->trans_begin();

            $this->db->reset_query();
            $this->db->where('stock_qty > ',0);
            $result = $this->db->get('rmstock')->result();
            
			$i=0;
			foreach($result as $row):
                
                if($row->stock_qty != 0):
                    $stockTrans=array();
    				$stockTrans = [
    					'location_id' => 3, //General Store RM
    					'batch_no' => $row->batch_no,
    					'trans_type' => 1,
    					'item_id' => $row->item_id,
    					'qty' => $row->stock_qty,
    					'remark' => 'OP_STOCK_BY_NYN_18082023',
    					'ref_type' => -1,
    					'ref_date' => date('Y-m-d')
    				];	$i++;
    				print_r($stockTrans);print_r('<hr>');$i++;
    				
    				$this->db->reset_query();
    				//$this->db->insert("stock_transaction",$stockTrans);
                endif;
			endforeach;
            exit;
            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                echo 'RM Stock updated successfully. '.$i.' Recored updated';
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        } 
    }

    /*** Created By : NYN @22.11.2023 Migration/migrateItemMasterLiveToDev ***/
    public function migrateItemMasterLiveToDev(){
        try{
            $this->db->trans_begin();

            $this->db->reset_query();
            $this->db->order_by('item_type','ASC');
            $this->db->where('is_delete',0);
            $this->db->where_in('item_type',[1,3,4,5,10,11]);
            $result = $this->db->get('item_master_live')->result();
            
			$i=0;
			foreach($result as $row):	
				$itmData=array();
				$itmData = [				
					'id' => '',
					'item_code' => $row->item_code,
					'item_name' => $row->item_name,
					'item_alias' => $row->item_alias,
					'party_id' => $row->party_id,
					'item_type' => $row->item_type,
					'category_id' => $row->category_id,
					'family_id' => $row->family_id,
					'description' => $row->description,
					'hsn_code' => $row->hsn_code,
					'gst_per' => $row->gst_per,
					'cess_per' => $row->cess_per,
					'price' => $row->price,
					'avg_price' => $row->avg_price,
					'unit_id' => $row->unit_id,
					'scrape_group' => $row->scrape_group,
					'wt_pcs' => $row->wt_pcs,
					'automotive' => $row->automotive,
					'part_no' => $row->part_no,
					'drawing_no' => $row->drawing_no,
					'drawing_file' => $row->drawing_file,
					'rev_no' => $row->rev_no,
					'rev_specification' => $row->rev_specification,
					'item_image' => $row->item_image,
					'material_grade' => $row->material_grade,
					'size' => $row->size,
					'make_brand' => $row->make_brand,
					'instrument_range' => $row->instrument_range,
					'least_count' => $row->least_count,
					'permissible_error' => $row->permissible_error,
					'gauge_type' => $row->gauge_type,
					'thread_type' => $row->thread_type,
					'fg_id' => $row->fg_id,
					'cal_required' => $row->cal_required,
					'cal_freq' => $row->cal_freq,
					'cal_reminder' => $row->cal_reminder,
					'cal_agency' => $row->cal_agency,
					'last_cal_date' => $row->last_cal_date,
					'next_cal_date' => $row->next_cal_date,
					'cal_certi_no' => $row->cal_certi_no,
					'mfg_year' => $row->mfg_year,
					'install_year' => $row->install_year,
					'installation_date' => $row->installation_date,
					'model' => $row->model,
					'location' => $row->location,
					'device_no' => $row->device_no,
					'prev_maint_req' => $row->prev_maint_req,
					'machine_hrcost' => $row->machine_hrcost,
					'process_id' => $row->process_id,
					'other' => $row->other,
					'job_card_id' => $row->job_card_id,
					'operator_id' => $row->operator_id,
					'stock_effect' => $row->stock_effect
				];
				print_r($itmData);print_r('<hr>');$i++;
				
				$this->db->reset_query();
				//$this->db->insert("item_master_new",$itmData);
				
			endforeach;
            exit;
            if($this->db->trans_status() !== FALSE):
				//$this->db->trans_commit();
                echo 'Item updated successfully. '.$i.' Recored updated';
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        } 
    }

    /*** Created By : NYN @24.11.2023 Migration/migratePackingMaster ***/
    public function migratePackingMaster(){
        try{
            $this->db->trans_begin();

            $this->db->reset_query();
            $this->db->where('is_delete',0);
            $this->db->where('item_type',9);
            $result = $this->db->get('item_master')->result();
            
			$i=0;
			foreach($result as $row):	
				$itmData=array();
				if(trim($row->make_brand) == 'Polythin'):
					$row->full_name = trim($row->item_code).' '.trim($row->location).' ('.trim($row->max_tvalue_per).'X'.trim($row->min_tqty_per).') '.trim($row->material_spec).' Mcr';
					$row->item_name = $row->full_name;
					
					$itmData = [			
						'full_name' => $row->item_name,
						'item_name' => $row->item_name
					];
					print_r($itmData);print_r('<hr>'); $i++;
					
					$this->db->reset_query();
					$this->db->where('id',$row->id);
					//$this->db->update('item_master',$itmData);
					
				elseif(trim($row->make_brand) == 'Box'):
				
					$row->full_name = trim($row->item_code).' '.trim($row->location).' ('.trim($row->max_tvalue_per).'X'.trim($row->min_tqty_per).'X'.trim($row->max_tqty_per).') '.trim($row->typeof_machine).' PLY';
					$row->item_name = $row->full_name;
					
					$itmData = [			
						'full_name' => $row->item_name,
						'item_name' => $row->item_name
					];
					print_r($itmData);print_r('<hr>'); $i++;
					
					$this->db->reset_query();
					$this->db->where('id',$row->id);
					//$this->db->update('item_master',$itmData);
					
				endif;
			endforeach;
            exit;
            if($this->db->trans_status() !== FALSE):
				//$this->db->trans_commit();
                echo 'Item updated successfully. '.$i.' Recored updated';
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        } 
    }

    /*** Created By : NYN @24.11.2023 Migration/migratePackingStandard ***/
    public function migratePackingStandard(){
        try{
            $this->db->trans_begin();

            $this->db->reset_query();
            $this->db->where('is_delete',0);
            $result = $this->db->get('packing_kit')->result();
            
			$i=0;
			foreach($result as $row):	
				$itmData=array();
				
				$this->db->reset_query();
			    $this->db->select('id,item_code');
                $this->db->where('item_type',1);
                $this->db->where('item_code',$row->item_code);
                $item = $this->db->get('item_master')->row();
				
				if(!empty($item->id)):
					$itmData = [			
						'item_id' => $item->id
					];
					print_r($itmData);print_r('<hr>'); $i++;
					
					$this->db->reset_query();
					$this->db->where('id',$row->id);
					//$this->db->update('packing_kit',$itmData);
				endif;
			endforeach;
            exit;
            if($this->db->trans_status() !== FALSE):
				//$this->db->trans_commit();
                echo 'Item updated successfully. '.$i.' Recored updated';
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        } 
    }

    /*** Created By : NYN @24.11.2023 Migration/migratePackingStandardMatrial ***/
    public function migratePackingStandardMatrial(){
        try{
            $this->db->trans_begin();

            $this->db->reset_query();
            $this->db->where('is_delete',0);
            $result = $this->db->get('packing_kit')->result();
            
			$i=0;
			foreach($result as $row):	
				$itmData=array();
				
				$this->db->reset_query();
			    $this->db->select('id,item_code');
				$this->db->where('item_type',9);
                $this->db->where('fg_id',$row->box_id);
                $item = $this->db->get('item_master')->row();
				
				if(!empty($item->id)):
					$itmData = [			
						'box_id' => $item->id,
						'remark' => 'Y'
					];
					print_r($itmData);print_r('<hr>'); $i++;
					
					$this->db->reset_query();
					$this->db->where('id',$row->id);
					//$this->db->update('packing_kit',$itmData);
				endif;
			endforeach;
            exit;
            if($this->db->trans_status() !== FALSE):
				//$this->db->trans_commit();
                echo 'Item updated successfully. '.$i.' Recored updated';
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        } 
    }

	/*** Created By : NYN @03.01.2024 Migration/migrateTransMainId ***/
    public function migrateTransMainId(){
        try{
            $this->db->trans_begin();

			$this->db->reset_query();
            $this->db->where('is_delete',0);
			$this->db->where('entry_type',2);
			$this->db->where('old_id !=',0);
            $soData = $this->db->get('trans_main')->result();
            
            foreach($soData as $row):
                $this->db->reset_query();
				$this->db->where('entry_type',2);
				$this->db->where('trans_main_id',$row->old_id);
				$this->db->update('trans_child',['trans_main_id'=>$row->id]);
            endforeach;
			//exit;
            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "Trans Child table Migration Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }

	/*** Created By : NYN @03.01.2024 Migration/migratePOMasterId ***/
    public function migratePOMasterId(){
        try{
            $this->db->trans_begin();

			$this->db->reset_query();
            $this->db->where('is_delete',0);
            $soData = $this->db->get('purchase_order_master')->result();
            
            foreach($soData as $row):
                $this->db->reset_query();
				$this->db->where('order_id',$row->old_id);
				//$this->db->update('purchase_order_trans',['order_id'=>$row->id]);
            endforeach;
			exit;
            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                echo "Purchase Order Trans table Migration Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }

	/*** Created By : NYN @10.01.2024 Migration/migrateCalibration ***/
    public function migrateCalibration(){
        try{
            $this->db->trans_begin();

			$this->db->reset_query();
            $this->db->where('is_delete',0);
            $qcData = $this->db->get('qc_instruments')->result();
            
            foreach($qcData as $row):
                $this->db->reset_query();
				$this->db->where('item_id',$row->item_id);
				//$this->db->update('calibration',['item_id'=>$row->id]);
            endforeach;
			exit;
            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                //echo "Purchase Order Trans table Migration Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }
    
    /*** Created By : NYN @10.01.2024 Migration/migrateQcPurchaseTrans ***/
    public function migrateQcPurchaseTrans(){
        try{
            $this->db->trans_begin();

			$this->db->reset_query();
            $this->db->where('is_delete',0);
            $this->db->where('item_id',0);
            $this->db->where('order_type',3);
            $qcData = $this->db->get('purchase_order_trans')->result();
            
            foreach($qcData as $row):
                $this->db->reset_query();
				$this->db->where('category_id',$row->category_id);
                $item = $this->db->get('item_master')->row();
                
                $this->db->reset_query();
				$this->db->where('id',$row->id);
				$this->db->update('purchase_order_trans',['item_id'=>$item->id]);
            endforeach;
			//exit;
            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "Purchase Order Trans table Migration Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }
    
    /*** Created By : NYN @20.03.2024 Migration/migrateCalibrationData ***/
    public function migrateCalibrationData(){
        try{
            $this->db->trans_begin();
            
            $this->db->reset_query();
            $this->db->where('is_delete',0);
            $calData = $this->db->get('qc_instruments')->result();
            
            $i=1;
            foreach($calData as $row):
                if(!empty($row->last_cal_date)){
                    $updateData['next_cal_date'] = date('Y-m-d', strtotime($row->last_cal_date . "+".$row->cal_freq." months") );
                
                    $this->db->reset_query();
                    $this->db->where('is_delete',0);
                    $this->db->where('id',$row->id);
                    //$this->db->update("qc_instruments",$updateData);
                    $i++;
                }
            endforeach;
            exit;
            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                //echo "Migration Success.".$i;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }

    public function migrateChQty(){
        try{
            $this->db->trans_begin();
            
            $this->db->reset_query();
            $chData =$this->db->query('select SUM(production_qty) as vendor_qty,out_process_id,job_card_id from production_log where is_delete = 0 AND send_to = 1 AND prod_type =4 group by job_card_id,out_process_id')->result();
            
            // print_r($chData);exit;
            $i=1;
            foreach($chData as $row):
                    $updateData['ch_qty'] = $row->vendor_qty;
                    $this->db->reset_query();
                    $this->db->where('is_delete',0);
                    $this->db->where('in_process_id',$row->out_process_id);
                    $this->db->where('job_card_id',$row->job_card_id);
                    $this->db->update("production_approval",$updateData);
                    $i++;
            endforeach;
            exit;
            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "Migration Success.".$i;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }
    
    /*** Created By : NYN @10.04.2025 Migration/migrateItemId ***/
    public function migrateItemId(){
        try{
            $this->db->trans_begin();

			$this->db->reset_query();
            $this->db->where('item_id',0);
            $stockData = $this->db->get('temp_stock')->result();
            
            $i=1;
            foreach($stockData as $row):
                $this->db->reset_query();
				$this->db->where('item_type',1);
				$this->db->where('is_delete',0);
				$this->db->where('item_code',$row->item_code);
                $itmData = $this->db->get('item_master')->row();
                
                if(!empty($itmData->id)){
                    $this->db->reset_query();
    				$this->db->where('id',$row->id);
                    $this->db->update('temp_stock',['item_id'=>$itmData->id]);  
                }
                $i++;
            endforeach;
			exit;
            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                echo "Item ID Migration Success.".$i;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }
    
    /*** Created By : NYN @10.04.2025 Migration/migratePackingAreaStock ***/
    public function migratePackingAreaStock(){
        try{
            $this->db->trans_begin();

			$this->db->reset_query();
            $this->db->where('item_id != ',0);
            $tempStock = $this->db->get('temp_stock')->result();
            
            $i=1;
            foreach($tempStock as $row):
                $this->db->reset_query();
                $this->db->select("item_id,location_id,batch_no,SUM(qty) as qty");
				$this->db->where('is_delete',0);
                $this->db->where('item_id',$row->item_id);
                $this->db->where('location_id',136);
                $this->db->group_by('location_id');
                $this->db->group_by('batch_no');
			    $this->db->group_by('item_id');
			    $this->db->having('SUM(qty) <> ',0);
                $stockData = $this->db->get('stock_transaction')->result();
                
                $j=0;
                foreach($stockData as $stockrow):
                    if(!empty($stockrow->qty)){
                        
                        $this->db->reset_query();
                        $stock = [
                            'item_id' => $stockrow->item_id,
                            'location_id' => $stockrow->location_id,
                            'batch_no' => $stockrow->batch_no,
                            'qty' => (($stockrow->qty > 0) ? '-'.$stockrow->qty : abs($stockrow->qty)),
                            'trans_type' => (($stockrow->qty > 0) ? 2 : 1),                    
                            'ref_type' => 6,
                            'ref_date' => '2025-04-10',
                            'remark' => 'NBT10042025'
                        ];
                        //$this->db->insert('stock_transaction',$stock);
                        
                        
                        print_r('<pre>'); print_r($this->db->last_query()); print_r('<hr>');
                    }
                endforeach;
                
                $this->db->reset_query();
				$this->db->where('id',$row->id);
                $this->db->update('temp_stock',['status'=>$j++]);  
                
                $i++;
            endforeach;
			exit;
            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                echo "Item ID Migration Success.".$i;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }

    /**** Created By : Mansee @22-05-2025 Migrate Vendor Jobwork Challan */

    public function migrateJobworkChallanreceive(){
        try{
            $this->db->trans_begin();

			$this->db->reset_query();
            $this->db->where('vendor_challan_trans.is_delete',0);
            $this->db->where('vendor_challan_trans.type',2);
            $receiveTrans = $this->db->get('vendor_challan_trans')->result();
            
            $i=0;
            foreach($receiveTrans as $row):
                $this->db->reset_query();
                $trans_no = $this->jobWorkVendor_v3->getNextReceiveNo();
                $trans = [
                    'id'=>'',
                    'trans_no'=>$trans_no,
                    'challan_id'=>$row->challan_id,
                    'ch_trans_id'=>$row->ref_id,
                    'process_id'=>$row->process_id,
                    'production_qty'=>$row->qty,
                    'without_prs_qty'=>$row->without_process_qty,
                    'in_challan_no'=>$row->in_challan_no,
                    'in_challan_date'=>$row->in_challan_date,
                    'created_by'=>$this->loginId,
                    'created_at'=>date("Y-m-d H:i:s"),
                    'accepted_by'=>$this->loginId,
                    'accepted_at'=>date("Y-m-d",strtotime($row->created_at)),
                ];
                $this->db->reset_query();
                $this->db->insert('vendor_receive',$trans);
                
                $i++;
            endforeach;
			exit;
            if($this->db->trans_status() !== FALSE):
                // $this->db->trans_commit();
                echo "Challan Receive Migration Success.".$i;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }
    
    // Migrate Without process Qty
    public function migrateWithoutProcessQty(){
         try{
            $this->db->trans_begin();

			$this->db->reset_query();
            $this->db->where('vendor_challan_trans.is_delete',0);
            $this->db->where('vendor_challan_trans.type',1);
            $this->db->where('vendor_challan_trans.without_process_qty > 0');
            $receiveTrans = $this->db->get('vendor_challan_trans')->result();
            // print_r("<pre>");
            // print_r($receiveTrans);exit;
            $i=0;
            foreach($receiveTrans as $row):
                $this->db->reset_query();
                $receiveData = $this->db->query("SELECT SUM(without_prs_qty) AS total_without_qty FROM vendor_receive WHERE is_delete = 0 AND ch_trans_id = ".$row->id)->row();;
                 print_r("<pre>");
                
                if($row->without_process_qty > $receiveData->total_without_qty){
                    print_r($row->without_process_qty .' > '. $receiveData->total_without_qty);
                    $withoutQty = $row->without_process_qty - (!empty($receiveData->total_without_qty)?$receiveData->total_without_qty:0);
                    $trans_no = $this->jobWorkVendor_v3->getNextReceiveNo();
                    
                	$returnJsn = json_decode($row->return_json);
                	
                    $trans = [
                        'id'=>'',
                        'trans_no'=>$trans_no,
                        'challan_id'=>$row->challan_id,
                        'ch_trans_id'=>$row->id,
                        'process_id'=>$row->process_id,
                        'without_prs_qty'=>$withoutQty,
                        'in_challan_no'=>'',
                        'in_challan_date'=> ((!empty($returnJsn[0]->entry_date))?$returnJsn[0]->entry_date:date("Y-m-d")),
                        'created_by'=>$this->loginId,
                        'created_at'=>date("Y-m-d H:i:s"),
                        'accepted_by'=>$this->loginId,
                        'accepted_at'=>date("Y-m-d",strtotime($row->created_at)),
                    ];
                    $this->db->reset_query();
                    // $this->db->insert('vendor_receive',$trans);
                      $i++;
                }
               
            endforeach;
            exit;
            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "Challan Receive Migration Success.".$i;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
        
    }
    
}
?>