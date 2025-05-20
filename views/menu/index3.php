<?php
/** @var yii\web\View $this */
/** @var app\models\PdfUploadForm $model */
/** @var string|null $pdfFilePath */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Электронное меню';
?>

<style>
.menu-container {
  display: flex;
  flex-direction: column;
  justify-content: center; /* вертикальное центрирование */
  align-items: center;     /* горизонтальное центрирование */
  min-height: 100vh;       /* занимает всю высоту окна */
  padding: 20px;
  box-sizing: border-box;
}

#menu-book {
  width: 1850.42px !important;
  height: 1151.82px !important;
}

#menu-book canvas {
  pointer-events: none;
  -webkit-user-drag: none;
}

button {
  min-width: 130px;
}
</style>

<div class="menu-container">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data'],
    ]); ?>
    <div class="d-flex align-items-center justify-content-center mb-4">
        <?= $form->field($model, 'file')->fileInput() ?>
        <?= Html::submitButton('Загрузить PDF', ['class' => 'btn btn-outline-primary ms-3']) ?>
    </div>
    <?php ActiveForm::end(); ?>

    <div id="menu-book"></div>

<div class="mt-3 text-center">
    <button id="prev-page" class="btn btn-outline-primary me-3" style="min-width: 80px;">
        <i class="bi bi-arrow-left"></i> Назад
    </button>
    <button id="next-page" class="btn btn-outline-primary" style="min-width: 80px;">
        Вперед <i class="bi bi-arrow-right"></i>
    </button>
</div>




</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.worker.min.js"></script>

<?php
$this->registerJsFile('@web/js/turn.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);

$pdfUrl       = $pdfFilePath ? Yii::getAlias('@web') . $pdfFilePath : '';
$flipSoundUrl = Yii::getAlias('@web') . '/sounds/flip2.wav';
$jsonPdfUrl   = json_encode($pdfUrl);
$jsonSoundUrl = json_encode($flipSoundUrl);

$js = <<<JS
let url = $jsonPdfUrl;
if (!url) {
    console.warn('PDF не загружен');
    return;
}

let viewportWidth, viewportHeight, bookInitialized = false;
let flipSound = new Audio($jsonSoundUrl);

pdfjsLib.GlobalWorkerOptions.workerSrc = 
  'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.worker.min.js';

const containerWidth = 1850.42;
const containerHeight = 1151.82;

function initBook(w, h, numPages) {
    $('#menu-book').turn({
        width:  w,
        height: h,
        autoCenter: true,
        display: numPages === 1 ? 'single' : 'double',
        startPage: 1,
        when: {
            turning: function() {
                flipSound.currentTime = 0;
                flipSound.play();
            }
        }
    });
    bookInitialized = true;
}

pdfjsLib.getDocument(url).promise.then(function(pdf) {
    const container = document.getElementById('menu-book');
    let loadedPages = 0;

    for (let i = 1; i <= pdf.numPages; i++) {
        pdf.getPage(i).then(function(page) {
            const baseScale = 1.5;
            const viewport = page.getViewport({ scale: baseScale });

            if (i === 1) {
                viewportWidth  = viewport.width;
                viewportHeight = viewport.height;
            }

            const canvas = document.createElement('canvas');
            canvas.width  = viewport.width;
            canvas.height = viewport.height;
            const ctx = canvas.getContext('2d');

            page.render({ canvasContext: ctx, viewport }).promise.then(function() {
                const scaleX = containerWidth / viewport.width;
                const scaleY = containerHeight / viewport.height;
                const scale = Math.min(scaleX, scaleY);

                canvas.style.width  = (viewport.width * scale) + 'px';
                canvas.style.height = (viewport.height * scale) + 'px';

                container.appendChild(canvas);

                loadedPages++;
                if (loadedPages === pdf.numPages) {
                    initBook(containerWidth, containerHeight, pdf.numPages);
                }
            });
        });
    }
}).catch(function(err) {
    console.error('Ошибка при загрузке PDF:', err);
});

document.getElementById('prev-page')
  .addEventListener('click', () => $('#menu-book').turn('previous'));
document.getElementById('next-page')
  .addEventListener('click', () => $('#menu-book').turn('next'));

window.addEventListener('resize', function() {
    if (!bookInitialized) return;
    $('#menu-book').turn('size', containerWidth, containerHeight).turn('center');
});

document.addEventListener('menuToggled', function() {
    if (!bookInitialized) return;
    $('#menu-book').turn('size', containerWidth, containerHeight).turn('center');
});
JS;

$this->registerJs($js);
?>





<!-- <php
/** @var yii\web\View $this */
/** @var app\models\PdfUploadForm $model */
/** @var string|null $pdfFilePath */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Электронное меню';
?>

<div class="menu-container">
    <h1><= Html::encode($this->title) ?></h1>

    <php $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data'],
    ]); ?>
    <div class="d-flex align-items-center justify-content-center mb-4">
        <= $form->field($model, 'file')->fileInput() ?>
        <= Html::submitButton('Загрузить PDF', ['class' => 'btn btn-outline-primary ms-3']) ?>
    </div>
    <php ActiveForm::end(); ?>

    <div id="menu-book"></div>

    <div class="mt-3 text-center">
        <button id="prev-page" class="btn btn-secondary me-2">Предыдущая страница</button>
        <button id="next-page" class="btn btn-secondary">Следующая страница</button>
    </div>
