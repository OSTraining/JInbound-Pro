## updated database structure ;

#####################################################
## Contacts, replaces core integration and contains entries for guests based on tracking
#####################################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_contacts` (

	`id` int(11) NOT NULL AUTO_INCREMENT
	COMMENT 'Primary Key',
	
	`user_id` int(11) NOT NULL DEFAULT 0
	COMMENT 'FK to #__users table',
	
	`core_contact_id` int(11) NOT NULL DEFAULT 0
	COMMENT 'FK to #__contact_details table',
	
	`alias` varchar(255) NOT NULL
	COMMENT 'Slug for URL',
	
	`first_name` varchar(255) NOT NULL
	COMMENT 'First name of Lead',
	
	`last_name` varchar(255) NOT NULL
	COMMENT 'Last name of Lead',
	
	`email` varchar(255) NOT NULL
	COMMENT 'Email of Lead',
	
	`address` TEXT NOT NULL DEFAULT ''
	COMMENT 'Address of Lead',
	
	`suburb` varchar(100) NOT NULL DEFAULT ''
	COMMENT 'City/suburb of Lead',
	
	`state` varchar(100) NOT NULL DEFAULT ''
	COMMENT 'State of Lead',
	
	`country` varchar(100) NOT NULL DEFAULT ''
	COMMENT 'Country of Lead',
	
	`postcode` varchar(100) NOT NULL DEFAULT ''
	COMMENT 'Postal code of Lead',
	
	`telephone` varchar(255) NOT NULL DEFAULT ''
	COMMENT 'Telephone number of Lead',
	
	`published` tinyint(1) default '0'
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

#####################################################
## Associates tracks to contacts
#####################################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_users_tracks` (
	
	`user_id` int(11) NOT NULL DEFAULT 0
	COMMENT 'FK to #__users table',
	
	`cookie` VARCHAR(255)
	COMMENT 'Cookie value'
	
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

#####################################################
## Tracks, details about where a user has been
#####################################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_tracks` (

	`id` VARCHAR(128) NOT NULL
	COMMENT 'Primary Key, microtime + ip + md5(session id)',
	
	`cookie` VARCHAR(255)
	COMMENT 'Cookie value for this track',
	
	`detected_user_id` int(11) NOT NULL DEFAULT 0
	COMMENT 'FK to #__users table',
	
	`current_user_id` int(11) NOT NULL DEFAULT 0
	COMMENT 'FK to #__users table',
	
	`user_agent` TEXT
	COMMENT 'Browser User Agent',
	
	`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'
	COMMENT 'Date this request was made',
	
	`ip` VARCHAR(64)
	COMMENT 'IP address as determined by PHP, either ipv4 or ipv6',
	
	`session_id` VARCHAR(255)
	COMMENT 'Session ID as determined by PHP',
	
	`type` ENUM('GET', 'POST', 'HEAD', 'PUT')
	COMMENT 'Request type',
	
	`url` TEXT
	COMMENT 'URL of the request',
	
	PRIMARY KEY (`id`(128))
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


#####################################################
## Conversions table, replaces former "leads" table
#####################################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_conversions` (

	`id` int(11) NOT NULL AUTO_INCREMENT
	COMMENT 'Primary Key',
	
	`contact_id` int(11) NOT NULL
	COMMENT 'FK to #__jinbound_contacts table',
	
	`published` tinyint(1) default '0'
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
	
	`formdata` TEXT
	COMMENT 'JSON encoded form data',
	
	PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;