<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
<div class="major-create">
    <div class="col-lg-10 col-md-10">
        <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($model, 'mobile')->textInput(['maxlength' => true, 'id' => 'mobile']) ?>
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? '创建' : '修改', ['class' => $model->isNewRecord ? 'btn btn-success add' : 'btn btn-primary edit']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
