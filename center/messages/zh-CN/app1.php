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
    'log/operate/index' => '操作日志',
    'log/login/index' => '登陆日志',


    //帮助信息
    'log login help1' => '拖拽要显示的字段及顺序',
    'log login help2' => '自由搜索',


    /**
     * 以下为私有模块语言
     * 登录模块翻译
     */
    'Login Title' => '登录系统',
    'Manager Name' => '管理员账户',
    'Manager Password' => '管理员密码',
    'Login' => '登录',
    'login help1' => '用户名或密码错误',
    'login help2' => '输入左侧验证码',
    'welcome' => '欢迎您',
    'login timeout' => '由于您长时间未操作,为了安全起见,系统已经将您自动安全退出.重新登录即可返回上次浏览页面.',
    'login timeout by reason' => '由于没有云端授权认证通过,具体原因：{message}.您稍后会被强制退出，具体详情请联系销售人员。谢谢！',
    'login timeout by reason1' => '由于没有云端授权认证通过,具体原因：{message}.您被强制退出，具体详情请联系销售人员。谢谢！',
    'login timeout by reason2' => '您现在是内网，需要连接到外网才能正常使用此系统！',


    //预约模块
    'appointment' => '预约管理',
    'appointment/import/phone' => '导入号码管理',
    'appointment/user/index' => '用户预约管理',

    //设置模块
    'Setting' => '系统设置',
    //权限数据翻译
    'auth/show/index' => '权限管理',
    'auth/assign/index' => '管理员管理',
    'auth/assign/view' => '查看管理员',
    'auth/assign/update' => '修改管理员',
    'auth/assign/signup' => '添加管理员',
    'auth/assign/delete' => '删除管理员',

    'auth/roles/index' => '角色管理',
    'auth/roles/view' => '展示角色',
    'auth/roles/create' => '添加角色',
    'auth/roles/delete' => '删除角色',
    'auth/roles/update' => '编辑角色',

    'auth/permission/index' => '权限管理',
    'auth/permission/create' => '添加权限',
    'auth/permission/delete' => '删除权限',
    'auth/permission/update' => '修改权限',
    'auth/permission/view' => '展示权限',

    'auth/empower/create' => '角色赋权',

    'auth/structure/index' => '组织结构管理',
    'auth/structure/create' => '创建子节点按钮',
    'auth/structure/deletes' => '删除结构',
    'auth/structure/update' => '修改节点名称',
    'auth/structure/manager' => '修改节点管理员',
    'auth/structure/signup' => '添加管理员',
    'roles' => '角色管理',
    'manager' => '管理员',
    'instructions' => '使用说明',
    //角色组管理
    'add roles' => '添加角色',
    'roles name' => '角色名称',
    'roles detail' => '角色详情',
    'roles description' => '角色描述',
    'edit roles' => '编辑角色',
    'add role failure' => '添加角色失败',
    'delete Roles success' => '删除角色成功',
    'No target role optional?' => '没有目标角色可选?',
    'roles group' => '角色组',
    'Update User' => '更新管理员',
    'Add Manager' => '添加用户',
    'Roles Manager' => '角色组管理',
    'Permission Edit' => '权限编辑项',
    'auth/assign/set-default-pass' => '设置默认密码',

    /**
     * 权限管理员
     * 字段翻译
     */
    'account name' => '管理员名称',
    'auth key' => '校验码',
    'Password Hash' => '密码加密',
    'Role' => '角色',
    'Status' => '状态',
    'path' => '结构路径',
    'pid' => '上级ID',
    'manager type' => '管理类型',
    'manager_mgr-portal' => 'Portal 模板',
    'manager_mgr-admin-type' => '管理类型',
    'manager_mgr-admin' => '可管理的用户',
    'manager_mgr-product' => '可管理的产品',
    'max_open_num' => '最大开户数',
    'max_open_num_msg1' => '不能大于自己的开户数。',

    //页面翻译
    'assign title 1' => '管理员基本信息编辑',
    'assign title 2' => '绑定组织结构',
    'assign title 3' => '绑定产品',
    'assign title 4' => '管理类型',
    'assign title 5' => '可管理的管理员',
    'assign title 6' => '可管理的 Portal 模板',
    'assign help 1' => '选中父节点，则其下的所有子节点都可以管理；如果只管理子节点，那么只选中子节点即可；',
    'assign title 7' => '绑定区域结构',
    'assign help1' => '请慎重操作, 您确定要删除该管理员么 ? 删除后不可恢复! ',
    'assign help2' => '蓝色部分为自己创建角色及其子级',
    'assign help3' => '对不起,没有可勾选的管理员',


    //格式化日志的模板
    'operate show default' => '{operator} {action} {action_type} [ {target} ]',
    'operate type Setting Roles' => '角色',
    'Today' => '今天',
    'yesterday' => '昨天',
    'last seven days' => '7天',
    'last thirty days' => '30天',
    'other fee' => '其他费用',
    'system' => '操作系统',
    'show detail' => '查看详情',
    'property' => '属性',
    'new Value' => '值',
    'operate help1' => '点击加载更多',
    'operate help2' => '没有更多数据了',
    '{times} seconds age' => '{times}秒前',
    '{times} minutes ago' => '{times}分钟前',
    '{times} hours ago' => '{times}小时前',
    'Mon' => '周一',
    'Tue' => '周二',
    'Wed' => '周三',
    'Thu' => '周四',
    'Fri' => '周五',
    'Sat' => '周六',
    'Sun' => '周日',
    'operate show Setting Roles' => '设置角色',
    'target' => '操作目标',
    'action select' => '选择行为',
    'start opt time' => '开始时间',
    'end opt time' => '结束时间',
    'operate ip' => '操作ip',
    'action add' => '添加',
    'action import' => '导入',
    'export' => '导出',
    'batch export log help' => '您确定要导出这些操作日志吗 ?',
    'create role' => '创建角色',
    'go create role' => '去创建角色',
    'role info' => '角色在整个权限系统是非常重要的一个环节，它是连接权限和用户的纽带，将用户赋予角色角色赋予权限，这个整个权限系统才能生效',
    'add account for one role' => '为角色添加用户',
    'Role description' => '角色说明',
    'role help1' => '以root开头的权限即为超管, 对其设置权限, 使之权限可管理化, 所能查看的页面即为root查看到的一样',
    'add failure.' => '添加失败,异常: {msg}',
    'user id' => '管理员id',
    'created at' => '添加时间',
];