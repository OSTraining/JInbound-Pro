/* using comments to bypass limitations in JDatabaseDriver::splitSql - do not remove! */

DROP PROCEDURE IF EXISTS add_jinbound_enabled_column ;
CREATE PROCEDURE add_jinbound_enabled_column()
BEGIN
	DECLARE CONTINUE HANDLER FOR 1060 BEGIN END /*"*/;/*"*/
	
	ALTER TABLE #__jinbound_contacts_campaigns ADD
		`enabled` tinyint(1) NOT NULL default 1
		COMMENT 'enabled status, 1 = enabled, 0 = disabled'
		AFTER `campaign_id`
	/*"*/;/*"*/
END
;
CALL add_jinbound_enabled_column();
DROP PROCEDURE IF EXISTS add_jinbound_enabled_column ;
