<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR . "/components/com_jinbound/helpers/jinbound.php");
JInbound::registerLibrary('JInboundListView', 'views/baseviewlist');
JInbound::registerLibrary('JInboundBaseModel', 'models/basemodel');

class JInboundViewReports extends JInboundListView
{
	
	function display($tpl = null, $safeparams = false) {
		if (!JFactory::getUser()->authorise('core.manage', 'com_jinbound.report')) {
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}
		$this->state = $this->get('State');
		$this->permissions = $this->get('Permissions');
		$this->filter_change_code = $this->getReportFormFilterChangeCode();
		$this->campaign_filter = $this->getCampaignFilter();
		$this->page_filter = $this->getPageFilter();
		$display  = parent::display($tpl, $safeparams);
		$min      = defined('JDEBUG') && JDEBUG ? '' : '.min';
		$js       = $min . '.js';
		$css      = $min . '.css';
		$document = JFactory::getDocument();
		if (method_exists($document, 'addScript'))
		{
			$document->addScript('../media/jinbound/jqplot/excanvas' . $js);
			$document->addScript('../media/jinbound/jqplot/jquery.jqplot' . $js);
			$document->addScript('../media/jinbound/jqplot/plugins/jqplot.dateAxisRenderer' . $js);
			$document->addScript('../media/jinbound/jqplot/plugins/jqplot.canvasTextRenderer' . $js);
			$document->addScript('../media/jinbound/jqplot/plugins/jqplot.canvasAxisTickRenderer' . $js);
			$document->addScript('../media/jinbound/jqplot/plugins/jqplot.categoryAxisRenderer' . $js);
			$document->addScript('../media/jinbound/jqplot/plugins/jqplot.barRenderer' . $js);
		}
		if (method_exists($document, 'addStyleSheet'))
		{
			$document->addStyleSheet('../media/jinbound/jqplot/jquery.jqplot' . $css);
		}
		return $display;
	}
	
	public function getReportFormFilterChangeCode()
	{
		return "window.fetchReports("
			. "window.jinbound_leads_start, "
			. "window.jinbound_leads_limit, "
			. "jQuery('#filter_start').val(), "
			. "jQuery('#filter_end').val(), "
			. "jQuery('#filter_campaign').find(':selected').val(), "
			. "jQuery('#filter_page').find(':selected').val()"
			. ");";
	}
	
	public function getCampaignFilter()
	{
		$db = JFactory::getDbo();
		$options = $db->setQuery($db->getQuery(true)
			->select('id AS value, name AS text')
			->from('#__jinbound_campaigns')
			->order('name ASC')
		)->loadObjectList();
		array_unshift($options, (object) array('value' => '', 'text' => JText::_('COM_JINBOUND_SELECT_CAMPAIGN')));
		return JHtml::_('select.genericlist', $options, 'filter_campaign', array(
			'list.attr' => array(
				'onchange' => $this->filter_change_code
			)
		));
	}
	
	public function getPageFilter()
	{
		$db = JFactory::getDbo();
		$options = $db->setQuery($db->getQuery(true)
			->select('id AS value, name AS text')
			->from('#__jinbound_pages')
			->order('name ASC')
		)->loadObjectList();
		array_unshift($options, (object) array('value' => '', 'text' => JText::_('COM_JINBOUND_SELECT_PAGE')));
		return JHtml::_('select.genericlist', $options, 'filter_page', array(
			'list.attr' => array(
				'onchange' => $this->filter_change_code
			)
		));
	}
	
	public function getRecentLeads() {
		return $this->_callModelMethod('getRecentContacts');
	}
	
	public function getVisitCount() {
		return $this->_callModelMethod('getVisitCount');
	}
	
	public function getViewsToLeads() {
		return $this->_callModelMethod('getViewsToLeads');
	}
	
	public function getLeadCount() {
		return $this->_callModelMethod('getContactsCount');
		//return $this->_callModelMethod('getLeadCount');
	}
	
	public function getTopLandingPages() {
		return $this->_callModelMethod('getTopPages');
		//return $this->_callModelMethod('getTopLandingPages');
	}
	
	public function getConversionCount() {
		return $this->_callModelMethod('getConversionsCount');
		//return $this->_callModelMethod('getConversionCount');
	}
	
	public function getConversionRate() {
		return $this->_callModelMethod('getConversionRate');
	}
	
	private function _callModelMethod($method, $state = null) {
		$model = JInboundBaseModel::getInstance('Reports', 'JInboundModel');
		$model->getState('init.state');
		if (is_array($state) && !empty($state)) {
			foreach ($state as $key => $value) {
				$model->setState($key, $value);
			}
		}
		return $model->$method();
	}
	
	public function addToolBar() {
		// only fire in administrator, and only once
		if (!JFactory::getApplication()->isAdmin()) return;
		
		static $set;
		
		if (is_null($set)) {
			// export icons
			if (JFactory::getUser()->authorise('core.create', JInbound::COM . '.report'))
			{
				JToolBarHelper::custom($this->_name.'.exportleads', 'export.png', 'export_f2.png', 'COM_JINBOUND_EXPORT_LEADS', false);
				//JToolBarHelper::custom($this->_name.'.exportpages', 'export.png', 'export_f2.png', 'COM_JINBOUND_EXPORT_PAGES', false);
			}
			// skip parent and go to grandparent so we don't have the normal list view icons like "new" and "save"
			$gpview = new JInboundView(array());
			$gpview->addToolbar();
			// set the title (because we're skipping the list view's addToolBar later)
			JToolBarHelper::title(JText::_(strtoupper(JInbound::COM.'_REPORTS')), 'jinbound-'.strtolower($this->_name));
		}
		$set = true;
	}
}
