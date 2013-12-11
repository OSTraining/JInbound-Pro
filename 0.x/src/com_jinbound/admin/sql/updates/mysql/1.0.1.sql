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
