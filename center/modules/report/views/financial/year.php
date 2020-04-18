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
use center\widgets\Alert;

ReportAsset::newEchartsJs($this);
if (Yii::$app->session->get('searchBillingField')) {
    $searchField = array_keys(Yii::$app->session->get('searchBillingField'));
} else {
    $searchField = [];
}

$this->title = Yii::t('app', Yii::$app->requestedRoute);
?>

<div class="page page-table">
    <?= Alert::widget() ?>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <form name="form_constraints" action="<?= \yii\helpers\Url::to(['year']) ?>"
                      class="form-horizontal form-validation" method="get">
                    <div class="panel-body">
                        <div class="form-group">
                            <?= Html::radioList('type', isset($params['type']) ? $params['type'] : 'gdp',  [
                                'gdp' => 'gdp数据',
                                'country' => '国家gdp排名'
                            ],['class' => 'form-control', 'id'=>'type']) ?>
                        </div>
                        <div class="form-group" id="year" style="display:none;">
                            <?= Html::input('text', 'year',  isset($params['year']) ? $params['year'] : 2016,['class' => 'form-control', 'placeHolder' => '年份']) ?>
                        </div>
                        <?= Html::submitButton(Yii::t('app', 'search'), ['class' => 'btn btn-success']) ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row" style="border:none;margin: 0;padding:0;margin-top:10px;overflow-x: auto;">
    <section class="panel panel-default table-dynamic" style="margin:0;padding:0;">
        <div class="panel-heading"><strong><span
                        class="glyphicon glyphicon-th-large"></span> <?= Yii::t('app', 'search result') ?></strong>
        </div>
        <div style="clear:both;"></div>
        <?php if ($data['code'] == 1) : ?>
            <?= $this->render('/map/bytes', [
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
<?php
$js = <<< JS
$('#type').change(function() {
    var type = $("input[name='type']:checked").val();
    if (type == 'country') {
        $('#year').show();
    } else {
        $('#year').hide();
    }
  
})
$(function() {
    var type = $("input[name='type']:checked").val();
    if (type == 'country') {
        $('#year').show();
    } else {
        $('#year').hide();
    }
})
JS;
$this->registerJs($js);
?>