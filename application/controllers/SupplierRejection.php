<?php
class SupplierRejection extends MY_Controller{
    private $index = "supplier_rejection/index";
    private $form = "supplier_rejection/form";

    public function __construct(){
        parent::__construct();
        $this->data['headData']->pageTitle = "Supplier Rejection";
		$this->data['headData']->controller = "supplierRejection";
		$this->data['headData']->pageUrl = "supplierRejection";
    }

    public function index(){
        $this->load->view($this->index,$this->data);
    }

    public function getItemList(){
        $item_type = $this->input->post('item_type');
        $itemData = $this->item->getItemList($item_type);

        $itemOption = '<option value="">Select Item</option>';
        foreach($itemData as $row):
            $itemOption .= '<option value="'.$row->id.'">[ '.$row->item_code.' ] '.$row->item_name.'</option>';
        endforeach;

        $this->printJson(['status'=>1,'itemOption'=>$itemOption]);
    }

    public function getItemStock(){
        $data = $this->input->post();
        $stockData = $this->supplierRejection->getItemStockOnGrn($data);

        $rejectData = $this->supplierRejection->getPurchaseInspectData(['insp_status' => 'Not Ok', 'item_id'=>$data['item_id']]);

        $tbody = '';$i=1;
        foreach($stockData as $row):
            $editParam = "{'item_id' : ".$row->item_id.", 'party_id':'".$row->party_id."','batch_no': '".$row->batch_no."','location_id' : '".$row->location_id."','stock_qty':".$row->stock_qty.", 'modal_id' : 'modal-lg', 'form_id' : 'supplierRejection', 'title' : 'Supplier Rejection', 'fnsave' : 'save', 'fnEdit':'addRejection','button':'close'}";

            $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Rejection" flow="right" onclick="customEdit('.$editParam.');"><i class="ti-close" ></i></a>';

            $action = getActionButton($editButton);
			$po_no = (!empty($row->po_no))?getPrefixNumber($row->po_prefix,$row->po_no) : '';
            $tbody .= '<tr>
                <td>'.$action.'</td>
                <td>'.$i++.'</td>
                <td>'.getPrefixNumber($row->grn_prefix,$row->grn_no).'</td>
                <td>'.formatDate($row->grn_date).'</td>
                <td>'.$row->party_name.'</td>
                <td>'.$row->challan_no.'</td>
                <td>'.$po_no.'</td>
                <td>'.$row->store_name.'</td>
                <td>'.$row->location.'</td>
                <td>'.$row->batch_no.'</td>
                <td>'.$row->stock_qty.'</td>
            </tr>';
        endforeach;
        
        foreach($rejectData as $row):
            $editParam = "{'id':'', 'item_id' : ".$row->item_id.", 'party_id':'".$row->party_id."','batch_no': '".$row->batch_no."','location_id' : '".$row->location_id."','qty':".$row->reject_qty.", 'pur_insp_id':'".$row->pur_insp_id."'}";

            $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Rejection" flow="right" onclick="rejectStockEffect('.$editParam.');"><i class="ti-close" ></i></a>';

            $action = getActionButton($editButton);
			$po_no = (!empty($row->po_no))?getPrefixNumber($row->po_prefix,$row->po_no) : '';
            $tbody .= '<tr>
                <td>'.$action.'</td>
                <td>'.$i++.'</td>
                <td>'.getPrefixNumber($row->grn_prefix,$row->grn_no).'</td>
                <td>'.formatDate($row->grn_date).'</td>
                <td>'.$row->party_name.'</td>
                <td>'.$row->challan_no.'</td>
                <td>'.$po_no.'</td>
                <td>'.$row->store_name.'</td>
                <td>'.$row->location.'</td>
                <td>'.$row->batch_no.'</td>
                <td>'.$row->reject_qty.'</td>
            </tr>';
        endforeach;

        $this->printJson(['status'=>1,'tbody'=>$tbody]);
    }

    public function addRejection(){
        $data = $this->input->post();

        $postData['stock_required'] = 1;
        $postData['item_id'] = $data['item_id'];
        $postData['location_id'] = $this->SUP_REJ_STORE->id;
        $postData['batch_no'] = $data['batch_no'];
        $postData['trans_type'] = 1;
        $postData['ref_type'] = 34;

        $this->data['dataRow'] = (object) $data;
        $this->data['stockTransData'] = $this->getItemStockTransHtml($postData);
        $this->load->view($this->form,$this->data);
    }

    public function getItemStockTransHtml($postData){
        $stockTransData = $this->store->getItemStockTransactions($postData);
        $html = '';
        if(!empty($stockTransData)):
            $i=1;
            foreach($stockTransData as $row):
                $deleteBtn = '<a href="javascript:void(0)" class="btn btn-outline-danger btn-delete permission-remove" onclick="removeTrans('.$row->ref_no.','.floatVal($row->qty).');" datatip="Remove" flow="left"><i class="ti-trash"></i></a>';

                $html .= '<tr>
                    <td class="text-center">'.$i++.'</td>
                    <td class="text-center">'.formatDate($row->ref_date).'</td>
                    <td class="text-center">'.floatVal($row->qty).'</td>
                    <td>'.$row->remark.'</td>
                    <td class="text-center">'.$deleteBtn.'</td>
                </tr>';
            endforeach;
        else:
            $html = '<tr>
                <td class="text-center" colspan="5">No data available in table</td>
            </tr>';
        endif;

        return $html;
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        
        if(empty($data['qty'])):
            $errorMessage['qty'] = "Qty is required.";
        endif;

        if(empty($data['pur_insp_id'])):
            if(!empty($data['qty'])):
                $postData = array();
                $postData['stock_required'] = 1;
                $postData['item_id'] = $data['item_id'];
                $postData['location_id'] = $data['location_id'];
                $postData['batch_no'] = $data['batch_no'];
                $postData['single_row'] = 1;
                $stockData = $this->store->getItemStockBatchWise($postData);
                if($data['qty'] > $stockData->qty):
                    $errorMessage['qty'] = "Stock not avalible.";
                endif;
            endif;
        endif;

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $result = $this->supplierRejection->saveSupplierRejection($data);

            $postData = array();
            $postData['stock_required'] = 1;
            $postData['item_id'] = $data['item_id'];
            $postData['location_id'] = $this->SUP_REJ_STORE->id;
            $postData['batch_no'] = $data['batch_no'];
            $postData['trans_type'] = 1;
            $postData['ref_type'] = 34;

            $result['stockTransData'] = $this->getItemStockTransHtml($postData);
            $this->printJson($result);
        endif;
    }

    public function delete(){
        $data = $this->input->post();
        if(empty($data['id'])):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
            $result = $this->supplierRejection->deleteSupplierRejection($data['id']);

			$postData = array();
            $postData['stock_required'] = 1;
            $postData['item_id'] = $data['item_id'];
            $postData['location_id'] = $this->SUP_REJ_STORE->id;
            $postData['batch_no'] = $data['batch_no'];
            $postData['trans_type'] = 1;
            $postData['ref_type'] = 34;

            $result['stockTransData'] = $this->getItemStockTransHtml($postData);
            $this->printJson($result);
		endif;
    }
}
?>