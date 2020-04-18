<?php
/**
 * Created by PhpStorm.
 * User: sihuo
 * Date: 2018/3/19
 * Time: 17:41
 */

namespace center\modules\report\controllers;

use center\modules\report\models\CollectModel;
use center\modules\report\models\CountryGdpCollect;
use center\modules\report\models\YearGdpCollect;
use center\modules\report\models\Zone;
use common\extend\Excel;
use common\models\FileOperate;
use yii;
use center\controllers\ValidateController;
use center\modules\report\models\UserCard;

/**
 * 身份证号码管理
 * Class CardController
 * @package center\modules\report\controllers
 */
class FinancialController extends ValidateController
{

    /**
     * 国内gdp
     * @return string
     */
    public function actionIndex()
    {
        $model = new YearGdpCollect();
        $param = Yii::$app->request->queryParams;
        $listRs = $model->getList($param);
        $list = isset($listRs['data']) ? $listRs['data'] : [];
        $pages = isset($listRs['pages']) ? $listRs['pages'] : [];

        return $this->render('index', [
            'model' => $model,
            'list' => $list,
            'pagination' => $pages,
            'params' => $param
        ]);
    }

    /**
     * 国家gdp变化
     * @return string
     */
    public function actionCountry()
    {
        $model = new CountryGdpCollect();
        $param = Yii::$app->request->queryParams;
        $listRs = $model->getList($param);
        $list = isset($listRs['data']) ? $listRs['data'] : [];
        $pages = isset($listRs['pages']) ? $listRs['pages'] : [];

        return $this->render('country', [
            'model' => $model,
            'list' => $list,
            'pagination' => $pages,
            'params' => $param
        ]);
    }

    /**
     * 导出国内信息
     * @return yii\web\Response
     */
    public function actionExport()
    {
        $model = new YearGdpCollect();
        $param = Yii::$app->request->queryParams;
        $model->export($param);

        return $this->redirect('index');
    }

    /**
     * 导出国际gdp信息
     * @return yii\web\Response
     */
    public function actionExportCountry()
    {
        $model = new CountryGdpCollect();
        $param = Yii::$app->request->queryParams;
        $model->export($param);

        return $this->redirect('index');
    }

    /**
     * @return string
     */
    public function actionYear()
    {
        $model = new YearGdpCollect();
        $param = Yii::$app->request->queryParams;
        if (!empty($param)) {
            $model = $param['type'] == 'country' ? new CountryGdpCollect() : new YearGdpCollect();
        }
        $rs = $model->getStatistics($param);

        return $this->render('year', [
            'data' => $rs,
            'model' => $model,
            'params' => $param
        ]);
    }


    /**
     * 增加测试数据
     * @return string
     */
    public function actionAdd()
    {
        $model = new Zone();
        $session = Yii::$app->session->get('batch_excel');
        if ($session && isset($session['selectField'])) {
            $model->selectField = $session['selectField'];
        }

        return $this->render('add', [
            'model' => $model
        ]);
    }

    /**
     * 预览界面
     * @return string
     */
    public function actionPreview()
    {
        set_time_limit(0);
        //提交的数据
        //var_dump($_POST);exit;
        $post = Yii::$app->request->post();
        //var_dump($post, $post['batchType'] == 7 && !empty($post['export_group_id']));exit;
        if (isset($post['download'])) {
            header('location:download', true, 307);
            exit;
        }
        $model = new Zone();
        $model->selectField = isset($post['addSelectField']) ? $post['addSelectField'] : [];
        if (!in_array('year', $model->selectField) || (!in_array('province_name', $model->selectField))) {
            Yii::$app->getSession()->setFlash('error', Yii::t('app', '年份和地区必不可少'));
            return $this->redirect('add');
        }
        $model->file = yii\web\UploadedFile::getInstance($model, 'file');
        if ($model->file) {
            $newFileName = FileOperate::dir('import') . '/batch' . '_' . date('YmdHis') . rand(100, 999) . '.' . $model->file->extension;
            $model->file->saveAs($newFileName);
        } else {
            Yii::$app->getSession()->setFlash('error', Yii::t('app', 'batch excel help7'));

            return $this->redirect('add');
        }

        $excelData = Excel::set_file($newFileName);
        //var_dump($count, $excelData);exit;
        $model->excelData = $excelData[0];
        //保存在session中
        Yii::$app->session->set('batch_excel', [
            'selectField' => $model->selectField, //选择的字段
            'fileName' => $newFileName, //文件名
        ]);

        return $this->render('preview', [
            'model' => $model
        ]);
    }

