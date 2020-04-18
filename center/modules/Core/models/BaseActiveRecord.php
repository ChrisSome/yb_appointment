<?php
/**
 * Created by PhpStorm.
 * User: Sihuo
 * Date: 2017/7/7
 * Time: 11:35
 */

namespace center\modules\Core\models;

use yii;
use common\models\User;
use common\models\Redis;
use yii\db\ActiveRecord;
use center\modules\user\models\Base;
use center\modules\auth\models\SrunJiegou;
use center\modules\report\models\Financial;
use center\modules\financial\models\PayList;
use center\modules\strategy\models\Product;
use center\modules\strategy\models\Package;
use center\modules\financial\models\WaitCheck;
use center\modules\strategy\models\Recharge;
use center\modules\user\models\ExpireProducts;
use center\modules\strategy\models\ProductsChange;
use center\modules\financial\models\CheckoutList;
use center\modules\interfaces\models\SoapCenter;

/**
 * 数据库操作基类
 * Class BaseActiveRecord
 * @package center\modules\Core\models
 */
class BaseActiveRecord extends ActiveRecord
{
    public $start_time;
    public $stop_time;
    public $timePoint;
    public $operator;
    public $flag; //是否超管
    public $baseModel;
    public $_mgrName;
    public $products;

    //CDR的key
    const INTERFACE_NAME_KEY = 'key:interface_name:';

    const USERS_EXPORT_LIMIT = 30000;  //excel一次最多导出量
    const CSV_EXPORT_LIMIT = 100000; //csv一次性导出量

    /* @inheritdoc
     */
    public static function tableName()
    {
        return 'user_appointment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['start_time', 'stop_time', 'timePoint', 'operator'], 'safe'],
        ];
    }

    /**
     * 类初始化
     */
    public function init()
    {
        $this->flag = User::isSuper();
        parent::init(); //TODO:: change some settings
    }




    /**
     * 获取操作的管理员
     * @return mixed
     */
    public function getMgrName()
    {
        if ($this->_mgrName == '') {
            $this->setMgrName();
        }
        return $this->_mgrName;
    }

    /**
     * 设置管理员姓名
     * @param $name null|string
     * @return string
     */
    public function setMgrName($name = null)
    {
        if (is_null($name)) {
            $this->_mgrName = Yii::$app->user->identity->username;
        } else {
            $this->_mgrName = $name;
        }
    }



    /**
     * 将异常错误信息写入表统计
     * @param $action
     * @param $msg
     * @return int
     * @throws yii\db\Exception
     */
    public function writeMessage($action, $msg)
    {
        $db = Yii::$app->db;
        $time = time();
        $ip_addr = Yii::$app->request->userIP;
        return $db->createCommand("INSERT INTO `operate_exception`(exception_time, action_type, err_msg, ip_addr) VALUES('{$time}', '{$action}', '{$msg}', '{$ip_addr}')")->execute();
    }

}