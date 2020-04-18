<?php
/**
 * Created by PhpStorm.
 * User: sihuo
 * Date: 2018/6/29
 * Time: 21:22
 */

use yii\helpers\Html;
use center\widgets\Alert;
use yii\bootstrap\ActiveForm;

$this->title = Yii::t('app', 'appointment/import-mobile/batch');
?>

<div class="page page-table">
    <?= Alert::widget() ?>

    <section class="panel panel-default">
        <div class="panel-heading">
            <strong><span class="glyphicon glyphicon-th-large"></span> <?= Yii::t('app', 'Batch Import Mobile') ?></strong>

        </div>
        <div class="panel-body">
            <?php $form = \yii\bootstrap\ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data'], 'id' => 'form', 'action' => 'preview']) ?>

            <?= Html::hiddenInput('batchType', "{{batchType}}") ?>

            <div class="divider-md"></div>
            <div class="row">
                <!--操作流程-->
                <div class="col-md-2">
                    <?= Yii::t('app', '操作流程') ?>
                </div>

                <div class="col-md-10 text-info">
                    1. 填充历史数据》2. 导入预览 》3.确定导入
                </div>
            </div>
            <!--下载模板-->
         <!--   <div class="excel">
                <div class="divider-xl"></div>
                <div class="row">
                    <div class="col-md-2">
                        <?/*= Yii::t('app', '下载模板'); */?>
                    </div>
                    <div class="col-md-10">
                        <?/*= Html::submitInput(Yii::t('app', '下载模板'), ['name' => 'download', 'class' => 'btn btn-default']) */?>
                    </div>
                </div>
            </div>-->
            <!--上传文件-->
            <div class="excel">
                <div class="divider-xl"></div>
                <div class="row">
                    <div class="col-md-2">
                        <?= Yii::t('app', '文件上传'); ?>
                    </div>
                    <div class="col-md-2">
                        <input type="file" name="ImportMobile[file]" title="<?= Yii::t('app', '选择文件') ?>"
                               data-ui-file-upload accept=".xls">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="divider-xl"></div>
                <div class="col-md-2"></div>
                <div class="col-md-10">
                    <?= Html::submitInput(Yii::t('app', 'preview'), ['name' => 'preview', 'class' => 'btn btn-success']) ?>
                    <?= Html::a(Yii::t('app', '返回'), 'index', ['class' => 'btn btn-info']) ?>
                </div>
            </div>
            <p class="text text-danger">
                注意：  仅支持标准xls,csv, xlsx格式文件
            </p>
        </div>
        <?php $form->end() ?>
    </section>
</div>
