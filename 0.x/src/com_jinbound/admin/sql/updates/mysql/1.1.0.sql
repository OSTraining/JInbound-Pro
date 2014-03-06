## updated database structure ;

#####################################################
## Contacts, replaces core integration and contains entries for guests based on tracking
#####################################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_contacts` (

	`id` int(11) NOT NULL AUTO_INCREMENT
	COMMENT 'Primary Key',
	
	`user_id` int(11) NOT NULL DEFAULT 0
	COMMENT 'FK to #__users table',
	
	`first_name` varchar(255) NOT NULL
	COMMENT 'First name of Lead (contacts uses single column for both names)',
	
	`last_name` varchar(255) NOT NULL
	COMMENT 'Last name of Lead (contacts uses single column for both names)',
	
	PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


#####################################################
## Tracks, details about where a user has been
#####################################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_tracks` (

	`id` VARCHAR(128) NOT NULL
	COMMENT 'Primary Key, microtime + ip + md5(session id)',
	
	`contact_id` int(11) NOT NULL
	COMMENT 'FK to #__jinbound_contacts table',
	
	`cookie_id` VARCHAR(255)
	COMMENT 'Cookie ID for this track',
	
	`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'
	COMMENT 'Date this request was made',
	
	`ip` VARCHAR(64)
	COMMENT 'IP address as determined by PHP, either ipv4 or ipv6',
	
	`session_id` VARCHAR(255)
	COMMENT 'Session ID as determined by PHP',
	
	`type` ENUM('GET', 'POST', 'HEAD', 'PUT')
	COMMENT 'Request type',
	
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
	
	PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;