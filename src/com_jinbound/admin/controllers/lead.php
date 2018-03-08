<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

$e = new Exception(__FILE__);
JLog::add('JInboundControllerLead is deprecated. ' . $e->getTraceAsString(), JLog::WARNING, 'deprecated');

JLoader::register('JInbound', JPATH_ADMINISTRATOR . '/components/com_jinbound/helpers/jinbound.php');
JInbound::registerLibrary('JInboundFormController', 'controllers/basecontrollerform');

class JInboundControllerLead extends JInboundFormController
{
	public function save($key = null, $urlVar = null) {
		$app  = JFactory::getApplication();
		$data = $app->input->post->get('jform', array(), 'array');
		if (array_key_exists('formdata', $data)) {
			unset($formdata);
		}
		$data['formdata'] = json_encode($data);
		return parent::save($key, $urlVar);
	}
	
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