</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.worker.min.js"></script>
<php
// Turn.js (зависит от jQuery)
$this->registerJsFile('@web/js/turn.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
?>

<php
$pdfUrl       = $pdfFilePath ? Yii::getAlias('@web') . $pdfFilePath : '';
$flipSoundUrl = Yii::getAlias('@web') . '/sounds/flip2.wav';
$jsonPdfUrl   = json_encode($pdfUrl);
$jsonSoundUrl = json_encode($flipSoundUrl);

$js = <<<JS
    let url = $jsonPdfUrl;
    if (!url) {
        console.warn('PDF не загружен');
        return;
    }

    let pdfDoc = null,
        viewportWidth, viewportHeight,
        bookInitialized = false;

    let flipSound = new Audio($jsonSoundUrl);

    pdfjsLib.GlobalWorkerOptions.workerSrc = 
      'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.worker.min.js';

    // Фиксированный размер контейнера для книги
    const containerWidth = 1850.42;
    const containerHeight = 1151.82;

    function initBook(w, h) {
        $('#menu-book').turn({
            width:  w,
            height: h,
            autoCenter: true,
            display: 'double',
            startPage: 2,
            when: {
                turning: function() {
                    flipSound.currentTime = 0;
                    flipSound.play();
                }
            }
        });
        bookInitialized = true;
    }

    pdfjsLib.getDocument(url).promise.then(function(pdf) {
        const container = document.getElementById('menu-book');
        let loadedPages = 0;

        for (let i = 1; i <= pdf.numPages; i++) {
            pdf.getPage(i).then(function(page) {
                const baseScale = 1.5;
                const viewport = page.getViewport({ scale: baseScale });

                if (i === 1) {
                    viewportWidth  = viewport.width;
                    viewportHeight = viewport.height;
                }

                const canvas = document.createElement('canvas');
                canvas.width  = viewport.width;
                canvas.height = viewport.height;
                const ctx = canvas.getContext('2d');

                page.render({ canvasContext: ctx, viewport }).promise.then(function() {
                    // Масштабируем canvas по CSS, сохраняя пропорции
                    const scaleX = containerWidth / viewport.width;
                    const scaleY = containerHeight / viewport.height;
                    const scale = Math.min(scaleX, scaleY);

                    canvas.style.width  = (viewport.width * scale) + 'px';
                    canvas.style.height = (viewport.height * scale) + 'px';

                    container.appendChild(canvas);

                    loadedPages++;
                    if (loadedPages === pdf.numPages) {
                        initBook(containerWidth, containerHeight);
                    }
                });
            });
        }
    }).catch(function(err) {
        console.error('Ошибка при загрузке PDF:', err);
    });

    document.getElementById('prev-page')
      .addEventListener('click', () => $('#menu-book').turn('previous'));
    document.getElementById('next-page')
      .addEventListener('click', () => $('#menu-book').turn('next'));

    window.addEventListener('resize', function() {
        if (!bookInitialized) return;
        $('#menu-book').turn('size', containerWidth, containerHeight).turn('center');
    });

    document.addEventListener('menuToggled', function() {
        if (!bookInitialized) return;
        $('#menu-book').turn('size', containerWidth, containerHeight).turn('center');
    });
JS;

$this->registerJs($js);
?>

<style>

</style>
 -->
