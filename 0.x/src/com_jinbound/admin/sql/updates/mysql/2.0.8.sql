/* using comments to bypass limitations in JDatabaseDriver::splitSql - do not remove! */

DROP PROCEDURE IF EXISTS add_jinbound_campaign_params ;
CREATE PROCEDURE add_jinbound_campaign_params()
BEGIN
	DECLARE CONTINUE HANDLER FOR 1060 BEGIN END /*"*/;/*"*/
	
	ALTER TABLE #__jinbound_campaigns ADD
		`params` TEXT COMMENT 'JSON encoded custom params'
	/*"*/;/*"*/
END
;
CALL add_jinbound_campaign_params();
DROP PROCEDURE IF EXISTS add_jinbound_campaign_params ;

