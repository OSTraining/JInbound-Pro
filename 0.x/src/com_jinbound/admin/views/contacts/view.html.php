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
		$campaigns = $this->get('CampaignsOptions');
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		if (1 >= count($campaigns)) {
			$this->app->enqueueMessage(JText::_('COM_JINBOUND_NO_CAMPAIGNS_YET'), 'warning');
		}
		return parent::display($tpl, $safeparams);
	}
}
