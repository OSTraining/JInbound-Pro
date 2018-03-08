/* using comments to bypass limitations in JDatabaseDriver::splitSql - do not remove! */	

	ALTER TABLE #__jinbound_pages ADD
		`image_size_small_height` varchar(50) NOT NULL DEFAULT ''
		AFTER `imagealttext`
	/*"*/;/*"*/
	
	ALTER TABLE #__jinbound_pages ADD
		`image_size_small_width` varchar(50) NOT NULL DEFAULT ''
		AFTER `imagealttext`
	/*"*/;/*"*/
	
	ALTER TABLE #__jinbound_pages ADD
		`image_size_medium_height` varchar(50) NOT NULL DEFAULT ''
		AFTER `imagealttext`
	/*"*/;/*"*/
	
	ALTER TABLE #__jinbound_pages ADD
		`image_size_medium_width` varchar(50) NOT NULL DEFAULT ''
		AFTER `imagealttext`
	/*"*/;/*"*/
	
	ALTER TABLE #__jinbound_pages ADD
		`image_size_large_height` varchar(50) NOT NULL DEFAULT ''
		AFTER `imagealttext`
	/*"*/;/*"*/
	
	ALTER TABLE #__jinbound_pages ADD
		`image_size_large_width` varchar(50) NOT NULL DEFAULT ''
		AFTER `imagealttext`
	/*"*/;/*"*/

