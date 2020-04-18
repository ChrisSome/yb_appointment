<?php

use center\widgets\Alert;
use center\assets\ZTreeAsset;
use yii\helpers\Html;

$session = Yii::$app->session->get('batch_excel');
$this->title = Yii::t('app', 'report/financial/add');
$params = [
    'selectAddField' => isset($session['addSelectField']) ? $session['addSelectField'] : ['user_name', 'user_password', 'group_id', 'products_id'],
    'selectEditField' => isset($session['editSelectField']) ? $session['editSelectField'] : ['user_name'],
    'selectExportField' => isset($session['exportSelectField']) ? $session['exportSelectField'] : ['user_id', 'user_name', 'user_real_name'],
];
$AttributesList = $model->getAttributesList();
//权限
$canAdd = Yii::$app->user->can('user/batch/_excelAdd');
$canEdit = Yii::$app->user->can('user/batch/_excelEdit');
$canDelete = Yii::$app->user->can('user/batch/_excelDelete');
$canExport = Yii::$app->user->can('user/batch/_excelExport');
$canRefund = Yii::$app->user->can('user/batch/_excelRefund');
$canBuy = Yii::$app->user->can('user/batch/_excelBuy');
$canSettleAccounts = Yii::$app->user->can('user/batch/_excelSettleAccounts');
$canBatchRenew = Yii::$app->user->can('user/batch/buy');

if (!$canAdd && !$canEdit && !$canDelete && !$canExport) {
    exit('forbid');
}

//ztree
ZTreeAsset::register($this);
$this->registerJsFile('/js/ztree_select_multi.js', ['depends' => [center\assets\ZTreeAsset::className()]]);
?>
<style type="text/css">
    .ztree li a.curSelectedNode span {
        background-color: #0088cc;
        color: #fff;
        border-radius: 2px;
        padding: 2px;
    }
</style>

