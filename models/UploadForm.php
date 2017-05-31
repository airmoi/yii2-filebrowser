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
            // #9407 : normalize filename (prevent special chars issues)
            $filename = \Normalizer::normalize($this->file->getBaseName(). '.' . $this->file->getExtension());
            $encodedPath = $this->path . '/' . $filename;
            $this->file->saveAs(mb_convert_encoding($encodedPath, 'WINDOWS-1252'));
            $this->encodedName =  $filename;
            return true;
        } else {
            return false;
        }
    }
}