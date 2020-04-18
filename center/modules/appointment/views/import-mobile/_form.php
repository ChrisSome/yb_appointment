<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model center\modules\appointment\models\ImportMobile */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="major-form">

    <?php $form = ActiveForm::begin([
        'id' => 'import-form', //声明小部件的id 即form的id
         'action' => '/appointment/import-mobile/create',
        'options' => [
            'class' => 'form-horizontal',

        ],
    ]); ?>

    <?= $form->field($model, 'mobile')->textInput(['maxlength' => true]) ?>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : '提交', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