<div class="page page-table" data-ng-controller="batch-excel">
    <?= Alert::widget() ?>
    <section class="panel panel-default">
        <div class="panel-heading"><strong><span class="glyphicon glyphicon-th-large"></span> <?= $this->title ?>
            </strong></div>
        <div class="panel-body">
            <ul class="nav nav-tabs" id="tab" data-ng-init="batchType=1">
                <?php if ($canAdd): ?>
                    <li class="active"><a href="#" onclick="getType(1)"
                                          ng-click="batchType=1"><?= $this->title ?></a>
                    </li><?php endif ?>

            </ul>

            <?php $form = \yii\bootstrap\ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data'], 'id' => 'form', 'action' => 'preview']) ?>

            <?= Html::hiddenInput('batchType', "{{batchType}}") ?>

            <div class="tab-content">

                <div class="divider-md"></div>
                <div class="row">
                    <div class="col-md-12">
                        <!--操作流程-->
                        <div class="col-md-2"
                             ng-show="batchType==1 || batchType==2 || batchType==3 || batchType==4 || batchType==5 || batchType == 7">
                            <?= Yii::t('app', 'batch excel tip') ?>
                        </div>
                        <!--操作说明-->
                        <div class="col-md-2" ng-show="batchType==6">
                            <?= Yii::t('app', 'batch excel explain') ?>
                        </div>
                        <div class="col-md-10" ng-cloak ng-show="batchType==1 || batchType==2">
                            <?= Yii::t('app', 'batch excel help19'); ?>
                        </div>
                        <div class="col-md-10" ng-cloak ng-show="batchType==3 && deleteType!=2">
                            <?= Yii::t('app', 'batch excel help20'); ?>
                        </div>
                        <div class="col-md-10" ng-cloak ng-show="batchType==3 && deleteType==2">
                            <?= Yii::t('app', 'batch excel help20.1'); ?>
                        </div>
                        <div class="col-md-10" ng-cloak ng-show="batchType==4">
                            <?= Yii::t('app', 'batch excel help21'); ?>
                        </div>
                        <div class="col-md-10" ng-cloak ng-show="batchType==5">
                            <?= Yii::t('app', 'batch excel help33'); ?>
                        </div>
                        <div class="col-md-10" ng-cloak ng-show="batchType==6" style="color: red">
                            <?= Yii::t('app', 'batch excel help54'); ?>
                        </div>
                        <div class="col-md-10" ng-cloak ng-show="batchType==7">
                            <?= Yii::t('app', 'batch excel help56'); ?>
                        </div>
                    </div>
                </div>


                <!--设置项-->
                <div ng-cloak ng-show="batchType==2">
                    <div class="divider-xl"></div>
                    <div class="row">

                        <div class="col-md-12">
                            <div class="col-md-2">
                                <?= Yii::t('app', 'batch excel setting'); ?>
                            </div>
                            <div class="col-md-10">
                                <div class="col-md-12">
                                    <?= Html::radioList('setting[pay_where]', 1, [
                                        1 => Yii::t('app', 'batch excel pay_where1'),
                                        2 => Yii::t('app', 'batch excel pay_where2')
                                    ]) ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--操作产品动作-->
                    <div class="divider-xl"></div>
                    <div class="row">

                        <div class="col-md-12">
                            <div class="col-md-2">
                                <?= Yii::t('app', 'batch excel setting product action'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div ng-cloak ng-show="batchType==5">
                    <div class="divider-xl"></div>
                    <div class="row">

                        <div class="col-md-12">
                            <div class="col-md-2">
                                <?= Yii::t('app', 'batch excel setting'); ?>
                            </div>
                            <div class="col-md-10">
                                <div class="col-md-12">
                                    <?= Html::radioList('setting[refund_where]', 1, [
                                        1 => Yii::t('app', 'batch excel refund_where1'),
                                        2 => Yii::t('app', 'batch excel refund_where2')
                                    ]) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div ng-cloak ng-show="batchType==5">
                    <div class="divider-xl"></div>
                    <div class="row">

                        <div class="col-md-12">
                            <div class="col-md-2">
                                <?= Yii::t('app', 'batch_refund_checkout_setting'); ?>
                            </div>
                            <div class="col-md-10">
                                <div class="col-md-12">
                                    <?= Html::radioList('setting[refund_is_checkout]', 1, [
                                        1 => Yii::t('app', 'yes'),
                                        0 => Yii::t('app', 'no')
                                    ]) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!--勾选要操作的数据，用于新增-->
                <div ng-cloak ng-show="batchType==1">
                    <div class="divider-xl"></div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-2">
                                <?= Yii::t('app', 'batch excel select'); ?>
                            </div>
                            <div class="col-md-10">
                                <div class="col-md-10">
                                    <?= Html::checkboxList('addSelectField[]', isset($session['addSelectField']) ? $session['addSelectField'] : ['province_name', 'year'], $model->showField, ['class' => 'drag_inline']) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--购买的字段-->



                <!--下载模板-->
                <div ng-hide="deleteType == 2 || batchType == 4 || batchType == 8 || (batchType==5 && type_value == 2) || (batchType==7 && type_value == 2)"
                     class="excel">
                    <div class="divider-xl"></div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-2">
                                <?= Yii::t('app', 'batch excel download template'); ?>
                            </div>
                            <div class="col-md-10">
                                <?= Html::submitInput(Yii::t('app', 'batch excel download'), ['name' => 'download', 'class' => 'btn btn-default']) ?>
                                <span ng-cloak
                                      ng-show="batchType==1 || batchType==2 "> <?= Yii::t('app', 'batch excel help22'); ?> </span>
                                <span ng-cloak
                                      ng-show="batchType==3 || batchType==5 || batchType==6"> <?= Yii::t('app', 'batch excel help23'); ?> </span>
                            </div>
                        </div>
                    </div>
                </div>
                <!--上传文件-->
                <div ng-hide="deleteType == 2 || batchType == 4 || batchType == 8 ||  (batchType == 5 && type_value == 2) || (batchType == 7 && type_value == 2)"
                     class="excel">
                    <div class="divider-xl"></div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-2">
                                <?= Yii::t('app', 'batch excel upload'); ?>
                            </div>
                            <div class="col-md-2">
                                <input type="file" name="Zone[file]"
                                       title="<?= Yii::t('app', 'batch excel select file') ?>" data-ui-file-upload
                                       accept=".xls">
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="divider-xl"></div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-2"></div>
                            <div class="col-md-10">
                                <?= Html::submitInput(Yii::t('app', 'preview'), ['name' => 'preview', 'class' => 'btn btn-success']) ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="callout callout-info" ng-cloak ng-show="batchType==1 || batchType==2">
                    <h4><?= Yii::t('app', 'batch excel help help'); ?>：</h4>
                    <hr/>
                    <p class="text text-primary"><?= Yii::t('app', 'batch excel help font1'); ?></p>
                    <p class="text text-primary"><?= Yii::t('app', 'batch excel help font2'); ?></p>
                    <p style="line-height:28px;"><?= Yii::t('app', 'batch excel help font3'); ?></p>
                    <p class="text text-danger"><?= Yii::t('app', 'batch excel help font4'); ?></p>
                </div>


            </div>
            <?php $form->end() ?>
        </div>
    </section>
</div>

<?php
$this->registerJs("
    $('#tab a').click(function (e) {
          e.preventDefault();//阻止a链接的跳转行为
          $(this).tab('show');//显示当前选中的链接及关联的content

    })
    createTree('zTreeAddUser');
 ");
?>