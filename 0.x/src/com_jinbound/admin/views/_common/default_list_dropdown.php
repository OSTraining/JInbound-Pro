<?php
/**
 * @version		$Id$
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
	$list = JInboundInflector::pluralize($this->getName());

	JHtml::_('dropdown.edit', $item->id, $this->getName() . '.');
	JHtml::_('dropdown.divider');
	JHtml::_('dropdown.' . ($item->published ? 'un' : '') . 'publish', 'cb' . $this->_itemNum, $list . '.');
	if ($item->checked_out) :
		JHtml::_('dropdown.checkin', 'cb' . $this->_itemNum, $list . '.');
	endif;
	JHtml::_('dropdown.' . ($trashed ? 'un' : '') . 'trash', 'cb' . $this->_itemNum, $list . '.');

	echo JHtml::_('dropdown.render');

?>
</div>
<?php

endif;
