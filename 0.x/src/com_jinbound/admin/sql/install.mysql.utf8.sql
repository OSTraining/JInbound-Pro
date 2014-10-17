################################################################################
##  JInbound SQL Install                                                      ##
################################################################################;


########################################
##    Campaigns                       ##
##                                    ##
##    Campaigns are basically just    ##
##    simple tags to categorize the   ##
##    emails                          ##
########################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_campaigns` (

	`id` int(11) NOT NULL AUTO_INCREMENT
	COMMENT 'Primary Key',
	
	`asset_id` int(11) NOT NULL
	COMMENT 'Key for assets table',
	
	`name` varchar(255) NOT NULL
	COMMENT 'name of this record',
	
	`label` tinyint(1) default '0'
	COMMENT 'label type',
	
	`greedy` tinyint(1) default '0'
	COMMENT 'this campaign wants the contacts to itself',

	`conversion_url` TEXT NOT NULL default ''
	COMMENT 'query params that denote a successful conversion',
	
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



########################################
##    Emails                          ##
##                                    ##
##    Emails to be sent to the user   ##
########################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_emails` (

	`id` int(11) NOT NULL AUTO_INCREMENT
	COMMENT 'Primary Key',
	 
	`asset_id` int(11) NOT NULL
	COMMENT 'Key for assets table',
	
	`name` varchar(255) NOT NULL
	COMMENT 'name of this record',
	
	`campaign_id` int(11) NOT NULL
	COMMENT 'Primary key of associated campaign',

	`layout` varchar(1) NOT NULL DEFAULT "A"
	COMMENT 'Layout type - 0 for custom, or A-D',
	
	`fromname` varchar(255) NOT NULL,
	`fromemail` varchar(255) NOT NULL,
	`sendafter` int(4) NOT NULL,
	`subject` varchar(255) NOT NULL,
	`htmlbody` blob NOT NULL,
	`plainbody` blob NOT NULL,
	
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



########################################
##    Email Records                   ##
##                                    ##
##    Records of emails already sent  ##
########################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_emails_records` (

	`id` int(11) NOT NULL AUTO_INCREMENT
	COMMENT 'Primary Key',
	 
	`asset_id` int(11) NOT NULL
	COMMENT 'Key for assets table',

	`email_id` int(11) NOT NULL
	COMMENT 'Primary Key of associated Email',

	`version_id` int(11) NOT NULL DEFAULT 0
	COMMENT 'Primary Key of associated Email version',
	
	`lead_id` int(11) NOT NULL
	COMMENT 'Primary key of associated Lead',
	
	`sent` datetime NOT NULL default '0000-00-00 00:00:00'
	COMMENT 'when record was created, in UTC',
	
	PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;



########################################
##    Leads                           ##
##                                    ##
##    Leads are associated with       ##
##    contacts, which will be created ##
##    when saving a new lead or       ##
##    updated when this lead is       ##
########################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_leads` (

	`id` int(11) NOT NULL AUTO_INCREMENT
	COMMENT 'Primary Key',
	 
	`asset_id` int(11) NOT NULL
	COMMENT 'Key for assets table',
	
	`page_id` int(11) NOT NULL
	COMMENT 'Primary key of Page associated with this lead',
	
	`contact_id` int(11) NOT NULL
	COMMENT 'Primary key of Contact associated with this lead',
	
	`priority_id` int(11) NOT NULL
	COMMENT 'Primary key of Priority associated with this lead',
	
	`status_id` int(11) NOT NULL
	COMMENT 'Primary key of Status associated with this lead',
	
	`campaign_id` int(11) NOT NULL
	COMMENT 'Primary key of Campaign associated with this lead',
	
	`first_name` varchar(255) NOT NULL
	COMMENT 'First name of Lead (contacts uses single column for both names)',
	
	`last_name` varchar(255) NOT NULL
	COMMENT 'Last name of Lead (contacts uses single column for both names)',
	
	`ip` varchar(255) NOT NULL DEFAULT ''
	COMMENT 'IPv4/6 address of record creator',
	
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



########################################
##    Lead Statuses                   ##
##                                    ##
##    Leads are associated with       ##
##    contacts, which will be created ##
##    when saving a new lead or       ##
##    updated when this lead is       ##
########################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_lead_statuses` (

	`id` int(11) NOT NULL AUTO_INCREMENT
	COMMENT 'Primary Key',
	 
	`asset_id` int(11) NOT NULL
	COMMENT 'Key for assets table',
	
	`name` varchar(255) NOT NULL
	COMMENT 'name of this record',
	
	`description` mediumtext NOT NULL
	COMMENT 'description of this record',
	 
	`ordering` int(11) NOT NULL default '0'
	COMMENT 'Ordering column for priority level',
	
	`default` tinyint(1) default '0'
	COMMENT 'Default status',
	
	`active` tinyint(1) default '0'
	COMMENT 'Active statuses count towards leads, inactive do not',
	
	`final` tinyint(1) default '0'
	COMMENT 'Final status',
	
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



