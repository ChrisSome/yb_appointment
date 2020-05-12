<?php


namespace center\controllers;

use yii;
use center\extend\Log;
use center\modules\appointment\models\DomainManager;
use yii\validators\Validator;
use yii\web\Controller;
use yii\web\Response;

class ApiController extends Controller
{
    private $sign_key = 'sign';
    public $check_params = [];
    public $errors;
    public $model;


    public function checkDomain()
    {
        $referrer = Yii::$app->request->referrer;
        $referrer = parse_url($referrer);
        $referrer_domain = $referrer['host'];

        return DomainManager::checkIsAllowedDomain($referrer_domain);
    }

    public function checkParams($params) {
        if ($this->model) {
            $this->model->load($params, '');
            return $this->model->validate();
        }

        return true;
    }
    /**
     * 加密或者验签
     * @param $params
     * @param bool $checked
     * @return bool|string
     */
    public function checkSign($params, $checked = false) {
        ksort($params); //ascii升序
        $sSafeStr = ''; //加密字段
        foreach ($params as $k => $v) {
            if ($k != $this->sign_key) {
                $sSafeStr .= $k.'='.$v.'&';
            }
        }

        $sSafeStr = rtrim($sSafeStr, '&');
        //var_dump(md5($sSafeStr));

        return $checked ? md5($sSafeStr) == $params[$this->sign_key] : md5($sSafeStr);
    }

    /**
     * 接口返回数据
     * @param $code
     * @param array $data
     * @return Response
     */
    public static function returnJson($code, $data = []) {
        $response = new Response();
        $response->format = Response::FORMAT_JSON;

        $response->setStatusCode(200);
        $response->data = $data;

        return $response;
    }
}