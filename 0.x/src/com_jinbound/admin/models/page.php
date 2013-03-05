<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInboundAdminModel', JPATH_ADMINISTRATOR.'/components/com_jinbound/libraries/models/basemodeladmin.php');

/**
 * This models supports retrieving a location.
 *
 * @package		JInbound
 * @subpackage	com_jinbound
 */
class JInboundModelPage extends JInboundAdminModel
{
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	public $_context = 'com_jinbound.page';

	public function getForm($data = array(), $loadData = true) {
		// Get the form.
		$form = $this->loadForm($this->option.'.'.$this->name, $this->name, array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}
		if (!JFactory::getApplication()->isAdmin()) {
			// set the frontend locations to be auto-published
			$form->setFieldAttribute('published', 'type', 'hidden');
			$form->setFieldAttribute('published', 'default', '1');
			$form->setValue('published', '1');
		}
		return $form;
	}
}
