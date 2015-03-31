/* using comments to bypass limitations in JDatabaseDriver::splitSql - do not remove! */

DROP PROCEDURE IF EXISTS fix_jinbound_contact_campaigns_date ;
CREATE PROCEDURE fix_jinbound_contact_campaigns_date()
BEGIN
	DECLARE CONTINUE HANDLER FOR 1060 BEGIN END /*"*/;/*"*/
	
	ALTER TABLE #__jinbound_contacts_campaigns ADD
		`added` datetime NOT NULL default '0000-00-00 00:00:00'
		AFTER `enabled`
	/*"*/;/*"*/

	/* Find the values from existing conversions and use those */
	UPDATE #__jinbound_contacts_campaigns CoCa
	INNER JOIN #__jinbound_pages Pg
		ON Pg.campaign = CoCa.campaign_id
	INNER JOIN #__jinbound_conversions Co
		ON Co.contact_id = CoCa.contact_id
		AND Co.page_id = Pg.id
	SET CoCa.added = Co.created
	WHERE CoCa.added = '0000-00-00 00:00:00'
	/*"*/;/*"*/

END
;
CALL fix_jinbound_contact_campaigns_date();
DROP PROCEDURE IF EXISTS fix_jinbound_contact_campaigns_date ;

