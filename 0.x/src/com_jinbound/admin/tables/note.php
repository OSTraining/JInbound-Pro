<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR . "/components/com_jinbound/helpers/jinbound.php");
JInbound::registerLibrary('JInboundTable', 'table');

class JInboundTableNote extends JInboundTable
{
	var $id; // Primary Key
	var $asset_id; // Key for assets table
	var $lead_id; // Key for leads table
	var $text; // note text
	var $published; // publication status of record - 0 is Unpublished, 1 is Published, -2 is Trashed
	var $created; // when record was created, in UTC
	var $created_by; // User id of record creator
	var $modified; // when record was last modified in UTC
	var $modified_by; // User id of last modifier
	var $checked_out; // Locking column to prevent simultaneous updates
	var $checked_out_time; // Date and Time record was checked out
	
	function __construct(&$db) {
		parent::__construct('#__jinbound_notes', 'id', $db);
	}
}
