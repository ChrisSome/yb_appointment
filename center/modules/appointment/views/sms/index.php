<?php
/**
 * Created by PhpStorm.
 * User: sihuo
 * Date: 2017/10/28
 * Time: 11:05
 */

use yii\helpers\Html;
use center\widgets\Alert;
use yii\widgets\LinkPager;

$can = Yii::$app->user->can('appointment/ip-manage/index');
$canAdd = Yii::$app->user->can('appointment/ip-manage/create');
$canEdit = Yii::$app->user->can('appointment/ip-manage/update');
$canDelete = Yii::$app->user->can('appointment/ip-manage/delete');

$this->title = Yii::t('app', 'appointment/ip-manage/index');
?>

<div class="page">
    <?= Alert::widget() ?>
    <form name="form_constraints" action="<?= \yii\helpers\Url::to(['index']) ?>"
          class="form-horizontal form-validation" method="get">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <?php
                        if ($model->searchInput) {
                            $searchInput = $model->searchInput;
                            $count = count($searchInput);
                            $i = 0;
                            foreach ($searchInput as $key => $value) {
                                if ($i % 6 == 0) {
                                    echo '<div class="form-group">';
                                }
                                //列表形式
                                if (isset($value['list']) && !empty($value['list'])) {
                                    if ($key == 'type') unset($value['list'][1]);
                                    $content = Html::dropDownList($key, isset($params[$key]) ? $params[$key] : '', $value['list'], ['class' => 'form-control']);
                                } else if ($key == 'push_time') {
                                    $content = Html::input('text', $key, isset($params[$key]) ? $params[$key] : '', [
                                        'class' => 'form-control inputDate',
                                        'placeHolder' => isset($value['label']) ? $value['label'] : '',
                                        'id' => isset($value['id']) ? $value['id'] : '',
                                    ]);
                                } ////普通文本格式
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
                        <?= Html::submitButton(Yii::t('app', 'search'), ['class' => 'btn btn-success']) ?>
                        </label>
                        &nbsp;
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <section class="panel panel-default table-dynamic">
                    <div class="panel-heading"><strong><span
                                    class="glyphicon glyphicon-th-large"></span> <?= Yii::t('app', 'search result') ?>
                        </strong>
                        <div class="pull-right" style="margin-top:-5px;">
                          <!--  <a type="button" class="btn  btn-sm"
                               href="<?/*= Yii::$app->urlManager->createUrl(array_merge(['/appointment/ip-manage/index'], $params, ['export' => 'excel'])) */?>"><span
                                        class="glyphicon glyphicon-log-out"></span><?/*= Yii::t('app', 'excel') */?>
                            </a>-->

                        </div>
                    </div>

                    <div style="clear:both;"></div>

                    <?php if (!empty($list)): ?>
                    <div style="overflow-x: auto;">
                        <table class="table table-bordered table-striped table-responsive" style="width: 100%;">
                            <thead>
                            <tr>
                                <?php
                                if (isset($params['showField']) && $params['showField']) {
                                    //不需要排序的字段
                                    $no_sort_field = ['ip', 'content', 'mgr_name', 'ip_addr', 'user_name', 'user_real_name', 'user_online_status'];
                                    echo '<td><input type="checkbox" id="all"/></td>';
                                    foreach ($params['showField'] as $value) {
                                        if (in_array($value, $no_sort_field)) {
                                            echo '<th nowrap="nowrap"><div class="th">' . $model->searchField[$value] . '</div></th>';
                                        } else {
                                            $newParams = $params;
                                            echo '<th nowrap="nowrap"><div class="th">';
                                            echo $model->searchField[$value];

                                            $newParams['orderBy'] = $value;
                                            array_unshift($newParams, '/appointment/sms/index');

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
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($list as $one): ?>
                                <tr>
                                    <td><input type="checkbox" name="id[]" value="<?php echo $one['user_id'] ?>"/></td>
                                    <?php foreach ($params['showField'] as $value): ?>
                                        <td>
                                            <?php
                                            if ($value == 'created_at' || $value == 'updated_at') {
                                                $one[$value] = date('Y-m-d H:i:s', $one[$value]);
                                            } else if ($value == 'status') {
                                                $one[$value] = $one[$value] == 1 ? '已使用' : '未使用';
                                            } else if ($value == 'ip_addr') {
                                                if ($one[$value] == '') {
                                                    $one[$value] = '未知';
                                                } else {
                                                    $one[$value] = long2ip($one[$value]);
                                                }
                                            }
                                            echo Html::encode($one[$value]);
                                            ?>
                                        </td>
                                    <?php endforeach ?>
                                </tr>
                            <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="divider"></div>
                    <footer class="table-footer">
                        <div class="row">
                            <div class="col-md-6">
                                <?php
                                echo Yii::t('app', 'pagination show page', [
                                    'totalCount' => $pagination->totalCount,
                                    'totalPage' => $pagination->getPageCount(),
                                    'perPage' => '<input type=text name=offset size=3 value=' . $params['offset'] . '>',
                                    'pageInput' => '<input type=text name=page size=4>',
                                    'buttonGo' => '<input type=submit value=go>',
                                ]);
                                ?>
                            </div>
                            <div class="col-md-6 text-right">
                                <?= LinkPager::widget(['pagination' => $pagination, 'maxButtonCount' => 5]); ?>
                            </div>
                        </div>
                    </footer>

                </section>
                <?php else: ?>
                    <div class="panel-body">
                        <?= Yii::t('app', 'no record') ?>
                    </div>
                <?php endif; ?>
            </div>
    </form>
</div>
<script>
    function test(id) {
        $('#' + id).toggle();
    }
</script>