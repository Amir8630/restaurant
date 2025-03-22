<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\UploadedFile;
use app\models\MenuUploadForm;
use app\models\PdfUploadForm;
class MenuController extends Controller
{
    public function actionIndex()
    {
        $model = new MenuUploadForm();
        $menuItems = [];

        if (Yii::$app->request->isPost) {
            $model->file = UploadedFile::getInstance($model, 'file');
            if ($filePath = $model->upload()) {
                $menuData = file_get_contents($filePath);
                $menuItems = json_decode($menuData, true)['items'] ?? [];
            }
        }

        return $this->render('index', [
            'model' => $model,
            'menuItems' => $menuItems,
        ]);
    }

    public function actionIndex2()
    {
        $model = new PdfUploadForm();
        $pdfFilePath = null;

        if (Yii::$app->request->isPost) {
            $model->file = UploadedFile::getInstance($model, 'file');
            if ($filePath = $model->upload()) {
                $pdfFilePath = '/uploads/' . $model->file->name;
            }
        }

        return $this->render('index2', [
            'model' => $model,
            'pdfFilePath' => $pdfFilePath,
        ]);
    }

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