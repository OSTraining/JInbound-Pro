<?php
/**
 * @version		$Id$
 * @package		JInbound
 * @subpackage	com_jinbound

**********************************************
JInbound
Copyright (c) 2012 Anything-Digital.com
**********************************************
JInbound is some kind of marketing thingy

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This header must not be removed. Additional contributions/changes
may be added to this header as long as no information is deleted.
**********************************************
Get the latest version of JInbound at:
http://anything-digital.com/
**********************************************

 */

defined('JPATH_PLATFORM') or die;

?>
<div class="jinbound-empty">
	<h3><?php echo JText::_('COM_JINBOUND_PAGES_EMPTY'); ?></h3>
	<div class="jinbound-empty-button">
		<div class="icon-wrapper">
			<div class="icon">
				<a href="<?php echo JInboundHelperUrl::task('page.add'); ?>">
					<?php /*echo JHTML::_('jinbound.image', 'icon-48-page.png');*/ ?><br />
					<span><?php echo JText::_('COM_JINBOUND_PAGE_ADD_NEW'); ?></span></a>
			</div>
		</div>
	</div>
</div>
