// $('#main').on('click', '[id^="table"]', function(e) {
//     e.preventDefault();
//     let booking_id = $('.booking-view').data('booking-id');
//     let table = $(this);
//     let number = table.attr('id').replace(/^\D+/, '');
    
//     if (!table.hasClass('pendingDelete')) {
//         if (confirm(`Вы точно хотите удалить ${number} стол из брони?`)) {
//             table.addClass('pendingDelete');

          
//             $.post('toggle-delete', { table_id: number, booking_id: booking_id, pending: true });

//             let timerId = setTimeout(function() {
//                 // $.ajax({
//                 //     url: 'deleted-table',
//                 //     method: 'POST',
//                 //     data: { table_id: number, booking_id: booking_id, delete: true  },
//                 //     success: function(response) {
//                         console.log('Столик удалён из брони окончательно.');
//                         table.addClass('disabledTable');
//                     // },
//                     // error: function() {
//                     //     console.error('Ошибка при удалении брони.');
//                     // }
//                 // });
//             }, 50000); // например, 50 сек. для тестирования
//             table.data('deleteTimeout', timerId);
//         }
//     } else {
//         if (confirm("Отменить удаление этого стола из брони?")) {
//             clearTimeout(table.data('deleteTimeout'));
//             table.removeClass('pendingDelete complete');
//             table.find('.red-filler').remove();
//             $.post('return-table', { table_id: number, booking_id: booking_id});
//         }
//     }
// });


// $('#main').on('click', '[id^="table"]', function(e) {
//     e.preventDefault();
//     let booking_id = $('.booking-view').data('booking-id');
//     let table = $(this);
//     let number = table.attr('id').replace(/^\D+/, '');
    
//     // Если столик ещё не в состоянии "pendingDelete"
//     if (!table.hasClass('pendingDelete')) {
//         // Получаем все активные столы (без pendingDelete и disabledTable)
//         let activeTables = $('.selectedDiv').not('.pendingDelete, .disabledTable');
        
//         // Если это последний активный стол, просим дополнительное подтверждение
//         if (activeTables.length === 1) {
//             if (!confirm('Это последний стол в брони. При его удалении бронь будет отменена. Продолжить?')) {
//                 return;
//             }
//         } else {
//             if (!confirm(`Вы точно хотите удалить ${number} стол из брони?`)) {
//                 return;
//             }
//         }
        
//         table.addClass('pendingDelete');
//         $.post('toggle-delete', { table_id: number, booking_id: booking_id, pending: true });
        
//         let timerId = setTimeout(function() {
//             console.log('Столик удалён из брони окончательно.');
//             table.addClass('disabledTable');
//             // Если серверная логика реализована корректно, при отсутствии активных столов бронь автоматически отменится.
//         }, 50000); // 50 сек. для тестирования
//         table.data('deleteTimeout', timerId);
//     } else {
//         if (confirm("Отменить удаление этого стола из брони?")) {
//             clearTimeout(table.data('deleteTimeout'));
//             table.removeClass('pendingDelete complete');
//             table.find('.red-filler').remove();
//             $.post('return-table', { table_id: number, booking_id: booking_id });
//         }
//     }
// });

$('#main').on('click', '[id^="table"]', function(e) {
    e.preventDefault();
    let booking_id = $('.booking-view').data('booking-id');
    let table = $(this);
    let number = table.attr('id').replace(/^\D+/, '');
    
    // Если столик ещё не в состоянии "pendingDelete"
    if (!table.hasClass('pendingDelete')) {
        // Получаем все активные столы (без pendingDelete и disabledTable)
        let activeTables = $('.selectedDiv').not('.pendingDelete, .disabledTable');
        
        // Если это последний активный стол, запрашиваем дополнительное подтверждение
        if (activeTables.length === 1) {
            if (!confirm('Это последний стол в брони. При его удалении бронь будет отменена. Продолжить?')) {
                return;
            }
            // Отправляем запрос и перезагружаем страницу после выполнения
            $.post('cancel?id=' + booking_id, function() {
                location.reload();
            });
            return;
        } 
        
        if (!confirm(`Вы точно хотите удалить ${number} стол из брони?`)) {
            return;
        }
        
        table.addClass('pendingDelete');
        $.post('toggle-delete', { table_id: number, booking_id: booking_id, pending: true });
        
        let timerId = setTimeout(function() {
            console.log('Столик удалён из брони окончательно.');
            table.addClass('disabledTable');
        }, 50000); // 50 сек. для тестирования
        table.data('deleteTimeout', timerId);
    } else {
        if (confirm("Отменить удаление этого стола из брони?")) {
            clearTimeout(table.data('deleteTimeout'));
            table.removeClass('pendingDelete complete');
            table.find('.red-filler').remove();
            $.post('return-table', { table_id: number, booking_id: booking_id });
        }
    }
});