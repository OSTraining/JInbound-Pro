<?php
/**
 * @package             JInbound
 * @subpackage          com_jinbound
 * @ant_copyright_header@
 */

defined('_JEXEC') or die;

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$user           = JFactory::getUser();
$app            = JFactory::getApplication();
$userId         = $user->get('id');
$listOrder      = $this->escape($this->state->get('list.ordering'));
$listDirn       = $this->escape($this->state->get('list.direction'));
$ordering       = ($listOrder == 'Priority.ordering');
$canOrder       = $user->authorise('core.edit.state', 'com_jinbound.priority');
$saveOrder      = ($listOrder == 'Priority.ordering' && strtolower($listDirn) == 'asc');
$originalOrders = array();

if ($saveOrder) {
    $saveOrderingUrl = 'index.php?option=com_jinbound&task=priorities.saveOrderAjax&tmpl=component';
    JHtml::_('sortablelist.sortable', 'itemList', 'adminForm', strtolower($listDirn), $saveOrderingUrl, false, true);
}

$sortFields = $this->getSortFields();
$assoc      = JLanguageAssociations::isEnabled();
?>
<script type="text/javascript">
    Joomla.orderTable = function() {
        table = document.getElementById("sortTable");
        direction = document.getElementById("directionTable");
        order = table.options[table.selectedIndex].value;
        if (order != '<?php echo $listOrder; ?>') {
            dirn = 'asc';
        }
        else {
            dirn = direction.options[direction.selectedIndex].value;
        }
        Joomla.tableOrdering(order, dirn, '');
    }