########################################
##    Pages                           ##
##                                    ##
##    Landing pages users will visit  ##
########################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_pages` (

	`id` int(11) NOT NULL AUTO_INCREMENT
	COMMENT 'Primary Key',
	 
	`asset_id` int(11) NOT NULL
	COMMENT 'Key for assets table',

	`layout` varchar(1) NOT NULL DEFAULT "A"
	COMMENT 'Layout type - 0 for custom, or A-D',
	
	`heading` varchar(255) NOT NULL,
	`subheading` varchar(255) NOT NULL,
	`socialmedia` tinyint(1) NOT NULL,
	`maintext` blob NOT NULL,
	`sidebartext` blob NOT NULL,
	`alias` varchar(255) NOT NULL,
	`name` varchar(255) NOT NULL,
	`image` varchar(255) NOT NULL,
	`imagealttext` varchar(255) NOT NULL,
	`category` varchar(255) NOT NULL,
	`metatitle` varchar(55) NOT NULL,
	`metadescription` varchar(155) NOT NULL,
	`formname` varchar(255) NOT NULL,
	`formbuilder` blob NOT NULL,
	`campaign` int(11) NOT NULL,
	`converts_on_another_form` varchar(25) NOT NULL,
	`converts_on_same_campaign` varchar(25) NOT NULL,
	`submit_text` varchar(255) NOT NULL,
	`notify_form_submits` varchar(255) NOT NULL,
	`notification_email` varchar(255) NOT NULL,
	`after_submit_sendto` varchar(20) NOT NULL,
	`menu_item` varchar(10) NOT NULL,
	`send_to_url` varchar(255) NOT NULL,
	`sendto_message` text NOT NULL,
	`template` text NOT NULL,
	
	`hits` int(11) default '0'
	COMMENT 'number of views for this record',
	
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



########################################
##    Priorities                      ##
##                                    ##
##    Allow leads to be sorted by     ##
##    custom priority                 ##
########################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_priorities` (

	`id` int(11) NOT NULL AUTO_INCREMENT
	COMMENT 'Primary Key',
	 
	`asset_id` int(11) NOT NULL
	COMMENT 'Key for assets table',
	
	`name` varchar(255) NOT NULL
	COMMENT 'name of this record',
	
	`description` mediumtext NOT NULL
	COMMENT 'description of this record',
	 
	`ordering` int(11) NOT NULL default '0'
	COMMENT 'Ordering column for priority level',
	
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



########################################
##    Stages                          ##
##                                    ##
##    Customizeable steps to show     ##
##    where leads are in the process  ##
########################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_stages` (

	`id` int(11) NOT NULL AUTO_INCREMENT
	COMMENT 'Primary Key',
	 
	`asset_id` int(11) NOT NULL
	COMMENT 'Key for assets table',
	
	`name` varchar(255) NOT NULL
	COMMENT 'name of this record',
	
	`description` mediumtext NOT NULL
	COMMENT 'description of this record',
	 
	`ordering` int(11) NOT NULL default '0'
	COMMENT 'Ordering column for stage level',
	
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;





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

########################################
##    Subscriptions                   ##
##                                    ##
##    Simple xref table to track      ##
##    if leads should be sent emails  ##
########################################;

CREATE TABLE IF NOT EXISTS #__jinbound_subscriptions (
	
	`id` int(11) NOT NULL AUTO_INCREMENT
	COMMENT 'Primary Key',
	
	`contact_id` int(11) NOT NULL
	COMMENT 'FK to #__contact_details.id',
	
	`enabled` int(1) NOT NULL DEFAULT 1
	COMMENT 'If 0 emails will not be sent',
	
	PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


########################################
##    Email Versions                  ##
##                                    ##
##    stores copies of previous       ##
##    versions of emails for accurate ##
##    recordkeeping                   ##
########################################;

