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
  padding: 20px;
  box-sizing: border-box;
  justify-content: flex-start;
}


.menu-container h1 {
  /* margin-top: 40px;       отступ сверху */
  margin-bottom: 30px;    /* отступ снизу */
  font-size: 2.5rem;      /* размер шрифта */
  font-weight: 700;       /* жирность */
  line-height: 1.2;       /* межстрочный интервал */
  color: #333333;         /* цвет текста */
  text-align: center;     /* центрирование */
  /* можно добавить шрифт */
}


button {
  min-width: 130px;
}

@media (max-width: 600px) {
  button {
    min-width: 80px;
    font-size: 14px;
  }
  .menu-container {
    padding: 10px;
  }
}
</style>


<div class="menu-container">
    <h1><?= Html::encode($this->title) ?></h1>

<?php if (!Yii::$app->user->isGuest && (Yii::$app->user->identity->isAdmin || Yii::$app->user->identity->getRoleTitle() == 'Менеджер')): ?>
    <div class="mb-4 w-100" style="max-width: 500px;">

        <?php $form = ActiveForm::begin([
            'options' => ['enctype' => 'multipart/form-data', 'id' => 'upload-form', 'class' => 'd-flex justify-content-center'],
        ]); ?>

        <?= $form->field($model, 'file')->fileInput([
            'accept' => 'application/pdf',
            'class' => 'd-none',
            'id' => 'pdfUploadInput',
            'onchange' => 'document.getElementById("upload-form").submit();',
        ])->label(false) ?>

        <button type="button" id="custom-upload-button" class="btn btn-outline-primary2" style="min-width: 150px;">
            <i class="bi bi-cloud-upload me-2"></i> Загрузить PDF-файл
        </button>

        <?php ActiveForm::end(); ?>

    </div>

    <script>
        document.getElementById('custom-upload-button').addEventListener('click', function() {
            document.getElementById('pdfUploadInput').click();
        });
    </script>
<?php endif; ?>


    <div id="menu-book"></div>

    <div class="mt-3 text-center">
        <button id="prev-page" class="btn btn-outline-primary2 me-3">
            <i class="bi bi-arrow-left"></i> Назад
        </button>
        <button id="next-page" class="btn btn-outline-primary2">
            Вперед <i class="bi bi-arrow-right"></i>
        </button>
    </div>
</div>

<script src="/js/pdf.min.js"></script>
<script src="/js/pdf.worker.min"></script>
<script>
  pdfjsLib.GlobalWorkerOptions.workerSrc = '/js/pdf.worker.min.js';
</script>

<?php
$this->registerJsFile('@web/js/turn.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);

$flipSoundUrl = Yii::getAlias('@web') . '/sounds/flip2.wav';
$jsonSoundUrl = json_encode($flipSoundUrl);

$js = <<<JS
let url = '/uploads/menu.pdf?v=' + new Date().getTime();
if (!url) {
    console.warn('PDF не загружен');
    return;
}

let viewportWidth, viewportHeight, bookInitialized = false;
let flipSound = new Audio($jsonSoundUrl);

// Фиксированные размеры для ПК
const desktopWidth = 1850.42;
const desktopHeight = 1151.82;

// Размеры для мобильных устройств
const mobileWidth = 360;
const mobileHeight = 640;

function getContainerSize() {
    const aspectRatio = 1850.42 / 1151.82; // соотношение сторон PDF (примерно 1.6)

    if (window.innerWidth <= 600) {
        return { width: 360, height: 640 };
    }
    if (window.innerWidth <= 1980) {
        return { width: 1207.94, height: 752 };
    }

    const maxWidth = Math.min(window.innerWidth * 0.85, 1600); // ограничим максимум
    const height = maxWidth / aspectRatio;

    return { width: maxWidth, height };
}

function initBook(w, h, numPages) {
    $('#menu-book').turn({
        width:  w,
        height: h,
        autoCenter: true,
        display: (numPages === 1 || w <= 600) ? 'single' : 'double',
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
                const { width: containerWidth, height: containerHeight } = getContainerSize();

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
    const { width, height } = getContainerSize();
    $('#menu-book').turn('size', width, height).turn('center');
});
JS;

$this->registerJs($js);
?>
