<div class="col-md-4 offset-md-4" style="padding-top: 100px;padding-bottom: 50px;">
    <div class="card animate-modal" style="text-align: center;border: 1px solid #deff;">
        <div class="card-body"> <i class="fa fa-warning" aria-hidden="true" style="font-size: 82px;margin-left: -11px;margin-bottom: 21px;color: #da8089;"></i>
            <h2 style="color: #575757; font-size: 30px; font-weight: 600; letter-spacing: 1px;">Something went wrong !</h2>
            <?php
            if($try_again){
            ?>
            <a href="<?php echo base_url(); ?>policyproposal/fullQuote/<?php echo isset($lead_id) ? $lead_id : ''; ?>"><button class="btn mt-4 cnl-btn">Try Again</button></a>
            <?php
            }
            ?>
        </div>
    </div>
</div>
</div>