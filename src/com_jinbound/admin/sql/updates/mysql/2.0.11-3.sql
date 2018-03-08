/* using comments to bypass limitations in JDatabaseDriver::splitSql - do not remove! */

ALTER TABLE #__jinbound_emails ADD
`type` VARCHAR (255) NOT NULL DEFAULT 'campaign'
AFTER `name`
/*"*/;
/*"*/

