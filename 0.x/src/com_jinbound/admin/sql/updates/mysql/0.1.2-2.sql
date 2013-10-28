########################################
##        Add active to status        ##
########################################;

ALTER TABLE #__jinbound_lead_statuses ADD COLUMN `active` tinyint(1) DEFAULT 0 COMMENT 'Active statuses count towards leads, inactive do not' AFTER `default`;
