<?php
/**
 * @package             jInbound
 * @subpackage          com_jinbound
 * @ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR . "/components/com_jinbound/helpers/jinbound.php");
JInbound::registerLibrary('JInboundItemView', 'views/baseviewitem');

class JInboundViewContact extends JInboundItemView
{
    function display($tpl = null, $safeparams = false)
    {
        $this->notes = $this->get('Notes');
        if (!$this->hasCampaigns()) {
            $this->app->enqueueMessage(JText::_('COM_JINBOUND_NO_CAMPAIGNS_YET_ERROR'), 'error');
            $this->app->redirect(JRoute::_('index.php?option=com_jinbound&view=contacts', false));
        }
        return parent::display($tpl, $safeparams);
    }

    public function hasCampaigns()
    {
        $db        = JFactory::getDbo();
        $campaigns = $db->setQuery($db->getQuery(true)
            ->select('Campaign.id AS value, Campaign.name as text')
            ->from('#__jinbound_campaigns AS Campaign')
            ->where('Campaign.published = 1')
            ->group('Campaign.id')
        )->loadObjectList();
        return !empty($campaigns);
    }

    public function renderFormField($page_id, $field, $value)
    {
        $html       = array();
        $fromplugin = '';
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onJinboundFormbuilderRenderValue', array(&$fromplugin, $page_id, $field, $value));
        if (!empty($fromplugin)) {
            return $fromplugin;
        }
        if (is_object($value) || is_array($value)) {
            $array = (array)$value;
            if (1 === count($array)) {
                $html[] = $this->escape(array_shift($array));
            } else {
                $html[] = '<ul>';
                foreach ($array as $k => $v) {
                    $html[] = '<li>' . $this->escape($v) . '</li>';
                }
                $html[] = '</ul>';
            }
        } else {
            $html[] = $this->escape($value);
        }
        return implode("\n", $html);
    }
}
