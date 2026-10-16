-- Apply once to an existing database after taking a backup.
-- Existing permission IDs and role assignments are preserved.
ALTER TABLE `dt_perms`
  ADD COLUMN `node_type` varchar(16) NOT NULL DEFAULT 'group' COMMENT 'group/page/action/api' AFTER `name`,
  ADD COLUMN `permission_key` varchar(191) NULL DEFAULT NULL COMMENT 'Stable backend permission key' AFTER `node_type`,
  ADD COLUMN `route_path` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL COMMENT 'Registered frontend route path' AFTER `permission_key`;

UPDATE `dt_perms`
SET
  `node_type` = CASE
    WHEN `route_status` = 1 AND `route_name` IN ('/admin/system', '/admin/account', '/admin/finance', '/user') THEN 'group'
    WHEN `route_status` = 1 THEN 'page'
    WHEN `action` <> '' AND `module` <> '' AND `controller` <> '' AND `only_api` = 1 THEN 'api'
    WHEN `action` <> '' AND `module` <> '' AND `controller` <> '' THEN 'action'
    ELSE 'group'
  END,
  `permission_key` = CASE
    WHEN `route_status` = 0 AND `action` <> '' AND `module` <> '' AND `controller` <> ''
      THEN LOWER(CONCAT(`module`, '.', `controller`, '.', `action`))
    ELSE NULL
  END,
  `route_path` = CASE
    WHEN `route_status` = 1 AND `route_name` <> '' THEN `route_name`
    ELSE NULL
  END;

ALTER TABLE `dt_perms`
  ADD UNIQUE KEY `uk_permission_key` (`permission_key`),
  ADD UNIQUE KEY `uk_route_path` (`route_path`),
  DROP COLUMN `route_status`,
  DROP COLUMN `route_name`,
  DROP COLUMN `only_api`;
