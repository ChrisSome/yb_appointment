<?php

namespace center\models;

/**
 * This is the model class for table "users_group".
 *
 * @property integer $id
 * @property string $name
 * @property string $path
 * @property integer $pid
 * @property integer $level
 * @property integer $status
 * @property string $tid
 */
class UsersGroup extends \yii\db\ActiveRecord
{

    static public $StructureArr;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'users_group';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'path', 'tid'], 'required'],
            [['pid', 'level', 'status'], 'integer'],
            [['name'], 'string', 'max' => 100],
            [['path'], 'string', 'max' => 255],
            [['tid'], 'string', 'max' => 64]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'path' => 'Path',
            'pid' => 'Pid',
            'level' => 'Level',
            'status' => 'Status',
            'tid' => 'Tid',
        ];
    }


    // 根本传过来的参数 返回 本节点以及子节点的 所有ID值.
    public static function getNodeId($array)
    {
        $source = [];

        if (!empty($array) && is_array($array)) {
            foreach ($array as $val) {
                $source[] = $val;
                $model = new UsersGroup();
                $path = $model->findOne($val)->path . '-' . $val;
                $data = $model->find()->andFilterWhere(['like', 'path', $path])->asArray()->all();
                if ($data) {
                    foreach ($data as $value) {
                        $source[] = $value['id'];
                    }
                }
            }
            $source = array_unique($source);
        }

        return $source;
    }


    /**
     * 获取表中所有的数据
     * */
    static public function getAll()
    {
        return self::find()->asArray()->all();
    }


    /**
     * 获取用户分组结构
     *
     * */
    static public function getAllGroup()
    {
        $arr = self::getAll();
        $result = [];
        foreach ($arr as $key => $value) {
            $result[$value['id']] = $value['path'] . "-" . $value['id'];
        }
        return $result;
    }

    /**
     * 获取组织结构数组
     *
     * */
    static public function getStructureArr()
    {
        if (!self::$StructureArr) {
            $result = self::find()->all();
            $temp = [];
            foreach ($result as $key => $value) {
                $tempSt = explode("-", $value['path']);
                if (!is_array($tempSt)) {
                    $number = 1;
                } else {
                    $number = count($tempSt);
                }
                $temp[$number][$value['id']]['name'] = $value['name'];
            }
            self::$StructureArr = $temp;
        }

        return self::$StructureArr;
    }

    /**
     * 获取组织结构层级
     *
     * */
    static public function getLayout($num = null)
    {
        $result = self::find()->all();
        $temp = [];

        foreach ($result as $key => $value) {
            $tempSt = explode("-", $value['path']);

            if (!is_array($tempSt)) {
                $number = 1;
            } else {
                $number = count($tempSt);
            }
            $temp[$number][$value['pid']][$value['id']] = $value['name'];
        }

        if ($num) {
            $temp = array_values($temp[$num])[0];
        }
        return $temp;
    }

    /**
     * 获取每层组织结构的冗余结构
     *
     * */
    static public function getLayoutStructure()
    {
        $result = self::find()->all();
        $temp = [];
        foreach ($result as $key => $value) {
            $num = count(explode('-', $value['path'])) - 1;
            $temp[$num][] = self::getRealStructure($value['path'] . '-' . $value['id']);
        }
        return $temp;
    }


    /**
     * 获取第一层下的三层结构
     *
     * */
    static public function getChildLayout($id)
    {
        if (!is_numeric($id)) {
            return 0;
        }
        $layoutArr = self::getLayout();
        $arr = isset($layoutArr[3][$id]) ? $layoutArr[3][$id] : 0;
        if (!$arr) {
            return false;
        }
        $result[1] = $arr;
        $temp1 = [];
        //第二层结构
        foreach ($arr as $key => $value) {
            if (isset($layoutArr[4][$key])) {
                foreach ($layoutArr[4][$key] as $k => $val) {
                    $temp1[$k] = $val;
                }
            } else {
                continue;
            }
        }
        //第三层结构
        if (!empty($temp1)) {
            $result[2] = $temp1;
            $temp2 = [];
            foreach ($temp1 as $key => $value) {
                if (isset($layoutArr[5][$key])) {
                    foreach ($layoutArr[5][$key] as $k => $val) {
                        $temp2[$k] = $val;
                    }
                } else {
                    continue;
                }
            }
            if (!empty($temp2)) {
                $result[3] = $temp2;
            }
        }
        $result[1] = array_keys(array_flip($result[1]));
        $temp = [];
        foreach ($result[1] as $key => $value) {
            $temp[1][$value] = $value;
        }
        if (isset($result[2])) {
            $result[2] = array_keys(array_flip($result[2]));
            foreach ($result[2] as $key => $value) {
                $temp[2][$value] = $value;
            }
        }
        if (isset($result[3])) {
            $result[3] = array_keys(array_flip($result[3]));
            foreach ($result[3] as $key => $value) {
                $temp[3][$value] = $value;
            }
        }
        return $temp;
    }

    /**
     *获取所有基础冗余组织结构
     *
     * */
    static public function getAllBaseStructure()
    {
        $arr = self::getAll();
        $temp = [];
        foreach ($arr as $key => $value) {
            $path = $value['path'] . '-' . $value['id'];
            $temp[] = self::getRealStructure($path);
        }
        return $temp;
    }


    /**
     * 根据id或者id数组获取冗余组织结构
     *
     * */
    static public function getRealStructureById($groupId)
    {
        //获取组织结构列表
        $arr_group = UsersGroup::getAllGroup();
        $groupArr = [];
        if (is_array($groupId)) {
            foreach ($groupId as $key => $value) {
                if (!isset($arr_group[$value])) {
                    return false;
                }
                $groupArr[] = self::getRealStructure($arr_group[$value]);
            }
            return $groupArr;
        } else {
            if (!isset($arr_group[$groupId])) {
                return false;
            }
            $path = $arr_group[$groupId];
            return self::getRealStructure($path);
        }
    }


    /**
     *根据path获取冗余组织结构
     *
     * */
    static public function getRealStructure($path)
    {
        $arr = self::getStructureArr();
        $tempSt = explode('-', $path);
        $result = '';
        foreach ($tempSt as $key => $value) {
            if ($value == 0) {
                continue;
            }
            $result = $result . '-' . $arr[$key][$value]['name'];
        }
        return $result;
    }

    /**
     * 获取group的名称
     *
     * */
    static public function getGroupName($groupId, $type = 'str')
    {
        $nameArr = self::getNameArr();
        if (is_array($groupId)) {
            $result = [];
            foreach ($groupId as $key => $value) {
                $name = isset($nameArr[$value])?$nameArr[$value]:$value;
                $name = $name == '/'?\Yii::t('app','All Group'):$name;
                $result[] = $name;
            }
            if($type == 'str'){
                $result = implode(',',$result);
            }
        }else if($groupId == null){
            return \Yii::t('app','All Group');
        }
        return $result;
    }

    /**
     * 获取所有group的名称id对应数组
     *
     * */
    static public function getNameArr()
    {
        $nameAll = [];
        if ($result = self::getAll()) {
            foreach ($result as $key => $value) {
                $nameAll[$value['id']] = $value['name'] ;
            }
            return $nameAll;
        }


    }
}