<?php
$data = [];
if(isset($geProposalPolicyDetails->status_code) && $geProposalPolicyDetails->status_code==200){
    $data = $geProposalPolicyDetails->Metadata->data;
}
?>
<!-- page title area end -->
<div class="main-content-inner" style="min-height: calc(96vh - 100px);">
            <div class="container-fluid">
            <div class="row">
            <div class="col-lg-12 mt-2">
              <div class="card login-card">
                <div class="card-body">
                  <p class="text-center thank-spell mb-5 mt-4">Thank you for choosing Aditya Birla</p>
                  <div class="row sp-10">
                  <div class="col-md-5 col-lg-3">
                     <img src="<?php echo base_url();?>/assets/images/ad-thankyou2.png">
                  </div>
                  <div class="col-md-7 col-lg-9">
                    <div class="lead-head mb-5">
                      <span class="id-lead-p">Your Lead ID: <span class="id-span"><?php echo isset($lead_id) ? $lead_id : '';?></span></span>
                    </div>
                    <div class="coi-table mt-3">
                         <div class="single-table">
                                    <div class="table-responsive">
                                        <table class="table table-bordered text-center">
                                            <thead class="table-coi-tr">
                                                <tr>
                                                    <th scope="col">Cover</th>
                                                    <th scope="col">Propsoal</th>
                                                    <th scope="col">Member</th>
                                                    <th scope="col">COI #</th>
                                                    <th scope="col">Download COI</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php

                                                if(!empty($data)){
                                                    $policy_members = (array) $data->policy_members;
                                                    foreach($data as $key => $value){
                                                        if(empty($value->proposal_no)){
                                                            continue;
                                                        }
                                                        $members = isset($policy_members[$value->proposal_policy_id]) ? $policy_members[$value->proposal_policy_id] : [];
                                                    ?>
                                                    <tr>
                                                        <th scope="row"><?php echo isset($value->policy_number) ? $value->policy_number : '';?></th>
                                                        <td><?php echo isset($value->proposal_no) ? $value->proposal_no : '';?></td>
                                                        <td><?php echo implode(", ",$members) ;?></td>
                                                        <td><?php echo isset($value->certificate_number) ? $value->certificate_number : '';?></td>
                                                        <td><a target="blank" href="<?php echo isset($value->letter_url) ? $value->letter_url : '';?>"><button class="btn-down-ld btn">COI <i class="ti-file" style="font-weight: bold;"></i></button></a></td>
                                                    </tr>
                                                    <?php }
                                                }?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                    </div>
                  </div>
                  </div>
                </div>
              </div>
            </div>
            </div>
            </div>
        </div>
        <!-- main content area end -->