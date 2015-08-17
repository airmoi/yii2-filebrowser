<?php

namespace airmoi\yii2filebrowser;
use Yii;
/**
 * This is just an example.
 */
class FileBrowser extends \yii\base\Widget
{
    public $root;
    public $permissions = ['upload' => false, 'delete' => true, 'createdir' => false, 'subdir' => false];
    
    private $_validationKey;
    
    public function init(){ 
    	parent::init(); 
    	
        if ( empty(Yii::$app->getModule('filebrowser')->validationKey)){
            throw new \yii\base\InvalidConfigException('Invalid FileBrowser module config : please provide a validationKey');
        }
        $this->_validationKey = Yii::$app->getModule('filebrowser')->validationKey;
        
        
        
    } 
    
    public function run()
    {
        $view = $this->getView();
        FileBrowserAssets::register($view);
        
        //$files = json_encode($this->scan($this->root));
        $data = json_encode(array(
                "name" => basename($this->root),
                "type" => "folder",
                "path" => '/'.basename($this->root),
                "absolutePath" => dirname($this->root),
                "items" => $this->scan($this->root, $this->root),
                "token" => Yii::$app->getModule('filebrowser')->generateConfigToken('test'),
        ));
        $js = <<<JS
            fileBrowser.init($data)    
JS;
        $view->registerJs($js);
        return <<<HTML
    <div class="filemanager">

            <div class="search">
                    <input type="search" placeholder="Find a file.." />
            </div>

            <div class="breadcrumbs"></div>

            <ul class="data"></ul>

            <div class="nothingfound">
                    <div class="nofiles"></div>
                    <span>No files here.</span>
            </div>

    </div>            
HTML;
    }
    
    // This function scans the files folder recursively, and builds a large array

    public function scan($dir, $root){

	$files = array();

	// Is there actually such a folder/file?

	if(file_exists($dir)){
	
		foreach(scandir($dir) as $f) {
		
			if(!$f || $f[0] == '.') {
				continue; // Ignore hidden files
			}
                        $path = str_replace(dirname($root).'/', '/', $dir . '/'.$f);
			if(is_dir($dir . '/' . $f)) {

				// The path is a folder

				$files[] = array(
					"name" => $f,
					"type" => "folder",
					"path" => $path,
					"absolutePath" => $dir . '/'.$f,
					"items" => $this->scan($dir . '/' . $f, $root) // Recursively get the contents of the folder
				);
			}
			
			else {

				// It is a file

				$files[] = array(
					"name" => $f,
					"type" => "file",
					"path" => $path,
					"absolutePath" => $dir . '/'.$f,
					"size" => filesize($dir . '/' . $f) // Gets the size of this file
				);
			}
		}
	
	}

	return $files;
    }

}
