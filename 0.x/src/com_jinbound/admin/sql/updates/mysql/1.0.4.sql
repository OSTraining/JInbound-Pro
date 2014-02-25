########################################
##    Email Version Number            ##
##                                    ##
########################################;

ALTER TABLE #__jinbound_emails_records ADD COLUMN `version_id` INT(11) NOT NULL DEFAULT 0 AFTER email_id;


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
