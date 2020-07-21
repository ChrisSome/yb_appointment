<?php

namespace center\modules\api\controllers;


use center\modules\appointment\models\DomainManager;
use center\modules\appointment\models\IpBlacklist;
use center\modules\appointment\models\SmsHistory;
use Yii;
use common\models\Redis;
use center\controllers\ApiController;
use center\modules\appointment\models\ImportMobile;
use center\modules\appointment\models\UserAppointment;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * 预约控制器
 * Class AppointmentController
 * @package center\modules\api\controllers
 */
class AppointmentController extends ApiController
{
    public $enableCsrfValidation = false; //关闭验证
    public $check_params = [
        'mobile' => 'required|preg:/^1\d{10}/',
        'username' => 'required',
        'sign' => 'required'
    ];

    public $errors;
    public $model;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {//test
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    //'apply' => ['post'],
                ],
            ],
        ];
    }
    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
        $this->model =  new UserAppointment();
    }

    /**
     * 开始申请
     * @return \yii\web\Response
     */
    public function actionApply()
    {
        $params = Yii::$app->request->post();
        $get = Yii::$app->request->queryParams;
        $params = $params ? $params : $get;

        if (Yii::$app->request->method == 'OPTIONS') {
            exit(204);
        }

        if (!$this->checkDomain() && false) {
            return self::returnJson(403, [
                'code' => 403,
                'message' => '别瞎闹'
            ]);
        }
        //验证参数
        if (!$this->checkParams($params)) {
            return self::returnJson(406, [
                'code' => 406,
                'message' => json_encode($this->model->getErrors(), JSON_UNESCAPED_UNICODE)
            ]);
        }

//        if (!isset($params['code'])) {
//            return self::returnJson(400, [
//                'code' => 400,
//                'message' => '验证码不能为空'
//            ]);
//        }

        if (!$this->checkSign($params, true) && false) {
            return self::returnJson(408, [
                'code' => 408,
                'message' => '验签不通过'
            ]);
        }


        $sPhone = $params['mobile'];
        if (!$oImportPhone = ImportMobile::findOne(['mobile' => $sPhone])) {
            return self::returnJson(409, [
                'code' => 409,
                'message' => '不是预约用户，如果有疑问咨询在线客服'
            ]);
        }
        $model = $this->model;
        //$aHasAlreadyBindPhones = $model->getHasBindAlreadyPhone();
        //var_dump($aHasAlreadyBindPhones, in_array($sPhone, $aHasAlreadyBindPhones));exit;
        if ($model_one = UserAppointment::findOne(['mobile' => $sPhone])) {
            if ($model_one->status == 2) {

                return self::returnJson(407, [
                    'code' => 407,
                    'message' => ' 返回您的号码被限制, 请联系客服'
                ]);
            }

            return self::returnJson(406, [
                'code' => 406,
                'message' => '很抱歉， 您已预约'
            ]);
        } else {
            //增加验证码验证逻辑
            $lastCodes = SmsHistory::find()->where(['phone' => $sPhone, 'type' => 1])->orderBy(['id' =>  SORT_DESC])->limit(1)->one();
           /* $sql = SmsHistory::find()->where(['phone' => $sPhone])->orderBy(['id' =>  SORT_DESC])->limit(1)->createCommand()->getRawSql();
            var_dump($lastCodes->content, $sql);*/
            if ($lastCodes->content != $params['code']) {
                return self::returnJson(408, [
                    'code' => 408,
                    'message' => '很抱歉，验证码错误'
                ]);
            }
            if ($lastCodes->status != 0) {
                return self::returnJson(408, [
                    'code' => 408,
                    'message' => '该验证码已使用， 不可重复使用'
                ]);
            }
            //echo 1;exit;
            $model->save();
            $lastCodes->status = 1;
            $lastCodes->save(false);

            //echo 1;exit;
            return self::returnJson(200, [
                'code' => 200,
                'message' => '恭喜您， 预约成功',
            ]);
        }
    }

    /**
     * 获取参数
     * @return \yii\web\Response
     */
    public function actionGetSign()
    {
        $params = Yii::$app->request->queryParams;
        if (!$this->checkDomain()) {
            return self::returnJson(403, [
                'code' => 403,
                'message' => '别瞎闹'
            ]);
        }
        //验证参数
        if (!$this->checkParams($params)) {
            return self::returnJson(408, [
                'code' => 408,
                'message' => json_encode($this->model->getErrors(), JSON_UNESCAPED_UNICODE)
            ]);
        }

        $sign = $this->checkSign($params);

        return self::returnJson(200, [
            'code' => 200,
            'sign' => $sign
        ]);

    }
    public function actionDemo()
    {
        return $this->renderPartial('demo');
    }


    /**
     * 验证ip是否为黑名单
     * @return \yii\web\Response
     */
    public function actionIpIsBlack()
    {
        if (!$this->checkDomain()) {
            return self::returnJson(403, [
                'code' => 403,
                'message' => '别瞎闹'
            ]);
        }
        $ip = Yii::$app->request->userIP;
        $ipLong = ip2long($ip);

        if (IpBlacklist::findOne(['ip_addr' => $ipLong])) {
            return self::returnJson(403, [
                'code' => 403,
                'message' => $ip.'在黑名单'
            ]);
        } else {
            return self::returnJson(200, [
                'code' => 200,
                'message' => $ip.'可以正常访问'
            ]);
        }

    }

}