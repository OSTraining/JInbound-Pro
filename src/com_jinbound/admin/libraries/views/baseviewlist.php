<?php
/**
 * @package             JInbound
 * @subpackage          com_jinbound
 * @ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JInboundView', JPATH_ADMINISTRATOR . '/components/com_jinbound/libraries/views/baseview.php');

class JInboundListView extends JInboundView
{
    protected $items;
    protected $pagination;
    protected $state;

    function display($tpl = null, $safeparams = false)
    {
        if (!JFactory::getUser()
            ->authorise('core.manage', 'com_jinbound.' . strtolower(JInboundInflector::singularize($this->_name)))) {
            JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
            return false;
        }
        $items         = $this->get('Items');
        $pagination    = $this->get('Pagination');
        $state         = $this->get('State');
        $permissions   = $this->get('Permissions');
        $filterForm    = $this->get('FilterForm');
        $activeFilters = $this->get('ActiveFilters');
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode('<br />', $errors));
            return false;
        }
        $this->items         = $items;
        $this->pagination    = $pagination;
        $this->state         = $state;
        $this->permissions   = $permissions;
        $this->filterForm    = $filterForm;
        $this->activeFilters = $activeFilters;

        $this->ordering = array(0 => array());
        if (!empty($items)) {
            foreach ($items as $item) {
                if (!(property_exists($item, 'ordering') || property_exists($item, 'lft'))) {
                    break;
                }
                $this->ordering[0][] = $item->id;
            }
        }

        $publishedOptions = $this->get('PublishedStatus');
        if (!empty($publishedOptions)) {
            $this->addFilter(JText::_('COM_JINBOUND_SELECT_PUBLISHED'), 'filter[published]', $publishedOptions,
                $state->get('filter.published'));
        }

        return parent::display($tpl, $safeparams);
    }

    public function addFilter($label, $name, $options, $default, $trim = true)
    {
        $filter          = new stdClass;
        $filter->label   = $label;
        $filter->name    = $name;
        $filter->options = $options;
        $filter->default = $default;
        $filter->trim    = $trim;
        if (!is_array($this->_filters)) {
            $this->_filters = array();
        }
        return $this->_filters[] = $filter;
    }

    public function renderFilters()
    {
        if (empty($this->_filters)) {
            return;
        }
        if (class_exists('JHtmlSidebar')) {
            foreach ($this->_filters as $filter) {
                if (empty($filter->options)) {
                    continue;
                }
                if ($filter->trim) {
                    array_shift($filter->options);
                }
                $options = JHtml::_('select.options', $filter->options, 'value', 'text', $filter->default, true);
                JHtmlSidebar::addFilter($filter->label, $filter->name, $options);
            }
            return;
        }
        $html = array();
        foreach ($this->_filters as $filter) {
            if (empty($filter->options)) {
                continue;
            }
            $this->currentFilter = JHtml::_('select.genericlist', $filter->options, $filter->name,
                sprintf('id="%s" class="listbox" onchange="this.form.submit()"', $filter->name), 'value', 'text',
                $filter->default);
            $html[]              = $this->loadTemplate('filter', 'default');
        }
        return implode("\n", $html);
    }

    public function addToolBar()
    {
        // only fire in administrator, and only once
        if (!JFactory::getApplication()->isAdmin()) {
            return;
        }

        static $set;

        if (is_null($set)) {
            $single       = strtolower(JInboundInflector::singularize($this->_name));
            $user         = JFactory::getUser();
            $canCreate    = $user->authorise('core.create', JInbound::COM . ".$single");
            $canDelete    = $user->authorise('core.delete', JInbound::COM . ".$single");
            $canEdit      = $user->authorise('core.edit', JInbound::COM . ".$single");
            $canEditOwn   = $user->authorise('core.edit.own', JInbound::COM . ".$single");
            $canEditState = $user->authorise('core.edit.state', JInbound::COM . ".$single");
            // set the toolbar title
            $title = strtoupper(JInbound::COM . '_' . $this->_name . '_MANAGER');
            $class = 'jinbound-' . strtolower($this->_name);
            if ('contacts' === $this->_name) {
                $title = strtoupper(JInbound::COM . '_LEADS_MANAGER');
                $class = 'jinbound-leads';
            }
            if ($canCreate) {
                JToolBarHelper::addNew($single . '.add', 'JTOOLBAR_NEW');
            }
            if ($canEdit || $canEditOwn) {
                JToolBarHelper::editList($single . '.edit', 'JTOOLBAR_EDIT');
                JToolBarHelper::divider();
            }
            if ($canEditState) {
                JToolBarHelper::publish($this->_name . '.publish', 'JTOOLBAR_PUBLISH', true);
                JToolBarHelper::unpublish($this->_name . '.unpublish', 'JTOOLBAR_UNPUBLISH', true);
                JToolBarHelper::checkin($this->_name . '.checkin');
                JToolBarHelper::divider();
            }
            if ($this->state->get('filter.published') == -2 && $canDelete) {
                JToolBarHelper::deleteList('', $this->_name . '.delete', 'JTOOLBAR_EMPTY_TRASH');
            } else {
                if ($canEditState) {
                    JToolBarHelper::trash($this->_name . '.trash');
                    JToolBarHelper::divider();
                }
            }
            // add parent toolbar
            parent::addToolBar();

            JToolBarHelper::title(JText::_($title), $class);
        }
        $set = true;
    }

    /**
     * Returns an array of fields the table can be sorted by
     *
     * @return  array  Array containing the field name to sort by as the key and display text as value
     */
    protected function getSortFields()
    {
        return array();
    }
}
