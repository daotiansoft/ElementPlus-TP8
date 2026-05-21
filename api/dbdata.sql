/*
 Navicat Premium Data Transfer

 Source Server         : 127.0.0.1
 Source Server Type    : MySQL
 Source Server Version : 50726
 Source Host           : 127.0.0.1:3306
 Source Schema         : credit_diagnosis_report

 Target Server Type    : MySQL
 Target Server Version : 50726
 File Encoding         : 65001

 Date: 21/05/2026 12:27:39
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for dt_app_list
-- ----------------------------
DROP TABLE IF EXISTS `dt_app_list`;
CREATE TABLE `dt_app_list`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NULL DEFAULT 0,
  `appid` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT '',
  `secret` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT '',
  `status` tinyint(1) NULL DEFAULT 1,
  `remark` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT '',
  `create_time` int(10) NULL DEFAULT 0,
  `update_time` int(10) NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `appid`(`appid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 15 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '应用接口' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of dt_app_list
-- ----------------------------
INSERT INTO `dt_app_list` VALUES (14, 2, 'test', 'test', 1, '', 1772865592, 1772865592);

-- ----------------------------
-- Table structure for dt_config
-- ----------------------------
DROP TABLE IF EXISTS `dt_config`;
CREATE TABLE `dt_config`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '英文名称',
  `type` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '类型 text image select',
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '中文名称',
  `content` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '内容',
  `placeholder` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '提示内容',
  `params` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '附加内容',
  `sort` int(10) NULL DEFAULT 50,
  `show` tinyint(1) NULL DEFAULT 1 COMMENT '是否显示 1是-1否',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '配置' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of dt_config
-- ----------------------------
INSERT INTO `dt_config` VALUES (1, 'site_name', 'text', '站点名称', '管理面板', '', NULL, 10, 1);
INSERT INTO `dt_config` VALUES (2, 'site_status', 'select', '是否闭站', '1', '站点关闭后，用户无法登录', '[{\"id\":1,\"name\":\"否\"},{\"id\":2,\"name\":\"是\"}]', 10, 1);
INSERT INTO `dt_config` VALUES (3, 'logo', 'image', 'LOGO', 'storage/topic/20260429/e0144674ba48ce1a230c85cffb412ba4.png', NULL, NULL, 50, 1);
INSERT INTO `dt_config` VALUES (4, 'site_stop_msg', 'text', '闭站提示', '系统维护中...', NULL, NULL, 10, 1);
INSERT INTO `dt_config` VALUES (5, 'demo_textarea', 'textarea', '多行文本', NULL, NULL, NULL, 1, -1);
INSERT INTO `dt_config` VALUES (6, 'captcha_status', 'select', '登录验证码', '2', NULL, '[{\"id\":1,\"name\":\"启用\"},{\"id\":2,\"name\":\"关闭\"}]', 50, 1);

-- ----------------------------
-- Table structure for dt_editor
-- ----------------------------
DROP TABLE IF EXISTS `dt_editor`;
CREATE TABLE `dt_editor`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '英文名称',
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '中文名称',
  `content` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL COMMENT '内容',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `key`(`key`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '富文本内容' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of dt_editor
-- ----------------------------
INSERT INTO `dt_editor` VALUES (1, 'notice', '系统通知内容', '<p>欢迎使用！</p>');

-- ----------------------------
-- Table structure for dt_login_log
-- ----------------------------
DROP TABLE IF EXISTS `dt_login_log`;
CREATE TABLE `dt_login_log`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(20) NULL DEFAULT 0,
  `device` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '登录设备',
  `time` int(20) NULL DEFAULT 0,
  `status` tinyint(1) NULL DEFAULT 1 COMMENT '状态1成功2失败',
  `desc` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `ip` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 45 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '登录记录' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of dt_login_log
-- ----------------------------
INSERT INTO `dt_login_log` VALUES (1, 1, 'api', 1693893501, 1, '登录成功', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (2, 1, '', 1772786856, 2, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (3, 1, '', 1772786862, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (4, 1, '', 1772786964, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (5, 1, '', 1772786983, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (6, 1, '', 1772787015, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (7, 1, '', 1772787059, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (8, 1, '', 1772787094, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (9, 1, '', 1772787120, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (10, 1, '', 1772787169, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (11, 1, '', 1772787215, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (12, 1, '', 1772787231, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (13, 1, '', 1772787276, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (14, 1, '', 1772787309, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (15, 1, '', 1772787348, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (16, 1, '', 1772787379, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (17, 1, '', 1772787395, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (18, 1, '', 1772787425, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (19, 1, '', 1772787456, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (20, 1, '', 1772787490, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (21, 1, '', 1772787507, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (22, 1, '', 1772787516, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (23, 1, '', 1772787549, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (24, 1, '', 1772787558, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (25, 1, '', 1772787571, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (26, 1, '', 1772787584, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (27, 1, '', 1772787595, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (28, 1, '', 1772846672, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (29, 1, '', 1772846764, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (30, 1, '', 1772848581, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (31, 1, '', 1772848651, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (32, 1, '', 1772849985, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (33, 1, '', 1772850428, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (34, 1, '', 1772850457, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (35, 1, '', 1772850831, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (36, 1, '', 1772851277, 2, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (37, 1, '', 1772851285, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (38, 1, '', 1772856924, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (39, 1, '', 1772860651, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (40, 1, '', 1776436153, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (41, 1, '', 1776493196, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (42, 1, '', 1777387617, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (43, 1, '', 1777464110, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (44, 1, '', 1779337555, 1, '', '127.0.0.1');

-- ----------------------------
-- Table structure for dt_perms
-- ----------------------------
DROP TABLE IF EXISTS `dt_perms`;
CREATE TABLE `dt_perms`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT '',
  `type` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `module` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT '' COMMENT '模块',
  `controller` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '控制器',
  `action` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '方法',
  `status` tinyint(1) NULL DEFAULT 1,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `type`(`type`, `module`, `controller`, `action`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 25 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '权限' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of dt_perms
-- ----------------------------
INSERT INTO `dt_perms` VALUES (1, '测试', 'user', 'webapi', 'index', 'test', 1);
INSERT INTO `dt_perms` VALUES (2, '配置', 'admin', 'super', 'config', 'items', 1);
INSERT INTO `dt_perms` VALUES (3, '配置', 'admin', 'super', 'config', 'save', 1);
INSERT INTO `dt_perms` VALUES (4, '富文本', 'admin', 'super', 'editor', 'items', 1);
INSERT INTO `dt_perms` VALUES (5, '富文本', 'admin', 'super', 'editor', 'save', 1);
INSERT INTO `dt_perms` VALUES (6, '账号管理', 'admin', 'super', 'member', 'items', 1);
INSERT INTO `dt_perms` VALUES (7, '账号管理', 'admin', 'super', 'member', 'add', 1);
INSERT INTO `dt_perms` VALUES (8, '账号管理', 'admin', 'super', 'member', 'edit', 1);
INSERT INTO `dt_perms` VALUES (9, '账号管理', 'admin', 'super', 'member', 'del', 1);
INSERT INTO `dt_perms` VALUES (10, '接口管理', 'admin', 'super', 'AppList', 'items', 1);
INSERT INTO `dt_perms` VALUES (11, '接口管理', 'admin', 'super', 'applist', 'add', 1);
INSERT INTO `dt_perms` VALUES (12, '接口管理', 'admin', 'super', 'applist', 'edit', 1);
INSERT INTO `dt_perms` VALUES (13, '接口管理', 'admin', 'super', 'applist', 'del', 1);
INSERT INTO `dt_perms` VALUES (14, '角色管理', 'admin', 'super', 'roles', 'items', 1);
INSERT INTO `dt_perms` VALUES (15, '角色管理', 'admin', 'super', 'roles', 'add', 1);
INSERT INTO `dt_perms` VALUES (16, '角色管理', 'admin', 'super', 'roles', 'edit', 1);
INSERT INTO `dt_perms` VALUES (17, '角色管理', 'admin', 'super', 'roles', 'del', 1);
INSERT INTO `dt_perms` VALUES (18, '角色管理', 'admin', 'super', 'roles', 'all', 1);
INSERT INTO `dt_perms` VALUES (19, '权限管理', 'admin', 'super', 'perms', 'items', 1);
INSERT INTO `dt_perms` VALUES (20, '权限管理', 'admin', 'super', 'perms', 'add', 1);
INSERT INTO `dt_perms` VALUES (21, '权限管理', 'admin', 'super', 'perms', 'edit', 1);
INSERT INTO `dt_perms` VALUES (22, '权限管理', 'admin', 'super', 'perms', 'types', 1);
INSERT INTO `dt_perms` VALUES (24, '权限管理', 'admin', 'super', 'Perms', 'del', 1);

-- ----------------------------
-- Table structure for dt_roles
-- ----------------------------
DROP TABLE IF EXISTS `dt_roles`;
CREATE TABLE `dt_roles`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT '',
  `name` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT '',
  `status` tinyint(1) NULL DEFAULT 0,
  `remark` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT '',
  `create_time` int(10) NULL DEFAULT 0,
  `update_time` int(10) NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `type`(`type`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '角色' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of dt_roles
-- ----------------------------
INSERT INTO `dt_roles` VALUES (1, 'admin', '管理员', 1, '不可删除', 1772866827, 1772866858);
INSERT INTO `dt_roles` VALUES (2, 'user', '普通账号', 1, '不可删除', 1772866849, 1772866855);

-- ----------------------------
-- Table structure for dt_upload
-- ----------------------------
DROP TABLE IF EXISTS `dt_upload`;
CREATE TABLE `dt_upload`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT '',
  `size` int(20) NULL DEFAULT 0,
  `mime` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT '',
  `type` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT '',
  `path` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT '',
  `time` int(20) NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of dt_upload
-- ----------------------------
INSERT INTO `dt_upload` VALUES (1, 'phpE6E6.tmp', 7668, 'image/png', 'image', '/storage/topic/20260429/e0144674ba48ce1a230c85cffb412ba4.png', 1777464228);
INSERT INTO `dt_upload` VALUES (2, 'phpDE3F.tmp', 7668, 'image/png', 'image', '/storage/topic/20260429/02d70216955419bec3f1a1e1f59ef665.png', 1777465471);

-- ----------------------------
-- Table structure for dt_user
-- ----------------------------
DROP TABLE IF EXISTS `dt_user`;
CREATE TABLE `dt_user`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '账号类型 admin  user',
  `username` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `password` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `rand_code` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '随机码',
  `status` tinyint(1) NULL DEFAULT 1 COMMENT '状态1正常2停用',
  `create_time` int(20) NULL DEFAULT 0,
  `update_time` int(20) NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `username`(`username`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '账号表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of dt_user
-- ----------------------------
INSERT INTO `dt_user` VALUES (1, 'admin', 'admin', '0d3e24d606ccb8b0e1e9d5c11bdfe1ae', 'xump', 1, 1652500366, 0);
INSERT INTO `dt_user` VALUES (2, 'user', '张三', 'f56fe008fb5908e9bd35a64730cc5226', 'cNc2', 1, 1652506679, 1772868471);

SET FOREIGN_KEY_CHECKS = 1;
