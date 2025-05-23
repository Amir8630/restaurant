document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('pjax-booking-index');

    document.body.addEventListener('click', function (e) {
        if (e.target.matches('.change-order-status-btn')) {
            const id = e.target.dataset.id;
            const status = e.target.dataset.status;

            fetch(`/cook/order/update-status?id=${id}&status=${status}`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-Token': yii.getCsrfToken(),
                },
            }).then(response => response.json())
              .then(data => {
                if (data.success) {
                    $.pjax.reload({container: '#pjax-booking-index', timeout: 5000});
                }
            });
        }

        if (e.target.matches('.change-dish-status-btn')) {
            const id = e.target.dataset.id;
            const status = e.target.dataset.status;

            fetch(`/cook/order/update-dish-status?id=${id}&status=${status}`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-Token': yii.getCsrfToken(),
                },
            }).then(response => response.json())
              .then(data => {
                if (data.success) {
                    $.pjax.reload({container: '#pjax-booking-index', timeout: 5000});
                }
            });
        }
    });
});
