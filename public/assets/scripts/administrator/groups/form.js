var redirect = baseUrl("/groups");

$('.select2').select2();

function check(t) {
    $(t).closest('.row').find('input[type="checkbox"]')
        .eq(0).prop('checked', true);
}

function check_all_childs(t) {
    var is_checked = $(t).is(':checked');
    if (is_checked) {
        $(t).closest('.card').find('.row').find('input[type="checkbox"]')
            .prop('checked', true);
    } else {
        $(t).closest('.card').find('.row').find('input[type="checkbox"]')
            .prop('checked', false);
    }
}

$('#form-data').formSubmit(function (response) {
    if (response.statusCode == 200) {
        swal('Sukses!', response.message, 'success');
        location.href = redirect;
    }
});
