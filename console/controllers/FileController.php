<?php

namespace console\controllers;

use center\modules\appointment\models\ImportFiles;
use center\modules\appointment\models\ImportMobile;
use common\extend\Excel;
use common\models\FileOperate;
use yii\console\Controller;
use yii\db\Query;
use Yii;
use yii\helpers\Url;

class FileController extends Controller
{
    public function actionIndex()
    {
        $query = new Query();
        $files = $query->where(['status' => [0, 3]])
            ->from('upload_files')
            ->all();
        $model = new ImportMobile();
        $db = Yii::$app->db;
        $ids = [];
        try {
            foreach ($files as $file) {
                echo "{$file['file']} begin exec\r\n";
                $ids[] = $file['id'];
                $db->createCommand()->update('upload_files', ['status' => 2, 'updated_at' => time()], ['id' => $file['id']])->execute();
                $rs = $this->import_datas($model, $file['file']);
                $db->createCommand()->update('upload_files', ['status' => 1, 'result' => $rs['msg'], 'updated_at' => time()], ['id' => $file['id']])->execute();
                echo "{$file['file']} end exec\r\n";

                return  ;
            }
        } catch (\Exception $e) {
            if (!empty($ids)) {
                $db->createCommand()->update('upload_files', ['status' => 3, 'result' => '处理异常' . $e->getMessage()], [
                    'id' => $ids,
                    'status' => 2
                ])->execute();
            }
        }

    }

    public function import_datas($model, $sFileName)
    {
        $batch_data = [];
        $success_num = $fail_num = 0;
        $db = Yii::$app->db;
        $table = 'import_mobiles';
        $aHasAlreadyImportPhone = ImportMobile::getImportMobile();
        $execute_rs[] = ['行数', '是否成功', '处理结果'];
        $field = ['import_time', 'mgr_id', 'mgr_name', 'mobile'];
        foreach ($model->getPhones($sFileName) as $line => $phone) {
            echo $line."\r\n";
            $phone = trim($phone);
            if (!empty($phone) && preg_match('/^1\d{10}/', $phone) && !in_array($phone, $aHasAlreadyImportPhone)) {
                if ($success_num != 0 && $success_num % 1000 == 0) {
                    $db->createCommand()->batchInsert($table, $field, $batch_data)->execute();
                    $batch_data = [];
                }
                $aHasAlreadyImportPhone[] = $phone;
                $insert_data = [
                    'import_time' => time(),
                    'mgr_id' => 0,
                    'mgr_name' => 'SYSTEM',
                    'mobile' => $phone
                ];
                $success_num++;
                $batch_data[] = $insert_data;
                $execute_rs[] = [$line + 1, 'success', $phone . '导入成功'];
            } else {
                $fail_num++;
                $execute_rs[] = [$line + 1, 'fail', $phone . '已经存在或者不符合规范'];
            }
        }

        //写入数据库
        if (!empty($batch_data)) {
            $db->createCommand()->batchInsert($table, $field, $batch_data)->execute();
        }


        $logFile = FileOperate::dir('import') . '/import_excel_' . '_' . date('YmdHis') . '.xls';
        Excel::arrayToExcel($execute_rs, $logFile, 'import detail');
        $url = '/appointment/import-mobile/download?file=' . $logFile;
        $smg = sprintf("批量导入 完成，导入成功 %s 个，失败 %s 个,  操作结果 : {%s}", $success_num, $fail_num, "详情请<a href=\"$url\">点此下载Excel文件</a>");
        //记录日志
        $rs = ['code' => 1, 'msg' => $smg];

        return $rs;

    }

}