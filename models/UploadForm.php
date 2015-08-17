<?php
namespace airmoi\yii2filebrowser\models;

use yii\base\Model;
use yii\web\UploadedFile;

class UploadForm extends Model
{
    /**
     * @var UploadedFile
     */
    public $file;
    public $path;

    public function rules()
    {
        return [
            [['file'], 'file', 'skipOnEmpty' => false, 'extensions' => ''],
        ];
    }

    public function upload()
    {
        if ($this->validate()) {
            $this->file->saveAs($this->path . '/' .$this->file->baseName . '.' . $this->file->extension);
            return true;
        } else {
            return false;
        }
    }
}