
$(document).on('change', '.membercount', function () {
    let member_count = $(this).val();
    let current_adult_row_count = $("#adult-members-list .adult-row").length;

    if (current_adult_row_count > member_count) {
        $("#adult-members-list .adult-row:gt(" + (member_count - 1) + ")").remove();
    }

    if (!parseInt(member_count)) {
        $("#adult-members-list").hide();
        $('#children-members-list').hide();
        disableInnerFields('children-members-list');
        return;
    }
    $("#adult-members-list").show();
    populateOptionsForEmptyAdults();
    if (canAddMoreAdultMembers()) {
        $('#add-another-member').show();
        $('#children-members-list').show();
        enableInnerFields('children-members-list');
    } else {
        $('#add-another-member').hide();
        $('#children-members-list').hide();
        disableInnerFields('children-members-list');
    }
    updateCounters();
})
$(document).on('click', '#add-another-member', function () {
    let adult_row_html = $('.adultmembers .adult-row').html();
    $('.adultmembers').append(`<div class="row adult-row">${adult_row_html}</div>`);
    $('.adult-row').last().find(':input:not([type=hidden])').removeAttr('value');
    $('.adult-row').last().find('.adult-member-select').html('');
    populateOptionsForEmptyAdults();

    if (!canAddMoreAdultMembers()) {
        $(this).hide();
        $('#children-members-list').hide();
        disableInnerFields('children-members-list');
    }
    updateCounters();
});

$(document).on('click', '.delete-member-btn', function () {
    let adult_row_count = $('.adult-row').length;

    if (adult_row_count <= 1) {
        swal("error", "Policy should have atleast one member type");
        return;
    }
    $(this).closest('.adult-row').remove();
    if (canAddMoreAdultMembers()) {
        $('#add-another-member').show();
        $('#children-members-list').show();
        enableInnerFields('children-members-list');
    } else {
        $('#add-another-member').hide();
        $('#children-members-list').hide();
        disableInnerFields('children-members-list');
    }
    updateCounters();
});

function canAddMoreAdultMembers() {
    let adult_row_count = $('.adult-row').length;
    let member_count = parseInt($('[name="membercount"]').val());

    return adult_row_count < member_count;
}

function updateCounters() {
    let adult_rows = $('.adult-row').length;
    $('#adult-count-display').val(adult_rows);

    let member_count = parseInt($('[name="membercount"]').val());
    let child_count = member_count - adult_rows;
    $('#kids-count-display').val(child_count);
}

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

        if (unselected_adult_members.length <= 0) {
            $('.adult-row').last().remove();
            displayMsg('error', 'No more member types available');
        }
        unselected_adult_members.forEach(member => {
            options_html += `<option value="${member.fr_id}">${member.fr_name}</option>`;
        });

        $(this).html(options_html);
    });
}
