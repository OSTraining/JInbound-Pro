/* using comments to bypass limitations in JDatabaseDriver::splitSql - do not remove! */

DROP PROCEDURE IF EXISTS add_jinbound_email_type ;
CREATE PROCEDURE add_jinbound_email_type()
BEGIN
	DECLARE CONTINUE HANDLER FOR 1060 BEGIN END /*"*/;/*"*/
	
	ALTER TABLE #__jinbound_emails ADD
		`type` VARCHAR(255) NOT NULL DEFAULT 'campaign'
		AFTER `name`
	/*"*/;/*"*/
END
;
CALL add_jinbound_email_type();
DROP PROCEDURE IF EXISTS add_jinbound_email_type ;

