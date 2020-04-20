<?php


namespace center\modules\appointment\controllers;


use yii;
use center\models\Pagination;
use center\controllers\ValidateController;
use center\modules\appointment\models\IpBlacklist;

/**
 * ip管理
 * Class DomainController
 * @package center\modules\appointment\controllers
 */
class IpManageController extends ValidateController
{
    public function actionIndex()
    {
        $model = new IpBlacklist();
        $params = Yii::$app->request->queryParams;
        $params['showField'] = $model->default_field;
        $query = IpBlacklist::find();
        $pagesSize = 20; // 每页条数
        foreach ($params as $k => $v) {
            if ((!empty($v) || preg_match('/^0$/', $v)) && array_key_exists($k, $model->getSearchField())) {
                switch ($k) {
                    case 'start_time':
                        $query->andWhere('created_at >= :start', [':start' => strtotime($v)]);
                        break;
                    case 'stop_time':
                        $query->andWhere('created_at <= :end', [':end' => strtotime($v)]);
                        break;
                    case 'ip_addr':
                        $query->andWhere('ip_addr <= :ip', [':ip' => ip2long($v)]);
                        break;
                    default:
                        $query->andWhere([$k => $v]);
                        break;
                }

            }
        }
        $pagination = new Pagination([
            'totalCount' => $query->count(),
            'pageSize' => $pagesSize
        ]);
        //排序
        if (isset($params['orderBy']) && array_key_exists($params['orderBy'], $model->searchField)) {
            $query->orderBy([$params['orderBy'] => $params['sort'] == 'desc' ? SORT_DESC : SORT_ASC]);
        } else {
            $query->orderBy(['id' => SORT_DESC]);
        }
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
     * 禁用|启用
     * @param $id
     * @return yii\web\Response
     */
    public function actionChgStatus($id)
    {
        $model = IpBlacklist::findOne($id);

        if (!$model) {
            Yii::$app->getSession()->setFlash('error', '请求对象不存在');
        }
        $iStatus = Yii::$app->request->get('status');
        $model->status = $iStatus;
        if ($model->save(false)) {
            Yii::$app->getSession()->setFlash('success', '操作成功');
        } else {
            Yii::$app->getSession()->setFlash('danger', '操作失败， 请稍后重试');
        }

        return $this->redirect('index');

    }


    /**
     * 创建域名
     * @return string|yii\web\Response
     */
    public function actionCreate()
    {

        $model = new IpBlacklist();
        $model->loadDefaultValues(); //加载默认值

        $post = Yii::$app->request->post();
        if ($post && $model->load($post) && $model->validate()) {
            if ( $model->save()) {
                Yii::$app->getSession()->setFlash('success', '添加域名成功');
            } else {
                Yii::$app->getSession()->setFlash('success', '添加域名失败');
            }

            return $this->redirect('index');
        }

        return $this->render('create', [
            'model' => $model
        ]);
    }

    /**
     * 更新
     * @param $id
     * @return string|yii\web\Response
     */
    public function actionUpdate($id)
    {
        $model = IpBlacklist::findOne($id);
        if (!$model) {
            Yii::$app->getSession()->setFlash('error', '请求对象不存在');
        }
        //将当前记录保存在临时旧数据
        $model->_temOldAttr = $model->getCurrentData();
        $post = Yii::$app->request->post();
        if ($post && $model->load($post) && $model->validate()) {
            if ( $model->save()) {
                Yii::$app->getSession()->setFlash('success', '编辑域名成功');
            } else {
                Yii::$app->getSession()->setFlash('success', '编辑域名失败');
            }

            return $this->redirect('index');
        }

        $model->ip_addr = long2ip($model->ip_addr);
        return $this->render('_form', [
            'model' => $model,
            'action' => 'edit'
        ]);
    }

    /**
     * 删除
     * @param $id
     * @return yii\web\Response
     * @throws \Throwable
     * @throws yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $model = IpBlacklist::findOne($id);
        if (!$model) {
            Yii::$app->getSession()->setFlash('error', '请求对象不存在');
        } else {
            $ip = long2ip($model->ip_addr);
            if ($model->delete()) {
                Yii::$app->getSession()->setFlash('success', '删除ip'.$ip.'成功');
            } else {
                Yii::$app->getSession()->setFlash('error', '删除ip'.$ip.'失败');
            }
        }

        return $this->redirect('index');

    }

}