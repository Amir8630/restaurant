<?php
namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class PdfUploadForm extends Model
{
    public $file;

    public function rules()
    {
        return [
            [['file'], 'file', 'extensions' => 'pdf', 'checkExtensionByMimeType' => false],
        ];
    }

    public function upload()
    {
        if ($this->validate() && $this->file !== null) {
            $uploadPath = Yii::getAlias('@webroot/uploads/menu.pdf');  // всегда одно имя
            if ($this->file->saveAs($uploadPath)) {
                return '/uploads/menu.pdf';  // путь, который можно передать во view
            }
        }
        return false;
    }

    public function attributeLabels()
    {
        return [
            'file' => 'Загрузить меню',
        ];
    }
}

?>