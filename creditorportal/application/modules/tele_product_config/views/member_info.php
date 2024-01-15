<?php
//$members = $datalist->family_construct ?? null;
//var_dump($members);exit;
//echo $max_member_count;die;
?>
    <div class="row col-md-12">
        <div class="col-md-4 mb-3">
            <label for="validationCustomUsername" class="col-form-label">Policy Member Count</label>
            <div class="input-group">
                <input type="number" name="membercount" class="form-control membercount" min="0" value="0" />
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
                <div class="col-md-4">
                    <div class="row m-0">
                        <div class="col">
                            <input type="number" class="form-control agefield" min="0" max="100" placeholder="Min Age" name="minage[]">
                        </div>
                        <input type="hidden" name="min_age_type[]" value="years">
                        <div class="col">
                            <input type="number" class="form-control agefield" min="0" max="100" placeholder="Max Age" name="maxage[]">
                        </div>
                        <button type="button" class="ml-2 mt-2 delete-member-btn" style="border: none; background: none;">
                            <i class="fa fa-trash"></i>
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
        <?php
        //var_dump($child_members);die
        $child_members = $child_members;
        ?>
        <?php foreach ($child_members as $key => $member) : ?>
            <div class="row children-row">
                <div class="col-md-4">
                    <div class="form-group">
                        <select name="member[]" id="" class="form-control" readonly aria-readonly="" disabled>
                            <option value="<?php echo $member->fr_id ?>"><?php echo $member->fr_name ?></option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row m-0">
                        <div class="col">
                            <input type="number" class="form-control agefield" min="0" placeholder="Min Age" name="minage[]" disabled>
                        </div>
                        <div class="col">
                            <select name="min_age_type[]" class="form-control agefield" disabled>
                                <option value="years">Years</option>
                                <option value="days">Days</option>
                            </select>
                        </div>
                        <div class="col">
                            <input type="number" class="form-control agefield" min="0" max="100" placeholder="Max Age" name="maxage[]" disabled>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php if ($members) : ?>
    <script>
        var adult_members = JSON.parse('<?php echo json_encode($adult_members); ?>');
        $(document).ready(function() {
          //  console.log(adult_members);die;
            $("[name='membercount']").val(0);

        });

        function populateOptionsForEmptyAdults() {
         
            let selected_members = [];

            $('.adult-row .adult-member-select').each(function (index, element) {
                let value = $(this).val();
                if (selected_members.indexOf(value) === -1) {
                    selected_members.push(value);
                }
            });

            let unselected_adult_members = adult_members.filter(function (member) {
                return selected_members.includes(member.id) == false;
            });

            $('.adult-row .adult-member-select').each(function (index, element) {

                let option_length = $(this).find('option').length;

                if (option_length != 0) {
                    return;
                }

                let options_html = "";
console.log(unselected_adult_members);
                if (unselected_adult_members.length <= 0) {
                    $('.adult-row').last().remove();
                    displayMsg('error', 'No more member types available');
                }
                unselected_adult_members.forEach(member => {
                    options_html += `<option value="${member.id}">${member.member_type}</option>`;
                });

                $(this).html(options_html);
            });
        }
        function generateMemberSelects(members) {
console.log(123);
            //members=  JSON.parse(members);
            members= members;
            console.log(members);
            let adult_member_ids = [];
            adult_members.forEach(member => {
                adult_member_ids.push(member.fr_id);
            });

            let selected_adult_members = (members).filter(function(member) {
                return adult_member_ids.includes(member.fr_id);
            });

            let selected_child_members = (members).filter(function(member) {
                return !adult_member_ids.includes(member.fr_id);
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
            console.log(selected_adult_members);
            selected_adult_members.forEach((member, index, members) => {
                let member_id = member.member_type_id;
                populateOptionsForEmptyAdults();

                $('.adult-row .adult-member-select').last().val(member_id);
                $('.adult-row [name="minage[]"]').last().val(member.member_min_age);
                $('.adult-row [name="maxage[]"]').last().val(member.member_max_age);
                $('.adult-row [name="member[]"]').last().val(member.fr_id);
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
                    $(this).closest('.children-row').find('[name="member[]"]').val(member.fr_id);
                })
            });

            updateCounters();
        }
        function updateCounters() {
            let adult_rows = $('.adult-row').length;
            $('#adult-count-display').val(adult_rows);

            let member_count = parseInt($('[name="membercount"]').val());
            let child_count = member_count - adult_rows;
            $('#kids-count-display').val(child_count);
        }
        function enableInnerFields(element_id) {
            $('#' + element_id + ' :input').prop('disabled', false);
        }
        function disableInnerFields(element_id) {
            $('#' + element_id + ' :input').prop('disabled', true);
        }
    </script>
<?php endif; ?>