<?php
class MergeItem extends MY_Controller{
    private $indexPage = "merge_item/index";
    private $mergeForm = "merge_item/form";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Merge Item";
		$this->data['headData']->controller = "mergeItem";
	}
	
	public function index(){
        $this->data['tableHeader'] = getConfigDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $result = $this->mergeModel->getDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getMergeItemData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addMergeItem(){
        $this->data['fromItemType'] = $this->data['toItemType'] = $this->item->getItemGroup();
        $this->load->view($this->mergeForm,$this->data);
    }

    public function getItemListForSelect(){
		$data = $this->input->post();
        $result = $this->item->getItemListForSelect($data['item_type']);
		$options="";
		if(!empty($result)): 
			$options .= '<option value="">'.$data['option_label'].'</option>';
			foreach($result as $row):
				$itmStock = $this->store->getItemStockRTD($row->id,$row->item_type);
				$row->qty = 0;
				if(!empty($itmStock->qty)){ $row->qty = $itmStock->qty;}
			
				$selected = (!empty($item_id) && $item_id == $row->id)?'selected':'';
				
				$item_name = (!empty($row->item_code)) ? "[".$row->item_code."] ".$row->item_name : $row->item_name;
				$options .= "<option data-row='".json_encode($row)."' value='".$row->id."' ".$selected.">".$item_name." | Stock Qty: ".$row->qty."</option>";
			endforeach;
		else:
			$options .= '<option value="">'.$data['option_label'].'</option>';
		endif;
		
		$this->printJson(['status'=>1, 'options'=>$options]);
	}

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['item_type']))
            $errorMessage['item_type'] = "Item Type is required.";
        if(empty($data['from_item']))
            $errorMessage['from_item'] = "From Item is required.";
        if(empty($data['to_item']))
            $errorMessage['to_item'] = "To Item is required.";
        if(!empty($data['to_item']) && !empty($data['from_item'])):
            if($data['to_item'] == $data['from_item']):
                $errorMessage['to_item'] = "Invalid Item.";
            endif;
        endif;

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:   
            $this->printJson($this->mergeModel->save($data));
        endif;
    }
}
?>