<?php
use yii\helpers\Html;
use yii\widgets\ListView;
use yii\bootstrap\ActiveForm;
use airmoi\yii2filebrowser\models\UploadForm;
/* @var $this yii\web\View */
/* @var $widget airmoi\yii2filebrowser\FileBrowser */

?>
<div class="filemanager" id="<?= $widget->id?>">
    <?php if($widget->allowSearch) { 
    ?><div class="search"><input type="search" placeholder="Rechercher..." /></div><?php }

    if($widget->showBreadcrumbs) { ?><div class="breadcrumbs"></div><?php } ?>

    <ul class="items"></ul>

    <div class="nothingfound">
            <div class="nofiles"></div>
            <span>Aucun fichier.</span>
    </div>
    <div class="upload-box">
        <?php $form = ActiveForm::begin(['method'=>'post', 'options' => ['enctype' => 'multipart/form-data']]) ?>

            <?= $form->field(new UploadForm(), 'file')->fileInput() ?>

            <button>Submit</button>

        <?php ActiveForm::end() ?>
    </div>
    <div class="newdir">
        <?php $form = ActiveForm::begin(['action' => \yii\helpers\Url::to(['filebrowser/browser/createdir']), 'method'=>'get']) ?>

            <?= Html::input('text', 'dirname') ?>

            <button>Submit</button>

        <?php ActiveForm::end() ?>
    </div>
    
    <div id="folder-tpl" style="display:none">
        <li class="folders">
            <a class="folders">
                <span class="icon folder"></span>
                <span class="name"></span>
                <span class="details"></span>
                <button type="button" class="btn btn-default delete" aria-label="Supprimer" title="Supprimer">
                    <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                </button>
            </a>
        </li>
    </div>
    <div id="file-tpl" style="display:none">
        <li class="files">
            <a class="files">
                <span class="icon file"></span>
                <span class="name"></span>
                <span class="details"></span>
                <button type="button" class="btn btn-default delete" aria-label="Supprimer" title="Supprimer">
                    <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                </button>
            </a>
        </li> 
    </div>
</div>
