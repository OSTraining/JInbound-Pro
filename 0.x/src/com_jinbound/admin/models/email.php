<?php
/**
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
		
		// remove the sidebar stuff if layout isn't "a" or empty
		$template = strtolower(JFactory::getApplication()->input->get('set', $form->getValue('layout', 'A'), 'cmd'));
		if (!empty($template) && 'a' !== $template) {
			if (1 == JString::strlen($template)) {
				$template = JString::strtoupper($template);
			}
			$form->setValue('layout', null, $template);
		}
		// check published permissions
		if (!JFactory::getUser()->authorise('core.edit.state', 'com_jinbound.email'))
		{
			$form->setFieldAttribute('published', 'readonly', 'true');
		}
		
		return $form;
	}
}
