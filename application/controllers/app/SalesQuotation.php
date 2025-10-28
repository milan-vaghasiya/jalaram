<?php
class SalesQuotation extends MY_Controller
{
    private $sales_quot_index = "app/sales_quot_index";
    private $confirm_quotation = "app/confirm_quotation";

    public function __construct(){
        parent::__construct();
		$this->data['headData']->pageTitle = "SalesQuotation";
		$this->data['headData']->controller = "app/salesQuotation";    
    }

    public function index(){
		$this->data['headData']->appMenu = "app/salesQuotation";
        $this->data['salesQuotList'] = $this->getSalesQuotationData(['status'=>0]);
        $this->load->view($this->sales_quot_index,$this->data);
    }

    public function getSalesQuotationData($parameter = []){
        $postData = !empty($parameter)?$parameter :  $this->input->post();
        $salesQuotList = $this->salesQuotation->getStatusWiseSalesQuotData(['status'=>$postData['status']]);

        $html="";
		if(!empty($salesQuotList))
		{
			foreach($salesQuotList as $row)
			{
				$userImg = base_url('assets/images/users/user_default.png');

                // $confirmParam = "{'postData':{'id':".$row->trans_main_id.",'quote_id':'".$row->trans_main_id."','party':'".$row->party_name."','customer_id':'".$row->party_id."','quote_no':'".getPrefixNumber($row->trans_prefix,$row->trans_no)."','quotation_date':'".date("d-m-Y",strtotime($row->trans_date))."'},'modal_id':'modal-xl','form_id':'movement','title': 'Move To Next Process','button':'close','fnsave':'saveConfirmQuotation','fnedit':'getQuotationItems','btnSave':'other'}";         

				$html .= '<li class=" grid_item listItem item transition position-static " data-category="transition">
                                    <a href="javascript:void(0)">
                                        <div class="media-content">
                                            <div>
                                                <h6 class="name">'.(!empty($row->party_name) ? $row->party_name : '').'</h6>
                                                <p class="my-1"> '.getPrefixNumber($row->trans_prefix,$row->trans_no).' | <i class="far fa-clock"></i> '.date("d, M Y", strtotime($row->trans_date)).'</p>
                                            </div>
                                        </div>
                                        <div class="left-content w-auto">';                                        
                                            if(empty($row->confirm_by)):
                                                $html .= '<a href="javascript:void(0)" class="confirmQuotation permission-write" data-id="'.$row->trans_main_id.'" data-quote_id="'.$row->trans_main_id.'" data-party="'.$row->party_name.'" data-customer_id="'.$row->party_id.'" data-quote_no="'.getPrefixNumber($row->trans_prefix,$row->trans_no).'" data-quotation_date="'.date("d-m-Y",strtotime($row->trans_date)).'" data-button="both" data-modal_id="modal-xl" data-function="getQuotationItems" data-form_title="Confirm Quotation" datatip="Confirm Quotation" flow="down"><i class="fa fa-check"></i></a>';
                                            endif;
                                            
                                            $html .= '<br><a class="permission-read" href="'.base_url('salesQuotation/printQuotation/'.$row->trans_main_id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print"></i></a>';
                                        $html .= '</div>                                          
                                    </a>
                                </li>';
			}
		}
		if(empty($parameter)){$this->printJson(['html'=>$html]);}
		else{return $html;}
    }

    public function getQuotationItems(){
        $quote_id = $this->input->post('quote_id');
        $this->data['quoteItems'] = $this->salesQuotation->getQuotationItems($quote_id,1);
        $this->load->view($this->confirm_quotation,$this->data);
    }

    public function saveConfirmQuotation(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['trans_id'][0])):
            $errorMessage['item_name_error'] = "Please select Items.";
        else:
            foreach($data['confirm_price'] as $key=>$value):
                if(empty($value)):
                    $errorMessage['confirm_price'.$data['trans_id'][$key]] = "Confirm Price is required.";
                endif;
            endforeach;
        endif;

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['confirm_by'] = $this->session->userdata('loginId');
            $result = $this->salesQuotation->saveConfirmQuotation($data);
            $result['html'] = $this->getSalesQuotationData(['status'=>0]);			
			$this->printJson($result);
        endif;
    }
}
?>