<?php
namespace app\models;

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
        if ($this->validate()) {
            $filePath = \Yii::getAlias('@webroot/uploads/') . $this->file->name;
            $this->file->saveAs($filePath);
            return $filePath;
        }
        return false;
    }
}

?>