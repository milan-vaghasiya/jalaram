<?php
class PurchaseOrder extends MY_Controller
{
    private $po_index = "app/po_index";

    public function __construct(){
        parent::__construct();
		$this->data['headData']->pageTitle = "PurchaseOrder";
		$this->data['headData']->controller = "app/purchaseOrder";    
    }

    public function index(){
		$this->data['headData']->appMenu = "app/purchaseOrder";
        $this->data['orderList'] = $this->getPurchaseOrderData(['status'=>0]);
        $this->load->view($this->po_index,$this->data);
    }

    public function getPurchaseOrderData($parameter = []){
        $postData = !empty($parameter)?$parameter :  $this->input->post();
        $OrderList = $this->purchaseOrder->getStatusWisePurchaseOrderData(['status'=>$postData['status']]);
        $html="";
		if(!empty($OrderList))
		{
			foreach($OrderList as $row)
			{
				$userImg = base_url('assets/images/users/user_default.png');    

				$html .= '<li class=" grid_item listItem item transition position-static " data-category="transition">
                                    <a href="javascript:void(0)">
                                        <div class="media-content">
                                            <div>
                                                <h6 class="name">'.(!empty($row->party_name) ? $row->party_name : '').'</h6>
                                                <p class="my-1"> '.getPrefixNumber($row->po_prefix,$row->po_no).'</p>
                                                <p class="my-1"><i class="far fa-clock"></i> '.date("d, M Y", strtotime($row->po_date)).(!empty($row->delivery_date) ? ' | <i class="far fa-clock"></i> '.date("d, M Y", strtotime($row->delivery_date)) : '').'</p>
                                            </div>
                                        </div>
										<div class="left-content w-auto">';                                        
                                            if(empty($row->is_approve)):
                                                $html .= '<a href="javascript:void(0)" class="permission-approve approvePO" data-id="'.$row->order_id.'" data-val="1" data-msg="Approve" datatip="Approve Order" flow="down"><i class="fas fa-check"></i></a>';
                                            endif;
                                            
                                            $html .= '<br><a class="permission-read" href="'.base_url('purchaseOrder/printPO/'.$row->order_id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print"></i></a>';
                                        $html .= '</div>                                        
                                    </a>
                                </li>';
			}
		}
		if(empty($parameter)){$this->printJson(['html'=>$html]);}
		else{return $html;}
    }

    public function approvePOrder(){
		$data = $this->input->post();
		
		if(empty($data['id'])):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
            $result = $this->purchaseOrder->approvePOrder($data);
			$result['html'] = $this->getPurchaseOrderData(['status'=>0]);			
			$this->printJson($result);
		endif;
	}
}
?>