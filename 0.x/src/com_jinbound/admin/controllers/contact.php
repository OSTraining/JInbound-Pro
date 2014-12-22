<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR . '/components/com_jinbound/helpers/jinbound.php');
JInbound::registerHelper('contact');
JInbound::registerHelper('status');
JInbound::registerHelper('priority');
JInbound::registerLibrary('JInboundFormController', 'controllers/basecontrollerform');

class JInboundControllerContact extends JInboundFormController
{
	public function edit($key = 'id', $urlVar = 'id') {
		if (!JFactory::getUser()->authorise('core.manage', 'com_jinbound.contact')) {
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}
		return parent::edit($key, $urlVar);
	}
	
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
		$model    = $this->getModel();
		$model->$how($id, $campaign, $value);
	}
	
	/**
	 * Saves campaign, status etc.
	 * 
	 * (non-PHPdoc)
	 * @see JControllerForm::postSaveHook()
	 */
	protected function postSaveHook($model, $validData = array())
	{
		// only operate on valid records
		$contact = (int) $model->getState('contact.id');
		if ($contact)
		{
			// clear this contact's campaigns
			$db = JFactory::getDbo();
			$db->setQuery($db->getQuery(true)
				->delete('#__jinbound_contacts_campaigns')
				->where('contact_id = ' . $db->quote($contact))
			)->query();
			
			// ensure campaigns is an array
			$campaigns = is_array($validData['_campaigns']) ? $validData['_campaigns'] : (
				empty($validData['_campaigns']) ? array() : array($validData['_campaigns'])
			);
			JArrayHelper::toInteger($campaigns);
			
			// re-add to the desired campaigns
			if (!empty($campaigns))
			{
				$query = $db->getQuery(true)
					->insert('#__jinbound_contacts_campaigns')
					->columns(array('contact_id', 'campaign_id'))
				;
				foreach ($campaigns as $campaign)
				{
					$query->values($contact . ',' . $campaign);
				}
				
				$db->setQuery($query)->query();
				
				// find campaigns this contact has no status for yet
				$new_campaigns = $db->setQuery($db->getQuery(true)
					->select('campaign_id')
					->from('#__jinbound_contacts_campaigns')
					->where('campaign_id NOT IN(('
					.	$db->getQuery(true)
							->select('DISTINCT campaign_id')
							->from('#__jinbound_contacts_statuses')
							->where('contact_id = ' . $contact)
					. '))')
				)->loadColumn();
				
				// this user does not have a status for these campaigns - add a default status for each
				if (!empty($new_campaigns))
				{
					// the default status
					$status_id = JInboundHelperStatus::getDefaultStatus();
					sort($new_campaigns);
					
					foreach (array_unique($new_campaigns) as $new_campaign)
					{
						JInboundHelperStatus::setContactStatusForCampaign($status_id, $contact, $new_campaign);
					}
				}
				
				// find campaigns this contact has no priority for yet
				$new_campaigns = $db->setQuery($db->getQuery(true)
					->select('campaign_id')
					->from('#__jinbound_contacts_campaigns')
					->where('campaign_id NOT IN(('
					.	$db->getQuery(true)
							->select('DISTINCT campaign_id')
							->from('#__jinbound_contacts_priorities')
							->where('contact_id = ' . $contact)
					. '))')
				)->loadColumn();
				
				// this user does not have a status for these campaigns - add a default status for each
				if (!empty($new_campaigns))
				{
					// the default status
					$priority_id = JInboundHelperPriority::getDefaultPriority();
					sort($new_campaigns);
					
					foreach (array_unique($new_campaigns) as $new_campaign)
					{
						JInboundHelperPriority::setContactPriorityForCampaign($priority_id, $contact, $new_campaign);
					}
				}
			}
		}
	}
}
