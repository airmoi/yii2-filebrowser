<?php
namespace airmoi\yii2filebrowser\controllers;

use yii\web\Controller;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class BrowserController extends Controller {
    
    public function actionDownload($file, $token){
        return "Download";
    }
    
    public function actionUpload($file, $token){
        return "Upload";
    }
    
    public function actionDelete($file, $token){
        return "Delete";
    }
}
