/* using comments to bypass limitations in JDatabaseDriver::splitSql - do not remove! */

DROP PROCEDURE IF EXISTS add_jinbound_page_css ;
CREATE PROCEDURE add_jinbound_page_css()
BEGIN
	DECLARE CONTINUE HANDLER FOR 1060 BEGIN END /*"*/;/*"*/
	
	ALTER TABLE #__jinbound_pages ADD
		`css` TEXT NOT NULL default ''
		AFTER `template`
	/*"*/;/*"*/
END
;
CALL add_jinbound_page_css();
DROP PROCEDURE IF EXISTS add_jinbound_page_css ;

