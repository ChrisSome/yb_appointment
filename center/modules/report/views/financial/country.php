<?php
use yii\helpers\Html;
use center\widgets\Alert;
use yii\widgets\LinkPager;

$this->title = Yii::t('app', 'report/financial/index');

$canAdd = Yii::$app->user->can('message/news/add');
$canList = Yii::$app->user->can('message/news/list');
$canView = Yii::$app->user->can('message/news/view');
$canDelete = Yii::$app->user->can('message/news/del');
$canEdit = Yii::$app->user->can('message/news/edit');

//权限操作
$isOnlyAdd = $canAdd && !$canList;
$errors = $model->getErrors();
$attr = $model->getAttributesList();
?>
<div class="page page-table">
    <?= Alert::widget() ?>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <form name="form_constraints" action="<?=\yii\helpers\Url::to(['country'])?>" class="form-horizontal form-validation" method="get">
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
                                    $content = Html::dropDownList($key, isset($params[$key]) ? $params[$key] : '', $value['list'], ['class' => 'form-control',]);
                                }
                                //日期插件格式
                                else  if ($key == 'birth') {
                                    $content = Html::input('text', $key, isset($params[$key]) ? $params[$key] : '', [
                                        'class' => 'form-control inputDate',
                                        'placeHolder' => isset($value['label']) ? $value['label'] : '',
                                        'id' => isset($value['id']) ? $value['id'] : '',
                                    ]);
                                }
                                //普通文本格式
                                else {
                                    $content = Html::input('text', $key, isset($params[$key]) ? $params[$key] : '', [
                                        'class' => 'form-control',
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
                        <?= Html::submitButton(Yii::t('app', 'search'), ['class' => 'btn btn-success']) ?>
                    </div>

            </div>
        </div>
        <div class="panel panel-default" style="margin:0;padding:0;overflow-x: hidden;">
            <div class="panel-heading"><strong><span
                        class="glyphicon glyphicon-list-alt text-small"></span> <?= Yii::t('app', 'list') ?></strong>
                <div class="pull-right">
                    <a type="button" class="btn btn-primary btn-sm" style="margin-top: -5px;"
                       href="<?= Yii::$app->urlManager->createUrl(array_merge(['/report/financial/export-country'], $params, ['export' => 'excel'])) ?>"><span
                                class="glyphicon glyphicon-log-out"></span><?= Yii::t('app', 'excel export') ?></a>
                </div>
            </div>

            <?php if (!empty($list)) : ?>

                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th><?= Yii::t('app', '国家名称') ?></th>
                        <th><?= Yii::t('app', '所在州') ?></th>
                        <th><?= Yii::t('app', '年份') ?></th>
                        <th><?= Yii::t('app', 'gdp(亿元)') ?></th>
                        <th><?= Yii::t('app', '排名') ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($list as $id => $one) { ?>
                        <tr>
                            <td></td>
                            <td><?= $one['country']?></td>
                            <td><?= $one['zhou']?></td>
                            <td><?= $one['year']?></td>
                            <td><?= $one['number'].'美元'?></td>
                            <td><?= $one['sort_order']?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>

                <div class="divider"></div>

                <footer class="table-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <?=
                            Yii::t('app', 'pagination show1', [
                                'totalCount' => $pagination->totalCount,
                                'totalPage' => $pagination->getPageCount(),
                                'perPage' => $pagination->pageSize,
                            ])?>

                        </div>
                        <div class="col-md-6 text-right">
                            <?php
                            echo LinkPager::widget(['pagination' => $pagination, 'maxButtonCount' => 5]);
                            ?>
                        </div>
                    </div>
                </footer>
                </form>

            <?php else: ?>
                <div class="panel-body">
                    <?= Yii::t('app', 'no record') ?>
                </div>
            <?php endif ?>
        </div>
    </div>
