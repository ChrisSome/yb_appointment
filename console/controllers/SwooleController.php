<?php


namespace console\controllers;


use center\modules\appointment\models\ImportMobile;
use common\extend\Excel;
use common\models\FileOperate;
use yii\console\Controller;
use yii\db\Query;
use yii;

class SwooleController extends Controller
{
    public $config = [
        'host' => '127.0.0.1',
        'port' => '9501',
        'pid_file' => './swoole.pid',
        'task_worker_num' => 4
    ];

    public function actionServer()
    {
        $serv = new \Swoole\Server($this->config['host'], $this->config['port']);
        //设置异步任务的工作进程数量
        $serv->set(array('task_worker_num' => 4));
        //此回调函数在worker进程中执行
        $serv->on('receive', function($serv, $fd, $from_id, $data) {
            //投递异步任务
            var_dump($data);
            $task_id = $serv->task($data);
            echo "Dispatch AsyncTask: id=$task_id\n";
        });
        //处理异步任务(此回调函数在task进程中执行)
        $serv->on('task', function ($serv, $task_id, $from_id, $data) {
            echo "New AsyncTask[id=$task_id]".PHP_EOL;
            //返回任务执行的结果
            $this->import_datas($data);
            $serv->finish("$data -> OK");
        });
        //处理异步任务的结果(此回调函数在worker进程中执行)
        $serv->on('finish', function ($serv, $task_id, $data) {
            echo "AsyncTask[$task_id] Finish: $data".PHP_EOL;
        });

        $serv->start();
    }
    public function import_datas($sFileName)
    {
        if (!is_file($sFileName)) {
            echo  $sFileName.' is not file'.PHP_EOL;
        } else {
            $model = new ImportMobile();
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
                    if ($success_num != 0 && $success_num % 300 == 0) {
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

            echo $smg."\r\n";
        }





    }
    /**
     * 客户端
     */
    public function actionClient()
    {
        $query = new Query();
        $files =  $query->where(['status' => [0, 3]])->from('upload_files')->all();
        $client = new \Swoole\Coroutine\Client(SWOOLE_SOCK_TCP);

        foreach ($files as $file) {
            \Co\run(function() use ($client, $file){
                //$client->set()
                if (!$client->connect($this->config['host'], $this->config['port'], 0.5))
                {
                    echo "connect failed. Error: {$client->errCode}\n";
                    return ;
                }
                echo $file['file']."\r\n";
                $client->send($file['file']);
                echo $client->recv();
                $client->close();
            });
        }


    }

}