#############################################
##               Forms Table               ##
#############################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_forms` (

  `id`               INT(11)          NOT NULL AUTO_INCREMENT
  COMMENT 'Primary Key',

  `asset_id`         INT(10) UNSIGNED NOT NULL DEFAULT '0'
  COMMENT 'FK to the #__assets table.',

  `title`            VARCHAR(255)     NOT NULL DEFAULT ''
  COMMENT 'Form Title',

  `created`          DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'when form was created',

  `created_by`       INT(11)          NOT NULL DEFAULT '0'
  COMMENT 'User id of form creator',

  `modified`         DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'when form was last modified',

  `modified_by`      INT(11)          NOT NULL DEFAULT '0'
  COMMENT 'User id of last modifier',

  `published`        TINYINT(1)                DEFAULT '0'
  COMMENT 'Publication status',

  `checked_out`      INT(11) UNSIGNED NOT NULL DEFAULT '0'
  COMMENT 'Locking column to prevent simultaneous updates',

  `checked_out_time` DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'Date and Time form was checked out',

  `default`          TINYINT(1)       NOT NULL DEFAULT '0'
  COMMENT 'Determines if this is the default custom form',

  PRIMARY KEY (id)
)
  ENGINE = MyISAM;

#############################################
##               Fields Table              ##
#############################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_fields` (

  `id`               INT(11)          NOT NULL AUTO_INCREMENT
  COMMENT 'Primary Key',

  `asset_id`         INT(10) UNSIGNED NOT NULL DEFAULT '0'
  COMMENT 'FK to the #__assets table.',

  `name`             VARCHAR(255)     NOT NULL DEFAULT ''
  COMMENT 'Field name attribute',

  `title`            VARCHAR(255)     NOT NULL DEFAULT ''
  COMMENT 'Field Title (and label)',

  `type`             VARCHAR(255)     NOT NULL DEFAULT 'text'
  COMMENT 'Type of field, which must match a corresponding class/trigger',

  `description`      TEXT             NOT NULL DEFAULT ''
  COMMENT 'Description of field displayed to the end user',

  `default`          VARCHAR(255)     NOT NULL DEFAULT ''
  COMMENT 'Default value of the field',

  `params`           TEXT             NOT NULL DEFAULT ''
  COMMENT 'Various parameters for field - options, html attributes, etc',

  `created`          DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'when field was created',

  `created_by`       INT(11)          NOT NULL DEFAULT '0'
  COMMENT 'User id of field creator',

  `modified`         DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'when field was last modified',

  `modified_by`      INT(11)          NOT NULL DEFAULT '0'
  COMMENT 'User id of last modifier',

  `published`        TINYINT(1)                DEFAULT '0'
  COMMENT 'Publication status',

  `checked_out`      INT(11) UNSIGNED NOT NULL DEFAULT '0'
  COMMENT 'Locking column to prevent simultaneous updates',

  `checked_out_time` DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'when field was checked out',

  PRIMARY KEY (id)
)
  ENGINE = MyISAM;

#############################################
##         Form Fields Xref Table          ##
#############################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_form_fields` (

  `form_id`  INT(11) NOT NULL
  COMMENT 'Primary key of form',

  `field_id` INT(11) NOT NULL
  COMMENT 'Primary key of field',

  `ordering` INT(11) NOT NULL DEFAULT 0
  COMMENT 'ordering of fields'

)
  ENGINE = MyISAM;
