#############################################
##               Forms Table               ##
#############################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_forms` (

	`id` int(11)	NOT NULL auto_increment
	COMMENT 'Primary Key',
	
	`asset_id` int(10) unsigned NOT NULL DEFAULT '0'
	COMMENT 'FK to the #__assets table.', 
	 
	`title` varchar(255) NOT NULL default ''
	COMMENT 'Form Title',
	
	`created` datetime NOT NULL default '0000-00-00 00:00:00'
	COMMENT 'when form was created',	
	 
	`created_by` int(11) NOT NULL default '0'
	COMMENT 'User id of form creator',
	
	`modified` datetime NOT NULL default '0000-00-00 00:00:00'
	COMMENT 'when form was last modified',	
	 
	`modified_by` int(11) NOT NULL default '0'
	COMMENT 'User id of last modifier',
	 
	`published` tinyint(1) default '0'
	COMMENT 'Publication status',
	
	`checked_out` int(11) unsigned NOT NULL default '0'
	COMMENT 'Locking column to prevent simultaneous updates',
	
	`checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00'
	COMMENT 'Date and Time form was checked out',
	 
	`default` tinyint(1) NOT NULL default '0'
	COMMENT 'Determines if this is the default custom form',
	 
	PRIMARY KEY (id)
) ENGINE=MyISAM;



#############################################
##               Fields Table              ##
#############################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_fields` (

	`id` int(11)	NOT NULL auto_increment
	COMMENT 'Primary Key',
	
	`asset_id` int(10) unsigned NOT NULL DEFAULT '0'
	COMMENT 'FK to the #__assets table.',
	 
	`name` varchar(255) NOT NULL default ''
	COMMENT 'Field name attribute',
	 
	`title` varchar(255) NOT NULL default ''
	COMMENT 'Field Title (and label)',
	 
	`type` varchar(255) NOT NULL default 'text'
	COMMENT 'Type of field, which must match a corresponding class/trigger',
	 
	`description` text NOT NULL default ''
	COMMENT 'Description of field displayed to the end user',
	
	`default` varchar(255) NOT NULL default ''
	COMMENT 'Default value of the field',
	
	`params` text NOT NULL default ''
	COMMENT 'Various parameters for field - options, html attributes, etc',
	
	`created` datetime NOT NULL default '0000-00-00 00:00:00'
	COMMENT 'when field was created',	
	 
	`created_by` int(11) NOT NULL default '0'
	COMMENT 'User id of field creator',
	
	`modified` datetime NOT NULL default '0000-00-00 00:00:00'
	COMMENT 'when field was last modified',	
	 
	`modified_by` int(11) NOT NULL default '0'
	COMMENT 'User id of last modifier',
	 
	`published` tinyint(1) default '0'
	COMMENT 'Publication status',
	
	`checked_out` int(11) unsigned NOT NULL default '0'
	COMMENT 'Locking column to prevent simultaneous updates',
	
	`checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00'
	COMMENT 'when field was checked out',
	 
	PRIMARY KEY (id)
) ENGINE=MyISAM;



#############################################
##         Form Fields Xref Table          ##
#############################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_form_fields` (

	`form_id` int(11) NOT NULL
	COMMENT 'Primary key of form',
	
	`field_id` int(11) NOT NULL
	COMMENT 'Primary key of field',
	
	`ordering` int(11) NOT NULL DEFAULT 0
	COMMENT 'ordering of fields'
	
) ENGINE=MyISAM;
