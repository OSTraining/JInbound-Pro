<?php
/**
 * @package             JInbound
 * @subpackage          com_jinbound
 * @ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInboundAdminModel',
    JPATH_ADMINISTRATOR . '/components/com_jinbound/libraries/models/basemodeladmin.php');

/**
 * This models supports retrieving a location.
 *
 * @package        JInbound
 * @subpackage     com_jinbound
 */
class JInboundModelPriority extends JInboundAdminModel
{
    /**
     * Model context string.
     *
     * @var        string
     */
    public $_context = 'com_jinbound.priority';

    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm($this->option . '.' . $this->name, $this->name,
            array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }
        if (!JFactory::getApplication()->isAdmin()) {
            // set the frontend locations to be auto-published
            $form->setFieldAttribute('published', 'type', 'hidden');
            $form->setFieldAttribute('published', 'default', '1');
            $form->setValue('published', '1');
        } // check published permissions
        else {
            if (!JFactory::getUser()->authorise('core.edit.state', 'com_jinbound.priority')) {
                $form->setFieldAttribute('published', 'readonly', 'true');
            }
        }
        return $form;
    }
}
