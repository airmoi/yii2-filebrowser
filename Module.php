<?php
namespace airmoi\yii2filebrowser;


use Yii;
use yii\base\InvalidConfigException;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'airmoi\yii2filebrowser\controllers';

    public $tempDir;
    
    
    public function init()
    {
        parent::init();
        
        if (!isset(Yii::$app->session)) {
            throw new InvalidConfigException("The yii2-filebrowser extension requires session handling");
        }
        
        if(empty($this->tempDir)){
            $this->tempDir = sys_get_temp_dir();
        }
    }
    
    /**
     * 
     * @param type $config
     * @return string the config token
     */
    public static function storeConfig($config) {
        $token = Yii::$app->getSecurity()->generateRandomString();
        Yii::$app->getSession()->set('yii2-filebrowser', [$token => $config]);

        return $token;
    }
    
    public static function getConfig($token) {
        if(!$config = Yii::$app->getSession()->get('yii2-filebrowser')[$token])
            throw new \yii\web\BadRequestHttpException('Invalid token provided');
        
        return $config;
    }
    
    public static function scan($dir, $root = "", $rootName = ""){

	$files = array();
        if(empty($root))
            $root = $dir;
        
        if(empty($rootName))
            $rootName = basename($root);
	// Is there actually such a folder/file?

	if(file_exists($dir)){
	
		foreach(scandir($dir) as $f) {
		
			if(!$f || $f[0] == '.') {
				continue; // Ignore hidden files
			}
                        
                        $path = $rootName . str_replace($root, '', $dir . '/' .$f);
			if(is_dir($dir . '/' . $f)) {

				// The path is a folder

				$files[] = array(
					"name" => $f,
					"type" => "folder",
					"path" => $path,
					"items" => self::scan($dir . '/' . $f, $root, $rootName) // Recursively get the contents of the folder
				);
			}
			
			else {

				// It is a file

				$files[] = array(
					"name" => $f,
					"type" => "file",
					"path" => $path,
					"size" => filesize($dir . '/' . $f) // Gets the size of this file
				);
			}
		}
	
	}
	return $files;
    }
}