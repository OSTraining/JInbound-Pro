########################################
##    Notes                           ##
##                                    ##
##    Lead notes                      ##
########################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_notes` (

  `id`               INT(11)          NOT NULL AUTO_INCREMENT
  COMMENT 'Primary Key',

  `asset_id`         INT(11)          NOT NULL
  COMMENT 'Key for assets table',

  `lead_id`          INT(11)          NOT NULL
  COMMENT 'Key for leads table',

  `text`             MEDIUMTEXT       NOT NULL
  COMMENT 'note text',

  `published`        TINYINT(1)                DEFAULT '1'
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

