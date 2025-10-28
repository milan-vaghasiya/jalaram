<?php
class Ledger extends MY_Controller
{
    private $indexPage = "ledger/index";
    private $ledgerForm = "ledger/form";
    private $opbal_index = "ledger/opbal_index";
    private $gstPercentage = Array(["rate"=>0,"val"=>'NIL'],["rate"=>0.25,"val"=>'0.25%'],["rate"=>3,"val"=>'3%'],["rate"=>5,"val"=>'5%'],["rate"=>12,"val"=>'12%'],["rate"=>18,"val"=>'18%'],["rate"=>28,"val"=>'28%']);
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Ledger";
		$this->data['headData']->controller = "ledger";		
	}
	
	public function index(){
        $this->data['tableHeader'] = getAccountDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }
	
    public function getDTRows(){
        $result = $this->ledger->getDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getLedgerData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }
    
    public function addLedger(){
        $this->data['gstPercentage'] = $this->gstPercentage;
		$this->data['hsnData'] = $this->hsnModel->getHsnList();
        $this->data['grpData'] = $this->group->getGroupListOnGroupCode("group_code NOT IN ('SD','SC')");
        $this->load->view($this->ledgerForm,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['party_name']))
            $errorMessage['party_name'] = "Ledger name is required.";
        if(empty($data['group_id']))
            $errorMessage['group_id'] = "Group Name is required.";

        if(!empty($data['is_gst_applicable'])):
            if(empty($data['gst_per']))
                $errorMessage['gst_per'] = "Gst Percentage Name is required.";
            if(empty($data['cess_per'])) 
                $errorMessage['cess_per'] = "Cess Percentage is required.";
            if(empty($data['hsn_code'])) 
                $errorMessage['hsn_code'] = "Hsn code is required.";
        endif;
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->ledger->save($data));
        endif;
    }

    public function edit(){
        $data = $this->input->post();
        $this->data['dataRow'] = $this->ledger->getLedger($data['id']);
        $this->data['gstPercentage'] = $this->gstPercentage;
        $this->data['grpData'] = $this->group->getGroupList();
		$this->data['hsnData'] = $this->hsnModel->getHsnList();
        $this->load->view($this->ledgerForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->ledger->delete($id));
        endif;
    }
    
    // Created By Meghavi @20/09/2022
    public function opBalIndex(){
        $this->data['grpData'] = $this->group->getGroupList();
        $this->load->view($this->opbal_index,$this->data);
    }

    //Created By Meghavi @20/09/2022
    //updated by milan @18/07/2023
    public function getGroupWiseLedger(){
        $data = $this->input->post();
        $toDate = date('Y-m-d',strtotime($this->startYearDate.' -1 day'));
        $ledgerData = $this->accountingReport->getLedgerSummary("1970-01-01",$toDate,[],$data['group_id']);
        
        //$ledgerData = $this->ledger->getGroupWiseLedger($data); 
        $tbody="";$i=1;
        if(!empty($ledgerData)):
            foreach($ledgerData as $row):          
                $row->opb = $row->cl_balance;
                $crSelected = (!empty($row->balance_type) && $row->balance_type == "1")?"selected":"";
                $drSelected = (!empty($row->balance_type) && $row->balance_type == "-1")?"selected":"";

                $row->opbalinput = '<div class="input-group">
                    <select name="balance_type[]" id="balance_type_'.$row->id.'" class="form-control" style="width: 20%;">
                        <option value="1" '.$crSelected.'>CR</option>
                        <option value="-1" '.$drSelected.'>DR</option>
                    </select>
                    <input type="text" id="opening_balance_'.$row->id.'" name="opening_balance[]" class="form-control floatOnly" value="'.floatVal(abs($row->opb)).'" style="width: 60%;" />
                    <button type="button" class="btn btn-success saveOp" datatip="Save" flow="down" data-id="'.$row->id.'" style="width: 10%;"><i class="fa fa-check"></i></button>
                </div>
                <input type = "hidden"  id="id_'.$row->id.'" name="id[]" value="'.$row->id.'" >' ;

                $tbody .= '<tr>
                    <td style="width: 5%;">'.$i++.'</td>
                    <td style="width: 10%;">'.$row->account_code.'</td>
                    <td style="width: 25%;">'.$row->account_name.'</td>
                    <td class="text-right" style="width: 15%;" id="cur_op_'.$row->id.'">'.$row->opb.'</td>
                    <td style="width: 35%;">' .$row->opbalinput. '</td>';
                $tbody.='</tr>';
            endforeach;
        else:
            $tbody .= '<tr><td class="text-center" colspan="5">No data available in table</td></tr>';
        endif;
        $this->printJson(['status'=>1, 'count'=>$i, 'tbody'=>$tbody]);
    }

    public function saveOpeningBalance(){
        $data = $this->input->post();
        $this->printJson($this->ledger->saveOpeningBalance($data));
    }

    //Created By Meghavi @20/09/2022
    public function saveBulkOpeningBalance(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data))
            $errorMessage['op_data_error'] = "Ledger Opening is required.";

        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->ledger->saveBulkOpeningBalance($data));
        endif;
    }
}
?>