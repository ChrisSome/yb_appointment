<?php


namespace center\modules\appointment\controllers;


use center\modules\appointment\models\SmsHistory;
use Yii;
use center\models\Pagination;
use center\controllers\ValidateController;

class SmsController extends ValidateController
{
    /**
     * 预约首页
     * @return string
     */
    public function actionIndex()
    {
        $model = new SmsHistory();
        $params = Yii::$app->request->queryParams;
        $params['showField'] = $model->default_field;
        $query = SmsHistory::find();
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
                    case 'ip_addr':
                        $query->andWhere(['ip_addr' => ip2long($v)]);
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
     * @param $id
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $model = SmsHistory::findOne($id);
        if (!$model) {
            Yii::$app->getSession()->setFlash('success', '对象不存在');
        } else {
            if ($model->delete()) {
                Yii::$app->getSession()->setFlash('success', '删除号码成功');
            } else {
                Yii::$app->getSession()->setFlash('success', '删除号码失败');
            }
        }

        return $this->redirect('index');
    }

    /**
     * 查看
     * @param $id
     * @return string|\yii\web\Response
     */
    public function actionView($id) {
        $model = SmsHistory::findOne($id);
        if (!$model) {
            Yii::$app->getSession()->setFlash('success', '对象不存在');

            return $this->redirect('index');
        }


        return $this->render('view', [
            'model' => $model
        ]);
    }


    /**
     * 操作
     * @param $id
     * @return string|\yii\web\Response
     */
    public function actionOperate($id)
    {
        $model = SmsHistory::findOne($id);
        $iChgStatus = Yii::$app->request->get('status');
        $sRemark = Yii::$app->request->get('remark', '');
        if (!$model) {
            Yii::$app->getSession()->setFlash('success', '对象不存在');

            return $this->redirect('index');
        }
        //将当前记录保存在临时旧数据
        $model->_temOldAttr = $model->getCurrentData();
        if (Yii::$app->request->isPost) {
            $model->status = $iChgStatus;
            $model->remark = $sRemark;
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
     * 批量操作
     * @return \yii\web\Response
     * @throws \yii\db\Exception
     */
    public function actionBatch()
    {
        $params = Yii::$app->request->queryParams;
        $ids = $params['ids'];
        $id_arr = explode(',', $ids);
        $status = $params['status'];
        $remark = $params['remark'];
        $model = new SmsHistory();
        $rs = Yii::$app->db->createCommand()->update(
            SmsHistory::tableName(),
            ['status' => $status, 'remark' => $remark, 'updated_at' => time(), 'operator' => $model->getMgrName()],
            ['id' => $id_arr]
        )->execute();
        if ($rs) {
            Yii::$app->getSession()->setFlash('success', '操作成功');
        } else {
            Yii::$app->getSession()->setFlash('success', '操作失败');
        }

        $model->batchLog($params);

        return  $this->redirect('index');
    }


    /**
     * 通知
     * @param $id
     * @return \yii\web\Response
     */
    public function actionNotice($id)
    {
        $model = SmsHistory::findOne($id);
        if (!$model || $model->status != 1) {
            Yii::$app->getSession()->setFlash('success', '对象不存在或者未通过状态不允许通知');

            return $this->redirect('index');
        }
        $sms = new SmsHistory();
        $rs = $sms->sendNoticeToUser($model);
        if ($rs['code'] == 0) {
            Yii::$app->getSession()->setFlash('success', $rs['message']);
        } else {
            Yii::$app->getSession()->setFlash('error', $rs['message']);
        }

        return $this->redirect('index');
    }
}