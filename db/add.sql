-- 对数据库操作新增sql
--

use appointments;


ALTER TABLE `user_appointments` ADD `status` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '审核状态0待审核1通过2拒绝' after `ip` ;
ALTER TABLE `user_appointments` ADD `remark` VARCHAR(64)  NOT NULL DEFAULT '' COMMENT '备注' after `status`;
ALTER TABLE `user_appointments` ADD `operator` VARCHAR(64)  NOT NULL DEFAULT '' COMMENT '操作员' after `remark`;
ALTER TABLE `user_appointments` ADD `updated_at` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间' AFTER `created_at`;




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