/* using comments to bypass limitations in JDatabaseDriver::splitSql - do not remove! */
	
	ALTER TABLE #__jinbound_emails ADD
		`params` BLOB NOT NULL
		AFTER `plainbody`
	/*"*/;/*"*/

	ALTER TABLE #__jinbound_reports_emails ADD
		`email_id` INT(11) NOT NULL DEFAULT 0
		AFTER `email`
	/*"*/;/*"*/

