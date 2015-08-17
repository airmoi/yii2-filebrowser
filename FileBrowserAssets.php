<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace airmoi\yii2filebrowser;

use yii\web\AssetBundle;
/**
 * Description of FileBrowserAssets
 *
 * @author romain
 */
class FileBrowserAssets extends AssetBundle {
    
    /**
     * @inheritdoc
     */
    public $depends = [
        'yii\web\JqueryAsset'
    ];
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = __DIR__ . '/assets';
        $this->js =  ['js/yii2filebrowser.js'];
        $this->css =  ['css/styles.css'];
        parent::init();
    }
}
