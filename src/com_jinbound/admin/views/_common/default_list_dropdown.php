<?php
/**
 * @package             JInbound
 * @subpackage          com_jinbound
 * @ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JInbound::registerLibrary('JInboundInflector', 'inflector');

$trashed  = (-2 == $this->state->get('filter.published'));
$itemName = JInboundInflector::singularize($this->getName());
$listName = JInboundInflector::pluralize($this->getName());

$user       = JFactory::getUser();
$userId     = $user->get('id');
$canCheckin = $user->authorise('core.manage',
        'com_checkin') || $this->currentItem->checked_out == $userId || $this->currentItem->checked_out == 0;
$canEdit    = $user->authorise('core.edit', JInbound::COM . ".$itemName") && $canCheckin;
$canChange  = $user->authorise('core.edit.state', JInbound::COM . ".$itemName") && $canCheckin;
$canEditOwn = $user->authorise('core.edit.own',
        JInbound::COM . ".$itemName") && $this->currentItem->created_by == $userId && $canCheckin;

if (JInbound::version()->isCompatible('3.0') && ($canEdit || $canEditOwn || $canChange)) : ?>
    <div class="pull-left">
        <?php
        if ($canEdit || $canEditOwn) {
            JHtml::_('dropdown.edit', $this->currentItem->id, $itemName . '.');
            JHtml::_('dropdown.divider');
        }
        if ($canChange) {
            JHtml::_('dropdown.' . ($this->currentItem->published ? 'un' : '') . 'publish', 'cb' . $this->_itemNum,
                $listName . '.');
        }
        if ($canCheckin && $this->currentItem->checked_out) {
            JHtml::_('dropdown.checkin', 'cb' . $this->_itemNum, $listName . '.');
        }
        if ($canChange) {
            JHtml::_('dropdown.' . ($trashed ? 'un' : '') . 'trash', 'cb' . $this->_itemNum, $listName . '.');
        }

        echo JHtml::_('dropdown.render');

        ?>
    </div>
<?php

endif;
