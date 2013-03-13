<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;


JLoader::register('JInbound', JPATH_ADMINISTRATOR.'/components/com_jinbound/helpers/jinbound.php');
JInbound::registerLibrary('JInboundBaseModel', 'models/basemodel');

class JInboundModelPage extends JInboundBaseModel
{
	public $_context = 'com_jinbound.page';

	public function &getItem()
	{
		// Initialise variables.
		$id = JFactory::getApplication()->input->get('id', 0, 'int');

		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select('Page.*');
		$query->from('#__jinbound_pages AS Page');
		$query->where('Page.id = ' . (int) $id);

		$db->setQuery($query);

		$data = $db->loadObject();
		
		$registry = new JRegistry();
		$registry->loadString($data->formbuilder);
		$data->formbuilder = $registry->toArray();//JArrayHelper::fromObject($registry);

		return $data;
	}
}
