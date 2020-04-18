<?php
use yii\helpers\Html;

$lang = Yii::$app->language;
?>

<li class="dropdown langs text-normal">
    <?= Html::a(Yii::t('app', 'version') . ': 4.1.0', 'javascript:;'); ?>
</li>