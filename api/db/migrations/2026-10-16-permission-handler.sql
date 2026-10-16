-- Run after 2026-10-16-permission-model.sql.
-- Restores the original module/controller/action columns if the previous
-- handler consolidation migration was already applied. Safe if it was not.
SET @permission_schema = DATABASE();

SET @has_module = (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @permission_schema AND TABLE_NAME = 'dt_perms' AND COLUMN_NAME = 'module'
);
SET @sql = IF(@has_module = 0,
  'ALTER TABLE `dt_perms` ADD COLUMN `module` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '''' COMMENT ''所属模块'' AFTER `route_path`',
  'SELECT 1');
PREPARE permission_stmt FROM @sql;
EXECUTE permission_stmt;
DEALLOCATE PREPARE permission_stmt;

SET @has_controller = (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @permission_schema AND TABLE_NAME = 'dt_perms' AND COLUMN_NAME = 'controller'
);
SET @sql = IF(@has_controller = 0,
  'ALTER TABLE `dt_perms` ADD COLUMN `controller` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '''' COMMENT ''控制器'' AFTER `module`',
  'SELECT 1');
PREPARE permission_stmt FROM @sql;
EXECUTE permission_stmt;
DEALLOCATE PREPARE permission_stmt;

SET @has_action = (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @permission_schema AND TABLE_NAME = 'dt_perms' AND COLUMN_NAME = 'action'
);
SET @sql = IF(@has_action = 0,
  'ALTER TABLE `dt_perms` ADD COLUMN `action` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '''' COMMENT ''操作方法'' AFTER `controller`',
  'SELECT 1');
PREPARE permission_stmt FROM @sql;
EXECUTE permission_stmt;
DEALLOCATE PREPARE permission_stmt;

SET @has_handler = (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @permission_schema AND TABLE_NAME = 'dt_perms' AND COLUMN_NAME = 'handler'
);
SET @sql = IF(@has_handler = 1,
  'UPDATE `dt_perms` SET `module` = CASE WHEN `handler` IS NOT NULL AND `handler` <> '''' THEN SUBSTRING_INDEX(`handler`, ''.'', 1) ELSE `module` END, `controller` = CASE WHEN `handler` IS NOT NULL AND `handler` <> '''' THEN SUBSTRING_INDEX(SUBSTRING_INDEX(`handler`, ''.'', 2), ''.'', -1) ELSE `controller` END, `action` = CASE WHEN `node_type` IN (''action'', ''api'') AND `handler` IS NOT NULL AND `handler` <> '''' THEN SUBSTRING_INDEX(`handler`, ''.'', -1) ELSE `action` END',
  'SELECT 1');
PREPARE permission_stmt FROM @sql;
EXECUTE permission_stmt;
DEALLOCATE PREPARE permission_stmt;

SET @has_handler_index = (
  SELECT COUNT(*) FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = @permission_schema AND TABLE_NAME = 'dt_perms' AND INDEX_NAME = 'idx_handler'
);
SET @sql = IF(@has_handler_index = 1, 'ALTER TABLE `dt_perms` DROP INDEX `idx_handler`', 'SELECT 1');
PREPARE permission_stmt FROM @sql;
EXECUTE permission_stmt;
DEALLOCATE PREPARE permission_stmt;

SET @has_handler = (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @permission_schema AND TABLE_NAME = 'dt_perms' AND COLUMN_NAME = 'handler'
);
SET @sql = IF(@has_handler = 1, 'ALTER TABLE `dt_perms` DROP COLUMN `handler`', 'SELECT 1');
PREPARE permission_stmt FROM @sql;
EXECUTE permission_stmt;
DEALLOCATE PREPARE permission_stmt;

SET @has_endpoint_index = (
  SELECT COUNT(*) FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = @permission_schema AND TABLE_NAME = 'dt_perms' AND INDEX_NAME = 'idx_module_controller_action'
);
SET @sql = IF(@has_endpoint_index = 0,
  'ALTER TABLE `dt_perms` ADD INDEX `idx_module_controller_action` (`module`, `controller`, `action`)',
  'SELECT 1');
PREPARE permission_stmt FROM @sql;
EXECUTE permission_stmt;
DEALLOCATE PREPARE permission_stmt;
