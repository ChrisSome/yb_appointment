<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>测试页面</title>
</head>
<body>

<form action="/api/appointment/apply" method="post" onsubmit="return getSign();">
    手机号： <input type="text" name="mobile" id="mobile"/>
    <input type="hidden" name="sign" id="sign"/>
    用户名: <input type="text" name="username" id="username"/>

    <input type="submit" value="确认"/>
</form>
<span class="button" style="border: 1px solid red; cursor: pointer">提交</span>

</body>
<script src="/js/lib/jquery.js"></script>
<script src="/js/md5.js"></script>
<script>
    function getSign()
    {
        var _username = document.getElementById('username').value;
        var _mobile = document.getElementById('mobile').value;
        var _safe_str = 'mobile='+_mobile+'&username='+_username;
        document.getElementById('sign').value = hex_md5(_safe_str);

        return true;
        var _sign = $('#sign').val();
        $.ajax({
            //请求方式
            type : "post",
            //请求的媒体类型
            contentType: "application/json;charset=UTF-8",
            async: true,
            //请求地址
            //url : "http://appoint978.sihuocy.cn/api/appointment/apply?username="+_username+'&mobile='+_mobile+'&sign='+_sign,
            url : "http://ldyadmin.com/api/appointment/apply?username="+_username+'&mobile='+_mobile+'&sign='+_sign,
            //url : "/api/appointment/apply?username="+_username+'&mobile='+_mobile+'&sign='+_sign,
            //数据，json字符串
            //data : JSON.stringify(list),
            //请求成功
            success : function(result) {
                console.log(result);
            },
            //请求失败，包含具体的错误信息
            error : function(e){
                console.log(e.status);
                console.log(e.responseText);
            }
        });
        return false;
    }

    $(function () {
        $('.button').off().on('click',function() {
            var _username = document.getElementById('username').value;
            var _mobile = document.getElementById('mobile').value;
            var _safe_str = 'mobile='+_mobile+'&username='+_username;
            document.getElementById('sign').value = hex_md5(_safe_str);
            var _sign = $('#sign').val();
            $.ajax({
                //请求方式
                type : "post",
                //请求的媒体类型
                contentType: "application/json;charset=UTF-8",
                //请求地址
                //url : "http://appoint978.sihuocy.cn/api/appointment/apply?username="+_username+'&mobile='+_mobile+'&sign='+_sign,
                //url : "http://ldyadmin.com/api/appointment/apply?username="+_username+'&mobile='+_mobile+'&sign='+_sign,
                url : "/api/appointment/ip-is-black?username="+_username+'&mobile='+_mobile+'&sign='+_sign,
                //数据，json字符串
                //data : JSON.stringify(list),
                //请求成功
                success : function(result) {
                    console.log(result);
                },
                //请求失败，包含具体的错误信息
                error : function(e){
                    console.log(e.status);
                    console.log(e.responseText);
                }
            });
        });
    })

    function cors()
    {
        console.log('sign')
        $.ajax({
            //请求方式
            type : "get",
            //请求的媒体类型
            contentType: "application/json;charset=UTF-8",
            //请求地址
            url : "http://tyreg.sihuocy.cn/api/appointment/get-sign?callback=jQuery1110028253783626237294_1587031689994&mobile=13265782671&username=2&_=1587031689995",
            //数据，json字符串
            //data : JSON.stringify(list),
            //请求成功
            success : function(result) {
                console.log(result);
            },
            //请求失败，包含具体的错误信息
            error : function(e){
                console.log(e.status);
                console.log(e.responseText);
            }
        });



        console.log('apply')
        $.ajax({
            //请求方式
            type : "get",
            //请求的媒体类型
            contentType: "application/json;charset=UTF-8",
            //请求地址
            url : "http://ldyadmin.com/api/appointment/apply?mobile=18870201490&username=18870201490&sign=6f188d81d438c0c69cee8e680073e2e9",
            //数据，json字符串
            //data : JSON.stringify(list),
            //请求成功
            success : function(result) {
                console.log(result);
            },
            //请求失败，包含具体的错误信息
            error : function(e){
                console.log(e.status);
                console.log(e.responseText);
            }
        });



    }


    cors();
</script>
</html>