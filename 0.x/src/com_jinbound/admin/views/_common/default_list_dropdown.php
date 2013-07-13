<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

JInbound::registerLibrary('JInboundInflector', 'inflector');

$trashed = (-2 == $this->state->get('filter.published'));

if (JInbound::version()->isCompatible('3.0')) : ?>
<div class="pull-left">
<?php
	$itemName = JInboundInflector::singularize($this->getName());
	$listName = JInboundInflector::pluralize($this->getName());

	JHtml::_('dropdown.edit', $this->currentItem->id, $itemName . '.');
	JHtml::_('dropdown.divider');
	JHtml::_('dropdown.' . ($this->currentItem->published ? 'un' : '') . 'publish', 'cb' . $this->_itemNum, $listName . '.');
	if ($this->currentItem->checked_out) :
		JHtml::_('dropdown.checkin', 'cb' . $this->_itemNum, $listName . '.');
	endif;
	JHtml::_('dropdown.' . ($trashed ? 'un' : '') . 'trash', 'cb' . $this->_itemNum, $listName . '.');

	echo JHtml::_('dropdown.render');

?>
</div>
<?php

endif;
