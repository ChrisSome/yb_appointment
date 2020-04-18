<?php

namespace center\models;

use Yii;

/**
 * This is the model class for table "ip_area".
 *
 * @property integer $area_id
 * @property string $area_name
 * @property string $area_ip_start
 * @property string $area_ip_end
 * @property string $area_desc
 */
class IpArea extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ip_area';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['area_name', 'area_desc'], 'required'],
            [['area_ip_start', 'area_ip_end'], 'integer'],
            [['area_name', 'area_desc'], 'string', 'max' => 128],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'area_id' => 'Area ID',
            'area_name' => 'Area Name',
            'area_ip_start' => 'Area Ip Start',
            'area_ip_end' => 'Area Ip End',
            'area_desc' => 'Area Desc',
        ];
    }

    /**
     * 获取所有的区域数组
     * */
    public static function getAllArea(){
        $result = self::find()->asArray()->all();
        if($result){
            foreach ($result as $key => $value){
                $areaGroup[$value['area_id']] = $value['area_name'];
            }
            return $areaGroup;
        }else{
            return false;
        }
    }

    /**
     * 获取ip区域的名字
     *
     * */
    public static function getAreaName($idArr){
        $result = self::find()->asArray()->indexBy('area_id')->all();
        $temp = [];
        foreach ($idArr as $key => $value){
            $temp[] = $result[$value]['area_name'];
        }
        return $temp;
    }
}
