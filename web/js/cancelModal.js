$(() => {
    $('#pjax-booking-index, #booking-view').on('click', '.btn-cancel-modal', function(e) {
        e.preventDefault();
        $('#cancel-modal').find('.btn-cancel').attr('href', $(this).attr('href'));
        $('#cancel-modal').modal('show');
        return false;
    })

    $('#cancel-modal').on('click', '.btn-close-modal', function(e) {
        e.preventDefault();
        $('#cancel-modal').modal('hide');
    })

    $('#cancel-modal').on('click', '.btn-cancel[data-pjx^="#"]', function(e) {
        e.preventDefault();
        const pjx = $(this).data('pjx')

        $.ajax({
            url: $(this).attr('href'),
            method: 'POST',
            success: function(data) {
                if(data) {
                    $.pjax.reload({container: pjx})
                    $('#cancel-modal').modal('hide')
                }
            }
        })
        return false;
    })
})