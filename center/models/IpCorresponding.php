<?php

namespace center\models;

use Yii;

/**
 * This is the model class for table "ip_corresponding".
 *
 * @property integer $cid
 * @property string $login_user
 * @property string $user_mac
 * @property string $device_ip
 * @property string $switch_port
 * @property string $device_type
 * @property string $in_vlan
 * @property string $out_vlan
 * @property string $qinq
 * @property string $cdevice_ip
 * @property string $cdevice_type
 * @property string $power
 * @property string $locations
 * @property string $install
 * @property integer $created_at
 * @property integer $updated_at
 */
class IpCorresponding extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ip_corresponding';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['login_user', 'user_mac', 'device_ip', 'switch_port', 'device_type', 'in_vlan', 'out_vlan', 'qinq', 'cdevice_ip', 'cdevice_type', 'power', 'locations', 'install', 'created_at', 'updated_at'], 'required'],
            [['created_at', 'updated_at'], 'integer'],
            [['login_user', 'user_mac', 'device_ip', 'cdevice_type', 'power'], 'string', 'max' => 50],
            [['switch_port', 'in_vlan', 'out_vlan'], 'string', 'max' => 20],
            [['device_type', 'qinq', 'cdevice_ip'], 'string', 'max' => 30],
            [['locations', 'install'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'cid' => 'Cid',
            'login_user' => 'Login User',
            'user_mac' => 'User Mac',
            'device_ip' => 'Device Ip',
            'switch_port' => 'Switch Port',
            'device_type' => 'Device Type',
            'in_vlan' => 'In Vlan',
            'out_vlan' => 'Out Vlan',
            'qinq' => 'Qinq',
            'cdevice_ip' => 'Cdevice Ip',
            'cdevice_type' => 'Cdevice Type',
            'power' => 'Power',
            'locations' => 'Locations',
            'install' => 'Install',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * 获取所有的vlan地址
     * */
    public static function getAllVlan(){
        $result = self::find()->asArray()->all();
        $vlanGroup = [];
        if($result){
            foreach ($result as $key => $value){
                $vlanGroup[$value['cid']] = $value['locations'];
            }
            return $vlanGroup;
        }else{
            return false;
        }
    }

    /**
     * 获取vlan区域的名字
     *
     * */
    public static function getAreaName($idArr){
        $result = self::find()->asArray()->indexBy('cid')->all();
        $temp = [];
        foreach ($idArr as $key => $value){
            $temp[] = $result[$value]['area_name'];
        }
        return $temp;
    }
}
