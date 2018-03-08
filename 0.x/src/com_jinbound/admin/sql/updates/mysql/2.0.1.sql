/* using comments to bypass limitations in JDatabaseDriver::splitSql - do not remove! */

	ALTER TABLE #__jinbound_campaigns ADD
		`conversion_url` TEXT NOT NULL default ''
		COMMENT 'query params that denote a successful conversion'
		AFTER `greedy`
	/*"*/;/*"*/
