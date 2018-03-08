<?php
/**
 * @package             JInbound
 * @subpackage          com_jinbound
 * @ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');
$saveOrder = ($listOrder == 'Form.id');
?>
<tr>
    <th width="1%" class="nowrap hidden-phone">
        <input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);"/>
    </th>
    <th width="1%" class="nowrap">
        <?php echo JHtml::_($this->sortFunction, 'COM_JINBOUND_PUBLISHED', 'Form.published', $listDirn, $listOrder); ?>
    </th>
    <th class="nowrap">
        <?php echo JHtml::_($this->sortFunction, 'COM_JINBOUND_TITLE', 'Form.title', $listDirn, $listOrder); ?>
    </th>
    <th width="1%" class="nowrap hidden-phone">
        <?php echo JHtml::_($this->sortFunction, 'COM_JINBOUND_FIELD_COUNT', 'FormFieldCount', $listDirn,
            $listOrder); ?>
    </th>
    <th width="1%" class="nowrap hidden-phone hidden-tablet">
        <?php echo JHtml::_($this->sortFunction, 'COM_JINBOUND_ID', 'Form.id', $listDirn, $listOrder); ?>
    </th>
</tr>
