<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR.'/components/com_jinbound/helpers/jinbound.php');
JInbound::registerHelper('url');
JInbound::registerLibrary('JInboundInflector', 'inflector');
JInbound::registerLibrary('JInboundPageController', 'controllers/basecontrollerpage');

class JInboundControllerPage extends JInboundPageController
{
	public function edit($key = 'id', $urlVar = 'id') {
		$model      = $this->getModel('Pages', 'JInboundModel');
		$canAdd     = true;
		foreach (array('categories', 'campaigns') as $var) {
			$single = JInboundInflector::singularize($var);
			$method = 'get' . ucwords($var) . 'Options';
			$$var = $model->$method();
			// if we don't have any categories yet, warn the user
			// there's always going to be one option in this list
			if (1 >= count($$var)) {
				JFactory::getApplication()->enqueueMessage(JText::_('COM_JINBOUND_NO_' . strtoupper($var) . '_YET'), 'error');
				$canAdd = false;
			}
		}
		if (!$canAdd) {
			$this->redirect(JInboundHelperUrl::view('pages'));
			jexit();
		}
		return parent::edit($key, $urlVar);
	}
	
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'set') {
		$set     = JFactory::getApplication()->input->get('set', 'a', 'cmd');
		$append  = parent::getRedirectToItemAppend($recordId, $urlVar);
		$append .= '&set=' . $set;
		return $append;
	}
}
