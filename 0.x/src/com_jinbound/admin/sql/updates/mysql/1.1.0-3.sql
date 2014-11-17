/* using comments to bypass limitations in JDatabaseDriver::splitSql - do not remove! */

DROP PROCEDURE IF EXISTS add_jinbound_greedy_campaign ;
CREATE PROCEDURE add_jinbound_greedy_campaign()
BEGIN
	DECLARE CONTINUE HANDLER FOR 1060 BEGIN END /*"*/;/*"*/
	
	ALTER TABLE #__jinbound_campaigns ADD
		`greedy` tinyint(1) NOT NULL default 0
		COMMENT 'this campaign wants the contacts to itself'
		AFTER `label`
	/*"*/;/*"*/
END
;
CALL add_jinbound_greedy_campaign();
DROP PROCEDURE IF EXISTS add_jinbound_greedy_campaign ;
