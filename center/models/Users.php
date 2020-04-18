<?php

    namespace center\models;

use Yii;

/**
 * This is the model class for table "users".
 *
 * @property integer $user_id
 * @property string $user_name
 * @property string $user_real_name
 * @property integer $group_id
 * @property integer $user_create_time
 * @property integer $user_update_time
 * @property integer $user_expire_time
 * @property integer $user_status
 * @property double $balance
 * @property string $mgr_name_create
 * @property string $mgr_name_update
 * @property integer $user_start_time
 * @property integer $user_stop_time
 * @property integer $user_allow_chgpass
 * @property string $cert_type
 * @property string $cert_num
 * @property string $phone
 * @property string $email
 * @property string $user_type
 * @property string $can_create_visitor
 * @property string $create_visitor_num
 * @property integer $question1
 * @property string $answer1
 * @property integer $question2
 * @property string $answer2
 * @property integer $question3
 * @property string $answer3
 */
class Users extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_name', 'user_create_time', 'user_update_time', 'user_expire_time', 'mgr_name_create', 'mgr_name_update', 'phone', 'email'], 'required'],
            [['group_id', 'user_create_time', 'user_update_time', 'user_expire_time', 'user_status', 'user_start_time', 'user_stop_time', 'user_allow_chgpass', 'question1', 'question2', 'question3'], 'integer'],
            [['balance'], 'number'],
            [['user_name', 'user_real_name', 'mgr_name_create', 'mgr_name_update', 'phone'], 'string', 'max' => 64],
            [['cert_type', 'cert_num', 'email', 'user_type', 'can_create_visitor', 'create_visitor_num', 'answer1', 'answer2', 'answer3'], 'string', 'max' => 255],
            [['user_name'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'user_name' => 'User Name',
            'user_real_name' => 'User Real Name',
            'group_id' => 'Group ID',
            'user_create_time' => 'User Create Time',
            'user_update_time' => 'User Update Time',
            'user_expire_time' => 'User Expire Time',
            'user_status' => 'User Status',
            'balance' => 'Balance',
            'mgr_name_create' => 'Mgr Name Create',
            'mgr_name_update' => 'Mgr Name Update',
            'user_start_time' => 'User Start Time',
            'user_stop_time' => 'User Stop Time',
            'user_allow_chgpass' => 'User Allow Chgpass',
            'cert_type' => 'Cert Type',
            'cert_num' => 'Cert Num',
            'phone' => 'Phone',
            'email' => 'Email',
            'user_type' => 'User Type',
            'can_create_visitor' => 'Can Create Visitor',
            'create_visitor_num' => 'Create Visitor Num',
            'question1' => 'Question1',
            'answer1' => 'Answer1',
            'question2' => 'Question2',
            'answer2' => 'Answer2',
            'question3' => 'Question3',
            'answer3' => 'Answer3',
        ];
    }
}
