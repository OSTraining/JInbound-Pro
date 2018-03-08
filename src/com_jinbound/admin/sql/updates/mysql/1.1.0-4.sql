
#####################################################
## pages hits records
#####################################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_landing_pages_hits` (
	
	`day` DATE NOT NULL,
	`page_id` INT(11) NOT NULL DEFAULT 0,
	`hits` INT(11) NOT NULL DEFAULT 0,
	PRIMARY KEY (`day`, `page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
