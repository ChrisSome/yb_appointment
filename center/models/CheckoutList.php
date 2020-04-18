<?php

namespace center\models;

use Yii;

/**
 * This is the model class for table "checkout_list".
 *
 * @property integer $id
 * @property string $user_name
 * @property double $spend_num
 * @property double $rt_spend_num
 * @property integer $product_id
 * @property integer $buy_id
 * @property double $flux
 * @property string $minutes
 * @property integer $sum_times
 * @property integer $create_at
 * @property integer $type
 * @property string $remark
 */
class CheckoutList extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'checkout_list';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_name', 'product_id', 'buy_id', 'flux', 'minutes', 'sum_times', 'create_at', 'remark'], 'required'],
            [['spend_num', 'rt_spend_num', 'flux'], 'number'],
            [['product_id', 'buy_id', 'minutes', 'sum_times', 'create_at', 'type'], 'integer'],
            [['user_name'], 'string', 'max' => 64],
            [['remark'], 'string', 'max' => 255]
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
            'spend_num' => 'Spend Num',
            'rt_spend_num' => 'Rt Spend Num',
            'product_id' => 'Product ID',
            'buy_id' => 'Buy ID',
            'flux' => 'Flux',
            'minutes' => 'Minutes',
            'sum_times' => 'Sum Times',
            'create_at' => 'Create At',
            'type' => 'Type',
            'remark' => 'Remark',
        ];
    }

    public static function getMonthSpend($value,$beginTime,$endTime){
        $querySpend = new \yii\db\Query();
        $result = $querySpend->select(['user_name','SUM(spend_num)','SUM(rt_spend_num)'])
            ->from('checkout_list')
            ->where(['user_name'=>$value['user_name'],'product_id'=>$value['product']])
            ->andWhere(['between', 'create_at', $beginTime, $endTime])
            ->all();
        return  $result[0]['SUM(spend_num)'] + $result[0]['SUM(rt_spend_num)'];
    }

    public static function getMonthSpends($beginTime,$endTime){
        $querySpend = new \yii\db\Query();
        $result = $querySpend->select(['user_name','product_id','SUM(spend_num) as spend_num','SUM(rt_spend_num) as rt_spend_num','buy_id'])
            ->from('checkout_list')
            ->andWhere(['between', 'create_at', $beginTime, $endTime])
            ->groupBy(['user_name','product_id'])
            ->all();
        $temp = [];
        foreach($result as $key => $value){
            if($value['buy_id'] == 0){
                $temp[$value['user_name']][$value['product_id']]['package_spend'] = $value['spend_num'];
                $temp[$value['user_name']][$value['product_id']]['real_time_spend'] = $value['rt_spend_num'];
                $temp[$value['user_name']][$value['product_id']]['plan_spend'] = 0;
            }else{
                $temp[$value['user_name']][$value['product_id']]['package_spend'] = 0;
                $temp[$value['user_name']][$value['product_id']]['real_time_spend'] = 0;
                $temp[$value['user_name']][$value['product_id']]['plan_spend'] = $value['spend_num'];
            }

        }
        return  $temp;
    }

}
