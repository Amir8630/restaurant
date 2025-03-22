// Обработчик клика по столику
$('#hall-container').on('click', '[id^="table"]', function(e) {
    e.preventDefault();
    
    if (!$(this).hasClass('booked')) {
        $(this).toggleClass('selected');
        updateSelectedTables();
    }
});

// Функция обновления скрытого поля с выбранными столами
function updateSelectedTables() {
    let selectedTables = [];
    $('[id^="table"].selected').each(function() {
        let tableId = $(this).attr('id').replace('table', '');
        selectedTables.push(tableId);
    });
    $('#selected-tables').val(selectedTables.join(','));
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

    // Проверяем, заполнены ли все три поля
    if (bookingDate && startTime && endTime) {
        console.log('data success');
        $.ajax({
            url: 'get-booked-tables', // '/account/booking/get-booked-tables'
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
            },
            error: function() {
                console.log('Ошибка при получении данных о забронированных столах');
            }
        });
    }
}

// Обработчик изменения времени начала
$('#booking-booking_time_start').on('change', function() {
    let timeStart = $(this).val();
    if (timeStart) {
        let parts = timeStart.split(':');
        let hour = parseInt(parts[0], 10);
        let minute = parseInt(parts[1], 10);

        hour += 2;
        if (hour >= 24) {
            hour = hour - 24;
        }

        let newHour = hour < 10 ? '0' + hour : hour;
        let newMinute = minute < 10 ? '0' + minute : minute;
        let newTime = newHour + ':' + newMinute;

        $('#booking-booking_time_end').val(newTime);

        validateAndFetchBookedTables();
    }
});
