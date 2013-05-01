<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR . '/components/com_jinbound/helpers/jinbound.php');
JInbound::registerLibrary('JInboundFormController', 'controllers/basecontrollerform');

class JInboundControllerLead extends JInboundFormController
{
	public function status() {
		$this->_changeLead('status');
	}
	
	public function priority() {
		$this->_changeLead('priority');
	}
	
	private function _changeLead($how) {
		$app   = JFactory::getApplication();
		$id    = $app->input->get('id');
		$value = $app->input->get('value');
		$model = $this->getModel();
		$model->$how($id, $value);
	}
}
