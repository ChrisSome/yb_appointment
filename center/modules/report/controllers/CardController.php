<?php
/**
 * Created by PhpStorm.
 * User: sihuo
 * Date: 2018/3/19
 * Time: 17:41
 */

namespace center\modules\report\controllers;

use yii;
use center\controllers\ValidateController;
use center\modules\report\models\UserCard;

/**
 * 身份证号码管理
 * Class CardController
 * @package center\modules\report\controllers
 */
class CardController extends ValidateController
{

    public function actionIndex()
    {
        $model = new UserCard();
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
     * 导出身份证
     * @return yii\web\Response
     */
    public function actionExport()
    {
        $model = new UserCard();
        $param = Yii::$app->request->queryParams;
        $model->export($param);

        return $this->redirect('index');
    }

    /**
     * @return string
     */
    public function actionYear()
    {
        $model = new UserCard();
        $param = Yii::$app->request->post();
        $rs = $model->getStatisticsByYear($param);

        return $this->render('year', [
            'data' => $rs,
            'model' => $model
        ]);
    }

    /**
     * @return string
     */
    public function actionMonth()
    {
        $model = new UserCard();
        $param = Yii::$app->request->post();
        $rs = $model->getStatisticsByMonth($param);

        return $this->render('month', [
            'data' => $rs,
            'model' => $model
        ]);
    }

    /**
     * 按省份统计
     * @return string
     */
    public function actionProvince()
    {
        $model = new UserCard();
        $param = Yii::$app->request->post();
        $rs = $model->getStatisticsByProvince($param);

        return $this->render('province', [
            'data' => $rs,
            'model' => $model
        ]);
    }

    /**
     * 按性别统计
     * @return string
     */
    public function actionSex()
    {
        $model = new UserCard();
        $param = Yii::$app->request->post();
        $rs = $model->getStatisticsByProvince($param, 'sex');

        return $this->render('province', [
            'data' => $rs,
            'model' => $model
        ]);
    }

    public function actionShuxiang()
    {
        $model = new UserCard();
        $param = Yii::$app->request->post();
        $rs = $model->getStatisticsByProvince($param, 'shuxiang');

        return $this->render('province', [
            'data' => $rs,
            'model' => $model
        ]);

    }

    public function actionXingzuo()
    {
        $model = new UserCard();
        $param = Yii::$app->request->post();
        $rs = $model->getStatisticsByProvince($param, 'xingzuo');

        return $this->render('province', [
            'data' => $rs,
            'model' => $model
        ]);
    }
}