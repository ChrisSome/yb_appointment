<?php

class usernoticemess{

    const STATUS_SUCCESS = 'Success';               //发送成功

    private $user_id    = '904';                   //企业id
    private $timstamp   = '';
    private $account    = '2020060617';             //账号
    private $pass       = '2020060617';             //密码
    private $sign       = '';                       //签名
    private $url        = 'http://39.99.239.106:8888/v2sms.aspx';


    public  $copying    = '【竹语】尊敬的用户，您好，恭喜您在我平台参加预约活动，已预约成功，请上线查看。';

    public function __construct()
    {
        $this->timstamp = time();
        $this->sign = md5($this->account . $this->pass . $this->timstamp);
    }

    private function post_curls($url, $post)
    {
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post); // Post提交的数据包
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        $res = curl_exec($curl); // 执行操作
        if (curl_errno($curl)) {
            echo 'Errno'.curl_error($curl);//捕抓异常
        }
        curl_close($curl); // 关闭CURL会话
        return $res; // 返回数据，json格式

    }

    /**
     * 发送短信通知用户
     * @param $mobile
     * @param $msg
     * @return array
     */
    public function send($mobile, $msg = ''){
        $url = $this->url;
        $data = [
            'userid'    => $this->user_id,
            'timestamp' => $this->timstamp,
            'sign'      => $this->sign,
            'mobile'    => $mobile,
            'content'   => $msg ?: $this->copying,
            'action'    => 'send',
            'extno'     => ''
        ];

        $response = $this->post_curls($url,$data);
        $returndata = json_decode(json_encode(simplexml_load_string($response)), true);
        return $returndata ;

    }

}
