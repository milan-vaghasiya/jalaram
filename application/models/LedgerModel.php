<?php 
class LedgerModel extends MasterModel{
	private $partyMaster = "party_master";

	public function getDTRows($data){
		$data['tableName'] = $this->partyMaster;
		$data['select'] = "party_master.*,group_master.name,((party_master.opening_balance) + SUM( CASE WHEN tl.trans_date < '".$this->startYearDate."' THEN (tl.amount * tl.p_or_m) ELSE 0 END )) as op_balance,((party_master.opening_balance) + SUM( CASE WHEN tl.trans_date <= '".$this->endYearDate."' THEN (tl.amount * tl.p_or_m) ELSE 0 END )) as closing_balance";

		$data['leftJoin']['group_master'] = "group_master.id = party_master.group_id";
        $data['leftJoin']['trans_ledger as tl'] = "party_master.id = tl.vou_acc_id";
		
        $data['group_by'][] = 'party_master.id';
        
		$data['searchCol'][] = "group_name";
		$data['searchCol'][] = "party_name";
		$data['searchCol'][] = "is_gst_applicable";
		$data['searchCol'][] = "hsn_code";
		$data['searchCol'][] = "opening_balance";
		$data['searchCol'][] = "cess_per";

		$columns =array('','','group_name','ledger_name','is_gst_applicable','hsn_code','opening_bal','cess_per');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
	}

