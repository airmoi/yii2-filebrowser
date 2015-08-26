<?php
namespace airmoi\yii2filebrowser\controllers;

use Yii;
use yii\rest\Controller;
use yii\helpers\FileHelper;
use airmoi\yii2filebrowser\Module;
use airmoi\yii2filebrowser\models\UploadForm;
use yii\web\UploadedFile;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class BrowserController extends Controller {
    
    public function actionDownload($file, $token){
        $path = Module::getAbsolutePath($token, $file);
        if(!file_exists($path)){
            throw new \yii\web\NotFoundHttpException('Le fichier est introuvable');
        }
        
        return Yii::$app->getResponse()->sendFile($path, basename($path));
    }
    
    public function actionUpload($token, $path){
         $model = new UploadForm();
         
        if (Yii::$app->request->isPost) {
            $model->file = UploadedFile::getInstance($model, 'file');
            $model->path = Module::getAbsolutePath($token, $path); 
            if ($model->upload()) {
                // file is uploaded successfully
                return ['success' => true, 'item' => [
					"name" => $model->encodedName,
					"type" => "file",
					"path" => $path . '/' . $model->encodedName,
					"size" => $model->file->size // Gets the size of this file
				]];
            }
            
            return ['success' => false, 'message' => $model->getErrors('file')];
        }
        return ['success' => false, 'message' => 'Not a post method !'];
    }
    
    public function actionDelete($file, $token){
        try {
            $path = Module::getAbsolutePath($token, $file);
            
            if( !Module::canDelete($token))
                throw new \yii\web\UnauthorizedHttpException('Vous ne pouvez pas supprimer de document');
            
            if(is_dir($path)){
                if(!is_writable($path))
                    throw new \yii\web\UnauthorizedHttpException('Le dossier ' . basename($path) . ' n\'est pas modifiable');
                
                $r = Module::removeDirectory($path);
            }
            else {
                /*if(!is_writable($path))
                    throw new \yii\web\UnauthorizedHttpException('Le fichier ' . basename($path) . ' n\'est pas modifiable');*/
                if( !$r = unlink($path)){
                    throw new \yii\web\UnauthorizedHttpException('Le fichier ' . basename($path) . ' n\'est pas modifiable');
                }
            }
            return ['success' => true];
        } catch(\Exception $e){
            return ['success' => false, 'message' => mb_convert_encoding($e->getMessage(), 'UTF-8' )];
        }
    }
    
    public function actionCreatedir($dirname, $path, $token){
        try {
            $absPath = Module::getAbsolutePath($token, $path.'/'.$dirname);
            
            if( !Module::canCreateDir($token))
                throw new \yii\web\UnauthorizedHttpException('Vous ne pouvez pas créer de dossier');
            
            mkdir($absPath);
            return ['success' => true, 'item' => [
					"name" => $dirname,
					"type" => "folder",
					"path" => $path.'/'.$dirname,
					"items" => []
				]];
        } catch(\Exception $e){
            return ['success' => false, 'message' => $e->getMessage() .$path  ];
        }
    }
    
    public function actionList($token){
        $config = Module::getConfig($token);     
        return [
                "name" => $config['rootName'],
                "type" => "folder",
                "path" => $config['rootName'],
                "items" => Module::scan($config['rootPath'], null, $config['rootName']),
            ];
        
    }
}
