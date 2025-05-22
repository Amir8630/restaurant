$(() => {
    $('#pjax-booking-index').on('change', '#usersearch-gender', function() {       
        $('#form_search').submit();
    })

    $('#pjax-booking-index').on('change', '#usersearch-role_id', function() {       
        $('#form_search').submit();
    })




    $('#pjax-booking-index').on('input', '#usersearch-id', function() {
        $('#usersearch-user_id').val(1);
        $('#form_search').submit();
    })
    
    $('#pjax-booking-index').on('pjax:end', function() {
        if ($('#usersearch-user_id').val() == 1) {
            $('#usersearch-id').focus();
            $('#usersearch-user_id').val(0);
            $('#usersearch-id')[0]
                .setSelectionRange(
                    $('#usersearch-id').val().length,
                    $('#usersearch-id').val().length
                )
        }
    })




    $('#pjax-booking-index').on('input', '#usersearch-created_by_id', function() {
        $('#usersearch-id_created_by').val(1);
        $('#form_search').submit();
    })
    
    $('#pjax-booking-index').on('pjax:end', function() {
        if ($('#usersearch-id_created_by').val() == 1) {
            $('#usersearch-created_by_id').focus();
            $('#usersearch-id_created_by').val(0);
            $('#usersearch-created_by_id')[0]
                .setSelectionRange(
                    $('#usersearch-created_by_id').val().length,
                    $('#usersearch-created_by_id').val().length
                )
        }
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

