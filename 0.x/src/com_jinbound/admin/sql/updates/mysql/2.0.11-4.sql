/* using comments to bypass limitations in JDatabaseDriver::splitSql - do not remove! */

DROP PROCEDURE IF EXISTS fix_jinbound_report_emails ;
CREATE PROCEDURE fix_jinbound_report_emails()
BEGIN
	DECLARE CONTINUE HANDLER FOR 1060 BEGIN END /*"*/;/*"*/
	
	ALTER TABLE #__jinbound_emails ADD
		`params` BLOB NOT NULL
		AFTER `plainbody`
	/*"*/;/*"*/

	ALTER TABLE #__jinbound_reports_emails ADD
		`email_id` INT(11) NOT NULL DEFAULT 0
		AFTER `email`
	/*"*/;/*"*/
END
;
CALL fix_jinbound_report_emails();
DROP PROCEDURE IF EXISTS fix_jinbound_report_emails ;

