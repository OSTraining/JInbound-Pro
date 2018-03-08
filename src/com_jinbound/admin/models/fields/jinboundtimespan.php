<?php
/**
 * @package             JInbound
 * @subpackage          com_jinbound
 * @ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('text');

JLoader::register('JInboundFieldView', JPATH_ADMINISTRATOR . '/components/com_jinbound/libraries/views/fieldview.php');

class JFormFieldJinboundTimespan extends JFormFieldText
{
    protected $type = 'JinboundTimespan';

    /**
     * Builds the input element
     *
     * (non-PHPdoc)
     * @see JFormField::getInput()
     */
    protected function getInput()
    {
        $view        = $this->getView();
        $view->input = parent::getInput();
        return $view->loadTemplate();
    }

    /**
     * gets a new instance of the base field view
     *
     * @return JInboundFieldView
     */
    protected function getView()
    {
        $viewConfig = array('template_path' => dirname(__FILE__) . '/timespan');
        $view       = new JInboundFieldView($viewConfig);
        return $view;
    }
}
