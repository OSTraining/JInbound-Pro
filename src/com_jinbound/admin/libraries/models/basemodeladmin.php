<?php
/**
 * @package             JInbound
 * @subpackage          com_jinbound
 * @ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.application.component.modeladmin') or jimport('legacy.model.admin');
jimport('joomla.form.form');
jimport('joomla.form.helper');

JLoader::register('JInboundHelperPath', JPATH_ADMINISTRATOR . '/components/com_jinbound/helpers/path.php');
JLoader::register('JInbound', JInboundHelperPath::helper('jinbound'));

JForm::addFormPath(JInboundHelperPath::admin('models/forms'));
JForm::addFieldPath(JInboundHelperPath::admin('models/fields'));

class JInboundAdminModel extends JModelAdmin
{
    public $option = JInbound::COM;

    private $_registryColumns = null;

    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm(JInbound::COM . '.' . $this->name, $this->name,
            array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }
        return $form;
    }

    public function getTable($type = null, $prefix = 'JInboundTable', $config = array())
    {
        if (empty($type)) {
            $type = $this->name;
        }
        return JTable::getInstance($type, $prefix, $config);
    }

    function cleanCache($group = null, $client_id = 0)
    {
        parent::cleanCache($this->option);
        parent::cleanCache('_system');
        parent::cleanCache($group, $client_id);
    }

    /**
     * give public read access to the model's context
     *
     */
    public function getContext()
    {
        return (string)$this->_context;
    }

    protected function loadFormData()
    {
        $data = JFactory::getApplication()
            ->getUserState(JInbound::COM . '.edit.' . strtolower($this->name) . '.data', array());
        if (empty($data)) {
            $data = $this->getItem();
        }
        return $data;
    }

    public function getItem($id = null)
    {
        $item = parent::getItem($id);
        // if we have no columns to alter, we're done
        if (!is_array($this->_registryColumns) || empty($this->_registryColumns)) {
            return $item;
        }
        foreach ($this->_registryColumns as $col) {
            if (!property_exists($item, $col)) {
                continue;
            }
            $registry = new JRegistry();
            $registry->loadString($item->$col);
            $item->$col = $registry;
        }
        return $item;
    }
}
