/* using comments to bypass limitations in JDatabaseDriver::splitSql - do not remove! */

ALTER TABLE #__jinbound_campaigns ADD
`greedy` TINYINT (1) NOT NULL DEFAULT 0
COMMENT 'this campaign wants the contacts to itself'
AFTER `label`
/*"*/;
/*"*/
