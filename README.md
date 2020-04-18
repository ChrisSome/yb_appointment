###开启快乐的编程之旅吧
##lamp环境搭配
##1、安装apache
    1.1 安装apache
        [root@nmserver-7 ~]# yum install httpd httpd-devel
    1.2 启动apache服务
        [root@nmserver-7 ~]# systemctl start  httpd
    1.3 设置httpd服务开机启动
        [root@nmserver-7 ~]# systemctl enable  httpd
    1.4 查看服务状态
        [root@nmserver-7 ~]# systemctl status httpd
    1.5 防火墙设置开启80端口
        [root@nmserver-7 ~]# firewall-cmd --permanent --zone=public  --add-service=http
        [root@nmserver-7 ~]# firewall-cmd --permanent --zone=public  --add-service=https
        [root@nmserver-7 ~]# firewall-cmd --reload
    1.6确认80端口监听中
        [root@nmserver-7 ~]# netstat -tulp
    1.7 查服务器IP[root@nmserver-7 ~]# ip addr　　
    1.8 浏览器登陆
##2、安装mysql
    2.1安装mysql
        [root@nmserver-7 ~]# yum install mariadb mariadb-server mariadb-libs mariadb-devel
        root@nmserver-7 ~]# rpm -qa |grep maria
    2.2 开启mysql服务，并设置开机启动，检查mysql状态
        [root@nmserver-7 ~]# systemctl start  mariadb 
        [root@nmserver-7 ~]# systemctl enable  mariadb 
        [root@nmserver-7 ~]# systemctl status  mariadb 
        [root@nmserver-7 ~]# netstat -tulp
    2.3 数据库安全设置
        [root@nmserver-7 ~]# mysql_secure_installation 
            Set root password? [Y/n] y
            New password: 
            Re-enter new password: 
            Password updated successfully!
            Reloading privilege tables..
            ... Success!
           Remove anonymous users? [Y/n] y
           ... Success!
           Disallow root login remotely? [Y/n] n
            ... skipping.
           Remove test database and access to it? [Y/n] y
           ......
    2.4 登陆数据库测试
        [root@nmserver-7 ~]# mysql -uroot -p
        Enter password:  
    3、安装PHP
        3.1 安装php
            [root@nmserver-7 ~]# yum -y install php
            [root@nmserver-7 ~]# rpm -ql php
        3.2 将php与mysql关联起来
            [root@nmserver-7 ~]# yum install php-mysql
            [root@nmserver-7 ~]# rpm -ql php-mysql
        3.3 安装常用PHP模块
            [root@nmserver-7 ~]# yum install -y php-gd php-ldap php-odbc php-pear php-xml php-xmlrpc php-mbstring php-snmp php-soap curl curl-devel php-bcmath
        3.4 测试PHP
            [root@nmserver-7 ~]# cd  /var/www/html/
            [root@nmserver-7 html]# ls
            [root@nmserver-7 html]# vi info.php
                <?php
                    phpinfo();
                ?>
       :wq
      　3.5重启apache服务器
            [root@nmserver-7 html]# systemctl restart http
        3.6测试PHP
            在自己电脑浏览器输入 192.168.8.9/info.php，你可以看到已经安装的模块；
            
##项目介绍
##开发说明

* 开发工具：建议phpstorm 8.0
* 代码书写格式：采用yii框架的书写格式，稍后补充

##项目安装说明

* 在电脑上安装运行环境Apache+Mysql+PHP ( 简单方式可以安装[wamp](http://www.wampserver.com/en/)，选择php5.5版本的下载 )


    >注意：需要开启Apache的rewrite模块 
    
* 安装git客户端，git使用可以参考<http://git-scm.com/book/zh>.
* 将整个项目clone到www根目录.
* 访问<http://localhost/web/requirements.php>查看环境是否支持yii框架。
* 修改数据库的本地配置，打开 common/config/main-local.php

    ```php
    'db' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=localhost;dbname=report_wei',//建议数据库名称保持为srun
        'username' => 'root',//数据库用户名
        'password' => '',//数据库密码，如果没有则留空
        'charset' => 'utf8',
    ],
    ```

* 在mysql中新建数据库srun.语句如下：


    >mysql>CREATE DATABASE report_wei /*!40100 DEFAULT CHARACTER SET utf8 */;
    
* 双击运行根目录下的yii.bat。

* 运行下方命令创建权限数据库数据库

    >yii migrate --migrationPath=@yii/rbac/migrations/


* 创建数据表：在命令行下运行>yii migrate ，按照提示输入yes即可完成创建。


    >以后所有的数据库变化都通过此命令进行，具体用法请查看yii的api文档migrate类。
    >请按照文档中标准写法完成，方便其他人升级数据库以及以后的数据库迁移
    >如果您不是第一次运行此命令，还请您依次执行如下命令

    ```php
        DELETE FROM `auth_item`
        DELETE FROM `auth_assignment`
        DELETE FROM `auth_item_child`
        DELETE FROM `migration` WHERE `version` = 'm141230_060236_auth_modules_date'
    ```
    
* 访问<http://localhost/web/api/domos>,看到xml或json格式的内容即代表安装成功！


    >以后web目录为系统的根目录，建议使用域名或者别名的方式直接指向到web目录，则url变为<http://xxx/api/demos>
    >保留了默认的frontend文件，可以通过<http://localhost/web/api/frontend.php>访问；只为演示，实际不再使用；
    >yii的入口文件为www/web/api/index.php。工作目录为www/center目录




## 获取api
             