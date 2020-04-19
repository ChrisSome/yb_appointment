<?php

//配置服务器ip，测试环境连接185
if (YII_ENV_DEV) {
    $server_ips = ['127.0.0.1'];
} else {
    $array = parse_ini_file('/srun3/etc/distribute.conf'); //取得server_ip
    $server_ips = explode(',', $array["server_ip"]); //server_id有可能是多个
}

return [
    //8081中语言的定义
    'define8081' => [
        'zh-CN' => '/srun3/www/include/define.php',
        'en-us' => '/srun3/www/include/define_en.php',
    ],
    //帮助中心地址
    'help_url' => 'http://121.41.95.98:8000/index/message?code=',
    //分布式ip
    'distribute_ip' => $server_ips,
    //菜单和权限
    'menu' => [
        'appointment' => [
            'label' => 'appointment',
            'ico' => 'fa fa-user',
            'color' => 'bg-success',
            'url' => 'report/statistics/',
            'items' => [
                'appointment/import-mobile/index' => [
                    'appointment/import-mobile/create',
                    //'appointment/import-mobile/update',
                    'appointment/import-mobile/delete',
                    'appointment/import-mobile/batch',
                    'appointment/import-mobile/view',
                    'appointment/import-mobile/operate',
                    'appointment/import-mobile/preview',
                ],
                'appointment/user/index' => [
                    'appointment/user/operate',
                    'appointment/user/batch',
                ],
                'appointment/domain/index' => [
                    'appointment/domain/create',
                    'appointment/domain/update',
                    'appointment/domain/chg-status',
                ],
            ],
        ],
        //设置
        'setting' => [
            'label' => 'Setting',
            'ico' => 'fa fa-cogs',
            'color' => 'bg-violet',
            'url' => 'setting',
            'items' => [
                //权限管理
                'auth/show/index' => [
                    //角色
                    'auth/roles/index' => [
                        //'auth/roles/view',
                        'auth/roles/create',
                        'auth/roles/update',
                        'auth/roles/delete',
                    ],
                    //管理员
                    'auth/assign/index' => [
                        //'auth/assign/view',
                        //添加
                        'auth/assign/signup',
                        //编辑
                        'auth/assign/update',
                        'auth/assign/delete',
                        'auth/assign/set-default-pass'
                    ],
                ],
            ],
        ],
        //日志管理
        'log' => [
            'label' => 'Log Manage',
            'ico' => 'fa fa-pencil-square-o',
            'color' => 'bg-info',
            'url' => 'log',
            'items' => [
                //操作日志
                'log/operate/index' => [
                    'log/operate/export'
                ],
                //登陆日志
                'log/login/index',
            ],
        ],
    ],
];