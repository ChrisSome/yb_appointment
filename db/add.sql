-- 对数据库操作新增sql
--

use appointments;


ALTER TABLE `user_appointments` ADD `status` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '审核状态0待审核1通过2拒绝' after `ip` ;
ALTER TABLE `user_appointments` ADD `remark` VARCHAR(64)  NOT NULL DEFAULT '' COMMENT '备注' after `status`;
ALTER TABLE `user_appointments` ADD `operator` VARCHAR(64)  NOT NULL DEFAULT '' COMMENT '操作员' after `remark`;
ALTER TABLE `user_appointments` ADD `updated_at` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间' AFTER `created_at`;


--
-- 去除唯一索引
--
ALTER TABLE `import_mobiles` drop key `mobile`;
alter table `import_mobiles` add key `mobile`(`mobile`);

--
-- 增加ip黑名单功能
--
DROP TABLE IF EXISTS `ip_blacklists`;
CREATE TABLE IF NOT EXISTS `ip_blacklists`(
  `id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT  PRIMARY KEY,
  `ip_addr` INT NOT NULL UNIQUE KEY,
  `mgr_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '操作人id',
  `mgr_name` VARCHAR(32) NOT NULL DEFAULT '' COMMENT '操作人名称',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
)ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='操作异常表';

# 增加上传记录表
drop table if exists `upload_files`;
CREATE TABLE IF NOT EXISTS `upload_files`(
    id INT UNSIGNED NOT NULL AUTO_INCREMENT  PRIMARY KEY,
    `file` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '上传文件',
    `status` tinyint unsigned not null default 0 comment '是否处理0未处理1处理中2处理完毕',
    `result` VARCHAR(255) NOT NULL DEFAULT "" COMMENT '处理结果',
    `mgr_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '操作人id',
    `mgr_name` VARCHAR(32) NOT NULL DEFAULT '' COMMENT '操作人名称',
    `created_at` int(11) NOT NULL,
    `updated_at` int(11) NOT NULL
)ENGINE=Innodb DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='增加上传记录表';



# 创建短信记录表
DROP TABLE IF EXISTS `sms_history`;
CREATE TABLE IF NOT EXISTS `sms_history`(
    id INT UNSIGNED  AUTO_INCREMENT  PRIMARY KEY,
    `phone` char(13) not null comment '手机号',
    `content` VARCHAR(255) NOT NULL DEFAULT "" COMMENT '发送内容',
    `mgr_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '操作人id',
    `mgr_name` VARCHAR(32) NOT NULL DEFAULT '' COMMENT '操作人名称',
    `sender_id` VARCHAR(128) NOT NULL COMMENT '三方id',
    `ip_addr` CHAR(15) NOT NULL DEFAULT  '' COMMENT '操作ip',
    `status` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否使用',
    `type` TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '1验证码2短信',
    `created_at` int(11) NOT NULL,
    INDEX `phone`(`phone`),
    INDEX `ip`(`ip_addr`)
)ENGINE=Innodb DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='短信记录表';