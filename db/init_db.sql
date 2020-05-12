-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2015-04-16 08:40:07
-- 服务器版本： 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
CREATE DATABASE IF NOT EXISTS `appointments` CHARSET utf8;


use appointments;

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `srun` 
--

-- --------------------------------------------------------

--
-- 表的结构 `auth_assignment`
--

DROP TABLE IF EXISTS `auth_assignment`;
CREATE TABLE IF NOT EXISTS `auth_assignment` (
  `item_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`item_name`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 转存表中的数据 `auth_assignment`
--

INSERT INTO `auth_assignment` (`item_name`, `user_id`, `created_at`) VALUES
('root', '1', 1586856181);

-- --------------------------------------------------------

--
-- 表的结构 `auth_item`
--

DROP TABLE IF EXISTS `auth_item`;
CREATE TABLE IF NOT EXISTS `auth_item` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `type` int(11) NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `rule_name` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `data` text COLLATE utf8_unicode_ci,
  `path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '角色层级',
  `p_id` int(11) DEFAULT NULL COMMENT '上级ID',
  `by_id` int(11) DEFAULT NULL COMMENT '创建人',
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `auth_item`
--

INSERT INTO `auth_item` (`id`, `name`, `type`, `description`, `rule_name`, `data`, `path`, `p_id`, `by_id`, `created_at`, `updated_at`) VALUES
(1, 'root', 1, '超级管理员', NULL, NULL, '0', 0, NULL, 1586856181, 1586856181);

-- --------------------------------------------------------

--
-- 表的结构 `auth_item_child`
--

DROP TABLE IF EXISTS `auth_item_child`;
CREATE TABLE IF NOT EXISTS `auth_item_child` (
  `parent` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `child` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`parent`,`child`),
  KEY `child` (`child`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `auth_rule`
--

DROP TABLE IF EXISTS `auth_rule`;
CREATE TABLE IF NOT EXISTS `auth_rule` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `data` text COLLATE utf8_unicode_ci,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



-- --------------------------------------------------------

--
-- 表的结构 `manager`
--

DROP TABLE IF EXISTS `manager`;
CREATE TABLE IF NOT EXISTS `manager` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `auth_key` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password_reset_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT '10',
  `pid` int(11) DEFAULT NULL COMMENT '父ID',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

--
-- 管理表中的数据 `manager`
--

INSERT INTO `manager` (`id`, `username`, `auth_key`, `password_hash`, `password_reset_token`, `email`, `status`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'wgsmx0m7uSQ98aBEJ8W-OBowbwqozctq', '$2y$13$2xW..AD7LnU6vG7mZm6sneWuQw5x5P8vu7eE30JS/NVWpI3/JroEG', NULL, 'admin@admin.com', 1,1586856181, 1586856181);


--
-- 日志表的结构 `log_operate`
--

DROP TABLE IF EXISTS `log_operate`;
CREATE TABLE IF NOT EXISTS `log_operate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `operator` varchar(128) COLLATE utf8_unicode_ci NOT NULL COMMENT '操作人',
  `target` varchar(128) COLLATE utf8_unicode_ci NOT NULL COMMENT '操作目标',
  `action` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT '操作动作',
  `action_type` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT '动作类型',
  `content` text COLLATE utf8_unicode_ci NOT NULL COMMENT '操作内容',
  `opt_ip` varchar(32) COLLATE utf8_unicode_ci NOT NULL COMMENT '操作人ip',
  `opt_time` int(11) NOT NULL COMMENT '操作时间',
  `class` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '本日志的类，主要用户解析日志中的字段和值',
  `type` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '日志类型：默认0格式化数据，1描述性日志',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 COMMENT='日志表';



--
-- 手机号码表
--
DROP TABLE IF EXISTS `import_mobiles`;
CREATE TABLE IF NOT EXISTS `import_mobiles`(
 `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 `mobile` CHAR(13) NOT NULL DEFAULT '' COMMENT '手机号',
 `import_time` int(11) NOT NULL COMMENT '导入时间',
 `mgr_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '操作人id',
 `mgr_name` VARCHAR(32) NOT NULL DEFAULT '' COMMENT '操作人名称',
 unique key `mobile`(`mobile`),
 key time_oper(`import_time`, `mgr_name`) using hash
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 COMMENT='导入手机号码表';


--
-- 手机号码预约表
--
DROP TABLE IF EXISTS `user_appointments`;
CREATE TABLE IF NOT EXISTS `user_appointments`(
 `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 `username` VARCHAR(128) NOT NULL DEFAULT '' COMMENT '用户名',
 `mobile` CHAR(11) NOT NULL DEFAULT '' COMMENT '手机号',
 `ip` CHAR(15) NOT NULL DEFAULT  '' COMMENT '预约ip',
 `created_at` int(11) NOT NULL COMMENT '预约时间',
 UNIQUE KEY `mobile`(`mobile`)
)ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 COMMENT='手机号码预约表';

# 增加是否通知字段
ALTER TABLE user_appointments ADD `is_notice` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否通知' AFTER `ip`;

--
-- 操作异常表
--
DROP TABLE IF EXISTS `operate_exception`;
CREATE TABLE IF NOT EXISTS `operate_exception`(
   `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
   `exception_time` INT(11) NOT NULL DEFAULT 0 COMMENT '异常时间',
   `action_type` VARCHAR(20) NOT NULL DEFAULT '' COMMENT '操作类型',
   `ip_addr` CHAR(15) NOT NULL DEFAULT  '' COMMENT '操作ip',
   `err_msg` varchar(256) COLLATE utf8_unicode_ci NOT NULL COMMENT '异常信息',
   PRIMARY KEY (`id`),
   KEY `exception_time`(`exception_time`),
   KEY `action_type`(`action_type`),
   KEY `ip_addr`(`ip_addr`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='操作异常表';


--
-- 创建管理远登录表`manager_login_log`
--
CREATE TABLE IF NOT EXISTS `manager_login_log`(
  `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT '主键id',
  `user_id` INT NOT NULL DEFAULT 0 COMMENT '管理员登录id',
  `manager_name` VARCHAR(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT  '' COMMENT '管理员名称',
  `ip` VARCHAR(64)  COLLATE utf8_unicode_ci  NOT NULL DEFAULT '' COMMENT '登录ip',
  `login_time` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '登录时间',
   KEY `manager_name`(`manager_name`),
   KEY `login_time`(`login_time`),
   KEY `user_id`(`user_id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci  AUTO_INCREMENT = 1 COMMENT='管理员登录日志表';


--
-- 添加域名表， 可以访问的
--
DROP TABLE IF EXISTS `domain_managers`;
CREATE TABLE IF NOT EXISTS `domain_managers`(
  `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT '主键id',
  `domain` VARCHAR(64) NOT NULL DEFAULT '' UNIQUE KEY COMMENT '域名',
  `status` TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否启用',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
)ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci  AUTO_INCREMENT = 1 COMMENT='域名管理表';