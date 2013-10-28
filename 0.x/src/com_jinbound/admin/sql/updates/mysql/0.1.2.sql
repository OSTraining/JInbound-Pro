########################################
##    Add raw formdata to lead        ##
########################################;

ALTER TABLE #__jinbound_leads ADD COLUMN `formdata` TEXT COMMENT 'JSON encoded form data';
