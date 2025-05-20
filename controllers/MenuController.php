<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\UploadedFile;
use app\models\MenuUploadForm;
use app\models\PdfUploadForm;
class MenuController extends Controller
{
    public function actionIndex3()
    {
        $model = new PdfUploadForm();
        $pdfFilePath = null;

        if (Yii::$app->request->isPost) {
            $model->file = UploadedFile::getInstance($model, 'file');
            if ($filePath = $model->upload()) {
                $pdfFilePath = '/uploads/' . $model->file->name;
            }
        }

        return $this->render('index3', [
            'model' => $model,
            'pdfFilePath' => $pdfFilePath,
        ]);
    }
}