<?php
/**
 * Created by PhpStorm.
 * User: sihuo
 * Date: 2016/5/11
 * Time: 16:23
 */


use yii\widgets\LinkPager;
use yii\helpers\Html;
use center\widgets\Alert;

$this->title = \Yii::t('app', 'appointment/import-mobile/index');


$canAdd = Yii::$app->user->can('appointment/import-mobile/create');
$canList = Yii::$app->user->can('appointment/import-mobile/index');
$canView = Yii::$app->user->can('appointment/import-mobile/view');
$canDelete = Yii::$app->user->can('appointment/import-mobile/delete');
$canBatch = Yii::$app->user->can('appointment/import-mobile/batch');

//权限操作
$isOnlyAdd = $canAdd && !$canList;
$errors = $model->getErrors();
?>
<div class="page page-table">
    <?= Alert::widget() ?>
    <form name="form_constraints" action="<?= \yii\helpers\Url::to(['index']) ?>"
          class="form-horizontal form-validation" method="get" onsubmit="return checkPerPage();">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">

                        <?php
                        if ($model->searchInput) {
                            $searchInput = $model->searchInput;
                            $count = count($searchInput);
                            $i = 0;
                            foreach ($model->searchInput as $key => $value) {
                                if ($i % 6 == 0) {
                                    echo '<div class="form-group">';
                                }
                                //列表形式
                                if (isset($value['list']) && !empty($value['list'])) {
                                    $content = Html::dropDownList($key, isset($params[$key]) ? $params[$key] : '', $value['list'], ['class' => 'form-control']);
                                } //普通文本格式
                                else {
                                    $content = Html::input('text', $key, isset($params[$key]) ? $params[$key] : '', [
                                        'class' => 'form-control' . (isset($value['class']) ? $value['class'] : ''),
                                        'placeHolder' => isset($value['label']) ? $value['label'] : '',
                                        'id' => isset($value['id']) ? $value['id'] : '',
                                    ]);
                                }

                                echo Html::tag('div', $content, ['class' => 'col-md-2']);

                                $i++;
                                if ($i % 6 == 0 || $i == $count) {
                                    echo '</div>';
                                }
                            }
                        }
                        ?>
                    </div>
                    <div style="margin-top: -10px; margin-bottom: 10px; margin-left: 10px;">
                        <?= Html::submitButton(Yii::t('app', 'search'), ['class' => 'btn btn-success']) ?>
                        <?php if ($canBatch): ?>
                            <?= Html::a(Html::button(Yii::t('app', 'appointment/import-mobile/batch'), ['class' => 'btn btn-warning']), 'batch') ?>
                        <?php endif; ?>
                    </div>


                </div>
            </div>
        </div>
    </form>
    <?php
    //权限操作
    if ($canAdd):
        ?>

        <?php if (!$isOnlyAdd): ?>
        <button type="button" class="btn btn-w-md btn-gap-v btn-primary" ng-click="isCollapsed = !isCollapsed">
            <?= Yii::t('app', 'add') ?>
            <i ng-show="!isCollapsed" class="fa fa-chevron-down"></i>
            <i ng-show="isCollapsed" class="fa fa-chevron-up"></i>
        </button>
    <?php endif ?>

        <div class="panel panel-default" data-ng-controller="packageController"
             <?php if (!empty($errors)){ ?>ng-cloak collapse="isCollapsed" <?php }elseif (!$isOnlyAdd){ ?>ng-cloak
             collapse="!isCollapsed"<?php } ?>>
            <div class="panel-heading">
                <strong><span class="glyphicon glyphicon-plus"></span> <?= Yii::t('app', 'add'); ?></strong>
            </div>
            <div class="panel-body">
                <?php
                //展现表单
                echo $this->render('_form', [
                    'model' => $model,
                ]);
                ?>
            </div>
        </div>
    <?php endif ?>


    <?php
    //是否有权限
    if ($canList):
        ?>
        <div class="panel panel-default">
            <div class="panel-heading"><strong><span
                            class="glyphicon glyphicon-list-alt text-small"></span> <?= Yii::t('app', 'list') ?>
                </strong>
            </div>
            <div style="clear:both;"></div>
            <div class="col-md-12">
                <section class="panel panel-default table-dynamic">
                    <div style="clear:both;"></div>

                    <?php if (!empty($list)): ?>
                    <div style="overflow-x: auto;">
                        <table class="table table-bordered table-striped table-responsive">
                            <thead>
                            <tr>
                                <?php
                                if (isset($params['showField']) && $params['showField']) {
                                    //不需要排序的字段
                                    $no_sort_field = ['ip', 'products_id', 'user_available', 'user_balance', 'user_name', 'user_real_name', 'user_online_status'];
                                    foreach ($params['showField'] as $value) {
                                        if (in_array($value, $no_sort_field)) {
                                            echo '<th nowrap="nowrap"><div class="th">' . $model->searchField[$value] . '</div></th>';
                                        } else {
                                            $newParams = $params;
                                            echo '<th nowrap="nowrap"><div class="th">';
                                            echo $model->searchField[$value];

                                            $newParams['orderBy'] = $value;
                                            array_unshift($newParams, '/appointment/import-mobile/index');

                                            //上面按钮
                                            $newParams['sort'] = 'asc';
                                            $upActive = (isset($params['orderBy'])
                                                && $params['orderBy'] == $value
                                                && isset($params['sort'])
                                                && $params['sort'] == 'asc') ? 'active' : '';
                                            echo Html::a('<span class="glyphicon glyphicon-chevron-up ' . $upActive . '"></span>', $newParams);

                                            //下面按钮
                                            $newParams['sort'] = 'desc';
                                            $downActive = (isset($params['orderBy'])
                                                && $params['orderBy'] == $value
                                                && isset($params['sort'])
                                                && $params['sort'] == 'desc') ? 'active' : '';
                                            echo Html::a('<span class="glyphicon glyphicon-chevron-down ' . $downActive . '"></span>', $newParams);

                                            echo '</div></th>';
                                        }
                                    }
                                }

                                ?>
                                <?php if($canDelete || $canView): ?>
                                    <th nowrap="nowrap">
                                        <div class="th"><?= Yii::t('app', 'operate') ?></div>
                                    </th>
                                <?php endif;?>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($list as $id => $one) { ?>
                                <tr>
                                    <?php foreach ($one as $k => $v) {
                                        if ($k == 'import_time') {
                                            $v = date('Y-m-d H:i:s', $v);
                                        }
                                        echo "<td>{$v}</td>";
                                    }
                                    ?>

                                    <?php
                                    if ($canDelete || $canView):
                                        ?>
                                        <td>
                                            <?php if ($canView) {
                                                echo Html::a(Html::button(Yii::t('app', 'view'), ['class' => 'btn btn-warning btn-xs']),
                                                    ['view', 'id' => $one['id']],
                                                    ['title' => Yii::t('app', 'view')]);
                                            } ?>
                                            <?php if ($canDelete) {
                                                echo Html::a(Html::button(Yii::t('app', 'delete'), ['class' => 'btn btn-danger btn-xs']),
                                                    ['delete', 'id' => $one['id']], [
                                                        'title' => Yii::t('app', 'delete'),
                                                        'data' => [
                                                            'method' => 'post',
                                                            'confirm' => Yii::t('app', '你确定要删除此号码么'),
                                                        ],
                                                    ]);
                                            } ?>
                                        </td>
                                    <?php endif ?>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="divider"></div>

                    <footer class="table-footer">
                        <div class="row">
                            <div class="col-md-6">
                                <?=
                                Yii::t('app', 'pagination show1', [
                                    'totalCount' => $pagination->totalCount,
                                    'totalPage' => $pagination->getPageCount(),
                                    'perPage' => $pagination->pageSize,
                                ]) ?>

                            </div>
                            <div class="col-md-6 text-right">
                                <?php
                                echo LinkPager::widget(['pagination' => $pagination, 'maxButtonCount' => 5]);
                                ?>
                            </div>
                        </div>
                    </footer>
                </section>
            </div>

            <?php else: ?>
                <div class="panel-body">
                    <?= Yii::t('app', 'no record') ?>
                </div>
            <?php endif;
            ?>
        </div>
    <?php endif ?>
</div>
