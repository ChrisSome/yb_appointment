<?php
/**
 * Created by PhpStorm.
 * User: sihuo
 * Date: 2018/6/29
 * Time: 21:58
 */

use center\widgets\Alert;
use yii\helpers\Html;

$this->title = Yii::t('app', '导入');

$typeArr = [
    '1' => Yii::t('app', 'batch excel import'),
    '2' => Yii::t('app', 'batch excel update'),
    '3' => Yii::t('app', 'batch excel delete'),
];

?>

<div class="page page-table">
    <?= Alert::widget() ?>
    <section class="panel panel-default">
        <div class="panel-heading"><strong><span class="glyphicon glyphicon-th-large"></span>
                导入历史数据预览
            </strong></div>
        <div class="panel-body">

            <div style="overflow-x: auto;">
                <table class="table table-bordered table-striped table-responsive">
                    <thead>
                    <tr>
                        <th nowrap="nowrap"><?= Yii::t('app', 'Mobile') ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($model->excelData as $key => $value): ?>
                        <?php if ($key == 1) continue ?>
                        <?php if ($key >= 12) break ?>
                        <tr>
                            <?php foreach ($value as $k => $v):?>
                                <td><?= $v ?></td>
                            <?php endforeach ?>
                        </tr>
                    <?php endforeach ?>
                    </tbody>
                </table>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <?= Yii::t('app', 'batch excel help24') ?>
                </div>
            </div>
            <div class="divider-md"></div>

            <?php $form = \yii\bootstrap\ActiveForm::begin(['options' => [
            ], 'id' => '#w1', 'action' => 'operate']) ?>
            <div class="row">
                <div class="col-md-12">
                    <?= Html::submitInput(Yii::t('app', 'confirm'), ['name' => 'confirm', 'class' => 'btn btn-success']) ?>
                    <?= Html::a(Yii::t('app', 'cancel'), null, ['class' => 'btn btn-default', 'onclick' => 'window.history.back()']) ?>
                </div>
            </div>
            <?php $form->end() ?>
        </div>
    </section>
</div>
