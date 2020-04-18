<?php

namespace center\models;

use Yii;

/**
 * This is the model class for table "pay_type".
 *
 * @property integer $id
 * @property string $type_name
 * @property integer $default
 * @property integer $create_at
 * @property string $mgr_name
 * @property integer $is_del
 */
class PayType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pay_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type_name', 'create_at', 'mgr_name'], 'required'],
            [['default', 'create_at', 'is_del'], 'integer'],
            [['type_name', 'mgr_name'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type_name' => 'Type Name',
            'default' => 'Default',
            'create_at' => 'Create At',
            'mgr_name' => 'Mgr Name',
            'is_del' => 'Is Del',
        ];
    }

    /**
     * 返回所有的支付类型
     *
     * */
    public static function getAllPayType(){
        $result = self::find()->where(1)->asArray()->all();
        if(!$result){
            return false;
        }
        $typeArr = [];
        foreach ($result as $key => $value){
                $typeArr[$value['id']] = $value['type_name'];
        }
        return $typeArr;
    }
}
