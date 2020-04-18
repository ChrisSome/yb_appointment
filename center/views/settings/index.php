<?php
/**
 * Created by PhpStorm.
 * User: DM
 * Date: 17/4/17
 * Time: 17:26
 */
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><?=Yii::t('app','please_set_multi')?><span> <?=Yii::t('app','now')?> <span class="badge" id="show_multi"><?= $re['multi'] ?></span> <?=Yii::t('app','multi')?></span></h3>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-lg-3">
                <div class="input-group">
                    <input type="number" id="multi" required class="form-control" placeholder="<?=Yii::t('app','please_set_multi')?>">
                    <span class="input-group-btn">
                        <button class="btn btn-default" onclick="set_multi(this);" type="submit"><?=Yii::t('app','submit')?></button>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function set_multi(obj) {
        // TODO: 校验

        $(obj).addClass('disabled').html('<?=Yii::t('app','settings')?>');
        var multi = $("#multi").val();
        var url = 'index';
        var data = {'multi':multi};
        var dataType = 'json';
        var fun = function (e) {
            $(obj).removeClass('disabled').html('<?=Yii::t('app','submit')?>');
            if(e.status == 1){
                alert(e.msg);
                $("#show_multi").html(e.data);
            }else {
                alert(e.msg);
            }
        };
        $.post(url,data,fun,dataType);
    }
</script>