CREATE TABLE IF NOT EXISTS #__jinbound_emails_versions (
	
	`id` int(11) NOT NULL AUTO_INCREMENT
	COMMENT 'Primary Key',
	
	`email_id` int(11) NOT NULL
	COMMENT 'FK to #__jinbound_emails.id',
	
	`subject` varchar(255),
	`htmlbody` blob,
	`plainbody` blob,
	
	PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;




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
	
	`asset_id` int(11) NOT NULL
	COMMENT 'FK to #__assets table',
	
	`cookie` VARCHAR(255)
	COMMENT 'Cookie value',
	
	`alias` varchar(255) NOT NULL
	COMMENT 'Slug for URL',
	
	`first_name` varchar(255) NOT NULL
	COMMENT 'First name of Contact',
	
	`last_name` varchar(255) NOT NULL
	COMMENT 'Last name of Contact',
	
	`company` varchar(255) NOT NULL DEFAULT ''
	COMMENT 'Company name of Contact',
	
	`website` varchar(255) NOT NULL DEFAULT ''
	COMMENT 'Company name of Contact',
	
	`email` varchar(255) NOT NULL
	COMMENT 'Email of Contact',
	
	`address` TEXT NOT NULL DEFAULT ''
	COMMENT 'Address of Contact',
	
	`suburb` varchar(100) NOT NULL DEFAULT ''
	COMMENT 'City/suburb of Contact',
	
	`state` varchar(100) NOT NULL DEFAULT ''
	COMMENT 'State of Contact',
	
	`country` varchar(100) NOT NULL DEFAULT ''
	COMMENT 'Country of Contact',
	
	`postcode` varchar(100) NOT NULL DEFAULT ''
	COMMENT 'Postal code of Contact',
	
	`telephone` varchar(255) NOT NULL DEFAULT ''
	COMMENT 'Telephone number of Contact',
	
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
	
	`page_id` int(11) NOT NULL
	COMMENT 'FK to #__jinbound_pages table',
	
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

#####################################################
## Associates contacts to campaigns
#####################################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_contacts_campaigns` (
	
	`contact_id` int(11) NOT NULL DEFAULT 0
	COMMENT 'FK to #__jinbound_contacts table',
	
	`campaign_id` int(11) NOT NULL DEFAULT 0
	COMMENT 'FK to #__jinbound_campaigns table',
	
	`enabled` tinyint(1) NOT NULL DEFAULT 1
	COMMENT 'enabled status, 1 = enabled, 0 = disabled'
	
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


#####################################################
## Tracks contact statuses over time
#####################################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_contacts_statuses` (
	
	`status_id` int(11) NOT NULL
	COMMENT 'FK to #__jinbound_lead_statuses table',
	
	`campaign_id` int(11) NOT NULL
	COMMENT 'FK to #__jinbound_campaigns table',
	
	`contact_id` int(11) NOT NULL
	COMMENT 'FK to #__jinbound_contacts table',
	
	`created` datetime NOT NULL default '0000-00-00 00:00:00'
	COMMENT 'when record was created, in UTC',	
	 
	`created_by` int(11) NOT NULL default '0'
	COMMENT 'User id of record creator',
	
	`modified` datetime NOT NULL default '0000-00-00 00:00:00'
	COMMENT 'when record was last modified in UTC',	
	 
	`modified_by` int(11) NOT NULL default '0'
	COMMENT 'User id of last modifier'
	
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



#####################################################
## Tracks contact priorities over time
#####################################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_contacts_priorities` (
	
	`priority_id` int(11) NOT NULL
	COMMENT 'FK to #__jinbound_lead_statuses table',
	
	`campaign_id` int(11) NOT NULL
	COMMENT 'FK to #__jinbound_campaigns table',
	
	`contact_id` int(11) NOT NULL
	COMMENT 'FK to #__jinbound_contacts table',
	
	`created` datetime NOT NULL default '0000-00-00 00:00:00'
	COMMENT 'when record was created, in UTC',
	 
	`created_by` int(11) NOT NULL default '0'
	COMMENT 'User id of record creator',
	
	`modified` datetime NOT NULL default '0000-00-00 00:00:00'
	COMMENT 'when record was last modified in UTC',	
	 
	`modified_by` int(11) NOT NULL default '0'
	COMMENT 'User id of last modifier'
	
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


#####################################################
## reports email records
#####################################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_reports_emails` (
	
	`email` TEXT NOT NULL
	COMMENT 'Email the reports were sent to',
	
	`created` datetime NOT NULL default '0000-00-00 00:00:00'
	COMMENT 'when record was created, in UTC'
	
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


#####################################################
## pages hits records
#####################################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_landing_pages_hits` (
	
	`day` DATE NOT NULL,
	`page_id` INT(11) NOT NULL DEFAULT 0,
	`hits` INT(11) NOT NULL DEFAULT 0,
	PRIMARY KEY (`day`, `page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
