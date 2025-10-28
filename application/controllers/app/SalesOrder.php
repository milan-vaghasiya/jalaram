<?php
class SalesOrder extends MY_Controller
{
    private $sales_ord_index = "app/sales_ord_index";

    public function __construct(){
        parent::__construct();
		$this->data['headData']->pageTitle = "SalesOrder";
		$this->data['headData']->controller = "app/salesOrder";    
    }

    public function index(){
		$this->data['headData']->appMenu = "app/salesOrder";
        $this->data['salesOrdList'] = $this->getSalesOrderData(['status'=>0]);
        $this->load->view($this->sales_ord_index,$this->data);
    }

    public function getSalesOrderData($parameter = []){
        $postData = !empty($parameter)?$parameter :  $this->input->post();
        $salesOrdList = $this->salesOrder->getStatusWiseSalesOrderData(['status'=>$postData['status']]);
        $html="";
		if(!empty($salesOrdList))
		{
			foreach($salesOrdList as $row)
			{
				$userImg = base_url('assets/images/users/user_default.png');              

				$html .= '<li class=" grid_item listItem item transition position-static " data-category="transition">
                                    <a href="javascript:void(0)">   
                                        <div class="media-content">
                                            <div>
                                                <h6 class="name">'.(!empty($row->party_name) ? $row->party_name : '').'</h6>
                                                <p class="my-1"> '.getPrefixNumber($row->trans_prefix,$row->trans_no).' | <i class="far fa-clock"></i> '.date("d, M Y", strtotime($row->trans_date)).'</p>
                                                <p class="my-1"> '.$row->doc_no.(!empty($row->cod_date) ? ' | <i class="far fa-clock"></i> '.date("d, M Y", strtotime($row->cod_date)) : '').'</p>
                                            </div>
                                        </div>
										<div class="left-content w-auto">';                                        
                                            if(empty($row->is_approve)):
                                                $html .= '<a href="javascript:void(0)" class="permission-approve approveSO" data-id="'.$row->trans_main_id.'" data-val="1" data-msg="Approve" datatip="Approve Order" flow="down"><i class="fas fa-check"></i></a>';
                                            endif;
                                            
                                            $html .= '<br><a class="permission-read" href="'.base_url('salesOrder/salesOrder_pdf/'.$row->trans_main_id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print"></i></a>';
                                        $html .= '</div>                                          
                                    </a>
                                </li>';
			}
		}
		if(empty($parameter)){$this->printJson(['html'=>$html]);}
		else{return $html;}
    }

    public function approveSOrder(){
		$data = $this->input->post();
		
		if(empty($data['id'])):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
            $result = $this->salesOrder->approveSOrder($data);
			$result['html'] = $this->getSalesOrderData(['status'=>0]);			
			$this->printJson($result);
		endif;
	}
}
?>