<?php
$members = $datalist->family_construct ?? null;
?>
<style type="text/css">
    #max_mi_display .error{position: unset !important;}
</style>
<div class="row col-md-12">
    <div class="col-md-4 mb-3">
        <label for="validationCustomUsername" class="col-form-label">Policy Member Count<span class="lbl-star">*</span></label>
        <div class="input-group">
            <input type="number" placeholder="Enter Policy Member Count" id="membercount" name="membercount" class="form-control membercount" min="1" value="" maxlength="2" />
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="row">
            <div class="col">
                <label for="adult-count-display" class="col-form-label">Adult Count</label>
                <input type="number" class="form-control" name = "adult_count" id="adult-count-display" readonly value="0" />
            </div>
            <div class="col">
                <label for="kids-count-display" class="col-form-label">Child Count</label>
                <input type="number" class="form-control" name = "child_count" id="kids-count-display" readonly value="0" />
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3" id="max_mi_display">
        <div class="row">
            <div class="col">
                <label for="max_mi" class="col-form-label">Maximum Insured Member Count</label>
                <input type="number" class="form-control" placeholder="Enter Maximum Insured Member Count " name = "max_mi" id="max_mi" value="" min="0" max="0" />
            </div>
        </div>
    </div>
</div>

<div id="adult-members-list" class="col-md-12" style="display: none;">
    <div class="adultmembers">
        <h4 class="col-form-label">Adult Members</h4>
        <div class="row adult-row">
            <div class="col-md-4">
                <div class="form-group">
                    <select name="member[]" class="form-control adult-member-select">
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group m-0">
                    <input type="number" class="form-control agefield minfield" min="18" max="100" placeholder="Min Age" name="minage[]" id="memmin<?php echo rand(100,100000);?>" >
                    <input type="hidden" name="min_age_type[]" value="years">
                    <input type="number" class="form-control agefield maxfield" min="18" max="100" placeholder="Max Age" name="maxage[]" maxage="true" id="mem<?php echo rand(100,100000);?>">
                    <button type="button" class="ml-2 mt-2 delete-member-btn">
                        <span class="material-icons">
                            delete
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <button type="button" class="btn btn-info btn-sm ml-2" id="add-another-member">Add Another Adult <i class="ti-plus add-plus1"></i></button>
        </div>
    </div>
</div>

<div id="children-members-list" class="mt-3 col-md-12" style="display: none;">
    <h4 class="col-form-label">Children</h4>
    <div class="col-md-4" style="display:flex;">
        <div class="form-check-inline" style=" margin-top: 6px;">
            <input type="checkbox" class="form-check-input" name="adult_consider" value="1" id="adult_consider" <?php if($datalist->policydetails[0]->is_consider_adult ==1){ echo 'checked';}?>>
            <label class="form-check-label" for="adult_consider"> Is consider as adult </label>

        </div>
    </div>
    <?php
    $child_members = $datalist->child_members;
    ?>
    <?php foreach ($child_members as $key => $member) : ?>
        <div class="row children-row">
            <div class="col-md-4">
                <div class="form-group">
                    <select name="member[]" id="" class="form-control" readonly aria-readonly="" disabled>
                        <option value="<?php echo $member->id ?>"><?php echo $member->member_type ?></option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group m-0">
                    <input type="number" class="form-control agefield minfield" min="1" placeholder="Min Age" name="minage[]" disabled>
                    <select name="min_age_type[]" class="form-control agefield min_age_type" disabled>
                        <option value="years">Years</option>
                        <option value="days">Days</option>
                    </select>
                    <input type="number" class="form-control agefield maxfield" min="1" max="100" placeholder="Max Age" name="maxage[]" disabled maxage="true" id="mem<?php echo $member->id ?>">
                </div>
            </div>
           
        </div>
    <?php endforeach; ?>
</div>
<?php if ($members) : ?>
    <script>
        $(document).ready(function() {
            $("[name='membercount']").val(<?php echo $datalist->policydetails[0]->max_member_count ?>);
            $("[name='max_mi']").val(<?php echo $datalist->policydetails[0]->max_insured_count ?>);
            $("[name='max_mi']").attr('max',<?php echo $datalist->policydetails[0]->max_member_count ?>);
            generateMemberSelects(JSON.parse('<?php echo json_encode($members); ?>'))
        });

        function generateMemberSelects(members) {
            let adult_member_ids = [];
            adult_members.forEach(member => {
                adult_member_ids.push(member.id);
            });

            let selected_adult_members = members.filter(function(member) {
                return adult_member_ids.includes(member.member_type_id);
            });

            let selected_child_members = members.filter(function(member) {
                return !adult_member_ids.includes(member.member_type_id);
            });

            if (selected_adult_members.length > 0) {
                $("#adult-members-list").show();
                if (selected_adult_members.length == members.length) {
                    $("#add-another-member").hide();
                }
            }

            if (selected_child_members.length > 0) {
                $("#children-members-list").show();
                enableInnerFields('children-members-list');
            }
            let adult_row_html = $('.adultmembers .adult-row').html();
            selected_adult_members.forEach((member, index, members) => {
                let member_id = member.member_type_id;
                populateOptionsForEmptyAdults();
                $('.adult-row .adult-member-select').last().val(member_id);
                $('.adult-row [name="minage[]"]').last().val(member.member_min_age);
                $('.adult-row [name="maxage[]"]').last().val(member.member_max_age);
                $('.adult-row [name="maxage[]"]').last().attr('id','mem'+member_id)
                if (index !== (members.length - 1)) {
                    $('.adultmembers').append(`<div class="row adult-row">${adult_row_html}</div>`);
                }
            });

            selected_child_members.forEach(member => {
                let member_id = member.member_type_id;
                $('#children-members-list select').each(function(index, element) {
                    let selected_member_id = $(this).val();
                    if (selected_member_id != member_id) {
                        return;
                    }

                    if (member.member_min_age) {
                        $(this).closest('.children-row').find('[name="minage[]"]').val(member.member_min_age);
                    } else {
                        $(this).closest('.children-row').find('[name="minage[]"]').val(member.member_min_age_days);
                        $(this).closest('.children-row').find('[name="min_age_type[]"]').val('days');

                    }
                    $(this).closest('.children-row').find('[name="maxage[]"]').val(member.member_max_age);
                    $(this).closest('.children-row').find('[name="maxage[]"]').attr('id','mem'+member_id)
                })
            });

            updateCounters();
        }
    </script>
<?php endif; ?>