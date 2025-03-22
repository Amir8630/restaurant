<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
?>
<div class="menu-container">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
        <?= $form->field($model, 'file')->fileInput() ?>
        <?= Html::submitButton('Загрузить PDF', ['class' => 'btn btn-primary']) ?>
    <?php ActiveForm::end(); ?>

    <?php if (!empty($pdfFilePath)): ?>
        <div class="pdf-viewer">
            <canvas id="pdf-canvas"></canvas>
        </div>

        <button id="prev-page">Предыдущая страница</button>
        <span id="page-num"></span> / <span id="page-count"></span>
        <button id="next-page">Следующая страница</button>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
        <script>
            let url = "<?= Html::encode($pdfFilePath) ?>";
            let pdfDoc = null,
                pageNum = 1,
                pageRendering = false,
                pageNumPending = null,
                scale = 1.5,
                canvas = document.getElementById("pdf-canvas"),
                ctx = canvas.getContext("2d");

            // Указываем воркер
            pdfjsLib.GlobalWorkerOptions.workerSrc = "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js";

            function renderPage(num) {
                pageRendering = true;
                pdfDoc.getPage(num).then(function(page) {
                    let viewport = page.getViewport({ scale: scale });
                    canvas.height = viewport.height;
                    canvas.width = viewport.width;

                    let renderContext = {
                        canvasContext: ctx,
                        viewport: viewport
                    };
                    let renderTask = page.render(renderContext);

                    renderTask.promise.then(function() {
                        pageRendering = false;
                        if (pageNumPending !== null) {
                            renderPage(pageNumPending);
                            pageNumPending = null;
                        }
                    });
                });

                document.getElementById("page-num").textContent = num;
            }

            function queueRenderPage(num) {
                if (pageRendering) {
                    pageNumPending = num;
                } else {
                    renderPage(num);
                }
            }

            document.getElementById("prev-page").addEventListener("click", function() {
                if (pageNum <= 1) return;
                pageNum--;
                queueRenderPage(pageNum);
            });

            document.getElementById("next-page").addEventListener("click", function() {
                if (pageNum >= pdfDoc.numPages) return;
                pageNum++;
                queueRenderPage(pageNum);
            });

            pdfjsLib.getDocument(url).promise.then(function(pdfDoc_) {
                pdfDoc = pdfDoc_;
                document.getElementById("page-count").textContent = pdfDoc.numPages;
                renderPage(pageNum);
            });
        </script>
    <?php endif; ?>
</div>

<style>
.menu-container {
    text-align: center;
    margin: 20px auto;
    max-width: 800px;
}

.pdf-viewer {
    display: flex;
    justify-content: center;
    margin: 20px 0;
}

canvas {
    border: 1px solid #ddd;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
}

button {
    margin: 10px;
    padding: 10px 15px;
    font-size: 16px;
    cursor: pointer;
}
</style>