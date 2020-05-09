<?php

namespace console\controllers;

use center\modules\appointment\models\ImportFiles;
use center\modules\appointment\models\ImportMobile;
use common\extend\Excel;
use common\extend\OfficesTool;
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
        $files = $query->where(['status' => [0, 2, 3]])
            ->from('upload_files')
            ->all();
        $db = Yii::$app->db;
        $ids = [];
        try {
            foreach ($files as $file) {
                echo "{$file['file']} begin exec\r\n";
                $ids[] = $file['id'];
                $db->createCommand()->update('upload_files', ['status' => 2, 'updated_at' => time()], ['id' => $file['id']])->execute();
                $rs = $this->import_datas($file['file']);
                $db->createCommand()->update('upload_files', ['status' => 1, 'result' => $rs['msg'], 'updated_at' => time()], ['id' => $file['id']])->execute();
                echo "{$file['file']} end exec\r\n";

                return  ;
            }
        } catch (\Exception $e) {
            if (!empty($ids)) {
                var_dump($e->getMessage());
                $db->createCommand()->update('upload_files', ['status' => 3, 'result' => '处理异常'], [
                    'id' => $ids,
                    'status' => 2
                ])->execute();
            }
        }

    }

    public function getRs($phone, & $phone_arr)
    {
        if (in_array($phone, $phone_arr)) {
            yield false;
        } else {
            $phone_arr[] = $phone;
            yield [
                'import_time' => time(),
                'mgr_id' => 0,
                'mgr_name' => 'SYSTEM',
                'mobile' => $phone
            ];
        }

    }
    public function getPhones($sFileName, &$phones)
    {
        if (preg_match('/\.csv$/', $sFileName)) {
            # code...
            $handle = fopen($sFileName, 'rb');

            while (feof($handle) === false) {
                # code...
                $phone = trim(fgets($handle));
                if (in_array($phone, $phones)) {
                    yield false;
                } else {
                    $phones[] = $phone;
                    yield $phone;
                }
            }

            fclose($handle);
        } else {
            $officesTool = new OfficesTool();
            foreach ($officesTool->readExecl($sFileName) as $sheet => $vals) {
                if ($sheet == 0) {
                    foreach ($vals as $value) {
                        $phone = $value['A'];
                        if (in_array($phone, $phones)) {
                            yield false;
                        } else {
                            $phones[] = $phone;
                            yield $phone;
                        }
                    }
                } else {
                    break;
                }
            }
        }
    }


    public function import_datas($sFileName)
    {
        $batch_data = [];
        $success_num = $fail_num = 0;
        $db = Yii::$app->db;
        $table = 'import_mobiles';
        $aHasAlreadyImportPhone = ImportMobile::getImportMobile();
        $execute_rs[] = ['行数', '是否成功', '处理结果'];
        $field = ['import_time', 'mgr_id', 'mgr_name', 'mobile'];
        foreach ($this->getPhones($sFileName, $aHasAlreadyImportPhone) as $line => $phone) {
            echo $line."\r\n";
            $phone = trim($phone);
            if (!empty($phone) &&  preg_match('/^1\d{10}/', $phone)) {
                if (!$phone) {
                    $fail_num++;
                    $execute_rs[] = [$line + 1, 'fail', $phone . '已经存在或者不符合规范'];
                } else {
                    $success_num++;
                    $batch_data[] = [
                        'import_time' => time(),
                        'mgr_id' => 0,
                        'mgr_name' => 'SYSTEM',
                        'mobile' => $phone
                    ];
                    $execute_rs[] = [$line + 1, 'success', $phone . '导入成功'];
                }
            } else {
                $fail_num++;
                $execute_rs[] = [$line + 1, 'fail', $phone . '为空或者不符合规范'];
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
        //释放内存
        unset($execute_rs, $batch_data, $aHasAlreadyImportPhone);

        return $rs;

    }

}