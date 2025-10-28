<?php
class CustomPackingModel extends MasterModel{
    private $transMain = "trans_main";
    private $transChild = "trans_child";
    private $packingMaster = "packing_master";
    private $packingTrans = "packing_transaction";
    private $exportPacking = "export_packing";

    public function getDTRows($data){
        $data['tableName'] = $this->transMain;
        $data['select'] = "trans_main.*,packing_master.trans_number as packing_no";
        $data['leftJoin']['packing_master'] = "packing_master.id = trans_main.ref_by";
        $data['customWhere'][] = 'trans_main.entry_type IN ('.$data['entry_type'].')';
        if($data['status'] == 0):
            $data['where']['trans_main.trans_status'] = $data['status'];
        else:
            $data['where']['trans_main.trans_status'] = $data['status'];
            $data['where']['trans_main.trans_date >= '] = $this->startYearDate;
            $data['where']['trans_main.trans_date <= '] = $this->endYearDate;
        endif;

        $data['order_by']['trans_main.trans_no'] = "DESC";
        
        $data['searchCol'][] = "packing_master.trans_number";
        $data['searchCol'][] = "trans_main.trans_number";
        $data['searchCol'][] = "trans_main.doc_no";
        $data['searchCol'][] = "trans_main.party_name";
        $data['searchCol'][] = "trans_main.net_amount";

        $columns =array('','','trans_main.trans_number','packing_master.trans_number','trans_main.doc_no','','trans_main.party_name','','trans_main.net_amount');

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function getCommercialPackingNoList(){
        $queryData = array();
        $queryData['tableName'] = $this->transMain;
        $queryData['select'] = "id,trans_number,ref_by,trans_status";
        $queryData['where']['entry_type'] = 19;
        $result = $this->rows($queryData);
        return $result;
    }

    public function save($masterData,$itemData){
        try{
            $this->db->trans_begin();
            if(!empty($masterData['id'])):
                $cumPckData = $this->getCustomPackingData($masterData['id']);
                /* Update order status */
                $queryData = array();
                $queryData['tableName'] = $this->exportPacking;
                $queryData['select'] = "so_trans_id";
                $queryData['where']['trans_no'] = $cumPckData->ref_by;
                $queryData['where']['packing_type'] = 2;
                $queryData['where']['status'] = 2;
                $pacData = $this->rows($queryData);
                foreach($pacData as $row):
                    $this->edit($this->transChild,['id'=>$row->so_trans_id],['trans_status'=>0]);
                endforeach;

                $this->edit($this->transMain,['ref_by'=>$cumPckData->ref_by,'entry_type'=>19],['trans_status'=>0]);
                $this->edit($this->transMain,['ref_by'=>$cumPckData->ref_by,'entry_type'=>10],['trans_status'=>0]);
                $this->edit($this->exportPacking,['trans_no'=>$cumPckData->ref_by,'packing_type'=>2,'status'=>2],['status'=>1]);

                // $queryData = array();
                // $queryData['tableName'] = $this->packingTrans;
                // $queryData['where_in']['com_pck_status'] = [0,1];
                // $queryData['where']['packing_id'] = $cumPckData->ref_by;
                // $checkPackingStatus = $this->numRows($queryData);
                // if($checkPackingStatus > 0):
                //     $this->edit($this->packingMaster,['id'=>$cumPckData->ref_by],['packing_status'=>0]);
                // endif;

                $this->trash($this->transChild,['trans_main_id'=>$masterData['id']]);
            else:
                $masterData['trans_date'] = date("Y-m-d");
                $masterData['trans_prefix'] = $this->transModel->getTransPrefix(20);
                $masterData['trans_no'] = $this->transModel->nextTransNo(20);
                $masterData['trans_number'] = getPrefixNumber($masterData['trans_prefix'],$masterData['trans_no']);
            endif;

            $result = $this->store($this->transMain,$masterData,'Custom Packing');
            $result['url'] = base_url('customPacking');
            $masterData['id'] = (empty($masterData['id']))?$result['insert_id']:$masterData['id'];

            foreach($itemData as $row):
                $row['created_by'] = $masterData['created_by'];
                $row['trans_main_id'] = $masterData['id'];
                $row['entry_type'] = $masterData['entry_type'];
                $row['from_entry_type'] = $masterData['from_entry_type'];
                $row['is_delete'] = 0;
                $this->store($this->transChild,$row);
            endforeach;

            if(!empty($masterData['ref_by'])):
                /* Update order status */
                $queryData = array();
                $queryData['tableName'] = $this->exportPacking;
                $queryData['select'] = "so_trans_id,total_qty";
                $queryData['where']['trans_no'] = $masterData['ref_by'];
                $queryData['where']['packing_type'] = 2;
                $packingData = $this->rows($queryData);
                foreach($packingData as $row):
                    $setData = Array();
                    $setData['tableName'] = $this->transChild;
                    $setData['where']['id'] = $row->so_trans_id;
                    $setData['set']['dispatch_qty'] = 'dispatch_qty, + '.$row->total_qty;
                    $this->setValue($setData);

                    $queryData = array();
                    $queryData['tableName'] = $this->transChild;
                    $queryData['where']['id'] = $row->so_trans_id;
                    $transRow = $this->row($queryData);

                    if($transRow->qty <= $transRow->dispatch_qty):
                        $this->store($this->transChild,['id'=>$row->so_trans_id,'trans_status'=>1]);
                    endif;
                    //$this->edit($this->transChild,['id'=>$row->so_trans_id],['trans_status'=>1]);
                endforeach;

                $this->edit($this->transMain,['ref_by'=>$masterData['ref_by'],'entry_type'=>19],['trans_status'=>1]);
                $this->edit($this->transMain,['ref_by'=>$masterData['ref_by'],'entry_type'=>10],['trans_status'=>1]);
                $this->edit($this->exportPacking,['trans_no'=>$masterData['ref_by'],'packing_type'=>2,'status'=>1],['status'=>2]);

                // $queryData = array();
                // $queryData['tableName'] = $this->packingTrans;
                // $queryData['where_in']['com_pck_status'] = [0,1];
                // $queryData['where']['packing_id'] = $masterData['ref_by'];
                // $checkPackingStatus = $this->numRows($queryData);
                // if($checkPackingStatus == 0):
                //     $this->edit($this->packingMaster,['id'=>$masterData['ref_by']],['packing_status'=>1]);
                // endif;
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

    public function getCustomPackingData($id,$is_pdf = 0){
        $queryData = array();
        $queryData['tableName'] = $this->transMain;
        $queryData['where']['id'] = $id;
        $result = $this->row($queryData);
        if(!empty($result->extra_fields)):
            $jsonData = json_decode($result->extra_fields);
            foreach($jsonData as $key=>$value):
                $result->{$key} = $value;
            endforeach;
        endif;

        if($is_pdf == 1):
            $result->itemData = $this->getCustomPackingItemsForPdf($id); 
        else:
            $result->itemData = $this->getCustomPackingItems($id);
        endif;
        return $result;
    }

    public function getCustomPackingItems($id){
        $queryData = array();
        $queryData['tableName'] = $this->transChild;
        $queryData['select'] = "trans_child.*,soi.price as so_price,export_packing.status as packing_status,LPAD(export_packing.package_no, 4, '0') as package_no,item_master.part_no as PartNo,item_master.drawing_no as DrgNo,item_master.rev_no as RevNo";
        $queryData['leftJoin']['trans_child as comp'] = "trans_child.ref_id = comp.id";
        $queryData['leftJoin']['export_packing'] = "export_packing.id = comp.ref_id";
        $queryData['leftJoin']['trans_child as soi'] = "export_packing.so_trans_id = soi.id";
        $queryData['leftJoin']['item_master'] = "item_master.id = trans_child.item_id";
        $queryData['where']['trans_child.trans_main_id'] = $id;
        $result = $this->rows($queryData);
        return $result;
    }

    public function getCustomPackingItemsForPdf($id){
        $queryData = array();
        $queryData['tableName'] = $this->transChild;
        $queryData['select'] = "trans_child.item_id,trans_child.item_code,trans_child.item_name,trans_child.item_desc,trans_child.hsn_code,trans_child.hsn_desc,SUM(trans_child.qty) as qty,AVG(trans_child.price) as price,SUM(trans_child.amount) as amount,SUM(trans_child.net_amount) as net_amount,soi.price as so_price,export_packing.status as packing_status,LPAD(export_packing.package_no, 2, '0') as package_no,item_master.part_no as PartNo,item_master.drawing_no as DrgNo,item_master.rev_no as RevNo,SUM((export_packing.total_qty * export_packing.wpp)) as totalPackWt,SUM(export_packing.pack_weight) as pack_weight,export_packing.wooden_weight,item_master.item_alias";
        $queryData['leftJoin']['trans_child as comp'] = "trans_child.ref_id = comp.id";
        $queryData['leftJoin']['export_packing'] = "export_packing.id = comp.ref_id";
        $queryData['leftJoin']['trans_child as soi'] = "export_packing.so_trans_id = soi.id";
        $queryData['leftJoin']['item_master'] = "item_master.id = trans_child.item_id";
        $queryData['where']['trans_child.trans_main_id'] = $id;
        $queryData['group_by'][] = "trans_child.item_id";
        $queryData['group_by'][] = "export_packing.package_no";
        $queryData['order_by']['export_packing.package_no'] = "ASC";
        $queryData['order_by']['trans_child.id'] = "ASC";
        $result = $this->rows($queryData);
        return $result;
    }

    public function delete($id){
        try{
            $this->db->trans_begin();
            $packingData = $this->getCustomPackingData($id);
            
            $this->trash($this->transChild,['trans_main_id'=>$id]);
            $result = $this->trash($this->transMain,['id'=>$id],'Custom Packing');

            if(!empty($packingData->ref_by)):
                /* Update order status */
                $queryData = array();
                $queryData['tableName'] = $this->exportPacking;
                $queryData['select'] = "so_trans_id,total_qty";
                $queryData['where']['trans_no'] = $packingData->ref_by;
                $queryData['where']['packing_type'] = 2;
                $queryData['where']['status'] = 2;
                $pacData = $this->rows($queryData);
                foreach($pacData as $row):
                    $setData = Array();
                    $setData['tableName'] = $this->transChild;
                    $setData['where']['id'] = $row->so_trans_id;
                    $setData['set']['dispatch_qty'] = 'dispatch_qty, - '.$row->total_qty;
                    $this->setValue($setData);

                    $queryData = array();
                    $queryData['tableName'] = $this->transChild;
                    $queryData['where']['id'] = $row->so_trans_id;
                    $transRow = $this->row($queryData);

                    if($transRow->qty >= $transRow->dispatch_qty):
                        $this->store($this->transChild,['id'=>$row->so_trans_id,'trans_status'=>0]);
                    endif;
                    //$this->edit($this->transChild,['id'=>$row->so_trans_id],['trans_status'=>0]);
                endforeach;

                $this->edit($this->transMain,['ref_by'=>$packingData->ref_by,'entry_type'=>19],['trans_status'=>0]);
                $this->edit($this->transMain,['ref_by'=>$packingData->ref_by,'entry_type'=>10],['trans_status'=>0]);
                $this->edit($this->exportPacking,['trans_no'=>$packingData->ref_by,'packing_type'=>2,'status'=>2],['status'=>1]);

                // $queryData = array();
                // $queryData['tableName'] = $this->packingTrans;
                // $queryData['where_in']['com_pck_status'] = [0,1];
                // $queryData['where']['packing_id'] = $packingData->ref_by;
                // $checkPackingStatus = $this->numRows($queryData);
                // if($checkPackingStatus > 0):
                //     $this->edit($this->packingMaster,['id'=>$packingData->ref_by],['packing_status'=>0]);
                // endif;
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
    
    public function getCustomPackingItemsGroupByBox($id){
        $queryData = array();
        $queryData['tableName'] = $this->transChild;
        $queryData['select'] = "trans_child.*,item_master.price as so_price,export_packing.status as packing_status,LPAD(export_packing.package_no, 2, '0') as package_no,item_master.part_no as PartNo,item_master.drawing_no as DrgNo,item_master.rev_no as RevNo,som.currency,export_packing.wooden_size";
        $queryData['leftJoin']['export_packing'] = "export_packing.id = trans_child.ref_id";
        $queryData['leftJoin']['trans_child as soi'] = "export_packing.comm_pack_id = soi.id";
        $queryData['leftJoin']['trans_main as som'] = "export_packing.so_id = som.id";
        $queryData['leftJoin']['item_master'] = "item_master.id = trans_child.item_id";
        $queryData['where']['trans_child.trans_main_id'] = $id;
        $queryData['group_by'][]="trans_child.item_id";
        $queryData['group_by'][]="export_packing.package_no";
        $queryData['group_by'][]="export_packing.wooden_size";
        $result = $this->rows($queryData);
        return $result;
    }
}
?>