</script>
<?php if (!empty($this->sidebar)) : ?>
<div id="j-sidebar-container" class="span2">
    <?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
    <?php else : ?>
    <div id="j-main-container">
        <?php endif; ?>
        <?php echo JHtml::_('jinbound.startTabSet', 'jinbound_default_tabs', array('active' => 'content_tab')); ?>
        <?php echo JHtml::_('jinbound.addTab', 'jinbound_default_tabs', 'content_tab',
            JText::_('JTOOLBAR_EDIT', true)); ?>
        <div class="row-fluid">
            <form action="<?php echo JRoute::_('index.php?option=com_jinbound&view=priorities'); ?>" method="post"
                  name="adminForm" id="adminForm">
                <?php
                // Search tools bar
                echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this), null,
                    array('debug' => false));
                ?>
                <?php if (empty($this->items)) : ?>
                    <div class="alert alert-no-items">
                        <?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
                    </div>
                <?php else : ?>
                    <table class="table table-striped" id="itemList">
                        <thead>
                        <tr>
                            <th width="1%" class="hidden-phone">
                                <?php echo JHtml::_('searchtools.sort', '', 'Priority.ordering', $listDirn, $listOrder,
                                    null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
                            </th>
                            <th width="1%" class="hidden-phone">
                                <?php echo JHtml::_('grid.checkall'); ?>
                            </th>
                            <th width="1%" class="nowrap center">
                                <?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'Priority.published', $listDirn,
                                    $listOrder); ?>
                            </th>
                            <th class="title">
                                <?php echo JHtml::_('searchtools.sort', 'COM_JINBOUND_NAME', 'Priority.name', $listDirn,
                                    $listOrder); ?>
                            </th>
                            <th width="25%" class="hidden-phone">
                                <?php echo JHtml::_('searchtools.sort', 'COM_JINBOUND_DESCRIPTION',
                                    'Priority.description', $listDirn, $listOrder); ?>
                            </th>
                            <th width="1%" class="nowrap hidden-phone">
                                <?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'Priority.id', $listDirn,
                                    $listOrder); ?>
                            </th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <td colspan="6">
                                <?php echo $this->pagination->getListFooter(); ?>
                            </td>
                        </tr>
                        </tfoot>

                        <tbody>
                        <?php
                        foreach ($this->items as $i => $item) :
                            $orderkey = array_search($item->id, $this->ordering[0]);
                            $canCreate = $user->authorise('core.create', 'com_jinbound');
                            $canEdit = $user->authorise('core.edit', 'com_jinbound');
                            $canCheckin = $user->authorise('core.manage',
                                    'com_checkin') || $item->checked_out == $user->get('id') || $item->checked_out == 0;
                            $canChange = $user->authorise('core.edit.state', 'com_jinbound') && $canCheckin;
                            ?>
                            <tr class="row<?php echo $i % 2; ?>" item-id="<?php echo $item->id ?>">
                                <td class="order nowrap center hidden-phone">
                                    <?php
                                    $iconClass = '';
                                    if (!$canChange) {
                                        $iconClass = ' inactive';
                                    } elseif (!$saveOrder) {
                                        $iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::tooltipText('JORDERINGDISABLED');
                                    }
                                    ?>
                                    <span class="sortable-handler<?php echo $iconClass ?>">
								<i class="icon-menu"></i>
							</span>
                                    <?php if ($canChange && $saveOrder) : ?>
                                        <input type="text" style="display:none" name="order[]" size="5"
                                               value="<?php echo $orderkey + 1; ?>"/>
                                    <?php endif; ?>
                                </td>
                                <td class="center hidden-phone">
                                    <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                                </td>
                                <td class="center">
                                    <?php echo JHtml::_('jgrid.published', $item->published, $i, 'priorities.',
                                        $canChange, 'cb'); ?>
                                </td>
                                <td>
                                    <?php if ($item->checked_out) : ?>
                                        <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor,
                                            $item->checked_out_time, 'items.', $canCheckin); ?>
                                    <?php endif; ?>
                                    <?php if ($canEdit) : ?>
                                        <a href="<?php echo JRoute::_('index.php?option=com_jinbound&task=priority.edit&id=' . (int)$item->id); ?>">
                                            <?php echo $this->escape($item->name); ?></a>
                                    <?php else : ?>
                                        <?php echo $this->escape($item->name); ?>
                                    <?php endif; ?>
                                </td>
                                <td class="hidden-phone">
                                    <span class="small"><?php echo nl2br($this->escape($item->description)); ?></span>
                                </td>
                                <td class="center hidden-phone">
                                    <?php echo (int)$item->id; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>

                <input type="hidden" name="task" value=""/>
                <input type="hidden" name="boxchecked" value="0"/>
                <input type="hidden" name="original_order_values" value="<?php echo implode($originalOrders, ','); ?>"/>
                <?php echo JHtml::_('form.token'); ?>
            </form>
        </div>
        <?php echo JHtml::_('jinbound.endTab'); ?>
        <?php if ($this->permissions && JFactory::getUser()->authorise('core.admin', JInbound::COM)) : ?>
            <?php echo JHtml::_('jinbound.addTab', 'jinbound_default_tabs', 'permissions_tab',
                JText::_('JCONFIG_PERMISSIONS_LABEL', true)); ?>
            <div class="row-fluid">
                <form
                    action="<?php echo JRoute::_('index.php?option=com_jinbound&task=' . $this->viewName . '.permissions'); ?>"
                    method="post">
                    <?php foreach ($this->permissions->getFieldsets() as $fieldset) : ?>
                        <?php $fields = $this->permissions->getFieldset($fieldset->name); ?>
                        <fieldset>
                            <legend><?php echo JText::_($fieldset->label); ?></legend>
                            <?php foreach ($fields as $field) : ?>
                                <?php echo $field->input; ?>
                            <?php endforeach; ?>
                        </fieldset>
                    <?php endforeach; ?>
                    <?php echo JHtml::_('form.token'); ?>
                    <button type="submit" class="btn btn-primary"><i
                            class="icon-save"></i> <?php echo JText::_('JTOOLBAR_APPLY'); ?> </button>
                </form>
            </div>
            <?php echo JHtml::_('jinbound.endTab'); ?>
        <?php endif; ?>
        <?php echo JHtml::_('jinbound.endTabSet'); ?>
