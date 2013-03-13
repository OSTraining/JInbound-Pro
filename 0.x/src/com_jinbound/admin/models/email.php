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
 * This models supports retrieving an email.
 *
 * @package		JInbound
 * @subpackage	com_jinbound
 */
class JInboundModelEmail extends JInboundAdminModel
{
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	public $_context = 'com_jinbound.email';

	public function getForm($data = array(), $loadData = true) {
		// Get the form.
		$form = $this->loadForm($this->option.'.'.$this->name, $this->name, array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}
		return $form;
	}
}
