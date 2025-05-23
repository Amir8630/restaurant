document.addEventListener('DOMContentLoaded', function () {
    const containerId = '#pjax-booking-index';

    document.body.addEventListener('click', async function (e) {
        const button = e.target;

        if (button.matches('.change-order-status-btn') || button.matches('.change-dish-status-btn')) {
            const isOrder = button.matches('.change-order-status-btn');
            const id = button.dataset.id;
            const status = button.dataset.status;
            const url = isOrder
                ? `/waiter/order/update-status?id=${id}&status=${status}`
                : `/waiter/order/update-dish-status?id=${id}&status=${status}`;

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-Token': yii.getCsrfToken(),
                    },
                });

                const data = await response.json();

                if (data.success) {
                    // Определяем цвет кнопки
                    const buttonStyle = window.getComputedStyle(button);
                    const backgroundColor = '#7e57c2';

                    // Показываем уведомление
                    showToast(
                        isOrder ? 'Статус заказа обновлён' : 'Статус блюда обновлён',
                        backgroundColor
                    );

                    // Сохраняем текущий URL с пагинацией
                    const currentUrl = window.location.href;

                    $.pjax.reload({
                        container: containerId,
                        url: currentUrl,
                        timeout: 5000
                    });
                } else {
                    showToast('Не удалось обновить статус', '#dc3545');
                }
            } catch (err) {
                showToast('Ошибка запроса', '#dc3545');
            }
        }
    });
});


function showToast(message, bgColor = '#28a745', duration = 3000) {
    const toast = document.createElement('div');
    toast.textContent = message;
    toast.style.cssText = `
        background: ${bgColor};
        color: white;
        padding: 10px 20px;
        margin-top: 10px;
        border-radius: 5px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        animation: fadein 0.3s, fadeout 0.3s ${duration - 300}ms;
        opacity: 0;
        transition: opacity 0.3s;
        z-index: 9999;
    `;

    setTimeout(() => toast.style.opacity = '1', 10);
    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }, duration);

    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.style.position = 'fixed';
        container.style.top = '20px';
        container.style.right = '20px';
        container.style.zIndex = '9999';
        document.body.appendChild(container);
    }

    container.appendChild(toast);
}
