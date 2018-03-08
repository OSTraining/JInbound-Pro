/* using comments to bypass limitations in JDatabaseDriver::splitSql - do not remove! */

ALTER TABLE #__jinbound_contacts_campaigns ADD
`added` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'
AFTER `enabled`
/*"*/;
/*"*/

/* Find the values from existing conversions and use those */
UPDATE #__jinbound_contacts_campaigns CoCa
    INNER
    JOIN #__jinbound_pages Pg
    ON Pg.campaign = CoCa.campaign_id
INNER JOIN #__jinbound_conversions Co
ON Co.contact_id = CoCa.contact_id
AND Co.page_id = Pg.id
SET CoCa.added = Co.created
WHERE CoCa.added = '0000-00-00 00:00:00'
/*"*/;
/*"*/


