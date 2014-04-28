########################################
##       JInbound SQL Uninstall       ##
########################################;

DROP TABLE IF EXISTS `#__jinbound_campaigns`;
DROP TABLE IF EXISTS `#__jinbound_emails`;
DROP TABLE IF EXISTS `#__jinbound_emails_records`;
DROP TABLE IF EXISTS `#__jinbound_emails_versions`;
DROP TABLE IF EXISTS `#__jinbound_lead_statuses`;
DROP TABLE IF EXISTS `#__jinbound_leads`;
DROP TABLE IF EXISTS `#__jinbound_notes`;
DROP TABLE IF EXISTS `#__jinbound_pages`;
DROP TABLE IF EXISTS `#__jinbound_priorities`;
DROP TABLE IF EXISTS `#__jinbound_stages`;
DROP TABLE IF EXISTS `#__jinbound_subscriptions`;


DROP TABLE IF EXISTS `#__jinbound_contacts`;
DROP TABLE IF EXISTS `#__jinbound_contacts_tracks`;
DROP TABLE IF EXISTS `#__jinbound_tracks`;