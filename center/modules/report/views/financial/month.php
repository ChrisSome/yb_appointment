<?php
/**
 * Created by PhpStorm.
 * User: sihuo
 * Date: 2018/3/19
 * Time: 20:12
 */

use yii\helpers\Html;
use center\extend\Tool;
use center\assets\ReportAsset;
use yii\bootstrap\ActiveForm;

ReportAsset::newEchartsJs($this);
if (Yii::$app->session->get('searchBillingField')) {
    $searchField = array_keys(Yii::$app->session->get('searchBillingField'));
} else {
    $searchField = [];
}

$this->title = Yii::t('app', Yii::$app->requestedRoute);
?>

<div class="panel panel-default">
    <div class="panel-body" style="padding: 10px">
        <?php
        $form = ActiveForm::begin([
            'layout' => 'horizontal',
            'fieldConfig' => [
                'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}"
            ],
        ]);
        ?>
        <div class="col-md-2">
            <?=
            $form->field($model, 'province', ['template' => '<div class="col-sm-12">{input}</div>'])
                ->textInput(
                    [
                        'value' => isset($model->province) ? $model->province : '',
                        'class' => 'form-control',
                        'placeHolder' => Yii::t('app', '省份')
                    ]);
            ?>
        </div>
        <div class="col-md-2">
            <?=
            $form->field($model, 'start_At', ['template' => '<div class="col-sm-12">{input}</div>'])
                ->textInput(
                    [
                        'value' => isset($model->start_At) ? $model->start_At : '',
                        'class' => 'form-control',
                        'placeHolder' => Yii::t('app', 'start time')
                    ]);
            ?>
        </div>


        <div class="col-md-2">
            <?=
            $form->field($model, 'stop_At', [
                'template' => '<div class="col-sm-12">{input}</div>'
            ])->textInput(
                [
                    'value' => isset($model->stop_At) ? $model->stop_At : '',
                    'class' => 'form-control',
                    'placeHolder' => Yii::t('app', 'end time')
                ]);
            ?>
        </div>
        <?= Html::submitButton(Yii::t('app', 'search'), ['class' => 'btn btn-success']) ?>

        <div class="col-sm-12" style="text-align: left;color: #ffffff;">
            <?= $form->errorSummary($model); ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>

<div class="row" style="border:none;margin: 0;padding:0;margin-top:10px;overflow-x: auto;">
    <section class="panel panel-default table-dynamic" style="margin:0;padding:0;">
        <div class="panel-heading"><strong><span
                        class="glyphicon glyphicon-th-large"></span> <?= Yii::t('app', 'search result') ?></strong>
        </div>
        <div style="clear:both;"></div>
        <?php if ($data['code'] == 1) : ?>
            <?= $this->render('/map/pie', [
                'data' => $data,
                'model' => $model,
            ]) ?>
        <?php else: ?>
            <div class="panel-body">
                <?= Yii::t('app', 'no record') ?>
            </div>
        <?php endif ?>
</div>

</section>
</div>
