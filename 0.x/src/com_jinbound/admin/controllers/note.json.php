<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR . '/components/com_jinbound/helpers/jinbound.php');
JInbound::registerLibrary('JInboundFormController', 'controllers/basecontrollerform');

class JInboundControllerNote extends JInboundFormController
{
	public function delete()
	{
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
		$model  = $this->getModel();
		$input  = JFactory::getApplication()->input;
		$ids    = $input->get('id', array(), 'array');
		$lead   = $input->get('leadid', 0, 'int');
		$delete = $model->delete($ids);
		$notes  = $this->_getNotes($lead);
		$return = array(
			'notes'   => $notes
		,	'error'   => $model->getError()
		);
		
		echo json_encode($return);
		jexit();
	}
	
	public function save() {
		$save = parent::save();
		$data = JFactory::getApplication()->input->get('jform', array(), 'array');
		$lead = 0;
		if (is_array($data) && array_key_exists('lead_id', $data)) {
			$lead = (int) $data['lead_id'];
		}
		
		$notes = $this->_getNotes($lead);
		
		$return = array(
			'notes'   => $notes
		,	'lead'    => $lead
		,	'error'   => $this->getError()
		);
		
		echo json_encode($return);
		jexit();
	}
	
	private function _getNotes($lead)
	{
		$db = JFactory::getDbo();
		try
		{
			$notes = $db->setQuery($db->getQuery(true)
				->select('n.id, n.lead_id, n.created, u.username AS author, n.text')
				->from('#__jinbound_notes AS n')
				->leftJoin('#__users AS u ON u.id = n.created_by')
				->where('n.lead_id = ' . $lead)
				->where('n.published = 1')
				->group('n.id')
			)->loadObjectList();
			if (!is_array($notes) || empty($notes))
			{
				throw new Exception('Empty');
			}
		}
		catch (Exception $e)
		{
			$notes = array();
		}
		
		return $notes;
	}
}
