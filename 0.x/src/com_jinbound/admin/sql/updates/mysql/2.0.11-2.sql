/* using comments to bypass limitations in JDatabaseDriver::splitSql - do not remove! */

DROP PROCEDURE IF EXISTS add_jinbound_page_formid ;
CREATE PROCEDURE add_jinbound_page_formid()
BEGIN
	DECLARE CONTINUE HANDLER FOR 1060 BEGIN END /*"*/;/*"*/
	
	ALTER TABLE #__jinbound_pages ADD
		`formid` INT(11)NOT NULL DEFAULT 0
		AFTER `metadescription`
	/*"*/;/*"*/
END
;
CALL add_jinbound_page_formid();
DROP PROCEDURE IF EXISTS add_jinbound_page_formid ;

