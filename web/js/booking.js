// Обработчик клика по столику
$('#hall-container').on('click', '[id^="table"]', function(e) {
    e.preventDefault();
    
    if (!$(this).hasClass('booked')) {
        $(this).toggleClass('selected');
        // $(this).toggleClass('available');
        updateSelectedTables();
    }
});

// функция для валидации столиков и количества гостей
function validateTablesAndGuests() {
    $('#form-create').yiiActiveForm('validateAttribute', 'booking-count_guest');
    $('#form-create').yiiActiveForm('validateAttribute', 'booking-selected_tables');
}

// Функция обновления скрытого поля с выбранными столами
function updateSelectedTables() {
    let selectedTables = [];
    $('[id^="table"].selected').each(function() {
        let tableId = $(this).attr('id').replace('table', '');
        selectedTables.push(tableId);
    });
    $('#booking-selected_tables').val(selectedTables.join(','));

    // Вызываем функцию валидации после обновления выбранных столов
    validateTablesAndGuests();

}

// Обработчик изменения даты и времени бронирования
$('#booking-booking_date, #booking-booking_time_start, #booking-booking_time_end').on('change', function() {
    validateAndFetchBookedTables();
});

// Функция для валидации и отправки AJAX-запроса
function validateAndFetchBookedTables() {
    let bookingDate = $('#booking-booking_date').val();
    let startTime = $('#booking-booking_time_start').val();
    let endTime = $('#booking-booking_time_end').val();

    if (bookingDate && startTime && endTime) {
        console.log('data success');

        $.ajax({
            url: 'get-booked-tables',
            type: 'POST',
            data: {
                booking_date: bookingDate,
                booking_time_start: startTime,
                booking_time_end: endTime
            },
            success: function(bookedTablesId) {
                $('[id^="table"]').removeClass('booked');

                bookedTablesId.forEach(function(tableId) {
                    $('#table' + tableId).removeClass('selected');
                    $('#table' + tableId).addClass('booked');
                });
                // удаляем забронированные столы из поля #booking-selected_tables и сохраняем только свободные
                let selectedTables = $('#booking-selected_tables').val().split(',');
                selectedTables = selectedTables.filter(tableId => !bookedTablesId.includes(parseInt(tableId)));
                $('#booking-selected_tables').val(selectedTables.join(','));
                
                // Вызываем функцию валидации после изменения даты и времени
                validateTablesAndGuests();
            },
            error: function() {
                console.log('Ошибка при получении данных о забронированных столах');
            }
        });
    }
}

// Автоматическое обновление забронированных столов после загрузки страницы
$(document).ready(function() {
    validateAndFetchBookedTables();
    // $('#booking-selected_tables').val('');
});

// Автоматическое заполнение времени окончания (+2 часа, с учетом рабочего времени ресторана)
$('#booking-booking_time_start').on('change', function() {
    let timeStart = $(this).val();
    let alertElement = $('.alert');

    if (timeStart) {
        let parts = timeStart.split(':');
        let hour = parseInt(parts[0], 10);
        let minute = parseInt(parts[1], 10);

        // Проверяем, если время начала меньше 7 или больше 22
        if (hour < 7 || hour > 22) {
            alertElement.removeClass('d-none'); // Показываем предупреждение
            return; // Прекращаем выполнение
        } else {
            alertElement.addClass('d-none'); // Скрываем предупреждение
        }

        // Добавляем 2 часа, но учитываем рабочее время ресторана (7:00 - 23:00)
        hour += 2;
        if (hour >= 23) {
            hour = 23; // Время окончания не может быть позже 23:00
            minute = 0; // Сбрасываем минуты на 00
        }

        let newHour = hour < 10 ? '0' + hour : hour;
        let newMinute = minute < 10 ? '0' + minute : minute;
        let newTime = newHour + ':' + newMinute;

        $('#booking-booking_time_end').val(newTime);

        // без задержки возникает ошибка в аякс запросе
        setTimeout(validateAndFetchBookedTables, 250);
    }
});

// $(document).ready(function () {
//     $('#booking-booking_date, #booking-booking_time_start, #booking-booking_time_end').on('change', function () {
//         $('#form-create').yiiActiveForm('validateAttribute', 'booking-booking_date');
//         $('#form-create').yiiActiveForm('validateAttribute', 'booking-booking_time_start');
//         $('#form-create').yiiActiveForm('validateAttribute', 'booking-booking_time_end');
//     });
// });

