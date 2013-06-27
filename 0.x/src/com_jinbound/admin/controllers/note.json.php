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
	public function save() {
		$save = parent::save();
		$data = JFactory::getApplication()->input->get('jform', array(), 'array');
		$lead = 0;
		if (is_array($data) && array_key_exists('lead_id', $data)) {
			$lead = (int) $data['lead_id'];
		}
		$db = JFactory::getDbo();
		$db->setQuery($db->getQuery(true)
			->select('id, created, text')
			->from('#__jinbound_notes')
			->where('lead_id = ' . $lead)
			->where('published = 1')
		);
		try {
			$notes = $db->loadObjectList();
			if (!is_array($notes) || empty($notes)) {
				throw new Exception('Empty');
			}
		}
		catch (Exception $e) {
			$notes = array();
		}
		
		$return = array(
			'notes'   => $notes
		,	'lead'    => $lead
		,	'error'   => $this->getError()
		);
		
		echo json_encode($return);
		jexit();
	}
}
