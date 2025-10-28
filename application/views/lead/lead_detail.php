<div class="row">
    <div class="col-md-12">
        <div class="cd-header">
            <div class="media">
				<div class="party_image_view">
				     <img src="<?=base_url()?>assets/uploads/party/user_default.png" alt="user" class="rounded-circle thumb-sm party_image">
                </div>
                <div class="media-body" >
                    <div class="row">
                        <h6 class="m-0"><?= $partyData->party_name ?></h6>
                        <p class="mb-0 lastSeen">Welcomes You</p>
                    </div>
                </div>
            </div>
            <hr>
        </div>
        <div class="cd-body">
            <div class="cd-detail slimscroll">
			    <div class="activity">
                    <?php echo $salesLog; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
// $(document).ready(function(){
//     $(document).on('click','.partyData',function(){
//         var id = $(this).data('id');
//         var lead_id = $(this).data('lead_id');
//         $("#lead_id").val(lead_id);console.log(lead_id);
//         $("#id").val(id);

//         $.ajax({
//             url: base_url + controller + '/getLeadDetails', // 28-03-2024
//             data: { lead_id:lead_id ,id:id},
//             global:false,
//             type: "POST",
//             dataType:"json",
//         }).done(function(data){
//             $(".partyName").html(data.partyData.party_name);
//             $(".salesLog").html(data.salesLog);
//             // if(data.partyData.executive_id == 0){
//             //     $(".salesOption").hide();
//             // }else{
//                 $(".salesOption").show();
//             // }
//             $(".cd-features").removeClass("visually-hidden");
//             $(".cd-footer").removeClass("visually-hidden");
// 			scrollBottom();
//         });
//     });
// });
</script>