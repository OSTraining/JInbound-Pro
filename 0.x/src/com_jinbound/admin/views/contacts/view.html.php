<?php
/**
 * @package		jInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR . "/components/com_jinbound/helpers/jinbound.php");
JInbound::registerLibrary('JInboundListView', 'views/baseviewlist');

class JInboundViewContacts extends JInboundListView
{
	/**
	 * 
	 * 
	 * (non-PHPdoc)
	 * @see JInboundListView::display()
	 */
	function display($tpl = null, $safeparams = false) {
		$state = $this->get('State');
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		return parent::display($tpl, $safeparams);
	}
}
