<?php

namespace center\modules\report\models;

use center\models\Pagination;
use common\extend\Excel;
use Yii;

/**
 * This is the model class for table "year_gdp_collect".
 *
 * @property string $id
 * @property string $year
 * @property double $number
 */
class YearGdpCollect extends \yii\db\ActiveRecord
{
    public $start_At;
    public $stop_At;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'year_gdp_collect';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['year'], 'integer'],
            [['number'], 'number'],
            [['start_At', 'stop_At'], 'string']
        ];
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
            $list = $query->orderBy('year asc')->asArray()->all();
            $excelData = [];
            $excelData[0] = ['年份',  'gdp(美元)'];
            foreach ($list as $v) {
                $excelData[] = [
                    $v['year'], $v['number']
                ];
            }

            $title = '年度导出';
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
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'year' => 'Year',
            'number' => 'Number',
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

            $pages = new Pagination([
                'totalCount' => $query->count(),
                'pageSize' => 10
            ]);
            $list = $query->orderBy('year desc')->offset($pages->offset)
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
     * 图形界面
     * @param $param
     * @return array
     */
    public function getStatistics($param)
    {
        try {
            $query = self::find();
            $this->load($param);
            $start = $this->start_At ? $this->start_At : 2002;
            $stop = $this->stop_At ? $this->stop_At : 2016;
            $query->andWhere('year >= :start', [':start' => $start]);
            $query->andWhere('year <= :stop', [':stop' => $stop]);
            $data = $query->select('*')->orderBy('year asc')->asArray()->all();
            $baseX = [];
            $legend = ['gdp', 'finance', 'one_pro', 'two_pro', 'three_pro', 'house', 'other'];
            $seriesData = $xAxis = [];
            $min = $start;
            foreach ($data as $v) {
                $xAxis[] = $v['year'];
                $seriesData[] = $v['number'];
            }
            $series = $this->getLineSeries($seriesData, '国内gdp');
            if (empty($data)) {
                $rs = ['code' => 404, 'msg' => '暂无数据'];
            } else {
                $rs = [
                    'code' => 1,
                    'title' => '国内gdp统计',
                    'series' => json_encode($series),
                    'xAxis' => json_encode($xAxis),
                ];
            }

        } catch (\Exception $e) {
            $rs = ['code' => 500, 'msg' => '发生异常' . $e->getMessage()];
        }

        return $rs;
    }

    /**
     * 组装线性
     * @param $series
     * @param $name
     * @return array
     */

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
