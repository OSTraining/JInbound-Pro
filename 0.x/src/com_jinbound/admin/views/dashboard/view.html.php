<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.html.pane');

$base = JPATH_ADMINISTRATOR . '/components/com_jinbound';

JLoader::register('JInbound', "$base/helpers/jinbound.php");
JLoader::register('JInboundView', "$base/libraries/views/baseview.php");

class JInboundViewDashboard extends JInboundView
{

	function display($tpl = null, $echo = true) {
		$this->addToolBar();
		$this->addMenuBar();
		parent::display($tpl);
	}
	
	function addToolBar() {
		JToolBarHelper::title(JText::_(JInbound::COM . '_DASHBOARD_TITLE'), 'jinbound');
		parent::addToolBar();
	}
}
