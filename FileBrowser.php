<?php

namespace airmoi\yii2filebrowser;

use Yii;
use airmoi\yii2filebrowser\Module;

use SuperClosure\Serializer;
use SuperClosure\Analyzer\TokenAnalyzer;

/**
 * This is just an example.
 */
class FileBrowser extends \yii\base\Widget
{
    public $rootPath;
    public $rootName;
    public $permissions = ['upload' => false, 'delete' => false, 'createdir' => false, 'subdir' => true];
    public $allowSearch = true;
    public $showBreadcrumbs = true;
    
    
    public $afterUpload;
    
    public $afterDelete;
    
    public $afterCreateDir;
    
    private $_token;
    
    public function init(){ 
    	parent::init(); 
        if(empty($this->rootPath)){
            $this->rootPath = Yii::getAlias('@webroot/uploads');
        }
        if(empty($this->rootName)){
            $this->rootName = basename($this->rootPath);
        }
    } 
    
    public function run()
    {
        $view = $this->getView();
        FileBrowserAssets::register($view);
        
        $serializer = new Serializer(new TokenAnalyzer());
        
        $this->_token = Module::storeConfig([
            'rootPath' => $this->rootPath, 
            'rootName' => $this->rootName, 
            'permissions' => $this->permissions,
            'afterUpload' => is_callable($this->afterUpload) ? $serializer->serialize($this->afterUpload) : null,
            'afterDelete' => is_callable($this->afterDelete) ? $serializer->serialize($this->afterDelete) : null,
            'afterCreateDir' => is_callable($this->afterCreateDir) ? $serializer->serialize($this->afterCreateDir) : null,
        ]);
        $params = [
            'token' => $this->_token,
            'permissions' => $this->permissions,
            'route' => \yii\helpers\Url::to(['filebrowser/browser']),
            'prettyUrl' => Yii::$app->urlManager->enablePrettyUrl,
        ];
        $js = "\$('#{$this->id}')
            .filebrowser(".  json_encode($params).")";
        $view->registerJs($js);
        
        return $this->render('widget', ['widget' =>$this]);
    }
    
    public function getToken() {
        return $this->_token;
    }

}
