<?php
/**
 * @package             jInbound
 * @subpackage          com_jinbound
 * @ant_copyright_header@
 */

defined('_JEXEC') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR . "/components/com_jinbound/helpers/jinbound.php");
JInbound::registerLibrary('JInboundItemView', 'views/baseviewitem');
JInbound::registerLibrary('JInboundBaseModel', 'models/basemodel');
JInbound::registerHelper('filter');

class JInboundViewEmail extends JInboundItemView
{
    function display($tpl = null, $safeparams = false)
    {
        $reports_model = JInboundBaseModel::getInstance('Reports', 'JInboundModel');
        $reports_tags  = $reports_model->getReportEmailTags();

        $reports_tips = JText::_('COM_JINBOUND_TIPS_REPORTS_TAGS');
        if (!empty($reports_tags)) {
            $reports_tips .= '<ul>';
            foreach ($reports_tags as $tag) {
                $reports_tips .= '<li>{%' . JInboundHelperFilter::escape($tag) . '%}</li>';
            }
            $reports_tips .= '</ul>';
        }

        $this->emailtags           = new stdClass();
        $this->emailtags->campaign = JText::_('COM_JINBOUND_TIPS_JFORM_EMAIL_TIPS');
        $this->emailtags->report   = $reports_tips;

        return parent::display($tpl, $safeparams);
    }

    public function addToolBar()
    {
        parent::addToolBar();
        $icon = 'send';
        if (JInbound::version()->isCompatible('3.0.0')) {
            $icon = 'mail';
        }
        JToolbarHelper::custom('email.test', "{$icon}.png", "{$icon}_f2.png", 'COM_JINBOUND_EMAIL_TEST', false);
    }
}
