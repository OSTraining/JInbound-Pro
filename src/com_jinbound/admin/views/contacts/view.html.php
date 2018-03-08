<?php
/**
 * @package             jInbound
 * @subpackage          com_jinbound
 * @ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR . "/components/com_jinbound/helpers/jinbound.php");
JInbound::registerLibrary('JInboundListView', 'views/baseviewlist');
JInbound::registerHelper('status');
JInbound::registerHelper('priority');

class JInboundViewContacts extends JInboundListView
{
    /**
     *
     *
     * (non-PHPdoc)
     * @see JInboundListView::display()
     */
    function display($tpl = null, $safeparams = false)
    {
        $campaigns  = $this->get('CampaignsOptions');
        $pages      = $this->get('PagesOptions');
        $statuses   = JInboundHelperStatus::getSelectOptions();
        $priorities = JInboundHelperPriority::getSelectOptions();
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode('<br />', $errors));
            return false;
        }
        if (1 >= count($campaigns)) {
            $this->app->enqueueMessage(JText::_('COM_JINBOUND_NO_CAMPAIGNS_YET'), 'warning');
        }
        if (!JInbound::version()->isCompatible('3.0.0')) {
            array_unshift($statuses, (object)array('value' => '', 'text' => JText::_('COM_JINBOUND_SELECT_STATUS')));
            array_unshift($priorities,
                (object)array('value' => '', 'text' => JText::_('COM_JINBOUND_SELECT_PRIORITY')));
        }

        $filter = (array)$this->app->getUserStateFromRequest('com_jinbound.contacts.filter', 'filter', array(),
            'array');

        $this->addFilter(JText::_('COM_JINBOUND_SELECT_CAMPAIGN'), 'filter[campaign]', $campaigns,
            array_key_exists('campaign', $filter) ? $filter['campaign'] : '');
        $this->addFilter(JText::_('COM_JINBOUND_SELECT_PAGE'), 'filter[page]', $pages,
            array_key_exists('page', $filter) ? $filter['page'] : '');
        $this->addFilter(JText::_('COM_JINBOUND_SELECT_STATUS'), 'filter[status]', $statuses,
            array_key_exists('status', $filter) ? $filter['status'] : '', false);
        $this->addFilter(JText::_('COM_JINBOUND_SELECT_PRIORITY'), 'filter[priority]', $priorities,
            array_key_exists('priority', $filter) ? $filter['priority'] : '', false);

        return parent::display($tpl, $safeparams);
    }

    public function addToolBar()
    {
        $icon = 'export';
        if (JInbound::version()->isCompatible('3.0.0')) {
            $icon = 'download';
        }
        // export icons
        if (JFactory::getUser()->authorise('core.create', JInbound::COM . '.report')) {
            JToolBarHelper::custom('reports.exportleads', "{$icon}.png", "{$icon}_f2.png", 'COM_JINBOUND_EXPORT_LEADS',
                false);
        }
        parent::addToolBar();
    }
}
