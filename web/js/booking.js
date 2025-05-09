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

        validateAndFetchBookedTables();
    }
});


// $('#pjax-booking').on('pjax:end', () => {
//     console.log('Мы тут');
//     // получаем данные полей для отправки письма из session
//     let bookingDate = $('#booking-booking_date').val();
//     let startTime = $('#booking-booking_time_start').val();
//     let endTime = $('#booking-booking_time_end').val();
//     let guestCount = $('#booking-count_guest').val();
//     let selectedTables = $('#booking-selected_tables').val().split(',');
 
    
    
//     if (status.success) {
//         $.ajax({
//             url: '/account/booking/mail',
//             type: 'POST',
//             data: $('#form-create').serialize(),
//             success: function(res) {
//                 console.log('Письмо отправлено');
//             },
//             error: function() {
//                 console.error('Ошибка отправки');
//             }
//         });
//     }
// });


// $(function() {
//     $('#form-create').on('beforeSubmit', function() {
//         var $form = $(this);
//         $.ajax({
//             url:      $form.attr('action'),
//             type:     'post',
//             data:     $form.serialize(),
//             dataType: 'json'
//         }).done(function(data) {
//             if (data.success) {
//                 // fire-and-forget: отправляем письмо
//                 if (navigator.sendBeacon) {
//                     navigator.sendBeacon(data.mailUrl);
//                 } else {
//                     fetch(data.mailUrl, { method: 'GET', keepalive: true });
//                 }
//                 // мгновенный редирект
//                 window.location.href = data.redirectUrl;
//             } else if (data.errors) {
//                 // показываем ошибки ActiveForm
//                 $form.yiiActiveForm('updateMessages', data.errors, true);
//             } else {
//                 alert('Не удалось сохранить бронь. Попробуйте ещё раз.');
//             }
//         }).fail(function() {
//             alert('Серверная ошибка. Повторите позднее.');
//         });

//         return false; // отменяем дефолтную отправку
//     });
// });


// После успешного PJAX сабмита (бронь сохранена) запускаем actionMail
// $('#pjax-booking').on('pjax:end', () => {
//     let data = $('#form-create').serialize();
//     console.log('Письмо подтверждения отправлено');
//     console.log(data);

    // $.ajax({
    //     url:  'mail',        // при необходимости поправьте на полный URL
    //     type: 'POST',
    //     dataType: 'json',
    //     data: data,
    //     success: function(res) {
    //         if (res.success) {
    //             console.log('Письмо подтверждения отправлено');
    //         } else {
    //             console.warn('Ошибка при отправке письма');
    //         }
    //     },
    //     error: function() {
    //         console.error('Не удалось вызвать actionMail');
    //     }
    // });
// });

