<?php
/**
 * @package             JInbound
 * @subpackage          com_jinbound
 * @ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInbound', JPATH_ADMINISTRATOR . '/components/com_jinbound/helpers/jinbound.php');
JInbound::registerLibrary('JInboundAdminModel', 'models/basemodeladmin');

/**
 * This models supports retrieving lists of fields.
 *
 * @package        JInbound
 * @subpackage     com_jinbound
 */
class JInboundModelField extends JInboundAdminModel
{
    /**
     * Model context string.
     *
     * @var        string
     */
    protected $context = 'com_jinbound.field';

    /**
     * The event to trigger after saving the data.
     *
     * @var    string
     */
    protected $event_after_save = 'onJInboundAfterSave';

    /**
     * The event to trigger before saving the data.
     *
     * @var    string
     */
    protected $event_before_save = 'onJInboundBeforeSave';

    public function getTable($type = 'Field', $prefix = 'JInboundTable', $config = array())
    {
        return parent::getTable($type, $prefix, $config);
    }

    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm($this->option . '.' . $this->name, $this->name,
            array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }
        return $form;
    }
}
