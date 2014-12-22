/* using comments to bypass limitations in JDatabaseDriver::splitSql - do not remove! */

DROP PROCEDURE IF EXISTS add_jinbound_page_ga ;
CREATE PROCEDURE add_jinbound_page_ga()
BEGIN
	DECLARE CONTINUE HANDLER FOR 1060 BEGIN END /*"*/;/*"*/
	
	ALTER TABLE #__jinbound_pages ADD
		`ga` TINYINT(1) NOT NULL DEFAULT 0
		AFTER `css`
	/*"*/;/*"*/
	
	ALTER TABLE #__jinbound_pages ADD
		`ga_code` VARCHAR(255) NOT NULL DEFAULT ''
		AFTER `ga`
	/*"*/;/*"*/
END
;
CALL add_jinbound_page_ga();
DROP PROCEDURE IF EXISTS add_jinbound_page_ga ;

