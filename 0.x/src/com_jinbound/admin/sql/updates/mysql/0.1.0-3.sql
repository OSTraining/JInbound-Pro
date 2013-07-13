########################################
##    Add campaign to lead            ##
########################################;

ALTER TABLE #__jinbound_leads ADD COLUMN campaign_id INT(11) NOT NULL AFTER status_id;