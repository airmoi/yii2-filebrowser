<?php
namespace airmoi\yii2filebrowser;


use Yii;
use yii\base\InvalidConfigException;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'airmoi\yii2filebrowser\controllers';

    public $tempDir;
    
    private static $_config;
    
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
        if(!empty(self::$_config))
            return self::$_config;
        
        if(!self::$_config = Yii::$app->getSession()->get('yii2-filebrowser')[$token])
            throw new \yii\web\BadRequestHttpException('Invalid token provided');
        
        return self::$_config;
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
                        $encodedName = utf8_encode($f);
                        $encodedDir = utf8_encode($dir) ;
			if(!$f || $f[0] == '.') {
				continue; // Ignore hidden files
			}
                        
                        $path = $rootName . str_replace($root, '', $encodedDir . '/' .$encodedName);
			if(is_dir($dir . '/' . $f)) {

				// The path is a folder

				$files[] = array(
					"name" => $encodedName,
					"type" => "folder",
					"path" => $path,
					"items" => self::scan($dir . '/' . $f, $root, $rootName) // Recursively get the contents of the folder
				);
			}
			
			else {

				// It is a file

				$files[] = array(
					"name" => $encodedName,
					"type" => "file",
					"path" => $path,
					"size" => @filesize($dir . '/' . $f) // Gets the size of this file
				);
			}
		}
	
	}
	return $files;
    }
    
    
    public static function getAbsolutePath($token, $file) {
        $config = Module::getConfig($token);
        $relativePath = explode('/', trim(utf8_decode($file)));
        array_shift($relativePath);
        return $config['rootPath'].'/'. implode('/', $relativePath);
    }
    
    /**
     * Removes a directory (and all its content) recursively.
     * @param string $dir the directory to be deleted recursively.
     * @param array $options options for directory remove. Valid options are:
     *
     * - traverseSymlinks: boolean, whether symlinks to the directories should be traversed too.
     *   Defaults to `false`, meaning the content of the symlinked directory would not be deleted.
     *   Only symlink would be removed in that default case.
     */
    public static function removeDirectory($dir, $options = [])
    {
        if (!is_dir($dir)) {
            return false;
        }
        if (isset($options['traverseSymlinks']) && $options['traverseSymlinks'] || !is_link($dir)) {
            if (!($handle = opendir($dir))) {
                return false;
            }
            while (($file = readdir($handle)) !== false) {
                if ($file === '.' || $file === '..') {
                    continue;
                }
                $path = $dir . DIRECTORY_SEPARATOR . $file;
                if (is_dir($path)) {
                    static::removeDirectory($path, $options);
                } else {
                    unlink($path);
                }
            }
            closedir($handle);
        }
        if (is_link($dir)) {
            return unlink($dir);
        } else {
            return rmdir($dir);
        }
    }
    
    public static function canDelete($token){
        return self::getConfig($token)['permissions']['delete'];
    }
    
    public static function canCreateDir($token){
        return self::getConfig($token)['permissions']['createdir'];
    }
    
    public static function canUpload($token){
        return self::getConfig($token)['permissions']['upload'];
    }
}