let pendingTableId = null;

$('#main').on('click', '[id^="table"]', function(e) {
    e.preventDefault();
    let booking_id = $('.booking-view').data('booking-id');
    let table = $(this);
    let number = table.attr('id').replace(/^\D+/, '');

    // Получаем все активные столы (без pendingDelete и disabledTable)
    let activeTables = $('.selectedDiv').not('.pendingDelete, .disabledTable');

    // Если последний активный стол и он НЕ в процессе удаления — показываем подтверждение отмены всей брони
    if (activeTables.length === 1 && !table.hasClass('pendingDelete')) {
        $('#tableDeleteModalTitle').text('Отменить бронь?');
        $('#tableDeleteModalText').text('Это последний стол в брони. При его удалении бронь будет отменена. Продолжить?');
        $('#confirmTableDeleteBtn')
            .text('Отменить бронь')
            .removeClass('btn-warning')
            .addClass('btn-danger');

        // Запоминаем данные в кнопке модалки
        $('#confirmTableDeleteBtn').data('cancelWholeBooking', true);
        $('#confirmTableDeleteBtn').data('pendingTableId', number);

        $('#tableDeleteModal').modal('show');
        return;
    }

    // Если столик уже помечен на удаление — показываем модалку с отменой удаления
    if (table.hasClass('pendingDelete')) {
        $('#tableDeleteModalTitle').text('Отменить удаление стола?');
        $('#tableDeleteModalText').text('Вы уверены, что хотите отменить удаление стола ' + number + '?');
        $('#confirmTableDeleteBtn')
            .text('Отменить удаление')
            .removeClass('btn-danger')
            .addClass('btn-warning');

        $('#confirmTableDeleteBtn').data('cancelWholeBooking', false);
        $('#confirmTableDeleteBtn').data('pendingTableId', number);

        $('#tableDeleteModal').modal('show');
        return;
    }

    // Обычное удаление стола — показываем подтверждение
    $('#tableDeleteModalTitle').text('Удалить стол из брони?');
    $('#tableDeleteModalText').text('Вы точно хотите удалить стол ' + number + ' из брони?');
    $('#confirmTableDeleteBtn')
        .text('Удалить')
        .removeClass('btn-warning')
        .addClass('btn-danger');

    $('#confirmTableDeleteBtn').data('cancelWholeBooking', false);
    $('#confirmTableDeleteBtn').data('pendingTableId', number);

    $('#tableDeleteModal').modal('show');
});

$('#confirmTableDeleteBtn').on('click', function () {
    let booking_id = $('.booking-view').data('booking-id');
    let isCancelWholeBooking = $(this).data('cancelWholeBooking');
    let pendingTableId = $(this).data('pendingTableId');
    let table = $('#table' + pendingTableId);

    if (isCancelWholeBooking) {
        // Отмена всей брони
        $('#tableDeleteModal').modal('hide');
        $.post('cancel?id=' + booking_id, function () {
            location.reload();
        });
    } else if (table.hasClass('pendingDelete')) {
        // Отмена удаления стола
        $('#tableDeleteModal').modal('hide');
        clearTimeout(table.data('deleteTimeout'));
        table.removeClass('pendingDelete complete');
        table.find('.red-filler').remove();
        $.post('return-table', { table_id: pendingTableId, booking_id: booking_id });
    } else {
        // Помечаем стол как ожидающий удаления
        $('#tableDeleteModal').modal('hide');
        table.addClass('pendingDelete');
        $.post('toggle-delete', { table_id: pendingTableId, booking_id: booking_id, pending: true });

        let timerId = setTimeout(function () {
            table.addClass('disabledTable');
        }, 20000); // Через 20 секунд блокируем стол окончательно
        table.data('deleteTimeout', timerId);
    }
});
