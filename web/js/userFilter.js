$(() => {
    $('#pjax-booking-index').on('change', '#usersearch-gender', function() {       
        $('#form_search').submit();
    })

    $('#pjax-booking-index').on('change', '#usersearch-role_id', function() {       
        $('#form_search').submit();
    })

    $('#pjax-booking-index').on('input', '#usersearch-fio', function() {
        $('#usersearch-title_search').val(1);
        $('#form_search').submit();
    })
    
    $('#pjax-booking-index').on('pjax:end', function() {
        if ($('#usersearch-title_search').val() == 1) {
            $('#usersearch-fio').focus();
            $('#usersearch-title_search').val(0);
            $('#usersearch-fio')[0]
                .setSelectionRange(
                    $('#usersearch-fio').val().length,
                    $('#usersearch-fio').val().length
                )
        }
    })

})

