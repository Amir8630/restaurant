$(() => {
    const container = $('.scheme-container')[0];
    const svg = container.querySelector('svg');
    const input = document.getElementById('svgUploadInput');
    const form = container.querySelector('form');

    if (svg) {
        const rect = svg.getBoundingClientRect();
        container.style.width = rect.width + 'px';
        container.style.height = rect.height + 'px';
    }

    container.addEventListener('dragover', (e) => {
        e.preventDefault();
        container.style.opacity = '0.8';
    });

    container.addEventListener('dragleave', () => {
        container.style.opacity = '1';
    });

    container.addEventListener('drop', (e) => {
        e.preventDefault();
        container.style.opacity = '1';
        if (e.dataTransfer.files[0].type === 'image/svg+xml') {
            input.files = e.dataTransfer.files;
            form.submit();
        }
    });

    container.addEventListener('click', () => {
        input.click();
    });

    input.addEventListener('change', () => {
        if (input.files.length > 0) {
            form.submit();
        }
    });

    // автоматическое закрытие session->setFlash('success', 'Схема успешно загружена!') через 3 секунды
    setTimeout(() => {
        const flash = document.querySelector('.alert');
        if (flash) {
            flash.style.transition = 'opacity 0.5s ease';
            flash.style.opacity = '0';
            setTimeout(() => {
                flash.remove();
            }, 500);
        }
    }, 3000);
})

