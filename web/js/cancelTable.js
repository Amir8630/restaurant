$('#main').on('click', '[id^="table"]', function(e) {
    e.preventDefault();
    let booking_id = $('.booking-view').data('booking-id');
    let table = $(this);
    let number = table.attr('id').replace(/^\D+/, '');
    
    if (!table.hasClass('pendingDelete')) {
        if (confirm(`Вы точно хотите удалить ${number} стол из брони?`)) {
            table.addClass('pendingDelete');

          
            $.post('toggle-delete', { table_id: number, booking_id: booking_id, pending: true });

            let timerId = setTimeout(function() {
                // $.ajax({
                //     url: 'deleted-table',
                //     method: 'POST',
                //     data: { table_id: number, booking_id: booking_id, delete: true  },
                //     success: function(response) {
                        console.log('Столик удалён из брони окончательно.');
                        table.addClass('disabledTable');
                    // },
                    // error: function() {
                    //     console.error('Ошибка при удалении брони.');
                    // }
                // });
            }, 50000); // например, 50 сек. для тестирования
            table.data('deleteTimeout', timerId);
        }
    } else {
        if (confirm("Отменить удаление этого стола из брони?")) {
            clearTimeout(table.data('deleteTimeout'));
            table.removeClass('pendingDelete complete');
            table.find('.red-filler').remove();
            $.post('return-table', { table_id: number, booking_id: booking_id});
        }
    }
});