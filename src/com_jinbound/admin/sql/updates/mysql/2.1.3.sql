#############################################
##       Contact Follower Xref Table       ##
#############################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_contacts_followers` (

  `contact_id`  INT(11) NOT NULL
  COMMENT 'Primary key of contact',

  `follower_id` INT(11) NOT NULL
  COMMENT 'Primary key of user',

  UNIQUE KEY `idx_contact_follower` (`contact_id`, `follower_id`)

)
  ENGINE = MyISAM;

