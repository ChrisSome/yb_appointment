<?php

class paasoocode{

    const STATUS_SUCCESS = "0";   //发送成功

    const STATE_CODE    = 1;    //验证码短信
    const STATE_MARKING = 2;    //营销短信
    const STATE_VOICE   = 3;    //语音短信

    const REPEAT_NUM    = 3;    //重复次数


    private $url = 'https://api.paasoo.cn/voice?key=%s&secret=%s&from=85299998888&to=%s&lang=zh-cn&text=%s&repeat=%s';              //语音地址
    private $codeUrl = 'https://api.paasoo.com/json?key=%s&secret=%s&from=sdfknsdf&to=86%s&text=%s';    //短息地址
    public  $maxCount = 100;                        //每日最大发送量，后续验证

    private $API_KEY    = 'ybqxenxy';               //语音
    private $API_KEY_MESS = 'taihv6tw';             //短信

    private $API_SERECT = 'bBn2ebt3';               //语音
    private $API_SERECT_MESS = 'vvd4gWnb';          //短信

    public $copying = '【竹语】尊敬的用户，您好，您本次的验证码是: %s';     //短信模板





    private function curlpost($url)
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
    public function curlget($url)
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
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        $result = curl_exec($curl);
        curl_close($curl);

        return json_decode($result, true);


    }

    /**
     * 发送短信验证码
     * @param $mobile
     * @param $content
     * @return mixed
     */

    public function sendMess($mobile,$content){
        $url = sprintf($this->codeUrl, $this->API_KEY_MESS, $this->API_SERECT_MESS, $mobile, urlencode($content));
        $huawei_res = $this->curlget($url);
        return $huawei_res ;

    }

}
