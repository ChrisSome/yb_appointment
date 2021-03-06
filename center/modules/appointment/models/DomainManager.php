<?php

namespace center\modules\appointment\models;

use center\modules\Core\interfaces\BaseModelInterface;
use center\modules\Core\models\BaseActiveRecord;
use center\modules\log\models\LogWriter;
use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "domain_managers".
 *
 * @property integer $id
 * @property string $domain
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 */
class DomainManager extends BaseActiveRecord implements BaseModelInterface
{
    public $default_field = ['id', 'domain', 'status', 'created_at', 'updated_at'];


    public function beforeSave($insert)
    {
        if ($insert) {
            $this->created_at = $this->updated_at = time();
        } else {
            $this->updated_at = time();
        }

        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'domain_managers';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'created_at', 'updated_at'], 'integer'],
            [['domain'], 'string', 'max' => 64],
            [['domain'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'domain' => Yii::t('app', 'domain'),
            'status' => Yii::t('app', 'status'),
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    //搜索字段
    private $_searchField = null;

    public function getSearchField()
    {
        // TODO: Implement getSearchField() method.
        return [
            'id' => 'ID',
            'domain' => Yii::t('app', 'domain'),
            'status' => Yii::t('app', 'status'),
            'created_at' => '创建时间',
            'updated_at' => '最后更新时间',
            'start_time' => '最后更新时间',
            'stop_time' => '最后更新时间',
        ];
    }

    public function getSearchInput()
    {
        // TODO: Implement getSearchInput() method.
        //扩展字段加入搜索
        $exField = [];

        return yii\helpers\ArrayHelper::merge([
            'domain' => [
                'label' => Yii::t('app', 'domain')
            ],
            'status' => [
                'label' => Yii::t('app', 'status'),
                'list' => [
                    '' => '请选择',
                    '0' => '禁用',
                    1 => '启用'
                ]
            ],

            'start_time' => [
                'label' => Yii::t('app', 'start opt time'),
                'class' => '  inputDate'
            ],
            'stop_time' => [
                'label' => Yii::t('app', 'end opt time'),
                'class' => '  inputDate'
            ]

        ], $exField);
    }

    public function getAttributesList()
    {
        // TODO: Implement getAttributesList() method.
        //扩展字段加入搜索
        $exField = [];

        return yii\helpers\ArrayHelper::merge([
            'domain' => [
                'label' => Yii::t('app', 'domain')
            ],
            'status' => [
                'label' => Yii::t('app', 'status'),
                'list' => [
                    '' => '请选择',
                    '0' => '禁用',
                    1 => '启用'
                ]
            ],

            'start_time' => [
                'label' => Yii::t('app', 'start opt time'),
                'class' => '  inputDate'
            ],
            'stop_time' => [
                'label' => Yii::t('app', 'end opt time'),
                'class' => '  inputDate'
            ]

        ], $exField);
    }


    public $_temOldAttr;

    /**
     * 保存后记录日志
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub
        //写日志开始 获取脏数据
        $dirtyArr = LogWriter::dirtyData($this->_temOldAttr, $this->getCurrentData());
        if (!empty($dirtyArr)) {
            $logData = [
                'operator' => Yii::$app->user->identity->username,
                'target' => $this->domain,
                'action' => $insert ? 'add' : 'edit',
                'action_type' => 'domain manage',
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
        //获取扩展字段
        $normalField = yii\helpers\ArrayHelper::merge([
            'mobile', 'mgr_name'
        ], []);


        return [
            'domain' => $this->domain,
            'status' => $this->status == 0 ? '禁用' : '启用'
        ];

    }


    /**
     * 验证域名是否允许访问
     * @param $domain
     * @return bool
     */
    public static function checkIsAllowedDomain($domain)
    {
        $aDomains = self::find()->select('domain')->where(['status' => 1])->column();

        return $aDomains && in_array($domain, $aDomains);

    }
}
