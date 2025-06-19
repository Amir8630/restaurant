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
        $pdfFilePath = '/uploads/menu.pdf';  // всегда один и тот же путь

        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post())) {
                $model->file = UploadedFile::getInstance($model, 'file');
                if ($model->upload()) {
                    Yii::$app->session->setFlash('success', 'Меню успешно обновлено.');
                    return $this->redirect(['index3']);  // чтобы обновилось отображение
                }
            }
        }

        return $this->render('index3', [
            'model' => $model,
            'pdfFilePath' => $pdfFilePath,
        ]);
    }

}