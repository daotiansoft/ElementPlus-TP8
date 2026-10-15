-- 2026-10-15 热点查询索引优化
-- 适用：MySQL 5.7 / 8.0，库名 dbdata，表前缀 dt_
--
-- 说明：
--   均为二级索引变更，InnoDB 走 ONLINE DDL（INPLACE + LOCK=NONE），执行期间不阻塞读写。
--   api/dbdata.sql 的建表语句已同步这些索引，全新安装无需执行本文件。
--
-- 变更依据（EXPLAIN 实测）：
--   1. dt_login_log   原先只有主键：WHERE uid = ? ORDER BY time DESC → type=ALL + filesort
--   2. dt_upload      原先只有主键：附件按类型+时间分页需要全表扫描
--   3. dt_balance_log 原为 user_id / create_time 两个单列索引：
--                     WHERE user_id = ? ORDER BY create_time DESC → Using filesort
--   4. dt_user        删除 (type) 单列索引：基数仅 3；角色 JOIN 实际由 dt_roles 的 uk_type 驱动，
--                     该索引未被任何现有查询使用（如需「按角色筛选账号」再按需加回）

ALTER TABLE `dt_login_log`
    ADD INDEX `idx_uid_time` (`uid`, `time`) COMMENT '按用户+时间查登录记录',
    ALGORITHM = INPLACE, LOCK = NONE;

ALTER TABLE `dt_upload`
    ADD INDEX `idx_type_time` (`type`, `time`) COMMENT '按类型+时间分页查附件',
    ALGORITHM = INPLACE, LOCK = NONE;

ALTER TABLE `dt_balance_log`
    ADD INDEX `idx_user_time` (`user_id`, `create_time`) COMMENT '用户+时间组合索引(按用户查流水并排序)',
    ALGORITHM = INPLACE, LOCK = NONE;

ALTER TABLE `dt_balance_log`
    DROP INDEX `idx_user_id`,
    ALGORITHM = INPLACE, LOCK = NONE;

ALTER TABLE `dt_user`
    DROP INDEX `idx_type`,
    ALGORITHM = INPLACE, LOCK = NONE;
