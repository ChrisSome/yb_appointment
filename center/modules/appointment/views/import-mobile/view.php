<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model center\modules\appointment\models\ImportMobile */

$this->title = "号码详情";
$this->params['breadcrumbs'][] = ['label' => 'Majors', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$canDelete = Yii::$app->user->can('appointment/import-mobile/delete');
?>
<div class="major-view">
    <h4><?= Html::encode($this->title) ?></h4>

    <div class="col-lg-10">
        <p>
            <?php if ($canDelete): ?>
                <?= Html::a('删除', ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => '你确定要删除该号码吗？ ',
                        'method' => 'post',
                    ],
                ]) ?>
            <?php endif; ?>
            <?= Html::a('返回列表', ['index',], [
                'class' => 'btn btn-info',
            ]) ?>
        </p>

        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                'mobile',
                [
                    'attribute' => 'import_time',
                    'value' => date('Y-m-d H:i:s', $model->import_time)
                ],
            ],
        ]) ?>
    </div>
</div>
