/* using comments to bypass limitations in JDatabaseDriver::splitSql - do not remove! */

DROP PROCEDURE IF EXISTS add_jinbound_page_image_sizes ;
CREATE PROCEDURE add_jinbound_page_image_sizes()
BEGIN
	DECLARE CONTINUE HANDLER FOR 1060 BEGIN END /*"*/;/*"*/
	
	ALTER TABLE #__jinbound_pages ADD
		`image_size_small_height` varchar(50) NOT NULL DEFAULT ''
		AFTER `imagealttext`
	/*"*/;/*"*/
	
	ALTER TABLE #__jinbound_pages ADD
		`image_size_small_width` varchar(50) NOT NULL DEFAULT ''
		AFTER `imagealttext`
	/*"*/;/*"*/
	
	ALTER TABLE #__jinbound_pages ADD
		`image_size_medium_height` varchar(50) NOT NULL DEFAULT ''
		AFTER `imagealttext`
	/*"*/;/*"*/
	
	ALTER TABLE #__jinbound_pages ADD
		`image_size_medium_width` varchar(50) NOT NULL DEFAULT ''
		AFTER `imagealttext`
	/*"*/;/*"*/
	
	ALTER TABLE #__jinbound_pages ADD
		`image_size_large_height` varchar(50) NOT NULL DEFAULT ''
		AFTER `imagealttext`
	/*"*/;/*"*/
	
	ALTER TABLE #__jinbound_pages ADD
		`image_size_large_width` varchar(50) NOT NULL DEFAULT ''
		AFTER `imagealttext`
	/*"*/;/*"*/

END
;
CALL add_jinbound_page_image_sizes();
DROP PROCEDURE IF EXISTS add_jinbound_page_image_sizes ;

