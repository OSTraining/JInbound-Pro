



########################################
##    Notes                           ##
##                                    ##
##    Lead notes                      ##
########################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_notes` (
	
	`id` int(11) NOT NULL AUTO_INCREMENT
	COMMENT 'Primary Key',
	 
	`asset_id` int(11) NOT NULL
	COMMENT 'Key for assets table',
	 
	`lead_id` int(11) NOT NULL
	COMMENT 'Key for leads table',
	
	`text` mediumtext NOT NULL
	COMMENT 'note text',
	
	`published` tinyint(1) default '1'
	COMMENT 'publication status of record - 0 is Unpublished, 1 is Published, -2 is Trashed',
	
	`created` datetime NOT NULL default '0000-00-00 00:00:00'
	COMMENT 'when record was created, in UTC',	
	 
	`created_by` int(11) NOT NULL default '0'
	COMMENT 'User id of record creator',
	
	`modified` datetime NOT NULL default '0000-00-00 00:00:00'
	COMMENT 'when record was last modified in UTC',	
	 
	`modified_by` int(11) NOT NULL default '0'
	COMMENT 'User id of last modifier',
	
	`checked_out` int(11) unsigned NOT NULL default '0'
	COMMENT 'Locking column to prevent simultaneous updates',
	
	`checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00'
	COMMENT 'Date and Time record was checked out',
	
	PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

