<?php
use yii\helpers\Html;
use yii\widgets\ListView;
use yii\bootstrap\ActiveForm;
use airmoi\yii2filebrowser\models\UploadForm;
/* @var $this yii\web\View */
/* @var $widget airmoi\yii2filebrowser\FileBrowser */

?>
<div class="filemanager" id="<?= $widget->id?>">
    <header>
        <div class="row">
            <?php 
            if($widget->showBreadcrumbs) { 
                ?><div class="breadcrumbs col-sm-8">
                <h2 class="folderName">Dossier personnel</h2>
            </div><?php 
            } 
            ?>
            <div class="tools col-sm-4">
                <?php if($widget->allowSearch) { ?><div class="search">
                    <input class="form-control form-control-search" type="search" placeholder="Rechercher…">
                </div><?php } ?>
                <div class="items-view-switcher">
                    <button class="btn btn-default active"><span class="fa fa-fw fa-th-large"></span></button>
                    <button class="btn btn-default"><span class="fa fa-fw fa-th-list"></span></button>
                </div>
            </div>
        </div>

    </header>
    
    <ul class="items items-icons animated"></ul>

    <div class="nothingfound">
        <div class="nofiles"></div>
        <span>Aucun fichier.</span>
    </div>
    <div class="upload-box">
        <?php $form = ActiveForm::begin(['id' => "upload-file-form", 'method'=>'post', 'options' => ['enctype' => 'multipart/form-data']]) ?>

            <?= $form->field(new UploadForm(), 'file')->fileInput() ?>

            <button>Submit</button>
            
        <?php ActiveForm::end() ?>
        <div class="row">
            <div id="upload-progress" class="col-lg-6" style="display:none">
                <p>Envoi de votre fichier en cours...</p>
                <div class="progress" >
                    <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                      <span class="sr-only">Upload en cours...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="newdir">
        <?php $form = ActiveForm::begin(['action' => \yii\helpers\Url::to(['filebrowser/browser/createdir']), 'method'=>'get']) ?>
            <?= Html::label('Créer un dossier', 'dirname') ?>
            <div class="input-group col-xs-8 col-sm-4">
                <div class="input-group-btn">
                    <?= Html::input('text', 'dirname', '', ['class'=>'form-control']) ?>
                    <button class="btn btn-primary" type="submit">Envoyer</button>
                </div>
            </div>

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
