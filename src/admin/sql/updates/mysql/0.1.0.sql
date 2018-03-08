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

  `id`               INT(11)          NOT NULL AUTO_INCREMENT
  COMMENT 'Primary Key',

  `asset_id`         INT(11)          NOT NULL
  COMMENT 'Key for assets table',

  `name`             VARCHAR(255)     NOT NULL
  COMMENT 'name of this record',

  `label`            TINYINT(1)                DEFAULT '0'
  COMMENT 'label type',

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

########################################
##    Emails                          ##
##                                    ##
##    Emails to be sent to the user   ##
########################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_emails` (

  `id`               INT(11)          NOT NULL AUTO_INCREMENT
  COMMENT 'Primary Key',

  `asset_id`         INT(11)          NOT NULL
  COMMENT 'Key for assets table',

  `name`             VARCHAR(255)     NOT NULL
  COMMENT 'name of this record',

  `campaign_id`      INT(11)          NOT NULL
  COMMENT 'Primary key of associated campaign',

  `fromname`         VARCHAR(255)     NOT NULL,
  `fromemail`        VARCHAR(255)     NOT NULL,
  `sendafter`        INT(4)           NOT NULL,
  `subject`          VARCHAR(255)     NOT NULL,
  `htmlbody`         BLOB             NOT NULL,
  `plainbody`        BLOB             NOT NULL,

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

########################################
##    Email Records                   ##
##                                    ##
##    Records of emails already sent  ##
########################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_emails_records` (

  `id`       INT(11)  NOT NULL AUTO_INCREMENT
  COMMENT 'Primary Key',

  `asset_id` INT(11)  NOT NULL
  COMMENT 'Key for assets table',

  `email_id` INT(11)  NOT NULL
  COMMENT 'Primary Key of associated Email',

  `lead_id`  INT(11)  NOT NULL
  COMMENT 'Primary key of associated Lead',

  `sent`     DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'when record was created, in UTC',

  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

########################################
##    Leads                           ##
##                                    ##
##    Leads are associated with       ##
##    contacts, which will be created ##
##    when saving a new lead or       ##
##    updated when this lead is       ##
########################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_leads` (

  `id`               INT(11)          NOT NULL AUTO_INCREMENT
  COMMENT 'Primary Key',

  `asset_id`         INT(11)          NOT NULL
  COMMENT 'Key for assets table',

  `page_id`          INT(11)          NOT NULL
  COMMENT 'Primary key of Page associated with this lead',

  `contact_id`       INT(11)          NOT NULL
  COMMENT 'Primary key of Contact associated with this lead',

  `priority_id`      INT(11)          NOT NULL
  COMMENT 'Primary key of Priority associated with this lead',

  `status_id`        INT(11)          NOT NULL
  COMMENT 'Primary key of Status associated with this lead',

  `first_name`       VARCHAR(255)     NOT NULL
  COMMENT 'First name of Lead (contacts uses single column for both names)',

  `last_name`        VARCHAR(255)     NOT NULL
  COMMENT 'Last name of Lead (contacts uses single column for both names)',

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

########################################
##    Lead Statuses                   ##
##                                    ##
##    Leads are associated with       ##
##    contacts, which will be created ##
##    when saving a new lead or       ##
##    updated when this lead is       ##
########################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_lead_statuses` (

  `id`               INT(11)          NOT NULL AUTO_INCREMENT
  COMMENT 'Primary Key',

  `asset_id`         INT(11)          NOT NULL
  COMMENT 'Key for assets table',

  `name`             VARCHAR(255)     NOT NULL
  COMMENT 'name of this record',

  `description`      MEDIUMTEXT       NOT NULL
  COMMENT 'description of this record',

  `ordering`         INT(11)          NOT NULL DEFAULT '0'
  COMMENT 'Ordering column for priority level',

  `default`          TINYINT(1)                DEFAULT '0'
  COMMENT 'Default status',

  `final`            TINYINT(1)                DEFAULT '0'
  COMMENT 'Final status',

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

