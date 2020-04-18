<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\forecast\models\WeatherType */

$this->title = Yii::t('app', 'appointment/domain/create');
$this->params['breadcrumbs'][] = ['label' => 'Weather Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="weather-type-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'action' => 'add'
    ]) ?>

</div>
