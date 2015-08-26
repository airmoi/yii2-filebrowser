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
    public $encodedName;

    public function rules()
    {
        return [
            [['file'], 'file', 'skipOnEmpty' => false, 'extensions' => ''],
        ];
    }

    public function upload()
    {
        if ($this->validate()) {
            $this->file->saveAs($this->path . '/' . mb_convert_encoding( $this->file->baseName, 'Windows-1252') . '.' . $this->file->extension);
            $this->encodedName = utf8_encode(basename($this->path . '/' . mb_convert_encoding( $this->file->baseName, 'Windows-1252') . '.' . $this->file->extension));
            return true;
        } else {
            return false;
        }
    }
}