########################################
##    Pages                           ##
##                                    ##
##    Landing pages users will visit  ##
########################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_pages` (

  `id`                        INT(11)          NOT NULL AUTO_INCREMENT
  COMMENT 'Primary Key',

  `asset_id`                  INT(11)          NOT NULL
  COMMENT 'Key for assets table',

  `layout`                    VARCHAR(1)       NOT NULL DEFAULT "A"
  COMMENT 'Layout type - 0 for custom, or A-D',

  `heading`                   VARCHAR(255)     NOT NULL,
  `subheading`                VARCHAR(255)     NOT NULL,
  `socialmedia`               TINYINT(1)       NOT NULL,
  `maintext`                  BLOB             NOT NULL,
  `sidebartext`               BLOB             NOT NULL,
  `alias`                     VARCHAR(255)     NOT NULL,
  `name`                      VARCHAR(255)     NOT NULL,
  `image`                     VARCHAR(255)     NOT NULL,
  `imagealttext`              VARCHAR(255)     NOT NULL,
  `category`                  VARCHAR(255)     NOT NULL,
  `metatitle`                 VARCHAR(55)      NOT NULL,
  `metadescription`           VARCHAR(155)     NOT NULL,
  `formname`                  VARCHAR(255)     NOT NULL,
  `formbuilder`               BLOB             NOT NULL,
  `campaign`                  INT(11)          NOT NULL,
  `converts_on_another_form`  VARCHAR(25)      NOT NULL,
  `converts_on_same_campaign` VARCHAR(25)      NOT NULL,
  `submit_text`               VARCHAR(255)     NOT NULL,
  `notify_form_submits`       VARCHAR(255)     NOT NULL,
  `notification_email`        VARCHAR(255)     NOT NULL,
  `after_submit_sendto`       VARCHAR(20)      NOT NULL,
  `menu_item`                 VARCHAR(10)      NOT NULL,
  `send_to_url`               VARCHAR(255)     NOT NULL,
  `sendto_message`            TEXT             NOT NULL,
  `template`                  TEXT             NOT NULL,

  `hits`                      INT(11)                   DEFAULT '0'
  COMMENT 'number of views for this record',

  `published`                 TINYINT(1)                DEFAULT '0'
  COMMENT 'publication status of record - 0 is Unpublished, 1 is Published, -2 is Trashed',

  `created`                   DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'when record was created, in UTC',

  `created_by`                INT(11)          NOT NULL DEFAULT '0'
  COMMENT 'User id of record creator',

  `modified`                  DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'when record was last modified in UTC',

  `modified_by`               INT(11)          NOT NULL DEFAULT '0'
  COMMENT 'User id of last modifier',

  `checked_out`               INT(11) UNSIGNED NOT NULL DEFAULT '0'
  COMMENT 'Locking column to prevent simultaneous updates',

  `checked_out_time`          DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'Date and Time record was checked out',

  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

########################################
##    Priorities                      ##
##                                    ##
##    Allow leads to be sorted by     ##
##    custom priority                 ##
########################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_priorities` (

  `id`               INT(11)          NOT NULL AUTO_INCREMENT
  COMMENT 'Primary Key',

  `asset_id`         INT(11)          NOT NULL
  COMMENT 'Key for assets table',

  `name`             VARCHAR(255)     NOT NULL
  COMMENT 'name of this record',

  `description`      MEDIUMTEXT       NOT NULL
  COMMENT 'description of this record',

  `ordering`         INT(11)          NOT NULL DEFAULT '0'
  COMMENT 'Ordering column for priority level',

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

########################################
##    Stages                          ##
##                                    ##
##    Customizeable steps to show     ##
##    where leads are in the process  ##
########################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_stages` (

  `id`               INT(11)          NOT NULL AUTO_INCREMENT
  COMMENT 'Primary Key',

  `asset_id`         INT(11)          NOT NULL
  COMMENT 'Key for assets table',

  `name`             VARCHAR(255)     NOT NULL
  COMMENT 'name of this record',

  `description`      MEDIUMTEXT       NOT NULL
  COMMENT 'description of this record',

  `ordering`         INT(11)          NOT NULL DEFAULT '0'
  COMMENT 'Ordering column for stage level',

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

