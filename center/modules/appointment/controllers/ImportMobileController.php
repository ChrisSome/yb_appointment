<?php

namespace center\modules\appointment\controllers;

use center\controllers\ValidateController;
use center\models\Pagination;
use common\extend\Excel;
use common\extend\OfficesTool;
use common\models\FileOperate;
use League\Csv\Reader;
use Yii;
use center\modules\appointment\models\ImportMobile;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * ImportMobileController implements the CRUD actions for ImportMobile model.
 */
class ImportMobileController extends ValidateController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all ImportMobile models.
     * @return mixed
     */
    public function actionIndex()
    {
        $model =  new ImportMobile();
        $params = Yii::$app->request->queryParams;
        $params['showField'] = $model->default_field;
        $query = ImportMobile::find();
        $pagesSize = 20; // 每页条数
        foreach ($params as $k => $v) {
            if (!empty($v) &&  array_key_exists($k, $model->getSearchField())) {
                switch ($k) {
                    case 'start_time':
                        $query->andWhere('import_time >= :start', [':start' => strtotime($v)]);
                        break;
                    case 'stop_time':
                        $query->andWhere('import_time <= :end', [':end' => strtotime($v)]);
                        break;
                    default:
                        $query->andWhere([$k => $v]);
                        break;
                }

            }
        }

        //排序
        if (isset($params['orderBy']) && array_key_exists($params['orderBy'], $model->searchField)) {
            $query->orderBy([$params['orderBy'] => $params['sort'] == 'desc' ? SORT_DESC : SORT_ASC]);
        } else {
            $query->orderBy(['id' => SORT_DESC]);
        }

        $pagination = new Pagination([
            'totalCount' => $query->count(),
            'pageSize' => $pagesSize
        ]);
        $list = $query->select($model->default_field)->offset($pagination->offset)
            ->limit($pagination->limit)
            ->asArray()
            ->all();


        return $this->render('index', [
            'pagination' => $pagination,
            'list' => $list,
            'model' => $model,
            'params' => $params
        ]);
    }

    /**
     * Displays a single ImportMobile model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new ImportMobile model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ImportMobile();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            Yii::$app->getSession()->setFlash('error', $model->getErrors('mobile')[0]);
            return $this->redirect('index');
        }
    }

    /**
     * Updates an existing ImportMobile model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing ImportMobile model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $rs = $model->delete();
        if ($rs) {
            Yii::$app->getSession()->setFlash('success', '删除号码'.$model->mobile.'成功');
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the ImportMobile model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ImportMobile the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ImportMobile::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    public function actionBatch()
    {
        return $this->render('batch');
    }

    public function actionPreview()
    {
        $model = new ImportMobile();
        $model->file = UploadedFile::getInstance($model, 'file');
        if ($model->file) {
            $newFileName = FileOperate::dir('import') . '/batch' . '_' . date('YmdHis') . rand(100, 999) . '.' . $model->file->extension;
            $model->file->saveAs($newFileName);
        } else {
            Yii::$app->getSession()->setFlash('error', Yii::t('app', 'batch excel help7'));

            return $this->redirect('index');
        }

        if (preg_match('/\.csv$/', $newFileName)) {
            ini_set("auto_detect_line_endings", '1');
            $oReader = Reader::createFromPath($newFileName);
            $oReader->setEnclosure("'");

            $excelData = [];
            $i = 0;
            $oReader->each(function ($row) use (&$excelData, &$i) {
                if (preg_match('/^1/', $row[0]) && $i < 10) {
                    $excelData[0][] = [$row[0]];
                    $i++;
                }

                return true;
            });
        } else if (preg_match('/\.xlsx$/', $newFileName)) {
            $excelData = [];
            $officesTool = new OfficesTool();
            foreach ($officesTool->readExecl($newFileName) as $sheet => $vals) {
               if ($sheet == 0) {
                   $excelData[0] = $vals;
               }

            }
        } else {
            $excelData = Excel::set_file($newFileName);
        }

        //var_dump($count, $excelData);exit;
        $model->excelData = $excelData[0];
        //如果小于等于1行数据，那么是个空表格
        if (count($model->excelData) < 1) {
            Yii::$app->getSession()->setFlash('error', Yii::t('app', 'batch excel help6'));

            return $this->redirect('index');
        }

        Yii::$app->session->set('batch_excel_import', [
            'fileName' => $newFileName, //文件名
        ]);

        return $this->render('excel_preview', [
            'model' => $model,
        ]);
    }


    public function actionOperate()
    {
        set_time_limit(0);
        ini_set('memory_limit', '2048M');
        //$post = Yii::$app->request->post();
        $error = '';
        $db = Yii::$app->db;
        $trans = $db->beginTransaction();
        try {
            /*读取临时文件*/
            $model = new ImportMobile();
            $session = Yii::$app->session->get('batch_excel_import');
            $sFileName = $session['fileName'];

            $rs = $model->import_datas($sFileName);


            /*调用模板发送短信*/
            //$rs = $model->import_data();
            if ($rs['code'] != 1) {
                $error = $rs['msg'];
            } else {
                Yii::$app->getSession()->setFlash('success', $rs['msg']);
            }
            $trans->commit();
        } catch (\Exception $e) {
            $trans->rollBack();
            var_dump($e->getMessage());
            $error = "服务器异常，稍后重试~~".$e->getTraceAsString();
        }
        if ($error) {
            Yii::$app->getSession()->setFlash('error', $error);
        }

        return $this->redirect('batch');
    }

    /**
     * 下载文件
     * @return \yii\console\Response|\yii\web\Response
     */
    public function actionDownload()
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
        return $this->redirect(['index']);
    }
}
