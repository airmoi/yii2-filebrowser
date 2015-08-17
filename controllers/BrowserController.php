<?php
namespace airmoi\yii2filebrowser\controllers;

use Yii;
use yii\rest\Controller;
use yii\helpers\FileHelper;
use airmoi\yii2filebrowser\Module;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class BrowserController extends Controller {
    
    public function actionDownload($file, $token){
        $config = Module::getConfig($token);
        $relativePath = explode('/', trim($file));
        array_shift($relativePath);
        $path = $config['rootPath'].'/'. implode('/', $relativePath);
        if(!file_exists($path)){
            throw new \yii\web\NotFoundHttpException('Le fichier est introuvable');
        }
        
        return Yii::$app->getResponse()->sendFile($path, basename($path));
    }
    
    public function actionUpload($file, $token){
        return "Upload";
    }
    
    public function actionDelete($file, $token){
        return "Delete";
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
