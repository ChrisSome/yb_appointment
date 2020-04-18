<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use center\widgets\Alert;
use yii\helpers\Url;

$canList = Yii::$app->user->can('user/complaints/index');
$canEdit = Yii::$app->user->can('user/complaints/edit');
$canListAll = Yii::$app->user->can('user/complaints/index-all');

if ($action != 'add') {
    $this->title = \Yii::t('app', 'user/complaints/' . $action);
}
/* @var $this yii\web\View */
/* @var $model center\modules\user\models\UserCloundComplaints */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="col-md-12">
    <?= Alert::widget() ?>
    <div class="panel-heading"><strong>
            <?php if ($action == 'edit') {
                echo '<span class="glyphicon glyphicon-edit"></span> ';
                echo Yii::t('app', 'edit');
            } else if ($action == 'view' || $action == 'view-all') {
                echo '<span class="glyphicon glyphicon-check"></span> ';
                echo Yii::t('app', 'view');
            } else if ($action == 'add') {
                echo '<span class="glyphicon glyphicon-plus"></span> ';
                echo Yii::t('app', 'add');
            } ?>
        </strong></div>

    <div class="panel panel-default">
        <div class="panel-body">
            <?php if ($action == 'view' || $action == 'edit'): ?>
                <?php $form = ActiveForm::begin([
                    'layout' => 'horizontal',
                    'fieldConfig' => [
                        'template' => "{label}\n{beginWrapper}\n{input}\n{error}\n{hint}\n{endWrapper}",
                        'horizontalCssClasses' => [
                            'label' => 'col-sm-2',
                            'offset' => 'col-sm-offset-4',
                            'wrapper' => 'col-sm-8',
                            'error' => '',
                            'hint' => '',
                        ],
                    ],
                ]); ?>
            <?php else: ?>
                <?php $form = ActiveForm::begin(['action' => yii\helpers\Url::to('create'), 'options' => ['enctype' => 'multipart/form-data'],
                        'layout' => 'horizontal',
                        'fieldConfig' => [
                            'template' => "{label}\n{beginWrapper}\n{input}\n{error}\n{hint}\n{endWrapper}",
                            'horizontalCssClasses' => [
                                'label' => 'col-sm-2',
                                'offset' => 'col-sm-offset-4',
                                'wrapper' => 'col-sm-8',
                                'error' => '',
                                'hint' => '',
                            ],
                        ],]
                ); ?>

            <?php endif; ?>
            <?= $form->field($model, 'domain')->textInput(['maxlength' => true, 'class' => 'form-control']) ?>
            <?= $form->field($model, 'status')->inline()->radioList([
                0 => '禁用',
                1 => '启用'
            ], ['prompt' => Yii::t('app', 'Please Select')]) ?>


            <div class="form-group" style="margin-left:220px;">
                <?php if ($action == 'add'): ?>
                    <?= Html::submitButton(Yii::t('app', 'save'), ['class' => 'btn btn-success']) ?>
                <?php endif; ?>
                <?php if ($canEdit && $action == 'edit'): ?>
                    <?= Html::submitButton(Yii::t('app', 'save'), ['class' => 'btn btn-success']) ?>
                <?php endif; ?>
                <?php if ($canList && $action == 'view'): ?>
                    <?= Html::a(Html::button(Yii::t('app', 'goBack'), ['class' => 'btn btn-primary']),
                        ['index']
                    ) ?>
                <?php endif; ?>
                <?php if ($canListAll && $action == 'edit' || $action == 'view-all'): ?>
                    <?= Html::a(Html::button(Yii::t('app', 'goBack'), ['class' => 'btn btn-primary']),
                        ['index-all']
                    ) ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