    /**
     * 下载模板
     * @return $this|yii\web\Response
     */
    public function actionDownload()
    {
        $model = new  Zone();
        $post = Yii::$app->request->post();
        foreach ($post as $k => $v) {
            if ($model->hasProperty($k)) {
                if ($k == 'export_group_id' && !empty($v)) {
                    $v = explode(',', $v);
                }
                $model->$k = $v;
            }
        }
        //已选择的字段
        $model->selectField = isset($post['addSelectField']) ? $post['addSelectField'] : [];
        //导入模式， 用户名和密码 必需, 2015/7/14改为密码可以输入MD5

        if (!in_array('year', $model->selectField) || (!in_array('province_name', $model->selectField))) {
            Yii::$app->getSession()->setFlash('error', Yii::t('app', '年份和地区必不可少'));
            return $this->redirect('add');
        }
        $res = $model->template();
        if ($res) {
            $file = FileOperate::dir('temp') . '/user_template_' . date('YmdHis') . '.xls';
            $title = Yii::t('app', 'batch excel help1');
            Excel::arrayToExcel($res, $file, $title);


            return Yii::$app->response->sendFile($file);
        }

        return $this->redirect('add');
    }

    /**
     * 批量excel处理
     * @return yii\web\Response
     */
    public function actionOperate()
    {
        $model = new Zone();
        $session = Yii::$app->session->get('batch_excel');
        //session 不存在了
        if (empty($session['selectField']) || empty($session['fileName'])) {
            Yii::$app->getSession()->setFlash('error', Yii::t('app', 'batch excel help8'));

            return $this->redirect('add');
        }
        //excel文件已过期
        if (!is_file($session['fileName'])) {
            Yii::$app->getSession()->setFlash('error', Yii::t('app', 'batch excel help9'));

            return $this->redirect('add');
        }
        $excelData = Excel::set_file($session['fileName']);
        $model->excelData = $excelData[0]; //excel 数据
        $model->selectField = $session['selectField'];
        $res = $model->batch_data();

        if ($res) {
            //Yii::$app->session->set('batch_excel', '');//删除session
            Yii::$app->session->set('batch_excel', ['fileName' => '']);
            $file = FileOperate::dir('account') . '/user_excel_' . '_' . date('YmdHis') . '.xls';
            $title = Yii::t('app', 'batch excel help11');
            //将内容写入excel文件
            Excel::arrayToExcel($res['list'], $file, $title);
            //设置下载文件session
            Yii::$app->session->set('batch_excel_download_file', $file);

            //日志结束
            Yii::$app->getSession()->setFlash('success', Yii::t('app', 'batch excel help10', [
                'ok_num' => $res['ok'],
                'err_num' => $res['err'],
            ]));

            return $this->redirect('add');
        } else

            return $this->redirect('add');
    }

    /**
     * 采集数据
     * @return string
     */
    public function actionCollect()
    {
        $param = Yii::$app->request->queryParams;
        if (!empty($param)) {
            //开始用phpQuery采集数据
            $model = new CollectModel();
            $model->type = $param['type'];
            $rs = $model->getCollectResult($param);
        }
        return $this->render('collect', [
            'params' => $param
        ]);
    }

    /**
     * @return $this|yii\web\Response
     */
    public function actionDownloadFile()
    {
        //下载文件
        if (Yii::$app->request->get('file')) {
            return Yii::$app->response->sendFile(Yii::$app->request->get('file'));

        }
        if (Yii::$app->session->get('batch_excel_download_file')) {
            return Yii::$app->response->sendFile(Yii::$app->session->get('batch_excel_download_file'));
        } else {
            Yii::$app->getSession()->setFlash('error', Yii::t('app', 'batch excel help31'));
        }
        return $this->redirect(['collect']);

    }
}