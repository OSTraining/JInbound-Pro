/* using comments to bypass limitations in JDatabaseDriver::splitSql - do not remove! */

ALTER TABLE #__jinbound_pages ADD
`ga` TINYINT (1) NOT NULL DEFAULT 0
AFTER `css`
/*"*/;
/*"*/

ALTER TABLE #__jinbound_pages ADD
`ga_code` VARCHAR (255) NOT NULL DEFAULT ''
AFTER `ga`
/*"*/;
/*"*/
