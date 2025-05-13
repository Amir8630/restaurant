<?php
namespace app\modules\manager\controllers;

use app\modules\manager\models\ModelsSvgUploadForm;
use Yii;
use yii\web\Controller;
use yii\web\UploadedFile;

class SvgController extends Controller
{
    public function actionUpload()
    {
        $model = new ModelsSvgUploadForm();

        if (Yii::$app->request->isPost) {
            $model->svgFile = UploadedFile::getInstance($model, 'svgFile');
            if ($model->validate()) {
                $uploadPath = Yii::getAlias('@webroot/img/');
                $filePath = $uploadPath . 'tables.svg';

                if ($model->svgFile->saveAs($filePath)) {
                    Yii::$app->session->setFlash('success', 'Файл успешно загружен.');
                } else {
                    Yii::$app->session->setFlash('error', 'Ошибка при сохранении файла.');
                }

                if (Yii::$app->request->isPjax) {
                    return $this->renderPartial('upload', ['model' => $model]);
                }
                return $this->redirect(['upload']);
            }
        }

        return $this->render('upload', ['model' => $model]);
    }
}