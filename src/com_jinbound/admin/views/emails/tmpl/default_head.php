<?php
/**
 * @package             JInbound
 * @subpackage          com_jinbound
 * @ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');
$saveOrder = ($listOrder == 'Email.id');
?>
<tr>
    <th width="1%" class="nowrap hidden-phone">
        <input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);"/>
    </th>
    <th width="15%">
        <?php echo JHtml::_($this->sortFunction, 'COM_JINBOUND_CAMPAIGN_NAME', 'Campaign.name', $listDirn,
            $listOrder); ?>
    </th>
    <th>
        <?php echo JHtml::_($this->sortFunction, 'COM_JINBOUND_EMAIL_NAME', 'Email.name', $listDirn, $listOrder); ?>
    </th>
    <th width="5%" class="nowrap hidden-phone">
        <?php echo JHtml::_($this->sortFunction, 'COM_JINBOUND_CAMPAIGN_ACTIVE', 'Email.published', $listDirn,
            $listOrder); ?>
    </th>
    <th width="5%">
        <?php echo JHtml::_($this->sortFunction, 'COM_JINBOUND_EMAIL_TYPE', 'Email.type', $listDirn, $listOrder); ?>
    </th>
    <th width="5%" class="nowrap hidden-phone">
        <?php echo JHtml::_($this->sortFunction, 'COM_JINBOUND_CAMPAIGN_SCHEDULE', 'Email.sendafter', $listDirn,
            $listOrder); ?>
    </th>
    <th width="1%" class="nowrap hidden-phone">
        <?php echo JText::_('COM_JINBOUND_ID'); ?>
    </th>
</tr>
