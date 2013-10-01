<?php
/**
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
	
	private $_registryColumns = array('formbuilder');

	public function getForm($data = array(), $loadData = true) {
		// Get the form.
		$form = $this->loadForm($this->option.'.'.$this->name, $this->name, array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}
		
		// remove the sidebar stuff if layout isn't "a" or empty
		$template = strtolower(JFactory::getApplication()->input->get('set', $form->getValue('layout', 'A'), 'cmd'));
		if (!empty($template) && 'a' !== $template) {
			// single length value - defined template
			if (1 == JString::strlen($template)) {
				// upper case
				$template = JString::strtoupper($template);
			}
			$form->setValue('layout', null, $template);
		}
		
		return $form;
	}
}
