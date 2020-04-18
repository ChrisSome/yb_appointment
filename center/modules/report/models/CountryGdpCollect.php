<?php

namespace center\modules\report\models;

use common\extend\Excel;
use Yii;
use center\models\Pagination;

/**
 * This is the model class for table "country_gdp_collect".
 *
 * @property string $id
 * @property string $zhou
 * @property string $country
 * @property string $year
 * @property double $number
 * @property integer $sort_order
 */
class CountryGdpCollect extends \yii\db\ActiveRecord
{
    public $start_At;
    public $stop_At;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'country_gdp_collect';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['year', 'sort_order'], 'integer'],
            [['number'], 'number'],
            [['zhou'], 'string', 'max' => 30],
            [['country'], 'string', 'max' => 64],
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
            'zhou' => 'Zhou',
            'country' => 'Country',
            'year' => 'Year',
            'number' => 'Number',
            'sort_order' => 'Sort Order',
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
            'country' => [
                'label' => Yii::t('app', '国家名称')
            ],
            'zhou' => [
                'label' => Yii::t('app', '所在州')
            ],
            'start_At' => [
                'label' => Yii::t('app', '开始年份')
            ],
            'stop_At' => [
                'label' => Yii::t('app', '结束年份')
            ],

        ], $exField);
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
     * 获取列表
     * @param $param
     * @return array
     */
    public function getList($param)
    {
        try {
            $query = self::find();
            $this->load($param, '');
            if (!empty($this->start_At)) {
                $query->andWhere('year >= :start', [':start' => $this->start_At]);
            }
            if (!empty($this->stop_At)) {
                $query->andWhere('year <= :stop', [':stop' => $this->stop_At]);
            }
            if (!empty($this->country)) {
                $query->andWhere('country like :country', [':country' => '%'.$this->country]);
            }
            if (!empty($this->zhou)) {
                $query->andWhere('zhou = :zhou', [':zhou' => $this->zhou]);
            }


            $pages = new Pagination([
                'totalCount' => $query->count(),
                'pageSize' => 10
            ]);
            $list = $query->orderBy('year desc, sort_order asc')->offset($pages->offset)
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
            $this->load($param, '');
            if (!empty($this->start_At)) {
                $query->andWhere('year >= :start', [':start' => $this->start_At]);
            }
            if (!empty($this->stop_At)) {
                $query->andWhere('year <= :stop', [':stop' => $this->stop_At]);
            }
            if (!empty($this->country)) {
                $query->andWhere('country like :country', [':country' => '%'.$this->country]);
            }
            if (!empty($this->zhou)) {
                $query->andWhere('zhou = :zhou', [':zhou' => $this->zhou]);
            }

            $list = $query->orderBy('year asc, sort_order asc')->asArray()->all();
            $excelData = [];
            $excelData[0] = ['国家', '所在州', '年份', '人均gdp（美元计）', '年度排名'];
            foreach ($list as $v) {
                $excelData[] = [
                    $v['country'], $v['zhou'], $v['year'], $v['number'], $v['sort_order']
                ];
            }

            $title = '国际导出';
            Excel::header_file($excelData, $title . '.xls', $title);

            exit;
        } catch (\Exception $e) {
            Yii::$app->getSession()->setFlash('error', '发生异常');
            //var_dump($e->getMessage());exit;
            $rs = ['code' => 500, 'msg' => '发生异常'];
        }

        return $rs;
    }
    /**
     * 图形界面
     * @param $param
     * @return array
     */
    public function getStatistics($param)
    {
        try {
            $query = self::find();
            $this->load($param, '');
            $query->andWhere('year = :year', [':year' => $this->year]);
            $data = $query->select('*')->orderBy('year asc')->asArray()->all();
            $baseX = [];
            $legend = ['gdp', 'finance', 'one_pro', 'two_pro', 'three_pro', 'house', 'other'];
            $seriesData = $xAxis = [];
            foreach ($data as $v) {
                $xAxis[] = $v['country'];
                $seriesData[] = $v['number'];
            }
            $series = $this->getLineSeries($seriesData, '国际gdp');
            if (empty($data)) {
                $rs = ['code' => 404, 'msg' => '暂无数据'];
            } else {
                $rs = [
                    'code' => 1,
                    'title' => '国际gdp统计',
                    'subText' => '年份:'. $this->year,
                    'series' => json_encode($series),
                    'xAxis' => json_encode($xAxis),
                ];
            }

        } catch (\Exception $e) {
            $rs = ['code' => 500, 'msg' => '发生异常' . $e->getMessage()];
        }

        return $rs;
    }

    public function getLineSeries($series, $name) {
        $object = [];
        $object[0] = new \StdClass();
        $object[0]->name = $name;
        $object[0]->type = 'line';
        $object[0]->areaStyle = new \StdClass();
        $object[0]->areaStyle->normal = json_encode([]);
        $object[0]->data = $series;

        return $object;
    }
}
