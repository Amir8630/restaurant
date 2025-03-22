$('#main').on('click', '[id^="table"]', function(e) {
    e.preventDefault();
    let table = $(this);
    let number = table.attr('id').replace(/^\D+/, '');

    // Если элемент ещё не в состоянии "ожидания удаления"
    if (!table.hasClass('pendingDelete')) {
        if (confirm(`Вы точно хотите удалить ${number} стол из брони?`)) {
            table.toggleClass('selectedDiv');
            table.addClass('pendingDelete');
            
            // Запускаем CSS-переход: фон меняется на красный за 300 секунд (5 минут)
            // table.css({
            //     'transition': 'background-color 300s linear',
            //     'background-color': 'red'
            // });

            table.find('::before').css({
                'transform': 'translateY(0)'
            });
            
            // Запускаем таймер на 5 минут для окончательного удаления
            let timerId = setTimeout(function() {
                $.ajax({
                    url: '/booking/delete', // замените URL на нужный
                    method: 'POST',
                    data: { tableId: table.attr('id') },
                    success: function(response) {
                        // console.log(`${response} Столик удалён из брони окончательно.`);
                        console.log('Столик удалён из брони окончательно.');
                        // Дополнительно можно изменить внешний вид или удалить элемент из DOM
                    },
                    error: function() {
                        console.error('Ошибка при удалении брони.');
                    }
                });
            }, 300000); // 300000 мс = 5 минут
            
            // Сохраняем ID таймера для возможности отмены
            table.data('deleteTimeout', timerId);
        }
    } else {
        // Если элемент уже в состоянии ожидания удаления, даём возможность отменить
        if (confirm("Отменить удаление этого стола из брони?")) {
            // Отменяем таймер и сбрасываем изменения
            clearTimeout(table.data('deleteTimeout'));
            table.removeClass('pendingDelete');
            table.css({
                'transition': '',
                'background-color': 'grey' // или другой исходный цвет
            });
        }
    }
});