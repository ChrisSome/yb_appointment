<?php

namespace center\models;

use Yii;

/**
 * This is the model class for table "pay_list".
 *
 * @property string $id
 * @property string $user_name
 * @property double $pay_num
 * @property integer $type
 * @property integer $pay_type_id
 * @property integer $product_id
 * @property integer $package_id
 * @property integer $extra_pay_id
 * @property string $order_no
 * @property integer $create_at
 * @property string $mgr_name
 * @property string $bill_number
 * @property integer $print_num
 */
class PayList extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pay_list';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_name', 'package_id', 'order_no', 'create_at', 'mgr_name', 'bill_number'], 'required'],
            [['pay_num'], 'number'],
            [['type', 'pay_type_id', 'product_id', 'package_id', 'extra_pay_id', 'create_at', 'print_num'], 'integer'],
            [['user_name', 'mgr_name'], 'string', 'max' => 64],
            [['order_no', 'bill_number'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_name' => 'User Name',
            'pay_num' => 'Pay Num',
            'type' => 'Type',
            'pay_type_id' => 'Pay Type ID',
            'product_id' => 'Product ID',
            'package_id' => 'Package ID',
            'extra_pay_id' => 'Extra Pay ID',
            'order_no' => 'Order No',
            'create_at' => 'Create At',
            'mgr_name' => 'Mgr Name',
            'bill_number' => 'Bill Number',
            'print_num' => 'Print Num',
        ];
    }
}
