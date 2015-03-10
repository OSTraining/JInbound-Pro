<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR . "/components/com_jinbound/helpers/jinbound.php");
JInbound::registerLibrary('JInboundInflector', 'inflector');
JInbound::registerLibrary('JInboundListView', 'views/baseviewlist');

class JInboundViewPages extends JInboundListView
{
	function display($tpl = null, $safeparams = false) {
		$filter = (array) $this->app->getUserStateFromRequest($this->get('State')->get('context') . '.filter', 'filter', array(), 'array');
		foreach (array('categories', 'campaigns') as $var) {
			$single = JInboundInflector::singularize($var);
			$$var = $this->get(ucwords($var) . 'Options');
			// if we don't have any categories yet, warn the user
			// there's always going to be one option in this list
			if (1 >= count($$var)) {
				JFactory::getApplication()->enqueueMessage(JText::_('COM_JINBOUND_NO_' . strtoupper($var) . '_YET'), 'warning');
			}
			// add category filter
			$this->addFilter(JText::_('COM_JINBOUND_SELECT_' . strtoupper($single)), 'filter[' . $single . ']', $$var, array_key_exists($single, $filter) ? $filter[$single] : '');
		}
		
		return parent::display($tpl, $safeparams);
	}
	
	/**
	 * Returns an array of fields the table can be sorted by
	 * 
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 */
	protected function getSortFields() {
		return array(
			'Page.name'      => JText::_('COM_JINBOUND_LANDINGPAGE_NAME')
		,	'Page.published' => JText::_('COM_JINBOUND_PUBLISHED')
		,	'Page.category'  => JText::_('COM_JINBOUND_CATEGORY')
		,	'Page.hits'      => JText::_('COM_JINBOUND_VIEWS')
		,	'submissions'    => JText::_('COM_JINBOUND_SUBMISSIONS')
		,	'conversions'    => JText::_('COM_JINBOUND_CONVERSIONS')
		);
	}
	
	public function addToolBar() {
		$icon = 'export';
		if (JInbound::version()->isCompatible('3.0.0'))
		{
			$icon = 'download';
		}
		// export icon
		if (JFactory::getUser()->authorise('core.create', JInbound::COM . '.report'))
		{
			JToolBarHelper::custom('reports.exportpages', "{$icon}.png", "{$icon}_f2.png", 'COM_JINBOUND_EXPORT_PAGES', false);
		}
		parent::addToolBar();
	}
}