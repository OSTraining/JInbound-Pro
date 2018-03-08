/* using comments to bypass limitations in JDatabaseDriver::splitSql - do not remove! */
	
	ALTER TABLE #__jinbound_campaigns ADD
		`greedy` tinyint(1) NOT NULL default 0
		COMMENT 'this campaign wants the contacts to itself'
		AFTER `label`
	/*"*/;/*"*/
