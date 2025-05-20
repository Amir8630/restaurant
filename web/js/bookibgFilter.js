$(() => {
    // $('#pjax-booking-index').on('change', '#bookingsearch-id', function() {       
    //     $('#form_search').submit();
    // })

    // $('#pjax-booking-index').on('change', '#usersearch-role_id', function() {       
    //     $('#form_search').submit();
    // })

    $('#pjax-booking-index').on('input', '#bookingsearch-id', function() {
        $('#bookingsearch-id_search').val(1);
        $('#form_search').submit();
    })
    
    $('#pjax-booking-index').on('pjax:end', function() {
        if ($('#bookingsearch-id_search').val() == 1) {
            $('#bookingsearch-id').focus();
            $('#bookingsearch-id_search').val(0);
            $('#bookingsearch-id')[0]
                .setSelectionRange(
                    $('#bookingsearch-id').val().length,
                    $('#bookingsearch-id').val().length
                )
        }
    })

    $('#pjax-booking-index').on('input', '#bookingsearch-fio_guest', function() {
        $('#bookingsearch-title_search').val(1);
        $('#form_search').submit();
    })
    
    $('#pjax-booking-index').on('pjax:end', function() {
        if ($('#bookingsearch-title_search').val() == 1) {
            $('#bookingsearch-fio_guest').focus();
            $('#bookingsearch-title_search').val(0);
            $('#bookingsearch-fio_guest')[0]
                .setSelectionRange(
                    $('#bookingsearch-fio_guest').val().length,
                    $('#bookingsearch-fio_guest').val().length
                )
        }
    })

})

