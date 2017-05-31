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
    
    public function attributeLabels() {
        return [
            'file' => 'Ajouter un fichier',
        ];
    }

    public function upload()
    {
        if ($this->validate()) {
            $encodedPath = utf8_decode($this->path . '/' . $this->file->getBaseName(). '.' . $this->file->getExtension());
            $this->file->saveAs($encodedPath);
            $this->encodedName =  $this->file->getBaseName(). '.' . $this->file->getExtension();
            return true;
        } else {
            return false;
        }
    }
}