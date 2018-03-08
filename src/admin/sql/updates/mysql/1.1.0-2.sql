/* using comments to bypass limitations in JDatabaseDriver::splitSql - do not remove! */

ALTER TABLE #__jinbound_contacts_campaigns ADD
`enabled` TINYINT (1) NOT NULL DEFAULT 1
COMMENT 'enabled status, 1 = enabled, 0 = disabled'
AFTER `campaign_id`
/*"*/;
/*"*/
