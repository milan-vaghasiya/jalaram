<?php
class PrintFormat extends MY_Controller{
    private $indexPage = "print_format/index";
    private $form = "print_format/form";

    private $packingField = [
        'company_name'=>"Company Name",
        'dispatch_date'=>"Dispatch Date",
        'party_name'=>"Customer Name",
        'part_no' => "Customer Ref. No.",
        'item_name'=> 'Item Description',
        'item_alias'=>"Item Alias",        
        'doc_no'=>'Po. Number',
        'inv_no'=>'Inv. No.',
        'lr_no'=>'LR. No.',
        "qty_per_box"=>"Qty Per Box",
        'total_box'=>"Box Qty",
        'lot_qty'=>"Lot Qty",        
        'job_card_no'=>'Job Card No.',
        'gross_wt'=>"Gross Wt(kg)",
        /* 'part_no' => "Part No.",
        'drawing_no' => "Drawing No.",
        'rev_no'=>'Rev. No.', */
        'heat_no'=>'Heat No.',
        'trans_way'=>'Shipment',
        'tag_remark'=>"Remarks"
    ];

    public function __construct(){
        parent::__construct();
        $this->data['headData']->pageTitle = "Print Format";
		$this->data['headData']->controller = "printFormat";
        $this->data['headData']->pageUrl = "printFormat";
    }

    public function index(){
        $this->data['tableHeader'] = getConfigDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $result = $this->printFormat->getDTRows($this->input->post());
        $sendData = array();$i=1;$count=0;
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;         
            $sendData[] = getPrintFormatData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addFormat(){
        $this->data['packingField'] = $this->packingField;
        $this->load->view($this->form,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['format_name']))
            $errorMessage['format_name'] = "Format Name is required.";
        if(empty($data['formate_field']))
            $errorMessage['formate_field'] = "Format field is required.";
        if(empty($data['width']))
            $errorMessage['width'] = "Format width is required.";
        if(empty($data['height']))
            $errorMessage['height'] = "Format height is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['formate_field'] = json_encode($data['formate_field']);
            $data['created_by'] = $this->loginId;
            $this->printJson($this->printFormat->save($data));
        endif;
    }

    public function edit(){     
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->printFormat->getPrintFormat($id);
        $this->data['packingField'] = $this->packingField;
        $this->load->view($this->form, $this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->printFormat->delete($id));
        endif;
    }
}
?>