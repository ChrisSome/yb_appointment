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
use yii\bootstrap\Modal;
use yii\helpers\Url;

$can = Yii::$app->user->can('appointment/user/index');
$canDelete = Yii::$app->user->can('appointment/user/delete');
$canAdd = Yii::$app->user->can('appointment/file/add');
$canOperate = Yii::$app->user->can('appointment/file/operate');
$canBatch = Yii::$app->user->can('appointment/user/batch');

$this->title = Yii::t('app', 'appointment/user/index');
$attributes = $model->getAttributesList();
?>
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

                        </div>
                    </div>

                    <div style="clear:both;"></div>

                    <?php if (!empty($list)): ?>
                    <div style="overflow-x: auto;">
                        <table class="table table-bordered table-striped table-responsive">
                            <thead>
                            <tr>
                                <?php
                                if (isset($params['showField']) && $params['showField']) {
                                    //不需要排序的字段
                                    $no_sort_field = ['ip', 'products_id', 'file', 'result', 'user_name', 'user_real_name', 'user_online_status', 'operator'];
                                    echo '<td><input type="checkbox" id="all"/></td>';
                                    foreach ($params['showField'] as $value) {
                                        if (in_array($value, $no_sort_field)) {
                                            echo '<th nowrap="nowrap"><div class="th">' . $model->searchField[$value] . '</div></th>';
                                        } else {
                                            $newParams = $params;
                                            echo '<th nowrap="nowrap"><div class="th">';
                                            echo $model->searchField[$value];

                                            $newParams['orderBy'] = $value;
                                            array_unshift($newParams, '/appointment/file/index');

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
                                <?php if ($canDelete || $canOperate): ?>
                                    <th nowrap="nowrap">
                                        <div class="th"><?= Yii::t('app', 'operate') ?></div>
                                    </th>
                                <?php endif; ?>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($list as $one): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="id[]" value="<?php echo $one['id'] ?>"/>
                                    </td>
                                    <?php foreach ($params['showField'] as $value): ?>
                                        <td>
                                            <?php
                                            if ($value == 'created_at' || $value == 'updated_at') {
                                                if ($one['updated_at'] == 0) {
                                                    $one['updated_at'] = $one['created_at'];
                                                }
                                                $one[$value] = date('Y-m-d H:i:s', $one[$value]);
                                                echo Html::encode($one[$value]);
                                            } else if ($value == 'status') {
                                                $available_css = $one['status'] == 1 ? 'btn-success' : ($one[$value] == 2 ? 'btn-danger' : 'btn-xs');
                                                echo '<button type="button" class="btn ' . $available_css . ' btn-xs">' . $attributes['status'][$one['status']] . '</button>';
                                            } elseif ($value == 'file') {
                                                echo Html::a(Html::button('下载文件'),['/appointment/file/download', 'id' => $one['id']]);
                                            } else {
                                                echo $one[$value];
                                            }

                                            ?>

                                        </td>


                                    <?php endforeach ?>
                                    <td>
                                        <?php if ($canDelete || $canOperate): ?>
                                            <?php if ($canDelete) echo Html::a(Html::button(Yii::t('app', 'delete'), ['class' => 'btn btn-danger btn-xs']), ['delete', 'id' => $one['id']], [
                                                'title' => Yii::t('app', 'User Delete'),
                                                'data' => [
                                                    'method' => 'post',
                                                    'confirm' => '确定删除此上传数据?'
                                                ],]) ?>
                                            <?php if ($canOperate) echo Html::a(Html::button(Yii::t('app', '修改状态'), ['class' => 'btn btn-info btn-xs']), ['operate', 'id' => $one['id']], [
                                                'title' => Yii::t('app', 'User Delete'),
                                                'data' => [
                                                    'method' => 'post',
                                                    'confirm' => '确定修改状态?'
                                                ],]) ?>
                                        <?php endif; ?>
                                    </td>
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

<script src="/js/jquery.min.js"></script>
<script>
    $('.stage1_submit').click(function () {
        var _this = $(this);
        $('#id').val(_this.attr('data-id'));
        $('#remark').val('');
        $('#myModal').modal({backdrop: 'static', keyboard: false});
    });

    $('.sure').click(function () {
        var _remark = $('#remark').val();
        if (!_remark) {
            alert('备注原因不能为空');

            return ;
        }
        var _id = $('#id').val();
        $.ajax({
            //请求方式
            type : "post",
            //请求的媒体类型
            contentType: "application/json;charset=UTF-8",
            //请求地址
            url : "operate?id="+_id+'&status=2&remark='+_remark,
            //数据，json字符串
            data : {'id': _id, 'status': 2, 'remark': _remark },
            //请求成功
            success : function(result) {
                console.log(result);
            },
            //请求失败，包含具体的错误信息
            error : function(e){
                console.log(e.status);
                console.log(e.responseText);
            }
        });
    })

    function chgStatus()
    {
        var _status = $('#operate').val();
        if (_status == 2) {
            $('#batch').show();
        } else {
            $('#batch').hide();
        }
    }


    function batch() {
        var _ids = getSelections();
        var _status = $('#operate').val();
        var _remark = $('#batch_remark').val();

        if (_ids.length == 0) {
            alert('没有选择数据');

            return ;
        }
        if (_status == 2 && !_remark) {
            alert('拒绝原因不能为空');

            return ;
        }
        $.ajax({
            //请求方式
            type : "post",
            //请求的媒体类型
            contentType: "application/json;charset=UTF-8",
            //请求地址
            url : "batch?ids="+_ids+'&status='+_status+'&remark='+_remark,
            //数据，json字符串
            data : {'id': _ids, 'status': _status, 'remark': _remark },
            //请求成功
            success : function(result) {
                console.log(result);
            },
            //请求失败，包含具体的错误信息
            error : function(e){
                console.log(e.status);
                console.log(e.responseText);
            }
        });

    }
    /**
     *列表得到选中的id
     */
    function getSelections()
    {
        var ids = [];
        var checkBoxes = document.getElementsByName('id[]');
        for (var i= 0, len= checkBoxes.length; i < len; i++) {
            var status = checkBoxes[i].checked;
            if (status) {//选中了
                ids.push(checkBoxes[i].value)
            }
        }

        return ids;
    }

    $(document).on('click', '#all', function () {
        var _status = document.getElementById('all').checked;
        var checkBoxes = document.getElementsByName('id[]');
        for(var i = 0;i<checkBoxes.length;i++){
            checkBoxes[i].checked = _status;
        }
    })
</script>
