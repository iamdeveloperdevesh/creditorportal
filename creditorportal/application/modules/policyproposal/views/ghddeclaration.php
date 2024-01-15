<?php if ($members) : ?>
    <form action="#">
        <table class="table table-bordered table-responsive table-ghd-declaration ghd-<?=$customer_id;?>">
            <thead>
                <tr class="bg-light">
                    <th scope="col" colspan="4">Do you and/or any other proposed member ever been diagnosed with or had signs/symptions or advised/taken treatment or surgery for any of the following </th>
                    <?php foreach ($members as $member) : ?>
                        <th scope="col"><?php echo $member->member_type ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php

                    $is_answer_available = false;
                ?>
                <?php foreach ($questions as $question) : ?>
                    <tr>
                        <td colspan="4"><?php echo $question->question ?></td>
                        <?php $i = 0;
                            foreach ($members as $member) : ?>
                            <?php
                            $member_type = $member->member_type;
                            $member_id = $member->member_id;
                            $agree_radio_id = 'agree-question-question' . $question->id . '-member-' . $member_type . "-" . $customer_id. "-" .$i;
                            $disagree_radio_id = 'disagree-question-question' . $question->id . '-member-' . $member_type . "-" . $customer_id. "-" .$i;
                            $checked = false;

                            $original_answer = 1;

                            if (!empty($answers)) {
                                $current_answer = array_filter($answers, function ($answer) use ($question, $member) {
                                    return $answer->question_id == $question->id && $answer->member_id == $member->member_id;
                                });

                                $original_answer = reset($current_answer)->answer;

                                $is_answer_available = true;
                            }
                            ?>
                            <td>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="<?php echo $agree_radio_id ?>" name="answers[<?php echo $question->id ?>][<?php echo $member_id ?>]" class="custom-control-input" <?php if ($original_answer) : ?>checked<?php endif; ?> value="1">
                                    <label class="custom-control-label" for="<?php echo $agree_radio_id ?>">Agree</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="<?php echo $disagree_radio_id ?>" name="answers[<?php echo $question->id ?>][<?php echo $member_id ?>]" class="custom-control-input" <?php if (!$original_answer) : ?>checked<?php endif; ?> value="0">
                                    <label class="custom-control-label" for="<?php echo $disagree_radio_id ?>">Disagree</label>
                                </div>
                            </td>
                        <?php $i++;
                        endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </form>
    <?php if ($mode == 'preview') { ?>
        <script>
            $(".table-ghd-declaration :input").prop("disabled", true);
        </script>
    <?php }
        else if(!$is_answer_available) { ?>

            <script>
                $(document).ready(function(){

                    $(".ghd-<?=$customer_id;?>").closest('.according').find('.health-declaration-accordion').addClass('no-collapsable');
                });
            </script>

    <?php } ?>
<?php else : ?>
    <div class="alert alert-warning fade show ghd-message-<?=$customer_id;?>" role="alert">
        <strong>Please fill the complete proposal first!</strong>
        <script>
            $(document).ready(function(){

                $(".ghd-message-<?=$customer_id;?>").closest('.according').find('.health-declaration-accordion').addClass('no-collapsable');
            });
        </script>
    </div>
<?php endif; ?>