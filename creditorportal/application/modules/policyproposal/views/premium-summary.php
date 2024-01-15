<?php
    
    if(!empty($data)){
        
        foreach($data as $applicant_type => $applicant_policies){

            ?>
            <table class="table tbl-600 table-bordered">
                <thead>
                    <tr>
                        <th colspan="2"><strong class="head-lbl-1"><?=$applicant_type;?></strong></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach($applicant_policies['policies'] as $policy_name => $policy_amount){ 

                        ?>
                        <tr>
                            <td>
                                <strong><?=$policy_name;?></strong>
                            </td>
                            <td>
                                <span class="fl-right"><i class="fa fa-inr"></i> <?=$policy_amount;?></span>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
            <?php
        }
    }
?>
<script>
    panForm = '<?php echo $pan_form; ?>';
</script>