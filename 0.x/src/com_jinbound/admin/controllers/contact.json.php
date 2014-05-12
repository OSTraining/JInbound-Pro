<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR . '/components/com_jinbound/helpers/jinbound.php');
JInbound::registerLibrary('JInboundBaseController', 'controllers/basecontroller');

class JInboundControllerContact extends JInboundBaseController
{
	public function status() {
		$this->_changeContact('status');
	}
	
	public function priority() {
		$this->_changeContact('priority');
	}
	
	private function _changeContact($how) {
		$app      = JFactory::getApplication();
		$id       = $app->input->get('id');
		$campaign = $app->input->get('campaign_id');
		$value    = $app->input->get('value');
		$model    = $this->getModel('Contact', 'JInboundModel', array('ignore_request' => true));
		$result   = $model->$how($id, $campaign, $value);
		echo json_encode(array(
			'success' => $result
		,	'request' => array(
				'contact_id'  => $id
			,	'campaign_id' => $campaign
			,	"{$how}_id"   => $value
			)
		));
		jexit();
	}
}
