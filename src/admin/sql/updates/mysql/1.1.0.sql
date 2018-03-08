## updated database structure ;

#####################################################
## Contacts, replaces core integration and contains entries for guests based on tracking
#####################################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_contacts` (

  `id`               INT(11)          NOT NULL AUTO_INCREMENT
  COMMENT 'Primary Key',

  `user_id`          INT(11)          NOT NULL DEFAULT 0
  COMMENT 'FK to #__users table',

  `core_contact_id`  INT(11)          NOT NULL DEFAULT 0
  COMMENT 'FK to #__contact_details table',

  `asset_id`         INT(11)          NOT NULL
  COMMENT 'FK to #__assets table',

  `cookie`           VARCHAR(255)
  COMMENT 'Cookie value',

  `alias`            VARCHAR(255)     NOT NULL
  COMMENT 'Slug for URL',

  `first_name`       VARCHAR(255)     NOT NULL
  COMMENT 'First name of Contact',

  `last_name`        VARCHAR(255)     NOT NULL
  COMMENT 'Last name of Contact',

  `company`          VARCHAR(255)     NOT NULL DEFAULT ''
  COMMENT 'Company name of Contact',

  `website`          VARCHAR(255)     NOT NULL DEFAULT ''
  COMMENT 'Company name of Contact',

  `email`            VARCHAR(255)     NOT NULL
  COMMENT 'Email of Contact',

  `address`          TEXT             NOT NULL DEFAULT ''
  COMMENT 'Address of Contact',

  `suburb`           VARCHAR(100)     NOT NULL DEFAULT ''
  COMMENT 'City/suburb of Contact',

  `state`            VARCHAR(100)     NOT NULL DEFAULT ''
  COMMENT 'State of Contact',

  `country`          VARCHAR(100)     NOT NULL DEFAULT ''
  COMMENT 'Country of Contact',

  `postcode`         VARCHAR(100)     NOT NULL DEFAULT ''
  COMMENT 'Postal code of Contact',

  `telephone`        VARCHAR(255)     NOT NULL DEFAULT ''
  COMMENT 'Telephone number of Contact',

  `published`        TINYINT(1)                DEFAULT '0'
  COMMENT 'publication status of record - 0 is Unpublished, 1 is Published, -2 is Trashed',

  `created`          DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'when record was created, in UTC',

  `created_by`       INT(11)          NOT NULL DEFAULT '0'
  COMMENT 'User id of record creator',

  `modified`         DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'when record was last modified in UTC',

  `modified_by`      INT(11)          NOT NULL DEFAULT '0'
  COMMENT 'User id of last modifier',

  `checked_out`      INT(11) UNSIGNED NOT NULL DEFAULT '0'
  COMMENT 'Locking column to prevent simultaneous updates',

  `checked_out_time` DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'Date and Time record was checked out',

  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

#####################################################
## Associates tracks to contacts
#####################################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_users_tracks` (

  `user_id` INT(11) NOT NULL DEFAULT 0
  COMMENT 'FK to #__users table',

  `cookie`  VARCHAR(255)
  COMMENT 'Cookie value'

)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

#####################################################
## Tracks, details about where a user has been
#####################################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_tracks` (

  `id`               VARCHAR(128) NOT NULL
  COMMENT 'Primary Key, microtime + ip + md5(session id)',

  `cookie`           VARCHAR(255)
  COMMENT 'Cookie value for this track',

  `detected_user_id` INT(11)      NOT NULL DEFAULT 0
  COMMENT 'FK to #__users table',

  `current_user_id`  INT(11)      NOT NULL DEFAULT 0
  COMMENT 'FK to #__users table',

  `user_agent`       TEXT
  COMMENT 'Browser User Agent',

  `created`          DATETIME     NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'Date this request was made',

  `ip`               VARCHAR(64)
  COMMENT 'IP address as determined by PHP, either ipv4 or ipv6',

  `session_id`       VARCHAR(255)
  COMMENT 'Session ID as determined by PHP',

  `type`             ENUM ('GET', 'POST', 'HEAD', 'PUT')
  COMMENT 'Request type',

  `url`              TEXT
  COMMENT 'URL of the request',

  PRIMARY KEY (`id`(128))
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

#####################################################
## Conversions table, replaces former "leads" table
#####################################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_conversions` (

  `id`               INT(11)          NOT NULL AUTO_INCREMENT
  COMMENT 'Primary Key',

  `page_id`          INT(11)          NOT NULL
  COMMENT 'FK to #__jinbound_pages table',

  `contact_id`       INT(11)          NOT NULL
  COMMENT 'FK to #__jinbound_contacts table',

  `published`        TINYINT(1)                DEFAULT '0'
  COMMENT 'publication status of record - 0 is Unpublished, 1 is Published, -2 is Trashed',

  `created`          DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'when record was created, in UTC',

  `created_by`       INT(11)          NOT NULL DEFAULT '0'
  COMMENT 'User id of record creator',

  `modified`         DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'when record was last modified in UTC',

  `modified_by`      INT(11)          NOT NULL DEFAULT '0'
  COMMENT 'User id of last modifier',

  `checked_out`      INT(11) UNSIGNED NOT NULL DEFAULT '0'
  COMMENT 'Locking column to prevent simultaneous updates',

  `checked_out_time` DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'Date and Time record was checked out',

  `formdata`         TEXT
  COMMENT 'JSON encoded form data',

  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

#####################################################
## Associates contacts to campaigns
#####################################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_contacts_campaigns` (

  `contact_id`  INT(11)    NOT NULL DEFAULT 0
  COMMENT 'FK to #__jinbound_contacts table',

  `campaign_id` INT(11)    NOT NULL DEFAULT 0
  COMMENT 'FK to #__jinbound_campaigns table',

  `enabled`     TINYINT(1) NOT NULL DEFAULT 1
  COMMENT 'enabled status, 1 = enabled, 0 = disabled'

)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

#####################################################
## Tracks contact statuses over time
#####################################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_contacts_statuses` (

  `status_id`   INT(11)  NOT NULL
  COMMENT 'FK to #__jinbound_lead_statuses table',

  `campaign_id` INT(11)  NOT NULL
  COMMENT 'FK to #__jinbound_campaigns table',

  `contact_id`  INT(11)  NOT NULL
  COMMENT 'FK to #__jinbound_contacts table',

  `created`     DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'when record was created, in UTC',

  `created_by`  INT(11)  NOT NULL DEFAULT '0'
  COMMENT 'User id of record creator',

  `modified`    DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'when record was last modified in UTC',

  `modified_by` INT(11)  NOT NULL DEFAULT '0'
  COMMENT 'User id of last modifier'

)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

#####################################################
## Tracks contact priorities over time
#####################################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_contacts_priorities` (

  `priority_id` INT(11)  NOT NULL
  COMMENT 'FK to #__jinbound_lead_statuses table',

  `campaign_id` INT(11)  NOT NULL
  COMMENT 'FK to #__jinbound_campaigns table',

  `contact_id`  INT(11)  NOT NULL
  COMMENT 'FK to #__jinbound_contacts table',

  `created`     DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'when record was created, in UTC',

  `created_by`  INT(11)  NOT NULL DEFAULT '0'
  COMMENT 'User id of record creator',

  `modified`    DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'when record was last modified in UTC',

  `modified_by` INT(11)  NOT NULL DEFAULT '0'
  COMMENT 'User id of last modifier'

)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

#####################################################
## reports email records
#####################################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_reports_emails` (

  `email`   TEXT     NOT NULL
  COMMENT 'Email the reports were sent to',

  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'when record was created, in UTC'

)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

