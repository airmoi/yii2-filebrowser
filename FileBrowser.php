<?php

namespace airmoi\yii2filebrowser;

use Yii;
use airmoi\yii2filebrowser\Module;

/**
 * This is just an example.
 */
class FileBrowser extends \yii\base\Widget
{
    public $rootPath;
    public $rootName;
    public $permissions = ['upload' => false, 'delete' => false, 'createdir' => false, 'subdir' => false];
    public $allowSearch = true;
    public $showBreadcrumbs = true;
    
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
        
        $this->_token = Module::storeConfig(['rootPath' => $this->rootPath, 'rootName' => $this->rootName, 'permissions' => $this->permissions]);
        
        $js = "\$('#{$this->id}').filebrowser({token:'{$this->_token}'})";
        $view->registerJs($js);
        
        return $this->render('widget', ['widget' =>$this]);
    }
    
    public function getToken() {
        return $this->_token;
    }

}
