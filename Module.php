<?php
namespace airmoi\yii2filebrowser;


class Module extends \yii\base\Module
{
    public $controllerNamespace = 'airmoi\yii2filebrowser\controllers';
    
    public $root;
    public $tempDir;
    
    public $validationKey;
    public $rules = [];
    public function init()
    {
        parent::init();
        // custom initialization code goes here
        
        //$this->defaultRoute = 'browser';
    }
    
    public function generateConfigToken($params) {
        return sha1(json_encode($params).$this->validationKey);
    }
}