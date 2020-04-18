<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\User;
use center\modules\strategy\models\Product;
use center\modules\auth\models\AuthAssignment;
use center\modules\auth\models\AuthItem;
use center\modules\auth\models\UserModel;
use center\extend\Tool;

$this->title = ($model->isNewRecord) ? Yii::t('app', 'User Manager') : Yii::t('app', 'Update User');

$ProductList = []; // 产品列表
$user = new User();

$canAddRoles = Yii::$app->user->can('auth/roles/create');


//判断是更新数据还是新增数据.
if (!$model->isNewRecord) {
    $authAssignment = AuthAssignment::findOne(['user_id' => Yii::$app->request->get('id')]);

    if (!empty($authAssignment)) {
        $model->roles = $authAssignment->item_name;
    }
}

$canSetDefaultPass = Yii::$app->user->can('auth/assign/set-default-pass');
?>

<style type="text/css">
    .ztree li a.curSelectedNode span {
        background-color: #0088cc;
        color: #fff;
        border-radius: 2px;
        padding: 2px;
    }
</style>

<div class="padding-top-15px">
<?= $this->render('/layouts/nav'); ?>
<div class="col-lg-10">
<h3 class="page-header">
    <i class="glyphicon glyphicon-user"></i>&nbsp;&nbsp;<?= Html::encode($this->title) ?>
</h3>

<div>
<?= \center\widgets\Alert::widget(); ?>

<div class="panel panel-default">
<div class="panel-body">
<?php $form = ActiveForm::begin([
    'id' => 'form-signup',
    'layout' => 'horizontal',
    'fieldConfig' => [
        'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{endWrapper}\n{error}",
        'horizontalCssClasses' => [
            'label' => 'col-sm-3',
            'wrapper' => 'col-sm-5',
            'hint' => '',
        ],
    ],
    'options' => ['class' => 'padding-top-15px'],
]); ?>

<!--用户基本信息开始-->
<h5><?= Yii::t('app', 'assign title 1'); ?></h5><div class="hr"></div>

<?php
/**
 * 判断权限。 超级管理员可以管理全部角色, 普通管理员如果没有权限则不能选择角色
 */
if (AuthItem::getChildRolesData()) {
    //判断是更新数据还是新增数据.
    if ($model->isNewRecord) {
        echo $form->field($model, 'roles')->dropDownList(ArrayHelper::map(AuthItem::getChildRolesData(), 'name', 'name'), ['onchange' => 'selectRoles(this.value)']);
    } else {
        //如果修改不是自己的信息，角色组可以进行修改
        if ($model->id !== Yii::$app->user->id) {
            echo $form->field($model, 'roles')->dropDownList(ArrayHelper::map(AuthItem::getChildRolesData(), 'name', 'name'), ['onchange' => 'selectRoles(this.value)']);
        } else {
            //如果是修改的自己信息，那么不可以修改自己所在的角色组
            echo $form->field($model, 'roles')->dropDownList(ArrayHelper::map(AuthItem::getChildRolesData(), 'name', 'name'), ['disabled' => true]);
            echo $form->field($model, 'roles', ['template' => '{input}', 'options' => ['style' => 'display:none;']])->hiddenInput(['value' => $model->roles]);
        }
    }
}?>

<!-- 判断是否有创建角色的权限 -->
<?php if($canAddRoles){?>
    <div class="form-group">
        <label class="control-label col-sm-3"></label>

        <div class="col-sm-4">
            <?= Yii::t('app', 'No target role optional?'); ?>
            <?= Html::a(Yii::t('app', 'add roles'), ['/auth/roles/create'], ['class' => 'btn btn-success btn-xs', 'target' => '_bank']) ?>
        </div>
    </div>
<?php }?>

<?php
/**
 * 如果是添加管理员,名称可以创建。否则在修改过程中,管理员名称不可以修改.
 */
if ($model->isNewRecord) {
    echo $form->field($model, 'username')->textInput();
} else {
    echo $form->field($model, 'username')->textInput(['disabled' => true]);
    echo $form->field($model, 'username', ['template' => '{input}', 'options' => ['style' => 'display:none;']])->hiddenInput(['value' => $model->username]);
}
?>

<?php //echo $form->field($model, 'email')->textInput(['autocomplete' => 'off']) ?>
    <?php if ($model->isNewRecord) : ?>
        <?= $form->field($model, 'password')->passwordInput() ?>
        <?= $form->field($model, 'passwords')->passwordInput() ?>
    <?php else :?>
        <div class="form-group field-usermodel-old_password">
            <label class="control-label col-sm-3"
                   for="usermodel-old_password"><?= Yii::t('app', 'Old password') ?></label>

            <div class="col-sm-5">
                *****<span>&nbsp;&nbsp;<?= Html::Button(Yii::t('app', '/api/v1/user/reset-password'), ['class' => 'btn btn-success', 'id' => 'test', 'onclick' => "chgPassword()"]) ?></span>
                <?= Html::button(Yii::t('app', 'auth/assign/set-default-pass'), [
                    'class' => 'btn btn-danger btn-xs',
                    'onClick' => 'checkMgrResetPass("' . Yii::t('app', 'auth help5') . '",  "' . $model->id . '")',
                    'style' => 'line-height:30px;margin-left:26%;margin-bottom:10px;'
                ]) ?>
            </div>


            <div class="help-block help-block-error "></div>
        </div>
        <div id = 'test_ch' style="display:none">
            <?= $form->field($model, 'old_password', [
                'inputOptions' => ['onblur'=>'verifyPassword()']
            ])->passwordInput() ?>
            <div class="help-block col-sm-offset-3 "><?=Yii::t("app", "user base help6")?></div>
            <?= $form->field($model, 'password')->passwordInput() ?>
            <?= $form->field($model, 'passwords')->passwordInput() ?>
        </div>

    <?php endif;?>

<?= $form->field($model, 'email')->textInput() ?>

<!-- 保存按钮 -->
<div>
    <label class="control-label col-sm-3"></label>
    <div class="col-sm-5 padding-top-15px">
        <?= Html::submitButton('<i class="glyphicon glyphicon-flag"></i> ' . Yii::t('app', 'save'), ['class' => 'col-lg-12 btn btn-success ']) ?>
    </div>
</div>

<?php ActiveForm::end(); ?>
</div>
</div>
</div>
</div>
</div>
<script>
    //修改密码
    function chgPassword() {
        var obj = document.getElementById('test_ch');
        if (obj) {
            if (obj.style.display == 'block') {
                obj.style.display = 'none';
            } else {
                obj.style.display = 'block';
            }
        }
    }

    //设置默认密码
    function checkMgrResetPass(msg1, id){
        if(!confirm(msg1)){
            return false;
        }
        $.post('set-default-pass',{'id':id},function(res){
            alert(res.msg);
            window.location.href = window.location.href;
        },'json')
    }
</script>