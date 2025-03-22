<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;

$this->title = 'Электронное меню';
?>

<div class="menu-container">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
    <div class="d-flex" style="align-items: center; justify-content: center;">
        <?= $form->field($model, 'file')->fileInput() ?>
        <?= Html::submitButton('Загрузить PDF', ['class' => 'btn btn-outline-primary']) ?>    
    </div>
    <?php ActiveForm::end(); ?>

    <div id="menu-book">
        <!-- Сюда будут загружаться страницы PDF как изображения -->
    </div>

    <button id="prev-page">Предыдущая страница</button>
    <button id="next-page">Следующая страница</button>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.worker.min.js"></script>
<?php $this->registerJsFile('@web/js/turn.min.js', ['depends' => [\yii\web\JqueryAsset::class]]); ?>

<script>
    // let flipSound = new Audio("< Yii::getAlias('@web/sounds/flip2.mp3') ?>");
    let flipSound = new Audio("<?= Yii::getAlias('@web/sounds/flip2.wav') ?>");



    
    // После загрузки PDF
    let url = "<?= Html::encode($pdfFilePath) ?>"; // Путь к загруженному файлу
    let pdfDoc = null;
    let scale = 1.5;

    // Настроим worker для PDF.js
    pdfjsLib.GlobalWorkerOptions.workerSrc = "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.worker.min.js";

    // Загружаем PDF документ
    pdfjsLib.getDocument(url).promise.then(function (pdf) {
        pdfDoc = pdf;
        let pagesContainer = document.getElementById("menu-book");

        // Отображаем все страницы как изображения
        for (let i = 1; i <= pdf.numPages; i++) {
            pdf.getPage(i).then(function (page) {
                let viewport = page.getViewport({ scale: scale });
                let canvas = document.createElement("canvas");
                let ctx = canvas.getContext("2d");
                canvas.height = viewport.height;
                canvas.width = viewport.width;

                let renderContext = {
                    canvasContext: ctx,
                    viewport: viewport
                };

                page.render(renderContext).promise.then(() => {
                    let img = document.createElement("img");
                    img.src = canvas.toDataURL("image/png");
                    img.classList.add("menu-page");
                    pagesContainer.appendChild(img);

                    // Инициализируем Turn.js после загрузки всех страниц
                    if (i === pdf.numPages) {
                        $("#menu-book").turn({
                        width: viewport.width * 1.1,
                        height: viewport.height * 1.1,
                        autoCenter: true,
                        display: 'double',
                        startPage: 2,
                        when: {
                            turning: function () {
                                flipSound.currentTime = 0; // Сброс звука на начало
                                flipSound.play(); // Воспроизведение звука
                            }
                        }
                    });
                    }
                });
            });
        }
    });

    // Навигация по страницам
    document.getElementById("prev-page").addEventListener("click", function () {
        $("#menu-book").turn("previous");
    });

    document.getElementById("next-page").addEventListener("click", function () {
        $("#menu-book").turn("next");
    });
</script>

<!-- <style>
    .menu-container {
        text-align: center;
        margin: 20px auto;
        max-width: 800px;
    }

    #menu-book {
        display: flex;
        justify-content: center;
        margin: 20px 0;
    }

    .menu-page {
        max-width: 100%;
        margin: 5px;
    }

    button {
        margin: 10px;
        padding: 10px 15px;
        font-size: 16px;
        cursor: pointer;
    }
</style> -->

