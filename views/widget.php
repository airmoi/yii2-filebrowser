<?php
use yii\helpers\Html;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $widget airmoi\yii2filebrowser\FileBrowser */

?>
<div class="filemanager" id="<?= $widget->id?>">
    <?php if($widget->allowSearch) { 
    ?><div class="search"><input type="search" placeholder="Find a file.." /></div><?php }

    if($widget->showBreadcrumbs) { ?><div class="breadcrumbs"></div><?php } ?>

    <ul class="items"></ul>

    <div class="nothingfound">
            <div class="nofiles"></div>
            <span>Aucun fichier.</span>
    </div>
</div>
