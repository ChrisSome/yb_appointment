-- 对数据库操作新增sql
--

use appointments;


ALTER TABLE `user_appointments` ADD `status` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '审核状态0待审核1通过2拒绝' after `ip` ;
ALTER TABLE `user_appointments` ADD `remark` VARCHAR(64)  NOT NULL DEFAULT '' COMMENT '备注' after `status`;
ALTER TABLE `user_appointments` ADD `operator` VARCHAR(64)  NOT NULL DEFAULT '' COMMENT '操作员' after `remark`;
ALTER TABLE `user_appointments` ADD `updated_at` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间' AFTER `created_at`;



CREATE USER `native‘@‘localhost‘ IDENTIFIED WITH mysql_native_password BY ‘password!2#4‘;