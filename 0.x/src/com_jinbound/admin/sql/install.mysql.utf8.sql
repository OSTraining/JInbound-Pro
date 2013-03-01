########################################
##        JInbound SQL Install        ##
########################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_campaigns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` datetime NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_by` int(11) NOT NULL,
  `modified_by` int(11) NOT NULL,
  `modified` datetime NOT NULL,
  `fromname` varchar(255) NOT NULL,
  `fromemail` varchar(255) NOT NULL,
  `sendafter` int(11) NOT NULL,
  `subject` int(11) NOT NULL,
  `htmlbody` blob NOT NULL,
  `plainbody` blob NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__jinbound_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` datetime NOT NULL,
  `published` tinyint(1) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_by` int(11) NOT NULL,
  `modified_by` int(11) NOT NULL,
  `modified` datetime NOT NULL,
  `description` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__jinbound_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `modified_by` int(11) NOT NULL,
  `modified` datetime NOT NULL,
  `heading` varchar(255) NOT NULL,
  `subheading` varchar(255) NOT NULL,
  `socialmedia` tinyint(1) NOT NULL,
  `maintext` blob NOT NULL,
  `sidebartext` blob NOT NULL,
  `alias` varchar(255) NOT NULL,
  `published` tinyint(1) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `imagealttext` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `metatitle` varchar(55) NOT NULL,
  `metadescription` varchar(155) NOT NULL,
  `formname` varchar(255) NOT NULL,
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__jinbound_priorities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` datetime NOT NULL,
  `published` tinyint(1) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_by` int(11) NOT NULL,
  `modified_by` int(11) NOT NULL,
  `modified` datetime NOT NULL,
  `description` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__jinbound_stages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` datetime NOT NULL,
  `published` tinyint(1) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_by` int(11) NOT NULL,
  `modified_by` int(11) NOT NULL,
  `modified` datetime NOT NULL,
  `description` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

