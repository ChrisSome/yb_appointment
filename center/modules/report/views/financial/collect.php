<?php
/**
 * Created by PhpStorm.
 * User: sihuo
 * Date: 2018/4/22
 * Time: 11:21
 */

use center\widgets\Alert;
use yii\helpers\Html;

$this->title = Yii::t('app', 'report/financial/collect');
?>
<div class="page page-table">
    <?= Alert::widget() ?>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <form name="form_constraints" action="<?= \yii\helpers\Url::to(['collect']) ?>"
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



