// работает но с ошибкой в консоли
$(() => {
    $('#pjax-booking-index, #booking-view').on('click', '.btn-cancel-modal', function(e) {
        e.preventDefault();
        const bookingNumber = $(this).data('number');
        const bookingId = $(this).attr('href').split('id=')[1]; // Получаем ID из URL

        $('#cancel-modal').find('#text').text(`Отменить бронь ${bookingNumber}?`); // Обновляем текст в модалке
        $('#cancel-modal').find('.btn-cancel')
            .attr('href', $(this).attr('href'))
            .attr('data-id', bookingId); // Запоминаем ID
        $('#cancel-modal').modal('show');
        return false;
    });

    $('#cancel-modal').on('click', '.btn-close-modal', function(e) {
        e.preventDefault();
        $('#cancel-modal').modal('hide');
    });

    $('#cancel-modal').on('click', '.btn-cancel[data-pjx^="#"]', function(e) {
        e.preventDefault();
        const pjx = $(this).data('pjx');
        const bookingId = $(this).data('id');
        const mailUrlBase = $('#cancel-modal').data('mail-url');

        $.ajax({
            url: $(this).attr('href'),
            method: 'POST',
            success: function(data) {
                if (data) {
                    $('#cancel-modal').modal('hide');
                    // $.pjax.reload({container: pjx});
                    

                                       $.pjax.reload({
                        container: '#pjax-booking-index',
                        url: '/account/booking/index?sendMailCancel=1&id=' + bookingId
                    });

                    // http://localhost/account/booking/index
                        // $.pjax.reload({
                        //     container: '#pjax-booking-index',
                        //     url: window.location.pathname + '?sendMailCancel=1&id=' + bookingId
                        // });

                    // fetch(`${mailUrlBase}?id=${bookingId}`)
                    //navigator.sendBeacon(`${mailUrlBase}?id=${bookingId}`);
                    // navigator.sendBeacon(mailUrlBase, new URLSearchParams({ id: bookingId }))
                }
            }
        });
        return false;
    });
});