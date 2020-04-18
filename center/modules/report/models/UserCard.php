<?php

namespace center\modules\report\models;

use center\models\Pagination;
use common\extend\Excel;
use Yii;

/**
 * This is the model class for table "user_card".
 *
 * @property string $id
 * @property string $card_number
 * @property string $birth_year
 * @property string $birth_month
 * @property string $birth_day
 * @property string $birth
 * @property string $sex
 * @property string $province
 */
class UserCard extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_card';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['birth_year', 'birth_month', 'birth_day'], 'integer'],
            [['card_number'], 'string', 'max' => 18],
            [['birth', 'province'], 'string', 'max' => 12],
            [['sex'], 'string', 'max' => 6],
            [['card_number'], 'unique'],
            [['start_At', 'stop_At'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'card_number' => 'Card Number',
            'birth_year' => 'Birth Year',
            'birth_month' => 'Birth Month',
            'birth_day' => 'Birth Day',
            'birth' => 'Birth',
            'sex' => 'Sex',
            'province' => 'Province',
        ];
    }

    public function getAttributesList()
    {
        //1校内新闻2大赛新闻3行业新闻
        return [
            'status' => [
                0 => '待审核',
                1 => '审核通过',
                2 => '未通过'
            ]
        ];
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
            'card_number' => [
                'label' => Yii::t('app', '身份号码')
            ],
            'province' => [
                'label' => Yii::t('app', '省份')
            ],

        ], $exField);
    }

    /**
     * 获取列表
     * @param $param
     * @return array
     */
    public function getList($param)
    {
        try {
            $query = self::find();
            foreach ($param as $k => $v) {
                if (!empty($v) && $this->hasAttribute($k)) {
                    if ($k == 'birth') {
                        $query->andWhere(["$k >= :$k", [":$k" => $v]]);
                    } else if ($k == 'card_number') {
                        $query->andWhere("$k like :$k", [":$k" => '%' . $v . "%"]);
                    } else {
                        $query->andWhere([$k => $v]);
                    }
                }
            }
            $pages = new Pagination([
                'totalCount' => $query->count(),
                'pageSize' => 10
            ]);
            $list = $query->orderBy('birth asc')->offset($pages->offset)
                ->limit($pages->limit)
                ->asArray()
                ->all();
            $rs = ['code' => 1, 'data' => $list, 'pages' => $pages];
        } catch (\Exception $e) {
            $rs = ['code' => 500, 'msg' => '发生异常'];
        }

        return $rs;
    }

    /**
     * 导出
     * @param $param
     * @return array
     */
    public function export($param)
    {
        try {
            $query = self::find();
            foreach ($param as $k => $v) {
                if (!empty($v) && $this->hasAttribute($k)) {
                    if ($k == 'birth') {
                        $query->andWhere(["$k >= :$k", [":$k" => $v]]);
                    } else if ($k == 'card_number') {
                        $query->andWhere("$k like :$k", [":$k" => '%' . $v . "%"]);
                    } else {
                        $query->andWhere([$k => $v]);
                    }
                }
            }
            $list = $query->orderBy('birth asc')->asArray()->all();
            $excelData = [];
            $excelData[0] = ['身份证号码', '出生日期', '性别', '属相', '星座'];
            foreach ($list as $v) {
                $excelData[] = [
                    $v['card_number'], $v['birth'], $v['sex'], $v['shuxiang'], $v['xingzuo']
                ];
            }
            $title = '身份证导出';
            Excel::header_file($excelData, $title . '.xls', $title);

            exit;
        } catch (\Exception $e) {
            Yii::$app->getSession()->setFlash('error', '发生异常');
            $rs = ['code' => 500, 'msg' => '发生异常'];
        }

        return $rs;
    }

    public $start_At;
    public $stop_At;
    public function getStatisticsByYear($param)
    {
        try {
            $query = self::find();
            $this->load($param);
            if (!empty($this->start_At)) {
                $query->andWhere('birth_year >= :start', [':start' => $this->start_At]);
            }
            if (!empty($this->stop_At)) {
                $query->andWhere('birth_year <= :stop', [':stop' => $this->stop_At]);
            }

            if (!empty($this->province)) {
                $query->andWhere(['province' => $this->province]);
            }
            $extremum = $query->select("max(birth_year) max, min(birth_year) min")->asArray()->one();
            $max = $extremum['max'];
            $min = $extremum['min'];
            $xAxis = [];
            $data = $query->select("count(id) number, birth_year")
                ->groupBy('birth_year')
                ->indexBy('birth_year')
                ->asArray()
                ->all();
            $yAxis = [];
            for ($i = $min; $i <= $max; $i++) {
                $xAxis[] = $i;
                $yAxis[] = isset($data[$i]) ? $data[$i]['number'] : 0;
            }
            $series = $this->getLineSeries('人数', $yAxis);
            $rs = [
                'code' => 1,
                'title' => '年度人口统计',
                'legends' => json_encode(['人数']),
                'series' => json_encode($series),
                'xAxis' => json_encode($xAxis),
            ];
        } catch (\Exception $e) {
            $rs = ['code' => 500, 'msg' => '发生异常' . $e->getMessage()];
        }

        return $rs;
    }

    /**
     * 按地区统计
     * @param $param
     * @return array
     */
    public function getStatisticsByProvince($param, $fields = 'province')
    {
        try {
            $query = self::find();
            $this->load($param);
            if (!empty($this->start_At)) {
                $query->andWhere('birth_year >= :start', [':start' => $this->start_At]);
            }
            if (!empty($this->stop_At)) {
                $query->andWhere('birth_year <= :stop', [':stop' => $this->stop_At]);
            }

            if (!empty($this->province)) {
                $query->andWhere(['province' => $this->province]);
            }
            $xAxis = [];
            $data = $query->select("count(id) number, $fields")
                ->groupBy($fields)
                ->indexBy($fields)
                ->asArray()
                ->all();
            $yAxis = [];
            foreach ($data as $province => $datum) {
                $yAxis[] = ['name' => $province, 'value' => $datum['number']];
            }
            $xAxis = array_keys($data);
            //$series = $this->getLineSeries('人数', $yAxis);
            $title = '按月统计';
            if ($fields == 'province') {
                $title = '按地区统计';
            } else if ($fields == 'shuxiang') {
                $title = '按属相统计';
            } else if ($fields == 'sex'){
                $title = '按性别统计';
            } else {
                $title = '按星座统计';
            }
            $rs = [
                'code' => 1,
                'title' => $title,
                'legends' => json_encode(['人数']),
                'series' => json_encode($yAxis),
                'xAxis' => json_encode($xAxis),
            ];
        } catch (\Exception $e) {
            $rs = ['code' => 500, 'msg' => $e->getLine().'发生异常' . $e->getMessage()];
        }

        return $rs;
    }

    /**
     * 按月统计
     * @param $param
     * @return array
     */
    public function getStatisticsByMonth($param)
    {
        try {
            $query = self::find();
            $this->load($param);
            if (!empty($this->start_At)) {
                $query->andWhere('birth_year >= :start', [':start' => $this->start_At]);
            }
            if (!empty($this->stop_At)) {
                $query->andWhere('birth_year <= :stop', [':stop' => $this->stop_At]);
            }

            if (!empty($this->province)) {
                $query->andWhere(['province' => $this->province]);
            }
            $xAxis = [];
            $data = $query->select("count(id) number, birth_month")
                ->groupBy('birth_month')
                ->indexBy('birth_month')
                ->asArray()
                ->all();
            $yAxis = [];
            for ($i = 1; $i <= 12; $i++) {
                $xAxis[] = $i.'月';
                $yAxis[] = ['name' => $i.'月', 'value' => isset($data[$i]) ? $data[$i]['number'] : 0];
            }
            //$series = $this->getLineSeries('人数', $yAxis);
            $rs = [
                'code' => 1,
                'title' => '按月统计',
                'legends' => json_encode(['人数']),
                'series' => json_encode($yAxis),
                'xAxis' => json_encode($xAxis),
            ];
        } catch (\Exception $e) {
            $rs = ['code' => 500, 'msg' => $e->getLine().'发生异常' . $e->getMessage()];
        }

        return $rs;
    }
    /**
     * 打包数据
     * @param $key
     * @param $data
     * @return array
     */
    public function getLineSeries($key, $data)
    {
        $result = [];
        $object = new \stdClass();
        $object->type = 'line';
        $object->name = $key;
        $object->data = $data;
        $object->symbol = true;
        $object->sampling = 'average';
        $object->symbol = 'none';
        $object->areaStyle = ['normal' => []];
        $result = $object;

        return $result;
    }

}
