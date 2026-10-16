-- OpenAPI permissions are managed only in dt_perms and granted through roles.
-- Drop the app-level allowlist introduced by the previous implementation.
DROP TABLE IF EXISTS `dt_app_perms`;

INSERT INTO `dt_perms`
  (`parent_id`,`name`,`node_type`,`permission_key`,`route_path`,`module`,`controller`,`action`,`sort`,`status`,`create_time`,`update_time`)
SELECT 0,'开放接口','group',NULL,NULL,'','','',0,1,0,0
WHERE NOT EXISTS (
  SELECT 1 FROM `dt_perms` WHERE `parent_id`=0 AND `name`='开放接口' AND `node_type`='group'
);

INSERT INTO `dt_perms`
  (`parent_id`,`name`,`node_type`,`permission_key`,`route_path`,`module`,`controller`,`action`,`sort`,`status`,`create_time`,`update_time`)
SELECT `id`,'接口连通性检查','api','openapi.index.ping',NULL,'openapi','index','index',10,1,0,0
FROM `dt_perms` WHERE `parent_id`=0 AND `name`='开放接口' AND `node_type`='group'
  AND NOT EXISTS (SELECT 1 FROM `dt_perms` WHERE `permission_key`='openapi.index.ping');

INSERT INTO `dt_perms`
  (`parent_id`,`name`,`node_type`,`permission_key`,`route_path`,`module`,`controller`,`action`,`sort`,`status`,`create_time`,`update_time`)
SELECT `id`,'获取任务','api','task.api.get_task',NULL,'task','api','getTask',20,1,0,0
FROM `dt_perms` WHERE `parent_id`=0 AND `name`='开放接口' AND `node_type`='group'
  AND NOT EXISTS (SELECT 1 FROM `dt_perms` WHERE `permission_key`='task.api.get_task');

INSERT INTO `dt_perms`
  (`parent_id`,`name`,`node_type`,`permission_key`,`route_path`,`module`,`controller`,`action`,`sort`,`status`,`create_time`,`update_time`)
SELECT `id`,'提交任务报告','api','task.api.set_report',NULL,'task','api','setReport',30,1,0,0
FROM `dt_perms` WHERE `parent_id`=0 AND `name`='开放接口' AND `node_type`='group'
  AND NOT EXISTS (SELECT 1 FROM `dt_perms` WHERE `permission_key`='task.api.set_report');
