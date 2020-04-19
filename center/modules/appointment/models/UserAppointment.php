<?php

namespace center\modules\appointment\models;

use center\modules\Core\interfaces\BaseModelInterface;
use center\modules\Core\models\BaseActiveRecord;
use center\modules\log\models\LogWriter;
use common\extend\Excel;
use common\extend\OfficesTool;
use common\models\Redis;
use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "user_appointments".
 *
 * @property integer $id
 * @property string $username
 * @property string $operator
 * @property integer $status
 * @property string $mobile
 * @property string $remark
 * @property string $ip
 * @property integer $created_at
 * @property integer $updated_at
 */
class UserAppointment extends BaseActiveRecord implements BaseModelInterface
{
    public static $appointment_phones = "list:appointment:phones";
    public $default_field = ['id', 'username' ,'mobile', 'status', 'remark', 'operator' ,'created_at', 'updated_at','ip'];
    public $_temOldAttr;

    public function beforeSave($insert)
    {
        parent::beforeSave($insert); // TODO: Change the autogenerated stub
        if ($insert) {
            $this->created_at = $this->updated_at =  time();
            $this->ip = Yii::$app->request->userIP;
        } else {
            $this->updated_at = time();
            $this->operator = $this->getMgrName();
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_appointments';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mobile', 'username'], 'required'],
            [['created_at', 'updated_at', 'status'], 'integer'],
            [['username', 'ip', 'remark', 'operator'], 'string', 'max' => 64],
            [['mobile'], 'string', 'max' => 11],
            ['mobile', 'match', 'pattern' => '/^1\d{10}$/', 'message' => '{attribute}格式不正确'],
            //[['mobile'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => '用户名',
            'mobile' => '手机号',
            'created_at' => '预约时间',
            'ip' => 'ip地址',
            'status' => '状态',
            'remark' => '备注',
            'operator' => '操作员'
        ];
    }

    //搜索字段
    private $_searchField = null;

    public function getSearchField()
    {
        if (!is_null($this->_searchField)) {
            return $this->_searchField;
        }

        $this->_searchField = [
            'id' => 'ID',
            'username' => '用户名',
            'mobile' => '手机号',
            'created_at' => '预约时间',
            'start_time' => '开始时间',
            'stop_time' => '结束时间',
            'ip' => 'ip地址',
            'status' => '状态',
            'updated_at' => '最后更新时间',
            'operator' => '操作员',
            'remark' => '备注'
        ];

        return $this->_searchField;
    }

    public function savePhone()
    {
        return $this->save();
    }

    /**
     * 要搜索的字段
     * @return array
     */
    public function getSearchInput()
    {
        //扩展字段加入搜索
        $exField = [];

        return yii\helpers\ArrayHelper::merge([
            'mobile' => [
                'label' => Yii::t('app', 'Mobile')
            ],
            'status' => [
                'label' => Yii::t('app', 'status'),
                'list' => [
                    '' => '请选择',
                    0 => '待审核',
                    1 => '已通过',
                    2 => '已拒绝',
                ]
            ],
            'username' => [
                'label' => Yii::t('app', 'username')
            ],

            'start_time' => [
                'label' => Yii::t('app', 'start opt time'),
                'class' => '  inputDate'
            ],
            'stop_time' => [
                'label' => Yii::t('app', 'end opt time'),
                'class' => '  inputDate'
            ],
            'ip' => [
                'label' => Yii::t('app', 'ip')
            ],

        ], $exField);
    }

    public function getAttributesList()
    {
        // TODO: Implement getAttributesList() method.
        return [
            'id' => 'ID',
            'username' => '用户名',
            'mobile' => '手机号',
            'created_at' => '预约时间',
            'status' => [
                '' => '请选择',
                0 => '待审核',
                1 => '已通过',
                2 => '已拒绝',
            ],
        ];
    }

    /**
     * 导出预约数据
     * @param $query
     */
    public function exportData($query)
    {

        $file = Yii::t('app', 'appointment/user/index') . '.xls';
        $title = Yii::t('app', 'batch export');

        $data = $query->all();
        $excelData = [];
        //$excelData[] = ['用户名', '手机号', '预约时间', '预约状态', '备注', 'ip'];
        $header = [
            'A1'=>'用户名',
            'B1'=>'手机号',
            'C1'=>'预约时间',
            'D1'=>'预约状态',
            'E1'=>'备注',
            'F1'=>'ip'
        ];
        $attr = $this->getAttributesList();
        foreach ($data as $item) {
            $time = date('Y-m-d H:i:s', $item->created_at);
            $excelData[] = [
                $item->username, "{$item->mobile}", "$time", $attr['status'][$item->status], $item->remark, $item->ip
            ];
        }


        //将内容写入excel文件
        OfficesTool::exportData($header, $excelData, $file, $title);exit;
    }

    /**
     *
     *获取已经绑定的手机号
     * @return array|mixed
     */
    public function getHasBindAlreadyPhone()
    {
        $sListKey = UserAppointment::$appointment_phones; //预约队列， 用redis存储
        if (Redis::executeCommand('exists', $sListKey)) {
            $aPhones = Redis::executeCommand('lRange', $sListKey, [0, -1]);
        } else {
            $aPhones = self::find()->select('mobile')->column();
            if ($aPhones) {
                Redis::executeCommand('rPush', $sListKey,$aPhones);
            }
        }

        return  $aPhones;
    }


    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub
        Redis::executeCommand('RPUSH', self::$appointment_phones, [$this->mobile]);
        //写日志开始 获取脏数据
        $dirtyArr = LogWriter::dirtyData($this->_temOldAttr, $this->getCurrentData());
        if (!empty($dirtyArr)) {
            $logData = [
                'operator' => Yii::$app->user->identity->username,
                'target' => $this->mobile,
                'action' => $insert ? 'add' : 'edit',
                'action_type' => 'User Appointment',
                'content' => Json::encode($dirtyArr),
                'class' => __CLASS__,
                'type' => 0,
            ];
            LogWriter::write($logData);
        }
    }


    /**
     * 获取当前的日志需要记录的值
     * @return array
     */
    public function getCurrentData()
    {


        return  [
            'status' => $this->status,
            'remark' => $this->remark
        ];

    }

    public function afterDelete()
    {
        parent::afterDelete(); // TODO: Change the autogenerated stub
        $logData = [
            'operator' => $this->getMgrName(),
            'target' => $this->mobile,
            'action' => 'delete',
            'action_type' => 'delete Mobile',
            'content' => sprintf('%s删除预约号码【%s】成功', $this->getMgrName(), $this->mobile),
            'class' => get_class($this),
            'type' => 1
        ];
        Redis::executeCommand('lrem', self::$appointment_phones, [1, $this->mobile]);
        return LogWriter::write($logData);
    }


    public function batchLog($params) {
        //写日志
        $logContent = sprintf('%s批量%s用户预约, ids: %s', $this->getMgrName(), $params['status'] == 1 ? '启用' : '禁用', $params['ids']);
        $logData = [
            'operator' => Yii::$app->user->identity->username,
            'target' => $params['ids'],
            'action' => 'batch',
            'action_type' => 'Batch Operate '.($params['status'] == 1 ? 'Enable' : 'Disable'),
            'content' => $logContent,
            'class' => get_class($this),
            'type' => 1
        ];
        return LogWriter::write($logData);
    }
}
