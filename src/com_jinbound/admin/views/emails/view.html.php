<?php
/**
 * @package             jInbound
 * @subpackage          com_jinbound
 * @ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR . "/components/com_jinbound/helpers/jinbound.php");
JInbound::registerLibrary('JInboundInflector', 'inflector');
JInbound::registerLibrary('JInboundListView', 'views/baseviewlist');
JLoader::register('JInboundModelPages', JPATH_ADMINISTRATOR . '/components/com_jinbound/models/pages.php');

class JInboundViewEmails extends JInboundListView
{
    function display($tpl = null, $safeparams = false)
    {
        $model     = new JInboundModelPages(array());
        $campaigns = $model->getCampaignsOptions();
        // if we don't have any categories yet, warn the user
        // there's always going to be one option in this list
        if (1 >= count($campaigns)) {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_JINBOUND_NO_CAMPAIGNS_YET'), 'warning');
        }
        // set advice text
        $this->adviceText = JText::_('COM_JINBOUND_LEAD_MANAGER_RANDOM_ADVICE_' . rand(1, 5));

        // set filters
        $filter = (array)$this->app->getUserStateFromRequest($this->get('State')->get('context') . '.filter', 'filter',
            array(), 'array');
        $this->addFilter(JText::_('COM_JINBOUND_SELECT_STATUS'), 'filter[status]', $this->get('StatusOptions'),
            array_key_exists('status', $filter) ? $filter['status'] : '', false);

        // display
        return parent::display($tpl, $safeparams);
    }

    /**
     * Returns an array of fields the table can be sorted by
     *
     * @return  array  Array containing the field name to sort by as the key and display text as value
     */
    protected function getSortFields()
    {
        return array(
            'Campaign.name'   => JText::_('COM_JINBOUND_CAMPAIGN_NAME')
        ,
            'Email.name'      => JText::_('COM_JINBOUND_EMAIL_NAME')
        ,
            'Email.published' => JText::_('COM_JINBOUND_CAMPAIGN_ACTIVE')
        ,
            'Email.sendafter' => JText::_('COM_JINBOUND_CAMPAIGN_SCHEDULE')
        );
    }
}
