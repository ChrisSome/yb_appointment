<?php
/**
 * Created by PhpStorm.
 * User: sihuo
 * Date: 2018/4/22
 * Time: 11:30
 */

namespace center\modules\report\models;


use common\extend\Excel;
use common\models\FileOperate;
use yii\base\Model;
use yii;
use yii\helpers\Url;

require_once 'phpQuery.php';

class CollectModel extends Model
{

    public $type;
    public $url = [
        'country' => 'http://www.kuaiyilicai.com/stats/global/yearly/g_gnp_per_capita/',
        'gdp' => 'http://www.kuaiyilicai.com/stats/global/yearly_per_country/g_gdp_per_capita/chn.html',
    ];

    /**
     * 获取gdp采集
     * @param $url
     * @return array
     */
    protected function getGdpCollect($url)
    {
        \phpQuery::newDocumentFile($url);
        $companies = pq('table[class="table"]>tbody>tr');
        //使用dom方式匹配需要的部分信息。会返回该匹配的对象
        $data = $excelData = [];
        $excelData[] = ['年份', '人均gdp'];
        foreach ($companies as $company) {
            $year = trim(pq($company)->find('td')->eq(0)->text());
            $number = trim(pq($company)->find('td')->eq(1)->text());
            if (!preg_match('/window/', $year)) {
                $one = [
                    'year' => $year,
                    'number' => $number
                ];
                $data[] = $one;
                $excelData[] = array_values($one);
            }
        }
        $db = Yii::$app->db;
        if (!empty($data)) {
            //写入表中
            $db->createCommand()->delete('year_gdp_collect')->execute();
            $db->createCommand()->batchInsert('year_gdp_collect', ['year', 'number'], $data)->execute();
        }
        //写入文件提供下载
        $file = FileOperate::dir('other') . '/' . $this->type . '.xls';
        Excel::arrayToExcel($excelData, $file, $this->type);
        $logContent = Yii::t('app', 'collect success gdp', [
            'file' => Yii::t('app', 'down info', ['download_url' => Url::to(['/report/financial/download-file?file=' . $file])]),
        ]);
        $rs = ['code' => 1, 'msg' => 'success', 'file' => $file];
        Yii::$app->getSession()->setFlash('success', $logContent);

        return $rs;
    }

    /**
     * 采集国家gdp数据
     * @param $url
     * @param $year
     * @return array
     */
    protected function getCountryCollect($url, $year)
    {
        $url .= $year . '.html';
        \phpQuery::newDocumentFile($url);
        $companies = pq('table[class="table"]>tbody>tr');
        //使用dom方式匹配需要的部分信息。会返回该匹配的对象
        $data = $excelData = [];
        $excelData[] = ['国家', '所在州', '年份', '人均gdp', '年度排名'];
        foreach ($companies as $company) {
            $country = trim(pq($company)->find('td')->eq(0)->text());
            $zhou = trim(pq($company)->find('td')->eq(1)->text());
            $number = trim(pq($company)->find('td')->eq(2)->text());
            $order = trim(pq($company)->find('td')->eq(3)->text());
            if (!preg_match('/window/', $country)) {
                if (preg_match('/万/', $number)) {
                    $number *= 10000;
                }
                $one = [
                    'country' => $country,
                    'zhou' => $zhou,
                    'year' => $year,
                    'number' => $number,
                    'sort_order' => $order
                ];
                $data[] = $one;
                $excelData[] = array_values($one);
            }
        }
        $db = Yii::$app->db;
        if (!empty($data)) {
            //写入表中
            $db->createCommand()->delete('country_gdp_collect','year=:year', [':year' => $year])->execute();
            $db->createCommand()->batchInsert('country_gdp_collect', ['country', 'zhou', 'year', 'number', 'sort_order'], $data)->execute();
        }
        //写入文件提供下载
        $file = FileOperate::dir('other') . '/' . $this->type . '.xls';
        Excel::arrayToExcel($excelData, $file, $this->type);
        $logContent = Yii::t('app', 'collect success country', [
            'file' => Yii::t('app', 'down info', [
                'download_url' => Url::to(['/report/financial/download-file?file=' . $file]),
            ]),
            'year' => $year
        ]);
        $rs = ['code' => 1, 'msg' => 'success', 'file' => $file];
        Yii::$app->getSession()->setFlash('success', $logContent);

        return $rs;
    }

    /**
     * 获取数据采集结果，并写入对应表
     * @param $param
     * @return array
     */
    public function getCollectResult($param)
    {
        $this->type = $this->type ? $this->type : 'gdp';
        $url = $this->url[$this->type];
        $year = isset($param['year']) ? $param['year'] : 2016;
        try {
            switch ($this->type) {
                case 'gdp':
                    $rs = $this->getGdpCollect($url);
                    break;
                case 'country':
                    $rs = $this->getCountryCollect($url, $year);
                    break;
                default:
                    $this->getGdpCollect($url);
            }
        } catch (\Exception $e) {
            $rs = ['code' => 500, 'msg' => '发生异常' . $e->getMessage()];
        }

        return $rs;
    }
}