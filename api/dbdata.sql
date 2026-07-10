/*
 Navicat Premium Data Transfer

 Source Server         : 127.0.0.1
 Source Server Type    : MySQL
 Source Server Version : 50726
 Source Host           : 127.0.0.1:3306
 Source Schema         : dbdata

 Target Server Type    : MySQL
 Target Server Version : 50726
 File Encoding         : 65001

 Date: 10/07/2026 14:53:15
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
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '应用接口' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of dt_app_list
-- ----------------------------
INSERT INTO `dt_app_list` VALUES (1, 1, 'admin', '1d3gweoqpnqyjponpzovna', 1, '', 1782270416, 1783523439);

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
INSERT INTO `dt_config` VALUES (3, 'logo', 'image', 'LOGO', 'storage/topic/20260707/bf572246e849a76025a6358f9ce6009b.png', NULL, NULL, 50, 1);
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
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '富文本内容' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of dt_editor
-- ----------------------------
INSERT INTO `dt_editor` VALUES (1, 'notice', '系统通知内容', '<p>欢迎使用！</p>\n<p><img src=\"storage/topic/20260707/f1252e4eead4d0c7c548113b2e7cca51.png\" alt=\"\" width=\"96\" height=\"96\" /></p>');
INSERT INTO `dt_editor` VALUES (2, 'test', '测试内容', '<p>666</p>');

-- ----------------------------
-- Table structure for dt_form_field
-- ----------------------------
DROP TABLE IF EXISTS `dt_form_field`;
CREATE TABLE `dt_form_field`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '唯一标识',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '名称',
  `field_json` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `create_time` int(10) NULL DEFAULT 0,
  `update_time` int(10) NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `key`(`key`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '表单字段' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of dt_form_field
-- ----------------------------
INSERT INTO `dt_form_field` VALUES (1, 'formTestAdd', '测试新增', '', 1779028674, 1779028674);

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
) ENGINE = InnoDB AUTO_INCREMENT = 27 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '登录记录' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of dt_login_log
-- ----------------------------
INSERT INTO `dt_login_log` VALUES (1, 1, '', 1782270368, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (2, 1, '', 1783329753, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (3, 1, '', 1783329799, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (4, 1, '', 1783329857, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (5, 1, '', 1783330232, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (6, 1, '', 1783330249, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (7, 1, '', 1783345808, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (8, 1, '', 1783346779, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (9, 1, '', 1783346797, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (10, 1, '', 1783346944, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (11, 1, '', 1783346955, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (12, 1, '', 1783346981, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (13, 1, '', 1783563287, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (14, 2, '', 1783578780, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (15, 2, '', 1783579393, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (16, 1, '', 1783584222, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (17, 1, '', 1783584232, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (18, 1, '', 1783584356, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (19, 1, '', 1783584548, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (20, 2, '', 1783584873, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (21, 1, '', 1783586391, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (22, 1, '', 1783604687, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (23, 1, '', 1783663239, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (24, 1, '', 1783663268, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (25, 2, '', 1783663716, 1, '', '127.0.0.1');
INSERT INTO `dt_login_log` VALUES (26, 2, '', 1783663820, 1, '', '127.0.0.1');

-- ----------------------------
-- Table structure for dt_perms
-- ----------------------------
DROP TABLE IF EXISTS `dt_perms`;
CREATE TABLE `dt_perms`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `parent_id` int(11) NOT NULL DEFAULT 0 COMMENT '父级ID',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '权限名称',
  `module` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '所属模块',
  `controller` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '控制器',
  `action` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '操作方法',
  `sort` int(11) NOT NULL DEFAULT 0 COMMENT '排序(升序)',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：0禁用 1启用',
  `route_status` tinyint(1) NULL DEFAULT 0,
  `route_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '',
  `create_time` int(10) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(10) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_parent_id`(`parent_id`) USING BTREE COMMENT '父级索引',
  INDEX `idx_module_controller_action`(`module`, `controller`, `action`) USING BTREE COMMENT '模块控制器方法组合索引'
) ENGINE = InnoDB AUTO_INCREMENT = 34 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '权限节点表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of dt_perms
-- ----------------------------
INSERT INTO `dt_perms` VALUES (1, 0, '设置', 'super', '', '', 0, 1, 1, '/admin/config', 0, 0);
INSERT INTO `dt_perms` VALUES (2, 1, '系统设置', 'super', 'system', '', 0, 1, 1, '/admin/config/system', 0, 0);
INSERT INTO `dt_perms` VALUES (3, 1, '富文本', 'super', 'editor', '', 0, 1, 1, '/admin/config/editor', 0, 0);
INSERT INTO `dt_perms` VALUES (5, 3, '保存', 'super', 'editor', 'save', 0, 1, 0, '', 0, 0);
INSERT INTO `dt_perms` VALUES (6, 3, '列表', 'super', 'editor', 'items', 0, 1, 0, '', 0, 0);
INSERT INTO `dt_perms` VALUES (7, 2, '列表', 'super', 'config', 'items', 0, 1, 0, '', 0, 0);
INSERT INTO `dt_perms` VALUES (8, 2, '保存', 'super', 'config', 'save', 0, 1, 0, '', 0, 0);
INSERT INTO `dt_perms` VALUES (9, 0, '账号管理', 'super', '', '', 0, 1, 1, '/admin/account', 0, 0);
INSERT INTO `dt_perms` VALUES (10, 9, 'API管理', 'super', 'appList', '', 0, 1, 1, '/admin/account/appList', 0, 0);
INSERT INTO `dt_perms` VALUES (11, 10, '列表', 'super', 'appList', 'items', 0, 1, 0, '', 0, 0);
INSERT INTO `dt_perms` VALUES (12, 10, '新增', 'super', 'appList', 'add', 0, 1, 0, '', 0, 0);
INSERT INTO `dt_perms` VALUES (13, 10, '编辑', 'super', 'appList', 'edit', 0, 1, 0, '', 0, 0);
INSERT INTO `dt_perms` VALUES (14, 10, '删除', 'super', 'appList', 'del', 0, 1, 0, '', 0, 0);
INSERT INTO `dt_perms` VALUES (15, 9, '角色管理', 'super', 'roles', '', 0, 1, 1, '/admin/account/roles', 0, 0);
INSERT INTO `dt_perms` VALUES (16, 15, '列表', 'super', 'roles', 'items', 0, 1, 0, '', 0, 0);
INSERT INTO `dt_perms` VALUES (17, 15, 'All', 'super', 'roles', 'all', 0, 1, 0, '', 0, 0);
INSERT INTO `dt_perms` VALUES (18, 15, '新增', 'super', 'roles', 'add', 0, 1, 0, '', 0, 0);
INSERT INTO `dt_perms` VALUES (19, 15, '编辑', 'super', 'roles', 'edit', 0, 1, 0, '', 0, 0);
INSERT INTO `dt_perms` VALUES (20, 15, '删除', 'super', 'roles', 'del', 0, 1, 0, '', 0, 0);
INSERT INTO `dt_perms` VALUES (21, 9, '权限关联', 'super', 'RolePerms', '', 0, 1, 0, '', 0, 0);
INSERT INTO `dt_perms` VALUES (22, 21, '保存', 'super', 'RolePerms', 'save', 0, 1, 0, '', 0, 0);
INSERT INTO `dt_perms` VALUES (23, 9, '权限节点', 'super', 'perms', '', 0, 1, 1, '/admin/account/perms', 0, 0);
INSERT INTO `dt_perms` VALUES (24, 23, '列表', 'super', 'perms', 'items', 0, 1, 0, '', 0, 0);
INSERT INTO `dt_perms` VALUES (25, 23, 'All', 'super', 'perms', 'all', 0, 1, 0, '', 0, 0);
INSERT INTO `dt_perms` VALUES (26, 23, '新增', 'super', 'perms', 'add', 0, 1, 0, '', 0, 0);
INSERT INTO `dt_perms` VALUES (27, 23, '编辑', 'super', 'perms', 'edit', 0, 1, 0, '', 0, 0);
INSERT INTO `dt_perms` VALUES (28, 23, '删除', 'super', 'perms', 'del', 0, 1, 0, '', 0, 0);
INSERT INTO `dt_perms` VALUES (29, 9, '账号列表', 'super', 'member', '', 0, 1, 1, '/admin/account/member', 0, 0);
INSERT INTO `dt_perms` VALUES (30, 29, '列表', 'super', 'member', 'items', 0, 1, 0, '', 0, 0);
INSERT INTO `dt_perms` VALUES (31, 29, '新增', 'super', 'member', 'add', 0, 1, 0, '', 0, 0);
INSERT INTO `dt_perms` VALUES (32, 29, '编辑', 'super', 'member', 'edit', 0, 1, 0, '', 0, 0);
INSERT INTO `dt_perms` VALUES (33, 29, '删除', 'super', 'member', 'del', 0, 1, 0, '', 0, 0);

-- ----------------------------
-- Table structure for dt_role_perms
-- ----------------------------
DROP TABLE IF EXISTS `dt_role_perms`;
CREATE TABLE `dt_role_perms`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '角色类型(关联dt_roles.type)',
  `perms_id` int(10) NOT NULL DEFAULT 0 COMMENT '权限ID(关联dt_perms.id)',
  `create_time` bigint(10) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(10) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `uk_type_perms_id`(`type`, `perms_id`) USING BTREE COMMENT '角色类型+权限ID唯一索引',
  INDEX `idx_type`(`type`) USING BTREE COMMENT '角色类型索引',
  INDEX `idx_perms_id`(`perms_id`) USING BTREE COMMENT '权限ID索引'
) ENGINE = InnoDB AUTO_INCREMENT = 422 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '角色-权限关联表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of dt_role_perms
-- ----------------------------
INSERT INTO `dt_role_perms` VALUES (168, 'super', 1, 1783603227, 1783603227);
INSERT INTO `dt_role_perms` VALUES (169, 'super', 2, 1783603227, 1783603227);
INSERT INTO `dt_role_perms` VALUES (170, 'super', 7, 1783603227, 1783603227);
INSERT INTO `dt_role_perms` VALUES (171, 'super', 8, 1783603227, 1783603227);
INSERT INTO `dt_role_perms` VALUES (172, 'super', 3, 1783603227, 1783603227);
INSERT INTO `dt_role_perms` VALUES (173, 'super', 5, 1783603227, 1783603227);
INSERT INTO `dt_role_perms` VALUES (174, 'super', 6, 1783603227, 1783603227);
INSERT INTO `dt_role_perms` VALUES (175, 'super', 9, 1783603227, 1783603227);
INSERT INTO `dt_role_perms` VALUES (176, 'super', 10, 1783603227, 1783603227);
INSERT INTO `dt_role_perms` VALUES (177, 'super', 11, 1783603227, 1783603227);
INSERT INTO `dt_role_perms` VALUES (178, 'super', 12, 1783603227, 1783603227);
INSERT INTO `dt_role_perms` VALUES (179, 'super', 13, 1783603227, 1783603227);
INSERT INTO `dt_role_perms` VALUES (180, 'super', 14, 1783603227, 1783603227);
INSERT INTO `dt_role_perms` VALUES (181, 'super', 15, 1783603227, 1783603227);
INSERT INTO `dt_role_perms` VALUES (182, 'super', 16, 1783603227, 1783603227);
INSERT INTO `dt_role_perms` VALUES (183, 'super', 17, 1783603227, 1783603227);
INSERT INTO `dt_role_perms` VALUES (184, 'super', 18, 1783603227, 1783603227);
INSERT INTO `dt_role_perms` VALUES (185, 'super', 19, 1783603227, 1783603227);
INSERT INTO `dt_role_perms` VALUES (186, 'super', 20, 1783603227, 1783603227);
INSERT INTO `dt_role_perms` VALUES (187, 'super', 21, 1783603227, 1783603227);
INSERT INTO `dt_role_perms` VALUES (188, 'super', 22, 1783603227, 1783603227);
INSERT INTO `dt_role_perms` VALUES (189, 'super', 23, 1783603227, 1783603227);
INSERT INTO `dt_role_perms` VALUES (190, 'super', 24, 1783603227, 1783603227);
INSERT INTO `dt_role_perms` VALUES (191, 'super', 25, 1783603227, 1783603227);
INSERT INTO `dt_role_perms` VALUES (192, 'super', 26, 1783603227, 1783603227);
INSERT INTO `dt_role_perms` VALUES (193, 'super', 27, 1783603227, 1783603227);
INSERT INTO `dt_role_perms` VALUES (194, 'super', 28, 1783603227, 1783603227);
INSERT INTO `dt_role_perms` VALUES (195, 'super', 29, 1783603227, 1783603227);
INSERT INTO `dt_role_perms` VALUES (196, 'super', 30, 1783603227, 1783603227);
INSERT INTO `dt_role_perms` VALUES (197, 'super', 31, 1783603227, 1783603227);
INSERT INTO `dt_role_perms` VALUES (198, 'super', 32, 1783603227, 1783603227);
INSERT INTO `dt_role_perms` VALUES (199, 'super', 33, 1783603227, 1783603227);
INSERT INTO `dt_role_perms` VALUES (403, 'admin', 1, 1783666348, 1783666348);
INSERT INTO `dt_role_perms` VALUES (404, 'admin', 2, 1783666348, 1783666348);
INSERT INTO `dt_role_perms` VALUES (405, 'admin', 7, 1783666348, 1783666348);
INSERT INTO `dt_role_perms` VALUES (406, 'admin', 8, 1783666348, 1783666348);
INSERT INTO `dt_role_perms` VALUES (407, 'admin', 3, 1783666348, 1783666348);
INSERT INTO `dt_role_perms` VALUES (408, 'admin', 5, 1783666348, 1783666348);
INSERT INTO `dt_role_perms` VALUES (409, 'admin', 6, 1783666348, 1783666348);
INSERT INTO `dt_role_perms` VALUES (410, 'admin', 10, 1783666348, 1783666348);
INSERT INTO `dt_role_perms` VALUES (411, 'admin', 11, 1783666348, 1783666348);
INSERT INTO `dt_role_perms` VALUES (412, 'admin', 12, 1783666348, 1783666348);
INSERT INTO `dt_role_perms` VALUES (413, 'admin', 13, 1783666348, 1783666348);
INSERT INTO `dt_role_perms` VALUES (414, 'admin', 14, 1783666348, 1783666348);
INSERT INTO `dt_role_perms` VALUES (415, 'admin', 17, 1783666348, 1783666348);
INSERT INTO `dt_role_perms` VALUES (416, 'admin', 25, 1783666348, 1783666348);
INSERT INTO `dt_role_perms` VALUES (417, 'admin', 29, 1783666348, 1783666348);
INSERT INTO `dt_role_perms` VALUES (418, 'admin', 30, 1783666348, 1783666348);
INSERT INTO `dt_role_perms` VALUES (419, 'admin', 31, 1783666348, 1783666348);
INSERT INTO `dt_role_perms` VALUES (420, 'admin', 32, 1783666348, 1783666348);
INSERT INTO `dt_role_perms` VALUES (421, 'admin', 33, 1783666348, 1783666348);

-- ----------------------------
-- Table structure for dt_roles
-- ----------------------------
DROP TABLE IF EXISTS `dt_roles`;
CREATE TABLE `dt_roles`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '角色类型唯一标识(对应用户表角色字段)',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '角色名称',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：0禁用 1启用',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '备注',
  `create_time` int(10) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(10) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `uk_type`(`type`) USING BTREE COMMENT '角色类型唯一索引'
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '角色表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of dt_roles
-- ----------------------------
INSERT INTO `dt_roles` VALUES (1, 'super', '超管', 1, '', 1783564275, 1783579631);
INSERT INTO `dt_roles` VALUES (2, 'admin', '管理员', 1, '', 1783564283, 1783576767);
INSERT INTO `dt_roles` VALUES (3, 'user', '用户', 1, '', 1783578101, 1783578101);

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
) ENGINE = InnoDB AUTO_INCREMENT = 38 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of dt_upload
-- ----------------------------
INSERT INTO `dt_upload` VALUES (1, 'phpE6E6.tmp', 7668, 'image/png', 'image', '/storage/topic/20260429/e0144674ba48ce1a230c85cffb412ba4.png', 1777464228);
INSERT INTO `dt_upload` VALUES (2, 'phpDE3F.tmp', 7668, 'image/png', 'image', '/storage/topic/20260429/02d70216955419bec3f1a1e1f59ef665.png', 1777465471);
INSERT INTO `dt_upload` VALUES (3, 'php4098.tmp', 7668, 'image/png', 'image', '/storage/topic/20260707/80b6ca1eb4f7134df86873251d16c819.png', 1783387528);
INSERT INTO `dt_upload` VALUES (4, 'phpD400.tmp', 7668, 'image/png', 'image', '/storage/topic/20260707/8b1b9a7c660071b2353c998fa1a30c94.png', 1783387566);
INSERT INTO `dt_upload` VALUES (5, 'php570A.tmp', 7668, 'image/png', 'image', '/storage/topic/20260707/d094b640fbba50d449a269a7bbcde8db.png', 1783387600);
INSERT INTO `dt_upload` VALUES (6, 'phpA819.tmp', 7668, 'image/png', 'image', '/storage/topic/20260707/81dc5cf322010b73d44b894f153eb15c.png', 1783387620);
INSERT INTO `dt_upload` VALUES (7, 'phpD842.tmp', 7668, 'image/png', 'image', '/storage/topic/20260707/d51e9cdd3102de10f340e0cb846ec714.png', 1783387633);
INSERT INTO `dt_upload` VALUES (8, 'phpE492.tmp', 7668, 'image/png', 'image', '/storage/topic/20260707/bf572246e849a76025a6358f9ce6009b.png', 1783387701);
INSERT INTO `dt_upload` VALUES (9, 'phpB598.tmp', 7668, 'image/png', 'image', '/storage/topic/20260707/aeccf7566eed138d2b6974d3c580737e.png', 1783400141);
INSERT INTO `dt_upload` VALUES (10, 'php60CA.tmp', 7668, 'image/png', 'image', '/storage/topic/20260707/274861c3dd327330dc2a0d4bda3135a7.png', 1783400185);
INSERT INTO `dt_upload` VALUES (11, 'phpB67E.tmp', 7668, 'image/png', 'image', '/storage/topic/20260707/f2ccb8ea79df478fcde74dc556439e7c.png', 1783400207);
INSERT INTO `dt_upload` VALUES (12, 'php299C.tmp', 7668, 'image/png', 'image', '/storage/topic/20260707/444acbf23805e59e48dc9f767d96f49b.png', 1783400236);
INSERT INTO `dt_upload` VALUES (13, 'php88A1.tmp', 7668, 'image/png', 'image', '/storage/topic/20260707/5e7e7f3ace17444c0d3131729cfc8a1a.png', 1783400261);
INSERT INTO `dt_upload` VALUES (14, 'phpCFBD.tmp', 7668, 'image/png', 'image', '/storage/topic/20260707/c26cade61debf5f72f77a1112a0a09ca.png', 1783400279);
INSERT INTO `dt_upload` VALUES (15, 'php20EE.tmp', 7668, 'image/png', 'image', '/storage/topic/20260707/6d3032982b5e2194b710f2a40a03116d.png', 1783400300);
INSERT INTO `dt_upload` VALUES (16, 'php7354.tmp', 7668, 'image/png', 'image', '/storage/topic/20260707/696b7035ed751e793ceff37c34893c3c.png', 1783400321);
INSERT INTO `dt_upload` VALUES (17, 'php6910.tmp', 7668, 'image/png', 'image', '/storage/topic/20260707/33a8cbf7768814ea71c50e4387a05a53.png', 1783400384);
INSERT INTO `dt_upload` VALUES (18, 'phpBA68.tmp', 7668, 'image/png', 'image', '/storage/topic/20260707/38140bad89e5f7306f8394a414ea9616.png', 1783400405);
INSERT INTO `dt_upload` VALUES (19, 'php117C.tmp', 7668, 'image/png', 'image', '/storage/topic/20260707/fa1812d3daee3d83ee7f4c15b35c2922.png', 1783400558);
INSERT INTO `dt_upload` VALUES (20, 'php23E2.tmp', 7668, 'image/png', 'image', '/storage/topic/20260707/ffccb05a3361a93986a97b6cf072bbd9.png', 1783400694);
INSERT INTO `dt_upload` VALUES (21, 'phpB59E.tmp', 7668, 'image/png', 'image', '/storage/topic/20260707/4f057e3a20a181ee236ad7edba547bc6.png', 1783400731);
INSERT INTO `dt_upload` VALUES (22, 'php1FF.tmp', 7668, 'image/png', 'image', '/storage/topic/20260707/e126c8b95be6289308237990eb22c3e7.png', 1783400751);
INSERT INTO `dt_upload` VALUES (23, 'php671C.tmp', 7668, 'image/png', 'image', '/storage/topic/20260707/f0115cbf4b49fea9fdc2e3ff04242981.png', 1783400776);
INSERT INTO `dt_upload` VALUES (24, 'phpFD72.tmp', 7668, 'image/png', 'image', '/storage/topic/20260707/2c07dd87937a32ad30086430ab99a030.png', 1783400815);
INSERT INTO `dt_upload` VALUES (25, 'phpBEAB.tmp', 7668, 'image/png', 'image', '/storage/topic/20260707/7db7421f31d7dba40ea7c63752bafb59.png', 1783400930);
INSERT INTO `dt_upload` VALUES (26, 'phpE88C.tmp', 7668, 'image/png', 'image', '/storage/topic/20260707/254578e8471ec13f938042e433e9ca33.png', 1783401137);
INSERT INTO `dt_upload` VALUES (27, 'php1B74.tmp', 7668, 'image/png', 'image', '/storage/topic/20260707/64430c8c6320d3c802b56bb1dd52361b.png', 1783401150);
INSERT INTO `dt_upload` VALUES (28, 'phpCA4F.tmp', 7668, 'image/png', 'image', '/storage/topic/20260707/95bd4f9f5680161eb3cca472dced6016.png', 1783401261);
INSERT INTO `dt_upload` VALUES (29, 'php1FB3.tmp', 7668, 'image/png', 'image', '/storage/topic/20260707/230cf5d0e1ed5f47d7311e1a4808d006.png', 1783401282);
INSERT INTO `dt_upload` VALUES (30, 'phpD797.tmp', 7668, 'image/png', 'image', '/storage/topic/20260707/a5be952ca0095d68c143e907b74042d4.png', 1783401329);
INSERT INTO `dt_upload` VALUES (31, 'phpD69A.tmp', 7668, 'image/png', 'image', '/storage/topic/20260707/ebddcac3fd85863796d369182e7930b1.png', 1783401395);
INSERT INTO `dt_upload` VALUES (32, 'php1239.tmp', 7668, 'image/png', 'image', '/storage/topic/20260707/35f396ca4d133836063febff97406f65.png', 1783401476);
INSERT INTO `dt_upload` VALUES (33, 'php6D77.tmp', 7668, 'image/png', 'image', '/storage/topic/20260707/4a99c58cad172a7453432713798a801d.png', 1783401499);
INSERT INTO `dt_upload` VALUES (34, 'php8D2B.tmp', 7668, 'image/png', 'image', '/storage/topic/20260707/a8b6d217b22ab17739b539707e339338.png', 1783401638);
INSERT INTO `dt_upload` VALUES (35, 'phpFB6.tmp', 7668, 'image/png', 'image', '/storage/topic/20260707/188a9b2dadb2f663f9f7ffb88bbefba5.png', 1783401737);
INSERT INTO `dt_upload` VALUES (36, 'php87C4.tmp', 7668, 'image/png', 'image', '/storage/topic/20260707/ef02cb4772ee08cf32185cb0ab1d4997.png', 1783401768);
INSERT INTO `dt_upload` VALUES (37, 'php3B9A.tmp', 7668, 'image/png', 'image', '/storage/topic/20260707/f1252e4eead4d0c7c548113b2e7cca51.png', 1783401945);

-- ----------------------------
-- Table structure for dt_user
-- ----------------------------
DROP TABLE IF EXISTS `dt_user`;
CREATE TABLE `dt_user`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '账号类型 admin  user',
  `username` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '账号',
  `nickname` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT '' COMMENT '昵称',
  `password` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `avatar` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT '' COMMENT '头像',
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
INSERT INTO `dt_user` VALUES (1, 'super', 'admin', '', '0d3e24d606ccb8b0e1e9d5c11bdfe1ae', '', 'xump', 1, 1652500366, 1783578761);
INSERT INTO `dt_user` VALUES (2, 'admin', '张三', '', 'f56fe008fb5908e9bd35a64730cc5226', '', 'cNc2', 1, 1652506679, 1783578752);

SET FOREIGN_KEY_CHECKS = 1;
