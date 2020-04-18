<?php
/**
 * 中文语言文件
 * note：在添加语言时请注意，最好先搜索是否已经存在了自己需要的语言；使用指定编辑器，如果key重复了，会有不同颜色的提示；
 * 公共语言定义如下，比如：搜索，提交，保存 等可能很多地方都要用到的语言
 * 专业术语等语言
 * 菜单语言
 * 其他各个模块使用的语言，菜单项的各个单词首字母必须大写
 */
return [
    /**
     * 公共基本语言，比如：提交，保存等
     */
    'add' => '添加',
    'edit' => '编辑',
    'setting' => '设置',
    'view' => '查看',
    'delete' => '删除',
    'download' => '下载明细',
    'confirm' => '确认',
    'cancel' => '取消',
    'submit' => '提交',
    'preview' => '预览',
    'save' => '保存',
    'reset' => '重置',
    'close' => '关闭',
    'operate' => '操作',
    'refresh' => '刷新',
    'refund' => '退费',
    'payment' => '缴费',
    'amount' => '金额',
    'print' => '打印',
    'print_browser' => '(暂只支持火狐浏览器)',
    'print bill' => '打印票据',
    'operator' => '操作员',
    'account' => '账号',
    'password' => '密码',
    'Old password' => '原密码',
    'Confirm Password' => '确认密码',
    'verification code' => '验证码',
    'search' => '搜索',
    'search result' => '搜索结果',
    'success' => '成功',
    'failed' => '失败',
    'operate success.' => '操作成功。',
    'operate failed.' => '操作失败。',
    'add success.' => '添加成功。',
    'edit success.' => '编辑成功。',
    'delete success.' => '删除成功。',
    'No results found.' => '没有找到数据。',
    'condition' => '使用条件',
    'list' => '列表',
    '$' => '¥',
    'currency' => '元',
    'yes' => '是',
    'no' => '否',
    'no record' => '没有记录哦',
    'product' => '产品',
    'pagination show page' => '共{totalPage}页{totalCount}条  每页{perPage}条  转到{pageInput}页 {buttonGo}',
    'pagination show1' => '共:{totalCount}条记录  共{totalPage}页  每页显示{perPage}条',
    'pagination show2' => '总流量:{total_bytes}&nbsp;&nbsp;&nbsp;&nbsp;入流量:{bytes_in}&nbsp;&nbsp;&nbsp;&nbsp;出流量:{bytes_out}<br /> 总时长:{time_long}&nbsp;&nbsp;&nbsp;&nbsp;总金额:{user_charge}元<br />共:{totalCount}条记录  共{totalPage}页  每页显示{perPage}条',

    /**
     * 其他
     */
    'zh-CN' => '简体中文',
    'en-us' => 'English',
    'zh-tw' => '繁體中文',
    'es' => '西班牙语',

    'company' => '预约后台管理系统',
    'version' => '版本',
    'version log' => '升级日志',
    'Dashboard' => '系统概况',
    /**
     *云端错误
     */
    'cloud login fail' => '远端登录失败',
    'statistics package expire or have no package' => '未购买统计系统套餐或者已过期',

    /**
     * 抛出异常
     */
    'message error' => '发生错误',
    'message unknown error' => '发生未知错误',
    'message 400' => '请求不合法',
    'message 404' => '请求的资源找不到',
    'message 401' => '无权限进行此操作',
    'message 401 1' => '无权限操作此组',
    'message 401 2' => '无权限订购此产品',
    'message 401 3' => '无权限管理此用户',
    'message 401 4' => '无权限管理此产品',
    'message Invalid Param' => '请求参数无效',
    'message Failed Redis connection' => 'redis地址{redis}异常,连接失败',


    'TheRedis' => 'Redis从',
    'danger' => '警告',
    'warning' => '异常',
    /**
     * 通用消息
     */
    'confirm delete' => '确定要删除此记录吗?',





    /**
     * 日志模块
     */
    'Log Manage' => '日志管理',
    'Detail Log' => '上网明细',
    'Login Log' => '认证日志',
    'System Log' => '系统日志',
    'Operate Log' => '操作日志',
    'User Add Detail' => '开销户日志',
    'Product Change Log' => '下个产品日志',


    //帮助信息
    'log login help1' => '拖拽要显示的字段及顺序',
    'log login help2' => '自由搜索',
];