ALTER TABLE `dt_perms`
  MODIFY `permission_key` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL COMMENT 'Stable backend permission key',
  MODIFY `module` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '所属模块',
  MODIFY `controller` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '控制器',
  MODIFY `action` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '操作方法';

UPDATE `dt_perms`
SET `controller` = CASE LOWER(`controller`)
  WHEN 'applist' THEN 'AppList'
  WHEN 'balance' THEN 'Balance'
  WHEN 'config' THEN 'Config'
  WHEN 'configgroup' THEN 'ConfigGroup'
  WHEN 'configlist' THEN 'ConfigList'
  WHEN 'editor' THEN 'Editor'
  WHEN 'index' THEN 'Index'
  WHEN 'member' THEN 'Member'
  WHEN 'perms' THEN 'Perms'
  WHEN 'roleperms' THEN 'RolePerms'
  WHEN 'roles' THEN 'Roles'
  ELSE `controller`
END
WHERE `controller` <> '';

UPDATE `dt_perms`
SET `controller` = 'Config'
WHERE `route_path` = '/admin/system/config'
  AND `node_type` = 'page'
  AND LOWER(`controller`) = 'system';

UPDATE `dt_perms`
SET `action` = CASE
  WHEN `controller` IN ('Roles', 'ConfigGroup', 'Member') AND LOWER(`action`) = 'getoptions' THEN 'getOptions'
  WHEN `controller` = 'Config' AND LOWER(`action`) = 'clearcache' THEN 'clearCache'
  WHEN `controller` = 'Api' AND LOWER(`action`) = 'gettask' THEN 'getTask'
  WHEN `controller` = 'Api' AND LOWER(`action`) = 'setreport' THEN 'setReport'
  ELSE `action`
END
WHERE `node_type` IN ('action', 'api');