	public function save($data){
		try{
            $this->db->trans_begin();
            if($this->checkDuplicate($data['party_name'],$data['party_category'],$data['id']) > 0):
                $errorMessage['party_name'] = "Ledger name is duplicate.";
                $result = ['status'=>0,'message'=>$errorMessage];
            else:
                $data['opening_balance'] = (!empty($data['opening_balance']))?$data['opening_balance']:0;
				if(empty($data['id'])):
					$data['cl_balance'] = $data['opening_balance'] = $data['opening_balance'] * $data['balance_type'];
				else:
					$partyData = $this->getLedger($data['id']);
                    $data['opening_balance'] = $data['opening_balance'] * $data['balance_type'];
                    if($partyData->opening_balance > $data['opening_balance']):
                        $varBalance = $partyData->opening_balance - $data['opening_balance'];
                        $data['cl_balance'] = $partyData->cl_balance - $varBalance;
                    elseif($partyData->opening_balance < $data['opening_balance']):
                        $varBalance = $data['opening_balance'] - $partyData->opening_balance;
                        $data['cl_balance'] = $partyData->cl_balance + $varBalance;
                    else:
                        $data['cl_balance'] = $partyData->cl_balance;
                    endif;
				endif;
				$result = $this->store($this->partyMaster,$data,'Ledger');
			endif;
			
			if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
        return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
	}

	public function checkDuplicate($name,$party_category,$id=""){
        $data['tableName'] = $this->partyMaster;
        $data['where']['party_name'] = $name;
        $data['where']['party_category'] = $party_category;
        
        if(!empty($id))
            $data['where']['id !='] = $id;
            
        return $this->numRows($data);
    }

	public function getLedger($id){
        $data['where']['id'] = $id;
        $data['tableName'] = $this->partyMaster;
        return $this->row($data);
    }

    public function getLedgerList($groupCode = array()){
        $queryData = array();
        $queryData['tableName'] = $this->partyMaster;
        $queryData['where']['party_category'] = 4;
        if(!empty($groupCode))
            $queryData['where_in']['group_code'] = $groupCode;
        $result = $this->rows($queryData);
        return $result;
    }

    public function getLedgerOnSystemCode($systemCode){
        $queryData = array();
        $queryData['tableName'] = "party_master";
        $queryData['where']['system_code'] = $systemCode;
        $ledger = $this->row($queryData);
        return $ledger;
    }

	public function delete($id){
        try{
            $this->db->trans_begin();
            $ledgerData = $this->getLedger($id);
            if(!empty($ledgerData->system_code)):
                return ['status'=>0,'message'=>'You cannot delete. Because This is default ledger.'];
            endif;

            $result = $this->trash($this->partyMaster,['id'=>$id],'Ledger');

            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
        return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
	}
	
	// Created By Meghavi @20/09/2022
    public function getGroupWiseLedger($data){
        $queryData = array();
		$queryData['tableName'] = $this->partyMaster;
        $queryData['select'] = "party_master.*,group_master.name";
		$queryData['leftJoin']['group_master'] = "group_master.id = party_master.group_id";
		if(!empty($data['group_id'])){$queryData['where']['party_master.group_id'] = $data['group_id'];}
		return $this->rows($queryData);
    }

    public function saveOpeningBalance00($data){
        try{
            $this->db->trans_begin();

            if(!empty($data['id'])):
                $data['opening_balance'] = (!empty($data['opening_balance']))?$data['opening_balance']:0;
                $partyData = $this->getLedger($data['id']);
                $data['opening_balance'] = $data['opening_balance'] * $data['balance_type'];

                if($partyData->opening_balance > $data['opening_balance']):
                    $varBalance = $partyData->opening_balance - $data['opening_balance'];
                    $data['cl_balance'] = $partyData->cl_balance - $varBalance;
                elseif($partyData->opening_balance < $data['opening_balance']):
                    $varBalance = $data['opening_balance'] - $partyData->opening_balance;
                    $data['cl_balance'] = $partyData->cl_balance + $varBalance;
                else:
                    $data['cl_balance'] = $partyData->cl_balance;
                endif;

                $opData = [
                    'id' => $data['id'],
                    'balance_type' => $data['balance_type'],
                    'opening_balance' => $data['opening_balance'],
                    'cl_balance' => $data['cl_balance'],
                ];
                $this->store($this->partyMaster,$opData);
            endif;

            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return ['status'=>1,'message'=>'Ledger Opening Balance updated successfully.'];
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
        return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function saveOpeningBalance($data){
        try{
            $this->db->trans_begin();

            if(!empty($data['id'])):
                $this->edit($this->partyMaster,['id'=>$data['id']],['opening_balance'=>0]);

                $toDate = date('Y-m-d',strtotime($this->startYearDate.' -1 day'));
                $ledgerData = $this->accountingReport->getLedgerBalance("1970-01-01",$toDate,$data['id']);

                $data['opening_balance'] = floatval($data['opening_balance']);
                $data['opening_balance'] = floatval(($data['opening_balance'] * $data['balance_type']) - $ledgerData->cl_balance);

                $this->edit($this->partyMaster,['id'=>$data['id']],['opening_balance'=>$data['opening_balance'],'balance_type'=>(($data['opening_balance'] >= 0)?1:-1)]);

                $ledgerData = $this->accountingReport->getLedgerBalance("1970-01-01",date("Y-m-d"),$data['id']);
                $this->edit($this->partyMaster,['id'=>$data['id']],['cl_balance'=>$ledgerData->cl_balance]);
            else:
                return ['status'=>1,'message'=>'Somthing is wrong...Ledger not found.'];
            endif;

            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return ['status'=>1,'message'=>'Ledger Opening Balance updated successfully.'];
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
        return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    //Created By Meghavi @20/09/2022
    /* public function saveBulkOpeningBalance($data){
		try{
            $this->db->trans_begin();

            if(!empty($data['id'])):
                foreach($data['id'] as $key=>$value):

                    $data['opening_balance'][$key] = (!empty($data['opening_balance'][$key]))?$data['opening_balance'][$key]:0;
                    $partyData = $this->getLedger($value);
                    $data['opening_balance'][$key] = $data['opening_balance'][$key] * $data['balance_type'][$key];

                    if($partyData->opening_balance > $data['opening_balance'][$key]):
                        $varBalance = $partyData->opening_balance - $data['opening_balance'][$key];
                        $data['cl_balance'][$key] = $partyData->cl_balance - $varBalance;
                    elseif($partyData->opening_balance < $data['opening_balance'][$key]):
                        $varBalance = $data['opening_balance'][$key] - $partyData->opening_balance;
                        $data['cl_balance'][$key] = $partyData->cl_balance + $varBalance;
                    else:
                        $data['cl_balance'][$key] = $partyData->cl_balance;
                    endif;

                    $opData = [
                        'id' => $value,
                        'balance_type' => $data['balance_type'][$key],
                        'opening_balance' => $data['opening_balance'][$key],
                        'cl_balance' => $data['cl_balance'][$key],
                    ];
				    $this->store($this->partyMaster,$opData);
                endforeach;
            endif;
			
			if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return ['status'=>1,'message'=>'Ledger Opening Balance updated successfully.'];
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
        return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
	} */
}
?>