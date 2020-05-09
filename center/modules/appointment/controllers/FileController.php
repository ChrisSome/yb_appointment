<?php


namespace center\modules\appointment\controllers;


use center\modules\appointment\models\ImportFiles;
use common\models\FileOperate;
use Yii;
use center\models\Pagination;
use center\controllers\ValidateController;
use center\modules\appointment\models\UserAppointment;
use yii\web\UploadedFile;

class FileController extends ValidateController
{
    /**
     * 预约首页
     * @return string
     */
    public function actionIndex()
    {
        $model = new ImportFiles();
        $params = Yii::$app->request->queryParams;
        $params['showField'] = $model->default_field;
        $query = ImportFiles::find();
        $pagesSize = isset($params['offset']) ? ($params['offset'] ? $params['offset'] : 20) : 20; // 每页条数
        foreach ($params as $k => $v) {
            if ((!empty($v) || preg_match('/^0$/', $v)) &&  array_key_exists($k, $model->getSearchField())) {
                switch ($k) {
                    case 'start_time':
                        $query->andWhere('created_at >= :start', [':start' => strtotime($v)]);
                        break;
                    case 'stop_time':
                        $query->andWhere('created_at <= :end', [':end' => strtotime($v)]);
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

        if (isset($params['export'])) {
            if ($query->count() > 10000) {
                Yii::$app->getSession()->setFlash('error', '导出数据过多，建议分批次导出');

                return $this->redirect('index');
            }

            if (!$query->count()) {
                Yii::$app->getSession()->setFlash('error', '没有要导出的数据');

                return $this->redirect('index');
            }
            $model->exportData($query);
            exit;
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
     * @param $id
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $model = ImportFiles::findOne($id);
        if (!$model || $model->status == 2) {
            Yii::$app->getSession()->setFlash('error', '对象不存在或者正在进行中');
        } else {
            $file = $model->file;
            if ($model->delete()) {
                @unlink($file);
                Yii::$app->getSession()->setFlash('success', '删除号码成功');
            } else {
                Yii::$app->getSession()->setFlash('success', '删除号码失败');
            }
        }

        return $this->redirect('index');
    }

    /**
     * 操作
     * @param $id
     * @return string|\yii\web\Response
     */
    public function actionOperate($id)
    {
        $model = ImportFiles::findOne($id);
        if (!$model) {
            Yii::$app->getSession()->setFlash('success', '对象不存在');

            return $this->redirect('index');
        }
        if (Yii::$app->request->isPost) {
            $model->status = $model->status == 2 ? 3 : 1;
            if ($model->save(false)) {
                Yii::$app->getSession()->setFlash('success', '操作成功');
            } else {
                var_dump($model->getErrors());EXIT;
                Yii::$app->getSession()->setFlash('success', '操作失败');
            }

            return $this->redirect('index');
        } else {
            return $this->renderAjax('reason');
        }
    }
    /**
     * 查看
     * @param $id
     * @return string|\yii\web\Response
     */
    public function actionView($id) {
        $model = UserAppointment::findOne($id);
        if (!$model) {
            Yii::$app->getSession()->setFlash('success', '对象不存在');

            return $this->redirect('index');
        }


        return $this->render('view', [
            'model' => $model
        ]);
    }

    public function actionAdd()
    {
        $post = Yii::$app->request->post();
        $model = new ImportFiles();
        if ($post && $model->load($post) && $model->validate()) {
            $file = UploadedFile::getInstance($model, 'file');
            if (!$file) {
                Yii::$app->getSession()->setFlash('ERROR', '上传文件不能为空');
            } else {
                $dir = FileOperate::dir('import').'/'.date('Y-m');
                if (!is_dir($dir)) {
                    @mkdir($dir);
                }
                $filename = $dir.'/'.uniqid().mt_rand(0, 99).'.'.$file->getExtension();
                if(!$file->saveAs($filename)) {
                    Yii::$app->getSession()->setFlash('ERROR', '上传文件失败');
                } else {
                    $model->file = $filename;
                    if ($model->save(false)) {
                        Yii::$app->getSession()->setFlash('ERROR', '上传文件成功');
                    } else {
                        Yii::$app->getSession()->setFlash('ERROR', '上传文件失败1');
                    }
                }
            }
        }

        return $this->redirect('index');
    }

    public function actionDownload($id)
    {
        $model = ImportFiles::findOne($id);
        if (!$model) {
            Yii::$app->getSession()->setFlash('success', '对象不存在');

            return $this->redirect('index');
        }

        return Yii::$app->response->sendFile($model->file);
    }
}