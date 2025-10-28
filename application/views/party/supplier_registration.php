<style>td{height:30px !important;}</style>
<div class="row">
	<div style="border:1px solid #000000;">
	    <div class="text-center fs-20" style="border-bottom:1px solid #000000;">(To be filled up by SUPPLIER)</div>
	    
        <table class="table" style="margin-top:2px;border-collapse:collapse;margin:2px;">
            <tr class="text-left">
                <td style="width:50%;" height="30">1. Supplier's Name :- <u><?=$partyData->party_name?></u></td>
            </tr>
            <tr class="text-left">
                <td height="2">2. Scope Of Work :- <u><?=$partyData->supplied_types?></u></td>
            </tr>
            <tr class="text-left">
                <td height="2">3. Address (Office):-<u><?=$partyData->party_address?></u></td>
            </tr>
            <tr class="text-left">
                <td height="2">Address (Factory):-<u><?=$partyData->party_address?></td>
            </tr>
            <tr class="text-left">
                <td height="2"> 4.Contact Person:- <u><?=$partyData->contact_person?></u>      Tel. No.<u><?=$partyData->party_mobile?></u>      Fax. No.________________________</td>
            </tr>
            <tr class="text-left">
                <td height="2"> 5.Mobile No.:- ________________________ Email:<u><?=$partyData->party_email?></u></td>
            </tr>
            <tr class="text-left">
                <td height="2">
                    6.Type Of Company :-
                    Partnership: <span style="border:1px solid black;">___</span>
                    Public Limited:	 <span style="border:1px solid black;">___</span>
                    Private Ltd.:  <span style="border:1px solid black;">___</span>
                    Proprietary:  <span style="border:1px solid black;">___</span>
                </td> 	
            </tr>
            <tr class="text-left">
                <td height="2"> 7.Working Shift :-__________________ Working Hrs:-_________________________ Weekly Holiday:-_________________________</td>
            </tr>
            <tr class="text-left">
                <td height="2"> 8.	ISO 9001:2015 Certified company : Yes / No (If Yes, please attached the copy of certificate).</td>
            </tr>
            <tr class="text-left">
                <td height="2"> 9.GST Details :<u><?=$partyData->gstin?></u></td>
            </tr>
            <tr class="text-left">
                <td height="2"> 10.Details Of Machines :</td>
            </tr>
            <tr class="text-left">
                <td height="2">11.	Details Of Measuring Instruments :	</td>
            </tr>
            <tr class="text-left">
                <td height="2"> 12.Details Of Inspection Before Dispatch Of Material :	</td>
            </tr>
            <tr class="text-left">
                <td height="2">Representative's Signature :</td>
            </tr>
            <tr class="text-left">
                <td height="2">Designation :</td>
            </tr>
            <tr class="text-left">
                <td height="2">Date :</td>
            </tr>
        </table>
        
        <div class="text-center fs-20" style="border-bottom:1px solid #000000;border-top:1px solid #000000;">(To be filled up by Jay Jalaram Precision Component LLP)</div>
        
        <table class="table item-list-bb" style="margin:5px;">
            <td>			
                <tr class="text-left">
                    <td height="2"> 1.Follwing points to be reviewed by the organization prior to select the new supplier :</td>
                </tr>
            </td>
        </table>
        
        <table class="table">
            <tr>
                <td>
                    <table class="table item-list-bb">
                        <tr>
                            <th style="width:10%;">Sr.</th><th style="width:40%;">Description</th><th style="width:50%;">Observations</th>
                        </tr>
                        <tr>
                            <td style="width:10%;">1</td><td style="width:40%;">Product Price </td><td style="width:50%;"></td>
                        </tr>
                        <tr>
                            <td style="width:10%;">2</td><td style="width:40%;">Delivery Service </td><td style="width:50%;"></td>
                        </tr>
                        <tr>
                            <td style="width:10%;">3</td><td style="width:40%;">Market Reputation</td><td style="width:50%;"></td>
                        </tr>
                        <tr>
                            <td style="width:10%;">4</td><td style="width:40%;">Ordering system	</td><td style="width:50%;"></td>
                        </tr>
                        <tr>
                            <td style="width:10%;">5</td><td style="width:40%;">Product Quality </td><td style="width:50%;"></td>
                        </tr>
                        <tr>
                            <td style="width:10%;">6</td><td style="width:40%;">Complaint Response </td><td style="width:50%;"></td>
                        </tr>
                        <tr>
                            <td style="width:10%;">7</td><td style="width:40%;">Technical Expertise</td><td style="width:50%;"></td>
                        </tr>
                        <tr>
                            <td style="width:10%;">8</td><td style="width:40%;">Manufacturing Facility </td><td style="width:50%;"></td>
                        </tr>
                        <tr>
                            <td style="width:10%;">9</td><td style="width:40%;">Inspection & Testing Facility</td><td style="width:50%;"></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        
        <table class="table item-list-bb">
            <td>			
                <tr class="text-left">
                    <td height="2">2.Supplier is suitable to our requirements: YES <span style="border:1px solid black;">___</span> NO <span style="border:1px solid black;">___</span></td>
                </tr>
                <tr class="text-left">
                    <td height="2">If Yes, Place the trial order. </td>
                </tr>
                <tr class="text-left">
                    <td height="2">If No, specify the reason :</td>
                </tr>
            </td>
        </table>
        
        <table class="table item-list-bb">
            <thead>
                <tr class="text-left">
                    <th colspan="7">3.Evaluation of trial order :</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th colspan="3">Trial Order details</th>
                    <th colspan="2">Received</th>
                    <th rowspan="2">Result</th>
                    <th rowspan="2">Remark</th>
                </tr>
                <tr>
                    <th>Date</th>
                    <th>Item</th>
                    <th>Qty.</th>
                    <th>Date</th>
                    <th>Qty.</th>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        
        <table class="table item-list-bb">
            <td>			
                <tr class="text-left">
                    <td height="2">4.Allotted Registration Number :</td>
                </tr>
                <tr class="text-left">
                    <td height="2">Comments (If any) :</td>
                </tr>
                <tr class="text-left">
                    <td height="2">5.GST Details : <u><?=$partyData->gstin?></td>
                </tr>
            </td>
        </table>
	</div>
</div>