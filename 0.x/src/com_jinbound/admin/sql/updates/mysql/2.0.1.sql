/* using comments to bypass limitations in JDatabaseDriver::splitSql - do not remove! */

DROP PROCEDURE IF EXISTS add_jinbound_campaign_url ;
CREATE PROCEDURE add_jinbound_campaign_url()
BEGIN
	DECLARE CONTINUE HANDLER FOR 1060 BEGIN END /*"*/;/*"*/
	
	ALTER TABLE #__jinbound_campaigns ADD
		`conversion_url` TEXT NOT NULL default ''
		COMMENT 'query params that denote a successful conversion'
		AFTER `greedy`
	/*"*/;/*"*/
END
;
CALL add_jinbound_campaign_url();
DROP PROCEDURE IF EXISTS add_jinbound_campaign_url ;

