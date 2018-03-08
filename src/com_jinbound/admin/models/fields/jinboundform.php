<?php
/**
 * @package             JInbound
 * @subpackage          com_jinbound
 * @ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.form.formfield');
jimport('joomla.form.helper');

JFormHelper::loadFieldClass('list');

JLoader::register('JInbound', JPATH_ADMINISTRATOR . '/components/com_jinbound/helpers/jinbound.php');
JInbound::registerLibrary('JInboundBaseModel', 'models/basemodel');

class JFormFieldJInboundForm extends JFormFieldList
{
    public $type = 'Jinboundform';

    protected function getOptions()
    {
        // get our form model
        JInboundBaseModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_jinbound/models');
        $model = JInboundBaseModel::getInstance('Forms', 'JInboundModel');
        // fetch the list of available, published forms from the model
        $model->getState('filter.published');
        $model->setState('filter.published', '1');
        $forms = $model->getItems();
        // list of available forms
        $list = array();
        // loop available forms & add to the list
        if (!empty($forms)) {
            foreach ($forms as $form) {
                $list[] = JHtml::_('select.option', $form->id, $form->title);
            }
        }
        // send back all options
        return array_merge(parent::getOptions(), $list);
    }
}
