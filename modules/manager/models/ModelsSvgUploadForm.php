<?php
namespace app\modules\manager\models;

use yii\base\Model;
use yii\web\UploadedFile;

class ModelsSvgUploadForm extends Model
{
    /**
     * @var UploadedFile
     */
    public $svgFile;

    public function rules()
    {
        return [
            [['svgFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'svg', 'checkExtensionByMimeType' => false],
        ];
    }

    public function attributeLabels()
    {
        return [
            'svgFile' => 'Схема зала',
        ];
    }
}