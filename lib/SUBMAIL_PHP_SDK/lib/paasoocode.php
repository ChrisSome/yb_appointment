<?php

class paasoocode{

    const STATUS_SUCCESS = 0;   //发送成功

    const STATE_CODE    = 1;    //验证码短信
    const STATE_MARKING = 2;    //营销短信
    const STATE_VOICE   = 3;    //语音短信

    const REPEAT_NUM    = 3;    //重复次数


    private $url = 'https://api.paasoo.cn/voice?key=%s&secret=%s&from=85299998888&to=%s&lang=zh-cn&text=%s&repeat=%s';              //语音地址
    private $codeUrl = 'https://api.paasoo.cn/json?key=ybqxenxy&secret=bBn2ebt3&from=132432&to=8615670660962&text=Hello+world';    //短息地址
    private $accountUser = '13222930591';           //账户名
    private $accountpass = '3388858123Qwer.';       //账户密码
    public  $maxCount = 100;                        //每日最大发送量，后续验证

    private $API_KEY    = 'ybqxenxy';
    private $API_SERECT = 'bBn2ebt3';




    private function curlpost($url,$data,$method = 'GET',$type='json',$headers=[])
    {



        $ch = curl_init();

        //设置选项，包括URL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//绕过ssl验证
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        //执行并获取HTML文档内容
        $output = curl_exec($ch);

        //释放curl句柄
        curl_close($ch);
        var_dump($output);die;


    }

    /**
     * @param $url
     * @return mixed
     */
    public function get($url)
    {
        //发送请求
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //设置请求方式
        curl_setopt($curl, CURLOPT_POST, FALSE);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        //curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        $result = curl_exec($curl);
        curl_close($curl);

        return json_decode($result, true);


    }

    //$account 用户账号

    public function send($mobile,$msg, $repeatNum){
        $url = sprintf($this->codeUrl, $this->API_KEY, $this->API_SERECT, $mobile, $msg, $repeatNum);

var_dump($url);die;

        $huawei_res = $this->curlpost($url,'');var_dump($huawei_res);die;
        $huawei_res = json_decode($huawei_res,true);
        return $huawei_res ;

    }

}

$paasoo = new paasoocode();
//$res = $paasoo->send('15670660962', '验证码123123', 3);
$res = $paasoo->get();
var_dump($res);