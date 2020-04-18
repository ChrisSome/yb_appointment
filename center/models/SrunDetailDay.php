<?php

namespace center\models;

use common\models\User;
use Yii;

/**
 * This is the model class for table "srun_detail_day".
 *
 * @property string $detail_day_id
 * @property string $user_name
 * @property integer $record_day
 * @property string $bytes_in
 * @property string $bytes_out
 * @property string $bytes_in6
 * @property string $bytes_out6
 * @property integer $products_id
 * @property integer $billing_id
 * @property integer $control_id
 * @property double $user_balance
 * @property string $total_bytes
 * @property integer $time_long
 * @property integer $user_login_count
 * @property integer $user_group_id
 */
class SrunDetailDay extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */




    public static function tableName()
    {
        return 'srun_detail_day';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_name','record_day', 'bytes_in', 'bytes_out', 'bytes_in6', 'bytes_out6', 'products_id', 'billing_id', 'control_id', 'total_bytes', 'time_long', 'user_login_count'], 'required'],
            [['record_day', 'bytes_in', 'bytes_out', 'bytes_in6', 'bytes_out6', 'products_id', 'billing_id', 'control_id', 'total_bytes', 'time_long', 'user_login_count', 'user_group_id'], 'integer'],
            [['user_balance'], 'number'],
            [['user_name'], 'string', 'max' => 64]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'detail_day_id' => 'Detail Day ID',
            'user_name' => 'User Name',
            'record_day' => 'Record Day',
            'bytes_in' => 'Bytes In',
            'bytes_out' => 'Bytes Out',
            'bytes_in6' => 'Bytes In6',
            'bytes_out6' => 'Bytes Out6',
            'products_id' => 'Products ID',
            'billing_id' => 'Billing ID',
            'control_id' => 'Control ID',
            'user_balance' => 'User Balance',
            'total_bytes' => 'Total Bytes',
            'time_long' => 'Time Long',
            'user_login_count' => 'User Login Count',
            'user_group_id' => 'User Group ID',
        ];
    }
    
    
    public static function getDataByTime($beginTime,$endTime){
        $query = new \yii\db\Query();
        $result = $query->select(['user_name','products_id','record_day','user_group_id','bytes_in','bytes_out','total_bytes','time_long','user_login_count'])
            ->from(SrunDetailDay::tableName())
            ->where(['>=','record_day',$beginTime])
            ->andWhere(['<','record_day',$endTime])
            ->all();
        if(!$result){
            return false;
        }
        return $result;
    }
    
}
