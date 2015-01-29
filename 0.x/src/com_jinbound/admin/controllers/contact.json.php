<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR . '/components/com_jinbound/helpers/jinbound.php');
JInbound::registerHelper('contact');
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
		$list     = array();
		if ('priority' == $how)
		{
			$list = JInboundHelperContact::getContactPriorities($id);
		}
		else if ('status' == $how)
		{
			$statuses  = JInboundHelperContact::getContactStatuses($id);
			$campaigns = JInboundHelperContact::getContactCampaigns($id);
			$list      = array();
			if (!empty($campaigns))
			{
				foreach ($campaigns as $c)
				{
					if (array_key_exists($c->id, $statuses))
					{
						$list[$c->id] = $statuses[$c->id];
					}
				}
			}
		}
		$plugin_results = JDispatcher::getInstance()
			->trigger('onJInboundAfterJsonChangeState', array(
				$how, $id, $campaign, $value, $result
			))
		;
		echo json_encode(array(
			'success' => $result
		,	'list'    => $list
		,	'request' => array(
				'contact_id'  => $id
			,	'campaign_id' => $campaign
			,	"{$how}_id"   => $value
			)
		,	'plugin' => $plugin_results
		));
		jexit();
	}
}
