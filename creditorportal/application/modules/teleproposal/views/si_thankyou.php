<?php
if(!empty($data))
{
    //print_pre($data);
    if($data['create_proposal'])
    {

        $proposals=$data[0]['proposal_no'];
    }
    else
    {
        $lead_id = $data[0]['lead_id'];
        $proposal_no = array();
        foreach($data  as $proposal){
            $lead_id = $proposal['lead_id'];
            $proposal_no[] = $proposal['proposal_no'];
        }
        if(is_array($proposal_no))
        {
            $proposals=implode(",",$proposal_no);

        }
        else
        {
            $proposals=$proposal['proposal_no'];
        }
    }


}


?>

<div class="horizontal-main-wrapper" style="overflow:hidden;">
    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-xl-12">
            <div class="card mb-3 mt-4">
                <div class="card-body" style="background: #fff;text-align: center;">
                    <h1 class="">Thankyou for choosing Aditya Birla </h1>
                    <p style="font-size: 17px;"> <span>Your Proposal Number is <span> <span style="color:#da8085;"><?php echo $proposals;?></span><br><?php if($lead_id !=NULL && $lead_id !=''){ ?>Lead Number is<span style="color:#da8085;"> <?php echo $lead_id;?></span></p></br><?php }?>

                    <?php if(isset($lead_id)){?>
                        <div class="row">
                            <p class="col-md-12 text-center mt-3"><span>“Proposal details authentication bitly link has been sent to customer mobile number and email id ”,<br> Thank you.</span></p>
                        </div>
                    <?php } ?>
                    <p><img src="/public/assets/images/new-icons/ad-thankyou.png"></p>
                    <!-- <p style="font-size: 20px;">Your Proposal Already existed </p> -->
                </div>
            </div>
        </div>
        <div class="col-md-1"></div>
    </div>
</div>
</div>